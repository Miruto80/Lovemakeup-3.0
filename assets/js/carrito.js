document.addEventListener("DOMContentLoaded", () => {
    document.addEventListener('click', function (e) {
        // Manejar botón de eliminar (solo los del carrito lateral, no los de otras vistas)
        const btnEliminar = e.target.closest('#listgroup .btn-eliminar');
        if (btnEliminar) {
            e.preventDefault();
            const id = btnEliminar.getAttribute('data-id');
            if (id) eliminarProducto(id);
            return;
        }

        // Manejar botones de sumar/restar cantidad (si aplican en tu vista)
        const btnCantidad = e.target.closest('.btn-cantidad');
        if (btnCantidad) {
            e.preventDefault();
            const id = btnCantidad.getAttribute('data-id');
            const accion = btnCantidad.getAttribute('data-accion'); 
            if (id && accion) actualizarCantidad(id, accion);
        }
    });
});

function actualizarCantidad(id, accion) {
    fetch('controlador/carrito.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: new URLSearchParams({ id, accion })
    })
    .then(res => res.json())
    .then(data => {
        if (data.success) {
            const item = document.querySelector(`div[data-id="${id}"]`);
            
            if (data.eliminado) {
                if (item) item.remove();
                actualizarContadorGlobal();
                verificarCarritoVacio();
            } else if (item) {
                const cant = parseInt(data.cantidad, 10);
                const precio = parseFloat(data.precio).toFixed(2);
                const subtotal = parseFloat(data.subtotal).toFixed(2);

                // Actualizar el texto del desglose (Cantidad x Precio)
                const infoTexto = item.querySelector('.cantidad-texto');
                if (infoTexto) {
                    infoTexto.textContent = `${cant} x $${precio}`;
                }

                // Actualizar el subtotal del producto
                const subtotalTexto = item.querySelector('.subtotal-texto');
                if (subtotalTexto) {
                    subtotalTexto.textContent = `$${subtotal}`;
                }
            }

            actualizarTotal(data.total);
        } else {
            console.error('Error al actualizar cantidad:', data.message || data);
        }
    })
    .catch(error => console.error('Error en la solicitud:', error));
}

function eliminarProducto(id) {
    fetch('controlador/carrito.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: new URLSearchParams({ accion: 'eliminar', id })
    })
    .then(res => res.json())
    .then(data => {
        if (data.success) {
            const item = document.querySelector(`div[data-id="${id}"]`);
            if (item) item.remove();

            actualizarTotal(data.total);
            actualizarContadorGlobal();
            verificarCarritoVacio();
        } else {
            alert(data.message || 'Error al eliminar el producto');
        }
    })
    .catch(error => console.error('Error al procesar la eliminación:', error));
}

function actualizarContadorGlobal() {
    const totalItems = document.querySelectorAll('#listgroup > div[data-id]').length;
    document.querySelectorAll('.contador').forEach(el => {
        el.textContent = totalItems;
    });
}

function actualizarTotal(montoBackend) {
    const subtotalGeneral = document.getElementById('cartSubtotalUSD');
    if (!subtotalGeneral) return;

    if (montoBackend !== undefined && !isNaN(montoBackend)) {
        subtotalGeneral.textContent = `$${parseFloat(montoBackend).toFixed(2)}`;
    } else {
        let acumulado = 0;
        document.querySelectorAll('.subtotal-texto').forEach(el => {
            const monto = parseFloat(el.textContent.replace('$', '').replace(/,/g, ''));
            if (!isNaN(monto)) acumulado += monto;
        });
        subtotalGeneral.textContent = `$${acumulado.toFixed(2)}`;
    }
}

function verificarCarritoVacio() {
    const productosRestantes = document.querySelectorAll('div[data-id]');
    if (productosRestantes.length === 0) {
        recargarCarrito();
    }
}


function recargarCarrito() {
    const contenedorCarrito = document.getElementById("listgroup");
    if (!contenedorCarrito) return;

    contenedorCarrito.innerHTML = `
        <div class="flex flex-col items-center justify-center py-12 px-4 text-center">
            <div class="w-16 h-16 bg-pink-50 text-accent-fuchsia rounded-full flex items-center justify-center mb-4 shadow-inner">
                <i class="fa-solid fa-cart-shopping text-2xl"></i>
            </div>
            <p class="text-sm font-bold text-gray-800 mb-1">Tu carrito está vacío</p>
            <p class="text-xs text-gray-500 max-w-xs mb-6">Explora nuestro catálogo para agregar productos a tu compra.</p>
            <a href="?pagina=catalogo" class="px-5 py-2.5 bg-accent-fuchsia hover:bg-pink-700 text-white rounded-2xl text-xs font-bold transition-all shadow-md shadow-pink-500/20 flex items-center gap-2">
                <i class="fa-solid fa-store"></i>
                <span>Ver Catálogo</span>
            </a>
        </div>
    `;

    // Bloquea el botón de checkout/ver carrito si existiera
    const btnCheckout = document.getElementById("carritover");
    if (btnCheckout) {
        btnCheckout.classList.add('opacity-50', 'pointer-events-none');
    }
}