<?php

// Recuperar datos de entrega y carrito
$entrega = $_SESSION['pedido_entrega'];
$carrito = $_SESSION['carrito'];

// Calcular total USD
$total = 0;
foreach ($carrito as $item) {
    $cantidad = $item['cantidad'];
    $precioUnitario = $cantidad >= $item['cantidad_mayor'] ? $item['precio_mayor'] : $item['precio_detal'];
    $total += $cantidad * $precioUnitario;
}

?>
<!DOCTYPE html>
<html lang="es">
<head>
    <?php include 'vista/complementos/head_catalogo.php' ?>
    <title>Pago - pedido | LoveMakeup C.A </title> 
</head>
<body class="min-h-screen flex flex-col pb-20 lg:pb-0">
<!----  tasa dolar   --->
<script>
async function obtenerTasaDolarApi() {
    try {
        const respuesta = await fetch('https://ve.dolarapi.com/v1/dolares/oficial');
        if (!respuesta.ok) {
            throw new Error(`Error HTTP: ${respuesta.status}`);
        }
        const datos = await respuesta.json();
        const tasaBCV = parseFloat(datos.promedio).toFixed(2); 
        var totalBs = <?php echo $total; ?>;
        var resultadoBs = (totalBs * tasaBCV).toFixed(2); 
        var resultadoForBs = new Intl.NumberFormat('es-VE', {
            minimumFractionDigits: 2,
            maximumFractionDigits: 2
        }).format(resultadoBs);

        document.getElementById("bs").textContent = "Bs " + resultadoForBs;  
        document.getElementById("precio_total_bs").value = resultadoBs ;  
    } catch (error) {
        document.getElementById("bs").textContent = "Error al cargar el total";
        console.error("Error al obtener la tasa:", error);
    }
}
document.addEventListener("DOMContentLoaded", obtenerTasaDolarApi);
</script>
<!----  tasa dolar   --->
    <!-- php CARRITO--> 
