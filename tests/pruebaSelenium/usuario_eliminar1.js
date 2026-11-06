// === DEPENDENCIAS ===
const { Builder, By, Key, until } = require('selenium-webdriver');
const edge = require('selenium-webdriver/edge');

// === CONFIGURACIÓN DE URLS ===
const BASE_URL = 'http://localhost/proyecto%20T3-T4/Lovemakeup-3.0/';

// === CONFIGURACIÓN DEL NAVEGADOR ===
const BROWSER = 'edge';

// === NÚMERO DE REPETICIONES Y LISTA SECUENCIAL DE CÉDULAS A ELIMINAR ===
const CEDULAS_A_ELIMINAR = [
  '10200300',
  '74283221',
  '97561191',
  '37496275',
  '24862409'
];

const TOTAL_ITERACIONES = CEDULAS_A_ELIMINAR.length; //ITERACIONES

// === TEST AUTOMATIZADO: ELIMINAR USUARIO (UNA ITERACIÓN) ===
async function runTest(iteracion, cedulaObjetivo) {
  let driver;
  let status = 'f';

  try {
    console.log(`[Iteracion ${iteracion}/${TOTAL_ITERACIONES}] Inicializando navegador: ${BROWSER}... Buscando cedula a eliminar: ${cedulaObjetivo}`);
    
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
    await driver.sleep(2000);
    
    const usuarioInput = await driver.wait(until.elementLocated(By.id('usuario')), 1000);
    await driver.sleep(500);

    // === Paso 2: Ingresar credenciales válidas del administrador ===
    try {
      const tipoDocSelect = await driver.findElement(By.id('DocumentoSelct'));
      await driver.executeScript("arguments[0].value = 'V';", tipoDocSelect);
      await driver.executeScript("arguments[0].dispatchEvent(new Event('change', { bubbles: true }));", tipoDocSelect);
      await driver.sleep(300);
    } catch (e) {
      // Ignorar si no aplica
    }
    
    await usuarioInput.clear();
    await usuarioInput.sendKeys('10200300');
    
    const passwordInput = await driver.findElement(By.id('pid'));
    await passwordInput.clear();
    await passwordInput.sendKeys('love1234');
    await driver.sleep(500);

    // === Paso 3: Hacer clic en Ingresar ===
    const ingresarBtn = await driver.findElement(By.id('ingresar'));
    await ingresarBtn.click();
    
    // Esperar redirección a home o catalogo
    await driver.wait(async () => {
      const url = await driver.getCurrentUrl();
      return url.includes('pagina=home') || url.includes('pagina=catalogo');
    }, 15000);
    await driver.sleep(1000);
    console.log(`[Iteracion ${iteracion}] Login exitoso.`);

    // === Paso 4: Navegar a la página de Usuario (Donde está el DataTable) ===
    console.log(`[Iteracion ${iteracion}] Accediendo a la seccion de Usuarios...`);
    await driver.get(BASE_URL + '?pagina=usuario');
    await driver.sleep(1000);
    
    await driver.wait(until.urlContains('pagina=usuario'), 1000);
    await driver.sleep(1000);

    // === Paso 5: Buscar la cédula en el buscador del DataTable ===
    console.log(`[Iteracion ${iteracion}] Buscando la cedula ${cedulaObjetivo} en el DataTable...`);
    
    // Los DataTables de Bootstrap/jQuery suelen usar un input de búsqueda dentro de un div con clase 'dataTables_filter' o similar
    let searchInput;
    try {
      searchInput = await driver.findElement(By.css('div.dataTables_filter input, input[type="search"]'));
    } catch (e) {
      searchInput = await driver.findElement(By.xpath("//input[@type='search' or contains(@placeholder, 'Buscar')]"));
    }

    await driver.wait(until.elementIsVisible(searchInput), 5000);
    await searchInput.clear();
    await searchInput.sendKeys(cedulaObjetivo);
    await driver.sleep(1000); // Pausa para que el DataTable filtre la tabla en pantalla

    // === Paso 6: Localizar y hacer clic en el botón de eliminar del usuario filtrado ===
    console.log(`[Iteracion ${iteracion}] Localizando boton eliminar para la cedula ${cedulaObjetivo}...`);
    
    // Basado en tu estructura HTML: <button id="eliminar" value="CEDULA">
    const botonEliminar = await driver.wait(
      until.elementLocated(By.css(`button#eliminar[value="${cedulaObjetivo}"], button.eliminar[value="${cedulaObjetivo}"]`)),
      1000
    );
    await driver.wait(until.elementIsVisible(botonEliminar), 5000);
    await driver.executeScript("arguments[0].scrollIntoView({block: 'center', behavior: 'smooth'});", botonEliminar);
    await driver.sleep(1000);
    await botonEliminar.click();
    await driver.sleep(1000);

    // === Paso 7: Confirmar la eliminación en la alerta / modal (SweetAlert) ===
    console.log(`[Iteracion ${iteracion}] Confirmando la eliminacion en la ventana emergente...`);
    
    try {
      const confirmBtn = await driver.wait(
        until.elementLocated(By.css('.swal2-confirm, .swal2-styled.swal2-confirm, button.swal2-confirm')),
        5000
      );
      await driver.wait(until.elementIsVisible(confirmBtn), 3000);
      await confirmBtn.click();
      await driver.sleep(1000);
    } catch (e) {
      // Si usa confirm nativo del navegador
      try {
        const alert = await driver.switchTo().alert();
        await alert.accept();
        await driver.sleep(1000);
      } catch (e2) {
        console.log(`[Iteracion ${iteracion}] No se requirio confirmacion adicional, continuando...`);
      }
    }

    // === Paso 8: Validar alerta de éxito o error ===
    console.log(`[Iteracion ${iteracion}] Verificando resultado de la operacion...`);
    
    try {
      // Esperar a que aparezca cualquier indicador de SweetAlert (éxito o error)
      const swalPopup = await driver.wait(
        until.elementLocated(By.css('.swal2-popup, .swal2-container')),
        1000
      );
      await driver.wait(until.elementIsVisible(swalPopup), 500);

      // Verificar si es de tipo success o error
      const esSuccess = await driver.findElements(By.css('.swal2-success, .swal2-icon-success'));
      const esError = await driver.findElements(By.css('.swal2-error, .swal2-icon-error, .swal2-warning'));

      if (esSuccess.length > 0) {
        console.log(`[Iteracion ${iteracion}] Usuario ${cedulaObjetivo} eliminado correctamente.`);
        status = 'p';
      } else if (esError.length > 0) {
        console.log(`[Iteracion ${iteracion}] Error: Hubo un error al intentar eliminar al usuario ${cedulaObjetivo}.`);
        status = 'f';
      } else {
        console.log(`[Iteracion ${iteracion}] Operacion procesada, pero no se pudo clasificar con certeza el icono de la alerta.`);
        status = 'p';
      }

      // Cerrar el diálogo SweetAlert si sigue abierto
      try {
        const okBtn = await driver.findElement(By.css('.swal2-confirm'));
        await okBtn.click();
        await driver.sleep(500);
      } catch (err) {
        // Ignorar si ya se cerró solo
      }

    } catch (e) {
      throw new Error('No se detecto la alerta de resultado en pantalla.');
    }

  } catch (error) {
    console.error(`[Iteracion ${iteracion}] ERROR: Hubo un error durante la prueba para la cedula ${cedulaObjetivo}:`, error.message);
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

// === EJECUCIÓN SECUENCIAL MÚLTIPLE ===
async function runLoop() {
  console.log(`Iniciando ejecucion de ${TOTAL_ITERACIONES} pruebas automatizadas de eliminacion en orden secuencial...\n`);
  
  for (let i = 0; i < CEDULAS_A_ELIMINAR.length; i++) {
    const iteracionActual = i + 1;
    const cedulaActual = CEDULAS_A_ELIMINAR[i];
    
    try {
      await runTest(iteracionActual, cedulaActual);
    } catch (err) {
      console.error(`La iteracion ${iteracionActual} finalizo con error critico.`);
    }
    
    // Pausa de 2 segundos entre cada iteración para estabilizar el sistema
    await new Promise(resolve => setTimeout(resolve, 900));
  }
  console.log('Todas las iteraciones de eliminacion han finalizado.');
}

if (require.main === module) {
  runLoop().catch(error => {
    console.error('Error fatal en la ejecucion de las pruebas:', error);
    process.exit(1);
  });
}

module.exports = { runTest };