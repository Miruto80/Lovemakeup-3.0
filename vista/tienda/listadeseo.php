<!DOCTYPE html>
<html lang="es">
<head>
    <?php include 'vista/complementos/head_catalogo.php' ?>
    <title>Lista Deseos | LoveMakeup C.A </title> 
</head>
<body class="min-h-screen flex flex-col pb-20 lg:pb-0">

    <!-- php CARRITO--> 
<?php include 'vista/complementos/carrito.php' ?>

  <?php include 'vista/complementos/nav_catalogo.php' ?>

    <!-- MAIN CONTAINER -->
    <main class="flex-grow max-w-7xl w-full mx-auto px-4 sm:px-6 lg:px-8 py-6">

      <!--  UBICACION -->
      <section id="ListaDeseos" class="tab-content">
         
         <div class="mb-8">
                <div class="flex items-center justify-between mb-6">
                    <div>
                        <h2 class="text-xl font-bold text-gray-900">Mi Lista de Deseos </h2>
                        <p class="text-s text-gray-500 mt-0.5">Guarda tus productos favoritos en un solo lugar y compralos cuando quieras</p>
                    </div>
                    <a href="?pagina=catalogo_producto" class="text-xs font-bold text-accent-fuchsia hover:text-pink-800 flex items-center gap-1">
                        Ver catálogo <i class="fa-solid fa-chevron-right text-[10px]"></i>
                    </a>
                </div>

                <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-4 sm:gap-6" id="s">
                    <?php if (!empty($lista)): ?>
                        <?php foreach ($lista as $producto): ?>
                            <!-- Tarjeta individual del producto (va DIRECTO en el grid principal) -->
                            <div class="product-item product-card bg-white border border-pink-100/80 rounded-3xl p-3 sm:p-4 flex flex-col justify-between relative group shadow-xs hover:shadow-md transition-all duration-300 w-full"
                                  data-id="<?php echo $producto['id_producto']; ?>"
                                  data-nombre="<?php echo htmlspecialchars($producto['nombre']); ?>"
                                  data-precio="<?php echo $producto['precio_detal']; ?>"
                                  data-marca="<?php echo htmlspecialchars($producto['nombre_marca']); ?>"
                                  data-descripcion="<?php echo htmlspecialchars($producto['descripcion']); ?>"
                                  data-cantidad-mayor="<?php echo $producto['cantidad_mayor']; ?>"
                                  data-precio-mayor="<?php echo $producto['precio_mayor']; ?>"
                                  data-stock-disponible="<?php echo $producto['stock_disponible']; ?>"
                                  data-id-lista="<?php echo $producto['id_lista'] ?? ''; ?>"
                                  data-imagenes="<?php echo htmlspecialchars(json_encode($producto['imagenes'])); ?>">   
                                
                                <!-- Imagen y Favoritos -->
                                <div class="relative rounded-2xl overflow-hidden bg-pink-50/40 mb-3 h-40 sm:h-48 w-full flex items-center justify-center cursor-pointer shrink-0"
                                    data-bs-toggle="modal" 
                                    data-bs-target="#productModal" 
                                    onclick="openModal(this.closest('.product-item'))">
                                    
                                    <?php if ($sesion_activa): ?>
                                        <?php if ($_SESSION["nivel_rol"] == 1): ?>
                                            
                                        <?php else: ?>
                                            <a href="?pagina=catalogo" class="absolute top-2 right-2 w-7 h-7 sm:w-8 sm:h-8 rounded-full bg-white/90 text-gray-400 hover:text-accent-fuchsia flex items-center justify-center text-xs sm:text-sm shadow-xs z-10 transition-colors">
                                                <i class="fa-solid fa-heart"></i>
                                            </a>
                                        <?php endif; ?>
                                    <?php else: ?>
                                        <a href="?pagina=login"  class="absolute top-2 right-2 w-7 h-7 sm:w-8 sm:h-8 rounded-full bg-white/90 text-gray-400 hover:text-accent-fuchsia flex items-center justify-center text-xs sm:text-sm shadow-xs z-10 transition-colors">
                                            <i class="fa-solid fa-heart"></i>
                                        </a>
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
                                            <button type="button" class="btn-agregar-carrito-exterior w-full bg-pink-50 hover:bg-accent-fuchsia text-accent-fuchsia hover:text-white font-bold py-2 rounded-2xl text-xs flex items-center justify-center gap-1.5 transition-all">
                                                <i class="fa-solid fa-plus text-[10px]"></i> Añadir
                                            </button>
                                            <div class="my-2 pt-2 border-t border-gray-100 flex items-baseline justify-between gap-1">
                                            <button type="button" class="pt-3 w-full text-white  bg-red-500 hover:bg-red-700  hover:text-white font-bold py-2 rounded-2xl text-xs flex items-center justify-center gap-1.5 transition-all btn-eliminar-deseo" data-id-lista="<?php echo $producto['id_lista']; ?>">
                                                <i class="fa fa-times"></i> Eliminar de Favoritos
                                            </button>
                                        </div>
                                        <?php else: ?>
                                            <a href="<?php echo $sesion_activa ? '?pagina=catalogo' : '?pagina=login'; ?>" class="w-full bg-pink-50 hover:bg-accent-fuchsia text-accent-fuchsia hover:text-white font-bold py-2 rounded-2xl text-xs flex items-center justify-center gap-1.5 transition-all text-center block">
                                                <i class="fa-solid fa-plus text-[10px]"></i> Añadir
                                            </a>
                                        <?php endif; ?>
                                    </form>
                                </div>

                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <div class="col-span-full flex flex-col items-center justify-center py-12 px-4 text-center bg-white border border-pink-100/60 rounded-3xl shadow-xs">
                            <div class="w-16 h-16 bg-pink-50 text-accent-fuchsia rounded-full flex items-center justify-center mb-4 shadow-inner">
                                <i class="fa-solid fa-heart text-2xl"></i>
                            </div>
                            <h1 class="text-base font-bold text-gray-800 mb-1">Aún no as guardado tus producto favoritos</h1>
                            <p class="text-s text-black max-w-sm mb-6">Explora nuestro catálogo</p>
                            <a href="?pagina=catalogo_producto" class="px-6 py-2.5 bg-accent-fuchsia hover:bg-pink-700 text-white rounded-2xl text-xs font-bold transition-all shadow-md shadow-pink-500/20 flex items-center gap-2">
                                <i class="fa-solid fa-border-all"></i>
                                <span>Ir al Catálogo</span>
                            </a>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
      </section>
    
      <div class="my-2 pt-2 border-t border-gray-200 flex items-baseline justify-between gap-1"> </div>
    
    </main>

        <!-- MODAL: DETALLE DEL PRODUCTO -->
        <div id="productDetailModal" class="fixed inset-0 z-50 flex items-center justify-center bg-gray-900/60 backdrop-blur-xs hidden p-3 sm:p-4">
            <div class="bg-white rounded-3xl max-w-3xl w-full max-h-[90vh] overflow-y-auto shadow-2xl border border-pink-100 p-6 sm:p-8 relative">
                
                <!-- Botón Cerrar (Posicionado arriba a la derecha con margen suficiente) -->
                <button type="button" onclick="closeProductModal()" class="absolute top-5 right-5 w-9 h-9 rounded-full bg-pink-50 text-gray-500 hover:text-gray-800 flex items-center justify-center text-sm shadow-xs z-20 transition-colors">
                    <i class="fa-solid fa-xmark"></i>
                </button>

                <div class="grid grid-cols-1 md:grid-cols-12 gap-6 sm:gap-8 items-start pt-2">
                    
                    <!-- Lado de la Galería / Slider -->
                    <div class="md:col-span-6 space-y-3">
                        <div class="bg-pink-50/50 rounded-3xl p-4 border border-pink-100 aspect-square flex items-center justify-center relative overflow-hidden group">
                            <img id="modal-main-image" src="" alt="Producto" class="w-full h-full object-contain transition-all duration-300">
                            <span id="modal-marca-badge" class="absolute top-3 left-3 bg-accent-fuchsia text-white text-[10px] font-extrabold px-2.5 py-1 rounded-full uppercase tracking-wider">
                                Original
                            </span>
                        </div>

                        <!-- Contenedor de Miniaturas -->
                        <div id="modal-slider-inner" class="flex items-center gap-2 overflow-x-auto pb-1">
                            <!-- Las miniaturas se inyectan dinámicamente -->
                        </div>
                    </div>

                    <!-- Lado de Información del Producto -->
                    <div class="md:col-span-6 space-y-4 flex flex-col justify-between h-full">
                        <div>
                            <!-- Encabezado con Marca y Favorito (Con pr-8 para no chocar con la X de cerrar) -->
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

                            <!-- Descripción -->
                            <div class="space-y-2 mb-4">
                                <h4 class="text-xs font-extrabold text-gray-800 uppercase tracking-wider">Descripción del Producto</h4>
                                <p id="modal-descripcion" class="text-s text-gray-600 leading-relaxed"></p>
                            </div>
                        </div>

                        <!-- Formulario Oculto y Botón de Acción -->
                        <form id="form-carrito"  class="pt-3 border-t border-pink-100">
                            <!-- Inputs ocultos para envío de datos al carrito -->
                            <input type="hidden" name="id" id="form-id">
                              <input type="hidden" name="nombre" id="form-nombre">
                              <input type="hidden" name="precio_detal" id="form-precio-detal">
                              <input type="hidden" name="precio_mayor" id="form-precio-mayor">
                              <input type="hidden" name="cantidad_mayor" id="form-cantidad-mayor">
                              <input type="hidden" name="imagen" id="form-imagen">
                              <input type="hidden" name="stockDisponible" id="form-stock-disponible">
                            
                            <?php if ($sesion_activa && $_SESSION["nivel_rol"] == 1): ?>
                            <button type="submit" id="btn-agregar-carrito"class="w-full bg-accent-fuchsia hover:bg-pink-700 text-white font-bold py-3.5 rounded-2xl text-xs shadow-lg shadow-pink-500/20 transition-all flex items-center justify-center gap-2">
                                <i class="fa-solid fa-bag-shopping"></i> Agregar al Carrito
                            </button>
                            
                            <?php else: ?>
                                <button type="button" class="w-full bg-accent-fuchsia hover:bg-pink-700 text-white font-bold py-3.5 rounded-2xl text-xs shadow-lg shadow-pink-500/20 transition-all flex items-center justify-center gap-2">
                                    <i class="fa-solid fa-bag-shopping"></i> Agregar al Carrito
                                </button>

                            <?php endif; ?>
                        </form>

                    </div>

                </div>
            </div>
        </div>

    <?php include 'vista/complementos/footer_catalogo.php' ?>
    <script src="assets/js/lista_deseo.js"></script>
      
</body>
</html>