<?php include 'vista/complementos/carrito.php' ?>

  <?php include 'vista/complementos/nav_catalogo.php' ?>

    <!-- MAIN CONTENIDO  -->
    <main class="flex-grow max-w-7xl w-full mx-auto px-4 sm:px-6 lg:px-8 py-6">

        <!--  PAGO -->
       <section id="pago" class="tab-content">
    <div class="mb-6">
        <h2 class="text-2xl font-bold text-gray-900">Completar Pago</h2>
        <p class="text-s text-gray-700 mt-0.5">Selecciona el destino de tu Pago Móvil y adjunta el comprobante.</p>
    </div>

    <!-- PASOS -->
    <div class="mb-8 border-b border-gray-200 pb-4 px-2">
        <div class="flex items-center justify-between sm:justify-center sm:space-x-8 max-w-2xl mx-auto">
            <!-- Paso 1 -->
            <div class="flex items-center space-x-1 sm:space-x-2 text-emerald-400 shrink-0">
                <span class="w-7 h-7 rounded-full bg-emerald-600 text-white flex items-center justify-center text-xs shrink-0">1</span>
                <span class="text-[11px] sm:text-sm">Carrito</span>
            </div>
            <!-- Divisor 1 -->
            <div class="h-0.5 bg-gray-200 flex-1 mx-1.5 sm:flex-none sm:w-16"></div>
            <!-- Paso 2 -->
            <div class="flex items-center space-x-1 sm:space-x-2 text-emerald-400 shrink-0">
                <span class="w-7 h-7 rounded-full bg-emerald-600 text-white flex items-center justify-center text-xs shrink-0">2</span>
                <span class="text-[11px] sm:text-sm">Entrega</span>
            </div>
            <!-- Divisor 2 -->
            <div class="h-0.5 bg-gray-200 flex-1 mx-1.5 sm:flex-none sm:w-16"></div>
            <!-- Paso 3 -->
            <div class="flex items-center space-x-1 sm:space-x-2 text-pink-400 font-semibold shrink-0">
                <span class="w-7 h-7 rounded-full bg-pink-600 text-white flex items-center justify-center text-xs shrink-0">3</span>
                <span class="text-[11px] sm:text-sm">Pago</span>
            </div>
            <!-- Divisor 3 -->
            <div class="h-0.5 bg-gray-200 flex-1 mx-1.5 sm:flex-none sm:w-16"></div>
            <!-- Paso 4 -->
            <div class="flex items-center space-x-1 sm:space-x-2 text-gray-400 shrink-0">
                <span class="w-7 h-7 rounded-full bg-gray-100 border border-gray-300 flex items-center justify-center text-xs shrink-0">4</span>
                <span class="text-[11px] sm:text-sm">Confirmación</span>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
        
        <!-- COLUMNA IZQUIERDA: Datos de Pago Móvil + Formulario -->
        <div class="lg:col-span-7 space-y-6">
            
            <!-- Card Informativa: Datos de Pago Móvil -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="bg-pink-500 text-white px-4 py-2.5 flex justify-between items-center">
                    <span class="font-semibold text-xs sm:text-sm flex items-center gap-2">
                        <i class="fa-solid fa-mobile-screen-button"></i> Datos para Pago Móvil
                    </span>
                    <button type="button" onclick="copiarDatosPagoMovil()" class="bg-white text-gray-800 text-xs font-semibold px-2.5 py-1 rounded-md shadow-xs hover:bg-gray-50 transition-colors flex items-center gap-1">
                        <i class="fa-regular fa-copy"></i> Copiar Datos
                    </button>
                </div>
                <div class="p-4 bg-gray-50/50">
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-xs text-gray-800">
                        <div class="sm:border-r sm:border-gray-200 sm:pr-4 space-y-1">
                            <span class="inline-block bg-gray-200 text-gray-700 font-medium px-2 py-0.5 rounded text-[11px] mb-1">Banco de Venezuela (0102)</span>
                            <div><span class="font-semibold text-gray-600">C.I:</span> V-30.352.937</div>
                            <div><span class="font-semibold text-gray-600">Telf:</span> 0414-5094959</div>
                        </div>
                        <div class="space-y-1">
                            <span class="inline-block bg-gray-200 text-gray-700 font-medium px-2 py-0.5 rounded text-[11px] mb-1">Banco Mercantil (0105)</span>
                            <div><span class="font-semibold text-gray-600">C.I:</span> V-11.787.299</div>
                            <div><span class="font-semibold text-gray-600">Telf:</span> 0426-5541364</div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Formulario de Registro de Pago -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5 sm:p-6">
                <form id="formPago" class="grid grid-cols-1 sm:grid-cols-2 gap-4" enctype="multipart/form-data">
                    
                    <!-- Flag AJAX -->
                    <input type="hidden" name="continuar_pago" value="1">

                    <!-- Datos ocultos de entrega -->
                    <?php foreach (['id_metodoentrega','direccion_envio','sucursal_envio','empresa_envio','zona','parroquia','sector'] as $field):
                        if (isset($entrega[$field])):
                            $val = htmlspecialchars($entrega[$field], ENT_QUOTES);
                    ?>
                    <input type="hidden" name="<?= $field ?>" value="<?= $val ?>">
                    <?php endif; endforeach; ?>

                    <!-- Datos ocultos de persona -->
                    <input type="hidden" name="id_persona" value="<?= $_SESSION['id'] ?>">

                    <!-- Datos ocultos de carrito -->
                    <?php foreach ($carrito as $i => $item): ?>
                    <input type="hidden" name="carrito[<?= $i ?>][id]" value="<?= $item['id'] ?>">
                    <input type="hidden" name="carrito[<?= $i ?>][cantidad]" value="<?= $item['cantidad'] ?>">
                    <input type="hidden" name="carrito[<?= $i ?>][cantidad_mayor]" value="<?= $item['cantidad_mayor'] ?>">
                    <input type="hidden" name="carrito[<?= $i ?>][precio_detal]" value="<?= $item['precio_detal'] ?>">
                    <input type="hidden" name="carrito[<?= $i ?>][precio_mayor]" value="<?= $item['precio_mayor'] ?>">
                    <?php endforeach; ?>

                    <!-- Totales -->
                    <input type="hidden" name="precio_total_usd" id="precio_total_usd" value="<?= $total ?>">
                    <input type="hidden" name="precio_total_bs" id="precio_total_bs" value="">
                    
                    <!-- Método de Pago ID -->
                    <input type="hidden" value="1" name="id_metodopago" id="metodopago">

                    <!-- Selects de Banco -->
                    <div class="sm:col-span-1">
                        <label for="banco" class="block text-xs font-semibold text-gray-700 mb-1">Banco Origen</label>
                        <select name="banco" id="banco" class="w-full px-3.5 py-2.5 bg-white border border-slate-300 rounded-xl text-slate-800 text-sm focus:ring-2 focus:ring-pink-400 focus:border-pink-400 focus:outline-none" required>
                            <option value="" disabled selected>Selecciona origen...</option>
                            <option value="0102-Banco De Venezuela">0102 - Banco De Venezuela</option>
                            <option value="0156-100% Banco">0156 - 100% Banco</option>
                            <option value="0172-Bancamiga Banco Universal,C.A">0172 - Bancamiga</option>
                            <option value="0114-Bancaribe">0114 - Bancaribe</option>
                            <option value="0171-Banco Activo">0171 - Banco Activo</option>
                            <option value="0166-Banco Agricola De Venezuela">0166 - Banco Agrícola</option>
                            <option value="0128-Bancon Caroni">0128 - Banco Caroní</option>
                            <option value="0163-Banco Del Tesoro">0163 - Banco del Tesoro</option>
                            <option value="0175-Banco Digital De Los Trabajadores, Banco Universal">0175 - BDT (Trabajadores)</option>
                            <option value="0115-Banco Exterior">0115 - Banco Exterior</option>
                            <option value="0151-Banco Fondo Comun">0151 - BFC Banco Fondo Común</option>
                            <option value="0173-Banco Internacional De Desarrollo">0173 - B.I.D</option>
                            <option value="0105-Banco Mercantil">0105 - Banco Mercantil</option>
                            <option value="0191-Banco Nacional De Credito">0191 - BNC</option>
                            <option value="0138-Banco Plaza">0138 - Banco Plaza</option>
                            <option value="0137-Banco Sofitasa">0137 - Banco Sofitasa</option>
                            <option value="0104-Banco Venezolano De Credito">0104 - Venezolano de Crédito</option>
                            <option value="0168-Bancrecer">0168 - Bancrecer</option>
                            <option value="0134-Banesco">0134 - Banesco</option>
                            <option value="0177-Banfanb">0177 - Banfanb</option>
                            <option value="0146-Bangente">0146 - Bangente</option>
                            <option value="0174-Banplus">0174 - Banplus</option>
                            <option value="0108-BBVA Provincial">0108 - BBVA Provincial</option>
                            <option value="0157-Delsur Banco Universal">0157 - Delsur</option>
                            <option value="0601-Instituto Municipal De Credito Popular">0601 - IMCP</option>
                            <option value="0178-N58 Banco Digital Banco Microfinanciero S.A">0178 - N58 Banco Digital</option>
                            <option value="0169-R4 Banco Microfinanciero C.A.">0169 - R4 Banco Microfinanciero</option>
                        </select>
                    </div>

                    <div class="sm:col-span-1">
                        <label for="banco_destino" class="block text-xs font-semibold text-gray-700 mb-1">Banco Destino</label>
                        <select name="banco_destino" id="banco_destino" class="w-full px-3.5 py-2.5 bg-white border border-slate-300 rounded-xl text-slate-800 text-sm focus:ring-2 focus:ring-pink-400 focus:border-pink-400 focus:outline-none" required>
                            <option value="0102-Banco De Venezuela">0102 - Banco De Venezuela</option>
                            <option value="0105-Banco Mercantil">0105 - Banco Mercantil</option>
                        </select>
                    </div>

                    <!-- Referencia y Teléfono -->
                    <div class="sm:col-span-1">
                        <label for="referencia_bancaria" class="block text-xs font-semibold text-gray-700 mb-1">Referencia Bancaria</label>
                        <input type="text" name="referencia_bancaria" id="referencia_bancaria" placeholder="Ej: 00123456" class="w-full px-3.5 py-2.5 bg-white border border-slate-300 rounded-xl text-slate-800 text-sm focus:ring-2 focus:ring-pink-400 focus:border-pink-400 focus:outline-none" required>
                    </div>

                    <div class="sm:col-span-1">
                        <label for="telefono_emisor" class="block text-xs font-semibold text-gray-700 mb-1">Teléfono Emisor</label>
                        <input type="tel" name="telefono_emisor" id="telefono_emisor" placeholder="Ej: 04121234567" class="w-full px-3.5 py-2.5 bg-white border border-slate-300 rounded-xl text-slate-800 text-sm focus:ring-2 focus:ring-pink-400 focus:border-pink-400 focus:outline-none" required>
                    </div> 

                    <!-- Comprobante -->
                    <div class="sm:col-span-2">
                        <label for="imagen" class="block text-xs font-semibold text-gray-700 mb-1">Subir comprobante</label>
                        <input type="file" name="imagen" id="imagen" accept=".jpg, .jpeg, .png, .webp" class="block w-full text-xs text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-xs file:font-semibold file:bg-pink-50 file:text-pink-700 hover:file:bg-pink-100 border border-gray-300 rounded-lg cursor-pointer" required>
                    </div>

                    <!-- Vista previa -->
                    <div class="sm:col-span-2 flex justify-center">
                        <img id="preview" alt="Vista previa del comprobante" class="hidden max-h-72 object-contain border border-gray-200 rounded-lg p-1 shadow-xs mt-2">
                    </div>
                    

                    <!-- Términos y Condiciones -->
                    <div class="sm:col-span-2 mt-1">
                        <label class="inline-flex items-center gap-2 cursor-pointer">
                            <input type="checkbox" id="che" class="sr-only peer">
                            <div class="relative w-9 h-5 bg-gray-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full rtl:peer-checked:after:-translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:start-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-4 after:w-4 after:transition-all peer-checked:bg-pink-500"></div>
                            <span class="text-xs text-gray-600">
                                Acepto los 
                                <button type="button" onclick="abrirModalTerminos()" class="underline text-pink-600 font-semibold hover:text-pink-700">
                                    Términos y Condiciones
                                </button>
                            </span>
                        </label>
                    </div>

                    <!-- Botón de Envío -->
                    <div class="sm:col-span-2 mt-2">
                        <button type="button" id="btn-guardar-pago" disabled class="w-full py-2.5 px-4 bg-emerald-500 hover:bg-emerald-600 text-white font-bold text-sm rounded-lg shadow-sm disabled:opacity-50 disabled:cursor-not-allowed transition-all flex items-center justify-center gap-2">
                            <span>Realizar Pago</span>
                            <i class="fa-solid fa-credit-card"></i>
                        </button>
                        <p class="text-center text-xs text-gray-500 mt-2 flex items-center justify-center gap-1">
                            <i class="fa-solid fa-shield-halved text-gray-400"></i> Compra con confianza, tu mejor elección te espera.
                        </p>
                    </div>
                </form>
            </div>
        </div>

        <!-- COLUMNA DERECHA: Datos del Delivery y Resumen -->
        <div class="lg:col-span-5 space-y-4">
            
            <!-- Datos del Delivery (si existen) -->
            <?php if (!empty($entrega['delivery_nombre'])): ?>
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4">
                <h3 class="font-bold text-sm text-pink-600 mb-2 flex items-center gap-2">
                    <i class="fa-solid fa-truck-fast"></i> Datos del Delivery
                </h3>
                <div class="text-xs text-gray-700 space-y-1">
                    <p><span class="font-semibold text-gray-900">Nombre:</span> <?= htmlspecialchars($entrega['delivery_nombre'] ?? '—') ?></p>
                    <p><span class="font-semibold text-gray-900">Transporte:</span> <?= htmlspecialchars($entrega['delivery_tipo'] ?? '-') ?></p>
                    <p><span class="font-semibold text-gray-900">Contacto:</span> <?= htmlspecialchars($entrega['delivery_contacto'] ?? '-') ?></p>
                </div>
            </div>
            <?php endif; ?>

            <!-- Resumen del Pedido -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4">
                <div class="flex justify-between items-center mb-3 pb-2 border-b border-gray-100">
                    <h3 class="font-bold text-sm text-pink-600 flex items-center gap-2">
                        <i class="fa-solid fa-receipt"></i> Resumen del Pedido
                    </h3>
                    <span class="text-xs bg-gray-100 text-gray-600 font-medium px-2 py-0.5 rounded-full border border-gray-200">
                        <?= count($carrito) ?> producto<?= count($carrito) !== 1 ? 's' : '' ?>
                    </span>
                </div>

                <!-- Lista de productos -->
                <div class="max-h-64 overflow-y-auto pr-1 divide-y divide-gray-100">
                    <?php foreach($carrito as $item): 
                        $precio = $item['cantidad'] >= $item['cantidad_mayor']
                                    ? $item['precio_mayor']
                                    : $item['precio_detal'];
                        $subtotal = $item['cantidad'] * $precio;
                    ?>
                        <div class="py-2.5 flex items-center gap-3">
                            <img src="<?= htmlspecialchars($item['imagen']) ?>" alt="" class="w-12 h-12 object-cover rounded-md border border-gray-200 shrink-0">
                            <div class="flex-1 min-w-0 text-xs">
                                <p class="font-semibold text-gray-800 truncate"><?= htmlspecialchars($item['nombre']) ?></p>
                                <p class="text-gray-500">Cant: <?= $item['cantidad'] ?> × $<?= number_format($precio, 2) ?></p>
                            </div>
                            <div class="text-xs font-bold text-gray-900 shrink-0">
                                $<?= number_format($subtotal, 2) ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>

                <!-- Totales -->
                <div class="pt-3 mt-2 border-t border-gray-100 space-y-1.5 text-s">
                    <div class="flex justify-between items-center">
                        <span class="text-gray-600 font-medium">Total USD:</span>
                        <span class="text-s font-bold text-emerald-600">$<?= number_format($total, 2) ?></span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-gray-600 font-medium">Total Bs:</span>
                        <span class="text-s font-bold text-emerald-600" id="bs">0,00</span>
                    </div>
                </div>
            </div>

            <!-- Botón Volver -->
            <div>
                <a href="?pagina=Pedidoentrega" class="w-full inline-flex items-center justify-center gap-2 px-6 py-3 bg-gray-900 hover:bg-gray-700 text-white font-medium rounded-xl transition-colors duration-150 inline-flex items-center justify-center text-sm">
                    <i class="fa-solid fa-arrow-left"></i> Volver a Entrega
                </a>
            </div>

        </div>
    </div>
