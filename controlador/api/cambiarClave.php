<?php
header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

// Cargar autoload o modelo manualmente
$autoload = __DIR__ . '/../../vendor/autoload.php';
if (file_exists($autoload)) {
    require_once $autoload;
} else {
    require_once __DIR__ . '/../../modelo/Datos.php';
}

use LoveMakeup\Proyecto\Modelo\Datos;

// Ruta de la clave pública para verificar tokens firmados por la app
$publicKeyPath = __DIR__ . '/../../config/jwt_public.pem';
if (!file_exists($publicKeyPath)) {
    http_response_code(500);
    echo json_encode(['respuesta' => 0, 'mensaje' => 'Public key not found. Configure jwt_public.pem in config/']);
    exit;
}

$publicKey = file_get_contents($publicKeyPath);

function base64url_decode($data) {
    $remainder = strlen($data) % 4;
    if ($remainder) {
        $padlen = 4 - $remainder;
        $data .= str_repeat('=', $padlen);
    }
    return base64_decode(strtr($data, '-_', '+/'));
}

function getAuthorizationHeader() {
    if (isset($_SERVER['HTTP_AUTHORIZATION'])) {
        return trim($_SERVER['HTTP_AUTHORIZATION']);
    }
    if (function_exists('getallheaders')) {
        $headers = getallheaders();
        if (!empty($headers['Authorization'])) {
            return trim($headers['Authorization']);
        }
        if (!empty($headers['authorization'])) {
            return trim($headers['authorization']);
        }
    }
    return null;
}

$authHeader = getAuthorizationHeader();
if (!$authHeader || stripos($authHeader, 'Bearer ') !== 0) {
    http_response_code(401);
    echo json_encode(['respuesta' => 0, 'mensaje' => 'Token de autorización faltante o inválido']);
    exit;
}

$jwt = trim(substr($authHeader, 7));
$parts = explode('.', $jwt);
if (count($parts) !== 3) {
    http_response_code(401);
    echo json_encode(['respuesta' => 0, 'mensaje' => 'Token JWT mal formado']);
    exit;
}

list($rawHeader, $rawPayload, $rawSignature) = $parts;
$signingInput = $rawHeader . '.' . $rawPayload;
$signature = base64url_decode($rawSignature);

$pubKeyId = openssl_get_publickey($publicKey);
if ($pubKeyId === false) {
    http_response_code(500);
    echo json_encode(['respuesta' => 0, 'mensaje' => 'Configuración de clave pública inválida']);
    exit;
}

$verified = openssl_verify($signingInput, $signature, $pubKeyId, OPENSSL_ALGO_SHA256);
openssl_free_key($pubKeyId);

if ($verified !== 1) {
    http_response_code(401);
    echo json_encode(['respuesta' => 0, 'mensaje' => 'Firma del token inválida']);
    exit;
}

$payloadJson = base64url_decode($rawPayload);
$payload = json_decode($payloadJson, true);
if (!$payload) {
    http_response_code(401);
    echo json_encode(['respuesta' => 0, 'mensaje' => 'Payload JWT inválido']);
    exit;
}

// Validar expiración
if (isset($payload['exp']) && time() > (int)$payload['exp']) {
    http_response_code(401);
    echo json_encode(['respuesta' => 0, 'mensaje' => 'Token expirado']);
    exit;
}

// Identificador del usuario (sub) o datos adicionales
$userCedula = isset($payload['sub']) ? $payload['sub'] : null;
$userData = isset($payload['data']) ? $payload['data'] : null;

if ((!$userCedula && !$userData) || empty($userData['id_usuario'])) {
    http_response_code(401);
    echo json_encode(['respuesta' => 0, 'mensaje' => 'Token sin información de usuario']);
    exit;
}

// Procesar body JSON
$body = file_get_contents('php://input');
$dataJson = json_decode($body, true);
if (!$dataJson || !isset($dataJson['clave_actual']) || !isset($dataJson['clave_nueva'])) {
    http_response_code(400);
    echo json_encode(['respuesta' => 0, 'mensaje' => 'Datos incompletos o JSON inválido']);
    exit;
}

$claveActual = $dataJson['clave_actual'];
$claveNueva  = $dataJson['clave_nueva'];

// Llamamos al modelo correcto para cambiar la contraseña
$objDatos = new Datos();

// Estructuramos el request interno usando la operación que ya existe en modelo/Datos.php
$datosCambio = [
    'operacion' => 'actualizarclave',
    'datos' => [
        'id_usuario' => $userData['id_usuario'],
        'clave_actual' => $claveActual,
        'clave' => $claveNueva
    ]
];

try {
    $resultado = $objDatos->procesarUsuario(json_encode($datosCambio));

    if (is_string($resultado)) {
        $resultado = json_decode($resultado, true);
    }

    if (isset($resultado['respuesta']) && (int)$resultado['respuesta'] === 1) {
        http_response_code(200);
        echo json_encode([
            'respuesta' => 1,
            'mensaje' => $resultado['mensaje'] ?? $resultado['text'] ?? 'Contraseña actualizada'
        ]);
        exit;
    }

    http_response_code(400);
    echo json_encode([
        'respuesta' => 0,
        'mensaje' => $resultado['text'] ?? $resultado['mensaje'] ?? 'No se pudo actualizar la contraseña'
    ]);
    exit;
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['respuesta' => 0, 'mensaje' => $e->getMessage()]);
    exit;
}

?>