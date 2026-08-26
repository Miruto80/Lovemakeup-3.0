/* -------------------------------- TIENDA ----------------------------------------*/
/// LOADER
window.addEventListener('load', function() {
    const loader = document.getElementById('app-loader');
        if (loader) {
            loader.classList.add('opacity-0', 'pointer-events-none');
                setTimeout(() => {
                loader.remove(); // Elimina el elemento del DOM tras el desvanecimiento
            }, 500);
        }
});

/// MODAL CONFIRAMCION
 function openLogoutModal() {
    const modal = document.getElementById('logoutModal');
    if (modal) {
        modal.classList.remove('hidden');
    }
}
/// Cierra el modal de confirmación
function closeLogoutModal() {
    const modal = document.getElementById('logoutModal');
    if (modal) {
        modal.classList.add('hidden');
    }
}


        let exchangeRate = 775.34;
        let cart = [];
        let favorites = [1, 3];
        let activeSlide = 0;
        let slideTimer = null;


        document.addEventListener('DOMContentLoaded', () => {
            startHeroCarousel();
        });

      

        // 4-Second Auto Banner Slider Carousel Logic
        function startHeroCarousel() {
            if (slideTimer) clearInterval(slideTimer);
            slideTimer = setInterval(() => {
                nextSlide();
            }, 4000);
        }

        function goToSlide(index) {
            const slides = document.querySelectorAll('.carousel-slide');
            const dots = document.querySelectorAll('#carouselIndicators button');

            slides.forEach((slide, idx) => {
                if (idx === index) {
                    slide.classList.remove('opacity-0', 'pointer-events-none');
                    slide.classList.add('opacity-100');
                } else {
                    slide.classList.remove('opacity-100');
                    slide.classList.add('opacity-0', 'pointer-events-none');
                }
            });

            dots.forEach((dot, idx) => {
                if (idx === index) {
                    dot.classList.remove('bg-white/40');
                    dot.classList.add('bg-white', 'w-6');
                } else {
                    dot.classList.remove('bg-white', 'w-6');
                    dot.classList.add('bg-white/40', 'w-3');
                }
            });

            activeSlide = index;
        }

        function nextSlide() {
            let next = (activeSlide + 1) % 4;
            goToSlide(next);
        }

        function prevSlide() {
            let prev = (activeSlide - 1 + 4) % 4;
            goToSlide(prev);
        }

        // Navigation Tabs Handling
        function switchTab(tabName) {
            const tabs = ['inicio', 'productos', 'favoritos', 'pedidos', 'datos', 'ubicacion', 'contacto'];
            tabs.forEach(t => {
                const el = document.getElementById(`view-${t}`);
                if (el) el.classList.add('hidden');

                const deskNav = document.getElementById(`desk-nav-${t}`);
                if (deskNav) deskNav.classList.remove('active', 'text-accent-fuchsia');

                const botNav = document.getElementById(`bot-nav-${t}`);
                if (botNav) botNav.classList.remove('text-accent-fuchsia');
            });

            const activeEl = document.getElementById(`view-${tabName}`);
            if (activeEl) activeEl.classList.remove('hidden');

            const activeDeskNav = document.getElementById(`desk-nav-${tabName}`);
            if (activeDeskNav) activeDeskNav.classList.add('active', 'text-accent-fuchsia');

            const activeBotNav = document.getElementById(`bot-nav-${tabName}`);
            if (activeBotNav) activeBotNav.classList.add('text-accent-fuchsia');

            window.scrollTo({ top: 0, behavior: 'smooth' });

            if (tabName === 'favoritos') renderFavorites();
        }

        function switchTabMobile(tabName) {
            toggleMobileDrawer(false);
            switchTab(tabName);
        }

        function toggleMobileDrawer(open) {
            const drawer = document.getElementById('mobileDrawer');
            const panel = document.getElementById('mobileDrawerPanel');
            if (open) {
                drawer.classList.remove('pointer-events-none', 'opacity-0');
                drawer.classList.add('opacity-100');
                panel.classList.remove('-translate-x-full');
            } else {
                panel.classList.add('-translate-x-full');
                drawer.classList.remove('opacity-100');
                drawer.classList.add('opacity-0', 'pointer-events-none');
            }
        }

        function syncMobileSearch() {
            const q = document.getElementById('searchInputMobile').value;
            document.getElementById('searchInput').value = q;
            filterProducts();
        }

        function toggleCartDrawer(open) {
            const drawer = document.getElementById('cartDrawer');
            const panel = document.getElementById('cartDrawerPanel');
            if (open) {
                drawer.classList.remove('pointer-events-none', 'opacity-0');
                drawer.classList.add('opacity-100');
                panel.classList.remove('translate-x-full');
            } else {
                panel.classList.add('translate-x-full');
                drawer.classList.remove('opacity-100');
                drawer.classList.add('opacity-0', 'pointer-events-none');
            }
        }

