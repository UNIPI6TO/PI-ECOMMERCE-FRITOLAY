@extends('layouts.app')

@section('title', 'Trazado de Ruta y Ubicación - Camión #' . $idCamion)

@section('content')
<div class="max-w-7xl mx-auto py-6 px-4 sm:px-6" x-data="flotaMapaDetalleApp({{ $idCamion }})">
    
    <!-- Botón Regresar y Encabezado con Filtros Datadog -->
    <div class="mb-6 bg-white rounded-2xl shadow-xs border border-gray-100 p-6">
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div>
                <a href="/admin/flota/ubicaciones" class="inline-flex items-center gap-1.5 text-xs font-extrabold text-gray-500 hover:text-slate-900 transition-colors mb-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                    Volver a Ubicaciones de Flota
                </a>
                <h1 class="text-2xl font-black text-slate-900 tracking-tight flex items-center gap-3">
                    <span>Camión <span class="text-blue-600" x-text="camion ? `#${camion.id} (${camion.placa})` : '#{{ $idCamion }}'"></span></span>
                    <span class="px-2.5 py-0.5 rounded-full text-xs font-extrabold bg-blue-50 text-blue-700 border border-blue-100 uppercase" x-text="camion ? camion.estado : 'Cargando...'"></span>
                </h1>
                <p class="text-xs font-semibold text-gray-500 mt-1" x-text="camion ? `Conductor asignado: ${camion.chofer?.nombre || camion.chofer_nombre || 'Sin asignación'}` : ''"></p>
            </div>

            <!-- Filtro de Fechas (Desde, Hasta y Atajos Rápidos) -->
            <div class="bg-white p-2 sm:p-2.5 rounded-2xl border border-gray-200/80 shadow-xs flex flex-wrap items-center gap-3 text-xs">
                
                <!-- Input Fecha DESDE -->
                <div class="flex items-center gap-2">
                    <span class="font-black text-slate-500 uppercase text-[11px] tracking-wide">DESDE:</span>
                    <input type="date" 
                           x-model="fechaDesdeInput" 
                           @change="aplicarRangoExactoFechas()"
                           class="px-3 py-1.5 bg-gray-50 border border-gray-200 rounded-xl text-xs font-extrabold text-slate-800 focus:ring-2 focus:ring-slate-900 focus:bg-white outline-none cursor-pointer">
                </div>

                <!-- Input Fecha HASTA -->
                <div class="flex items-center gap-2">
                    <span class="font-black text-slate-500 uppercase text-[11px] tracking-wide">HASTA:</span>
                    <input type="date" 
                           x-model="fechaHastaInput" 
                           @change="aplicarRangoExactoFechas()"
                           class="px-3 py-1.5 bg-gray-50 border border-gray-200 rounded-xl text-xs font-extrabold text-slate-800 focus:ring-2 focus:ring-slate-900 focus:bg-white outline-none cursor-pointer">
                </div>

                <!-- Atajos de Selección Rápida (Último Mes, Última Semana, Hoy) -->
                <div class="flex items-center gap-1 bg-gray-100 p-1 rounded-xl">
                    <button @click="seleccionarAtajo('MES')" 
                            :class="filterLabel === 'Último Mes' ? 'bg-white text-slate-900 font-black shadow-2xs' : 'text-gray-600 hover:text-slate-900 font-bold'"
                            class="px-3 py-1 rounded-lg text-xs transition-all">
                        Último Mes
                    </button>
                    <button @click="seleccionarAtajo('SEMANA')" 
                            :class="filterLabel === 'Última Semana' ? 'bg-white text-slate-900 font-black shadow-2xs' : 'text-gray-600 hover:text-slate-900 font-bold'"
                            class="px-3 py-1 rounded-lg text-xs transition-all">
                        Última Semana
                    </button>
                    <button @click="seleccionarAtajo('HOY')" 
                            :class="filterLabel === 'Hoy' ? 'bg-white text-slate-900 font-black shadow-2xs' : 'text-gray-600 hover:text-slate-900 font-bold'"
                            class="px-3 py-1 rounded-lg text-xs transition-all">
                        Hoy
                    </button>
                </div>
            </div>
        </div>

        <!-- Rango de Fechas Activo e Información -->
        <div class="mt-3 pt-3 border-t border-gray-100 flex flex-wrap items-center justify-between text-xs font-semibold text-gray-500 gap-2">
            <div class="flex items-center gap-2">
                <span class="w-2.5 h-2.5 rounded-full bg-emerald-500 animate-pulse"></span>
                <span>Filtro activo: <strong class="text-slate-900" x-text="filterLabel"></strong></span>
                <span class="text-gray-300">•</span>
                <span>Período: <strong class="text-slate-800" x-text="`${fechaDesdeInput} al ${fechaHastaInput}`"></strong></span>
            </div>
            <div class="text-blue-600 font-extrabold" x-text="`${puntosFiltrados.length} puntos trazados en mapa`"></div>
        </div>
    </div>

    <!-- Panel del Mapa y Métricas del Trazado -->
    <div class="grid grid-cols-1 lg:grid-cols-4 gap-6">
        <!-- Mapa Leaflet Interactivo con Polyline -->
        <div class="lg:col-span-3 bg-white rounded-2xl shadow-xs border border-gray-100 overflow-hidden relative" style="min-height: 520px;">
            <div id="mapaFlotaDetalle" class="w-full h-full min-h-[520px] z-10"></div>
            
            <template x-if="cargando">
                <div class="absolute inset-0 bg-white/80 backdrop-blur-xs flex items-center justify-center z-20">
                    <div class="inline-flex items-center gap-2 font-bold text-xs text-slate-800">
                        <svg class="animate-spin h-5 w-5 text-blue-600" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                        <span>Cargando trazado histórico desde Firestore...</span>
                    </div>
                </div>
            </template>
        </div>

        <!-- Panel Lateral de Resumen de Telemetría -->
        <div class="space-y-4">
            <!-- Card Posición Actual -->
            <div class="bg-slate-900 text-white rounded-2xl p-5 shadow-xs border border-slate-800">
                <h3 class="text-xs font-black uppercase tracking-wider text-amber-400 mb-2 flex items-center gap-2">
                    <svg class="w-4 h-4 text-emerald-400 animate-bounce" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                    Última Posición Conocida
                </h3>
                
                <template x-if="ultimaUbicacion">
                    <div class="space-y-2 text-xs">
                        <div class="flex justify-between border-b border-slate-800 pb-1.5">
                            <span class="text-slate-400">Latitud:</span>
                            <span class="font-mono font-bold text-white" x-text="ultimaUbicacion.lat"></span>
                        </div>
                        <div class="flex justify-between border-b border-slate-800 pb-1.5">
                            <span class="text-slate-400">Longitud:</span>
                            <span class="font-mono font-bold text-white" x-text="ultimaUbicacion.lng"></span>
                        </div>
                        <div class="flex justify-between pt-1">
                            <span class="text-slate-400">Timestamp:</span>
                            <span class="font-bold text-emerald-400" x-text="formatearFecha(ultimaUbicacion.timestamp)"></span>
                        </div>
                    </div>
                </template>

                <template x-if="!ultimaUbicacion">
                    <p class="text-xs text-slate-400 font-semibold italic">No hay coordenadas registradas para este camión.</p>
                </template>
            </div>

            <!-- Card Estadísticas del Trazado -->
            <div class="bg-white rounded-2xl p-5 border border-gray-100 shadow-xs space-y-3">
                <h3 class="text-xs font-black uppercase tracking-wider text-gray-500">Métricas de la Ruta</h3>
                
                <div class="flex items-center justify-between border-b border-gray-100 pb-2">
                    <span class="text-xs font-bold text-gray-500">Total Coordenadas:</span>
                    <span class="text-sm font-black text-slate-900" x-text="puntosFiltrados.length"></span>
                </div>
                <div class="flex items-center justify-between border-b border-gray-100 pb-2">
                    <span class="text-xs font-bold text-gray-500">Distancia Estimada:</span>
                    <span class="text-sm font-black text-blue-600" x-text="`${distanciaTotalKm.toFixed(2)} km`"></span>
                </div>
                <div class="flex items-center justify-between">
                    <span class="text-xs font-bold text-gray-500">Identificador Firestore:</span>
                    <span class="text-xs font-mono font-extrabold text-gray-700" x-text="`camion_${camionId}`"></span>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('alpine:init', () => {
    Alpine.data('flotaMapaDetalleApp', (camionId) => ({
        camionId: camionId,
        camion: null,
        cargando: true,
        fechaDesdeInput: new Date().toISOString().split('T')[0],
        fechaHastaInput: new Date().toISOString().split('T')[0],
        filterLabel: 'Hoy',
        fechaInicio: new Date(),
        fechaFin: new Date(),
        historialPuntos: [],
        puntosFiltrados: [],
        ultimaUbicacion: null,
        distanciaTotalKm: 0,
        map: null,
        polyLineLayer: null,
        markersLayerGroup: null,

        async init() {
            try {
                // Cargar datos de camión desde MySQL API
                const res = await window.api(`/api/camiones`);
                const list = Array.isArray(res) ? res : (res.data || []);
                this.camion = list.find(c => Number(c.id) === Number(this.camionId)) || null;

                // Inicializar fechas a 'Hoy'
                this.seleccionarAtajo('HOY');

                // Cargar historial de Firestore vinculando clave idCamion
                await this.cargarHistorialFirestore();

            } catch (err) {
                window.toast(err.message || 'Error al cargar datos del camión', 'error');
            } finally {
                this.cargando = false;
                this.$nextTick(() => this.renderizarMapa());
            }
        },

        seleccionarAtajo(tipo) {
            const hoy = new Date();
            const fin = new Date(hoy);
            fin.setHours(23, 59, 59, 999);

            let inicio = new Date(hoy);
            inicio.setHours(0, 0, 0, 0);

            if (tipo === 'MES') {
                inicio.setDate(inicio.getDate() - 30);
                this.filterLabel = 'Último Mes';
            } else if (tipo === 'SEMANA') {
                inicio.setDate(inicio.getDate() - 7);
                this.filterLabel = 'Última Semana';
            } else {
                this.filterLabel = 'Hoy';
            }

            this.fechaInicio = inicio;
            this.fechaFin = fin;

            this.fechaDesdeInput = inicio.toISOString().split('T')[0];
            this.fechaHastaInput = fin.toISOString().split('T')[0];

            this.filtrarPuntosPorFecha();
        },

        aplicarRangoExactoFechas() {
            if (!this.fechaDesdeInput || !this.fechaHastaInput) return;

            const inicio = new Date(this.fechaDesdeInput + 'T00:00:00');
            const fin = new Date(this.fechaHastaInput + 'T23:59:59');

            if (inicio > fin) {
                window.toast('La fecha inicial no puede ser mayor a la fecha final', 'warning');
                return;
            }

            this.fechaInicio = inicio;
            this.fechaFin = fin;
            this.filterLabel = 'Personalizado';

            this.filtrarPuntosPorFecha();
        },

        async cargarHistorialFirestore() {
            const docId = `camion_${this.camionId}`;
            try {
                if (window.firestoreDb && window.firestoreDoc && window.firestoreGetDoc) {
                    const docRef = window.firestoreDoc(window.firestoreDb, 'ubicaciones_camion', docId);
                    const docSnap = await window.firestoreGetDoc(docRef);

                    if (docSnap.exists()) {
                        const data = docSnap.data();
                        this.historialPuntos = Array.isArray(data.historial) ? data.historial : [];
                        this.ultimaUbicacion = data.ultima_ubicacion || (this.historialPuntos.length > 0 ? this.historialPuntos[this.historialPuntos.length - 1] : null);
                    } else {
                        this.historialPuntos = [];
                        this.ultimaUbicacion = null;
                    }
                }
            } catch (err) {
                console.warn('[Mapa Detalle] Error Firestore. Usando almacenamiento local fallback:', err.message);
                const local = JSON.parse(sessionStorage.getItem(`gps_camion_${this.camionId}`) || '[]');
                this.historialPuntos = local;
                this.ultimaUbicacion = local.length > 0 ? local[local.length - 1] : null;
            }

            this.filtrarPuntosPorFecha();
        },

        filtrarPuntosPorFecha() {
            const fStart = new Date(this.fechaInicio).getTime();
            const fEnd = new Date(this.fechaFin).getTime();

            this.puntosFiltrados = this.historialPuntos.filter(p => {
                const pTime = new Date(p.timestamp).getTime();
                return pTime >= fStart && pTime <= fEnd;
            });

            // Calcular distancia total trazada con Haversine
            let distMeters = 0;
            for (let i = 1; i < this.puntosFiltrados.length; i++) {
                const p1 = this.puntosFiltrados[i - 1];
                const p2 = this.puntosFiltrados[i];
                distMeters += this.calcularHaversine(p1.lat, p1.lng, p2.lat, p2.lng);
            }
            this.distanciaTotalKm = distMeters / 1000;

            if (this.map) {
                this.actualizarCapasMapa();
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
        },

        renderizarMapa() {
            const container = document.getElementById('mapaFlotaDetalle');
            if (!container || typeof L === 'undefined') return;

            if (this.map) {
                this.map.remove();
            }

            const defaultLat = this.ultimaUbicacion?.lat || -1.24908;
            const defaultLng = this.ultimaUbicacion?.lng || -78.61675;

            this.map = L.map('mapaFlotaDetalle').setView([defaultLat, defaultLng], 14);

            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                attribution: '&copy; OpenStreetMap contributors'
            }).addTo(this.map);

            this.markersLayerGroup = L.layerGroup().addTo(this.map);
            this.actualizarCapasMapa();
        },

        actualizarCapasMapa() {
            if (!this.map || !this.markersLayerGroup) return;

            this.markersLayerGroup.clearLayers();
            if (this.polyLineLayer) {
                this.map.removeLayer(this.polyLineLayer);
            }

            if (this.puntosFiltrados.length === 0) return;

            const latLngs = this.puntosFiltrados.map(p => [p.lat, p.lng]);

            // Trazado de Ruta con Polyline Punteada Estilizada
            this.polyLineLayer = L.polyline(latLngs, {
                color: '#2563eb',
                weight: 4,
                opacity: 0.8,
                dashArray: '8, 8',
                lineJoin: 'round'
            }).addTo(this.map);

            // Ajustar vista del mapa al trazado
            this.map.fitBounds(this.polyLineLayer.getBounds(), { padding: [40, 40] });

            // Marcadores de Puntos Históricos Intermedios
            this.puntosFiltrados.forEach((p, idx) => {
                const esUltimo = idx === this.puntosFiltrados.length - 1;
                
                if (esUltimo) {
                    // Marcador Destacado Especial para Última Posición Conocida (Icono de Camión Animado)
                    const iconCamion = L.divIcon({
                        className: 'custom-camion-pin',
                        html: `<div style="background-color: #0f172a; color: #fbbf24; width: 36px; height: 36px; border-radius: 12px; display: flex; align-items: center; justify-content: center; font-size: 18px; box-shadow: 0 4px 12px rgba(0,0,0,0.3); border: 2px solid #ffffff;">🚚</div>`,
                        iconSize: [36, 36],
                        iconAnchor: [18, 18]
                    });

                    L.marker([p.lat, p.lng], { icon: iconCamion })
                        .bindPopup(`
                            <div class="p-1 text-xs">
                                <strong class="text-blue-600 block mb-1">🚚 Última Posición Conocida</strong>
                                <div>Camión: #${this.camionId} (${this.camion?.placa || ''})</div>
                                <div>Hora: ${this.formatearFecha(p.timestamp)}</div>
                            </div>
                        `)
                        .addTo(this.markersLayerGroup);
                } else if (idx === 0) {
                    // Marcador Punto de Inicio (Verde)
                    const iconInicio = L.divIcon({
                        className: 'custom-start-pin',
                        html: `<div style="background-color: #10b981; color: white; width: 24px; height: 24px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 10px; font-weight: bold; border: 2px solid white;">A</div>`,
                        iconSize: [24, 24],
                        iconAnchor: [12, 12]
                    });
                    L.marker([p.lat, p.lng], { icon: iconInicio })
                        .bindPopup(`<div class="text-xs"><strong>Punto Inicial de Ruta</strong><br>${this.formatearFecha(p.timestamp)}</div>`)
                        .addTo(this.markersLayerGroup);
                } else {
                    // Círculos intermedios sutiles
                    L.circleMarker([p.lat, p.lng], {
                        radius: 4,
                        fillColor: '#3b82f6',
                        color: '#ffffff',
                        weight: 1,
                        opacity: 1,
                        fillOpacity: 0.8
                    }).bindPopup(`<div class="text-xs">Hora: ${this.formatearFecha(p.timestamp)}</div>`)
                    .addTo(this.markersLayerGroup);
                }
            });
        },

        formatearFecha(isoStr) {
            if (!isoStr) return 'N/A';
            const d = new Date(isoStr);
            return d.toLocaleString('es-EC', { dateStyle: 'short', timeStyle: 'short' });
        }
    }));
});
</script>
@endsection
