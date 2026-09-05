<div class="min-h-[calc(100vh-4rem)] bg-slate-100 pb-28" x-data="registrarEntregaMobile('{{ $pedidoId }}')">

    <!-- Header Fijo de la Entrega -->
    <div class="bg-slate-900 text-white p-4 sticky top-16 z-30 shadow-md">
        <div class="max-w-3xl mx-auto flex items-center justify-between gap-3">
            <div class="flex items-center gap-3">
                <button @click="volver()" class="p-2 rounded-xl bg-white/10 hover:bg-white/20 text-white transition-all flex items-center justify-center">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 19l-7-7 7-7"/></svg>
                </button>
                <div>
                    <h1 class="text-base sm:text-lg font-black tracking-tight flex items-center gap-2">
                        <span>Entrega Pedido #<span x-text="pedidoId"></span></span>
                    </h1>
                    <p class="text-xs text-slate-300 font-semibold truncate max-w-[200px] sm:max-w-md" x-text="clienteNombre"></p>
                </div>
            </div>
            <span class="px-2.5 py-1 rounded-full text-[10px] font-black uppercase tracking-wider border shadow-2xs"
                  :class="{
                      'bg-emerald-500/20 text-emerald-300 border-emerald-500/30': metodoPago === 'EFECTIVO',
                      'bg-blue-500/20 text-blue-300 border-blue-500/30': metodoPago === 'TC' || metodoPago === 'TD',
                      'bg-purple-500/20 text-purple-300 border-purple-500/30': metodoPago === 'DE_UNA' || metodoPago === 'DEPOSITO',
                      'bg-slate-700 text-slate-300 border-slate-600': !['EFECTIVO','TC','TD','DE_UNA','DEPOSITO'].includes(metodoPago)
                  }"
                  x-text="metodoPago"></span>
        </div>
    </div>

    <div class="max-w-3xl mx-auto px-3 sm:px-6 py-4 space-y-4">

        <!-- CLIENTE INFO CARD -->
        <div class="bg-white rounded-3xl p-5 border border-slate-200/80 shadow-2xs" x-show="clienteNombre">
            <h2 class="text-[10px] font-black uppercase text-slate-400 tracking-wider mb-3 flex items-center gap-1.5">
                <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                <span>Información del Punto de Entrega</span>
            </h2>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 text-xs">
                <div>
                    <span class="text-slate-400 font-bold block text-[10px] uppercase">Cliente / Local</span>
                    <p class="font-black text-slate-900 text-sm" x-text="clienteNombre">—</p>
                </div>
                <div>
                    <span class="text-slate-400 font-bold block text-[10px] uppercase">RUC / Cédula</span>
                    <p class="font-extrabold text-slate-800" x-text="clienteRuc || '—'"></p>
                </div>
                <div class="flex items-center justify-between">
                    <div>
                        <span class="text-slate-400 font-bold block text-[10px] uppercase">Teléfono</span>
                        <p class="font-extrabold text-slate-800" x-text="clienteTelefono || '—'"></p>
                    </div>
                    <template x-if="clienteTelefono">
                        <a :href="`tel:${clienteTelefono}`" class="px-3 py-1.5 bg-slate-100 hover:bg-slate-200 text-slate-800 rounded-xl font-bold text-xs flex items-center gap-1">
                            📞 Llamar
                        </a>
                    </template>
                </div>
                <div>
                    <span class="text-slate-400 font-bold block text-[10px] uppercase">Forma de Pago</span>
                    <p class="font-extrabold text-slate-800" x-text="metodoPagoLabel"></p>
                </div>
                <div class="sm:col-span-2 pt-1 border-t border-slate-100">
                    <span class="text-slate-400 font-bold block text-[10px] uppercase">Dirección Registrada</span>
                    <p class="font-semibold text-slate-700 text-xs leading-relaxed" x-text="direccionEntrega || '—'"></p>
                </div>
            </div>
        </div>

        <!-- FACTURA RESUMEN -->
        <div class="bg-white rounded-3xl p-5 border border-slate-200/80 shadow-2xs" x-show="factura">
            <div class="flex items-center justify-between mb-3">
                <h2 class="text-[10px] font-black uppercase text-slate-400 tracking-wider flex items-center gap-1.5">
                    <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                    <span>Comprobante de Factura SRI</span>
                </h2>
                <span class="font-mono text-xs font-black text-slate-900" x-text="factura?.numero_factura"></span>
            </div>
            <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 text-xs bg-slate-50 p-3 rounded-2xl border border-slate-100">
                <div>
                    <span class="text-slate-400 font-bold block text-[10px] uppercase">Emisión</span>
                    <p class="font-bold text-slate-800" x-text="factura?.fecha_emision || '—'"></p>
                </div>
                <div>
                    <span class="text-slate-400 font-bold block text-[10px] uppercase">Subtotal</span>
                    <p class="font-bold text-slate-800">$<span x-text="parseFloat(factura?.subtotal || 0).toFixed(2)"></span></p>
                </div>
                <div>
                    <span class="text-slate-400 font-bold block text-[10px] uppercase">IVA (15%)</span>
                    <p class="font-bold text-slate-800">$<span x-text="parseFloat(factura?.iva || 0).toFixed(2)"></span></p>
                </div>
                <div>
                    <span class="text-slate-400 font-bold block text-[10px] uppercase">Total Original</span>
                    <p class="font-black text-slate-900">$<span x-text="parseFloat(factura?.total || 0).toFixed(2)"></span></p>
                </div>
            </div>
        </div>

        <!-- LISTA DE PRODUCTOS EN TARJETAS CON CONTROLES TÁCTILES GRANDES -->
        <div class="bg-white p-5 rounded-3xl border border-slate-200/80 shadow-2xs space-y-4">
            <h2 class="font-black text-slate-900 text-base border-b border-slate-100 pb-3 flex items-center justify-between">
                <span>Productos a Entregar</span>
                <span class="text-xs text-slate-400 font-semibold" x-text="`${items.length} ítems`"></span>
            </h2>

            <div class="space-y-3.5">
                <template x-for="item in items" :key="item.id">
                    <div class="p-4 rounded-2xl border border-slate-200/80 bg-slate-50/50 space-y-3">
                        <div class="flex items-start justify-between gap-2">
                            <div>
                                <h3 class="font-black text-slate-900 text-sm leading-tight" x-text="item.nombre"></h3>
                                <p class="text-xs text-slate-500 font-medium mt-1">
                                    Solicitado: <span class="font-bold text-slate-800" x-text="item.solicitado"></span> unidades | $<span x-text="item.precio.toFixed(2)"></span> c/u
                                </p>
                            </div>
                            <span class="font-black text-sm text-slate-900" x-text="`$${(item.entregado * item.precio).toFixed(2)}`"></span>
                        </div>

                        <!-- Stepper Táctil Grande para Chofer (+ / -) -->
                        <div class="flex items-center justify-between gap-3 pt-2 border-t border-slate-200/60">
                            <span class="text-xs font-black text-slate-500 uppercase tracking-wider">Cantidad Entregada:</span>
                            <div class="flex items-center gap-1.5">
                                <button type="button" @click="decrementarItem(item)" 
                                        :disabled="item.entregado <= 0"
                                        class="w-11 h-11 rounded-xl bg-slate-200 hover:bg-slate-300 active:bg-slate-400 text-slate-800 font-black text-lg flex items-center justify-center transition-all disabled:opacity-30 touch-manipulation">
                                    -
                                </button>
                                <input type="number" x-model.number="item.entregado" min="0" :max="item.solicitado" 
                                       class="w-16 h-11 text-center font-black text-base text-slate-900 bg-white border border-slate-300 rounded-xl focus:ring-2 focus:ring-red-200 focus:outline-none shadow-2xs">
                                <button type="button" @click="incrementarItem(item)" 
                                        :disabled="item.entregado >= item.solicitado"
                                        class="w-11 h-11 rounded-xl bg-slate-200 hover:bg-slate-300 active:bg-slate-400 text-slate-800 font-black text-lg flex items-center justify-center transition-all disabled:opacity-30 touch-manipulation">
                                    +
                                </button>
                            </div>
                        </div>

                        <!-- Indicador de Devolución Parcial -->
                        <div x-show="item.entregado < item.solicitado" class="pt-1">
                            <div class="inline-flex items-center gap-1.5 bg-rose-50 text-rose-700 border border-rose-200 px-3 py-1.5 rounded-xl text-xs font-bold">
                                <span>⚠️ Devuelve:</span>
                                <span class="font-black" x-text="`${item.solicitado - item.entregado} unidades`"></span>
                            </div>
                        </div>
                    </div>
                </template>
            </div>
        </div>

        <!-- CHIPS TÁCTILES PARA MOTIVO DE DEVOLUCIÓN -->
        <div class="bg-rose-50 border border-rose-200 p-5 rounded-3xl shadow-2xs space-y-3" x-show="hayDevoluciones">
            <div class="flex items-center gap-2 text-rose-800 font-black text-xs uppercase tracking-wider">
                <svg class="w-5 h-5 text-rose-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                <span>Motivo de Devolución de la Entrega <span class="text-rose-600">*</span></span>
            </div>
            <p class="text-xs text-rose-700 font-medium leading-relaxed">Seleccione la razón por la cual no se entregan todas las unidades solicitadas:</p>
            
            <!-- Grid de Chips Táctiles para Selección Rápida con Pulgar -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-2">
                <template x-for="m in motivosChips" :key="m.val">
                    <button type="button" @click="motivoDevolucionGeneral = m.val"
                            :class="motivoDevolucionGeneral === m.val ? 'bg-[#E3001B] text-white border-[#E3001B] font-extrabold shadow-sm' : 'bg-white text-slate-800 border-slate-200 font-semibold hover:border-slate-300'"
                            class="p-3 rounded-2xl border text-xs text-left transition-all active:scale-[0.98] touch-manipulation flex items-center justify-between">
                        <span x-text="m.label"></span>
                        <span x-show="motivoDevolucionGeneral === m.val" class="text-white font-black">✓</span>
                    </button>
                </template>
            </div>
        </div>

        <!-- RESUMEN FINAL Y TOTAL -->
        <div class="bg-white p-5 rounded-3xl border border-slate-200/80 shadow-2xs flex items-center justify-between">
            <div>
                <span class="text-slate-400 font-bold block text-[10px] uppercase tracking-wider">Total Real a Recaudar</span>
                <span class="text-2xl font-black text-emerald-600" x-text="formatMoney(total)"></span>
            </div>
            <div x-show="metodoPago !== 'EFECTIVO' && totalDiferencia > 0" class="text-right max-w-[160px]">
                <span class="text-[10px] text-rose-600 font-bold block leading-tight">Devolución en pago electrónico requerirá Nota de Crédito.</span>
            </div>
        </div>

    </div>

    <!-- BOTTOM BAR FIJO DE CONFIRMACIÓN DE ENTREGA -->
    <div class="fixed bottom-0 left-0 right-0 bg-slate-900/95 backdrop-blur-md text-white p-3 border-t border-slate-800 z-40 shadow-2xl">
        <div class="max-w-3xl mx-auto flex items-center justify-between gap-3">
            <button @click="confirmarEntrega" :disabled="submitting" 
                    class="w-full h-14 bg-emerald-600 hover:bg-emerald-700 active:bg-emerald-800 text-white font-black text-base rounded-2xl flex items-center justify-center gap-2 shadow-lg transition-all border border-emerald-500 disabled:opacity-50 touch-manipulation">
                <svg x-show="!submitting" class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                <svg x-show="submitting" class="animate-spin h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                <span x-show="!submitting">Confirmar Entrega y Registrar Cobro</span>
                <span x-show="submitting">Procesando Entrega...</span>
            </button>
        </div>
    </div>

