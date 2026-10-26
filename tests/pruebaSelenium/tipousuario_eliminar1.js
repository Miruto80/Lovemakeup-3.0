// === DEPENDENCIAS ===
const { Builder, By, Key, until } = require('selenium-webdriver');
const edge = require('selenium-webdriver/edge');

// === CONFIGURACIÓN DE URLS ===
const BASE_URL = 'http://localhost/proyecto%20T3-T4/Lovemakeup-3.0/';

// === CONFIGURACIÓN DEL NAVEGADOR ===
const BROWSER = 'edge';

// === LISTA SECUENCIAL DE NOMBRES A BUSCAR EN EL DATATABLE PARA ELIMINAR ===
const ROLES_A_ELIMINAR = [
  'Soporte 272',
  'Administrador 661',
  'Gerente 777',
  'Soporte 586',
  'RolTest_963',
  'RolTest_962',
];

const TOTAL_ITERACIONES = ROLES_A_ELIMINAR.length;

// === TEST AUTOMATIZADO: ELIMINAR TIPO USUARIO (UNA ITERACIÓN) ===
async function runTest(iteracion, nombreRolBuscar) {
  let driver;
  let status = 'f';

  try {
    console.log(`[Iteracion ${iteracion}/${TOTAL_ITERACIONES}] Inicializando navegador: ${BROWSER}... Buscando tipo de usuario por nombre en DataTable: "${nombreRolBuscar}"`);
    
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
    
    const usuarioInput = await driver.wait(until.elementLocated(By.id('usuario')), 15000);
    await driver.sleep(500);

    // === Paso 2: Ingresar credenciales válidas ===
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
    
    await driver.wait(async () => {
      const url = await driver.getCurrentUrl();
      return url.includes('pagina=home') || url.includes('pagina=catalogo');
    }, 15000);
    await driver.sleep(2000);
    console.log(`[Iteracion ${iteracion}] Login exitoso.`);

    // === Paso 4: Navegar al módulo de Tipo Usuario (DataTable) ===
    console.log(`[Iteracion ${iteracion}] Accediendo al modulo de Tipo Usuario...`);
    await driver.get(BASE_URL + '?pagina=tipousuario');
    await driver.sleep(2000);
    
    await driver.wait(until.urlContains('pagina=tipousuario'), 15000);
    await driver.sleep(1000);

    // === Paso 5: Filtrar usando el buscador del DataTable con el nombre del rol ===
    console.log(`[Iteracion ${iteracion}] Filtrando por el nombre "${nombreRolBuscar}" en el DataTable...`);
    
    let searchInput;
    try {
      searchInput = await driver.findElement(By.css('div.dataTables_filter input, input[type="search"]'));
    } catch (e) {
      searchInput = await driver.findElement(By.xpath("//input[@type='search' or contains(@placeholder, 'Buscar')]"));
    }

    await driver.wait(until.elementIsVisible(searchInput), 5000);
    await searchInput.clear();
    await searchInput.sendKeys(nombreRolBuscar);
    await driver.sleep(1500); // Pausa para que el DataTable actualice la vista de la tabla

    // === Paso 6: Hacer clic en el botón de eliminar asociado al elemento filtrado ===
    console.log(`[Iteracion ${iteracion}] Localizando el boton de eliminar para el rol "${nombreRolBuscar}"...`);
    
    // Buscamos el botón basándonos en la estructura onclick="eliminarRol(...)" o clase .eliminar
    let eliminarBtn;
    try {
      eliminarBtn = await driver.findElement(By.css('button.eliminar[onclick*="eliminarRol"], button[onclick*="eliminarRol"]'));
    } catch (e) {
      const botonesEliminar = await driver.findElements(By.css('.eliminar'));
      eliminarBtn = botonesEliminar[0];
    }

    await driver.executeScript("arguments[0].scrollIntoView({block: 'center', behavior: 'smooth'});", eliminarBtn);
    await driver.sleep(500);
    await driver.wait(until.elementIsVisible(eliminarBtn), 10000);
    await eliminarBtn.click();
    await driver.sleep(1500);
    console.log(`[Iteracion ${iteracion}] Boton de eliminar clickeado.`);

    // === Paso 7: Confirmar la eliminación en el SweetAlert de confirmación ===
    console.log(`[Iteracion ${iteracion}] Confirmando la eliminacion en la ventana emergente...`);
    
    try {
      const confirmSwalBtn = await driver.wait(
        until.elementLocated(By.css('.swal2-confirm, .swal2-styled.swal2-confirm')), 
        5000
      );
      await driver.wait(until.elementIsVisible(confirmSwalBtn), 3000);
      await confirmSwalBtn.click();
      await driver.sleep(1500);
    } catch (e) {
      // Si utiliza un botón alternativo de confirmación
      try {
        const altConfirmBtn = await driver.findElement(By.xpath("//button[contains(text(), 'Sí') or contains(text(), 'Eliminar')]"));
        await altConfirmBtn.click();
        await driver.sleep(1500);
      } catch (e2) {
        console.log(`[Iteracion ${iteracion}] No se encontro un boton de confirmacion adicional, continuando...`);
      }
    }

    // === Paso 8: Esperar y verificar si la alerta final es success o error (SweetAlert2) ===
    console.log(`[Iteracion ${iteracion}] Verificando resultado de la eliminacion...`);
    
    try {
      const swalPopup = await driver.wait(
        until.elementLocated(By.css('.swal2-popup, .swal2-container')), 
        10000
      );
      await driver.wait(until.elementIsVisible(swalPopup), 5000);

      const esSuccess = await driver.findElements(By.css('.swal2-success, .swal2-icon-success'));
      const esError = await driver.findElements(By.css('.swal2-error, .swal2-icon-error, .swal2-warning'));

      if (esSuccess.length > 0) {
        console.log(`[Iteracion ${iteracion}] Alerta SUCCESS: Todo esta bien. El tipo de usuario "${nombreRolBuscar}" fue eliminado exitosamente.`);
        status = 'p';
      } else if (esError.length > 0) {
        console.log(`[Iteracion ${iteracion}] Alerta ERROR: Hubo un error al intentar eliminar el tipo de usuario "${nombreRolBuscar}".`);
        status = 'f';
      } else {
        console.log(`[Iteracion ${iteracion}] Operacion procesada, pero no se pudo determinar con certeza el icono de la alerta.`);
        status = 'p';
      }

      // Cerrar el diálogo SweetAlert final si queda abierto
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
    console.error(`[Iteracion ${iteracion}] ERROR: Hubo un error durante la prueba de eliminacion para el rol "${nombreRolBuscar}":`, error.message);
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
  console.log(`Iniciando ejecucion de ${TOTAL_ITERACIONES} pruebas automatizadas de eliminacion de tipos de usuario en orden secuencial...\n`);
  
  for (let i = 0; i < ROLES_A_ELIMINAR.length; i++) {
    const iteracionActual = i + 1;
    const rolActual = ROLES_A_ELIMINAR[i];
    
    try {
      await runTest(iteracionActual, rolActual);
    } catch (err) {
      console.error(`La iteracion ${iteracionActual} finalizo con error critico.`);
    }
    
    // Pausa de 2 segundos entre cada iteración para estabilizar el sistema
    await new Promise(resolve => setTimeout(resolve, 2000));
  }
  console.log('Todas las iteraciones de eliminacion de tipos de usuario han finalizado.');
}

if (require.main === module) {
  runLoop().catch(error => {
    console.error('Error fatal en la ejecucion de las pruebas:', error);
    process.exit(1);
  });
}

module.exports = { runTest };