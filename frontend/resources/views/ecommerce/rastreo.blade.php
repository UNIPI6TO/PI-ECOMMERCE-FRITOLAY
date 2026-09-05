@extends('layouts.app')

@section('title', 'Rastreo de Entrega en Vivo - Fritolay Ambato')

@section('content')
<div class="max-w-7xl mx-auto py-4 px-3 sm:px-6" x-data="rastreoClienteLive('{{ $pedidoId }}')">
    
    <!-- Encabezado y Estado de Conexión Mobile-First -->
    <div class="mb-4 bg-white rounded-3xl shadow-xs border border-gray-100 p-4 sm:p-6">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
            <div>
                <a href="/ecommerce/historial" class="inline-flex items-center gap-1 text-xs font-extrabold text-gray-500 hover:text-slate-900 transition-colors mb-1.5">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                    Volver a Mis Pedidos
                </a>
                <h1 class="text-xl sm:text-2xl font-black text-slate-900 tracking-tight flex items-center gap-2.5 flex-wrap">
                    <span>Rastreo en Vivo <span class="text-[#E3001B]" x-text="`#${pedidoId}`"></span></span>
                    <span class="px-3 py-1 rounded-full text-xs font-black uppercase tracking-wider bg-emerald-100 text-emerald-800 border border-emerald-200 animate-pulse">
                        🚚 En Camino
                    </span>
                </h1>
                <p class="text-xs font-semibold text-gray-500 mt-1" x-text="pedido ? `Dirección de Entrega: ${pedido.direccion}` : 'Cargando datos del pedido...'"></p>
            </div>

            <!-- Card Resumen Distancia Restante -->
            <div class="bg-slate-900 text-white p-3.5 px-5 rounded-2xl border border-slate-800 flex items-center justify-center sm:justify-end">
                <div>
                    <span class="text-[10px] font-black uppercase text-amber-400 tracking-wider block">Distancia Restante</span>
                    <span class="text-lg font-black font-mono text-white" x-text="distanciaRestanteKm !== null ? `${distanciaRestanteKm.toFixed(2)} km` : 'Calculando...'"></span>
                </div>
            </div>
        </div>
    </div>

    <!-- Layout Principal: Mapa Interactivo & Resumen del Pedido -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-5 items-start">
        
        <!-- Mapa Leaflet con Posición en Tiempo Real del Vehículo -->
        <div class="lg:col-span-8 bg-white rounded-3xl shadow-xs border border-gray-100 overflow-hidden relative" style="min-height: 480px;">
            <div id="mapaClienteRastreo" class="w-full h-full min-h-[480px] z-10"></div>
            
            <template x-if="cargando">
                <div class="absolute inset-0 bg-white/80 backdrop-blur-xs flex items-center justify-center z-20">
                    <div class="inline-flex items-center gap-2 font-bold text-xs text-slate-800">
                        <svg class="animate-spin h-5 w-5 text-[#E3001B]" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                        <span>Sincronizando con el vehículo en directo...</span>
                    </div>
                </div>
            </template>

            <!-- Banner Flotante sobre el Mapa con Estado del Vehículo -->
            <div class="absolute bottom-4 left-4 right-4 bg-white/90 backdrop-blur-md p-3 rounded-2xl border border-gray-200 shadow-lg z-20 flex items-center justify-between text-xs">
                <div class="flex items-center gap-2">
                    <span class="w-2.5 h-2.5 rounded-full bg-emerald-500 animate-ping"></span>
                    <span class="font-extrabold text-slate-900" x-text="camionPlaca ? `Camión asignado: ${camionPlaca}` : 'Vehículo Fritolay en camino'"></span>
                </div>
                <button @click="centrarUbicacionCamion()" class="px-3 py-1 bg-slate-900 text-white rounded-xl text-[11px] font-extrabold hover:bg-slate-800 transition-all">
                    Centrar Vehículo 🚚
                </button>
            </div>
        </div>

        <!-- Panel Lateral con Detalle del Pedido y Productos -->
        <div class="lg:col-span-4 space-y-4">
            
            <!-- Resumen Financiero y Pago -->
            <div class="bg-white rounded-3xl p-5 border border-gray-100 shadow-xs space-y-3">
                <h3 class="text-xs font-black uppercase tracking-wider text-slate-500 border-b border-gray-100 pb-2">Resumen de Pago</h3>
                
                <div class="flex items-center justify-between text-xs">
                    <span class="font-bold text-gray-500">Método de Pago:</span>
                    <span class="font-black text-slate-900 bg-gray-100 px-2.5 py-1 rounded-lg" x-text="pedido?.metodo_pago || 'EFECTIVO'"></span>
                </div>
                
                <div class="flex items-center justify-between text-xs pt-1">
                    <span class="font-bold text-gray-500">Monto Total a Cancelar:</span>
                    <span class="text-base font-black text-[#E3001B]" x-text="formatMoney(pedido?.total || 0)"></span>
                </div>
            </div>

            <!-- Detalle de Productos Solicitados -->
            <div class="bg-white rounded-3xl p-5 border border-gray-100 shadow-xs space-y-3">
                <div class="flex items-center justify-between border-b border-gray-100 pb-2">
                    <h3 class="text-xs font-black uppercase tracking-wider text-slate-900">Productos del Pedido</h3>
                    <span class="text-[10px] bg-red-50 text-[#E3001B] font-extrabold px-2 py-0.5 rounded-full" x-text="`${items.length} ítems`"></span>
                </div>

                <div class="max-h-64 overflow-y-auto space-y-2.5 pr-1 custom-scrollbar">
                    <template x-for="(item, idx) in items" :key="idx">
                        <div class="p-3 rounded-2xl bg-gray-50 border border-gray-100 flex items-center justify-between text-xs">
                            <div>
                                <h4 class="font-extrabold text-slate-900" x-text="item.nombre"></h4>
                                <span class="text-[10px] font-bold text-gray-500" x-text="`Cantidad: ${item.cantidad} unidades`"></span>
                            </div>
                            <span class="font-black text-slate-800" x-text="formatMoney(item.precio * item.cantidad)"></span>
                        </div>
                    </template>
                </div>
            </div>

        </div>

    </div>

