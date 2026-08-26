<!DOCTYPE html>
<html lang="es">
<head>
    <?php include 'vista/complementos/head_catalogo.php' ?>
    <title>Productos - Catalogo | LoveMakeup C.A </title> 
</head>
<body class="min-h-screen flex flex-col pb-20 lg:pb-0">

    <!-- php CARRITO--> 
<?php include 'vista/complementos/carrito.php' ?>

  <?php include 'vista/complementos/nav_catalogo.php' ?>

  <!-- BOTÓN FLOTANTE SOLO EN TELEFONO -->
<button type="button" 
        onclick="toggleFilterDrawer(true)"
        class="md:hidden fixed bottom-24 left-4 z-40 bg-accent-fuchsia text-white px-4 py-3 rounded-full shadow-2xl flex items-center gap-2 text-xs font-bold hover:bg-pink-700 transition-all active:scale-95">
    <i class="fa-solid fa-filter text-sm"></i>
    <span>Filtrar por categorias</span>
</button>

<!-- PANEL LATERAL DE CATEGORÍAS  -->
<div id="offcanvasCategorias" class="fixed inset-0 z-50 flex hidden">
    <!-- Backdrop oscuro -->
    <div onclick="toggleFilterDrawer(false)" class="fixed inset-0 bg-gray-900/60 backdrop-blur-xs transition-opacity"></div>
    
    <!-- Drawer panel -->
    <div class="relative bg-white w-4/5 max-w-xs h-full shadow-2xl p-5 flex flex-col z-10 space-y-4">
        <!-- Cabecera -->
        <div class="flex items-center justify-between border-b border-pink-100 pb-3">
            <h5 class="font-bold text-gray-900 text-sm flex items-center gap-2">
                <i class="fa-solid fa-filter text-accent-fuchsia"></i>
                Filtrado por categorías
            </h5>
            <button type="button" onclick="toggleFilterDrawer(false)" class="w-8 h-8 rounded-full bg-pink-50 text-gray-400 hover:text-gray-800 flex items-center justify-center text-sm transition-colors">
                <i class="fa-solid fa-xmark"></i>
            </button>
        </div>

        <!-- Cuerpo del Drawer con Scroll independiente -->
        <div id="drawerBody" class="overflow-y-auto flex-1 pr-1 space-y-2">
            <ul class="space-y-2">
                <?php if (empty($categorias)): ?>
                    <li class="text-xs text-gray-400 italic">No se encontraron categorías.</li>
                <?php endif; ?>
                <?php foreach ($categorias as $cat): ?>
                    <li>
                        <label for="mob-cat-<?php echo $cat['id_categoria']; ?>" 
                               class="categoria-label flex items-center gap-3 p-2.5 rounded-xl border border-pink-100/60 bg-pink-50/30 hover:bg-pink-50 hover:border-pink-200 cursor-pointer transition-all">
                            <input type="checkbox" 
                                   id="mob-cat-<?php echo $cat['id_categoria']; ?>" 
                                   value="<?php echo $cat['id_categoria']; ?>" 
                                   class="form-checkbox h-4 w-4 text-accent-fuchsia rounded border-pink-300 focus:ring-accent-fuchsia filtro-checkbox">
                            <svg width="20" height="20" viewBox="0 0 24 24" class="fill-current text-accent-fuchsia">
                                <use xlink:href="#icon-<?php echo strtolower($cat['nombre']); ?>"></use>
                            </svg>
                            <span class="text-xs font-semibold text-gray-700"><?php echo htmlspecialchars($cat['nombre']); ?></span>
                        </label>
                    </li>
                <?php endforeach; ?>
            </ul>
        </div>
    </div>
