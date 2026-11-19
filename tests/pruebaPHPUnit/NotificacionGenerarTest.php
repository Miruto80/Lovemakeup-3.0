<?php

namespace LoveMakeup\Tests\Modelo;

use PHPUnit\Framework\TestCase;
use LoveMakeup\Proyecto\Modelo\Notificacion;

class NotificacionGenerarTest extends TestCase
{
    public function testGenerarNotificacionesDePedidos(): void
    {
        $modelo = new Notificacion();
        $conexion = $modelo->getConex1();

        // Seguridad: trabajar solo con la base local.
        $base = $conexion->query("SELECT DATABASE()")
                         ->fetchColumn();

        $this->assertSame('lovemakeupbd1', $base);

        // Evitar generar notificaciones de pedidos ajenos.
        $sqlPendientes = "
            SELECT COUNT(*)
            FROM pedido p
            LEFT JOIN notificaciones n
                ON n.id_pedido = p.id_pedido
            WHERE p.tipo IN (2,3)
              AND p.fecha IS NOT NULL
              AND n.id_pedido IS NULL
        ";

        $pendientes = (int) $conexion
            ->query($sqlPendientes)->fetchColumn();

        $this->assertSame(0, $pendientes,
            'Existen pedidos pendientes ajenos a la prueba.');

        $conexion->exec("SET @app_cedula = '10200300'");
        $conexion->beginTransaction();

        try {
            // Crear datos temporales de prueba.
            $insertar = $conexion->prepare("
                INSERT INTO pedido
                    (tipo, fecha, estatus, tracking, precio_total_bs)
                VALUES
                    (:tipo, :fecha, '1', :tracking, 100)
            ");

            $casos = [
                ['2', date('Y-m-d H:i:s'), 'TEST-NOTI-WEB-01'],
                ['2', date('Y-m-d H:i:s'), 'TEST-NOTI-WEB-02'],
                ['3', date('Y-m-d H:i:s'), 'TEST-NOTI-RES-03'],
                ['2', null, 'TEST-NOTI-SIN-FECHA']
            ];

            $idsValidos = [];
            $idSinFecha = null;

            foreach ($casos as [$tipo, $fecha, $tracking]) {
                $insertar->execute([
                    'tipo' => $tipo,
                    'fecha' => $fecha,
                    'tracking' => $tracking
                ]);

                $id = (int) $conexion->lastInsertId();

                if ($fecha === null) {
                    $idSinFecha = $id;
                } else {
                    $idsValidos[] = $id;
                }
            }

            // Generar las notificaciones.
            $generadas = $modelo->generarDePedidos();

            $this->assertSame(3, $generadas);

            // Verificar las tres notificaciones válidas.
            $verificar = $conexion->prepare("
                SELECT COUNT(*)
                FROM notificaciones
                WHERE id_pedido IN (?, ?, ?)
                  AND estado = 1
                  AND fecha IS NOT NULL
            ");

            $verificar->execute($idsValidos);

            $this->assertSame(
                3,
                (int) $verificar->fetchColumn()
            );

            // El pedido sin fecha no debe generar notificación.
            $sinFecha = $conexion->prepare("
                SELECT COUNT(*)
                FROM notificaciones
                WHERE id_pedido = ?
            ");

            $sinFecha->execute([$idSinFecha]);

            $this->assertSame(
                0,
                (int) $sinFecha->fetchColumn()
            );

            // Comprobar que no se generan duplicados.
            $duplicadas = $modelo->generarDePedidos();

            $this->assertSame(0, $duplicadas);

        } finally {
            // Revertir todos los registros temporales.
            if ($conexion->inTransaction()) {
                $conexion->rollBack();
            }
        }
    }
}
