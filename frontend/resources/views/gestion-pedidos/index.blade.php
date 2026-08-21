@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto py-8 px-4" x-data="gestionPedidos()">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold">Gestión de Pedidos</h1>
        <input type="text" placeholder="Filtro (ej: last 24h)" class="border px-4 py-2 rounded w-64">
    </div>

    <!-- KPI Cards -->
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-8">
        <template x-for="estado in estados" :key="estado.nombre">
            <div @click="filtroEstado = estado.nombre" class="bg-white p-4 rounded shadow cursor-pointer hover:bg-gray-50 border-l-4" :class="estado.color">
                <div class="text-sm text-gray-500 font-medium" x-text="estado.nombre"></div>
                <div class="text-2xl font-bold" x-text="estado.count"></div>
            </div>
        </template>
    </div>

    <!-- Mapa -->
    <div class="bg-white p-4 rounded shadow mb-8">
        <h2 class="font-semibold mb-4">Vista Geográfica</h2>
        <div id="mapa-gestion" style="height: 400px;" class="rounded border z-0"></div>
    </div>

    <div class="flex justify-between items-center mb-4">
        <h2 class="font-semibold text-lg">Listado de Pedidos</h2>
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
    <div class="bg-white rounded shadow overflow-x-auto">
        <table class="w-full text-left">
            <thead class="bg-gray-100">
                <tr>
                    <th class="p-3 cursor-pointer hover:bg-gray-200" @click="sort('id')">ID ↕</th>
                    <th class="p-3 cursor-pointer hover:bg-gray-200" @click="sort('cliente')">Cliente ↕</th>
                    <th class="p-3">Método Pago</th>
                    <th class="p-3">Total</th>
                    <th class="p-3">Estado</th>
                    <th class="p-3 cursor-pointer hover:bg-gray-200" @click="sort('fecha')">Fecha ↕</th>
                    <th class="p-3">Acciones</th>
                </tr>
            </thead>
            <tbody>
                <template x-if="paginatedPedidos.length === 0">
                    <tr><td colspan="7" class="p-4 text-center text-gray-500">No hay pedidos para mostrar</td></tr>
                </template>
                <template x-for="p in paginatedPedidos" :key="p.id">
                    <tr class="border-b hover:bg-gray-50">
                        <td class="p-3" x-text="p.id"></td>
                        <td class="p-3" x-text="p.cliente"></td>
                        <td class="p-3" x-text="p.pago"></td>
                        <td class="p-3 font-medium" x-text="`$${Number(p.total).toFixed(2)}`"></td>
                        <td class="p-3"><span class="px-2 py-1 text-xs font-semibold rounded-full bg-gray-100 text-gray-700" x-text="p.estado"></span></td>
                        <td class="p-3 text-sm text-gray-500" x-text="p.fecha"></td>
                        <td class="p-3">
                            <button class="text-primary hover:text-red-800 font-medium text-sm transition-colors">Ver Detalle</button>
                        </td>
                    </tr>
                </template>
            </tbody>
        </table>
        
        <!-- Paginador -->
        <div class="p-4 border-t border-gray-100 flex items-center justify-between bg-gray-50">
            <div class="text-sm text-gray-500">
                Mostrando pág <span class="font-medium text-gray-800" x-text="currentPage"></span> de <span class="font-medium text-gray-800" x-text="totalPages"></span> 
                (<span x-text="filteredPedidos.length"></span> registros totales)
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
    Alpine.data('gestionPedidos', () => ({
        filtroEstado: null,
        estados: [
            {nombre: 'PENDIENTE', count: 0, color: 'border-yellow-500'},
            {nombre: 'APROBADO', count: 0, color: 'border-blue-500'},
            {nombre: 'EN_RUTA', count: 0, color: 'border-indigo-500'},
            {nombre: 'ENTREGADO', count: 0, color: 'border-green-500'}
        ],
        pedidos: [],
        
        // Paginación
        currentPage: 1,
        perPage: 10,
        sortCol: 'id',
        sortAsc: true,
        
        async init() {
            try {
                this.pedidos = await window.api('/api/pedidos');
                this.updateCounts();
            } catch (error) {
                console.error("Error al cargar pedidos:", error);
            }
            
            setTimeout(() => {
                const map = L.map('mapa-gestion').setView([-1.249, -78.616], 13);
                L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png').addTo(map);
            }, 100);
        },
        
        updateCounts() {
            this.estados.forEach(e => {
                e.count = this.pedidos.filter(p => p.estado === e.nombre).length;
            });
        },

        get filteredPedidos() {
            let filtered = this.pedidos;
            if(this.filtroEstado) filtered = filtered.filter(p => p.estado === this.filtroEstado);
            
            return filtered.sort((a, b) => {
                let mod = this.sortAsc ? 1 : -1;
                return a[this.sortCol] > b[this.sortCol] ? mod : -mod;
            });
        },
        
        get totalPages() {
            return Math.ceil(this.filteredPedidos.length / this.perPage) || 1;
        },
        
        get paginatedPedidos() {
            const start = (this.currentPage - 1) * this.perPage;
            return this.filteredPedidos.slice(start, start + this.perPage);
        },

        sort(col) {
            if(this.sortCol === col) this.sortAsc = !this.sortAsc;
            else { this.sortCol = col; this.sortAsc = true; }
        },
        
        nextPage() {
            if (this.currentPage < this.totalPages) this.currentPage++;
        },
        
        prevPage() {
            if (this.currentPage > 1) this.currentPage--;
        }
    }));
});
</script>
@endsection
