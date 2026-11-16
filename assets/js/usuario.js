import { activarLoaderBoton, desactivarLoaderBoton, activarLoaderBotonElemento, desactivarLoaderBotonElemento, muestraMensajetost, muestraMensaje, manejarAlerta, ValidacionExpresiones } from './LoveFunciones.js';

let botonActivo;

var deleteButtons = document.querySelectorAll('.eliminar');
deleteButtons.forEach(function (button) {
  button.addEventListener('click', function (e) {
    e.preventDefault();
    const $boton = $(this);
    botonActivo = $boton;

    const nombre = this.getAttribute('data-nombre');
    const apellido = this.getAttribute('data-apellido');

    Swal.fire({
      title: `¿Eliminar a ${nombre} ${apellido}?`,
      text: '¿Desea eliminar a este usuario?',
      icon: 'warning',
      showCancelButton: true,
      confirmButtonColor: '#2b8139',
      cancelButtonColor: '#d33',
      confirmButtonText: 'Sí, eliminar',
      cancelButtonText: 'Cancelar'
    }).then((result) => {
      if (result.isConfirmed) {
        activarLoaderBotonElemento($boton);
        var form = this.closest('form');
        var datos = new FormData(form);
        enviaAjax(datos);
      }
    });
  });
});

// PARA EDITAR DATOS DEL USUARIO
document.addEventListener("DOMContentLoaded", function() {
  var editarModal = document.getElementById("editarModal");
  editarModal.addEventListener("show.bs.modal", function(event) {
    var button = event.relatedTarget; // Botón que activó el modal
    var idPersona = button.getAttribute("data-id");
    var cedula = button.getAttribute("data-cedula");
    var correo = button.getAttribute("data-correo");
    var id_tipo = button.getAttribute("data-id_tipo");
    var nombre_rol = button.getAttribute("data-nombre_rol");
    var estatus = button.getAttribute("data-estatus"); 
    var tipo_doc = button.getAttribute("data-tipo_documento"); 

    // Asignar valores al modal
    document.getElementById("modalIdPersona").value = idPersona;
    document.getElementById("modalCedula").value = cedula;
    document.getElementById("modalce").value = cedula;
    document.getElementById("modalCorreo").value = correo;
    document.getElementById("modalco").value = correo;
    document.getElementById("modalrol").value = id_tipo;
    document.getElementById("modalrol").textContent = nombre_rol;
    document.getElementById("rolactual").value = id_tipo;
    document.getElementById("roldocumento").value = tipo_doc;

    document.getElementById("modalestatus").value = estatus;
    document.getElementById("modalestatus").textContent = estatus == "1" ? "Activo - Actual" : estatus == "2" ? "Inactivo - Actual" : "Desconocido";
    document.getElementById("roldocumento").textContent = 
          tipo_doc == "V" ? "V (Actual)" : 
          tipo_doc == "E" ? "E (Actual)" :  
          "Tipo desconocido";
    
  });
});