$(document).ready(function () {
    $(document).on("click", ".btn-login", function (e) {
        e.preventDefault();
        Swal.fire({
            title: '¡Inicia sesión!',
            text: 'Debes iniciar sesión para realizar esta acción.',
            icon: 'info',
            showCancelButton: true,
            confirmButtonColor: '#3c8b21', 
            cancelButtonColor: '#6b7280',  
            confirmButtonText: '<i class="fa-solid fa-right-to-bracket mr-1"></i> Iniciar sesión',
            cancelButtonText: 'Cancelar',
            customClass: {
                popup: 'rounded-2xl',       
                confirmButton: 'rounded-xl text-sm font-semibold px-4 py-4',
                cancelButton: 'rounded-xl text-sm font-semibold px-4 py-4'
            }
        }).then((result) => {
            if (result.isConfirmed) {
                window.location.href = 'Pagina=login'; 
            }
        });
    });
    const $btnVolverArriba = $("#btnVolverArriba");

    // Mostrar u ocultar el boton según la posición del scroll
    $(window).scroll(function () {
        if ($(this).scrollTop() > 300) {
            $btnVolverArriba.removeClass("hidden").fadeIn(200);
        } else {
            $btnVolverArriba.fadeOut(200, function() {
                $(this).addClass("hidden");
            });
        }
    });

    // Evento click para subir suavemente al inicio
    $btnVolverArriba.on("click", function (e) {
        e.preventDefault();
        $("html, body").animate({ scrollTop: 0 }, 500); 
    });
});
/* -------------------------------- FIN TIENDA ----------------------------------------*/


window.onload = function() {
    if (window.location.search.includes("busqueda=")) {
        window.history.replaceState({}, document.title, "index.php?pagina=catalogo_producto");
    }
};

document.addEventListener("DOMContentLoaded", () => {
    const checkboxes = document.querySelectorAll('.filtro-checkbox');
    const productos = document.querySelectorAll('.product-item[data-categoria]');

    checkboxes.forEach(cb => {
        cb.addEventListener('change', (e) => {
            const val = e.target.value;
            const isChecked = e.target.checked;

            // 1. Sincronizar el checkbox equivalente (móvil <-> desktop)
            checkboxes.forEach(otherCb => {
                if (otherCb.value === val) {
                    otherCb.checked = isChecked;
                    actualizarEstiloLabel(otherCb);
                }
            });

            // 2. Obtener categorías activas
            const categoriasSeleccionadas = Array.from(checkboxes)
                .filter(c => c.checked)
                .map(c => c.value);

            // 3. Filtrar los elementos dentro de la rejilla
            productos.forEach(prod => {
                // Si el mismo producto tiene la clase del grid o está envuelto por ella
                const target = prod.classList.contains('product-item') ? prod : prod.closest('.product-item');
                const categoria = prod.getAttribute('data-categoria');

                if (categoriasSeleccionadas.length === 0 || categoriasSeleccionadas.includes(categoria)) {
                    target.style.display = ''; // Muestra el producto
                } else {
                    target.style.display = 'none'; // Oculta y libera el espacio en el grid
                }
            });
        });
    });

    // Función auxiliar para manejar clases activas en Tailwind en lugar de style inline
    function actualizarEstiloLabel(checkbox) {
        const label = checkbox.closest('label');
        if (!label) return;

        if (checkbox.checked) {
            label.classList.add('bg-pink-100', 'border-accent-fuchsia', 'text-accent-fuchsia');
            label.classList.remove('bg-pink-50/30');
        } else {
            label.classList.remove('bg-pink-100', 'border-accent-fuchsia', 'text-accent-fuchsia');
            label.classList.add('bg-pink-50/30');
        }
    }
});


let currentModalQty = 1;

