import { activarLoaderBoton, desactivarLoaderBoton, muestraMensaje, ValidacionExpresiones } from './LoveFunciones.js';
window.eliminarRol = eliminarRol;
function eliminarRol(id_rol) {
  Swal.fire({
    title: '¿Eliminar Tipo de usuario?',
    text: '¿Desea eliminarlo?',
    icon: 'warning',
    showCancelButton: true,
    confirmButtonColor: '#2b8139',
    cancelButtonColor: '#d33',
    confirmButtonText: 'Sí, eliminar',
    cancelButtonText: 'Cancelar'
  }).then((result) => {
    if (result.isConfirmed) {
      const datos = new FormData();
      datos.append('id_rol', id_rol);
      datos.append('eliminar', 'eliminar');
      enviaAjax(datos); // Aquí sí usas tu flujo normal con muestraMensaje()
    }
  });
}

/* ||| FUNCION PARA VALIDAR ENVIO REGISTRO ||| */
function validarCampos() {
  
    let nombreValido = /^[A-Za-z0-9\s]{3,30}$/.test($("#nombre").val()); 
    let nivelValido = $("#nivel").val() !== "";

    function aplicarEstado(input, valido, feedback, mensaje = "") {
        if (valido) {
            $(input).removeClass("is-invalid").addClass("is-valid");
            $(feedback).hide();
        } else {
            $(input).removeClass("is-valid").addClass("is-invalid");
            $(feedback).text(mensaje).show();
        }
    }
    
    aplicarEstado("#nombre", nombreValido, "#snombre", "Solo letras (3 a 30 caracteres)");
    aplicarEstado("#nivel", nivelValido, "#snivel", "Por favor, seleccione un nivel válido.");
    
    return nombreValido &&  nivelValido ;
}

$(document).ready(function() {
  
  ValidacionExpresiones($("#nivel"), 'select', $("#snivel"), "Por favor, seleccione un nivel válido.");
  ValidacionExpresiones($("#nombre"), 'nombre123', $("#snombre"), "El formato debe ser letras, números y espacios (3 a 30 caracteres)");

  /*||| ENVIO AJAX FORMULARIO |||*/
  $('#registrar').on("click", function () {
      if (validarCampos()) {
          Swal.fire({
              title: '¿Deseas registrar?',
              text: 'Se asignarán permisos predeterminados según el nivel seleccionado.',
              icon: 'question',
              showCancelButton: true,
              confirmButtonText: 'Sí, registrar',
              cancelButtonText: 'Cancelar',
              confirmButtonColor: '#2b8139',
              cancelButtonColor: '#d33'
          }).then((result) => {
              if (result.isConfirmed) {
                  activarLoaderBoton('#registrar');
                  var datos = new FormData($('#ForRegistrar')[0]);
                  datos.append('registrar', 'registrar');
                  enviaAjax(datos);
              }
          });
      }
  });

  $('#btnModificar').on("click", function () {
    
          Swal.fire({
              title: '¿Deseas Actualizar?',
              text: '',
              icon: 'question',
              showCancelButton: true,
              confirmButtonText: 'Sí, actualizar',
              cancelButtonText: 'Cancelar',
              confirmButtonColor: '#2b8139',
              cancelButtonColor: '#d33'
          }).then((result) => {
              if (result.isConfirmed) {
                  activarLoaderBoton('#btnModificar');
                  var datos = new FormData($('#formModificar')[0]);
                  datos.append('actualizar', 'actualizar');
                  enviaAjax(datos);
              }
          });
     
  });

});

$(document).on('click', '.modificar', function () {

    let id     = $(this).data('id');
    let nombre = $(this).data('nombre');
    let nivel  = $(this).data('nivel');
  

    // Asignar valores al formulario del modal
    $('#id_tipo_modificar').val(id);
    $('#nombre_modificar').val(nombre);
    $('#nivel_modificar').val(nivel);
     $('#nivel_modificar_actual').val(nivel);
 

    // Abrir modal (por si no se abre automáticamente)
    $('#modificar').modal('show');
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
  
           if (lee.accion == 'registrar') {
                if (lee.respuesta == 1) {
                  muestraMensaje("success", 1500, "Se ha registrado con éxito", "Agregado Tipo Usuario");
                    desactivarLoaderBoton('#registrar'); 
                      setTimeout(function () {
                        location = '?pagina=tipousuario';
                      }, 2000);
                } else {
                  muestraMensaje("error", 3000, "Error", lee.text);
                  desactivarLoaderBoton('#registrar'); 
                }
            }else if (lee.accion == 'actualizar') {
                if (lee.respuesta == 1) {
                  muestraMensaje("success", 1500, "Se ha Actualizado con éxito", "");
                  desactivarLoaderBoton('#btnModificar'); 
                  setTimeout(function () {
                     location = '?pagina=tipousuario';
                  }, 2000);
                } else {
                  muestraMensaje("error", 3000, "Error", lee.text);
                  desactivarLoaderBoton('#btnModificar'); 
                }
            }else if (lee.accion == 'eliminar') {
                if (lee.respuesta == 1) {
                  muestraMensaje("success", 1500, "Se ha eliminado con éxito", "Tipo de usario Borrado");
                 
                    setTimeout(function () {
                      location = '?pagina=tipousuario';
                    }, 2000);
                } else {
                    muestraMensaje("error", 3000, "Error", lee.text);
                 
                }
            } else if (lee.accion == 'permisos') {
                if (lee.respuesta == 1) {

                } else {
                    muestraMensaje("error", 3000, "Error", lee.text);
                 
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

$(function(){
// ——— AYUDA con Driver.js v1 ———
$('#btnAyuda').on("click", function () {
  // instancia el driver (igual que en Proveedor)
  const driver = window.driver.js.driver;
  const driverObj = new driver({
    nextBtnText:  'Siguiente',
    prevBtnText:  'Anterior',
    doneBtnText:  'Listo',
    popoverClass: 'driverjs-theme',
    closeBtn:     false,
    steps: [
      {
        element: '.table-color',
        popover: {
          title:       'Tabla de Tipos de Usuario',
          description: 'Aquí ves la lista de tipos de usuario registrados.',
          side:        'top'
        }
      },
      {
        element: 'button[data-bs-target="#registro"]',
        popover: {
          title:       'Registrar Tipo de Usuario',
          description: 'Abre el modal para crear un nuevo tipo de usuario.',
          side:        'bottom'
        }
      },
      {
        element: '.permisotur',
        popover: {
          title:       'Modificar Permisos',
          description: 'Haz clic aquí para ver y modificar los permisos del tipo de usuario.',
          side:        'left'
        }
      },
      {
        element: '.modificar',
        popover: {
          title:       'Editar Tipo de Usuario',
          description: 'Modifica los datos de un tipo de usuario existente.',
          side:        'left'
        }
      },
      {
        element: '.eliminar',
        popover: {
          title:       'Eliminar Tipo de Usuario',
          description: 'Elimina un tipo de usuario del sistema.',
          side:        'left'
        }
      },
      {
        popover: {
          title:       '¡Listo!',
          description: 'Finalizaste la guía de ayuda del módulo Tipo de Usuario.'
        }
      }
    ]
  });

  driverObj.drive();
});

});