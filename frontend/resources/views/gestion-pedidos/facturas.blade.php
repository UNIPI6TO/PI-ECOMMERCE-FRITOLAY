@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto py-8 px-4" x-data="facturas()">
    <h1 class="text-2xl font-bold mb-6">Facturación</h1>

    <div class="flex gap-4 mb-6 bg-white p-4 rounded shadow">
        <input type="text" placeholder="Buscar por número..." class="border rounded px-4 py-2 flex-1">
        <input type="date" class="border rounded px-4 py-2">
        <button class="bg-gray-800 text-white px-6 py-2 rounded">Filtrar</button>
    </div>

    <div class="bg-white rounded shadow overflow-hidden">
        <table class="w-full text-left">
            <thead class="bg-gray-100">
                <tr>
                    <th class="p-4">Nro. Factura</th>
                    <th class="p-4">Fecha</th>
                    <th class="p-4">Cliente</th>
                    <th class="p-4">Total</th>
                    <th class="p-4 text-center">Acciones</th>
                </tr>
            </thead>
            <tbody>
                <template x-for="f in listado" :key="f.id">
                    <tr class="border-b hover:bg-gray-50">
                        <td class="p-4 font-medium" x-text="f.numero"></td>
                        <td class="p-4" x-text="f.fecha"></td>
                        <td class="p-4" x-text="f.cliente"></td>
                        <td class="p-4" x-text="`$${f.total}`"></td>
                        <td class="p-4 text-center">
                            <button @click="exportarPdf(f)" class="bg-[#E3001B] text-white px-3 py-1 rounded text-sm">Exportar PDF</button>
                        </td>
                    </tr>
                </template>
            </tbody>
        </table>
    </div>
</div>

<script>
document.addEventListener('alpine:init', () => {
    Alpine.data('facturas', () => ({
        listado: [
            {id: 1, numero: 'FAC-001-001-0000123', fecha: '2023-10-01', cliente: 'Tienda Juan', total: 45.50},
            {id: 2, numero: 'FAC-001-001-0000124', fecha: '2023-10-02', cliente: 'Supermaxi', total: 120.00}
        ],
        exportarPdf(factura) {
            if(window.pdfGenerator) window.pdfGenerator.generateFactura(factura);
        }
    }));
});
</script>
@endsection
