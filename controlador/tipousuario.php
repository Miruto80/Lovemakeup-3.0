<?php
// controlador/tipousuario.php
use LoveMakeup\Proyecto\Modelo\TipoUsuario;
use LoveMakeup\Proyecto\Modelo\Bitacora;
//-----------------
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
//----------------
if (!empty($_SESSION['id'])) {
        require_once 'verificarsession.php';
}
//-----
if (!isset($_SESSION['limite_tipousuario'])) {
    $_SESSION['limite_tipousuario'] = 100;
}
//--------
if (isset($_POST['ver_mas'])) {
    $_SESSION['limite_tipousuario'] += 100;
    header("location:?pagina=tipousuario");
    exit;
}
//------------------
require_once 'permiso.php';
require_once 'assets/ajuste/validaciones.php';
$objRol = new TipoUsuario();
//--------------------
    function registrarBitacora($accion, $descripcion) {
        //FUNCION PARA REGISTRAR LA BITACORA
        $datos = [
            'id_persona'  => $_SESSION["id"],
            'accion'      => $accion,
            'descripcion' => $descripcion
        ];

        // Instanciamos y registramos
        $bitacoraObj = new Bitacora();
        return $bitacoraObj->registrarOperacion($accion, 'tipo usuario (ROL)', $datos);
    }
