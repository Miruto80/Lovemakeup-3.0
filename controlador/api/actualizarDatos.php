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
    require_once __DIR__ . '/../../modelo/Catalogo_datos.php';
}

use LoveMakeup\Proyecto\Modelo\Catalogo_datos;

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
        $data .= str_repeat('=', 4 - $remainder);
    }
    return base64_decode(strtr($data, '-_', '+/'));
}

function getAuthorizationHeader() {
    if (!empty($_SERVER['HTTP_AUTHORIZATION'])) {
        return trim($_SERVER['HTTP_AUTHORIZATION']);
    }

    if (!empty($_SERVER['REDIRECT_HTTP_AUTHORIZATION'])) {
        return trim($_SERVER['REDIRECT_HTTP_AUTHORIZATION']);
    }

    if (function_exists('apache_request_headers')) {
        $headers = apache_request_headers();
        if (!empty($headers['Authorization'])) {
            return trim($headers['Authorization']);
        }
        if (!empty($headers['authorization'])) {
            return trim($headers['authorization']);
        }
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
if (!$payload || !is_array($payload)) {
    http_response_code(401);
    echo json_encode(['respuesta' => 0, 'mensaje' => 'Payload JWT inválido']);
    exit;
}

if (isset($payload['exp']) && time() > (int)$payload['exp']) {
    http_response_code(401);
    echo json_encode(['respuesta' => 0, 'mensaje' => 'Token expirado']);
    exit;
}

$userData = isset($payload['data']) ? $payload['data'] : null;
if (!$userData || empty($userData['id_usuario'])) {
    http_response_code(401);
    echo json_encode(['respuesta' => 0, 'mensaje' => 'Token sin información de usuario']);
    exit;
}

$body = file_get_contents('php://input');
$dataJson = json_decode($body, true);
if (!$dataJson) {
    http_response_code(400);
    echo json_encode(['respuesta' => 0, 'mensaje' => 'Datos incompletos o JSON inválido']);
    exit;
}

if (isset($dataJson['datos']) && is_array($dataJson['datos'])) {
    $dataJson = array_merge($dataJson, $dataJson['datos']);
}

if (
    !isset($dataJson['cedula']) ||
    !isset($dataJson['nombre']) ||
    !isset($dataJson['apellido']) ||
    !isset($dataJson['telefono']) ||
    !isset($dataJson['correo'])
) {
    http_response_code(400);
    echo json_encode(['respuesta' => 0, 'mensaje' => 'Datos incompletos o JSON inválido']);
    exit;
}

$objDatos = new Catalogo_datos();
$currentData = $objDatos->consultardatos($userData['id_usuario']);
if (!is_array($currentData) || count($currentData) === 0) {
    http_response_code(404);
    echo json_encode(['respuesta' => 0, 'mensaje' => 'Usuario no encontrado']);
    exit;
}

$current = $currentData[0];

$datosCambio = [
    'operacion' => 'actualizar',
    'datos' => [
        'cedula' => $dataJson['cedula'],
        'cedula_actual' => $current['cedula'],
        'correo' => $dataJson['correo'],
        'correo_actual' => $current['correo'],
        'nombre' => $dataJson['nombre'],
        'apellido' => $dataJson['apellido'],
        'telefono' => $dataJson['telefono'],
        'tipo_documento' => $dataJson['tipo_documento'] ?? $current['tipo_documento'],
    ]
];

try {
    echo "<pre>";
    print_r($datosCambio);
    echo "</pre>";
    exit;

    $resultado = $objDatos->procesarCliente(json_encode($datosCambio));
    if (is_string($resultado)) {
        $resultado = json_decode($resultado, true);
    }

    if (isset($resultado['respuesta']) && (int)$resultado['respuesta'] === 1) {
        http_response_code(200);
        echo json_encode([
            'respuesta' => 1,
            'mensaje' => 'Datos actualizados con éxito',
            'usuario' => [
                'id_usuario' => $current['id_usuario'],
                'cedula' => $dataJson['cedula'],
                'nombre' => $dataJson['nombre'],
                'apellido' => $dataJson['apellido'],
                'telefono' => $dataJson['telefono'],
                'correo' => $dataJson['correo'],
                'tipo_documento' => $datosCambio['datos']['tipo_documento'],
            ]
        ]);
        exit;
    }

    $mensaje = $resultado['text'] ?? $resultado['mensaje'] ?? 'No se pudo actualizar los datos';
    http_response_code(400);
    echo json_encode(['respuesta' => 0, 'mensaje' => $mensaje]);
    exit;
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['respuesta' => 0, 'mensaje' => 'Error crítico: ' . $e->getMessage()]);
    exit;
}
