<?php  
use LoveMakeup\Proyecto\Modelo\Olvidoclave;

     // Iniciar sesión solo si no está ya iniciada
    if (session_status() === PHP_SESSION_NONE) {
         session_start();
    }
    
    if (empty($_SESSION["iduser"])){
       header("location:?pagina=login");
    } /*  Validacion URL  */
    
    if (!empty($_SESSION['iduser'])) {
        require_once 'verificarsession.php';
    } 
    require_once 'assets/ajuste/validaciones.php';
  
  $objolvido = new Olvidoclave();
   
if (isset($_POST['cerrarolvido'])) {    
      session_destroy(); 
      header('Location: ?pagina=login');
      exit;
//--------      
} else  if (isset($_POST['validar'])) {  //----------------------- [ VALIDAR CORREO ]
//--------       
    if (isset($_SESSION['iduser']) && !empty($_SESSION['iduser'])) { /* V1 */
        if (!empty($_POST['correo'])) { 
               
                $correo = strtolower($_POST['correo']);    $correodato = $_SESSION['correos'];
                //validar datos
                validarExpresiones('correo', $correo, "Correo (F) inválida","validar");

                if ($correo === $correodato) {
                    
                    $codigo_recuperacion = rand(100000, 999999);
                    $_SESSION['codigo_recuperacion'] = $codigo_recuperacion;
                    
                    // Enviar correo con el código
                    require_once 'modelo/enviarcorreo.php'; 
                    enviarCodigoRecuperacion($correo, $codigo_recuperacion);

                    $_SESSION['codigo_tiempo'] = time();
                    MensajeJSON(1, 'validar', '');     
                } else {
                    MensajeJSON(0, 'validar', 'El correo no encuentra en su registro.');   
                }
        } else{  /* 2 */ 
            MensajeJSON(0,'validar','Datos Vacios - ERROR E300');
        }      
    } else{ /* V1 */ 
        MensajeJSON(0, 'validar', 'Session no encontrada - ERROR E100');   
    }            
//-----------
}else if (isset($_POST['validarcodigo'])) { //------------------------------- [ VALIDAR CODIGO ]
//-----------      
    if (isset($_SESSION['iduser']) && !empty($_SESSION['iduser'])) { /* V1 */
        if (!empty($_POST['codigo'])) { /* V2 */

            $codigo_ingresado = $_POST['codigo'];
            $codigo_guardado = isset($_SESSION['codigo_recuperacion']) ? $_SESSION['codigo_recuperacion'] : null;

            if (!isset($_SESSION['codigo_intentos'])) {
                $_SESSION['codigo_intentos'] = 0;
            }

            $campos = [
                'Codigo' => $codigo_ingresado
            ];
                 /// Sanitización de Entradas
                foreach ($campos as $nombre => $valor) {  /* V4 */ 
                    if (!validarEntradaSQL($valor)) {
                        MensajeJSON(0, 'validarcodigo', "Entrada inválida detectada en el campo: $nombre");   
                    }
                } 
                    //// Validar Datos  V5
                    validarExpresiones('codigo_ingresado', $codigo_ingresado, "Codigo Ingresado (F) inválida","validarcodigo");

                        // 1. Calcular el tiempo que ha pasado desde que se envió
                        $tiempo_actual = time();
                        $tiempo_transcurrido = isset($_SESSION['codigo_tiempo']) ? ($tiempo_actual - $_SESSION['codigo_tiempo']) : 999;

                        if ($tiempo_transcurrido > 90) {
                        
                            MensajeJSON(0, 'validarcodigo', 'El código ha expirado (límite 1:30 min). Solicita uno nuevo.');
                            
                            // Limpiamos las sesiones viejas por seguridad
                            unset($_SESSION['codigo_guardado']);
                            unset($_SESSION['codigo_tiempo']);
                            unset($_SESSION['codigo_intentos']);

                        } else if ($codigo_guardado && $codigo_ingresado == $codigo_guardado) {
                            MensajeJSON(1,'validarcodigo',''); // CORRECTO

                            unset($_SESSION['codigo_guardado']);
                            unset($_SESSION['codigo_tiempo']);
                            unset($_SESSION['codigo_intentos']);
                        } else {
                            $_SESSION['codigo_intentos']++; // Sumamos un intento fallido

                            if ($_SESSION['codigo_intentos'] === 3) {
                                session_destroy(); 
                                MensajeJSON(2, 'validarcodigo', 'Has superado el límite de 3 intentos. Serás redirigido al login.');
                            } else {

                                $intentos_restantes = 3 - $_SESSION['codigo_intentos'];
                                MensajeJSON(0, 'validarcodigo', "Código incorrecto. Te quedan $intentos_restantes intentos."); 
                            } 
                        }
        } else{  /* V2 */ 
            MensajeJSON(0,'validarcodigo','Datos Vacios - ERROR E300');
        }      
    } else{ /* V1 */ 
        MensajeJSON(0, 'validarcodigo', 'Session no encontrada - ERROR E100');   
    }  
//---------
} else if (isset($_POST['btnReenviar'])) { //------------------------------ [ REENVIAR CODIGO ]
//---------
    if (isset($_SESSION['iduser']) && !empty($_SESSION['iduser'])) { /* V1 */

        $correo = $_SESSION['correos'];

        if ($correo) {

            $tiempo_actual = time();
            $limite_segundos = 90; // 1 minuto y 30 segundos

            if (isset($_SESSION['codigo_tiempo'])) {
                $tiempo_transcurrido = $tiempo_actual - $_SESSION['codigo_tiempo'];
                
                // Si NO ha pasado el minuto y medio, bloqueamos el reenvío
                if ($tiempo_transcurrido < $limite_segundos) {
                    $segundos_restantes = $limite_segundos - $tiempo_transcurrido;

                    MensajeJSON(0, 'reenviar', "Debes esperar $segundos_restantes segundos para solicitar otro código.");
                }
            }

            $codigo_recuperacion = rand(100000, 999999);
            $_SESSION['codigo_recuperacion'] = $codigo_recuperacion;

            require_once 'modelo/enviarcorreo.php';
            enviarCodigoRecuperacion($correo, $codigo_recuperacion);

            $_SESSION['codigo_tiempo'] = time();
            $_SESSION['codigo_intentos'] = 0;

            MensajeJSON(1,'reenviar',''); // CORRECTO
        } else {
            MensajeJSON(0,'reenviar','al obtener el correo'); // INCORRECTO
        }

    } else{ /* V1 */ 
        MensajeJSON(0, 'reenviar', 'Session no encontrada - ERROR E100');   
    } 
//-----------
} else if(isset($_POST['validarclave'])){ //---------------------------------- [ ACTUALIZAR CLAVE ] 
//-----------
    if (isset($_SESSION['iduser']) && !empty($_SESSION['iduser'])) { /* V1 */
        if (!empty($_POST['clavenueva'])) { /* V2 */
            $clavenueva = $_POST['clavenueva'];

            $campos = [
                'Clave' => $clavenueva
            ];
                 /// Sanitización de Entradas
                foreach ($campos as $nombre => $valor) {  /* V3 */ 
                    if (!validarEntradaSQL($valor)) {
                       MensajeJSON(0, 'validarcodigo', "Entrada inválida detectada en el campo: $nombre");   
                    }
                } 
                    //// Validar Datos  V4
                    validarExpresiones('clave', $clavenueva, "Clave Nueva (F) inválida","actualizar");

                    $datosOlvido = [
                        'operacion' => 'actualizar',
                        'datos' => [
                            'cedula' => $_SESSION["cedula"],
                            'clave' => $clavenueva
                        ]
                    ]; 
                    $resultado = $objolvido->procesarOlvido(json_encode($datosOlvido));
                    echo json_encode($resultado);

        } else{  /* V2 */ 
           MensajeJSON(0,'actualizar','Datos Vacios - ERROR E300');
        }      
    } else{ /* V1 */ 
       MensajeJSON(0, 'actualizar', 'Session no encontrada - ERROR E100');   
    }  
    
} else{
    require_once 'vista/seguridad/olvidoclave.php';
}

?>