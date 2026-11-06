<?php
/**
 * =====================================================================
 *  Pruebas de Auditoría del Módulo VentaWeb (flujo de venta web)
 *  Lote A — Funciones puras: NO requieren base de datos ni modifican nada.
 *
 *  Mapeo con las planillas de prueba.docx:
 *   - 1.1 Inyección de Código (SQLi / XSS)      [caja blanca]
 *   - 1.2 Límites de Longitud                    [caja blanca]
 *   - 1.4 Tipos de Datos Erróneos                [caja blanca]
 *   - 2.1 Valores Límite (funciones puras)       [caja blanca]
 *   - 2.1 Límites de Longitud de dirección       [caja blanca]
 *
 *  NOTA DE AUDITORÍA (divergencias documentadas):
 *  - validarReferenciaBancaria() acepta 1-50 caracteres server-side, mientras
 *    el JS del cliente y la API móvil exigen 4-6 dígitos. El test
 *    testReferenciaBancariaFronteraServer confirma el comportamiento REAL
 *    del servidor; anotar esa divergencia en la Matriz de Defectos.
 * =====================================================================
 */
namespace Tests\PruebaPHPUnit;

use PHPUnit\Framework\TestCase;
use LoveMakeup\Proyecto\Modelo\VentaWeb;

class VentaWebAuditTest extends TestCase
{
    private VentaWeb $venta;

    protected function setUp(): void
    {
        $this->venta = new VentaWeb();
    }

    /* =================================================================
     *  PLANILLA 1.2 — Límites de Longitud
     * ================================================================= */

    public function testLimiteLongitudNombre5000SeTruncaA100(): void
    {
        $entrada  = str_repeat('a', 5000);
        $salida   = $this->venta->sanitizarString($entrada, 100);

        fwrite(STDOUT, "\n[CASO 1.2] Cadena de 5000 caracteres en campo Nombre -> truncada a " . strlen($salida) . " chars");

        $this->assertSame(100, strlen($salida));
    }

    public function testLimiteLongitudDireccion5000SeTruncaA500(): void
    {
        $entrada = str_repeat('Av. Lara edif. 12 ', 300); // ~5400 caracteres, sin patrones SQL
        $salida  = $this->venta->sanitizarDireccion($entrada);

        // El código: trim -> eliminar <>"' -> substr 500 -> htmlspecialchars
        $esperado = htmlspecialchars(substr(trim($entrada), 0, 500), ENT_QUOTES, 'UTF-8');

        fwrite(STDOUT, "\n[CASO 1.2] Direccion de " . strlen($entrada) . " chars -> truncada a " . strlen($salida) . " chars");

        $this->assertSame($esperado, $salida);
        $this->assertLessThanOrEqual(520, strlen($salida), 'La direccion no se trunco a ~500 chars');
    }

    public function testLimiteLongitudSucursal5000SeTruncaA100(): void
    {
        $entrada = str_repeat('S', 5000);
        $salida  = $this->venta->sanitizarSucursal($entrada);

        fwrite(STDOUT, "\n[CASO 1.2] Sucursal de 5000 chars -> truncada a " . strlen($salida) . " chars");

        $this->assertSame(100, strlen($salida));
    }

    public function testLimiteLongitudBancoOrigenSeTruncaA100(): void
    {
        $entrada = str_repeat('Banco ', 100); // 600 chars de formato bancario
        $salida  = $this->venta->sanitizarString($entrada, 100);

        fwrite(STDOUT, "\n[CASO 1.2] BD banco de 600 chars -> truncada a " . strlen($salida) . " chars");

        $this->assertSame(100, strlen($salida));
    }

    /* =================================================================
     *  PLANILLA 1.1 — Inyección de Código (SQLi / XSS)
     * ================================================================= */

    public function testSqliOr1igual1EsRechazadaEnCadena(): void
    {
        $entrada  = "1' OR '1'='1";
        $detectadaDirecta = $this->venta->detectarInyeccionSQL($entrada);
        $sanitizado = $this->venta->sanitizarString($entrada, 100);
        $detectadaTrasSanitizar = $this->venta->detectarInyeccionSQL($sanitizado);

        fwrite(STDOUT, "\n[CASO 1.1] OR 1=1 con comillas -> detector directo=" . var_export($detectadaDirecta, true)
            . " | tras sanitizar='" . $sanitizado . "' | detector=" . var_export($detectadaTrasSanitizar, true));

        // El sanitizador le quita las comillas al payload...
        $this->assertStringNotContainsString("'", $sanitizado);
        // ...y de ahí en adelante el detector sí lo atrapa:
        $this->assertTrue($detectadaTrasSanitizar, 'El payload desarmado no fue detenido en la cadena');
    }

