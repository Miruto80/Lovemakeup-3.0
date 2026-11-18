<?php
/**
 * =====================================================================
 *  AUDITORÍA VentaWeb — Lote B de Integración (BD real local)
 *  Regla: todo dato insertado lleva referencia única 'AUD-...' y se
 *  limpia al final de cada test (incluida restauración de stock).
 *
 *  Mapeo planillas prueba.docx:
 *   - 2.2 Cálculos Matemáticos (mayor/detal + total)      [2 tests]
 *   - 3.2 Persistencia Correcta (carácter por carácter)   [1 test]
 *   - 3.1 Mensajes de Error Seguros (inexistente/stock)   [2 tests]
 *   - 1.3 Datos Vacíos/Nulos (sin carrito / sin entrega)  [2 tests]
 *   - 3.3 Estructura de Datos Estándar (JSON/respuesta)   [1 test]
 * =====================================================================
 */
namespace Tests\PruebaPHPUnit;

use PHPUnit\Framework\TestCase;
use LoveMakeup\Proyecto\Modelo\VentaWeb;
use PDO;
use TypeError;

class VentaWebAuditIntegracionTest extends TestCase
{
    private VentaWeb $venta;
    private PDO $db;

    protected function setUp(): void
    {
        $this->venta = new VentaWeb();
        $this->db = $this->venta->getConex1();
    }

    private function ref(): string
    {
        return 'AUD-' . strtoupper(bin2hex(random_bytes(4)));
    }

    private function stockProducto(int $id): int
    {
        $st = $this->db->prepare('SELECT stock_disponible FROM producto WHERE id_producto = :id');
        $st->execute(['id' => $id]);
        return (int)($st->fetch(PDO::FETCH_ASSOC)['stock_disponible'] ?? 0);
    }

    private function pedidoPorReferencia(string $ref): ?array
    {
        $st = $this->db->prepare(
            'SELECT p.id_pedido, p.id_direccion, dp.id_pago
             FROM pedido p
             JOIN detalle_pago dp   ON dp.id_pago = p.id_pago
             JOIN referencia_pago rp ON rp.id_pago = dp.id_pago
             WHERE rp.referencia = :ref
             LIMIT 1'
        );
        $st->execute(['ref' => $ref]);
        $row = $st->fetch(PDO::FETCH_ASSOC);
        return $row ?: null;
    }

    /** Borra la huella del pedido de prueba y repone el stock consumido. */
    private function limpiar(string $ref): void
    {
        $row = $this->pedidoPorReferencia($ref);
        if (!$row) return;

        $det = $this->db->prepare('SELECT id_producto, cantidad FROM pedido_detalles WHERE id_pedido = :p');
        $det->execute(['p' => $row['id_pedido']]);
        $items = $det->fetchAll(PDO::FETCH_ASSOC);

        // Orden respetando FKs: comprobante (hijo) -> desligar pedido.id_pago ->
        // referencia -> detalle_pago -> pedido_detalles -> pedido -> direccion -> stock
        $st = $this->db->prepare('DELETE FROM comprobante_pago WHERE id_pago = :x');
        $st->execute(['x' => $row['id_pago']]);
        $st = $this->db->prepare('UPDATE pedido SET id_pago = NULL WHERE id_pedido = :x');
        $st->execute(['x' => $row['id_pedido']]);
        $st = $this->db->prepare('DELETE FROM referencia_pago WHERE id_pago = :x');
        $st->execute(['x' => $row['id_pago']]);
        $st = $this->db->prepare('DELETE FROM detalle_pago WHERE id_pago = :x');
        $st->execute(['x' => $row['id_pago']]);
        $st = $this->db->prepare('DELETE FROM pedido_detalles WHERE id_pedido = :x');
        $st->execute(['x' => $row['id_pedido']]);
        $st = $this->db->prepare('DELETE FROM pedido WHERE id_pedido = :x');
        $st->execute(['x' => $row['id_pedido']]);
        $st = $this->db->prepare('DELETE FROM direccion WHERE id_direccion = :x');
        $st->execute(['x' => $row['id_direccion']]);

        foreach ($items as $i) {
            $st = $this->db->prepare('UPDATE producto SET stock_disponible = stock_disponible + :c WHERE id_producto = :i');
            $st->execute(['c' => $i['cantidad'], 'i' => $i['id_producto']]);
        }
    }

