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

$objlogin = new Login();

$datosRegistro = [
    'operacion' => 'registrar',
    'datos' => [
        'nombre'         => $dataJson['nombre'],
        'apellido'       => $dataJson['apellido'],
        'cedula'         => $dataJson['cedula'],
        'telefono'       => $dataJson['telefono'],
        'correo'         => $dataJson['correo'],
        'tipo_documento' => $dataJson['tipo_documento'] ?? 'V',
        'clave'          => $dataJson['clave']
    ]
];

try {
    $resultado = $objlogin->procesarLogin(json_encode($datosRegistro));

   
    if (is_string($resultado)) {
        $resultado = json_decode($resultado);
    }

    // REVISIÓN DE ÉXITO: 
    // Cambiamos la condición para que sea específica al éxito del registro
    if ($resultado && (isset($resultado->respuesta) && ((int)$resultado->respuesta == 1 || $resultado->respuesta == 'exito'))) {
        http_response_code(200);
        echo json_encode([
            'respuesta' => 1,
            'mensaje'   => '¡Bienvenido! Registro completado con éxito.'
        ]);
        exit;
    } else {
        // Si no fue éxito, extraemos el mensaje real del modelo (ej: "Cédula duplicada")
        $errorReal = $resultado->mensaje ?? 'La cédula o el correo ya se encuentran registrados.';
        http_response_code(400);
        echo json_encode([
            'respuesta' => 0,
            'mensaje'   => $errorReal
        ]);
        exit;
    }

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['respuesta' => 0, 'mensaje' => 'Error de servidor: ' . $e->getMessage()]);
}