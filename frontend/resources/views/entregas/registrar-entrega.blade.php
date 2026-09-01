@extends('layouts.app')

@section('content')
<div class="max-w-3xl mx-auto py-8 px-4" x-data="registrarEntrega('{{ $pedidoId }}')">
    <div class="flex items-center justify-between mb-6">
        <h1 class="text-2xl font-bold">Entrega Pedido #<span x-text="pedidoId"></span></h1>
        <span class="bg-gray-200 px-3 py-1 rounded text-sm font-semibold" x-text="metodoPago"></span>
    </div>

    <div class="bg-white p-6 rounded shadow mb-6">
        <h2 class="font-bold mb-4 border-b pb-2">Productos</h2>
        <div class="space-y-4">
            <template x-for="item in items" :key="item.id">
                <div class="flex flex-col sm:flex-row justify-between items-center border-b pb-4 gap-4">
                    <div class="flex-1 w-full">
                        <div class="font-bold text-lg text-gray-800" x-text="item.nombre"></div>
                        <div class="text-sm text-gray-500 mt-1">Solicitado: <span class="font-bold text-gray-700" x-text="item.solicitado"></span> | Precio: $<span x-text="item.precio"></span></div>
                        
                        <div x-show="metodoPago === 'EFECTIVO' && item.entregado < item.solicitado" 
                             class="mt-2 inline-flex items-center gap-1 bg-red-50 text-red-700 border border-red-200 px-2 py-1 rounded text-sm font-semibold">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 15v-1a4 4 0 00-4-4H8m0 0l3 3m-3-3l3-3m9 14V5a2 2 0 00-2-2H6a2 2 0 00-2 2v16l4-2 4 2 4-2 4 2z" /></svg>
                            Devuelve: <span x-text="item.solicitado - item.entregado"></span>
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

    <div class="bg-white p-6 rounded shadow mb-6">
        <div class="flex justify-between items-center text-xl font-bold">
            <span>Total a Cobrar:</span>
            <span class="text-green-600">$<span x-text="total.toFixed(2)"></span></span>
        </div>
        <div x-show="metodoPago !== 'EFECTIVO' && totalDiferencia > 0" class="text-red-500 text-sm mt-2">
            Nota: El pago no es en efectivo. Las devoluciones requieren proceso administrativo.
        </div>
    </div>

    <button @click="confirmarEntrega" class="w-full bg-[#83b735] hover:bg-green-700 text-white font-bold py-4 rounded text-lg">
        Confirmar Entrega
    </button>
</div>

<script>
document.addEventListener('alpine:init', () => {
    Alpine.data('registrarEntrega', (id) => ({
        pedidoId: id,
        metodoPago: 'EFECTIVO', // Simulado
        items: [],

        async init() {
            try {
                const res = await window.api(`/api/pedidos/${this.pedidoId}`);
                const data = res.data || res;
                this.metodoPago = data.metodo_pago ? data.metodo_pago.toUpperCase() : 'EFECTIVO';
                
                this.items = (data.items || []).map(i => ({
                    id: i.id, // item_pedido_id
                    nombre: i.producto ? i.producto.nombre : 'Producto ' + i.producto_id,
                    precio: parseFloat(i.precio_unitario),
                    solicitado: parseFloat(i.cantidad_solicitada),
                    entregado: parseFloat(i.cantidad_solicitada), // Default full delivery
                    motivo_devolucion: ''
                }));
                
                if (this.metodoPago !== 'EFECTIVO') {
                    this.$watch('items', () => {
                        this.items.forEach(i => {
                            if(i.entregado < i.solicitado) i.entregado = i.solicitado;
                        });
                    }, {deep: true});
                }
            } catch (e) {
                console.error(e);
            }
        },

        get total() {
            return this.items.reduce((acc, item) => acc + (item.entregado * item.precio), 0);
        },
        
        get totalDiferencia() {
            return this.items.reduce((acc, item) => acc + ((item.solicitado - item.entregado) * item.precio), 0);
        },

        async confirmarEntrega() {
            try {
                const urlParams = new URLSearchParams(window.location.search);
                const guiaId = urlParams.get('guia');
                
                const payload = {
                    pedido_id: parseInt(this.pedidoId),
                    items: this.items.map(i => ({
                        item_pedido_id: i.id,
                        cantidad_entregada: i.entregado,
                        cantidad_devuelta: i.solicitado - i.entregado,
                        motivo_devolucion: i.motivo_devolucion || (i.solicitado > i.entregado ? 'Rechazado por cliente' : null),
                        estado_mercaderia: i.solicitado > i.entregado ? 'buen_estado' : null
                    }))
                };
                
                await window.api('/api/entregas', {
                    method: 'POST',
                    body: JSON.stringify(payload)
                });
                
                if(window.pdfGenerator) window.pdfGenerator.generateFactura();
                Swal.fire({ icon: 'success', title: 'Éxito', text: 'Entrega registrada', toast: true, position: 'bottom', showConfirmButton: false, timer: 3000 });
                
                if (guiaId) {
                    window.location.href = `/entregas/mapa/${guiaId}`;
                } else {
                    window.location.href = '/entregas';
                }
            } catch (e) {
                Swal.fire({ icon: 'error', title: 'Error', text: e.message || 'No se pudo registrar la entrega', toast: true, position: 'bottom', showConfirmButton: false, timer: 3000 });
            }
        }
    }));
});
</script>
@endsection

