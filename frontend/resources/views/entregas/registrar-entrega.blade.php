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
                <div class="flex flex-col sm:flex-row justify-between border-b pb-4 gap-4">
                    <div class="flex-1">
                        <div class="font-medium" x-text="item.nombre"></div>
                        <div class="text-sm text-gray-500">Solicitado: <span x-text="item.solicitado"></span> | Precio: $<span x-text="item.precio"></span></div>
                    </div>
                    <div class="flex gap-4 items-center">
                        <label class="flex flex-col items-center">
                            <span class="text-xs text-green-600 mb-1">Entregado</span>
                            <input type="number" x-model.number="item.entregado" min="0" :max="item.solicitado" class="w-20 border rounded px-2 py-1 text-center font-bold">
                        </label>
                        <label class="flex flex-col items-center" x-show="metodoPago === 'EFECTIVO' && item.entregado < item.solicitado">
                            <span class="text-xs text-red-600 mb-1">Devuelto</span>
                            <input type="number" :value="item.solicitado - item.entregado" disabled class="w-20 border rounded px-2 py-1 text-center bg-gray-100 text-red-600">
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
                // To be wired to GET /api/pedidos/{pedidoId} if needed
                // const data = await window.api(`/api/pedidos/${this.pedidoId}`);
                // this.items = data.items;
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

        init() {
            if(this.metodoPago !== 'EFECTIVO') {
                // Forzar entregado = solicitado si no es efectivo (por reglas de negocio simplificadas o deshabilitar parcial)
                this.items.forEach(i => {
                    this.$watch(`items`, () => {
                       if(i.entregado < i.solicitado) i.entregado = i.solicitado; 
                    }, {deep: true});
                });
            }
        },

        async confirmarEntrega() {
            // POST /api/entregas
            if(window.pdfGenerator) window.pdfGenerator.generateFactura();
            Swal.fire({ icon: 'success', title: 'Éxito', text: 'Entrega registrada', toast: true, position: 'bottom', showConfirmButton: false, timer: 3000 });
            window.location.href = '/entregas/mapa/1'; // redirect a mapa ruta
        }
    }));
});
</script>
@endsection

