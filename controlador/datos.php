<?php  

use LoveMakeup\Proyecto\Modelo\Datos;
use LoveMakeup\Proyecto\Modelo\Bitacora;

// Iniciar sesión solo si no está ya iniciada
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
if (empty($_SESSION["id"])) {
    header("location:?pagina=login");
    exit;
} /* Validacion URL */
if (!empty($_SESSION['id'])) { 
        require_once 'verificarsession.php';
} 

require_once 'permiso.php';
require_once 'assets/ajuste/validaciones.php';

$objdatos = new Datos();
//----
  function registrarBitacora($accion, $descripcion) {
        //FUNCION PARA REGISTRAR LA BITACORA
        $datos = [
            'id_persona'  => $_SESSION["id"],
            'accion'      => $accion,
            'descripcion' => $descripcion
        ];

        // Instanciamos y registramos
        $bitacoraObj = new Bitacora();
        return $bitacoraObj->registrarOperacion($accion, 'Datos Usuario', $datos);
    }
//------------------------------
if (isset($_POST['actualizar'])) {  //-------------------------------------------------- [ ACTUALIZAR DATOS ]
//-------------------------------
    if (isset($_SESSION['id']) && !empty($_SESSION['id'])) { /* V1 */
        if(!empty($_POST['nombre']) &&!empty($_POST['apellido']) && !empty($_POST['cedula']) && !empty($_POST['correo']) && 
            !empty($_POST['telefono']) && !empty($_POST['tipo_documento']) && !empty($_POST['cedula_actual']) && !empty($_POST['correo_actual'])){  /* V2 */
            
            $nombre =  ucfirst(strtolower($_POST['nombre'])); $apellido = ucfirst(strtolower($_POST['apellido'])); $cedula = $_POST['cedula']; 
            $correo = strtolower($_POST['correo']);  $telefono = $_POST['telefono']; $documento = $_POST['tipo_documento'];
            $cedula_actual = $_POST['cedula_actual']; $correo_actual = $_POST['correo_actual'];

            $campos = [
                'Nombre' => $nombre,
                'Apellido' => $apellido,      
                'Cedula' => $cedula, 
                'cedula_actual' => $cedula_actual, 
                'Documento' => $documento, 
                'Telefono' => $telefono
        
            ];
                 /// Sanitización de Entradas
                foreach ($campos as $nombree => $valor) {  /* V3 */ 
                    if (!validarEntradaSQL($valor)) {
                        MensajeJSON(0,'actualizar',"Entrada inválida detectada en el campo: $nombree");
                    }
                }   
                //Validar datos
                validarExpresiones('cedula', $cedula, "Cedula (F) inválido","actualizar");
                validarExpresiones('cedula', $cedula_actual, "Cedula Actual (F) inválido","actualizar");
                validarExpresiones('correo', $correo, "Correo (F) inválido","actualizar");
                validarExpresiones('correo', $correo_actual, "Correo Actual (F) inválido","actualizar");
                validarExpresiones('documento', $documento, "tipo de documento (F) inválido","actualizar");
                validarExpresiones('telefono', $telefono, "Telefono (F) inválido","actualizar");
                validarExpresiones('nombre', $nombre, "Nombre (F) inválido","actualizar");
                validarExpresiones('apellido', $apellido, "Apellido (F) inválido","actualizar");

                    if (!validarTipoDocumento($documento)) { // Validar tipo_documento
                        MensajeJSON(0,'actualizar','El tipo de documento no es válido - ERRROR E520');
                    }

                    $datosUsuario = [
                        'operacion' => 'actualizar',
                        'datos' => [
                            'nombre' => $nombre,
                            'apellido' => $apellido,
                            'cedula' => $cedula,
                            'correo' => $correo,
                            'telefono' => $telefono,
                            'cedula_actual' => $cedula_actual,
                            'correo_actual' => $correo_actual,
                            'tipo_documento' => $documento
                        ]
                    ];
    
                    $resultado = $objdatos->procesarUsuario(json_encode($datosUsuario));
                        if ($resultado['respuesta'] == 1) {
                            RegistrarBitacora('Modificación de Usuario', "Datos Modificado del usuario CI {$documento}-{$cedula_actual}, {$nombre} {$apellido}");
                        }
    
                            if ($resultado['respuesta'] == 1) {
                                $id_usuario = $_SESSION["id_usuario"];
                                $resultado1 = $objdatos->consultardatos($id_usuario);
    
                                        // Verificamos que hay al menos un resultado
                                    if (!empty($resultado1) && is_array($resultado1)) {
                                        $datos = $resultado1[0]; // Accedemos al primer elemento
    
                                        $_SESSION["nombre"]   = $datos["nombre"];
                                        $_SESSION["apellido"] = $datos["apellido"];
                                        $_SESSION["telefono"] = $datos["telefono"];
                                        $_SESSION["correo"]   = $datos["correo"];
                                        $_SESSION["documento"]   = $datos["tipo_documento"];
                                        $_SESSION["id"]   = $datos["cedula"];
                                    }
                            }   
                    echo json_encode($resultado);  
                    exit;
        } else{
            MensajeJSON(0,'actualizar','Datos Vacios - ERROR E300');
        }
    } else{ /* V1 */ 
        MensajeJSON(0,'actualizar','Session no encontrada - ERRROR E100');
    }
//-----------------------------------------     
} else if(isset($_POST['actualizarclave'])){ // -------------------------------------------------- [ ACTUALIZAR CLAVE ]
//-----------------------------------------   
    if (isset($_SESSION['id']) && !empty($_SESSION['id'])) { /* V1 */

        if(!empty($_POST['clave']) && !empty($_POST['clavenueva'])){
            
            $clave = $_POST['clave']; $clavenueva = $_POST['clavenueva'];
            
            $campos = [
                'Clave' => $clave,
                'Clavenueva' => $clavenueva
            ];
                 /// Sanitización de Entradas
                foreach ($campos as $nombree => $valor) {  /* V3 */ 
                    if (!validarEntradaSQL($valor)) {
                        MensajeJSON(0,'actualizar',"Entrada inválida detectada en el campo: $nombree");
                    }
                }   
                    //Validar datos V4
                    validarExpresiones('clave', $clave, "Clave  (F) inválido","clave");
                    validarExpresiones('clave', $clavenueva, "Clave Nueva (F) inválido","clave");


                        $datosUsuario = [
                            'operacion' => 'actualizarclave',
                            'datos' => [
                                'id_usuario' => $_SESSION["id_usuario"],
                                'clave_actual' => $clave,
                                'clave' => $clavenueva
                            ]
                        ];
            
                        $resultado = $objdatos->procesarUsuario(json_encode($datosUsuario));
                            if ($resultado['respuesta'] == 1) {
                                RegistrarBitacora('Modificación de Usuario - Clave', "Datos Modificado del usuario: {$_SESSION["id"]}");
                            }
                        echo json_encode($resultado);
                        exit;
        }else{
            MensajeJSON(0,'clave','Datos Vacios - ERRROR E300');
        }
    } else{ /* V1 */ 
        MensajeJSON(0,'clave','Session no encontrada - ERRROR E100');
    } 
//---------------    
}else if($_SESSION["nivel_rol"] != 2 && $_SESSION["nivel_rol"] != 3) {
//----------------    
    header("Location: ?pagina=catalogo");
    exit();
//------------------    
} else{ //----------------------------------------------------- [ VISTA ]
//------------------    
    RegistrarBitacora('Acceso a Modelo de Datos', "Entro al Modulo de Datos del usuario");
    require_once 'vista/seguridad/datos.php';
//-----------------
}

?>