function openModal(element) {
    // CAPTURAR Y VERIFICAR EL EVENTO
    const e = window.event;
    if (e && e.target) {
        if (e.target.closest('.btn-favorito') || e.target.closest('a[href*="pagina=login"]') || e.target.closest('.btn-login')) {
            return; 
        }
    }

    const id = element.dataset.id;
    const nombre = element.dataset.nombre;
    const precio = element.dataset.precio;
    const marca = element.dataset.marca || 'LoveMakeup';
    const descripcion = element.dataset.descripcion || 'Sin descripción disponible.';
    const cantidadMayor = element.dataset.cantidadMayor || '0';
    const precioMayor = element.dataset.precioMayor || '0.00';
    const stockDisponible = element.dataset.stockDisponible || '0';
    
    // Convertir el JSON de imágenes
    let imagenes = [];
    try {
        imagenes = JSON.parse(element.dataset.imagenes || '[]');
    } catch (e) {
        imagenes = [];
    }

    // Insertar datos en el modal
    document.getElementById('modal-title').textContent = nombre;
    document.getElementById('modal-precio').textContent = "$" + parseFloat(precio).toFixed(2);
    document.getElementById('modal-marca').textContent = marca;
    document.getElementById('modal-descripcion').textContent = descripcion;
    document.getElementById('modal-cantidad-mayor').textContent = cantidadMayor;
    document.getElementById('modal-precio-mayor').textContent = "$" + parseFloat(precioMayor).toFixed(2);
    document.getElementById('modal-stock-disponible').textContent = stockDisponible;

    const marcaBadge = document.getElementById('modal-marca-badge');
    if (marcaBadge) marcaBadge.textContent = marca;

    // Rellenar el slider/galería de miniaturas
    const sliderInner = document.getElementById('modal-slider-inner');
    const mainImg = document.getElementById('modal-main-image');
    sliderInner.innerHTML = '';

    const placeholder = 'https://placehold.co/800x800/fdf2f8/d81b60?text=LoveMakeup';

    if (imagenes.length > 0) {
        if (mainImg) mainImg.src = imagenes[0].url_imagen || placeholder;

        imagenes.forEach((img, index) => {
            const btn = document.createElement('button');
            btn.type = 'button';
            btn.className = `w-14 h-14 rounded-xl border-2 ${index === 0 ? 'border-accent-fuchsia' : 'border-pink-100'} overflow-hidden p-1 bg-pink-50 flex-shrink-0 transition-all`;
            
            const image = document.createElement('img');
            image.src = img.url_imagen;
            image.className = 'w-full h-full object-contain pointer-events-none';
            image.onerror = function() { this.src = placeholder; };

            btn.appendChild(image);

            btn.onclick = function() {
                if (mainImg) mainImg.src = img.url_imagen;
                document.querySelectorAll('#modal-slider-inner button').forEach(b => {
                    b.classList.remove('border-accent-fuchsia');
                    b.classList.add('border-pink-100');
                });
                btn.classList.remove('border-pink-100');
                btn.classList.add('border-accent-fuchsia');
            };

            sliderInner.appendChild(btn);
        });
    } else {
        if (mainImg) mainImg.src = placeholder;
    }

    // Rellenar formulario oculto
    document.getElementById('form-id').value = id;
    document.getElementById('form-nombre').value = nombre;
    document.getElementById('form-precio-detal').value = precio;
    document.getElementById('form-precio-mayor').value = precioMayor;
    document.getElementById('form-cantidad-mayor').value = cantidadMayor;
    document.getElementById('form-imagen').value = imagenes.length ? imagenes[0].url_imagen : '';
    document.getElementById('form-stock-disponible').value = stockDisponible;

    // MOSTRAR EL MODAL
    const modal = document.getElementById('productDetailModal');
    if (modal) modal.classList.remove('hidden');
}

/* Cierra el modal de detalle */
function closeProductModal() {
    const modal = document.getElementById('productDetailModal');
    if (modal) modal.classList.add('hidden');
}

/**
 * Maneja el envío del formulario del modal
 */
function handleModalSubmit(event) {
    event.preventDefault();
    const productId = document.getElementById('form-id').value;
    
    if (typeof agregarAlCarritoDirecto === 'function') {
        agregarAlCarritoDirecto(productId, 1);
    }

    closeProductModal();
}


function muestraMensaje(icono, tiempo, titulo, mensaje) {
    Swal.fire({
        icon: icono,
        timer: tiempo,
        title: titulo,
        html: mensaje,
        showConfirmButton: false,
    });
  }

