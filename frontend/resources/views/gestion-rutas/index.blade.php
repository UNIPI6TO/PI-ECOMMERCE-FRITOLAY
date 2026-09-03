@extends('layouts.app')

@section('title', 'Asignación de Rutas - Fritolay')

@section('content')
<div class="max-w-7xl mx-auto py-6 px-4 sm:px-6" x-data="gestionRutas()">
    <!-- Header Principal -->
    <div class="flex flex-col lg:flex-row justify-between items-start lg:items-center gap-4 mb-6">
        <div>
            <div class="flex items-center gap-2 mb-1">
                <span class="px-2.5 py-0.5 rounded-full bg-red-50 text-[#E3001B] border border-red-100 font-extrabold text-[10px] uppercase tracking-wider">
                    Logística & Despacho
                </span>
            </div>
            <h1 class="text-2xl font-black text-gray-900 tracking-tight">Asignación de Rutas</h1>
            <p class="text-xs font-semibold text-gray-500 mt-0.5">Gestión de itinerarios de despacho, vehícular y geolocalización de entregas.</p>
        </div>
        
        <!-- Filtro de Rango de Fechas -->
        <div class="bg-white p-3 rounded-2xl border border-gray-100 shadow-xs flex flex-wrap items-center gap-3 w-full lg:w-auto">
            <div class="flex items-center gap-2 text-xs">
                <span class="font-extrabold uppercase tracking-wider text-gray-400">Desde:</span>
                <input type="date" 
                       x-model="fechaInicio" 
                       @change="onFechaInicioChange()" 
                       class="border border-gray-200 rounded-xl px-2.5 py-1.5 font-medium text-gray-800 focus:ring-2 focus:ring-slate-800 outline-none bg-gray-50/50">
            </div>
            <div class="flex items-center gap-2 text-xs">
                <span class="font-extrabold uppercase tracking-wider text-gray-400">Hasta:</span>
                <input type="date" 
                       x-model="fechaFin" 
                       :min="fechaInicio"
                       :max="maxFechaFin" 
                       @change="onFechaFinChange()" 
                       class="border border-gray-200 rounded-xl px-2.5 py-1.5 font-medium text-gray-800 focus:ring-2 focus:ring-slate-800 outline-none bg-gray-50/50">
            </div>

            <!-- Presets -->
            <div class="flex items-center bg-gray-100/80 p-1 rounded-xl text-xs font-bold">
                <button @click="presetPeriodo('MES')" class="px-3 py-1 rounded-lg transition-all" :class="esPeriodo('MES') ? 'bg-white text-gray-900 shadow-2xs' : 'text-gray-500 hover:text-gray-900'">Último Mes</button>
                <button @click="presetPeriodo('SEMANA')" class="px-3 py-1 rounded-lg transition-all" :class="esPeriodo('SEMANA') ? 'bg-white text-gray-900 shadow-2xs' : 'text-gray-500 hover:text-gray-900'">Semana</button>
                <button @click="presetPeriodo('HOY')" class="px-3 py-1 rounded-lg transition-all" :class="esPeriodo('HOY') ? 'bg-white text-gray-900 shadow-2xs' : 'text-gray-500 hover:text-gray-900'">Hoy</button>
            </div>
        </div>
    </div>

    <!-- Indicador de Carga -->
    <div x-show="loading" class="flex flex-col items-center justify-center py-20 bg-white rounded-2xl shadow-xs border border-gray-100 my-4">
        <svg class="animate-spin h-10 w-10 text-slate-800 mb-3" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8z"></path>
        </svg>
        <span class="text-sm font-semibold text-gray-700">Cargando rutas y pedidos...</span>
    </div>

    <!-- Contenido de Asignación de Rutas -->
    <div x-show="!loading" x-transition.opacity>

        <!-- Mapa de Vista Geográfica -->
        <div class="bg-white p-5 rounded-2xl shadow-xs border border-gray-100 mb-6">
            <div class="flex items-center justify-between mb-4">
                <h2 class="font-extrabold text-base text-gray-900 flex items-center gap-2">
                    <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7"></path></svg>
                    Mapa de Rutas y Distribución
                </h2>
                <span class="text-xs font-semibold text-gray-400">Leyenda de color por estado de asignación</span>
            </div>
            <div id="mapa-gestion" style="height: 380px;" class="rounded-xl border border-gray-100 overflow-hidden z-0 shadow-inner"></div>
            
            <!-- Leyenda de Mapa -->
            <div class="mt-4 pt-3 border-t border-gray-100 flex flex-wrap items-center justify-center gap-6 text-xs text-gray-600 font-semibold">
                <div class="flex items-center gap-2">
                    <div class="w-3.5 h-3.5 rounded-full bg-blue-500 shadow-2xs border border-blue-600"></div>
                    <span>Libre / Sin asignar</span>
                </div>
                <div class="flex items-center gap-2">
                    <div class="w-3.5 h-3.5 rounded-full bg-emerald-600 shadow-2xs border border-emerald-700"></div>
                    <span>Asignado a Ruta</span>
                </div>
                <div class="flex items-center gap-2">
                    <div class="w-3.5 h-3.5 rounded-full bg-rose-600 shadow-2xs border border-rose-700"></div>
                    <span>Seleccionado</span>
                </div>
            </div>
        </div>

        <!-- Barra de Acciones de Selección -->
        <div class="mb-6 flex flex-wrap gap-3 items-center bg-white p-4 rounded-2xl shadow-xs border border-gray-100">
            <!-- Botón Asignar Ruta -->
            <button @click="abrirAsignacionMultiple()"
                    :disabled="!isSelectionFree"
                    :class="isSelectionFree
                        ? 'bg-slate-900 hover:bg-slate-800 text-white shadow-xs cursor-pointer'
                        : 'bg-gray-100 text-gray-400 cursor-not-allowed border border-gray-200'"
                    class="px-4 py-2.5 rounded-xl text-xs font-extrabold transition-all flex items-center gap-2">
                <svg class="h-4 w-4 text-amber-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M8 16.5a1.5 1.5 0 11-3 0 1.5 1.5 0 013 0zM15 16.5a1.5 1.5 0 11-3 0 1.5 1.5 0 013 0z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 00-1 1v10a1 1 0 001 1h1.05a2.5 2.5 0 014.9 0H10a1 1 0 001-1V5a1 1 0 00-1-1H3zM14 7a1 1 0 00-1 1v6.05A2.5 2.5 0 0115.95 16H17a1 1 0 001-1v-5l-4-4h-0.05z" /></svg>
                <span>Asignar Ruta</span>
            </button>

            <!-- Quitar Asignación -->
            <button x-show="isSelectionAssigned"
                    @click="confirmarQuitarAsignacionRapida(selectedIds)"
                    class="bg-rose-50 text-rose-700 border border-rose-200 hover:bg-rose-100 px-4 py-2.5 rounded-xl text-xs font-extrabold shadow-2xs transition-all cursor-pointer">
                Quitar Asignación
            </button>

            <!-- Cerrar Ruta por camion -->
            <template x-for="truck in activeRoutes" :key="truck.id">
                <button @click="cerrarRuta(truck.id, truck.placa)"
                        class="bg-emerald-600 hover:bg-emerald-700 text-white px-4 py-2.5 rounded-xl shadow-2xs text-xs font-extrabold transition-all flex items-center gap-2 cursor-pointer">
                    <svg class="h-4 w-4 text-emerald-200" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7" /></svg>
                    <span>Cerrar Ruta <strong x-text="truck.placa"></strong></span>
                </button>
            </template>

            <!-- Contador de selección -->
            <div class="ml-auto text-xs text-gray-500 font-semibold flex items-center gap-2">
                <template x-if="selectedIds.length > 0">
                    <span class="bg-slate-100 text-slate-900 border border-slate-200 px-3 py-1.5 rounded-xl font-extrabold">
                        <span x-text="selectedIds.length"></span> seleccionado(s)
                    </span>
                </template>
                <template x-if="selectedIds.length === 0">
                    <span class="text-gray-400 italic">Selecciona pedidos de la tabla para asignar</span>
                </template>
            </div>
        </div>

        <!-- Encabezado de la Tabla -->
        <div class="flex justify-between items-center mb-4">
            <h2 class="font-extrabold text-lg text-gray-900">Listado de Pedidos</h2>
            <div class="flex items-center gap-2 text-xs font-semibold text-gray-500">
                <span>Mostrar:</span>
                <select x-model.number="perPage" @change="currentPage = 1" class="border border-gray-200 rounded-xl px-3 py-1.5 text-xs font-bold text-gray-800 bg-gray-50 focus:bg-white focus:ring-2 focus:ring-slate-800 outline-none cursor-pointer">
                    <option value="10">10</option>
                    <option value="20">20</option>
                    <option value="50">50</option>
                    <option value="100">100</option>
                </select>
            </div>
        </div>

        <!-- Tabla Estilizada -->
        <div class="bg-white rounded-2xl shadow-xs border border-gray-100 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead class="bg-gray-50/80 border-b border-gray-100 text-[11px] font-extrabold uppercase tracking-wider text-gray-500">
                        <tr>
                            <th class="py-3.5 px-4 w-10 text-center">
                                <input type="checkbox" x-model="allSelected" @change="toggleAll(); renderMarkers();" class="rounded border-gray-300 text-slate-900 focus:ring-slate-800 w-4 h-4 cursor-pointer">
                            </th>
                            <th class="py-3.5 px-4 cursor-pointer hover:text-gray-900 transition-colors" @click="sort('id')">ID ⇕</th>
                            <th class="py-3.5 px-4 cursor-pointer hover:text-gray-900 transition-colors" @click="sort('cliente')">Comercio / Cliente ⇕</th>
                            <th class="py-3.5 px-4 cursor-pointer hover:text-gray-900 transition-colors" @click="sort('distancia')">Distancia ⇕</th>
                            <th class="py-3.5 px-4">Total</th>
                            <th class="py-3.5 px-4">Ubicación</th>
                            <th class="py-3.5 px-4 cursor-pointer hover:text-gray-900 transition-colors" @click="sort('raw_fecha')">Transcurrido ⇕</th>
                            <th class="py-3.5 px-4">Ruta / Camión</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 text-xs">
                        <template x-if="paginatedPedidos.length === 0">
                            <tr><td colspan="8" class="py-12 text-center text-gray-400 font-medium">No hay pedidos disponibles para asignación.</td></tr>
                        </template>
                        <template x-for="p in paginatedPedidos" :key="p.id">
                            <tr class="hover:bg-gray-50/80 transition-colors group">
                                <td class="py-3.5 px-4 text-center">
                                    <input type="checkbox" :value="p.id" x-model="selectedIds" @change="renderMarkers()" class="rounded border-gray-300 text-slate-900 focus:ring-slate-800 w-4 h-4 cursor-pointer">
                                </td>
                                <td class="py-3.5 px-4 font-black text-gray-900" x-text="`#${p.id}`"></td>
                                <td class="py-3.5 px-4">
                                    <div class="font-bold text-gray-900 group-hover:text-[#E3001B] transition-colors" x-text="p.cliente"></div>
                                    <div class="text-[11px] text-gray-400 font-medium" x-text="p.nombre_persona"></div>
                                </td>
                                <td class="py-3.5 px-4 text-blue-600 font-extrabold" x-text="(p.distancia && p.distancia !== 999999) ? p.distancia + ' km' : '-'"></td>
                                <td class="py-3.5 px-4 font-black text-slate-900 text-sm" x-text="`$${Number(p.total).toFixed(2)}`"></td>
                                <td class="py-3.5 px-4 font-semibold text-gray-600" x-init="fetchLocation(p)" :title="p.locationFull || 'Cargando...'" x-text="p.locationDisplay || 'Cargando...'"></td>
                                <td class="py-3.5 px-4 text-gray-500 font-medium" x-text="timeAgo(p.fecha)"></td>
                                <td class="py-3.5 px-4">
                                    <template x-if="p.camion_id">
                                        <div class="inline-flex items-center gap-1.5 bg-emerald-50 text-emerald-800 px-3 py-1 rounded-full text-[11px] font-extrabold border border-emerald-200 cursor-pointer hover:bg-rose-50 hover:text-rose-700 hover:border-rose-200 transition-colors" @click="confirmarQuitarAsignacionRapida([p.id])" title="Clic para quitar asignación">
                                            <span>🚚</span>
                                            <span x-text="p.camion_placa || 'Asignado'"></span>
                                        </div>
                                    </template>
                                    <template x-if="!p.camion_id">
                                        <span class="text-[11px] text-gray-400 italic font-medium">Sin asignar</span>
                                    </template>
                                </td>
                            </tr>
                        </template>
                    </tbody>
                </table>
            </div>
            
            <!-- Paginador Estandarizado Slate -->
            <div x-show="!loading && filteredPedidos.length > 0" class="px-6 py-4 border-t border-gray-100 flex flex-col sm:flex-row items-center justify-between gap-4 bg-gray-50/50">
                <div class="text-xs font-semibold text-gray-500">
                    Mostrando <span class="font-extrabold text-gray-900" x-text="startRecord"></span> a <span class="font-extrabold text-gray-900" x-text="endRecord"></span> de <span class="font-extrabold text-gray-900" x-text="filteredPedidos.length"></span> pedidos
                </div>

                <div class="flex items-center gap-1.5">
                    <button @click="prevPage()" :disabled="currentPage === 1" class="px-3 py-1.5 border border-gray-200 rounded-xl text-xs font-bold text-gray-700 bg-white hover:bg-gray-50 transition-all disabled:opacity-40 disabled:cursor-not-allowed shadow-2xs">
                        Anterior
                    </button>

                    <template x-for="p in totalPages" :key="p">
                        <button @click="goToPage(p)" 
                                class="w-8 h-8 rounded-xl text-xs font-bold transition-all shadow-2xs"
                                :class="currentPage === p ? 'bg-slate-900 text-white shadow-xs' : 'bg-white border border-gray-200 text-gray-700 hover:bg-gray-50'"
                                x-text="p"></button>
                    </template>

                    <button @click="nextPage()" :disabled="currentPage === totalPages" class="px-3 py-1.5 border border-gray-200 rounded-xl text-xs font-bold text-gray-700 bg-white hover:bg-gray-50 transition-all disabled:opacity-40 disabled:cursor-not-allowed shadow-2xs">
                        Siguiente
                    </button>
                </div>
            </div>
        </div>

        <!-- Modal Asignación Masiva de Ruta -->
        <div x-show="asignarModal" class="fixed inset-0 bg-slate-900/60 backdrop-blur-xs flex items-center justify-center z-50 p-4" style="display: none;">
            <div class="bg-white rounded-2xl w-full max-w-md p-6 sm:p-8 shadow-2xl relative border border-gray-100">
                <button @click="asignarModal = false" class="absolute top-4 right-4 text-gray-400 hover:text-gray-600 p-1 rounded-lg hover:bg-gray-100">
                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
                </button>
                <h3 class="text-lg font-extrabold text-gray-900 mb-1">Asignar a Ruta / Camión</h3>
                <p class="text-xs font-semibold text-gray-500 mb-4">Asignando <strong class="text-slate-900" x-text="selectedIds.length"></strong> pedido(s) seleccionado(s)</p>
                <div class="mb-5">
                    <label class="block text-xs font-bold text-gray-700 mb-1">Seleccionar Camión Disponible</label>
                    <select x-model="selectedCamionId" class="w-full border border-gray-200 rounded-xl px-3 py-2.5 text-xs font-semibold text-gray-800 bg-gray-50 focus:bg-white focus:ring-2 focus:ring-slate-800 outline-none cursor-pointer">
                        <option value="">-- Seleccione un camión --</option>
                        <template x-for="camion in camiones" :key="camion.id">
                            <option :value="camion.id" x-text="`${camion.placa} - ${camion.descripcion} (${camion.estado})`"></option>
                        </template>
                    </select>
                </div>
                <div class="flex justify-end gap-2.5 pt-4 border-t border-gray-100">
                    <button @click="asignarModal = false" class="px-4 py-2 border border-gray-200 text-gray-600 font-bold text-xs rounded-xl hover:bg-gray-50 transition-all">Cancelar</button>
                    <button @click="confirmarAsignacion()" :disabled="!selectedCamionId" class="px-4 py-2 bg-slate-900 hover:bg-slate-800 text-white font-bold text-xs rounded-xl transition-all shadow-xs disabled:opacity-40 cursor-pointer">
                        Confirmar Asignación
                    </button>
                </div>
            </div>
        </div>

    </div>
