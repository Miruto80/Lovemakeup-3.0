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
    require_once __DIR__ . '/../../modelo/Login.php';
}

use LoveMakeup\Proyecto\Modelo\Login;

// Rate limiter: máximo de intentos por IP (evita abuso de registro)
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
        echo json_encode(['respuesta' => 0, 'mensaje' => $mensaje429]);
        exit;
    }
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['error' => 'Method not allowed']);
    exit;
}

$body = file_get_contents('php://input');
$dataJson = json_decode($body, true);

// Validación de entrada
if (!$dataJson || !isset($dataJson['cedula']) || !isset($dataJson['nombre']) || !isset($dataJson['clave'])) {
    http_response_code(400);
    echo json_encode(['respuesta' => 0, 'mensaje' => 'Datos incompletos']);
    exit;
}

require_once __DIR__ . '/../../assets/ajuste/validaciones.php';


$objlogin = new Login();

    validarExpresionesAPP('nombre', $dataJson['nombre'], "Nombre Ingresada (F) invalido");
    validarExpresionesAPP('apellido', $dataJson['apellido'], "Apellido Ingresada (F) invalido");
    validarExpresionesAPP('cedula', $dataJson['cedula'], "Cedula Ingresada (F) invalido");
    validarExpresionesAPP('telefono', $dataJson['telefono'], "Telefono Ingresada (F) invalido");
    validarExpresionesAPP('correo', $dataJson['correo'], "Correo Ingresada (F) invalido");
    validarExpresionesAPP('documento', $dataJson['tipo_documento'], "documento Ingresada (F) invalido");
    validarExpresionesAPP('clave', $dataJson['clave'], "Clave Ingresada (F) invalido");

$datosRegistro = [
    'operacion' => 'registrar',
    'datos' => [
        'nombre'         => $dataJson['nombre'],
        'apellido'       => $dataJson['apellido'],
        'cedula'         => $dataJson['cedula'],
        'telefono'       => $dataJson['telefono'],
        'correo'         => $dataJson['correo'],
        'tipo_documento' => $dataJson['tipo_documento'],
        'clave'          => $dataJson['clave']
    ]
];

try {
    // 1. Llamamos al método público que a su vez llama al privado
    $resultado = $objlogin->procesarLogin(json_encode($datosRegistro));

    // 2. Si el resultado es un string JSON (a veces el modelo devuelve json), lo decodificamos
    if (is_string($resultado)) {
        $resultado = json_decode($resultado, true);
    }

    // 3. Validamos usando sintaxis de ARRAY (corchetes [])
    // Tu método privado devuelve ['respuesta' => 1]
    if (isset($resultado['respuesta']) && $resultado['respuesta'] == 1) {
        http_response_code(200);
        echo json_encode([
            'respuesta' => 1,
            'mensaje'   => '¡Usuario registrado con éxito!'
        ]);
        exit;
    } else {
        $msj = 'Error: Los datos ya existen o son inválidos.';
        
        if (isset($resultado['text'])) {
            if (strpos($resultado['text'], 'Duplicate entry') !== false) {
                $msj = 'Esta cédula o correo ya está en uso.';
            } else {
                $msj =  $resultado['text'];
            }
        }

        http_response_code(400);
        echo json_encode([
            'respuesta' => 0,
            'mensaje'   => $msj
        ]);
        exit;
    }

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['respuesta' => 0, 'mensaje' => 'Error crítico: ' . $e->getMessage()]);
}