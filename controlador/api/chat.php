<?php
declare(strict_types=1);

header('Content-Type: application/json; charset=utf-8');
header('Cache-Control: no-store');
header('X-Content-Type-Options: nosniff');

function responderChat(int $status, array $data): never
{
    http_response_code($status);
    echo json_encode($data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Allow: POST');
    responderChat(405, ['detail' => 'Método no permitido.']);
}

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/../../vendor/autoload.php';

$projectRoot = dirname(__DIR__, 2);
\Dotenv\Dotenv::createImmutable($projectRoot, 'passconfig.env')->safeLoad();

$limiter = new \Seguridad\FileRateLimiter(10, 60, __DIR__ . '/../../seguridad/data/chat');
$resultadoLimiter = $limiter->check($_SERVER['REMOTE_ADDR'] ?? '127.0.0.1');
if (!$resultadoLimiter['permitido']) {
    $reintentarEn = max(1, (int) $resultadoLimiter['reintentar_en']);
    http_response_code(429);
    header('Retry-After: ' . $reintentarEn);
    responderChat(429, ['detail' => 'Demasiadas consultas. Inténtalo de nuevo más tarde.']);
}

$contentLength = (int) ($_SERVER['CONTENT_LENGTH'] ?? 0);
if ($contentLength > 16 * 1024 * 1024) {
    responderChat(413, ['detail' => 'La solicitud supera el tamaño permitido.']);
}

$requestBody = file_get_contents('php://input');
if ($requestBody === false) {
    responderChat(400, ['detail' => 'No se pudo leer la solicitud.']);
}
try {
    $request = json_decode($requestBody, true, 32, JSON_THROW_ON_ERROR);
} catch (JsonException $exception) {
    responderChat(400, ['detail' => 'La solicitud no contiene JSON válido.']);
}

if (!is_array($request)) {
    responderChat(400, ['detail' => 'Formato de solicitud inválido.']);
}

$mensaje = $request['mensaje'] ?? null;
$historial = $request['historial'] ?? [];
$imagenes = $request['imagenes'] ?? [];

if (!is_string($mensaje) || trim($mensaje) === '' || strlen($mensaje) > 4000) {
    responderChat(400, ['detail' => 'El mensaje está vacío o supera el máximo permitido.']);
}
if (!is_array($historial) || count($historial) > 12) {
    responderChat(400, ['detail' => 'El historial de conversación no es válido.']);
}
foreach ($historial as $turno) {
    if (
        !is_array($turno)
        || !in_array($turno['role'] ?? null, ['user', 'assistant'], true)
        || !is_string($turno['content'] ?? null)
        || strlen($turno['content']) > 4000
    ) {
        responderChat(400, ['detail' => 'El historial de conversación no es válido.']);
    }
}
if (!is_array($imagenes) || count($imagenes) > 3) {
    responderChat(400, ['detail' => 'Se permiten como máximo tres imágenes.']);
}
foreach ($imagenes as $imagen) {
    if (
        !is_string($imagen)
        || strlen($imagen) > 4 * 1024 * 1024
        || !preg_match('/^data:image\/(?:jpeg|png|webp|gif|avif);base64,[A-Za-z0-9+\/]+=*$/D', $imagen)
    ) {
        responderChat(400, ['detail' => 'Una de las imágenes no tiene un formato permitido.']);
    }
}

$apiUrl = $_ENV['ANGEL_API_URL'] ?? '';
$parsedApiUrl = is_string($apiUrl) ? parse_url($apiUrl) : false;
if (
    !is_array($parsedApiUrl)
    || !in_array($parsedApiUrl['scheme'] ?? '', ['http', 'https'], true)
    || empty($parsedApiUrl['host'])
) {
    error_log('ANGEL_API_URL no está configurada como una URL HTTP válida.');
    responderChat(500, ['detail' => 'El servicio de Ángel no está configurado.']);
}

$keyPath = $_ENV['ANGEL_PRIVATE_KEY_PATH'] ?? '';
if (!is_string($keyPath) || trim($keyPath) === '') {
    error_log('ANGEL_PRIVATE_KEY_PATH no está configurado.');
    responderChat(500, ['detail' => 'El servicio de Ángel no está configurado.']);
}
if (
    $keyPath[0] !== DIRECTORY_SEPARATOR
    && !preg_match('/^[A-Za-z]:[\\\\\/]/', $keyPath)
) {
    $keyPath = $projectRoot . DIRECTORY_SEPARATOR
        . str_replace(['/', '\\'], DIRECTORY_SEPARATOR, $keyPath);
}

$privateKeyContents = @file_get_contents($keyPath);
$privateKey = $privateKeyContents === false
    ? false
    : openssl_pkey_get_private(
        $privateKeyContents,
        (string) ($_ENV['ANGEL_PRIVATE_KEY_PASSPHRASE'] ?? '')
    );
if ($privateKey === false) {
    error_log('No se pudo cargar la clave privada JWT de Ángel.');
    responderChat(500, ['detail' => 'El servicio de Ángel no está configurado.']);
}

$base64UrlEncode = static function (string $value): string {
    return rtrim(strtr(base64_encode($value), '+/', '-_'), '=');
};
$issuedAt = time();
$subject = !empty($_SESSION['id'])
    ? (string) $_SESSION['id']
    : 'anon_' . bin2hex(random_bytes(4));
$signingInput = $base64UrlEncode(json_encode(
    ['alg' => 'RS256', 'typ' => 'JWT'],
    JSON_THROW_ON_ERROR
)) . '.' . $base64UrlEncode(json_encode([
    'sub' => $subject,
    'iat' => $issuedAt,
    'exp' => $issuedAt + 3600,
], JSON_THROW_ON_ERROR));

if (!openssl_sign($signingInput, $signature, $privateKey, OPENSSL_ALGO_SHA256)) {
    error_log('No se pudo firmar el JWT de Ángel.');
    responderChat(500, ['detail' => 'El servicio de Ángel no está disponible.']);
}
$token = $signingInput . '.' . $base64UrlEncode($signature);

$payload = ['mensaje' => trim($mensaje), 'historial' => $historial];
if ($imagenes !== []) {
    $payload['imagenes'] = $imagenes;
}
$encodedPayload = json_encode($payload, JSON_UNESCAPED_UNICODE | JSON_THROW_ON_ERROR);
$upstreamResponse = false;
$upstreamStatus = 0;
$transportError = '';

if (function_exists('curl_init')) {
    $curl = curl_init($apiUrl);
    if ($curl !== false) {
        curl_setopt_array($curl, [
            CURLOPT_POST => true,
            CURLOPT_HTTPHEADER => [
                'Content-Type: application/json',
                'Accept: application/json',
                'Authorization: Bearer ' . $token,
            ],
            CURLOPT_POSTFIELDS => $encodedPayload,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_CONNECTTIMEOUT => 8,
            CURLOPT_TIMEOUT => 45,
        ]);
        $upstreamResponse = curl_exec($curl);
        $upstreamStatus = (int) curl_getinfo($curl, CURLINFO_RESPONSE_CODE);
        $transportError = curl_error($curl);
        curl_close($curl);
    } else {
        $transportError = 'No se pudo iniciar cURL.';
    }
} else {
    $context = stream_context_create([
        'http' => [
            'method' => 'POST',
            'header' => "Content-Type: application/json\r\n"
                . "Accept: application/json\r\n"
                . "Authorization: Bearer {$token}\r\n",
            'content' => $encodedPayload,
            'timeout' => 45,
            'ignore_errors' => true,
        ],
        'ssl' => ['verify_peer' => true, 'verify_peer_name' => true],
    ]);
    $upstreamResponse = @file_get_contents($apiUrl, false, $context);
    foreach ($http_response_header ?? [] as $header) {
        if (preg_match('/^HTTP\/\S+\s+(\d{3})/', $header, $matches)) {
            $upstreamStatus = (int) $matches[1];
        }
    }
    if ($upstreamResponse === false) {
        $transportError = 'Falló la conexión con el servicio de Ángel.';
    }
}

if ($upstreamResponse === false || $upstreamStatus < 200 || $upstreamStatus >= 300) {
    error_log('Error al consultar el servicio de Ángel (HTTP ' . $upstreamStatus . '): ' . $transportError);
    responderChat(502, ['detail' => 'Ángel no está disponible en este momento.']);
}

$responseData = json_decode($upstreamResponse, true);
if (!is_array($responseData) || !is_string($responseData['respuesta'] ?? null)) {
    error_log('El servicio de Ángel devolvió una respuesta inesperada.');
    responderChat(502, ['detail' => 'El servicio de Ángel devolvió una respuesta inválida.']);
}

responderChat(200, ['respuesta' => $responseData['respuesta']]);
