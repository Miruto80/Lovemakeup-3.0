<?php
header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
header('Access-Control-Allow-Headers: Authorization, Content-Type');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

// Cargar autoload si existe
$autoload = __DIR__ . '/../../vendor/autoload.php';
if (file_exists($autoload)) {
    require_once $autoload;
} else {
    // intentar cargar modelo manualmente
    require_once __DIR__ . '/../../modelo/VentaWeb.php.php';
}

use LoveMakeup\Proyecto\Modelo\VentaWeb;

// Ruta del public key
$publicKeyPath = __DIR__ . '/../../config/jwt_public.pem';
if (!file_exists($publicKeyPath)) {
    http_response_code(500);
    echo json_encode(['error' => 'Public key not found. Configure jwt_public.pem in config/']);
    exit;
}

$publicKey = file_get_contents($publicKeyPath);

// Obtiene token Bearer
function get_bearer_token() {
    $headers = [];
    if (function_exists('getallheaders')) {
        $headers = getallheaders();
    } else {
        foreach ($_SERVER as $name => $value) {
            if (substr($name, 0, 5) == 'HTTP_') {
                $headers[str_replace(' ', '-', ucwords(strtolower(str_replace('_', ' ', substr($name, 5)))))] = $value;
            }
        }
    }
    // Normalize header names to lowercase to handle clients/servers that lowercase headers
    $normalized = [];
    foreach ($headers as $k => $v) {
        $normalized[strtolower($k)] = $v;
    }
    if (isset($normalized['authorization'])) {
        if (preg_match('/Bearer\s+(.*)$/i', $normalized['authorization'], $matches)) {
            return trim($matches[1]);
        }
    }
    return null;
}

function base64url_decode($data) {
    $remainder = strlen($data) % 4;
    if ($remainder) $data .= str_repeat('=', 4 - $remainder);
    return base64_decode(strtr($data, '-_', '+/'));
}

function validate_jwt_rs256($jwt, $publicKey) {
    $parts = explode('.', $jwt);
    if (count($parts) != 3) return false;
    list($hdr, $payload, $sig) = $parts;
    $signed = $hdr . '.' . $payload;
    $signature = base64url_decode($sig);
    $pubKeyId = openssl_get_publickey($publicKey);
    if ($pubKeyId === false) return false;
    $ok = openssl_verify($signed, $signature, $pubKeyId, OPENSSL_ALGO_SHA256) === 1;
    openssl_free_key($pubKeyId);
    if (!$ok) return false;
    $payloadJson = json_decode(base64url_decode($payload), true);
    if (!is_array($payloadJson)) return false;
    if (isset($payloadJson['exp']) && time() > (int)$payloadJson['exp']) return false;
    return $payloadJson;
}

$token = get_bearer_token();
// Fallback: aceptar token también por query param (útil para depuración en emuladores)
if (empty($token) && isset($_GET['access_token'])) {
    $token = $_GET['access_token'];
}

// Modo depuración: mostrar cabeceras y parámetros recibidos
if (isset($_GET['debug']) && $_GET['debug'] === '1') {
    $hdrs = [];
    if (function_exists('getallheaders')) {
        $hdrs = getallheaders();
    } else {
        foreach ($_SERVER as $name => $value) {
            if (substr($name, 0, 5) == 'HTTP_') {
                $hdrs[str_replace(' ', '-', ucwords(strtolower(str_replace('_', ' ', substr($name, 5)))))] = $value;
            }
        }
    }
    $out = [
        'debug' => 1,
        'headers' => $hdrs,
        'get' => $_GET,
        'token_extracted' => $token
    ];

    // Si se pide validación, intentar validar el JWT y devolver el resultado
    if (isset($_GET['validate']) && $_GET['validate'] === '1') {
        if ($token) {
            $valid = validate_jwt_rs256($token, $publicKey);
            if ($valid) {
                $out['validation'] = ['valid' => true, 'claims' => $valid];
            } else {
                $out['validation'] = ['valid' => false];
            }
        } else {
            $out['validation'] = ['valid' => false, 'error' => 'no_token'];
        }
    }

    echo json_encode($out);
    exit;
}

if (!$token) {
    http_response_code(401);
    echo json_encode(['error' => 'Missing Authorization header']);
    exit;
}

$claims = validate_jwt_rs256($token, $publicKey);
if (!$claims) {
    http_response_code(401);
    echo json_encode(['error' => 'Invalid or expired token']);
    exit;
}


$objVentaWeb = new VentaWeb();

try {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $jsonDatos = file_get_contents('php://input');
        
        // Verificar si el JSON se decodifica correctamente
        $decodedData = json_decode($jsonDatos, true);
        if (!$decodedData) {
            http_response_code(400);
            echo json_encode(['error' => 'Error al decodificar JSON: ' . json_last_error_msg()]);
            exit;
        }

        // Verificar si la clave 'datos' existe en el JSON
        if (!isset($decodedData['datos'])) {
            http_response_code(400);
            echo json_encode(['error' => "La clave 'datos' es requerida en la solicitud."]);
            exit;
        }

        // Procesar el pedido
        $resultado = $objVentaWeb->procesarPedido($jsonDatos);
        echo json_encode($resultado);
    } else {
        http_response_code(405);
        echo json_encode(['error' => 'Method not allowed']);
    }
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => $e->getMessage()]);
}

//..