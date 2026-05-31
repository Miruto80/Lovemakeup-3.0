<?php  
use LoveMakeup\Proyecto\Modelo\Usuario;
use LoveMakeup\Proyecto\Modelo\Bitacora;
// Iniciar sesión solo si no está ya iniciada
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!empty($_SESSION['id'])) {
    require_once 'verificarsession.php';
}
//---- 
require_once 'permiso.php';
require_once 'assets/ajuste/validaciones.php';
//--
$objusuario = new Usuario();
//-----
if (!isset($_SESSION['limite_usuario'])) {
    $_SESSION['limite_usuario'] = 100;
}

if (isset($_POST['ver_mas'])) {
    $_SESSION['limite_usuario'] += 100;
    header("location:?pagina=usuario");
    exit;
}
//---
$rol = $objusuario->obtenerRol();
$roll = $objusuario->obtenerRol();
//----
$registro = $objusuario->consultar($_SESSION['limite_usuario']);
$total_registros = $objusuario->contarTotal(); 



//---
    // Obtiene el nivel correspondiente a un id_rol
    function obtenerNivelPorRol($id_rol, $roles) {
        foreach ($roles as $rol) {
            if ($rol['id_rol'] == $id_rol) {
                return $rol['nivel'];
            }
        }
        return null;
    }
