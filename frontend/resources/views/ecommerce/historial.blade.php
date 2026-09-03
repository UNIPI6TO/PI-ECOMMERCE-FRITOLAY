@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto py-8 px-4" x-data="historial()">
    <!-- Header Page Title & Summary Cards -->
    <div class="mb-8 flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div>
            <h1 class="text-3xl font-extrabold text-gray-900 tracking-tight flex items-center gap-3">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-[#E3001B]" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01" />
                </svg>
                Historial de Pedidos
            </h1>
            <p class="text-sm text-gray-500 mt-1">Consulta tus compras realizadas, rastrea entregas en ruta y descarga tus comprobantes oficiales SRI.</p>
        </div>
    </div>

    <!-- Metrics Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-8">
        <div class="bg-white p-5 rounded-xl border border-gray-100 shadow-sm flex items-center gap-4">
            <div class="p-3 bg-slate-100 text-slate-700 rounded-lg">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 11h14l1 12H4L5 11z" />
                </svg>
            </div>
            <div>
                <p class="text-xs font-semibold uppercase text-gray-400">Pedidos en Rango</p>
                <p class="text-2xl font-extrabold text-gray-900" x-text="pedidosFiltrados.length"></p>
            </div>
        </div>

        <div class="bg-white p-5 rounded-xl border border-gray-100 shadow-sm flex items-center gap-4">
            <div class="p-3 bg-emerald-50 text-emerald-600 rounded-lg">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                </svg>
            </div>
            <div>
                <p class="text-xs font-semibold uppercase text-gray-400">Entregados</p>
                <p class="text-2xl font-extrabold text-gray-900" x-text="pedidosFiltrados.filter(p => p.estado.includes('entregado')).length"></p>
            </div>
        </div>

        <div class="bg-white p-5 rounded-xl border border-gray-100 shadow-sm flex items-center gap-4">
            <div class="p-3 bg-amber-50 text-amber-600 rounded-lg">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
            </div>
            <div>
                <p class="text-xs font-semibold uppercase text-gray-400">Total Facturado</p>
                <p class="text-2xl font-extrabold text-gray-900" x-text="formatMoney(pedidosFiltrados.reduce((acc, p) => acc + (parseFloat(p.total) || 0), 0))"></p>
            </div>
        </div>
    </div>

    <!-- Filters & Pagination Toolbar Bar -->
    <div class="bg-white p-5 rounded-xl border border-gray-100 shadow-sm mb-6 space-y-4">
        <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-4">
            <!-- Date Filter Inputs -->
            <div class="flex flex-wrap items-center gap-3">
                <div class="flex items-center gap-2">
                    <span class="text-xs font-bold uppercase text-gray-500">Desde:</span>
                    <input type="date" x-model="fechaInicio" @change="onFechaInicioChange()" class="border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-slate-800 focus:border-slate-800 outline-none shadow-xs">
                </div>
                <div class="flex items-center gap-2">
                    <span class="text-xs font-bold uppercase text-gray-500">Hasta:</span>
                    <input type="date" x-model="fechaFin" :max="maxFechaFin" @change="filtrar()" class="border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-slate-800 focus:border-slate-800 outline-none shadow-xs">
                </div>

                <!-- Presets -->
                <div class="flex items-center bg-gray-100 p-1 rounded-lg text-xs font-semibold">
                    <button @click="presetPeriodo('MES')" class="px-3 py-1.5 rounded-md transition-all" :class="esPeriodo('MES') ? 'bg-white text-gray-900 shadow-xs font-bold' : 'text-gray-600 hover:text-gray-900'">Último Mes</button>
                    <button @click="presetPeriodo('SEMANA')" class="px-3 py-1.5 rounded-md transition-all" :class="esPeriodo('SEMANA') ? 'bg-white text-gray-900 shadow-xs font-bold' : 'text-gray-600 hover:text-gray-900'">Última Semana</button>
                    <button @click="presetPeriodo('HOY')" class="px-3 py-1.5 rounded-md transition-all" :class="esPeriodo('HOY') ? 'bg-white text-gray-900 shadow-xs font-bold' : 'text-gray-600 hover:text-gray-900'">Hoy</button>
                </div>
            </div>

            <!-- Action Buttons & PerPage Selector -->
            <div class="flex items-center justify-between lg:justify-end gap-3 border-t lg:border-t-0 pt-3 lg:pt-0">
                <div class="flex items-center gap-2">
                    <button @click="filtrar()" class="bg-slate-900 hover:bg-black text-white text-sm font-bold px-4 py-2 rounded-lg transition-colors shadow-xs flex items-center gap-1.5">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z" />
                        </svg>
                        Filtrar
                    </button>
                    <button @click="limpiarFiltros()" class="bg-gray-100 hover:bg-gray-200 text-gray-700 text-sm font-semibold px-3 py-2 rounded-lg transition-colors">
                        Restablecer
                    </button>
                </div>

                <!-- Per Page Selector -->
                <div class="flex items-center gap-2 border-l pl-3 border-gray-200">
                    <span class="text-xs font-semibold text-gray-500 hidden sm:inline">Mostrar:</span>
                    <select x-model.number="perPage" @change="currentPage = 1" class="border border-gray-300 rounded-lg px-2.5 py-1.5 text-sm font-medium focus:ring-2 focus:ring-slate-800 focus:border-slate-800 outline-none shadow-xs bg-white cursor-pointer">
                        <option value="10">10</option>
                        <option value="20">20</option>
                        <option value="50">50</option>
                        <option value="100">100</option>
                    </select>
                    <span class="text-xs font-semibold text-gray-500 hidden sm:inline">registros</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Table Container -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-gray-50/80 border-b border-gray-200 text-[11px] font-extrabold uppercase tracking-wider text-gray-500">
                        <th class="py-4 px-6"># Pedido</th>
                        <th class="py-4 px-6">Fecha</th>
                        <th class="py-4 px-6">Estado</th>
                        <th class="py-4 px-6">Método Pago</th>
                        <th class="py-4 px-6 text-right">Total</th>
                        <th class="py-4 px-6 text-right">Acciones</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 text-sm">
                    <!-- Loading Spinner -->
                    <tr x-show="loading">
                        <td colspan="6" class="py-12 text-center text-gray-500">
                            <div class="inline-flex items-center gap-3">
                                <svg class="animate-spin h-6 w-6 text-slate-800" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                </svg>
                                <span class="font-medium text-gray-700">Cargando historial...</span>
                            </div>
                        </td>
                    </tr>

                    <!-- Empty State -->
                    <tr x-show="!loading && paginatedPedidos.length === 0">
                        <td colspan="6" class="py-12 text-center text-gray-500">
                            <div class="max-w-xs mx-auto">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12 mx-auto text-gray-300 mb-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4" />
                                </svg>
                                <p class="font-bold text-gray-800">No se encontraron pedidos</p>
                                <p class="text-xs text-gray-400 mt-1">Prueba seleccionando otro rango de fechas.</p>
                            </div>
                        </td>
                    </tr>

                    <!-- Rows -->
                    <template x-for="pedido in paginatedPedidos" :key="pedido.id">
                        <tr class="hover:bg-gray-50/80 transition-colors">
                            <td class="py-4 px-6 font-bold text-gray-900">
                                #<span x-text="pedido.id"></span>
                            </td>
                            <td class="py-4 px-6 text-gray-600 font-medium whitespace-nowrap" x-text="new Date(pedido.creado_en || pedido.created_at).toLocaleDateString('es-EC')"></td>
                            <td class="py-4 px-6 whitespace-nowrap">
                                <span class="px-2.5 py-1 rounded-full text-xs font-extrabold uppercase tracking-wide inline-flex items-center gap-1.5" 
                                    :class="{
                                        'bg-amber-50 text-amber-700 border border-amber-200': pedido.estado.includes('espera'),
                                        'bg-emerald-50 text-emerald-700 border border-emerald-200': pedido.estado.includes('entregado'),
                                        'bg-blue-50 text-blue-700 border border-blue-200': pedido.estado === 'en_ruta' || pedido.estado === 'listo_para_entregar',
                                        'bg-rose-50 text-rose-700 border border-rose-200': pedido.estado === 'cancelado' || pedido.estado === 'no_entregado'
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
                            <td class="py-4 px-6 text-gray-600 font-semibold uppercase text-xs" x-text="(pedido.metodo_pago || '').replace(/_/g, ' ')"></td>
                            <td class="py-4 px-6 text-right font-extrabold text-gray-900 text-base whitespace-nowrap" x-text="formatMoney(pedido.total)"></td>
                            <td class="py-4 px-6 text-right">
                                <div class="flex items-center justify-end flex-wrap gap-1.5">
                                    <!-- 1. Al Inicio: Nota de Crédito (si aplica) -->
                                    <button @click="verNotaCreditoPdf(pedido)" x-show="pedido.factura && pedido.factura.nota_credito" class="text-xs bg-white hover:bg-purple-50 text-slate-700 hover:text-purple-700 border border-slate-200 hover:border-purple-300 px-3 py-1.5 rounded-lg font-bold transition-all shadow-xs inline-flex items-center gap-1.5">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5 text-purple-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 14l6-6m-5.5.5h.01m4.99 5h.01M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16l4-2 4 2 4-2 4 2z" />
                                        </svg>
                                        N/C
                                    </button>

                                    <a :href="`/ecommerce/rastreo/${pedido.id}`" x-show="pedido.estado === 'en_ruta'" class="text-xs bg-amber-50/80 hover:bg-amber-100 text-amber-900 border border-amber-200/80 px-3 py-1.5 rounded-lg font-bold transition-all inline-flex items-center gap-1.5">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5 text-amber-700" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                                        </svg>
                                        Rastrear
                                    </a>

                                    <!-- 2. En Medio: Factura -->
                                    <button @click="verPdf(pedido)" class="text-xs bg-white hover:bg-red-50 text-slate-700 hover:text-red-700 border border-slate-200 hover:border-red-300 px-3 py-1.5 rounded-lg font-bold transition-all shadow-xs inline-flex items-center gap-1.5">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5 text-red-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                        </svg>
                                        Factura
                                    </button>

                                    <!-- 3. Al Final: Detalles -->
                                    <button @click="verDetalle(pedido)" class="text-xs bg-white hover:bg-slate-100 text-slate-700 border border-slate-200 px-3 py-1.5 rounded-lg font-bold transition-all shadow-xs inline-flex items-center gap-1.5">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5 text-slate-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                        </svg>
                                        Detalles
                                    </button>

                                    <button @click="cancelarPedido(pedido)" x-show="!['en_ruta', 'listo_para_entregar', 'entregado', 'entregado_parcialmente', 'cancelado', 'no_entregado'].includes(pedido.estado)" class="text-xs bg-white hover:bg-rose-50 text-slate-600 hover:text-rose-700 border border-slate-200 hover:border-rose-300 px-3 py-1.5 rounded-lg font-bold transition-all">
                                        Cancelar
                                    </button>
                                </div>
                            </td>
                        </tr>
                    </template>
                </tbody>
            </table>
        </div>

        <!-- Pagination Controls Footer Bar -->
        <div x-show="!loading && pedidosFiltrados.length > 0" class="bg-gray-50/80 border-t border-gray-200 px-6 py-4 flex flex-col sm:flex-row items-center justify-between gap-4">
            <div class="text-xs font-semibold text-gray-600">
                Mostrando <span class="font-extrabold text-gray-900" x-text="startRecord"></span> a <span class="font-extrabold text-gray-900" x-text="endRecord"></span> de <span class="font-extrabold text-gray-900" x-text="pedidosFiltrados.length"></span> pedidos
            </div>

            <!-- Page Buttons -->
            <div class="flex items-center gap-1">
                <button @click="prevPage()" :disabled="currentPage === 1" class="px-3 py-1.5 rounded-lg border text-xs font-bold transition-all disabled:opacity-40 disabled:cursor-not-allowed bg-white hover:bg-gray-100 text-gray-700 border-gray-300">
                    Anterior
                </button>

                <template x-for="p in totalPages" :key="p">
                    <button @click="goToPage(p)" class="w-8 h-8 rounded-lg text-xs font-extrabold transition-all"
                            :class="currentPage === p ? 'bg-slate-900 text-white shadow-xs' : 'bg-white hover:bg-gray-100 text-gray-700 border border-gray-300'"
                            x-text="p"></button>
                </template>

                <button @click="nextPage()" :disabled="currentPage === totalPages" class="px-3 py-1.5 rounded-lg border text-xs font-bold transition-all disabled:opacity-40 disabled:cursor-not-allowed bg-white hover:bg-gray-100 text-gray-700 border-gray-300">
                    Siguiente
                </button>
            </div>
        </div>
    </div>

    <!-- Modal Detalles -->
    <div x-show="pedidoSeleccionado" class="fixed inset-0 bg-black/60 backdrop-blur-xs flex items-center justify-center z-50 p-4" style="display: none;">
        <div class="bg-white rounded-2xl w-full max-w-2xl p-6 relative max-h-[90vh] overflow-y-auto shadow-2xl border border-gray-100" @click.away="pedidoSeleccionado = null">
            <button @click="pedidoSeleccionado = null" class="absolute top-4 right-4 text-gray-400 hover:text-gray-900 bg-gray-100 hover:bg-gray-200 p-1.5 rounded-full transition-colors">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>

            <div class="flex items-center gap-3 mb-6">
                <div class="p-3 bg-slate-100 text-slate-800 rounded-xl">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                </div>
                <div>
                    <h2 class="text-xl font-extrabold text-gray-900">Detalles del Pedido #<span x-text="pedidoSeleccionado?.id"></span></h2>
                    <p class="text-xs text-gray-500">Resumen detallado de productos y comprobante tributario</p>
                </div>
            </div>
            
            <div class="mb-6 text-sm text-gray-700 grid grid-cols-2 gap-4 bg-gray-50 p-4 rounded-xl border border-gray-100">
                <div><span class="font-bold text-gray-500 text-xs uppercase block">Fecha Emisión</span> <span class="font-semibold text-gray-900" x-text="pedidoSeleccionado ? new Date(pedidoSeleccionado.creado_en || pedidoSeleccionado.created_at).toLocaleString('es-EC') : ''"></span></div>
                <div><span class="font-bold text-gray-500 text-xs uppercase block">Estado</span> <span class="uppercase font-bold text-gray-900" x-text="pedidoSeleccionado?.estado === 'no_entregado' ? 'Devolución' : pedidoSeleccionado?.estado.replace(/_/g, ' ')"></span></div>
                <div><span class="font-bold text-gray-500 text-xs uppercase block">Método de Pago</span> <span class="uppercase font-semibold text-gray-900" x-text="(pedidoSeleccionado?.metodo_pago || '').replace(/_/g, ' ')"></span></div>
                <div><span class="font-bold text-gray-500 text-xs uppercase block">Subtotal</span> <span class="font-semibold text-gray-900" x-text="formatMoney(pedidoSeleccionado?.subtotal)"></span></div>
                <div><span class="font-bold text-gray-500 text-xs uppercase block">IVA 15%</span> <span class="font-semibold text-gray-900" x-text="formatMoney(pedidoSeleccionado?.iva)"></span></div>
                <div><span class="font-bold text-gray-500 text-xs uppercase block">Descuento</span> <span class="font-semibold text-gray-900" x-text="formatMoney(pedidoSeleccionado?.descuento)"></span></div>
                <div class="col-span-2 pt-2 border-t border-gray-200 flex justify-between items-center"><span class="font-bold text-gray-900">Total Pedido:</span> <span class="text-xl font-extrabold text-slate-900" x-text="formatMoney(pedidoSeleccionado?.total)"></span></div>
            </div>

            <h3 class="font-bold text-base text-gray-900 mb-3 flex items-center gap-2">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                </svg>
                Productos Solicitados
            </h3>
            <div class="border rounded-xl overflow-hidden mb-6">
                <ul class="divide-y divide-gray-100">
                    <template x-for="item in pedidoSeleccionado?.items" :key="item.id">
                        <li class="p-3.5 flex justify-between items-center hover:bg-gray-50/50">
                            <div>
                                <span class="font-bold text-gray-900" x-text="item.producto ? item.producto.nombre : 'Producto #' + item.producto_id"></span>
                                <div class="text-xs text-gray-500 font-medium">Cantidad: <span class="font-bold text-gray-800" x-text="item.cantidad_solicitada"></span> unidades</div>
                            </div>
                            <div class="text-right">
                                <div class="font-extrabold text-gray-900" x-text="formatMoney(item.precio_unitario * item.cantidad_solicitada)"></div>
                                <div class="text-[11px] text-gray-400 font-medium">(<span x-text="formatMoney(item.precio_unitario)"></span> c/u)</div>
                            </div>
                        </li>
                    </template>
                </ul>
            </div>

            <template x-if="pedidoSeleccionado?.factura?.nota_credito">
                <div class="mt-4 mb-6 bg-purple-50 p-4 rounded-xl border border-purple-200">
                    <h4 class="font-extrabold text-purple-900 mb-2 flex items-center gap-2">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-purple-700" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 14l6-6m-5.5.5h.01m4.99 5h.01M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16l4-2 4 2 4-2 4 2z" />
                        </svg>
                        Nota de Crédito Oficial SRI
                    </h4>
                    <div class="grid grid-cols-2 gap-2 text-xs text-purple-800 font-medium">
                        <div><span class="font-bold">N° de Nota:</span> <span class="font-mono font-bold" x-text="pedidoSeleccionado.factura.nota_credito.numero_nota"></span></div>
                        <div><span class="font-bold">Fecha Emisión:</span> <span x-text="new Date(pedidoSeleccionado.factura.nota_credito.fecha_emision).toLocaleDateString('es-EC')"></span></div>
                        <div><span class="font-bold">Monto Modificado:</span> $<span class="font-extrabold" x-text="Number(pedidoSeleccionado.factura.nota_credito.valor_total).toFixed(2)"></span></div>
                        <div class="col-span-2"><span class="font-bold">Motivo:</span> <span x-text="pedidoSeleccionado.factura.nota_credito.motivo"></span></div>
                    </div>
                </div>
            </template>
            
            <div class="flex justify-end gap-3">
                <button @click="pedidoSeleccionado = null" class="bg-gray-100 hover:bg-gray-200 text-gray-800 px-5 py-2.5 rounded-xl font-bold transition-colors">
                    Cerrar
                </button>
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
        perPage: 10,
        currentPage: 1,
        loading: false,

        async init() {
            if (!localStorage.getItem('jwt_token')) {
                window.location.href = '/auth/login';
                return;
            }

            // Default dates: 1 month ago to today
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

        verDetalle(pedido) {
            this.pedidoSeleccionado = pedido;
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
                    nombre: item.producto ? item.producto.nombre : 'Producto ' + item.producto_id,
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
                    nombre: item.producto ? item.producto.nombre : 'Producto ' + item.producto_id,
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
