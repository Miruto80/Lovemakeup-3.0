<?php
header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, OPTIONS');
header('Access-Control-Allow-Headers: Authorization, Content-Type');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

require_once __DIR__ . '/../../vendor/autoload.php';

use LoveMakeup\Proyecto\Modelo\Delivery;

// Ruta del public key
$publicKeyPath = __DIR__ . '/../../config/jwt_public.pem';
if (!file_exists($publicKeyPath)) {
    http_response_code(500);
    echo json_encode(['error' => 'Public key not found. Configure jwt_public.pem in config/']);
    exit;
}

$publicKey = file_get_contents($publicKeyPath);

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
if (empty($token) && isset($_GET['access_token'])) {
    $token = $_GET['access_token'];
}

if (!$token) {
    http_response_code(401);
    echo json_encode(['error' => 'Missing Authorization header']);
    exit;
}

if (!validate_jwt_rs256($token, $publicKey)) {
    http_response_code(401);
    echo json_encode(['error' => 'Invalid or expired token']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    http_response_code(405);
    echo json_encode(['error' => 'Method not allowed']);
    exit;
}

try {
    $objDelivery = new Delivery();
    echo json_encode([
        'success'    => true,
        'deliveries' => $objDelivery->consultarActivos()
    ]);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => $e->getMessage()]);
}
