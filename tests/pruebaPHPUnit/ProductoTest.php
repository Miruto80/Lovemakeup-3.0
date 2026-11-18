<?php

namespace Tests\PruebaPHPUnit;

use PHPUnit\Framework\TestCase;
use LoveMakeup\Proyecto\Modelo\Producto;
use ReflectionClass;

/*||||||||||||||||||||||||||||||| CLASE TESTABLE CON REFLEXIÓN ||||||||||||||||||||||||||*/
class ProductoTestable {
    private Producto $producto;
    private ReflectionClass $reflection;

    public function __construct() {
        $this->producto = new Producto();
        $this->reflection = new ReflectionClass($this->producto);
    }

    private function invokePrivate(string $method, array $args = []) {
        $m = $this->reflection->getMethod($method);
        $m->setAccessible(true);
        return $m->invokeArgs($this->producto, $args);
    }

    /* Métodos privados obtenidos mediante Reflection */
    public function testVerificarProductoExistente($nombre, $id_marca) {
        return $this->invokePrivate('verificarProductoExistente', [$nombre, $id_marca]);
    }

    public function testEjecutarRegistro($datos) {
        return $this->invokePrivate('ejecutarRegistro', [$datos]);
    }

    public function testEjecutarActualizacion($datos) {
        return $this->invokePrivate('ejecutarActualizacion', [$datos]);
    }

    public function testEjecutarEliminacion($datos) {
        return $this->invokePrivate('ejecutarEliminacion', [$datos]);
    }

    public function testEjecutarCambioEstatus($datos) {
        return $this->invokePrivate('ejecutarCambioEstatus', [$datos]);
    }

    public function getProducto() {
        return $this->producto;
    }
}

/*||||||||||||||||||||||||||||||| CLASE DE TEST PRINCIPAL ||||||||||||||||||||||||||||||*/
class ProductoTest extends TestCase {

    private ProductoTestable $producto;

    protected function setUp(): void {
        $this->producto = new ProductoTestable();
    }

    /*|||||||||||| OPERACIÓN INVÁLIDA ||||||||||||*/
    public function testOperacionInvalida() {
        $productoReal = new Producto();

        $json = json_encode([
            'operacion' => 'desconocido',
            'datos' => []
        ]);

        $resultado = $productoReal->procesarProducto($json);

        $this->assertEquals(0, $resultado['respuesta']);
        $this->assertEquals('Operación no válida', $resultado['mensaje']);
    }

    /*|||||||||||| REGISTRAR PRODUCTO EXISTENTE ||||||||||||*/
    public function testRegistrarProductoExistente() {
        $json = json_encode([
            'operacion' => 'registrar',
            'datos' => [
                'nombre' => 'Bálsamo premium',
                'id_marca' => 1,
                'descripcion' => 'Prueba',
                'cantidad_mayor' => 10,
                'precio_mayor' => 20,
                'precio_detal' => 25,
                'stock_maximo' => 100,
                'stock_minimo' => 1,
                'imagenes' => ['imagen.png'],
                'id_categoria' => 1
            ]
        ]);

        $resultado = $this->producto->getProducto()->procesarProducto($json);

        $this->assertIsArray($resultado);
        $this->assertArrayHasKey('respuesta', $resultado);
    }

    /*|||||||||||| CONSULTAR PRODUCTOS ||||||||||||*/
    public function testConsultarProductos() {
        $resultado = $this->producto->getProducto()->consultar();

        $this->assertIsArray($resultado);

        if (empty($resultado)) {
            fwrite(STDOUT, "No se encontraron productos." . PHP_EOL);
            return;
        }

        fwrite(STDOUT, "Productos consultados:" . PHP_EOL);
        foreach ($resultado as $producto) {
            $this->assertIsArray($producto);
            $this->assertArrayHasKey('id_producto', $producto);
            $this->assertArrayHasKey('nombre', $producto);
            $this->assertArrayHasKey('estatus', $producto);
            $this->assertNotEmpty($producto['id_producto']);
            $this->assertNotEmpty($producto['nombre']);

            fwrite(
                STDOUT,
                sprintf(
                    "ID: %s | Nombre: %s | Estatus: %s%s",
                    $producto['id_producto'],
                    $producto['nombre'],
                    $producto['estatus'],
                    PHP_EOL
                )
            );
        }

        fwrite(STDOUT, "Prueba de consulta de productos finalizada correctamente." . PHP_EOL);
    }

