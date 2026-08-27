<?php
$tasa_bcv = 0;
$api_url = "https://ve.dolarapi.com/v1/dolares/oficial";

$json = @file_get_contents($api_url);
if ($json) {
    $data = json_decode($json, true);
    $tasa_bcv = $data['promedio'] ?? 0;
}
?>

<!--LOADER -->
<div id="app-loader" class="fixed inset-0 z-50 bg-white flex flex-col items-center justify-center transition-opacity duration-500">
    <!-- CONTENEDOR DEL LOGO + SPINNER -->
    <div class="relative flex items-center justify-center w-32 h-32 mb-4">
        <!-- CIRCULO SPINNER -->
        <div class="absolute inset-0 rounded-full border-4 border-pink-100 border-t-pink-500 animate-spin-custom"></div>
        <!-- LOGO AL CENTRO -->
        <div class="w-24 h-24 rounded-full bg-white p-2 shadow-xs flex items-center justify-center z-10">
            <img src="assets/img/img200.png" alt="Logo" class="w-full h-full object-contain">
        </div>
    </div>
    <!-- TEXTO CARGANDO -->
    <div class="flex items-center gap-1">
        <span class="text-sm font-bold text-gray-700 tracking-wider">Cargando</span>
        <!-- Puntos animados -->
        <span class="inline-flex text-pink-500 font-bold text-sm">
            <span class="animate-bounce" style="animation-delay: 0s;">.</span>
            <span class="animate-bounce" style="animation-delay: 0.2s;">.</span>
            <span class="animate-bounce" style="animation-delay: 0.4s;">.</span>
        </span>
    </div>
