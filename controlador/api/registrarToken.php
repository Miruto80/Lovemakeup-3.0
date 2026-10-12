<?php
/**
 * API de registro de token Expo para LoveApp.
 * Ubicar en: controlador/api/registrarToken.php
 * Guarda tokens FUERA del directorio publico, sin MySQL.
 */

header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(204);
    exit;
}

function tokenResponse(int $status, string $message, int $success = 0): void
{
    http_response_code($status);
    echo json_encode(['respuesta' => $success, 'mensaje' => $message]);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Allow: POST, OPTIONS');
    tokenResponse(405, 'Metodo no permitido.');
}

function decodeJwtPart(string $part): ?string
{
    if (!preg_match('/^[A-Za-z0-9_-]+$/D', $part)) {
        return null;
    }
    $data = base64_decode(strtr($part, '-_', '+/') . str_repeat('=', (4 - strlen($part) % 4) % 4), true);
    return $data === false ? null : $data;
}

function verifySessionToken(string $jwt, string $publicKey): ?array
{
    $parts = explode('.', $jwt);
    if (count($parts) !== 3) {
        return null;
    }

    $headerJson = decodeJwtPart($parts[0]);
    $payloadJson = decodeJwtPart($parts[1]);
    $signature = decodeJwtPart($parts[2]);
    if ($headerJson === null || $payloadJson === null || $signature === null) {
        return null;
    }
    $header = json_decode($headerJson, true);
    $claims = json_decode($payloadJson, true);
    if (!is_array($header) || ($header['alg'] ?? null) !== 'RS256' || !is_array($claims)) {
        return null;
    }
    if (!isset($claims['exp']) || !is_numeric($claims['exp']) || time() >= (int) $claims['exp']) {
        return null;
    }
    if (isset($claims['nbf']) && time() < (int) $claims['nbf']) {
        return null;
    }
    $key = openssl_pkey_get_public($publicKey);
    if ($key === false) {
        return null;
    }
    if (openssl_verify($parts[0] . '.' . $parts[1], $signature, $key, OPENSSL_ALGO_SHA256) !== 1) {
        return null;
    }
    return $claims;
}

$authHeader = $_SERVER['HTTP_AUTHORIZATION'] ?? $_SERVER['REDIRECT_HTTP_AUTHORIZATION'] ?? '';
if ($authHeader === '' && function_exists('getallheaders')) {
    foreach (getallheaders() as $name => $value) {
        if (strcasecmp($name, 'Authorization') === 0) {
            $authHeader = $value;
            break;
        }
    }
}
if (!preg_match('/^Bearer\s+(\S+)$/i', trim($authHeader), $matches)) {
    tokenResponse(401, 'Falta el token de sesion.');
}

$publicKeyPath = __DIR__ . '/../../config/jwt_public.pem';
$publicKey = @file_get_contents($publicKeyPath);
if ($publicKey === false) {
    error_log('registrarToken: no se pudo abrir jwt_public.pem');
    tokenResponse(500, 'Error de configuracion del servidor.');
}
$claims = verifySessionToken($matches[1], $publicKey);
if ($claims === null) {
    tokenResponse(401, 'La sesion no es valida o ha expirado.');
}

// El JWT de login.php identifica al cliente mediante el claim sub.
$cedula = isset($claims['sub']) && (is_string($claims['sub']) || is_int($claims['sub']))
    ? (string) $claims['sub'] : '';
if (!preg_match('/^[0-9]{6,12}$/D', $cedula)) {
    tokenResponse(403, 'No se pudo identificar al cliente.');
}

$body = file_get_contents('php://input');
$payload = is_string($body) ? json_decode($body, true) : null;
$token = is_array($payload) ? ($payload['expoPushToken'] ?? null) : null;
if (!is_string($token)) {
    tokenResponse(400, 'Falta el token de notificaciones.');
}
$token = trim($token);
if (!preg_match('/^(?:ExponentPushToken|ExpoPushToken)\[[A-Za-z0-9_-]+\]$/D', $token)) {
    tokenResponse(400, 'Formato de token Expo invalido.');
}

require_once __DIR__ . '/../../config/PushTokenStore.php';
try {
    \LoveMakeup\Proyecto\Config\PushTokenStore::saveToken($cedula, $token);
} catch (\Throwable $error) {
    error_log('registrarToken: no se pudo guardar token: ' . $error->getMessage());
    tokenResponse(500, 'No se pudo registrar el dispositivo. Verifique el almacenamiento privado del servidor.');
}

tokenResponse(200, 'Dispositivo registrado para notificaciones.', 1);
