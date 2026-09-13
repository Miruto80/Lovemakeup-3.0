<?php

$carrito = $_SESSION['carrito'] ?? [];
?> 


<!-- BODY OFFCANVA CARRITO -->
<div id="cartDrawer" class="fixed inset-0 z-50 overflow-hidden pointer-events-none transition-opacity duration-300 opacity-0">
    <div class="absolute inset-0 bg-gray-900/40 backdrop-blur-xs transition-opacity" onclick="toggleCartDrawer(false)"></div>

    <div class="fixed inset-y-0 right-0 max-w-full flex pl-10">
        <div id="cartDrawerPanel" class="w-screen max-w-md bg-white shadow-2xl transform translate-x-full transition-transform duration-300 pointer-events-auto flex flex-col">
            
            <!-- Drawer Header -->
            <div class="p-5 border-b border-pink-100 flex items-center justify-between bg-pink-50/50">
                <div class="flex items-center gap-2">
                    <i class="fa-solid fa-cart-shopping  text-accent-fuchsia text-lg"></i>
                    <h3 class="font-extrabold text-gray-900 text-base">Carrito de Compras</h3>
                    <span class="bg-pink-700 text-pink-100 text-xs font-bold px-2 py-0.5 rounded-full contador">
                        <?php echo count($carrito); ?>
                    </span>
                </div>
                <button onclick="toggleCartDrawer(false)" class="w-8 h-8 rounded-full bg-white text-gray-400 hover:text-gray-700 flex items-center justify-center text-sm shadow-xs transition-colors">
                    <i class="fa-solid fa-xmark"></i>
                </button>
            </div>

            <!-- Drawer Body (Items) -->
            <div class="flex-grow p-5 overflow-y-auto space-y-3 carrito-dropdown" id="listgroup">
                <?php if (empty($carrito)): ?>
                    <!-- Carrito Vacío -->
                    <div class="flex flex-col items-center justify-center h-full py-10 text-center">
                        <svg class="w-16 h-16 text-gray-400 mb-3" fill="currentColor" viewBox="0 0 231.523 231.523" xml:space="preserve">
                            <g>
                                <path d="M107.415,145.798c0.399,3.858,3.656,6.73,7.451,6.73c0.258,0,0.518-0.013,0.78-0.04c4.12-0.426,7.115-4.111,6.689-8.231 l-3.459-33.468c-0.426-4.12-4.113-7.111-8.231-6.689c-4.12,0.426-7.115,4.111-6.689,8.231L107.415,145.798z"></path>
                                <path d="M154.351,152.488c0.262,0.027,0.522,0.04,0.78,0.04c3.796,0,7.052-2.872,7.451-6.73l3.458-33.468 c0.426-4.121-2.569-7.806-6.689-8.231c-4.123-0.421-7.806,2.57-8.232,6.689l-3.458,33.468 C147.235,148.377,150.23,152.062,154.351,152.488z"></path>
                                <path d="M96.278,185.088c-12.801,0-23.215,10.414-23.215,23.215c0,12.804,10.414,23.221,23.215,23.221 c12.801,0,23.216-10.417,23.216-23.221C119.494,195.502,109.079,185.088,96.278,185.088z M96.278,216.523 c-4.53,0-8.215-3.688-8.215-8.221c0-4.53,3.685-8.215,8.215-8.215c4.53,0,8.216,3.685,8.216,8.215 C104.494,212.835,100.808,216.523,96.278,216.523z"></path>
                                <path d="M173.719,185.088c-12.801,0-23.216,10.414-23.216,23.215c0,12.804,10.414,23.221,23.216,23.221 c12.802,0,23.218-10.417,23.218-23.221C196.937,195.502,186.521,185.088,173.719,185.088z M173.719,216.523 c-4.53,0-8.216-3.688-8.216-8.221c0-4.53,3.686-8.215,8.216-8.215c4.531,0,8.218,3.685,8.218,8.215 C181.937,212.835,178.251,216.523,173.719,216.523z"></path>
                                <path d="M218.58,79.08c-1.42-1.837-3.611-2.913-5.933-2.913H63.152l-6.278-24.141c-0.86-3.305-3.844-5.612-7.259-5.612H18.876 c-4.142,0-7.5,3.358-7.5,7.5s3.358,7.5,7.5,7.5h24.94l6.227,23.946c0.031,0.134,0.066,0.267,0.104,0.398l23.157,89.046 c0.86,3.305,3.844,5.612,7.259,5.612h108.874c3.415,0,6.399-2.307,7.259-5.612l23.21-89.25C220.49,83.309,220,80.918,218.58,79.08z M183.638,165.418H86.362l-19.309-74.25h135.895L183.638,165.418z"></path>
                                <path d="M105.556,52.851c1.464,1.463,3.383,2.195,5.302,2.195c1.92,0,3.84-0.733,5.305-2.198c2.928-2.93,2.927-7.679-0.003-10.607 L92.573,18.665c-2.93-2.928-7.678-2.927-10.607,0.002c-2.928,2.93-2.927,7.679,0.002,10.607L105.556,52.851z"></path>
                                <path d="M159.174,55.045c1.92,0,3.841-0.733,5.306-2.199l23.552-23.573c2.928-2.93,2.925-7.679-0.005-10.606 c-2.93-2.928-7.679-2.925-10.606,0.005l-23.552,23.573c-2.928,2.93-2.925,7.679,0.005,10.607 C155.338,54.314,157.256,55.045,159.174,55.045z"></path>
                                <path d="M135.006,48.311c0.001,0,0.001,0,0.002,0c4.141,0,7.499-3.357,7.5-7.498l0.008-33.311c0.001-4.142-3.356-7.501-7.498-7.502 c-0.001,0-0.001,0-0.001,0c-4.142,0-7.5,3.357-7.501,7.498l-0.008,33.311C127.507,44.951,130.864,48.31,135.006,48.311z"></path>
                            </g>
                        </svg>
                        <p class="text-gray-500 font-medium">El carrito está vacío</p>
                    </div>
                <?php else: ?>
                    <!-- Lista de Productos -->
                    <?php
                    $total = 0;
                    foreach ($carrito as $item):
                        $id = $item['id'];
                        $cantidad = $item['cantidad'];
                        $precioUnitario = $cantidad >= $item['cantidad_mayor'] ? $item['precio_mayor'] : $item['precio_detal'];
                        $subtotal = $cantidad * $precioUnitario;
                        $total += $subtotal;
                    ?>
                        <div class="flex items-center justify-between p-3 bg-gray-50 rounded-xl border border-gray-100" data-id="<?= $id ?>">
                            <div class="pr-2">
                                <h6 class="text-sm font-semibold text-gray-800 leading-snug"><?= htmlspecialchars($item['nombre']) ?></h6>
                                <span class="text-xs text-gray-500 cantidad-texto"><?= $cantidad ?> x $<?= number_format($precioUnitario, 2) ?></span>
                            </div>
                            <div class="text-end flex flex-col items-end gap-1">
                                <span class="text-sm font-bold text-gray-900 subtotal-texto">$<?= number_format($subtotal, 2) ?></span>
                                <button class="btn-eliminar text-xs text-red-500 hover:text-red-700 hover:bg-red-50 p-1 rounded-md transition-colors" data-id="<?= $id ?>" title="Eliminar producto">
                                    <i class="fa-solid fa-trash-can"></i> Quitar
                                </button>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>

            <!-- Drawer Footer -->
            <div class="p-5 border-t border-pink-100 bg-white space-y-3">
                <div class="flex justify-between items-center text-xs text-gray-500">
                    <span>Subtotal (USD):</span>
                    <strong class="text-sm font-bold text-gray-900" id="cartSubtotalUSD">$<?= number_format($total ?? 0, 2) ?></strong>
                </div>

                <a href="?pagina=vercarrito" id="carritover"
                   class="w-full bg-accent-fuchsia hover:bg-pink-700 text-white font-bold py-3 rounded-2xl text-sm shadow-md transition-all flex items-center justify-center gap-2 <?= empty($carrito) ? 'opacity-50 pointer-events-none' : '' ?>">
                    Ver Carrito <i class="fa-solid fa-arrow-right text-xs"></i>
                </a>
            </div>

        </div>
    </div>
</div>


<script src="assets/js/carrito.js?v=<?= filemtime('assets/js/carrito.js') ?>"></script>
