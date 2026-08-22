@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto py-8 px-4" x-data="historial()">
    <h1 class="text-3xl font-bold mb-8">Historial de Pedidos</h1>
    
    <div class="mb-6 flex gap-4 items-center">
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Desde</label>
            <input type="date" x-model="fechaInicio" class="border rounded px-4 py-2 w-40">
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Hasta</label>
            <input type="date" x-model="fechaFin" class="border rounded px-4 py-2 w-40">
        </div>
        <div class="pt-6">
            <button @click="filtrar" class="bg-gray-800 text-white px-4 py-2 rounded font-semibold hover:bg-gray-700">Aplicar Filtros</button>
            <button @click="limpiarFiltros" class="bg-gray-300 text-black px-4 py-2 rounded font-semibold hover:bg-gray-400 ml-2">Limpiar</button>
        </div>
    </div>

    <div class="bg-white rounded-lg shadow overflow-hidden">
        <table class="w-full text-left">
            <thead class="bg-gray-50 border-b">
                <tr>
                    <th class="p-4"># Pedido</th>
                    <th class="p-4">Fecha</th>
                    <th class="p-4">Estado</th>
                    <th class="p-4">Total</th>
                    <th class="p-4 text-center">Acciones</th>
                </tr>
            </thead>
            <tbody>
                <template x-for="pedido in pedidos" :key="pedido.id">
                    <tr class="border-b hover:bg-gray-50">
                        <td class="p-4 font-medium" x-text="pedido.id"></td>
                        <td class="p-4" x-text="new Date(pedido.creado_en || pedido.created_at).toLocaleDateString()"></td>
                        <td class="p-4">
                            <span class="px-2 py-1 rounded text-xs font-semibold uppercase" 
                                :class="{
                                    'bg-yellow-100 text-yellow-800': pedido.estado.includes('espera'),
                                    'bg-green-100 text-green-800': pedido.estado.includes('entregado'),
                                    'bg-blue-100 text-blue-800': pedido.estado === 'en_ruta' || pedido.estado === 'listo_para_entregar',
                                    'bg-red-100 text-red-800': pedido.estado === 'cancelado' || pedido.estado === 'no_entregado'
                                }"
                                x-text="pedido.estado.replace(/_/g, ' ')"></span>
                        </td>
                        <td class="p-4" x-text="`$${Number(pedido.total).toFixed(2)}`"></td>
                        <td class="p-4 text-center space-x-2">
                            <button @click="verPdf(pedido)" class="text-sm bg-[#E3001B] text-white px-3 py-1 rounded">Ver Factura PDF</button>
                            <a :href="`/ecommerce/rastreo/${pedido.id}`" x-show="pedido.estado === 'en_ruta'" class="text-sm bg-[#F5C518] text-black px-3 py-1 rounded font-medium">Rastrear</a>
                        </td>
                    </tr>
                </template>
            </tbody>
        </table>
    </div>
</div>

<script>
document.addEventListener('alpine:init', () => {
    Alpine.data('historial', () => ({
        fechaInicio: '',
        fechaFin: '',
        pedidos: [],
        pedidosOriginales: [],
        async init() {
            try {
                // Get current client info to get clienteId
                let clienteData = await window.api('/api/clientes/me');
                if (clienteData && clienteData.data) clienteData = clienteData.data;
                if (clienteData && clienteData.id) {
                    const response = await window.api(`/api/clientes/${clienteData.id}/pedidos`);
                    this.pedidosOriginales = response.data || response || [];
                    this.pedidos = [...this.pedidosOriginales];
                }
            } catch (error) {
                console.error("Error al cargar historial:", error);
            }
        },
        filtrar() {
            if (!this.fechaInicio && !this.fechaFin) {
                this.pedidos = [...this.pedidosOriginales];
                return;
            }
            
            this.pedidos = this.pedidosOriginales.filter(pedido => {
                // The date format from backend might be 'YYYY-MM-DD HH:MM:SS'
                const pedidoFecha = new Date(pedido.creado_en || pedido.fecha || pedido.created_at);
                let valido = true;

                if (this.fechaInicio) {
                    const inicio = new Date(this.fechaInicio + 'T00:00:00');
                    if (pedidoFecha < inicio) valido = false;
                }
                
                if (this.fechaFin) {
                    const fin = new Date(this.fechaFin + 'T23:59:59');
                    if (pedidoFecha > fin) valido = false;
                }

                return valido;
            });
        },
        limpiarFiltros() {
            this.fechaInicio = '';
            this.fechaFin = '';
            this.pedidos = [...this.pedidosOriginales];
        },
        verPdf(pedido) {
            if(window.pdfGenerator) window.pdfGenerator.generateFactura(pedido);
        }
    }));
});
</script>
@endsection
