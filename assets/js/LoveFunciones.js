/*||| Funcion para cambiar el boton a loader |||*/
export function activarLoaderBoton(idBoton, texto) {
    const $boton = $(idBoton);
    const textoActual = $boton.html();
    $boton.data('texto-original', textoActual); 
    $boton.prop('disabled', true);
    $boton.html(`<span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span>${texto}`);
}

export function desactivarLoaderBoton(idBoton) {
    const $boton = $(idBoton);
    const textoOriginal = $boton.data('texto-original');
    $boton.prop('disabled', false);
    $boton.html(textoOriginal);
}

export function validarCampoA($elemento, regex, $mensajeError, textoError) {
    return validarCampo($elemento, regex, $mensajeError, textoError);
}

export function validarkeypress(regex, e) {
    var char = String.fromCharCode(e.which || e.keyCode);
    if (!regex.test(char)) {
        e.preventDefault();
    }
}

//Función para validar por keyup
export function validarkeyup(er,etiqueta,etiquetamensaje,mensaje){
    a = er.test(etiqueta.val());
    if(a){
        etiquetamensaje.text("");
        return 1;
    }
    else{
        etiquetamensaje.text(mensaje);
        return 0;
    }
}

export function muestraMensajetost(icono, titulo, mensaje, tiempo) {
  Swal.fire({
    icon: icono,
    title: titulo,
    text: mensaje,
    toast: true,
    position: "top",
    showConfirmButton: false,
    timer: tiempo,
    timerProgressBar: true,
    didOpen: (toast) => {
      toast.onmouseenter = Swal.stopTimer;
      toast.onmouseleave = Swal.resumeTimer;
    }
  });
}



export function activarLoaderBotonElemento($boton, texto = 'Eliminando...') {
    const textoActual = $boton.html();
    $boton.data('texto-original', textoActual);
    $boton.prop('disabled', true);
    $boton.html(`<span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span>${texto}`);
}

export function desactivarLoaderBotonElemento($boton) {
    const textoOriginal = $boton.data('texto-original');
    $boton.prop('disabled', false);
    $boton.html(textoOriginal);
}


/*||| Funcion para validar compas de formulario |||*/
// funciones.js

export function validarCampo($elemento, regex, $mensajeError, textoError) {
    var valor = $elemento.val();
    
    if (!regex.test(valor)) {
        $mensajeError.text(textoError).show();
        $elemento.removeClass('is-valid').addClass('is-invalid');
        return false;
    } else {
        $mensajeError.text('').hide();
        $elemento.removeClass('is-invalid').addClass('is-valid');
        return true;
    }
}

export function validarCampoS(campo, regex, textoError, mensaje) {
  const valor = campo.val();

  if (campo.is("select")) {
    if (valor === "") {
      campo.removeClass("input-correcto").addClass("input-incorrecto");
      textoError.text(mensaje);
    } else {
      campo.removeClass("input-incorrecto").addClass("input-correcto");
      textoError.text("");
    }
  } else {
    if (regex.test(valor)) {
      campo.removeClass("input-incorrecto").addClass("input-correcto");
      textoError.text("");
    } else {
      campo.removeClass("input-correcto").addClass("input-incorrecto");
      textoError.text(mensaje);
    }
  }
}

export function muestraMensaje(icono, tiempo, titulo, mensaje) {
  Swal.fire({
    icon: icono,
    timer: tiempo,
    title: titulo,
    html: mensaje,
    showConfirmButton: false,
  });
}


export function manejarAlerta(idCampo, idAlerta, mostrar) {
  const alerta = document.getElementById(idAlerta);
  if (mostrar) {
    alerta.style.display = 'block';
  } else {
    alerta.style.display = 'none';
  }
}



// Función maestra actualizada con soporte para SELECT
export function ValidacionExpresiones($elemento, tipo, $mensajeError, mensajeErrorTexto) {
    var regexKeypress = /.*/;
    var regexKeyup = /.*/;
    var maxLength = null;
    var esSelect = $elemento.is('select'); 

    switch (tipo) {
        case 'cedula':
            regexKeypress = /^[0-9\b]*$/;
            regexKeyup = /^[1-9][0-9]{7,9}$/;
            maxLength = 9;
            break;
        case 'telefono':
            regexKeypress = /^[0-9-]*$/;
            regexKeyup = /^04(14|24|16|26|12|22)-[0-9]{7}$/;
            maxLength = 12;
            break;
        case 'correo':
            regexKeypress = /^[a-zA-Z0-9._%+-@\b]*$/;
            regexKeyup = /^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,60}$/;
            maxLength = 100;
            break;
        case 'nombre':
            regexKeypress = /^[A-Za-z\b\u00f1\u00d1\u00E0-\u00FC]*$/;
            regexKeyup = /^[a-zA-Z\u00f1\u00d1\u00E0-\u00FC]{3,30}$/;
            maxLength = 30;
            break;
        case 'apellido':
            regexKeypress = /^[A-Za-z\b\u00f1\u00d1\u00E0-\u00FC]*$/;
            regexKeyup = /^[a-zA-Z\u00f1\u00d1\u00E0-\u00FC]{3,30}$/;
            maxLength = 30;
            break;
        case 'clave':
            regexKeypress = /^[a-zA-Z0-9!#*\$.?,=]*$/;
            regexKeyup = /^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[!#*\$.?,=])[A-Za-z\d!#*\$.?,=]{8,16}$/;
            maxLength = 16;
            break;
        case 'nombre123':
            regexKeypress = /^[A-Za-z0-9\b\s]*$/;
            regexKeyup = /^[A-Za-z0-9\s]{3,30}$/;
            maxLength = 30;
            break;    
        case 'select':
            regexKeyup = /^(?!\s*$).+$/; 
            break;
    }

    // Si ES UN SELECT, manejamos el evento "change" en lugar de keypress/keyup de texto
    if (esSelect) {
        $elemento.on("change", function () {
            // Si el valor es diferente de vacío o "0", pasa la validación (se pone verde)
            var valorSeleccionado = $(this).val();
            var esValido = valorSeleccionado !== "" && valorSeleccionado !== "0" && valorSeleccionado !== null;
            
            if (!esValido) {
                $mensajeError.text(mensajeErrorTexto).show();
                $(this).removeClass('is-valid').addClass('is-invalid');
            } else {
                $mensajeError.text('').hide();
                $(this).removeClass('is-invalid').addClass('is-valid');
            }
        });
    } else {
        // Comportamiento normal para los inputs de texto
        $elemento.on("keypress", function (e) {
            validarkeypress(regexKeypress, e);
        });

        $elemento.on("input", function () {
            var valor = $(this).val();
            if (tipo === 'telefono') {
                var inputLimpio = valor.replace(/[^0-9]/g, '');
                if (inputLimpio.length > 4) {
                    inputLimpio = inputLimpio.substring(0, 4) + '-' + inputLimpio.substring(4, 11);
                }
                valor = inputLimpio;
            }
            if (maxLength && valor.length > maxLength) {
                valor = valor.substring(0, maxLength);
            }
            $(this).val(valor);
        });

        $elemento.on("keyup", function () {
            validarCampo($elemento, regexKeyup, $mensajeError, mensajeErrorTexto);
        });
    }
}