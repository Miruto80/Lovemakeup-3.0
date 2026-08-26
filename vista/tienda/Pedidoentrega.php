<!DOCTYPE html>
<html lang="es">
<head>
    <?php include 'vista/complementos/head_catalogo.php' ?>
    <title>Entrega - Pedido | LoveMakeup C.A </title> 
</head>
<body class="min-h-screen flex flex-col pb-20 lg:pb-0">

    <!-- php CARRITO--> 
<?php include 'vista/complementos/carrito.php' ?>

  <?php include 'vista/complementos/nav_catalogo.php' ?>

    <!-- MAIN CONTENT CONTAINER -->
    <main class="flex-grow max-w-7xl w-full mx-auto px-4 sm:px-6 lg:px-8 py-6">

  <!-- sección de entrega -->
<section id="entrega" class="tab-content">
  <div class="mb-6">
    <h2 class="text-2xl font-bold text-gray-900">Método de Entrega</h2>
    <p class="text-s text-gray-600 mt-0.5">Selecciona cómo deseas recibir tu pedido o visítanos en nuestra tienda física</p>
  </div>

      <!-- 2. STEPPER DE PASOS -->
    <div class="mb-8 border-b border-gray-200 pb-4">
    
        <div class="flex items-center justify-center space-x-4 sm:space-x-8 max-w-2xl mx-auto">
            <!-- Paso 1 -->
            <div class="flex items-center space-x-2 text-emerald-400">
                <span class="w-7 h-7 rounded-full bg-emerald-600 text-white flex items-center justify-center text-xs">1</span>
                <span class="text-xs sm:text-sm">Carrito</span>
            </div>
            <div class="w-8 sm:w-16 h-0.5 bg-gray-200"></div>
            <!-- Paso 2 -->
            <div class="flex items-center space-x-2 text-pink-600 font-semibold">
                <span class="w-7 h-7 rounded-full bg-pink-600 text-white flex items-center justify-center text-xs">2</span>
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

  <form id="formEntrega" class="bg-white rounded-2xl shadow-lg border border-slate-100 p-6 md:p-8 space-y-8">
    
    <!-- SELECCIÓN DE MÉTODO DE ENTREGA -->
    <div>
      <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
        <!-- Opción 1: Tienda física -->
        <label for="op1" class="relative flex flex-col items-center justify-center p-5 border-2 rounded-xl cursor-pointer transition-all duration-200 border-slate-200 bg-white hover:border-pink-300 hover:bg-pink-50/40 has-[:checked]:border-pink-500 has-[:checked]:bg-pink-50/50 has-[:checked]:text-pink-600 has-[:checked]:shadow-sm group">
          <input type="radio" id="op1" name="metodo_entrega" value="4" class="sr-only">
          <div class="w-10 h-10 rounded-full bg-slate-100 group-hover:bg-pink-100 group-has-[:checked]:bg-pink-500 group-has-[:checked]:text-white flex items-center justify-center text-slate-600 mb-3 transition-colors">
            <i class="fa-solid fa-shop text-lg"></i>
          </div>
          <span class="font-semibold text-sm text-slate-700 group-has-[:checked]:text-pink-600">Tienda física</span>
          <span class="text-xs text-slate-400 mt-0.5">Retiro presencial</span>
        </label>

        <!-- Opción 2: Envíos nacionales -->
        <label for="op2" class="relative flex flex-col items-center justify-center p-5 border-2 rounded-xl cursor-pointer transition-all duration-200 border-slate-200 bg-white hover:border-pink-300 hover:bg-pink-50/40 has-[:checked]:border-pink-500 has-[:checked]:bg-pink-50/50 has-[:checked]:text-pink-600 has-[:checked]:shadow-sm group">
          <input type="radio" id="op2" name="metodo_entrega" value="2" class="sr-only">
          <div class="w-10 h-10 rounded-full bg-slate-100 group-hover:bg-pink-100 group-has-[:checked]:bg-pink-500 group-has-[:checked]:text-white flex items-center justify-center text-slate-600 mb-3 transition-colors">
            <i class="fa-solid fa-truck text-lg"></i>
          </div>
          <span class="font-semibold text-sm text-slate-700 group-has-[:checked]:text-pink-600">Envíos nacionales</span>
          <span class="text-xs text-slate-400 mt-0.5">Agencias de envío</span>
        </label>

        <!-- Opción 3: Delivery -->
        <label for="op3" class="relative flex flex-col items-center justify-center p-5 border-2 rounded-xl cursor-pointer transition-all duration-200 border-slate-200 bg-white hover:border-pink-300 hover:bg-pink-50/40 has-[:checked]:border-pink-500 has-[:checked]:bg-pink-50/50 has-[:checked]:text-pink-600 has-[:checked]:shadow-sm group">
          <input type="radio" id="op3" name="metodo_entrega" value="1" class="sr-only">
          <div class="w-10 h-10 rounded-full bg-slate-100 group-hover:bg-pink-100 group-has-[:checked]:bg-pink-500 group-has-[:checked]:text-white flex items-center justify-center text-slate-600 mb-3 transition-colors">
            <i class="fa-solid fa-motorcycle text-lg"></i>
          </div>
          <span class="font-semibold text-sm text-slate-700 group-has-[:checked]:text-pink-600">Delivery</span>
          <span class="text-xs text-slate-400 mt-0.5">Envío local express</span>
        </label>
      </div>
    </div>

    <input type="hidden" name="continuar_entrega" value="1">

    <!-- CONTENEDOR DE FORMULARIO DINÁMICO -->
    <div id="formulario-opciones" class="transition-all duration-300"></div>

    <!-- BOTONES DE NAVEGACIÓN -->
    <div class="pt-6 border-t border-slate-100 flex flex-col-reverse sm:flex-row justify-between items-center gap-3">
      <a href="?pagina=vercarrito" id="btn-atras" class="w-full sm:w-auto px-6 py-3 bg-gray-900 hover:bg-gray-700 text-white font-medium rounded-xl transition-colors duration-150 inline-flex items-center justify-center text-sm">
        <i class="fa-solid fa-arrow-left me-2"></i> Regresar al carrito
      </a>
      <button type="button" id="btn-continuar-entrega" class="w-full sm:w-auto px-8 py-3 bg-emerald-500 hover:bg-emerald-600 text-white font-semibold rounded-xl shadow-md shadow-pink-200 transition-all duration-150 inline-flex items-center justify-center text-sm">
        Continuar al Pago <i class="fa-solid fa-arrow-right ms-2"></i>
      </button>
    </div>
  </form>
