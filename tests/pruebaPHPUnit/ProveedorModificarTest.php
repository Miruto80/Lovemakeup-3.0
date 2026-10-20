<?php

namespace LoveMakeup\Tests\Modelo;

use PHPUnit\Framework\TestCase;
use LoveMakeup\Proyecto\Modelo\Proveedor;

class ProveedorModificarTest extends TestCase
{
    public function testModificarProveedor(): void
    {
        $proveedor = new Proveedor();

        // Identificar al administrador para la bitácora
        $conexion = $proveedor->getConex1();
        $conexion->exec("SET @app_cedula = '10200300'");

        // Buscar únicamente el proveedor creado en la prueba anterior
        $id = null;

        foreach ($proveedor->consultar() as $fila) {
            if (
                $fila['nombre'] === 'Distribuidora Belleza' &&
                $fila['numero_documento'] === '123456789'
            ) {
                $id = $fila['id_proveedor'];
                break;
            }
        }

        $this->assertNotNull($id, 'No se encontró el proveedor de prueba.');

        // Modificar el proveedor
        $resultado = $proveedor->procesarProveedor(json_encode([
            'operacion' => 'actualizar',
            'datos' => [
                'id_proveedor' => $id,
                'tipo_documento' => 'J',
                'numero_documento' => '123456789',
                'nombre' => 'Distribuidora Belleza Central',
                'correo' => 'distribuidora.prueba@example.com',
                'telefono' => '0414-1234567',
                'direccion' => 'Avenida Principal Barquisimeto'
            ]
        ]));

        $this->assertSame(1, $resultado['respuesta'], $resultado['mensaje']);
        $this->assertSame('Proveedor actualizado', $resultado['mensaje']);

        // Verificar que el cambio se guardó en la base de datos
        $datos = $proveedor->consultarPorId((int) $id);

        $this->assertSame('Distribuidora Belleza Central', $datos['nombre']);
    }
}
