// === DEPENDENCIAS ===
const { Builder, By, Key, until } = require('selenium-webdriver');
const edge = require('selenium-webdriver/edge');

// === CONFIGURACIÓN DE URLS ===
const BASE_URL = 'http://localhost/proyecto%20T3-T4/Lovemakeup-3.0/';

// === CONFIGURACIÓN DEL NAVEGADOR ===
const BROWSER = 'edge';

// === NÚMERO DE REPETICIONES ===
const TOTAL_ITERACIONES = 100;

// === DATOS BASE PARA GENERAR REGISTROS VARIADOS ===
const NOMBRES = ['Eduardo', 'Maria', 'Carlos', 'Ana', 'Jose', 'Valeria', 'Daniel', 'Carmen', 'Alejandro', 'Lucia'];
const APELLIDOS = ['Rojas', 'Perez', 'Gomez', 'Rodriguez', 'Silva', 'Torres', 'Mendoza', 'Blanco', 'Suarez', 'Morales'];

// === TEST AUTOMATIZADO: REGISTRO DE CLIENTE EN LOGIN (UNA ITERACIÓN) ===
async function runTest(iteracion) {
  let driver;
  let status = 'f';

  // Generar datos variados y únicos para esta iteración
  const nombreAleatorio = NOMBRES[Math.floor(Math.random() * NOMBRES.length)];
  const apellidoAleatorio = APELLIDOS[Math.floor(Math.random() * APELLIDOS.length)];
  
  const cedulaAleatoria = Math.floor(10000000 + Math.random() * 90000000).toString();
  const telefonoAleatorio = '0412-' + Math.floor(1000000 + Math.random() * 9000000).toString();
  const correoAleatorio = `${nombreAleatorio.toLowerCase()}.${apellidoAleatorio.toLowerCase()}${cedulaAleatoria.substring(4)}@gmail.com`;

  // Seleccionar aleatoriamente V, E o J para el documento
  const tiposDoc = ['V', 'E', 'J'];
  const tipoDocAleatorio = tiposDoc[Math.floor(Math.random() * tiposDoc.length)];

  try {
    console.log(`[Iteracion ${iteracion}/${TOTAL_ITERACIONES}] Inicializando navegador: ${BROWSER}... Registrando cliente: ${nombreAleatorio} ${apellidoAleatorio} (${tipoDocAleatorio}-${cedulaAleatoria})`);
    
    if (BROWSER === 'edge') {
      const options = new edge.Options();
      driver = await new Builder()
        .forBrowser('MicrosoftEdge')
        .setEdgeOptions(options)
        .build();
    } else if (BROWSER === 'chrome') {
      const chrome = require('selenium-webdriver/chrome');
      const options = new chrome.Options();
      driver = await new Builder()
        .forBrowser('chrome')
        .setChromeOptions(options)
        .build();
    } else {
      driver = await new Builder().forBrowser(BROWSER).build();
    }
    
  } catch (driverError) {
    console.error(`[Iteracion ${iteracion}] Error al inicializar el navegador:`, driverError.message);
    throw driverError;
  }

  try {
    await driver.manage().setTimeouts({
      implicit: 10000,
      pageLoad: 30000,
      script: 30000
    });

    await driver.manage().window().maximize();

    // === Paso 1: Entrar a la página de login ===
    console.log(`[Iteracion ${iteracion}] Navegando al login...`);
    await driver.get(BASE_URL + '?pagina=login');
    await driver.sleep(2000); // Pausa prudente para que cargue la vista
    
    // Esperar a que la página cargue ubicando el campo de usuario
    await driver.wait(until.elementLocated(By.id('usuario')), 1000);
    await driver.sleep(1000);

    // === Paso 2: Abrir el modal de registro de clientes (id: registroclientess) ===
    console.log(`[Iteracion ${iteracion}] Abriendo modal de registro de cliente...`);
    
    try {
      const abrirModalBtn = await driver.findElement(By.css('[data-bs-target="#registroclientess"], button.registroclientess, a[href*="registroclientess"]'));
      await driver.wait(until.elementIsVisible(abrirModalBtn), 500);
      await abrirModalBtn.click();
    } catch (e) {
      // Forzar apertura por script si el botón no es accesible directamente
      await driver.executeScript("var modalEl = document.getElementById('registroclientess'); if(modalEl){ var myModal = new bootstrap.Modal(modalEl); myModal.show(); }");
    }

    // Pausa para dar tiempo a que la animación del modal termine de renderizarse
    await driver.sleep(500);

    // Esperar a que el campo nombre del modal #registroclientess esté completamente visible
    const nombreInput = await driver.wait(
      until.elementLocated(By.id('nombre')), 
      500
    );
    await driver.wait(until.elementIsVisible(nombreInput), 500);
    console.log(`[Iteracion ${iteracion}] Modal de registro de clientes abierto y listo.`);
    await driver.sleep(500);

    // === Paso 3: Ingresar datos variados en el formulario del modal (Con pausas para evitar bugs de velocidad) ===
    console.log(`[Iteracion ${iteracion}] Llenando datos del formulario de forma pausada...`);
    
    // tipo de documento id: rolSelect2 (V, E o J)
    try {
      const rolSelect2 = await driver.findElement(By.id('rolSelect2'));
      await driver.wait(until.elementIsVisible(rolSelect2), 500);
      await driver.executeScript(`arguments[0].value = '${tipoDocAleatorio}';`, rolSelect2);
      await driver.executeScript("arguments[0].dispatchEvent(new Event('change', { bubbles: true }));", rolSelect2);
      await driver.sleep(500);
    } catch (e) {
      console.log(`[Iteracion ${iteracion}] No se pudo seleccionar rolSelect2, continuando...`);
    }

    // cedula id: cedula
    const cedulaInput = await driver.findElement(By.id('cedula'));
    await cedulaInput.clear();
    await cedulaInput.sendKeys(cedulaAleatoria);
    await driver.sleep(500);

    // nombre id: nombre
    await nombreInput.clear();
    await nombreInput.sendKeys(nombreAleatorio);
    await driver.sleep(500);

    // apellido id: apellido
    const apellidoInput = await driver.findElement(By.id('apellido'));
    await apellidoInput.clear();
    await apellidoInput.sendKeys(apellidoAleatorio);
    await driver.sleep(500);

    // correo id: correo
    const correoInput = await driver.findElement(By.id('correo'));
    await correoInput.clear();
    await correoInput.sendKeys(correoAleatorio);
    await driver.sleep(500);

    // telefono id: telefono
    const telefonoInput = await driver.findElement(By.id('telefono'));
    await telefonoInput.clear();
    await telefonoInput.sendKeys(telefonoAleatorio);
    await driver.sleep(500);

    // contraseña id: recontrasena
    const recontrasenaInput = await driver.findElement(By.id('recontrasena'));
    await recontrasenaInput.clear();
    await recontrasenaInput.sendKeys('Lara500*');
    await driver.sleep(500);

    // confirmar contraseña id: clave
    const claveInput = await driver.findElement(By.id('clave'));
    await claveInput.clear();
    await claveInput.sendKeys('Lara500*');
    await driver.sleep(500); // Pausa antes de hacer clic en registrar

    // === Paso 4: Hacer clic en el botón de registrar id: registrar ===
    console.log(`[Iteracion ${iteracion}] Haciendo clic en Registrar...`);
    const registrarBtn = await driver.findElement(By.id('registrar'));
    await driver.wait(until.elementIsEnabled(registrarBtn), 5000);
    await registrarBtn.click();
    
    // Pausa para que el servidor procese la petición AJAX o el envío del formulario
    await driver.sleep(500);

    // === Paso 5: Esperar a la alerta que sea success (SweetAlert2) ===
    console.log(`[Iteracion ${iteracion}] Esperando alerta de éxito...`);
    
    const swalSuccess = await driver.wait(
      until.elementLocated(By.css('.swal2-success, .swal2-popup, .alert-success')), 
      1000
    );
    await driver.wait(until.elementIsVisible(swalSuccess), 1000);
    
    console.log(`[Iteracion ${iteracion}] Cliente registrado exitosamente.`);
    status = 'p';
    await driver.sleep(1000);

    // Intentar cerrar el popup de éxito para limpiar la pantalla para la siguiente iteración
    try {
      const swalOkBtn = await driver.findElement(By.css('.swal2-confirm'));
      await swalOkBtn.click();
      await driver.sleep(1000);
    } catch (e) {
      // Ignorar si no requiere clic de cierre manual
    }

  } catch (error) {
    console.error(`[Iteracion ${iteracion}] Error durante la prueba:`, error.message);
    status = 'f';
  } finally {
    if (driver) {
      try {
        await driver.quit();
        console.log(`[Iteracion ${iteracion}] Navegador cerrado.\n--------------------------------------------------`);
      } catch (quitError) {
        console.log(`[Iteracion ${iteracion}] Error al cerrar el navegador:`, quitError.message);
      }
    }
  }
}

// === EJECUCIÓN SECUENCIAL 100 VECES (CON TIEMPO DE RESPPIRO ENTRE CADA UNA) ===
async function runLoop() {
  console.log(`Iniciando ejecucion de ${TOTAL_ITERACIONES} pruebas automatizadas de registro de clientes...\n`);
  for (let i = 1; i <= TOTAL_ITERACIONES; i++) {
    try {
      await runTest(i);
    } catch (err) {
      console.error(`La iteracion ${i} finalizo con error critico.`);
    }
    // Pausa de 2 segundos entre cada iteración completa para evitar saturar el servidor local
    await new Promise(resolve => setTimeout(resolve, 1000));
  }
  console.log('Todas las iteraciones de registro de clientes han finalizado.');
}

if (require.main === module) {
  runLoop().catch(error => {
    console.error('Error fatal en la ejecucion de las pruebas:', error);
    process.exit(1);
  });
}

module.exports = { runTest };