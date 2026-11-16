
import { activarLoaderBoton, desactivarLoaderBoton, validarkeypress, muestraMensajetost, validarCampo, validarCampoS, muestraMensaje, manejarAlerta, ValidacionExpresiones } from './LoveFunciones.js';

// ||||||||||||||| OJITO ||||||||||||||||||||
const passwordInput = document.getElementById('pid');
const showPasswordButton = document.getElementById('show-password');

showPasswordButton.addEventListener('click', () => {
  if (passwordInput.type === 'password') {
    passwordInput.type = 'text';
    showPasswordButton.classList.remove('fa-eye');
    showPasswordButton.classList.add('fa-eye-slash');
  } else {
    passwordInput.type = 'password';
    showPasswordButton.classList.remove('fa-eye-slash');
    showPasswordButton.classList.add('fa-eye'); 
  }
});


function validarFormulario() {
    validarCampo($("#cedula"), /^[1-9][0-9]{7,9}$/, $("#textocedula"), "Formato incorrecto o vacio.");
    validarCampo($("#telefono"), /^04(14|24|16|26|12|22)-[0-9]{7}$/, $("#textotelefono"), "Formato incorrecto o vacio.");
    validarCampo($("#nombre"), /^[a-zA-Z]{3,50}$/, $("#textonombre"), "Formato incorrecto o vacio.");
    validarCampo($("#apellido"), /^[a-zA-Z]{3,50}$/, $("#textoapellido"), "Formato incorrecto o vacio.");
    validarCampo($("#correo"), /^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,60}$/, $("#textocorreo"), "Formato incorrecto o vacio.");
    validarCampo($("#recontrasena"), /^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[!#*\$.?,=])[A-Za-z\d!#*\$.?,=]{8,16}$/, $("#textorecontrasena"), "Debe tener entre 8 y 16 caracteres.");
    validarCampo($("#clave"), /^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[!#*\$.?,=])[A-Za-z\d!#*\$.?,=]{8,16}$/, $("#textoclave"), "Debe tener entre 8 y 16 caracteres.");

    const clave = $("#clave").val();
    const recontrasena = $("#recontrasena").val();
    if (clave !== recontrasena) {
        $("#textoclave").text("Las contraseñas no coinciden.");
        $("#clave, #recontrasena").removeClass("is-valid").addClass("is-invalid");
    }
    return $(".is-invalid").length === 0;
}

//|||||||||||||| VALIDAR ENVIO cedula ||||||||||||||||||||||
function validarFor() {
    validarCampo($("#cedulac"), /^[1-9][0-9]{7,9}$/, $("#textocedulac"), "Formato incorrecto o vacio.");
    return $(".is-invalid").length === 0;
}


//|||||||||||||| VALIDAR ENVIO login ||||||||||||||||||||||
function validarForlogin() {
    let valido = true;
  
    if (!/^[1-9][0-9]{7,9}$/.test($("#usuario").val())) {
        $("#usuario").removeClass("input-correcto").addClass("input-incorrecto");
        $("#textousuario").text("Formato incorrecto.");
        valido = false;
    } else {
        $("#usuario").removeClass("input-incorrecto").addClass("input-correcto");
        $("#textousuario").text("");
    }

    if (!/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[!#*\$.?,=])[A-Za-z\d!#*\$.?,=]{8,16}$/.test($("#pid").val())) {
        $("#pid").removeClass("input-correcto").addClass("input-incorrecto");
        $("#textop").text("Debe tener entre 8 y 16 caracteres.");
        valido = false;
    } else {
        $("#pid").removeClass("input-incorrecto").addClass("input-correcto");
        $("#textop").text("");
    }

    return valido;
}



$(document).ready(function() {
    //|||||| ENVIO OLVIDO CLAVE FORM
    $('#validarolvido').on("click", function(event) {
        event.preventDefault(); 
        if (validarFor()) {
            activarLoaderBoton('#validarolvido',"Validando...");

            var datos = new FormData($('#olvidoclave')[0]);
            datos.append('validarclave', 'validarclave');
            enviaAjax(datos);
        } else {
            muestraMensajetost("error","Error", "Debe Colocar el nro de cedula.", "2000");
        }
    });

    //|||||| ENVIO lOGIN FORM
    $('#ingresar').on("click", function(event) {
        event.preventDefault(); 

        if (validarForlogin()) {
       
          activarLoaderBoton('#ingresar',"Iniciando...");  
          
          var datos = new FormData($('#login')[0]);
          datos.append('ingresar', 'ingresar');
          enviaAjax(datos);

        } else {
          muestraMensajetost("error","Formato incorrecto o Vacio", "Debe colocar el formato correcto.", "2000");
        }
    });

    //|||||| ENVIO REGISTRO CLIENTE FORM
    $('#registrar').on("click", function(event) {
        event.preventDefault();

        if (validarFormulario()) {
            activarLoaderBoton('#registrar',"Guardando...");  
            
            var datos = new FormData($('#registrocliente')[0]);
            datos.append('registrar', 'registrar');
            enviaAjax(datos);
        } else {
           muestraMensajetost("error","Validaciones incorrectas", "Corrige los errores antes de enviar el formulario.", "3000");
        }
    });

    // --------------------------------- EXPRESIONES REGULARES 
    $("#usuario").on("keypress",function(e){
      validarkeypress(/^[0-9\b]*$/,e);
    });

    $("#usuario").on("input", function () {
      var input = $(this).val().replace(/[^0-9]/g, ''); 
      if (input.length > 8) {
        input = input.substring(0, 9);
      }
      $(this).val(input);
    });

    $("#usuario").on("keyup", function () {
      validarCampoS($(this),/^[1-9][0-9]{7,9}$/,
      $("#textousuario"),"El formato debe ser 1222333");
    });
  
    $("#pid").on("keyup", function() {
      validarCampoS($(this), /^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[!#*\$.?,=])[A-Za-z\d!#*\$.?,=]{8,16}$/, 
      $("#textop"), "El formato debe ser entre 8 y 16 caracteres");
    });

    ValidacionExpresiones($("#cedulac"), 'cedula', $("#textocedulac"), "El formato debe ser de 7 u 8 dígitos");

    ValidacionExpresiones($("#cedula"), 'cedula', $("#textocedula"), "El formato debe ser de 7 u 8 dígitos");
    ValidacionExpresiones($("#telefono"), 'telefono', $("#textotelefono"), "El formato debe ser un prefijo valido (ej: 0414-1234567)");
    ValidacionExpresiones($("#correo"), 'correo', $("#textocorreo"), "El formato debe ser un correo electrónico válido");
    ValidacionExpresiones($("#nombre"), 'nombre', $("#textonombre"), "El formato debe ser de 3 a 30 caracteres");
    ValidacionExpresiones($("#apellido"), 'apellido', $("#textoapellido"), "El formato debe ser de 3 a 30 caracteres");
    ValidacionExpresiones($("#clave"), 'clave', $("#textoclave"), "El formato debe ser de 8 a 16 caracteres");
    ValidacionExpresiones($("#recontrasena"), 'clave', $("#textorecontrasena"), "El formato debe ser de 8 a 16 caracteres");
  
  
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
    
});

document.getElementById('fecha').value = moment().format('YYYY-MM-DD');

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
        var respuestaLimpiada = respuesta.split("<!DOCTYPE html>")[0].trim();
        var lee = JSON.parse(respuestaLimpiada);
        console.log("JSON parseado correctamente:", lee);
        } catch (error) {
        console.error("Error al parsear JSON:", error.message); 
        }
        try {

           if (lee.accion == 'incluir') {  // Registro Cliente
                if (lee.respuesta == 1) {  
                  muestraMensaje("success", 2000, "Se ha registrado con éxito", "Ya puede Iniciar Session con Exitosamente");
                  desactivarLoaderBoton('#registrar');
                  setTimeout(() => {
                     location = '?pagina=login';
                  }, 2000); 
              } else {
                  muestraMensaje("error", 2500, lee.text, "revise o cambialo y lo vuelve a intentar");
                   desactivarLoaderBoton('#registrar');
                   
                }
              
              } else if (lee.accion == 'ingresar') { // Login
                if (lee.respuesta == 1) {
                  muestraMensajetost("success","Inicio de Session", "Exitosamente", "1000"); 
                  desactivarLoaderBoton('#ingresar');
                  
                    setTimeout(function () {
                     location = '?pagina=catalogo';
                  }, 1000);
              
                } else if (lee.respuesta == 2) {
                   muestraMensajetost("success","Inicio de Session - Personal", "Exitosamente", "1000");
                   desactivarLoaderBoton('#ingresar');
                   setTimeout(function () {
                      location = '?pagina=home';
                    }, 1000);
                
                }else if (lee.respuesta == 3) {
                   muestraMensajetost("success","Inicio de Session - Desarrollador", "Exitosamente", "1000");
                   desactivarLoaderBoton('#ingresar');
                   setTimeout(function () {
                      location = '?pagina=root_home';
                    }, 1000);
                
                }else{
                  muestraMensaje("error", 2000, lee.text);
                   desactivarLoaderBoton('#ingresar');
                }
              
              } else if (lee.accion == 'validarclave') { // olvido de clave
                if (lee.respuesta == 1) {
                     muestraMensaje("success", 1000, "Verificado con Exito", "");
                     desactivarLoaderBoton('#validarolvido');
                  setTimeout(function () {
                     location = '?pagina=olvidoclave';
                  }, 1000);
                } else {
                  muestraMensaje("error", 2000, "ERROR", lee.text);
                  desactivarLoaderBoton('#validarolvido');
                }
              
             }else if (lee.accion == 'verificar') {
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
              }

        } catch (e) {
            muestraMensaje("error", 2000, "Error", "ERROR: <br/>" + e.name);
                desactivarLoaderBoton('#registrar');
                  desactivarLoaderBoton('#validarolvido');
                    desactivarLoaderBoton('#ingresar');
        }
      },
      error: function (request, status, err) {
        Swal.close();
        if (status == "timeout") {
          muestraMensaje("error", 2000, "Error", "Servidor ocupado, intente de nuevo");
                desactivarLoaderBoton('#registrar');
                    desactivarLoaderBoton('#validarolvido');
                      desactivarLoaderBoton('#ingresar');

        } else {
          muestraMensaje("error", 2000, "Error", "ERROR: <br/>" + request + status + err);
              desactivarLoaderBoton('#registrar');
                  desactivarLoaderBoton('#validarolvido');
                    desactivarLoaderBoton('#ingresar');
        }
      },
      complete: function () {
      }
    });
  }
  


