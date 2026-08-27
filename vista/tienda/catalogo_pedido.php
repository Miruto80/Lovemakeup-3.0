<!DOCTYPE html>
<html lang="es">
<head>
    <?php include 'vista/complementos/head_catalogo.php' ?>
    <title>Mis Pedidos | LoveMakeup C.A </title> 
</head>
<body class="min-h-screen flex flex-col pb-20 lg:pb-0">

    <!-- php CARRITO--> 
<?php include 'vista/complementos/carrito.php' ?>

  <?php include 'vista/complementos/nav_catalogo.php' ?>

    <!-- MAIN CONTENT CONTAINER -->
    <main class="flex-grow max-w-7xl w-full mx-auto px-4 sm:px-6 lg:px-8 py-6">

        <!--  contactos -->
    <section id="pedidos" class="tab-content">

     <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-6 pb-4 border-b border-pink-100">
        <div>
            <h2 class="text-2xl font-bold text-gray-900">Mis Pedidos</h2>
            <p class="text-s text-gray-500 mt-0.5">Historial de tus compras y pedidos con su estado de entrega</p>
        </div>
        <div class="flex items-center gap-2">
            <span id="productCountBadge" class="bg-black text-white text-xs font-bold px-3 py-2 rounded-full">
               <?= count($pedidos)  ?>  <?= count($pedidos) === 1 ? 'Pedido' : ' Pedidos' ?>
            </span>
        </div>
    </div> 

            <!-- REJILLA DE TARJETAS DE PEDIDOS -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-5">
        <?php if (!empty($pedidos)): ?>
        <?php foreach ($pedidos as $pedido): 
            $estatus_texto = [
                '0' => 'Rechazado', '1' => 'Verificar pago', '2' => 'Pago Verificado',
                '3' => 'Pendiente envío', '4' => 'En camino', '5' => 'Entregado'
            ];
            $tipo_texto = ['1' => 'Tienda', '2' => 'Web', '3' => 'Reserva'];

            // Badges con colores pastel en Tailwind
            $badgeStyle = match ((string)$pedido['estatus']) {
                '0' => 'bg-red-600 text-white border-red-700',
                '1' => 'bg-amber-400 text-black border-amber-500',
                '2' => 'bg-blue-50 text-blue-700 border-blue-200',
                '3' => 'bg-purple-700 text-white border-purple-600',
                '4' => 'bg-sky-500 text-white border-sky-600',
                '5' => 'bg-emerald-500 text-white border-emerald-600',
                default => 'bg-gray-500 text-white border-gray-600'
            };
        ?>
            <div class="bg-white border border-pink-100 rounded-3xl p-5 shadow-sm hover:shadow-md transition-all flex flex-col justify-between">
                <div>
                    <!-- Cabecera de la Card -->
                    <div class="flex items-center justify-between pb-3 border-b border-pink-50 mb-4">
                        <div class="flex items-center gap-2">
                            <span class="w-20 h-8 rounded-3xl bg-pink-50 text-accent-fuchsia flex items-center justify-center text-xs font-bold">
                                N° <?= $pedido['id_pedido'] ?>
                            </span>
                            <span class="text-xs font-semibold text-black">
                                <?= $tipo_texto[$pedido['tipo']] ?? 'Pedido' ?>
                            </span>
                        </div>
                        <span class="text-[11px] font-semibold px-2.5 py-1 rounded-full border <?= $badgeStyle ?>">
                            <?= $estatus_texto[$pedido['estatus']] ?? 'Desconocido' ?>
                        </span>
                    </div>

                    <!-- Datos Principales -->
                    <div class="space-y-2 text-xs text-gray-600 mb-4">
                        <div class="flex justify-between items-center">
                            <span class="text-gray-600">Fecha:</span>
                            <span class="font-medium text-gray-800"><?= date('d/m/Y h:i A', strtotime($pedido['fecha'])) ?></span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-gray-600">Método de Pago:</span>
                            <span class="font-medium text-gray-800"><?= htmlspecialchars($pedido['metodo_pago'] ?? 'N/A') ?></span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-gray-600">Entrega:</span>
                            <span class="font-medium text-gray-800"><?= htmlspecialchars($pedido['metodo_entrega'] ?? 'N/A') ?></span>
                        </div>
                        <?php if(!empty($pedido['precio_total_bs'])): ?>
                            <div class="flex justify-between items-center pt-2 border-t border-pink-50">
                                <span class="font-bold text-black">Total Bs:</span>
                                <span class="font-extrabold text-accent-fuchsia text-sm">Bs. <?= number_format($pedido['precio_total_bs'], 2,',', '.') ?></span>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Botón de Acción -->
                <button type="button" 
                        onclick="openModal('modalPedido<?= $pedido['id_pedido'] ?>')"
                        class="w-full py-2.5 bg-pink-50 text-accent-fuchsia hover:bg-accent-fuchsia hover:text-white rounded-2xl text-xs font-bold flex items-center justify-center gap-2 transition-all">
                    <i class="fa-solid fa-eye"></i>
                    <span>Ver Detalles de la Compra</span>
                </button>
            </div>
        <?php endforeach; ?>
        <?php else: ?>

    <!-- Estado Vacío (Sin Pedidos) -->
    <div class="col-span-full flex flex-col items-center justify-center py-12 px-4 text-center bg-white border border-pink-100/60 rounded-3xl shadow-xs">
        <div class="w-16 h-16 bg-pink-50 text-accent-fuchsia rounded-full flex items-center justify-center mb-4 shadow-inner">
            <i class="fa-solid fa-receipt text-2xl"></i>
        </div>
        <h1 class="text-base font-bold text-gray-800 mb-1">Aún no tienes pedidos registrados</h1>
        <p class="text-s text-black max-w-sm mb-6">Explora nuestro catálogo y realiza tu primera compra para ver el seguimiento aquí.</p>
        <a href="?pagina=catalogo_producto" class="px-6 py-2.5 bg-black hover:bg-gray-700 text-white rounded-2xl text-xs font-bold transition-all shadow-md shadow-pink-500/20 flex items-center gap-2">
            <i class="fa-solid fa-border-all"></i>
            <span>Ir al Catálogo</span>
        </a>
    </div>

