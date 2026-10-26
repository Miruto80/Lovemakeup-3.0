// === DEPENDENCIAS ===
const { Builder, By, Key, until } = require('selenium-webdriver');
const edge = require('selenium-webdriver/edge');

// === CONFIGURACIÓN DE URLS ===
const BASE_URL = 'http://localhost/proyecto%20T3-T4/Lovemakeup-3.0/';

// === CONFIGURACIÓN DEL NAVEGADOR ===
const BROWSER = 'edge';

// === LISTA SECUENCIAL DE NOMBRES A BUSCAR EN EL DATATABLE PARA MODIFICAR ===
const ROLES_A_MODIFICAR = [
  'Supervisor 855',
  'Supervisor 758',
  'Asesor 379',
  'Operador 855',
  'Administrador 490',
];

const TOTAL_ITERACIONES = ROLES_A_MODIFICAR.length;

// === DATOS BASE PARA GENERAR NUEVOS VALORES ALEATORIOS ===
const NUEVOS_NOMBRES_BASE = ['Director', 'Coordinador', 'Encargado', 'Especialista', 'Analista', 'Supervisor Jr'];

// === TEST AUTOMATIZADO: MODIFICAR TIPO USUARIO (UNA ITERACIÓN) ===
async function runTest(iteracion, nombreRolBuscar) {
  let driver;
  let status = 'f';

  // Generar datos aleatorios para la modificación en esta iteración
  const nombreBaseAleatorio = NUEVOS_NOMBRES_BASE[Math.floor(Math.random() * NUEVOS_NOMBRES_BASE.length)];
  const nuevoNombreRol = `${nombreBaseAleatorio} ${Math.floor(100 + Math.random() * 900)}`; // Ej: Coordinador 542
  
  // Seleccionar aleatoriamente el nivel 2 o 3 para el select id="nivel_modificar"
  const nivelesDisponibles = ['2', '3'];
  const nuevoNivel = nivelesDisponibles[Math.floor(Math.random() * nivelesDisponibles.length)];

  try {
    console.log(`[Iteracion ${iteracion}/${TOTAL_ITERACIONES}] Inicializando navegador: ${BROWSER}... Buscando rol: "${nombreRolBuscar}" | Nuevos datos -> Nombre: "${nuevoNombreRol}", Nivel: ${nuevoNivel}`);
    
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
    await driver.sleep(1500); // Pausa para que el DataTable actualice la vista

    // === Paso 6: Hacer clic en el botón de editar asociado al elemento filtrado ===
    console.log(`[Iteracion ${iteracion}] Localizando el boton de editar para el rol "${nombreRolBuscar}"...`);
    
    let editarBtn;
    try {
      editarBtn = await driver.findElement(By.css('button.modificar[data-bs-target="#modificar"], button[data-bs-target="#modificar"]'));
    } catch (e) {
      const botonesEditar = await driver.findElements(By.css('.modificar'));
      editarBtn = botonesEditar[0];
    }

    await driver.executeScript("arguments[0].scrollIntoView({block: 'center', behavior: 'smooth'});", editarBtn);
    await driver.sleep(500);
    await driver.wait(until.elementIsVisible(editarBtn), 10000);
    await editarBtn.click();
    await driver.sleep(1500);
    console.log(`[Iteracion ${iteracion}] Boton de editar clickeado.`);

    // Verificar que el modal #modificar esté abierto
    const modalEditar = await driver.wait(until.elementLocated(By.id('modificar')), 5000);
    await driver.wait(until.elementIsVisible(modalEditar), 5000);
    await driver.sleep(1000);
    console.log(`[Iteracion ${iteracion}] Modal de modificacion abierto correctamente.`);

    // === Paso 7: Modificar los datos en el modal ===
    console.log(`[Iteracion ${iteracion}] Borrando y rellenando los campos del modal...`);

    // Nombre del rol (id: nombre_modificar)
    const nombreModInput = await driver.findElement(By.id('nombre_modificar'));
    await driver.wait(until.elementIsVisible(nombreModInput), 5000);
    await nombreModInput.clear();
    await driver.sleep(300);
    await nombreModInput.sendKeys(nuevoNombreRol);
    await driver.sleep(300);

    // Select del nivel (id: nivel_modificar) -> Nivel 2 o Nivel 3
    try {
      const nivelModSelect = await driver.findElement(By.id('nivel_modificar'));
      await driver.executeScript(`
        arguments[0].value = '${nuevoNivel}';
        arguments[0].dispatchEvent(new Event('change', { bubbles: true }));
        arguments[0].dispatchEvent(new Event('input', { bubbles: true }));
      `, nivelModSelect);
      await driver.sleep(500);
    } catch (e) {
      console.log(`[Iteracion ${iteracion}] No se pudo configurar nivel_modificar, continuando...`);
    }

    // === Paso 8: Hacer clic en el botón de actualizar (dentro del modal o con clase/id correspondiente) ===
    console.log(`[Iteracion ${iteracion}] Guardando cambios...`);
    
    let btnActualizar;
    try {
      btnActualizar = await driver.findElement(By.id('btnModificar'));
    } catch (e) {
      try {
        btnActualizar = await driver.findElement(By.css('#modificar button[type="submit"], #modificar .btn-primary, #modificar .btn-success'));
      } catch (e2) {
        btnActualizar = await driver.findElement(By.xpath("//div[@id='modificar']//button[contains(text(), 'Actualizar') or contains(text(), 'Guardar')]"));
      }
    }

    await driver.wait(until.elementIsEnabled(btnActualizar), 5000);
    await btnActualizar.click();
    await driver.sleep(1500);

    // Confirmación previa de SweetAlert si aparece ("¿Deseas Cambiar estos datos?")
    try {
      const confirmSwalBtn = await driver.wait(
        until.elementLocated(By.css('.swal2-confirm, .swal2-styled.swal2-confirm')), 
        3000
      );
      if (await confirmSwalBtn.isDisplayed()) {
        await confirmSwalBtn.click();
        await driver.sleep(1500);
      }
    } catch (e) {
      // Continuar si no hay confirmación previa
    }

    // === Paso 9: Verificar si la alerta es success o error (SweetAlert2) ===
    console.log(`[Iteracion ${iteracion}] Verificando resultado de la modificacion...`);
    
    try {
      const swalPopup = await driver.wait(
        until.elementLocated(By.css('.swal2-popup, .swal2-container')), 
        10000
      );
      await driver.wait(until.elementIsVisible(swalPopup), 5000);

      const esSuccess = await driver.findElements(By.css('.swal2-success, .swal2-icon-success'));
      const esError = await driver.findElements(By.css('.swal2-error, .swal2-icon-error, .swal2-warning'));

      if (esSuccess.length > 0) {
        console.log(`[Iteracion ${iteracion}] Alerta SUCCESS: Todo esta bien. El tipo de usuario fue modificado exitosamente.`);
        status = 'p';
      } else if (esError.length > 0) {
        console.log(`[Iteracion ${iteracion}] Alerta ERROR: Hubo un error al intentar modificar el tipo de usuario.`);
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
    console.error(`[Iteracion ${iteracion}] ERROR: Hubo un error durante la prueba de modificacion para el rol "${nombreRolBuscar}":`, error.message);
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
  console.log(`Iniciando ejecucion de ${TOTAL_ITERACIONES} pruebas automatizadas de modificacion de tipos de usuario en orden secuencial...\n`);
  
  for (let i = 0; i < ROLES_A_MODIFICAR.length; i++) {
    const iteracionActual = i + 1;
    const rolActual = ROLES_A_MODIFICAR[i];
    
    try {
      await runTest(iteracionActual, rolActual);
    } catch (err) {
      console.error(`La iteracion ${iteracionActual} finalizo con error critico.`);
    }
    
    // Pausa de 2 segundos entre cada iteración para estabilizar el sistema
    await new Promise(resolve => setTimeout(resolve, 2000));
  }
  console.log('Todas las iteraciones de modificacion de tipos de usuario han finalizado.');
}

if (require.main === module) {
  runLoop().catch(error => {
    console.error('Error fatal en la ejecucion de las pruebas:', error);
    process.exit(1);
  });
}

module.exports = { runTest };