</div>

<script>
document.addEventListener('alpine:init', () => {
    Alpine.data('registrarEntregaMobile', (id) => ({
        pedidoId: id,
        metodoPago: 'EFECTIVO',
        metodoPagoLabel: 'Efectivo',
        clienteNombre: '',
        clienteRuc: '',
        clienteTelefono: '',
        direccionEntrega: '',
        factura: null,
        submitting: false,
        motivoDevolucionGeneral: '',
        items: [],

        motivosChips: [
            { val: 'Producto dañado / mal estado', label: '📦 Producto dañado / mal estado' },
            { val: 'Pedido incompleto / equivocado', label: '❌ Pedido incompleto / equivocado' },
            { val: 'Rechazado por cliente', label: '👤 Rechazado por cliente' },
            { val: 'Fecha de caducidad corta', label: '⏳ Fecha caducidad corta' },
            { val: 'Otro motivo', label: '✏️ Otro motivo' }
        ],

        get hayDevoluciones() {
            return this.items.some(i => i.entregado < i.solicitado);
        },

        async init() {
            try {
                const res = await window.api(`/api/pedidos/${this.pedidoId}`);
                const data = res.data || res;

                this.metodoPago = data.metodo_pago ? data.metodo_pago.toUpperCase() : 'EFECTIVO';
                const pagoLabels = {
                    'EFECTIVO': 'Efectivo', 'TC': 'Tarjeta Crédito', 'TD': 'Tarjeta Débito',
                    'DE_UNA': 'De Una (Transferencia)', 'DEPOSITO': 'Depósito Bancario'
                };
                this.metodoPagoLabel = pagoLabels[this.metodoPago] || this.metodoPago;

                const cliente = data.cliente || {};
                const usuario = cliente.usuario || {};
                this.clienteNombre = cliente.razon_social || cliente.nombre_cliente || usuario.nombre || 'Sin nombre';
                this.clienteRuc = cliente.ruc_cedula || '';
                this.clienteTelefono = cliente.telefono || usuario.telefono || '';
                
                const dir = data.direccion || {};
                this.direccionEntrega = dir.descripcion || '';

                this.factura = data.factura || null;

                this.items = (data.items || []).map(i => ({
                    id: i.id,
                    nombre: i.producto ? i.producto.nombre : 'Producto ' + i.producto_id,
                    precio: parseFloat(i.precio_unitario),
                    solicitado: parseFloat(i.cantidad_solicitada),
                    entregado: parseFloat(i.cantidad_solicitada)
                }));
            } catch (e) {
                console.error("Error al cargar datos de pedido:", e);
            }
        },

        incrementarItem(item) {
            if (item.entregado < item.solicitado) item.entregado++;
        },

        decrementarItem(item) {
            if (item.entregado > 0) item.entregado--;
        },

        get total() {
            return this.items.reduce((acc, item) => acc + (item.entregado * item.precio), 0);
        },
        
        get totalDiferencia() {
            return this.items.reduce((acc, item) => acc + ((item.solicitado - item.entregado) * item.precio), 0);
        },

        volver() {
            const urlParams = new URLSearchParams(window.location.search);
            const guiaId = urlParams.get('guia');
            if (guiaId) {
                window.location.href = `/entregas/mapa/${guiaId}`;
            } else {
                window.location.href = '/entregas';
            }
        },

        async confirmarEntrega() {
            if (this.submitting) return;
            
            if (this.hayDevoluciones && (!this.motivoDevolucionGeneral || !this.motivoDevolucionGeneral.trim())) {
                window.toast('Seleccione el motivo de devolución', 'warning', 'bottom');
                return;
            }

            this.submitting = true;
            try {
                const urlParams = new URLSearchParams(window.location.search);
                const guiaId = urlParams.get('guia');
                const motivo = this.motivoDevolucionGeneral || 'Otro motivo';
                
                const payload = {
                    pedido_id: parseInt(this.pedidoId),
                    items: this.items.map(i => ({
                        item_pedido_id: i.id,
                        cantidad_entregada: i.entregado,
                        cantidad_devuelta: i.solicitado - i.entregado,
                        motivo_devolucion: i.solicitado > i.entregado ? motivo : null,
                        estado_mercaderia: i.solicitado > i.entregado ? (motivo === 'Producto dañado / mal estado' ? 'mal_estado' : 'buen_estado') : null
                    }))
                };
                
                await window.api('/api/entregas', {
                    method: 'POST',
                    body: JSON.stringify(payload)
                });
                
                window.toast('Entrega registrada exitosamente', 'success', 'bottom');
                
                setTimeout(() => {
                    this.volver();
                }, 800);
            } catch (e) {
                this.submitting = false;
                window.toast(e.message || 'Error al registrar la entrega', 'error', 'bottom');
            }
        }
    }));
});
</script>
@endsection
