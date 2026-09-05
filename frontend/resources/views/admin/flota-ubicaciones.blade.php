@extends('layouts.app')

@section('title', 'Ubicaciones de Flota en Tiempo Real - Fritolay')

@section('content')
<div class="max-w-7xl mx-auto py-6 px-4 sm:px-6" x-data="flotaUbicacionesApp()">
    
    <!-- Encabezado de Sección -->
    <div class="bg-white rounded-2xl shadow-xs border border-gray-100 p-6 mb-6">
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div>
                <div class="flex items-center gap-2 mb-1">
                    <span class="px-2.5 py-0.5 rounded-full bg-blue-50 text-blue-700 border border-blue-100 font-extrabold text-[10px] uppercase tracking-wider">
                        Geolocalización y Telemetría
                    </span>
                </div>
                <h1 class="text-2xl font-black text-gray-900 tracking-tight">Monitoreo de Flota (Ubicaciones)</h1>
                <p class="text-xs font-semibold text-gray-500 mt-0.5">Listado de vehículos activos y estado de transmisión GPS en tiempo real.</p>
            </div>

            <!-- Buscador Dinámico -->
            <div class="w-full md:w-72 relative">
                <input type="text" 
                       x-model="searchTerm" 
                       placeholder="Buscar vehículo por placa o chofer..." 
                       class="w-full pl-9 pr-4 py-2 bg-gray-50/60 border border-gray-200 rounded-xl text-xs text-gray-900 font-medium placeholder-gray-400 focus:bg-white focus:ring-2 focus:ring-slate-800 outline-none transition-all">
                <svg class="w-4 h-4 text-gray-400 absolute left-3 top-2.5 pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
            </div>
        </div>
    </div>

    <!-- Indicador de Carga -->
    <template x-if="cargando">
        <div class="bg-white rounded-2xl p-12 text-center border border-gray-100 text-gray-400">
            <div class="inline-flex items-center gap-3">
                <svg class="animate-spin h-5 w-5 text-red-600" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
                <span class="text-xs font-extrabold text-gray-600">Cargando datos de geolocalización de flota...</span>
            </div>
        </div>
    </template>

    <!-- Grid de Tarjetas de Camiones -->
    <template x-if="!cargando">
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            
            <template x-if="camionesFiltrados.length === 0">
                <div class="col-span-full bg-white rounded-2xl p-12 text-center border border-gray-100 text-gray-400">
                    <svg class="w-12 h-12 mx-auto text-gray-300 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                    <p class="text-xs font-bold text-gray-500">No se encontraron camiones coincidentes.</p>
                </div>
            </template>

            <template x-for="c in camionesFiltrados" :key="c.id">
                <div class="bg-white rounded-2xl border border-gray-100 shadow-xs hover:shadow-md transition-all p-5 flex flex-col justify-between relative overflow-hidden group">
                    <div class="absolute top-0 left-0 right-0 h-1 bg-gradient-to-r from-blue-600 to-indigo-600"></div>

                    <div>
                        <!-- Header de la Tarjeta -->
                        <div class="flex items-center justify-between mb-3">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 rounded-xl bg-slate-900 text-amber-400 font-black text-base flex items-center justify-center shadow-2xs">
                                    🚚
                                </div>
                                <div>
                                    <h3 class="text-base font-black text-gray-900 group-hover:text-blue-600 transition-colors" x-text="c.placa"></h3>
                                    <p class="text-[11px] font-semibold text-gray-400" x-text="`Camión #${c.id} • ${c.descripcion || 'Sin modelo'}`"></p>
                                </div>
                            </div>
                            
                            <span class="px-2.5 py-0.5 rounded-full text-[10px] font-extrabold uppercase tracking-wider"
                                  :class="c.estado === 'ACTIVO' ? 'bg-emerald-50 text-emerald-700 border border-emerald-100' : 'bg-gray-100 text-gray-600 border border-gray-200'"
                                  x-text="c.estado">
                            </span>
                        </div>

                        <!-- Chofer y Última Conexión -->
                        <div class="bg-gray-50/80 rounded-xl p-3 mb-4 space-y-2 text-xs">
                            <div class="flex items-center justify-between">
                                <span class="font-bold text-gray-500">Conductor:</span>
                                <span class="font-extrabold text-slate-900" x-text="c.chofer_nombre || 'Sin asignación'"></span>
                            </div>
                            <div class="flex items-center justify-between">
                                <span class="font-bold text-gray-500">Puntos Registrados:</span>
                                <span class="font-extrabold text-blue-600" x-text="`${c.puntos_count || 0} coordenadas`"></span>
                            </div>
                            <div class="flex items-center justify-between">
                                <span class="font-bold text-gray-500">Último Reporte:</span>
                                <span class="font-bold text-gray-700" x-text="c.ultima_actualizacion ? formatearFecha(c.ultima_actualizacion) : 'Sin datos GPS'"></span>
                            </div>
                        </div>
                    </div>

                    <!-- Botón de Ver Mapa y Trazado de Ruta -->
                    <a :href="`/admin/flota/ubicaciones/${c.id}`" 
                       class="w-full py-2.5 px-4 bg-slate-900 hover:bg-blue-600 text-white font-bold text-xs rounded-xl shadow-xs transition-all flex items-center justify-center gap-2 cursor-pointer">
                        <svg class="w-4 h-4 text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7"/></svg>
                        <span>Ver Trazado de Ruta en Mapa</span>
                    </a>
                </div>
            </template>
        </div>
    </template>
