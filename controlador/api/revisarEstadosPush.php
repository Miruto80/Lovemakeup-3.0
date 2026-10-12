<?php
/**
 * Monitor independiente para notificaciones LoveApp (solo lectura de MySQL).
 * No requiere cron de Hostinger ni modificaciones de los modulos de pedidos.
 * El disparador previsto es un GitHub Action programado.
 */
declare(strict_types=1);

header('Content-Type: application/json; charset=utf-8');
header('Cache-Control: no-store');
header('X-Robots-Tag: noindex, nofollow');

function respond(int $code, array $data): never {
    http_response_code($code);
    echo json_encode($data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Allow: POST');
    respond(405, ['ok' => false, 'error' => 'Metodo no permitido']);
}

// Solo se publica el SHA-256 de una clave aleatoria de 256 bits.
const AUTH_KEY_SHA256 = '5af21847421e89fb3766b6a5b53e3addd211ffa56e39bab82ca9280e3699bd92';
$authorization = $_SERVER['HTTP_AUTHORIZATION'] ?? $_SERVER['REDIRECT_HTTP_AUTHORIZATION'] ?? '';
if ($authorization === '' && function_exists('getallheaders')) {
    foreach (getallheaders() as $header => $value) {
        if (strcasecmp($header, 'Authorization') === 0) {
            $authorization = (string)$value;
            break;
        }
    }
}
if (!preg_match('/^Bearer ([a-f0-9]{64})$/D', trim($authorization), $match) ||
    !hash_equals(AUTH_KEY_SHA256, hash('sha256', $match[1]))) {
    respond(401, ['ok' => false, 'error' => 'No autorizado']);
}

require_once __DIR__ . '/../../vendor/autoload.php';
require_once __DIR__ . '/../../config/PushTokenStore.php';

// Mismo archivo de configuracion usado por el modelo PedidoWeb, si existe.
$projectRoot = dirname(__DIR__, 2);
if (is_file($projectRoot . '/passconfig.env') && class_exists(\Dotenv\Dotenv::class)) {
    \Dotenv\Dotenv::createImmutable($projectRoot, 'passconfig.env')->safeLoad();
}

function privateStateDirectory(): string {
    $docroot = realpath($_SERVER['DOCUMENT_ROOT'] ?? '');
    if ($docroot === false) {
        throw new RuntimeException('No se pudo obtener DOCUMENT_ROOT');
    }
    $setting = getenv('LOVEAPP_PUSH_STORE_DIR');
    $dir = ($setting !== false && trim($setting) !== '')
        ? rtrim($setting, '/\\')
        : dirname($docroot) . DIRECTORY_SEPARATOR . 'loveapp_private_tokens';
    if (!is_dir($dir) && !@mkdir($dir, 0700, true) && !is_dir($dir)) {
        throw new RuntimeException('No se pudo crear almacenamiento privado');
    }
    $real = realpath($dir);
    if ($real === false) {
        throw new RuntimeException('Almacenamiento no disponible');
    }
    $normalizedRoot = rtrim(str_replace('\\', '/', $docroot), '/');
    $normalizedDir = rtrim(str_replace('\\', '/', $real), '/');
    if (DIRECTORY_SEPARATOR === '\\') {
        $normalizedRoot = strtolower($normalizedRoot);
        $normalizedDir = strtolower($normalizedDir);
    }
    if ($normalizedDir === $normalizedRoot || str_starts_with($normalizedDir, $normalizedRoot . '/')) {
        throw new RuntimeException('Almacenamiento ubicado en carpeta publica');
    }
    return $real;
}

function readStatuses(string $filename): ?array {
    if (!file_exists($filename)) {
        return null;  // Primera ejecucion: linea base sin enviar avisos antiguos.
    }
    $json = @file_get_contents($filename);
    if ($json === false) {
        throw new RuntimeException('No se pudo leer el historial de estados');
    }
    $parsed = json_decode($json, true, 32, JSON_THROW_ON_ERROR);
    if (!is_array($parsed)) {
        throw new RuntimeException('Historial invalido');
    }
    return $parsed;
}

function writeStatuses(string $filename, string $dir, array $statuses): void {
    $encoded = json_encode($statuses, JSON_UNESCAPED_SLASHES | JSON_THROW_ON_ERROR);
    $temp = @tempnam($dir, '.push_estado_');
    if ($temp === false) {
        throw new RuntimeException('Error al preparar historial');
    }
    try {
        @chmod($temp, 0600);
        if (file_put_contents($temp, $encoded) !== strlen($encoded)) {
            throw new RuntimeException('No se pudo escribir historial');
        }
        if (!@rename($temp, $filename)) {
            throw new RuntimeException('No se pudo actualizar historial');
        }
        @chmod($filename, 0600);
    } finally {
        if (is_file($temp)) {
            @unlink($temp);
        }
    }
}

function statusLabel(string $status): string {
    return match ($status) {
        '0', 'cancelado' => 'cancelado',
        '1', 'pendiente' => 'pendiente de verificacion',
        '2' => 'confirmado',
        '3' => 'en preparacion para delivery',
        '4', 'en_camino' => 'en camino',
        '5', 'entregado' => 'entregado',
        default => 'actualizado',
    };
}

/** Devuelve true solo si Expo acepta todos los mensajes del pedido. */
function sendPush(array $tokens, string $orderId, string $status): bool {
    $messages = [];
    foreach (array_values(array_unique($tokens)) as $token) {
        $messages[] = [
            'to' => $token,
            'title' => 'LoveMakeup - Actualizacion de pedido',
            'body' => 'Tu pedido #' . $orderId . ' esta ' . statusLabel($status) . '.',
            'priority' => 'high',
            'channelId' => 'loveapp.default',
            'data' => ['orderId' => $orderId, 'status' => $status],
        ];
    }

    foreach (array_chunk($messages, 100) as $batch) {
        $payload = json_encode($batch, JSON_THROW_ON_ERROR);
        $httpCode = 0;
        $response = false;
        $transportError = '';
        $url = 'https://exp.host/--/api/v2/push/send';

        // Preferir cURL si esta disponible. En hostings sin la extension,
        // usar el transporte HTTPS de PHP sin cambiar modulos existentes.
        if (function_exists('curl_init')) {
            $curl = curl_init($url);
            if ($curl === false) {
                $transportError = 'curl_init no disponible';
            } else {
                try {
                    curl_setopt_array($curl, [
                        CURLOPT_POST => true,
                        CURLOPT_HTTPHEADER => ['Content-Type: application/json', 'Accept: application/json'],
                        CURLOPT_POSTFIELDS => $payload,
                        CURLOPT_RETURNTRANSFER => true,
                        CURLOPT_CONNECTTIMEOUT => 8,
                        CURLOPT_TIMEOUT => 20,
                    ]);
                    $response = curl_exec($curl);
                    $httpCode = (int)curl_getinfo($curl, CURLINFO_RESPONSE_CODE);
                    $transportError = curl_error($curl);
                } finally {
                    curl_close($curl);
                }
            }
        } else {
            $context = stream_context_create([
                'http' => [
                    'method' => 'POST',
                    'header' => "Content-Type: application/json\r\nAccept: application/json\r\n",
                    'content' => $payload,
                    'timeout' => 20,
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
                    $httpCode = (int)$matches[1];
                    break;
                }
            }
            if ($response === false) {
                $transportError = 'Transporte HTTPS no disponible';
            }
        }

        if ($response === false || $httpCode < 200 || $httpCode >= 300) {
            error_log('LoveApp Push: envio Expo rechazado o fallido (HTTP ' . $httpCode . '): ' . $transportError);
            return false;
        }
        $parsed = json_decode($response, true);
        if (!is_array($parsed) || !isset($parsed['data']) || !is_array($parsed['data'])) {
            error_log('LoveApp Push: respuesta inesperada de Expo');
            return false;
        }
        foreach ($parsed['data'] as $ticket) {
            if (!is_array($ticket) || ($ticket['status'] ?? '') !== 'ok') {
                error_log('LoveApp Push: Expo rechazo un mensaje para pedido ' . $orderId);
                return false;
            }
        }
    }
    return true;
}

$diagnosticStage = 'storage_directory';
try {
    $dir = privateStateDirectory();
    $diagnosticStage = 'monitor_lock';
    $lockHandle = @fopen($dir . '/.push_monitor.lock', 'c');
    if (!$lockHandle) {
        throw new RuntimeException('No se pudo abrir archivo de bloqueo');
    }
    @chmod($dir . '/.push_monitor.lock', 0600);
    if (!flock($lockHandle, LOCK_EX | LOCK_NB)) {
        fclose($lockHandle);
        respond(409, ['ok' => false, 'error' => 'Otra ejecucion en curso']);
    }

    try {
        $storage = $dir . '/pedido_estados_push.json';
        $diagnosticStage = 'read_previous_statuses';
        $previous = readStatuses($storage);

        // Misma conexion de lectura utilizada por el modelo existente.
        $diagnosticStage = 'database_connection';
        $connection = new class extends \LoveMakeup\Proyecto\Config\Conexion {
            public function readOnlyPdo(): \PDO {
                return $this->getConex1();
            }
        };
        $pdo = $connection->readOnlyPdo();
        $diagnosticStage = 'read_orders';
        $query = $pdo->query('SELECT id_pedido, cedula, estatus FROM pedido WHERE tipo = 2');
        if ($query === false) {
            throw new RuntimeException('No se pudo consultar pedidos web');
        }
        $orders = $query->fetchAll(\PDO::FETCH_ASSOC);
        $latest = [];
        $details = [];
        foreach ($orders as $order) {
            $id = (string)$order['id_pedido'];
            $latest[$id] = (string)$order['estatus'];
            $details[$id] = (string)($order['cedula'] ?? '');
        }

        if ($previous === null) {
            $diagnosticStage = 'write_baseline';
            writeStatuses($storage, $dir, $latest);
            respond(200, ['ok' => true, 'baseline' => true, 'orders' => count($latest), 'sent' => 0]);
        }

        $sent = 0;
        $changed = 0;
        $failures = 0;
        $updated = $previous;
        foreach ($latest as $id => $status) {
            if (!array_key_exists($id, $previous)) {
                // Nuevo pedido: no enviar cambio de estado ficticio.
                $updated[$id] = $status;
                continue;
            }
            if ((string)$previous[$id] === $status) {
                continue;
            }
            $changed++;
            $cedula = $details[$id];
            $diagnosticStage = 'lookup_device_tokens';
            $tokens = \LoveMakeup\Proyecto\Config\PushTokenStore::getTokensForCedula($cedula);
            if ($tokens === []) {
                // Sin telefono asociado: se registra el nuevo estado sin enviar.
                $updated[$id] = $status;
                continue;
            }
            $diagnosticStage = 'send_to_expo';
            if (sendPush($tokens, (string) $id, $status)) {
                $sent += count($tokens);
                $updated[$id] = $status;
            } else {
                // Reintento en siguiente ejecucion.
                $failures++;
            }
        }
        // Quitar del historial los pedidos que ya no aparecen en la consulta.
        $updated = array_intersect_key($updated, $latest);
        $diagnosticStage = 'write_updated_statuses';
        writeStatuses($storage, $dir, $updated);
        respond($failures > 0 ? 503 : 200, [
            'ok' => $failures === 0,
            'baseline' => false,
            'changed' => $changed,
            'sent' => $sent,
            'failed' => $failures,
        ]);
    } finally {
        flock($lockHandle, LOCK_UN);
        fclose($lockHandle);
    }
} catch (\Throwable $e) {
    // El mensaje detallado solo se guarda en el log privado de PHP.
    // GitHub Actions recibe la etapa y el tipo de error, sin rutas, tokens ni credenciales.
    error_log('LoveApp push watcher [' . $diagnosticStage . '] (' . get_class($e) . '): ' . $e->getMessage());
    respond(500, [
        'ok' => false,
        'error' => 'Fallo interno del monitor',
        'stage' => $diagnosticStage,
        'exception_type' => (new \ReflectionClass($e))->getShortName(),
    ]);
}