</div>

<script>
document.addEventListener('alpine:init', () => {
    Alpine.data('gestionRutas', () => {
        const getFormattedDate = (dateObj) => {
            const year = dateObj.getFullYear();
            const month = String(dateObj.getMonth() + 1).padStart(2, '0');
            const day = String(dateObj.getDate()).padStart(2, '0');
            return `${year}-${month}-${day}`;
        };

        const todayObj = new Date();
        const todayStr = getFormattedDate(todayObj);
        
        const oneWeekAgoObj = new Date(Date.now() - 7 * 24 * 60 * 60 * 1000);
        const oneWeekAgoStr = getFormattedDate(oneWeekAgoObj);

        const initialFechaInicio = sessionStorage.getItem('gestion_rutas_fecha_inicio') || oneWeekAgoStr;
        const initialFechaFin = sessionStorage.getItem('gestion_rutas_fecha_fin') || todayStr;

        return {
            fechaInicio: initialFechaInicio,
            fechaFin: initialFechaFin,
            loading: true,
            filtroEstado: null,
            pedidos: [],
            selectedIds: [],
            allSelected: false,
            selectedCamionesParaCerrar: [],
            asignarModal: false,
            selectedCamionId: '',
            camiones: [],
            map: null,
            markersLayer: null,
            currentLat: null,
            currentLng: null,
            
            currentPage: 1,
            perPage: 10,
            sortCol: 'distancia',
            sortAsc: true,
            
            locationQueue: [],
            isProcessingQueue: false,

            revisarModal: false,
            selectedPedido: null,
            comprobanteUrl: null,
            loadingComprobante: false,
            mostrarRechazo: false,
            motivoRechazo: '',

            get maxFechaFin() {
                if (!this.fechaInicio) return '';
                const parts = this.fechaInicio.split('-').map(Number);
                const dateObj = new Date(parts[0], parts[1] - 1, parts[2]);
                dateObj.setMonth(dateObj.getMonth() + 1);
                return getFormattedDate(dateObj);
            },

            async init() {
                this.validarRangoFechas();

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

                await this.cargarDatos();

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

                this.$watch('filtroEstado', () => {
                    this.currentPage = 1;
                    this.renderMarkers();
                });
            },

            validarRangoFechas() {
                if (!this.fechaInicio) this.fechaInicio = oneWeekAgoStr;
                if (!this.fechaFin) this.fechaFin = todayStr;

                if (this.fechaFin < this.fechaInicio) {
                    this.fechaFin = this.fechaInicio;
                }

                const maxPermitida = this.maxFechaFin;
                if (maxPermitida && this.fechaFin > maxPermitida) {
                    this.fechaFin = maxPermitida;
                }

                sessionStorage.setItem('gestion_rutas_fecha_inicio', this.fechaInicio);
                sessionStorage.setItem('gestion_rutas_fecha_fin', this.fechaFin);
            },

            onFechaInicioChange() {
                this.validarRangoFechas();
                this.cargarDatos();
            },

            onFechaFinChange() {
                this.validarRangoFechas();
                this.cargarDatos();
            },

            resetFechasSemana() {
                this.fechaInicio = oneWeekAgoStr;
                this.fechaFin = todayStr;
                this.validarRangoFechas();
                this.cargarDatos();
            },

            presetPeriodo(tipo) {
                const hoyObj = new Date();
                const hoyStrVal = getFormattedDate(hoyObj);
                this.fechaFin = hoyStrVal;

                if (tipo === 'MES') {
                    const haceUnMes = new Date();
                    haceUnMes.setMonth(hoyObj.getMonth() - 1);
                    this.fechaInicio = getFormattedDate(haceUnMes);
                } else if (tipo === 'SEMANA') {
                    const haceUnaSemana = new Date();
                    haceUnaSemana.setDate(hoyObj.getDate() - 7);
                    this.fechaInicio = getFormattedDate(haceUnaSemana);
                } else if (tipo === 'HOY') {
                    this.fechaInicio = hoyStrVal;
                }
                this.validarRangoFechas();
                this.cargarDatos();
            },

            esPeriodo(tipo) {
                const hoyObj = new Date();
                const hoyStrVal = getFormattedDate(hoyObj);
                if (this.fechaFin !== hoyStrVal) return false;

                if (tipo === 'HOY') return this.fechaInicio === hoyStrVal;
                if (tipo === 'SEMANA') {
                    const haceUnaSemana = new Date();
                    haceUnaSemana.setDate(hoyObj.getDate() - 7);
                    return this.fechaInicio === getFormattedDate(haceUnaSemana);
                }
                if (tipo === 'MES') {
                    const haceUnMes = new Date();
                    haceUnMes.setMonth(hoyObj.getMonth() - 1);
                    return this.fechaInicio === getFormattedDate(haceUnMes);
                }
                return false;
            },

            async cargarDatos() {
                this.loading = true;
                try {
                    const params = `?fecha_inicio=${this.fechaInicio}&fecha_fin=${this.fechaFin}`;
                    const [pedidosRes, camionesRes] = await Promise.all([
                        window.api(`/api/pedidos${params}`),
                        window.api('/api/camiones')
                    ]);
                    
                    const rawPedidos = Array.isArray(pedidosRes) ? pedidosRes : (pedidosRes.data || []);
                    this.pedidos = rawPedidos.map(p => ({
                        ...p,
                        lat: p.lat ? parseFloat(p.lat) : null,
                        lng: p.lng ? parseFloat(p.lng) : null,
                        distancia: (this.currentLat && this.currentLng && p.lat && p.lng) 
                            ? parseFloat(this.getDist(this.currentLat, this.currentLng, p.lat, p.lng))
                            : 999999,
                        raw_fecha: p.raw_fecha || 0
                    }));
                    
                    this.camiones = Array.isArray(camionesRes) ? camionesRes : (camionesRes.data || []);
                    this.updateCounts();
                    this.renderMapa();
                } catch (error) {
                    console.error("Error al cargar pedidos en rutas:", error);
                } finally {
                    this.loading = false;
                }
            },

            renderMapa() {
                if (!this.map) {
                    setTimeout(() => {
                        const mapEl = document.getElementById('mapa-gestion');
                        if (mapEl) {
                            this.map = L.map('mapa-gestion').setView([-1.249, -78.616], 13);
                            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png').addTo(this.map);
                            this.markersLayer = L.layerGroup().addTo(this.map);
                            this.renderMarkers();
                        }
                    }, 100);
                } else {
                    this.renderMarkers();
                }
            },
        
            renderMarkers() {
                if(!this.markersLayer) return;
                this.markersLayer.clearLayers();
                
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
                        distanceHtml = `
                            <div style="font-size:11px;font-weight:700;color:#2563eb;margin-bottom:8px;background:#f0f9ff;padding:4px 8px;border-radius:6px;border:1px solid #bae6fd;display:flex;align-items:center;gap:4px;">
                                <span>📍</span>
                                <span>a ${dist} km de tu ubicación actual</span>
                            </div>
                        `;
                    }

                    let ordersHtml = `<div style="max-height: 260px; overflow-y: auto; padding-right: 2px;">`;
                    pedidos.forEach(p => {
                        const estadoClean = (p.estado || p.raw_estado || '').toUpperCase();
                        const timeFormatted = this.timeAgo(p.fecha || p.creado_en);
                        
                        let badgeBg = '#fef3c7';
                        let badgeColor = '#92400e';
                        let badgeDot = '#f59e0b';
                        let estadoLabel = p.estado || 'PENDIENTE';

                        if (estadoClean.includes('RUTA') || estadoClean.includes('APROBADO') || estadoClean.includes('LISTO')) {
                            badgeBg = '#dbeafe';
                            badgeColor = '#1e40af';
                            badgeDot = '#3b82f6';
                        } else if (estadoClean.includes('ENTREGADO')) {
                            badgeBg = '#d1fae5';
                            badgeColor = '#065f46';
                            badgeDot = '#10b981';
                        }

                        let logisticaHtml = '';
                        if (estadoClean.includes('RUTA') || estadoClean.includes('APROBADO') || estadoClean.includes('LISTO')) {
                            const guiaNum = p.guia_numero || p.guia_id ? `TRK-${p.guia_numero || p.guia_id}` : 'TRK-8839201';
                            const camionPlaca = p.camion_placa || p.placa || (p.camion_id ? 'ABC-1234' : 'Sin Asignar');
                            logisticaHtml = `
                                <div style="margin-top:8px;padding-top:6px;border-top:1px solid #f1f5f9;display:grid;grid-template-columns:1fr 1fr;gap:6px;background:#f8fafc;padding:6px;border-radius:6px;font-size:11px;">
                                    <div>
                                        <span style="font-size:9px;font-weight:800;text-transform:uppercase;color:#94a3b8;display:block;">Guía</span>
                                        <span style="font-family:monospace;font-weight:700;color:#0f172a;">${guiaNum}</span>
                                    </div>
                                    <div>
                                        <span style="font-size:9px;font-weight:800;text-transform:uppercase;color:#94a3b8;display:block;">Vehículo (Placa)</span>
                                        <span style="font-weight:700;color:#0f172a;">${camionPlaca}</span>
                                    </div>
                                </div>
                            `;
                        }

                        const removeBtn = p.camion_id ? `<button onclick="window.dispatchEvent(new CustomEvent('quitar-asignacion-popup',{detail:${p.id}}))" style="width:100%;margin-top:8px;padding:5px;font-size:11px;font-weight:bold;color:#dc2626;background:#fee2e2;border:1px solid #fca5a5;border-radius:6px;cursor:pointer;">Quitar Asignación</button>` : '';

                        ordersHtml += `
                            <div style="background:#fff;padding:10px;border-radius:10px;border:1px solid #e2e8f0;margin-bottom:8px;font-family:sans-serif;font-size:12px;">
                                <div style="display:flex;align-items:center;justify-between;border-bottom:1px solid #f1f5f9;padding-bottom:6px;margin-bottom:6px;">
                                    <div style="font-weight:900;color:#0f172a;font-size:13px;">
                                        Pedido #${p.id}
                                        ${timeFormatted ? `<span style="font-size:10px;color:#94a3b8;font-weight:normal;margin-left:4px;">(${timeFormatted})</span>` : ''}
                                    </div>
                                    <span style="background:${badgeBg};color:${badgeColor};padding:2px 8px;border-radius:12px;font-size:10px;font-weight:800;text-transform:uppercase;display:inline-flex;align-items:center;gap:4px;">
                                        <span style="width:6px;height:6px;border-radius:50%;background:${badgeDot};display:inline-block;"></span>
                                        ${estadoLabel}
                                    </span>
                                </div>

                                <div style="display:flex;flex-direction:column;gap:3px;font-size:11px;">
                                    <div style="display:flex;justify-content:space-between;">
                                        <span style="font-weight:700;color:#64748b;">Comercio:</span>
                                        <span style="font-weight:700;color:#0f172a;text-align:right;">${p.cliente || 'Tienda Minorista Lopez'}</span>
                                    </div>
                                    <div style="display:flex;justify-content:space-between;">
                                        <span style="font-weight:700;color:#64748b;">Contacto:</span>
                                        <span style="font-weight:500;color:#334155;text-align:right;">${p.nombre_persona || 'Carlos Lopez'}</span>
                                    </div>
                                    <div style="display:flex;justify-content:space-between;padding-top:4px;border-top:1px solid #f8fafc;margin-top:2px;">
                                        <span style="font-weight:900;color:#0f172a;">Total:</span>
                                        <span style="font-weight:900;color:#0f172a;font-size:13px;">$${Number(p.total).toFixed(2)}</span>
                                    </div>
                                </div>

                                ${logisticaHtml}
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

                    const popupTitle = pedidos.length === 1 
                        ? `Detalles del Pedido #${pedidos[0].id}` 
                        : `Ubicación: ${pedidos.length} Pedidos`;
                    
                    marker.bindPopup(`
                        <div style="min-width:250px;max-width:290px;font-family:sans-serif;padding:2px;">
                            <h3 style="font-weight:900;font-size:14px;color:#0f172a;margin-bottom:6px;border-bottom:1px solid #e2e8f0;padding-bottom:4px;">${popupTitle}</h3>
                            ${distanceHtml}
                            ${ordersHtml}
                            <div style="font-size:10px;text-align:center;color:#94a3b8;margin-top:4px;font-style:italic;">💡 Click en el pin = seleccionar en tabla</div>
                        </div>
                    `);
                    this.markersLayer.addLayer(marker);
                }
            },

            getDist(lat1, lon1, lat2, lon2) {
                const R = 6371;
                const dLat = (lat2 - lat1) * Math.PI / 180;
                const dLon = (lon2 - lon1) * Math.PI / 180;
                const a = Math.sin(dLat/2) * Math.sin(dLat/2) +
                          Math.cos(lat1 * Math.PI / 180) * Math.cos(lat2 * Math.PI / 180) *
                          Math.sin(dLon/2) * Math.sin(dLon/2);
                const c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1-a));
                return (R * c).toFixed(2);
            },

            timeAgo(dateStr) {
                if (!dateStr) return '';
                const date = new Date(dateStr.replace(/-/g, '/'));
                const now = new Date();
                let diffSecs = Math.floor((now - date) / 1000);
                
                if (isNaN(diffSecs) || diffSecs <= 0) return 'hace 0s';
                
                if (diffSecs < 60) return `hace ${diffSecs}s`;
                const mins = Math.floor(diffSecs / 60);
                if (mins < 60) return `hace ${mins}m`;
                const hours = Math.floor(mins / 60);
                if (hours < 24) return `hace ${hours}h`;
                const days = Math.floor(hours / 24);
                return `hace ${days}d`;
            },

            updateCounts() {},
            
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
                    ids = [ids];
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
                    
                    await new Promise(r => setTimeout(r, 1500));
                }
                this.isProcessingQueue = false;
            },

            isSubmitting: false,

            async confirmarAsignacion() {
                if (this.isSubmitting) return;
                if(!this.selectedCamionId) return;
                if(this.selectedIds.length === 0) {
                    Swal.fire('Error', 'No hay pedidos seleccionados.', 'error');
                    return;
                }
                this.isSubmitting = true;
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
                    this.isSubmitting = false;
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
                    filtered = filtered.filter(p => p.raw_estado === this.filtroEstado || p.estado === this.filtroEstado);
                } else {
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

            get startRecord() {
                if (this.filteredPedidos.length === 0) return 0;
                return (this.currentPage - 1) * this.perPage + 1;
            },

            get endRecord() {
                return Math.min(this.currentPage * this.perPage, this.filteredPedidos.length);
            },

            goToPage(p) {
                if (p >= 1 && p <= this.totalPages) this.currentPage = p;
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
        };
    });
});
</script>
@endsection