    /* =================================================================
     *  PLANILLA 2.2 — Cálculos Matemáticos (mayor / detal)
     * ================================================================= */

    public function testCalculoPrecioDetalCuandoCantidadMenorAlUmbral(): void
    {
        $ref = $this->ref();
        $stock0 = $this->stockProducto(6); // Lip glaze: umbral 10, pMayor 3.60, pDetal 4.70
        $this->assertGreaterThan(0, $stock0, 'Producto 6 ausente o sin stock en BD local');

        $datos = ['datos' => [
            'tipo' => '2', 'fecha' => date('Y-m-d'), 'estado' => 1,
            'id_persona' => 10200999,
            'id_metodopago' => 1,
            'banco' => '0102-Banco De Venezuela', 'banco_destino' => '0105-Banco Mercantil',
            'referencia_bancaria' => $ref, 'telefono_emisor' => '04141234567',
            'monto' => 42.30, 'monto_usd' => 42.30,
            'precio_total_usd' => 42.30, 'precio_total_bs' => 42.30,
            'id_metodoentrega' => 4, 'direccion_envio' => 'Retiro en Tienda Fisica (Barquisimeto)',
            'sucursal_envio' => '', 'id_delivery' => null, 'imagen' => '',
            'carrito' => [['id' => 6, 'cantidad' => 9, 'cantidad_mayor' => 10, 'precio_detal' => 4.70, 'precio_mayor' => 3.60]],
        ]];
        $res = $this->venta->procesarPedido(json_encode($datos));

        try {
            fwrite(STDOUT, "\n[2.2] 9u (< umbral 10): precio esperado pDetal=4.70 | success=" . (int)$res['success'] . " id=" . ($res['id_pedido'] ?? '-') . "\n");

            $this->assertTrue($res['success'], 'Falló registrar pedido de prueba: ' . $res['message']);

            // precio_unitario persistido debe ser precio_detal (9 < 10)
            $st = $this->db->prepare('SELECT precio_unitario, cantidad FROM pedido_detalles WHERE id_pedido = :p');
            $st->execute(['p' => $res['id_pedido']]);
            $d = $st->fetch(PDO::FETCH_ASSOC);

            $this->assertEquals(4.70, (float)$d['precio_unitario'], 'El sistema NO aplicó precio_detal con cantidad 9 < umbral 10');
            $this->assertEquals(9, (int)$d['cantidad']);
            $this->assertEquals($stock0 - 9, $this->stockProducto(6), 'El stock no se descontó exactamente 9 unidades');
        } finally {
            $this->limpiar($ref);
        }
        $this->assertEquals($stock0, $this->stockProducto(6), 'El stock quedó alterado tras la limpieza');
    }

    public function testCalculoPrecioMayorCuandoCantidadAlcanzaElUmbral(): void
    {
        $ref = $this->ref();
        $stock0 = $this->stockProducto(6);
        $this->assertGreaterThan(10, $stock0, 'Producto 6 sin stock suficiente');

        $datos = ['datos' => [
            'tipo' => '2', 'fecha' => date('Y-m-d'), 'estado' => 1,
            'id_persona' => 10200999,
            'id_metodopago' => 1,
            'banco' => '0102-Banco De Venezuela', 'banco_destino' => '0105-Banco Mercantil',
            'referencia_bancaria' => $ref, 'telefono_emisor' => '04141234567',
            'monto' => 36.00, 'monto_usd' => 36.00,
            'precio_total_usd' => 36.00, 'precio_total_bs' => 36.00,
            'id_metodoentrega' => 4, 'direccion_envio' => 'Retiro en Tienda Fisica (Barquisimeto)',
            'sucursal_envio' => '', 'id_delivery' => null, 'imagen' => '',
            'carrito' => [['id' => 6, 'cantidad' => 10, 'cantidad_mayor' => 10, 'precio_detal' => 4.70, 'precio_mayor' => 3.60]],
        ]];
        $res = $this->venta->procesarPedido(json_encode($datos));

        try {
            fwrite(STDOUT, "\n[2.2] 10u (= umbral 10): precio esperado pMayor=3.60 | success=" . (int)$res['success'] . "\n");

            $this->assertTrue($res['success'], 'Falló registrar pedido de prueba: ' . $res['message']);

            $st = $this->db->prepare('SELECT precio_unitario FROM pedido_detalles WHERE id_pedido = :p');
            $st->execute(['p' => $res['id_pedido']]);
            $d = $st->fetch(PDO::FETCH_ASSOC);

            $this->assertEquals(3.60, (float)$d['precio_unitario'], 'El sistema NO aplicó precio_mayor al alcanzar el umbral (10 >= 10)');
            $this->assertEquals($stock0 - 10, $this->stockProducto(6));
        } finally {
            $this->limpiar($ref);
        }
        $this->assertEquals($stock0, $this->stockProducto(6));
    }

