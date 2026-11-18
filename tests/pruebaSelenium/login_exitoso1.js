//  DEPENDENCIAS 
const { Builder, By, Key, until } = require('selenium-webdriver');
const edge = require('selenium-webdriver/edge');

// CONFIGURACIÓN DE URLS 
const BASE_URL = 'http://localhost/proyecto%20T3-T4/Lovemakeup-3.0/';

// === CONFIGURACIÓN DEL NAVEGADOR ===
const BROWSER = 'edge';

//  NÚMERO DE REPETICIONES ===
const TOTAL_ITERACIONES = 100;

// CREDENCIALES  
const CREDENCIALES = [
  { tipoDoc: 'V', usuario: '10200300', password: 'love1234' },
  { tipoDoc: 'V', usuario: '11697944', password: 'love1234' },
  { tipoDoc: 'V', usuario: '32121168', password: 'love1234' },
  { tipoDoc: 'V', usuario: '15200300', password: 'love1334' },
  { tipoDoc: 'V', usuario: '15330450', password: 'love1334' }
];

// TEST AUTOMATIZADO: LOGIN EXITOSO 
async function runTest(iteracion) {
  let driver;
  let status = 'f';

  // Seleccionar una credencial aleatoria del array
  const credencialActual = CREDENCIALES[Math.floor(Math.random() * CREDENCIALES.length)];

  try {
    console.log(`[Iteracion ${iteracion}/${TOTAL_ITERACIONES}] Inicializando navegador: ${BROWSER}... Usando usuario: ${credencialActual.usuario}`);
    
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
    
    console.log(`[Iteracion ${iteracion}] Navegador inicializado correctamente.`);
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
    await driver.sleep(2000);
    
    await driver.wait(until.elementLocated(By.id('usuario')), 15000);
    console.log(`[Iteracion ${iteracion}] Pagina de login cargada correctamente.`);

    // === Paso 2: Ingresar credenciales aleatorias ===
    console.log(`[Iteracion ${iteracion}] Ingresando credenciales...`);
    
    // Seleccionar tipo de documento si existe
    try {
      const tipoDocSelect = await driver.findElement(By.id('DocumentoSelct'));
      await driver.wait(until.elementIsVisible(tipoDocSelect), 10000);
      await driver.executeScript(`arguments[0].value = '${credencialActual.tipoDoc}';`, tipoDocSelect);
      await driver.executeScript("arguments[0].dispatchEvent(new Event('change', { bubbles: true }));", tipoDocSelect);
      await driver.sleep(300);
    } catch (e) {
      console.log(`[Iteracion ${iteracion}] No se encontro selector de tipo de documento, continuando...`);
    }
    
    const usuarioInput = await driver.findElement(By.id('usuario'));
    await driver.wait(until.elementIsVisible(usuarioInput), 10000);
    await usuarioInput.clear();
    await usuarioInput.sendKeys(credencialActual.usuario);
    
    const passwordInput = await driver.findElement(By.id('pid'));
    await driver.wait(until.elementIsVisible(passwordInput), 10000);
    await passwordInput.clear();
    await passwordInput.sendKeys(credencialActual.password);
    
    console.log(`[Iteracion ${iteracion}] Credenciales ingresadas correctamente para la cedula: ${credencialActual.usuario}`);

    // === Paso 3: Hacer clic en Ingresar ===
    console.log(`[Iteracion ${iteracion}] Haciendo clic en Ingresar...`);
    
    const ingresarBtn = await driver.findElement(By.id('ingresar'));
    await driver.wait(until.elementIsEnabled(ingresarBtn), 10000);
    await ingresarBtn.click();
    
    // Esperar a que la URL cambie a home o catalogo
    await driver.wait(async () => {
      const url = await driver.getCurrentUrl();
      return url.includes('pagina=home') || url.includes('pagina=catalogo');
    }, 15000);
    
    await driver.sleep(2000);
    
    const currentUrl = await driver.getCurrentUrl();
    if (currentUrl.includes('pagina=home') || currentUrl.includes('pagina=catalogo')) {
      console.log(`[Iteracion ${iteracion}] Login exitoso. Redireccion confirmada en: ${currentUrl}`);
      status = 'p';
    } else {
      throw new Error('No se redirigio a la pagina esperada (home o catalogo). URL actual: ' + currentUrl);
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

// EJECUCIÓN SECUENCIAL 
async function runLoop() {
  console.log(`Iniciando ejecucion de ${TOTAL_ITERACIONES} pruebas automatizadas...\n`);
  for (let i = 1; i <= TOTAL_ITERACIONES; i++) {
    try {
      await runTest(i);
    } catch (err) {
      console.error(`La iteracion ${i} finalizo con error critico.`);
    }
    // Pequeña pausa opcional de 1 segundo entre cada iteración para estabilizar el sistema
    await new Promise(resolve => setTimeout(resolve, 1000));
  }
  console.log('Todas las iteraciones han finalizado.');
}

if (require.main === module) {
  runLoop().catch(error => {
    console.error('Error fatal en la ejecucion de las pruebas:', error);
    process.exit(1);
  });
}

module.exports = { runTest };