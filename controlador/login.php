<?php

use LoveMakeup\Proyecto\Modelo\Login;
use LoveMakeup\Proyecto\Modelo\Bitacora;

// Iniciar sesión solo si no está ya iniciada
if (session_status() === PHP_SESSION_NONE) {
session_start();
}

require_once 'assets/ajuste/validaciones.php';

$objlogin = new Login();

function registrarBitacora($accion, $descripcion) {
    //FUNCION PARA REGISTRAR LA BITACORA
    $datos = [
        'id_persona'  => $_SESSION["id"],
        'accion'      => $accion,
        'descripcion' => $descripcion
    ];

    // Instanciamos y registramos
    $bitacoraObj = new Bitacora();
    return $bitacoraObj->registrarOperacion($accion, 'Login', $datos);
}

if (isset($_POST['ingresar'])) { /*|||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||  INGRESAR AL SISTEMA */
    if (empty($_SESSION['id'])) { /* V1 */
        if ( !empty($_POST['fecha']) && !empty($_POST['usuario']) && !empty($_POST['clave'])&& !empty($_POST['tipo_documento'])) {

        $fecha = $_POST['fecha'];  $dolar = $_POST['tasa'];  
        $usuario = $_POST['usuario']; $clave = $_POST['clave'];  $documento = $_POST['tipo_documento'];

        $ipCliente = obtenerIP();
                                
        $datosLogin = [
            'operacion' => 'hastabloqueado',
            'datos' => [
                'ip' => $ipCliente,
                'cedula' => $usuario
            ]
        ];

        $bloqueado = $objlogin->procesarLogin(json_encode($datosLogin));
        
        if (isset($bloqueado['respuesta']) && $bloqueado['respuesta'] == 0) {
            echo json_encode($bloqueado);
            exit;
        }

        $campos = [
            'Fecha' => $fecha,
            'Dolar' => $dolar,
            'Usuario' => $usuario,
            'Clave' => $clave,
            'Documento' => $documento
        ];
        /// Sanitización de Entradas
            foreach ($campos as $nombre => $valor) {  /* V2 */ 
                if (!validarEntradaSQL($valor)) {
                     MensajeJSON(0, 'ingresar', "Entrada inválida detectada en el campo: $nombre");
                }
            }
                //// Validar Datos  V3
                validarExpresiones('cedula', $usuario, "Cédula (F) inválida","ingresar");
                validarExpresiones('documento', $documento, "Documento (F) inválido","ingresar");
                validarExpresiones('clave', $clave, "Clave (F) inválida","ingresar");
                validarExpresiones('cedula', $usuario, "Cédula (F) inválida","ingresar");

                $hoy = date('Y-m-d');
                if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $fecha) || $fecha < $hoy) {
                    MensajeJSON(0, 'ingresar', "Fecha inválida o menor a hoy ($fecha) - ERROR E520");  
                }

                if(!$dolar === 0){
                    if (validarExpresiones('dolar', $dolar, "Dolar (F) inválida","ingresar"));
                }
               
                // Validar tipo_documento
                if (!validarTipoDocumento($documento)) {
                    MensajeJSON(0, 'ingresar', 'El tipo de documento no es válido - ERROR E520');  
                }
 
                $datosLogin = [
                    'operacion' => 'verificar',
                        'datos' => [
                            'tipo_documento' => $documento,
                            'cedula' => $usuario,
                            'ip' => $ipCliente,
                            'clave' => $clave
                        ]
                ];
    
                $resultado = $objlogin->procesarLogin(json_encode($datosLogin));

                if ($resultado && isset($resultado->cedula)) { // VERIFICADOR QUE NO ESTE ACTIVO
                    if ((int)$resultado->estatus === 2) {
                        MensajeJSON(0, 'ingresar', 'Lo sentimos, su cuenta está suspendida. Por favor, póngase en contacto con el administrador');  
                    }

                    if ((int)$resultado->estatus === 1) { // VERIFICADOR QUE SI ESTE ACTIVO
        
                        $_SESSION["id"] = $resultado->cedula;
                        $_SESSION["rol"] = $resultado->id_rol;

                        // VERIFICAMOS LOS PERMISOS
                        $id_persona = $_SESSION["rol"]; 
                        $resultadopermiso = $objlogin->consultar($id_persona);
                        if (!$resultadopermiso || empty($resultadopermiso)) {
                            session_destroy();
                            MensajeJSON(0, 'ingresar', 'No tienes un Cargo asignados. Por favor, contacta al administrador..');  
                        } else {
                            $_SESSION["permisos"] = $resultadopermiso;
                        }

                        $_SESSION['id_usuario']= $resultado->id_usuario;
                        $_SESSION['documento']= $resultado->tipo_documento;
                        $_SESSION["nombre"] = $resultado->nombre;
                        $_SESSION["apellido"] = $resultado->apellido;
                        $_SESSION["nivel_rol"] = $resultado->nivel;
                        $_SESSION['nombre_usuario'] = $resultado->nombre_rol;
                        $_SESSION["telefono"] = $resultado->telefono;
                        $_SESSION["correo"] = $resultado->correo;

                            if($dolar >= 1){ // la tasa del dolar se actualiza
                                $datosLogin = [
                                    'operacion' => 'dolar',
                                    'datos' => [
                                        'fecha' => $fecha,
                                        'tasa' => $dolar,
                                        'fuente' => 'Automatico'
                                    ]
                                ];
                                $resultado = $objlogin->procesarLogin(json_encode($datosLogin));
                            } 
                        
                            $resultadoT = $objlogin->consultaTasaUltima();
                            $_SESSION["tasa"] = $resultadoT;
                        
                            if ($_SESSION["nivel_rol"] == 1) {
                                MensajeJSON(1, 'ingresar', '');  
                                
                            } else if ($_SESSION["nivel_rol"] == 2 || $_SESSION["nivel_rol"] == 3) {
                                RegistrarBitacora('Acceso al sistema', "Entro al panel administrativo el usuario: {$_SESSION['documento']} - {$_SESSION["id"]}, {$_SESSION['nombre']} {$_SESSION["apellido"]}");
                                MensajeJSON(2, 'ingresar', '');  
                            } else {
                                MensajeJSON(0, 'ingresar', 'Su nivel de acceso no está definido.');  
                            }
                    }

                } else {
                    MensajeJSON(0, 'ingresar', 'Cédula y/o Clave inválida.'); 
                }
        } else{
             MensajeJSON(0, 'ingresar', 'Datos Vacios - E300');  
        }
    } else{ /* V1 */ 
        MensajeJSON(0, 'ingresar', 'Session Activa - E150');  
    }   
