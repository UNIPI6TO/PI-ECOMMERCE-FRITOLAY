@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto py-8 px-4" x-data="historial()">
    <h1 class="text-3xl font-bold mb-8">Historial de Pedidos</h1>
    
    <div class="mb-6 flex gap-4 items-center">
        <input type="text" x-model="dateFilter" placeholder="Ej: last 7 days" class="border rounded px-4 py-2 w-64">
        <button @click="filtrar" class="bg-gray-800 text-white px-4 py-2 rounded">Filtrar</button>
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
                        <td class="p-4" x-text="pedido.fecha"></td>
                        <td class="p-4">
                            <span class="px-2 py-1 rounded text-xs font-semibold" 
                                :class="{
                                    'bg-yellow-100 text-yellow-800': pedido.estado === 'PENDIENTE',
                                    'bg-green-100 text-green-800': pedido.estado === 'ENTREGADO',
                                    'bg-blue-100 text-blue-800': pedido.estado === 'EN RUTA'
                                }"
                                x-text="pedido.estado"></span>
                        </td>
                        <td class="p-4" x-text="`$${pedido.total.toFixed(2)}`"></td>
                        <td class="p-4 text-center space-x-2">
                            <button @click="verPdf(pedido)" class="text-sm bg-[#E3001B] text-white px-3 py-1 rounded">Ver Factura PDF</button>
                            <a :href="`/ecommerce/rastreo/${pedido.id}`" x-show="pedido.estado === 'EN RUTA'" class="text-sm bg-[#F5C518] text-black px-3 py-1 rounded font-medium">Rastrear</a>
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
        dateFilter: '',
        pedidos: [
            { id: 'PED-001', fecha: '2023-10-01', estado: 'ENTREGADO', total: 45.50 },
            { id: 'PED-002', fecha: '2023-10-15', estado: 'EN RUTA', total: 12.00 }
        ],
        filtrar() {
            // Logica filtro
        },
        verPdf(pedido) {
            if(window.pdfGenerator) window.pdfGenerator.generateFactura(pedido);
        }
    }));
});
</script>
@endsection
