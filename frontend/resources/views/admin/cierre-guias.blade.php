@extends('layouts.app')

@section('title', 'Cierre de Guías - Fritolay Ambato')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6" x-data="cierreGuiasApp()" x-init="init()">
    <!-- Encabezado -->
    <div class="mb-6 flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-black text-slate-900 tracking-tight flex items-center gap-2">
                <svg class="w-7 h-7 text-[#E3001B]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
                Cierre de Guías
            </h1>
            <p class="text-sm font-semibold text-gray-500 mt-1">
                Administración y revisión de guías de remisión generadas en el sistema.
            </p>
        </div>
    </div>

    <!-- Filtros de Fecha y Estado -->
    <div class="bg-white p-4 rounded-2xl border border-gray-100 shadow-2xs mb-6 flex flex-col md:flex-row md:items-center justify-between gap-4">
        <!-- Rango de Fechas + Presets estilo Dashboard -->
        <div class="flex flex-wrap items-center gap-3">
            <div class="flex items-center gap-2">
                <span class="text-xs font-bold uppercase tracking-wider text-gray-500">DESDE:</span>
                <input type="date" 
                       x-model="filtros.fecha_inicio" 
                       @change="cargarGuias()" 
                       class="border border-gray-300 rounded-lg px-3 py-1.5 text-xs font-medium text-gray-800 focus:ring-2 focus:ring-slate-800 focus:border-slate-800 outline-none shadow-xs">
            </div>
            <div class="flex items-center gap-2">
                <span class="text-xs font-bold uppercase tracking-wider text-gray-500">HASTA:</span>
                <input type="date" 
                       x-model="filtros.fecha_fin" 
                       @change="cargarGuias()" 
                       class="border border-gray-300 rounded-lg px-3 py-1.5 text-xs font-medium text-gray-800 focus:ring-2 focus:ring-slate-800 focus:border-slate-800 outline-none shadow-xs">
            </div>

            <!-- Presets (Último Mes, Última Semana, Hoy) -->
            <div class="flex items-center bg-gray-100 p-1 rounded-xl text-xs font-semibold">
                <button @click="presetPeriodo('MES')" 
                        class="px-3 py-1.5 rounded-lg transition-all cursor-pointer" 
                        :class="esPeriodo('MES') ? 'bg-white text-gray-900 shadow-xs font-black' : 'text-gray-600 hover:text-gray-900 font-bold'">Último Mes</button>
                <button @click="presetPeriodo('SEMANA')" 
                        class="px-3 py-1.5 rounded-lg transition-all cursor-pointer" 
                        :class="esPeriodo('SEMANA') ? 'bg-white text-gray-900 shadow-xs font-black' : 'text-gray-600 hover:text-gray-900 font-bold'">Última Semana</button>
                <button @click="presetPeriodo('HOY')" 
                        class="px-3 py-1.5 rounded-lg transition-all cursor-pointer" 
                        :class="esPeriodo('HOY') ? 'bg-white text-gray-900 shadow-xs font-black' : 'text-gray-600 hover:text-gray-900 font-bold'">Hoy</button>
            </div>
        </div>

        <!-- Filtro por Estado de la Guía -->
        <div class="flex items-center gap-2 w-full md:w-auto">
            <span class="text-xs font-bold uppercase tracking-wider text-gray-500 whitespace-nowrap">ESTADO:</span>
            <select x-model="filtros.estado" @change="cargarGuias()"
                    class="w-full md:w-48 bg-gray-50 border border-gray-200 rounded-xl px-3 py-1.5 text-xs font-bold text-gray-800 focus:outline-none focus:border-red-500 transition-colors">
                <option value="">Todos los estados</option>
                <option value="abierta">Abierta</option>
                <option value="cerrada">Cerrada</option>
                <option value="revisada">Revisada</option>
            </select>
        </div>
    </div>

    <!-- Tabla de Guías -->
    <div class="bg-white rounded-2xl border border-gray-100 shadow-2xs overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-slate-900 text-white text-[11px] font-black uppercase tracking-wider">
                        <th class="py-3.5 px-4">Guía #</th>
                        <th class="py-3.5 px-4">Fecha</th>
                        <th class="py-3.5 px-4">Chofer / Vehículo</th>
                        <th class="py-3.5 px-4 text-center">Total Pedidos</th>
                        <th class="py-3.5 px-4 text-right">Total Entregado ($)</th>
                        <th class="py-3.5 px-4 text-right">Devoluciones ($)</th>
                        <th class="py-3.5 px-4 text-center">Estado</th>
                        <th class="py-3.5 px-4 text-center">Acciones</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 text-xs font-semibold text-gray-700">
                    <template x-if="cargando">
                        <tr>
                            <td colspan="8" class="py-12 text-center text-gray-400">
                                <div class="inline-flex items-center gap-2">
                                    <svg class="animate-spin h-5 w-5 text-red-600" fill="none" viewBox="0 0 24 24">
                                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                    </svg>
                                    <span>Cargando guías de remisión...</span>
                                </div>
                            </td>
                        </tr>
                    </template>

                    <template x-if="!cargando && guias.length === 0">
                        <tr>
                            <td colspan="8" class="py-12 text-center text-gray-400">
                                <svg class="w-12 h-12 mx-auto text-gray-300 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"/>
                                </svg>
                                <p class="font-bold text-sm text-gray-500">No se encontraron guías</p>
                                <p class="text-xs text-gray-400">Intenta cambiando los filtros de búsqueda.</p>
                            </td>
                        </tr>
                    </template>

                    <template x-for="g in guias" :key="g.id">
                        <tr class="hover:bg-gray-50/80 transition-colors">
                            <td class="py-3.5 px-4 font-black text-slate-900">
                                #<span x-text="g.id"></span>
                            </td>
                            <td class="py-3.5 px-4 text-gray-600 font-bold" x-text="formatearFecha(g.fecha_generacion)"></td>
                            <td class="py-3.5 px-4">
                                <div class="font-bold text-gray-900" x-text="g.chofer_nombre"></div>
                                <div class="text-[10px] text-gray-400 font-extrabold uppercase" x-text="'Placa: ' + g.camion_placa"></div>
                            </td>
                            <td class="py-3.5 px-4 text-center font-black text-gray-800" x-text="g.total_pedidos"></td>
                            <td class="py-3.5 px-4 text-right font-black text-emerald-600" x-text="formatMoney(g.total_entregado)"></td>
                            <td class="py-3.5 px-4 text-right font-black text-rose-600" x-text="formatMoney(g.total_devoluciones)"></td>
                            <td class="py-3.5 px-4 text-center">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[10px] font-black uppercase tracking-wider"
                                      :class="{
                                          'bg-amber-100 text-amber-800 border border-amber-200': g.estado === 'abierta',
                                          'bg-blue-100 text-blue-800 border border-blue-200': g.estado === 'cerrada',
                                          'bg-emerald-100 text-emerald-800 border border-emerald-200': g.estado === 'revisada'
                                      }">
                                    <span x-text="g.estado"></span>
                                </span>
                            </td>
                            <td class="py-3.5 px-4 text-center">
                                <a :href="'/admin/cierre-guias/' + g.id"
                                   :class="g.estado === 'revisada' ? 'bg-gray-100 text-gray-700 hover:bg-gray-200' : 'bg-slate-900 text-white hover:bg-slate-800'"
                                   class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl font-black text-xs transition-colors shadow-2xs">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                    </svg>
                                    <span x-text="g.estado === 'revisada' ? 'Ver' : 'Revisar'"></span>
                                </a>
                            </td>
                        </tr>
                    </template>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
    function getFormattedDateStr(d) {
        const year = d.getFullYear();
        const month = String(d.getMonth() + 1).padStart(2, '0');
        const day = String(d.getDate()).padStart(2, '0');
        return `${year}-${month}-${day}`;
    }

    document.addEventListener('alpine:init', () => {
        Alpine.data('cierreGuiasApp', () => ({
            guias: [],
            cargando: false,
            filtros: {
                fecha_inicio: '',
                fecha_fin: '',
                estado: ''
            },
            async init() {
                this.presetPeriodo('SEMANA', false);
                await this.cargarGuias();
            },
            presetPeriodo(tipo, triggerCargar = true) {
                const hoyObj = new Date();
                const hoyStrVal = getFormattedDateStr(hoyObj);
                this.filtros.fecha_fin = hoyStrVal;

                if (tipo === 'MES') {
                    const haceUnMes = new Date();
                    haceUnMes.setMonth(hoyObj.getMonth() - 1);
                    this.filtros.fecha_inicio = getFormattedDateStr(haceUnMes);
                } else if (tipo === 'SEMANA') {
                    const haceUnaSemana = new Date();
                    haceUnaSemana.setDate(hoyObj.getDate() - 7);
                    this.filtros.fecha_inicio = getFormattedDateStr(haceUnaSemana);
                } else if (tipo === 'HOY') {
                    this.filtros.fecha_inicio = hoyStrVal;
                }

                if (triggerCargar) {
                    this.cargarGuias();
                }
            },
            esPeriodo(tipo) {
                const hoyObj = new Date();
                const hoyStrVal = getFormattedDateStr(hoyObj);
                if (this.filtros.fecha_fin !== hoyStrVal) return false;

                if (tipo === 'HOY') return this.filtros.fecha_inicio === hoyStrVal;
                if (tipo === 'SEMANA') {
                    const haceUnaSemana = new Date();
                    haceUnaSemana.setDate(hoyObj.getDate() - 7);
                    return this.filtros.fecha_inicio === getFormattedDateStr(haceUnaSemana);
                }
                if (tipo === 'MES') {
                    const haceUnMes = new Date();
                    haceUnMes.setMonth(hoyObj.getMonth() - 1);
                    return this.filtros.fecha_inicio === getFormattedDateStr(haceUnMes);
                }
                return false;
            },
            async cargarGuias() {
                this.cargando = true;
                try {
                    let query = new URLSearchParams();
                    if (this.filtros.fecha_inicio) query.append('fecha_inicio', this.filtros.fecha_inicio);
                    if (this.filtros.fecha_fin) query.append('fecha_fin', this.filtros.fecha_fin);
                    if (this.filtros.estado) query.append('estado', this.filtros.estado);

                    const data = await window.api(`/api/guias-remision?${query.toString()}`);
                    this.guias = Array.isArray(data) ? data : [];
                } catch (e) {
                    console.error("Error al cargar guías:", e);
                    window.toast(e.message || "Error al obtener listado de guías", "error");
                } finally {
                    this.cargando = false;
                }
            },
            formatearFecha(fechaStr) {
                if (!fechaStr) return 'N/A';
                const d = new Date(fechaStr);
                if (isNaN(d.getTime())) return fechaStr;
                return d.toLocaleDateString('es-EC', { day: '2-digit', month: '2-digit', year: 'numeric', hour: '2-digit', minute: '2-digit' });
            }
        }));
    });
</script>
@endsection
