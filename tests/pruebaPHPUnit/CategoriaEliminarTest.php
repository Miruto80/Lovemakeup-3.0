<?php

namespace LoveMakeup\Tests\Modelo;

use PHPUnit\Framework\TestCase;
use LoveMakeup\Proyecto\Modelo\Categoria;

class CategoriaEliminarTest extends TestCase
{
    public function testEliminarCategoria(): void
    {
        $categoria = new Categoria();

        // Identificar al administrador para la bitácora
        $conexion = $categoria->getConex1();
        $conexion->exec("SET @app_cedula = '10200300'");

        // Buscar únicamente nuestra categoría de prueba
        $id = null;

        foreach ($categoria->consultar() as $fila) {
            if ($fila['nombre'] === 'Categoria Modificada') {
                $id = $fila['id_categoria'];
                break;
            }
        }

        $this->assertNotNull($id, 'No se encontró la categoría de prueba.');

        // Eliminar lógicamente la categoría
        $resultado = $categoria->procesarCategoria(json_encode([
            'operacion' => 'eliminar',
            'datos' => [
                'id_categoria' => $id
            ]
        ]));

        $this->assertSame(1, $resultado['respuesta'], $resultado['mensaje']);
        $this->assertSame('Categoría eliminada', $resultado['mensaje']);

        // Verificar que ya no aparezca entre las categorías activas
        $ids = array_column($categoria->consultar(), 'id_categoria');

        $this->assertNotContains($id, $ids);
    }
}
