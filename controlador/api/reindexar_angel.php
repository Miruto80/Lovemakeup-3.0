<?php

use LoveMakeup\Proyecto\Modelo\AngelReindexador;

header('Content-Type: application/json; charset=utf-8');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['respuesta' => 0, 'mensaje' => 'Método no permitido']);
    exit;
}

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
if (empty($_SESSION['id'])) {
    http_response_code(401);
    echo json_encode(['respuesta' => 0, 'mensaje' => 'Debe iniciar sesión']);
    exit;
}

require_once __DIR__ . '/../../vendor/autoload.php';
require_once __DIR__ . '/../permiso.php';

if (
    (int) ($_SESSION['nivel_rol'] ?? 0) !== 3
    || !tieneAcceso(6, 1)
) {
    http_response_code(403);
    echo json_encode(['respuesta' => 0, 'mensaje' => 'Solo un administrador autorizado puede reindexar']);
    exit;
}

try {
    $solicitud = json_decode(
        file_get_contents('php://input'),
        true,
        512,
        JSON_THROW_ON_ERROR
    );
    if (!is_array($solicitud)) {
        throw new InvalidArgumentException('El cuerpo debe ser un objeto JSON');
    }

    $tipo = $solicitud['tipo'] ?? '';
    if (!in_array($tipo, ['conocimiento', 'productos', 'todo'], true)) {
        throw new InvalidArgumentException('Tipo no válido: use conocimiento, productos o todo');
    }

    $id = $solicitud['id'] ?? null;
    if ($id !== null && (!filter_var($id, FILTER_VALIDATE_INT) || (int) $id < 1)) {
        throw new InvalidArgumentException('El ID debe ser un entero positivo');
    }
    $ids = $solicitud['ids'] ?? null;
    if ($ids !== null) {
        if (!is_array($ids) || count($ids) < 1 || count($ids) > 100) {
            throw new InvalidArgumentException('La lista de IDs debe contener entre 1 y 100 elementos');
        }
        foreach ($ids as $identificador) {
            if (!filter_var($identificador, FILTER_VALIDATE_INT) || (int) $identificador < 1) {
                throw new InvalidArgumentException('Todos los IDs deben ser enteros positivos');
            }
        }
        $ids = array_map('intval', $ids);
    }
    if ($id !== null) {
        $id = (int) $id;
    }
    if (isset($solicitud['solo_pendientes']) && !is_bool($solicitud['solo_pendientes'])) {
        throw new InvalidArgumentException('solo_pendientes debe ser booleano');
    }

    $resultado = AngelReindexador::reindexar(
        $tipo,
        $id,
        $ids,
        $solicitud['solo_pendientes'] ?? false
    );
    echo json_encode([
        'respuesta' => 1,
        'reindexacion' => $resultado,
    ], JSON_UNESCAPED_UNICODE);
} catch (JsonException | InvalidArgumentException $e) {
    http_response_code(422);
    echo json_encode(['respuesta' => 0, 'mensaje' => $e->getMessage()], JSON_UNESCAPED_UNICODE);
} catch (Throwable $e) {
    error_log('Error al reindexar Ángel: ' . $e->getMessage());
    http_response_code(502);
    echo json_encode([
        'respuesta' => 0,
        'mensaje' => 'No se pudo completar la reindexación de Ángel: ' . $e->getMessage(),
    ], JSON_UNESCAPED_UNICODE);
}
