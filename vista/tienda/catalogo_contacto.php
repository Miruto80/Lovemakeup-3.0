<!DOCTYPE html>
<html lang="es">
<head>
    <?php include 'vista/complementos/head_catalogo.php' ?>
    <title>Contactos | LoveMakeup C.A </title> 
</head>
<body class="min-h-screen flex flex-col pb-20 lg:pb-0">

    <!-- php CARRITO--> 
<?php include 'vista/complementos/carrito.php' ?>

  <?php include 'vista/complementos/nav_catalogo.php' ?>

    <!-- MAIN CONTENT CONTAINER -->
    <main class="flex-grow max-w-7xl w-full mx-auto px-4 sm:px-6 lg:px-8 py-6">

        <!--  contactos -->
        <section id="contactos" class="tab-content">
            <div class="mb-6">
                <h2 class="text-2xl font-bold text-gray-900">Ubicación</h2>
                <p class="text-s text-gray-700 mt-0.5">Visítanos en nuestra tienda fisica </p>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
                <!-- Details Card -->
                <div class="lg:col-span-5 bg-white border border-pink-100 rounded-3xl p-6 sm:p-8 shadow-sm flex flex-col justify-between">
                    <div>
                        <span class="inline-block bg-gray-900 text-white text-[10px] font-bold px-2.5 py-1 rounded-full uppercase tracking-wider mb-4">
                            Sede Principal
                        </span>
                        <h3 class="text-xl font-bold text-gray-900 mb-3">LoveMakeup C.A Barquisimeto</h3>
                        
                        <div class="space-y-4 text-xs text-gray-600">
                            <div class="flex items-start gap-3">
                                <i class="fa-solid fa-location-dot text-pink-500 text-base mt-0.5"></i>
                                <div class="text-black">
                                    <strong class="text-black block">Dirección Comercial:</strong>
                                    Ubicada en la av 20 con calles 29 y 30 CC Barquisimeto plaza, Estado Lara, Venezuela.
                                </div>
                            </div>
                            <div class="flex items-start gap-3">
                                <i class="fa-solid fa-clock text-pink-500 text-base mt-0.5"></i>
                                <div class="text-black">
                                    <strong class="text-gray-900 block">Horario de Atención:</strong>
                                    Lunes a Sábado: 9:00 AM - 5:00 PM<br>
                                    Domingos: 9:00 AM - 2:00 PM
                                </div>
                            </div>
                            
                        </div>
                    </div>

                    <div class="mt-8 pt-4 border-t border-pink-100">
                        <a href="https://maps.app.goo.gl/WvERpZkzNKyCX5nL9" target="_blank" class="w-full bg-black hover:bg-gray-700 text-white  font-bold py-3 px-4 rounded-2xl text-xs flex items-center justify-center gap-2 transition-all">
                            <i class="fa-solid fa-map-location-dot"></i> Abrir en Google Maps
                        </a>
                    </div>
                </div>

                <!-- Google Maps Embed -->
                <div class="lg:col-span-7 bg-white border border-pink-100 rounded-3xl overflow-hidden min-h-[350px] shadow-sm">
                    <iframe class="w-full h-full min-h-[350px] border-0" 
                            src="https://www.google.com/maps/embed?pb=!1m14!1m8!1m3!1d3928.392977210938!2d-69.3236822!3d10.0668507!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x8e8767c1ba2d21fb%3A0x6864564ca75c44e4!2sBarquisimeto%20Plaza!5e0!3m2!1ses!2sve!4v1747239622868!5m2!1ses!2sve" 
                            allowfullscreen="" loading="lazy"></iframe>
                </div>
            </div>
            
        </section>

        <!-- CONTACTO -->
      <section id="view-contactos" class="tab-content pt-7">
        <div class="mb-6">
            <h2 class="text-2xl font-bold text-gray-900">Atención al Cliente Y Contacto</h2>
            <p class="text-s text-gray-700 mt-0.5">¿Tienes dudas con la elección de un tono o pedido? Escríbenos directamente</p>
        </div>

    
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
            
            <!-- 1 WhatsApp -->
            <a href="https://wa.link/0e2clu" target="_blank" rel="noopener noreferrer" 
            class="bg-white border border-pink-100 rounded-3xl p-5 shadow-sm hover:shadow-md hover:border-emerald-200 transition-all duration-300 flex items-center gap-4 group">
                <div class="w-12 h-12 rounded-2xl bg-emerald-50 text-emerald-600 text-xl flex items-center justify-center flex-shrink-0 group-hover:scale-110 transition-transform">
                    <i class="fa-brands fa-whatsapp"></i>
                </div>
                <div class="overflow-hidden">
                    <span class="text-[10px] font-bold uppercase text-gray-400 block">Atención Inmediata</span>
                    <h4 class="font-bold text-gray-900 text-sm group-hover:text-emerald-600 transition-colors">WhatsApp Oficial</h4>
                    <p class="text-xs text-gray-600 font-semibold truncate">+58 424 511 5414</p>
                </div>
            </a>

            <!-- 2 Instagram -->
            <a href="https://www.instagram.com/lovemakeupyk/" target="_blank" rel="noopener noreferrer" 
            class="bg-white border border-pink-100 rounded-3xl p-5 shadow-sm hover:shadow-md hover:border-pink-200 transition-all duration-300 flex items-center gap-4 group">
                <div class="w-12 h-12 rounded-2xl bg-pink-50 text-pink-600 text-xl flex items-center justify-center flex-shrink-0 group-hover:scale-110 transition-transform">
                    <i class="fa-brands fa-instagram"></i>
                </div>
                <div class="overflow-hidden">
                    <span class="text-[10px] font-bold uppercase text-gray-400 block">Red Social</span>
                    <h4 class="font-bold text-gray-900 text-sm group-hover:text-pink-600 transition-colors">Instagram</h4>
                    <p class="text-xs text-gray-600 font-semibold truncate">@lovemakeupyk</p>
                </div>
            </a>

            <!-- 3 TikTok -->
            <a href="https://www.tiktok.com/@lovemakeupyk" target="_blank" rel="noopener noreferrer" 
            class="bg-white border border-pink-100 rounded-3xl p-5 shadow-sm hover:shadow-md hover:border-gray-300 transition-all duration-300 flex items-center gap-4 group">
                <div class="w-12 h-12 rounded-2xl bg-gray-100 text-gray-900 text-xl flex items-center justify-center flex-shrink-0 group-hover:scale-110 transition-transform">
                    <i class="fa-brands fa-tiktok"></i>
                </div>
                <div class="overflow-hidden">
                    <span class="text-[10px] font-bold uppercase text-gray-400 block">Red Social</span>
                    <h4 class="font-bold text-gray-900 text-sm group-hover:text-gray-700 transition-colors">TikTok</h4>
                    <p class="text-xs text-gray-600 font-semibold truncate">@lovemakeupyk</p>
                </div>
            </a>

            <!-- 4 FACEBOOK -->
            <a href="https://www.facebook.com/lovemakeupyk/" target="_blank" rel="noopener noreferrer" 
            class="bg-white border border-pink-100 rounded-3xl p-5 shadow-sm hover:shadow-md hover:border-sky-700 transition-all duration-300 flex items-center gap-4 group">
                <div class="w-12 h-12 rounded-2xl bg-sky-50 text-accent-sky text-xl flex items-center justify-center flex-shrink-0 group-hover:scale-110 transition-transform">
                    <i class="fa-brands fa-facebook"></i>
                </div>
                <div class="overflow-hidden">
                    <span class="text-[10px] font-bold uppercase text-gray-400 block">Red Social</span>
                    <h4 class="font-bold text-gray-900 text-sm group-hover:text-sky-900 transition-colors">Facebook</h4>
                    <p class="text-xs text-gray-600 font-semibold truncate">lovemakeupyk</p>
                </div>
            </a>

        </div>
    </section>

    </main>

   <?php include 'vista/complementos/footer_catalogo.php' ?>
</body>
</html>