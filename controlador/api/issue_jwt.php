<?php
// Script de utilidad para firmar un JWT con RS256 usando la clave privada en config/jwt_private.pem
// Uso: llamar por web o CLI con parámetros `sub` y `exp` (duración en segundos)

header('Content-Type: application/json; charset=utf-8');

$privPath = __DIR__ . '/../../config/jwt_private.pem';
if (!file_exists($privPath)) {
    http_response_code(500);
    echo json_encode(['error' => 'Private key not found. Place jwt_private.pem in config/']);
    exit;
}

$privateKey = file_get_contents($privPath);
$sub = isset($_GET['sub']) ? $_GET['sub'] : (isset($argv[1]) ? $argv[1] : 'test-user');
$duration = isset($_GET['exp']) ? intval($_GET['exp']) : (isset($argv[2]) ? intval($argv[2]) : 3600);

function base64url_encode($data) {
    return rtrim(strtr(base64_encode($data), '+/', '-_'), '=');
}

$header = ['alg' => 'RS256', 'typ' => 'JWT'];
$payload = ['sub' => $sub, 'iat' => time(), 'exp' => time() + $duration];

$rawHeader = base64url_encode(json_encode($header));
$rawPayload = base64url_encode(json_encode($payload));
$signingInput = $rawHeader . '.' . $rawPayload;

$privKeyId = openssl_get_privatekey($privateKey);
if ($privKeyId === false) {
    http_response_code(500);
    echo json_encode(['error' => 'Invalid private key']);
    exit;
}

openssl_sign($signingInput, $signature, $privKeyId, OPENSSL_ALGO_SHA256);
openssl_free_key($privKeyId);

$jwt = $signingInput . '.' . base64url_encode($signature);

echo json_encode(['jwt' => $jwt, 'payload' => $payload]);

?>
