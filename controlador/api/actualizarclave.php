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

// Rate limiter: máximo de intentos por IP (evita abuso del cambio de clave)
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

// Buscamos el token de todas las formas posibles en el servidor
$authHeader = $_SERVER['HTTP_AUTHORIZATION'] ?? $_SERVER['REDIRECT_HTTP_AUTHORIZATION'] ?? '';

if (empty($authHeader) && function_exists('apache_request_headers')) {
    $headers = apache_request_headers();
    $authHeader = $headers['Authorization'] ?? $headers['authorization'] ?? '';
}

$token = null;

// Extraemos el JWT puro limpiando la palabra "Bearer" si existe
if (!empty($authHeader)) {
    if (preg_match('/Bearer\s(\S+)/i', $authHeader, $matches)) {
        $token = $matches[1];
    } else {
        $token = trim($authHeader);
    }
}

// Si no hay nada, detenemos con un mensaje claro
if (!$token || trim($token) === '') {
    http_response_code(401);
    echo json_encode(['respuesta' => 0, 'mensaje' => 'No se recibió ningún token en el servidor.']);
    exit;
}

// Limpiamos comillas o espacios raros invisibles
$token = trim($token, '"\' ');

// Ahora sí, picamos el token en 3 partes de forma segura
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

require_once __DIR__ . '/../../assets/ajuste/validaciones.php';


$cedula = $payload['data']['cedula'];

// Leer contraseña enviada por el formulario
$body = file_get_contents('php://input');
$dataJson = json_decode($body, true);
$ClaveNueva = trim($dataJson['password'] ?? '');

    validarExpresionesAPP('clave', $ClaveNueva, "Clave Ingresada (F) invalido");

if (empty($ClaveNueva)) {
    http_response_code(400);
    echo json_encode(['respuesta' => 0, 'mensaje' => 'La nueva contraseña no puede estar vacía.']);
    exit;
}

$objolvido = new Olvidoclave();

   validarExpresionesAPP('codigo_ingresado', $codigoIngresado, "Codigo Ingresado (F) invalido");
      validarExpresionesAPP('codigo_ingresado', $codigoIngresado, "Codigo Ingresado (F) invalido");
$datosRegistro = [
    'operacion' => 'actualizar', 
    'datos' => [
        'cedula' => $cedula,
        'clave'  => $ClaveNueva
    ]
];

try {
    // Mandamos la orden al modelo
    $resultado = $objolvido->procesarOlvido(json_encode($datosRegistro));

    if (is_string($resultado)) {
        $resultado = json_decode($resultado, true);
    }

    http_response_code(200);
    echo json_encode([
        'respuesta' => 1,
        'mensaje'   => '¡Tu contraseña ha sido restablecida con éxito!'
    ]);
    exit;

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['respuesta' => 0, 'mensaje' => 'Error: ' . $e->getMessage()]);
    exit;
}