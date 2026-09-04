@extends('layouts.app')

@section('content')
<div class="max-w-3xl mx-auto py-8 px-4" x-data="registrarEntrega('{{ $pedidoId }}')">

    {{-- HEADER --}}
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-2xl font-bold">Entrega Pedido #<span x-text="pedidoId"></span></h1>
            <p class="text-sm text-gray-500 mt-1" x-text="clienteNombre"></p>
        </div>
        <span class="px-3 py-1 rounded text-sm font-bold uppercase"
              :class="{
                  'bg-green-100 text-green-700': metodoPago === 'EFECTIVO',
                  'bg-blue-100 text-blue-700': metodoPago === 'TC' || metodoPago === 'TD',
                  'bg-purple-100 text-purple-700': metodoPago === 'DE_UNA' || metodoPago === 'DEPOSITO',
                  'bg-gray-200 text-gray-700': !['EFECTIVO','TC','TD','DE_UNA','DEPOSITO'].includes(metodoPago)
              }"
              x-text="metodoPago"></span>
    </div>

    {{-- CLIENTE INFO --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5 mb-5" x-show="clienteNombre">
        <h2 class="text-xs font-bold uppercase text-gray-400 tracking-widest mb-3">Información del Cliente</h2>
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 text-sm">
            <div>
                <span class="text-gray-500">Nombre / Razón Social</span>
                <p class="font-semibold text-gray-800" x-text="clienteNombre">—</p>
            </div>
            <div>
                <span class="text-gray-500">RUC / Cédula</span>
                <p class="font-semibold text-gray-800" x-text="clienteRuc || '—'"></p>
            </div>
            <div>
                <span class="text-gray-500">Teléfono</span>
                <p class="font-semibold text-gray-800" x-text="clienteTelefono || '—'"></p>
            </div>
            <div>
                <span class="text-gray-500">Método de Pago</span>
                <p class="font-semibold" x-text="metodoPagoLabel"></p>
            </div>
            <div class="sm:col-span-2">
                <span class="text-gray-500">Dirección de Entrega</span>
                <p class="font-semibold text-gray-800" x-text="direccionEntrega || '—'"></p>
            </div>
        </div>
    </div>

    {{-- FACTURA --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5 mb-5" x-show="factura">
        <h2 class="text-xs font-bold uppercase text-gray-400 tracking-widest mb-3">Factura</h2>
        <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 text-sm">
            <div>
                <span class="text-gray-500">N° Factura</span>
                <p class="font-bold text-gray-800" x-text="factura?.numero_factura || '—'"></p>
            </div>
            <div>
                <span class="text-gray-500">Fecha Emisión</span>
                <p class="font-semibold text-gray-800" x-text="factura?.fecha_emision || '—'"></p>
            </div>
            <div>
                <span class="text-gray-500">Subtotal</span>
                <p class="font-semibold text-gray-800">$<span x-text="parseFloat(factura?.subtotal || 0).toFixed(2)"></span></p>
            </div>
            <div>
                <span class="text-gray-500">IVA</span>
                <p class="font-semibold text-gray-800">$<span x-text="parseFloat(factura?.iva || 0).toFixed(2)"></span></p>
            </div>
        </div>
        <div class="mt-3 pt-3 border-t flex justify-between items-center">
            <span class="text-gray-500 text-sm">Total Facturado</span>
            <span class="text-lg font-bold text-gray-900">$<span x-text="parseFloat(factura?.total || 0).toFixed(2)"></span></span>
        </div>
    </div>

    {{-- Sin factura aún --}}
    <div class="bg-yellow-50 border border-yellow-200 rounded-xl p-4 mb-5 text-sm text-yellow-800" x-show="!factura && dataLoaded">
        <strong>Sin factura registrada.</strong> La factura se generará al confirmar la entrega.
    </div>

    {{-- PRODUCTOS --}}
    <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100 mb-5">
        <h2 class="font-bold mb-4 border-b pb-2">Productos</h2>
        <div class="space-y-4">
            <template x-for="item in items" :key="item.id">
                <div class="flex flex-col sm:flex-row justify-between items-center border-b pb-4 gap-4">
                    <div class="flex-1 w-full">
                        <div class="font-bold text-lg text-gray-800" x-text="item.nombre"></div>
                        <div class="text-sm text-gray-500 mt-1">Solicitado: <span class="font-bold text-gray-700" x-text="item.solicitado"></span> | Precio: $<span x-text="item.precio"></span></div>
                        
                        <div x-show="item.entregado < item.solicitado" 
                             class="mt-2">
                            <div class="inline-flex items-center gap-1 bg-red-50 text-red-700 border border-red-200 px-2.5 py-1 rounded-lg text-xs font-semibold">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 15v-1a4 4 0 00-4-4H8m0 0l3 3m-3-3l3-3m9 14V5a2 2 0 00-2-2H6a2 2 0 00-2 2v16l4-2 4 2 4-2 4 2z" /></svg>
                                Devuelve: <span class="font-black" x-text="item.solicitado - item.entregado"></span> unidades
                            </div>
                        </div>
                    </div>
                    <div class="flex gap-4 items-center">
                        <label class="flex flex-col items-center bg-gray-50 p-2 rounded-xl border border-gray-200">
                            <span class="text-xs text-green-700 font-bold mb-2 uppercase tracking-wider">Entregado</span>
                            <input type="number" x-model.number="item.entregado" min="0" :max="item.solicitado" 
                                   class="w-24 border-2 border-green-500 rounded-lg px-2 py-3 text-center font-black text-xl text-green-700 focus:ring focus:ring-green-200 focus:outline-none transition-all shadow-inner bg-white">
                        </label>
                    </div>
                </div>
            </template>
        </div>
    </div>

    {{-- SECCIÓN MOTIVO DE DEVOLUCIÓN DE LA ENTREGA (Solo si hay ítems devueltos) --}}
    <div class="bg-red-50 border-2 border-red-200 p-5 rounded-xl shadow-sm mb-5" x-show="hayDevoluciones">
        <div class="flex items-center gap-2 mb-2 text-red-800 font-extrabold text-sm">
            <svg class="w-5 h-5 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
            <span>Motivo de Devolución de la Entrega <span class="text-red-600">*</span></span>
        </div>
        <p class="text-xs text-red-600 font-medium mb-3">Se han detectado productos no entregados en su totalidad. Por favor seleccione el motivo global de la devolución:</p>
        <select x-model="motivoDevolucionGeneral" class="w-full border-2 border-red-300 rounded-xl p-3 text-xs font-bold text-gray-800 focus:ring focus:ring-red-200 focus:outline-none bg-white">
            <option value="">-- Seleccione un motivo de devolución --</option>
            <option value="Producto dañado / mal estado">Producto dañado / mal estado</option>
            <option value="Pedido incompleto / equivocado">Pedido incompleto / equivocado</option>
            <option value="Rechazado por cliente">Rechazado por cliente</option>
            <option value="Fecha de caducidad corta">Fecha de caducidad corta</option>
            <option value="Otro motivo">Otro motivo</option>
        </select>
    </div>

    {{-- TOTAL --}}
    <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100 mb-6">
        <div class="flex justify-between items-center text-xl font-bold">
            <span>Total a Cobrar:</span>
            <span class="text-green-600">$<span x-text="total.toFixed(2)"></span></span>
        </div>
        <div x-show="metodoPago !== 'EFECTIVO' && totalDiferencia > 0" class="text-red-500 text-sm mt-2">
            Nota: El pago no es en efectivo. Las devoluciones requieren proceso administrativo.
        </div>
    </div>

    <button @click="confirmarEntrega" :disabled="submitting" class="w-full bg-[#83b735] hover:bg-green-700 text-white font-bold py-4 rounded-xl text-lg shadow-md transition-colors disabled:opacity-50 flex items-center justify-center gap-2">
        <span x-show="!submitting">Confirmar Entrega</span>
        <span x-show="submitting">Registrando Entrega...</span>
    </button>
</div>

<script>
document.addEventListener('alpine:init', () => {
    Alpine.data('registrarEntrega', (id) => ({
        pedidoId: id,
        metodoPago: 'EFECTIVO',
        metodoPagoLabel: 'Efectivo',
        clienteNombre: '',
        clienteRuc: '',
        clienteTelefono: '',
        direccionEntrega: '',
        factura: null,
        dataLoaded: false,
        submitting: false,
        motivoDevolucionGeneral: '',
        items: [],

        get hayDevoluciones() {
            return this.items.some(i => i.entregado < i.solicitado);
        },

        async init() {
            try {
                const res = await window.api(`/api/pedidos/${this.pedidoId}`);
                const data = res.data || res;

                // Método de pago
                this.metodoPago = data.metodo_pago ? data.metodo_pago.toUpperCase() : 'EFECTIVO';
                const pagoLabels = {
                    'EFECTIVO': 'Efectivo', 'TC': 'Tarjeta Crédito', 'TD': 'Tarjeta Débito',
                    'DE_UNA': 'De Una (Transferencia)', 'DEPOSITO': 'Depósito Bancario'
                };
                this.metodoPagoLabel = pagoLabels[this.metodoPago] || this.metodoPago;

                // Información del cliente
                const cliente = data.cliente || {};
                const usuario = cliente.usuario || {};
                this.clienteNombre = cliente.razon_social || cliente.nombre_cliente || usuario.nombre || 'Sin nombre';
                this.clienteRuc = cliente.ruc_cedula || '';
                this.clienteTelefono = cliente.telefono || usuario.telefono || '';
                
                // Dirección
                const dir = data.direccion || {};
                this.direccionEntrega = dir.descripcion || '';

                // Factura
                this.factura = data.factura || null;

                // Items
                this.items = (data.items || []).map(i => ({
                    id: i.id,
                    nombre: i.producto ? i.producto.nombre : 'Producto ' + i.producto_id,
                    precio: parseFloat(i.precio_unitario),
                    solicitado: parseFloat(i.cantidad_solicitada),
                    entregado: parseFloat(i.cantidad_solicitada)
                }));

                if (this.metodoPago !== 'EFECTIVO') {
                    this.$watch('items', () => {
                        this.items.forEach(i => {
                            if(i.entregado < i.solicitado) i.entregado = i.solicitado;
                        });
                    }, {deep: true});
                }

                this.dataLoaded = true;
            } catch (e) {
                console.error(e);
                this.dataLoaded = true;
            }
        },

        get total() {
            return this.items.reduce((acc, item) => acc + (item.entregado * item.precio), 0);
        },
        
        get totalDiferencia() {
            return this.items.reduce((acc, item) => acc + ((item.solicitado - item.entregado) * item.precio), 0);
        },

        async confirmarEntrega() {
            if (this.submitting) return;
            
            // Validar motivo de devolución global si hay ítems no entregados en su totalidad
            if (this.hayDevoluciones && (!this.motivoDevolucionGeneral || !this.motivoDevolucionGeneral.trim())) {
                Swal.fire({ 
                    icon: 'warning', 
                    title: 'Motivo de Devolución Requerido', 
                    text: 'Por favor seleccione el motivo de devolución al momento de confirmar la entrega.', 
                    toast: true, 
                    position: 'bottom', 
                    showConfirmButton: false, 
                    timer: 3500 
                });
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
                
                await Swal.fire({ icon: 'success', title: 'Éxito', text: 'Entrega registrada', toast: true, position: 'bottom', showConfirmButton: false, timer: 3000 });
                
                if (guiaId) {
                    window.location.href = `/entregas/mapa/${guiaId}`;
                } else {
                    window.location.href = '/entregas';
                }
            } catch (e) {
                this.submitting = false;
                Swal.fire({ icon: 'error', title: 'Error', text: e.message || 'No se pudo registrar la entrega', toast: true, position: 'bottom', showConfirmButton: false, timer: 3000 });
            }
        }
    }));
});
</script>
@endsection
