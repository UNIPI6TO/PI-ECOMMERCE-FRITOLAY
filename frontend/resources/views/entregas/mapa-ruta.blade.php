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
                    <div @click="seleccionar(p.id)" class="border p-3 rounded cursor-pointer transition-all hover:border-gray-400" 
                         :class="{'border-blue-500 bg-blue-50 shadow-sm': p.ui_estado === 'SELECCIONADO', 'opacity-60 bg-gray-50': ['entregado', 'entregado_parcialmente', 'no_entregado', 'cancelado'].includes(p.estado)}">
                        <div class="flex justify-between items-start mb-2">
                            <div class="font-bold text-gray-900"><span x-text="index+1"></span>. <span x-text="p.cliente"></span></div>
                            <span class="text-xs px-2 py-0.5 rounded font-semibold uppercase tracking-wider" 
                                  :class="{
                                      'bg-blue-100 text-blue-800': p.estado === 'en_ruta',
                                      'bg-green-100 text-green-800': p.estado === 'entregado' || p.estado === 'entregado_parcialmente',
                                      'bg-red-100 text-red-800': p.estado === 'no_entregado' || p.estado === 'cancelado'
                                  }"
                                  x-text="p.estado === 'en_ruta' ? 'En Camino' : (p.estado === 'no_entregado' ? 'No Entregado' : p.estado)"></span>
                        </div>
                        <div class="text-sm text-gray-600 mb-3" x-text="p.direccion"></div>
                        
                        <div class="flex space-x-2" x-show="!['entregado', 'entregado_parcialmente', 'no_entregado', 'cancelado'].includes(p.estado)">
                            <button @click.stop="abrirModalDevolverTodo(p)" class="flex-1 bg-red-600 hover:bg-red-700 text-white text-xs font-bold py-2 rounded transition-colors text-center shadow-sm">
                                Devolver Todo
                            </button>
                            <button @click.stop="navegar(p)" x-show="p.ui_estado === 'SELECCIONADO'" class="flex-1 bg-blue-600 hover:bg-blue-700 text-white text-xs font-bold py-2 rounded transition-colors text-center shadow-sm">
                                Navegar GPS
                            </button>
                            <a :href="`/entregas/entregar/${p.id}?guia=${guiaId}`" @click.stop x-show="p.ui_estado === 'SELECCIONADO'" class="flex-1 bg-green-600 hover:bg-green-700 text-white text-xs font-bold py-2 rounded text-center block transition-colors leading-loose shadow-sm">
                                Entregar
                            </a>
                        </div>
                    </div>
                </template>
            </div>
            
            <a :href="`/entregas/cierre-caja?guia=${guiaId}`" class="mt-4 w-full bg-[#E3001B] hover:bg-red-700 text-white py-3 rounded font-bold text-center block transition-colors shadow">
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
                let selected = this.pedidos.find(p => !['entregado', 'entregado_parcialmente', 'no_entregado', 'cancelado'].includes(p.estado));
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
                    const iconUrl = ['entregado', 'entregado_parcialmente'].includes(p.estado) ? 'https://raw.githubusercontent.com/pointhi/leaflet-color-markers/master/img/marker-icon-grey.png' :
                                    (p.estado === 'no_entregado' ? 'https://raw.githubusercontent.com/pointhi/leaflet-color-markers/master/img/marker-icon-black.png' :
                                    (p.ui_estado === 'SELECCIONADO' ? 'https://raw.githubusercontent.com/pointhi/leaflet-color-markers/master/img/marker-icon-red.png' :
                                    'https://raw.githubusercontent.com/pointhi/leaflet-color-markers/master/img/marker-icon-blue.png'));
                                    
                    const icon = L.icon({ iconUrl, shadowUrl: 'https://cdnjs.cloudflare.com/ajax/libs/leaflet/0.7.7/images/marker-shadow.png', iconSize: [25, 41], iconAnchor: [12, 41], popupAnchor: [1, -34], shadowSize: [41, 41] });
                    const marker = L.marker([p.lat, p.lng], {icon}).addTo(this.map);
                    
                    marker.bindPopup(`<b>${idx+1}. ${p.cliente}</b><br>${p.direccion}<br><strong>Estado: ${p.estado}</strong>`);
                    
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
            if (p) {
                p.ui_estado = 'SELECCIONADO';
                this.renderMarkers();
                if (p.lat && p.lng) {
                    this.map.flyTo([p.lat, p.lng], 16);
                }
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
        },

        async abrirModalDevolverTodo(p) {
            const { value: formValues } = await Swal.fire({
                title: `Devolver Todo - Pedido #${p.id}`,
                html: `
                    <p class="text-xs text-gray-500 mb-3 text-left">Indique el motivo por el cual no se puede entregar el pedido a <strong>${p.cliente}</strong>:</p>
                    <div class="text-left space-y-2 mb-3">
                        <label class="flex items-center gap-2 p-2 border rounded hover:bg-gray-50 cursor-pointer">
                            <input type="radio" name="swal_motivo" value="Local Cerrado" checked class="text-red-600">
                            <span class="text-sm font-semibold">🏬 Local Cerrado</span>
                        </label>
                        <label class="flex items-center gap-2 p-2 border rounded hover:bg-gray-50 cursor-pointer">
                            <input type="radio" name="swal_motivo" value="Dirección Inválida" class="text-red-600">
                            <span class="text-sm font-semibold">📍 Dirección Inválida / No Ubicada</span>
                        </label>
                        <label class="flex items-center gap-2 p-2 border rounded hover:bg-gray-50 cursor-pointer">
                            <input type="radio" name="swal_motivo" value="Cliente Ausente" class="text-red-600">
                            <span class="text-sm font-semibold">👤 Cliente Ausente / No Atiende</span>
                        </label>
                        <label class="flex items-center gap-2 p-2 border rounded hover:bg-gray-50 cursor-pointer">
                            <input type="radio" name="swal_motivo" value="Pedido Cancelado por Cliente" class="text-red-600">
                            <span class="text-sm font-semibold">❌ Pedido Cancelado por Cliente</span>
                        </label>
                        <label class="flex items-center gap-2 p-2 border rounded hover:bg-gray-50 cursor-pointer">
                            <input type="radio" name="swal_motivo" value="Mercadería Rechazada" class="text-red-600">
                            <span class="text-sm font-semibold">📦 Mercadería Rechazada / Mal Estado</span>
                        </label>
                        <label class="flex items-center gap-2 p-2 border rounded hover:bg-gray-50 cursor-pointer">
                            <input type="radio" name="swal_motivo" value="Otro" class="text-red-600">
                            <span class="text-sm font-semibold">✏️ Otro Motivo</span>
                        </label>
                    </div>
                    <textarea id="swal_observaciones" class="w-full border border-gray-300 rounded p-2 text-sm focus:ring focus:ring-red-200" placeholder="Observaciones adicionales (opcional)..." rows="2"></textarea>
                `,
                showCancelButton: true,
                confirmButtonText: 'Confirmar Devolución',
                cancelButtonText: 'Cancelar',
                confirmButtonColor: '#E3001B',
                preConfirm: () => {
                    const selectedRadio = document.querySelector('input[name="swal_motivo"]:checked');
                    const motivo = selectedRadio ? selectedRadio.value : 'Sin motivo';
                    const obs = document.getElementById('swal_observaciones').value.trim();
                    return {
                        motivo: motivo === 'Otro' ? (obs || 'Otro motivo') : (obs ? `${motivo} - ${obs}` : motivo)
                    };
                }
            });

            if (formValues && formValues.motivo) {
                try {
                    let items = p.items;
                    if (!items || !items.length) {
                        const fullPedido = await window.api(`/api/pedidos/${p.id}`);
                        const pData = fullPedido.data || fullPedido;
                        items = pData.items || [];
                    }

                    const payload = {
                        pedido_id: parseInt(p.id),
                        motivo_no_entrega: formValues.motivo,
                        items: items.map(i => ({
                            item_pedido_id: parseInt(i.id),
                            cantidad_entregada: 0,
                            cantidad_devuelta: parseInt(i.cantidad_solicitada || i.cantidad || 0),
                            motivo_devolucion: formValues.motivo,
                            estado_mercaderia: formValues.motivo.includes('Rechazada') ? 'mal_estado' : 'buen_estado'
                        }))
                    };

                    await window.api('/api/entregas', {
                        method: 'POST',
                        body: JSON.stringify(payload)
                    });

                    Swal.fire({
                        icon: 'success',
                        title: 'Devolución Registrada',
                        text: `El pedido #${p.id} fue devuelto (${formValues.motivo}).`,
                        toast: true,
                        position: 'bottom',
                        timer: 3500,
                        showConfirmButton: false
                    });

                    // Recargar lista de pedidos de la ruta
                    this.pedidos = await window.api(`/api/guias-ruta/${this.guiaId}/pedidos`);
                    let selected = this.pedidos.find(x => !['entregado', 'entregado_parcialmente', 'no_entregado', 'cancelado'].includes(x.estado));
                    this.pedidos.forEach(item => item.ui_estado = item.estado);
                    if (selected) selected.ui_estado = 'SELECCIONADO';
                    this.renderMarkers();
                } catch (e) {
                    console.error(e);
                    Swal.fire('Error', e.message || 'No se pudo procesar la devolución', 'error');
                }
            }
        }
    }));
});
</script>
@endsection
