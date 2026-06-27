<?php
header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

// Cargar autoload o modelo manualmente
$autoload = __DIR__ . '/../../vendor/autoload.php';
if (file_exists($autoload)) {
    require_once $autoload;
} else {
    require_once __DIR__ . '/../../modelo/Olvidoclave.php';
}

use LoveMakeup\Proyecto\Modelo\Olvidoclave;

// 🔑 RUTA EXACTA DEL PRIVATE KEY (Igual que en tu Login)
$privateKeyPath = __DIR__ . '/../../config/jwt_private.pem';
if (!file_exists($privateKeyPath)) {
    http_response_code(500);
    echo json_encode(['respuesta' => 0, 'mensaje' => 'Falta la clave privada jwt_private.pem en la carpeta config.']);
    exit;
}

$privateKey = file_get_contents($privateKeyPath);

function base64url_encode($data) {
    return rtrim(strtr(base64_encode($data), '+/', '-_'), '=');
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['respuesta' => 0, 'mensaje' => 'Método no permitido']);
    exit;
}

// Leer el JSON que manda Axios desde React Native
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
    $resultado = $objolvido->procesarOlvido(json_encode($datosRegistro));

    // 2️⃣ Convertir string JSON → array u objeto según lo devuelva tu modelo
    if (is_string($resultado)) {
        $resultado = json_decode($resultado, true);
    }

    // 3️⃣ Validar si el correo existe leyendo la respuesta (Sintaxis basada en tu Login)
    // Nota: Si procesarOlvido devuelve un objeto, usamos $resultado->cedula. Si es un array: $resultado['cedula']
    // Para asegurar ambos casos, extraemos la cédula de forma segura:
    $cedulaUsuario = is_object($resultado) ? ($resultado->cedula ?? null) : ($resultado['cedula'] ?? null);
    $statusRespuesta = is_object($resultado) ? ($resultado->respuesta ?? null) : ($resultado['respuesta'] ?? null);

    if ($resultado && ($cedulaUsuario || $statusRespuesta == 1)) {
        
        // Si no capturaste la cédula directamente pero el status es válido, lo manejamos.
        // Asumiendo que viene la cédula:
        $cedulaFinal = $cedulaUsuario ?? ($resultado['cedula'] ?? null);

        // --- GENERAR EL JWT ASIMÉTRICO (RS256) IDÉNTICO AL LOGIN ---
        $privKeyId = openssl_get_privatekey($privateKey);
        if ($privKeyId === false) {
            http_response_code(500);
            echo json_encode(['respuesta' => 0, 'mensaje' => 'Configuración de clave privada inválida']);
            exit;
        }

        // Generar código numérico temporal
        $codigo_recuperacion = rand(100000, 999999);

        // Enviar correo
        require_once __DIR__ . '/../../modelo/enviarcorreo.php';
        $enviado = enviarCodigoRecuperacion($correo, $codigo_recuperacion);

        if (!$enviado) {
            http_response_code(400);
            echo json_encode([
                'respuesta' => 0,
                'mensaje'   => 'Error al enviar el correo de recuperación'
            ]);
            exit;
        }

        $header = ['alg' => 'RS256', 'typ' => 'JWT'];
        $duration = 600; 
        
        $payload = [
            'sub'  => $cedulaFinal,
            'iat'  => time(),
            'exp'  => time() + $duration,
            'data' => [ 
                'cedula' => $cedulaFinal,
                'codigo' => $codigo_recuperacion
            ]
        ];

        $rawHeader  = base64url_encode(json_encode($header));
        $rawPayload = base64url_encode(json_encode($payload));
        $signingInput = $rawHeader . '.' . $rawPayload;

        // Firmar token usando la librería OpenSSL cargada con el archivo físico .pem
        openssl_sign($signingInput, $signature, $privKeyId, OPENSSL_ALGO_SHA256);
        openssl_free_key($privKeyId);

        $jwt = $signingInput . '.' . base64url_encode($signature);

        // Respuesta final exitosa para la app móvil (Código 200)
        http_response_code(200);
        echo json_encode([
            'respuesta' => 1,
            'token'     => $jwt
        ]);
        exit;

    } else {
        http_response_code(401);
        echo json_encode([
            'respuesta' => 0,
            'mensaje'   => 'Este correo no está registrado en el sistema.'
        ]);
        exit;
    }

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'respuesta' => 0,
        'mensaje'   => 'Error crítico: ' . $e->getMessage()
    ]);
    exit;
}