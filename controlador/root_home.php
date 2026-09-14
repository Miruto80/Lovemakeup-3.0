<?php  
// Iniciar sesión solo si no está ya iniciada
if (session_status() === PHP_SESSION_NONE) {
session_start();
}


if (!empty($_SESSION['id'])) {
    require_once 'verificarsession.php';
} 

    
 use LoveMakeup\Proyecto\Modelo\Home;
 use LoveMakeup\Proyecto\Modelo\Reporte;
 
 require_once 'permiso.php';



$objhome = new Home();

$registro = $objhome->consultarMasVendidos();

$totales = $objhome->consultarTotales();

$pendientes=$objhome->consultarTotalesPendientes();

$graficaHome = Reporte::graficaVentaTop5(); 


  

if ($_SESSION["nombre_usuario"] == "Desarrollador") {
    $pagina_actual = isset($_GET['pagina']) ? $_GET['pagina'] : 'home';
    require_once 'vista/root/home.php';
} else{
    header("Location: ?pagina=home");
    exit();
}



?>