//-----
function registrarBitacora($accion, $descripcion) {
    //FUNCION PARA REGISTRAR LA BITACORA
    $datos = [
        'id_persona'  => $_SESSION["id"],
        'accion'      => $accion,
        'descripcion' => $descripcion
    ];

    // Instanciamos y registramos
    $bitacoraObj = new Bitacora();
    return $bitacoraObj->registrarOperacion($accion, 'usuario', $datos);
}
//---
if (isset($_POST['registrar'])) { //---------------------------------- REGISTRAR USUARIO 
//----    
    if (isset($_SESSION['id']) && !empty($_SESSION['id'])) { /* V1 */
        if ($_SESSION["nivel_rol"] == 3 && tieneAcceso(16, 2)) { /* V2 */ 

            if (!empty($_POST['nombre']) && !empty($_POST['apellido']) && !empty($_POST['cedula']) && !empty($_POST['telefono']) 
                && !empty($_POST['correo']) && !empty($_POST['id_rol']) && !empty($_POST['clave'])) {  /* V3 */

                $nombre = ucfirst(strtolower($_POST['nombre'])); $apellido = ucfirst(strtolower($_POST['apellido'])); $cedula =  $_POST['cedula'];
                $documento = $_POST['tipo_documento']; $telefono = $_POST['telefono']; $correo = strtolower($_POST['correo']); $clave = $_POST['clave'];
                $id_rol = (int)$_POST['id_rol']; 
  
                $campos = [
                    'Nombre' => $nombre,
                    'Apellido' => $apellido,      
                    'Cedula' => $cedula, 
                    'Documento' => $documento, 
                    'Telefono' => $telefono,
                    'Clave' => $clave,
                    'Id_rol' => $id_rol 
                ];
                     /// Sanitización de Entradas
                    foreach ($campos as $nombree => $valor) {  /* V4 */ 
                        if (!validarEntradaSQL($valor)) {
                              MensajeJSON(0, 'incluir', "Entrada inválida detectada en el campo: $nombree");
                        }
                    } 
                        //// Validar Datos  V5
                        validarExpresiones('cedula', $cedula, "Cédula (F) inválida","incluir");
                        validarExpresiones('correo', $correo, "Correo (F) inválido","incluir");
                        validarExpresiones('documento', $documento, "Documento (F) inválido","incluir");
                        validarExpresiones('rol', $id_rol, "ROL (F) inválido","incluir");
                        validarExpresiones('clave', $clave, "Clave (F) inválida","incluir");
                        validarExpresiones('telefono', $telefono, "Telefono (F) inválido","incluir");
                        validarExpresiones('nombre', $nombre, "Nombre (F) inválido","incluir");
                        validarExpresiones('apellido', $apellido, "Apellido (F) inválido","incluir");

                       
                        if (!validarTipoDocumento($_POST['tipo_documento'])) {   // Validar tipo_documento
                             MensajeJSON(0, 'incluir', 'El tipo de documento no es válido - ERROR520');     
                        }

                        // Validar y corregir nivel según el id_rol (por seguridad, ignoramos el nivel enviado y usamos el del rol)
                        $nivel_valido = obtenerNivelPorRol($_POST['id_rol'], $rol);
                        if ($nivel_valido === null) {
                            MensajeJSON(0, 'incluir', 'No se pudo obtener el nivel del rol - ERROR520');  
                        }

                        // VALIDACION EXISTENTE ---------- PK O FK
                            // USUARIO EXISTE
                            $datosUsuario = ['operacion' => 'verificar','datos' => ['cedula' => $cedula]  ];
            
                            $resultadoVerificacion = $objusuario->procesarUsuario(json_encode($datosUsuario));
                            if ($resultadoVerificacion['respuesta'] == 1) {
                                 MensajeJSON(0, 'incluir', 'La cédula ya está registrada - ERROR530');  
                            }

                            // ROL EXISTE
                            $datosUsuario = ['operacion' => 'verificarrol','datos' => ['id_rol' => $id_rol]  ];
            
                            $resultadoVerificacion1 = $objusuario->procesarUsuario(json_encode($datosUsuario));
                            if ($resultadoVerificacion1['respuesta'] == 0) {
                                 MensajeJSON(0, 'incluir', 'ROL no existente - ERROR530'); 
                            }

                            $datosUsuario = [
                                'operacion' => 'registrar',
                                    'datos' => [
                                        'nombre' => $nombre,
                                        'apellido' => $apellido,
                                        'cedula' => $cedula,
                                        'tipo_documento' => $documento,
                                        'telefono' => $telefono,
                                        'correo' => $correo,
                                        'clave' => $clave,
                                        'id_rol' => $id_rol, 
                                        'nivel' => $nivel_valido
                                    ]
                            ];
            
                            $resultadoRegistro = $objusuario->procesarUsuario(json_encode($datosUsuario));
                        
                                if ($resultadoRegistro['respuesta'] == 1) { // BITACORA
                                    RegistrarBitacora('Registrado de usuario', "Se registro un nuevo usuario: {$documento}-{$cedula}, {$nombre}, {$apellido}, Correo:{$correo}");
                                }

                            echo json_encode($resultadoRegistro); /// RESULTADO DE LA REGISTRO
                            exit;

            } else{  /* V3 DATOS VACIOS*/
                MensajeJSON(0, 'incluir', 'Datos Vacios');
            }
        } else{  /* V2 NO TIENE PERMISO */ 
            MensajeJSON(0, 'incluir', 'No Tiene Permiso para realizar esta operacion');
        }      
    } else{ /* V1 SESSION NO ACTIVA*/ 
        MensajeJSON(0, 'incluir', 'Session no encontrada');
    } 
//---
} else if(isset($_POST['actualizar'])){ //---------------------------- ACTUALIZAR DATOS DEL USUARIO
//---
    if (isset($_SESSION['id']) && !empty($_SESSION['id'])) { /* V1 */
        if ($_SESSION["nivel_rol"] == 3 && tieneAcceso(16, 3)) { /* V2 */ 

            if (!empty($_POST['id_persona']) && !empty($_POST['cedula']) && !empty($_POST['correo']) && !empty($_POST['id_rol']) &&
            !empty($_POST['estatus']) && !empty($_POST['cedulaactual']) && !empty($_POST['correoactual']) && !empty($_POST['rol_actual']) &&
            !empty($_POST['tipo_documento'])) { /* V3 VACIOS  */

            $id_persona = $_POST['id_persona'];   $cedula = $_POST['cedula']; $correo = strtolower($_POST['correo']);
            $id_rol = (int)$_POST['id_rol'];  $estatus  = (int)$_POST['estatus'];  $cedula_actual = $_POST['cedulaactual'];
            $correo_actual = strtolower($_POST['correoactual']);  $rol_actual = (int)$_POST['rol_actual']; $tipo_documento = $_POST['tipo_documento'];
    
            $campos = [
                'Id_persona' => $id_persona,
                'Cedula' => $cedula,      
                'Id_rol' => $id_rol,
                'Estatus' => $estatus,
                'Cedula_actual' => $cedula_actual,
                'rol_actual' => $rol_actual,
                'tipo_documento' => $tipo_documento
            ];
            /// Sanitización de Entradas
                foreach ($campos as $nombre => $valor) {  /* V4 */ 
                    if (!validarEntradaSQL($valor)) {
                        MensajeJSON(0, 'actualizar', "Entrada inválida detectada en el campo: $nombre");
                    }
                }
                    
                    //// Validar Datos  V5
                    validarExpresiones('id_usuario', $id_persona, "Usuario (F) inválido", "actualizar");
                    validarExpresiones('cedula', $cedula, "Cédula (F) inválida", "actualizar");
                    validarExpresiones('cedula', $cedula_actual, "Cédula A (F) inválida", "actualizar");
                    validarExpresiones('correo', $correo, "Correo (F) inválida", "actualizar");
                    validarExpresiones('correo', $correo_actual, "Correo A (F) inválida", "actualizar");
                    validarExpresiones('estatus', $estatus, "Estatus (F) inválida", "actualizar");
                    validarExpresiones('documento', $tipo_documento, "Tipo_documento (F) inválida", "actualizar");
                    validarExpresiones('rol', $id_rol, "ROL (F) inválida", "actualizar");
                    validarExpresiones('rol', $rol_actual, "ROL A (F) inválida", "actualizar");
                    
                 
                    if (!validarTipoDocumento($_POST['tipo_documento'])) {    // Validar tipo_documento
                        MensajeJSON(0, 'actualizar', 'El tipo de documento no es válido - ERROR520');
                    }


                        //VALIDACION EXISTENTE
                        // USUARIO EXISTE
                        $datosUsuario = ['operacion' => 'verificar','datos' => ['cedula' => $cedula_actual]  ];
        
                        $resultadoVerificacion = $objusuario->procesarUsuario(json_encode($datosUsuario));
                        if ($resultadoVerificacion['respuesta'] == 0) {
                            MensajeJSON(0, 'actualizar', 'Cedula no existente - ERROR 530');
                        }
                        
                        // ROL EXISTE
                        $datosUsuario = ['operacion' => 'verificarrol','datos' => ['id_rol' => $id_rol]  ];
        
                        $resultadoVerificacion1 = $objusuario->procesarUsuario(json_encode($datosUsuario));
                        if ($resultadoVerificacion1['respuesta'] == 0) {
                            MensajeJSON(0, 'actualizar', 'ROL no existente - ERROR 530');
                        } 
                            // ENVIO AL MODULO
                            $datosUsuario = [
                                'operacion' => 'actualizar',
                                    'datos' => [
                                        'id_persona' => $id_persona,
                                        'cedula' => $cedula,
                                        'correo' => $correo,
                                        'id_rol' => $id_rol,
                                        'estatus' => $estatus,
                                        'cedula_actual' => $cedula_actual,
                                        'correo_actual' => $correo_actual,
                                        'rol_actual' => $rol_actual,
                                        'tipo_documento' => $tipo_documento,
                                        //'nivel' => $nivel_valido
                                    ]
                            ]; 
                    
                                if($datosUsuario['datos']['id_persona'] == 2) { 
                                    if($datosUsuario['datos']['id_rol'] != 4) {
                                        MensajeJSON(0, 'actualizar', 'No puedes cambiar el Rol del usuario administrador');
                                    }
                                    if($datosUsuario['datos']['estatus'] != 1) {
                                        MensajeJSON(0, 'actualizar', 'No puedes cambiar el Rol del usuario administrador');
                                    }
                                }
                    
                                $resultado = $objusuario->procesarUsuario(json_encode($datosUsuario));
                            
                                    if ($resultado['respuesta'] == 1) {
                                        registrarBitacora('Modificación de usuario', "Se modificó datos del usuario: {$tipo_documento}-{$cedula}, {$correo}");
                                    }
                    
                                echo json_encode($resultado); /// RESULTADO DE LA ACTUALIZACION
                                exit;
                            //FIN DEL ENVIO    

            } else{  /* V3 datos vacios */
                MensajeJSON(0, 'actualizar', 'Datos Vacios - ERROR E300');
            }
        } else{  /* 2 */ 
            MensajeJSON(0, 'actualizar', 'No Tiene Permiso para realizar esta operacion - ERROR E200');
        }      
    } else{ /* V1 */ 
        MensajeJSON(0, 'actualizar', 'Session no encontrada - ERROR E100');
    } 
//---------
} else if(isset($_POST['eliminar'])){ //--------------------------------- ELIMINAR USUARIO
//----------    
    if (isset($_SESSION['id']) && !empty($_SESSION['id'])) { /* V1 */
        if ($_SESSION["nivel_rol"] == 3 && tieneAcceso(16, 4)) { /* V2 */ 

            if (!empty($_POST['eliminar'] && !empty($_POST['id_usuario'])) ) { /* V3 VACIOS  */
                $cedula = $_POST['eliminar'];  $id_usuario = $_POST['id_usuario'];
                
                $campos = [
                    'Cedula' => $cedula,
                    'id_usuario' => $id_usuario
                ];
                /// Sanitización de Entradas
                    foreach ($campos as $nombre => $valor) {  /* V4 */ 
                        if (!validarEntradaSQL($valor)) {
                            MensajeJSON(0, 'actualizar', "Entrada inválida detectada en el campo: $nombre");
                        }
                    }
                
                     //// Validar Datos  V5
                    validarExpresiones('cedula', $cedula, "Cédula  inválida (F)", "eliminar");

                    if (ctype_digit($cedula)) {
                
                        if ($cedula == $_SESSION['id']) { /* NO ELIMINARSE ASI MISMO | ELIMINAR  */
                            MensajeJSON(0, 'eliminar', 'No puedes eliminarte a ti mismo');
                        }

                        if ($id_usuario == 2 || $id_usuario == 1){
                            MensajeJSON(0, 'eliminar', 'Usuario restringido, no se puede elimimar');
                        }
                        
                        //VALIDACION EXISTENTE
                        $datosUsuario = ['operacion' => 'verificar','datos' => ['cedula' => $cedula]  ];
        
                        $resultadoVerificacion = $objusuario->procesarUsuario(json_encode($datosUsuario));
                        if ($resultadoVerificacion['respuesta'] == 0) {
                            MensajeJSON(0, 'eliminar', 'Cedula no existente - ERROR M530');
                        } 
                            // ENVIO AL METODO -----------
                                $datosUsuario = [
                                    'operacion' => 'eliminar',
                                    'datos' => [
                                        'cedula' => $cedula
                                    ] 
                                ];
        
                                $resultado = $objusuario->procesarUsuario(json_encode($datosUsuario));
        
                                if ($resultado['respuesta'] == 1) {
                                   registrarBitacora('Eliminación de usuario', "Se eliminó al usuario con Cédula: {$cedula}");
                                }
        
                                echo json_encode($resultado); /* RESPUESTA | ELIMINAR  */
                                exit;
                            //FIN ENVIO ------------   
        
                    } else { /* CEDULA NO NUMERICA | ELIMINAR  */
                        MensajeJSON(0, 'eliminar', 'La cédula no es válida. Debe contener solo números - ERROR E320');
                    }
            } else{  /* DATOS VACIOS | ELIMINAR  */
                 MensajeJSON(0, 'eliminar', 'Datos Vacios - ERROR E300'); 
            }
        } else{  /* V2 */ 
             MensajeJSON(0, 'eliminar', 'No Tiene Permiso para realizar esta operacion - ERROR E200'); 
        }      
    } else{ /* V1 */ 
        MensajeJSON(0, 'eliminar', 'Session no encontrada - ERROR E100'); 
    } 
//-----------    
} else if(isset($_POST['cedula'])){ //-------------------------------------------- VERIFICAR CEDULA
//----------
    if (isset($_SESSION['id']) && !empty($_SESSION['id'])) { /* V1 */
        if (!empty($_POST['cedula']) ) {   /* V2 VACIOS  */

            $cedulaValidar = $_POST['cedula'];
            // Validar V3
            validarExpresiones('cedula', $cedulaValidar, "Cédula  inválida (F)", "verificar");
            
            if (ctype_digit($cedulaValidar)) {
                // ENVIO AL METODO -----------
                $datosUsuario = [
                    'operacion' => 'verificar',
                        'datos' => [
                            'cedula' => $cedulaValidar
                        ] 
                ];

                $resultado = $objusuario->procesarUsuario(json_encode($datosUsuario));
                echo json_encode($resultado);
                exit;
                 //FIN ENVIO ------------ 
            } else {
                MensajeJSON(0, 'verificar', 'La cédula no es válida. Debe contener solo números - ERROR E320');
            }
        } else{ /* V2 DATOS VACIOS */
            MensajeJSON(0, 'verificar', 'Datos Vacios - ERROR E300'); 
        }
    } else{ /* V1 */ 
        MensajeJSON(0, 'verificar', 'Session no encontrada - ERROR E100'); 
    } 
//------------    
} else if(isset($_POST['correo'])){ //--------------------------- VERIFICAR CORREO
//------------
    if (isset($_SESSION['id']) && !empty($_SESSION['id'])) { /* V1 */
        if (!empty($_POST['correo']) ) {   /* V2 */

            $correo = strtolower($_POST['correo']);
                //validar | V3
                validarExpresiones('correo', $correo, "Correo  inválida (F)", "verificarcorreo");
                
                // ENVIO AL METODO -----------
                $datosUsuario = [
                    'operacion' => 'verificarCorreo',
                    'datos' => [
                        'correo' => $correo
                    ] 
                ];
                
                $resultado = $objusuario->procesarUsuario(json_encode($datosUsuario));
                echo json_encode($resultado);
                exit; 
                //FIN ENVIO ------------ 

        }else{ /*2  DATOS VACIOS */
            MensajeJSON(0, 'eliminar', 'Datos Vacios - ERROR E300');  
        }
    } else{ /* 1 */ 
        MensajeJSON(0, 'verificarcorreo', 'Session no encontrada - ERROR E100'); 
    }
//---------------    
} else  if(isset($_POST['rol'])){ //-------------------------------------- VERIFICAR ROL
//---------------
    if (isset($_SESSION['id']) && !empty($_SESSION['id'])) { /* V1 */
        if (!empty($_POST['rol']) ) {  /* V2 */ 

            $rolValidar = $_POST['rol'];
            
            //validar | V3
            validarExpresiones('rol', $rolValidar, "Correo  inválida (F)", "verifirol");

                if (ctype_digit($rolValidar)) {
                    
                        // ENVIO AL METODO -----------
                        $datosUsuario = [
                            'operacion' => 'verificarrol',
                            'datos' => [
                                'id_rol' =>  $rolValidar
                            ] 
                        ];

                        $resultado = $objusuario->procesarUsuario(json_encode($datosUsuario));
                        echo json_encode($resultado);
                        exit; 
                        //FIN ENVIO ------------ 

                } else {  /* FORMATO NO VALIDO | VERIFICAR ROL  */
                    MensajeJSON(0, 'verifirol', 'Formato inválido - ERROR E350'); 
                }
        }else{ /* DATOS VACIOS | VERIFICAR ROL  */
            MensajeJSON(0, 'verifirol', 'Datos Vacios - ERROR E300'); 
        }
    } else{ /* 1 */ 
        MensajeJSON(0, 'verifirol', 'Session no encontrada - ERROR E100'); 
    } 
//------    
} else{ //----------------------- VISTA
//------- 
    if ($_SESSION["nivel_rol"] == 3 && tieneAcceso(16, 1)) {
            registrarBitacora('Acceso a Usuario', "Entro al módulo de Usuario");
        $pagina_actual = isset($_GET['pagina']) ? $_GET['pagina'] : 'usuario';
        require_once 'vista/usuario.php';
    } else{
        require_once 'vista/seguridad/privilegio.php';
    }
//--------             
} 
?>