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
} else  if (isset($_POST['validar'])) {    
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

}else if (isset($_POST['validarcodigo'])) {  
    if (isset($_SESSION['iduser']) && !empty($_SESSION['iduser'])) { /* V1 */
        if (!empty($_POST['codigo'])) { /* V2 */

            $codigo_ingresado = $_POST['codigo'];
            $codigo_guardado = isset($_SESSION['codigo_recuperacion']) ? $_SESSION['codigo_recuperacion'] : null;

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

                        if ($codigo_guardado && $codigo_ingresado == $codigo_guardado) {
                            MensajeJSON(1,'validarcodigo',''); // CORRECTO
                        } else {
                            MensajeJSON(0,'validarcodigo','Código incorrecto.'); // INCORRECTO
                        }

        } else{  /* V2 */ 
            MensajeJSON(0,'validarcodigo','Datos Vacios - ERROR E300');
        }      
    } else{ /* V1 */ 
        MensajeJSON(0, 'validarcodigo', 'Session no encontrada - ERROR E100');   
    }  

} else if (isset($_POST['btnReenviar'])) {

    if (isset($_SESSION['iduser']) && !empty($_SESSION['iduser'])) { /* V1 */

        $correo = $_SESSION['correos'];

        if ($correo) {
            $codigo_recuperacion = rand(100000, 999999);
            $_SESSION['codigo_recuperacion'] = $codigo_recuperacion;

            require_once 'modelo/enviarcorreo.php';
            enviarCodigoRecuperacion($correo, $codigo_recuperacion);

            MensajeJSON(1,'reenviar',''); // CORRECTO
        } else {
            $res = array('respuesta' => 0, 'accion' => 'reenviar', 'text' => 'al obtener el correo');
            MensajeJSON(0,'reenviar','al obtener el correo'); // INCORRECTO
        }

    } else{ /* V1 */ 
        MensajeJSON(0, 'reenviar', 'Session no encontrada - ERROR E100');   
    } 

} else if(isset($_POST['validarclave'])){

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