</section>


    <div class="my-2 pt-2 border-t border-gray-200 flex items-baseline justify-between gap-1"> </div>


<!-- Modal Términos y Condiciones -->
<div id="scrollableModal" class="fixed inset-0 z-50 hidden overflow-y-auto bg-gray-900/50 backdrop-blur-xs flex items-center justify-center p-4">
    <div class="relative w-full max-w-2xl bg-white rounded-xl shadow-xl border border-gray-100 max-h-[90vh] flex flex-col overflow-hidden">
        
        <!-- Header del Modal -->
        <div class="px-6 py-4 border-b border-gray-100 flex justify-between items-center bg-white sticky top-0 z-10">
            <h3 class="text-base font-bold text-gray-900 flex items-center gap-2">
                <i class="fa-solid fa-file-contract text-pink-500"></i> Términos y Condiciones
            </h3>
            <button type="button" onclick="cerrarModalTerminos()" class="text-gray-400 hover:text-gray-600 p-1.5 rounded-lg hover:bg-gray-100 transition-colors">
                <i class="fa-solid fa-xmark text-lg"></i>
            </button>
        </div>

        <!-- Cuerpo con Scroll Interno -->
        <div class="p-6 overflow-y-auto space-y-3 divide-y-0 text-xs">
            
            <!-- Item 1 -->
            <details class="group bg-gray-50/70 border border-gray-200/80 rounded-lg overflow-hidden transition-all duration-200" open>
                <summary class="flex justify-between items-center p-3.5 cursor-pointer font-semibold text-gray-800 hover:bg-gray-100/70 transition-colors select-none">
                    <span>1. Generalidades</span>
                    <i class="fa-solid fa-chevron-down text-gray-400 text-xs group-open:rotate-180 transition-transform duration-200"></i>
                </summary>
                <div class="px-3.5 pb-3.5 pt-1 text-gray-600 leading-relaxed border-t border-gray-200/50">
                    Al acceder y utilizar este sitio web, usted acepta cumplir con los presentes Términos y Condiciones. Estos aplican a todas las compras realizadas a través de nuestra plataforma de comercio electrónico.
                </div>
            </details>

            <!-- Item 2 -->
            <details class="group bg-gray-50/70 border border-gray-200/80 rounded-lg overflow-hidden transition-all duration-200">
                <summary class="flex justify-between items-center p-3.5 cursor-pointer font-semibold text-gray-800 hover:bg-gray-100/70 transition-colors select-none">
                    <span>2. Productos y Precios</span>
                    <i class="fa-solid fa-chevron-down text-gray-400 text-xs group-open:rotate-180 transition-transform duration-200"></i>
                </summary>
                <div class="px-3.5 pb-3.5 pt-1 text-gray-600 leading-relaxed border-t border-gray-200/50">
                    Todos los productos ofrecidos están sujetos a disponibilidad. Nos reservamos el derecho de modificar precios, descripciones y condiciones de venta sin previo aviso.
                </div>
            </details>

            <!-- Item 3 -->
            <details class="group bg-gray-50/70 border border-gray-200/80 rounded-lg overflow-hidden transition-all duration-200">
                <summary class="flex justify-between items-center p-3.5 cursor-pointer font-semibold text-gray-800 hover:bg-gray-100/70 transition-colors select-none">
                    <span>3. Proceso de Compra</span>
                    <i class="fa-solid fa-chevron-down text-gray-400 text-xs group-open:rotate-180 transition-transform duration-200"></i>
                </summary>
                <div class="px-3.5 pb-3.5 pt-1 text-gray-600 leading-relaxed border-t border-gray-200/50">
                    El cliente debe verificar cuidadosamente los detalles del producto antes de confirmar su compra. Una vez realizado el pago, no se aceptan modificaciones ni cancelaciones del pedido.
                </div>
            </details>

            <!-- Item 4 -->
            <details class="group bg-gray-50/70 border border-gray-200/80 rounded-lg overflow-hidden transition-all duration-200">
                <summary class="flex justify-between items-center p-3.5 cursor-pointer font-semibold text-gray-800 hover:bg-gray-100/70 transition-colors select-none">
                    <span>4. Pagos</span>
                    <i class="fa-solid fa-chevron-down text-gray-400 text-xs group-open:rotate-180 transition-transform duration-200"></i>
                </summary>
                <div class="px-3.5 pb-3.5 pt-1 text-gray-600 leading-relaxed border-t border-gray-200/50">
                    Aceptamos los métodos de pago indicados en el sitio web. Todos los pagos deben realizarse en su totalidad antes del envío del producto.
                </div>
            </details>

            <!-- Item 5 -->
            <details class="group bg-gray-50/70 border border-gray-200/80 rounded-lg overflow-hidden transition-all duration-200">
                <summary class="flex justify-between items-center p-3.5 cursor-pointer font-semibold text-gray-800 hover:bg-gray-100/70 transition-colors select-none">
                    <span>5. Envíos</span>
                    <i class="fa-solid fa-chevron-down text-gray-400 text-xs group-open:rotate-180 transition-transform duration-200"></i>
                </summary>
                <div class="px-3.5 pb-3.5 pt-1 text-gray-600 leading-relaxed border-t border-gray-200/50">
                    Los tiempos de entrega son estimados y pueden variar según la ubicación y condiciones externas. No nos hacemos responsables por retrasos ocasionados por terceros.
                </div>
            </details>

            <!-- Item 6 -->
            <details class="group bg-pink-50/50 border border-pink-200/80 rounded-lg overflow-hidden transition-all duration-200">
                <summary class="flex justify-between items-center p-3.5 cursor-pointer font-semibold text-pink-900 hover:bg-pink-100/50 transition-colors select-none">
                    <span>6. Política de No Devoluciones</span>
                    <i class="fa-solid fa-chevron-down text-pink-400 text-xs group-open:rotate-180 transition-transform duration-200"></i>
                </summary>
                <div class="px-3.5 pb-3.5 pt-1 text-gray-700 leading-relaxed border-t border-pink-200/50 space-y-2">
                    <p class="font-semibold text-pink-700">No aceptamos devoluciones ni cambios bajo ninguna circunstancia.</p>
                    <p>Al realizar una compra, el cliente reconoce y acepta esta política.</p>
                    <p>En caso de recibir un producto defectuoso o incorrecto, se deberá contactar al servicio de atención al cliente dentro de las 48 horas siguientes a la recepción para evaluar posibles soluciones.</p>
                </div>
            </details>

            <!-- Item 7 -->
            <details class="group bg-gray-50/70 border border-gray-200/80 rounded-lg overflow-hidden transition-all duration-200">
                <summary class="flex justify-between items-center p-3.5 cursor-pointer font-semibold text-gray-800 hover:bg-gray-100/70 transition-colors select-none">
                    <span>7. Responsabilidad</span>
                    <i class="fa-solid fa-chevron-down text-gray-400 text-xs group-open:rotate-180 transition-transform duration-200"></i>
                </summary>
                <div class="px-3.5 pb-3.5 pt-1 text-gray-600 leading-relaxed border-t border-gray-200/50">
                    No nos responsabilizamos por el uso indebido de los productos adquiridos. Nuestra responsabilidad se limita al valor del producto adquirido.
                </div>
            </details>

            <!-- Item 8 -->
            <details class="group bg-gray-50/70 border border-gray-200/80 rounded-lg overflow-hidden transition-all duration-200">
                <summary class="flex justify-between items-center p-3.5 cursor-pointer font-semibold text-gray-800 hover:bg-gray-100/70 transition-colors select-none">
                    <span>8. Propiedad Intelectual</span>
                    <i class="fa-solid fa-chevron-down text-gray-400 text-xs group-open:rotate-180 transition-transform duration-200"></i>
                </summary>
                <div class="px-3.5 pb-3.5 pt-1 text-gray-600 leading-relaxed border-t border-gray-200/50">
                    Todo el contenido del sitio web (textos, imágenes, logotipos, etc.) está protegido por derechos de autor y no puede ser reproducido sin autorización.
                </div>
            </details>

        </div>

        <!-- Footer del Modal -->
        <div class="px-6 py-3.5 border-t border-gray-100 bg-gray-50 flex justify-end">
            <button type="button" onclick="cerrarModalTerminos()" class="px-4 py-2 bg-pink-500 hover:bg-pink-600 text-white font-semibold text-xs rounded-lg transition-colors shadow-xs">
                Entendido
            </button>
        </div>

    </div>
