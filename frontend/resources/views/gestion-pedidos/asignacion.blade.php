@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto py-8 px-4" x-data="asignacion()">
    <h1 class="text-2xl font-bold mb-6">Asignación de Rutas</h1>
    
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Panel Izquierdo: Mapa y Disponibles -->
        <div class="bg-white p-4 rounded shadow">
            <div id="mapa-asignacion" style="height: 300px;" class="rounded border mb-4 z-0"></div>
            
            <h3 class="font-bold mb-2">Pedidos Listos para Asignar</h3>
            <div class="space-y-2 max-h-64 overflow-y-auto">
                <template x-for="p in pendientes" :key="p.id">
                    <label class="flex items-center p-3 border rounded hover:bg-gray-50 cursor-pointer">
                        <input type="checkbox" :value="p.id" x-model="selectedPendientes" class="mr-3">
                        <div>
                            <div class="font-medium" x-text="p.id + ' - ' + p.cliente"></div>
                            <div class="text-sm text-gray-500" x-text="p.direccion"></div>
                        </div>
                    </label>
                </template>
            </div>
            <button @click="moverADerecha" class="mt-4 w-full bg-blue-600 text-white py-2 rounded">Añadir a Ruta →</button>
        </div>

        <!-- Panel Derecho: Camion y Asignados -->
        <div class="bg-white p-4 rounded shadow flex flex-col">
            <h3 class="font-bold mb-2">Configurar Ruta</h3>
            <select x-model="camionSeleccionado" class="w-full border rounded p-2 mb-4">
                <option value="">Seleccione un camión...</option>
                <option value="CAM-1">CAM-1 (Chofer: Luis)</option>
                <option value="CAM-2">CAM-2 (Chofer: Mario)</option>
            </select>

            <h3 class="font-bold mb-2 text-sm text-gray-600">Pedidos en esta ruta:</h3>
            <div class="flex-1 space-y-2 overflow-y-auto border p-2 rounded bg-gray-50">
                <template x-for="p in asignados" :key="p.id">
                    <div class="flex justify-between p-2 bg-white border rounded">
                        <span x-text="p.id"></span>
                        <button @click="moverAIzquierda(p.id)" class="text-red-500 text-sm">Quitar</button>
                    </div>
                </template>
                <div x-show="asignados.length === 0" class="text-sm text-gray-400 text-center py-4">No hay pedidos asignados</div>
            </div>
            
            <button @click="cerrarAsignacion" :disabled="!camionSeleccionado || asignados.length === 0" class="mt-4 w-full bg-green-600 text-white py-3 rounded font-bold disabled:opacity-50">
                Cerrar Asignación e Imprimir Guías
            </button>
        </div>
    </div>
</div>

<script>
document.addEventListener('alpine:init', () => {
    Alpine.data('asignacion', () => ({
        pendientes: [],
        asignados: [],
        selectedPendientes: [],
        camionSeleccionado: '',

        init() {
            setTimeout(() => {
                const map = L.map('mapa-asignacion').setView([-1.249, -78.616], 13);
                L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png').addTo(map);
            }, 100);
        },

        moverADerecha() {
            const toMove = this.pendientes.filter(p => this.selectedPendientes.includes(p.id));
            this.asignados.push(...toMove);
            this.pendientes = this.pendientes.filter(p => !this.selectedPendientes.includes(p.id));
            this.selectedPendientes = [];
        },

        moverAIzquierda(id) {
            const item = this.asignados.find(p => p.id === id);
            this.pendientes.push(item);
            this.asignados = this.asignados.filter(p => p.id !== id);
        },

        async cerrarAsignacion() {
            // POST /api/asignaciones
            if(window.pdfGenerator) {
                window.pdfGenerator.generateGuiaRemision();
                window.pdfGenerator.generateGuiaRuta();
            }
            Swal.fire({ icon: 'success', title: 'Éxito', text: 'Ruta asignada con éxito', toast: true, position: 'bottom', showConfirmButton: false, timer: 3000 });
            this.asignados = [];
        }
    }));
});
</script>
@endsection