    /* =================================================================
     *  PLANILLA 3.2 — Persistencia Correcta (carácter por carácter)
     * ================================================================= */

    public function testPersistenciaCoincideCaracterPorCaracter(): void
    {
        $ref = $this->ref();
        $dir   = 'Zona norte Parroquia Tamaca Sector Tamaca Centro Auditoria 5';
        $tel   = '04141234567';
        $com   = 'https://res.cloudinary.com/demo/auditoria_comprobante_test.jpg';
        $stock0 = $this->stockProducto(8); // Lip oil rosado: pDetal 2.70, stock>
        $this->assertGreaterThan(2, $stock0, 'Producto 8 ausente');

        $datos = ['datos' => [
            'tipo' => '2', 'fecha' => date('Y-m-d'), 'estado' => 1,
            'id_persona' => 10200999,
            'id_metodopago' => 1,
            'banco' => '0102-Banco De Venezuela', 'banco_destino' => '0105-Banco Mercantil',
            'referencia_bancaria' => $ref, 'telefono_emisor' => $tel,
            'monto' => 5.40, 'monto_usd' => 5.40,
            'precio_total_usd' => 5.40, 'precio_total_bs' => 5.40,
            'id_metodoentrega' => 2, 'direccion_envio' => 'MRW Sucursal Barquisimeto Centro',
            'sucursal_envio' => '2140', 'id_delivery' => null, 'imagen' => $com,
            'carrito' => [['id' => 8, 'cantidad' => 2, 'cantidad_mayor' => 10, 'precio_detal' => 2.70, 'precio_mayor' => 1.75]],
        ]];
        $res = $this->venta->procesarPedido(json_encode($datos));

        try {
            $this->assertTrue($res['success'], 'Falló: ' . $res['message']);
            $idP = $res['id_pedido'];

            // composición de la fila del pedido
            $st = $this->db->prepare('SELECT tipo, estatus, precio_total_usd, cedula, id_pago, id_direccion FROM pedido WHERE id_pedido = :p');
            $st->execute(['p' => $idP]);
            $ped = $st->fetch(PDO::FETCH_ASSOC);
            $this->assertNotEmpty($ped['id_direccion'], 'El pedido no quedó ligado a una direccion');

            // fila de direccion
            $st = $this->db->prepare('SELECT direccion_envio, sucursal_envio, id_metodoentrega, id_delivery FROM direccion WHERE id_direccion = :d');
            $st->execute(['d' => $ped['id_direccion']]);
            $dirRow = $st->fetch(PDO::FETCH_ASSOC);

            // fila de pago + referencia + comprobante
            $st = $this->db->prepare('SELECT monto, monto_usd, id_metodopago FROM detalle_pago WHERE id_pago = :x');
            $st->execute(['x' => $ped['id_pago']]);
            $pago = $st->fetch(PDO::FETCH_ASSOC);
            $st = $this->db->prepare('SELECT banco_emisor, banco_receptor, referencia, telefono_emisor FROM referencia_pago WHERE id_pago = :x');
            $st->execute(['x' => $ped['id_pago']]);
            $refRow = $st->fetch(PDO::FETCH_ASSOC);
            $st = $this->db->prepare('SELECT COUNT(*) c FROM comprobante_pago WHERE id_pago = :x');
            $st->execute(['x' => $ped['id_pago']]);
            $compCount = (int)$st->fetch()['c'];

            fwrite(STDOUT, "\n[3.2] Pedido persistido=>" . json_encode([
                'tipo' => $ped['tipo'], 'estatus' => $ped['estatus'], 'total' => $ped['precio_total_usd'],
                'ref' => $refRow['referencia'], 'tel' => $refRow['telefono_emisor'],
                'dir' => $dirRow['direccion_envio'], 'suc' => $dirRow['sucursal_envio'],
                'comprobante' => $compCount,
            ], JSON_UNESCAPED_SLASHES) . "\n");

            $this->assertEquals('2',              (string)$ped['tipo'], 'El tipo no se persistió como web');
            $this->assertEquals('1',              (string)$ped['estatus'], 'El estatus no quedó como 1 (verificar pago)');
            $this->assertEquals(5.40,             (float)$ped['precio_total_usd'], 'El total no coincide');
            $this->assertEquals('MRW Sucursal Barquisimeto Centro', $dirRow['direccion_envio'], 'La dirección no coincide carácter por carácter');
            $this->assertEquals('2140',           $dirRow['sucursal_envio'], 'La sucursal no coincide');
            $this->assertEquals(2,                (int)$dirRow['id_metodoentrega'], 'El método de entrega no se asoció');
            $this->assertEquals(5.40,             (float)$pago['monto'], 'El monto Bs no coincide');
            $this->assertEquals(5.40,             (float)$pago['monto_usd'], 'El monto USD no coincide');
            $this->assertEquals('0102-Banco De Venezuela', $refRow['banco_emisor'], 'El banco emisor no coincide');
            $this->assertEquals('0105-Banco Mercantil', $refRow['banco_receptor'], 'El banco receptor no coincide');
            $this->assertEquals($ref, $refRow['referencia'], 'La referencia NO coincide carácter por carácter');
            $this->assertEquals($tel, $refRow['telefono_emisor'], 'El teléfono emisor no coincide');
            $this->assertEquals(1, $compCount, 'El comprobante URL no se persistió');

            $st = $this->db->prepare('SELECT precio_unitario FROM pedido_detalles WHERE id_pedido = :p');
            $st->execute(['p' => $idP]);
            $this->assertEquals(2.70, (float)$st->fetch()['precio_unitario']);
        } finally {
            $this->limpiar($ref);
        }
        $this->assertEquals($stock0, $this->stockProducto(8));
    }

