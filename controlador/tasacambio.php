<?php
use LoveMakeup\Proyecto\Modelo\TasaCambio;   
use LoveMakeup\Proyecto\Modelo\Bitacora; 

// Iniciar sesión solo si no está ya iniciada
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!empty($_SESSION['id'])) {
    require_once 'verificarsession.php';
} 

require_once 'permiso.php';
require_once 'assets/ajuste/validaciones.php';

$objtasa = new TasaCambio();
/**
 * FUNCION PARA REGISTRAR LA BITACORA
 */
function registrarBitacora($accion, $descripcion) {
    $datos = [
        'id_persona'  => $_SESSION["id"],
        'accion'      => $accion,
        'descripcion' => $descripcion
    ];

    // Instanciamos y registramos
    $bitacoraObj = new Bitacora();
    return $bitacoraObj->registrarOperacion($accion, 'Tasa de Cambio', $datos);
}
//---
$registro = $objtasa->consultar();
//---
if(isset($_POST['modificar'])){
//---    
    if (isset($_SESSION['id']) && !empty($_SESSION['id'])) { /* V1 */
        if ($_SESSION["nivel_rol"] == 3 && tieneAcceso(14, 3)) { /* V2 */ 
            
            if (!empty($_POST['fecha']) && !empty($_POST['tasa']) && !empty($_POST['fuente'])) {  /* V3 */

                // Validar y sanitizar datos
                $fecha = $_POST['fecha'];
                $tasa = isset($_POST['tasa']) ? filter_var($_POST['tasa'], FILTER_VALIDATE_FLOAT) : false;
                $fuente = $_POST['fuente'];

                if ($tasa === false || $tasa <= 0) {
                    MensajeJSON(0, 'modificar', 'La tasa debe ser un número válido mayor a 0');
                }

                $campos = [
                    'Fecha' => $fecha,
                    'Tasa' => $tasa,      
                    'Fuente' => $fuente
                ];

                /// Sanitización de Entradas
                foreach ($campos as $nombre => $valor) {  /* V4 */ 
                    if (!validarEntradaSQL($valor)) {
                        MensajeJSON(0, 'modificar', "Entrada inválida detectada en el campo: $nombre");
                    }
                }
                
                // Validar formato de fecha
                $fechaValidada = DateTime::createFromFormat('Y-m-d', $fecha);
                if (!$fechaValidada || $fechaValidada->format('Y-m-d') !== $fecha) {
                    MensajeJSON(0, 'modificar', 'Formato de fecha inválido - ERROR E520');
                }

                validarExpresiones('dolar', $tasa, "Tasa (F) inválido", "modificar");

                $datosTasa = [
                    'operacion' => 'modificar',
                    'datos' => [
                        'fecha' => $fecha,
                        'tasa' => $tasa,
                        'fuente' => $fuente
                    ]
                ]; 

                $resultado = $objtasa->procesarTasa(json_encode($datosTasa));

                if($resultado['respuesta'] == 1){
                    $resultado1 = $objtasa->consultaTasaUltima();
                    if (!empty($resultado1)) {
                        $_SESSION["tasa"] = $resultado1;
                    }
                    registrarBitacora('Modificar Tasa de Cambio', "Se Modifico la tasa manualmente, a: {$tasa}");    
                }

                echo json_encode($resultado);

            } else{  /* V3 datos vacios */
                MensajeJSON(0, 'modificar', 'Datos Vacios - ERROR E300');
            }    
        } else{  /* 2 */ 
            MensajeJSON(0, 'modificar', 'No Tiene Permiso para realizar esta operacion - ERROR E200');
        }      
    } else{ /* V1 */ 
        MensajeJSON(0, 'modificar', 'Session no encontrada - ERROR E100');
    } 

} else if(isset($_POST['sincronizar'])){
    if (isset($_SESSION['id']) && !empty($_SESSION['id'])) { /* V1 */
        if ($_SESSION["nivel_rol"] == 3 && tieneAcceso(14, 3)) { /* V2 */ 
            
            if (!empty($_POST['fecha']) && !empty($_POST['tasa']) && !empty($_POST['fuente'])) {  /* V3 */

                // Validar y sanitizar datos
                $fecha = $_POST['fecha'];
                $tasa = isset($_POST['tasa']) ? filter_var($_POST['tasa'], FILTER_VALIDATE_FLOAT) : false;
                $fuente = $_POST['fuente'];
                
                // Validaciones
                if(empty($tasa) || $tasa === false){
                    MensajeJSON(0, 'sincronizar', 'Tasa no encontrada o inválida');
                } 
                
                if($tasa <= 0){
                    MensajeJSON(0, 'sincronizar', 'La tasa debe ser mayor a 0');
                }
                
                $campos = [
                    'Fecha' => $fecha,
                    'Tasa' => $tasa,      
                    'Fuente' => $fuente
                ];

                /// Sanitización de Entradas
                foreach ($campos as $nombre => $valor) {  /* V4 */ 
                    if (!validarEntradaSQL($valor)) {
                        MensajeJSON(0, 'sincronizar', "Entrada inválida detectada en el campo: $nombre");
                    }
                }
                
                // Validar formato de fecha
                $fechaValidada = DateTime::createFromFormat('Y-m-d', $fecha);
                if (!$fechaValidada || $fechaValidada->format('Y-m-d') !== $fecha) {
                    echo json_encode(['respuesta' => 0, 'accion' => 'sincronizar', 'text' => 'Formato de fecha inválido']);
                    exit;
                }

                validarExpresiones('dolar', $tasa, "Tasa (F) inválido", "sincronizar");

                $datosTasa = [
                    'operacion' => 'sincronizar',
                    'datos' => [
                        'fecha' => $fecha,
                        'tasa' => $tasa,
                        'fuente' => $fuente
                    ]
                ]; 

                $resultado = $objtasa->procesarTasa(json_encode($datosTasa));
                if($resultado['respuesta'] == 1){
                    $resultado1 = $objtasa->consultaTasaUltima();
                    if (!empty($resultado1)) {
                        $_SESSION["tasa"] = $resultado1;
                    }
                    registrarBitacora('Modificar Tasa de Cambio', "Se Modifico la tasa via Internet, a: {$tasa}");    
                }
                echo json_encode($resultado);
                exit;

            } else{  /* V3 datos vacios */
                MensajeJSON(0, 'sincronizar', 'Datos Vacios - ERROR E200');
            }

        } else{  /* 2 */ 
            MensajeJSON(0, 'sincronizar', 'No Tiene Permiso para realizar esta operacion - ERROR E200');
        }     

    } else{ /* V1 */ 
        MensajeJSON(0, 'sincronizar', 'Session no encontrada - ERROR E100');
    } 
    
} else if(isset($_POST['obtener_tasa_actual']) || (isset($_GET['obtener_tasa_actual']) && $_GET['obtener_tasa_actual'] == '1')) {
    // Endpoint para obtener la tasa actual desde la base de datos
    header('Content-Type: application/json; charset=utf-8');
    try {
        $tasa = $objtasa->obtenerTasaActual();
        if ($tasa && isset($tasa['tasa_bs'])) {
            echo json_encode([
                'respuesta' => 1,
                'tasa' => floatval($tasa['tasa_bs']),
                'fecha' => $tasa['fecha'],
                'fuente' => $tasa['fuente'] ?? 'Base de datos'
            ]);
        } else {
            echo json_encode([
                'respuesta' => 0,
                'mensaje' => 'No se encontró una tasa de cambio en la base de datos'
            ]);
        }
    } catch (\Exception $e) {
        echo json_encode([
            'respuesta' => 0,
            'mensaje' => 'Error al obtener la tasa de cambio: ' . $e->getMessage()
        ]);
    }
    exit;
    
} else if ($_SESSION["nivel_rol"] >= 2 && tieneAcceso(14, 1)) {
    registrarBitacora('Acceso a Módulo Tasa Cambio', "Entro al módulo de Tasa Cambio");
    $pagina_actual = isset($_GET['pagina']) ? $_GET['pagina'] : 'tasaCambio';
    require_once 'vista/tasacambio.php'; // Asegúrate de tener esta vista
} else {
    require_once 'vista/seguridad/privilegio.php';
}