</div>

<!-- Script de Copiado -->
<script>
  document.getElementById('imagen').addEventListener('change', function(e) {
    const file = e.target.files[0];
    const preview = document.getElementById('preview');

    if (file) {
        const reader = new FileReader();
        
        reader.onload = function(e) {
            preview.src = e.target.result;
            preview.classList.remove('hidden'); // Muestra la imagen quitando la clase hidden
        };

        reader.readAsDataURL(file);
    } else {
        preview.src = '';
        preview.classList.add('hidden'); // Oculta la imagen agregando la clase hidden
    }
});
  document.getElementById('scrollableModal')?.addEventListener('click', function(e) {
    if (e.target === this) {
        cerrarModalTerminos();
    }
});
  function abrirModalTerminos() {
      document.getElementById('scrollableModal').classList.remove('hidden');
  }

function cerrarModalTerminos() {
    document.getElementById('scrollableModal').classList.add('hidden');
}
function copiarDatosPagoMovil() {
    const texto = "Mercantil (0105) V11787299 04265541364";
   navigator.clipboard.writeText(texto).then(() => {
        Swal.fire({
            toast: true,
            position: 'top',
            icon: 'success',
            title: '¡Datos copiados!',
            text: 'Información guardada en el portapapeles.',
            showConfirmButton: false,
            timer: 1200,
            timerProgressBar: true,
            customClass: {
                popup: 'rounded-xl shadow-lg border border-gray-100'
            }
        });
    }).catch(err => {
        console.error("Error al copiar: ", err);
        Swal.fire({
            toast: true,
            position: 'center',
            icon: 'error',
            title: 'No se pudo copiar',
            showConfirmButton: false,
            timer: 2000
        });
    });
}
</script>

      

  </main>
  <?php include 'vista/complementos/footer_catalogo.php' ?>
  <script src="assets/js/Pedidopago.js"></script>
</body>
</html>