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
    require_once __DIR__ . '/../../modelo/Catalogopedido.php';
}

use LoveMakeup\Proyecto\Modelo\Catalogopedido;

// Ruta del public key
$publicKeyPath = __DIR__ . '/../../config/jwt_public.pem';
if (!file_exists($publicKeyPath)) {
    http_response_code(500);
    echo json_encode(['error' => 'Public key not found. Configure jwt_public.pem in config/']);
    exit;
}

$publicKey = file_get_contents($publicKeyPath);

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
if (empty($token) && isset($_GET['access_token'])) {
    $token = $_GET['access_token'];
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

try {
    $obj = new Catalogopedido();
    $pedidos = $obj->consultarPedidosCompletosCatalogo();

    // Determinar cédula del usuario desde las claims del token (producción)
    $requestCedula = null;
    if (is_array($claims)) {
        if (!empty($claims['data']['cedula'])) {
            $requestCedula = preg_replace('/\D/', '', $claims['data']['cedula']);
        } elseif (!empty($claims['cedula'])) {
            $requestCedula = preg_replace('/\D/', '', $claims['cedula']);
        } elseif (!empty($claims['data']['usuario'])) {
            $requestCedula = preg_replace('/\D/', '', $claims['data']['usuario']);
        }
    }

    // Para depuración local solo: permitir override con ?debug=1&cedula=...
    if (isset($_GET['debug']) && $_GET['debug'] === '1' && isset($_GET['cedula']) && !empty($_GET['cedula'])) {
        $requestCedula = preg_replace('/\D/', '', $_GET['cedula']);
    }

    // En producción exigir la cédula en el token
    if (!$requestCedula) {
        http_response_code(403);
        echo json_encode(['respuesta' => 0, 'mensaje' => 'Cédula no encontrada en token.']);
        exit;
    }

    // Adjuntar detalles de cada pedido
    foreach ($pedidos as &$p) {
        $id = $p['id_pedido'] ?? null;
        if ($id) {
            $det = $obj->consultarDetallesPedidoCatalogo($id);
            // normalizar campos para cliente móvil
            $productos = [];
            if (is_array($det)) {
                foreach ($det as $d) {
                    $productos[] = [
                        'nombre' => $d['nombre'] ?? $d['nombre_producto'] ?? '',
                        'cantidad' => isset($d['cantidad']) ? (int)$d['cantidad'] : 0,
                        'precio' => isset($d['precio_unitario']) ? (float)$d['precio_unitario'] : (float)($d['precio'] ?? 0),
                    ];
                }
            }
            $p['productos'] = $productos;
        } else {
            $p['productos'] = [];
        }
    }
    unset($p);

    // Filtrar solo los pedidos asociados a la cédula del token
    $filtered = array_filter($pedidos, function ($item) use ($requestCedula) {
        $itemCed = isset($item['cedula']) ? preg_replace('/\D/', '', $item['cedula']) : '';
        return $itemCed !== '' && $itemCed === $requestCedula;
    });
    $pedidos = array_values($filtered);

    echo json_encode(['respuesta' => 1, 'pedidos' => $pedidos]);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['respuesta' => 0, 'mensaje' => $e->getMessage()]);
}

?>
