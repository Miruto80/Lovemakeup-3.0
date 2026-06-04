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
        // Si entra aquí es porque respuesta fue 0 o hubo un error
        $msj = 'Error: Los datos ya existen o son inválidos.';
        
        // Si el catch del privado mandó el texto del error de PDO
        if (isset($resultado['text'])) {
            if (strpos($resultado['text'], 'Duplicate entry') !== false) {
                $msj = 'Esta cédula o correo ya está en uso.';
            } else {
                $msj = 'Error de BD: ' . $resultado['text'];
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