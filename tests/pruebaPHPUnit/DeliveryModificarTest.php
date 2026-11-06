<?php

namespace LoveMakeup\Tests\Modelo;

use PHPUnit\Framework\TestCase;
use LoveMakeup\Proyecto\Modelo\Delivery;

class DeliveryModificarTest extends TestCase
{
    public function testModificarDelivery(): void
    {
        $delivery = new Delivery();

        // Identificar al administrador para la bitácora
        $conexion = $delivery->getConex1();
        $conexion->exec("SET @app_cedula = '10200300'");

        // Buscar únicamente el delivery creado anteriormente
        $id = null;

        foreach ($delivery->consultar() as $fila) {
            if (
                $fila['nombre'] === 'Carlos Mendoza' &&
                $fila['contacto'] === '0414-1234567'
            ) {
                $id = (int) $fila['id_delivery'];
                break;
            }
        }

        $this->assertNotNull($id, 'No se encontró el delivery de prueba.');

        // Modificar los datos del delivery
        $resultado = $delivery->procesarDelivery(json_encode([
            'operacion' => 'actualizar',
            'datos' => [
                'id_delivery' => $id,
                'nombre' => 'Carlos Mendoza Perez',
                'tipo' => 'Moto',
                'contacto' => '0414-1234567',
                'estatus' => 1
            ]
        ]));

        $this->assertSame(1, $resultado['respuesta'], $resultado['mensaje']);
        $this->assertSame('Delivery actualizado', $resultado['mensaje']);

        // Verificar que el nuevo nombre se guardó correctamente
        $datos = $delivery->consultarPorId($id);

        $this->assertSame('Carlos Mendoza Perez', $datos['nombre']);
    }
}
