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

    <!-- Tabla -->
    <div class="bg-white rounded shadow overflow-x-auto">
        <table class="w-full text-left">
            <thead class="bg-gray-100">
                <tr>
                    <th class="p-3 cursor-pointer" @click="sort('id')">ID ↕</th>
                    <th class="p-3 cursor-pointer" @click="sort('cliente')">Cliente ↕</th>
                    <th class="p-3">Método Pago</th>
                    <th class="p-3">Total</th>
                    <th class="p-3">Estado</th>
                    <th class="p-3">Fecha</th>
                    <th class="p-3">Acciones</th>
                </tr>
            </thead>
            <tbody>
                <template x-for="p in sortedPedidos" :key="p.id">
                    <tr class="border-b hover:bg-gray-50">
                        <td class="p-3" x-text="p.id"></td>
                        <td class="p-3" x-text="p.cliente"></td>
                        <td class="p-3" x-text="p.pago"></td>
                        <td class="p-3" x-text="`$${p.total}`"></td>
                        <td class="p-3"><span class="px-2 py-1 text-xs rounded bg-gray-200" x-text="p.estado"></span></td>
                        <td class="p-3" x-text="p.fecha"></td>
                        <td class="p-3">
                            <button class="text-blue-600 hover:underline text-sm">Ver Detalle</button>
                        </td>
                    </tr>
                </template>
            </tbody>
        </table>
    </div>
</div>

<script>
document.addEventListener('alpine:init', () => {
    Alpine.data('gestionPedidos', () => ({
        filtroEstado: null,
        estados: [
            {nombre: 'Pendientes', count: 12, color: 'border-yellow-500'},
            {nombre: 'Aprobados', count: 5, color: 'border-blue-500'},
            {nombre: 'En Ruta', count: 8, color: 'border-indigo-500'},
            {nombre: 'Entregados', count: 24, color: 'border-green-500'}
        ],
        pedidos: [
            {id: 'P-1', cliente: 'Juan Perez', pago: 'EFECTIVO', total: 100, estado: 'Pendientes', fecha: '2023-10-01'},
            {id: 'P-2', cliente: 'Ana Gomez', pago: 'DEPOSITO', total: 250, estado: 'En Ruta', fecha: '2023-10-02'}
        ],
        sortCol: 'id',
        sortAsc: true,
        
        get sortedPedidos() {
            let filtered = this.pedidos;
            if(this.filtroEstado) filtered = filtered.filter(p => p.estado === this.filtroEstado);
            
            return filtered.sort((a, b) => {
                let mod = this.sortAsc ? 1 : -1;
                return a[this.sortCol] > b[this.sortCol] ? mod : -mod;
            });
        },

        sort(col) {
            if(this.sortCol === col) this.sortAsc = !this.sortAsc;
            else { this.sortCol = col; this.sortAsc = true; }
        },

        init() {
            setTimeout(() => {
                const map = L.map('mapa-gestion').setView([-1.249, -78.616], 13);
                L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png').addTo(map);
            }, 100);
        }
    }));
});
</script>
@endsection
