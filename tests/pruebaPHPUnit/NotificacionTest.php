<?php

namespace LoveMakeup\Tests\Modelo;

use PHPUnit\Framework\TestCase;
use LoveMakeup\Proyecto\Modelo\Notificacion;

class NotificacionTest extends TestCase
{
    public function testConsultarNotificaciones(): void
    {
        $modelo = new Notificacion();
        $notificaciones = $modelo->getAll();

        $this->assertIsArray($notificaciones);

        foreach ($notificaciones as $notificacion) {
            $this->assertArrayHasKey('id_notificacion', $notificacion);
            $this->assertArrayHasKey('titulo', $notificacion);
            $this->assertArrayHasKey('mensaje', $notificacion);
            $this->assertArrayHasKey('fecha', $notificacion);
            $this->assertArrayHasKey('estado', $notificacion);

            $this->assertContains(
                (int) $notificacion['estado'],
                [1, 2, 3, 4]
            );
        }

        echo "\nNotificaciones consultadas: "
            . count($notificaciones) . "\n";
    }
}
