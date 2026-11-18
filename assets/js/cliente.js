import { activarLoaderBoton, desactivarLoaderBoton, muestraMensajetost, muestraMensaje, ValidacionExpresiones } from './LoveFunciones.js';

document.addEventListener("DOMContentLoaded", function() {
  var editarModal = document.getElementById("editarModal");
  editarModal.addEventListener("show.bs.modal", function(event) {
    var button = event.relatedTarget; 
 
    var cedula = button.getAttribute("data-cedula");
    var correo = button.getAttribute("data-correo");
    var estatus = button.getAttribute("data-estatus"); 
    var tipo_doc = button.getAttribute("data-tipo_documento"); 

    // Asignar valores al modal
    document.getElementById("modalCedula").value = cedula;
    document.getElementById("modalCorreo").value = correo;
    document.getElementById("modalce").value = cedula;
    document.getElementById("modalco").value = correo;
    document.getElementById("modalestatus").value = estatus;
    document.getElementById("modaldocumento").value = tipo_doc;
    
    document.getElementById("modalestatus").textContent = estatus == "1" ? "Activo - Actual" : estatus == "2" ? "Inactivo - Actual" : "Desconocido";
    document.getElementById("modaldocumento").textContent = 
      tipo_doc == "V" ? "V (Actual)" : 
      tipo_doc == "E" ? "E (Actual)" : 
      tipo_doc == "J" ? "J (Actual)" : 
      "Tipo desconocido";
      });
});


$(document).ready(function() {

  $('#actualizar').on("click", function () {
    Swal.fire({
      title: '¿Actualizar cliente?',
      text: 'Confirma los cambios.',
      icon: 'question',
      showCancelButton: true,
      color: "#00000",
      confirmButtonColor: '#2b8139',
      cancelButtonColor: '#d33',
      confirmButtonText: ' Si, Actualizar ',
      cancelButtonText: 'NO'
    }).then((result) => {
     if (result.isConfirmed) {
        // Validación de los campos antes de enviar
            let cedulaValida = /^[1-9][0-9]{7,9}$/.test($("#modalCedula").val());
            let correoValido = /^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,60}$/.test($("#modalCorreo").val());

            if (!cedulaValida) {
                $("#modalCedula").addClass("is-invalid");
                $("#textocedulamodal").show();
            } else {
                $("#modalCedula").removeClass("is-invalid").addClass("is-valid");
                $("#textocedulamodal").hide();
            }

            if (!correoValido) {
                $("#modalCorreo").addClass("is-invalid");
                $("#textocorreomodal").show();
            } else {
                $("#modalCorreo").removeClass("is-invalid").addClass("is-valid");
                $("#textocorreomodal").hide();
            }

            // Si todos los campos son válidos, enviar el formulario
            if (cedulaValida && correoValido) {
            activarLoaderBoton('#actualizar');
            var datos = new FormData($('#formdatosactualizar')[0]);
            datos.append('actualizar', 'actualizar');
            enviaAjax(datos);
          }
      }
    });
 }); 

    ValidacionExpresiones($("#modalCedula"), 'cedula', $("#textocedulamodal"), "El formato debe ser 1222333");
    ValidacionExpresiones($("#modalCorreo"), 'correo', $("#textocorreomodal"), "El formato debe incluir @ y ser válido .");

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
  
           if (lee.accion == 'actualizar') {
                if (lee.respuesta == 1) {
                  muestraMensaje("success", 1500, "Se ha Modificado con éxito", "Datos actualizados");
                    desactivarLoaderBoton('#actualizar'); 
                      setTimeout(function () {
                        location = '?pagina=cliente';
                      }, 2000);
                } else {
                  muestraMensaje("error", 3000, "Error", lee.text);
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
  
  let driverObj; // Definido globalmente
    $('#ayudacliente').on("click", function () {
      const driver = window.driver?.js?.driver || window.driver;

      if (!driver) {
          muestraMensajetost("info","Driver.js no está cargado correctamente.", "", "1000"); 
          return;
      }

      driverObj = driver({
        nextBtnText: 'Siguiente',
        prevBtnText: 'Anterior',
        doneBtnText: 'Listo',
        popoverClass: 'driverjs-theme',
        closeBtn: false,
        steps: [
          { element: '.table-color', popover: { title: 'Tabla de cliente', description: 'Aqui es donde se guardaran los registros de los clientes', side: "left" } },
          { element: '.Ayudatelefono', popover: { title: 'Boton para contactar el cliente "WhatsApp"', description: 'Este botón te lleva directo al chat de WhatsApp con el cliente para que puedas escribirle fácilmente.', side: "left", align: 'start' } },
          { element: '.Ayudacorreo', popover: { title: 'Boton para contactar el cliente "Correo"', description: 'Este botón Abre tu aplicación de correo para que puedas enviarle un email al cliente sin copiar su dirección.', side: "left", align: 'start' } },
          { element: '.modificar', popover: { title: 'Modificar datos del cliente', description: 'Este botón te permite editar la cedula y el correo de un cliente registrado.', side: "left", align: 'start' } },
          { element: '.dt-search', popover: { title: 'Buscar', description: 'Te permite buscar un cliente en la tabla', side: "right", align: 'start' } },
          { popover: { title: 'Eso es todo', description: 'Este es el fin de la guia espero hayas entendido' } }
        ]
      });
      
      // Iniciar el tour usando la variable global
      driverObj.drive();
  });
