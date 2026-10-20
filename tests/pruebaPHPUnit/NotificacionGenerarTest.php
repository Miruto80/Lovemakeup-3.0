<?php

namespace LoveMakeup\Tests\Modelo;

use PHPUnit\Framework\TestCase;
use LoveMakeup\Proyecto\Modelo\Notificacion;
use PDO;

class NotificacionGenerarTest extends TestCase
{
    public function testGenerarNotificacionesDePedidos(): void
    {
        $modelo = new Notificacion();
        $conexion = $modelo->getConex1();

        // Verificar que sea la base local de pruebas.
        $base = $conexion
            ->query("SELECT DATABASE()")
            ->fetchColumn();

        $this->assertSame('lovemakeupbd1', $base);

        // Consultar todos los pedidos que aún no
        // tienen una notificación.
        $sql = "
            SELECT p.id_pedido, p.tracking
            FROM pedido p
            LEFT JOIN notificaciones n
                ON n.id_pedido = p.id_pedido
            WHERE p.tipo IN (2, 3)
              AND n.id_pedido IS NULL
        ";

        $pendientes = $conexion
            ->query($sql)
            ->fetchAll(PDO::FETCH_ASSOC);

        // Evitar modificar pedidos ajenos a la prueba.
        $this->assertCount(3, $pendientes);

        $tracking = array_column($pendientes, 'tracking');
        sort($tracking);

        $this->assertSame([
            'TEST-REPORTE-WEB-01',
            'TEST-REPORTE-WEB-02',
            'TEST-REPORTE-WEB-03'
        ], $tracking);

        // Identificación para los triggers locales.
        $conexion->exec(
            "SET @app_cedula = '10200300'"
        );

        // Proteger las inserciones con una transacción.
        $conexion->beginTransaction();

        try {
            // Generar notificaciones automáticamente.
            $generadas = $modelo->generarDePedidos();

            $this->assertSame(3, $generadas);

            // Comprobar los registros creados.
            $sqlVerificar = "
                SELECT COUNT(*)
                FROM notificaciones n
                INNER JOIN pedido p
                    ON p.id_pedido = n.id_pedido
                WHERE p.tracking IN (
                    'TEST-REPORTE-WEB-01',
                    'TEST-REPORTE-WEB-02',
                    'TEST-REPORTE-WEB-03'
                )
                  AND n.estado = 1
            ";

            $registradas = (int) $conexion
                ->query($sqlVerificar)
                ->fetchColumn();

            $this->assertSame(3, $registradas);

            // Ejecutar nuevamente para comprobar
            // que no se generen duplicados.
            $duplicadas = $modelo->generarDePedidos();

            $this->assertSame(0, $duplicadas);

            // Guardar definitivamente las notificaciones.
            $conexion->commit();

            echo "\nNotificaciones generadas: $generadas";
            echo "\nNotificaciones registradas: $registradas";
            echo "\nNotificaciones duplicadas: $duplicadas\n";

        } catch (\Throwable $e) {
            if ($conexion->inTransaction()) {
                $conexion->rollBack();
            }

            throw $e;
        }
    }
}