document.addEventListener("DOMContentLoaded", () => {
    const formCarrito = document.getElementById("form-carrito");
    const btnAgregarCarrito = document.getElementById("btn-agregar-carrito");

    if (btnAgregarCarrito && formCarrito) {
        btnAgregarCarrito.addEventListener("click", (e) => {
            e.preventDefault();

            const formData = new FormData(formCarrito);

            const stockDisponible = parseInt(formData.get("stockDisponible"));
            const idProducto = formData.get("id");
            console.log(stockDisponible)
            console.log(idProducto)

            const itemExistente = document.querySelector(`li[data-id="${idProducto}"]`);
            let cantidadActual = 0;
            if (itemExistente) {
             const textoCantidad = itemExistente.querySelector('.cantidad-texto')?.textContent;
             const match = textoCantidad.match(/^(\d+)/);
              if (match) {
               cantidadActual = parseInt(match[1]);
             }
            }

      if(stockDisponible === 0){
            muestraMensaje('error', 1000, 'Sin stock', 'Este producto no está disponible actualmente.');
        return;
            }
      if (cantidadActual >= stockDisponible) {
             muestraMensaje('error', 1000, 'Stock limitado', 'Ya has agregado el máximo permitido.');
                  return;
      } 

      

            fetch("controlador/carrito.php", {
                method: "POST",
                body: formData,
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    muestraMensaje('success', 1000, '¡Agregado!', 'El producto se agregó al carrito.');
                    const ulCarrito = document.querySelector('.carrito-dropdown');
                    if (!ulCarrito) {
                        console.error("No se encontró el <ul> del carrito en el HTML.");
                        return;
                    }
                      

                    const liVacio = ulCarrito.querySelector('li.text-center');
                    if (liVacio) {
                        liVacio.remove();
                    }

                    const id = data.producto.id;
                    let itemExistente = ulCarrito.querySelector(`li[data-id="${id}"]`);

                    if (!itemExistente) {
                       
                        const item = document.createElement('li');
                        item.className = 'list-group-item d-flex justify-content-between lh-sm';
                        item.setAttribute('data-id', id);

                        item.innerHTML = `
                            <div>
                                <h6 class="fs-5 fw-normal my-0">${data.producto.nombre}</h6>
                                <small class="text-muted cantidad-texto">${data.producto.cantidad} x $${data.producto.precio_unitario}</small>
                            </div>
                            <div class="text-end">
                                <span class="text-body-secondary subtotal-texto">$${data.producto.subtotal}</span><br>
                                <button class="btn-eliminar btn btn-sm btn-outline-danger mt-1" data-id="${id}">
                                    <i class="fa-solid fa-x"></i>
                                </button>
                            </div>
                        `;

                        ulCarrito.insertBefore(item, ulCarrito.lastElementChild); 

                      
                        item.querySelector('.btn-eliminar').addEventListener('click', function (e) {
                            e.preventDefault();
                            eliminarProducto(id);
                        });

                        muestraMensaje('success',1000,'¡Agregado!', 'El producto se agregó al carrito.');

                        setTimeout(() => location.reload(), 500);
                    } else {
                      
                        itemExistente.querySelector('small.cantidad-texto').textContent = `${data.producto.cantidad} x $${data.producto.precio_unitario}`;
                        itemExistente.querySelector('span.subtotal-texto').textContent = `$${data.producto.subtotal}`;
                    }

                    // Actualizar contador
                    const contador = document.querySelector('.contador');
                    if (contador) {
                       
                        if (!itemExistente) {
                            contador.textContent = parseInt(contador.textContent) + 1;
                        }
                    }

                    const totalGeneral = document.getElementById('total-general');
                    if (totalGeneral) {
                        totalGeneral.textContent = data.total_general; 
                    }
                } else {
                    alert("Error: " + data.mensaje);
                }
            })
            .catch(error => console.error("Error al procesar la solicitud:", error));
        });
    }
});


