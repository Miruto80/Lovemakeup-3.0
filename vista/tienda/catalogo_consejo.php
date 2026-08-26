<!DOCTYPE html>
<html lang="es">
<head>
    <?php include 'vista/complementos/head_catalogo.php' ?>
    <title>Consejos | LoveMakeup C.A </title> 
</head>
<body class="min-h-screen flex flex-col pb-20 lg:pb-0">

    <!-- php CARRITO--> 
<?php include 'vista/complementos/carrito.php' ?>
  <?php include 'vista/complementos/nav_catalogo.php' ?>

    <!-- MAIN CONTENT CONTAINER -->
    <main class="flex-grow max-w-7xl w-full mx-auto px-4 sm:px-6 lg:px-8 py-6">

       <!-- VISTA CONSEJOS -->
<section id="view-consejos" class="tab-content">
    <div class="mb-6">
        <h2 class="text-2xl font-bold text-gray-900">Consejos</h2>
        <p class="text-s text-gray-500 mt-0.5">Aprende a potenciar tu imagen, cuidar tu piel y elegir los mejores productos</p>
    </div>

    <!-- Rejilla de Cards de Consejos -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
        
        <!-- Card 1 -->
        <article class="bg-white border border-pink-100 rounded-3xl overflow-hidden shadow-sm hover:shadow-md transition-all duration-300 flex flex-col group">
            <div class="relative overflow-hidden aspect-[4/3] bg-pink-50">
                <img src="assets/img/Consejos/maquillaje_autoestima.jpg" alt="Maquillaje y autoestima" 
                     class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
                <span class="absolute top-3 left-3 bg-white/90 backdrop-blur-md text-accent-fuchsia text-[10px] font-bold px-3 py-1 rounded-full uppercase shadow-xs">
                    Bienestar
                </span>
            </div>
            <div class="p-5 flex flex-col flex-1">
                <div class="flex items-center gap-2 text-[11px] text-gray-400 font-medium mb-2">
                    <i class="fa-regular fa-calendar text-xs"></i>
                    <span>15 May 2025</span>
                </div>
                <h3 class="font-bold text-gray-900 text-sm mb-2 group-hover:text-accent-fuchsia transition-colors line-clamp-2">
                    Maquillaje como impulsor de la autoestima
                </h3>
                <p class="text-xs text-gray-500 line-clamp-3 mb-4 flex-1">
                    Descubre cómo el maquillaje puede transformar tu confianza personal y potenciar tu bienestar emocional...
                </p>
                <button type="button" onclick="openConsejoModal('modalConsejo1')"
                        class="w-full bg-pink-50 text-accent-fuchsia font-bold py-2.5 rounded-2xl text-xs hover:bg-accent-fuchsia hover:text-white transition-all duration-300 flex items-center justify-center gap-2 group/btn">
                    <span>Leer más</span>
                    <i class="fa-solid fa-arrow-right text-xs group-hover/btn:translate-x-1 transition-transform"></i>
                </button>
            </div>
        </article>

        <!-- Card 2 -->
        <article class="bg-white border border-pink-100 rounded-3xl overflow-hidden shadow-sm hover:shadow-md transition-all duration-300 flex flex-col group">
            <div class="relative overflow-hidden aspect-[4/3] bg-pink-50">
                <img src="assets/img/Consejos/maquillaje_calidad.jpg" alt="Maquillaje de calidad" 
                     class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
                <span class="absolute top-3 left-3 bg-white/90 backdrop-blur-md text-accent-fuchsia text-[10px] font-bold px-3 py-1 rounded-full uppercase shadow-xs">
                    Productos
                </span>
            </div>
            <div class="p-5 flex flex-col flex-1">
                <div class="flex items-center gap-2 text-[11px] text-gray-400 font-medium mb-2">
                    <i class="fa-regular fa-calendar text-xs"></i>
                    <span>10 May 2025</span>
                </div>
                <h3 class="font-bold text-gray-900 text-sm mb-2 group-hover:text-accent-fuchsia transition-colors line-clamp-2">
                    La importancia del maquillaje de calidad
                </h3>
                <p class="text-xs text-gray-500 line-clamp-3 mb-4 flex-1">
                    Por qué invertir en productos de calidad marca la diferencia en tu piel y en los resultados finales...
                </p>
                <button type="button" onclick="openConsejoModal('modalConsejo2')"
                        class="w-full bg-pink-50 text-accent-fuchsia font-bold py-2.5 rounded-2xl text-xs hover:bg-accent-fuchsia hover:text-white transition-all duration-300 flex items-center justify-center gap-2 group/btn">
                    <span>Leer más</span>
                    <i class="fa-solid fa-arrow-right text-xs group-hover/btn:translate-x-1 transition-transform"></i>
                </button>
            </div>
        </article>

        <!-- Card 3 -->
        <article class="bg-white border border-pink-100 rounded-3xl overflow-hidden shadow-sm hover:shadow-md transition-all duration-300 flex flex-col group">
            <div class="relative overflow-hidden aspect-[4/3] bg-pink-50">
                <img src="assets/img/Consejos/asesoria_maquillaje.jpg" alt="Asesoría maquillaje" 
                     class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
                <span class="absolute top-3 left-3 bg-white/90 backdrop-blur-md text-accent-fuchsia text-[10px] font-bold px-3 py-1 rounded-full uppercase shadow-xs">
                    Asesoría
                </span>
            </div>
            <div class="p-5 flex flex-col flex-1">
                <div class="flex items-center gap-2 text-[11px] text-gray-400 font-medium mb-2">
                    <i class="fa-regular fa-calendar text-xs"></i>
                    <span>5 May 2025</span>
                </div>
                <h3 class="font-bold text-gray-900 text-sm mb-2 group-hover:text-accent-fuchsia transition-colors line-clamp-2">
                    Asesoría personalizada en maquillaje
                </h3>
                <p class="text-xs text-gray-500 line-clamp-3 mb-4 flex-1">
                    Aprende a elegir los productos y técnicas que mejor se adaptan a tu rostro, estilo y necesidades...
                </p>
                <button type="button" onclick="openConsejoModal('modalConsejo3')" 
                        class="w-full bg-pink-50 text-accent-fuchsia font-bold py-2.5 rounded-2xl text-xs hover:bg-accent-fuchsia hover:text-white transition-all duration-300 flex items-center justify-center gap-2 group/btn">
                    <span>Leer más</span>
                    <i class="fa-solid fa-arrow-right text-xs group-hover/btn:translate-x-1 transition-transform"></i>
                </button>
            </div>
        </article>

        <!-- Card 4 -->
        <article class="bg-white border border-pink-100 rounded-3xl overflow-hidden shadow-sm hover:shadow-md transition-all duration-300 flex flex-col group">
            <div class="relative overflow-hidden aspect-[4/3] bg-pink-50">
                <img src="assets/img/Consejos/gama_maquillaje.jpg" alt="Gamas de maquillaje" 
                     class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
                <span class="absolute top-3 left-3 bg-white/90 backdrop-blur-md text-accent-fuchsia text-[10px] font-bold px-3 py-1 rounded-full uppercase shadow-xs">
                    Gamas
                </span>
            </div>
            <div class="p-5 flex flex-col flex-1">
                <div class="flex items-center gap-2 text-[11px] text-gray-400 font-medium mb-2">
                    <i class="fa-regular fa-calendar text-xs"></i>
                    <span>28 Abr 2025</span>
                </div>
                <h3 class="font-bold text-gray-900 text-sm mb-2 group-hover:text-accent-fuchsia transition-colors line-clamp-2">
                    Tipos de gama en productos de maquillaje
                </h3>
                <p class="text-xs text-gray-500 line-clamp-3 mb-4 flex-1">
                    Guía completa sobre las diferentes gamas de productos y cómo elegir según tus necesidades y presupuesto...
                </p>
                <button type="button" onclick="openConsejoModal('modalConsejo4')" 
                        class="w-full bg-pink-50 text-accent-fuchsia font-bold py-2.5 rounded-2xl text-xs hover:bg-accent-fuchsia hover:text-white transition-all duration-300 flex items-center justify-center gap-2 group/btn">
                    <span>Leer más</span>
                    <i class="fa-solid fa-arrow-right text-xs group-hover/btn:translate-x-1 transition-transform"></i>
                </button>
            </div>
        </article>

    </div>
