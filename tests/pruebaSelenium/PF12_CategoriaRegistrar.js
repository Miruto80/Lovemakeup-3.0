'use strict';

// PF-12: Registrar Categoria - LoveMakeup 3.0

const { Builder, By, until } = require('selenium-webdriver');
const edge = require('selenium-webdriver/edge');
const crypto = require('crypto');
const fs = require('fs');
const path = require('path');

const BASE_URL = (
  process.env.LM_URL ||
  'http://localhost:8080/PROYECTO/Lovemakeup-3.0/'
).replace(/\/?$/, '/');

const USUARIO = process.env.LM_USER || '10200300';
const CLAVE = process.env.LM_PASS || 'love1234';

const letras = 'abcdefghijklmnopqrstuvwxyz';

const sufijo = Array.from(
  crypto.randomBytes(8),
  b => letras[b % letras.length]
).join('');

const nombreCategoria = `Categoria ${sufijo}`;

async function ejecutarPrueba() {
  let driver;

  try {
    const destino = new URL(BASE_URL);

    if (!['localhost', '127.0.0.1', '::1'].includes(destino.hostname)) {
      throw new Error('Solo se permite ejecutar la prueba en localhost.');
    }

    driver = await new Builder()
      .forBrowser('MicrosoftEdge')
      .setEdgeOptions(new edge.Options())
      .build();

    await driver.manage().window().maximize();

    console.log('\nPF-12 - REGISTRAR CATEGORIA');

    // 1. Iniciar sesion
    await driver.get(`${BASE_URL}?pagina=login`);

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

    // 2. Entrar al modulo Categoria
    await driver.get(`${BASE_URL}?pagina=categoria`);

    const botonRegistrar = await driver.wait(
      until.elementLocated(By.id('btnAbrirRegistrar')),
      15000
    );

    await botonRegistrar.click();

    const modal = await driver.findElement(By.id('registro'));

    await driver.wait(
      until.elementIsVisible(modal),
      10000
    );

    console.log('Formulario de registro abierto.');

    // 3. Introducir datos
    await driver.findElement(By.id('nombre'))
      .sendKeys(nombreCategoria);

    console.log('Categoria: ' + nombreCategoria);

    // 4. Registrar
    await driver.findElement(By.id('btnEnviar')).click();

    // 5. Comprobar únicamente el mensaje de éxito
    await driver.wait(async () => {
      const mensajes = await driver.findElements(
        By.css('.swal2-popup.swal2-show .swal2-title')
      );

      for (const mensaje of mensajes) {
        const texto = await mensaje.getText();

        if (texto.includes('Categoría registrada')) {
          return true;
        }
      }

      return false;
    }, 10000, 'No se mostró el mensaje de registro exitoso.');

    // 6. Guardar evidencia
    const carpeta = path.join(__dirname, 'evidencias');

    fs.mkdirSync(carpeta, { recursive: true });

    fs.writeFileSync(
      path.join(carpeta, 'PF12_RegistrarCategoria.png'),
      await driver.takeScreenshot(),
      'base64'
    );

    // Guardar nombre para la prueba de modificacion
    fs.writeFileSync(
      path.join(__dirname, 'PF12_categoria_creada.json'),
      JSON.stringify({
        nombre: nombreCategoria
      }, null, 2),
      'utf8'
    );

    console.log('Mensaje: Categoria registrada.');
    console.log('\nPF-12: APROBADA');

  } catch (error) {
    console.error('\nPF-12: FALLIDA');
    console.error('Error: ' + error.message);
    process.exitCode = 1;

  } finally {
    if (driver) await driver.quit();
  }
}

ejecutarPrueba();
