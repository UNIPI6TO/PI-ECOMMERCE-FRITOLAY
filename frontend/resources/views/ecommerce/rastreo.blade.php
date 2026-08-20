@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto py-8 px-4" x-data="rastreo('{{ $pedidoId }}')">
    <h1 class="text-3xl font-bold mb-4">Rastreo de Pedido <span x-text="pedidoId"></span></h1>
    
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <div class="md:col-span-2">
            <div id="mapa-rastreo" style="height: 500px;" class="rounded-lg shadow border z-0"></div>
        </div>
        <div>
            <div class="bg-white p-6 rounded-lg shadow mb-6">
                <h3 class="font-bold text-lg mb-4">Estado del Pedido</h3>
                <div class="flex items-center space-x-3 text-blue-600 font-semibold mb-2">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path></svg>
                    <span>En Camino</span>
                </div>
                <p class="text-sm text-gray-600 mb-4">El camión está en ruta hacia tu dirección.</p>
                <h4 class="font-semibold mb-2">Productos:</h4>
                <ul class="text-sm space-y-1 text-gray-700">
                    <li>2x Papas Fritas Clasicas</li>
                    <li>1x Doritos Queso</li>
                </ul>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('alpine:init', () => {
    Alpine.data('rastreo', (id) => ({
        pedidoId: id,
        map: null,
        marker: null,
        camionId: 'CAM-123',

        init() {
            setTimeout(() => {
                this.map = L.map('mapa-rastreo').setView([-1.249, -78.616], 14);
                L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png').addTo(this.map);
                
                const truckIcon = L.icon({
                    iconUrl: 'https://cdn-icons-png.flaticon.com/512/2765/2765101.png',
                    iconSize: [40, 40]
                });
                this.marker = L.marker([-1.249, -78.616], {icon: truckIcon}).addTo(this.map);

                // Simulacion Firestore onSnapshot
                setInterval(() => {
                    const current = this.marker.getLatLng();
                    const newLat = current.lat + (Math.random() - 0.5) * 0.001;
                    const newLng = current.lng + (Math.random() - 0.5) * 0.001;
                    this.marker.setLatLng([newLat, newLng]);
                    this.map.panTo([newLat, newLng]);
                }, 3000);
            }, 100);
        }
    }));
});
</script>
@endsection
