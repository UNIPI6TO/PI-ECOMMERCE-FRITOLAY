@extends('layouts.app')

@section('title', 'Gestión de Pedidos - Fritolay')

@section('content')
<div class="max-w-7xl mx-auto py-6 px-4 sm:px-6" x-data="gestionPedidos()">
    <!-- Header Principal -->
    <div class="flex flex-col lg:flex-row justify-between items-start lg:items-center gap-4 mb-6">
        <div>
            <div class="flex items-center gap-2 mb-1">
                <span class="px-2.5 py-0.5 rounded-full bg-red-50 text-[#E3001B] border border-red-100 font-extrabold text-[10px] uppercase tracking-wider">
                    Operaciones
                </span>
            </div>
            <h1 class="text-2xl font-black text-gray-900 tracking-tight">Gestión de Pedidos</h1>
            <p class="text-xs font-semibold text-gray-500 mt-0.5">Control centralizado de órdenes, aprobaciones de pago y geolocalización.</p>
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
        <span class="text-sm font-semibold text-gray-700">Cargando pedidos...</span>
    </div>

    <!-- Contenido de Gestión de Pedidos -->
    <div x-show="!loading" x-transition.opacity>

        <!-- KPI Cards con diseño de tarjetas modernas -->
        <div class="grid grid-cols-2 md:grid-cols-6 gap-3 mb-6">
            <template x-for="estado in estados" :key="estado.nombre">
                <div @click="filtroEstado = (estado.nombre === 'TODOS' ? null : estado.nombre); currentPage = 1" 
                     class="bg-white p-4 rounded-2xl border transition-all duration-200 cursor-pointer flex flex-col justify-between group" 
                     :class="[
                        (filtroEstado === estado.nombre || (filtroEstado === null && estado.nombre === 'TODOS')) 
                            ? 'border-slate-900 ring-2 ring-slate-900/10 shadow-md bg-slate-900/2 transform -translate-y-0.5' 
                            : 'border-gray-100 shadow-2xs hover:border-gray-300 hover:shadow-xs'
                     ]">
                    <div class="flex items-center justify-between mb-2">
                        <span class="text-[10px] font-extrabold uppercase tracking-wider text-gray-500" x-text="estado.nombre"></span>
                        <span class="w-2 h-2 rounded-full" 
                              :class="{
                                  'bg-gray-400': estado.nombre === 'TODOS',
                                  'bg-rose-500': estado.nombre === 'CANCELADO',
                                  'bg-amber-500': estado.nombre === 'PENDIENTE',
                                  'bg-blue-500': estado.nombre === 'APROBADO',
                                  'bg-indigo-500': estado.nombre === 'EN_RUTA',
                                  'bg-emerald-500': estado.nombre === 'ENTREGADO'
                              }"></span>
                    </div>
                    <div class="text-2xl font-black text-gray-900" x-text="estado.count"></div>
                </div>
            </template>
        </div>

        <!-- Mapa de Vista Geográfica -->
        <div class="bg-white p-5 rounded-2xl shadow-xs border border-gray-100 mb-6">
            <div class="flex items-center justify-between mb-4">
                <h2 class="font-extrabold text-base text-gray-900 flex items-center gap-2">
                    <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                    Vista Geográfica de Pedidos
                </h2>
                <span class="text-xs font-semibold text-gray-400">Pines interactivos agrupados por ubicación</span>
            </div>
            <div id="mapa-gestion" style="height: 380px;" class="rounded-xl border border-gray-100 overflow-hidden z-0 shadow-inner"></div>
        </div>

        <!-- Encabezado de la Tabla -->
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-4">
            <h2 class="font-extrabold text-lg text-gray-900">Listado de Pedidos</h2>
            <div class="flex items-center gap-3">
                <button x-show="hayPedidosParaAutoAprobar()" 
                        @click="autoAprobarMasivo()" 
                        class="bg-emerald-600 hover:bg-emerald-700 text-white px-4 py-2 rounded-xl text-xs font-extrabold shadow-2xs transition-all flex items-center gap-1.5 cursor-pointer">
                    <svg class="w-4 h-4 text-emerald-200" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"></path></svg>
                    <span>Auto Aprobar Pagos Directos</span>
                </button>

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
        </div>

        <!-- Tabla Estilizada de Pedidos -->
        <div class="bg-white rounded-2xl shadow-xs border border-gray-100 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead class="bg-gray-50/80 border-b border-gray-100 text-[11px] font-extrabold uppercase tracking-wider text-gray-500">
                        <tr>
                            <th class="py-3.5 px-4 cursor-pointer hover:text-gray-900 transition-colors" @click="sort('id')">ID ⇕</th>
                            <th class="py-3.5 px-4 cursor-pointer hover:text-gray-900 transition-colors" @click="sort('cliente')">Comercio / Cliente ⇕</th>
                            <th class="py-3.5 px-4">Método Pago</th>
                            <th class="py-3.5 px-4 cursor-pointer hover:text-gray-900 transition-colors" @click="sort('distancia')">Distancia ⇕</th>
                            <th class="py-3.5 px-4">Total</th>
                            <th class="py-3.5 px-4">Estado</th>
                            <th class="py-3.5 px-4 cursor-pointer hover:text-gray-900 transition-colors" @click="sort('raw_fecha')">Transcurrido ⇕</th>
                            <th class="py-3.5 px-4 text-center">Acción</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 text-xs">
                        <template x-if="paginatedPedidos.length === 0">
                            <tr><td colspan="8" class="py-12 text-center text-gray-400 font-medium">No se encontraron pedidos registrados.</td></tr>
                        </template>
                        <template x-for="p in paginatedPedidos" :key="p.id">
                            <tr class="hover:bg-gray-50/80 transition-colors group">
                                <td class="py-3.5 px-4 font-black text-gray-900" x-text="`#${p.id}`"></td>
                                <td class="py-3.5 px-4">
                                    <div class="font-bold text-gray-900 group-hover:text-[#E3001B] transition-colors" x-text="p.cliente"></div>
                                    <div class="text-[11px] text-gray-400 font-medium" x-text="p.nombre_persona"></div>
                                </td>
                                <td class="py-3.5 px-4">
                                    <span class="px-2.5 py-1 rounded-full text-[11px] font-bold bg-gray-100 text-gray-700 border border-gray-200 uppercase tracking-wider" x-text="p.pago"></span>
                                </td>
                                <td class="py-3.5 px-4 text-blue-600 font-extrabold" x-text="(p.distancia && p.distancia !== 999999) ? p.distancia + ' km' : '-'"></td>
                                <td class="py-3.5 px-4 font-black text-slate-900 text-sm" x-text="`$${Number(p.total).toFixed(2)}`"></td>
                                <td class="py-3.5 px-4">
                                    <span class="px-2.5 py-1 rounded-full text-[10px] font-extrabold uppercase tracking-wider border"
                                          :class="{
                                              'bg-amber-50 text-amber-800 border-amber-200': p.estado === 'PENDIENTE',
                                              'bg-blue-50 text-blue-800 border-blue-200': p.estado === 'APROBADO',
                                              'bg-indigo-50 text-indigo-800 border-indigo-200': p.estado === 'EN_RUTA',
                                              'bg-emerald-50 text-emerald-800 border-emerald-200': p.estado === 'ENTREGADO',
                                              'bg-rose-50 text-rose-800 border-rose-200': p.estado === 'CANCELADO'
                                          }"
                                          x-text="p.estado"></span>
                                </td>
                                <td class="py-3.5 px-4 text-gray-500 font-medium" x-text="timeAgo(p.fecha)"></td>
                                <td class="py-3.5 px-4 text-center">
                                    <button @click="
                                        if(p.estado === 'PENDIENTE') verDetalle(p); 
                                        else if(p.estado === 'APROBADO') abrirAsignarRuta(p); 
                                        else verDetalle(p)
                                    " 
                                            class="py-1.5 px-3 rounded-xl font-bold text-xs shadow-2xs transition-all cursor-pointer"
                                            :class="{
                                                'bg-amber-500 text-slate-950 hover:bg-amber-400': p.estado === 'PENDIENTE',
                                                'bg-slate-900 text-white hover:bg-slate-800': p.estado === 'APROBADO',
                                                'bg-gray-100 text-gray-700 hover:bg-gray-200 border border-gray-200': p.estado !== 'PENDIENTE' && p.estado !== 'APROBADO'
                                            }" 
                                            x-text="p.estado === 'PENDIENTE' ? 'Revisión' : (p.estado === 'APROBADO' ? 'Asignar Ruta' : 'Ver Detalle')"></button>
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

        <!-- Modal Revisión -->
        <div x-show="revisarModal" class="fixed inset-0 bg-slate-900/60 backdrop-blur-xs flex items-center justify-center z-50 p-4" style="display: none;">
            <div class="bg-white p-6 sm:p-8 rounded-2xl w-full max-w-2xl max-h-[90vh] overflow-y-auto shadow-2xl border border-gray-100">
                <div class="flex justify-between items-center mb-5 pb-3 border-b border-gray-100">
                    <h3 class="font-extrabold text-lg text-gray-900">Revisión de Pedido #<span x-text="selectedPedido?.id"></span></h3>
                    <button @click="cerrarRevision()" class="text-gray-400 hover:text-gray-600 p-1 rounded-lg hover:bg-gray-100">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                    </button>
                </div>
                
                <div class="grid grid-cols-2 gap-4 mb-5 text-xs bg-gray-50/80 p-4 rounded-xl border border-gray-100">
                    <div><span class="font-bold text-gray-500 uppercase tracking-wider">Comercio:</span> <span class="font-extrabold text-gray-900 block" x-text="selectedPedido?.cliente"></span></div>
                    <div><span class="font-bold text-gray-500 uppercase tracking-wider">Contacto:</span> <span class="font-bold text-gray-900 block" x-text="selectedPedido?.nombre_persona"></span></div>
                    <div><span class="font-bold text-gray-500 uppercase tracking-wider">Método:</span> <span class="font-bold text-gray-900 block" x-text="selectedPedido?.pago"></span></div>
                    <div><span class="font-bold text-gray-500 uppercase tracking-wider">Total:</span> <span class="font-black text-slate-900 text-sm block" x-text="`$${selectedPedido?.total}`"></span></div>
                </div>

                <div class="mb-6">
                    <h4 class="font-extrabold text-xs uppercase tracking-wider text-gray-500 mb-2">Comprobante / Documento</h4>
                    <div class="bg-gray-50 rounded-xl flex items-center justify-center min-h-[200px] relative border border-gray-200/80 p-4 overflow-hidden">
                        <div x-show="loadingComprobante" class="absolute inset-0 flex items-center justify-center bg-white/80 backdrop-blur-xs">
                            <span class="text-xs font-bold text-gray-600 flex items-center gap-2">
                                <svg class="animate-spin h-4 w-4 text-slate-800" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8z"></path></svg>
                                Cargando comprobante...
                            </span>
                        </div>
                        
                        <div x-show="!loadingComprobante && !comprobanteUrl" class="text-gray-400 text-center p-4">
                            <svg class="w-10 h-10 mx-auto text-gray-300 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                            <span class="text-xs font-semibold">No hay comprobante adjunto o es un método directo.</span>
                        </div>

                        <template x-if="!loadingComprobante && comprobanteUrl">
                            <div class="text-center">
                                <template x-if="comprobanteUrl.split('?')[0].toLowerCase().endsWith('.pdf')">
                                    <div class="bg-white p-4 rounded-xl border border-gray-200 shadow-2xs">
                                        <svg class="w-10 h-10 mx-auto text-rose-500 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path></svg>
                                        <p class="text-xs text-gray-700 mb-3 font-semibold">Comprobante en formato PDF</p>
                                        <a :href="comprobanteUrl" target="_blank" class="inline-block bg-slate-900 text-white px-4 py-2 rounded-xl text-xs font-bold hover:bg-slate-800 transition-all">
                                            Abrir Documento PDF
                                        </a>
                                    </div>
                                </template>
                                <template x-if="!comprobanteUrl.split('?')[0].toLowerCase().endsWith('.pdf')">
                                    <a :href="comprobanteUrl" target="_blank" title="Click para abrir en otra pestaña">
                                        <img :src="comprobanteUrl" class="max-w-full max-h-[350px] object-contain rounded-xl shadow-md hover:opacity-90 transition-opacity mx-auto" />
                                    </a>
                                </template>
                            </div>
                        </template>
                    </div>
                </div>

                <!-- Motivo rechazo -->
                <div class="mb-5" x-show="mostrarRechazo" style="display: none;">
                    <label class="block text-xs font-bold text-rose-700 mb-1">Motivo de Cancelación</label>
                    <textarea x-model="motivoRechazo" class="w-full border border-rose-300 rounded-xl p-3 text-xs text-gray-900 bg-rose-50/50 focus:bg-white focus:ring-2 focus:ring-rose-500 outline-none" rows="3" placeholder="Especifique el motivo obligatorio para el cliente..."></textarea>
                </div>

                <div class="flex justify-end gap-2.5 border-t border-gray-100 pt-4">
                    <button @click="cerrarRevision()" class="px-4 py-2 border border-gray-200 rounded-xl text-gray-600 hover:bg-gray-50 text-xs font-bold transition-all">
                        Mantener Pendiente
                    </button>
                    
                    <template x-if="!mostrarRechazo">
                        <button @click="mostrarRechazo = true" class="px-4 py-2 bg-white border border-rose-200 text-rose-600 hover:bg-rose-50 rounded-xl text-xs font-bold transition-all">
                            Cancelar Pedido
                        </button>
                    </template>

                    <template x-if="mostrarRechazo">
                        <button @click="confirmarRechazo()" class="px-4 py-2 bg-rose-600 hover:bg-rose-700 text-white rounded-xl text-xs font-bold shadow-2xs transition-all">
                            Confirmar Cancelación
                        </button>
                    </template>

                    <button @click="confirmarAprobacion()" x-show="!mostrarRechazo" class="px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white rounded-xl text-xs font-bold shadow-2xs transition-all">
                        Aprobar Pedido
                    </button>
                </div>
            </div>
        </div>

        <!-- Modal Asignar Ruta -->
        <div x-show="asignarModal" class="fixed inset-0 bg-slate-900/60 backdrop-blur-xs flex items-center justify-center z-50 p-4" style="display: none;">
            <div class="bg-white rounded-2xl w-full max-w-md p-6 sm:p-8 shadow-2xl relative border border-gray-100">
                <button @click="asignarModal = false" class="absolute top-4 right-4 text-gray-400 hover:text-gray-600 p-1 rounded-lg hover:bg-gray-100">
                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
                </button>
                <h3 class="text-lg font-extrabold text-gray-900 mb-1">Asignar a Ruta / Camión</h3>
                <p class="text-xs font-semibold text-gray-500 mb-4" x-text="`Pedido #${selectedPedido?.id} - ${selectedPedido?.cliente || ''}`"></p>
                
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
                    <button @click="confirmarAsignacion()" :disabled="!selectedCamionId" class="px-4 py-2 bg-slate-900 hover:bg-slate-800 text-white font-bold text-xs rounded-xl transition-all shadow-xs disabled:opacity-40 cursor-pointer">Asignar y Enviar</button>
                </div>
            </div>
        </div>

    </div>
