<!-- Mini Carrito Overlays (Slide-Over Panel) -->
<div x-data="miniCart()" 
     @toggle-cart.window="open = !open" 
     @cart-updated.window="updateCart()"
     class="fixed inset-0 z-50 overflow-hidden pointer-events-none" 
     x-show="open" 
     style="display: none;">
    
    <!-- Backdrop -->
    <div class="absolute inset-0 bg-slate-900/60 backdrop-blur-xs pointer-events-auto transition-opacity" 
         @click="open = false" 
         x-show="open" 
         x-transition:enter="ease-in-out duration-300" 
         x-transition:enter-start="opacity-0" 
         x-transition:enter-end="opacity-100" 
         x-transition:leave="ease-in-out duration-300" 
         x-transition:leave-start="opacity-100" 
         x-transition:leave-end="opacity-0"></div>

    <!-- Sliding Panel -->
    <div class="absolute inset-y-0 right-0 max-w-md w-full bg-white shadow-2xl flex flex-col pointer-events-auto transform transition ease-in-out duration-300 border-l border-gray-100"
         :class="{'translate-x-0': open, 'translate-x-full': !open}">
        
        <!-- Header con Identidad Frito-Lay (Uso Sutil del Rojo) -->
        <div class="px-5 py-4 border-b border-gray-100 flex items-center justify-between bg-white shadow-2xs">
            <div class="flex items-center gap-2.5">
                <div class="w-8 h-8 rounded-xl bg-red-50 text-[#E3001B] border border-red-100 flex items-center justify-center font-black text-base shadow-2xs">
                    F
                </div>
                <div>
                    <h2 class="text-base font-extrabold text-gray-900 leading-tight">Mi Carrito</h2>
                    <p class="text-[11px] font-semibold text-gray-400" x-text="items.length > 0 ? items.length + ' producto(s) agregados' : 'Carrito de compras'"></p>
                </div>
            </div>
            <button @click="open = false" class="p-1.5 rounded-xl text-gray-400 hover:text-gray-700 hover:bg-gray-100 transition-all focus:outline-none">
                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
            </button>
        </div>

        <!-- Lista de Items -->
        <div class="flex-1 overflow-y-auto p-5 space-y-4 divide-y divide-gray-100">
            <!-- Estado Vacío -->
            <template x-if="items.length === 0">
                <div class="flex flex-col items-center justify-center py-20 text-center">
                    <div class="w-16 h-16 rounded-full bg-gray-50 flex items-center justify-center text-gray-300 mb-4 border border-gray-100">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path>
                        </svg>
                    </div>
                    <h3 class="font-bold text-gray-800 text-base">Tu carrito está vacío</h3>
                    <p class="text-xs text-gray-400 mt-1 max-w-xs">Explora nuestro catálogo y agrega tus snacks favoritos para realizar tu pedido.</p>
                </div>
            </template>

            <!-- Loop de Productos en Carrito -->
            <template x-for="item in items" :key="item.productoId">
                <div class="pt-4 first:pt-0 flex items-center gap-3 group">
                    <!-- Thumbnail de Producto -->
                    <div class="w-14 h-14 bg-gray-50 rounded-xl p-1 flex-shrink-0 flex items-center justify-center border border-gray-100 overflow-hidden">
                        <img :src="item.imagen || 'https://via.placeholder.com/80?text=Fritolay'" 
                             :alt="item.nombre" 
                             class="max-h-12 w-auto object-contain">
                    </div>

                    <!-- Título y Detalles -->
                    <div class="flex-1 min-w-0">
                        <h4 class="font-bold text-xs text-gray-900 truncate group-hover:text-[#E3001B] transition-colors" x-text="item.nombre"></h4>
                        <div class="text-[11px] font-medium text-gray-500 mt-0.5 flex items-center gap-1">
                            <span x-text="formatQty(item)"></span>
                            <span class="text-gray-300">•</span>
                            <span x-text="formatMoney(item.precioUnitario) + ' c/u'"></span>
                        </div>

                        <!-- Controles Táctiles de Cantidad (- / +) -->
                        <div class="flex items-center gap-2 mt-2">
                            <div class="flex items-center border border-gray-200 rounded-lg bg-gray-50/80 overflow-hidden">
                                <button type="button" 
                                        @click="window.CarritoManager.decrementarCantidad(item.productoId); updateCart();" 
                                        class="px-2 py-0.5 text-gray-600 hover:bg-gray-200 transition-colors font-bold text-xs">-</button>
                                <span class="w-6 text-center text-xs font-extrabold text-gray-900" x-text="item.cantidad"></span>
                                <button type="button" 
                                        @click="window.CarritoManager.incrementarCantidad(item.productoId); updateCart();" 
                                        class="px-2 py-0.5 text-gray-600 hover:bg-gray-200 transition-colors font-bold text-xs">+</button>
                            </div>
                        </div>
                    </div>

                    <!-- Subtotal de Línea y Eliminar (Uso de Rojo para Acción Destructiva) -->
                    <div class="flex flex-col items-end justify-between self-stretch py-0.5">
                        <button @click="window.CarritoManager.eliminarItem(item.productoId); updateCart();" 
                                title="Eliminar del carrito"
                                class="text-gray-400 hover:text-rose-600 transition-colors p-1 rounded-lg hover:bg-rose-50">
                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                        </button>
                        <span class="font-extrabold text-sm text-slate-900" x-text="formatMoney(item.cantidad * item.precioUnitario)"></span>
                    </div>
                </div>
            </template>
        </div>

        <!-- Footer con Resumen de Totales y Botones de Acción -->
        <div class="border-t border-gray-100 p-5 bg-gray-50/50 space-y-4">
            <!-- Fila de Subtotal/Total -->
            <div class="flex items-baseline justify-between">
                <span class="text-xs font-extrabold uppercase tracking-wider text-gray-500">Subtotal Estimado:</span>
                <span class="text-2xl font-black text-slate-900" x-text="formatMoney(subtotal)"></span>
            </div>

            <!-- Botones Primarios -->
            <div class="space-y-2">
                <!-- Proceder al Checkout (Color Corporativo Frito-Lay Amarillo #F5C518 con Texto Oscuro Elegante) -->
                <button @click="checkout" 
                        class="w-full bg-[#F5C518] hover:bg-amber-400 text-slate-950 font-black py-3.5 px-4 rounded-xl shadow-sm hover:shadow-md transition-all flex items-center justify-center gap-2 text-sm cursor-pointer disabled:opacity-40 disabled:cursor-not-allowed" 
                        :disabled="items.length === 0">
                    <span>Proceder al Checkout</span>
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M14 5l7 7m0 0l-7 7m7-7H3"></path></svg>
                </button>

                <!-- Vaciar Carrito (Acción Destructiva Sutil) -->
                <button @click="vaciarConAbandono()" 
                        class="w-full bg-white border border-gray-200 text-gray-600 hover:text-rose-600 hover:border-rose-200 hover:bg-rose-50 py-2.5 rounded-xl text-xs font-bold transition-all disabled:opacity-40 disabled:cursor-not-allowed cursor-pointer" 
                        :disabled="items.length === 0">
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
            Swal.fire({title: 'Procesando...', text: 'Redirigiendo a confirmación de pedido...', icon: 'info', timer: 1000, showConfirmButton: false});
            setTimeout(() => {
                window.location.href = '/ecommerce/checkout';
            }, 1000);
        },
        async vaciarConAbandono() {
            let opcionesMotivos = {
                'Encontré mejor precio': 'Encontré mejor precio',
                'Costo de envío alto': 'Costo de envío alto',
                'Decidí comprar después': 'Decidí comprar después',
                'Error en el pedido': 'Error en el pedido',
                'Problemas con método de pago': 'Problemas con método de pago',
                'Otro': 'Otro motivo'
            };

            try {
                const res = await window.api('/api/catalogo-motivos?tipo=abandono');
                if (res && res.data && res.data.length > 0) {
                    opcionesMotivos = {};
                    res.data.forEach(m => { opcionesMotivos[m.descripcion] = m.descripcion; });
                }
            } catch (_) {}

            const { value: motivoSeleccionado } = await Swal.fire({
                title: 'Vaciar Carrito',
                text: 'Indica obligatoriamente el motivo para vaciar tu carrito:',
                input: 'select',
                inputOptions: opcionesMotivos,
                inputPlaceholder: '-- Selecciona un motivo --',
                showCancelButton: true,
                confirmButtonColor: '#E3001B',
                cancelButtonColor: '#6b7280',
                confirmButtonText: 'Confirmar y Vaciar',
                cancelButtonText: 'Volver',
                inputValidator: (value) => {
                    return !value && 'Debes seleccionar un motivo para continuar';
                }
            });

            if (motivoSeleccionado) {
                await window.CarritoManager.abandonarCarrito(motivoSeleccionado);
                this.updateCart();
                Swal.fire({ icon: 'info', title: 'Carrito vaciado', text: 'Se ha registrado el motivo de abandono.', toast: true, position: 'bottom', showConfirmButton: false, timer: 3000 });
            }
        }
    }
}
</script>
