<?php
/**
 * Almacenamiento privado de tokens Expo para LoveApp.
 * No consulta ni modifica tablas MySQL.
 * Ubicar en: config/PushTokenStore.php
 */

namespace LoveMakeup\Proyecto\Config;

final class PushTokenStore
{
    private const FILE_NAME = 'expo_tokens.json';
    private const LOCK_NAME = '.expo_tokens.lock';

    private static function directory(): string
    {
        // El directorio debe estar FUERA de la raiz publica del servidor web.
        $webRoot = realpath($_SERVER['DOCUMENT_ROOT'] ?? '');
        if ($webRoot === false || !is_dir($webRoot)) {
            throw new \RuntimeException('No se pudo determinar DOCUMENT_ROOT.');
        }

        $configured = getenv('LOVEAPP_PUSH_STORE_DIR');
        $location = ($configured !== false && trim($configured) !== '')
            ? rtrim($configured, '/\\')
            : dirname($webRoot) . DIRECTORY_SEPARATOR . 'loveapp_private_tokens';

        if (!is_dir($location) && !@mkdir($location, 0700, true) && !is_dir($location)) {
            throw new \RuntimeException('No se pudo crear el directorio privado de tokens.');
        }

        $directory = realpath($location);
        if ($directory === false) {
            throw new \RuntimeException('Directorio de tokens no disponible.');
        }

        $rootNormalized = rtrim(str_replace('\\', '/', $webRoot), '/');
        $dirNormalized = rtrim(str_replace('\\', '/', $directory), '/');
        $caseInsensitive = DIRECTORY_SEPARATOR === '\\';
        if ($caseInsensitive) {
            $rootNormalized = strtolower($rootNormalized);
            $dirNormalized = strtolower($dirNormalized);
        }
        if ($dirNormalized === $rootNormalized || str_starts_with($dirNormalized, $rootNormalized . '/')) {
            throw new \RuntimeException('El almacenamiento debe ubicarse fuera de la raiz publica.');
        }

        return $directory;
    }

    private static function readRegistry(string $filename): array
    {
        if (!is_file($filename)) {
            return [];
        }
        $content = @file_get_contents($filename);
        if ($content === false) {
            throw new \RuntimeException('No se pudo leer el registro de tokens.');
        }
        $decoded = json_decode($content, true, 32, JSON_THROW_ON_ERROR);
        if (!is_array($decoded)) {
            throw new \RuntimeException('Registro de tokens invalido.');
        }
        return $decoded;
    }

    private static function withLock(bool $exclusive, callable $action)
    {
        $dir = self::directory();
        $lockPath = $dir . DIRECTORY_SEPARATOR . self::LOCK_NAME;
        $lock = @fopen($lockPath, 'c');
        if ($lock === false) {
            throw new \RuntimeException('No se pudo abrir el bloqueo de tokens.');
        }
        @chmod($lockPath, 0600);
        try {
            if (!flock($lock, $exclusive ? LOCK_EX : LOCK_SH)) {
                throw new \RuntimeException('No se pudo bloquear el registro de tokens.');
            }
            try {
                return $action($dir . DIRECTORY_SEPARATOR . self::FILE_NAME, $dir);
            } finally {
                flock($lock, LOCK_UN);
            }
        } finally {
            fclose($lock);
        }
    }

    public static function saveToken(string $cedula, string $token): void
    {
        if (!preg_match('/^[0-9]{6,12}$/D', $cedula) ||
            !preg_match('/^(?:ExponentPushToken|ExpoPushToken)\[[A-Za-z0-9_-]+\]$/D', $token)) {
            throw new \InvalidArgumentException('Datos de token invalidos.');
        }

        self::withLock(true, static function (string $filename, string $dir) use ($cedula, $token): void {
            $registry = self::readRegistry($filename);
            // La clave es el hash del token: permite varios dispositivos por cliente
            // y reasigna un mismo dispositivo cuando inicia sesion otro cliente.
            $registry[hash('sha256', $token)] = [
                'cedula' => $cedula,
                'token' => $token,
                'updated_at' => gmdate('c'),
            ];

            $json = json_encode($registry, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_THROW_ON_ERROR);
            $temporary = @tempnam($dir, '.expo_tmp_');
            if ($temporary === false) {
                throw new \RuntimeException('No se pudo crear archivo temporal de tokens.');
            }
            try {
                @chmod($temporary, 0600);
                if (file_put_contents($temporary, $json) !== strlen($json)) {
                    throw new \RuntimeException('No se pudo guardar el registro de tokens.');
                }
                if (!@rename($temporary, $filename)) {
                    throw new \RuntimeException('No se pudo actualizar el registro de tokens.');
                }
                @chmod($filename, 0600);
            } finally {
                if (file_exists($temporary)) {
                    @unlink($temporary);
                }
            }
        });
    }

    /** @return string[] */
    public static function getTokensForCedula(string $cedula): array
    {
        if (!preg_match('/^[0-9]{6,12}$/D', $cedula)) {
            return [];
        }
        return self::withLock(false, static function (string $filename) use ($cedula): array {
            $registry = self::readRegistry($filename);
            $tokens = [];
            foreach ($registry as $record) {
                if (is_array($record) && ($record['cedula'] ?? null) === $cedula &&
                    is_string($record['token'] ?? null)) {
                    $tokens[] = $record['token'];
                }
            }
            return array_values(array_unique($tokens));
        });
    }
}
