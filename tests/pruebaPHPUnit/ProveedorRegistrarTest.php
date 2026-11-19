<?php

namespace LoveMakeup\Tests\Modelo;

use PHPUnit\Framework\TestCase;
use LoveMakeup\Proyecto\Modelo\Proveedor;

class ProveedorRegistrarTest extends TestCase
{
    public function testRegistrarProveedor(): void
    {
        $proveedor = new Proveedor();

        // Identificar al administrador para la bitácora
        $conexion = $proveedor->getConex1();
        $conexion->exec("SET @app_cedula = '10200300'");

        $resultado = $proveedor->procesarProveedor(json_encode([
            'operacion' => 'registrar',
            'datos' => [
                'tipo_documento' => 'J',
                'numero_documento' => '123456789',
                'nombre' => 'Distribuidora Belleza',
                'correo' => 'distribuidora.prueba@example.com',
                'telefono' => '0414-1234567',
                'direccion' => 'Avenida Principal Barquisimeto'
            ]
        ]));

        echo "\nRespuesta del sistema: " .
            json_encode($resultado, JSON_UNESCAPED_UNICODE) . "\n";

        $this->assertSame(1, $resultado['respuesta'], $resultado['mensaje']);
        $this->assertSame('incluir', $resultado['accion']);
        $this->assertSame('Proveedor registrado', $resultado['mensaje']);
    }
}
