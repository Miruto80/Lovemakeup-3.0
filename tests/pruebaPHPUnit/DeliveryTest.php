<?php

namespace LoveMakeup\Tests\Modelo;

use PHPUnit\Framework\TestCase;
use LoveMakeup\Proyecto\Modelo\Delivery;

class DeliveryTest extends TestCase
{
    public function testConsultarDelivery(): void
    {
        $delivery = new Delivery();

        $resultado = $delivery->consultar();

        $this->assertIsArray($resultado);

        foreach ($resultado as $fila) {
            $this->assertArrayHasKey('id_delivery', $fila);
            $this->assertArrayHasKey('nombre', $fila);
            $this->assertArrayHasKey('tipo', $fila);
            $this->assertArrayHasKey('contacto', $fila);
            $this->assertArrayHasKey('estatus', $fila);
        }
    }
}