    /* =================================================================
     *  PLANILLA 3.1 — Mensajes de Error Seguros
     * ================================================================= */

    public function testErrorProductoInexistenteEsGenericoSinTrazas(): void
    {
        $ref = $this->ref();
        $stock0 = $this->stockProducto(6);

        $datos = ['datos' => [
            'tipo' => '2', 'fecha' => date('Y-m-d'), 'estado' => 1,
            'id_persona' => 10200999, 'id_metodopago' => 1,
            'banco' => '0102-Banco De Venezuela', 'banco_destino' => '0105-Banco Mercantil',
            'referencia_bancaria' => $ref, 'telefono_emisor' => '04141234567',
            'monto' => 1.00, 'monto_usd' => 1.00, 'precio_total_usd' => 1.00, 'precio_total_bs' => 1.00,
            'id_metodoentrega' => 4, 'direccion_envio' => 'Retiro en Tienda Fisica',
            'sucursal_envio' => '', 'id_delivery' => null, 'imagen' => '',
            'carrito' => [['id' => 999999, 'cantidad' => 1, 'cantidad_mayor' => 10, 'precio_detal' => 1.00, 'precio_mayor' => 1.00]],
        ]];
        $res = $this->venta->procesarPedido(json_encode($datos));

        fwrite(STDOUT, "\n[3.1] producto inexistente -> msg='" . ($res['message'] ?? '') . "'\n");

        $this->assertFalse($res['success']);
        $this->assertSame('Producto no encontrado', $res['message'], 'El mensaje expuso información sensible');
        $this->assertStringNotContainsString('SQL', $res['message']);
        $this->assertStringNotContainsString('Fatal', $res['message']);
        $this->assertStringNotContainsString('Warning', $res['message']);
        $this->assertNull($this->pedidoPorReferencia($ref), 'Se escribió un pedido aún fallando (transacción no revertida)');
        $this->assertEquals($stock0, $this->stockProducto(6), 'El stock cambió sin pedido creado');
    }

