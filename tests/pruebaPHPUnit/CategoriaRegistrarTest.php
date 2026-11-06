<?php

namespace LoveMakeup\Tests\Modelo;

use PHPUnit\Framework\TestCase;
use LoveMakeup\Proyecto\Modelo\Categoria;

class CategoriaRegistrarTest extends TestCase
{
    public function testRegistrarCategoria(): void
    {
        $categoria = new Categoria();
        $conexion = $categoria->getConex1();
        $conexion->exec("SET @app_cedula = '10200300'");
        $nombre = 'Prueba ' . substr(str_shuffle('abcdefghijklmnopqrstuvwxyz'), 0, 8);

        $resultado = $categoria->procesarCategoria(json_encode([
            'operacion' => 'incluir',
            'datos' => ['nombre' => $nombre]
        ]));

        echo "\nRespuesta del sistema: " . json_encode($resultado, JSON_UNESCAPED_UNICODE) . "\n";
        $this->assertSame(1, $resultado['respuesta']);
        $this->assertSame('incluir', $resultado['accion']);
        $this->assertSame('Categoría creada', $resultado['mensaje']);

        echo "\nCategoría creada: $nombre\n";
    }
}
