@extends('layouts.app')

@section('content')
<div class="min-h-[calc(100vh-4rem)] bg-slate-100 pb-28 sm:pb-8" x-data="mapaRutaMobile('{{ $guiaRutaId }}')">
    
    <!-- Header Fijo Superior Mobile -->
    <div class="bg-slate-900 text-white p-4 sticky top-16 z-30 shadow-md">
        <div class="max-w-7xl mx-auto flex items-center justify-between gap-3">
            <div class="flex items-center gap-3">
                <a :href="`/entregas`" class="p-2 rounded-xl bg-white/10 hover:bg-white/20 text-white transition-all flex items-center justify-center">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 19l-7-7 7-7"/></svg>
                </a>
                <div>
                    <h1 class="text-lg font-black tracking-tight flex items-center gap-2">
                        <span>Ruta #<span x-text="guiaId"></span></span>
                        <span class="text-xs bg-[#F5C518] text-slate-900 px-2 py-0.5 rounded-full font-extrabold" x-text="`${pedidosCompletados}/${pedidos.length}`"></span>
                    </h1>
                    <p class="text-xs text-slate-300 font-semibold" x-text="`Efectivo estimado: $${montoEfectivoTotal.toFixed(2)}`"></p>
                </div>
            </div>

            <!-- Switcher Lista / Mapa para móviles -->
            <div class="flex bg-slate-800 p-1 rounded-xl border border-slate-700 md:hidden">
                <button @click="tabActiva = 'lista'" 
                        :class="tabActiva === 'lista' ? 'bg-[#E3001B] text-white font-black shadow-2xs' : 'text-slate-400 font-bold'"
                        class="px-3 py-1.5 rounded-lg text-xs transition-all flex items-center gap-1">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16"/></svg>
                    <span>Lista</span>
                </button>
                <button @click="tabActiva = 'mapa'; setTimeout(() => { if(map) map.invalidateSize(); }, 150);" 
                        :class="tabActiva === 'mapa' ? 'bg-[#E3001B] text-white font-black shadow-2xs' : 'text-slate-400 font-bold'"
                        class="px-3 py-1.5 rounded-lg text-xs transition-all flex items-center gap-1">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7"/></svg>
                    <span>Mapa</span>
                </button>
            </div>
        </div>
    </div>

    <!-- Contenido Principal Adaptativo -->
    <div class="max-w-7xl mx-auto px-3 sm:px-6 py-4">
        <div class="grid grid-cols-1 md:grid-cols-12 gap-5 items-start">
            
            <!-- PANEL DE LISTA DE PEDIDOS (TARJETAS MOBILE-FIRST) -->
            <div class="md:col-span-5 lg:col-span-5 space-y-4" x-show="tabActiva === 'lista' || window.innerWidth >= 768">
                
                <!-- Selector de Ordenamiento por Distancia / Antigüedad -->
                <div class="bg-white p-2.5 rounded-2xl border border-slate-200/80 shadow-2xs flex items-center justify-between gap-2">
                    <span class="text-xs font-black text-slate-500 uppercase tracking-wider pl-2">Ordenar por:</span>
                    <div class="flex bg-slate-100 p-1 rounded-xl flex-1 max-w-xs">
                        <button @click="cambiarOrden('CERCANO')" 
                                :class="orden === 'CERCANO' ? 'bg-white text-slate-900 font-black shadow-2xs' : 'text-slate-500 font-bold'" 
                                class="flex-1 py-1.5 text-xs rounded-lg transition-all text-center">
                            📍 Proximidad
                        </button>
                        <button @click="cambiarOrden('ANTIGUO')" 
                                :class="orden === 'ANTIGUO' ? 'bg-white text-slate-900 font-black shadow-2xs' : 'text-slate-500 font-bold'" 
                                class="flex-1 py-1.5 text-xs rounded-lg transition-all text-center">
                            ⏱️ Antigüedad
                        </button>
                    </div>
                </div>

                <!-- Lista de Pedidos en Tarjetas Apiladas -->
                <div class="space-y-3.5">
                    <template x-for="(p, index) in pedidosList" :key="p.id">
                        <div @click="seleccionar(p.id)" 
                             class="bg-white rounded-2xl p-4 border transition-all shadow-2xs relative overflow-hidden active:scale-[0.99] touch-manipulation cursor-pointer"
                             :class="{
                                 'border-2 border-[#E3001B] bg-red-50/20 ring-4 ring-red-500/10': p.ui_estado === 'SELECCIONADO',
                                 'border-slate-200 bg-white hover:border-slate-300': p.ui_estado !== 'SELECCIONADO' && !['entregado', 'entregado_parcialmente', 'no_entregado', 'cancelado'].includes(p.estado),
                                 'border-slate-200 bg-slate-50 opacity-70': ['entregado', 'entregado_parcialmente', 'no_entregado', 'cancelado'].includes(p.estado)
                             }">
                            
                            <!-- Indicador de Orden y Estado -->
                            <div class="flex items-center justify-between gap-2 mb-1.5">
                                <div class="flex items-center gap-2">
                                    <span class="w-7 h-7 rounded-xl bg-slate-900 text-white font-black text-xs flex items-center justify-center shadow-2xs shrink-0"
                                          :class="p.ui_estado === 'SELECCIONADO' ? 'bg-[#E3001B]' : 'bg-slate-900'"
                                          x-text="index + 1"></span>
                                    <div>
                                        <h3 class="font-black text-slate-900 text-base leading-tight truncate max-w-[180px] sm:max-w-xs" x-text="p.cliente"></h3>
                                        <template x-if="p.nombre_cliente && p.nombre_cliente !== p.cliente">
                                            <p class="text-[11px] font-bold text-slate-500 flex items-center gap-1 mt-0.5">
                                                <svg class="w-3 h-3 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                                                <span x-text="`Cliente: ${p.nombre_cliente}`"></span>
                                            </p>
                                        </template>
                                    </div>
                                </div>
                                <span class="px-2.5 py-1 rounded-full text-[11px] font-black uppercase tracking-wide border shadow-2xs shrink-0" 
                                      :class="{
                                          'bg-blue-100 text-blue-800 border-blue-200': p.estado === 'en_ruta',
                                          'bg-emerald-100 text-emerald-800 border-emerald-200': p.estado === 'entregado' || p.estado === 'entregado_parcialmente',
                                          'bg-rose-100 text-rose-800 border-rose-200': p.estado === 'no_entregado' || p.estado === 'cancelado'
                                      }"
                                      x-text="p.estado === 'en_ruta' ? 'Listo / En Ruta' : (p.estado === 'no_entregado' ? 'Devuelto' : p.estado.replace('_', ' '))"></span>
                            </div>

                            <!-- Dirección y Monto -->
                            <p class="text-xs text-slate-600 font-semibold mb-3 leading-relaxed flex items-start gap-1.5">
                                <svg class="w-4 h-4 text-slate-400 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                                <span x-text="p.direccion"></span>
                            </p>
                            
                            <div class="p-2.5 rounded-xl bg-slate-50 border border-slate-100 mb-3 text-xs space-y-2">
                                <div class="flex items-center justify-between">
                                    <div>
                                        <span class="text-slate-400 font-bold block text-[10px] uppercase">Forma de Pago</span>
                                        <span class="font-extrabold text-slate-800" x-text="p.metodo_pago ? p.metodo_pago.toUpperCase() : 'EFECTIVO'"></span>
                                    </div>
                                    <div class="text-right">
                                        <span class="text-slate-400 font-bold block text-[10px] uppercase" x-text="['entregado', 'entregado_parcialmente'].includes(p.estado) ? 'Monto Cobrado' : (p.estado === 'no_entregado' ? 'Devuelto' : 'Monto a Cobrar')"></span>
                                        <span class="font-black text-sm" 
                                              :class="['entregado', 'entregado_parcialmente'].includes(p.estado) ? 'text-emerald-600' : (p.estado === 'no_entregado' ? 'text-rose-600' : 'text-slate-900')" 
                                              x-text="formatMoney(['entregado', 'entregado_parcialmente'].includes(p.estado) ? (p.valor_entrega !== undefined && p.valor_entrega !== null ? p.valor_entrega : p.total) : (p.estado === 'no_entregado' ? 0 : p.total))"></span>
                                    </div>
                                </div>

                                <!-- Detalle de Pedido Original y Devolución Parcial si aplica -->
                                <template x-if="p.estado === 'entregado_parcialmente' || (p.valor_entrega !== null && p.valor_entrega !== undefined && (parseFloat(p.total) - parseFloat(p.valor_entrega)) > 0.01)">
                                    <div class="pt-2 border-t border-slate-200/80 space-y-1 text-[11px]">
                                        <div class="flex items-center justify-between text-slate-500 font-medium">
                                            <span>Pedido Original (Factura):</span>
                                            <span class="font-bold text-slate-700 line-through" x-text="formatMoney(p.total)"></span>
                                        </div>
                                        <div class="flex items-center justify-between text-rose-700 font-extrabold">
                                            <span class="flex items-center gap-1">
                                                <svg class="w-3.5 h-3.5 text-rose-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 15v-1a4 4 0 00-4-4H4m0 0l4 4m-4-4l4-4"/></svg>
                                                Devolución (N/C SRI):
                                            </span>
                                            <span class="font-black text-rose-600" x-text="`-${formatMoney(parseFloat(p.total) - parseFloat(p.valor_entrega || 0))}`"></span>
                                        </div>
                                    </div>
                                </template>
                            </div>

                            <!-- Acciones Rápidas Directas (Touch Targets de 48px) -->
                            <div class="grid grid-cols-2 gap-2" x-show="!['entregado', 'entregado_parcialmente', 'no_entregado', 'cancelado'].includes(p.estado)">
                                <!-- Botón Llamar -->
                                <template x-if="p.telefono">
                                    <a :href="`tel:${p.telefono}`" @click.stop 
                                       class="h-12 bg-slate-100 hover:bg-slate-200 text-slate-800 rounded-xl font-bold text-xs flex items-center justify-center gap-1.5 transition-all border border-slate-200 active:bg-slate-300">
                                        <svg class="w-4 h-4 text-slate-700" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/></svg>
                                        <span>Llamar</span>
                                    </a>
                                </template>
                                <!-- Navegar GPS (Maps / Waze) -->
                                <button @click.stop="navegar(p)" 
                                        class="h-12 bg-blue-50 hover:bg-blue-100 text-blue-700 rounded-xl font-bold text-xs flex items-center justify-center gap-1.5 transition-all border border-blue-200 active:bg-blue-200"
                                        :class="!p.telefono ? 'col-span-2' : ''">
                                    <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                                    <span>Navegar GPS</span>
                                </button>
                                
                                <!-- Entregar Principal -->
                                <a :href="`/entregas/entregar/${p.id}?guia=${guiaId}`" @click.stop 
                                   class="col-span-2 h-12 bg-emerald-600 hover:bg-emerald-700 active:bg-emerald-800 text-white font-extrabold text-sm rounded-xl flex items-center justify-center gap-2 shadow-md transition-all">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                                    <span>Procesar Entrega / Cobro</span>
                                </a>

                                <!-- Devolver Todo -->
                                <button @click.stop="abrirModalDevolverTodo(p)" 
                                        class="col-span-2 h-11 bg-rose-50 hover:bg-rose-100 active:bg-rose-200 text-rose-700 rounded-xl font-bold text-xs flex items-center justify-center gap-1.5 transition-all border border-rose-200">
                                    <svg class="w-4 h-4 text-rose-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                                    <span>Devolución Total</span>
                                </button>
                            </div>
                        </div>
                    </template>
                </div>
            </div>

            <!-- PANEL DE MAPA INTERACTIVO FULL (RESPONSIVO Y ADAPTATIVO) -->
            <div class="md:col-span-7 lg:col-span-7 h-[calc(100vh-14rem)] md:h-[calc(100vh-8rem)] bg-white rounded-3xl border border-slate-200 shadow-sm relative overflow-hidden sticky top-36 md:top-20"
                 x-show="tabActiva === 'mapa' || window.innerWidth >= 768">
                <div id="mapa-ruta-full" class="absolute inset-0 z-0"></div>
            </div>

        </div>
    </div>

    <!-- BOTTOM BAR FIJO DE NAVEGACIÓN Y ACCIÓN PRINCIPAL (ZONA DEL PULGAR) -->
    <div class="fixed bottom-0 left-0 right-0 bg-slate-900/95 backdrop-blur-md text-white p-3 border-t border-slate-800 z-40 shadow-2xl">
        <div class="max-w-7xl mx-auto flex items-center justify-between gap-3">
            <div class="hidden sm:block text-xs font-semibold text-slate-300">
                <span>Avance: </span>
                <span class="font-black text-white" x-text="`${pedidosCompletados} de ${pedidos.length} completados`"></span>
            </div>
            <a :href="`/entregas/cierre-caja?guia=${guiaId}`" 
               class="w-full sm:w-auto px-8 h-14 bg-[#E3001B] hover:bg-red-700 active:scale-95 text-white font-black text-base rounded-2xl flex items-center justify-center gap-2 shadow-lg transition-all border border-red-500">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                <span>Finalizar y Cerrar Caja de Ruta</span>
            </a>
        </div>
    </div>

