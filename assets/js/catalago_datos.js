import { activarLoaderBoton, desactivarLoaderBoton, muestraMensajetost, validarCampo, muestraMensaje, ValidacionExpresiones } from './LoveFunciones.js';

function validarFormulario() {
    let validar = true;
    // Validar campos con la lógica original
    validarCampo($("#clave"), /^.{8,16}$/, $("#textoclave"), "El formato debe ser entre 8 y 16 caracteres");
    validarCampo($("#clavenueva"), /^.{8,16}$/, $("#textoclavenueva"), "El formato debe ser entre 8 y 16 caracteres");
    validarCampo($("#clavenuevac"), /^.{8,16}$/, $("#textoclavenuevac"), "El formato debe ser entre 8 y 16 caracteres");
    // Validar coincidencia entre las contraseñas
    const clavenueva = $("#clavenueva").val();
    const clavenuevac = $("#clavenuevac").val();

    if (clavenueva !== clavenuevac) {
        $("#clavenueva, #clavenuevac").removeClass("is-valid").addClass("is-invalid");
        $("#textoclavenuevac").text("Las contraseñas no coinciden").css("color", "red");
        validar = false;
    }
    // Verificar que todos los campos requeridos estén válidos visualmente
    const clavesValidas = $("#clave").hasClass("is-valid") &&
                          $("#clavenueva").hasClass("is-valid") &&
                          $("#clavenuevac").hasClass("is-valid");
    if (!clavesValidas) {
        validar = false;
    }
    if (!validar) {
        muestraMensajetost("error", "Validaciones incorrectas", "Corrige los errores antes de enviar.", "2500");
    }
    return validar;
}


$(document).ready(function() {

  $('#actualizar').on("click", function () {
    Swal.fire({
      title: '¿Desea guardar los cambios?',
      text: '',
      icon: 'question', 
      showCancelButton: true,
      color: "#00000",
      confirmButtonColor: '#2b8139',
      cancelButtonColor: '#d33',
      confirmButtonText: ' Si, actualizar ',
      cancelButtonText: 'NO'
    }).then((result) => {
      if (result.isConfirmed) {
         activarLoaderBoton('#actualizar');
        var datos = new FormData($('#u')[0]);
        datos.append('actualizar', 'actualizar');
        enviaAjax(datos);
      }
    });
  });

  $('#actualizarclave').on("click", function () {
      if (validarFormulario()) {
          Swal.fire({
              title: '¿Desea Cambiar la clave?',
              text: '',
              icon: 'question',
              showCancelButton: true,
              color: "#00000",
              confirmButtonColor: '#2b8139',
              cancelButtonColor: '#d33',
              confirmButtonText: ' Si, cambiar',
              cancelButtonText: 'NO'
          }).then((result) => {
              if (result.isConfirmed) {
                  activarLoaderBoton('#actualizarclave');
                  var datos = new FormData($('#formclave')[0]);
                  datos.append('actualizarclave', 'actualizarclave');
                  enviaAjax(datos);
              }
          });
      }
  });

  ValidacionExpresiones($("#cedula"), 'cedula', $("#textocedula"), "El formato debe ser 1222333");
  ValidacionExpresiones($("#telefono"), 'telefono', $("#textotelefono"), "El formato debe ser 0000-0000000");
  ValidacionExpresiones($("#correo"), 'correo', $("#textocorreo"), "El formato debe incluir @ y ser válido.");
  ValidacionExpresiones($("#nombre"), 'nombre', $("#textonombre"), "El formato debe ser solo letras");
  ValidacionExpresiones($("#apellido"), 'apellido', $("#textoapellido"), "El formato debe ser solo letras");
  ValidacionExpresiones($("#clave"), 'clave', $("#textoclave"), "El formato debe ser entre 8 y 16 caracteres");

  ValidacionExpresiones($("#cedula"), 'cedula', $("#textocedula"), "El formato debe ser 1222333");
  ValidacionExpresiones($("#clavenueva"), 'clavenueva', $("#textoclavenueva"), "El formato debe ser entre 8 y 16 caracteres");
  ValidacionExpresiones($("#clavenuevac"), 'clavenuevac', $("#textoclavenuevac"), "El formato debe ser entre 8 y 16 caracteres");
        

});

