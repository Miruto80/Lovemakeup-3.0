// DEPENDENCIAS
const { Builder, By, Key, until } = require('selenium-webdriver');
const edge = require('selenium-webdriver/edge');

// CONFIGURACIÓN DE URLS ===
const BASE_URL = 'http://localhost/proyecto%20T3-T4/Lovemakeup-3.0/';

//  CONFIGURACIÓN DEL NAVEGADOR ===
const BROWSER = 'edge';

// NÚMERO DE REPETICIONES 
const TOTAL_ITERACIONES = 10; 

// === DATOS BASE PARA GENERAR NOMBRES ALEATORIOS ===
const ROLES_BASE = ['Administrador', 'Vendedor', 'Supervisor', 'Asesor', 'Soporte', 'Gerente', 'Operador', 'Auditor'];

// === TEST AUTOMATIZADO: REGISTRAR TIPO USUARIO (UNA ITERACIÓN) ===
async function runTest(iteracion) {
  let driver;
  let status = 'f';

  // Generar datos aleatorios para esta iteración
  const rolAleatorioBase = ROLES_BASE[Math.floor(Math.random() * ROLES_BASE.length)];
  const nombreRolAleatorio = `${rolAleatorioBase} ${Math.floor(100 + Math.random() * 900)}`;
  
  // Seleccionar aleatoriamente entre nivel 2 o nivel 3
  const nivelesDisponibles = ['2', '3'];
  const nivelAleatorio = nivelesDisponibles[Math.floor(Math.random() * nivelesDisponibles.length)];

  try {
    console.log(`[Iteracion ${iteracion}/${TOTAL_ITERACIONES}] Inicializando navegador: ${BROWSER}... Registrando tipo usuario -> Nombre: "${nombreRolAleatorio}", Nivel: ${nivelAleatorio}`);
    
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

    // === Paso 1: Iniciar sesión ===
    console.log(`[Iteracion ${iteracion}] Navegando al login...`);
    await driver.get(BASE_URL + '?pagina=login');
    await driver.sleep(1000);
    
    await driver.wait(until.elementLocated(By.id('usuario')), 1000);
    
    // Seleccionar tipo de documento (V)
    try {
      const tipoDocSelect = await driver.findElement(By.id('DocumentoSelct'));
      await driver.executeScript("arguments[0].value = 'V';", tipoDocSelect);
      await driver.executeScript("arguments[0].dispatchEvent(new Event('change', { bubbles: true }));", tipoDocSelect);
      await driver.sleep(300);
    } catch (e) {
      // Ignorar si no aplica
    }
    
    const usuarioInput = await driver.findElement(By.id('usuario'));
    await usuarioInput.clear();
    await usuarioInput.sendKeys('10200300');
    
    const passwordInput = await driver.findElement(By.id('pid'));
    await passwordInput.clear();
    await passwordInput.sendKeys('love1234');
    
    const ingresarBtn = await driver.findElement(By.id('ingresar'));
    await ingresarBtn.click();
    
    // Esperar redirección después del login
    await driver.wait(async () => {
      const url = await driver.getCurrentUrl();
      return url.includes('pagina=home') || url.includes('pagina=catalogo');
    }, 1000);
    await driver.sleep(1000);
    console.log(`[Iteracion ${iteracion}] Login exitoso.`);

    // === Paso 2: Ir al módulo Tipo Usuario ===
    console.log(`[Iteracion ${iteracion}] Accediendo al modulo de Tipo Usuario...`);
    await driver.get(BASE_URL + '?pagina=tipousuario');
    await driver.sleep(1000);
    
    await driver.wait(until.urlContains('pagina=tipousuario'), 1000);
    await driver.sleep(1000);

    // === Paso 3: Abrir formulario de registro (Modal) ===
    console.log(`[Iteracion ${iteracion}] Abriendo formulario de registro...`);
    
    let registrarModalBtn;
    try {
      registrarModalBtn = await driver.findElement(By.css('button[data-bs-target="#registro"], button.registrar'));
    } catch (e) {
      registrarModalBtn = await driver.findElement(By.xpath("//button[contains(text(), 'Registrar') or contains(@data-bs-target, '#registro')]"));
    }

    await driver.executeScript("arguments[0].scrollIntoView({block: 'center', behavior: 'smooth'});", registrarModalBtn);
    await driver.sleep(500);
    await driver.wait(until.elementIsVisible(registrarModalBtn), 10000);
    await registrarModalBtn.click();
    await driver.sleep(1500);

    // Esperar a que el modal de registro esté visible
    const modal = await driver.wait(until.elementLocated(By.id('registro')), 10000);
    await driver.wait(until.elementIsVisible(modal), 1000);
    console.log(`[Iteracion ${iteracion}] Modal de registro abierto correctamente.`);
    await driver.sleep(1000);

    // === Paso 4: Llenar el formulario con los datos aleatorios ===
    console.log(`[Iteracion ${iteracion}] Llenando datos del formulario...`);

    // Nombre del rol (id: nombre)
    const nombreInput = await driver.findElement(By.id('nombre'));
    await driver.wait(until.elementIsVisible(nombreInput), 1000);
    await nombreInput.clear();
    await driver.sleep(300);
    await nombreInput.sendKeys(nombreRolAleatorio);
    await driver.sleep(500);

    // Nivel de Acceso (id: nivel) -> Selecciona nivel 2 o 3 aleatoriamente
    const nivelSelect = await driver.findElement(By.id('nivel'));
    await driver.wait(until.elementIsVisible(nivelSelect), 1000);
    await driver.executeScript(`
      arguments[0].value = '${nivelAleatorio}';
      arguments[0].dispatchEvent(new Event('change', { bubbles: true }));
      arguments[0].dispatchEvent(new Event('input', { bubbles: true }));
    `, nivelSelect);
    await driver.sleep(500);

    // === Paso 5: Hacer clic en el botón Registrar (id: registrar) ===
    console.log(`[Iteracion ${iteracion}] Haciendo clic en Registrar...`);
    const btnRegistrar = await driver.findElement(By.id('registrar'));
    await driver.wait(until.elementIsEnabled(btnRegistrar), 1000);
    await btnRegistrar.click();
    await driver.sleep(1000);

    // === Paso 6: Confirmar si se va a registrar (SweetAlert confirm previo si aplica) ===
    try {
      const confirmSwalBtn = await driver.wait(
        until.elementLocated(By.css('.swal2-confirm, .swal2-styled.swal2-confirm')), 
        1000
      );
      if (await confirmSwalBtn.isDisplayed()) {
        await confirmSwalBtn.click();
        await driver.sleep(1000);
      }
    } catch (e) {
      // Continuar si no hay confirmación previa
    }

    // === Paso 7: Esperar y verificar si la alerta es success o error (SweetAlert2) ===
    console.log(`[Iteracion ${iteracion}] Verificando resultado de la alerta de SweetAlert2...`);
    
    try {
      const swalPopup = await driver.wait(
        until.elementLocated(By.css('.swal2-popup, .swal2-container')), 
        1000
      );
      await driver.wait(until.elementIsVisible(swalPopup), 1000);

      const esSuccess = await driver.findElements(By.css('.swal2-success, .swal2-icon-success'));
      const esError = await driver.findElements(By.css('.swal2-error, .swal2-icon-error, .swal2-warning'));

      if (esSuccess.length > 0) {
        console.log(`[Iteracion ${iteracion}] Todo esta bien. El tipo de usuario "${nombreRolAleatorio}" fue registrado exitosamente.`);
        status = 'p';
      } else if (esError.length > 0) {
        console.log(`[Iteracion ${iteracion}] ERROR: Hubo un error al intentar registrar el tipo de usuario "${nombreRolAleatorio}".`);
        status = 'f';
      } else {
        console.log(`[Iteracion ${iteracion}] Operacion procesada, pero no se pudo determinar con certeza el icono de la alerta.`);
        status = 'p';
      }

      // Cerrar el diálogo SweetAlert
      try {
        const swalOkBtn = await driver.findElement(By.css('.swal2-confirm'));
        await swalOkBtn.click();
        await driver.sleep(1000);
      } catch (err) {
        // Ignorar si ya se cerró
      }

    } catch (e) {
      throw new Error('No se detecto la alerta de resultado en pantalla.');
    }

  } catch (error) {
    console.error(`[Iteracion ${iteracion}] ERROR: Hubo un error durante la prueba:`, error.message);
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

// === EJECUCIÓN SECUENCIAL DE MÚLTIPLES SOLICITUDES ===
async function runLoop() {
  console.log(`Iniciando ejecucion de ${TOTAL_ITERACIONES} pruebas automatizadas de registro de tipos de usuario...\n`);
  
  for (let i = 1; i <= TOTAL_ITERACIONES; i++) {
    try {
      await runTest(i);
    } catch (err) {
      console.error(`La iteracion ${i} finalizo con error critico.`);
    }
    
    // Pausa de 2 segundos entre cada iteración para estabilizar el sistema
    await new Promise(resolve => setTimeout(resolve, 1000));
  }
  console.log('Todas las iteraciones de registro de tipos de usuario han finalizado.');
}

if (require.main === module) {
  runLoop().catch(error => {
    console.error('Error fatal en la ejecucion de las pruebas:', error);
    process.exit(1);
  });
}

module.exports = { runTest };