</section>


<!-- MODAL DETALLE: CONSEJO 1 -->
<div id="modalConsejo1" class="fixed inset-0 z-50 flex items-center justify-center bg-gray-900/60 backdrop-blur-xs hidden p-4 overflow-y-auto">
    <div class="bg-white rounded-3xl max-w-3xl w-full p-6 shadow-2xl border border-pink-100 my-8 relative max-h-[90vh] flex flex-col">
        
        <!-- Botón Cerrar (X) -->
        <button type="button" onclick="closeConsejoModal('modalConsejo1')" class="absolute top-4 right-4 w-8 h-8 rounded-full bg-pink-50 text-gray-400 hover:text-gray-800 flex items-center justify-center text-sm transition-colors z-10">
            <i class="fa-solid fa-xmark"></i>
        </button>

        <!-- Cabecera -->
        <div class="border-b border-pink-100 pb-4 mb-4 pr-8">
            <h3 class="font-bold text-gray-900 text-lg">Maquillaje como impulsor de la autoestima</h3>
        </div>

        <!-- Cuerpo con Scroll Interno -->
        <div class="overflow-y-auto flex-1 pr-1 space-y-6">
            <div class="grid grid-cols-1 md:grid-cols-12 gap-6 items-start">
                
                <!-- Columna Izquierda: Imagen e Info -->
                <div class="md:col-span-5 space-y-3">
                    <div class="rounded-2xl overflow-hidden bg-pink-50 border border-pink-100 aspect-square">
                        <img src="assets/img/Consejos/maquillaje_autoestima.jpg" alt="Maquillaje y autoestima" class="w-full h-full object-cover">
                    </div>
                    <div class="flex items-center gap-2 flex-wrap">
                        <span class="bg-pink-100 text-accent-fuchsia text-[10px] font-bold px-3 py-1 rounded-full uppercase">Bienestar</span>
                        <span class="bg-gray-100 text-gray-500 text-[10px] font-medium px-3 py-1 rounded-full">15 May 2025</span>
                    </div>
                </div>

                <!-- Columna Derecha: Contenido y Desplegables -->
                <div class="md:col-span-7 space-y-4">
                    <div>
                        <h4 class="font-bold text-gray-900 text-sm mb-1">El poder transformador del maquillaje en tu confianza</h4>
                        <p class="text-xs text-gray-600 leading-relaxed">
                            El maquillaje va mucho más allá de la estética; es una poderosa herramienta de autoexpresión que puede impactar positivamente en nuestra percepción personal y bienestar emocional.
                        </p>
                    </div>

                    <!-- Acordeones / Acordeón Nativo -->
                    <div class="space-y-2.5 pt-2">
                        
                        <!-- Ítem 1 -->
                        <details class="group bg-pink-50/50 border border-pink-100 rounded-2xl p-3.5 [&_summary::-webkit-details-marker]:hidden" open>
                            <summary class="flex items-center justify-between cursor-pointer font-bold text-xs text-gray-800">
                                <span>Autoexpresión y creatividad</span>
                                <span class="text-accent-fuchsia transition-transform group-open:rotate-180">
                                    <i class="fa-solid fa-chevron-down text-[10px]"></i>
                                </span>
                            </summary>
                            <p class="mt-2 text-xs text-gray-600 leading-relaxed pt-2 border-t border-pink-100/60">
                                El maquillaje permite expresar nuestra personalidad, estado de ánimo y estilo único. Esta libertad creativa nos conecta con nuestro yo auténtico y fomenta la aceptación personal.
                            </p>
                        </details>

                        <!-- Ítem 2 -->
                        <details class="group bg-pink-50/50 border border-pink-100 rounded-2xl p-3.5 [&_summary::-webkit-details-marker]:hidden">
                            <summary class="flex items-center justify-between cursor-pointer font-bold text-xs text-gray-800">
                                <span>El ritual de autocuidado</span>
                                <span class="text-accent-fuchsia transition-transform group-open:rotate-180">
                                    <i class="fa-solid fa-chevron-down text-[10px]"></i>
                                </span>
                            </summary>
                            <p class="mt-2 text-xs text-gray-600 leading-relaxed pt-2 border-t border-pink-100/60">
                                Dedicar tiempo a maquillarnos es un acto de amor propio. Este ritual diario nos permite conectar con nosotros mismos, practicar mindfulness y comenzar el día con una actitud positiva.
                            </p>
                        </details>

                        <!-- Ítem 3 -->
                        <details class="group bg-pink-50/50 border border-pink-100 rounded-2xl p-3.5 [&_summary::-webkit-details-marker]:hidden">
                            <summary class="flex items-center justify-between cursor-pointer font-bold text-xs text-gray-800">
                                <span>Refuerzo positivo</span>
                                <span class="text-accent-fuchsia transition-transform group-open:rotate-180">
                                    <i class="fa-solid fa-chevron-down text-[10px]"></i>
                                </span>
                            </summary>
                            <p class="mt-2 text-xs text-gray-600 leading-relaxed pt-2 border-t border-pink-100/60">
                                Ver nuestra mejor versión en el espejo genera un circuito de retroalimentación positiva. Los cumplidos recibidos y la sensación de vernos bien potencian nuestra confianza en entornos sociales y profesionales.
                            </p>
                        </details>

                        <!-- Ítem 4 -->
                        <details class="group bg-pink-50/50 border border-pink-100 rounded-2xl p-3.5 [&_summary::-webkit-details-marker]:hidden">
                            <summary class="flex items-center justify-between cursor-pointer font-bold text-xs text-gray-800">
                                <span>Empoderamiento personal</span>
                                <span class="text-accent-fuchsia transition-transform group-open:rotate-180">
                                    <i class="fa-solid fa-chevron-down text-[10px]"></i>
                                </span>
                            </summary>
                            <p class="mt-2 text-xs text-gray-600 leading-relaxed pt-2 border-t border-pink-100/60">
                                El maquillaje nos permite tomar el control de nuestra imagen. Esta capacidad de transformación nos empodera y nos recuerda que tenemos libertad para definirnos a nosotros mismos.
                            </p>
                        </details>

                        <!-- Ítem 5 -->
                        <details class="group bg-pink-50/50 border border-pink-100 rounded-2xl p-3.5 [&_summary::-webkit-details-marker]:hidden">
                            <summary class="flex items-center justify-between cursor-pointer font-bold text-xs text-gray-800">
                                <span>Maquillaje consciente</span>
                                <span class="text-accent-fuchsia transition-transform group-open:rotate-180">
                                    <i class="fa-solid fa-chevron-down text-[10px]"></i>
                                </span>
                            </summary>
                            <p class="mt-2 text-xs text-gray-600 leading-relaxed pt-2 border-t border-pink-100/60">
                                Lo importante es mantener una relación saludable con el maquillaje, usándolo como potenciador, no como una máscara. El verdadero poder está en sentirnos bien con y sin él, apreciando su capacidad para realzar nuestra belleza natural.
                            </p>
                        </details>

                    </div>
                </div>

            </div>
        </div>

        <!-- Pie del Modal -->
        <div class="border-t border-pink-100 pt-4 mt-4 flex gap-3 justify-end">
            <button type="button" onclick="closeConsejoModal('modalConsejo1')" class="px-5 bg-gray-100 text-gray-600 font-bold py-2.5 rounded-2xl text-xs hover:bg-gray-200 transition-all">
                Cerrar
            </button>
            <a href="?pagina=catalogo_producto" class="px-5 bg-accent-fuchsia text-white font-bold py-2.5 rounded-2xl text-xs shadow-md hover:bg-pink-700 transition-all flex items-center gap-2">
                <span>Ver productos</span>
                <i class="fa-solid fa-arrow-right text-[10px]"></i>
            </a>
        </div>

    </div>
