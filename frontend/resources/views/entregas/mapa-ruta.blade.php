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
                    <div class="border p-3 rounded" :class="{'border-blue-500 bg-blue-50': p.ui_estado === 'SELECCIONADO', 'opacity-50': p.estado === 'ENTREGADO'}">
                        <div class="flex justify-between items-start mb-2">
                            <div class="font-bold"><span x-text="index+1"></span>. <span x-text="p.cliente"></span></div>
                            <span class="text-xs bg-gray-200 px-2 rounded" x-text="p.estado === 'EN_RUTA' ? 'En Camino' : p.estado"></span>
                        </div>
                        <div class="text-sm text-gray-600 mb-2" x-text="p.direccion"></div>
                        
                        <div class="flex space-x-2" x-show="p.estado !== 'ENTREGADO'">
                            <button @click="seleccionar(p.id)" x-show="p.estado !== 'SELECCIONADO'" class="flex-1 bg-gray-800 text-white text-sm py-1 rounded">Seleccionar</button>
                            <button @click="navegar(p)" x-show="p.ui_estado === 'SELECCIONADO'" class="flex-1 bg-blue-600 text-white text-sm py-1 rounded">Navegar GPS</button>
                            <a :href="`/entregas/entregar/${p.id}`" x-show="p.ui_estado === 'SELECCIONADO'" class="flex-1 bg-green-600 text-white text-sm py-1 rounded text-center block leading-loose">Entregar</a>
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

        markers: [],
        
        async init() {
            try {
                this.pedidos = await window.api(`/api/guias-ruta/${this.guiaId}/pedidos`);
                // By default the backend sorted them by proximidad (orden column)
                // We'll set the first non-delivered as selected if none is.
                let selected = this.pedidos.find(p => p.estado !== 'ENTREGADO');
                if (selected) {
                    this.pedidos.forEach(p => p.ui_estado = p.estado);
                    selected.ui_estado = 'SELECCIONADO';
                } else {
                    this.pedidos.forEach(p => p.ui_estado = p.estado);
                }
            } catch (e) {
                console.error(e);
            }
            setTimeout(() => {
                this.map = L.map('mapa-ruta-full').setView([-1.249, -78.616], 14);
                L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png').addTo(this.map);
                
                this.renderMarkers();
                
                if(window.gpsTracker) window.gpsTracker.startTracking();
            }, 100);
        },
        
        renderMarkers() {
            this.markers.forEach(m => this.map.removeLayer(m));
            this.markers = [];
            
            const bounds = [];
            this.pedidos.forEach((p, idx) => {
                if (p.lat && p.lng) {
                    const iconUrl = p.ui_estado === 'ENTREGADO' ? 'https://raw.githubusercontent.com/pointhi/leaflet-color-markers/master/img/marker-icon-grey.png' :
                                    (p.ui_estado === 'SELECCIONADO' ? 'https://raw.githubusercontent.com/pointhi/leaflet-color-markers/master/img/marker-icon-red.png' :
                                    'https://raw.githubusercontent.com/pointhi/leaflet-color-markers/master/img/marker-icon-blue.png');
                                    
                    const icon = L.icon({ iconUrl, shadowUrl: 'https://cdnjs.cloudflare.com/ajax/libs/leaflet/0.7.7/images/marker-shadow.png', iconSize: [25, 41], iconAnchor: [12, 41], popupAnchor: [1, -34], shadowSize: [41, 41] });
                    const marker = L.marker([p.lat, p.lng], {icon}).addTo(this.map);
                    
                    marker.bindPopup(`<b>${idx+1}. ${p.cliente}</b><br>${p.direccion}`);
                    
                    marker.on('click', () => {
                        this.seleccionar(p.id);
                    });
                    
                    this.markers.push(marker);
                    bounds.push([p.lat, p.lng]);
                }
            });
            
            if (bounds.length > 0) {
                this.map.fitBounds(bounds, { padding: [50, 50] });
            }
        },

        seleccionar(id) {
            this.pedidos.forEach(p => { 
                if (p.ui_estado === 'SELECCIONADO') p.ui_estado = p.estado; 
            });
            const p = this.pedidos.find(x => x.id === id);
            p.ui_estado = 'SELECCIONADO';
            
            this.renderMarkers();
            if (p.lat && p.lng) {
                this.map.flyTo([p.lat, p.lng], 16);
            }
        },

        navegar(p) {
            // URL Scheme for Google Maps (Deep link to app or fallback to web)
            const isIOS = /iPad|iPhone|iPod/.test(navigator.userAgent) && !window.MSStream;
            if (isIOS) {
                window.open(`comgooglemaps://?daddr=${p.lat},${p.lng}&directionsmode=driving`, '_system');
            } else if (/Android/.test(navigator.userAgent)) {
                window.open(`google.navigation:q=${p.lat},${p.lng}&mode=d`, '_system');
            } else {
                window.open(`https://www.google.com/maps/dir/?api=1&destination=${p.lat},${p.lng}`);
            }
        }
    }));
});
</script>
@endsection