</div>

  <!-- MAIN CONTENT CONTAINER -->
  <main class="flex-grow max-w-7xl w-full mx-auto px-4 sm:px-6 lg:px-8 py-6">
    <section id="view-productos" class="tab-content">

            <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-6 pb-4 border-b border-pink-100">
                <div>
                    <h2 class="text-2xl font-bold text-gray-900">Catálogo General</h2>
                    <p class="text-s text-gray-500 mt-0.5">Explora nuestros productos con precios actualizados en Dólares y Bolívares</p>
                </div>
                <div class="flex items-center gap-2">
                    <span id="productCountBadge" class="bg-black text-white text-xs font-bold px-3 py-1.5 rounded-full">
                        <?= count($registro) ?> <?= count($registro) === 1 ? 'Productos' : 'Productos' ?>
                    </span>
                </div>
            </div>  
        
            <!-- CONTENEDOR PRINCIPAL: FILTROS DESKTOP + PRODUCTOS -->
           <div class="grid grid-cols-1 md:grid-cols-12 gap-6 items-start pt-4">

    <!-- CATEGORÍAS EN DESKTOP (Sticky optimizado con overflow interno si hay muchas categorías) -->
    <div class="hidden md:block md:col-span-3 sticky top-36 max-h-[calc(100vh-8rem)]">
        <div class="p-6 bg-white border border-pink-100 rounded-3xl shadow-sm flex flex-col max-h-full">
            
            <!-- Título fijo visible -->
            <h5 class="font-bold text-gray-900 text-sm border-b border-pink-100 pb-3 mb-3 flex items-center gap-2 shrink-0">
                <i class="fa-solid fa-filter text-accent-fuchsia"></i>
                Filtrado por categorías
            </h5>
            
            <!-- Lista de categorías con scroll propio si excede la pantalla -->
            <div class="overflow-y-auto pr-1 flex-1">
                <ul class="space-y-2">
                    <?php if (empty($categorias)): ?>
                        <li class="text-xs text-gray-400 italic">No se encontraron categorías.</li>
                    <?php endif; ?>
                    <?php foreach ($categorias as $cat): ?>
                        <li>
                            <label for="cat-<?php echo $cat['id_categoria']; ?>" 
                                   class="categoria-label flex items-center gap-3 p-2 rounded-xl hover:bg-pink-50 cursor-pointer transition-all">
                                <input type="checkbox" 
                                       id="cat-<?php echo $cat['id_categoria']; ?>" 
                                       value="<?php echo $cat['id_categoria']; ?>" 
                                       class="form-checkbox h-4 w-4 text-accent-fuchsia rounded border-pink-300 focus:ring-accent-fuchsia filtro-checkbox">
                                <svg width="18" height="18" viewBox="0 0 24 24" class="fill-current text-accent-fuchsia">
                                    <use xlink:href="#icon-<?php echo strtolower($cat['nombre']); ?>"></use>
                                </svg>
                                <span class="text-xs font-medium text-gray-700"><?php echo htmlspecialchars($cat['nombre']); ?></span>
                            </label>
                        </li>
                    <?php endforeach; ?>
                </ul>
            </div>
        </div>
    </div>

                <!--  PRODUCTOS  -->
                <div class="col-span-1 md:col-span-9">
                    <div class="grid grid-cols-2 sm:grid-cols-2 lg:grid-cols-4 gap-4 sm:gap-6" id="allProductsGrid">
                      <?php if (!empty($registro)): ?>
                              <?php foreach ($registro as $producto): ?>
                                  <!-- Tarjeta individual del producto  -->
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
                                              <?php if ($_SESSION["nivel_rol"] == 1): ?>
                                                  <button type="button" 
                                                         
                                                          class="btn-favorito absolute top-2 right-2 w-7 h-7 sm:w-8 sm:h-8 rounded-full bg-white/90 text-gray-400 hover:text-accent-fuchsia <?php echo in_array($producto['id_producto'], $idsProductosFavoritos) ? 'favorito-activo text-red-500' : ''; ?> flex items-center justify-center text-xs sm:text-sm shadow-xs z-10 transition-colors"
                                                          data-id="<?php echo $producto['id_producto']; ?>">
                                                      <i class="fa-solid fa-heart"></i>
                                                  </button>
                                              <?php else: ?>
                                                  <button type="button" class="btn-login absolute top-2 right-2 w-7 h-7 sm:w-8 sm:h-8 rounded-full bg-white/90 text-gray-400 hover:text-accent-fuchsia flex items-center justify-center text-xs sm:text-sm shadow-xs z-10 transition-colors">
                                                      <i class="fa-solid fa-heart"></i>
                                              </button>
                                              <?php endif; ?>
                                          <?php else: ?>
                                              <button type="button"  class="btn-login absolute top-2 right-2 w-7 h-7 sm:w-8 sm:h-8 rounded-full bg-white/90 text-gray-400 hover:text-accent-fuchsia flex items-center justify-center text-xs sm:text-sm shadow-xs z-10 transition-colors">
                                                  <i class="fa-solid fa-heart"></i>
                                          </button>
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
                                              <?php else: ?>
                                                
                                                <button type="Button" class="btn-login w-full bg-pink-50 hover:bg-accent-fuchsia text-accent-fuchsia hover:text-white font-bold py-2 rounded-2xl text-xs flex items-center justify-center gap-1.5 transition-all text-center block">
                                                      <i class="fa-solid fa-plus text-[10px]"></i> Añadir
                                                </button>
                                              <?php endif; ?> 
                                          </form>
                                      </div>
      
                                  </div>
                              <?php endforeach; ?>
                          <?php else: ?>
                              <div class="col-span-full text-center">
                                <div class="col-span-full flex flex-col items-center justify-center py-12 px-4 text-center bg-white border border-pink-100/60 rounded-3xl shadow-xs">
                                    <div class="w-16 h-16 bg-pink-50 text-accent-fuchsia rounded-full flex items-center justify-center mb-4 shadow-inner">
                                        <i class="fa-solid fa-magnifying-glass text-2xl"></i>
                                    </div>
                                    <h1 class="text-lg font-bold text-gray-800 mb-1">No se encontraron productos</h1>
                                    <p class="text-s text-black max-w-sm mb-6">Sin resultados disponibles por el momento.</p>
                                </div>
                              </div>
                          <?php endif; ?>

                    </div>
                </div>

            </div>
            </div>
        </section>
       