document.querySelectorAll(".btn-agregar-carrito-exterior").forEach((btn) => {
    btn.addEventListener("click", (e) => {
      e.preventDefault();
      const formCarrito = btn.closest(".form-carrito-exterior"); // form asociado a ese botón
      manejarCarrito(formCarrito);
    });
  });

  // 🔹 Función común para procesar el carrito
  function manejarCarrito(formCarrito) {
    const formData = new FormData(formCarrito);

    const stockDisponible = parseInt(formData.get("stockDisponible"));
    const idProducto = formData.get("id");
    console.log("Stock:", stockDisponible);
    console.log("Producto:", idProducto);

    // Verificar si ya existe en carrito
    const itemExistente = document.querySelector(`li[data-id="${idProducto}"]`);
    let cantidadActual = 0;
    if (itemExistente) {
      const textoCantidad = itemExistente.querySelector(".cantidad-texto")?.textContent;
      const match = textoCantidad.match(/^(\d+)/);
      if (match) {
        cantidadActual = parseInt(match[1]);
      }
    }

    if (stockDisponible === 0) {
      muestraMensaje("error", 1000, "Sin stock", "Este producto no está disponible actualmente.");
      return;
    }
    if (cantidadActual >= stockDisponible) {
      muestraMensaje("error", 1000, "Stock limitado", "Ya has agregado el máximo permitido.");
      return;
    }

    // Fetch al backend
    fetch("controlador/carrito.php", {
      method: "POST",
      body: formData,
    })
      .then((response) => response.json())
      .then((data) => {
        if (data.success) {
          muestraMensaje("success", 1000, "¡Agregado!", "El producto se agregó al carrito.");
          const ulCarrito = document.querySelector(".carrito-dropdown");
          if (!ulCarrito) {
            console.error("No se encontró el <ul> del carrito en el HTML.");
            return;
          }

          const liVacio = ulCarrito.querySelector("li.text-center");
          if (liVacio) {
            liVacio.remove();
          }

          const id = data.producto.id;
          let itemExistente = ulCarrito.querySelector(`li[data-id="${id}"]`);

          if (!itemExistente) {
            const item = document.createElement("li");
            item.className = "list-group-item d-flex justify-content-between lh-sm";
            item.setAttribute("data-id", id);

            item.innerHTML = `
              <div>
                <h6 class="fs-5 fw-normal my-0">${data.producto.nombre}</h6>
                <small class="text-muted cantidad-texto">${data.producto.cantidad} x $${data.producto.precio_unitario}</small>
              </div>
              <div class="text-end">
                <span class="text-body-secondary subtotal-texto">$${data.producto.subtotal}</span><br>
                <button class="btn-eliminar btn btn-sm btn-outline-danger mt-1" data-id="${id}">
                  <i class="fa-solid fa-x"></i>
                </button>
              </div>
            `;

            ulCarrito.insertBefore(item, ulCarrito.lastElementChild);

            item.querySelector(".btn-eliminar").addEventListener("click", function (e) {
              e.preventDefault();
              eliminarProducto(id);
            });

            muestraMensaje("success", 1000, "¡Agregado!", "El producto se agregó al carrito.");
            setTimeout(() => location.reload(), 500);
          } else {
            itemExistente.querySelector("small.cantidad-texto").textContent = `${data.producto.cantidad} x $${data.producto.precio_unitario}`;
            itemExistente.querySelector("span.subtotal-texto").textContent = `$${data.producto.subtotal}`;
          }

          // Actualizar contador
          const contador = document.querySelector(".contador");
          if (contador && !itemExistente) {
            contador.textContent = parseInt(contador.textContent) + 1;
          }

          const totalGeneral = document.getElementById("total-general");
          if (totalGeneral) {
            totalGeneral.textContent = data.total_general;
          }
        } else {
          alert("Error: " + data.mensaje);
        }
      })
      .catch((error) => console.error("Error al procesar la solicitud:", error));
  }



document.addEventListener("DOMContentLoaded", () => {
    const btnCart = document.querySelectorAll("button[href='?pagina=login']");

    btnCart.forEach((btnCart) =>{
        btnCart.addEventListener("click", (event) => {
            event.preventDefault();
            
            Swal.fire({
                title: "Registro requerido",
                text: "Necesitas registrarte para realizar esta accion. ¿Deseas continuar?",
                icon: "warning",
                showCancelButton: true,
                confirmButtonText: "Sí, continuar",
                cancelButtonText: "Cancelar"
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = "?pagina=login"; 
                }
            });
        });
    }
)});

