<?php

namespace LoveMakeup\Tests\Modelo;

use PHPUnit\Framework\TestCase;
use LoveMakeup\Proyecto\Modelo\Delivery;

class DeliveryEliminarTest extends TestCase
{
    public function testEliminarDelivery(): void
    {
        $delivery = new Delivery();

        // Identificar al administrador para la bitácora
        $conexion = $delivery->getConex1();
        $conexion->exec("SET @app_cedula = '10200300'");

        // Buscar únicamente el delivery de prueba
        $id = null;

        foreach ($delivery->consultar() as $fila) {
            if (
                $fila['nombre'] === 'Carlos Mendoza Perez' &&
                $fila['contacto'] === '0414-1234567'
            ) {
                $id = (int) $fila['id_delivery'];
                break;
            }
        }

        $this->assertNotNull($id, 'No se encontró el delivery de prueba.');
        $this->assertGreaterThan(0, $id);

        // Eliminar lógicamente el delivery
        $resultado = $delivery->procesarDelivery(json_encode([
            'operacion' => 'eliminar',
            'datos' => [
                'id_delivery' => $id
            ]
        ]));

        $this->assertSame(1, $resultado['respuesta'], $resultado['mensaje']);
        $this->assertSame('Delivery eliminado', $resultado['mensaje']);

        // Verificar que el delivery quedó eliminado lógicamente
        $datos = $delivery->consultarPorId($id);

        $this->assertSame(0, (int) $datos['estatus']);
    }
}
