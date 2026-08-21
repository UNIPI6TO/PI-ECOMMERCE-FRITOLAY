@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto py-8 px-4" x-data="facturas()">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold">Listado de Facturas</h1>
        <div class="flex items-center space-x-2 text-sm">
            <span class="text-gray-600">Mostrar:</span>
            <select x-model="perPage" @change="currentPage = 1" class="border-gray-300 rounded-md text-sm py-1 pl-2 pr-8 focus:ring-primary focus:border-primary border">
                <option :value="5">5</option>
                <option :value="10">10</option>
                <option :value="20">20</option>
                <option :value="50">50</option>
            </select>
            <span class="text-gray-600">registros</span>
        </div>
    </div>

    <div class="flex gap-4 mb-6 bg-white p-4 rounded-lg shadow-sm border border-gray-100">
        <input type="text" placeholder="Buscar por número..." class="border-gray-300 rounded-md px-4 py-2 flex-1 border focus:ring-primary focus:border-primary">
        <input type="date" class="border-gray-300 rounded-md px-4 py-2 border focus:ring-primary focus:border-primary">
        <button class="bg-gray-800 hover:bg-gray-900 text-white px-6 py-2 rounded-md transition-colors">Filtrar</button>
    </div>

    <div class="bg-white rounded-lg shadow-sm border border-gray-100 overflow-hidden">
        <table class="w-full text-left">
            <thead class="bg-neutral-light border-b border-gray-100">
                <tr>
                    <th class="p-4">Nro. Factura</th>
                    <th class="p-4">Fecha</th>
                    <th class="p-4">Cliente</th>
                    <th class="p-4">Total</th>
                    <th class="p-4 text-center">Acciones</th>
                </tr>
            </thead>
            <tbody>
                <template x-if="paginatedListado.length === 0">
                    <tr><td colspan="5" class="p-4 text-center text-gray-500">No hay facturas para mostrar</td></tr>
                </template>
                <template x-for="f in paginatedListado" :key="f.id">
                    <tr class="border-b hover:bg-gray-50">
                        <td class="p-4 font-medium" x-text="f.numero"></td>
                        <td class="p-4" x-text="f.fecha"></td>
                        <td class="p-4" x-text="f.cliente"></td>
                        <td class="p-4 font-medium" x-text="`$${Number(f.total).toFixed(2)}`"></td>
                        <td class="p-4 text-center">
                            <button @click="exportarPdf(f)" class="bg-[#E3001B] hover:bg-red-800 transition-colors text-white px-3 py-1 rounded text-sm shadow-sm">Exportar PDF</button>
                        </td>
                    </tr>
                </template>
            </tbody>
        </table>
        
        <!-- Paginador -->
        <div class="p-4 border-t border-gray-100 flex items-center justify-between bg-gray-50">
            <div class="text-sm text-gray-500">
                Mostrando pág <span class="font-medium text-gray-800" x-text="currentPage"></span> de <span class="font-medium text-gray-800" x-text="totalPages"></span> 
                (<span x-text="listado.length"></span> registros totales)
            </div>
            <div class="flex space-x-2">
                <button @click="prevPage" :disabled="currentPage === 1" class="px-3 py-1 border border-gray-300 rounded-md text-sm font-medium text-gray-700 hover:bg-gray-100 transition-colors disabled:opacity-50 disabled:cursor-not-allowed">Anterior</button>
                <button @click="nextPage" :disabled="currentPage === totalPages" class="px-3 py-1 border border-gray-300 rounded-md text-sm font-medium text-gray-700 hover:bg-gray-100 transition-colors disabled:opacity-50 disabled:cursor-not-allowed">Siguiente</button>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('alpine:init', () => {
    Alpine.data('facturas', () => ({
        listado: [],
        
        // Paginación
        currentPage: 1,
        perPage: 10,
        
        get totalPages() {
            return Math.ceil(this.listado.length / this.perPage) || 1;
        },
        
        get paginatedListado() {
            const start = (this.currentPage - 1) * this.perPage;
            return this.listado.slice(start, start + this.perPage);
        },

        nextPage() {
            if (this.currentPage < this.totalPages) this.currentPage++;
        },
        
        prevPage() {
            if (this.currentPage > 1) this.currentPage--;
        },

        async init() {
            try {
                this.listado = await window.api('/api/facturas');
            } catch (error) {
                console.error("Error al cargar facturas:", error);
            }
        },
        exportarPdf(factura) {
            if(window.pdfGenerator) window.pdfGenerator.generateFactura(factura);
        }
    }));
});
</script>
@endsection
