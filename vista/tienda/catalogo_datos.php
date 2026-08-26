<!DOCTYPE html>
<html lang="es">
<head>
    <?php include 'vista/complementos/head_catalogo.php' ?>
    <title>Mis datos  | LoveMakeup C.A </title> 
</head>
<body class="min-h-screen flex flex-col pb-20 lg:pb-0">

    <!-- php CARRITO--> 
<?php include 'vista/complementos/carrito.php' ?>      
  <?php include 'vista/complementos/nav_catalogo.php' ?>

    <!-- MAIN  CONTAINER -->
  <main class="flex-grow max-w-7xl w-full mx-auto px-4 sm:px-6 lg:px-8 py-6">

        <!--  contactos -->
        <section id="contactos" class="tab-content">
            <div class="mb-6">
                <h2 class="text-2xl font-bold text-gray-900">Mis datos</h2>
                <p class="text-s text-gray-700 mt-0.5">Gestiona tu información personal y la seguridad de tu cuenta. </p>
            </div>


    <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">
        
        <!-- INFORMACIÓN PERSONAL -->
        <div class="lg:col-span-7">
            <div class="bg-white rounded-3xl shadow-sm border border-pink-100 overflow-hidden">
                <div class="px-6 py-4 border-b border-pink-50 bg-gray-50">
                    <h5 class="text-lg font-bold text-gray-800 flex items-center gap-2">
                        <i class="fa-solid fa-id-card text-pink-500"></i> Información Personal
                    </h5>
                </div>
                
                <form action="?pagina=catalogo_datos" method="POST" autocomplete="off" id="u" class="p-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                        
                        <!-- Cedula -->
                        <div class="md:col-span-2">
                            <label for="cedula" class="block text-sm font-semibold text-black mb-1">Cédula</label>
                            <div class="flex relative rounded-xl shadow-sm">
                                <select class="inline-flex items-center px-3 rounded-l-xl border border-r-0 border-gray-300 bg-gray-50 text-gray-700 focus:ring-pink-500 focus:border-pink-500 font-medium sm:text-sm" name="tipo_documento" id="rolSelect2" required>
                                    <option value="<?php echo $_SESSION['documento'] ?>"> <?php echo $_SESSION['documento'] . " (ACTUAL)";?> </option>
                                    <option value="V"> V </option>
                                    <option value="E"> E </option>
                                </select>
                                <input type="text" id="cedula" name="cedula" value="<?php echo $_SESSION['id'] ?>" class="flex-1 block w-full min-w-0 rounded-none rounded-r-xl border border-gray-300 px-3 py-2.5 text-gray-900 focus:ring-pink-500 focus:border-pink-500 sm:text-sm outline-none transition-colors">
                                <input type="hidden" name="cedula_actual" value="<?php echo $_SESSION['id'] ?>">
                            </div>
                            <p id="textocedula" class="text-red-500 text-xs mt-1.5 font-medium"></p>
                        </div>

                        <!-- Nombre -->
                        <div>
                            <label for="nombre" class="block text-sm font-semibold text-black mb-1">Nombre</label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <i class="fa-solid fa-user text-pink-400"></i>
                                </div>
                                <input type="text" id="nombre" name="nombre" value="<?php echo $_SESSION['nombre'] ?>" class="input-con-icono block w-full rounded-xl border border-gray-300 px-3 py-2.5 text-gray-900 focus:ring-pink-500 focus:border-pink-500 sm:text-sm outline-none transition-colors">
                            </div>
                            <p id="textonombre" class="text-red-500 text-xs mt-1.5 font-medium"></p>
                        </div>

                        <!-- Apellido -->
                        <div>
                            <label for="apellido" class="block text-sm font-semibold text-black mb-1">Apellido</label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <i class="fa-solid fa-user text-pink-400"></i>
                                </div>
                                <input type="text" id="apellido" name="apellido" value="<?php echo $_SESSION['apellido'] ?>" class="input-con-icono block w-full rounded-xl border border-gray-300 px-3 py-2.5 text-gray-900 focus:ring-pink-500 focus:border-pink-500 sm:text-sm outline-none transition-colors">
                            </div>
                            <p id="textoapellido" class="text-red-500 text-xs mt-1.5 font-medium"></p>
                        </div>

                        <!-- Telefono -->
                        <div>
                            <label for="telefono" class="block text-sm font-semibold text-black mb-1">Teléfono</label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <i class="fa-solid fa-mobile-screen-button text-pink-400"></i>
                                </div>
                                <input type="text" id="telefono" name="telefono" value="<?php echo $_SESSION['telefono'] ?>" class="input-con-icono block w-full rounded-xl border border-gray-300 px-3 py-2.5 text-gray-900 focus:ring-pink-500 focus:border-pink-500 sm:text-sm outline-none transition-colors">
                            </div>
                            <p id="textotelefono" class="text-red-500 text-xs mt-1.5 font-medium"></p>
                        </div>

                        <!-- Correo -->
                        <div>
                            <label for="correo" class="block text-sm font-semibold text-black mb-1">Correo Electrónico</label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <i class="fa-solid fa-envelope text-pink-400"></i>
                                </div>
                                <input type="email" id="correo" name="correo" value="<?php echo $_SESSION['correo'] ?>" class="input-con-icono block w-full rounded-xl border border-gray-300 px-3 py-2.5 text-gray-900 focus:ring-pink-500 focus:border-pink-500 sm:text-sm outline-none transition-colors">
                                <input type="hidden" name="correo_actual" value="<?php echo $_SESSION['correo'] ?>">
                            </div>
                            <p id="textocorreo" class="text-red-500 text-xs mt-1.5 font-medium"></p>
                        </div>
                    </div>

                    <!-- Botones Formulario 1 -->
                    <div class="mt-6 flex flex-col sm:flex-row justify-end gap-3 pt-5 border-t border-gray-100">
                        <button type="reset" class="px-5 py-2.5 rounded-xl text-white bg-gray-900 hover:bg-gray-800 font-semibold text-sm flex items-center justify-center gap-2 transition-all shadow-sm">
                            <i class="fa-solid fa-repeat"></i> Restaurar
                        </button>
                        <button type="button" id="actualizar" class="px-5 py-2.5 rounded-xl text-white bg-emerald-500 hover:bg-emerald-600 font-semibold text-sm flex items-center justify-center gap-2 transition-all shadow-sm">
                            <i class="fa-solid fa-floppy-disk"></i> Actualizar Datos
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- SEGURIDAD / CLAVE -->
        <div class="lg:col-span-5">
            <div class="bg-white rounded-3xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-100 bg-gray-50">
                    <h5 class="text-lg font-bold text-gray-800 flex items-center gap-2">
                        <i class="fa-solid fa-shield-halved text-pink-500"></i> Seguridad
                    </h5>
                </div>
                
                <form action="?pagina=catalogo_datos" method="POST" autocomplete="off" id="formclave" class="p-6">
                    <div class="space-y-5">
                        
                        <!-- Clave Actual -->
                        <div>
                            <label for="clave" class="block text-sm font-semibold text-black mb-1">Clave actual</label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <i class="fa-solid fa-key text-gray-400"></i>
                                </div>
                                
                                <input type="text" id="clave" name="clave" class="input-con-icono block w-full rounded-xl border border-gray-300 px-3 py-2.5 text-gray-900 focus:ring-gray-500 focus:border-gray-500 sm:text-sm outline-none transition-colors">
                            </div>
                            <p id="textoclave" class="text-red-500 text-xs mt-1.5 font-medium"></p>
                        </div>

                        <!-- Clave Nueva -->
                        <div>
                            <label for="clavenueva" class="block text-sm font-semibold text-black mb-1">Clave nueva</label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <i class="fa-solid fa-unlock text-pink-400"></i>
                                </div>
                                <input type="text" id="clavenueva" name="clavenueva" class="input-con-icono block w-full rounded-xl border border-gray-300 px-3 py-2.5 text-gray-900 focus:ring-pink-500 focus:border-pink-500 sm:text-sm outline-none transition-colors">
                            </div>
                            <p id="textoclavenueva" class="text-red-500 text-xs mt-1.5 font-medium"></p>
                        </div>

                        <!-- Confirmar Clave Nueva -->
                        <div>
                            <label for="clavenuevac" class="block text-sm font-semibold text-black mb-1">Confirmar clave nueva</label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <i class="fa-solid fa-lock text-pink-400"></i>
                                </div>
                                <input type="text" id="clavenuevac" name="clavenuevac" class="input-con-icono block w-full rounded-xl border border-gray-300 px-3 py-2.5 text-gray-900 focus:ring-pink-500 focus:border-pink-500 sm:text-sm outline-none transition-colors">
                            </div>
                            <p id="textoclavenuevac" class="text-red-500 text-xs mt-1.5 font-medium"></p>
                        </div>

                        <input type="hidden" name="persona" value="<?php echo $_SESSION['id_usuario'] ?>">
                    </div>

                    <!-- Botones Formulario 2 -->
                    <div class="mt-6 flex flex-col sm:flex-row justify-end gap-3 pt-5 border-t border-gray-100">
                        <button type="reset" class="px-5 py-2.5 rounded-xl text-white bg-gray-900 hover:bg-gray-800 font-semibold text-sm flex items-center justify-center gap-2 transition-all shadow-sm">
                            <i class="fa-solid fa-eraser"></i> Limpiar
                        </button>
                        <button type="button" id="actualizarclave" class="px-5 py-2.5 rounded-xl text-white bg-emerald-500 hover:bg-emerald-600 font-semibold text-sm flex items-center justify-center gap-2 transition-all shadow-sm">
                            <i class="fa-solid fa-key"></i> Cambiar Clave
                        </button>
                    </div>
                </form>
            </div>
        </div>

    </div>
</div>


            
        </section>

      

  </main>
     <script src="assets/js/catalago_datos.js"></script>

  <?php include 'vista/complementos/footer_catalogo.php' ?>
</body>
</html>