</div>


<!-- MODAL DETALLE: CONSEJO 2 -->
<div id="modalConsejo2" class="fixed inset-0 z-50 flex items-center justify-center bg-gray-900/60 backdrop-blur-xs hidden p-4 overflow-y-auto">
    <div class="bg-white rounded-3xl max-w-3xl w-full p-6 shadow-2xl border border-pink-100 my-8 relative max-h-[90vh] flex flex-col">
        
        <!-- Botón Cerrar (X) -->
        <button type="button" onclick="closeConsejoModal('modalConsejo2')" class="absolute top-4 right-4 w-8 h-8 rounded-full bg-pink-50 text-gray-400 hover:text-gray-800 flex items-center justify-center text-sm transition-colors z-10">
            <i class="fa-solid fa-xmark"></i>
        </button>

        <!-- Cabecera -->
        <div class="border-b border-pink-100 pb-4 mb-4 pr-8">
            <h3 class="font-bold text-gray-900 text-lg">La importancia del maquillaje de calidad</h3>
        </div>

        <!-- Cuerpo con Scroll Interno -->
        <div class="overflow-y-auto flex-1 pr-1 space-y-6">
            <div class="grid grid-cols-1 md:grid-cols-12 gap-6 items-start">
                
                <!-- Columna Izquierda: Imagen e Info -->
                <div class="md:col-span-5 space-y-3">
                    <div class="rounded-2xl overflow-hidden bg-pink-50 border border-pink-100 aspect-square">
                        <img src="assets/img/Consejos/maquillaje_calidad.jpg" alt="Maquillaje de calidad" class="w-full h-full object-cover">
                    </div>
                    <div class="flex items-center gap-2 flex-wrap">
                        <span class="bg-pink-100 text-accent-fuchsia text-[10px] font-bold px-3 py-1 rounded-full uppercase">Productos</span>
                        <span class="bg-gray-100 text-gray-500 text-[10px] font-medium px-3 py-1 rounded-full">10 May 2025</span>
                    </div>
                </div>

                <!-- Columna Derecha: Contenido y Desplegables -->
                <div class="md:col-span-7 space-y-4">
                    <div>
                        <h4 class="font-bold text-gray-900 text-sm mb-1">Por qué invertir en productos de calidad marca la diferencia</h4>
                        <p class="text-xs text-gray-600 leading-relaxed">
                            Elegir productos de maquillaje de calidad no es un lujo sino una inversión en tu piel y en resultados profesionales. Descubre las razones por las que vale la pena invertir en buenos cosméticos:
                        </p>
                    </div>

                    <!-- Acordeones Nativo -->
                    <div class="space-y-2.5 pt-2">
                        
                        <!-- Ítem 1 (Abierto por defecto) -->
                        <details class="group bg-pink-50/50 border border-pink-100 rounded-2xl p-3.5 [&_summary::-webkit-details-marker]:hidden" open>
                            <summary class="flex items-center justify-between cursor-pointer font-bold text-xs text-gray-800">
                                <span>Protección para tu piel</span>
                                <span class="text-accent-fuchsia transition-transform group-open:rotate-180">
                                    <i class="fa-solid fa-chevron-down text-[10px]"></i>
                                </span>
                            </summary>
                            <p class="mt-2 text-xs text-gray-600 leading-relaxed pt-2 border-t border-pink-100/60">
                                Los productos de calidad contienen ingredientes dermatológicamente testados, libres de sustancias nocivas y con propiedades beneficiosas para la piel. Muchos incluyen protección solar, antioxidantes y activos hidratantes que cuidan tu piel mientras la embellecen.
                            </p>
                        </details>

                        <!-- Ítem 2 -->
                        <details class="group bg-pink-50/50 border border-pink-100 rounded-2xl p-3.5 [&_summary::-webkit-details-marker]:hidden">
                            <summary class="flex items-center justify-between cursor-pointer font-bold text-xs text-gray-800">
                                <span>Mayor durabilidad y rendimiento</span>
                                <span class="text-accent-fuchsia transition-transform group-open:rotate-180">
                                    <i class="fa-solid fa-chevron-down text-[10px]"></i>
                                </span>
                            </summary>
                            <p class="mt-2 text-xs text-gray-600 leading-relaxed pt-2 border-t border-pink-100/60">
                                Un buen maquillaje permanece intacto durante horas sin necesidad de retoques constantes. La pigmentación superior requiere menos cantidad de producto, haciendo que tu inversión rinda más a largo plazo.
                            </p>
                        </details>

                        <!-- Ítem 3 -->
                        <details class="group bg-pink-50/50 border border-pink-100 rounded-2xl p-3.5 [&_summary::-webkit-details-marker]:hidden">
                            <summary class="flex items-center justify-between cursor-pointer font-bold text-xs text-gray-800">
                                <span>Acabado profesional</span>
                                <span class="text-accent-fuchsia transition-transform group-open:rotate-180">
                                    <i class="fa-solid fa-chevron-down text-[10px]"></i>
                                </span>
                            </summary>
                            <p class="mt-2 text-xs text-gray-600 leading-relaxed pt-2 border-t border-pink-100/60">
                                La diferencia es visible: texturas refinadas que se funden con la piel, colores vibrantes y fieles, y acabados naturales que realzan sin apelmazar. El resultado final siempre se ve más natural y pulido.
                            </p>
                        </details>

                        <!-- Ítem 4 -->
                        <details class="group bg-pink-50/50 border border-pink-100 rounded-2xl p-3.5 [&_summary::-webkit-details-marker]:hidden">
                            <summary class="flex items-center justify-between cursor-pointer font-bold text-xs text-gray-800">
                                <span>Seguridad en cada aplicación</span>
                                <span class="text-accent-fuchsia transition-transform group-open:rotate-180">
                                    <i class="fa-solid fa-chevron-down text-[10px]"></i>
                                </span>
                            </summary>
                            <p class="mt-2 text-xs text-gray-600 leading-relaxed pt-2 border-t border-pink-100/60">
                                Las marcas reconocidas invierten en investigación y pruebas rigurosas. Esto minimiza el riesgo de reacciones alérgicas, irritaciones o problemas como acné cosmético que pueden surgir con productos de baja calidad.
                            </p>
                        </details>

                        <!-- Ítem 5 -->
                        <details class="group bg-pink-50/50 border border-pink-100 rounded-2xl p-3.5 [&_summary::-webkit-details-marker]:hidden">
                            <summary class="flex items-center justify-between cursor-pointer font-bold text-xs text-gray-800">
                                <span>Inversión inteligente</span>
                                <span class="text-accent-fuchsia transition-transform group-open:rotate-180">
                                    <i class="fa-solid fa-chevron-down text-[10px]"></i>
                                </span>
                            </summary>
                            <p class="mt-2 text-xs text-gray-600 leading-relaxed pt-2 border-t border-pink-100/60">
                                No es necesario que todo tu kit sea de alta gama. Prioriza la inversión en productos de larga duración que están en contacto directo con tu piel, como bases, correctores y primers, mientras puedes ser más flexible con productos como sombras o labiales.
                            </p>
                        </details>

                    </div>
                </div>

            </div>
        </div>

        <!-- Pie del Modal -->
        <div class="border-t border-pink-100 pt-4 mt-4 flex gap-3 justify-end">
            <button type="button" onclick="closeConsejoModal('modalConsejo2')" class="px-5 bg-gray-100 text-gray-600 font-bold py-2.5 rounded-2xl text-xs hover:bg-gray-200 transition-all">
                Cerrar
            </button>
            <a href="?pagina=catalogo_producto" class="px-5 bg-accent-fuchsia text-white font-bold py-2.5 rounded-2xl text-xs shadow-md hover:bg-pink-700 transition-all flex items-center gap-2">
                <span>Ver productos</span>
                <i class="fa-solid fa-arrow-right text-[10px]"></i>
            </a>
        </div>

    </div>
