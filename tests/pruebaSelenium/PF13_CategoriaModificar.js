
'use strict';

const { Builder, By, until } = require('selenium-webdriver');
const edge = require('selenium-webdriver/edge');
const crypto = require('crypto');
const fs = require('fs');
const path = require('path');

const BASE_URL = 'http://localhost:8080/PROYECTO/Lovemakeup-3.0/';
const USUARIO = process.env.LM_USER || '10200300';
const CLAVE = process.env.LM_PASS || 'love1234';

async function ejecutarPrueba() {
    let driver;

    try {
        driver = await new Builder()
            .forBrowser('MicrosoftEdge')
            .setEdgeOptions(new edge.Options())
            .build();

        await driver.manage().window().maximize();

        console.log('\nPF-13 - MODIFICAR CATEGORIA');

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

        // 2. Entrar al modulo
        await driver.get(BASE_URL + '?pagina=categoria');

        await driver.wait(
            until.elementLocated(By.css('#myTable tbody tr[data-id]')),
            15000
        );

        console.log('Modulo Categoria cargado.');

        // 3. Seleccionar una categoria existente
        const filas = await driver.findElements(
            By.css('#myTable tbody tr[data-id]')
        );

        let seleccionada = null;
        let primeraDisponible = null;

        for (const fila of filas) {
            const botones = await fila.findElements(
                By.css('.btnModif')
            );

            if (!botones.length || !(await fila.isDisplayed())) {
                continue;
            }

            const nombre = (
                await fila.findElement(
                    By.css('td .text-dark b')
                ).getText()
            ).trim();

            const categoria = {
                fila,
                nombre
            };

            if (!primeraDisponible) {
                primeraDisponible = categoria;
            }

            // Preferir categorias creadas para pruebas
            if (/categoria|prueba|selenium/i.test(nombre)) {
                seleccionada = categoria;
                break;
            }
        }

        seleccionada = seleccionada || primeraDisponible;

        if (!seleccionada) {
            throw new Error(
                'No hay categorias disponibles para modificar.'
            );
        }

        console.log(
            'Categoria seleccionada: ' + seleccionada.nombre
        );

        // 4. Abrir formulario de modificacion
        await seleccionada.fila.findElement(
            By.css('.btnModif')
        ).click();

        const modal = await driver.wait(
            until.elementLocated(By.id('registro')),
            10000
        );

        await driver.wait(
            until.elementIsVisible(modal),
            10000
        );

        console.log('Formulario de modificacion abierto.');

        // 5. Cambiar nombre
        const letras = 'abcdefghijklmnopqrstuvwxyz';

        const sufijo = Array.from(
            crypto.randomBytes(8),
            b => letras[b % letras.length]
        ).join('');

        const nuevoNombre = 'Categoria Modificada ' + sufijo;

        const nombre = await driver.findElement(
            By.id('nombre')
        );

        await nombre.clear();
        await nombre.sendKeys(nuevoNombre);

        const valorReal = await nombre.getAttribute('value');
        const accionReal = await driver.findElement(By.id('accion')).getAttribute('value');

        console.log('Nombre enviado:', nuevoNombre);
        console.log('Nombre dentro del campo:', JSON.stringify(valorReal));
        console.log('Accion del formulario:', accionReal);
        console.log(
            'Nombre valido:',
            /^[A-Za-zÁÉÍÓÚáéíóúÑñ\s]{3,50}$/.test(valorReal.trim())
        );

        console.log('Nuevo nombre: ' + nuevoNombre);

        // 6. Guardar modificacion
        await driver.findElement(
            By.id('btnEnviar')
        ).click();

        // 7. Comprobar mensaje de exito
        await driver.wait(async () => {
            const mensajes = await driver.findElements(
                By.css('.swal2-popup.swal2-show .swal2-title')
            );

            for (const mensaje of mensajes) {
                const texto = await mensaje.getText();

                if (texto.includes('Categoría modificada')) {
                    return true;
                }
            }

            return false;
        }, 10000, 'No aparecio el mensaje de modificacion exitosa.');

        // 8. Captura de evidencia
        const carpeta = path.join(__dirname, 'evidencias');

        fs.mkdirSync(carpeta, { recursive: true });

        fs.writeFileSync(
            path.join(carpeta, 'PF13_ModificarCategoria.png'),
            await driver.takeScreenshot(),
            'base64'
        );

        console.log('Mensaje: Categoria modificada.');
        console.log('\nPF-13: APROBADA');

    } catch (error) {
        console.error('\nPF-13: FALLIDA');
        console.error('Error: ' + error.message);
        process.exitCode = 1;

    } finally {
        if (driver) {
            await driver.quit();
        }
    }
}

ejecutarPrueba();
