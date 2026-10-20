<?php

namespace LoveMakeup\Tests\Modelo;

use PHPUnit\Framework\TestCase;
use LoveMakeup\Proyecto\Modelo\Notificacion;
use PDO;

class NotificacionLeidaAsesoraTest extends TestCase
{
    public function testMarcarLeidaPorAsesora(): void
    {
        $modelo = new Notificacion();
        $conexion = $modelo->getConex1();

        // Verificar que se utiliza la base de datos local.
        $base = $conexion
            ->query("SELECT DATABASE()")
            ->fetchColumn();

        $this->assertSame('lovemakeupbd1', $base);

        // Identificación necesaria para triggers locales.
        $conexion->exec(
            "SET @app_cedula = '10200300'"
        );

        // Transacción para no alterar definitivamente
        // las notificaciones de prueba.
        $conexion->beginTransaction();

        try {
            // Buscar una notificación nueva perteneciente
            // a nuestros pedidos de prueba.
            $sql = "
                SELECT n.id_notificacion
                FROM notificaciones n
                INNER JOIN pedido p
                    ON p.id_pedido = n.id_pedido
                WHERE p.tracking LIKE 'TEST-REPORTE-WEB-%'
                  AND n.estado = 1
                ORDER BY n.id_notificacion ASC
                LIMIT 1
            ";

            $id = $conexion
                ->query($sql)
                ->fetchColumn();

            $this->assertNotFalse(
                $id,
                'No se encontró una notificación nueva de prueba.'
            );

            $id = (int) $id;

            // Obtener los conteos iniciales.
            $asesoraAntes = $modelo->contarNuevas();
            $adminAntes = $modelo->contarParaAdmin();

            // Marcar como leída por asesora.
            $resultado = $modelo->marcarLeidaAsesora($id);

            $this->assertTrue($resultado);

            // Consultar el nuevo estado.
            $stmt = $conexion->prepare("
                SELECT estado
                FROM notificaciones
                WHERE id_notificacion = :id
            ");

            $stmt->execute(['id' => $id]);

            $estado = (int) $stmt->fetchColumn();

            $this->assertSame(4, $estado);

            // Verificar los conteos posteriores.
            $asesoraDespues = $modelo->contarNuevas();
            $adminDespues = $modelo->contarParaAdmin();

            $this->assertSame(
                $asesoraAntes - 1,
                $asesoraDespues
            );

            $this->assertSame(
                $adminAntes,
                $adminDespues
            );

            // Comprobar la visibilidad según el rol.
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
            $this->assertContains($id, $idsAdmin);

            echo "\nNotificación evaluada: $id";
            echo "\nEstado inicial: 1";
            echo "\nEstado después de leer: $estado";
            echo "\nPendientes asesora: "
                . "$asesoraAntes -> $asesoraDespues";
            echo "\nPendientes administrador: "
                . "$adminAntes -> $adminDespues\n";

        } finally {
            // Recuperar el estado original para
            // poder repetir la prueba sin problemas.
            if ($conexion->inTransaction()) {
                $conexion->rollBack();
            }
        }

        echo "Cambios de prueba revertidos correctamente.\n";
    }
}
