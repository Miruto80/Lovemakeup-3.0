<?php
namespace Seguridad;

/**
 * Rate limiter por archivos para rutas sensibles (login, olvidoclave, registrocliente, API).
 *
 * - Un archivo JSON por IP (hash sha256) con hits, violaciones, ultima_violacion y ban_hasta.
 * - Escritura atómica con flock(LOCK_EX): sin race conditions entre peticiones concurrentes.
 * - Las violaciones decaen tras VIOLACION_TTL si la IP deja de exceder el límite.
 * - Sin cooldown artificial: exceder el límite solo rechaza la petición con reintentar_en.
 * - El ban (BAN_DURACION) solo aplica tras MAX_VIOLACIONES dentro del TTL.
 */
class FileRateLimiter
{
    private const VIOLACION_TTL = 1800;   // 30 min: tiempo para que decaiga una violación
    private const MAX_VIOLACIONES = 3;    // violaciones dentro del TTL antes del ban
    private const BAN_DURACION = 3600;    // 1 hora de ban

    private int $limite;
    private int $ventana;
    private string $storagePath;

    public function __construct(int $limite = 5, int $ventana = 60, ?string $storagePath = null)
    {
        $this->limite = $limite;
        $this->ventana = $ventana;
        $this->storagePath = rtrim($storagePath ?? (__DIR__ . '/data/'), '/\\') . DIRECTORY_SEPARATOR;

        if (!is_dir($this->storagePath)) {
            @mkdir($this->storagePath, 0775, true);
        }
    }

    /**
     * Verifica si la IP puede realizar la solicitud y registra el intento.
     *
     * @param string $ip Dirección IP del cliente (REMOTE_ADDR).
     * @return array{permitido: bool, motivo: string, reintentar_en: int}
     *         motivo: 'ok' | 'limite' | 'baneado'. reintentar_en en segundos.
     */
    public function check(string $ip): array
    {
        if (getenv('RATELIMIT_DISABLE') === '1') {
            return ['permitido' => true, 'motivo' => 'ok', 'reintentar_en' => 0];
        }

        $now = microtime(true);
        $file = $this->storagePath . hash('sha256', $ip) . '.json';

        $fh = @fopen($file, 'c+');
        if ($fh === false) {
            // Si el almacenamiento no está disponible, fallar abierto: no tumbar el sitio.
            return ['permitido' => true, 'motivo' => 'ok', 'reintentar_en' => 0];
        }

        flock($fh, LOCK_EX);
        $raw = stream_get_contents($fh);
        $data = json_decode((string)$raw, true);
        if (!is_array($data)) {
            $data = [];
        }

        // 1. Ban activo: rechazar hasta su vencimiento.
        $banHasta = (float)($data['ban_hasta'] ?? 0);
        if ($banHasta > $now) {
            flock($fh, LOCK_UN);
            fclose($fh);
            return [
                'permitido'     => false,
                'motivo'        => 'baneado',
                'reintentar_en' => (int)ceil($banHasta - $now),
            ];
        }

        // 2. Ban expirado: reiniciar todo el estado de la IP.
        if ($banHasta > 0) {
            $data = [];
        }

        // 3. Las violaciones decaen si la IP se comportó bien durante el TTL.
        $violaciones = (int)($data['violaciones'] ?? 0);
        $ultimaViolacion = (float)($data['ultima_violacion'] ?? 0);
        if ($violaciones > 0 && ($now - $ultimaViolacion) > self::VIOLACION_TTL) {
            $violaciones = 0;
        }

        // 4. Registrar el hit y descartar los que salieron de la ventana.
        $hits = array_values(array_filter(
            (array)($data['hits'] ?? []),
            function ($t) use ($now) {
                return ($now - (float)$t) <= $this->ventana;
            }
        ));
        $hits[] = $now;

        // 5. Excede el límite: registrar violación y rechazar con tiempo real de espera.
        if (count($hits) > $this->limite) {
            $violaciones++;
            $reintentar = max(1, (int)ceil($this->ventana - ($now - $hits[0])));
            $banHasta = ($violaciones >= self::MAX_VIOLACIONES) ? ($now + self::BAN_DURACION) : 0;

            $data = [
                'hits'             => array_slice($hits, -$this->limite),
                'violaciones'      => $violaciones,
                'ultima_violacion' => $now,
                'ban_hasta'        => $banHasta,
            ];
            $this->escribir($fh, $data);

            if ($banHasta > 0) {
                return [
                    'permitido'     => false,
                    'motivo'        => 'baneado',
                    'reintentar_en' => self::BAN_DURACION,
                ];
            }
            return [
                'permitido'     => false,
                'motivo'        => 'limite',
                'reintentar_en' => $reintentar,
            ];
        }

        // 6. Dentro del límite: guardar y permitir.
        $data = [
            'hits'             => $hits,
            'violaciones'      => $violaciones,
            'ultima_violacion' => $ultimaViolacion,
            'ban_hasta'        => 0,
        ];
        $this->escribir($fh, $data);

        return ['permitido' => true, 'motivo' => 'ok', 'reintentar_en' => 0];
    }

    /**
     * Escritura atómica dentro del lock ya adquirido.
     */
    private function escribir($fh, array $data): void
    {
        ftruncate($fh, 0);
        rewind($fh);
        fwrite($fh, json_encode($data));
        fflush($fh);
        flock($fh, LOCK_UN);
        fclose($fh);
    }
}