    public function testEliminarProductoInexistenteDevuelveErrorGenerico() {
        $this->producto->getProducto()->getConex1();

        foreach ([-1, 'null'] as $idProducto) {
            $resultado = $this->producto->getProducto()->procesarProducto(json_encode([
                'operacion' => 'eliminar',
                'datos' => ['id_producto' => $idProducto]
            ], JSON_THROW_ON_ERROR));

            fwrite(STDOUT, "Resultado al eliminar ID " . json_encode($idProducto) . ":" . PHP_EOL);
            fwrite(STDOUT, json_encode($resultado, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_THROW_ON_ERROR) . PHP_EOL);

            $this->assertSame(0, $resultado['respuesta']);
            $this->assertSame('eliminar', $resultado['accion']);
            $this->assertSame('Ocurrió un error interno', $resultado['mensaje']);
            $this->assertStringNotContainsString('SQL', strtoupper($resultado['mensaje']));
            $this->assertStringNotContainsString('PDO', strtoupper($resultado['mensaje']));
        }
    }

    public function testRegistroPersisteDatosExactosYDevuelveJsonEstandar() {
        $producto = $this->producto->getProducto();
        $conexion = $producto->getConex1();

        $marca = $conexion->query('SELECT id_marca FROM marca LIMIT 1')->fetchColumn();
        $categoria = $conexion->query('SELECT id_categoria FROM categoria LIMIT 1')->fetchColumn();

        if ($marca === false || $categoria === false) {
            $this->markTestSkipped('Se requiere al menos una marca y una categoría en la base de pruebas.');
        }

        $sufijo = bin2hex(random_bytes(6));
        $nombre = 'QaPersistencia' . $sufijo;
        $descripcion = 'Descripcion exacta ' . $sufijo;

        try {
            $resultado = $producto->procesarProducto(json_encode([
                'operacion' => 'registrar',
                'datos' => [
                    'nombre' => $nombre,
                    'descripcion' => $descripcion,
                    'id_marca' => (int) $marca,
                    'cantidad_mayor' => 10,
                    'precio_mayor' => 20,
                    'precio_detal' => 25,
                    'stock_maximo' => 100,
                    'stock_minimo' => 1,
                    'id_categoria' => (int) $categoria,
                    'imagenes' => []
                ]
            ], JSON_THROW_ON_ERROR));

            fwrite(STDOUT, "Respuesta del registro:" . PHP_EOL);
            fwrite(STDOUT, json_encode($resultado, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_THROW_ON_ERROR) . PHP_EOL);

            $this->assertSame(1, $resultado['respuesta']);

            $respuestaJson = json_encode($resultado, JSON_THROW_ON_ERROR);
            $respuestaDecodificada = json_decode($respuestaJson, true, 512, JSON_THROW_ON_ERROR);
            $this->assertSame(['respuesta', 'accion', 'mensaje'], array_keys($respuestaDecodificada));
            $this->assertSame(1, $respuestaDecodificada['respuesta']);
            $this->assertSame('incluir', $respuestaDecodificada['accion']);
            $this->assertNotSame('', $respuestaDecodificada['mensaje']);

            $consulta = $conexion->prepare(
                'SELECT nombre, descripcion FROM producto WHERE nombre = :nombre AND id_marca = :id_marca ORDER BY id_producto DESC LIMIT 1'
            );
            $consulta->execute(['nombre' => $nombre, 'id_marca' => (int) $marca]);
            $productoPersistido = $consulta->fetch();

            fwrite(STDOUT, "Producto consultado inmediatamente después del registro:" . PHP_EOL);
            fwrite(STDOUT, json_encode($productoPersistido, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_THROW_ON_ERROR) . PHP_EOL);

            $this->assertIsArray($productoPersistido, 'El producto registrado debe poder consultarse inmediatamente.');
            $this->assertSame($nombre, $productoPersistido['nombre']);
            $this->assertSame($descripcion, $productoPersistido['descripcion']);
        } finally {
            $limpiar = $conexion->prepare(
                'UPDATE producto SET estatus = 0 WHERE nombre = :nombre AND id_marca = :id_marca'
            );
            $limpiar->execute(['nombre' => $nombre, 'id_marca' => (int) $marca]);
        }
    }

    /*|||||||||||| PRODUCTOS ACTIVOS ||||||||||||*/
    public function testProductosActivos() {
        $resultado = $this->producto->getProducto()->ProductosActivos();

        $this->assertIsArray($resultado);
        foreach ($resultado as $p) {
            $this->assertEquals(1, $p['estatus']);
        }

        fwrite(
            STDOUT,
            "Cantidad de productos activos: " . count($resultado) . PHP_EOL
        );
    }

