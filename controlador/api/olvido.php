<?php
header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

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

require_once __DIR__ . '/../../modelo/enviarcorreo.php';
require_once __DIR__ . '/../../config/private_key.php'; // tu clave privada

use LoveMakeup\Proyecto\Modelo\Olvidoclave;

function base64url_encode($data) {
    return rtrim(strtr(base64_encode($data), '+/', '-_'), '=');
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['respuesta' => 0, 'mensaje' => 'Método no permitido']);
    exit;
}

$body = file_get_contents('php://input');
$dataJson = json_decode($body, true);

if (!$dataJson || !isset($dataJson['correo'])) {
    http_response_code(400);
    echo json_encode(['respuesta' => 0, 'mensaje' => 'Correo es obligatorio']);
    exit;
}

$correo = trim($dataJson['correo']);
$objolvido = new Olvidoclave();

$datosRegistro = [
    'operacion' => 'verificar',
    'datos' => [
        'correo'         => $correo
    ]
];

try {

    // Verificar si existe el correo
    $datosUsuario = $objolvido->procesarOlvido(json_encode($datosRegistro));

    if (!$datosUsuario || !isset($datosUsuario['cedula'])) {
        echo json_encode([
            'respuesta' => 0,
            'mensaje'   => 'Este correo no está registrado'
        ]);
        exit;
    }

    $codigo_recuperacion = rand(100000, 999999);

    $enviado = enviarCodigoRecuperacion($correo, $codigo_recuperacion);

        if (!$enviado) {
            echo json_encode([
                'respuesta' => 2,
                'mensaje'   => 'Error al enviar el correo de recuperación'
            ]);
            exit;
        }

    // 4️⃣ Generar JWT RS256 (igual que tu login)
    $header = ['alg' => 'RS256', 'typ' => 'JWT'];
    $duration = 600; // 10 minutos

    $payload = [
        'sub'  => $datosUsuario['cedula'],
        'iat'  => time(),
        'exp'  => time() + $duration,
        'data' => [
            'cedula' => $datosUsuario['cedula'],
            'codigo' => $codigo_recuperacion
        ]
    ];

    $rawHeader  = base64url_encode(json_encode($header));
    $rawPayload = base64url_encode(json_encode($payload));
    $signingInput = $rawHeader . '.' . $rawPayload;

    $privKeyId = openssl_get_privatekey($privateKey);
    openssl_sign($signingInput, $signature, $privKeyId, OPENSSL_ALGO_SHA256);
    openssl_free_key($privKeyId);

    $jwt = $signingInput . '.' . base64url_encode($signature);

    echo json_encode([
        'respuesta' => 1,
        'token'     => $jwt
    ]);
    exit;

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'respuesta' => 0,
        'mensaje'   => 'Error crítico: ' . $e->getMessage()
    ]);
    exit;
}
