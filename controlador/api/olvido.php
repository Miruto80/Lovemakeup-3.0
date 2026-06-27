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
require_once __DIR__ . '/../../config/private_key.php';

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
        'valor' => $correo
    ]
];

try {

    // 1️⃣ Ejecutar el modelo
    $datosUsuario = $objolvido->procesarOlvido(json_encode($datosRegistro));

    // 2️⃣ Convertir string JSON → array
    if (is_string($datosUsuario)) {
        $datosUsuario = json_decode($datosUsuario, true);
    }

    // 3️⃣ Convertir objeto → array
    if (is_object($datosUsuario)) {
        $datosUsuario = (array)$datosUsuario;
    }

    // 4️⃣ Validar que exista la cédula
    if (!$datosUsuario || !isset($datosUsuario['cedula'])) {
        echo json_encode([
            'respuesta' => 0,
            'mensaje'   => 'Este correo no está registrado'
        ]);
        exit;
    }

    // Generar código
    $codigo_recuperacion = rand(100000, 999999);

    // Enviar correo
    $enviado = enviarCodigoRecuperacion($correo, $codigo_recuperacion);

    if (!$enviado) {
        echo json_encode([
            'respuesta' => 2,
            'mensaje'   => 'Error al enviar el correo de recuperación'
        ]);
        exit;
    }

    // Generar JWT RS256
    $header = ['alg' => 'RS256', 'typ' => 'JWT'];
    $duration = 600;

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

    // Respuesta final
    echo json_encode([
        'respuesta' => 1,
        'token'     => $jwt
    ]);
    exit;

} catch (Exception $e) {
    http_response_code(200); // Forzamos 200 temporalmente para leer el error en la app
    echo json_encode([
        'respuesta' => 0,
        'mensaje'   => 'Error crítico: ' . $e->getMessage() . ' en línea ' . $e->getLine() . ' del archivo ' . $e->getFile()
    ]);
    exit;
}
