@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto py-8 px-4" x-data="gestionRutas()">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold text-primary">Asignación de Rutas</h1>
        <div class="flex space-x-2">
            <input type="text" placeholder="Filtro (ej: last 24h)" class="border px-4 py-2 rounded w-64">
        </div>
    </div>

    <!-- Mapa -->
    <div class="bg-white p-4 rounded shadow mb-8">
        <h2 class="font-semibold mb-4">Vista Geográfica</h2>
        <div id="mapa-gestion" style="height: 400px;" class="rounded border z-0"></div>
        
        <!-- Leyenda de Mapa -->
        <div class="mt-4 p-3 border-t border-gray-200 flex flex-wrap items-center justify-center gap-6 text-sm text-gray-700">
            <div class="flex items-center gap-2">
                <div class="w-4 h-4 rounded-full bg-blue-500 shadow-sm border border-blue-700"></div>
                <span class="font-medium">Libre / Sin asignar</span>
            </div>
            <div class="flex items-center gap-2">
                <div class="w-4 h-4 rounded-full bg-green-600 shadow-sm border border-green-800"></div>
                <span class="font-medium">Asignado a Ruta</span>
            </div>
            <div class="flex items-center gap-2">
                <div class="w-4 h-4 rounded-full bg-red-600 shadow-sm border border-red-800"></div>
                <span class="font-medium">Seleccionado</span>
            </div>
        </div>
    </div>

    
    <!-- Barra de Acciones de Selección -->
    <div class="mb-4 flex flex-wrap gap-3 items-center bg-gray-50 p-4 rounded-lg shadow border border-gray-200">

        <!-- Botón Asignar Ruta: siempre visible, deshabilitado si no aplica -->
        <button
            @click="abrirAsignacionMultiple()"
            :disabled="!isSelectionFree"
            :class="isSelectionFree
                ? 'bg-blue-600 hover:bg-blue-700 text-white shadow cursor-pointer'
                : 'bg-gray-200 text-gray-400 cursor-not-allowed'"
            class="px-4 py-2 rounded-lg text-sm font-bold transition-colors flex items-center gap-2">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor"><path d="M8 16.5a1.5 1.5 0 11-3 0 1.5 1.5 0 013 0zM15 16.5a1.5 1.5 0 11-3 0 1.5 1.5 0 013 0z" /><path d="M3 4a1 1 0 00-1 1v10a1 1 0 001 1h1.05a2.5 2.5 0 014.9 0H10a1 1 0 001-1V5a1 1 0 00-1-1H3zM14 7a1 1 0 00-1 1v6.05A2.5 2.5 0 0115.95 16H17a1 1 0 001-1v-5l-4-4h-0.05z" /></svg>
            Asignar Ruta
        </button>

        <!-- Quitar Asignación: solo si hay pedidos asignados seleccionados -->
        <button
            x-show="isSelectionAssigned"
            @click="confirmarQuitarAsignacionRapida(selectedIds)"
            class="bg-red-500 hover:bg-red-600 text-white px-4 py-2 rounded-lg shadow text-sm font-bold transition-colors">
            Quitar Asignación
        </button>

        <!-- Cerrar Ruta: siempre visible por cada camión en ruta -->
        <template x-for="truck in activeRoutes" :key="truck.id">
            <button @click="cerrarRuta(truck.id, truck.placa)"
                class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg shadow text-sm font-bold transition-colors flex items-center gap-2">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" /></svg>
                Cerrar Ruta <span x-text="truck.placa"></span>
            </button>
        </template>

        <!-- Contador de selección -->
        <div class="ml-auto text-sm text-gray-600 font-medium flex items-center gap-2">
            <template x-if="selectedIds.length > 0">
                <span class="bg-blue-100 text-blue-800 px-3 py-1 rounded-full font-bold">
                    <span x-text="selectedIds.length"></span> seleccionado(s)
                </span>
            </template>
            <template x-if="selectedIds.length === 0">
                <span class="text-gray-400 italic">Selecciona pedidos de la tabla para asignar</span>
            </template>
        </div>
    </div>

    <div class="flex justify-between items-center mb-4">
        <h2 class="font-semibold text-lg">Listado de Pedidos</h2>
        <div class="flex items-center space-x-4">
            <div class="flex items-center space-x-2 text-sm border-l pl-4">
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
    </div>
    <div class="bg-white rounded shadow overflow-x-auto">
        <table class="w-full text-left">
            <thead class="bg-gray-100">
                <tr>
                    <th class="p-3 w-10 text-center"><input type="checkbox" x-model="allSelected" @change="toggleAll(); renderMarkers();" class="rounded border-gray-300 text-blue-600 focus:ring-blue-500 w-4 h-4"></th>
                    <th class="p-3 cursor-pointer hover:bg-gray-200" @click="sort('id')">ID ⇕</th>
                    <th class="p-3 cursor-pointer hover:bg-gray-200" @click="sort('cliente')">Nombre Comercial ⇕</th>
                    <th class="p-3 cursor-pointer hover:bg-gray-200" @click="sort('distancia')">Distancia ⇕</th>
                    <th class="p-3">Total</th>
                    <th class="p-3">Ubicación</th>
                    <th class="p-3 cursor-pointer hover:bg-gray-200" @click="sort('raw_fecha')">Tiempo Transcurrido ⇕</th>
                    <th class="p-3">Ruta/Camión</th>
                </tr>
            </thead>
            <tbody>
                <template x-if="paginatedPedidos.length === 0">
                    <tr><td colspan="8" class="p-4 text-center text-gray-500">No hay pedidos para mostrar</td></tr>
                </template>
                <template x-for="p in paginatedPedidos" :key="p.id">
                    <tr class="border-b hover:bg-gray-50">
                        <td class="p-3 text-center"><input type="checkbox" :value="p.id" x-model="selectedIds" @change="renderMarkers()" class="rounded border-gray-300 text-blue-600 focus:ring-blue-500 w-4 h-4"></td>
                        <td class="p-3" x-text="p.id"></td>
                        <td class="p-3 font-semibold text-gray-800" x-text="p.cliente"></td>
                        <td class="p-3 text-sm font-medium text-blue-600" x-text="(p.distancia && p.distancia !== 999999) ? p.distancia + ' km' : '-'"></td>
                        <td class="p-3 font-medium" x-text="`$${Number(p.total).toFixed(2)}`"></td>
                        <td class="p-3 cursor-help text-sm" x-init="fetchLocation(p)" :title="p.locationFull || 'Cargando...'" x-text="p.locationDisplay || 'Cargando...'"></td>
                        <td class="p-3 text-sm text-gray-500" x-text="timeAgo(p.fecha)"></td>
                        <td class="p-3">
                                <template x-if="p.camion_id">
                                    <div class="flex items-center gap-2 bg-green-50 text-green-700 px-3 py-1.5 rounded-lg text-xs font-bold shadow-sm border border-green-200 cursor-pointer hover:bg-red-50 hover:text-red-700 hover:border-red-200 transition-colors" @click="confirmarQuitarAsignacionRapida([p.id])" title="Clic para quitar asignación">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4" /></svg>
                                        🚚 <span x-text="p.camion_placa || 'Asignado'"></span>
                                    </div>
                                </template>
                                <template x-if="!p.camion_id">
                                    <span class="text-xs text-gray-400 italic">Sin asignar</span>
                                </template>
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

    <!-- Modal Asignar Ruta -->
    <div x-show="asignarModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50" style="display: none;">
        <div class="bg-white rounded-lg w-11/12 md:w-1/3 p-6 shadow-2xl relative">
            <button @click="asignarModal = false" class="absolute top-4 right-4 text-gray-500 hover:text-red-600 transition-colors">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
            </button>
            <h2 class="text-xl font-bold mb-4">Asignar a Ruta / Camión</h2>
            <p class="text-sm text-gray-600 mb-4" x-text="`Pedido #${selectedPedido?.id} - ${selectedPedido?.cliente || ''}`"></p>
            
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-1">Seleccionar Camión Disponible</label>
                <select x-model="selectedCamionId" class="w-full border-gray-300 rounded shadow-sm p-2 focus:border-primary focus:ring focus:ring-primary focus:ring-opacity-50">
                    <option value="">-- Seleccione un camión --</option>
                    <template x-for="camion in camiones" :key="camion.id">
                        <option :value="camion.id" x-text="`${camion.placa} - ${camion.descripcion} (${camion.estado})`"></option>
                    </template>
                </select>
            </div>
            
            <div class="flex justify-end space-x-3">
                <button @click="asignarModal = false" class="px-4 py-2 border border-gray-300 text-gray-600 font-medium rounded hover:bg-gray-100 transition-colors">Cancelar</button>
                <button @click="confirmarAsignacion" :disabled="!selectedCamionId" class="px-4 py-2 bg-primary text-white font-medium rounded hover:bg-red-800 transition-colors disabled:opacity-50">Asignar y Enviar</button>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('alpine:init', () => {
    Alpine.data('gestionRutas', () => ({
        filtroEstado: null,
                pedidos: [],
            selectedIds: [],
            allSelected: false,
            selectedCamionesParaCerrar: [],
            asignarModal: false,
            selectedCamionId: '',
            camiones: [],
        camiones: [],
        map: null,
        markersLayer: null,
        currentLat: null,
        currentLng: null,
        
        // Paginación
        currentPage: 1,
        perPage: 10,
        sortCol: 'distancia',
        sortAsc: true,
        
        locationQueue: [],
        isProcessingQueue: false,

        // Variables del Modal de Revisión
        revisarModal: false,
        selectedPedido: null,
        comprobanteUrl: null,
        loadingComprobante: false,
        mostrarRechazo: false,
        motivoRechazo: '',

        // Variables del Modal de Asignar Ruta
        asignarModal: false,
        selectedCamionId: '',
        
        async init() {
            // Load camiones for the assignment modal
            try {
                const camRes = await window.api('/api/camiones');
                this.camiones = Array.isArray(camRes) ? camRes : (camRes.data || []);
            } catch(e) { console.error('Error cargando camiones', e); }
            
            // Listen for popup events
            document.addEventListener('quitar-asignacion-popup', (e) => {
                this.confirmarQuitarAsignacionRapida(e.detail);
            });
            document.addEventListener('select-pin-group', (e) => {
                const ids = e.detail;
                const newSelection = new Set(this.selectedIds.map(String));
                ids.forEach(id => newSelection.add(String(id)));
                this.selectedIds = Array.from(newSelection).map(Number);
            });
            this.$watch('selectedIds', () => this.renderMarkers(), { deep: true });

            try {
                // Load Pedidos and Camiones in parallel
                const [pedidosRes, camionesRes] = await Promise.all([
                    window.api('/api/pedidos'),
                    window.api('/api/camiones')
                ]);
                
                const rawPedidos = Array.isArray(pedidosRes) ? pedidosRes : (pedidosRes.data || []);
                this.pedidos = rawPedidos.map(p => ({
                    ...p,
                    lat: p.lat ? parseFloat(p.lat) : null,
                    lng: p.lng ? parseFloat(p.lng) : null,
                    distancia: 999999, // default before geolocation
                    raw_fecha: p.raw_fecha || 0
                }));
                
                this.camiones = Array.isArray(camionesRes) ? camionesRes : (camionesRes.data || []);
                console.log('[GestionRutas] Pedidos cargados:', this.pedidos.length, 'Camiones:', this.camiones.length);
                
                this.updateCounts();
            } catch (error) {
                console.error("Error al cargar pedidos:", error);
            }

            if (navigator.geolocation) {
                navigator.geolocation.getCurrentPosition((pos) => {
                    this.currentLat = pos.coords.latitude;
                    this.currentLng = pos.coords.longitude;
                    
                    this.pedidos = this.pedidos.map(p => {
                        if (p.lat && p.lng) {
                            p.distancia = parseFloat(this.getDist(this.currentLat, this.currentLng, p.lat, p.lng));
                        }
                        return p;
                    });

                    this.renderMarkers();
                });
            }
            
            setTimeout(() => {
                this.map = L.map('mapa-gestion').setView([-1.249, -78.616], 13);
                L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png').addTo(this.map);
                this.markersLayer = L.layerGroup().addTo(this.map);
                this.renderMarkers();
            }, 100);

            this.$watch('filtroEstado', () => {
                this.currentPage = 1;
                this.renderMarkers();
            });
        },
        
        renderMarkers() {
            if(!this.markersLayer) return;
            this.markersLayer.clearLayers();
            
            // Agrupar pedidos con las mismas coordenadas
            const grouped = {};
            this.filteredPedidos.forEach(p => {
                if (p.lat && p.lng) {
                    const key = `${p.lat},${p.lng}`;
                    if (!grouped[key]) grouped[key] = [];
                    grouped[key].push(p);
                }
            });

            for (const key in grouped) {
                const pedidos = grouped[key];
                const [lat, lng] = key.split(',');
                
                let distanceHtml = '';
                if (this.currentLat && this.currentLng) {
                    const dist = this.getDist(this.currentLat, this.currentLng, lat, lng);
                    distanceHtml = `<div class="text-xs text-blue-600 font-bold mb-2">📍 a ${dist} km de tu ubicación actual</div>`;
                }

                let ordersHtml = `<div style="max-height: 150px; overflow-y: auto; padding-right: 5px;">`;
                pedidos.forEach(p => {
                    const truckStr = p.camion_id ? `<br><span style="color:#16a34a;font-weight:bold;">🚚 ${p.camion_placa || 'Asignado'}</span>` : '';
                    const removeBtn = p.camion_id ? `<button onclick="window.dispatchEvent(new CustomEvent('quitar-asignacion-popup',{detail:${p.id}}))" style="width:100%;margin-top:6px;padding:4px;font-size:11px;font-weight:bold;color:#dc2626;background:#fee2e2;border:1px solid #fca5a5;border-radius:4px;cursor:pointer;">Quitar Asignación</button>` : '';
                    ordersHtml += `
                        <div style="border-bottom: 1px solid #eee; padding-bottom: 8px; margin-bottom: 8px;">
                            <b>Pedido #${p.id}</b> <span style="font-size: 11px; color: #666;">(${this.timeAgo(p.fecha)})</span><br>
                            <b>Comercio:</b> ${p.cliente}<br>
                            <b>Contacto:</b> ${p.nombre_persona}<br>
                            <b>Total:</b> $${Number(p.total).toFixed(2)}
                            ${truckStr}
                            ${removeBtn}
                        </div>
                    `;
                });
                ordersHtml += `</div>`;

                const isAnyAssigned = pedidos.some(p => !!p.camion_id);
                const hasSelected2 = pedidos.some(p => this.selectedIds.map(String).includes(String(p.id)));
                const dynamicColor = hasSelected2 ? '#dc2626' : (isAnyAssigned ? '#16a34a' : '#3b82f6');
                const svgIcon2 = L.divIcon({
                    className: 'custom-div-icon',
                    html: `<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="${dynamicColor}" width="34px" height="34px" style="filter:drop-shadow(0px 2px 2px rgba(0,0,0,0.3));"><path d="M12 2C8.13 2 5 5.13 5 9c0 5.25 7 13 7 13s7-7.75 7-13c0-3.87-3.13-7-7-7zm0 9.5c-1.38 0-2.5-1.12-2.5-2.5s1.12-2.5 2.5-2.5 2.5 1.12 2.5 2.5-1.12 2.5-2.5 2.5z"/></svg>`,
                    iconSize: [34, 34],
                    iconAnchor: [17, 34]
                });
                const marker = L.marker([lat, lng], {icon: svgIcon2});
                
                marker.on('click', () => {
                    const ids = pedidos.map(p => p.id);
                    const newSel = new Set(this.selectedIds.map(String));
                    ids.forEach(id => newSel.add(String(id)));
                    this.selectedIds = Array.from(newSel).map(Number);
                });
                
                marker.bindPopup(`
                    <div style="min-width: 220px;">
                        <h4 style="font-weight:bold;margin-bottom:5px;font-size:14px;">${pedidos.length} pedido(s) aquí</h4>
                        ${distanceHtml}
                        ${ordersHtml}
                        <div style="font-size:11px;text-align:center;color:#666;margin-top:4px;font-style:italic;">Click = seleccionar en tabla</div>
                    </div>
                `);
                this.markersLayer.addLayer(marker);
            }
        },

        getDist(lat1, lon1, lat2, lon2) {
            const R = 6371; // radio de la Tierra en km
            const dLat = (lat2 - lat1) * Math.PI / 180;
            const dLon = (lon2 - lon1) * Math.PI / 180;
            const a = Math.sin(dLat/2) * Math.sin(dLat/2) +
                      Math.cos(lat1 * Math.PI / 180) * Math.cos(lat2 * Math.PI / 180) *
                      Math.sin(dLon/2) * Math.sin(dLon/2);
            const c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1-a));
            return (R * c).toFixed(2);
        },

        timeAgo(dateStr) {
            if (!dateStr) return 'Desconocido';
            const date = new Date(dateStr.replace(/-/g, '/'));
            const now = new Date();
            const diffSecs = Math.floor((now - date) / 1000);
            
            if (diffSecs < 60) return `${diffSecs} segs`;
            const mins = Math.floor(diffSecs / 60);
            if (mins < 60) return `${mins} mins`;
            const hours = Math.floor(mins / 60);
            if (hours < 24) return `${hours} horas`;
            const days = Math.floor(hours / 24);
            return `${days} días`;
        },

        updateCounts() {
            // No-op in gestion-rutas (no KPI cards)
        },
        

        toggleAll() {
            if (this.allSelected) {
                this.selectedIds = this.paginatedPedidos.map(p => p.id);
            } else {
                this.selectedIds = [];
            }
        },

        abrirAsignacionMultiple() {
            if (!this.isSelectionFree) {
                Swal.fire('Error', 'Selecciona solo pedidos que no estén asignados a una ruta.', 'error');
                return;
            }
            this.selectedCamionId = '';
            this.asignarModal = true;
        },

        async confirmarQuitarAsignacionRapida(ids) {
            if (!Array.isArray(ids)) {
                ids = [ids]; // para clics desde el popup del mapa
            }
            
            const result = await Swal.fire({
                title: '¿Quitar Asignación?',
                text: `¿Estás seguro de quitar la asignación a ${ids.length} pedido(s)?`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Sí, quitar',
                cancelButtonText: 'Cancelar'
            });

            if (result.isConfirmed) {
                try {
                    await window.api('/api/asignaciones', {
                        method: 'DELETE',
                        body: JSON.stringify({ pedido_ids: ids })
                    });
                    
                    Swal.fire({
                        toast: true,
                        position: 'bottom',
                        icon: 'success',
                        title: 'Asignación eliminada',
                        showConfirmButton: false,
                        timer: 3000
                    });
                    
                    this.selectedIds = [];
                    window.location.reload();
                } catch(e) {
                    Swal.fire('Error', e.message || 'Error al quitar la asignación', 'error');
                }
            }
        },

        async cerrarRuta(camionId, placa) {
            const result = await Swal.fire({
                title: `¿Cerrar ruta del camión ${placa}?`,
                text: "Los pedidos asignados pasarán a estado 'entregado'.",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Sí, cerrar ruta',
                cancelButtonText: 'Cancelar'
            });

            if (result.isConfirmed) {
                try {
                    await window.api(`/api/asignaciones/cerrar-ruta/${camionId}`, {
                        method: 'POST'
                    });
                    
                    Swal.fire({
                        toast: true,
                        position: 'bottom',
                        icon: 'success',
                        title: 'Ruta cerrada con éxito',
                        showConfirmButton: false,
                        timer: 3000
                    });
                    
                    this.selectedIds = [];
                    window.location.reload();
                } catch(e) {
                    Swal.fire('Error', e.message || 'Error al cerrar la ruta', 'error');
                }
            }
        },

        
        async fetchLocation(p) {
            if (p.locationDisplay !== undefined) return;
            p.locationDisplay = 'Cargando...';
            p.locationFull = 'Cargando...';
            if (!p.lat || !p.lng) {
                p.locationDisplay = 'Sin coords';
                p.locationFull = 'Sin coordenadas';
                return;
            }
            this.locationQueue.push(p);
            this.processLocationQueue();
        },
        
        async processLocationQueue() {
            if (this.isProcessingQueue) return;
            this.isProcessingQueue = true;
            
            while (this.locationQueue.length > 0) {
                const p = this.locationQueue.shift();
                try {
                    const res = await fetch(`https://nominatim.openstreetmap.org/reverse?format=json&lat=${p.lat}&lon=${p.lng}`, {
                        headers: { 'Accept-Language': 'es' }
                    });
                    if (res.ok) {
                        const data = await res.json();
                        const iso = data.address['ISO3166-2-lvl4'] || data.address.state || 'N/A';
                        const parroquia = data.address.suburb || data.address.village || data.address.town || data.address.neighbourhood || data.address.county || 'Desconocida';
                        
                        let full = `[${iso}] - ${parroquia}`;
                        p.locationFull = full;
                        p.locationDisplay = full.length > 15 ? full.substring(0, 15) + '...' : full;
                    } else {
                        p.locationDisplay = 'Err ' + res.status;
                        p.locationFull = 'Error HTTP ' + res.status;
                    }
                } catch(e) {
                    p.locationDisplay = 'Error';
                    p.locationFull = 'Error de conexión';
                }
                
                // Rate limit (1.5 seconds) to respect Nominatim policy
                await new Promise(r => setTimeout(r, 1500));
            }
            this.isProcessingQueue = false;
        },

        async confirmarAsignacion() {
            if(!this.selectedCamionId) return;
            if(this.selectedIds.length === 0) {
                Swal.fire('Error', 'No hay pedidos seleccionados.', 'error');
                return;
            }
            try {
                await window.api('/api/asignaciones', {
                    method: 'POST',
                    body: JSON.stringify({
                        pedido_ids: this.selectedIds,
                        camion_id: this.selectedCamionId
                    })
                });
                
                Swal.fire({
                    toast: true,
                    position: 'bottom',
                    icon: 'success',
                    title: 'Ruta asignada con éxito',
                    showConfirmButton: false,
                    timer: 3000
                });
                
                this.asignarModal = false;
                window.location.reload();
            } catch(e) {
                Swal.fire('Error', e.message || 'Error al asignar la ruta', 'error');
            }
        },

        get isSelectionFree() {
            if(this.selectedIds.length === 0) return false;
            return this.selectedIds.every(id => {
                const p = this.pedidos.find(p => String(p.id) === String(id));
                return p && !p.camion_id;
            });
        },
        
        get isSelectionAssigned() {
            if(this.selectedIds.length === 0) return false;
            return this.selectedIds.every(id => {
                const p = this.pedidos.find(p => String(p.id) === String(id));
                return p && p.camion_id;
            });
        },
        
        
        get activeRoutes() {
            const trucks = new Map();
            this.pedidos.forEach(p => {
                if (p.camion_id && p.raw_estado === 'listo_para_entregar') {
                    trucks.set(p.camion_id, { id: p.camion_id, placa: p.camion_placa || 'Desconocida' });
                }
            });
            return Array.from(trucks.values());
        },

        get selectedTrucks() {
            if(this.selectedIds.length === 0) return [];
            const trucks = new Map();
            this.selectedIds.forEach(id => {
                const p = this.pedidos.find(p => String(p.id) === String(id));
                if(p && p.camion_id) {
                    trucks.set(p.camion_id, { id: p.camion_id, placa: p.camion_placa || 'Desconocida' });
                }
            });
            return Array.from(trucks.values());
        },

        get filteredPedidos() {
            let filtered = this.pedidos;
            if(this.filtroEstado) {
                // Support filtering by raw_estado or display estado
                filtered = filtered.filter(p => p.raw_estado === this.filtroEstado || p.estado === this.filtroEstado);
            } else {
                // En gestion-rutas: mostrar pedidos pendientes de asignacion o listos para entregar (asignados pero no despachados)
                filtered = filtered.filter(p => p.raw_estado === 'en_espera_asignacion' || p.raw_estado === 'listo_para_entregar');
            }
            
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

    <!-- Modal Asignación Masiva de Ruta -->
    <div x-show="asignarModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50" style="display: none;">
        <div class="bg-white rounded-lg w-11/12 md:w-1/3 p-6 shadow-2xl relative">
            <button @click="asignarModal = false" class="absolute top-4 right-4 text-gray-500 hover:text-red-600 transition-colors">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
            </button>
            <h2 class="text-xl font-bold mb-2">Asignar a Ruta / Camión</h2>
            <p class="text-sm text-gray-500 mb-4">Asignando <strong x-text="selectedIds.length"></strong> pedido(s) seleccionado(s)</p>
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-1">Seleccionar Camión Disponible</label>
                <select x-model="selectedCamionId" class="w-full border-gray-300 rounded shadow-sm p-2 focus:border-primary focus:ring focus:ring-primary focus:ring-opacity-50">
                    <option value="">-- Seleccione un camión --</option>
                    <template x-for="camion in camiones" :key="camion.id">
                        <option :value="camion.id" x-text="`${camion.placa} - ${camion.descripcion} (${camion.estado})`"></option>
                    </template>
                </select>
            </div>
            <div class="flex justify-end space-x-3">
                <button @click="asignarModal = false" class="px-4 py-2 border border-gray-300 text-gray-600 font-medium rounded hover:bg-gray-100 transition-colors">Cancelar</button>
                <button @click="confirmarAsignacion()" :disabled="!selectedCamionId" class="px-4 py-2 bg-blue-600 text-white font-medium rounded hover:bg-blue-700 transition-colors disabled:opacity-50">
                    Confirmar Asignación
                </button>
            </div>
        </div>
    </div>

@endsection
