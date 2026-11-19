<?php

namespace LoveMakeup\Tests\Modelo;

use PHPUnit\Framework\TestCase;
use LoveMakeup\Proyecto\Modelo\Notificacion;

class NotificacionContarTest extends TestCase
{
    public function testContarNotificaciones(): void
    {
        $modelo = new Notificacion();
        $conexion = $modelo->getConex1();

        // Conteo para la asesora: estado 1.
        $nuevas = $modelo->contarNuevas();

        // Conteo para el administrador: estados 1 y 4.
        $admin = $modelo->contarParaAdmin();

        // Consultar los valores esperados en la BD.
        $esperadasNuevas = (int) $conexion
            ->query(
                "SELECT COUNT(*) FROM notificaciones
                 WHERE estado = 1"
            )
            ->fetchColumn();

        $esperadasAdmin = (int) $conexion
            ->query(
                "SELECT COUNT(*) FROM notificaciones
                 WHERE estado IN (1, 4)"
            )
            ->fetchColumn();

        // Verificar los resultados.
        $this->assertIsInt($nuevas);
        $this->assertIsInt($admin);

        $this->assertSame($esperadasNuevas, $nuevas);
        $this->assertSame($esperadasAdmin, $admin);

        $this->assertGreaterThanOrEqual(0, $nuevas);
        $this->assertGreaterThanOrEqual(0, $admin);

        echo "\nNotificaciones para asesora: $nuevas";
        echo "\nNotificaciones para administrador: $admin\n";
    }
}