</section>


<!-- Tienda física -->
<template id="form-op1">
  <div class="bg-slate-50/80 p-5 rounded-2xl border border-pink-100 space-y-3">
    <div class="flex items-center space-x-3 text-slate-800 font-semibold text-base border-b border-slate-200 pb-2">
      <i class="fa-solid fa-store text-pink-500"></i>
      <span>Retiro en Tienda Física</span>
    </div>
    <div>
      <label for="retira" class="block text-xs font-medium text-slate-600 mb-1.5">
        Ubicación principal: Av. 20 entre calles 29 y 30, C.C. Barquisimeto Plaza, Estado Lara.
      </label>
      <input type="text" class="w-full px-4 py-2.5 bg-white border border-pink-200 rounded-xl text-slate-700 text-sm focus:outline-none cursor-not-allowed font-medium" name="direccion_envio" id="retira" value="Retiro en Tienda Física (Barquisimeto)" readonly>
    </div>
  </div>
</template>

<!-- Envíos Nacionales -->
<template id="form-op2">
  <div class="bg-slate-50/80 p-5 rounded-2xl border border-slate-200/80 space-y-4">
    <div class="flex items-center space-x-3 text-slate-800 font-semibold text-base border-b border-slate-200 pb-2">
      <i class="fa-solid fa-boxes-packing text-pink-500"></i>
        <span>Datos para Envío Nacional</span>
    </div>
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
      <div>
        <label class="block text-xs font-semibold text-slate-700 mb-1">Empresa de Envío</label>
        <select name="empresa_envio" class="w-full px-3.5 py-2.5 bg-white border border-slate-300 rounded-xl text-slate-800 text-sm focus:ring-2 focus:ring-pink-400 focus:border-pink-400 focus:outline-none">
          <option value="">Selecciona agencia</option>
          <?php foreach($metodos_entrega as $me): ?>
            <?php if (in_array($me['id_entrega'], [2,3])): ?>
              <option value="<?= $me['id_entrega'] ?>"><?= $me['nombre'] ?></option>
            <?php endif; ?>
          <?php endforeach; ?>
        </select>
      </div>
      <div>
        <label for="codigoSucursal" class="block text-xs font-semibold text-slate-700 mb-1">Código de Sucursal</label>
        <input type="text" name="sucursal_envio" class="w-full px-3.5 py-2.5 bg-white border border-slate-300 rounded-xl text-slate-800 text-sm focus:ring-2 focus:ring-pink-400 focus:border-pink-400 focus:outline-none" id="codigoSucursal" placeholder="Ej. 2140">
      </div>
    </div>
    <div>
      <label for="nombreSucursal" class="block text-xs font-semibold text-slate-700 mb-1">Nombre / Dirección de la Sucursal</label>
      <input type="text" name="direccion_envio" class="w-full px-3.5 py-2.5 bg-white border border-slate-300 rounded-xl text-slate-800 text-sm focus:ring-2 focus:ring-pink-400 focus:border-pink-400 focus:outline-none" id="nombreSucursal" placeholder="Ej. MRW Sucursal Barquisimeto Centro">
    </div>
  </div>
