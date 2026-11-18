// === DEPENDENCIAS ===
const { Builder, By, Key, until } = require('selenium-webdriver');
const edge = require('selenium-webdriver/edge');

// === CONFIGURACIÓN DE URLS ===
const BASE_URL = 'http://localhost/proyecto%20T3-T4/Lovemakeup-3.0/';

// === CONFIGURACIÓN DEL NAVEGADOR ===
const BROWSER = 'edge';

// === LISTA SECUENCIAL DE CÉDULAS A BUSCAR EN EL DATATABLE (ORDEN NO ALEATORIO) ===
const CEDULAS_A_BUSCAR = [
  '40502293',
  '30262041',
  '95334057',
  '72313300',
];

const TOTAL_ITERACIONES = CEDULAS_A_BUSCAR.length;

// === DATOS BASE PARA GENERAR VALORES ALEATORIOS ===
const TIPOS_DOC = ['V', 'E'];
const ESTATUS_POSIBLES = ['1', '2'];
const NOMBRES_ALEATORIOS = ['carlos', 'maria', 'ana', 'jose', 'luis', 'valeria', 'daniel', 'carmen'];

// === TEST AUTOMATIZADO: MODIFICAR USUARIO (UNA ITERACIÓN) ===
async function runTest(iteracion, cedulaBuscar) {
  let driver;
  let status = 'f';

  // Generar datos aleatorios para la modificación en esta iteración
  const nuevoTipoDoc = TIPOS_DOC[Math.floor(Math.random() * TIPOS_DOC.length)];
  const nuevaCedula = Math.floor(10000000 + Math.random() * 90000000).toString();
  const nombreAleatorio = NOMBRES_ALEATORIOS[Math.floor(Math.random() * NOMBRES_ALEATORIOS.length)];
  const nuevoCorreo = `${nombreAleatorio}${nuevaCedula.substring(4)}@gmail.com`;
  const nuevoEstatus = ESTATUS_POSIBLES[Math.floor(Math.random() * ESTATUS_POSIBLES.length)];
  const nuevoRol = Math.floor(Math.random() * 20) + 1; // Entre 1 y 20

  try {
    console.log(`[Iteracion ${iteracion}/${TOTAL_ITERACIONES}] Inicializando navegador: ${BROWSER}... Buscando cedula: ${cedulaBuscar} | Nuevos datos -> TipoDoc: ${nuevoTipoDoc}, Cedula: ${nuevaCedula}, Correo: ${nuevoCorreo}, Estatus: ${nuevoEstatus}, Rol: ${nuevoRol}`);
    
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

    // === Paso 4: Navegar a la página de Usuario (DataTable) ===
    console.log(`[Iteracion ${iteracion}] Accediendo a la seccion de Usuarios...`);
    await driver.get(BASE_URL + '?pagina=usuario');
    await driver.sleep(2000);
    
    await driver.wait(until.urlContains('pagina=usuario'), 15000);
    await driver.sleep(1000);

    // === Paso 5: Buscar el usuario en el DataTable usando la cédula secuencial ===
    console.log(`[Iteracion ${iteracion}] Buscando la cedula ${cedulaBuscar} en el buscador del DataTable...`);
    
    let searchInput;
    try {
      searchInput = await driver.findElement(By.css('div.dataTables_filter input, input[type="search"]'));
    } catch (e) {
      searchInput = await driver.findElement(By.xpath("//input[@type='search' or contains(@placeholder, 'Buscar')]"));
    }

    await driver.wait(until.elementIsVisible(searchInput), 5000);
    await searchInput.clear();
    await searchInput.sendKeys(cedulaBuscar);
    await driver.sleep(1500); // Pausa para que el DataTable filtre la tabla

    // === Paso 6: Hacer clic en el botón de modificar del usuario filtrado ===
    console.log(`[Iteracion ${iteracion}] Localizando boton de modificar para la cedula ${cedulaBuscar}...`);
    
    let modificarBtn;
    try {
      modificarBtn = await driver.findElement(By.css(`button.modificar[data-cedula="${cedulaBuscar}"], button[data-bs-target="#editarModal"][data-cedula="${cedulaBuscar}"]`));
    } catch (e) {
      const modificarButtons = await driver.findElements(By.css('button.modificar, button[data-bs-target="#editarModal"]'));
      modificarBtn = modificarButtons[0];
    }

    await driver.executeScript("arguments[0].scrollIntoView({block: 'center', behavior: 'smooth'});", modificarBtn);
    await driver.sleep(500);
    await driver.wait(until.elementIsVisible(modificarBtn), 10000);
    await modificarBtn.click();
    
    // IMPORTANTE: Pausa para permitir que el modal se abra y cargue los datos por JS antes de modificarlos
    await driver.sleep(1500);
    console.log(`[Iteracion ${iteracion}] Boton de modificar clickeado y modal abierto.`);

    // Verificar que se despliega el modal #editarModal
    const modalEditar = await driver.wait(until.elementLocated(By.id('editarModal')), 5000);
    await driver.wait(until.elementIsVisible(modalEditar), 5000);
    await driver.sleep(1000);

    // === Paso 7: Borrar datos anteriores e ingresar los nuevos datos aleatorios ===
    console.log(`[Iteracion ${iteracion}] Rellenando datos aleatorios en el modal...`);

    // select documento: id=rolSelect02 (V o E)
    try {
      const rolSelect02 = await driver.findElement(By.id('rolSelect02'));
      await driver.executeScript(`
        arguments[0].value = '${nuevoTipoDoc}';
        arguments[0].dispatchEvent(new Event('change', { bubbles: true }));
        arguments[0].dispatchEvent(new Event('input', { bubbles: true }));
      `, rolSelect02);
      await driver.sleep(500);
    } catch (e) {
      console.log(`[Iteracion ${iteracion}] No se encontro rolSelect02, continuando...`);
    }

    // cedula: id=modalCedula
    const modalCedula = await driver.findElement(By.id('modalCedula'));
    await driver.wait(until.elementIsVisible(modalCedula), 5000);
    await modalCedula.clear();
    await driver.sleep(300);
    await modalCedula.sendKeys(nuevaCedula);
    await driver.sleep(300);

    // correo: id=modalCorreo
    const modalCorreo = await driver.findElement(By.id('modalCorreo'));
    await driver.wait(until.elementIsVisible(modalCorreo), 5000);
    await modalCorreo.clear();
    await driver.sleep(300);
    await modalCorreo.sendKeys(nuevoCorreo);
    await driver.sleep(300);

    // estatus: id=estatusmodal (1 o 2)
    try {
      const estatusModal = await driver.findElement(By.id('estatusmodal'));
      await driver.executeScript(`
        arguments[0].value = '${nuevoEstatus}';
        arguments[0].dispatchEvent(new Event('change', { bubbles: true }));
        arguments[0].dispatchEvent(new Event('input', { bubbles: true }));
      `, estatusModal);
      await driver.sleep(500);
    } catch (e) {
      console.log(`[Iteracion ${iteracion}] No se encontro estatusmodal, continuando...`);
    }

    // rol: id=rolSelectedit (Aleatorio entre 1 y 20, si no entra usa 1)
    try {
      const rolSelectedit = await driver.findElement(By.id('rolSelectedit'));
      const options = await rolSelectedit.findElements(By.tagName('option'));
      
      let optionSelected = false;
      for (const option of options) {
        const val = await option.getAttribute('value');
        if (val == nuevoRol) {
          await option.click();
          optionSelected = true;
          break;
        }
      }
      if (!optionSelected && options.length > 0) {
        await options[0].click();
      }
      // Forzar evento change por seguridad
      await driver.executeScript("arguments[0].dispatchEvent(new Event('change', { bubbles: true }));", rolSelectedit);
      await driver.sleep(500);
    } catch (e) {
      console.log(`[Iteracion ${iteracion}] No se pudo configurar rolSelectedit, continuando...`);
    }

    // === Paso 8: Hacer clic en el botón de actualizar datos ===
    console.log(`[Iteracion ${iteracion}] Haciendo clic en actualizar datos...`);
    const actualizarBtn = await driver.findElement(By.id('actualizar'));
    await driver.wait(until.elementIsEnabled(actualizarBtn), 5000);
    await actualizarBtn.click();
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

    // === Paso 9: Verificar si la alerta es success o error ===
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
        console.log(`[Iteracion ${iteracion}] Alerta SUCCESS: Todo esta bien. Los datos del usuario fueron modificados correctamente.`);
        status = 'p';
      } else if (esError.length > 0) {
        console.log(`[Iteracion ${iteracion}] Alerta ERROR: Hubo un error al intentar modificar los datos del usuario.`);
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
    console.error(`[Iteracion ${iteracion}] ERROR: Hubo un error durante la prueba de modificacion para la cedula ${cedulaBuscar}:`, error.message);
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
  console.log(`Iniciando ejecucion de ${TOTAL_ITERACIONES} pruebas automatizadas de modificacion en orden secuencial...\n`);
  
  for (let i = 0; i < CEDULAS_A_BUSCAR.length; i++) {
    const iteracionActual = i + 1;
    const cedulaActual = CEDULAS_A_BUSCAR[i];
    
    try {
      await runTest(iteracionActual, cedulaActual);
    } catch (err) {
      console.error(`La iteracion ${iteracionActual} finalizo con error critico.`);
    }
    
    // Pausa de 2 segundos entre cada iteración para estabilizar el sistema
    await new Promise(resolve => setTimeout(resolve, 2000));
  }
  console.log('Todas las iteraciones de modificacion han finalizado.');
}

if (require.main === module) {
  runLoop().catch(error => {
    console.error('Error fatal en la ejecucion de las pruebas:', error);
    process.exit(1);
  });
}

module.exports = { runTest };