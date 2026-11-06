<?php

namespace LoveMakeup\Tests\Modelo;

use PHPUnit\Framework\TestCase;
use LoveMakeup\Proyecto\Modelo\Notificacion;

class NotificacionLeidaAdminTest extends TestCase
{
    public function testMarcarLeidaPorAdministrador(): void
    {
        $modelo = new Notificacion();
        $conexion = $modelo->getConex1();

        // Verificar que usamos la base de datos local.
        $base = $conexion
            ->query("SELECT DATABASE()")
            ->fetchColumn();

        $this->assertSame('lovemakeupbd1', $base);

        // Identificación para triggers locales.
        $conexion->exec(
            "SET @app_cedula = '10200300'"
        );

        $conexion->beginTransaction();

        try {
            // Buscar una notificación nueva de prueba.
            $stmt = $conexion->query("
                SELECT n.id_notificacion
                FROM notificaciones n
                INNER JOIN pedido p
                    ON p.id_pedido = n.id_pedido
                WHERE p.tracking LIKE 'TEST-REPORTE-WEB-%'
                  AND n.estado = 1
                ORDER BY n.id_notificacion ASC
                LIMIT 1
            ");

            $id = $stmt->fetchColumn();

            $this->assertNotFalse(
                $id,
                'No se encontró una notificación nueva de prueba.'
            );

            $id = (int) $id;

            // Contadores antes del cambio.
            $asesoraAntes = $modelo->contarNuevas();
            $adminAntes = $modelo->contarParaAdmin();

            // Marcar como leída por administrador.
            $resultado = $modelo->marcarLeida($id);

            $this->assertTrue($resultado);

            // Comprobar el nuevo estado.
            $stmtEstado = $conexion->prepare("
                SELECT estado
                FROM notificaciones
                WHERE id_notificacion = :id
            ");

            $stmtEstado->execute(['id' => $id]);

            $estado = (int) $stmtEstado->fetchColumn();

            $this->assertSame(2, $estado);

            // Contadores después del cambio.
            $asesoraDespues = $modelo->contarNuevas();
            $adminDespues = $modelo->contarParaAdmin();

            $this->assertSame(
                $asesoraAntes - 1,
                $asesoraDespues
            );

            $this->assertSame(
                $adminAntes - 1,
                $adminDespues
            );

            // Verificar que ya no esté visible
            // para ninguno de los dos roles.
            $notificaciones = $modelo->getAll();

            $visiblesAsesora = array_filter(
                $notificaciones,
                fn($n) => (int)$n['estado'] === 1
            );

            $visiblesAdmin = array_filter(
                $notificaciones,
                fn($n) => in_array(
                    (int)$n['estado'],
                    [1, 4],
                    true
                )
            );

            $idsAsesora = array_map(
                fn($n) => (int)$n['id_notificacion'],
                $visiblesAsesora
            );

            $idsAdmin = array_map(
                fn($n) => (int)$n['id_notificacion'],
                $visiblesAdmin
            );

            $this->assertNotContains($id, $idsAsesora);
            $this->assertNotContains($id, $idsAdmin);

            echo "\nNotificación evaluada: $id";
            echo "\nEstado inicial: 1";
            echo "\nEstado después de leer: $estado";
            echo "\nPendientes asesora: "
                . "$asesoraAntes -> $asesoraDespues";
            echo "\nPendientes administrador: "
                . "$adminAntes -> $adminDespues\n";

        } finally {
            // Restaurar el estado original.
            if ($conexion->inTransaction()) {
                $conexion->rollBack();
            }
        }

        echo "Cambios de prueba revertidos correctamente.\n";
    }
}
