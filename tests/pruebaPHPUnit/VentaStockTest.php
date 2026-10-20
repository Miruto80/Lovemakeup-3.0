<?php

namespace Tests\PruebaPHPUnit;

use PHPUnit\Framework\TestCase;
use LoveMakeup\Proyecto\Modelo\Salida;

require_once __DIR__ . '/../../vendor/autoload.php';

class VentaStockTest extends TestCase
{
    public function testVerificarStock(): void
    {
        // Exponer el método protegido únicamente para la prueba
        $salida = new class extends Salida {
            public function consultarStock(int $idProducto): int
            {
                return $this->verificarStock($idProducto);
            }
        };

        $productos = $salida->consultarProductos();

        $this->assertIsArray($productos);
        $this->assertNotEmpty(
            $productos,
            'Debe existir al menos un producto activo con stock.'
        );

        $producto = $productos[0];

        $this->assertArrayHasKey('id_producto', $producto);
        $this->assertArrayHasKey('stock_disponible', $producto);

        $idProducto = (int) $producto['id_producto'];
        $stock = $salida->consultarStock($idProducto);

        $this->assertIsInt($stock);
        $this->assertGreaterThan(0, $stock);

        // Comparar el stock de ambas consultas
        $this->assertSame(
            (int) $producto['stock_disponible'],
            $stock
        );

        // Comprobar cantidades disponibles e insuficientes
        $this->assertTrue(1 <= $stock);
        $this->assertFalse(($stock + 1) <= $stock);

        echo "\nProducto consultado: " . $idProducto;
        echo "\nStock disponible: " . $stock;
        echo "\nCantidad superior al stock: " . ($stock + 1) . "\n";
    }
}
