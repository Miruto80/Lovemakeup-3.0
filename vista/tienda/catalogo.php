<!DOCTYPE html>
<html lang="es">
<head>
    <?php include 'vista/complementos/head_catalogo.php' ?>
    <title>LoveMakeup C.A RIF: J-50543440-3</title> 
</head>
<body class="min-h-screen flex flex-col pb-20 lg:pb-0">

    <!-- php CARRITO--> 
<?php include 'vista/complementos/carrito.php' ?>

  <?php include 'vista/complementos/nav_catalogo.php' ?>

    <!-- MAIN CONTENIDO-->
    <main class="flex-grow max-w-7xl w-full mx-auto px-4 sm:px-6 lg:px-8 py-6">

        <!-- INICIO -->
        <section id="view-inicio" class="tab-content">
            
            <!-- DIV DEL BANNER SLIDER  -->
            <div class="relative rounded-3xl overflow-hidden shadow-xl mb-10 group" id="heroCarousel">
                
                <!-- SLIDER (4 Banners) -->
                <div class="relative min-h-[360px] sm:min-h-[420px] lg:min-h-[460px] w-full">
                    
                    <!-- Banner 1 -->
                    <div class="carousel-slide absolute inset-0 opacity-100 pointer-events-none bg-black text-white flex" id="slide-0">
                        <!-- Lado Izquierdo Contenido  -->
                        <div class="w-full lg:w-1/2 p-6 sm:p-12 flex flex-col justify-center z-10 space-y-4">
                            <div>
                                <span class="inline-block bg-white/20 backdrop-blur-md text-white text-[11px] font-extrabold px-3.5 py-1 rounded-full uppercase tracking-wider border border-white/20">
                                    MARCA
                                </span>
                            </div>
                            <h2 class="text-3xl sm:text-5xl font-black tracking-tight leading-tight">
                                COSMÉTICOS Y BELLEZA SALOMÉ
                            </h2>
                            <p class="text-pink-100 text-xs sm:text-base leading-relaxed font-medium max-w-md">
                                Descubre nuestra gran variedad en productos de belleza y cuidado
                            </p>
                            <div class="pt-2">
                                <a href="?pagina=catalogo_producto" class="inline-block bg-white text-pink-600 hover:bg-pink-50 font-bold px-6 py-3 rounded-full text-xs sm:text-sm shadow-md transition-all transform hover:scale-105">
                                    Ver el Catálogo <i class="fa-solid fa-arrow-right ml-1"></i>
                                </a>
                            </div>
                        </div>
                        <!-- Lado Derecho Imagen -->
                        <div class="hidden lg:block lg:w-1/2 relative overflow-hidden">
                            <img src="assets/img/banner/B01.webp" class="w-full h-full object-cover object-center" alt="Mayorista venta lovemakeup">
                            <div class="absolute inset-y-0 left-0 w-16 bg-gradient-to-r from-black to-transparent"></div>
                        </div>
                    </div>

                    <!-- Banner 2 -->
                   <div class="carousel-slide absolute inset-0 opacity-0 pointer-events-none bg-pink-500 text-white flex" id="slide-1">
                        <!-- Lado Izquierdo Contenido  -->
                        <div class="w-full lg:w-1/2 p-6 sm:p-12 flex flex-col justify-center z-10 space-y-4">
                            <div>
                                <span class="inline-block bg-white/20 backdrop-blur-md text-white text-[11px] font-extrabold px-3.5 py-1 rounded-full uppercase tracking-wider border border-white/20">
                                    VENTA MAYORISTA
                                </span>
                            </div>
                            <h2 class="text-3xl sm:text-5xl font-black tracking-tight leading-tight">
                                ATENCIÓN CLIENTE MAYORISTA
                            </h2>
                            <p class="text-pink-100 text-xs sm:text-base leading-relaxed font-medium max-w-md">
                                Somos distribuidores con los mejores precios del mercado.
                            </p>
                            <div class="pt-2">
                                <a href="?pagina=catalogo_producto" class="inline-block bg-white text-pink-600 hover:bg-pink-50 font-bold px-6 py-3 rounded-full text-xs sm:text-sm shadow-md transition-all transform hover:scale-105">
                                    Ver el Catálogo <i class="fa-solid fa-arrow-right ml-1"></i>
                                </a>
                            </div>
                        </div>
                        <!-- Lado Derecho Imagen -->
                        <div class="hidden lg:block lg:w-1/2 relative overflow-hidden">
                            <img src="assets/img/banner/B02.webp" class="w-full h-full object-cover object-center" alt="Mayorista venta lovemakeup">
                            <div class="absolute inset-y-0 left-0 w-16 bg-gradient-to-r from-pink-500 to-transparent"></div>
                        </div>
                    </div>

                    <!-- Banner 3 -->
                    <div class="carousel-slide absolute inset-0 opacity-0 pointer-events-none bg-purple-900 text-white flex" id="slide-2">
                        <!-- Lado Izquierdo Contenido  -->
                        <div class="w-full lg:w-1/2 p-6 sm:p-12 flex flex-col justify-center z-10 space-y-4">
                            <div>
                                <span class="inline-block bg-white/20 backdrop-blur-md text-white text-[11px] font-extrabold px-3.5 py-1 rounded-full uppercase tracking-wider border border-white/20">
                                    MARCA
                                </span>
                            </div>
                            <h2 class="text-3xl sm:text-5xl font-black tracking-tight leading-tight">
                                ¿POR QUÉ ELEGIR DOLCE BELLA?
                            </h2>
                            <p class="text-pink-100 text-xs sm:text-base leading-relaxed font-medium max-w-md">
                                Alta pigmentación, excelente fijación y calidad profesional sin pagar de más.
                            </p>
                            <div class="pt-2">
                                <a href="?pagina=catalogo_producto" class="inline-block bg-white text-pink-600 hover:bg-pink-50 font-bold px-6 py-3 rounded-full text-xs sm:text-sm shadow-md transition-all transform hover:scale-105">
                                    Ver el Catálogo <i class="fa-solid fa-arrow-right ml-1"></i>
                                </a>
                            </div>
                        </div>
                        <!-- Lado Derecho Imagen -->
                        <div class="hidden lg:block lg:w-1/2 relative overflow-hidden">
                            <img src="assets/img/banner/B03.webp" class="w-full h-full object-cover object-center" alt="Mayorista venta lovemakeup">
                            <div class="absolute inset-y-0 left-0 w-16 bg-gradient-to-r from-purple-900 to-transparent"></div>
                        </div>
                    </div>

                    <!-- Banner 4 -->
                    <div class="carousel-slide absolute inset-0 opacity-0 pointer-events-none bg-pink-800 text-white flex" id="slide-3">
                        <!-- Lado Izquierdo Contenido  -->
                        <div class="w-full lg:w-1/2 p-6 sm:p-12 flex flex-col justify-center z-10 space-y-4">
                            <div>
                                <span class="inline-block bg-white/20 backdrop-blur-md text-white text-[11px] font-extrabold px-3.5 py-1 rounded-full uppercase tracking-wider border border-white/20">
                                    VENTA MAYORISTA
                                </span>
                            </div>
                            <h2 class="text-3xl sm:text-5xl font-black tracking-tight leading-tight">
                                ¿BUSCAS INGRESOS EXTRA?
                            </h2>
                            <p class="text-pink-100 text-xs sm:text-base leading-relaxed font-medium max-w-md">
                                Emprende vendiendo el maquillaje que todos buscan.
                            </p>
                            <div class="pt-2">
                                <a href="?pagina=catalogo_producto" class="inline-block bg-white text-pink-600 hover:bg-pink-50 font-bold px-6 py-3 rounded-full text-xs sm:text-sm shadow-md transition-all transform hover:scale-105">
                                    Ver el Catálogo <i class="fa-solid fa-arrow-right ml-1"></i>
                                </a>
                            </div>
                        </div>
                        <!-- Lado Derecho Imagen -->
                        <div class="hidden lg:block lg:w-1/2 relative overflow-hidden">
                            <img src="assets/img/banner/B04.webp" class="w-full h-full object-cover object-center" alt="Mayorista venta lovemakeup">
                            <div class="absolute inset-y-0 left-0 w-16 bg-gradient-to-r from-pink-800 to-transparent"></div>
                        </div>
                    </div>

                </div>

                <!-- CARRUSEL BOTON DE SIGUIENTE/ATRAS -->
                <button onclick="prevSlide()" class="absolute left-4 top-1/2 -translate-y-1/2 w-10 h-10 rounded-full bg-white/20 hover:bg-white/40 text-white backdrop-blur-md flex items-center justify-center transition-all z-20">
                    <i class="fa-solid fa-chevron-left text-sm"></i>
                </button>
                <button onclick="nextSlide()" class="absolute right-4 top-1/2 -translate-y-1/2 w-10 h-10 rounded-full bg-white/20 hover:bg-white/40 text-white backdrop-blur-md flex items-center justify-center transition-all z-20">
                    <i class="fa-solid fa-chevron-right text-sm"></i>
                </button>

                <!-- CARRUSEL BOTONES ABAJO PUNTOS -->
                <div class="absolute bottom-4 left-1/2 -translate-x-1/2 flex items-center gap-2 z-20" id="carouselIndicators">
                    <button onclick="goToSlide(0)" class="w-3 h-3 rounded-full bg-white transition-all shadow-sm" id="dot-0"></button>
                    <button onclick="goToSlide(1)" class="w-3 h-3 rounded-full bg-white/40 transition-all shadow-sm" id="dot-1"></button>
                    <button onclick="goToSlide(2)" class="w-3 h-3 rounded-full bg-white/40 transition-all shadow-sm" id="dot-2"></button>
                    <button onclick="goToSlide(3)" class="w-3 h-3 rounded-full bg-white/40 transition-all shadow-sm" id="dot-3"></button>
                </div>

            </div>

            <!-- BANNER DE MARCAS -->
            <div class="mb-12">
                <div class="flex items-center justify-between mb-4">
                    <h2 class="text-lg font-bold text-gray-900 flex items-center gap-2">
                        <span>Lo Mejor de Nuestro Catálogo</span>
                        <span class="text-xs font-normal text-pink-600 bg-pink-50 border border-pink-100 px-2.5 py-0.5 rounded-full">Marcas Top</span>
                    </h2>
                 
                </div>
                <div class="grid grid-cols-3 sm:grid-cols-6 gap-3 sm:gap-4">
                    <div class="bg-white border border-pink-100 rounded-2xl p-3.5 text-center cursor-pointer hover:border-pink-300 hover:shadow-md transition-all group">
                        <div class="w-34 h-12 rounded-xl bg-pink-50 mx-auto flex items-center justify-center overflow-hidden  mb-1.5 group-hover:scale-105 transition-transform">
                            <img src="assets/img/marcas/012.webp" alt="Marca Ushas" class="w-full h-full object-cover">
                        </div>
                        <span class="text-xs font-semibold text-gray-800 block">Ushas</span>
                    </div>
                    <div class="bg-white border border-pink-100 rounded-2xl p-3.5 text-center cursor-pointer hover:border-pink-300 hover:shadow-md transition-all group">
                        <div class="w-34 h-12 rounded-xl bg-pink-50 mx-auto flex items-center justify-center overflow-hidden  mb-1.5 group-hover:scale-105 transition-transform">
                            <img src="assets/img/marcas/02.webp" alt="Marca Ushas" class="w-full h-full object-cover">
                        </div>
                        <span class="text-xs font-semibold text-gray-800 block">Salome</span>
                    </div>
                    <div class="bg-white border border-pink-100 rounded-2xl p-3.5 text-center cursor-pointer hover:border-pink-300 hover:shadow-md transition-all group">
                        <div class="w-34 h-12 rounded-xl bg-pink-50 mx-auto flex items-center justify-center overflow-hidden  mb-1.5 group-hover:scale-105 transition-transform">
                            <img src="assets/img/marcas/03.webp" alt="Marca Ushas" class="w-full h-full object-cover">
                        </div>
                        <span class="text-xs font-semibold text-gray-800 block">Bioaqua</span>
                    </div>
                    <div class="bg-white border border-pink-100 rounded-2xl p-3.5 text-center cursor-pointer hover:border-pink-300 hover:shadow-md transition-all group">
                        <div class="w-34 h-12 rounded-xl bg-pink-50 mx-auto flex items-center justify-center overflow-hidden  mb-1.5 group-hover:scale-105 transition-transform">
                            <img src="assets/img/marcas/04.webp" alt="Marca Ushas" class="w-full h-full object-cover">
                        </div>
                        <span class="text-xs font-semibold text-gray-800 block">Sadoer</span>
                    </div>
                    <div class="bg-white border border-pink-100 rounded-2xl p-3.5 text-center cursor-pointer hover:border-pink-300 hover:shadow-md transition-all group">
                        <div class="w-34 h-12 rounded-xl bg-pink-50 mx-auto flex items-center justify-center overflow-hidden  mb-1.5 group-hover:scale-105 transition-transform">
                            <img src="assets/img/marcas/05.webp" alt="Marca Ushas" class="w-full h-full object-cover">
                        </div>
                        <span class="text-xs font-semibold text-gray-800 block">Karite</span>
                    </div>
                    <div class="bg-white border border-pink-100 rounded-2xl p-3.5 text-center cursor-pointer hover:border-pink-300 hover:shadow-md transition-all group">
                        <div class="w-34 h-12 rounded-xl bg-pink-50 mx-auto flex items-center justify-center overflow-hidden  mb-1.5 group-hover:scale-105 transition-transform">
                            <img src="assets/img/marcas/06.webp" alt="Marca Ushas" class="w-full h-full object-cover">
                        </div>
                        <span class="text-xs font-semibold text-gray-800 block">Dolce Bella</span>
                    </div>
                   
                    
                    
                </div>
            </div>

            <!-- PRODUCTOS MAS VENDIDOS -->
            <div class="mb-8 heplp-productotop">
                <div class="flex items-center justify-between mb-6">
                    <div>
                        <h2 class="text-xl font-bold text-gray-900">Productos Más Vendidos</h2>
                        <p class="text-xs text-gray-500 mt-0.5">Toca sobre cualquier imagen para ver detalles y fotos</p>
                    </div>
                    <a href="?pagina=catalogo_producto" class="text-xs font-bold text-accent-fuchsia hover:text-pink-800 flex items-center gap-1">
                        Ver catálogo <i class="fa-solid fa-chevron-right text-[10px]"></i>
                    </a>
                </div>

                <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-4 sm:gap-6" id="s">
                    <?php if (!empty($registro)): ?>
                        <?php foreach ($registro as $producto): ?>
                            <!-- Tarjeta individual del producto (va DIRECTO en el grid principal) -->
                            <div class="product-item product-card bg-white border border-pink-100/80 rounded-3xl p-3 sm:p-4 flex flex-col justify-between relative group shadow-xs hover:shadow-md transition-all duration-300 w-full"
                                data-categoria="<?php echo $producto['id_categoria']; ?>" 
                                data-id="<?php echo $producto['id_producto']; ?>"
                                data-nombre="<?php echo htmlspecialchars($producto['nombre']); ?>"
                                data-precio="<?php echo $producto['precio_detal']; ?>"
                                data-marca="<?php echo htmlspecialchars($producto['nombre_marca']); ?>"
                                data-descripcion="<?php echo htmlspecialchars($producto['descripcion']); ?>"
                                data-cantidad-mayor="<?php echo $producto['cantidad_mayor']; ?>"
                                data-precio-mayor="<?php echo $producto['precio_mayor']; ?>"
                                data-stock-disponible="<?php echo $producto['stock_disponible']; ?>"
                                data-imagenes="<?php echo htmlspecialchars(json_encode($producto['imagenes'])); ?>">
                                
                                <!-- Imagen y Favoritos -->
                                <div class="relative rounded-2xl overflow-hidden bg-pink-50/40 mb-3 h-40 sm:h-48 w-full flex items-center justify-center cursor-pointer shrink-0"
                                    data-bs-toggle="modal" 
                                    data-bs-target="#productModal" 
                                    onclick="openModal(this.closest('.product-item'))">
                                    
                                    <?php if ($sesion_activa): ?>
                                        <?php if ($_SESSION["nivel_rol"] === 1): ?>
                                            <button type="button" 
                                                    class="btn-favorito absolute top-2 right-2 w-7 h-7 sm:w-8 sm:h-8 rounded-full bg-white/90 text-gray-400 hover:text-accent-fuchsia <?php echo in_array($producto['id_producto'], $idsProductosFavoritos) ? 'favorito-activo text-red-500' : ''; ?> flex items-center justify-center text-xs sm:text-sm shadow-xs z-10 transition-colors"
                                                    data-id="<?php echo $producto['id_producto']; ?>">
                                                <i class="fa-solid fa-heart"></i>
                                            </button>
                                        <?php else: ?>
                                            <button type="Button" class=" btn-login absolute top-2 right-2 w-7 h-7 sm:w-8 sm:h-8 rounded-full bg-white/90 text-gray-400 hover:text-accent-fuchsia flex items-center justify-center text-xs sm:text-sm shadow-xs z-10 transition-colors">
                                                <i class="fa-solid fa-heart"></i>
                                            </button>
                                        <?php endif; ?>
                                    <?php else: ?>
                                        <Button type="Button" class=" btn-login absolute top-2 right-2 w-7 h-7 sm:w-8 sm:h-8 rounded-full bg-white/90 text-gray-400 hover:text-accent-fuchsia flex items-center justify-center text-xs sm:text-sm shadow-xs z-10 transition-colors">
                                            <i class="fa-solid fa-heart"></i>
                                         </Button>
                                    <?php endif; ?>

                                    <img src="<?php echo $producto['imagenes'][0]['url_imagen']; ?>" 
                                        alt="<?php echo htmlspecialchars($producto['nombre']); ?>" 
                                        class="w-full h-full object-contain p-2 group-hover:scale-105 transition-transform duration-300" 
                                        onerror="this.src='https://placehold.co/400x400/fdf2f8/d81b60?text=LoveMakeup'">
                                    
                                    <div class="absolute inset-x-0 bottom-0 bg-gradient-to-t from-gray-900/60 to-transparent p-2 text-center opacity-0 group-hover:opacity-100 transition-opacity">
                                        <span class="text-[10px] sm:text-xs text-white font-bold"><i class="fa-solid fa-eye mr-1"></i> Ver detalles</span>
                                    </div>
                                </div>

                                <!-- Textos e Información -->
                                <div class="flex-grow flex flex-col justify-between">
                                    <div>
                                        <?php if (!empty($producto['nombre_marca'])): ?>
                                            <span class="text-[10px] font-bold text-pink-600 uppercase tracking-wider block mb-0.5">
                                                <?php echo htmlspecialchars($producto['nombre_marca']); ?>
                                            </span>
                                        <?php endif; ?>

                                        <h4 class="font-bold text-gray-900 text-xs sm:text-sm line-clamp-2 mb-2 cursor-pointer hover:text-accent-fuchsia leading-snug" 
                                            title="<?php echo htmlspecialchars($producto['nombre']); ?>"
                                            data-bs-toggle="modal" 
                                            data-bs-target="#productModal" 
                                            onclick="openModal(this.closest('.product-item'))">
                                            <?php echo htmlspecialchars($producto['nombre']); ?>
                                        </h4>
                                    </div>
                                    
                                    <div class="my-2 pt-2 border-t border-gray-100 flex items-baseline justify-between gap-1">
                                        <span class="text-sm sm:text-base font-extrabold text-accent-fuchsia">
                                            Ref $ <?php echo number_format($producto['precio_detal'], 2); ?>
                                        </span>
                                        <?php if ($producto['precio_mayor']): ?>
                                            <span class="text-[13px] font-semibold text-gray-900">
                                                Bs <?php echo number_format($tasa_bcv * $producto['precio_detal'], 2, ',', '.'); ?>
                                            </span>
                                        <?php endif; ?>
                                    </div>
                                </div>

                                <!-- Botón Añadir -->
                                <div class="button-area mt-auto pt-1">
                                    <form class="form-carrito-exterior">
                                        <input type="hidden" name="id" value="<?php echo $producto['id_producto']; ?>">
                                        <input type="hidden" name="nombre" value="<?php echo htmlspecialchars($producto['nombre']); ?>">
                                        <input type="hidden" name="precio_detal" value="<?php echo $producto['precio_detal']; ?>">
                                        <input type="hidden" name="precio_mayor" value="<?php echo $producto['precio_mayor']; ?>">
                                        <input type="hidden" name="cantidad_mayor" value="<?php echo $producto['cantidad_mayor']; ?>">
                                        <input type="hidden" name="imagen" value="<?php echo $producto['imagenes'][0]['url_imagen']; ?>">
                                        <input type="hidden" name="stockDisponible" value="<?php echo $producto['stock_disponible']; ?>">

                                        <?php if ($sesion_activa && $_SESSION["nivel_rol"] == 1): ?>
                                               <button type="button" class="btn-agregar-carrito-exterior w-full bg-accent-fuchsia text-white hover:bg-pink-600 hover:text-white font-bold py-2 rounded-2xl text-xs flex items-center justify-center gap-1.5 transition-all">
                                                      <i class="fa-solid fa-plus text-[10px]"></i> Añadir
                                                  </button>
                                              <?php else: ?>
                                                
                                                <button type="Button" class="btn-login w-full bg-accent-fuchsia text-white hover:bg-pink-600 hover:text-white font-bold py-2 rounded-2xl text-xs flex items-center justify-center gap-1.5 transition-all text-center block">
                                                      <i class="fa-solid fa-plus text-[10px]"></i> Añadir
                                                </button>
                                        <?php endif; ?>
                                    </form>
                                </div>

                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <div class="col-span-full text-center py-12">
                            <p class="text-base text-gray-500 font-medium">No se encontraron productos.</p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </section>
        
        <div class="my-2 pt-2 border-t border-gray-200 flex items-baseline justify-between gap-1"> </div>

        <!-- Banner Instagram -->
        <section class="tab-content">
            <div class="max-w-7xl mx-auto bg-white rounded-3xl shadow-sm border border-gray-100 p-6 sm:p-10 lg:p-12">
                <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-center">
                
                <!-- Texto y Botón -->
                <div class="lg:col-span-5 space-y-6 text-center lg:text-left">
                    <h2 class="text-3xl sm:text-4xl font-extrabold text-gray-900 leading-tight">
                    ¡Resalta tu belleza con los mejores productos de maquillaje!
                    </h2>
                    <p class="text-gray-600 text-base">
                    Síguenos en Instagram <b class="font-semibold text-[#fa48c9]">@lovemakeupyk</b> para descubrir nuestra colección
                    </p>
                    <div>
                    <a href="https://www.instagram.com/lovemakeupyk/" 
                        target="_blank" 
                        class="inline-block w-full sm:w-auto py-3.5 px-8 text-center bg-gray-900 text-white font-medium text-base rounded-2xl shadow-md hover:bg-gray-800 transition-colors">
                        Seguir en Instagram
                    </a>
                    </div>
                </div>

                <!-- Imagen Banner -->
                <div class="lg:col-span-7 flex justify-center">
                    <img src="assets/img/banner/lovemakeuptk1.png" alt="LoveMakeup Banner" class="w-full h-auto max-h-[380px] object-cover rounded-2xl">
                </div>

                </div>
            </div>
        </section>
        <!-- CARACTERISTICAS -->
        <section class="mb-12 pt-7">
            <div class="max-w-7xl mx-auto">
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                
                <!-- Tarjeta 1 -->
                <div class="bg-white rounded-2xl p-6 border border-gray-100 shadow-sm flex flex-col items-center text-center space-y-3">
                    <div class="text-[#fa48c9] text-4xl mb-1">
                    <i class="fa-solid fa-truck"></i>
                    </div>
                    <h5 class="text-base font-bold text-gray-900">Envíos nacionales</h5>
                    <p class="text-xs text-gray-500 leading-relaxed">Recibe tus productos en todo el país con seguridad y rapidez.</p>
                </div>

                <!-- Tarjeta 2 -->
                <div class="bg-white rounded-2xl p-6 border border-gray-100 shadow-sm flex flex-col items-center text-center space-y-3">
                    <div class="text-[#fa48c9] text-4xl mb-1">
                    <i class="fa-solid fa-money-bill-wave"></i>
                    </div>
                    <h5 class="text-base font-bold text-gray-900">Pagos seguros</h5>
                    <p class="text-xs text-gray-500 leading-relaxed">Opciones confiables para que compres sin preocupaciones.</p>
                </div>

                <!-- Tarjeta 3 -->
                <div class="bg-white rounded-2xl p-6 border border-gray-100 shadow-sm flex flex-col items-center text-center space-y-3">
                    <div class="text-[#fa48c9] text-4xl mb-1">
                    <i class="fa-solid fa-comments"></i>
                    </div>
                    <h5 class="text-base font-bold text-gray-900">Atención personalizada</h5>
                    <p class="text-xs text-gray-500 leading-relaxed">Asesoría experta para ayudarte a elegir el maquillaje perfecto.</p>
                </div>

                <!-- Tarjeta 4 -->
                <div class="bg-white rounded-2xl p-6 border border-gray-100 shadow-sm flex flex-col items-center text-center space-y-3">
                    <div class="text-[#fa48c9] text-4xl mb-1">
                    <i class="fa-solid fa-percent"></i>
                    </div>
                    <h5 class="text-base font-bold text-gray-900">Ofertas y promociones</h5>
                    <p class="text-xs text-gray-500 leading-relaxed">Descuentos especiales en tus marcas favoritas.</p>
                </div>

                </div>
            </div>
        </section>
       
    </main>


