'use strict';

// PF-14: Eliminar Categoria - LoveMakeup 3.0

const { Builder, By, until } = require('selenium-webdriver');
const edge = require('selenium-webdriver/edge');
const fs = require('fs');
const path = require('path');

const BASE_URL = (
  process.env.LM_URL ||
  'http://localhost:8080/PROYECTO/Lovemakeup-3.0/'
).replace(/\/?$/, '/');

const USUARIO = process.env.LM_USER || '10200300';
const CLAVE = process.env.LM_PASS || 'love1234';

async function ejecutarPrueba() {
  let driver;

  try {
    const destino = new URL(BASE_URL);

    if (!['localhost', '127.0.0.1', '::1'].includes(destino.hostname)) {
      throw new Error('La prueba solo puede ejecutarse en localhost.');
    }

    driver = await new Builder()
      .forBrowser('MicrosoftEdge')
      .setEdgeOptions(new edge.Options())
      .build();

    await driver.manage().window().maximize();

    console.log('\nPF-14 - ELIMINAR CATEGORIA');

    // 1. Iniciar sesion
    await driver.get(BASE_URL + '?pagina=login');

    await driver.wait(
      until.elementLocated(By.id('usuario')),
      15000
    );

    await driver.findElement(By.id('DocumentoSelct')).sendKeys('V');
    await driver.findElement(By.id('usuario')).sendKeys(USUARIO);
    await driver.findElement(By.id('pid')).sendKeys(CLAVE);
    await driver.findElement(By.id('ingresar')).click();

    await driver.wait(
      until.urlContains('pagina=home'),
      20000
    );

    console.log('Inicio de sesion exitoso.');

    // 2. Ingresar al modulo Categoria
    await driver.get(BASE_URL + '?pagina=categoria');

    await driver.wait(
      until.elementLocated(
        By.css('#myTable tbody tr[data-id]')
      ),
      15000
    );

    console.log('Modulo Categoria cargado.');

    // 3. Buscar exclusivamente una categoria de prueba
    const filas = await driver.findElements(
      By.css('#myTable tbody tr[data-id]')
    );

    let categoriaSeleccionada = null;

    for (const fila of filas) {
      if (!(await fila.isDisplayed())) continue;

      const nombre = (
        await fila.findElement(
          By.css('td .text-dark b')
        ).getText()
      ).trim();

      // Solo categorias generadas por nuestras pruebas
      if (!/^Categoria (?:Modificada |Editada )?[a-z]{8}$/i.test(nombre)) {
        continue;
      }

      const botones = await fila.findElements(
        By.css('button.btnElim')
      );

      if (botones.length > 0) {
        categoriaSeleccionada = {
          nombre,
          boton: botones[0]
        };
        break;
      }
    }

    if (!categoriaSeleccionada) {
      throw new Error(
        'No se encontro una categoria de prueba visible para eliminar.'
      );
    }

    console.log(
      'Categoria seleccionada: ' + categoriaSeleccionada.nombre
    );

    // 4. Presionar Eliminar
    await categoriaSeleccionada.boton.click();

    // 5. Confirmar la eliminacion
    const confirmacion = await driver.wait(
      until.elementLocated(
        By.css('.swal2-popup.swal2-show .swal2-confirm')
      ),
      10000
    );

    await driver.wait(
      until.elementIsVisible(confirmacion),
      10000
    );

    await confirmacion.click();

    console.log('Eliminacion confirmada.');

    // 6. Verificar el mensaje de exito
    await driver.wait(async () => {
      const mensajes = await driver.findElements(
        By.css('.swal2-popup.swal2-show .swal2-title')
      );

      for (const mensaje of mensajes) {
        const texto = await mensaje.getText();

        if (texto.includes('Categoría eliminada')) {
          return true;
        }
      }

      return false;
    }, 10000, 'No aparecio el mensaje de eliminacion exitosa.');

    // 7. Guardar evidencia
    const carpeta = path.join(__dirname, 'evidencias');

    fs.mkdirSync(carpeta, { recursive: true });

    fs.writeFileSync(
      path.join(carpeta, 'PF14_EliminarCategoria.png'),
      await driver.takeScreenshot(),
      'base64'
    );

    console.log('Mensaje: Categoria eliminada.');
    console.log('\nPF-14: APROBADA');

  } catch (error) {
    console.error('\nPF-14: FALLIDA');
    console.error('Error: ' + error.message);
    process.exitCode = 1;

  } finally {
    if (driver) {
      await driver.quit();
    }
  }
}

ejecutarPrueba();