</div>

<script>
document.addEventListener('alpine:init', () => {
    Alpine.data('gestionPedidos', () => {
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

        const initialFechaInicio = sessionStorage.getItem('gestion_pedidos_fecha_inicio') || oneWeekAgoStr;
        const initialFechaFin = sessionStorage.getItem('gestion_pedidos_fecha_fin') || todayStr;

        return {
            fechaInicio: initialFechaInicio,
            fechaFin: initialFechaFin,
            loading: true,
            filtroEstado: null,
            estados: [
                {nombre: 'TODOS', count: 0, color: 'border-gray-400'},
                {nombre: 'CANCELADO', count: 0, color: 'border-rose-500'},
                {nombre: 'PENDIENTE', count: 0, color: 'border-amber-500'},
                {nombre: 'APROBADO', count: 0, color: 'border-blue-500'},
                {nombre: 'EN_RUTA', count: 0, color: 'border-indigo-500'},
                {nombre: 'ENTREGADO', count: 0, color: 'border-emerald-500'}
            ],
            pedidos: [],
            camiones: [],
            map: null,
            markersLayer: null,
            currentLat: null,
            currentLng: null,
            
            currentPage: 1,
            perPage: 10,
            sortCol: 'raw_fecha',
            sortDesc: true,

            revisarModal: false,
            selectedPedido: null,
            comprobanteUrl: null,
            loadingComprobante: false,
            mostrarRechazo: false,
            motivoRechazo: '',

            asignarModal: false,
            selectedCamionId: '',

            get maxFechaFin() {
                if (!this.fechaInicio) return '';
                const parts = this.fechaInicio.split('-').map(Number);
                const dateObj = new Date(parts[0], parts[1] - 1, parts[2]);
                dateObj.setMonth(dateObj.getMonth() + 1);
                return getFormattedDate(dateObj);
            },

            async init() {
                this.validarRangoFechas();
                await this.cargarDatos();

                if (navigator.geolocation) {
                    navigator.geolocation.getCurrentPosition((pos) => {
                        this.currentLat = pos.coords.latitude;
                        this.currentLng = pos.coords.longitude;
                        
                        this.pedidos.forEach(p => {
                            if (p.lat && p.lng) {
                                p.distancia = parseFloat(this.getDist(this.currentLat, this.currentLng, p.lat, p.lng));
                            } else {
                                p.distancia = 999999;
                            }
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

                sessionStorage.setItem('gestion_pedidos_fecha_inicio', this.fechaInicio);
                sessionStorage.setItem('gestion_pedidos_fecha_fin', this.fechaFin);
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
                    
                    this.pedidos = pedidosRes || [];
                    this.camiones = camionesRes || [];
                    if(this.camiones.data) this.camiones = this.camiones.data;
                    
                    this.updateCounts();
                    this.renderMapa();
                } catch (error) {
                    console.error("Error al cargar pedidos:", error);
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
                        distanceHtml = `<div class="text-xs text-blue-600 font-bold mb-2">📍 a ${dist} km de tu ubicación actual</div>`;
                    }

                    let ordersHtml = `<div style="max-height: 150px; overflow-y: auto; padding-right: 5px;">`;
                    pedidos.forEach(p => {
                        ordersHtml += `
                            <div style="border-bottom: 1px solid #eee; padding-bottom: 8px; margin-bottom: 8px;">
                                <b>Pedido #${p.id}</b> <span style="font-size: 11px; color: #666;">(hace ${this.timeAgo(p.fecha)})</span><br>
                                <b>Comercio:</b> ${p.cliente}<br>
                                <b>Contacto:</b> ${p.nombre_persona}<br>
                                <b>Estado:</b> ${p.estado}<br>
                                <b>Total:</b> $${Number(p.total).toFixed(2)}
                            </div>
                        `;
                    });
                    ordersHtml += `</div>`;

                    const marker = L.marker([lat, lng]).bindPopup(`
                        <div style="min-width: 220px;">
                            <h4 style="font-weight: bold; margin-bottom: 5px; font-size: 14px;">${pedidos.length} pedido(s) aquí</h4>
                            ${distanceHtml}
                            ${ordersHtml}
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
                this.estados.forEach(e => {
                    if (e.nombre === 'TODOS') {
                        e.count = this.pedidos.filter(p => p.estado !== 'CANCELADO').length;
                    } else {
                        e.count = this.pedidos.filter(p => p.estado === e.nombre).length;
                    }
                });
            },

            async verDetalle(p) {
                if (p.estado === 'PENDIENTE') {
                    this.selectedPedido = p;
                    this.revisarModal = true;
                    this.mostrarRechazo = false;
                    this.motivoRechazo = '';
                    
                    if (p.pago === 'DE_UNA' || p.pago === 'DEPOSITO') {
                        this.loadingComprobante = true;
                        this.comprobanteUrl = null;
                        try {
                            const res = await window.api(`/api/pedidos/${p.id}/comprobante`);
                            this.comprobanteUrl = res.data.url;
                        } catch (e) {
                            console.error("No se pudo cargar el comprobante", e);
                        }
                        this.loadingComprobante = false;
                    } else {
                        this.comprobanteUrl = null;
                        this.loadingComprobante = false;
                    }
                } else {
                    Swal.fire('Detalle', `Pedido #${p.id} pagado con ${p.pago}. Estado actual: ${p.estado}`, 'info');
                }
            },

            cerrarRevision() {
                this.revisarModal = false;
                this.selectedPedido = null;
                this.comprobanteUrl = null;
            },

            abrirAsignarRuta(p) {
                this.selectedPedido = p;
                this.selectedCamionId = '';
                this.asignarModal = true;
            },
            
            async confirmarAsignacion() {
                if(!this.selectedCamionId) return;
                try {
                    await window.api('/api/asignaciones', {
                        method: 'POST',
                        body: JSON.stringify({
                            pedido_ids: [this.selectedPedido.id],
                            camion_id: this.selectedCamionId
                        })
                    });
                    
                    Swal.fire({
                        toast: true,
                        position: 'top-end',
                        icon: 'success',
                        title: 'Ruta asignada con éxito',
                        showConfirmButton: false,
                        timer: 3000
                    });
                    
                    this.asignarModal = false;
                    await this.init();
                } catch(e) {
                    Swal.fire('Error', e.message || 'Error al asignar la ruta', 'error');
                }
            },

            async confirmarAprobacion() {
                try {
                    await window.api(`/api/pedidos/${this.selectedPedido.id}/aprobar`, { method: 'PATCH' });
                    await Swal.fire({ icon: 'success', title: 'Éxito', text: 'Pedido aprobado', toast: true, position: 'bottom', showConfirmButton: false, timer: 2000 });
                    this.revisarModal = false;
                    await this.init();
                } catch (e) {
                    Swal.fire('Error', e.message || 'Error al aprobar', 'error');
                }
            },

            async confirmarRechazo() {
                if (!this.motivoRechazo.trim()) {
                    Swal.fire('Atención', 'Debe especificar un motivo de cancelación', 'warning');
                    return;
                }
                try {
                    await window.api(`/api/pedidos/${this.selectedPedido.id}/rechazar`, {
                        method: 'PATCH',
                        body: JSON.stringify({ motivo: this.motivoRechazo })
                    });
                    await Swal.fire({ icon: 'success', title: 'Éxito', text: 'Pedido cancelado', toast: true, position: 'bottom', showConfirmButton: false, timer: 2000 });
                    this.revisarModal = false;
                    this.mostrarRechazo = false;
                    await this.init();
                } catch (e) {
                    Swal.fire('Error', e.message || 'Error al cancelar', 'error');
                }
            },

            async autoAprobarMasivo() {
                const result = await Swal.fire({
                    title: '¿Auto Aprobar pagos directos?',
                    text: 'Se aprobarán automáticamente todos los pedidos en estado PENDIENTE que hayan sido pagados con Efectivo, Tarjeta de Crédito o Débito.',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#10b981',
                    cancelButtonColor: '#E3001B',
                    confirmButtonText: 'Sí, aprobar masivamente',
                    cancelButtonText: 'Cancelar'
                });
                
                if (result.isConfirmed) {
                    try {
                        const res = await window.api('/api/pedidos/bulk-aprobar-directos', { method: 'POST' });
                        await Swal.fire({ icon: 'success', title: '¡Aprobados!', text: res.message, toast: true, position: 'bottom', showConfirmButton: false, timer: 2000 });
                        
                        await this.init();
                    } catch (e) {
                        Swal.fire('Error', e.message || 'No se pudo aprobar masivamente', 'error');
                    }
                }
            },

            hayPedidosParaAsignar() {
                return this.pedidos.some(p => p.estado === 'en_espera_asignacion');
            },
            hayPedidosParaAutoAprobar() {
                const pagosValidos = ['efectivo', 'tc', 'td', 'tarjeta', 'debito', 'de_una'];
                return this.pedidos.some(p => {
                    if (p.estado !== 'PENDIENTE' || !p.pago) return false;
                    const metodo = p.pago.toLowerCase();
                    return pagosValidos.some(pv => metodo.includes(pv));
                });
            },

            get filteredPedidos() {
                let filtered = this.pedidos;
                if(this.filtroEstado) {
                    filtered = filtered.filter(p => p.estado === this.filtroEstado);
                } else {
                    filtered = filtered.filter(p => p.estado !== 'CANCELADO');
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
