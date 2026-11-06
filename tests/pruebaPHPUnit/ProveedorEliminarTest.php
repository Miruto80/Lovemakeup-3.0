<?php

namespace LoveMakeup\Tests\Modelo;

use PHPUnit\Framework\TestCase;
use LoveMakeup\Proyecto\Modelo\Proveedor;

class ProveedorEliminarTest extends TestCase
{
    public function testEliminarProveedor(): void
    {
        $proveedor = new Proveedor();

        // Identificar al administrador para la bitácora
        $conexion = $proveedor->getConex1();
        $conexion->exec("SET @app_cedula = '10200300'");

        // Buscar únicamente el proveedor de prueba
        $id = null;

        foreach ($proveedor->consultar() as $fila) {
            if (
                $fila['nombre'] === 'Distribuidora Belleza Central' &&
                $fila['numero_documento'] === '123456789' &&
                $fila['tipo_documento'] === 'J'
            ) {
                $id = (int) $fila['id_proveedor'];
                break;
            }
        }

        $this->assertNotNull($id, 'No se encontró el proveedor de prueba.');

        // Eliminar lógicamente el proveedor
        $resultado = $proveedor->procesarProveedor(json_encode([
            'operacion' => 'eliminar',
            'datos' => [
                'id_proveedor' => $id
            ]
        ]));

        $this->assertSame(1, $resultado['respuesta'], $resultado['mensaje']);
        $this->assertSame('Proveedor eliminado', $resultado['mensaje']);

        // Verificar que el proveedor haya quedado inactivo
        $datos = $proveedor->consultarPorId($id);

        $this->assertSame(0, (int) $datos['estatus']);
    }
}
