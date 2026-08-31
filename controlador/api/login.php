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
    require_once __DIR__ . '/../../modelo/Login.php';
}

use LoveMakeup\Proyecto\Modelo\Login;

// Rate limiter: máximo de intentos de login por IP (evita fuerza bruta)
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
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode(['respuesta' => 0, 'accion' => 'error', 'text' => $mensaje429]);
        exit;
    }
}

// Ruta del private key para FIRMAR el token que va hacia la App
$privateKeyPath = __DIR__ . '/../../config/jwt_private.pem';
if (!file_exists($privateKeyPath)) {
    http_response_code(500);
    echo json_encode(['error' => 'Private key not found. Configure jwt_private.pem in config/']);
    exit;
}

$privateKey = file_get_contents($privateKeyPath);

function base64url_encode($data) {
    return rtrim(strtr(base64_encode($data), '+/', '-_'), '=');
}

// Solo procesamos peticiones POST para Login
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['error' => 'Method not allowed']);
    exit;
}

// Leer el JSON que manda Axios desde React Native
$body = file_get_contents('php://input');
$dataJson = json_decode($body, true);

if (!$dataJson || !isset($dataJson['usuario']) || !isset($dataJson['clave']) || !isset($dataJson['tipo_documento'])) {
    http_response_code(400);
    echo json_encode(['respuesta' => 0, 'mensaje' => 'Datos incompletos o JSON inválido']);
    exit;
}

$objlogin = new Login();

// Estructuramos el payload idéntico a como lo espera tu modelo
$datosLogin = [
    'operacion' => 'verificar',
    'datos' => [
        'tipo_documento' => $dataJson['tipo_documento'],
        'cedula' => $dataJson['usuario'],
        'clave' => $dataJson['clave']
    ]
];

try {
    // Enviamos el JSON al modelo
    $resultado = $objlogin->procesarLogin(json_encode($datosLogin));

    // Si el usuario existe y las credenciales son válidas
    if ($resultado && isset($resultado->cedula)) {
        
        if ((int)$resultado->estatus === 2) {
            http_response_code(403);
            echo json_encode(['respuesta' => 0, 'mensaje' => 'Lo sentimos, su cuenta está suspendida.']);
            exit;
        }

        if ((int)$resultado->estatus === 1) {
            
            // --- GENERAR EL JWT ASIMÉTRICO (RS256) ---
            $privKeyId = openssl_get_privatekey($privateKey);
            if ($privKeyId === false) {
                http_response_code(500);
                echo json_encode(['error' => 'Invalid private key configuration']);
                exit;
            }

            $header = ['alg' => 'RS256', 'typ' => 'JWT'];
            $duration = 86400; // 1 día de duración útil

            $payload = [
                'sub'  => $resultado->cedula,
                'iat'  => time(),
                'exp'  => time() + $duration,
                'data' => [ // Data útil para que tu frontend o endpoints identifiquen al usuario
                    'id_usuario' => $resultado->id_usuario,
                    'cedula'     => $resultado->cedula,
                    'nombre'     => $resultado->nombre,
                    'apellido'   => $resultado->apellido,
                    'correo'     => $resultado->correo,
                    'telefono'   => $resultado->telefono,
                    'id_rol'     => $resultado->id_rol,
                    'tipo_documento'     => $resultado->tipo_documento,
                    'nivel_rol'  => $resultado->nivel
                ]
            ];

            $rawHeader = base64url_encode(json_encode($header));
            $rawPayload = base64url_encode(json_encode($payload));
            $signingInput = $rawHeader . '.' . $rawPayload;

            // Firmamos el token usando OpenSSL local con la clave privada (.pem)
            openssl_sign($signingInput, $signature, $privKeyId, OPENSSL_ALGO_SHA256);
            openssl_free_key($privKeyId);

            $jwt = $signingInput . '.' . base64url_encode($signature);

            // Respuesta limpia y exitosa para la app móvil
            http_response_code(200);
            echo json_encode([
                'respuesta' => 1,
                'token'     => $jwt,
                'usuario'   => [
                    'cedula'   => $resultado->cedula,
                    'nombre'   => $resultado->nombre,
                    'apellido' => $resultado->apellido,
                    'rol'      => $resultado->nombre_rol
                ]
            ]);
            exit;
        }
    } else {
        http_response_code(401);
        echo json_encode(['respuesta' => 0, 'mensaje' => 'Cédula y/o Clave inválida.']);
        exit;
    }

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['respuesta' => 0, 'mensaje' => $e->getMessage()]);
}
?>