</div>


<!-- MODAL DETALLE: CONSEJO 3 -->
<div id="modalConsejo3" class="fixed inset-0 z-50 flex items-center justify-center bg-gray-900/60 backdrop-blur-xs hidden p-4 overflow-y-auto">
    <div class="bg-white rounded-3xl max-w-3xl w-full p-6 shadow-2xl border border-pink-100 my-8 relative max-h-[90vh] flex flex-col">
        
        <!-- Botón Cerrar (X) -->
        <button type="button" onclick="closeConsejoModal('modalConsejo3')" class="absolute top-4 right-4 w-8 h-8 rounded-full bg-pink-50 text-gray-400 hover:text-gray-800 flex items-center justify-center text-sm transition-colors z-10">
            <i class="fa-solid fa-xmark"></i>
        </button>

        <!-- Cabecera -->
        <div class="border-b border-pink-100 pb-4 mb-4 pr-8">
            <h3 class="font-bold text-gray-900 text-lg">Asesoría personalizada en maquillaje</h3>
        </div>

        <!-- Cuerpo con Scroll Interno -->
        <div class="overflow-y-auto flex-1 pr-1 space-y-6">
            <div class="grid grid-cols-1 md:grid-cols-12 gap-6 items-start">
                
                <!-- Columna Izquierda: Imagen e Info -->
                <div class="md:col-span-5 space-y-3">
                    <div class="rounded-2xl overflow-hidden bg-pink-50 border border-pink-100 aspect-square">
                        <img src="assets/img/Consejos/asesoria_maquillaje.jpg" alt="Asesoría maquillaje" class="w-full h-full object-cover">
                    </div>
                    <div class="flex items-center gap-2 flex-wrap">
                        <span class="bg-pink-100 text-accent-fuchsia text-[10px] font-bold px-3 py-1 rounded-full uppercase">Asesoría</span>
                        <span class="bg-gray-100 text-gray-500 text-[10px] font-medium px-3 py-1 rounded-full">5 May 2025</span>
                    </div>
                </div>

                <!-- Columna Derecha: Contenido y Desplegables -->
                <div class="md:col-span-7 space-y-4">
                    <div>
                        <h4 class="font-bold text-gray-900 text-sm mb-1">Cómo elegir lo mejor para ti según tu rostro y estilo</h4>
                        <p class="text-xs text-gray-600 leading-relaxed">
                            Navegar entre miles de productos y técnicas puede resultar abrumador. La asesoría personalizada es clave para encontrar lo que realmente funciona para ti:
                        </p>
                    </div>

                    <!-- Acordeones Nativo -->
                    <div class="space-y-2.5 pt-2">
                        
                        <!-- Ítem 1 (Abierto por defecto) -->
                        <details class="group bg-pink-50/50 border border-pink-100 rounded-2xl p-3.5 [&_summary::-webkit-details-marker]:hidden" open>
                            <summary class="flex items-center justify-between cursor-pointer font-bold text-xs text-gray-800">
                                <span>Conoce tu tipo de piel</span>
                                <span class="text-accent-fuchsia transition-transform group-open:rotate-180">
                                    <i class="fa-solid fa-chevron-down text-[10px]"></i>
                                </span>
                            </summary>
                            <p class="mt-2 text-xs text-gray-600 leading-relaxed pt-2 border-t border-pink-100/60">
                                El primer paso para una asesoría efectiva es identificar si tu piel es seca, grasa, mixta o sensible. Esto determinará el tipo de base, primer y productos de cuidado que mejor funcionarán para ti, evitando brillos indeseados o parches secos.
                            </p>
                        </details>

                        <!-- Ítem 2 -->
                        <details class="group bg-pink-50/50 border border-pink-100 rounded-2xl p-3.5 [&_summary::-webkit-details-marker]:hidden">
                            <summary class="flex items-center justify-between cursor-pointer font-bold text-xs text-gray-800">
                                <span>Identifica tu subtono</span>
                                <span class="text-accent-fuchsia transition-transform group-open:rotate-180">
                                    <i class="fa-solid fa-chevron-down text-[10px]"></i>
                                </span>
                            </summary>
                            <p class="mt-2 text-xs text-gray-600 leading-relaxed pt-2 border-t border-pink-100/60">
                                Determinar si tu subtono es cálido, frío o neutro es fundamental para elegir bases y correctores que se fundan perfectamente con tu piel, así como colores de maquillaje que realcen tu belleza natural.
                            </p>
                        </details>

                        <!-- Ítem 3 -->
                        <details class="group bg-pink-50/50 border border-pink-100 rounded-2xl p-3.5 [&_summary::-webkit-details-marker]:hidden">
                            <summary class="flex items-center justify-between cursor-pointer font-bold text-xs text-gray-800">
                                <span>Morfología facial</span>
                                <span class="text-accent-fuchsia transition-transform group-open:rotate-180">
                                    <i class="fa-solid fa-chevron-down text-[10px]"></i>
                                </span>
                            </summary>
                            <p class="mt-2 text-xs text-gray-600 leading-relaxed pt-2 border-t border-pink-100/60">
                                Cada rostro tiene proporciones únicas. Una buena asesoría te ayudará a identificar técnicas de contorno, iluminación y aplicación de rubor que potencien tus rasgos más favorecedores y armonicen tu rostro.
                            </p>
                        </details>

                        <!-- Ítem 4 -->
                        <details class="group bg-pink-50/50 border border-pink-100 rounded-2xl p-3.5 [&_summary::-webkit-details-marker]:hidden">
                            <summary class="flex items-center justify-between cursor-pointer font-bold text-xs text-gray-800">
                                <span>Maquillaje según ocasión</span>
                                <span class="text-accent-fuchsia transition-transform group-open:rotate-180">
                                    <i class="fa-solid fa-chevron-down text-[10px]"></i>
                                </span>
                            </summary>
                            <p class="mt-2 text-xs text-gray-600 leading-relaxed pt-2 border-t border-pink-100/60">
                                No es lo mismo un maquillaje para la oficina que para una boda o una sesión de fotos. Aprende a adaptar tu maquillaje según la iluminación, duración del evento y tipo de ocasión.
                            </p>
                        </details>

                        <!-- Ítem 5 -->
                        <details class="group bg-pink-50/50 border border-pink-100 rounded-2xl p-3.5 [&_summary::-webkit-details-marker]:hidden">
                            <summary class="flex items-center justify-between cursor-pointer font-bold text-xs text-gray-800">
                                <span>Cuidado y mantenimiento</span>
                                <span class="text-accent-fuchsia transition-transform group-open:rotate-180">
                                    <i class="fa-solid fa-chevron-down text-[10px]"></i>
                                </span>
                            </summary>
                            <p class="mt-2 text-xs text-gray-600 leading-relaxed pt-2 border-t border-pink-100/60">
                                Una asesoría completa incluye consejos sobre limpieza de brochas, orden de aplicación de productos y rutinas de desmaquillado que preserven la salud de tu piel y la duración de tus productos.
                            </p>
                        </details>

                    </div>
                </div>

            </div>
        </div>

        <!-- Pie del Modal -->
        <div class="border-t border-pink-100 pt-4 mt-4 flex gap-3 justify-end">
            <button type="button" onclick="closeConsejoModal('modalConsejo3')" class="px-5 bg-gray-100 text-gray-600 font-bold py-2.5 rounded-2xl text-xs hover:bg-gray-200 transition-all">
                Cerrar
            </button>
            <a href="?pagina=catalogo_producto" class="px-5 bg-accent-fuchsia text-white font-bold py-2.5 rounded-2xl text-xs shadow-md hover:bg-pink-700 transition-all flex items-center gap-2">
                <span>Ver productos</span>
                <i class="fa-solid fa-arrow-right text-[10px]"></i>
            </a>
        </div>

    </div>
