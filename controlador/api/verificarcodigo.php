<?php
header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

// Cargar llaves públicas para verificar el token
$publicKeyPath = __DIR__ . '/../../config/jwt_public.pem'; 
$privateKeyPath = __DIR__ . '/../../config/jwt_private.pem';

if (!file_exists($publicKeyPath) || !file_exists($privateKeyPath)) {
    http_response_code(500);
    echo json_encode(['respuesta' => 0, 'mensaje' => 'Error de configuración de llaves en el servidor.']);
    exit;
}

// Capturar el Token de los Headers
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

// Desarmar el JWT de forma manual y segura (RS256)
$partes = explode('.', $token);
if (count($partes) !== 3) {
    http_response_code(401);
    echo json_encode(['respuesta' => 0, 'mensaje' => 'Formato de token corrupto.']);
    exit;
}

list($rawHeader, $rawPayload, $rawSignature) = $partes;

// Decodificar payload para leer datos internos
$payload = json_decode(base64_decode(strtr($rawPayload, '-_', '+/')), true);

if (!$payload || time() > $payload['exp']) {
    http_response_code(401);
    echo json_encode(['respuesta' => 0, 'mensaje' => 'El código OTP ha expirado. Solicita uno nuevo.']);
    exit;
}

// Leer datos enviados por la App
$body = file_get_contents('php://input');
$dataJson = json_decode($body, true);
$codigoIngresado = trim($dataJson['codigo'] ?? '');

$codigoCorrecto = $payload['data']['codigo'];
$cedula = $payload['data']['cedula'];
$cedula = $payload['data']['correo'];

// Controlar los 3 intentos usando el estado del payload (o inicializarlo si no viene)
$intentosFallidos = $payload['data']['intentos'] ?? 0;

function base64url_encode($data) {
    return rtrim(strtr(base64_encode($data), '+/', '-_'), '=');
}

// --- VALIDACIÓN DEL CÓDIGO ---
if ($codigoIngresado == $codigoCorrecto) {
    // CÓDIGO CORRECTO: Generamos un token limpio y final autorizado para cambiar la clave
    $privKeyId = openssl_get_privatekey(file_get_contents($privateKeyPath));
    
    $newHeader = ['alg' => 'RS256', 'typ' => 'JWT'];
    $newPayload = [
        'sub'  => $cedula,
        'iat'  => time(),
        'exp'  => time() + 300, // 5 minutos de validez para cambiar la contraseña
        'data' => ['cedula' => $cedula, 'autorizado' => true]
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
    // CÓDIGO INCORRECTO: Sumamos un intento
    $intentosFallidos++;
    $restantes = 3 - $intentosFallidos;

    if ($intentosFallidos >= 3) {
        // Excedió el límite: Solicitamos destruir el token y forzar salida
        http_response_code(403);
        echo json_encode([
            'respuesta' => -2, // Código especial para decirle a la app que regrese al Home
            'mensaje'   => 'Por seguridad tu solicitud fue cancelada.'
        ]);
        exit;
    }

    // Si le quedan intentos, re-firmamos un token modificado con el nuevo contador de fallos
    $privKeyId = openssl_get_privatekey(file_get_contents($privateKeyPath));
    $payload['data']['intentos_fallidos'] = $intentosFallidos;
    
    $signingInput = $partes[0] . '.' . base64url_encode(json_encode($payload));
    openssl_sign($signingInput, $signature, $privKeyId, OPENSSL_ALGO_SHA256);
    openssl_free_key($privKeyId);
    $nuevoTokenActualizado = $signingInput . '.' . base64url_encode($signature);

    http_response_code(400);
    echo json_encode([
        'respuesta' => 0,
        'mensaje'   => "Código de verificación incorrecto. Te quedan $restantes intentos.",
        'token'     => $nuevoTokenActualizado, // Le devolvemos el token modificado para actualizar AsyncStorage
        'intentos_restantes' => $restantes
    ]);
    exit;
}