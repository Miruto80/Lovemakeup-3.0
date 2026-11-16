import { activarLoaderBoton, desactivarLoaderBoton, muestraMensajetost, validarCampo, muestraMensaje, ValidacionExpresiones } from './LoveFunciones.js';

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
          var datos = new FormData($('#datos')[0]);
          activarLoaderBoton('#actualizar');
          datos.append('actualizar', 'actualizar');
          enviaAjax(datos);
        }
      });
  });
 
  ValidacionExpresiones($("#cedula"), 'cedula', $("#textocedula"), "El formato debe ser numeros 12345678)");
  ValidacionExpresiones($("#telefono"), 'telefono', $("#textotelefono"), "El formato debe ser 0422-0001020");
  ValidacionExpresiones($("#correo"), 'correo', $("#textocorreo"), "El formato debe incluir @ y ser válido.");
  ValidacionExpresiones($("#nombre"), 'nombre', $("#textonombre"), "El formato debe ser letras (3 a 30 caracteres)");
  ValidacionExpresiones($("#apellido"), 'apellido', $("#textoapellido"), "El formato debe ser letras (3 a 30 caracteres)");

  ValidacionExpresiones($("#clave"), 'clave', $("#textoclave"), "El formato debe ser entre 8 y 16 caracteres");
  ValidacionExpresiones($("#clavenueva"), 'clavenueva', $("#textoclavenueva"), "El formato debe ser entre 8 y 16 caracteres");
  ValidacionExpresiones($("#clavenuevac"), 'clavenuevac', $("#textoclavenuevac"), "El formato debe ser entre 8 y 16 caracteres");

  function validarFormulario() {
    let validar = true;

      validarCampo($("#clave"), /^.{8,16}$/, $("#textoclave"), "El formato debe ser entre 8 y 16 caracteres");
      if (!/^.{8,16}$/.test($("#clave").val())) validar = false;

      validarCampo($("#clavenueva"), /^.{8,16}$/, $("#textoclavenueva"), "El formato debe ser entre 8 y 16 caracteres");
      if (!/^.{8,16}$/.test($("#clavenueva").val())) validar = false;

      validarCampo($("#clavenuevac"), /^.{8,16}$/, $("#textoclavenuevac"), "El formato debe ser entre 8 y 16 caracteres");
      if (!/^.{8,16}$/.test($("#clavenuevac").val())) validar = false;

      // Comparación entre nueva contraseña y confirmación
      if ($("#clavenueva").val() !== $("#clavenuevac").val()) {
          $("#clavenuevac").removeClass("is-valid").addClass("is-invalid");
          $("#clavenueva").removeClass("is-valid").addClass("is-invalid");
          $("#textoclavenuevac").text("Las contraseñas no coinciden");
          validar = false;
      }
      if (!validar) {
          muestraMensajetost('error', 'Validaciones incorrectas', 'Corrige los errores antes de enviar.', '3000');
      }
      return validar;
  }

  // Modificar el evento del botón para validar antes de enviar
  $('#actualizarclave').on("click", function () {
      if (validarFormulario()) {
          Swal.fire({
              title: '¿Desea Cambiar la Clave?',
              text: '',
              icon: 'question',
              showCancelButton: true,
              color: "#00000",
              confirmButtonColor: '#2b8139',
              cancelButtonColor: '#d33',
              confirmButtonText: ' Si, Cambiar ',
              cancelButtonText: 'NO'
          }).then((result) => {
              if (result.isConfirmed) {
                  var datos = new FormData($('#formclave')[0]);
                  activarLoaderBoton('#actualizarclave');
                  datos.append('actualizarclave', 'actualizarclave');
                  enviaAjax(datos);
              }
          });
      }
  });

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
        var lee = JSON.parse(respuesta);
        try {
  
           if (lee.accion == 'clave') {
                if (lee.respuesta == 1) {  
                  muestraMensaje("success", 1500, "Se ha Cambiado su clave con exito", "");
                  desactivarLoaderBoton('#actualizarclave');
                  setTimeout(function () {
                    location = '?pagina=datos';
                  }, 1500);
                } else {
                  muestraMensaje("error", 2000, lee.text, "");
                  desactivarLoaderBoton('#actualizarclave');
                }
              } else if (lee.accion == 'actualizar') {
                if (lee.respuesta == 1) {
                  muestraMensaje("success", 1500, "Se ha Modificado con éxito", "Su registro se ha Actualizado exitosamente");
                  desactivarLoaderBoton('#actualizar');
                  setTimeout(function () {
                    location = '?pagina=datos';
                  }, 1500);
                } else {  
                  muestraMensaje("error", 2000, lee.text, "");
                 desactivarLoaderBoton('#actualizar');
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
  
  $('#datosayuda').on("click", function () {
    const driver = window.driver.js.driver;
    const driverObj = new driver({
      nextBtnText: 'Siguiente',
          prevBtnText: 'Anterior',
          doneBtnText: 'Listo',
      popoverClass: 'driverjs-theme',
      closeBtn:false,
      steps: [
        { element: '.informacion', popover: { title: 'Modificar Datos', description: 'En esta seccion puede modificar sus datos personales', side: "left", }},
        { element: '.seguridad', popover: { title: 'Cambiar Clave', description: 'En esta seccion puede Cambiar la clave de acceso.', side: "bottom", align: 'start' }},
        { element: '.regresar', popover: { title: 'Boton Regresar', description: 'Este boton se puede regresar al inicio.', side: "left", align: 'start' }},
      
        { popover: { title: 'Eso es todo', description: 'Este es el fin de la guia espero hayas entendido'} }
      ]
    });
    // Iniciar el tour
    driverObj.drive();
  });