function enviaAjax(datos) {
    $.ajax({
      async: true,
      url: "",
      type: "POST",
      contentType: false,
      data: datos,
      processData: false,
      cache: false,
      beforeSend: function () { },
      timeout: 10000,
      success: function (respuesta) {
        console.log(respuesta);
            try {
        // Eliminar contenido HTML en caso de que aparezca
        var respuestaLimpiada = respuesta.split("<!DOCTYPE html>")[0].trim();
        var lee = JSON.parse(respuestaLimpiada);
        console.log("JSON parseado correctamente:", lee);
        } catch (error) {
            console.error("Error al parsear JSON:", error.message);
        }

        try {
  
           if (lee.accion == 'actualizar') {
                if (lee.respuesta == 1) {  
                  muestraMensaje("success", 2000, "Se ha Actualizado con éxito", "Sus datos se en modificado con exitosamente");
                  desactivarLoaderBoton('#actualizar');
                  setTimeout(() => {
                 location = '?pagina=catalogo_datos';
                  }, 2000); 
              } else {
                  muestraMensaje("error", 2000, lee.text, "");
                  desactivarLoaderBoton('#actualizar');
                }
              } else if (lee.accion == 'eliminar') {
                if (lee.respuesta == 1) {
                  muestraMensaje("success", 2000, "Se ha eliminado con éxito", "Los datos se han borrado correctamente ");
                      desactivarLoaderBoton('#btnEliminar');
                  setTimeout(function () {
                     location = '?pagina=catalogo';
                  }, 1000);
                } else {
                  muestraMensaje("error", 2000, "ERROR", lee.text);
                  desactivarLoaderBoton('#btnEliminar');
                }
              } else if (lee.accion == 'clave') {
                if (lee.respuesta == 1) {
                  muestraMensaje("success", 2000, "Se ha cambio con éxito", "correctamente ");
                      desactivarLoaderBoton('#actualizarclave');
                  setTimeout(function () {
                     location = '?pagina=catalogo_datos';
                  }, 1000);
                } else {
                  muestraMensaje("error", 2000, lee.text, "Revise y vuelva a intentar" );
                      desactivarLoaderBoton('#actualizarclave');
                }
              } else if (lee.accion == 'incluir') {
                if (lee.respuesta == 1) {
                  muestraMensaje("success", 2000, "Se ha registro con éxito", "Correctamente");
                      desactivarLoaderBoton('#agregardireccion');
                  setTimeout(function () {
                     location = '?pagina=catalogo_datos';
                  }, 2000);
                } else {
                  muestraMensaje("error", 2000, lee.text, "Revise y vuelva a intentar" );
                      desactivarLoaderBoton('#agregardireccion');
                }
              } else if (lee.accion == 'actualizardireccion') {
                if (lee.respuesta == 1) {
                  muestraMensaje("success", 2000, "Se ha cambio con éxito", "Direccion editada correctamente");
                      desactivarLoaderBoton('#direccion');
                  setTimeout(function () {
                     location = '?pagina=catalogo_datos';
                  }, 2000);
                } else {
                  muestraMensaje("error", 2000, lee.text, "Revise y vuelva a intentar" );
                      desactivarLoaderBoton('#direccion');
                }
              }
            
        } catch (e) {
          alert("Error en JSON " + e.name);
        }
      },
   error: function (request, status, err) {
        Swal.close();
        if (status == "timeout") {
          muestraMensaje("error", 2000, "Error", "Servidor ocupado, intente de nuevo");
        } else {
          muestraMensaje("error", 2000, "Error", "ERROR: <br/>" + request + status + err);
        }
      },
      complete: function () {
      }
    });
  }