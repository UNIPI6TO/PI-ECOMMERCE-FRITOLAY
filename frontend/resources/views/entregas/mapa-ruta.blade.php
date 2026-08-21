@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto py-8 px-4" x-data="mapaRuta('{{ $guiaRutaId }}')">
    <div class="flex h-[80vh] gap-4">
        <!-- Panel Lateral -->
        <div class="w-1/3 bg-white p-4 rounded shadow flex flex-col">
            <h2 class="font-bold text-lg mb-4">Ruta #<span x-text="guiaId"></span></h2>
            
            <div class="flex bg-gray-100 p-1 rounded mb-4">
                <button @click="orden = 'CERCANO'" class="flex-1 py-1 text-sm rounded" :class="orden === 'CERCANO' ? 'bg-white shadow font-bold' : ''">Más Cercano</button>
                <button @click="orden = 'ANTIGUO'" class="flex-1 py-1 text-sm rounded" :class="orden === 'ANTIGUO' ? 'bg-white shadow font-bold' : ''">Más Antiguo</button>
            </div>

            <div class="flex-1 overflow-y-auto space-y-3">
                <template x-for="(p, index) in pedidosList" :key="p.id">
                    <div class="border p-3 rounded" :class="{'border-blue-500 bg-blue-50': p.estado === 'SELECCIONADO', 'opacity-50': p.estado === 'ENTREGADO'}">
                        <div class="flex justify-between items-start mb-2">
                            <div class="font-bold"><span x-text="index+1"></span>. <span x-text="p.cliente"></span></div>
                            <span class="text-xs bg-gray-200 px-2 rounded" x-text="p.estado"></span>
                        </div>
                        <div class="text-sm text-gray-600 mb-2" x-text="p.direccion"></div>
                        
                        <div class="flex space-x-2" x-show="p.estado !== 'ENTREGADO'">
                            <button @click="seleccionar(p.id)" x-show="p.estado !== 'SELECCIONADO'" class="flex-1 bg-gray-800 text-white text-sm py-1 rounded">Seleccionar</button>
                            <button @click="navegar(p)" x-show="p.estado === 'SELECCIONADO'" class="flex-1 bg-blue-600 text-white text-sm py-1 rounded">Navegar GPS</button>
                            <a :href="`/entregas/entregar/${p.id}`" x-show="p.estado === 'SELECCIONADO'" class="flex-1 bg-green-600 text-white text-sm py-1 rounded text-center block leading-loose">Entregar</a>
                        </div>
                    </div>
                </template>
            </div>
            
            <a href="/entregas/cierre-caja" class="mt-4 w-full bg-[#E3001B] text-white py-3 rounded font-bold text-center block">
                Terminar Ruta
            </a>
        </div>

        <!-- Mapa -->
        <div class="w-2/3 bg-white rounded shadow relative">
            <div id="mapa-ruta-full" class="absolute inset-0 rounded z-0"></div>
        </div>
    </div>
</div>

<script>
document.addEventListener('alpine:init', () => {
    Alpine.data('mapaRuta', (id) => ({
        guiaId: id,
        orden: 'CERCANO',
        pedidos: [],
        map: null,

        get pedidosList() {
            return this.pedidos;
        },

        async init() {
            try {
                // If API exists to fetch this guia's details:
                // this.pedidos = await window.api(`/api/guias-ruta/${this.guiaId}/pedidos`);
            } catch (e) {
                console.error(e);
            }
            setTimeout(() => {
                this.map = L.map('mapa-ruta-full').setView([-1.249, -78.616], 14);
                L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png').addTo(this.map);
                
                if(window.gpsTracker) window.gpsTracker.startTracking();
            }, 100);
        },

        seleccionar(id) {
            // PATCH api/pedidos/seleccionar
            this.pedidos.forEach(p => { if(p.estado === 'SELECCIONADO') p.estado = 'PENDIENTE'; });
            const p = this.pedidos.find(x => x.id === id);
            p.estado = 'SELECCIONADO';
        },

        navegar(p) {
            window.open(`https://maps.google.com/?q=${p.lat},${p.lng}&travelmode=driving`);
        }
    }));
});
</script>
@endsection
