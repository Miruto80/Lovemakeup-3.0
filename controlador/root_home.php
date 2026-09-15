<?php  
// Iniciar sesión solo si no está ya iniciada
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}


if (!empty($_SESSION['id'])) {
    require_once 'verificarsession.php';
} 
    
 use LoveMakeup\Proyecto\Modelo\Homeroot;
 use LoveMakeup\Proyecto\Modelo\Reporte;
 
 require_once 'permiso.php';



$objhome = new Homeroot();

$accion = $_POST['accion'] ?? $_GET['accion'] ?? null;
if ($accion === 'obtener_metricas') {
    if (ob_get_length()) {
        ob_clean();
    }

    header('Content-Type: application/json; charset=utf-8');
    header('Cache-Control: no-cache, must-revalidate');

    echo json_encode([
        'metricas' => $objhome->obtenerMetricasSistema(),
        'procesos' => $objhome->obtenerProcesosActivos(),
        'bd_negocio'   => $objhome->obtenerInfoBDNegocio(),
        'bd_seguridad' => $objhome->obtenerInfoBDSeguridad()
    ], JSON_UNESCAPED_UNICODE);

    exit(); // Detiene la ejecución para NO cargar el HTML de abajo
}


if (isset($_SESSION["nombre_usuario"]) && $_SESSION["nombre_usuario"] === "Desarrollador") {
    $pagina_actual = isset($_GET['pagina']) ? $_GET['pagina'] : 'home';
    require_once 'vista/root/home.php';
} else {
    header("Location: ?pagina=home");
    exit();
}



?>