    public function testDetectorNoAtrapaQueryConComillasComportamientoReal(): void
    {
        // Documentación del hueco del detector regex tal como está hoy:
        $this->assertFalse(
            $this->venta->detectarInyeccionSQL("1' OR '1'='1"),
            'El detector cambió de comportamiento: ahora SÍ atrapa payloads con comillas. Actualizar hallazgo.'
        );
    }

    public function testInyeccionSQLUnionSelectEsDetectada(): void
    {
        $salida = $this->venta->detectarInyeccionSQL("banco UNION SELECT clave,1,2 FROM usuarios--");

        fwrite(STDOUT, "\n[CASO 1.1] Payload UNION SELECT -> detectado=" . var_export($salida, true));

        $this->assertTrue($salida);
    }

    public function testInyeccionSQLDropTableEsDetectada(): void
    {
        $salida = $this->venta->detectarInyeccionSQL("demo; DROP TABLE producto");

        fwrite(STDOUT, "\n[CASO 1.1] Payload DROP TABLE -> detectado=" . var_export($salida, true));

        $this->assertTrue($salida);
    }

    public function testXssScriptNoSobreviveEnSanitizarString(): void
    {
        $entrada = '<script>alert(1)</script>';
        $salida  = $this->venta->sanitizarString($entrada, 100);

        fwrite(STDOUT, "\n[CASO 1.1] XSS en nombre -> salida limpia: '" . $salida . "'");

        $this->assertStringNotContainsString('<', $salida);
        $this->assertStringNotContainsString('script', $salida);
    }

    public function testXssEnDireccionEscapadoSinEtiquetas(): void
    {
        $salida = $this->venta->sanitizarDireccion('<script>Av. 20 entre calles 29 y 30</script>');

        fwrite(STDOUT, "\n[CASO 1.1] XSS en direccion -> salida: '" . $salida . "'");

        $this->assertStringNotContainsString('<', $salida, 'La direccion conserva etiquetas HTML');
        $this->assertStringNotContainsString('script>', $salida);
    }

    public function testEntradaLimpiaNoEsFalsamenteDetectada(): void
    {
        // Direcciones reales con guiones/numerales NO deben disparar falsos positivos
        $this->assertFalse($this->venta->detectarInyeccionSQL('Av. 4--5 casa #5'), "Falso positivo en direccion legitima");
        $this->assertFalse($this->venta->detectarInyeccionSQL('C.C. Barquisimeto Plaza, piso 2, apto 2B'));
    }

    /* =================================================================
     *  PLANILLA 1.4 — Tipos de Datos Erróneos
     * ================================================================= */

    public function testEnteroNoNumericoDevuelveNulo(): void
    {
        $this->assertNull($this->venta->sanitizarEntero('veinticinco', 1), 'Texto en campo numérico no fue bloqueado');
        $this->assertNull($this->venta->sanitizarEntero('abc123', 1), 'Texto mezclado no fue bloqueado');
    }

    public function testDecimalNoNumericoDevuelveNulo(): void
    {
        $this->assertNull($this->venta->sanitizarDecimal('hola', 0), 'Texto en campo de precio no fue bloqueado');
        $this->assertNull($this->venta->sanitizarDecimal('', 0));
    }

    public function testMetodoEntregaTipoIncorrectoRechazado(): void
    {
        $this->assertFalse($this->venta->validarMetodoEntrega('uno'));
        $this->assertFalse($this->venta->validarMetodoEntrega(['1']));
    }

    public function testValoresNumericosValidosSonAceptados(): void
    {
        $this->assertSame(7, $this->venta->sanitizarEntero('7'));
        $this->assertEqualsWithDelta(100.50, $this->venta->sanitizarDecimal('100.50', 0), 0.001);
    }

    /* =================================================================
     *  PLANILLA 2.1 — Valores Límite (funciones puras)
     * ================================================================= */

    public function testTelefonosFronteras7y15Digitos(): void
    {
        $this->assertTrue($this->venta->validarTelefono('0414595'), 'Telefono de 7 digitos (minimo) fue rechazado');
        $this->assertTrue($this->venta->validarTelefono('041412345678912'), 'Telefono de 15 digitos (maximo) fue rechazado');
        $this->assertFalse($this->venta->validarTelefono('041459'), 'Telefono de 6 digitos fue aceptado');
        $this->assertFalse($this->venta->validarTelefono('0414123456789123'), 'Telefono de 16 digitos fue aceptado');
    }