    public function testErrorStockInsuficienteRevierteTodo(): void
    {
        $ref = $this->ref();
        $stock6 = $this->stockProducto(6);
        $stock8 = $this->stockProducto(8);

        $datos = ['datos' => [
            'tipo' => '2', 'fecha' => date('Y-m-d'), 'estado' => 1,
            'id_persona' => 10200999, 'id_metodopago' => 1,
            'banco' => '0102-Banco De Venezuela', 'banco_destino' => '0105-Banco Mercantil',
            'referencia_bancaria' => $ref, 'telefono_emisor' => '04141234567',
            'monto' => 1.00, 'monto_usd' => 1.00, 'precio_total_usd' => 1.00, 'precio_total_bs' => 1.00,
            'id_metodoentrega' => 4, 'direccion_envio' => 'Retiro en Tienda Fisica',
            'sucursal_envio' => '', 'id_delivery' => null, 'imagen' => '',
            'carrito' => [
                ['id' => 6, 'cantidad' => 1,  'cantidad_mayor' => 10, 'precio_detal' => 4.70, 'precio_mayor' => 3.60],
                ['id' => 8, 'cantidad' => $stock8 + 5000, 'cantidad_mayor' => 10, 'precio_detal' => 2.70, 'precio_mayor' => 1.75],
            ],
        ]];
        $res = $this->venta->procesarPedido(json_encode($datos));

        fwrite(STDOUT, "\n[3.1] stock insuficiente (2do item) -> msg='" . ($res['message'] ?? '') . "'\n");

        $this->assertFalse($res['success']);
        $this->assertSame('Stock insuficiente', $res['message']);
        $this->assertNull($this->pedidoPorReferencia($ref), 'La transacción NO revirtió: se quedó el pedido insertado');
        // el primer item se descontó y luego se revirtió → stock intacto
        $this->assertEquals($stock6, $this->stockProducto(6), 'El stock del primer item no se restauró (rollback fallido)');
        $this->assertEquals($stock8, $this->stockProducto(8));
    }

    /* =================================================================
     *  PLANILLA 1.3 — Datos Vacíos / Nulos
     * ================================================================= */

    public function testSinCarritoProvocaTypeError500NoControlado(): void
    {
        $ref = $this->ref();
        $datos = ['datos' => [
            'tipo' => '2', 'fecha' => date('Y-m-d'), 'estado' => 1,
            'id_persona' => 10200999, 'id_metodopago' => 1,
            'banco' => '0102-Banco De Venezuela', 'banco_destino' => '0105-Banco Mercantil',
            'referencia_bancaria' => $ref, 'telefono_emisor' => '04141234567',
            'monto' => 1.00, 'monto_usd' => 1.00, 'precio_total_usd' => 1.00, 'precio_total_bs' => 1.00,
            'id_metodoentrega' => 4, 'direccion_envio' => 'Retiro en Tienda Fisica',
            'sucursal_envio' => '', 'id_delivery' => null, 'imagen' => '',
            // 'carrito' OMITIDO DELIBERADAMENTE
        ]];

        $capturado = null;
        try {
            $res = $this->venta->procesarPedido(json_encode($datos));
            $this->fail('El modelo aceptó un pedido sin clave carrito (devolvió: ' . json_encode($res) . ')');
        } catch (TypeError $e) {
            $capturado = $e;
        } catch (\Throwable $e) {
            $capturado = $e;
        }

        $this->assertNotNull($capturado, 'Sin la debida validación de requeridos se intentó proseguir');
        $esTypeError = $capturado instanceof TypeError;
        fwrite(STDOUT, "\n[1.3] sin 'carrito' -> " . ($esTypeError ? 'TypeError crudo (HTTP 500 en producción)' : get_class($capturado)) . ": " . $capturado->getMessage() . "\n");
        $this->assertTrue($esTypeError, 'Se esperaba TypeError crudo (HTTP 500 no controlado)');
        $this->assertNull($this->pedidoPorReferencia($ref));
    }

