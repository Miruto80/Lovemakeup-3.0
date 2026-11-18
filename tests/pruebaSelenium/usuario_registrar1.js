//  DEPENDENCIAS ===
const { Builder, By, Key, until } = require('selenium-webdriver');
const edge = require('selenium-webdriver/edge');

// CONFIGURACIÓN DE URLS ===
const BASE_URL = 'http://localhost/proyecto%20T3-T4/Lovemakeup-3.0/';

// CONFIGURACIÓN DEL NAVEGADOR 
const BROWSER = 'edge';

// NÚMERO DE REPETICIONES 
const TOTAL_ITERACIONES = 100;

// DATOS BASE PARA GENERAR REGISTROS VARIADOS 
const NOMBRES = ['Eduardo', 'Maria', 'Carlos', 'Ana', 'Jose', 'Valeria', 'Daniel', 'Carmen', 'Alejandro', 'Lucia'];
const APELLIDOS = ['Rojas', 'Perez', 'Gomez', 'Rodriguez', 'Silva', 'Torres', 'Mendoza', 'Blanco', 'Suarez', 'Morales'];

// TEST AUTOMATIZADO: REGISTRAR USUARIO  
async function runTest(iteracion) {
  let driver;
  let status = 'f';

  // Generar datos variados y unicos para esta iteración
  const nombreAleatorio = NOMBRES[Math.floor(Math.random() * NOMBRES.length)];
  const apellidoAleatorio = APELLIDOS[Math.floor(Math.random() * APELLIDOS.length)];
  
  const cedulaAleatoria = Math.floor(10000000 + Math.random() * 90000000).toString();
  const telefonoAleatorio = '0412-' + Math.floor(1000000 + Math.random() * 9000000).toString();
  const correoAleatorio = `${nombreAleatorio.toLowerCase()}.${apellidoAleatorio.toLowerCase()}${cedulaAleatoria.substring(4)}@gmail.com`;

  // Seleccionar aleatoriamente V o E para el documento
  const tipoDocAleatorio = Math.random() < 0.5 ? 'V' : 'E';


  const rolAleatorio = Math.floor(Math.random() * 20) + 1;

  try {
    console.log(`[Iteracion ${iteracion}/${TOTAL_ITERACIONES}] Inicializando navegador: ${BROWSER}... Registrando a: ${nombreAleatorio} ${apellidoAleatorio} (${tipoDocAleatorio}-${cedulaAleatoria})`);
    
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
      implicit: 5000,
      pageLoad: 30000,
      script: 30000
    });

    await driver.manage().window().maximize();

    // Paso 1 Entrar a la página de login 
    console.log(`[Iteracion ${iteracion}] Navegando al login...`);
    await driver.get(BASE_URL + '?pagina=login');
    
    const usuarioInputLogin = await driver.wait(until.elementLocated(By.id('usuario')), 10000);
    await driver.wait(until.elementIsVisible(usuarioInputLogin), 5000);

    // Paso 2 Ingresar credenciales válidas para iniciar sesión
    try {
      const tipoDocLoginSelect = await driver.findElement(By.id('DocumentoSelct'));
      await driver.executeScript("arguments[0].value = 'V';", tipoDocLoginSelect);
      await driver.executeScript("arguments[0].dispatchEvent(new Event('change', { bubbles: true }));", tipoDocLoginSelect);
    } catch (e) {
      // Ignorar si no existe en el login
    }
    
    await usuarioInputLogin.clear();
    await usuarioInputLogin.sendKeys('10200300');
    
    const passwordInputLogin = await driver.findElement(By.id('pid'));
    await passwordInputLogin.clear();
    await passwordInputLogin.sendKeys('love1234');

    // === Paso 3: Hacer clic en Ingresar ===
    console.log(`[Iteracion ${iteracion}] Haciendo clic en Ingresar...`);
    const ingresarBtn = await driver.findElement(By.id('ingresar'));
    await ingresarBtn.click();
    
    // Esperar redirección a home o catalogo (Solución al cierre inesperado)
    await driver.wait(async () => {
      const url = await driver.getCurrentUrl();
      return url.includes('pagina=home') || url.includes('pagina=catalogo');
    }, 15000);
    console.log(`[Iteracion ${iteracion}] Login exitoso.`);

    // === Paso 4: Navegar a Usuario ===
    console.log(`[Iteracion ${iteracion}] Accediendo a Usuario...`);
    await driver.get(BASE_URL + '?pagina=usuario');
    await driver.wait(until.urlContains('pagina=usuario'), 15000);

    // === Paso 5: Hacer clic en "registrar" para abrir el modal ===
    console.log(`[Iteracion ${iteracion}] Abriendo formulario de registro...`);
    const registrarBtn = await driver.wait(
      until.elementLocated(By.css('button.registrar, button[data-bs-target="#registro"]')), 
      10000
    );
    await driver.wait(until.elementIsEnabled(registrarBtn), 5000);
    await registrarBtn.click();

    // Esperar a que el campo nombre del modal esté visible de forma segura
    const nombreInput = await driver.wait(
      until.elementLocated(By.id('nombre')), 
      10000
    );
    await driver.wait(until.elementIsVisible(nombreInput), 5000);

    // === Paso 6: Ingresar datos variados del formulario ===
    console.log(`[Iteracion ${iteracion}] Llenando datos del formulario...`);
    
    //(V o E aleatorio)
    try {
      const docSelect = await driver.findElement(By.id('rolSelect2'));
      await driver.executeScript(`arguments[0].value = '${tipoDocAleatorio}';`, docSelect);
      await driver.executeScript("arguments[0].dispatchEvent(new Event('change', { bubbles: true }));", docSelect);
    } catch (e) {
      // Continuar si no aplica
    }

    // Nombre
    await nombreInput.clear();
    await nombreInput.sendKeys(nombreAleatorio);

    // Apellido
    const apellidoInput = await driver.findElement(By.id('apellido'));
    await apellidoInput.clear();
    await apellidoInput.sendKeys(apellidoAleatorio);

    // Cédula
    const cedulaInput = await driver.findElement(By.id('cedula'));
    await cedulaInput.clear();
    await cedulaInput.sendKeys(cedulaAleatoria);

    // ROL - Selección entre 1 y 20 (si falla, agarra la opción 1 por defecto)
    try {
      const rolSelect = await driver.findElement(By.id('rolSelect'));
      const options = await rolSelect.findElements(By.tagName('option'));
      
      if (options.length > 0) {
        let indexToSelect = rolAleatorio < options.length ? rolAleatorio : 0;
        await options[indexToSelect].click();
      }
    } catch (e) {
      console.log(`[Iteracion ${iteracion}] No se pudo seleccionar ROL aleatorio, usando valor por defecto.`);
    }

    // Teléfono
    const telefonoInput = await driver.findElement(By.id('telefono'));
    await telefonoInput.clear();
    await telefonoInput.sendKeys(telefonoAleatorio);

    // Correo
    const correoInput = await driver.findElement(By.id('correo'));
    await correoInput.clear();
    await correoInput.sendKeys(correoAleatorio);

    // Contraseña
    const passwordInput = await driver.findElement(By.id('clave'));
    await passwordInput.clear();
    await passwordInput.sendKeys('Lara500*');

    // Confirmar Contraseña
    const confirmPasswordInput = await driver.findElement(By.id('confirmar_clave'));
    await confirmPasswordInput.clear();
    await confirmPasswordInput.sendKeys('Lara500*');

    // === Paso 7: Hacer clic en Registrar del Formulario ===
    console.log(`[Iteracion ${iteracion}] Enviando formulario de registro...`);
    const btnSubmitRegistro = await driver.findElement(By.id('registrar'));
    await btnSubmitRegistro.click();

    // Manejar posible confirmación previa (SweetAlert confirm)
    try {
      const confirmSwalBtn = await driver.wait(
        until.elementLocated(By.css('.swal2-confirm, .swal2-styled.swal2-confirm')), 
        2000
      );
      if (await confirmSwalBtn.isDisplayed()) {
        await confirmSwalBtn.click();
      }
    } catch (e) {
      // Si no hay ventana de confirmación previa, continuar
    }

    // === Paso 8: Verificar mensaje de éxito de SweetAlert2 ===
    const swalSuccess = await driver.wait(
      until.elementLocated(By.css('.swal2-success, .swal2-popup, .alert-success')), 
      10000
    );
    await driver.wait(until.elementIsVisible(swalSuccess), 5000);
    
    console.log(`[Iteracion ${iteracion}] Usuario registrado exitosamente.`);
    status = 'p';

    // Cerrar el popup de éxito para limpiar la pantalla para la siguiente iteración
    try {
      const swalOkBtn = await driver.findElement(By.css('.swal2-confirm'));
      await swalOkBtn.click();
      await driver.sleep(1000);
    } catch (e) {
      // Ignorar si no requiere clic de cierre
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

// === EJECUCIÓN SECUENCIAL 100 VECES ===
async function runLoop() {
  console.log(`Iniciando ejecucion de ${TOTAL_ITERACIONES} pruebas automatizadas de registro...\n`);
  for (let i = 1; i <= TOTAL_ITERACIONES; i++) {
    try {
      await runTest(i);
    } catch (err) {
      console.error(`La iteracion ${i} finalizo con error critico.`);
    }
    // Pausa corta de 500ms entre cada iteración
    await new Promise(resolve => setTimeout(resolve, 500));
  }
  console.log('Todas las iteraciones de registro han finalizado.');
}

if (require.main === module) {
  runLoop().catch(error => {
    console.error('Error fatal en la ejecucion de las pruebas:', error);
    process.exit(1);
  });
}

module.exports = { runTest };