    /*|||||||||||| CATEGORÍAS ||||||||||||*/
    public function testObtenerCategoria() {
        $resultado = $this->producto->getProducto()->obtenerCategoria();

        $this->assertIsArray($resultado);
        foreach ($resultado as $categoria) {
            $this->assertIsArray($categoria);
            $this->assertArrayHasKey('id_categoria', $categoria);
            $this->assertArrayHasKey('nombre', $categoria);
        }

        fwrite(STDOUT, "Categorías obtenidas:" . PHP_EOL);
        fwrite(STDOUT, print_r($resultado, true));
        fwrite(STDOUT, "Prueba de obtención de categorías finalizada correctamente." . PHP_EOL);
    }

    /*|||||||||||| MÉTODOS PRIVADOS ||||||||||||*/
    public function testVerificarProductoInexistente() {
        // nombre + ID de marca que no existe
        $resp = $this->producto->testVerificarProductoExistente('ProductoInexistenteXYZ', 9999);
        $this->assertFalse($resp);
        fwrite(STDOUT, "Prueba de producto inexistente finalizada correctamente." . PHP_EOL);
    }

    public function testVerificarProductoExistenteFallido() {
        // Producto real → Base de gotero con id_marca = 1 (ajusta si en BD es otro)
        $resp = $this->producto->testVerificarProductoExistente('Base de gotero', 1);
        $this->assertTrue($resp, "Debe devolver true porque el producto sí existe");
        fwrite(STDOUT, "Prueba de producto existente finalizada correctamente." . PHP_EOL);
    }

    public function testCambioEstatus() {
        $datos = [
            'id_producto' => 1,
            'estatus_actual' => 1
        ];

        $resultado = $this->producto->testEjecutarCambioEstatus($datos);

        $this->assertIsArray($resultado);
        $json = json_encode($resultado, JSON_THROW_ON_ERROR);
        $respuesta = json_decode($json, true, 512, JSON_THROW_ON_ERROR);

        $this->assertSame(
            ['respuesta', 'accion', 'nuevo_estatus', 'mensaje'],
            array_keys($respuesta)
        );
        $this->assertSame(1, $respuesta['respuesta']);
        $this->assertSame('cambiarEstatus', $respuesta['accion']);
        $this->assertContains($respuesta['nuevo_estatus'], [1, 2]);
        $this->assertNotSame('', trim($respuesta['mensaje']));

        fwrite(
            STDOUT,
            "JSON de respuesta:" . PHP_EOL .
            json_encode($respuesta, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_THROW_ON_ERROR) .
            PHP_EOL
        );
        fwrite(STDOUT, "Prueba de cambio de estatus finalizada correctamente." . PHP_EOL);
    }
    public function testRegistrarVariosProductos() {
    fwrite(STDOUT, "\n\n >---- INICIO DEL TEST DE REGISTRO MASIVO DE PRODUCTOS ------<\n");

    $cantidad = 500;

    for ($i = 1; $i <= $cantidad; $i++) {

        $nombre = "ProductoTest{$i}";

        $datosProducto = [
            'operacion' => 'registrar',
            'datos' => [
                'nombre' => $nombre,
                'id_marca' => 1,
                'descripcion' => "Descripción del producto {$i}",
                'cantidad_mayor' => 10,
                'precio_mayor' => 20 + $i,
                'precio_detal' => 25 + $i,
                'stock_maximo' => 100,
                'stock_minimo' => 1,
                'imagenes' => ["img{$i}.png"],
                'id_categoria' => 1
            ]
        ];

        $resultado = $this->producto->getProducto()->procesarProducto(json_encode($datosProducto));

        fwrite(STDOUT, "\n → Intento #{$i} | Nombre: {$nombre}\n");

        
        $this->assertIsArray($resultado);
        $this->assertArrayHasKey('accion', $resultado);

        
        $this->assertEquals('incluir', $resultado['accion']);

        
        if ($resultado['respuesta'] === 1) {

            fwrite(STDOUT, "  Registro exitoso.\n");
            $this->assertEquals(1, $resultado['respuesta']);

       
        } elseif ($resultado['respuesta'] === 0) {

            fwrite(STDOUT, "   Error: " . ($resultado['mensaje'] ?? 'Sin mensaje') . "\n");

            $this->assertTrue(
                isset($resultado['mensaje']) || isset($resultado['text']),
                "Debe traer mensaje de error en el intento #{$i}"
            );

            $mensaje = $resultado['mensaje'] ?? $resultado['text'];

            $this->assertContains(
                $mensaje,
                [
                    'El producto ya existe',
                    'La marca no existe',
                    'La categoría no existe'
                ],
                "Mensaje inesperado en el intento #{$i}"
            );

        } else {
            $this->fail("Respuesta inesperada en el intento #{$i}");
        }
    }

    fwrite(STDOUT, "\nPrueba de registro masivo finalizada correctamente.\n\n");
}
}
