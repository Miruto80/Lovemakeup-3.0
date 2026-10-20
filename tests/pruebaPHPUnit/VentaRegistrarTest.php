<?php

namespace Tests\PruebaPHPUnit;

use PHPUnit\Framework\TestCase;
use LoveMakeup\Proyecto\Modelo\Salida;
use PDO;

require_once __DIR__ . '/../../vendor/autoload.php';
require_once __DIR__ . '/../../config/config.php';

class VentaRegistrarTest extends TestCase
{
    public function testRegistrarVenta(): void
    {
        $this->assertContains(DB_HOST, [
            'localhost', '127.0.0.1', '::1'
        ]);

        $opciones = [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false
        ];

        // Anular el COMMIT real para poder revertir la venta.
        $db1 = new class(
            "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME_1 . ";charset=utf8mb4",
            DB_USER,
            DB_PASS,
            $opciones
        ) extends PDO {
            public function commit(): bool
            {
                return $this->inTransaction();
            }
        };

        $db2 = new PDO(
            "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME_2 . ";charset=utf8mb4",
            DB_USER_2,
            DB_PASS,
            $opciones
        );

        // Verificar que sean las bases de datos locales.
        $this->assertSame(
            'lovemakeupbd1',
            strtolower((string) $db1->query('SELECT DATABASE()')->fetchColumn())
        );
        $this->assertSame(
            'lovemakeupbds2',
            strtolower((string) $db2->query('SELECT DATABASE()')->fetchColumn())
        );

        // Comprobar que las tablas admitan ROLLBACK.
        foreach ([
            [$db1, DB_NAME_1, [
                'pedido', 'venta', 'pedido_detalles',
                'producto', 'detalle_pago'
            ]],
            [$db2, DB_NAME_2, ['bitacora']]
        ] as [$db, $nombreBD, $tablas]) {
            foreach ($tablas as $tabla) {
                $stmt = $db->prepare(
                    "SELECT ENGINE FROM information_schema.TABLES
                     WHERE TABLE_SCHEMA = ? AND TABLE_NAME = ?"
                );
                $stmt->execute([$nombreBD, $tabla]);

                $this->assertSame(
                    'InnoDB',
                    $stmt->fetchColumn(),
                    "La tabla {$tabla} debe ser InnoDB."
                );
            }
        }

        // Utilizar un cliente de una venta presencial existente.
        $cedula = $db1->query(
            "SELECT cedula FROM pedido
             WHERE tipo = 1 AND estatus IN (1,2)
             AND cedula IS NOT NULL
             ORDER BY id_pedido LIMIT 1"
        )->fetchColumn();

        $this->assertNotFalse($cedula);

        $stmt = $db2->prepare(
            "SELECT id_usuario FROM usuario
             WHERE cedula = ? AND estatus = 1 LIMIT 1"
        );
        $stmt->execute([(string) $cedula]);
        $idUsuario = $stmt->fetchColumn();

        $this->assertNotFalse($idUsuario);

        // Seleccionar un producto activo con existencias.
        $producto = $db1->query(
            "SELECT id_producto, stock_disponible, precio_detal
             FROM producto
             WHERE estatus = 1 AND stock_disponible > 0
             AND precio_detal > 0
             ORDER BY id_producto LIMIT 1"
        )->fetch();

        $this->assertIsArray($producto);

        // Seleccionar un método de pago activo.
        $metodo = $db1->query(
            "SELECT id_metodopago FROM metodo_pago
             WHERE estatus = 1
             ORDER BY CASE WHEN nombre LIKE '%Efectivo%'
                           THEN 0 ELSE 1 END,
                      id_metodopago
             LIMIT 1"
        )->fetchColumn();

        $this->assertNotFalse($metodo);

        $idProducto = (int) $producto['id_producto'];
        $stockAnterior = (int) $producto['stock_disponible'];
        $precio = (float) $producto['precio_detal'];

        $datos = [
            'id_persona' => (int) $idUsuario,
            'precio_total' => $precio,
            'precio_total_bs' => 0,
            'detalles' => [[
                'id_producto' => $idProducto,
                'cantidad' => 1,
                'precio_unitario' => $precio
            ]],
            'metodos_pago' => [[
                'id_metodopago' => (int) $metodo,
                'monto_usd' => $precio,
                'monto_bs' => 0
            ]]
        ];

        // Reutilizar las conexiones controladas.
        $salida = new class($db1, $db2) extends Salida {
            public function __construct(
                private PDO $dbVentas,
                private PDO $dbUsuarios
            ) {
                parent::__construct();
            }

            public function getConex1()
            {
                return $this->dbVentas;
            }

            public function getConex2()
            {
                return $this->dbUsuarios;
            }
        };

        $logAnterior = ini_get('error_log');
        ini_set(
            'error_log',
            PHP_OS_FAMILY === 'Windows' ? 'nul' : '/dev/null'
        );

        $idPedido = 0;

        try {
            // La bitácora también quedará dentro de una transacción.
            $db2->beginTransaction();

            $resultado = $salida->registrarVentaPublico($datos);

            $this->assertIsArray($resultado);
            $this->assertSame(
                1,
                (int) ($resultado['respuesta'] ?? 0),
                $resultado['mensaje'] ?? 'Error al registrar.'
            );

            $idPedido = (int) $resultado['id_pedido'];
            $this->assertGreaterThan(0, $idPedido);

            // Comprobar el pedido y su pago.
            $stmt = $db1->prepare(
                "SELECT tipo, estatus, id_pago
                 FROM pedido WHERE id_pedido = ?"
            );
            $stmt->execute([$idPedido]);
            $pedido = $stmt->fetch();

            $this->assertIsArray($pedido);
            $this->assertSame(1, (int) $pedido['tipo']);
            $this->assertSame(2, (int) $pedido['estatus']);
            $this->assertGreaterThan(0, (int) $pedido['id_pago']);

            $stmt = $db1->prepare(
                "SELECT COUNT(*) FROM venta WHERE id_pedido = ?"
            );
            $stmt->execute([$idPedido]);
            $this->assertEquals(1, $stmt->fetchColumn());

            $stmt = $db1->prepare(
                "SELECT cantidad FROM pedido_detalles
                 WHERE id_pedido = ? AND id_producto = ?"
            );
            $stmt->execute([$idPedido, $idProducto]);
            $this->assertEquals(1, $stmt->fetchColumn());

            // Comprobar descuento temporal de inventario.
            $stmt = $db1->prepare(
                "SELECT stock_disponible FROM producto
                 WHERE id_producto = ?"
            );
            $stmt->execute([$idProducto]);

            $this->assertSame(
                $stockAnterior - 1,
                (int) $stmt->fetchColumn()
            );

            // El método informó éxito, pero seguimos en transacción.
            $this->assertTrue($db1->inTransaction());

            echo "\nVenta registrada temporalmente";
            echo "\nPedido: " . $idPedido;
            echo "\nProducto: " . $idProducto;
            echo "\nStock anterior: " . $stockAnterior;
            echo "\nStock durante la venta: " . ($stockAnterior - 1);

        } finally {
            // Nunca conservar los cambios temporales.
            if ($db1->inTransaction()) {
                $db1->rollBack();
            }
            if ($db2->inTransaction()) {
                $db2->rollBack();
            }

            ini_set('error_log', $logAnterior);
        }

        // Confirmar que el inventario fue restaurado.
        $stmt = $db1->prepare(
            "SELECT stock_disponible FROM producto
             WHERE id_producto = ?"
        );
        $stmt->execute([$idProducto]);

        $this->assertSame(
            $stockAnterior,
            (int) $stmt->fetchColumn()
        );

        // Confirmar que el pedido temporal ya no existe.
        $stmt = $db1->prepare(
            "SELECT COUNT(*) FROM pedido WHERE id_pedido = ?"
        );
        $stmt->execute([$idPedido]);
        $this->assertEquals(0, $stmt->fetchColumn());

        echo "\nVenta revertida e inventario restaurado\n";
    }
}