//-------------------
if (isset($_POST['registrar'])) { //-------------------------------------------------- [ REGISTRAR ROL ]
//-------------------
     if (isset($_SESSION['id']) && !empty($_SESSION['id'])) { // Validacion 1
        if ($_SESSION["nivel_rol"] == 3 && tieneAcceso(17, 2)) { // Validacion 2
            if(!empty($_POST['nombreRol']) && !empty($_POST['nivelRol'])){ // Validacion 3

                $nombre = $_POST['nombreRol']; $nivel = $_POST['nivelRol'];
                
                $campos = [
                    'nombre' => $nombre,
                    'nivel' => $nivel
                ];
                     /// Sanitización de Entradas - Validacion  4
                    foreach ($campos as $nombree => $valor) { 
                        if (!validarEntradaSQL($valor)) {
                            MensajeJSON(0, 'registrar', "Entrada inválida detectada en el campo: $nombree");
                        }
                    } 
                    // VALIDAR EXPRESIONES 
                    validarExpresiones('nombre', $nombre, "Nombre (F) inválido","registrar");
                    validarExpresiones('nivel_acceso', $nivel, "Nivel (F) inválido","registrar");

                    $datosRol = [
                        'operacion' => 'registrar',
                        'datos' => [
                            'nombre' =>  $nombre,
                            'nivel' =>  $nivel
                        ] 
                    ];

                    $resultado = $objRol->procesarRol(json_encode($datosRol));
                        if ($resultado['respuesta'] == 1) { // Validacion de Registro Bitacora
                            RegistrarBitacora('Registrado de ROL', "Se registro un nuevo ROL: {$nombre}, Su nivel es:{$nivel}");
                        }
                    echo json_encode($resultado);
                    exit; 

            } else { // Validacion 3 - Error 
                MensajeJSON(0, 'registrar', 'Datos Vacios - ERROR E300');
            }
        } else{ // Validacion 2 - Error 
            MensajeJSON(0, 'registrar', 'No Tiene Permiso para realizar esta operacion - ERROR E200');
        }      
    } else{ // Validacion 1 - Error 
        MensajeJSON(0, 'registrar', 'Session no encontrada - ERROR E100');
    } 
//---------------
} else if(isset($_POST['modificar'])){ //----------------------------------------------------- [BUSCAR PERMISOS Y IR A LA VISTA]
//---------------
     if (isset($_SESSION['id']) && !empty($_SESSION['id'])) { // Validacion 1
        if ($_SESSION["nivel_rol"] == 3 && tieneAcceso(17, 5)) { // Validacion 2
            if (!empty($_POST['modificar']) && !empty($_POST['RolNombre'])) {   /* VACIOS   | VER LOS PERMISOS  */

                $id_rol = $_POST['modificar']; $nombre_usuario = $_POST['RolNombre']; $usuario = $_SESSION['id_usuario'];

                $campos = [
                    'id_rol' => $id_rol
                ];
                     /// Sanitización de Entradas
                    foreach ($campos as $nombree => $valor) {  // Validacion  4
                        if (!validarEntradaSQL($valor)) {
                            header("location:?pagina=tipousuario");
                            exit;
                        }
                    } 

                    if (!preg_match('/^[0-9]{1,6}$/', $id_rol)) {  // Validacion 5
                        header("location:?pagina=tipousuario");
                        exit;
                    }

                    if ($id_rol == 4 || $id_rol == 1) {
                        header("location:?pagina=tipousuario");
                        exit;
                    }

                    $modificar = $objRol->buscar($id_rol);
                    if ($modificar) {
                        // Si hay datos, obtenemos el nivel y cargamos la vista
                        $nivel_usuario = $objRol->obtenerNivelPorId($usuario);
                        require_once("vista/seguridad/permiso.php");
                    } else {
                    // no se encontro datos 
                    header("location:?pagina=tipousuario");
                    exit;
                    }
            } else{  /* DATOS VACIOS | VER LOS PERMISOS  */
                header("location:?pagina=tipousuario");
                exit;
            }  
        } else{ // Validacion 2 - Error 
            header("location:?pagina=tipousuario");
            exit;
        }      
    } else{ // Validacion 1 - Error 
        header("location:?pagina=tipousuario");
        exit;
    }
///---------       
} else if (isset($_POST['actualizar_permisos'])) { //-----------------------------------------------[ ACTUALIZAR PERMISOS ]
 //---------
    if (isset($_SESSION['id']) && !empty($_SESSION['id'])) { // Validacion 1
        if ($_SESSION["nivel_rol"] == 3 && tieneAcceso(17, 5)) { // Validacion 2 - Permisos
            if (!empty($_POST['permiso']) && !empty($_POST['permiso_id'])) {  // Validacion 3    

            // Permisos enviados desde la vista
            $permisosRecibidos = $_POST['permiso'] ?? [];      // switches activos
            $permisosId = $_POST['permiso_id'] ?? [];          // id_permiso_rol existentes
            
            // VALIDAR $permisosRecibidos
            foreach ($permisosRecibidos as $modulo_id => $permisosModulo) {
                validarExpresiones('id_fk', $modulo_id, "Modulo (F) inválido", "actualizar_permisos");

                foreach ($permisosModulo as $id_permiso => $valor) {
                    // id_permiso debe ser numérico
                    validarExpresiones('id_fk', $id_permiso, "Permiso (F) inválido", "actualizar_permisos");
                }
            }
            
            // VALIDAR $permisosId
            foreach ($permisosId as $modulo_id => $permisosModulo) {
                validarExpresiones('id_fk', $modulo_id, "Modulo en Permiso (F) inválido", "actualizar_permisos");

                foreach ($permisosModulo as $id_permiso => $id_permiso_rol) {
                    validarExpresiones('id_fk', $id_permiso, "NRO permiso inválido en permiso (F) inválido", "actualizar_permisos");
                    validarExpresiones('id_fk', $id_permiso_rol, "Nro permiso rol  (F) inválido", "actualizar_permisos");
                }
            }

            $listaPermisos = [];
            foreach ($permisosId as $modulo_id => $permisosModulo) {
                foreach ($permisosModulo as $id_permiso => $id_permiso_rol) {

                    // Si el switch está marcado → estado = 1, si no → 0
                    $estado = isset($permisosRecibidos[$modulo_id][$id_permiso]) ? 1 : 0;

                    $listaPermisos[] = [
                        'id_permiso_rol' => (int)$id_permiso_rol,
                        'id_modulo'      => (int)$modulo_id,
                        'id_permiso'     => (int)$id_permiso, // 1..5
                        'estado'         => $estado
                    ];
                }
            }

                $datosPermiso = [
                    'operacion' => 'actualizar_permisos',
                    'datos' => $listaPermisos
                ];

                // Procesar actualización
                $resultado = $objRol->procesarRol(json_encode($datosPermiso));
                    if ($resultado['respuesta'] == 1) { // Validacion de Registro Bitacora
                        RegistrarBitacora('Actualizar Permisos de tipo usuario', "Se Actualizo el  Permisos el tipo usuario: ");
                    }
                echo json_encode($resultado);
                exit;

            } else { // Validacion 3 - Error 
                MensajeJSON(0, 'actualizar_permisos', 'Datos Vacios - ERROR E300');
            }    
        } else{ // Validacion 2 - Error 
            MensajeJSON(0, 'actualizar_permisos', 'No Tiene Permiso para realizar esta operacion - ERROR E200');
        }      
    } else{ // Validacion 1 - Error 
        MensajeJSON(0, 'actualizar_permisos', 'Session no encontrada - ERROR E100');
    } 
//-------    
}else if(isset($_POST['actualizar'])){ //------------------------------------------------ [ ACTUALIZAR DATOS ]
 //---------
    if (isset($_SESSION['id']) && !empty($_SESSION['id'])) { // Validacion 1
        if ($_SESSION["nivel_rol"] == 3 && tieneAcceso(17, 3)) { // Validacion 2   
            if(!empty($_POST['id_rol']) && !empty($_POST['nombre']) && !empty($_POST['nivel']) && !empty($_POST['nivel_actual'])){  // Validacion 3

                $id_rol = $_POST['id_rol'];  $nombre = $_POST['nombre'];  $nivel = $_POST['nivel']; $nivel_actual = $_POST['nivel_actual'];
                
                $campos = [
                    'nombre' => $nombre,
                    'nivel' => $nivel,
                    'id_rol' => $id_rol,
                    'nivel_actual' => $nivel_actual
                ];
                     /// Sanitización de Entradas
                    foreach ($campos as $nombree => $valor) {  // Validacion  4
                        if (!validarEntradaSQL($valor)) {
                            MensajeJSON(0, 'actualizar', "Entrada inválida detectada en el campo: $nombree");
                        }
                    } 

                    validarExpresiones('nombre', $nombre, "Nombre (F) inválido", "actualizar");
                    validarExpresiones('nivel_acceso', $nivel, "Nivel (F) inválido", "actualizar");
                    validarExpresiones('nivel_acceso', $nivel_actual, "Nivel actual (F) inválido", "actualizar");
                    validarExpresiones('id_fk', $id_rol, "ROL (F) inválido", "actualizar");

                    // ROL EXISTE
                    $datosRol1 = ['operacion' => 'verificarrol','datos' => ['id_rol' => $id_rol]  ];
                        $resultadoVerificacion1 = $objRol->procesarRol(json_encode($datosRol1));
                            if ($resultadoVerificacion1['respuesta'] == 0) {
                                MensajeJSON(0, 'actualizar', 'ROL no existente - ERROR E520');
                            } 

                    $rolesRestringidos = [1, 2, 3, 4];
                        if (in_array($id_rol, $rolesRestringidos) && $nivel_actual != $nivel) {
                            MensajeJSON(0, 'actualizar', 'Restringido modificar el nivel - ERROR E520');
                        }
                    
                        $datosRol = [
                            'operacion' => 'actualizar',
                            'datos' => [
                                'id_rol' =>  $id_rol,
                                'nombre' =>  $nombre,
                                'nivel' =>  $nivel,
                                'nivel_actual' => $nivel_actual
                            ] 
                        ];

                        $resultado = $objRol->procesarRol(json_encode($datosRol));
                            if ($resultado['respuesta'] == 1) { // Validacion de Registro Bitacora
                                RegistrarBitacora('Actualizar datos de tipo usuario', "Se Actualizo los datos del tipo usuario: {$nombre} - {$nivel_actual} | (N) nivel: {$nivel}");
                            }
                        echo json_encode($resultado);
                        exit; 

            } else { // Validacion 3 - Error 
                MensajeJSON(0, 'actualizar', 'Datos Vacios - ERROR E300');
            }
        } else{ // Validacion 2 - Error 
            MensajeJSON(0, 'actualizar', 'No Tiene Permiso para realizar esta operacion - ERROR E200');
        }      
    } else{ // Validacion 1 - Error 
        MensajeJSON(0, 'actualizar', 'Session no encontrada - ERROR E100');
    } 
//---------
} else if(isset($_POST['eliminar'])){ //-------------------------------------------------- [ ELIMINAR ]
//---------
    if (isset($_SESSION['id']) && !empty($_SESSION['id'])) { // Validacion 1
        if ($_SESSION["nivel_rol"] == 3 && tieneAcceso(17, 4)) { // Validacion 2
            if(!empty($_POST['id_rol'])){ // Validacion 3

            $id_rol = $_POST['id_rol'];

            $campos = [
                    'id_rol' => $id_rol
                ];
                     /// Sanitización de Entradas
                    foreach ($campos as $nombree => $valor) {  // Validacion  4
                        if (!validarEntradaSQL($valor)) {
                            MensajeJSON(0, 'eliminar', "Entrada inválida detectada en el campo: $nombree");
                        }
                    } 
                    
                    validarExpresiones('id_fk', $id_rol, "ROL (F) inválido", "actualizar");

                    if($id_rol == 1 || $id_rol == 2 || $id_rol == 3 || $id_rol == 4 ){
                        MensajeJSON(0, 'eliminar', 'Tipo de usuario restringidos, no se pueden eliminar');
                    }

                    // ROL EXISTE
                    $datosRol1 = ['operacion' => 'verificarrol','datos' => ['id_rol' => $id_rol]  ];
                        $resultadoVerificacion1 = $objRol->procesarRol(json_encode($datosRol1));
                            if ($resultadoVerificacion1['respuesta'] == 0) {
                                MensajeJSON(0, 'eliminar', 'ROL no existente - ERROR E520');
                            } 
    
                    $datosRol = [
                        'operacion' => 'eliminar',
                            'datos' => [
                                'id_rol' =>  $id_rol
                            ] 
                    ];

                    $resultado = $objRol->procesarRol(json_encode($datosRol));
                        if ($resultado['respuesta'] == 1) { // Validacion de Registro Bitacora
                            RegistrarBitacora('Eliminar tipo usuario', "Se elimino el tipo usuario: {$id_rol}");
                        }
                    echo json_encode($resultado);
                    exit; 

            } else { // Validacion 3 - Error 
                MensajeJSON(0, 'eliminar', 'Datos Vacios - ERROR E300');
            }
        } else{ // Validacion 2 - Error 
            MensajeJSON(0, 'eliminar', 'No Tiene Permiso para realizar esta operacion - ERROR E200');
        }      
    } else{ // Validacion 1 - Error 
        MensajeJSON(0, 'eliminar', 'Session no encontrada - ERROR E100');
    } 
//------
} else if ($_SESSION["nivel_rol"] == 3 && tieneAcceso(17, 1)) { //----------------------------- [ VISTA ]
//------      
        RegistrarBitacora('Acceso a Módulo ROL', "Entro al módulo de Tipo usuario");
        $registro = $objRol->consultar($_SESSION['limite_tipousuario']);
        $total_registros = $objRol->contarTotal(); 

        $pagina_actual = isset($_GET['pagina']) ? $_GET['pagina'] : 'tipousuario';
        require_once 'vista/tipousuario.php';
//-------        
} else {
        require_once 'vista/seguridad/privilegio.php';

} 