<?php

namespace LoveMakeup\Tests\Modelo;

use PHPUnit\Framework\TestCase;
use LoveMakeup\Proyecto\Modelo\Delivery;

class DeliveryRegistrarTest extends TestCase
{
    public function testRegistrarDelivery(): void
    {
        $delivery = new Delivery();

        // Identificar al administrador para la bitácora
        $conexion = $delivery->getConex1();
        $conexion->exec("SET @app_cedula = '10200300'");

        $resultado = $delivery->procesarDelivery(json_encode([
            'operacion' => 'registrar',
            'datos' => [
                'nombre' => 'Carlos Mendoza',
                'tipo' => 'Moto',
                'contacto' => '0414-1234567',
                'estatus' => 1
            ]
        ]));

        echo "\nRespuesta del sistema: " .
            json_encode($resultado, JSON_UNESCAPED_UNICODE) . "\n";

        $this->assertSame(1, $resultado['respuesta'], $resultado['mensaje']);
        $this->assertSame('incluir', $resultado['accion']);
        $this->assertSame('Delivery registrado', $resultado['mensaje']);
    }
}
