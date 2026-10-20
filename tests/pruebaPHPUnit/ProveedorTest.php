<?php

namespace LoveMakeup\Tests\Modelo;

use PHPUnit\Framework\TestCase;
use LoveMakeup\Proyecto\Modelo\Proveedor;

class ProveedorTest extends TestCase
{
    public function testConsultarProveedores(): void
    {
        $proveedor = new Proveedor();

        $resultado = $proveedor->consultar();

        $this->assertIsArray($resultado);

        foreach ($resultado as $fila) {
            $this->assertArrayHasKey('id_proveedor', $fila);
            $this->assertArrayHasKey('numero_documento', $fila);
            $this->assertArrayHasKey('tipo_documento', $fila);
            $this->assertArrayHasKey('nombre', $fila);
            $this->assertArrayHasKey('correo', $fila);
            $this->assertArrayHasKey('telefono', $fila);
            $this->assertArrayHasKey('direccion', $fila);
        }
    }
}
