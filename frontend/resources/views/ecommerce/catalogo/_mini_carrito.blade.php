<!-- Mini Carrito Overlays -->
<div x-data="miniCart()" 
     @toggle-cart.window="open = !open" 
     @cart-updated.window="updateCart()"
     class="fixed inset-0 z-50 overflow-hidden pointer-events-none" 
     x-show="open" 
     style="display: none;">
    
    <!-- Backdrop -->
    <div class="absolute inset-0 bg-black bg-opacity-50 pointer-events-auto transition-opacity" 
         @click="open = false" 
         x-show="open" 
         x-transition:enter="ease-in-out duration-300" 
         x-transition:enter-start="opacity-0" 
         x-transition:enter-end="opacity-100" 
         x-transition:leave="ease-in-out duration-300" 
         x-transition:leave-start="opacity-100" 
         x-transition:leave-end="opacity-0"></div>

    <!-- Sliding Panel -->
    <div class="absolute inset-y-0 right-0 max-w-sm w-full bg-white shadow-xl flex flex-col pointer-events-auto transform transition ease-in-out duration-300"
         :class="{'translate-x-0': open, 'translate-x-full': !open}">
        
        <!-- Header -->
        <div class="px-4 py-4 border-b flex items-center justify-between bg-primary text-white">
            <h2 class="text-lg font-bold">Mi Carrito</h2>
            <button @click="open = false" class="text-white hover:text-gray-200 focus:outline-none">
                <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
            </button>
        </div>

        <!-- Items -->
        <div class="flex-1 overflow-y-auto p-4 space-y-4">
            <template x-if="items.length === 0">
                <p class="text-gray-500 text-center mt-4">Tu carrito está vacío.</p>
            </template>

            <template x-for="item in items" :key="item.productoId">
                <div class="flex justify-between items-center border-b pb-2">
                    <div>
                        <h4 class="font-bold text-sm text-neutral-dark" x-text="item.nombre"></h4>
                        <div class="text-xs text-gray-500 mt-1">
                            <span x-text="formatQty(item) + ' x $' + item.precioUnitario.toFixed(2)"></span>
                        </div>
                    </div>
                    <div class="flex items-center space-x-2">
                        <span class="font-bold text-sm" x-text="'$' + (item.cantidad * item.precioUnitario).toFixed(2)"></span>
                        <button @click="window.CarritoManager.eliminarItem(item.productoId); updateCart();" class="text-red-500 hover:text-red-700">
                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                        </button>
                    </div>
                </div>
            </template>
        </div>

        <!-- Footer -->
        <div class="border-t p-4 bg-gray-50">
            <div class="flex justify-between font-bold text-lg mb-4">
                <span>Subtotal:</span>
                <span x-text="'$' + subtotal"></span>
            </div>
            <div class="space-y-2">
                <button @click="checkout" class="w-full bg-secondary text-neutral-dark font-bold py-2 rounded hover:bg-yellow-500" :disabled="items.length === 0">
                    Proceder al Checkout
                </button>
                <button @click="vaciarConAbandono()" class="w-full bg-white border border-gray-300 text-gray-700 py-2 rounded hover:bg-gray-100" :disabled="items.length === 0">
                    Vaciar Carrito
                </button>
            </div>
        </div>
    </div>
</div>

<script>
function miniCart() {
    return {
        open: false,
        items: [],
        subtotal: 0,
        init() {
            this.updateCart();
        },
        updateCart() {
            if (window.CarritoManager) {
                this.items = window.CarritoManager.getItems();
                this.subtotal = window.CarritoManager.calcularSubtotal();
                this.$dispatch('cart-updated-internal');
            }
        },
        formatQty(item) {
            let uP = item.unidadesPorPaca || 1;
            if (uP <= 1) return `${item.cantidad} unds`;
            let pacas = Math.floor(item.cantidad / uP);
            let unds = item.cantidad % uP;
            let res = [];
            if (pacas > 0) res.push(`${pacas} paca${pacas > 1 ? 's' : ''}`);
            if (unds > 0) res.push(`${unds} und${unds > 1 ? 's' : ''}`);
            return res.join(' y ') || '0 unds';
        },
        checkout() {
            if (!localStorage.getItem('jwt_token')) {
                Swal.fire({
                    title: 'Inicia sesión',
                    text: 'Debes iniciar sesión para realizar tu pedido.',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#E3001B',
                    confirmButtonText: 'Ir al Login',
                    cancelButtonText: 'Seguir comprando'
                }).then((result) => {
                    if (result.isConfirmed) {
                        window.location.href = '/auth/login';
                    }
                });
                return;
            }
            Swal.fire({title: 'Procesando...', text: 'Redirigiendo a pasarela de pago / confirmación de pedido...', icon: 'info', timer: 1000, showConfirmButton: false});
            setTimeout(() => {
                window.location.href = '/ecommerce/checkout';
            }, 1000);
        },
        async vaciarConAbandono() {
            const result = await Swal.fire({
                title: '¿Vaciar carrito?',
                text: 'Se registrará el abandono del carrito y se eliminará su contenido.',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#E3001B',
                cancelButtonColor: '#6b7280',
                confirmButtonText: 'Sí, vaciar',
                cancelButtonText: 'Cancelar'
            });
            if (!result.isConfirmed) return;
            await window.CarritoManager.abandonarCarrito('Carrito vaciado manualmente por el usuario');
            this.updateCart();
            Swal.fire({ icon: 'info', title: 'Carrito vaciado', toast: true, position: 'bottom', showConfirmButton: false, timer: 2500 });
        }
    }
}
</script>
