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

$body = file_get_contents('php://input');
$dataJson = json_decode($body, true);

$accion = isset($dataJson['accion']) ? (int)$dataJson['accion'] : 0;

try {
    $correo = '';
    $cedulaFinal = '';

    if ($accion === 100) {
        // --- MÓDULO DE REENVÍO: LEER DATOS DESDE EL JWT EXISTENTE ---
        $authHeader = $_SERVER['HTTP_AUTHORIZATION'] ?? '';
        $token = null;

        if (preg_match('/Bearer\s(\S+)/', $authHeader, $matches)) {
            $token = $matches[1];
        }

        if (!$token) {
            http_response_code(401);
            echo json_encode(['respuesta' => 0, 'mensaje' => 'Token ausente. No se puede reenviar.']);
            exit;
        }

        $partes = explode('.', $token);
        if (count($partes) !== 3) {
            http_response_code(401);
            echo json_encode(['respuesta' => 0, 'mensaje' => 'Token inválido.']);
            exit;
        }

        // Decodificamos de forma segura el payload original
        $payloadOriginal = json_decode(base64_decode(strtr($partes[1], '-_', '+/')), true);
        
        $correo = trim($payloadOriginal['data']['correo'] ?? '');
        $cedulaFinal = trim($payloadOriginal['data']['cedula'] ?? '');

        if (empty($correo) || empty($cedulaFinal)) {
            http_response_code(400);
            echo json_encode(['respuesta' => 0, 'mensaje' => 'Datos de sesión corruptos. Intente de nuevo.']);
            exit;
        }
        
    } else {
        // --- MÓDULO  SÍ VERIFICA BASE DE DATOS ---
        if (!$dataJson || !isset($dataJson['correo'])) {
            http_response_code(400);
            echo json_encode(['respuesta' => 0, 'mensaje' => 'Correo es obligatorio']);
            exit;
        }

        $correo = trim($dataJson['correo']);
        $objolvido = new Olvidoclave();

        $datosRegistro = [
            'operacion' => 'verificar',
            'datos' => [ 'valor' => $correo ]
        ];

        $resultado = $objolvido->procesarOlvido(json_encode($datosRegistro));
        if (is_string($resultado)) {
            $resultado = json_decode($resultado, true);
        }

        $cedulaUsuario = is_object($resultado) ? ($resultado->cedula ?? null) : ($resultado['cedula'] ?? null);
        $statusRespuesta = is_object($resultado) ? ($resultado->respuesta ?? null) : ($resultado['respuesta'] ?? null);

        if ($resultado && ($cedulaUsuario || $statusRespuesta == 1)) {
            $cedulaFinal = $cedulaUsuario ?? ($resultado['cedula'] ?? null);
        } else {
            http_response_code(401);
            echo json_encode(['respuesta' => 0, 'mensaje' => 'Este correo no está registrado en el sistema.']);
            exit;
        }
    }

    // ---  GENERACIÓN DE CÓDIGO Y NUEVO JWT ---
    $privKeyId = openssl_get_privatekey($privateKey);
    if ($privKeyId === false) {
        http_response_code(500);
        echo json_encode(['respuesta' => 0, 'mensaje' => 'Configuración de clave privada inválida']);
        exit;
    }

    $codigo_recuperacion = rand(100000, 999999);

    require_once __DIR__ . '/../../modelo/enviarcorreo.php';
    $enviado = enviarCodigoRecuperacion($correo, $codigo_recuperacion);

    if (!$enviado) {
        http_response_code(400);
        echo json_encode(['respuesta' => 0, 'mensaje' => 'Error al enviar el correo de recuperación']);
        exit;
    }

    $header = ['alg' => 'RS256', 'typ' => 'JWT'];
    $duration = 300; 
    
    $payload = [
        'sub'  => $correo,
        'iat'  => time(),
        'exp'  => time() + $duration,
        'data' => [ 
            'correo'  => $correo,
            'cedula'  => $cedulaFinal,
            'codigo'  => $codigo_recuperacion,
            'intentos' => 0 // Se resetean los intentos fallidos al generar un nuevo código
        ]
    ];

    $rawHeader   = base64url_encode(json_encode($header));
    $rawPayload  = base64url_encode(json_encode($payload));
    $signingInput = $rawHeader . '.' . $rawPayload;

    openssl_sign($signingInput, $signature, $privKeyId, OPENSSL_ALGO_SHA256);
    openssl_free_key($privKeyId);

    $jwt = $signingInput . '.' . base64url_encode($signature);

    http_response_code(200);
    echo json_encode([
        'respuesta' => 1,
        'token'     => $jwt,
        'mensaje'   => $accion === 100 ? 'Código reenviado con éxito.' : 'Código enviado.'
    ]);
    exit;

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['respuesta' => 0, 'mensaje' => 'Error crítico: ' . $e->getMessage()]);
    exit;
}