</template>

<!--Delivery -->
<template id="form-op3">
  <div class="bg-slate-50/80 p-5 rounded-2xl border border-slate-200/80 space-y-4">
    <div class="flex items-center space-x-3 text-slate-800 font-semibold text-base border-b border-slate-200 pb-2">
      <i class="fa-solid fa-motorcycle text-pink-500"></i>
      <span>Detalles de Entrega Express (Delivery)</span>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
      <div>
        <label for="delivery" class="block text-xs font-semibold text-slate-700 mb-1">Servicio de Delivery</label>
        <select id="delivery" name="id_delivery" class="w-full px-3.5 py-2.5 bg-white border border-slate-300 rounded-xl text-slate-800 text-sm focus:ring-2 focus:ring-pink-400 focus:border-pink-400 focus:outline-none">
          <option value="">Seleccione repartidor o empresa</option>
          <?php foreach ($delivery_activos as $d): ?>
            <option value="<?= $d['id_delivery'] ?>"
                data-nombre="<?= $d['nombre'] ?>"
                data-tipo="<?= $d['tipo'] ?>"
                data-contacto="<?= $d['contacto'] ?>"
            >
              <?= $d['tipo'] ?> --- <?= $d['nombre'] ?>
            </option>
          <?php endforeach; ?>
        </select>

        <input type="hidden" id="id_delivery">
        <input type="hidden" name="delivery_nombre" id="delivery_nombre">
        <input type="hidden" name="delivery_tipo" id="delivery_tipo">
        <input type="hidden" name="delivery_contacto" id="delivery_contacto">
      </div>

      <div>
        <label for="zona" class="block text-xs font-semibold text-slate-700 mb-1">Zona</label>
        <select id="zona" name="zona" class="w-full px-3.5 py-2.5 bg-white border border-slate-300 rounded-xl text-slate-800 text-sm focus:ring-2 focus:ring-pink-400 focus:border-pink-400 focus:outline-none">
          <option value="">-- Selecciona una zona --</option>
          <option value="norte">Norte</option>
          <option value="sur">Sur</option>
          <option value="este">Este</option>
          <option value="oeste">Oeste</option>
          <option value="centro">Centro</option>
        </select>
      </div>

      <div>
        <label for="parroquia" class="block text-xs font-semibold text-slate-700 mb-1">Parroquia</label>
        <select id="parroquia" name="parroquia" class="w-full px-3.5 py-2.5 bg-white border border-slate-300 rounded-xl text-slate-800 text-sm focus:ring-2 focus:ring-pink-400 focus:border-pink-400 focus:outline-none">
          <option value="">-- Selecciona parroquia --</option>
        </select>
      </div>

      <div>
        <label for="sector" class="block text-xs font-semibold text-slate-700 mb-1">Sector / Urbanización</label>
        <select id="sector" name="sector" class="w-full px-3.5 py-2.5 bg-white border border-slate-300 rounded-xl text-slate-800 text-sm focus:ring-2 focus:ring-pink-400 focus:border-pink-400 focus:outline-none">
          <option value="">-- Selecciona sector --</option>
        </select>
      </div>
    </div>

    <div>
      <label for="direccion" class="block text-xs font-semibold text-slate-700 mb-1">Punto de referencia y dirección exacta</label>
      <input type="text" name="direccion_envio" class="w-full px-3.5 py-2.5 bg-white border border-slate-300 rounded-xl text-slate-800 text-sm focus:ring-2 focus:ring-pink-400 focus:border-pink-400 focus:outline-none" id="direccion" placeholder="Ej. Av. Lara con Av. Los Leones, edif. X, piso 2, apto 2B">
    </div>
  </div>
</template>
     

    </main>
   <?php include 'vista/complementos/footer_catalogo.php' ?>

<script src="assets/js/Pedidoentrega.js"></script>

  </body>
</html>