</div>

<script>
document.addEventListener('alpine:init', () => {
    Alpine.data('flotaUbicacionesApp', () => ({
        cargando: true,
        searchTerm: '',
        camiones: [],

        async init() {
            try {
                // Consultar camiones del backend MySQL
                const res = await window.api('/api/camiones');
                const list = Array.isArray(res) ? res : (res.data || []);
                
                // Cargar telemetría histórica básica de cada camión desde Firestore/Session
                this.camiones = await Promise.all(list.map(async (c) => {
                    let puntosCount = 0;
                    let ultAct = null;

                    try {
                        if (window.firestoreDb && window.firestoreDoc && window.firestoreGetDoc) {
                            const docRef = window.firestoreDoc(window.firestoreDb, 'ubicaciones_camion', `camion_${c.id}`);
                            const docSnap = await window.firestoreGetDoc(docRef);
                            
                            if (docSnap.exists()) {
                                const data = docSnap.data();
                                const hist = Array.isArray(data.historial) ? data.historial : [];
                                puntosCount = hist.length;
                                ultAct = data.ultima_actualizacion || (data.ultima_ubicacion?.timestamp ?? null);
                            }
                        }
                    } catch (e) {
                        // Fallback local en sessionStorage
                        const localData = JSON.parse(sessionStorage.getItem(`gps_camion_${c.id}`) || '[]');
                        puntosCount = localData.length;
                        if (puntosCount > 0) {
                            ultAct = localData[localData.length - 1].timestamp;
                        }
                    }

                    return {
                        ...c,
                        chofer_nombre: c.chofer?.nombre || c.chofer_nombre || 'No asignado',
                        puntos_count: puntosCount,
                        ultima_actualizacion: ultAct
                    };
                }));
            } catch (err) {
                window.toast(err.message || 'Error al cargar camiones de la flota', 'error');
            } finally {
                this.cargando = false;
            }
        },

        get camionesFiltrados() {
            if (!this.searchTerm) return this.camiones;
            const term = this.searchTerm.toLowerCase();
            return this.camiones.filter(c => 
                (c.placa && c.placa.toLowerCase().includes(term)) ||
                (c.chofer_nombre && c.chofer_nombre.toLowerCase().includes(term)) ||
                (c.descripcion && c.descripcion.toLowerCase().includes(term))
            );
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
