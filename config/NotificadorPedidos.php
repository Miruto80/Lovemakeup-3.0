<?php
/**
 * Notificador interno de pedidos web para LoveApp.
 * Se llama SOLO despues de que el modelo confirma el cambio en MySQL.
 * No tiene endpoint publico, ni altera pedidos ni datos de MySQL.
 * Comparte historial y lock con controlador/api/revisarEstadosPush.php.
 */
namespace LoveMakeup\Proyecto\Config;

final class NotificadorPedidos
{
    private const HISTORIAL = 'pedido_estados_push.json';
    private const LOCK = '.push_monitor.lock';

    /** Devuelve el resultado del intento sin lanzar errores hacia Pedidos Web. */
    public static function notificarDespuesDeCambio(int $idPedido): array
    {
        if ($idPedido <= 0) {
            return ['exitoso' => false, 'motivo' => 'id_invalido'];
        }

        $bloqueo = null;
        try {
            self::cargarDependencias();
            $directorio = self::directorioPrivado();
            $bloqueo = @fopen($directorio . DIRECTORY_SEPARATOR . self::LOCK, 'c');
            if ($bloqueo === false) {
                return ['exitoso' => false, 'motivo' => 'bloqueo_no_disponible'];
            }
            @chmod($directorio . DIRECTORY_SEPARATOR . self::LOCK, 0600);
            // El monitor utiliza el mismo bloqueo. Nunca leer/enviar/escribir sin adquirirlo.
            if (!flock($bloqueo, LOCK_EX | LOCK_NB)) {
                return ['exitoso' => false, 'motivo' => 'monitor_ocupado'];
            }

            // Mantener el bloqueo durante TODO el proceso para no competir con el cron.
            $archivo = $directorio . DIRECTORY_SEPARATOR . self::HISTORIAL;
            $historial = self::leerHistorial($archivo);
            $conexion = new class extends \LoveMakeup\Proyecto\Config\Conexion {
                public function readOnlyPdo(): \PDO { return $this->getConex1(); }
            };
            $pdo = $conexion->readOnlyPdo();
            $consulta = $pdo->prepare(
                'SELECT id_pedido, cedula, estatus FROM pedido WHERE id_pedido = ? AND tipo = 2'
            );
            $consulta->execute([$idPedido]);
            $pedido = $consulta->fetch(\PDO::FETCH_ASSOC);
            if (!is_array($pedido)) {
                return ['exitoso' => false, 'motivo' => 'pedido_no_elegible'];
            }

            $estado = (string) ($pedido['estatus'] ?? '');
            $id = (string) $idPedido;
            // Son los estados que realmente generan confirmar/enviar/entregar.
            if (!in_array($estado, ['2', '3', '4', '5'], true)) {
                return ['exitoso' => false, 'motivo' => 'estado_no_notificable'];
            }
            if (array_key_exists($id, $historial) && (string) $historial[$id] === $estado) {
                return ['exitoso' => false, 'motivo' => 'estado_ya_registrado'];
            }

            $cedula = (string) ($pedido['cedula'] ?? '');
            $tokens = PushTokenStore::getTokensForCedula($cedula);
            if ($tokens === []) {
                // Sin tokens no hubo entrega. El monitor conserva su logica original.
                return ['exitoso' => false, 'motivo' => 'sin_tokens'];
            }

            if (!self::enviarAExpo($tokens, $id, $estado)) {
                // NO avanzar el historial si Expo falla: permite un intento posterior.
                return ['exitoso' => false, 'motivo' => 'expo_no_acepto'];
            }

            // Mismo formato que el monitor: { "123": "4", "124": "3" }.
            // Conserva los demas pedidos y escribe bajo el mismo bloqueo.
            $historial[$id] = $estado;
            self::guardarHistorial($archivo, $directorio, $historial);
            return ['exitoso' => true, 'motivo' => 'aceptado_por_expo'];
        } catch (\Throwable $e) {
            error_log('LoveApp Push: fallo del notificador (' . get_class($e) . ')');
            return ['exitoso' => false, 'motivo' => 'error_interno'];
        } finally {
            if (is_resource($bloqueo)) {
                // flock se libera tambien al cerrar, pero hacerlo explicito.
                flock($bloqueo, LOCK_UN);
                fclose($bloqueo);
            }
        }
    }

    private static function cargarDependencias(): void
    {
        $raiz = dirname(__DIR__);
        $autoload = $raiz . '/vendor/autoload.php';
        if (is_file($autoload)) {
            require_once $autoload;
        }
        require_once __DIR__ . '/PushTokenStore.php';
        $configEnv = $raiz . '/passconfig.env';
        if (is_file($configEnv) && class_exists(\Dotenv\Dotenv::class)) {
            \Dotenv\Dotenv::createImmutable($raiz, 'passconfig.env')->safeLoad();
        }
    }