</div>

<script>
document.addEventListener('alpine:init', () => {
    Alpine.data('rastreoClienteLive', (pedidoId) => ({
        pedidoId: pedidoId,
        pedido: null,
        items: [],
        camionId: null,
        camionPlaca: '',
        cargando: true,
        refreshMinutes: parseFloat(window.VITE_LOCATION_REFRESH_MINUTES || '5'),
        distanciaRestanteKm: null,
        map: null,
        camionMarker: null,
        destinoMarker: null,
        routePolyline: null,
        telemetryTimer: null,
        authGuardTimer: null,

        async init() {
            // Manipulación preventiva del historial del navegador (Bloqueo de botón Atrás)
            history.replaceState({ page: 'rastreo' }, '', window.location.href);
            window.addEventListener('popstate', (e) => {
                window.toast('El rastreo activo requiere navegación continua.', 'warning', 'bottom');
                window.location.replace('/ecommerce/historial');
            });

            // Guardián de Acceso al Backend
            const valid = await this.validarAccesoBackend();
            if (!valid) return;

            this.$nextTick(() => {
                this.renderizarMapa();
                this.iniciarLecturasFirestore();
            });
        },

        async validarAccesoBackend() {
            try {
                const res = await window.api('/api/clientes/entrega-activa');
                const data = res ? (res.data || res) : null;

                // Validación estricta: debe existir una entrega activa y coincidir el ID de pedido
                if (!data || Number(data.pedido_id) !== Number(this.pedidoId) || data.estado !== 'en_ruta') {
                    window.toast('El rastreo ya no está disponible.', 'warning', 'bottom');
                    window.location.replace('/ecommerce/historial');
                    return false;
                }

                this.pedido = data;
                this.items = data.items || [];
                this.camionId = data.camion_id;
                this.camionPlaca = data.camion_placa || '';
                return true;
            } catch (err) {
                window.toast('El rastreo ya no está disponible.', 'error', 'bottom');
                window.location.replace('/ecommerce/historial');
                return false;
            } finally {
                this.cargando = false;
            }
        },

        renderizarMapa() {
            const container = document.getElementById('mapaClienteRastreo');
            if (!container || typeof L === 'undefined') return;

            const destLat = Number(this.pedido?.lat) || -1.24908;
            const destLng = Number(this.pedido?.lng) || -78.61675;

            this.map = L.map('mapaClienteRastreo').setView([destLat, destLng], 15);

            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                attribution: '&copy; OpenStreetMap contributors'
            }).addTo(this.map);

            // Marcador de Destino (Ubicación de Entrega del Cliente)
            const iconDestino = L.divIcon({
                className: 'custom-dest-pin',
                html: `<div style="background-color: #E3001B; color: white; width: 34px; height: 34px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 16px; box-shadow: 0 4px 10px rgba(0,0,0,0.3); border: 2px solid white;">🏠</div>`,
                iconSize: [34, 34],
                iconAnchor: [17, 17]
            });

            this.destinoMarker = L.marker([destLat, destLng], { icon: iconDestino })
                .bindPopup(`<div class="p-1 text-xs"><strong>Ubicación de Entrega</strong><br>${this.pedido?.direccion || ''}</div>`)
                .addTo(this.map);
        },

        async iniciarLecturasFirestore() {
            if (!this.camionId) return;

            const consultarPosicionCamion = async () => {
                // Revalidar en cada intervalo si el pedido sigue en ruta
                const activo = await this.validarAccesoBackend();
                if (!activo) return;

                try {
                    if (window.firestoreDb && window.firestoreDoc && window.firestoreGetDoc) {
                        const docRef = window.firestoreDoc(window.firestoreDb, 'ubicaciones_camion', `camion_${this.camionId}`);
                        const docSnap = await window.firestoreGetDoc(docRef);

                        if (docSnap.exists() && docSnap.data().ultima_ubicacion) {
                            const ult = docSnap.data().ultima_ubicacion;
                            this.actualizarPosicionCamion(Number(ult.lat), Number(ult.lng));
                        }
                    }
                } catch (e) {
                    console.warn('[Rastreo Cliente] Error al leer Firestore:', e.message);
                }
            };

            // Lectura inicial inmediata
            await consultarPosicionCamion();

            // Respetar variable de entorno LOCATION_REFRESH_MINUTES para optimizar lecturas
            const intervalMs = Math.max(0.2, this.refreshMinutes) * 60 * 1000;
            this.telemetryTimer = setInterval(consultarPosicionCamion, intervalMs);
        },

        actualizarPosicionCamion(lat, lng) {
            if (!this.map || !lat || !lng) return;

            const destLat = Number(this.pedido?.lat) || -1.24908;
            const destLng = Number(this.pedido?.lng) || -78.61675;

            // Calcular distancia Haversine restante
            const distM = this.calcularHaversine(lat, lng, destLat, destLng);
            this.distanciaRestanteKm = distM / 1000;

            // Marcador Animado del Vehículo Fritolay 🚚
            const iconCamion = L.divIcon({
                className: 'custom-live-camion',
                html: `<div style="background-color: #0f172a; color: #F5C518; width: 40px; height: 40px; border-radius: 14px; display: flex; align-items: center; justify-content: center; font-size: 20px; box-shadow: 0 4px 14px rgba(0,0,0,0.35); border: 2px solid white;">🚚</div>`,
                iconSize: [40, 40],
                iconAnchor: [20, 20]
            });

            if (this.camionMarker) {
                this.camionMarker.setLatLng([lat, lng]);
            } else {
                this.camionMarker = L.marker([lat, lng], { icon: iconCamion }).addTo(this.map);
            }

            // Trazar línea directa hasta el destino
            if (this.routePolyline) {
                this.map.removeLayer(this.routePolyline);
            }
            this.routePolyline = L.polyline([[lat, lng], [destLat, destLng]], {
                color: '#10b981',
                weight: 4,
                opacity: 0.8,
                dashArray: '6, 6'
            }).addTo(this.map);

            // Ajustar encuadre del mapa
            const bounds = L.latLngBounds([[lat, lng], [destLat, destLng]]);
            this.map.fitBounds(bounds, { padding: [60, 60] });
        },

        centrarUbicacionCamion() {
            if (this.camionMarker && this.map) {
                const pos = this.camionMarker.getLatLng();
                this.map.flyTo([pos.lat, pos.lng], 17, { duration: 1.2 });
            }
        },

        calcularHaversine(lat1, lon1, lat2, lon2) {
            const R = 6371000;
            const dLat = (lat2 - lat1) * (Math.PI / 180);
            const dLon = (lon2 - lon1) * (Math.PI / 180);
            const a = Math.sin(dLat / 2) * Math.sin(dLat / 2) +
                      Math.cos(lat1 * (Math.PI / 180)) * Math.cos(lat2 * (Math.PI / 180)) *
                      Math.sin(dLon / 2) * Math.sin(dLon / 2);
            return R * 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1 - a));
        }
    }));
});
</script>
@endsection