$('#btnAyuda').on("click", function () {
    const currentURL = window.location.href;
    const driver = window.driver.js.driver;

    let steps = [
        { element: '#search-form', popover: { title: 'Buscador', description: 'Aquí puedes buscar cualquier producto de nuestro catálogo', side: "left" }},
        { element: '[aria-controls="offcanvasCart"]', popover: { title: 'Carrito de compras', description: 'Haz clic aquí para ver los productos que has agregado al carrito.', side: "left", align: 'start' }},
        { element: '[data-bs-target="#cerrar"]', popover: { title: 'Cerrar sesión', description: 'Este botón te permite cerrar sesión en tu cuenta.', side: "left", align: 'start' }},
        { element: '.section-title', popover: { title:'Productos más vendidos', description: 'Un listado de nuestros 10 productos más vendidos.', side: "top", align: 'start' }},
        { element: '.product-item', popover: { title: 'Productos', description: 'Estas son las cartas de nuestros productos. Puedes dar clic en la imagen para ver más detalles del producto.', side: "left", align: 'start' }},
        { element: '.categorias', popover: { title: 'Filtrado por categoría', description: 'Aquí podrás seleccionar las categorías y te saldrán los productos asociados', side: "left", align: 'start' }},
        { element: '#Botonlado', popover: { title: 'Ver todos los productos', description: 'Aquí puedes ver el listado de todos los productos', side: "left", align: 'start' }},
        { popover: { title: 'Eso es todo', description: 'Este es el fin de la guía, espero que hayas entendido' }}
    ];

    // Si la URL contiene "catalogo_producto", modificar ciertos pasos
    if (currentURL.includes("catalogo_producto")) {
        steps = steps.map(step => {
            if (step.element === '.section-title') {
                step.popover.title = 'Lista de productos';
                step.popover.description = 'Nuestra selección completa de productos';
            }
            return step;
        });
        steps = steps.map(step => {
            if (step.element === '#Botonlado') {
                step.popover.title = 'Ir hacia el carrito';
                step.popover.description = 'Este botón te llevará a tu carrito de compras.';
            }
            return step;
        });
    }

    // Si la URL contiene "ver_carrito", mostrar solo los primeros 3 pasos y agregar uno con ".table-light"
    if (currentURL.includes("vercarrito")) {
        steps = [
            { element: '.table-light', popover: { title: 'Lista del carrito', description: 'Aquí puedes ver los productos que has añadido al carrito.', side: "left", align: 'start' }},
            { element: '.Enlacecompra', popover: { title: 'Datos del pago', description: 'Aquí colocaras los datos del pago movil realizado y despues esperaras a la confirmacion', side: "left", align: 'start' }},
        ];
    }
    if (currentURL.includes("verpedidoweb")) {
        steps = [
            { element: '#formPedido', popover: { title: 'Datos del pago', description: 'Aquí colocaras los datos del pago movil realizado y despues esperaras a la confirmacion', side: "left", align: 'start' }},
            { element: '.Enlacecarrito', popover: { title: 'Lista del carrito', description: 'Aquí puedes ver los productos que has añadido al carrito.', side: "top", align: 'start' }},
            { element: '.header2', popover: { title: 'Tu resumen de pedido', description: 'Aqui ves el resumen de compra de los productos con su total y detalles', side: "top", align: 'start' }},
            { element: '.btn-rp', popover: { title: 'Listo para comprar', description: 'Una vez completado el rellenado', side: "left", align: 'start' }}
        ];
    }

    const driverObj = new driver({
        nextBtnText: 'Siguiente',
        prevBtnText: 'Anterior',
        doneBtnText: 'Listo',
        popoverClass: 'driverjs-theme',
        modal: true,
        closeBtn: false,
        steps: steps
    });

    // Iniciar el tour con los pasos actualizados
    driverObj.drive();
});

document.addEventListener('DOMContentLoaded', () => {
  document.body.addEventListener('click', e => {
    const btn = e.target.closest('.btn-favorito');
    if (!btn) return;

    const idProducto = btn.dataset.id;
    if (!idProducto) {
      Swal.fire({
        icon: 'error',
        title: 'Error',
        text: 'ID de producto no válido',
        timer: 1500,
        showConfirmButton: false,
        willClose: () => location.reload()
      });
      return;
    }

    fetch('?pagina=listadeseo', {
      method: 'POST',
      headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
      body: 'accion=agregar&id_producto=' + encodeURIComponent(idProducto)
    })
    .then(res => res.json())
    .then(data => {
      let icon = 'error';
      let title = 'Error';
      let text = data.message || 'No se pudo agregar.';

      if (data.status === 'success') {
        icon = 'success';
        title = '¡Agregado!';
        text = 'Producto añadido a tu lista de deseos.';
      } else if (data.status === 'exists') {
        icon = 'info';
        title = 'Aviso';
        text = 'Este producto ya está en tu lista.';
      }

      Swal.fire({
        icon,
        title,
        text,
        timer: 1500,
        showConfirmButton: false,
        willClose: () => location.reload()
      });
    })
    .catch(() => {
      Swal.fire({
        icon: 'error',
        title: 'Error',
        text: 'Error al procesar la solicitud.',
        timer: 1500,
        showConfirmButton: false,
        willClose: () => location.reload()
      });
    });
  });
});

  