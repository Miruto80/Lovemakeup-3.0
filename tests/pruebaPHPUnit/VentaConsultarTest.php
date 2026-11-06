<?php

namespace LoveMakeup\Tests\Modelo;

use PHPUnit\Framework\TestCase;
use LoveMakeup\Proyecto\Modelo\Salida;

class VentaConsultarTest extends TestCase
{
    public function testConsultarVentas(): void
    {
        $modelo = new Salida();
        $conexion = $modelo->getConex1();

        // Confirmar la base de datos local.
        $base = $conexion
            ->query("SELECT DATABASE()")
            ->fetchColumn();

        $this->assertSame('lovemakeupbd1', $base);

        // Consultar las ventas desde el modelo.
        $ventas = $modelo->consultarVentas();

        $this->assertIsArray($ventas);

        // Comprobar que existen ventas para evaluar.
        $this->assertNotEmpty(
            $ventas,
            'No se encontraron ventas presenciales.'
        );

        foreach ($ventas as $venta) {
            $this->assertArrayHasKey('id_pedido', $venta);
            $this->assertArrayHasKey('tipo', $venta);
            $this->assertArrayHasKey('estado', $venta);
            $this->assertArrayHasKey('precio_total_usd', $venta);
            $this->assertArrayHasKey('precio_total_bs', $venta);

            $this->assertSame(1, (int)$venta['tipo']);

            $this->assertContains(
                (int)$venta['estado'],
                [1, 2]
            );
        }

        echo "\nVentas presenciales consultadas: "
            . count($ventas) . "\n";
    }
}