<!-- MODAL: DETALLE DEL PRODUCTO -->
<div id="productDetailModal" 
     onclick="cierremodalfuera(event)"
     class="fixed inset-0 z-50 flex items-center justify-center bg-gray-900/60 backdrop-blur-xs hidden p-3 sm:p-4 transition-opacity duration-300 opacity-0">
    
    <div id="productModalContent" 
         class="bg-white rounded-3xl max-w-3xl w-full max-h-[90vh] overflow-y-auto shadow-2xl border border-pink-100 p-6 sm:p-8 relative transform transition-all duration-300 scale-95 opacity-0">
        
        <!-- Botón Cerrar -->
        <button type="button" onclick="closeProductModal()" class="absolute top-5 right-5 w-9 h-9 rounded-full bg-red-500 text-white hover:text-gray-800 flex items-center justify-center text-sm shadow-xs z-20 transition-colors">
            <i class="fa-solid fa-xmark"></i>
        </button>

        <div class="grid grid-cols-1 md:grid-cols-12 gap-6 sm:gap-8 items-start pt-2">
            
            <!-- Lado de la Galeria -->
            <div class="md:col-span-6 space-y-3">
                <div class="bg-pink-50/50 rounded-3xl p-4 border border-pink-100 aspect-square flex items-center justify-center relative overflow-hidden group">
                    <img id="modal-main-image" src="" alt="Producto" class="w-full h-full object-contain transition-all duration-300">
                    <span id="modal-marca-badge" class="absolute top-3 left-3 bg-accent-fuchsia text-white text-[10px] font-extrabold px-2.5 py-1 rounded-full uppercase tracking-wider">
                        Original
                    </span>

                    <!-- Flecha Atras -->
                    <button type="button" id="modal-prev-btn" onclick="cambiarImagenModal(-1)" class="absolute left-3 top-1/2 -translate-y-1/2 w-8 h-8 rounded-full bg-white/90 hover:bg-white text-gray-800 shadow-md flex items-center justify-center transition-all z-10 hidden">
                        <i class="fa-solid fa-chevron-left text-xs"></i>
                    </button>

                    <!-- Flecha Siguiente -->
                    <button type="button" id="modal-next-btn" onclick="cambiarImagenModal(1)" class="absolute right-3 top-1/2 -translate-y-1/2 w-8 h-8 rounded-full bg-white/90 hover:bg-white text-gray-800 shadow-md flex items-center justify-center transition-all z-10 hidden">
                        <i class="fa-solid fa-chevron-right text-xs"></i>
                    </button>
                </div>

                <!-- Contenedor de Miniaturas -->
                <div id="modal-slider-inner" class="flex items-center gap-2 overflow-x-auto pb-1">
                    <!-- Las miniaturas dinamicamente -->
                </div>
            </div>

            <!-- Lado de Información del Producto -->
            <div class="md:col-span-6 space-y-4 flex flex-col justify-between h-full">
                <div>
                    <!-- Encabezado con Marca -->
                    <div class="flex justify-between items-center pr-8 mb-1">
                        <span id="modal-marca" class="text-xs font-extrabold text-pink-600 uppercase tracking-widest block"></span>
                    </div>
                    
                    <h3 id="modal-title" class="text-xl sm:text-2xl font-black text-gray-900 mb-2 leading-tight"></h3>
                    
                    <!-- Precios y Stock -->
                    <div class="p-3 bg-pink-50/70 rounded-2xl border border-pink-100 mb-4">
                        <div class="flex items-baseline gap-2 flex-wrap">
                            <span id="modal-precio" class="text-2xl font-black text-accent-fuchsia"></span>
                        </div>
                        <div class="mt-1 text-xs font-semibold text-black">
                            Precio al Mayor: <span id="modal-precio-mayor" class="text-pink-600 font-bold"></span> 
                            (a partir de <span id="modal-cantidad-mayor" class="font-bold"></span> unids)
                        </div>
                        <span class="text-[13px] text-emerald-600 font-bold flex items-center gap-1 mt-1">
                            <i class="fa-solid fa-circle-check"></i> Stock Disponible: <span id="modal-stock-disponible">0</span>
                        </span>
                    </div>

                    <!-- Descripcion -->
                    <div class="space-y-2 mb-4">
                        <h4 class="text-xs font-extrabold text-gray-800 uppercase tracking-wider">Descripción del Producto</h4>
                        <p id="modal-descripcion" class="text-s text-gray-600 leading-relaxed"></p>
                    </div>
                </div>

                <!-- Formulario Oculto y Botón de Acción -->
                <form id="form-carrito" class="pt-3 border-t border-pink-100">
                    <input type="hidden" name="id" id="form-id">
                    <input type="hidden" name="nombre" id="form-nombre">
                    <input type="hidden" name="precio_detal" id="form-precio-detal">
                    <input type="hidden" name="precio_mayor" id="form-precio-mayor">
                    <input type="hidden" name="cantidad_mayor" id="form-cantidad-mayor">
                    <input type="hidden" name="imagen" id="form-imagen">
                    <input type="hidden" name="stockDisponible" id="form-stock-disponible">
                    
                    <?php if ($sesion_activa && $_SESSION["nivel_rol"] == 1){ ?>
                        <button type="submit" id="btn-agregar-carrito" class="w-full bg-accent-fuchsia hover:bg-pink-700 text-white font-bold py-3.5 rounded-2xl text-xs shadow-lg shadow-pink-500/20 transition-all flex items-center justify-center gap-2">
                            <i class="fa-solid fa-bag-shopping"></i> Agregar al Carrito
                        </button>
                    <?php } else { ?>
                        <button type="button" class="btn-login w-full bg-accent-fuchsia hover:bg-pink-700 text-white font-bold py-3.5 rounded-2xl text-xs shadow-lg shadow-pink-500/20 transition-all flex items-center justify-center gap-2">
                            <i class="fa-solid fa-bag-shopping"></i> Agregar al Carrito
                        </button>
                    <?php } ?>
                </form>
            </div>

        </div>
    </div>
</div>


   <?php include 'vista/complementos/footer_catalogo.php' ?>
</body>
</html>