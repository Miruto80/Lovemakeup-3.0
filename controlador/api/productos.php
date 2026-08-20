<?php

header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, OPTIONS');
header('Access-Control-Allow-Headers: Authorization, Content-Type');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

$autoload = _DIR_ . '/../../vendor/autoload.php';

if (file_exists($autoload)) {
    require_once $autoload;
} else {
    require_once _DIR_ . '/../../modelo/Producto.php';
}

use LoveMakeup\Proyecto\Modelo\Producto;

$publicKeyPath = _DIR_ . '/../../config/jwt_public.pem';

if (!file_exists($publicKeyPath)) {

    http_response_code(500);

    echo json_encode([
        'respuesta' => 0,
        'mensaje' => 'Public key not found'
    ]);

    exit;
}

$publicKey = file_get_contents($publicKeyPath);


function get_bearer_token()
{
    $headers = [];

    if (function_exists('getallheaders')) {

        $headers = getallheaders();

    } else {

        foreach ($_SERVER as $name => $value) {

            if (substr($name, 0, 5) == 'HTTP_') {

                $headers[
                    str_replace(
                        ' ',
                        '-',
                        ucwords(
                            strtolower(
                                str_replace(
                                    '_',
                                    ' ',
                                    substr($name, 5)
                                )
                            )
                        )
                    )
                ] = $value;
            }
        }
    }

    $normalized = [];

    foreach ($headers as $k => $v) {
        $normalized[strtolower($k)] = $v;
    }

    if (isset($normalized['authorization'])) {

        if (
            preg_match(
                '/Bearer\s+(.*)$/i',
                $normalized['authorization'],
                $matches
            )
        ) {
            return trim($matches[1]);
        }
    }

    return null;
}


function base64url_decode($data)
{
    $remainder = strlen($data) % 4;

    if ($remainder) {
        $data .= str_repeat('=', 4 - $remainder);
    }

    return base64_decode(
        strtr($data, '-_', '+/')
    );
}


function validate_jwt_rs256($jwt, $publicKey)
{
    $parts = explode('.', $jwt);

    if (count($parts) !== 3) {
        return false;
    }

    [$hdr, $payload, $sig] = $parts;

    $signed = $hdr . '.' . $payload;

    $signature = base64url_decode($sig);

    $pubKeyId = openssl_get_publickey($publicKey);

    if ($pubKeyId === false) {
        return false;
    }

    $ok = openssl_verify(
        $signed,
        $signature,
        $pubKeyId,
        OPENSSL_ALGO_SHA256
    ) === 1;

    openssl_free_key($pubKeyId);

    if (!$ok) {
        return false;
    }

    $payloadJson = json_decode(
        base64url_decode($payload),
        true
    );

    if (!is_array($payloadJson)) {
        return false;
    }

    if (
        isset($payloadJson['exp']) &&
        time() > (int)$payloadJson['exp']
    ) {
        return false;
    }

    return $payloadJson;
}


if (
    isset($_GET['debug']) &&
    $_GET['debug'] === '1'
) {

    $token = get_bearer_token();

    $out = [
        'debug' => 1,
        'token_received' => $token ? true : false,
        'get' => $_GET
    ];

    if (
        isset($_GET['validate']) &&
        $_GET['validate'] === '1'
    ) {

        if ($token) {

            $valid = validate_jwt_rs256(
                $token,
                $publicKey
            );

            if ($valid) {

                $out['validation'] = [
                    'valid' => true,
                    'claims' => $valid
                ];

            } else {

                $out['validation'] = [
                    'valid' => false
                ];
            }

        } else {

            $out['validation'] = [
                'valid' => false,
                'error' => 'no_token'
            ];
        }
    }

    echo json_encode($out);

    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'GET') {

    try {

        $obj = new Producto();

        $tipo = $_GET['tipo'] ?? 'activos';

        if ($tipo === 'mas_vendidos') {

            $productos = $obj->MasVendidos();

        } elseif ($tipo === 'activos') {

            $productos = $obj->ProductosActivos();

        } else {

            http_response_code(400);

            echo json_encode([
                'respuesta' => 0,
                'mensaje' => 'Tipo de consulta no válido'
            ]);

            exit;
        }

        echo json_encode([
            'respuesta' => 1,
            'productos' => $productos
        ]);

        exit;

    } catch (Exception $e) {

        http_response_code(500);

        echo json_encode([
            'respuesta' => 0,
            'mensaje' => $e->getMessage()
        ]);

        exit;
    }
}
http_response_code(405);

echo json_encode([
    'respuesta' => 0,
    'mensaje' => 'Método no permitido'
]);

?>