</div>


  
  <!-- SALUDO + DOLAR -->
   <?php 
        $pagina_actual = $_GET['pagina'] ?? 'catalogo'; 

        // Clases CSS para el estado activo e inactivo
        $clase_activa = "text-accent-fuchsia font-bold border-b-2 border-accent-fuchsia";
        $clase_inactiva = "text-gray-600 hover:text-accent-fuchsia";

        $m_activa = "text-accent-fuchsia font-bold scale-105 transition-all";
        $m_inactiva = "text-gray-500 hover:text-accent-fuchsia transition-colors";
    ?>

    <div class="bg-gradient-to-r from-pink-900 via-accent-fuchsia to-brand-700 text-white text-xs py-2 px-4 shadow-sm">
        <div class="max-w-7xl mx-auto flex flex-wrap justify-between items-center gap-2">
            <div class="flex items-center gap-2">
                <span class="bg-white text-black px-2 py-0.5 rounded-full text-[10px] font-extrabold tracking-wider uppercase">
                    <i class="fa-solid fa-sparkles text-amber-300 mr-1"></i> Bienvenido/a
                </span>
                <span class="opacity-95 font-semibold text-xs sm:text-sm" id="saludo">
                    
                </span>
            </div>
            <div class="flex items-center gap-3 text-[13px] font-medium">
                <button  class="flex items-center gap-1.5 bg-white text-black px-2.5 py-1 rounded-full transition-all">
                    <i class="fa-solid fa-money-bill-1-wave text-black"></i>
                    <span> <strong id="bcv" class="font-bold text-black">Cargando ... </strong> </span>
                </button>
            </div>
        </div>
    </div>

    <!-- HEADER  -->
    <header class="sticky top-0 z-40 glass-header border-b border-pink-100">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between h-20 gap-3">
                
                <!-- LOGO -->
                <div class="flex items-center gap-2">
                    <button onclick="toggleMobileDrawer(true)" class="lg:hidden p-2 text-gray-700 hover:text-accent-fuchsia text-lg rounded-xl hover:bg-pink-50" title="Abrir menú">
                        <i class="fa-solid fa-bars-staggered"></i>
                    </button>

                    <div class="flex-shrink-0" >
                        <a href="?pagina=catalogo" class="flex-shrink-0 cursor-pointer focus:outline-none">
                            <img src="assets/img/logo2.png" alt="LoveMakeup C.A Logo" class="h-12 w-auto object-contain">
                        </a>
                    </div>
                </div>

                <!-- BUSCADOR DE PRODUCTOS -->
                <div class="hidden md:flex flex-1 max-w-md mx-4">
                    <div class="relative w-full">
                        <form id="search-form" class="text-center" action="index.php" method="get">
                        <input type="hidden" name="pagina" value="catalogo_producto">
                        <input type="text" name="busqueda" placeholder="Buscar bases, labiales, kits de skincare..." 
                               class="w-full bg-pink-50/60 border border-pink-200 text-gray-800 text-sm rounded-full pl-10 pr-10 py-2.5 focus:outline-none focus:ring-2 focus:ring-accent-fuchsia/40 focus:border-accent-fuchsia transition-all placeholder:text-gray-400">

                        <i class="fa-solid fa-magnifying-glass absolute left-3.5 top-1/2 -translate-y-1/2 text-pink-400 text-sm"></i>
                        <button class="absolute right-3.5 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600 text-xs hidden" id="clearSearchBtn">
                            <i class="fa-solid fa-circle-xmark"></i>
                        </button>
                        </form>
                    </div>
                </div>

                <!-- DIV INICIO SESSION + CARRITO -->
                <div class="flex items-center gap-1.5 sm:gap-3">
                    <!-- Auth Button (Desk & Mobile) -->
                  
                    <?php if ($sesion_activa): ?>
                        <!-- Si hay sesión activa, muestra el botón de cerrar sesión con otro ícono -->
                            <button onclick="openLogoutModal()" class="help-login flex items-center gap-1.5 text-xs font-semibold text-gray-700 hover:text-accent-fuchsia bg-pink-50 hover:bg-pink-100/80 px-2.5 sm:px-3.5 py-2 rounded-full border border-pink-100 transition-all">
                                <i class="fa-solid fa-right-from-bracket text-base text-accent-fuchsia"></i>
                                <span class="truncate max-w-[90px] sm:max-w-[140px]">Cerrar</span>
                            </button>
                    <?php else: ?>
                        <!-- Si no hay sesion activa, muestra el icono de usuario para iniciar sesion -->
                        <a href="?pagina=login" class="help-login flex items-center gap-1.5 text-xs font-semibold text-gray-700 hover:text-accent-fuchsia bg-pink-50 hover:bg-pink-100/80 px-2.5 sm:px-3.5 py-2 rounded-full border border-pink-100 transition-all">
                            <i class="fa-regular fa-circle-user text-base text-accent-fuchsia"></i>
                            <span  class="truncate max-w-[90px] sm:max-w-[140px]">Iniciar Sesion</span>
                        </a>
                    <?php endif; ?>

                    <?php if ($sesion_activa && $_SESSION["nivel_rol"] == 1): ?>
                        <?php 
                            $pagina = $_GET['pagina'] ?? '';
                            $paginasPermitidas = ['catalogo', 'catalogo_producto', 'vercarrito', 'verpedidoweb'];
                            $paginasOcultas = ['vercarrito', 'verpedidoweb','Pedidoentrega','Pedidopago','Pedidoconfirmar'];
                        ?>

                        <?php if (in_array($pagina,$paginasPermitidas)): ?>
                            <a class="p-2 mx-1" id="btnAyuda" title="Ayuda">
                                <span class="icon text-dark">
                                <i class="fa-solid fa-circle-question"  style="font-size: 25px; color:#004adf; cursor: pointer;"></i>
                                </span>
                                
                            </a>
                        <?php endif; ?>

                        <!-- CARRITO -->
                        <?php if (!in_array($pagina, $paginasOcultas)): ?>
                            <button onclick="toggleCartDrawer(true)" class="help-carrito relative flex items-center gap-1.5 sm:gap-2 bg-gradient-to-r from-accent-fuchsia to-pink-600 hover:from-pink-700 hover:to-accent-fuchsia text-white px-3 sm:px-4 py-2.5 rounded-full shadow-lg shadow-pink-500/20 text-xs font-bold transition-all transform active:scale-95">
                                <i class="fa-solid fa-cart-shopping  text-sm"></i>
                                <span class="hidden sm:inline">Carrito</span>
                                <span id="cartCountBadge" class="bg-white text-accent-fuchsia rounded-full text-[11px] font-extrabold px-1.5 py-0.2 min-w-[20px] text-center"> <?php echo count($carrito); ?>   </span>
                            </button>
                        <?php endif; ?>

                    <?php endif; ?> 

                    <?php if($sesion_activa && $_SESSION["nivel_rol"] == 3) { ?>
                        <a href="?pagina=home" class="relative flex items-center gap-1.5 sm:gap-2 bg-gradient-to-r from-accent-fuchsia to-pink-600 hover:from-pink-700 hover:to-accent-fuchsia text-white px-3 sm:px-4 py-2.5 rounded-full shadow-lg shadow-pink-500/20 text-xs font-bold transition-all transform active:scale-95">
                            <i class="fa-solid fa-share text-sm"></i>
                            <span class="hidden sm:inline">Volver</span>
                        </a>
                    <?php } ?>
                  
                </div>
            </div>

            <!-- barra de busqueda movil -->
            <div class="md:hidden pb-3">
                <div class="relative w-full">
                     <form id="search-form" class="text-center" action="index.php" method="get"> 
                        <input type="hidden" name="pagina" value="catalogo_producto">
                        <input type="text" id="searchInputMobile" name="busqueda" placeholder="Buscar producto, marca o kit..." 
                        class="w-full bg-pink-50/70 border border-pink-200 text-gray-800 text-xs rounded-full pl-9 pr-8 py-2 focus:outline-none focus:ring-2 focus:ring-accent-fuchsia/40">
                        <i class="fa-solid fa-magnifying-glass absolute left-3 top-1/2 -translate-y-1/2 text-pink-400 text-xs"></i>
                    </form>
                </div>
            </div>

            <!-- Barra de navegacion horizontal secundaria para escritorio -->
            <nav class="hidden lg:flex items-center justify-center gap-8 py-3 border-t border-pink-100/60 text-xs font-semibold">
                <!-- Inicio -->
                <a href="?pagina=catalogo" 
                    class="nav-link py-1 flex items-center gap-1.5 transition-colors <?php echo ($pagina_actual === 'catalogo') ? $clase_activa : $clase_inactiva; ?>">
                    <i class="fa-solid fa-house opacity-70"></i> Inicio
                </a>

                <!-- Catálogo Completo -->
                <a href="?pagina=catalogo_producto" 
                    class="nav-link py-1 flex items-center gap-1.5 transition-colors <?php echo ($pagina_actual === 'catalogo_producto') ? $clase_activa : $clase_inactiva; ?>">
                    <i class="fa-solid fa-border-all opacity-70"></i> Catálogo Completo
                </a>

                <!-- Consejos -->
                <a href="?pagina=catalogo_consejo" 
                    class="nav-link py-1 flex items-center gap-1.5 transition-colors <?php echo ($pagina_actual === 'catalogo_consejo') ? $clase_activa : $clase_inactiva; ?>">
                    <i class="fa-solid fa-lightbulb opacity-70"></i> Consejos
                </a>

                <!-- Contactos -->
                <a href="?pagina=catalogo_contacto" 
                    class="nav-link py-1 flex items-center gap-1.5 transition-colors <?php echo ($pagina_actual === 'catalogo_contacto') ? $clase_activa : $clase_inactiva; ?>">
                    <i class="fa-solid fa-headset opacity-70"></i> Contactos
                </a>
                    
                <?php if ($sesion_activa && $_SESSION["nivel_rol"] == 1) { ?>
                    <!-- Mis Favoritos -->
                    <a href="?pagina=listadeseo" 
                        class="nav-link py-1 flex items-center gap-1.5 transition-colors <?php echo ($pagina_actual === 'listadeseo') ? $clase_activa : $clase_inactiva; ?>">
                        <i class="fa-solid fa-heart opacity-70"></i> Mis Favoritos
                    </a>

                    <!-- Mis Pedidos -->
                    <a href="?pagina=catalogo_pedido" 
                        class="nav-link py-1 flex items-center gap-1.5 transition-colors <?php echo ($pagina_actual === 'catalogo_pedido') ? $clase_activa : $clase_inactiva; ?>">
                        <i class="fa-solid fa-receipt opacity-70"></i> Mis Pedidos
                    </a>

                    <!-- Mis Datos -->
                    <a href="?pagina=catalogo_datos" 
                        class="nav-link py-1 flex items-center gap-1.5 transition-colors <?php echo ($pagina_actual === 'catalogo_datos') ? $clase_activa : $clase_inactiva; ?>">
                        <i class="fa-solid fa-user-gear opacity-70"></i> Mis Datos
                    </a>
                <?php } else ?>
            </nav>
        </div>
    </header>

    <!-- Menu de navegacion lateral para moviles -->
    <div id="mobileDrawer" class="fixed inset-0 z-50 overflow-hidden pointer-events-none transition-opacity duration-300 opacity-0">
        <div class="absolute inset-0 bg-gray-900/50 backdrop-blur-xs transition-opacity" onclick="toggleMobileDrawer(false)"></div>
        <div class="fixed inset-y-0 left-0 max-w-full flex pr-10">
            <div id="mobileDrawerPanel" class="w-screen max-w-xs bg-white shadow-2xl transform -translate-x-full transition-transform duration-300 pointer-events-auto flex flex-col justify-between">
                <div>
                    <!-- Drawer Header -->
                    <div class="p-5 border-b border-pink-100 flex items-center justify-between bg-gradient-to-r from-pink-50 to-pink-100">
                        <div class="flex items-center gap-3">
                           <div class="w-10 h-10 rounded-xl overflow-hidden flex items-center justify-center bg-pink-50 p-0.5 border border-pink-100">
                                <img src="assets/img/img200.png" alt="LoveMakeup" class="w-full h-full object-contain">
                            </div>
                            <div>
                                <h3 class="font-extrabold text-gray-900 text-sm">LoveMakeup C.A</h3>
                        
                                    <?php if ($sesion_activa): ?>
                                        <?php if (isset($_GET['pagina']) && $_GET['pagina'] === 'listadeseo'): ?>
                                            <p class="text-[10px] text-pink-600 font-semibold drawerUserStatus">Cliente</p>
                                        <?php else: ?>
                                            <p class="text-[10px] text-pink-600 font-semibold drawerUserStatus">
                                                Cliente: <?php echo htmlspecialchars($nombreCompleto ?? '', ENT_QUOTES, 'UTF-8'); ?>
                                            </p>
                                        <?php endif; ?>
                                    <?php else: ?>
                                        <p class="text-[10px] text-pink-600 font-semibold drawerUserStatus">Sesión no iniciada</p>
                                    <?php endif; ?>
                               
                            </div>
                        </div>
                        <button onclick="toggleMobileDrawer(false)" class="w-8 h-8 rounded-full bg-white text-gray-400 hover:text-gray-700 flex items-center justify-center text-sm shadow-xs">
                            <i class="fa-solid fa-xmark"></i>
                        </button>
                    </div>

                    <!-- Navigation Items -->
                    <div class="p-4 space-y-1 text-sm font-semibold text-gray-700">
                        <a href="?pagina=catalogo"  class="w-full text-left px-4 py-3 rounded-2xl hover:bg-pink-50 hover:text-accent-fuchsia flex items-center gap-3">
                            <i class="fa-solid fa-house w-5 text-accent-fuchsia"></i> Inicio
                        </a>
                        <a href="?pagina=catalogo_producto"  class="w-full text-left px-4 py-3 rounded-2xl hover:bg-pink-50 hover:text-accent-fuchsia flex items-center gap-3">
                            <i class="fa-solid fa-border-all w-5 text-accent-fuchsia"></i> Catalogo de Productos
                        </a>
                        
                        <a href="?pagina=catalogo_consejo"  class="w-full text-left px-4 py-3 rounded-2xl hover:bg-pink-50 hover:text-accent-fuchsia flex items-center gap-3">
                            <i class="fa-solid fa-lightbulb w-5 text-accent-fuchsia"></i> Consejos
                        </a>
                        <a href="?pagina=catalogo_contacto"  class="w-full text-left px-4 py-3 rounded-2xl hover:bg-pink-50 hover:text-accent-fuchsia flex items-center gap-3">
                            <i class="fa-solid fa-headset w-5 text-accent-fuchsia"></i> Contactos
                        </a>

                        <?php if ($sesion_activa && $_SESSION["nivel_rol"] == 1){  ?>
                            <div class="my-2 border-t border-pink-100"> </div>

                            <a href="?pagina=listadeseo"  class="w-full text-left px-4 py-3 rounded-2xl hover:bg-pink-50 hover:text-accent-fuchsia flex items-center justify-between">
                                <span class="flex items-center gap-3"><i class="fa-solid fa-heart w-5 text-accent-fuchsia"></i> Mis Favoritos</span>
                            </a>
                            <a href="?pagina=catalogo_pedido"  class="w-full text-left px-4 py-3 rounded-2xl hover:bg-pink-50 hover:text-accent-fuchsia flex items-center gap-3">
                                <i class="fa-solid fa-receipt w-5 text-accent-fuchsia"></i> Mis Pedidos
                            </a>
                            <a href="?pagina=catalogo_datos"  class="w-full text-left px-4 py-3 rounded-2xl hover:bg-pink-50 hover:text-accent-fuchsia flex items-center gap-3">
                                <i class="fa-solid fa-user-gear w-5 text-accent-fuchsia"></i> Mis Datos Personales
                            </a>
                        <?php } else if($sesion_activa && $_SESSION["nivel_rol"] == 3){ ?>
                            <div class="my-2 border-t border-pink-100"> </div>

                            <a href="?pagina=home"  class="w-full text-left px-4 py-3 rounded-2xl hover:bg-pink-50 hover:text-accent-fuchsia flex items-center justify-between">
                                <span class="flex items-center gap-3"><i class="fa-solid fa-share w-5 text-accent-fuchsia"></i> volver </span>
                            </a>
                        <?php } ?>
                    </div>
                </div>

                <div class="p-4 border-t border-pink-100 bg-pink-50/50">
                    <?php if ($sesion_activa): ?>
                        <!-- Si hay sesión activa, muestra el botón de cerrar sesión con otro ícono -->
                        <button onclick="openLogoutModal()" class="w-full flex items-center justify-center bg-accent-fuchsia text-white py-3 rounded-2xl font-bold text-xs shadow-md hover:bg-pink-700 transition-all">
                            <i class="fa-solid fa-right-from-bracket mr-1.5"></i> Cerrar sesión
                        </button>
                        
                    <?php else: ?>
                        <!-- Si no hay sesion activa, muestra el icono de usuario para iniciar sesion -->
                        <a href="?pagina=login" class="w-full flex items-center justify-center bg-accent-fuchsia text-white py-3 rounded-2xl font-bold text-xs shadow-md hover:bg-pink-700 transition-all">
                            <i class="fa-regular fa-circle-user  mr-1.5"></i> Iniciar Sesión / Registro
                        </a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>



 <script src="assets/js/Tasa.js"></script>
 <script>
    // mostrarTasa
    document.addEventListener('DOMContentLoaded', () => {
        const tasaGuardada = localStorage.getItem('app_tasa_dolar');

        setTimeout(() => {
            const bcvElement = document.getElementById('bcv');
            const bcvFallback = document.getElementById('bcvFallback');

            if (bcvElement) {
                const contenido = bcvElement.textContent.trim().toLowerCase();
                const esError = contenido === "" || contenido.includes("error al cargar la tasa");

                if (esError) {
                    bcvElement.style.display = "none";

                    if (bcvFallback) {
                        if (tasaGuardada) {
                            bcvFallback.textContent = `Sin Conexión. Última tasa guardada: Bs ${tasaGuardada}`;
                        } else {
                            bcvFallback.textContent = "Sin Conexión. Tasa no disponible.";
                        }
                        bcvFallback.style.display = "block";
                    }
                }
            }
        }, 1000); 
    });
</script>