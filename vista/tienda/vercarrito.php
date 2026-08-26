<!DOCTYPE html>
<html lang="es">
<head>
    <?php include 'vista/complementos/head_catalogo.php' ?>
    <title>Carrito - Pedido | LoveMakeup C.A </title> 
</head>
<body class="min-h-screen flex flex-col pb-20 lg:pb-0">

    <!-- php CARRITO--> 
<?php include 'vista/complementos/carrito.php' ?>

  <?php include 'vista/complementos/nav_catalogo.php' ?>

    <!-- MAIN CONTENT CONTAINER -->
    <main class="flex-grow max-w-7xl w-full mx-auto px-4 sm:px-6 lg:px-8 py-6">

  <section id="carrito" class="tab-content ">


    <!-- HEADER DE LA SECCIÓN -->
    <div class="mb-6">
        <h2 class="text-2xl font-bold text-gray-900">Carrito de compra</h2>
        <p class="text-s text-gray-500 mt-0.5">Revisa tus productos antes de procesar el pedido</p>
    </div>

    <!-- 2. STEPPER DE PASOS -->
    <div class="mb-8 border-b border-gray-200 pb-4">
    
        <div class="flex items-center justify-center space-x-4 sm:space-x-8 max-w-2xl mx-auto">
            <!-- Paso 1 -->
            <div class="flex items-center space-x-2 text-pink-600 font-semibold">
                <span class="w-7 h-7 rounded-full bg-pink-600 text-white flex items-center justify-center text-xs">1</span>
                <span class="text-xs sm:text-sm">Carrito</span>
            </div>
            <div class="w-8 sm:w-16 h-0.5 bg-gray-200"></div>
            <!-- Paso 2 -->
            <div class="flex items-center space-x-2 text-gray-400">
                <span class="w-7 h-7 rounded-full bg-gray-100 border border-gray-300 flex items-center justify-center text-xs">2</span>
                <span class="text-xs sm:text-sm">Entrega</span>
            </div>
               <div class="w-8 sm:w-16 h-0.5 bg-gray-200"></div>
            <!-- Paso 2 -->
            <div class="flex items-center space-x-2 text-gray-400">
                <span class="w-7 h-7 rounded-full bg-gray-100 border border-gray-300 flex items-center justify-center text-xs">3</span>
                <span class="text-xs sm:text-sm">Pago</span>
            </div>
            <div class="w-8 sm:w-16 h-0.5 bg-gray-200"></div>
            <!-- Paso 3 -->
            <div class="flex items-center space-x-2 text-gray-400">
                <span class="w-7 h-7 rounded-full bg-gray-100 border border-gray-300 flex items-center justify-center text-xs">4</span>
                <span class="text-xs sm:text-sm">Confirmación</span>
            </div>
        </div>
    </div>

    <!-- 3. CONTENIDO DEL CARRITO (PHP) -->
    <?php if (empty($carrito)): ?>
        <!-- ESTADO VACÍO -->
        <div class="bg-white rounded-2xl border border-gray-200 p-8 text-center max-w-md mx-auto my-8 shadow-xs">
            <div class="w-16 h-16 bg-pink-50 text-pink-500 rounded-full flex items-center justify-center mx-auto mb-4">
                <i class="fa-solid fa-cart-shopping text-2xl"></i>
            </div>
            <h3 class="text-lg font-bold text-gray-800 mb-1">¡Tu carrito está vacío!</h3>
            <p class="text-gray-500 text-xs mb-6">Parece que aún no has añadido ningún producto.</p>
            <a href="?pagina=catalogo_producto" class="inline-flex items-center justify-center px-5 py-2.5 bg-pink-600 hover:bg-pink-700 text-white text-xs font-medium rounded-xl transition-colors shadow-xs">
                <i class="fa-solid fa-arrow-left me-2"></i> Ir al Catálogo
            </a>
        </div>

    <?php else: ?>
        <!-- LAYOUT DE 2 COLUMNAS (CARRITO + RESUMEN) -->
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-start">
            
            <!-- TABLA DE PRODUCTOS (Lado Izquierdo - 8 Col) -->
            <div class="lg:col-span-8 bg-white rounded-2xl border border-gray-200 shadow-xs overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="bg-gray-50/50 text-gray-500 text-[11px] uppercase tracking-wider border-b border-gray-200">
                                <th class="py-3 px-4">Acción</th>
                                <th class="py-3 px-4">Producto</th>
                                <th class="py-3 px-4">Precio</th>
                                <th class="py-3 px-4 text-center">Cantidad</th>
                                <th class="py-3 px-4 text-right">Subtotal</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 text-sm">
                            <?php 
                            $total = 0;
                            $total_items = 0;
                            foreach ($carrito as $item):
                                $id = $item['id'];
                                $cantidad = $item['cantidad'];
                                $precioUnitario = $cantidad >= $item['cantidad_mayor'] ? $item['precio_mayor'] : $item['precio_detal'];
                                $subtotal = $cantidad * $precioUnitario;
                                $total += $subtotal;
                                $total_items += $cantidad;
                            ?>
                            <tr data-id="<?= $id ?>" class="hover:bg-gray-50/50 transition-colors">
                                <!-- Botón Eliminar --> 
                                <td class="py-3 px-4">
                                    <button class=" text-gray-400 hover:text-red-500 transition-colors p-1 btn-eliminar" data-id="<?= $id ?>">
                                        <i class="fa-solid fa-xmark text-base"></i>
                                    </button>
                                </td>
                                <!-- Imagen + Nombre -->
                                <td class="py-3 px-4">
                                    <div class="flex items-center space-x-3">
                                        <img src="<?= htmlspecialchars($item['imagen']) ?>" alt="<?= htmlspecialchars($item['nombre']) ?>" class="w-12 h-12 object-cover rounded-lg border border-gray-100 bg-gray-50">
                                        <span class="font-semibold text-gray-800 text-xs sm:text-sm line-clamp-2"><?= htmlspecialchars($item['nombre']) ?></span>
                                    </div>
                                </td>
                                <!-- Precio -->
                                <td class="py-3 px-4 text-xs font-medium text-gray-600 precio-unitario">
                                   $ <?= number_format($precioUnitario, 2) ?>
                                </td>
                                <!-- Control de Cantidad -->
                                <td class="py-3 px-4">
                                    <div class="flex items-center justify-center space-x-1 bg-gray-100 p-1 rounded-lg w-max mx-auto">
                                        <button class="btn-menos w-6 h-6 rounded bg-white text-gray-600 hover:bg-gray-200 flex items-center justify-center text-xs font-bold shadow-xs transition-colors btn-menos" data-id="<?= $id ?>"> − </button>
                                        <span class="cantidad px-2 text-xs font-semibold text-gray-800 cantidad"><?= $cantidad ?></span>
                                        <button class="btn-mas w-6 h-6 rounded bg-white text-gray-600 hover:bg-gray-200 flex items-center justify-center text-xs font-bold shadow-xs transition-colors btn-mas" data-id="<?= $id ?>" data-stock="<?= $item['stockDisponible'] ?>">+</button>
                                    </div>
                                </td>
                                <!-- Subtotal -->
                                <td class="py-3 px-4 text-right font-bold text-gray-900 text-xs sm:text-sm subtotal">
                                    $<?= number_format($subtotal, 2) ?>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
                
                <!-- Botón Volver -->
                <div class="p-4 bg-gray-50/50 border-t border-gray-100">
                    <a href="?pagina=catalogo_producto" class="w-full flex items-center justify-center bg-gray-900 hover:bg-grey-700 text-white font-semibold py-2.5 px-4 rounded-xl text-xs transition-colors shadow-xs">
                        <i class="fa-solid fa-arrow-left me-1.5"></i> Seguir Comprando
                    </a>
                </div>
            </div>

            <!-- RESUMEN DE LA COMPRA (Lado Derecho - 4 Col) -->
            <div class="lg:col-span-4 bg-white rounded-2xl border border-gray-200 p-5 shadow-xs sticky top-4">
                <h3 class="text-base font-bold text-gray-900 mb-4 pb-2 border-b border-gray-100">Resumen del Pedido</h3>
                
                <div class="space-y-3 text-xs mb-4">
                    <div class="flex justify-between text-gray-600">
                        <span>Cantidad de artículos:</span>
                        <span class="font-semibold text-gray-800"><?= $total_items ?> unidad(es)</span>
                    </div>
                    
                </div>

                <div class="border-t border-dashed border-gray-200 pt-3 mb-6">
                    <div class="flex justify-between items-baseline">
                        <span class="text-sm font-bold text-gray-900">Total a Pagar:</span>
                        <span class="text-xl font-extrabold text-emerald-500 total-general">$<span id="total-carrito"><?= number_format($total, 2) ?></span></span>
                    </div>
                </div>

                <!-- ACCIONES Y BOTONES -->
                <div class="space-y-2">
                    <a href="?pagina=Pedidoentrega" id="btn-siguiente" class="w-full flex items-center justify-center bg-emerald-600 hover:bg-emerald-700 text-white font-semibold py-2.5 px-4 rounded-xl text-xs transition-colors shadow-xs">
                        <i class="fa-solid fa-cart-shopping me-2"></i> Continuar con la Compra
                    </a>
                    
                    <a href="?pagina=reserva_cliente" class="w-full flex items-center justify-center text-white bg-sky-600 hover:bg-sky-900 font-semibold py-2.5 px-4 rounded-xl text-xs transition-colors border border-sky-200">
                        <i class="fa-solid fa-bookmark me-2"></i> Reservar Productos
                    </a>
                </div>
            </div>

        </div>
    <?php endif; ?>
<div class="mb-8 border-b border-gray-200 pb-4"></div>
</section>



    </main>
     <script src="assets/js/vercarrito.js"></script>
   <?php include 'vista/complementos/footer_catalogo.php' ?>
</body>
</html>