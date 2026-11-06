<?php
header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

// Autoload (necesario para el rate limiter)
$autoload = __DIR__ . '/../../vendor/autoload.php';
if (file_exists($autoload)) {
    require_once $autoload;
}

// Rate limiter: máximo de intentos por IP (evita fuerza bruta sobre el código OTP)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $limiter = new \Seguridad\FileRateLimiter(5, 60);
    $resultadoLimiter = $limiter->check($_SERVER['REMOTE_ADDR'] ?? '127.0.0.1');

    if (!$resultadoLimiter['permitido']) {
        $reintentarEn = max(1, $resultadoLimiter['reintentar_en']);
        $mensaje429 = ($resultadoLimiter['motivo'] === 'baneado')
            ? 'E429: Demasiados intentos fallidos. Acceso bloqueado temporalmente, intenta de nuevo en ' . gmdate('H:i:s', $reintentarEn) . '.'
            : 'E429: Demasiadas solicitudes. Por favor, espera ' . gmdate('H:i:s', $reintentarEn) . ' antes de intentarlo de nuevo.';

        http_response_code(429);
        header('Retry-After: ' . $reintentarEn);
        echo json_encode(['respuesta' => 0, 'mensaje' => $mensaje429]);
        exit;
    }
}

$publicKeyPath = __DIR__ . '/../../config/jwt_public.pem';
$privateKeyPath = __DIR__ . '/../../config/jwt_private.pem';

if (!file_exists($publicKeyPath) || !file_exists($privateKeyPath)) {
    http_response_code(500);
    echo json_encode(['respuesta' => 0, 'mensaje' => 'Error de configuración de llaves en el servidor.']);
    exit;
}

$authHeader = $_SERVER['HTTP_AUTHORIZATION'] ?? '';
$token = null;

if (preg_match('/Bearer\s(\S+)/', $authHeader, $matches)) {
    $token = $matches[1];
}

if (!$token) {
    http_response_code(401);
    echo json_encode(['respuesta' => 0, 'mensaje' => 'Token de sesión ausente o inválido.']);
    exit;
}

$partes = explode('.', $token);
if (count($partes) !== 3) {
    http_response_code(401);
    echo json_encode(['respuesta' => 0, 'mensaje' => 'Formato de token corrupto.']);
    exit;
}

list($rawHeader, $rawPayload, $rawSignature) = $partes;
$payload = json_decode(base64_decode(strtr($rawPayload, '-_', '+/')), true);

if (!$payload || time() > $payload['exp']) {
    http_response_code(401);
    echo json_encode(['respuesta' => 0, 'mensaje' => 'El código OTP ha expirado. Solicita uno nuevo.']);
    exit;
}
require_once __DIR__ . '/../../assets/ajuste/validaciones.php';

// Leer datos enviados por la App
$body = file_get_contents('php://input');
$dataJson = json_decode($body, true);
$codigoIngresado = trim($dataJson['codigo'] ?? '');


$codigoCorrecto = $payload['data']['codigo'] ?? '';
$cedula         = $payload['data']['cedula'] ?? '';
$correo         = $payload['data']['correo'] ?? '';

// Extraemos los intentos 
$intentosFallidos = $payload['data']['intentos'] ?? 0;

function base64url_encode($data) {
    return rtrim(strtr(base64_encode($data), '+/', '-_'), '=');
}


    validarExpresionesAPP('codigo_ingresado', $codigoIngresado, "Codigo Ingresado (F) invalido");


// --- VALIDACIÓN DEL CODIGO ---
if ($codigoIngresado == $codigoCorrecto) {
    
    $privKeyId = openssl_get_privatekey(file_get_contents($privateKeyPath));
    
    $newHeader = ['alg' => 'RS256', 'typ' => 'JWT'];
    $newPayload = [
        'sub'  => $correo,
        'iat'  => time(),
        'exp'  => time() + 600, 
        'data' => [
            'cedula' => $cedula, 
            'correo' => $correo, 
            'autorizado' => true
        ]
    ];

    $signingInput = base64url_encode(json_encode($newHeader)) . '.' . base64url_encode(json_encode($newPayload));
    openssl_sign($signingInput, $signature, $privKeyId, OPENSSL_ALGO_SHA256);
    openssl_free_key($privKeyId);

    http_response_code(200);
    echo json_encode([
        'respuesta' => 1,
        'token'     => $signingInput . '.' . base64url_encode($signature)
    ]);
    exit;

} else {
    // CODIGO INCORRECTO Incrementamos el contador
    $intentosFallidos++;
    $restantes = 3 - $intentosFallidos;

    if ($intentosFallidos >= 3) {
        http_response_code(403);
        echo json_encode([
            'respuesta' => -2, 
            'mensaje'   => 'Por seguridad tu solicitud fue cancelada.'
        ]);
        exit;
    }

    // Actualizamos el contador dentro de la estructura original recibida
    $payload['data']['intentos'] = $intentosFallidos;
    
    $privKeyId = openssl_get_privatekey(file_get_contents($privateKeyPath));
    
    // Volvemos a armar el JWT con el Header original de la app y el payload modificado
    $signingInput = $partes[0] . '.' . base64url_encode(json_encode($payload));
    openssl_sign($signingInput, $signature, $privKeyId, OPENSSL_ALGO_SHA256);
    openssl_free_key($privKeyId);
    
    $nuevoTokenActualizado = $signingInput . '.' . base64url_encode($signature);

    http_response_code(400);
    echo json_encode([
        'respuesta' => 0,
        'mensaje'   => "Código de verificación incorrecto. Te quedan $restantes intentos.",
        'token'     => $nuevoTokenActualizado, 
        'intentos_restantes' => $restantes
    ]);
    exit;
}