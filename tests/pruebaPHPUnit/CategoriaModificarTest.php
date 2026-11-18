<?php

namespace LoveMakeup\Tests\Modelo;

use PHPUnit\Framework\TestCase;
use LoveMakeup\Proyecto\Modelo\Categoria;

class CategoriaModificarTest extends TestCase
{
    public function testModificarCategoria(): void
    {
        $categoria = new Categoria();

        // Identificar al administrador para la bitácora
        $conexion = $categoria->getConex1();
        $conexion->exec("SET @app_cedula = '10200300'");

        // Buscar la categoría creada en la prueba anterior
        $id = null;

        foreach ($categoria->consultar() as $fila) {
            if ($fila['nombre'] === 'Prueba rpoqlyxj') {
                $id = $fila['id_categoria'];
                break;
            }
        }

        $this->assertNotNull($id, 'No se encontró la categoría de prueba.');

        // Modificar la categoría
        $resultado = $categoria->procesarCategoria(json_encode([
            'operacion' => 'actualizar',
            'datos' => [
                'id_categoria' => $id,
                'nombre' => 'Categoria Modificada'
            ]
        ]));

        $this->assertSame(1, $resultado['respuesta'], $resultado['mensaje']);
        $this->assertSame('Categoría modificada', $resultado['mensaje']);

        // Verificar que el nuevo nombre se haya guardado
        $nombres = array_column($categoria->consultar(), 'nombre');
        $this->assertContains('Categoria Modificada', $nombres);
    }
}
