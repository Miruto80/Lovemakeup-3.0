<?php  

use LoveMakeup\Proyecto\Modelo\Catalogo_datos;

// Iniciar sesión solo si no está ya iniciada
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
$nombre = isset($_SESSION["nombre"]) && !empty($_SESSION["nombre"]) ? $_SESSION["nombre"] : "Estimado Cliente";
$apellido = isset($_SESSION["apellido"]) && !empty($_SESSION["apellido"]) ? $_SESSION["apellido"] : ""; 
$nombreCompleto = trim($nombre . " " . $apellido);
$sesion_activa = isset($_SESSION["id"]) && !empty($_SESSION["id"]);

if (!empty($_SESSION['id'])) {
    require_once 'verificarsession.php';
    require_once 'permiso.php';
}

require_once 'assets/ajuste/validaciones.php';
$objdatos = new Catalogo_datos();
//---------
if (isset($_POST['actualizar'])) {
//--------
    if (isset($_SESSION['id']) && !empty($_SESSION['id'])) { /* V1 */
        if ($_SESSION["nivel_rol"] == 1 && tieneAcceso(20, 3)) { 

        if(!empty($_POST['nombre']) &&!empty($_POST['apellido']) && !empty($_POST['cedula']) &&!empty($_POST['correo'])
         && !empty($_POST['telefono']) && !empty($_POST['tipo_documento']) && !empty($_POST['cedula_actual']) && !empty($_POST['correo_actual'])){ /* V2 */ 
          
            $nombre =  ucfirst(strtolower($_POST['nombre'])); $apellido = ucfirst(strtolower($_POST['apellido'])); $cedula = $_POST['cedula']; 
            $correo = strtolower($_POST['correo']);  $telefono = $_POST['telefono']; $documento = $_POST['tipo_documento'];
            $cedula_actual = $_POST['cedula_actual']; $correo_actual = strtolower($_POST['correo_actual']);

            $campos = [
                'Nombre' => $nombre,
                'Apellido' => $apellido,      
                'Cedula' => $cedula, 
                'Documento' => $documento, 
                'Telefono' => $telefono
        
            ];
                 /// Sanitización de Entradas
                foreach ($campos as $nombree => $valor) {  /* V3 */ 
                    if (!validarEntradaSQL($valor)) {
                        MensajeJSON(0,'actualizar',"Entrada inválida detectada en el campo: $nombree");
                    }
                }
                
                //Validar datos V4
                validarExpresiones('cedula', $cedula, "Cedula (F) inválida","actualizar");
                validarExpresiones('correo', $correo, "Correo (F) inválida","actualizar");
                validarExpresiones('documento', $documento, "Tipo de Documento (F) inválida","actualizar");
                validarExpresiones('telefono', $telefono, "Telefono (F) inválida","actualizar");
                validarExpresiones('nombre', $nombre, "Nombre (F) inválida","actualizar");
                validarExpresiones('apellido', $apellido, "Apellido (F) inválida","actualizar");

                // Validar tipo_documento
                if (!validarTipoDocumento($documento)) {
                    MensajeJSON(0,'actualizar','El tipo de documento no es válido - ERROR E520');
                }
        
                    $datosCliente = [
                        'operacion' => 'actualizar',
                        'datos' => [
                            'id_persona' => $_SESSION["id"],
                            'nombre' => $nombre,
                            'apellido' => $apellido,
                            'cedula' => $cedula,
                            'correo' => $correo,
                            'telefono' => $telefono,
                            'tipo_documento' => $documento,
                            'cedula_actual' =>$cedula_actual,
                            'correo_actual' => $correo_actual                        
                        ]
                    ];

                    $resultado = $objdatos->procesarCliente(json_encode($datosCliente));
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
        } else{  /* 2 */ 
            MensajeJSON(0,'actualizar','No puedes realizar esta operacion, intente mas tarde - ERROR E200');
        }  
    } else{ /* V1 */ 
        MensajeJSON(0,'actualizar','Session no encontrada - ERROR E100');
    }     
//------------------------
} else if(isset($_POST['eliminar'])){ // ||||||||||||||||||||||||||||||||||||||||||||||||||||||| ELIMINAR CLIENTE 
//------------------------    
    if (isset($_SESSION['id']) && !empty($_SESSION['id'])) { /* V1 */
      if ($_SESSION["nivel_rol"] == 1 && tieneAcceso(20, 4)) { 
        if(!empty($_POST['persona'])){/* V2 */
            $persona = $_POST['persona']; 
        
            $campos = [
                'Persona' => $persona
            ];
                 /// Sanitización de Entradas
                foreach ($campos as $nombree => $valor) {  /* V3 */ 
                    if (!validarEntradaSQL($valor)) {
                        MensajeJSON(0,'eliminar',"Entrada inválida detectada en el campo: $nombree");
                    }
                }   

                //Validar datos V4
                validarExpresiones('cedula', $persona, "Datos (F) inválida","actualizar");

                if($persona === $_SESSION['id']){
                    $datosCliente = [
                            'operacion' => 'eliminar',
                            'datos' => [
                                'id_usuario' => $_SESSION['id_usuario'],
                                'cedula' => $persona
                            ]
                    ];

                    $resultado = $objdatos->procesarCliente(json_encode($datosCliente));
                        if ($resultado['respuesta'] == 1) {
                            echo json_encode($resultado);
                            session_destroy();
                            exit;
                        } else{
                            echo json_encode($resultado);
                            exit;
                        }

                } else{
                    MensajeJSON(0,'eliminar','La datos de la persona no encontrados');
                }
            } else{
                MensajeJSON(0,'eliminar','Datos Vacios - ERROR E300');
            }
        } else{  /* 2 */ 
            MensajeJSON(0,'eliminar','No puedes realizar esta operacion, intente mas tarde - ERROR E200');
        }  
    } else{ /* V1 */ 
        MensajeJSON(0,'eliminar','Session no encontrada - ERROR E100');
    }  
//------------------------- 
} else if(isset($_POST['actualizarclave'])){ //||||||||||||||||||||||||||||||||||||||||||||||||||||| ACTUALIZAR CLAVE
//-------------------------
    if (isset($_SESSION['id']) && !empty($_SESSION['id'])) { /* V1 */
     if ($_SESSION["nivel_rol"] == 1 && tieneAcceso(20, 3)) { 

        if(!empty($_POST['clave'])&&!empty($_POST['clavenueva'])){ 
            $clave = $_POST['clave']; $clavenueva = $_POST['clavenueva'];

            $campos = [
                'Clave' => $clave,
                'Clavenueva' => $clavenueva
            ];
                 /// Sanitización de Entradas
                foreach ($campos as $nombre => $valor) {  /* V3 */ 
                    if (!validarEntradaSQL($valor)) {
                        MensajeJSON(0,'clave',"Entrada inválida detectada en el campo: $nombre");
                    }
                }   
                    //Validar datos V4
                    validarExpresiones('clave', $clave, "Clave (F) inválida","clave");
                    validarExpresiones('clave', $clavenueva, "Clave Nueva(F) inválida","clave");

                        $datosCliente = [
                            'operacion' => 'actualizarclave',
                                'datos' => [
                                    'id_usuario' => $_SESSION["id_usuario"],
                                    'clave_actual' => $_POST['clave'],
                                    'clave' => $_POST["clavenueva"]
                                ]
                        ];

                        $resultado = $objdatos->procesarCliente(json_encode($datosCliente));
                        echo json_encode($resultado);
                        exit;

            }else{ // datos vacios
                MensajeJSON(0,'clave','Datos Vacios - ERROR E300');
            }
        } else{  /* 2 */ 
            MensajeJSON(0,'clave','No puedes realizar esta operacion, intente mas tarde - ERROR E200');
        }  
    } else{ /* V1 */ 
        MensajeJSON(0,'clave','Session no encontrada - ERROR E100');
    }  
     
}else if ($sesion_activa) {
    if($_SESSION["nivel_rol"] == 1  && tieneAcceso(20, 1))  { 
      require_once('vista/tienda/catalogo_datos.php');
    } else{
        header('Location: ?pagina=catalogo');
    }   
} else {
   header('Location: ?pagina=catalogo');
}

?>


