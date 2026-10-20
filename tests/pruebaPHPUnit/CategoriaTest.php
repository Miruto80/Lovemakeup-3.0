<?php

namespace LoveMakeup\Tests\Modelo;

use PHPUnit\Framework\TestCase;
use LoveMakeup\Proyecto\Modelo\Categoria;

class CategoriaTest extends TestCase
{
    private Categoria $categoria;

    protected function setUp(): void
    {
        $this->categoria = new Categoria();
    }

    // PRUEBA 1: CONSULTAR CATEGORÍAS
    public function testConsultarCategorias(): void
    {
        // Consultar las categorías registradas
        $resultado = $this->categoria->consultar();

        // Verificar que la consulta devuelva un arreglo
        $this->assertIsArray($resultado);

        // Verificar la estructura de las categorías
        foreach ($resultado as $fila) {
            $this->assertArrayHasKey('id_categoria', $fila);
            $this->assertArrayHasKey('nombre', $fila);
        }
    }
}
