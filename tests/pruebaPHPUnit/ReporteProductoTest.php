<?php

namespace LoveMakeup\Tests\Modelo;

use PHPUnit\Framework\TestCase;
use LoveMakeup\Proyecto\Modelo\Reporte;

class ReporteProductoTest extends TestCase
{
    public function testGenerarReporteProductos(): void
    {
        // 1. Consultar el total de productos
        $total = Reporte::countProducto();

        $this->assertIsInt($total);
        $this->assertGreaterThanOrEqual(0, $total);

        echo "\nRegistros del reporte de productos: $total\n";

        // 2. Generar el PDF en un proceso independiente
        $proyecto = dirname(__DIR__, 2);
        $archivo = tempnam(sys_get_temp_dir(), 'reporte_');

        $codigo = 'require ' .
            var_export($proyecto . '/vendor/autoload.php', true) .
            '; \LoveMakeup\Proyecto\Modelo\Reporte::producto();';

        try {
            $proceso = proc_open(
                [PHP_BINARY, '-r', $codigo],
                [
                    0 => ['pipe', 'r'],
                    1 => ['file', $archivo, 'w'],
                    2 => ['pipe', 'w']
                ],
                $tuberias,
                $proyecto
            );

            $this->assertIsResource($proceso);

            fclose($tuberias[0]);
            $errores = stream_get_contents($tuberias[2]);
            fclose($tuberias[2]);

            $salida = proc_close($proceso);

            $this->assertSame(0, $salida, $errores);
            $this->assertGreaterThan(100, filesize($archivo));

            // Verificar que se generó un PDF válido
            $cabecera = file_get_contents($archivo, false, null, 0, 5);

            $this->assertSame('%PDF-', $cabecera);

            echo "PDF de productos generado correctamente.\n";
        } finally {
            if (file_exists($archivo)) {
                unlink($archivo);
            }
        }
    }
}
