<?php
header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
header('Access-Control-Allow-Headers: Authorization, Content-Type');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

// Cargar autoload si existe
$autoload = __DIR__ . '/../../vendor/autoload.php';
if (file_exists($autoload)) {
    require_once $autoload;
} else {
    // intentar cargar modelo manualmente
    require_once __DIR__ . '/../../modelo/VentaWeb.php.php';
}

use LoveMakeup\Proyecto\Modelo\VentaWeb;
use LoveMakeup\Proyecto\Modelo\Delivery;

// Ruta del public key
$publicKeyPath = __DIR__ . '/../../config/jwt_public.pem';
if (!file_exists($publicKeyPath)) {
    http_response_code(500);
    echo json_encode(['error' => 'Public key not found. Configure jwt_public.pem in config/']);
    exit;
}

$publicKey = file_get_contents($publicKeyPath);

// Obtiene token Bearer
function get_bearer_token() {
    $headers = [];
    if (function_exists('getallheaders')) {
        $headers = getallheaders();
    } else {
        foreach ($_SERVER as $name => $value) {
            if (substr($name, 0, 5) == 'HTTP_') {
                $headers[str_replace(' ', '-', ucwords(strtolower(str_replace('_', ' ', substr($name, 5)))))] = $value;
            }
        }
    }
    // Normalize header names to lowercase to handle clients/servers that lowercase headers
    $normalized = [];
    foreach ($headers as $k => $v) {
        $normalized[strtolower($k)] = $v;
    }
    if (isset($normalized['authorization'])) {
        if (preg_match('/Bearer\s+(.*)$/i', $normalized['authorization'], $matches)) {
            return trim($matches[1]);
        }
    }
    return null;
}

function base64url_decode($data) {
    $remainder = strlen($data) % 4;
    if ($remainder) $data .= str_repeat('=', 4 - $remainder);
    return base64_decode(strtr($data, '-_', '+/'));
}

function validate_jwt_rs256($jwt, $publicKey) {
    $parts = explode('.', $jwt);
    if (count($parts) != 3) return false;
    list($hdr, $payload, $sig) = $parts;
    $signed = $hdr . '.' . $payload;
    $signature = base64url_decode($sig);
    $pubKeyId = openssl_get_publickey($publicKey);
    if ($pubKeyId === false) return false;
    $ok = openssl_verify($signed, $signature, $pubKeyId, OPENSSL_ALGO_SHA256) === 1;
    openssl_free_key($pubKeyId);
    if (!$ok) return false;
    $payloadJson = json_decode(base64url_decode($payload), true);
    if (!is_array($payloadJson)) return false;
    if (isset($payloadJson['exp']) && time() > (int)$payloadJson['exp']) return false;
    return $payloadJson;
}

$token = get_bearer_token();
// Fallback: aceptar token también por query param (útil para depuración en emuladores)
if (empty($token) && isset($_GET['access_token'])) {
    $token = $_GET['access_token'];
}

// Modo depuración: mostrar cabeceras y parámetros recibidos
if (isset($_GET['debug']) && $_GET['debug'] === '1') {
    $hdrs = [];
    if (function_exists('getallheaders')) {
        $hdrs = getallheaders();
    } else {
        foreach ($_SERVER as $name => $value) {
            if (substr($name, 0, 5) == 'HTTP_') {
                $hdrs[str_replace(' ', '-', ucwords(strtolower(str_replace('_', ' ', substr($name, 5)))))] = $value;
            }
        }
    }
    $out = [
        'debug' => 1,
        'headers' => $hdrs,
        'get' => $_GET,
        'token_extracted' => $token
    ];

    // Si se pide validación, intentar validar el JWT y devolver el resultado
    if (isset($_GET['validate']) && $_GET['validate'] === '1') {
        if ($token) {
            $valid = validate_jwt_rs256($token, $publicKey);
            if ($valid) {
                $out['validation'] = ['valid' => true, 'claims' => $valid];
            } else {
                $out['validation'] = ['valid' => false];
            }
        } else {
            $out['validation'] = ['valid' => false, 'error' => 'no_token'];
        }
    }

    echo json_encode($out);
    exit;
}

if (!$token) {
    http_response_code(401);
    echo json_encode(['error' => 'Missing Authorization header']);
    exit;
}

