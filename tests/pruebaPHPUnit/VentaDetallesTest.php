<?php

namespace Tests\PruebaPHPUnit;

use PHPUnit\Framework\TestCase;
use LoveMakeup\Proyecto\Modelo\Salida;

require_once __DIR__ . '/../../vendor/autoload.php';

class VentaDetallesTest extends TestCase
{
    public function testConsultarDetallesVenta(): void
    {
        $salida = new Salida();

        // Venta presencial existente en la base de datos local
        $idPedido = 1;

        // Consultar los productos asociados a la venta
        $detalles = $salida->consultarDetallesPedido($idPedido);

        // Verificar que la consulta devuelva resultados
        $this->assertIsArray($detalles);
        $this->assertNotEmpty(
            $detalles,
            'La venta debe tener productos registrados.'
        );

        foreach ($detalles as $detalle) {

            // Verificar los campos del producto
            $this->assertArrayHasKey('id_pedido', $detalle);
            $this->assertArrayHasKey('id_producto', $detalle);
            $this->assertArrayHasKey('nombre_producto', $detalle);
            $this->assertArrayHasKey('cantidad', $detalle);
            $this->assertArrayHasKey('precio_unitario', $detalle);
            $this->assertArrayHasKey('precio_total', $detalle);

            // Verificar los datos de la venta
            $this->assertEquals(
                $idPedido,
                (int) $detalle['id_pedido']
            );

            $this->assertNotEmpty($detalle['nombre_producto']);

            $this->assertGreaterThan(
                0,
                (int) $detalle['cantidad']
            );

            $this->assertGreaterThan(
                0,
                (float) $detalle['precio_unitario']
            );

            // Comprobar que el subtotal sea correcto
            $subtotal = (int) $detalle['cantidad']
                * (float) $detalle['precio_unitario'];

            $this->assertEqualsWithDelta(
                $subtotal,
                (float) $detalle['precio_total'],
                0.01
            );
        }

        echo "\nVenta consultada: " . $idPedido;
        echo "\nProductos encontrados: " . count($detalles) . "\n";
    }
}