$(document).ready(function() {

/* ||| FUNCION PARA VALIDAR ENVIO REGISTRO ||| */
function validarCampos() {
    let cedulaValida = /^[1-9][0-9]{7,9}$/.test($("#cedula").val());
    let telefonoValido = /^04(14|24|16|26|12|22)-[0-9]{7}$/.test($("#telefono").val());
    let correoValido = /^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,60}$/.test($("#correo").val());
    let nombreValido = /^[a-zA-Z]{3,30}$/.test($("#nombre").val());
    let apellidoValido = /^[a-zA-Z]{3,30}$/.test($("#apellido").val());
    let claveValida = /^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[!#*\$.?,=])[A-Za-z\d!#*\$.?,=]{8,16}$/.test($("#clave").val());
    let confirmarClave = $("#confirmar_clave").val();
    let confirmarValida = /^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[!#*\$.?,=])[A-Za-z\d!#*\$.?,=]{8,16}$/.test(confirmarClave) && confirmarClave === $("#clave").val();
    let rolValido = $("#rolSelect").val() !== "";
    let rolValido2 = $("#rolSelect2").val() !== "";

    function aplicarEstado(input, valido, feedback, mensaje = "") {
        if (valido) {
            $(input).removeClass("is-invalid").addClass("is-valid");
            $(feedback).hide();
        } else {
            $(input).removeClass("is-valid").addClass("is-invalid");
            $(feedback).text(mensaje).show();
        }
    }
    aplicarEstado("#cedula", cedulaValida, "#textocedula", "Formato: entre 7 y 8 dígitos.");
    aplicarEstado("#telefono", telefonoValido, "#textotelefono", "Formato: 0000-0000000");
    aplicarEstado("#correo", correoValido, "#textocorreo", "Debe incluir @ y ser válido.");
    aplicarEstado("#nombre", nombreValido, "#textonombre", "Solo letras (3 a 50 caracteres)");
    aplicarEstado("#apellido", apellidoValido, "#textoapellido", "Solo letras (3 a 50 caracteres)");
    aplicarEstado("#clave", claveValida, "#textoclave", "la clave es entre 8 y 16 caracteres");
    aplicarEstado("#confirmar_clave", confirmarValida, "#textoconfirmar", "Las contraseñas no coinciden o no cumplen con el formato");
    aplicarEstado("#rolSelect", rolValido, "#textorol", "Por favor, seleccione un rol válido.");
    aplicarEstado("#rolSelect2", rolValido2, "#textorol2", "Por favor, seleccione nacionalidad.");
    return cedulaValida && telefonoValido && correoValido &&
           nombreValido && apellidoValido && claveValida &&
           confirmarValida && rolValido && rolValido2;
}


/*||| ENVIO AJAX FORMULARIO |||*/
$('#registrar').on("click", function () {
    if (validarCampos()) {
        Swal.fire({
            title: '¿Deseas registrar este usuario?',
            text: 'Se asignarán permisos predeterminados según el nivel seleccionado.',
            icon: 'question',
            showCancelButton: true,
            confirmButtonText: 'Sí, registrar',
            cancelButtonText: 'Cancelar',
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33'
        }).then((result) => {
            if (result.isConfirmed) {
                activarLoaderBoton('#registrar');
                var datos = new FormData($('#u')[0]);
                datos.append('registrar', 'registrar');
                enviaAjax(datos);
            }
        });
    }
});


$('#cedula').on("blur", function () {
  const cedula = $(this).val().trim();
  const formato = /^[1-9][0-9]{7,9}$/;

  if (formato.test(cedula)) {
    activarLoaderBoton('#registrar');
    const datos = new FormData();
    datos.append('cedula', cedula);
    enviaAjax(datos);
  } 
});

$('#correo').on("blur", function () {
  const correo = $(this).val().trim();
  const formato =  /^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,60}$/;

  if (formato.test(correo)) {
    activarLoaderBoton('#registrar');
        const datos = new FormData();
    datos.append('correo', correo);
    enviaAjax(datos);
  } 
});

$('#rolSelect').on("change", function () {
  const rol = parseInt($(this).val(), 10); // Convertir a número entero

  if (rol >= 1) {
    activarLoaderBoton('#registrar');
    const datos = new FormData();
    datos.append('rol', rol);
    enviaAjax(datos);
  }
});

$('#actualizar').on("click", function () {
    Swal.fire({
      title: '¿Desea Cambiar estos datos?',
      text: 'En caso de Cambiar el Rol, los permiso cambian a sus permisos Predeterminado',
      icon: 'question',
      showCancelButton: true,
      color: "#00000",
      confirmButtonColor: '#50c063ff',
      cancelButtonColor: '#42515A',
      confirmButtonText: ' Si, Actualizar',
      cancelButtonText: 'No'
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

 /* VALIDACIONES DE EXPRESIONES REGULARES */
    ValidacionExpresiones($("#rolSelect"), 'select', $("#textorol"), "Por favor, seleccione un rol válido.");
    ValidacionExpresiones($("#rolSelect2"), 'select', $("#textorol"), "Por favor, seleccione un rol válido.");

    ValidacionExpresiones($("#cedula"), 'cedula', $("#textocedula"), "El formato debe ser 1222333");
    ValidacionExpresiones($("#telefono"), 'telefono', $("#textotelefono"), "El formato debe ser 0000-0000000");
    ValidacionExpresiones($("#correo"), 'correo', $("#textocorreo"), "El formato debe incluir @ y ser válido.");
    ValidacionExpresiones($("#nombre"), 'nombre', $("#textonombre"), "El formato debe ser solo letras.");
    ValidacionExpresiones($("#apellido"), 'apellido', $("#textoapellido"), "El formato debe ser solo letras.");
    ValidacionExpresiones($("#clave"), 'clave', $("#textoclave"), "El formato debe ser entre 8 y 16 caracteres.");
    ValidacionExpresiones($("#confirmar_clave"), 'clave', $("#textoconfirmar"), "El formato debe ser entre 8 y 16 caracteres.");

    ValidacionExpresiones($("#modalCedula"), 'cedula', $("#textocedulamodal"), "El formato debe ser 1222333");
    ValidacionExpresiones($("#modalCorreo"), 'correo', $("#textocorreomodal"), "El formato debe incluir @ y ser válido.");

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
  
           if (lee.accion == 'incluir') {
                if (lee.respuesta == 1) {  
                  muestraMensaje("success", 1500, "Se ha registrado con éxito", "Se agrego un usuario nuevo");
                     desactivarLoaderBoton('#registrar');
                  setTimeout(function () {
                    location = '?pagina=usuario';
                  }, 2000);
                } else {
                      muestraMensaje("error", 3000, "Error",lee.text);
                     desactivarLoaderBoton('#registrar');
                }
              } else if (lee.accion == 'actualizar') {
                if (lee.respuesta == 1) {
                  muestraMensaje("success", 2000, "Se ha Modificado con éxito", "datos del usuario actualizados");
                    desactivarLoaderBoton('#actualizar');  
                  setTimeout(function () {
                    location = '?pagina=usuario';
                  }, 2000);
                } else {
                  muestraMensaje("error", 3000, "Error",lee.text);
                  desactivarLoaderBoton('#actualizar'); 
                }
              } else if (lee.accion == 'eliminar') {
                if (lee.respuesta == 1) {
                  muestraMensaje("success", 1500, "Se ha eliminado con éxito", "Usuario Borrado");
                  if (botonActivo) {
                        desactivarLoaderBotonElemento(botonActivo);
                        botonActivo = null; 
                    }
                  setTimeout(function () {
                     location = '?pagina=usuario';
                  }, 2000);
                } else {
                   muestraMensaje("error", 3000, "Error", lee.text);
                    if (botonActivo) {
                        desactivarLoaderBotonElemento(botonActivo);
                        botonActivo = null;
                    }
                }
              } else if (lee.accion == 'actualizar_permisos') {
                if (lee.respuesta == 1) {
                  muestraMensaje("success", 2000, "Se ha modificado los Permisos con éxito", "Los datos se han modificado correctamente ");
                  desactivarLoaderBoton('#actualizar_permisos');
                  setTimeout(function () {
                     location = '?pagina=usuario';
                  }, 1000);
                } else {
                  muestraMensaje("error", 2000, lee.text,"" );
                  desactivarLoaderBoton('#actualizar_permisos');
                }
              } else if (lee.accion == 'verificar') {
                  if (lee.respuesta == 1) {
                  
                    muestraMensaje("error", 2000, lee.text,"" );
                    desactivarLoaderBoton('#registrar');
                    manejarAlerta('cedula', 'alertcedula', true);
                   
                  } else {
                    muestraMensajetost("success",lee.text, "", "1500"); 
                    desactivarLoaderBoton('#registrar');
                     manejarAlerta('cedula', 'alertcedula', false);
                  }
              } else if (lee.accion == 'verificarcorreo') {
                  if (lee.respuesta == 1) {
                  
                    muestraMensaje("error", 2000, lee.text,"" );
                    desactivarLoaderBoton('#registrar');
                    manejarAlerta('correo', 'alertcorreo', true);
                   
                  } else {
                    muestraMensajetost("success",lee.text, "", "1500"); 
                    desactivarLoaderBoton('#registrar');
                     manejarAlerta('correo', 'alertcorreo', false);
                  }
              } else if (lee.accion == 'verifirol') {
                  if (lee.respuesta == 1) {
                  
                      
                      manejarAlerta('rolSelect', 'alertRol', false);
                      desactivarLoaderBoton('#registrar');
                  } else {
                       muestraMensaje("error", 2000, lee.text,"" );
                       desactivarLoaderBoton('#registrar');
                       manejarAlerta('rolSelect', 'alertRol', true);

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
          desactivarLoaderBoton('#registrar');
              desactivarLoaderBoton('#actualizar'); 
              manejarAlerta('rolSelect', 'alertRol', true);
                     manejarAlerta('correo', 'alertcorreo', false);
                     manejarAlerta('cedula', 'alertcedula', false);
                     desactivarLoaderBoton('#actualizar_permisos');
                     desactivarLoaderBotonElemento(botonActivo);
                        botonActivo = null; 

        } else {
          muestraMensaje("error", 2000, "Error", "ERROR: <br/>" + request + status + err);
        }
      },
      complete: function () {
      }
    });
  }

  let driverObj; // Definido globalmente

$('#ayuda').on("click", function () {
  const driver = window.driver.js.driver;

  driverObj = new driver({
    nextBtnText: 'Siguiente',
    prevBtnText: 'Anterior',
    doneBtnText: 'Listo',
    popoverClass: 'driverjs-theme',
    closeBtn: false,
    steps: [
      { element: '.table-color', popover: { title: 'Tabla de usuario', description: 'Aquí es donde se guardarán los registros de usuario', side: "left" }},
      { element: '.registrar', popover: { title: 'Botón de registrar', description: 'Te lleva a un modal para registrar', side: "bottom", align: 'start' }},
      { element: '.informacion', popover: { title: 'Más información del Usuario', description: 'Ver más información del usuario registrado.', side: "left", align: 'start' }},
      
      { element: '.modificar', popover: { title: 'Modificar Usuario', description: 'Editar información del usuario.', side: "left", align: 'start' }},
      { element: '.eliminar', popover: { title: 'Eliminar Usuario', description: 'Eliminar usuario de la lista.', side: "left", align: 'start' }},
      { element: '.dt-search', popover: { title: 'Buscar', description: 'Buscar usuario en la tabla.', side: "right", align: 'start' }},
      { popover: { title: 'Eso es todo', description: 'Fin de la guía. ¡Espero que te haya servido!' }}
    ]
  });

  driverObj.drive();
});

