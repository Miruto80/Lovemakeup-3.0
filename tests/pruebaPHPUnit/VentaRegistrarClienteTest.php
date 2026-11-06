<?php

namespace Tests\PruebaPHPUnit;

use PHPUnit\Framework\TestCase;
use LoveMakeup\Proyecto\Modelo\Salida;
use PDO;

require_once __DIR__ . '/../../vendor/autoload.php';
require_once __DIR__ . '/../../config/config.php';

class VentaRegistrarClienteTest extends TestCase
{
    public function testRegistrarClienteNuevo(): void
    {
        // Conexión de prueba: impide confirmar la transacción.
        $db = new class(
            "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME_2 . ";charset=utf8mb4",
            DB_USER_2,
            DB_PASS,
            [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
        ) extends PDO {
            public function commit(): bool
            {
                return true;
            }
        };

        // La reversión requiere tablas transaccionales.
        $stmt = $db->prepare(
            "SELECT TABLE_NAME, ENGINE
             FROM information_schema.TABLES
             WHERE TABLE_SCHEMA = ?
               AND TABLE_NAME IN ('persona', 'usuario')"
        );
        $stmt->execute([DB_NAME_2]);
        $motores = $stmt->fetchAll(PDO::FETCH_KEY_PAIR);

        $this->assertCount(2, $motores);
        $this->assertSame('InnoDB', $motores['persona']);
        $this->assertSame('InnoDB', $motores['usuario']);

        $salida = new class($db) extends Salida {
            public function __construct(private PDO $dbPrueba)
            {
                parent::__construct();
            }

            public function getConex2()
            {
                return $this->dbPrueba;
            }
        };

        // Datos temporales de prueba.
        $cedula = (string) random_int(90000000, 99999999);

        $datos = [
            'cedula' => $cedula,
            'nombre' => 'Cliente',
            'apellido' => 'Prueba',
            'telefono' => '04141234567',
            'correo' => "cliente{$cedula}@example.com"
        ];

        try {
            $resultado = $salida->registrarClientePublico($datos);

            $this->assertIsArray($resultado);
            $this->assertTrue($resultado['success']);
            $this->assertGreaterThan(
                0,
                (int) $resultado['id_cliente']
            );

            // Verificar los registros creados.
            $consulta = $db->prepare(
                "SELECT COUNT(*) FROM persona WHERE cedula = ?"
            );
            $consulta->execute([$cedula]);
            $this->assertEquals(1, $consulta->fetchColumn());

            $consulta = $db->prepare(
                "SELECT COUNT(*) FROM usuario
                 WHERE cedula = ? AND id_rol = 2"
            );
            $consulta->execute([$cedula]);
            $this->assertEquals(1, $consulta->fetchColumn());

            echo "\nCliente registrado correctamente";
        } finally {
            // Revertir los registros temporales.
            if ($db->inTransaction()) {
                $db->rollBack();
            }
        }

        echo "\nTransaccion revertida correctamente\n";
    }
}
