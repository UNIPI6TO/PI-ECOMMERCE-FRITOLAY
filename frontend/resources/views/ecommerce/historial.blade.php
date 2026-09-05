@extends('layouts.app')

@section('title', 'Historial de Pedidos - Fritolay')

@section('content')
<div class="max-w-7xl mx-auto py-8 px-4" x-data="historial()">
    <!-- Header Page Title & Summary Cards -->
    <div class="mb-8 flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div>
            <h1 class="text-3xl font-black text-gray-900 tracking-tight flex items-center gap-3">
                <svg class="h-8 w-8 text-[#E3001B]" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01" />
                </svg>
                Historial de Pedidos
            </h1>
            <p class="text-xs font-semibold text-gray-500 mt-1">Consulta tus compras realizadas, rastrea entregas en ruta y descarga tus comprobantes oficiales SRI.</p>
        </div>
    </div>

    <!-- Metrics Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-8">
        <div class="bg-white p-5 rounded-2xl border border-gray-100 shadow-2xs flex items-center gap-4">
            <div class="p-3 bg-slate-900 text-white rounded-xl shadow-2xs">
                <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 11h14l1 12H4L5 11z" />
                </svg>
            </div>
            <div>
                <p class="text-[10px] font-extrabold uppercase tracking-wider text-gray-400">Pedidos en Rango</p>
                <p class="text-2xl font-black text-gray-900" x-text="pedidosFiltrados.length"></p>
            </div>
        </div>

        <div class="bg-white p-5 rounded-2xl border border-gray-100 shadow-2xs flex items-center gap-4">
            <div class="p-3 bg-emerald-50 text-emerald-600 rounded-xl border border-emerald-100">
                <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                </svg>
            </div>
            <div>
                <p class="text-[10px] font-extrabold uppercase tracking-wider text-gray-400">Entregados</p>
                <p class="text-2xl font-black text-gray-900" x-text="pedidosFiltrados.filter(p => p.estado.includes('entregado')).length"></p>
            </div>
        </div>

        <div class="bg-white p-5 rounded-2xl border border-gray-100 shadow-2xs flex items-center gap-4">
            <div class="p-3 bg-amber-50 text-amber-600 rounded-xl border border-amber-100">
                <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
            </div>
            <div>
                <p class="text-[10px] font-extrabold uppercase tracking-wider text-gray-400">Total Facturado</p>
                <p class="text-2xl font-black text-slate-900" x-text="formatMoney(pedidosFiltrados.reduce((acc, p) => acc + (parseFloat(p.total) || 0), 0))"></p>
            </div>
        </div>
    </div>

    <!-- Filters & Pagination Toolbar Bar -->
    <div class="bg-white p-5 rounded-2xl border border-gray-100 shadow-2xs mb-6 space-y-4">
        <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-4">
            <!-- Date Filter Inputs -->
            <div class="flex flex-wrap items-center gap-3">
                <div class="flex items-center gap-2 text-xs">
                    <span class="font-extrabold uppercase text-gray-400 tracking-wider">Desde:</span>
                    <input type="date" x-model="fechaInicio" @change="onFechaInicioChange()" class="border border-gray-200 rounded-xl px-3 py-2 text-xs font-medium focus:ring-2 focus:ring-slate-800 outline-none bg-gray-50/50">
                </div>
                <div class="flex items-center gap-2 text-xs">
                    <span class="font-extrabold uppercase text-gray-400 tracking-wider">Hasta:</span>
                    <input type="date" x-model="fechaFin" :max="maxFechaFin" @change="filtrar()" class="border border-gray-200 rounded-xl px-3 py-2 text-xs font-medium focus:ring-2 focus:ring-slate-800 outline-none bg-gray-50/50">
                </div>

                <!-- Presets -->
                <div class="flex items-center bg-gray-100/80 p-1 rounded-xl text-xs font-bold">
                    <button @click="presetPeriodo('MES')" class="px-3 py-1 rounded-lg transition-all" :class="esPeriodo('MES') ? 'bg-white text-gray-900 shadow-2xs font-extrabold' : 'text-gray-500 hover:text-gray-900'">Último Mes</button>
                    <button @click="presetPeriodo('SEMANA')" class="px-3 py-1 rounded-lg transition-all" :class="esPeriodo('SEMANA') ? 'bg-white text-gray-900 shadow-2xs font-extrabold' : 'text-gray-500 hover:text-gray-900'">Semana</button>
                    <button @click="presetPeriodo('HOY')" class="px-3 py-1 rounded-lg transition-all" :class="esPeriodo('HOY') ? 'bg-white text-gray-900 shadow-2xs font-extrabold' : 'text-gray-500 hover:text-gray-900'">Hoy</button>
                </div>
            </div>

            <!-- Action Buttons & PerPage Selector -->
            <div class="flex items-center justify-between lg:justify-end gap-3 border-t lg:border-t-0 pt-3 lg:pt-0">
                <div class="flex items-center gap-2">
                    <button @click="filtrar()" class="bg-slate-900 hover:bg-slate-800 text-white text-xs font-extrabold px-4 py-2 rounded-xl transition-all shadow-xs flex items-center gap-1.5 cursor-pointer">
                        <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z" />
                        </svg>
                        Filtrar
                    </button>
                    <button @click="limpiarFiltros()" class="bg-gray-100 hover:bg-gray-200 text-gray-700 text-xs font-bold px-3.5 py-2 rounded-xl transition-all">
                        Restablecer
                    </button>
                </div>

                <!-- Per Page Selector -->
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
    </div>

    <!-- Table Container -->
    <div class="bg-white rounded-2xl shadow-xs border border-gray-100 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead class="bg-gray-50/80 border-b border-gray-100 text-[11px] font-extrabold uppercase tracking-wider text-gray-500">
                    <tr>
                        <th class="py-4 px-6"># Pedido</th>
                        <th class="py-4 px-6">Fecha Emisión</th>
                        <th class="py-4 px-6">Estado</th>
                        <th class="py-4 px-6">Método Pago</th>
                        <th class="py-4 px-6 text-right">Total</th>
                        <th class="py-4 px-6 text-right">Acciones</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 text-xs">
                    <!-- Loading Spinner -->
                    <tr x-show="loading">
                        <td colspan="6" class="py-12 text-center text-gray-500">
                            <div class="inline-flex items-center gap-3">
                                <svg class="animate-spin h-6 w-6 text-slate-800" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                </svg>
                                <span class="font-semibold text-gray-700">Cargando historial...</span>
                            </div>
                        </td>
                    </tr>

                    <!-- Empty State -->
                    <tr x-show="!loading && paginatedPedidos.length === 0">
                        <td colspan="6" class="py-12 text-center text-gray-400 font-medium">
                            No se encontraron pedidos en tu historial.
                        </td>
                    </tr>

                    <!-- Rows -->
                    <template x-for="pedido in paginatedPedidos" :key="pedido.id">
                        <tr class="hover:bg-gray-50/80 transition-colors group">
                            <td class="py-4 px-6 font-black text-gray-900" x-text="`#${pedido.id}`"></td>
                            <td class="py-4 px-6 text-gray-600 font-medium whitespace-nowrap" x-text="new Date(pedido.creado_en || pedido.created_at).toLocaleDateString('es-EC')"></td>
                            <td class="py-4 px-6 whitespace-nowrap">
                                <span class="px-2.5 py-1 rounded-full text-[10px] font-extrabold uppercase tracking-wider inline-flex items-center gap-1.5 border" 
                                    :class="{
                                        'bg-amber-50 text-amber-800 border-amber-200': pedido.estado.includes('espera'),
                                        'bg-emerald-50 text-emerald-800 border-emerald-200': pedido.estado.includes('entregado'),
                                        'bg-blue-50 text-blue-800 border-blue-200': pedido.estado === 'en_ruta' || pedido.estado === 'listo_para_entregar',
                                        'bg-rose-50 text-rose-800 border-rose-200': pedido.estado === 'cancelado' || pedido.estado === 'no_entregado'
                                    }">
                                    <span class="w-1.5 h-1.5 rounded-full"
                                          :class="{
                                              'bg-amber-500': pedido.estado.includes('espera'),
                                              'bg-emerald-500': pedido.estado.includes('entregado'),
                                              'bg-blue-500': pedido.estado === 'en_ruta' || pedido.estado === 'listo_para_entregar',
                                              'bg-rose-500': pedido.estado === 'cancelado' || pedido.estado === 'no_entregado'
                                          }"></span>
                                    <span x-text="pedido.estado === 'no_entregado' ? 'Devolución / No Entregado' : pedido.estado.replace(/_/g, ' ')"></span>
                                </span>
                            </td>
                            <td class="py-4 px-6 text-gray-600 font-bold uppercase text-[11px]" x-text="(pedido.metodo_pago || '').replace(/_/g, ' ')"></td>
                            <td class="py-4 px-6 text-right font-black text-slate-900 text-sm whitespace-nowrap" x-text="formatMoney(pedido.total)"></td>
                            <td class="py-4 px-6">
                                <div class="flex items-center justify-end flex-wrap gap-1.5">
                                     <!-- Cancelar -->
                                     <button @click="cancelarPedido(pedido)" 
                                             x-show="!['en_ruta', 'listo_para_entregar', 'entregado', 'entregado_parcialmente', 'cancelado', 'no_entregado'].includes(pedido.estado)" 
                                             title="Cancelar Pedido" 
                                             class="text-xs bg-rose-50 hover:bg-rose-100 text-rose-700 border border-rose-200/80 hover:border-rose-300 px-2.5 py-1.5 rounded-xl font-bold transition-all shadow-2xs inline-flex items-center gap-1">
                                         <svg class="w-3.5 h-3.5 text-rose-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/></svg>
                                         <span>Cancelar</span>
                                     </button>

                                     <!-- Nota de Crédito -->
                                     <button @click="verNotaCreditoPdf(pedido)" 
                                             x-show="pedido.factura && pedido.factura.nota_credito" 
                                             title="Ver Nota de Crédito PDF" 
                                             class="text-xs bg-purple-50 hover:bg-purple-100 text-purple-700 border border-purple-200 px-2.5 py-1.5 rounded-xl font-bold transition-all shadow-2xs inline-flex items-center gap-1">
                                         <svg class="w-3.5 h-3.5 text-purple-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 14l6-6m-5.5.5h.01m4.99 5h.01M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16l4-2 4 2 4-2 4 2z"/></svg>
                                         <span>N/C</span>
                                     </button>

                                     <!-- Rastrear -->
                                     <a :href="`/ecommerce/rastreo/${pedido.id}`" 
                                        x-show="pedido.estado === 'en_ruta'" 
                                        title="Rastrear Camión en Vivo" 
                                        class="text-xs bg-amber-500 hover:bg-amber-400 text-slate-950 px-2.5 py-1.5 rounded-xl font-bold transition-all shadow-2xs inline-flex items-center gap-1">
                                         <svg class="w-3.5 h-3.5 text-slate-950 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                                         <span>Rastrear</span>
                                     </a>

                                     <!-- Factura -->
                                     <button @click="verPdf(pedido)" 
                                             title="Ver / Descargar Factura SRI" 
                                             class="text-xs bg-white hover:bg-red-50 text-slate-700 hover:text-red-700 border border-gray-200 px-2.5 py-1.5 rounded-xl font-bold transition-all shadow-2xs inline-flex items-center gap-1">
                                         <svg class="w-3.5 h-3.5 text-red-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                         <span>Factura</span>
                                     </button>

                                     <!-- Detalles -->
                                     <button @click="verDetalle(pedido)" 
                                             title="Ver Detalles Completos de la Orden" 
                                             class="text-xs bg-slate-900 hover:bg-slate-800 text-white px-3 py-1.5 rounded-xl font-bold transition-all shadow-2xs inline-flex items-center gap-1 cursor-pointer">
                                         <svg class="w-3.5 h-3.5 text-white shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                         <span>Detalles</span>
                                     </button>
                                 </div>
                            </td>
                        </tr>
                    </template>
                </tbody>
            </table>
        </div>

        <!-- Paginador Estandarizado Slate -->
        <div x-show="!loading && pedidosFiltrados.length > 0" class="px-6 py-4 border-t border-gray-100 flex flex-col sm:flex-row items-center justify-between gap-4 bg-gray-50/50">
            <div class="text-xs font-semibold text-gray-500">
                Mostrando <span class="font-extrabold text-gray-900" x-text="startRecord"></span> a <span class="font-extrabold text-gray-900" x-text="endRecord"></span> de <span class="font-extrabold text-gray-900" x-text="pedidosFiltrados.length"></span> pedidos
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

    <!-- Modal Detalle Completo de Pedido del Cliente (Paridad Exacta con Gestión de Pedidos) -->
    <div x-show="pedidoSeleccionado" class="fixed inset-0 bg-slate-900/60 backdrop-blur-xs flex items-center justify-center z-50 p-4" style="display: none;">
        <div class="bg-white rounded-2xl w-full max-w-3xl max-h-[90vh] overflow-y-auto p-6 sm:p-8 shadow-2xl relative border border-gray-100" @click.away="pedidoSeleccionado = null">
            
            <!-- Header Modal -->
            <div class="flex items-center justify-between pb-4 border-b border-gray-100 mb-5">
                <div>
                    <div class="flex items-center gap-2 mb-1">
                        <span class="px-2.5 py-0.5 rounded-full text-[10px] font-extrabold uppercase tracking-wider border"
                              :class="{
                                  'bg-amber-50 text-amber-800 border-amber-200': (pedidoSeleccionado?.estado || '').includes('espera'),
                                  'bg-emerald-50 text-emerald-800 border-emerald-200': (pedidoSeleccionado?.estado || '').includes('entregado'),
                                  'bg-blue-50 text-blue-800 border-blue-200': pedidoSeleccionado?.estado === 'en_ruta' || pedidoSeleccionado?.estado === 'listo_para_entregar',
                                  'bg-rose-50 text-rose-800 border-rose-200': pedidoSeleccionado?.estado === 'cancelado' || pedidoSeleccionado?.estado === 'no_entregado'
                              }"
                              x-text="pedidoSeleccionado?.estado === 'no_entregado' ? 'Devolución / No Entregado' : (pedidoSeleccionado?.estado || '').replace(/_/g, ' ')"></span>
                        <span class="text-xs text-gray-400 font-semibold" x-text="pedidoSeleccionado ? new Date(pedidoSeleccionado.creado_en || pedidoSeleccionado.created_at).toLocaleDateString('es-EC') : ''"></span>
                    </div>
                    <h3 class="text-xl font-black text-gray-900">Detalles de Orden #<span x-text="pedidoSeleccionado?.id"></span></h3>
                </div>
                <button @click="pedidoSeleccionado = null" class="text-gray-400 hover:text-gray-600 p-1.5 rounded-xl hover:bg-gray-100 transition-all cursor-pointer">
                    <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
                </button>
            </div>

            <!-- Banner de Cancelación o Devolución si aplica (Solo para cancelados o devueltos) -->
            <template x-if="pedidoSeleccionado?.estado === 'cancelado' || pedidoSeleccionado?.estado === 'no_entregado'">
                <div class="mb-5 bg-rose-50 border border-rose-200 rounded-xl p-4 flex items-start gap-3">
                    <svg class="w-5 h-5 text-rose-600 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                    <div>
                        <h4 class="font-extrabold text-xs text-rose-900 uppercase tracking-wider" x-text="pedidoSeleccionado?.estado === 'no_entregado' ? 'Devolución de Pedido' : 'Orden Cancelada'"></h4>
                        <p class="text-xs text-rose-700 font-medium mt-0.5" x-text="pedidoSeleccionado?.motivo_cancelacion || 'Esta orden registra devolución o cancelación.'"></p>
                    </div>
                </div>
            </template>

            <!-- Grid Información Comercial, Dirección y Pago -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
                <!-- Cliente -->
                <div class="bg-gray-50/80 p-4 rounded-xl border border-gray-100">
                    <span class="text-[10px] font-extrabold uppercase tracking-wider text-gray-400 block mb-1">Comercio / Razón Social</span>
                    <div class="font-bold text-gray-900 text-xs" x-text="pedidoSeleccionado?.cliente?.razon_social || pedidoSeleccionado?.cliente?.nombre_cliente || 'Cliente'"></div>
                </div>

                <!-- Dirección -->
                <div class="bg-gray-50/80 p-4 rounded-xl border border-gray-100">
                    <span class="text-[10px] font-extrabold uppercase tracking-wider text-gray-400 block mb-1">Dirección de Entrega</span>
                    <div class="font-bold text-gray-900 text-xs truncate" x-text="pedidoSeleccionado?.direccion?.descripcion || 'Dirección registrada'"></div>
                </div>

                <!-- Pago y Factura -->
                <div class="bg-gray-50/80 p-4 rounded-xl border border-gray-100">
                    <span class="text-[10px] font-extrabold uppercase tracking-wider text-gray-400 block mb-1">Pago & Documento</span>
                    <div class="font-bold text-gray-900 text-xs capitalize" x-text="`Método: ${(pedidoSeleccionado?.metodo_pago || '').replace(/_/g, ' ')}`"></div>
                    <template x-if="pedidoSeleccionado?.factura">
                        <div class="text-xs font-bold text-emerald-700 mt-1">
                            <span>📄 FAC:</span>
                            <span x-text="pedidoSeleccionado.factura.numero_factura"></span>
                        </div>
                    </template>
                </div>
            </div>

            <!-- Tabla de Ítems Solicitados -->
            <div class="mb-6">
                <h4 class="font-extrabold text-xs uppercase tracking-wider text-gray-500 mb-3">Productos Comprados (Pedido Original)</h4>
                <div class="bg-white rounded-xl border border-gray-100 overflow-hidden shadow-2xs">
                    <table class="w-full text-left text-xs">
                        <thead class="bg-gray-50 border-b border-gray-100 text-[10px] font-extrabold uppercase tracking-wider text-gray-400">
                            <tr>
                                <th class="py-2.5 px-4">Producto</th>
                                <th class="py-2.5 px-4 text-center">Cant. Solicitada</th>
                                <th class="py-2.5 px-4 text-center">Cant. Entregada</th>
                                <th class="py-2.5 px-4 text-right">Precio Unit.</th>
                                <th class="py-2.5 px-4 text-right">Subtotal</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            <template x-if="!pedidoSeleccionado?.items || pedidoSeleccionado.items.length === 0">
                                <tr><td colspan="5" class="py-4 text-center text-gray-400">Sin detalles de productos</td></tr>
                            </template>
                            <template x-for="item in (pedidoSeleccionado?.items || [])" :key="item.id">
                                <tr>
                                    <td class="py-3 px-4 font-bold text-gray-900" x-text="item.nombre_producto || (item.producto ? item.producto.nombre : `Producto #${item.producto_id}`)"></td>
                                    <td class="py-3 px-4 text-center font-bold text-gray-800" x-text="item.cantidad_solicitada"></td>
                                    <td class="py-3 px-4 text-center font-bold text-emerald-700" x-text="item.cantidad_entregada || 0"></td>
                                    <td class="py-3 px-4 text-right text-gray-600 font-medium" x-text="formatMoney(item.precio_unitario)"></td>
                                    <td class="py-3 px-4 text-right font-black text-slate-900" x-text="formatMoney(item.precio_unitario * item.cantidad_solicitada)"></td>
                                </tr>
                            </template>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Tabla de Detalle de Devolución (Solo para pedidos procesados/entregados/cancelados con devoluciones reales) -->
            <template x-if="['entregado', 'entregado_parcialmente', 'no_entregado', 'cancelado'].includes(pedidoSeleccionado?.estado) && pedidoSeleccionado?.items && pedidoSeleccionado.items.some(i => (i.cantidad_solicitada - (i.cantidad_entregada || 0)) > 0)">
                <div class="mb-6">
                    <div class="flex items-center gap-2 mb-3">
                        <svg class="h-4 w-4 text-rose-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 15v-1a4 4 0 00-4-4H8m0 0l3 3m-3-3l3-3m9 14V5a2 2 0 00-2-2H6a2 2 0 00-2 2v16l4-2 4 2 4-2 4 2z" />
                        </svg>
                        <h4 class="font-extrabold text-xs uppercase tracking-wider text-rose-700">Detalle de Devolución</h4>
                    </div>
                    <div class="bg-rose-50/40 rounded-xl border border-rose-200 overflow-hidden shadow-2xs">
                        <table class="w-full text-left text-xs">
                            <thead class="bg-rose-100/60 border-b border-rose-200 text-[10px] font-extrabold uppercase tracking-wider text-rose-800">
                                <tr>
                                    <th class="py-2.5 px-4">Producto Devuelto</th>
                                    <th class="py-2.5 px-4 text-center">Cant. Devuelta</th>
                                    <th class="py-2.5 px-4 text-right">Precio Unit.</th>
                                    <th class="py-2.5 px-4 text-right">Valor Devuelto ($)</th>
                                    <th class="py-2.5 px-4">Motivo de Devolución</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-rose-100">
                                <template x-for="item in (pedidoSeleccionado?.items || []).filter(i => (i.cantidad_solicitada - (i.cantidad_entregada || 0)) > 0)" :key="'dev_' + item.id">
                                    <tr class="hover:bg-rose-50/80">
                                        <td class="py-3 px-4 font-bold text-gray-900" x-text="item.nombre_producto || (item.producto ? item.producto.nombre : `Producto #${item.producto_id}`)"></td>
                                        <td class="py-3 px-4 text-center font-black text-rose-700" x-text="item.cantidad_solicitada - (item.cantidad_entregada || 0)"></td>
                                        <td class="py-3 px-4 text-right text-gray-600 font-medium" x-text="formatMoney(item.precio_unitario)"></td>
                                        <td class="py-3 px-4 text-right font-black text-rose-700" x-text="formatMoney((item.cantidad_solicitada - (item.cantidad_entregada || 0)) * item.precio_unitario * 1.15)"></td>
                                        <td class="py-3 px-4">
                                            <span class="inline-block bg-rose-100 border border-rose-200 text-rose-800 px-2 py-0.5 rounded text-[10px] font-extrabold"
                                                  x-text="item.motivo_devolucion || pedidoSeleccionado.motivo_cancelacion || 'Otro motivo'"></span>
                                        </td>
                                    </tr>
                                </template>
                            </tbody>
                        </table>
                    </div>
                </div>
            </template>

            <!-- Resumen Financiero y Comprobante (Idéntico a Gestión de Pedidos) -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 items-start mb-6">
                <!-- Vista Previa de Comprobante / Documento -->
                <div>
                    <h4 class="font-extrabold text-xs uppercase tracking-wider text-gray-500 mb-2">Comprobante de Pago Adjunto</h4>
                    <div class="bg-gray-50 rounded-xl flex items-center justify-center min-h-[180px] relative border border-gray-200/80 p-4 overflow-hidden">
                        
                        <!-- Loading State -->
                        <div x-show="loadingComprobante" class="absolute inset-0 flex items-center justify-center bg-white/80 backdrop-blur-xs">
                            <span class="text-xs font-bold text-gray-600 flex items-center gap-2">
                                <svg class="animate-spin h-4 w-4 text-slate-800" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8z"></path></svg>
                                Cargando comprobante...
                            </span>
                        </div>
                        
                        <!-- Sin comprobante -->
                        <div x-show="!loadingComprobante && !comprobanteUrl" class="text-gray-400 text-center p-4">
                            <svg class="w-10 h-10 mx-auto text-gray-300 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                            <span class="text-xs font-semibold">No hay comprobante adjunto o es un método de pago directo.</span>
                        </div>

                        <!-- Con comprobante -->
                        <template x-if="!loadingComprobante && comprobanteUrl">
                            <div class="text-center">
                                <!-- PDF -->
                                <template x-if="comprobanteUrl.split('?')[0].toLowerCase().endsWith('.pdf')">
                                    <div class="bg-white p-4 rounded-xl border border-gray-200 shadow-2xs">
                                        <svg class="w-10 h-10 mx-auto text-rose-500 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path></svg>
                                        <p class="text-xs text-gray-700 mb-3 font-semibold">Comprobante de Pago (PDF)</p>
                                        <a :href="comprobanteUrl" target="_blank" class="inline-block bg-slate-900 text-white px-4 py-2 rounded-xl text-xs font-bold hover:bg-slate-800 transition-all shadow-2xs">
                                            Abrir Documento PDF ↗
                                        </a>
                                    </div>
                                </template>
                                <!-- Imagen -->
                                <template x-if="!comprobanteUrl.split('?')[0].toLowerCase().endsWith('.pdf')">
                                    <div class="space-y-2">
                                        <a :href="comprobanteUrl" target="_blank" title="Haz clic para abrir en otra pestaña">
                                            <img :src="comprobanteUrl" class="max-w-full max-h-[220px] object-contain rounded-xl shadow-md hover:opacity-90 transition-opacity mx-auto bg-white p-1" />
                                        </a>
                                        <p class="text-[10px] text-gray-400 font-semibold">💡 Haz clic para ver en tamaño completo.</p>
                                    </div>
                                </template>
                            </div>
                        </template>
                    </div>
                </div>

                <!-- Totales del Pedido -->
                <div class="bg-gray-50/80 p-4 rounded-2xl border border-gray-100 space-y-2 text-xs">
                    <div class="flex justify-between font-semibold text-gray-600">
                        <span>Subtotal:</span>
                        <span x-text="formatMoney(pedidoSeleccionado?.subtotal)"></span>
                    </div>
                    <div class="flex justify-between font-semibold text-gray-600">
                        <span>Descuento:</span>
                        <span class="text-rose-600" x-text="`-${formatMoney(pedidoSeleccionado?.descuento)}`"></span>
                    </div>
                    <div class="flex justify-between font-semibold text-gray-600">
                        <span>IVA (15%):</span>
                        <span x-text="formatMoney(pedidoSeleccionado?.iva)"></span>
                    </div>
                    <div class="border-t border-gray-200 pt-2 flex justify-between font-black text-gray-900 text-base">
                        <span>Total Final:</span>
                        <span class="text-slate-900" x-text="formatMoney(pedidoSeleccionado?.total)"></span>
                    </div>
                </div>
            </div>

            <!-- Banner Nota de Crédito si existe -->
            <template x-if="pedidoSeleccionado?.factura?.nota_credito">
                <div class="mb-6 bg-purple-50 p-4 rounded-xl border border-purple-200">
                    <h4 class="font-extrabold text-purple-900 text-xs uppercase tracking-wider mb-2 flex items-center gap-2">
                        <svg class="h-4 w-4 text-purple-700" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 14l6-6m-5.5.5h.01m4.99 5h.01M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16l4-2 4 2 4-2 4 2z" />
                        </svg>
                        Nota de Crédito Oficial SRI Emitida
                    </h4>
                    <div class="grid grid-cols-2 gap-2 text-xs text-purple-800 font-medium">
                        <div><span class="font-bold">N° de Nota:</span> <span class="font-mono font-bold" x-text="pedidoSeleccionado.factura.nota_credito.numero_nota"></span></div>
                        <div><span class="font-bold">Monto Ajustado:</span> $<span class="font-extrabold" x-text="Number(pedidoSeleccionado.factura.nota_credito.valor_total).toFixed(2)"></span></div>
                        <div class="col-span-2"><span class="font-bold">Motivo:</span> <span x-text="pedidoSeleccionado.factura.nota_credito.motivo"></span></div>
                    </div>
                </div>
            </template>
            
            <!-- Acciones -->
            <div class="flex items-center justify-between flex-wrap gap-2.5 pt-4 border-t border-gray-100">
                <div>
                    <template x-if="comprobanteUrl">
                        <a :href="comprobanteUrl" target="_blank" class="bg-amber-50 hover:bg-amber-100 text-amber-900 border border-amber-200 px-4 py-2 rounded-xl text-xs font-extrabold transition-all shadow-2xs flex items-center gap-1.5">
                            🖼️ Ver Comprobante Original ↗
                        </a>
                    </template>
                </div>
                <div class="flex items-center gap-2.5">
                    <button @click="verPdf(pedidoSeleccionado)" class="bg-white hover:bg-red-50 text-slate-800 hover:text-red-700 border border-gray-200 px-4 py-2 rounded-xl text-xs font-extrabold transition-all shadow-2xs">
                        📄 Descargar Factura
                    </button>
                    <template x-if="pedidoSeleccionado?.factura?.nota_credito">
                        <button @click="verNotaCreditoPdf(pedidoSeleccionado)" class="bg-purple-600 hover:bg-purple-700 text-white px-4 py-2 rounded-xl text-xs font-extrabold transition-all shadow-2xs">
                            🧾 Descargar Nota de Crédito
                        </button>
                    </template>
                    <button @click="pedidoSeleccionado = null" class="bg-slate-900 hover:bg-slate-800 text-white px-5 py-2 rounded-xl text-xs font-extrabold transition-all shadow-xs cursor-pointer">
                        Cerrar
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('alpine:init', () => {
    Alpine.data('historial', () => ({
        fechaInicio: '',
        fechaFin: '',
        maxFechaFin: '',
        pedidosOriginales: [],
        pedidosFiltrados: [],
        pedidoSeleccionado: null,
        comprobanteUrl: null,
        loadingComprobante: false,
        perPage: 10,
        currentPage: 1,
        loading: false,

        async init() {
            if (!localStorage.getItem('jwt_token')) {
                window.location.href = '/auth/login';
                return;
            }

            const hoy = new Date();
            const haceUnMes = new Date();
            haceUnMes.setMonth(hoy.getMonth() - 1);

            this.fechaFin = hoy.toISOString().split('T')[0];
            this.fechaInicio = haceUnMes.toISOString().split('T')[0];
            this.updateMaxFechaFin();

            this.loading = true;
            try {
                let clienteData = await window.api('/api/clientes/me');
                if (clienteData && clienteData.data) clienteData = clienteData.data;
                if (clienteData && clienteData.id) {
                    const response = await window.api(`/api/clientes/${clienteData.id}/pedidos`);
                    this.pedidosOriginales = response.data || response || [];
                    this.filtrar();
                }
            } catch (error) {
                console.error("Error al cargar historial:", error);
            } finally {
                this.loading = false;
            }
        },

        updateMaxFechaFin() {
            if (!this.fechaInicio) return;
            const fInicio = new Date(this.fechaInicio + 'T00:00:00');
            const maxFin = new Date(fInicio);
            maxFin.setMonth(maxFin.getMonth() + 1);
            this.maxFechaFin = maxFin.toISOString().split('T')[0];
        },

        onFechaInicioChange() {
            if (!this.fechaInicio) return;
            this.updateMaxFechaFin();
            if (this.fechaFin && this.fechaFin > this.maxFechaFin) {
                this.fechaFin = this.maxFechaFin;
            }
            this.currentPage = 1;
            this.filtrar();
        },

        presetPeriodo(tipo) {
            const hoy = new Date();
            this.fechaFin = hoy.toISOString().split('T')[0];
            
            if (tipo === 'MES') {
                const haceUnMes = new Date();
                haceUnMes.setMonth(hoy.getMonth() - 1);
                this.fechaInicio = haceUnMes.toISOString().split('T')[0];
            } else if (tipo === 'SEMANA') {
                const haceUnaSemana = new Date();
                haceUnaSemana.setDate(hoy.getDate() - 7);
                this.fechaInicio = haceUnaSemana.toISOString().split('T')[0];
            } else if (tipo === 'HOY') {
                this.fechaInicio = hoy.toISOString().split('T')[0];
            }
            this.onFechaInicioChange();
        },

        esPeriodo(tipo) {
            const hoy = new Date().toISOString().split('T')[0];
            if (this.fechaFin !== hoy) return false;
            
            if (tipo === 'HOY') return this.fechaInicio === hoy;
            if (tipo === 'SEMANA') {
                const haceUnaSemana = new Date();
                haceUnaSemana.setDate(new Date().getDate() - 7);
                return this.fechaInicio === haceUnaSemana.toISOString().split('T')[0];
            }
            if (tipo === 'MES') {
                const haceUnMes = new Date();
                haceUnMes.setMonth(new Date().getMonth() - 1);
                return this.fechaInicio === haceUnMes.toISOString().split('T')[0];
            }
            return false;
        },

        filtrar() {
            if (!this.fechaInicio && !this.fechaFin) {
                this.pedidosFiltrados = [...this.pedidosOriginales];
            } else {
                this.pedidosFiltrados = this.pedidosOriginales.filter(pedido => {
                    const pedidoFecha = new Date(pedido.creado_en || pedido.fecha || pedido.created_at);
                    let valido = true;

                    if (this.fechaInicio) {
                        const inicio = new Date(this.fechaInicio + 'T00:00:00');
                        if (pedidoFecha < inicio) valido = false;
                    }
                    
                    if (this.fechaFin) {
                        const fin = new Date(this.fechaFin + 'T23:59:59');
                        if (pedidoFecha > fin) valido = false;
                    }

                    return valido;
                });
            }
            this.currentPage = 1;
        },

        limpiarFiltros() {
            const hoy = new Date();
            const haceUnMes = new Date();
            haceUnMes.setMonth(hoy.getMonth() - 1);
            this.fechaFin = hoy.toISOString().split('T')[0];
            this.fechaInicio = haceUnMes.toISOString().split('T')[0];
            this.updateMaxFechaFin();
            this.filtrar();
        },

        get totalPages() {
            return Math.ceil(this.pedidosFiltrados.length / this.perPage) || 1;
        },

        get paginatedPedidos() {
            const start = (this.currentPage - 1) * this.perPage;
            return this.pedidosFiltrados.slice(start, start + this.perPage);
        },

        get startRecord() {
            if (this.pedidosFiltrados.length === 0) return 0;
            return (this.currentPage - 1) * this.perPage + 1;
        },

        get endRecord() {
            return Math.min(this.currentPage * this.perPage, this.pedidosFiltrados.length);
        },

        prevPage() {
            if (this.currentPage > 1) this.currentPage--;
        },

        nextPage() {
            if (this.currentPage < this.totalPages) this.currentPage++;
        },

        goToPage(p) {
            if (p >= 1 && p <= this.totalPages) this.currentPage = p;
        },

        async verDetalle(pedido) {
            this.pedidoSeleccionado = pedido;
            this.comprobanteUrl = null;
            this.loadingComprobante = true;

            try {
                const res = await window.api(`/api/pedidos/${pedido.id}`);
                const fullObj = res?.data || res;
                if (fullObj && fullObj.id) {
                    this.pedidoSeleccionado = fullObj;
                }
            } catch (e) {
                console.warn("Utilizando datos en memoria del pedido:", e);
            }

            const p = this.pedidoSeleccionado;
            const pagoUpper = (p.metodo_pago || p.pago || '').toUpperCase();
            
            if (p.comprobante_path || pagoUpper.includes('DE_UNA') || pagoUpper.includes('DEPOSITO') || pagoUpper.includes('DEPOSIT')) {
                try {
                    const compRes = await window.api(`/api/pedidos/${p.id}/comprobante`);
                    this.comprobanteUrl = compRes?.data?.url || compRes?.url || null;
                } catch (e) {
                    console.log("Sin comprobante adjunto o error al obtener URL:", e);
                }
            }
            this.loadingComprobante = false;
        },

        async cancelarPedido(pedido) {
            let opcionesMotivos = {
                'Cambio de opinión': 'Cambio de opinión',
                'Error en dirección o datos': 'Error en dirección o datos',
                'Precio muy alto': 'Precio muy alto',
                'Tiempo de entrega muy largo': 'Tiempo de entrega muy largo',
                'Producto equivocado': 'Producto equivocado',
                'Otro': 'Otro motivo'
            };

            try {
                const res = await window.api('/api/catalogo-motivos?tipo=cancelacion');
                if (res && res.data && res.data.length > 0) {
                    opcionesMotivos = {};
                    res.data.forEach(m => { opcionesMotivos[m.descripcion] = m.descripcion; });
                }
            } catch (_) {}

            const { value: motivoSeleccionado } = await Swal.fire({
                title: 'Cancelar Pedido #' + pedido.id,
                text: 'Selecciona obligatoriamente el motivo de la cancelación:',
                input: 'select',
                inputOptions: opcionesMotivos,
                inputPlaceholder: '-- Selecciona un motivo --',
                showCancelButton: true,
                confirmButtonColor: '#E3001B',
                cancelButtonColor: '#9CA3AF',
                confirmButtonText: 'Confirmar Cancelación',
                cancelButtonText: 'Volver',
                inputValidator: (value) => {
                    return !value && 'Debes seleccionar un motivo para continuar';
                }
            });

            if (motivoSeleccionado) {
                try {
                    await window.api(`/api/pedidos/${pedido.id}/cancelar`, { 
                        method: 'PATCH',
                        body: JSON.stringify({ motivo: motivoSeleccionado })
                    });
                    Swal.fire({ icon: 'success', title: 'Cancelado', text: 'El pedido ha sido cancelado correctamente.', toast: true, position: 'bottom', showConfirmButton: false, timer: 3000 });
                    this.init();
                } catch (e) {
                    Swal.fire('Error', e.message, 'error');
                }
            }
        },

        verPdf(pedido) {
            if(!window.generateFactura) {
                console.error("Generador de PDF no cargado");
                return;
            }
            const facturaData = {
                numero: pedido.factura ? pedido.factura.numero_factura : pedido.id,
                clienteNombre: pedido.cliente ? (pedido.cliente.razon_social || pedido.cliente.nombre_cliente || 'Consumidor Final') : 'Consumidor Final',
                clienteRuc: pedido.cliente ? (pedido.cliente.ruc_cedula || '9999999999999') : '9999999999999',
                clienteDireccion: pedido.direccion ? pedido.direccion.descripcion : 'S/N',
                clienteTelefono: pedido.cliente ? (pedido.cliente.telefono || '') : '',
                metodoPago: pedido.metodo_pago,
                fecha: new Date(pedido.creado_en || pedido.created_at).toLocaleDateString('es-EC'),
                subtotal: Number(pedido.subtotal).toFixed(2),
                descuento: Number(pedido.descuento).toFixed(2),
                iva: Number(pedido.iva).toFixed(2),
                total: Number(pedido.total).toFixed(2),
                items: (pedido.items || []).map(item => ({
                    nombre: item.nombre_producto || (item.producto ? item.producto.nombre : 'Producto ' + item.producto_id),
                    cantidad: item.cantidad_solicitada,
                    precioUnitario: item.precio_unitario
                }))
            };
            window.generateFactura(facturaData);
        },

        verNotaCreditoPdf(pedido) {
            if(!window.generateNotaCredito) {
                console.error("Generador de Nota de Crédito no cargado");
                return;
            }
            const nc = pedido.factura ? pedido.factura.nota_credito : null;
            if (!nc) {
                alert("No hay Nota de Crédito generada para este pedido.");
                return;
            }
            const notaData = {
                id: nc.id,
                numeroNota: nc.numero_nota,
                facturaNumero: pedido.factura ? pedido.factura.numero_factura : `FAC-${pedido.id}`,
                clienteNombre: pedido.cliente ? (pedido.cliente.razon_social || pedido.cliente.nombre_cliente || 'Consumidor Final') : 'Consumidor Final',
                clienteRuc: pedido.cliente ? (pedido.cliente.ruc_cedula || '9999999999999') : '9999999999999',
                clienteDireccion: pedido.direccion ? pedido.direccion.descripcion : 'S/N',
                clienteTelefono: pedido.cliente ? (pedido.cliente.telefono || '') : '',
                motivo: nc.motivo || pedido.motivo_cancelacion || 'Devolución Total',
                fecha: new Date(nc.fecha_emision || nc.created_at || new Date()).toLocaleDateString('es-EC'),
                subtotal: Number(pedido.subtotal).toFixed(2),
                descuento: Number(pedido.descuento).toFixed(2),
                iva: Number(pedido.iva).toFixed(2),
                total: Number(nc.valor_total || pedido.total).toFixed(2),
                items: (pedido.items || []).map(item => ({
                    nombre: item.nombre_producto || (item.producto ? item.producto.nombre : 'Producto ' + item.producto_id),
                    cantidad: item.cantidad_solicitada,
                    precioUnitario: item.precio_unitario
                }))
            };
            window.generateNotaCredito(notaData);
        }
    }));
});
</script>
@endsection
