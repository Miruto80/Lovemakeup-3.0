 <!-- ENHANCED MOBILE STICKY BOTTOM NAVIGATION BAR --> 
    <div class="lg:hidden fixed bottom-0 left-0 right-0 z-40 bg-white/95 backdrop-blur-md border-t border-pink-100 px-2 py-1.5 shadow-lg">
        <div class="grid grid-cols-5 text-center text-[10px] font-semibold text-gray-500">
           <?php if ($sesion_activa && $_SESSION["nivel_rol"] == 1): ?>
                <!-- INICIO -->
                <a href="?pagina=catalogo" class="flex flex-col items-center py-1 <?= ($pagina_actual == 'catalogo') ? $m_activa : $m_inactiva ?>">
                    <i class="fa-solid fa-house text-base mb-0.5"></i>
                    <span class="text-[11px]">Inicio</span>
                </a>
                <!-- CATÁLOGO -->
                <a href="?pagina=catalogo_producto" class="flex flex-col items-center py-1 <?= ($pagina_actual == 'catalogo_producto') ? $m_activa : $m_inactiva ?>">
                    <i class="fa-solid fa-border-all text-base mb-0.5"></i>
                    <span class="text-[11px]">Catálogo</span>
                </a>
                <!-- CARRITO -->
                <a href="?pagina=vercarrito" class="flex flex-col items-center py-1 <?= ($pagina_actual == 'vercarrito') ? $m_activa : $m_inactiva ?>">
                    <i class="fa-solid fa-cart-shopping text-base mb-0.5"></i>
                    <span class="text-[11px]">Carrito</span>
                </a>
                <!-- PEDIDOS -->
                <a href="?pagina=catalogo_pedido" class="flex flex-col items-center py-1 <?= ($pagina_actual == 'catalogo_pedido') ? $m_activa : $m_inactiva ?>">
                    <i class="fa-solid fa-receipt text-base mb-0.5"></i>
                    <span class="text-[11px]">Pedidos</span>
                </a>
                <!-- DRAWER / MENÚ -->
                <a onclick="toggleMobileDrawer(true)" class="flex flex-col items-center py-1 text-gray-500 hover:text-accent-fuchsia cursor-pointer">
                    <i class="fa-solid fa-bars text-base mb-0.5"></i>
                    <span class="text-[11px]">Menú</span>
                </a>
            <?php else: ?>
                <!-- INICIO -->
                <a href="?pagina=catalogo" class="flex flex-col items-center py-1 <?= ($pagina_actual == 'catalogo') ? $m_activa : $m_inactiva ?>">
                    <i class="fa-solid fa-house text-base mb-0.5"></i>
                    <span class="text-[11px]">Inicio</span>
                </a>
                <!-- CATALOGO -->
                <a href="?pagina=catalogo_producto" class="flex flex-col items-center py-1 <?= ($pagina_actual == 'catalogo_producto') ? $m_activa : $m_inactiva ?>">
                    <i class="fa-solid fa-border-all text-base mb-0.5"></i>
                    <span class="text-[11px]">Catálogo</span>
                </a>
                <!-- CONSEJOS -->
                <a href="?pagina=catalogo_consejo" class="flex flex-col items-center py-1 <?= ($pagina_actual == 'catalogo_consejo') ? $m_activa : $m_inactiva ?>">
                    <i class="fa-solid fa-lightbulb text-base mb-0.5"></i>
                    <span class="text-[11px]">Consejos</span>
                </a>
                <!-- CONTACTO -->
                <a href="?pagina=catalogo_contacto" class="flex flex-col items-center py-1 <?= ($pagina_actual == 'catalogo_contacto') ? $m_activa : $m_inactiva ?>">
                    <i class="fa-solid fa-headset text-base mb-0.5"></i>
                    <span class="text-[11px]">Contacto</span>
                </a>
                <!-- MENÚ -->
                <a onclick="toggleMobileDrawer(true)" class="flex flex-col items-center py-1 text-gray-500 hover:text-accent-fuchsia cursor-pointer">
                    <i class="fa-solid fa-bars text-base mb-0.5"></i>
                    <span class="text-[11px]">Menú</span>
                </a>
        <?php endif; ?>
        </div>
    </div>
 
 
 <!-- FOOTER -->
    <footer class="bg-black text-gray-300 pt-12 pb-20 lg:pb-8 border-t border-gray-800 mt-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 md:grid-cols-12 gap-8 pb-8 border-b border-gray-800">
                
                <div class="md:col-span-5 space-y-3">
                    <!-- LOGO COMO IMAGEN -->
                    <a href="?pagina=catalogo" class="inline-block">
                        <img src="assets/img/img200.png" alt="LoveMakeup Logo" class="h-40 w-auto object-contain">
                    </a>
                    <p class="text-xs text-gray-400 max-w-sm leading-relaxed">
                        RIF: J-50543440-3
                    </p>
                    <p class="text-xs text-gray-400 max-w-sm leading-relaxed">
                        Tu tienda de cosméticos y belleza favorita en Barquisimeto. Envíos garantizados a toda Venezuela y atención personalizada.
                    </p>

                    <div class="flex gap-3 text-base text-pink-400 pt-1">
                        <a href="https://www.instagram.com/lovemakeupyk/" target="_blank" class="w-8 h-8 rounded-full bg-gray-800 hover:bg-accent-fuchsia hover:text-white flex items-center justify-center transition-all"><i class="fa-brands fa-instagram"></i></a>
                        <a href="https://www.facebook.com/lovemakeupyk/"  target="_blank"class="w-8 h-8 rounded-full bg-gray-800 hover:bg-accent-fuchsia hover:text-white flex items-center justify-center transition-all"><i class="fa-brands fa-facebook"></i></a>
                        <a href="https://www.tiktok.com/@lovemakeupyk?_r=1&_t=ZS-994Rs9W3mlh"  target="_blank" class="w-8 h-8 rounded-full bg-gray-800 hover:bg-accent-fuchsia hover:text-white flex items-center justify-center transition-all"><i class="fa-brands fa-tiktok"></i></a>
                        <a href="https://wa.link/0e2clu"  target="_blank" class="w-8 h-8 rounded-full bg-gray-800 hover:bg-accent-fuchsia hover:text-white flex items-center justify-center transition-all"><i class="fa-brands fa-whatsapp"></i></a>
                    </div>
                </div>

                <div class="md:col-span-2 space-y-2 text-xs">
                    <h4 class="font-bold text-white uppercase tracking-wider mb-2">Navegación</h4>
                    <ul class="space-y-1.5 text-gray-400">
                        <li><a href="?pagina=catalogo" class="hover:text-pink-400">Inicio</a></li>
                        <li><a href="?pagina=catalogo_producto" class="hover:text-pink-400">Catálogo de Productos</a></li>
                        <li><a href="?pagina=catalogo_consejo" class="hover:text-pink-400">Consejos</a></li>
                        <li><a href="?pagina=catalogo_contacto" class="hover:text-pink-400">Contacto</a></li>
                        <li><a href="?pagina=listadeseo" class="hover:text-pink-400">Mis Favoritos</a></li>
                        <li><a href="?pagina=pedido" class="hover:text-pink-400">Mis Pedidos</a></li>
                        <li><a href="?pagina=datos" class="hover:text-pink-400">Mis Datos Personales</a></li>
                    </ul>
                </div>

                <div class="md:col-span-2 space-y-2 text-xs">
                    <h4 class="font-bold text-white uppercase tracking-wider mb-2">Información</h4>
                    <ul class="space-y-1.5 text-gray-400">
                        <li><a href="#" class="hover:text-pink-400">FAQ</a></li>
                        <li><a href="?pagina=aviso_legal" class="hover:text-pink-400">Aviso Legal</a></li>
                        <li><a href="#" class="hover:text-pink-400">Politica de Privacidad</a></li>
                        <li><a href="#" class="hover:text-pink-400">Politica de cookies</a></li>
                    </ul>
                </div>

                <div class="md:col-span-3 space-y-2 text-xs">
                    <h4 class="font-bold text-white uppercase tracking-wider mb-2">Contacto & Tienda</h4>
                    <p class="text-gray-400">Ubicada en la av 20 con calles 29 y 30 CC Barquisimeto plaza, Estado Lara, Venezuela.</p>
                    <p class="text-gray-400">WhatsApp: +58 424 5115414</p>
                </div>

            </div>

            <div class="pt-6 flex flex-col sm:flex-row justify-between items-center text-[11px] text-white gap-2">
                <p>&copy; 2026 Estudiante UPTAEB T4 | LoveMakeup C.A. Todos los derechos reservados.</p>
                <div class="flex items-center gap-3 text-gray-400">
                    <span class="flex items-center gap-1"><i class="fa-solid fa-mobile-screen-button text-xs"></i> Pago Móvil</span>
                </div>
            </div>
        </div>
    </footer>

      <!-- MODAL: AVISO CERRAR SESIÓN -->
        <div id="logoutModal" class="fixed inset-0 z-50 flex items-center justify-center bg-gray-900/60 backdrop-blur-xs hidden p-4">
            <div class="bg-white rounded-3xl max-w-sm w-full p-6 shadow-2xl border border-pink-100 space-y-4 text-center relative">
                
                <!-- Botón Cerrar (X) -->
                <button type="button" onclick="closeLogoutModal()" class="absolute top-4 right-4 w-8 h-8 rounded-full bg-pink-50 text-gray-400 hover:text-gray-800 flex items-center justify-center text-sm transition-colors">
                    <i class="fa-solid fa-xmark"></i>
                </button>
                
                <!-- Icono Despedida -->
                <div class="w-14 h-14 rounded-full bg-red-50 text-red-500 mx-auto flex items-center justify-center text-2xl mt-2">
                    <i class="fa-solid fa-arrow-right-from-bracket"></i>
                </div>
                
                <!-- Textos -->
                <div>
                    <h1 class="font-black text-black text-xl">¿Cerrar Sesión?</h1>
                    <p class="text-lg text-black mt-2 px-2">¿Estás segura de que deseas salir de tu cuenta?</p>
                </div>
                
                <!-- Botones de Acción -->
                <div class="flex gap-3 pt-3">
                    <button type="button" onclick="closeLogoutModal()" class="flex-1 bg-gray-500 text-white font-bold py-2.5 rounded-2xl text-xs hover:bg-gray-400 transition-all">
                        Cancelar
                    </button>
                <form action="?pagina=catalogo" method="POST" autocomplete="off" class="flex-1">
                        <button type="submit" name="cerrar" class="w-full bg-emerald-500 text-white font-bold py-2.5 rounded-2xl text-xs shadow-md hover:bg-emerald-900 transition-all">
                            Sí, salir
                        </button>
                    </form>
                 
                </div>
                
            </div>
        </div>

        <!-- Botón Volver Arriba -->
        <button id="btnVolverArriba" 
                type="button" 
                class="fixed bottom-20 right-6 z-10 hidden bg-accent-fuchsia text-white hover:bg-pink-900  p-3.5 rounded-full shadow-lg transition-all duration-300 transform hover:scale-110 focus:outline-none"
                title="Volver arriba">
        <i class="fa-solid fa-arrow-up text-lg"></i>
        </button>

        <script src="assets/js/catalago/jquery-1.11.0.min.js"></script>
        <script src="assets/js/catalago/catalogo.js?v=<?= filemtime('assets/js/catalago/catalogo.js') ?>"></script>
        
        <script>
            function obtenerSaludo(nombreCompleto) {
                    var hora = moment().hour(); // Obtiene la hora actual con Moment.js
                    var saludo = "";
        
                    if (hora >= 6 && hora < 12) {
                        saludo = "Buenos días <i class='fa-solid fa-cloud-sun'></i>, " + nombreCompleto;
                    } else if (hora >= 12 && hora < 18) {
                        saludo = "Buenas tardes <i class='fa-solid fa-sun'></i>, " + nombreCompleto;
                    } else {
                        saludo = "Buenas noches <i class='fa-solid fa-moon'></i>, " + nombreCompleto;
                    }
        
                    document.getElementById("saludo").innerHTML = saludo;
                   // document.getElementById("saludos").innerHTML = saludo;
                }
        
                var nombreUsuario = "<?php echo $nombreCompleto; ?>"; // Usa el nombre y apellido de sesión o valores por defecto
            obtenerSaludo(nombreUsuario)
        </script>
        
                