    private static function directorioPrivado(): string
    {
        $docroot = realpath($_SERVER['DOCUMENT_ROOT'] ?? '');
        if ($docroot === false) {
            throw new \RuntimeException('DOCUMENT_ROOT no disponible');
        }
        $setting = getenv('LOVEAPP_PUSH_STORE_DIR');
        $dir = ($setting !== false && trim($setting) !== '')
            ? rtrim($setting, '/\\')
            : dirname($docroot) . DIRECTORY_SEPARATOR . 'loveapp_private_tokens';
        if (!is_dir($dir) && !@mkdir($dir, 0700, true) && !is_dir($dir)) {
            throw new \RuntimeException('Almacenamiento privado no disponible');
        }
        $real = realpath($dir);
        if ($real === false) {
            throw new \RuntimeException('Directorio privado invalido');
        }
        // Idem al monitor: JAMAS guardar un estado bajo la raiz publica.
        $root = rtrim(str_replace('\\', '/', $docroot), '/');
        $realNorm = rtrim(str_replace('\\', '/', $real), '/');
        if (DIRECTORY_SEPARATOR === '\\') {
            $root = strtolower($root);
            $realNorm = strtolower($realNorm);
        }
        if ($realNorm === $root || str_starts_with($realNorm, $root . '/')) {
            throw new \RuntimeException('Almacenamiento no es privado');
        }
        return $real;
    }

    private static function leerHistorial(string $archivo): array
    {
        if (!is_file($archivo)) {
            return [];
        }
        $json = @file_get_contents($archivo);
        if ($json === false) {
            throw new \RuntimeException('Error leyendo historial');
        }
        $historial = json_decode($json, true, 32, JSON_THROW_ON_ERROR);
        if (!is_array($historial)) {
            throw new \RuntimeException('Formato historial invalido');
        }
        foreach ($historial as $estado) {
            if (!is_string($estado) && !is_int($estado)) {
                throw new \RuntimeException('Historial incompatible; no sobrescribir');
            }
        }
        return $historial;
    }

    private static function guardarHistorial(string $archivo, string $dir, array $historial): void
    {
        $json = json_encode($historial, JSON_UNESCAPED_SLASHES | JSON_THROW_ON_ERROR);
        $temporal = @tempnam($dir, '.push_estado_');
        if ($temporal === false) {
            throw new \RuntimeException('Error creando archivo temporal');
        }
        try {
            @chmod($temporal, 0600);
            if (file_put_contents($temporal, $json) !== strlen($json)) {
                throw new \RuntimeException('Error escribiendo historial temporal');
            }
            if (!@rename($temporal, $archivo)) {
                throw new \RuntimeException('Error guardando historial');
            }
            @chmod($archivo, 0600);
        } finally {
            if (is_file($temporal)) {
                @unlink($temporal);
            }
        }
    }

    private static function mensajes(array $tokens, string $id, string $estado): array
    {
        $labels = [
            '2' => 'confirmado',
            '3' => 'en preparacion para delivery',
            '4' => 'en camino',
            '5' => 'entregado',
        ];
        $mensajes = [];
        foreach (array_values(array_unique($tokens)) as $token) {
            if (!is_string($token) || $token === '') {
                continue;
            }
            $mensajes[] = [
                'to' => $token,
                'title' => 'LoveMakeup - Actualizacion de pedido',
                'body' => 'Tu pedido #' . $id . ' esta ' . $labels[$estado] . '.',
                'priority' => 'high',
                'channelId' => 'loveapp.default',
                'data' => ['orderId' => $id, 'status' => $estado],
            ];
        }
        return $mensajes;
    }

    private static function enviarAExpo(array $tokens, string $id, string $estado): bool
    {
        $mensajes = self::mensajes($tokens, $id, $estado);
        if ($mensajes === []) {
            return false;
        }
        foreach (array_chunk($mensajes, 100) as $lote) {
            // Expo espera un array PLANO de mensajes en CADA request.
            $payload = json_encode($lote, JSON_THROW_ON_ERROR);
            $url = 'https://exp.host/--/api/v2/push/send';
            $httpCode = 0;
            $response = false;

            if (function_exists('curl_init')) {
                $curl = curl_init($url);
                if ($curl === false) {
                    return false;
                }
                try {
                    curl_setopt_array($curl, [
                        CURLOPT_POST => true,
                        CURLOPT_HTTPHEADER => ['Content-Type: application/json', 'Accept: application/json'],
                        CURLOPT_POSTFIELDS => $payload,
                        CURLOPT_RETURNTRANSFER => true,
                        CURLOPT_CONNECTTIMEOUT => 3,
                        CURLOPT_TIMEOUT => 7,
                    ]);
                    $response = curl_exec($curl);
                    $httpCode = (int) curl_getinfo($curl, CURLINFO_RESPONSE_CODE);
                } finally {
                    curl_close($curl);
                }
            } else {
                $context = stream_context_create([
                    'http' => [
                        'method' => 'POST',
                        'header' => "Content-Type: application/json\r\nAccept: application/json\r\n",
                        'content' => $payload,
                        'timeout' => 7,
                        'ignore_errors' => true,
                    ],
                    'ssl' => [
                        'verify_peer' => true,
                        'verify_peer_name' => true,
                    ],
                ]);
                $response = @file_get_contents($url, false, $context);
                foreach ($http_response_header ?? [] as $header) {
                    if (preg_match('/^HTTP\/\S+\s+(\d{3})/', $header, $matches)) {
                        $httpCode = (int) $matches[1];
                    }
                }
            }
            if ($response === false || $httpCode < 200 || $httpCode >= 300) {
                return false;
            }
            $res = json_decode($response, true);
            if (!is_array($res) || !isset($res['data']) || !is_array($res['data'])
                || count($res['data']) !== count($lote)) {
                return false;
            }
            foreach ($res['data'] as $ticket) {
                if (!is_array($ticket) || ($ticket['status'] ?? '') !== 'ok') {
                    return false;
                }
            }
        }
        // ticket OK significa aceptacion por Expo, NO entrega confirmada al telefono.
        return true;
    }
}
