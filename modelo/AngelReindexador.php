<?php

namespace LoveMakeup\Proyecto\Modelo;

use Dotenv\Dotenv;
use RuntimeException;

class AngelReindexador
{
    public static function reindexar(
        string $tipo,
        ?int $id = null,
        ?array $ids = null,
        bool $soloPendientes = false
    ): array {
        $raiz = dirname(__DIR__);
        require_once $raiz . '/vendor/autoload.php';
        Dotenv::createImmutable($raiz, 'passconfig.env')->safeLoad();

        $clavePath = $_ENV['ANGEL_PRIVATE_KEY_PATH'] ?? '';
        if ($clavePath === '') {
            throw new RuntimeException('Falta configurar ANGEL_PRIVATE_KEY_PATH en passconfig.env.');
        }
        if (!is_file($clavePath)) {
            $clavePath = $raiz . DIRECTORY_SEPARATOR . ltrim($clavePath, '\\/');
        }
        $claveContenido = @file_get_contents($clavePath);
        $clavePrivada = $claveContenido === false
            ? false
            : openssl_pkey_get_private(
                $claveContenido,
                $_ENV['ANGEL_PRIVATE_KEY_PASSPHRASE'] ?? ''
            );
        if ($clavePrivada === false) {
            throw new RuntimeException('No se pudo cargar la clave privada RS256 de Ángel.');
        }

        $url = self::obtenerUrlEndpoint();
        $ahora = time();
        $codificar = static function (string $valor): string {
            return rtrim(strtr(base64_encode($valor), '+/', '-_'), '=');
        };
        $cabecera = $codificar(json_encode(
            ['alg' => 'RS256', 'typ' => 'JWT'],
            JSON_THROW_ON_ERROR
        ));
        $carga = $codificar(json_encode([
            'sub' => (string) ($_SESSION['id'] ?? 'administrador'),
            'iat' => $ahora,
            'exp' => $ahora + 300,
            'scope' => 'angel:reindex',
        ], JSON_THROW_ON_ERROR));
        $entradaFirma = $cabecera . '.' . $carga;
        if (!openssl_sign($entradaFirma, $firma, $clavePrivada, OPENSSL_ALGO_SHA256)) {
            throw new RuntimeException('No se pudo firmar el token de reindexación de Ángel.');
        }
        openssl_free_key($clavePrivada);

        $solicitud = ['tipo' => $tipo];
        if ($id !== null) {
            $solicitud['id'] = $id;
        }
        if ($ids !== null) {
            $solicitud['ids'] = $ids;
        }
        if ($soloPendientes) {
            $solicitud['solo_pendientes'] = true;
        }

        $curl = curl_init($url);
        if ($curl === false) {
            throw new RuntimeException('No se pudo iniciar la conexión con Ángel.');
        }
        set_time_limit(320);
        curl_setopt_array($curl, [
            CURLOPT_POST => true,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_CONNECTTIMEOUT => 10,
            CURLOPT_TIMEOUT => 300,
            CURLOPT_HTTPHEADER => [
                'Authorization: Bearer ' . $entradaFirma . '.' . $codificar($firma),
                'Content-Type: application/json',
                'Accept: application/json',
            ],
            CURLOPT_POSTFIELDS => json_encode($solicitud, JSON_THROW_ON_ERROR),
        ]);
        $respuesta = curl_exec($curl);
        $errorCurl = curl_error($curl);
        $codigoHttp = (int) curl_getinfo($curl, CURLINFO_HTTP_CODE);
        curl_close($curl);

        if ($respuesta === false) {
            throw new RuntimeException(
                'No se pudo conectar con Ángel' . ($errorCurl !== '' ? ': ' . $errorCurl : '.')
            );
        }
        $datos = json_decode($respuesta, true);
        if ($codigoHttp < 200 || $codigoHttp >= 300 || !is_array($datos)) {
            $mensaje = is_array($datos)
                ? ($datos['detail'] ?? $datos['mensaje'] ?? 'Respuesta no válida de Ángel.')
                : 'Respuesta no válida de Ángel.';
            if (is_array($mensaje)) {
                $mensaje = $mensaje['mensaje'] ?? json_encode($mensaje, JSON_UNESCAPED_UNICODE);
            }
            throw new RuntimeException(
                'Ángel respondió HTTP ' . $codigoHttp . ': ' . $mensaje
            );
        }

        return $datos;
    }

    private static function obtenerUrlEndpoint(): string
    {
        $urlConfigurada = trim($_ENV['ANGEL_REINDEX_URL'] ?? '');
        if ($urlConfigurada !== '') {
            return $urlConfigurada;
        }

        $urlChat = trim($_ENV['ANGEL_API_URL'] ?? '');
        if ($urlChat === '') {
            throw new RuntimeException('Falta configurar ANGEL_API_URL en passconfig.env.');
        }
        if (preg_match('~/api/(?:chat|reindexar)/?$~', $urlChat)) {
            return preg_replace('~/api/(?:chat|reindexar)/?$~', '/api/reindexar', $urlChat);
        }
        return rtrim($urlChat, '/') . '/api/reindexar';
    }
}
