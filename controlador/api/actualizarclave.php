<?php
header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

$autoload = __DIR__ . '/../../vendor/autoload.php';
if (file_exists($autoload)) {
    require_once $autoload;
} else {
    require_once __DIR__ . '/../../modelo/Olvidoclave.php';
}

use LoveMakeup\Proyecto\Modelo\Olvidoclave;

// 1. Buscamos el token de todas las formas posibles en el servidor
$authHeader = $_SERVER['HTTP_AUTHORIZATION'] ?? $_SERVER['REDIRECT_HTTP_AUTHORIZATION'] ?? '';

if (empty($authHeader) && function_exists('apache_request_headers')) {
    $headers = apache_request_headers();
    $authHeader = $headers['Authorization'] ?? $headers['authorization'] ?? '';
}

$token = null;

// 2. Extraemos el JWT puro limpiando la palabra "Bearer" si existe
if (!empty($authHeader)) {
    if (preg_match('/Bearer\s(\S+)/i', $authHeader, $matches)) {
        $token = $matches[1];
    } else {
        $token = trim($authHeader);
    }
}

// 3. Si no hay nada, detenemos con un mensaje claro
if (!$token || trim($token) === '') {
    http_response_code(401);
    echo json_encode(['respuesta' => 0, 'mensaje' => 'No se recibió ningún token en el servidor.']);
    exit;
}

// 4. Limpiamos comillas o espacios raros invisibles
$token = trim($token, '"\' ');

// 5. Ahora sí, picamos el token en 3 partes de forma segura
$partes = explode('.', $token);
if (count($partes) !== 3) {
    http_response_code(401);
    echo json_encode([
        'respuesta' => 0, 
        'mensaje' => 'Formato de token corrupto.',
        'debug' => "Recibido: " . substr($token, 0, 15) . "..." // Para ver qué llegó en el log
    ]);
    exit;
}

list($rawHeader, $rawPayload, $rawSignature) = $partes;

// 🛠️ DECODIFICACIÓN SEGURA DE BASE64 URL (Evita el Error 500 si vienen caracteres raros)
$remainder = strlen($rawPayload) % 4;
if ($remainder) {
    $rawPayload .= str_repeat('=', 4 - $remainder);
}
$decodedPayload = base64_decode(strtr($rawPayload, '-_', '+/'));
$payload = json_decode($decodedPayload, true);

// Validar que el payload se haya decodificado correctamente como array
if (!$payload || !is_array($payload)) {
    http_response_code(401);
    echo json_encode(['respuesta' => 0, 'mensaje' => 'Token corrupto o ilegible por el servidor.']);
    exit;
}

// Verificar expiración del token de cambio
if (!isset($payload['exp']) || time() > $payload['exp']) {
    http_response_code(401);
    echo json_encode(['respuesta' => 0, 'mensaje' => 'El tiempo límite expiró. Inicia el proceso de nuevo.']);
    exit;
}

// Validar que el token cuente con el permiso de OTP aprobado
if (!isset($payload['data']['autorizado']) || $payload['data']['autorizado'] !== true) {
    http_response_code(403);
    echo json_encode(['respuesta' => 0, 'mensaje' => 'Acción no autorizada. Verificación OTP incompleta.']);
    exit;
}

// Si llegó aquí, todo está verificado y seguro
$cedula = $payload['data']['cedula'];

// Leer contraseña enviada por el formulario
$body = file_get_contents('php://input');
$dataJson = json_decode($body, true);
$ClaveNueva = trim($dataJson['password'] ?? '');

if (empty($ClaveNueva)) {
    http_response_code(400);
    echo json_encode(['respuesta' => 0, 'mensaje' => 'La nueva contraseña no puede estar vacía.']);
    exit;
}

$objolvido = new Olvidoclave();

// Estructuramos el array tal como lo manejan tus modelos internos
$datosRegistro = [
    'operacion' => 'actualizar', // Ajusta este string al nombre exacto de tu operación en el modelo
    'datos' => [
        'cedula' => $cedula,
        'clave'  => $ClaveNueva
    ]
];

try {
    // Mandamos la orden al modelo
    $resultado = $objlogin->procesarOlvido(json_encode($datosRegistro));
    
    if (is_string($resultado)) {
        $resultado = json_decode($resultado, true);
    }

    // Evaluamos el éxito con tu misma estructura del Login
    // Si tu modelo devuelve un formato diferente para actualizar, ajusta este condicional
    http_response_code(200);
    echo json_encode([
        'respuesta' => 1,
        'mensaje'   => '¡Tu contraseña ha sido restablecida con éxito!'
    ]);
    exit;

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['respuesta' => 0, 'mensaje' => 'Error de base de datos: ' . $e->getMessage()]);
    exit;
}