</div>

<!-- MODAL  CONSEJO 4 -->
<div id="modalConsejo4" class="fixed inset-0 z-50 flex items-center justify-center bg-gray-900/60 backdrop-blur-xs hidden p-4 overflow-y-auto">
    <div class="bg-white rounded-3xl max-w-3xl w-full p-6 shadow-2xl border border-pink-100 my-8 relative max-h-[90vh] flex flex-col">
        
        <!-- Botón Cerrar  -->
        <button type="button" onclick="closeConsejoModal('modalConsejo4')" class="absolute top-4 right-4 w-8 h-8 rounded-full bg-pink-50 text-gray-400 hover:text-gray-800 flex items-center justify-center text-sm transition-colors z-10">
            <i class="fa-solid fa-xmark"></i>
        </button>

        <!-- Cabecera -->
        <div class="border-b border-pink-100 pb-4 mb-4 pr-8">
            <h3 class="font-bold text-gray-900 text-lg">Tipos de gama en productos de maquillaje</h3>
        </div>

        <!-- Cuerpo con Scroll Interno -->
        <div class="overflow-y-auto flex-1 pr-1 space-y-6">
            <div class="grid grid-cols-1 md:grid-cols-12 gap-6 items-start">
                
                <!-- Imagen e Info -->
                <div class="md:col-span-5 space-y-3">
                    <div class="rounded-2xl overflow-hidden bg-pink-50 border border-pink-100 aspect-square">
                        <img src="assets/img/Consejos/gama_maquillaje.jpg" alt="Gama de maquillaje" class="w-full h-full object-cover">
                    </div>
                    <div class="flex items-center gap-2 flex-wrap">
                        <span class="bg-pink-100 text-accent-fuchsia text-[10px] font-bold px-3 py-1 rounded-full uppercase">Gamas</span>
                        <span class="bg-gray-100 text-gray-500 text-[10px] font-medium px-3 py-1 rounded-full">28 Abr 2025</span>
                    </div>
                </div>

                <!-- Contenido y Desplegables -->
                <div class="md:col-span-7 space-y-4">
                    <div>
                        <h4 class="font-bold text-gray-900 text-sm mb-1">Entendiendo las diferentes categorías de productos cosméticos</h4>
                        <p class="text-xs text-gray-600 leading-relaxed">
                            El mercado del maquillaje ofrece opciones para todos los presupuestos y necesidades. Conocer las características de cada gama te ayudará a tomar decisiones informadas:
                        </p>
                    </div>

                    <!-- Acordeones Nativos -->
                    <div class="space-y-2.5 pt-2">
                        
                        <!-- Ítem 1 (Abierto por defecto) -->
                        <details class="group bg-pink-50/50 border border-pink-100 rounded-2xl p-3.5 [&_summary::-webkit-details-marker]:hidden" open>
                            <summary class="flex items-center justify-between cursor-pointer font-bold text-xs text-gray-800">
                                <span>Gama Alta</span>
                                <span class="text-accent-fuchsia transition-transform group-open:rotate-180">
                                    <i class="fa-solid fa-chevron-down text-[10px]"></i>
                                </span>
                            </summary>
                            <p class="mt-2 text-xs text-gray-600 leading-relaxed pt-2 border-t border-pink-100/60">
                                Caracterizada por ingredientes exclusivos, fórmulas patentadas y envases de diseño. Estas marcas invierten fuertemente en investigación e innovación, ofreciendo productos con texturas sofisticadas y alta pigmentación.
                            </p>
                        </details>

                        <!-- Ítem 2 -->
                        <details class="group bg-pink-50/50 border border-pink-100 rounded-2xl p-3.5 [&_summary::-webkit-details-marker]:hidden">
                            <summary class="flex items-center justify-between cursor-pointer font-bold text-xs text-gray-800">
                                <span>Gama Media</span>
                                <span class="text-accent-fuchsia transition-transform group-open:rotate-180">
                                    <i class="fa-solid fa-chevron-down text-[10px]"></i>
                                </span>
                            </summary>
                            <p class="mt-2 text-xs text-gray-600 leading-relaxed pt-2 border-t border-pink-100/60">
                                El equilibrio perfecto entre calidad y precio accesible. Estas marcas ofrecen productos con buena formulación y rendimiento, manteniendo estándares profesionales sin el precio elevado de las marcas de lujo.
                            </p>
                        </details>

                        <!-- Ítem 3 -->
                        <details class="group bg-pink-50/50 border border-pink-100 rounded-2xl p-3.5 [&_summary::-webkit-details-marker]:hidden">
                            <summary class="flex items-center justify-between cursor-pointer font-bold text-xs text-gray-800">
                                <span>Gama Farmacéutica</span>
                                <span class="text-accent-fuchsia transition-transform group-open:rotate-180">
                                    <i class="fa-solid fa-chevron-down text-[10px]"></i>
                                </span>
                            </summary>
                            <p class="mt-2 text-xs text-gray-600 leading-relaxed pt-2 border-t border-pink-100/60">
                                Desarrollados con enfoque dermatológico, estos productos combinan maquillaje con beneficios para la piel. Ideales para pieles sensibles o con condiciones específicas.
                            </p>
                        </details>

                        <!-- Ítem 4 -->
                        <details class="group bg-pink-50/50 border border-pink-100 rounded-2xl p-3.5 [&_summary::-webkit-details-marker]:hidden">
                            <summary class="flex items-center justify-between cursor-pointer font-bold text-xs text-gray-800">
                                <span>Gama Económica</span>
                                <span class="text-accent-fuchsia transition-transform group-open:rotate-180">
                                    <i class="fa-solid fa-chevron-down text-[10px]"></i>
                                </span>
                            </summary>
                            <p class="mt-2 text-xs text-gray-600 leading-relaxed pt-2 border-t border-pink-100/60">
                                Accesibles y versátiles, estas marcas han mejorado significativamente sus fórmulas en los últimos años, ofreciendo alternativas de calidad a precios competitivos.
                            </p>
                        </details>

                    </div>
                </div>

            </div>
        </div>

        <!-- Pie del Modal -->
        <div class="border-t border-pink-100 pt-4 mt-4 flex gap-3 justify-end">
            <button type="button" onclick="closeConsejoModal('modalConsejo4')" class="px-5 bg-gray-100 text-gray-600 font-bold py-2.5 rounded-2xl text-xs hover:bg-gray-200 transition-all">
                Cerrar
            </button>
            <a href="?pagina=catalogo_producto" class="px-5 bg-accent-fuchsia text-white font-bold py-2.5 rounded-2xl text-xs shadow-md hover:bg-pink-700 transition-all flex items-center gap-2">
                <span>Ver productos</span>
                <i class="fa-solid fa-arrow-right text-[10px]"></i>
            </a>
        </div>

    </div>
</div>

    </main>
    
<script>
/*Abre el modal de consejo especificado*/
function openConsejoModal(modalId) {
    const modal = document.getElementById(modalId);
    if (modal) {
        modal.classList.remove('hidden');
    }
}

/* Cierra el modal de consejo*/
function closeConsejoModal(modalId) {
    const modal = document.getElementById(modalId);
    if (modal) {
        modal.classList.add('hidden');
    }
}
</script>
  
  <?php include 'vista/complementos/footer_catalogo.php' ?>
  
</body>
</html>