<?php endif; ?>
    </div>       
    </section>
            
    </main>

  <!-- MODALES DE DETALLES ) -->
<?php if (isset($pedidos) && !empty($pedidos)): ?>
    <?php foreach ($pedidos as $pedido): ?>
        <div id="modalPedido<?= $pedido['id_pedido'] ?>" class="fixed inset-0 z-50 flex items-center justify-center p-4 hidden">
           
            <div onclick="closeModal('modalPedido<?= $pedido['id_pedido'] ?>')" class="fixed inset-0 bg-gray-900/60 backdrop-blur-xs"></div>

            <!-- Contenido Modal -->
            <div class="relative bg-white w-full max-w-3xl max-h-[90vh] rounded-3xl shadow-2xl flex flex-col z-10 overflow-hidden">
                <!-- Header -->
                <div class="p-5 bg-pink-500 from-pink-500 to-accent-fuchsia text-white flex items-center justify-between">
                    <div>
                        <h3 class="font-bold text-base">Detalles del Pedido #<?= $pedido['id_pedido'] ?></h3>
                        <p class="text-[13px] opacity-90"><?= date('d/m/Y - h:i A', strtotime($pedido['fecha'])) ?></p>
                    </div>
                    <button type="button" 
                            onclick="closeModal('modalPedido<?= $pedido['id_pedido'] ?>')"
                            class="w-8 h-8 rounded-full bg-white/20 hover:bg-white/30 flex items-center justify-center text-sm transition-colors">
                        <i class="fa-solid fa-xmark"></i>
                    </button>
                </div>

                <!-- Body con Scroll -->
                <div class="p-6 overflow-y-auto space-y-4 text-xs">
                    
                    <!-- Información de Cliente y Pago -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div class="p-4 bg-pink-50/40 border border-pink-100 rounded-2xl space-y-1.5 text-[14px]">
                            <h4 class="font-bold text-gray-900 border-b border-pink-100 pb-1 mb-2 flex items-center gap-1.5">
                                <i class="fa-solid fa-user text-accent-fuchsia"></i> Cliente
                            </h4>
                            <p><strong class="text-black">Nombre:</strong> <?= htmlspecialchars($pedido['nombre_cliente'] ?? '') ?> <?= htmlspecialchars($pedido['apellido_cliente'] ?? '') ?></p>
                            <p><strong class="text-black">Teléfono:</strong> <?= htmlspecialchars($pedido['telefono_emisor'] ?? 'N/A') ?></p>
                            <p><strong class="text-black">Estatus:</strong> <?= htmlspecialchars($estatus_texto[$pedido['estatus']] ?? '') ?></p>
                        </div>

                        <div class="p-4 bg-pink-50/40 border border-pink-100 rounded-2xl space-y-1.5 text-[14px]">
                            <h4 class="font-bold text-gray-900 border-b border-pink-100 pb-1 mb-2 flex items-center gap-1.5">
                                <i class="fa-solid fa-credit-card text-accent-fuchsia"></i> Pago Y Entrega
                            </h4>
                            <p><strong class="text-black">Método Pago:</strong> <?= htmlspecialchars($pedido['metodo_pago'] ?? 'N/A') ?></p>
                            <?php if (!empty($pedido['banco'])): ?>
                                <p><strong class="text-black">Banco:</strong> <?= htmlspecialchars($pedido['banco']) ?> &rarr; <?= htmlspecialchars($pedido['banco_destino'] ?? '') ?></p>
                            <?php endif; ?>
                            <?php if (!empty($pedido['referencia_bancaria'])): ?>
                                <p><strong class="text-black">Ref:</strong> <?= htmlspecialchars($pedido['referencia_bancaria']) ?></p>
                            <?php endif; ?>
                            <p><strong class="text-black">Entrega:</strong> <?= htmlspecialchars($pedido['metodo_entrega'] ?? 'N/A') ?></p>
                        </div>
                    </div>

                    <!-- Comprobante / Dirección si aplican -->
                    <?php if (!empty($pedido['direccion']) || !empty($pedido['imagen'])): ?>
                        <div class="p-4 bg-gray-50 border border-gray-100 rounded-2xl space-y-2 text-[14px]">
                            <?php if (!empty($pedido['direccion'])): ?>
                                <p><strong class="text-black">Dirección de Envío:</strong><br><?= nl2br(htmlspecialchars($pedido['direccion'])) ?></p>
                            <?php endif; ?>
                            <?php if (!empty($pedido['imagen'])): ?>
                                <div>
                                    <strong class="text-gray-700 block mb-1">Comprobante de Pago:</strong>
                                    <a href="<?= htmlspecialchars($pedido['imagen']) ?>" target="_blank">
                                        <img src="<?= htmlspecialchars($pedido['imagen']) ?>" alt="Comprobante" class="h-28 rounded-xl border object-cover hover:opacity-90">
                                    </a>
                                </div>
                            <?php endif; ?>
                        </div>
                    <?php endif; ?>

                    <!-- Productos Comprados -->
                    <div class="border border-pink-100 rounded-2xl overflow-hidden">
                        <table class="w-full text-left border-collapse">
                            <thead>
                                <tr class="bg-pink-50 text-gray-800 font-bold border-b border-pink-100">
                                    <th class="p-2.5">Producto</th>
                                    <th class="p-2.5 text-center">Cant.</th>
                                    <th class="p-2.5 text-right">Precio U.</th>
                                    <th class="p-2.5 text-right">Subtotal</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-pink-50">
                                <?php 
                                    $total = 0;
                                    if (isset($pedido['detalles']) && is_array($pedido['detalles'])):
                                    foreach ($pedido['detalles'] as $detalle):
                                        $subtotal = $detalle['cantidad'] * $detalle['precio_unitario'];
                                        $total += $subtotal;
                                ?>
                                    <tr class="hover:bg-pink-50/20">
                                        <td class="p-2.5 font-medium text-gray-800"><?= htmlspecialchars($detalle['nombre']) ?></td>
                                        <td class="p-2.5 text-center"><?= $detalle['cantidad'] ?></td>
                                        <td class="p-2.5 text-right">$<?= number_format($detalle['precio_unitario'], 2) ?></td>
                                        <td class="p-2.5 text-right font-semibold">$<?= number_format($subtotal, 2) ?></td>
                                    </tr>
                                <?php endforeach; endif; ?>
                            </tbody>
                            <tfoot>
                                <tr class="bg-pink-50/50 font-bold text-gray-900 border-t border-pink-100">
                                    <td colspan="3" class="p-2.5 text-right">Total USD:</td>
                                    <td class="p-2.5 text-right text-accent-fuchsia text-sm">$<?= number_format($total, 2) ?></td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>

                </div>
            </div>
        </div>
    <?php endforeach; ?>
<?php endif; ?>

   <?php include 'vista/complementos/footer_catalogo.php' ?>
  
<script>
    function openModal(id) {
        document.getElementById(id)?.classList.remove('hidden');
        document.body.classList.add('overflow-hidden');
    }

    function closeModal(id) {
        document.getElementById(id)?.classList.add('hidden');
        document.body.classList.remove('overflow-hidden');
    }
</script>
</body>
</html>