    public function testZonasValidasYFrontera(): void
    {
        foreach (['norte', 'sur', 'este', 'oeste', 'centro'] as $zona) {
            $this->assertTrue($this->venta->validarZona($zona), "Zona valida '$zona' fue rechazada");
        }
        $this->assertTrue($this->venta->validarZona('NORTE'), 'Zona en mayusculas fue rechazada (se normaliza a minusculas)');
        $this->assertFalse($this->venta->validarZona('atlantida'), 'Zona fuera de la lista fue aceptada');
    }

    public function testMetodoEntregaFronterasNumericas(): void
    {
        $this->assertFalse($this->venta->validarMetodoEntrega(0), 'ID 0 (fuera de rango) fue aceptado');
        $this->assertTrue($this->venta->validarMetodoEntrega(1));
        $this->assertTrue($this->venta->validarMetodoEntrega(4));
        $this->assertFalse($this->venta->validarMetodoEntrega(5), 'ID 5 (fuera de rango) fue aceptado');
    }

    public function testEmpresaEnvioFronterasNumericas(): void
    {
        $this->assertFalse($this->venta->validarEmpresaEnvio(1), 'ID 1 no es empresa de envio');
        $this->assertTrue($this->venta->validarEmpresaEnvio(2), 'MRW (2) debe ser valido');
        $this->assertTrue($this->venta->validarEmpresaEnvio(3), 'ZOOM (3) debe ser valido');
        $this->assertFalse($this->venta->validarEmpresaEnvio(4), 'ID 4 no es empresa de envio');
    }

    /**
     * DOCUMENTACIÓN DE COMPORTAMIENTO REAL (2.1):
     * El servidor web solo exige [0-9\-\s]{1..50}; el cliente JS y la API
     * exigen 4-6 dígitos. El servidor acepta referencias de 3 y 7 dígitos.
     * => Anotar en la Matriz de Defectos como divergencia de validación.
     */
    public function testReferenciaBancariaFronteraServer(): void
    {
        fwrite(STDOUT, "\n[2.1] Referencias de 3/4/6/7 digitos -> server: " .
            (int)$this->venta->validarReferenciaBancaria('123') . "/" .
            (int)$this->venta->validarReferenciaBancaria('1234') . "/" .
            (int)$this->venta->validarReferenciaBancaria('123456') . "/" .
            (int)$this->venta->validarReferenciaBancaria('1234567') . " (1=aceptada)");

        $this->assertTrue($this->venta->validarReferenciaBancaria('123'), 'El server rechazo 3 digitos');
        $this->assertTrue($this->venta->validarReferenciaBancaria('1234'));
        $this->assertTrue($this->venta->validarReferenciaBancaria('123456'));
        $this->assertTrue($this->venta->validarReferenciaBancaria('1234567'), 'El server rechazo 7 digitos');
        $this->assertFalse($this->venta->validarReferenciaBancaria('123456789012345678901234567890123456789012345678901'), '>50 chars rechazada');
        $this->assertFalse($this->venta->validarReferenciaBancaria('abc'), 'Texto fue aceptado como referencia');
    }

    public function testSucurialesValidasYRechazadas(): void
    {
        $this->assertNotEmpty($this->venta->sanitizarSucursal('2140'), 'Codigo de sucursal valido fue vaciado');
        $this->assertNotEmpty($this->venta->sanitizarSucursal('Central'), 'Sucursal con letras fue vaciada');
        $this->assertSame('', $this->venta->sanitizarSucursal('Sucursal #7'), 'Sucursal con # no fue rechazada');
        $this->assertSame('', $this->venta->sanitizarSucursal(''), 'Sucursal vacia no devolvio cadena vacia');
    }

    public function testParroquiaYSectorAceptarNombresLatinos(): void
    {
        $this->assertTrue($this->venta->validarParroquia('Juan de Villegas'));
        $this->assertTrue($this->venta->validarParroquia('Catedral'));
        $this->assertFalse($this->venta->validarParroquia('La<script>'), 'Parroquia con etiquetas fue aceptada');
        $this->assertTrue($this->venta->validarSector('Urbanizacion Santa Elena'));
        $this->assertFalse($this->venta->validarSector('Sector; DROP'), 'Sector con SQL fue aceptado');
    }
}