<!-- DETALLE DEL PRODUCTO -->
<div id="productDetailModal" class="fixed inset-0 z-50 flex items-center justify-center bg-gray-900/60 backdrop-blur-xs hidden p-3 sm:p-4">
    <div class="bg-white rounded-3xl max-w-3xl w-full max-h-[90vh] overflow-y-auto shadow-2xl border border-pink-100 p-6 sm:p-8 relative">
        
        <!-- Botón Cerrar -->
        <button type="button" onclick="closeProductModal()" class="absolute top-5 right-5 w-9 h-9 rounded-full bg-pink-50 text-gray-500 hover:text-gray-800 flex items-center justify-center text-sm shadow-xs z-20 transition-colors">
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
                </div>

                <!-- Contenedor de Miniaturas -->
                <div id="modal-slider-inner" class="flex items-center gap-2 overflow-x-auto pb-1">
                    <!-- Las miniaturas dinamicamente -->
                </div>
            </div>

            <!-- Información del Producto -->
            <div class="md:col-span-6 space-y-4 flex flex-col justify-between h-full">
                <div>
                    <!-- Encabezado con Marca  -->
                    <div class="flex justify-between items-center pr-8 mb-1">
                        <span id="modal-marca" class="text-xs font-extrabold text-pink-600 uppercase tracking-widest block"></span>
                    </div>
                    
                    <h3 id="modal-title" class="text-xl sm:text-2xl font-black text-gray-900 mb-2 leading-tight"></h3>
                    
                    <!-- Precios y Stock -->
                    <div class="p-3 bg-pink-50/70 rounded-2xl border border-pink-100 mb-4">
                        <div class="flex items-baseline gap-2 flex-wrap">
                            <span id="modal-precio" class="text-2xl font-black text-accent-fuchsia"></span>
                        </div>
                        <div class="mt-1 text-xs font-semibold text-gray-600">
                            Precio al Mayor: <span id="modal-precio-mayor" class="text-pink-600 font-bold"></span> 
                            (a partir de <span id="modal-cantidad-mayor" class="font-bold"></span> unids)
                        </div>
                        <span class="text-[10px] text-emerald-600 font-bold flex items-center gap-1 mt-1">
                            <i class="fa-solid fa-circle-check"></i> Stock Disponible: <span id="modal-stock-disponible">0</span>
                        </span>
                    </div>

                    <!-- Descripción -->
                    <div class="space-y-2 mb-4">
                        <h4 class="text-xs font-extrabold text-gray-800 uppercase tracking-wider">Descripción del Producto</h4>
                        <p id="modal-descripcion" class="text-xs text-gray-600 leading-relaxed"></p>
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

                    <?php if ($sesion_activa && $_SESSION["nivel_rol"] == 1){ ?>
                        <button type="submit" id="btn-agregar-carrito"class="w-full bg-accent-fuchsia hover:bg-pink-700 text-white font-bold py-3.5 rounded-2xl text-xs shadow-lg shadow-pink-500/20 transition-all flex items-center justify-center gap-2">
                            <i class="fa-solid fa-bag-shopping"></i> Agregar al Carrito
                        </button>
                    <?php  } else{  ?>
                        <button type="button" class="btn-login w-full bg-accent-fuchsia hover:bg-pink-700 text-white font-bold py-3.5 rounded-2xl text-xs shadow-lg shadow-pink-500/20 transition-all flex items-center justify-center gap-2">
                            <i class="fa-solid fa-bag-shopping"></i> Agregar al Carrito
                        </button>
                    <?php  } ?>
                </form>

            </div>

        </div>
    </div>
</div>

</main>

<script>
    function toggleFilterDrawer(open) {
        const drawer = document.getElementById('offcanvasCategorias');
        const drawerBody = document.getElementById('drawerBody');

        if (open) {
            drawer.classList.remove('hidden');
            document.body.classList.add('overflow-hidden'); 
            if (drawerBody) drawerBody.scrollTop = 0; 
        } else {
            drawer.classList.add('hidden');
            document.body.classList.remove('overflow-hidden');
        }
    }
</script>

  <?php include 'vista/complementos/footer_catalogo.php' ?>
</body>
</html>