$claims = validate_jwt_rs256($token, $publicKey);
if (!$claims) {
    http_response_code(401);
    echo json_encode(['error' => 'Invalid or expired token']);
    exit;
}


$objVentaWeb = new VentaWeb();

try {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $jsonDatos = file_get_contents('php://input');
        
        // Verificar si el JSON se decodifica correctamente
        $decodedData = json_decode($jsonDatos, true);
        if (!$decodedData) {
            http_response_code(400);
            echo json_encode(['error' => 'Error al decodificar JSON: ' . json_last_error_msg()]);
            exit;
        }

        // Verificar si la clave 'datos' existe en el JSON
        if (!isset($decodedData['datos'])) {
            http_response_code(400);
            echo json_encode(['error' => "La clave 'datos' es requerida en la solicitud."]);
            exit;
        }

        // Si imagen viene como base64 (data URI), guardarla como archivo
        $tieneImagen = !empty($decodedData['datos']['imagen']);
        $tipoImagen = $tieneImagen ? gettype($decodedData['datos']['imagen']) : 'none';
        $lenImagen = $tieneImagen ? strlen($decodedData['datos']['imagen']) : 0;
        $prefijoImagen = $tieneImagen ? substr($decodedData['datos']['imagen'], 0, 80) : '';

        $logLine = date('Y-m-d H:i:s') . " | imagen_recibida=" . ($tieneImagen ? 'SI' : 'NO') . " | tipo=$tipoImagen | len=$lenImagen | prefijo=" . $prefijoImagen . "\n";
        @file_put_contents(__DIR__ . '/debug_comprobante.log', $logLine, FILE_APPEND);

        if ($tieneImagen && preg_match('/^data:image\/(\w+);base64,/', $decodedData['datos']['imagen'], $m)) {
            $ext = strtolower($m[1]) === 'jpeg' ? 'jpg' : strtolower($m[1]);
            $extsPermitidas = ['jpg', 'jpeg', 'png', 'webp'];
            $ext = in_array($ext, $extsPermitidas) ? $ext : 'jpg';

            $base64Data = substr($decodedData['datos']['imagen'], strpos($decodedData['datos']['imagen'], ',') + 1);
            $binario = base64_decode($base64Data);
            if ($binario !== false) {
                $dirCaptures = __DIR__ . '/../../assets/img/captures';
                if (!is_dir($dirCaptures)) {
                    mkdir($dirCaptures, 0777, true);
                }
                $nombreArchivo = 'img_' . uniqid() . '.' . $ext;
                $rutaAbsoluta = $dirCaptures . '/' . $nombreArchivo;
                $guardado = file_put_contents($rutaAbsoluta, $binario);
                if ($guardado !== false) {
                    $decodedData['datos']['imagen'] = 'assets/img/captures/' . $nombreArchivo;
                    $jsonDatos = json_encode($decodedData);
                    @file_put_contents(__DIR__ . '/debug_comprobante.log', date('Y-m-d H:i:s') . " | archivo_guardado=$nombreArchivo | bytes=$guardado | ruta=assets/img/captures/$nombreArchivo\n", FILE_APPEND);
                } else {
                    @file_put_contents(__DIR__ . '/debug_comprobante.log', date('Y-m-d H:i:s') . " | ERROR_guardando_archivo\n", FILE_APPEND);
                }
            } else {
                @file_put_contents(__DIR__ . '/debug_comprobante.log', date('Y-m-d H:i:s') . " | ERROR_base64_decode_fallo\n", FILE_APPEND);
            }
        } else {
            @file_put_contents(__DIR__ . '/debug_comprobante.log', date('Y-m-d H:i:s') . " | imagen_no_es_data_uri_o_vacia\n", FILE_APPEND);
        }

        // ============================================
        // VALIDACIÓN DE ENTREGA (paridad con el flujo web)
        // Igual que controlador/Pedidoentrega.php: nunca confiar en lo que
        // manda el cliente para la dirección/delivery.
        // ============================================
        $datos = $decodedData['datos'];
        $idMetodoentrega = (int)($datos['id_metodoentrega'] ?? 0);

        if (!in_array($idMetodoentrega, [1, 2, 3, 4], true)) {
            http_response_code(400);
            echo json_encode(['error' => 'El método de entrega no es válido.']);
            exit;
        }

        if ($idMetodoentrega === 1) {
            // Delivery propio: validar contra el catálogo y reconstruir direccion_envio en el servidor
            $deliveriesActivos = (new Delivery())->consultarActivos();
            $idDelivery = $datos['id_delivery'] ?? null;

            if (empty($idDelivery) || !is_numeric($idDelivery)) {
                http_response_code(400);
                echo json_encode(['error' => 'Debe seleccionar un delivery.']);
                exit;
            }

            $idDelivery = (int)$idDelivery;
            $deliveryValido = false;
            foreach ($deliveriesActivos as $d) {
                if ((int)$d['id_delivery'] === $idDelivery) {
                    $deliveryValido = true;
                    break;
                }
            }
            if (!$deliveryValido) {
                http_response_code(400);
                echo json_encode(['error' => 'El delivery seleccionado no es válido.']);
                exit;
            }
            $datos['id_delivery'] = $idDelivery;

            foreach (['zona', 'parroquia', 'sector', 'direccion'] as $campo) {
                if (empty($datos[$campo])) {
                    http_response_code(400);
                    echo json_encode(['error' => "Falta el campo {$campo}."]);
                    exit;
                }
            }

            $zona = $objVentaWeb->sanitizarString((string)$datos['zona'], 50);
            $parroquia = $objVentaWeb->sanitizarString((string)$datos['parroquia'], 100);
            $sector = $objVentaWeb->sanitizarString((string)$datos['sector'], 100);
            $dirDetall = $objVentaWeb->sanitizarDireccion((string)$datos['direccion']);

            if (!$zona || !$objVentaWeb->validarZona($zona)) {
                http_response_code(400);
                echo json_encode(['error' => 'La zona seleccionada no es válida.']);
                exit;
            }
            if (!$parroquia || !$objVentaWeb->validarParroquia($parroquia)) {
                http_response_code(400);
                echo json_encode(['error' => 'La parroquia no es válida.']);
                exit;
            }
            if (!$sector || !$objVentaWeb->validarSector($sector)) {
                http_response_code(400);
                echo json_encode(['error' => 'El sector no es válido.']);
                exit;
            }
            if (!$dirDetall) {
                http_response_code(400);
                echo json_encode(['error' => 'La dirección no es válida.']);
                exit;
            }

            $datos['direccion_envio'] = "Zona: {$zona}, Parroquia: {$parroquia}, Sector: {$sector}, Dirección: {$dirDetall}";
            $datos['sucursal_envio'] = '';
        } else {
            // Tienda física / MRW / ZOOM: sanitizar como hace el flujo web
            $datos['direccion_envio'] = $objVentaWeb->sanitizarDireccion((string)($datos['direccion_envio'] ?? ''));
            if ($datos['direccion_envio'] === '') {
                http_response_code(400);
                echo json_encode(['error' => 'La dirección de envío es obligatoria.']);
                exit;
            }

            if ($idMetodoentrega === 2 || $idMetodoentrega === 3) {
                // En la app id_metodoentrega ES la empresa de envío (2=MRW, 3=ZOOM)
                $datos['sucursal_envio'] = $objVentaWeb->sanitizarSucursal((string)($datos['sucursal_envio'] ?? ''));
                if ($datos['sucursal_envio'] === '') {
                    http_response_code(400);
                    echo json_encode(['error' => 'Complete el código de la sucursal.']);
                    exit;
                }
            } else {
                $datos['sucursal_envio'] = $objVentaWeb->sanitizarSucursal((string)($datos['sucursal_envio'] ?? ''));
            }
            $datos['id_delivery'] = null;
        }

        // Referencia bancaria: entre 4 y 6 dígitos (pago móvil)
        $referencia = preg_replace('/[^0-9]/', '', (string)($datos['referencia_bancaria'] ?? ''));
        if (!preg_match('/^[0-9]{4,6}$/', $referencia)) {
            http_response_code(400);
            echo json_encode(['error' => 'El código de referencia debe tener entre 4 y 6 dígitos.']);
            exit;
        }
        $datos['referencia_bancaria'] = $referencia;

        $decodedData['datos'] = $datos;
        $jsonDatos = json_encode($decodedData);

        // Procesar el pedido
        $resultado = $objVentaWeb->procesarPedido($jsonDatos);
        echo json_encode($resultado);
    } else {
        http_response_code(405);
        echo json_encode(['error' => 'Method not allowed']);
    }
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => $e->getMessage()]);
}

//..