// ------------------
} else if (isset($_POST['registrar'])) { /*|||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||| REGISTRO CLIENTE */
    if ( !empty($_POST['nombre']) && !empty($_POST['apellido']) && !empty($_POST['cedula']) && !empty($_POST['telefono']) && !empty($_POST['correo']) && !empty($_POST['tipo_documento']) && !empty($_POST['clave'])) {
    
        $nombre = $_POST['nombre'];  $apellido  = $_POST['apellido']; $cedulaR = $_POST['cedula'];
        $telefono  = $_POST['telefono']; $correoR = strtolower($_POST['correo']); $tipoDocumento = $_POST['tipo_documento'];
        $claveRegistro = $_POST['clave'];

        $campos = [
            'Nombre' => $nombre,
            'Apellido' => $apellido,
            'Cedula' => $cedulaR,
            'Telefono' => $telefono,
            'Documento' => $tipoDocumento,
            'Clave' => $claveRegistro
        ];
        /// Sanitización de Entradas
            foreach ($campos as $nombree => $valor) {  /* V2 */ 
                if (!validarEntradaSQL($valor)) {
                    MensajeJSON(0, 'registrar', "Entrada inválida detectada en el campo: $nombree");
                }
            } 
                //// Validar Datos  V5
                validarExpresiones('cedula', $cedulaR, "Cédula (F) inválida","registrar");
                validarExpresiones('correo', $correoR, "Correo (F) inválida","registrar");
                validarExpresiones('documento', $tipoDocumento, "Tipo de Documento (F) inválida","registrar");
                validarExpresiones('clave', $claveRegistro, "Clave (F) inválida","registrar");
                validarExpresiones('telefono', $telefono, "Telefono (F) inválida","registrar");
                validarExpresiones('nombre', $nombre, "Nombre (F) inválida","registrar");
                validarExpresiones('apellido', $apellido, "Apellido (F) inválida","registrar");

                if (!validarTipoDocumento($tipoDocumento)) { // Validar tipo_documento
                    MensajeJSON(0, 'registrar', 'El tipo de documento no es válido - ERROR E520'); 
                }

                $datosRegistro = [
                    'operacion' => 'registrar',
                    'datos' => [
                        'nombre' => $nombre,
                        'apellido' => $apellido,
                        'cedula' => $cedulaR,
                        'telefono' => $telefono,
                        'correo' => $correoR,
                        'tipo_documento' => $tipoDocumento,
                        'clave' => $claveRegistro
                    ]
                ];
    
                $resultado = $objlogin->procesarLogin(json_encode($datosRegistro));
    
                /*if ($resultado['respuesta'] == 1) {
                    require_once 'modelo/CORREObienvenida.php';
                    $envio = enviarBienvenida($correoR);
                }*/
    
                echo json_encode($resultado);
              exit;
               
    } else {
        MensajeJSON(0, 'registrar', 'Datos Vacios - ERROR E300'); 
    }
// -------------
} else if (isset($_POST['validarclave'])) {

    if(!empty($_POST['cedula'])&&!empty($_POST['tipo_documentos'])){

        $cedulaClave = $_POST['cedula'];  $documentoClave = $_POST['tipo_documentos'];

        $campos = [
            'Cedula' => $cedulaClave,
            'Documento' => $documentoClave
        ];
        /// Sanitización de Entradas
            foreach ($campos as $nombre => $valor) {  /* V2 */ 
                if (!validarEntradaSQL($valor)) {
                    MensajeJSON(0, 'validarclave', "Entrada inválida detectada en el campo: $nombre");
                }
            }

             //// Validar Datos  V3
            validarExpresiones('cedula', $cedulaClave, "Cedula (F) inválida","validarclave");
            validarExpresiones('documento', $documentoClave, "Tipo de Documento (F) inválida","validarclave");
     
            if (!validarTipoDocumento($documentoClave)) { // Validar tipo_documento
                MensajeJSON(0, 'validarclave', 'El tipo de documento no es válido - ERROR E520');  
            }
            
                $datosValidar = [
                    'operacion' => 'validar',
                    'datos' => [
                        'cedula' => $cedulaClave,
                        'tipo_documento' => $documentoClave
                    ]
                ];

                $resultado = $objlogin->procesarLogin(json_encode($datosValidar));
               
                if ($resultado && isset($resultado->cedula)) {
                    $_SESSION["cedula"] = $resultado->cedula;
                    $_SESSION["nombres"] = $resultado->nombre;
                    $_SESSION["apellidos"] = $resultado->apellido;
                    $_SESSION["correos"] = $resultado->correo;
                    $_SESSION["iduser"] = 1;
                    $_SESSION["nivel"] = $resultado->nivel;
                        
                    MensajeJSON(1, 'validarclave', '');  

                } else {
                    MensajeJSON(0, 'validarclave', 'Cédula incorrecta o no hay registro');  
                }   
    }else{
        MensajeJSON(0, 'validarclave', 'Datos Vacios - ERROR E300');  
    }
 // ------------------
} else if(isset($_POST['cedula'])){ /* |||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||| VERIFICAR CEDULA  */
    
    if (!empty($_POST['cedula']) ) {   /*  VACIOS   | VERIFICAR CEDULA   */
        $cedulaValidar = $_POST['cedula'];
        
        $campos = [
            'Cedula' => $cedulaValidar
        ];
        /// Sanitización de Entradas
            foreach ($campos as $nombre => $valor) {  /* V2 */ 
                if (!validarEntradaSQL($valor)) {
                    MensajeJSON(0, 'verificar', "Entrada inválida detectada en el campo: $nombre");
                }
            }

            //// Validar Datos  V3
            validarExpresiones('cedula', $cedulaValidar, "Cedula (F) inválida","verificar");

                if (ctype_digit($cedulaValidar)) {
                    $datosLogin = [
                        'operacion' => 'verificarcedula',
                        'datos' => [
                            'cedula' => $_POST['cedula']
                        ] 
                    ];

                    $resultado = $objlogin->procesarLogin(json_encode($datosLogin));
                    echo json_encode($resultado);
                    exit;
                } else {
                    MensajeJSON(0, 'verificar', 'La cédula no es válida. - ERROR E520'); 
                }

     }else{ /* DATOS VACIOS | VERIFICAR CEDULA  */
        MensajeJSON(0, 'verificar', 'Datos Vacios - ERROR E300');  
     }

} else if(isset($_POST['correo'])){ /* |||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||| VERIFICAR COREREO  */

     if (!empty($_POST['correo']) ) {   /*  VACIOS   | VERIFICAR CORREO   */
        $correo = strtolower($_POST['correo']);

            // Validar datos V3
            validarExpresiones('correo', $correo, "Correo (F) inválida","verificarcorreo");

            $datosLogin = [
                'operacion' => 'verificarCorreo',
                'datos' => [
                    'correo' => $correo
                ] 
            ];

            $resultado = $objlogin->procesarLogin(json_encode($datosLogin));
            echo json_encode($resultado);
            exit; 

     }else{ /* DATOS VACIOS | VERIFICAR CORREO  */
        MensajeJSON(0, 'verificarcorreo', 'Datos Vacios - ERROR E300');   
     }
//--------------------------------
} else if (isset($_POST['cerrarolvido'])) {    /*||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||| CERRAR OLVIDO*/  
    session_destroy();
    header('Location: ?pagina=login');
    exit;
    
// ------------------
} else if (isset($_POST['cerrar'])) { /*||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||| CERRAR SESSION*/
    
    // Registrar en bitácora si es administrador o asesora de venta
    if (isset($_SESSION["nivel_rol"]) && ($_SESSION["nivel_rol"] == 2 || $_SESSION["nivel_rol"] == 3)) {
        $bitacora = [
            'id_persona' => $_SESSION["id"],
            'accion' => 'Cierre de sesión',
            'descripcion' => 'El usuario ha cerrado sesión desde el panel administrativo.'
        ];
        $bitacoraObj = new Bitacora();
        $bitacoraObj->registrarOperacion($bitacora['accion'], 'login', $bitacora);
    }
    
    session_destroy();
    header('Location: ?pagina=login');
    exit;

// ------------------
} else if (!empty($_SESSION['id'])) { /*||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||| CERRAR SESSION SI ENTRE POR URL*/
 
    if (isset($_SESSION["nivel_rol"]) && ($_SESSION["nivel_rol"] == 2 || $_SESSION["nivel_rol"] == 3)) {
    $bitacora = [
        'id_persona' => $_SESSION["id"],
        'accion' => 'Cierre de sesión',
        'descripcion' => 'El usuario ha cerrado sesión por URL.'
    ];
    $bitacoraObj = new Bitacora();
    $bitacoraObj->registrarOperacion($bitacora['accion'], 'login', $bitacora);
    }

    session_destroy();
    header('Location: ?pagina=login');
    exit;
//---------------------------------
} else {    
    require_once 'vista/login.php';
}

?>