    /**
     * HALLAZGO (1.3): el MODELO acepta registrar dirección/pedido sin
     * 'id_metodoentrega' — la columna queda en NULL y responde success.
     * (Los controladores web/API sí validan el campo; la vía vulnerable
     * es la llamada directa al modelo. Anotar planilla como FALLA con
     * ese contexto, severidad media.)
     */
    public function testSinIdMetodoentregaQuedaNULLYSeAcepta(): void
    {
        $ref = $this->ref();
        $datos = ['datos' => [
            'tipo' => '2', 'fecha' => date('Y-m-d'), 'estado' => 1,
            'id_persona' => 10200999, 'id_metodopago' => 1,
            'banco' => '0102-Banco De Venezuela', 'banco_destino' => '0105-Banco Mercantil',
            'referencia_bancaria' => $ref, 'telefono_emisor' => '04141234567',
            'monto' => 1.00, 'monto_usd' => 1.00, 'precio_total_usd' => 1.00, 'precio_total_bs' => 1.00,
            // 'id_metodoentrega' OMITIDO
            'direccion_envio' => 'Retiro en Tienda Fisica', 'sucursal_envio' => '', 'id_delivery' => null, 'imagen' => '',
            'carrito' => [['id' => 6, 'cantidad' => 1, 'cantidad_mayor' => 10, 'precio_detal' => 4.70, 'precio_mayor' => 3.60]],
        ]];

        $res = $this->venta->procesarPedido(json_encode($datos));

        fwrite(STDOUT, "\n[1.3] sin id_metodoentrega -> success=" . (int)$res['success'] . " msg='" . ($res['message'] ?? '') . "' (DEFECTO: se aceptó)\n");

        try {
            // Comportamiento REAL documentado: acepta y persiste NULL
            $this->assertTrue($res['success'], 'El modelo cambió: ahora rechaza pedidos sin id_metodoentrega. Actualizar hallazgo.');
            $st = $this->db->prepare('SELECT id_metodoentrega FROM direccion WHERE id_direccion = (SELECT id_direccion FROM pedido WHERE id_pedido = :p)');
            $st->execute(['p' => $res['id_pedido']]);
            $v = $st->fetch(PDO::FETCH_ASSOC);
            $this->assertNull($v['id_metodoentrega'], 'Esperaba NULL: el campo requerido se registró con otro valor');
        } finally {
            $this->limpiar($ref);
        }
    }

    /* =================================================================
     *  PLANILLA 3.3 — Estructura de Datos Estándar de la respuesta
     * ================================================================= */

    public function testEstructuraDeRespuestaEstandar(): void
    {
        // Respuesta de ÉXITO: claves exactas success, id_pedido, message
        $ref = $this->ref();
        $stock0 = $this->stockProducto(8);
        $datos = ['datos' => [
            'tipo' => '2', 'fecha' => date('Y-m-d'), 'estado' => 1,
            'id_persona' => 10200999, 'id_metodopago' => 1,
            'banco' => '0102-Banco De Venezuela', 'banco_destino' => '0105-Banco Mercantil',
            'referencia_bancaria' => $ref, 'telefono_emisor' => '04141234567',
            'monto' => 2.70, 'monto_usd' => 2.70, 'precio_total_usd' => 2.70, 'precio_total_bs' => 2.70,
            'id_metodoentrega' => 4, 'direccion_envio' => 'Retiro en Tienda Fisica',
            'sucursal_envio' => '', 'id_delivery' => null, 'imagen' => '',
            'carrito' => [['id' => 8, 'cantidad' => 1, 'cantidad_mayor' => 10, 'precio_detal' => 2.70, 'precio_mayor' => 1.75]],
        ]];
        $res = $this->venta->procesarPedido(json_encode($datos));

        try {
            $this->assertTrue($res['success']);
            fwrite(STDOUT, "\n[3.3] éxito   -> claves: " . implode(',', array_keys($res)) . "\n");
            $this->assertEquals(['success', 'id_pedido', 'message'], array_keys($res), 'La respuesta de éxito no tiene la estructura estándar documentada');

            // Respuesta de FALLO: claves success, message (sin id_pedido)
            $this->limpiar($ref);
            $datosfallos = $datos;
            $datosfallos['datos']['carrito'][0]['id'] = 999999;
            $datosfallos['datos']['referencia_bancaria'] = $this->ref();
            $resf = $this->venta->procesarPedido(json_encode($datosfallos));
            fwrite(STDOUT, "[3.3] fallo  -> claves: " . implode(',', array_keys($resf)) . "\n");

            $this->assertEquals(['success', 'message'], array_keys($resf), 'La respuesta de fallo expoe claves inesperadas');
        } finally {
            $this->limpiar($ref);
        }
        $this->assertEquals($stock0, $this->stockProducto(8));
    }
}
