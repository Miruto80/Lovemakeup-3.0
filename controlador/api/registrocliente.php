<?php
header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

// 1. Cargar el modelo (idéntico a tu login)
$autoload = __DIR__ . '/../../vendor/autoload.php';
if (file_exists($autoload)) {
    require_once $autoload;
} else {
    require_once __DIR__ . '/../../modelo/Login.php';
}

use LoveMakeup\Proyecto\Modelo\Login;

// 2. Solo procesamos peticiones POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['error' => 'Method not allowed']);
    exit;
}

// 3. Leer el JSON que manda React Native (Axios)
$body = file_get_contents('php://input');
$dataJson = json_decode($body, true);

// Validamos que vengan los datos necesarios
if (!$dataJson || !isset($dataJson['cedula']) || !isset($dataJson['nombre']) || !isset($dataJson['clave'])) {
    http_response_code(400);
    echo json_encode(['respuesta' => 0, 'mensaje' => 'Datos incompletos para el registro']);
    exit;
}

$objlogin = new Login();

// 4. Estructuramos el array según lo espera tu modelo para la operación 'registrar'
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
    // 5. Enviamos al modelo procesarLogin (que maneja el JSON de registro)
    $resultado = $objlogin->procesarLogin(json_encode($datosRegistro));

    // 6. Manejo de la respuesta del modelo
    // Nota: Ajusta 'exito' según lo que devuelva tu procedimiento almacenado/modelo
    if ($resultado && ($resultado->respuesta == 1 || $resultado->respuesta == 'exito')) {
        http_response_code(201); // 201 significa "Creado"
        echo json_encode([
            'respuesta' => 1,
            'mensaje'   => 'Usuario registrado correctamente.'
        ]);
    } else {
        // Si el modelo devuelve un error (ej: Cédula ya existe)
        http_response_code(400);
        echo json_encode([
            'respuesta' => 0, 
            'mensaje'   => isset($resultado->text) ? $resultado->text : 'No se pudo completar el registro.'
        ]);
    }

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'respuesta' => 0, 
        'mensaje'   => 'Error en el servidor: ' . $e->getMessage()
    ]);
}