</div>

<script>
document.addEventListener('alpine:init', () => {
    Alpine.data('mapaRutaMobile', (id) => ({
        guiaId: id,
        orden: 'CERCANO',
        tabActiva: 'lista', // 'lista' o 'mapa'
        pedidos: [],
        map: null,
        markers: [],

        get pedidosList() {
            return this.pedidos;
        },

        get pedidosCompletados() {
            return this.pedidos.filter(p => ['entregado', 'entregado_parcialmente', 'no_entregado', 'cancelado'].includes(p.estado)).length;
        },

        get montoEfectivoTotal() {
            return this.pedidos.reduce((sum, p) => {
                if (p.metodo_pago !== 'efectivo' && p.metodo_pago) return sum;
                if (p.estado === 'no_entregado' || p.estado === 'cancelado') return sum;
                const val = (['entregado', 'entregado_parcialmente'].includes(p.estado) && p.valor_entrega !== undefined && p.valor_entrega !== null) 
                    ? parseFloat(p.valor_entrega) 
                    : parseFloat(p.total);
                return sum + (val || 0);
            }, 0);
        },

        async init() {
            try {
                this.pedidos = await window.api(`/api/guias-ruta/${this.guiaId}/pedidos`);
                let selected = this.pedidos.find(p => !['entregado', 'entregado_parcialmente', 'no_entregado', 'cancelado'].includes(p.estado));
                this.pedidos.forEach(p => p.ui_estado = p.estado);
                if (selected) {
                    selected.ui_estado = 'SELECCIONADO';
                }
            } catch (e) {
                console.error("Error al cargar pedidos de ruta:", e);
            }

            setTimeout(() => {
                this.initMap();
                if (window.gpsTracker) window.gpsTracker.startTracking();
            }, 150);
        },

        initMap() {
            if (this.map) return;
            const container = document.getElementById('mapa-ruta-full');
            if (!container) return;

            this.map = L.map('mapa-ruta-full', { zoomControl: false }).setView([-1.249, -78.616], 14);
            L.control.zoom({ position: 'topright' }).addTo(this.map);

            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                maxZoom: 19,
                attribution: '&copy; OpenStreetMap'
            }).addTo(this.map);

            this.renderMarkers();
        },

        renderMarkers() {
            if (!this.map) return;
            this.markers.forEach(m => this.map.removeLayer(m));
            this.markers = [];

            const bounds = [];
            this.pedidos.forEach((p, idx) => {
                if (p.lat && p.lng) {
                    const isDone = ['entregado', 'entregado_parcialmente'].includes(p.estado);
                    const isFail = ['no_entregado', 'cancelado'].includes(p.estado);
                    const isSelected = p.ui_estado === 'SELECCIONADO';

                    const iconUrl = isDone ? 'https://raw.githubusercontent.com/pointhi/leaflet-color-markers/master/img/marker-icon-grey.png' :
                                   (isFail ? 'https://raw.githubusercontent.com/pointhi/leaflet-color-markers/master/img/marker-icon-black.png' :
                                   (isSelected ? 'https://raw.githubusercontent.com/pointhi/leaflet-color-markers/master/img/marker-icon-red.png' :
                                   'https://raw.githubusercontent.com/pointhi/leaflet-color-markers/master/img/marker-icon-blue.png'));

                    const icon = L.icon({
                        iconUrl,
                        shadowUrl: 'https://cdnjs.cloudflare.com/ajax/libs/leaflet/0.7.7/images/marker-shadow.png',
                        iconSize: [25, 41],
                        iconAnchor: [12, 41],
                        popupAnchor: [1, -34],
                        shadowSize: [41, 41]
                    });

                    const marker = L.marker([p.lat, p.lng], { icon }).addTo(this.map);
                    marker.bindPopup(`<div class="p-1 font-sans"><strong class="text-sm">${idx + 1}. ${p.cliente}</strong><br><span class="text-xs text-gray-600">${p.direccion}</span><br><strong class="text-xs text-[#E3001B]">$${parseFloat(p.total).toFixed(2)}</strong></div>`);
                    
                    marker.on('click', () => {
                        this.seleccionar(p.id);
                    });

                    this.markers.push(marker);
                    bounds.push([p.lat, p.lng]);
                }
            });

            if (bounds.length > 0) {
                this.map.fitBounds(bounds, { padding: [40, 40] });
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
                if (p.lat && p.lng && this.map) {
                    this.map.flyTo([p.lat, p.lng], 16);
                }
            }
        },

        cambiarOrden(tipo) {
            this.orden = tipo;
            if (tipo === 'ANTIGUO') {
                this.pedidos.sort((a, b) => new Date(a.created_at || 0) - new Date(b.created_at || 0));
            } else {
                this.pedidos.sort((a, b) => (a.orden || 0) - (b.orden || 0));
            }
        },

        navegar(p) {
            if (!p) return;
            const lat = p.lat;
            const lng = p.lng;
            const address = encodeURIComponent(p.direccion || p.cliente || '');

            if (!lat || !lng) {
                if (address) {
                    window.open(`https://www.google.com/maps/search/?api=1&query=${address}`, '_blank');
                } else {
                    window.toast('Ubicación o dirección no registrada', 'warning', 'bottom');
                }
                return;
            }

            const isIOS = /iPad|iPhone|iPod/.test(navigator.userAgent) && !window.MSStream;
            const isAndroid = /Android/.test(navigator.userAgent);

            if (isIOS) {
                window.location.href = `https://maps.apple.com/?daddr=${lat},${lng}`;
            } else if (isAndroid) {
                window.location.href = `geo:${lat},${lng}?q=${lat},${lng}(${encodeURIComponent(p.cliente || 'Entrega')})`;
            } else {
                window.open(`https://www.google.com/maps/dir/?api=1&destination=${lat},${lng}`, '_blank');
            }
        },

        async abrirModalDevolverTodo(p) {
            const { value: formValues } = await Swal.fire({
                title: `Devolución Total #${p.id}`,
                html: `
                    <p class="text-xs text-slate-500 mb-3 text-left font-semibold">Seleccione el motivo de devolución para <strong>${p.cliente}</strong>:</p>
                    
                    <!-- Chips Táctiles Grandes de Selección -->
                    <div class="grid grid-cols-1 gap-2 mb-3 text-left">
                        <label class="flex items-center gap-3 p-3 rounded-xl border border-slate-200 hover:bg-red-50 hover:border-red-300 cursor-pointer transition-all active:scale-[0.98]">
                            <input type="radio" name="swal_motivo" value="Local Cerrado" checked class="w-4 h-4 text-[#E3001B]">
                            <span class="text-xs font-bold text-slate-800">🏬 Local Cerrado</span>
                        </label>
                        <label class="flex items-center gap-3 p-3 rounded-xl border border-slate-200 hover:bg-red-50 hover:border-red-300 cursor-pointer transition-all active:scale-[0.98]">
                            <input type="radio" name="swal_motivo" value="Dirección Inválida" class="w-4 h-4 text-[#E3001B]">
                            <span class="text-xs font-bold text-slate-800">📍 Dirección Inválida / No Ubicada</span>
                        </label>
                        <label class="flex items-center gap-3 p-3 rounded-xl border border-slate-200 hover:bg-red-50 hover:border-red-300 cursor-pointer transition-all active:scale-[0.98]">
                            <input type="radio" name="swal_motivo" value="Cliente Ausente" class="w-4 h-4 text-[#E3001B]">
                            <span class="text-xs font-bold text-slate-800">👤 Cliente Ausente / No Atiende</span>
                        </label>
                        <label class="flex items-center gap-3 p-3 rounded-xl border border-slate-200 hover:bg-red-50 hover:border-red-300 cursor-pointer transition-all active:scale-[0.98]">
                            <input type="radio" name="swal_motivo" value="Pedido Cancelado por Cliente" class="w-4 h-4 text-[#E3001B]">
                            <span class="text-xs font-bold text-slate-800">❌ Cancelado por Cliente</span>
                        </label>
                        <label class="flex items-center gap-3 p-3 rounded-xl border border-slate-200 hover:bg-red-50 hover:border-red-300 cursor-pointer transition-all active:scale-[0.98]">
                            <input type="radio" name="swal_motivo" value="Mercadería Rechazada" class="w-4 h-4 text-[#E3001B]">
                            <span class="text-xs font-bold text-slate-800">📦 Mercadería Rechazada / Dañada</span>
                        </label>
                    </div>
                    <textarea id="swal_observaciones" class="w-full border border-slate-300 rounded-xl p-3 text-xs font-medium focus:ring-2 focus:ring-red-200 focus:outline-none" placeholder="Observaciones adicionales (opcional)..." rows="2"></textarea>
                `,
                showCancelButton: true,
                confirmButtonText: 'Confirmar Devolución',
                cancelButtonText: 'Cancelar',
                confirmButtonColor: '#E3001B',
                customClass: {
                    popup: 'rounded-3xl p-5',
                    confirmButton: 'rounded-xl py-3 px-6 text-xs font-black',
                    cancelButton: 'rounded-xl py-3 px-6 text-xs font-bold'
                },
                preConfirm: () => {
                    const selectedRadio = document.querySelector('input[name="swal_motivo"]:checked');
                    const motivo = selectedRadio ? selectedRadio.value : 'Sin motivo';
                    const obs = document.getElementById('swal_observaciones').value.trim();
                    return {
                        motivo: obs ? `${motivo} - ${obs}` : motivo
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

                    window.toast(`Devolución registrada (#${p.id})`, 'success', 'bottom');
                    this.pedidos = await window.api(`/api/guias-ruta/${this.guiaId}/pedidos`);
                    let selected = this.pedidos.find(x => !['entregado', 'entregado_parcialmente', 'no_entregado', 'cancelado'].includes(x.estado));
                    this.pedidos.forEach(item => item.ui_estado = item.estado);
                    if (selected) selected.ui_estado = 'SELECCIONADO';
                    this.renderMarkers();
                } catch (e) {
                    console.error(e);
                    window.toast(e.message || 'Error al procesar devolución', 'error', 'bottom');
                }
            }
        }
    }));
});
</script>
@endsection

