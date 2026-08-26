<!DOCTYPE html>
<html lang="es">
<head>
    <?php include 'vista/complementos/head_catalogo.php' ?>
    <title>Compra Exitosa  | LoveMakeup C.A </title> 
</head>
<body class="min-h-screen flex flex-col pb-20 lg:pb-0">

    <!-- php CARRITO--> 
<?php include 'vista/complementos/carrito.php' ?>

  <?php include 'vista/complementos/nav_catalogo.php' ?>

    <!-- MAIN CONTENT CONTAINER -->
    <main class="flex-grow max-w-7xl w-full mx-auto px-4 sm:px-6 lg:px-8 py-6">

      <!--  pedidoextiso -->
      <section id="pedidoextiso" class="tab-content">

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
            <div class="flex items-center space-x-2 text-emerald-400">
                  <span class="w-7 h-7 rounded-full bg-emerald-600 text-white flex items-center justify-center text-xs">2</span>
                  <span class="text-xs sm:text-sm">Entrega</span>
              </div>
               <div class="w-8 sm:w-16 h-0.5 bg-gray-200"></div>
            <!-- Paso 2 -->
            <div class="flex items-center space-x-2 text-emerald-400">
                <span class="w-7 h-7 rounded-full bg-emerald-600 text-white flex items-center justify-center text-xs">3</span>
                <span class="text-xs sm:text-sm">Pago</span>
            </div>
            <div class="w-8 sm:w-16 h-0.5 bg-gray-200"></div>
            <!-- Paso 3 -->
           <div class="flex items-center space-x-2 text-pink-600 font-semibold">
                <span class="w-7 h-7 rounded-full bg-pink-600 text-white flex items-center justify-center text-xs">4</span>
                <span class="text-xs sm:text-sm">Confirmación</span>
            </div>
        </div>
    </div>

            <div class="max-w-xl mx-auto my-8 bg-white rounded-2xl shadow-lg border border-gray-100 p-8 text-center space-y-6">
      
              <!-- Ícono Ilustrativo -->
              <div class="inline-flex items-center justify-center w-16 h-16 bg-emerald-100 text-emerald-500 rounded-full shadow-inner">
                  <i class="fa-solid fa-circle-check text-3xl"></i>
              </div>

              <!-- Título y Descripción -->
              <div class="space-y-2">
                  <h2 class="text-2xl font-extrabold text-gray-900 tracking-tight">¡Tu pedido se ha registrado con éxito!</h2>
                  <p class="text-s text-gray-600 leading-relaxed max-w-md mx-auto">
                      ¡Muchas gracias por tu compra! <strong>Tu pago se encuentra actualmente en proceso de verificación</strong>  por nuestro equipo. Una vez validado, prepararemos tu pedido para el despacho o retiro.
                  </p>
              </div>

              <!-- Bloque Informativo de Soporte -->
              <div class="bg-gray-50 border border-gray-100 rounded-xl p-4 text-s text-gray-600 space-y-2">
                  <p class="font-medium text-gray-800 flex items-center justify-center gap-1.5">
                      <i class="fa-solid fa-headset text-pink-500"></i> ¿Tienes alguna duda o inconveniente?
                  </p>
                  <p>
                      Si necesitas ayuda o realizar un ajuste en tu orden, comunícate con nuestro equipo de atención a través de  
                      <a href="https://wa.me/584245115414" target="_blank" class="text-pink-600 font-semibold hover:underline inline-flex items-center gap-1">
                          <i class="fa-brands fa-whatsapp"></i> WhatsApp
                      </a>
                  </p>
              </div>

              <!-- Cierre y Botón de Acción -->
              <div class="pt-2 space-y-4">
                  <p class="text-xs font-semibold text-gray-800 uppercase tracking-wider">¡Agradecemos tu confianza y preferencia!</p>
                  
                  <div>
                      <a href="?pagina=catalogo_pedido" class="inline-flex items-center justify-center gap-2 w-full sm:w-auto px-6 py-2.5 bg-gray-900 hover:bg-gray-600 text-white font-semibold text-sm rounded-xl shadow-xs transition-all duration-200">
                          <i class="fa-solid fa-bag-shopping"></i> Continuar comprando
                      </a>
                  </div>
              </div>

          </div>

<div class="mb-8 border-b border-gray-200 pb-4"></div>
            
      </section>

  </main>
  <?php include 'vista/complementos/footer_catalogo.php' ?>
</body>
</html>