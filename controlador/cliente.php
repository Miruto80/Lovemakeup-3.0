<?php  
use LoveMakeup\Proyecto\Modelo\Cliente; 
use LoveMakeup\Proyecto\Modelo\Bitacora;

// Iniciar sesión solo si no está ya iniciada
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
//--
if (!empty($_SESSION['id'])) {
    require_once 'verificarsession.php';
}
//-----
if (!isset($_SESSION['limite_cliente'])) {
    $_SESSION['limite_cliente'] = 100;
}
//--------
if (isset($_POST['ver_mas'])) {
    $_SESSION['limite_cliente'] += 100;
    header("location:?pagina=cliente");
    exit;
}
//---
require_once 'permiso.php';
require_once 'assets/ajuste/validaciones.php';
$objcliente = new Cliente();
//---
$registro = $objcliente->consultar($_SESSION['limite_cliente']);
$total_registros = $objcliente->contarTotal();
$pedidos = $objcliente->consultarPedidos();
//----
    function validarCorreoActual(array $registro, string $correoActual): bool {
        foreach ($registro as $usuario) {
         
            if (strtolower($usuario['correo']) === strtolower($correoActual)) {
               
                if ($usuario['id_usuario'] == 1 || $usuario['id_usuario'] == 2) {
                    return false; 
                }
                return true; // Está registrado y permitido
            }
        }
   
        return false;
    }
    
    function registrarBitacora($accion, $descripcion) {
        //FUNCION PARA REGISTRAR LA BITACORA
        $datos = [
            'id_persona'  => $_SESSION["id"],
            'accion'      => $accion,
            'descripcion' => $descripcion
        ];

        // Instanciamos y registramos
        $bitacoraObj = new Bitacora();
        return $bitacoraObj->registrarOperacion($accion, 'Cliente', $datos);
    }
//-----
if(isset($_POST['actualizar'])){ //--------------------------------- ACTUALIZAR DATOS DEL CLIENTES
//-------
    if (isset($_SESSION['id']) && !empty($_SESSION['id'])) { // Validacion 1
        if ($_SESSION["nivel_rol"] == 3 && tieneAcceso(10, 3)) { // Validacion 2
            if(!empty($_POST['cedula']) && !empty($_POST['correo']) && !empty($_POST['estatus']) && !empty($_POST['cedulaactual']) && !empty($_POST['tipo_documento']) && !empty($_POST['correoactual']) ){
            // Validacion 3

                $Cedula=$_POST['cedula'];           $Correo=strtolower($_POST['correo']);         $Estatus=$_POST['estatus'];
                $CedulaActual=$_POST['cedulaactual'];     $Documento=$_POST['tipo_documento'];      $CorreoActual=$_POST['correoactual'];
        
                $campos = [
                    'Cedula' => $Cedula,
                    'Estatus' => $Estatus,
                    'CedulaActual' => $CedulaActual,
                    'Documento' => $Documento
                ];
                /// Sanitización de Entradas
                    foreach ($campos as $nombre => $valor) { // Validacion 4
                        if (!validarEntradaSQL($valor)) {
                            echo json_encode(['respuesta' => 0, 'accion' => 'actualizar', 'text' => "#0400 - Entrada inválida detectada en el campo: $nombre"]);
                            exit;
                        }
                    }
                
                //// Validar Datos - Validacion 5
                validarExpresiones('cedula', $Cedula, "ROL (F) inválido", "actualizar");
                validarExpresiones('cedula', $CedulaActual, "ROL (F) inválido", "actualizar");
                validarExpresiones('correo', $CorreoActual, "ROL (F) inválido", "actualizar");
                validarExpresiones('correo', $Correo, "ROL (F) inválido", "actualizar");
                validarExpresiones('documento', $Documento, "ROL (F) inválido", "actualizar");
                validarExpresiones('estatus', $Estatus, "ROL (F) inválido", "actualizar");

                    if (!validarTipoDocumento($Documento)) {  
                        MensajeJSON(0, 'actualizar', 'El tipo de documento no es válido - ERROR E520');
                    }
        
                    if (!in_array($Estatus, [1, 2])) {
                        MensajeJSON(0, 'actualizar', 'El estatus no es válido - ERROR E520');
                    }

                        // VALIDACION EXISTENTE
                        $datosCliente = ['operacion' => 'verificar','datos' => ['cedula' => $CedulaActual]  ];
                            $resultadoVerificacion =$objcliente->procesarCliente(json_encode($datosCliente));
                                if ($resultadoVerificacion['respuesta'] == 0) {
                                    MensajeJSON(0, 'actualizar', 'Cedula no existente - ERROR E530');
                                }  // FIN
                        
                            if (validarCorreoActual($registro, $CorreoActual)) { // Validar si la Correo actual si existe en la BD
                              
                                // Envio al Modulo
                                $datosCliente = [
                                    'operacion' => 'actualizar',
                                    'datos' => [
                                        'cedula' => $Cedula,
                                        'correo' => $Correo,
                                        'estatus' => $Estatus,
                                        'cedula_actual' => $CedulaActual,
                                        'tipo_documento' => $Documento,
                                        'correo_actual' => $CorreoActual
                                    ]
                                ];  
                    
                                $resultado = $objcliente->procesarCliente(json_encode($datosCliente)); // Resultado 
                                    if ($resultado['respuesta'] == 1) {   // Bitacora
                                        RegistrarBitacora('Actualizacion de cliente', "Se actualizaron los datos el cliente CI:{$CedulaActual}, cedula nueva: {$Documento}-{$Cedula}, Correo actual:
                                                         {$CorreoActual} Correo Nuevo: {$Correo}, Estatus: {$Estatus}");
                                    }
                                echo json_encode($resultado); /// RESULTADO DE LA MODIFICACION
                                exit;  // Fin del envio modulo
                               
                            } else { /// si la Correo actual no existia o esta protegida
                                MensajeJSON(0, 'actualizar', 'Correo no encontrada O protegida - ERROR E530');
                            }

            } else{  /* 3 */ 
                MensajeJSON(0, 'actualizar', 'Datos Vacios - ERROR E300');
            }   
        } else{  /* 2 */ 
            MensajeJSON(0, 'actualizar', 'No Tiene Permiso para realizar esta operacion - ERROR E200');
        }  
    } else{ /* 1 */ 
        MensajeJSON(0, 'actualizar', 'Session no encontrada - ERROR E100');
    }
//-----------
} else if ($_SESSION["nivel_rol"] >= 2 && tieneAcceso(10, 1)) { //------------ VISTA
//-----------    
        RegistrarBitacora('Acceso a Módulo Cliente', "Entro al módulo de Cliente");
        $pagina_actual = isset($_GET['pagina']) ? $_GET['pagina'] : 'cliente';
        require_once 'vista/cliente.php';
//---------        
} else {
        require_once 'vista/seguridad/privilegio.php';

}
 
?>        