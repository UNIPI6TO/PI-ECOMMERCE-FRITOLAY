@extends('layouts.app')

@section('title', 'Detalle Cierre de Guía - Fritolay Ambato')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6" x-data="cierreGuiaDetalleApp({{ $id }})" x-init="init()">
    <!-- Botón Regresar y Título -->
    <div class="mb-6 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <a href="/admin/cierre-guias" class="inline-flex items-center gap-1.5 text-xs font-extrabold text-gray-500 hover:text-slate-900 transition-colors mb-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                Volver al listado de guías
            </a>
            <h1 class="text-2xl font-black text-slate-900 tracking-tight flex items-center gap-2">
                Guía de Remisión #<span x-text="guiaId"></span>
                <template x-if="guia.estado">
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-black uppercase tracking-wider ml-2"
                          :class="{
                              'bg-amber-100 text-amber-800 border border-amber-200': guia.estado === 'abierta',
                              'bg-blue-100 text-blue-800 border border-blue-200': guia.estado === 'cerrada',
                              'bg-emerald-100 text-emerald-800 border border-emerald-200': guia.estado === 'revisada'
                          }">
                        <span x-text="guia.estado"></span>
                    </span>
                </template>
            </h1>
        </div>

        <template x-if="guia.estado && guia.estado !== 'revisada'">
            <button @click="aprobarRevision()" :disabled="guardando"
                    class="bg-emerald-600 hover:bg-emerald-700 disabled:opacity-50 text-white px-5 py-2.5 rounded-xl font-extrabold text-xs transition-colors shadow-2xs flex items-center gap-2 cursor-pointer">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                <span x-text="guardando ? 'Aprobando...' : 'Aprobar Revisión'"></span>
            </button>
        </template>
        <template x-if="guia.estado === 'revisada'">
            <div class="bg-emerald-50 border border-emerald-200 text-emerald-800 px-4 py-2 rounded-xl text-xs font-bold flex items-center gap-2">
                <svg class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                <span>Aprobada por <strong x-text="guia.revisada_por || 'Administrador'"></strong> (<span x-text="formatearFecha(guia.fecha_revision)"></span>)</span>
            </div>
        </template>
    </div>

    <template x-if="cargando">
        <div class="bg-white rounded-2xl p-12 border border-gray-100 text-center text-gray-400">
            <div class="inline-flex items-center gap-2">
                <svg class="animate-spin h-5 w-5 text-red-600" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
                <span>Cargando detalle de la guía...</span>
            </div>
        </div>
    </template>

    <template x-if="!cargando && guia">
        <div class="space-y-6">
            <!-- Información general & Tarjetas de Recaudación -->
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <!-- Info Guía -->
                <div class="bg-white rounded-2xl p-4 border border-gray-100 shadow-2xs md:col-span-1 flex flex-col justify-between">
                    <div>
                        <p class="text-[10px] font-black uppercase text-gray-400">Chofer Asignado</p>
                        <p class="text-sm font-black text-slate-900 mt-0.5" x-text="guia.chofer_nombre"></p>
                        <p class="text-xs text-gray-500 font-bold mt-1" x-text="'Placa: ' + guia.camion_placa"></p>
                    </div>
                    <div class="mt-3 pt-3 border-t border-gray-100">
                        <p class="text-[10px] font-black uppercase text-gray-400">Fecha de Generación</p>
                        <p class="text-xs font-bold text-gray-700" x-text="formatearFecha(guia.fecha_generacion)"></p>
                    </div>
                </div>

                <!-- Recaudación Efectivo -->
                <div class="bg-white rounded-2xl p-4 border border-gray-100 shadow-2xs">
                    <div class="flex items-center justify-between">
                        <p class="text-[10px] font-black uppercase text-emerald-600">Recaudado Efectivo</p>
                        <svg class="w-5 h-5 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                    </div>
                    <p class="text-2xl font-black text-slate-900 mt-2" x-text="formatMoney(resumenCaja.efectivo)"></p>
                    <p class="text-[11px] font-bold text-gray-400 mt-1" x-text="'Declarado chofer: ' + formatMoney(guia.efectivo_declarado)"></p>
                </div>

                <!-- Recaudación Transferencia / De Una -->
                <div class="bg-white rounded-2xl p-4 border border-gray-100 shadow-2xs">
                    <div class="flex items-center justify-between">
                        <p class="text-[10px] font-black uppercase text-blue-600">De Una / Depósitos</p>
                        <svg class="w-5 h-5 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg>
                    </div>
                    <p class="text-2xl font-black text-slate-900 mt-2" x-text="formatMoney(resumenCaja.de_una)"></p>
                    <p class="text-[11px] font-bold text-gray-400 mt-1">Transacciones digitales directas</p>
                </div>

                <!-- Recaudación Bancos / Tarjeta -->
                <div class="bg-white rounded-2xl p-4 border border-gray-100 shadow-2xs">
                    <div class="flex items-center justify-between">
                        <p class="text-[10px] font-black uppercase text-purple-600">Bancos / Tarjetas</p>
                        <svg class="w-5 h-5 text-purple-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                    </div>
                    <p class="text-2xl font-black text-slate-900 mt-2" x-text="formatMoney(resumenCaja.bancos)"></p>
                    <p class="text-[11px] font-bold text-gray-400 mt-1">Cobros con tarjeta / datáfono</p>
                </div>
            </div>

            <!-- Tabla de Productos Devueltos / Faltantes -->
            <div class="bg-white rounded-2xl border border-gray-100 shadow-2xs p-5">
                <h3 class="text-sm font-black text-slate-900 uppercase tracking-wide mb-3 flex items-center gap-2">
                    <svg class="w-4 h-4 text-rose-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 15v-1a4 4 0 00-4-4H8m0 0l3 3m-3-3l3-3m9 14V5a2 2 0 00-2-2H6a2 2 0 00-2 2v16l4-2 4 2 4-2 4 2z"/></svg>
                    Resumen de Productos Devueltos (Devoluciones Parciales / Totales)
                </h3>
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="bg-gray-50 text-gray-500 text-[10px] font-black uppercase tracking-wider border-b border-gray-100">
                                <th class="py-2.5 px-3">Producto</th>
                                <th class="py-2.5 px-3 text-center">Cantidad Devuelta</th>
                                <th class="py-2.5 px-3 text-right">Monto Devuelto ($)</th>
                                <th class="py-2.5 px-3">Motivo de Devolución</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 text-xs font-semibold text-gray-700">
                            <template x-if="productosDevueltos.length === 0">
                                <tr>
                                    <td colspan="4" class="py-6 text-center text-gray-400">
                                        No se registraron devoluciones de productos en esta guía.
                                    </td>
                                </tr>
                            </template>
                            <template x-for="p in productosDevueltos" :key="p.producto + p.motivo">
                                <tr class="hover:bg-gray-50/50">
                                    <td class="py-2.5 px-3 font-bold text-gray-900" x-text="p.producto"></td>
                                    <td class="py-2.5 px-3 text-center font-black text-slate-800" x-text="p.cantidad_devuelta"></td>
                                    <td class="py-2.5 px-3 text-right font-black text-rose-600" x-text="formatMoney(p.total_usd)"></td>
                                    <td class="py-2.5 px-3">
                                        <span class="inline-block bg-rose-50 border border-rose-100 text-rose-700 px-2 py-0.5 rounded-md text-[11px] font-bold" x-text="p.motivo"></span>
                                    </td>
                                </tr>
                            </template>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Tabla de Pedidos en la Guía con Filtros por Pestañas -->
            <div class="bg-white rounded-2xl border border-gray-100 shadow-2xs overflow-hidden">
                <div class="p-5 border-b border-gray-100 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                    <h3 class="text-sm font-black text-slate-900 uppercase tracking-wide flex items-center gap-2">
                        <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                        Pedidos Asignados a la Guía
                    </h3>

                    <!-- Pestañas por Estado -->
                    <div class="flex items-center gap-1 bg-gray-100/80 p-1 rounded-xl">
                        <button @click="tabEstado = 'todos'"
                                :class="tabEstado === 'todos' ? 'bg-white text-slate-900 font-extrabold shadow-2xs' : 'text-gray-600 hover:text-gray-900 font-bold'"
                                class="px-3 py-1.5 rounded-lg text-xs transition-all cursor-pointer">
                            Todos (<span x-text="pedidos.length"></span>)
                        </button>
                        <button @click="tabEstado = 'entregado'"
                                :class="tabEstado === 'entregado' ? 'bg-white text-slate-900 font-extrabold shadow-2xs' : 'text-gray-600 hover:text-gray-900 font-bold'"
                                class="px-3 py-1.5 rounded-lg text-xs transition-all cursor-pointer">
                            Entregados
                        </button>
                        <button @click="tabEstado = 'entregado_parcialmente'"
                                :class="tabEstado === 'entregado_parcialmente' ? 'bg-white text-slate-900 font-extrabold shadow-2xs' : 'text-gray-600 hover:text-gray-900 font-bold'"
                                class="px-3 py-1.5 rounded-lg text-xs transition-all cursor-pointer">
                            Parciales
                        </button>
                        <button @click="tabEstado = 'no_entregado'"
                                :class="tabEstado === 'no_entregado' ? 'bg-white text-slate-900 font-extrabold shadow-2xs' : 'text-gray-600 hover:text-gray-900 font-bold'"
                                class="px-3 py-1.5 rounded-lg text-xs transition-all cursor-pointer">
                            No Entregados
                        </button>
                    </div>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="bg-slate-900 text-white text-[11px] font-black uppercase tracking-wider">
                                <th class="py-3 px-4">Pedido #</th>
                                <th class="py-3 px-4">Cliente</th>
                                <th class="py-3 px-4">Dirección</th>
                                <th class="py-3 px-4 text-center">Método Pago</th>
                                <th class="py-3 px-4 text-right">Total ($)</th>
                                <th class="py-3 px-4 text-center">Estado</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 text-xs font-semibold text-gray-700">
                            <template x-if="pedidosFiltrados.length === 0">
                                <tr>
                                    <td colspan="6" class="py-8 text-center text-gray-400">
                                        No hay pedidos con el estado seleccionado.
                                    </td>
                                </tr>
                            </template>
                            <template x-for="p in pedidosFiltrados" :key="p.id">
                                <tr class="hover:bg-gray-50/80 transition-colors">
                                    <td class="py-3 px-4 font-black text-slate-900" x-text="p.idPedido"></td>
                                    <td class="py-3 px-4 font-bold text-gray-900" x-text="p.cliente"></td>
                                    <td class="py-3 px-4 text-gray-600 truncate max-w-xs" x-text="p.direccion"></td>
                                    <td class="py-3 px-4 text-center">
                                        <span class="inline-block bg-gray-100 text-gray-800 font-extrabold px-2 py-0.5 rounded text-[10px]" x-text="p.metodo_pago"></span>
                                    </td>
                                    <td class="py-3 px-4 text-right font-black text-emerald-600" x-text="formatMoney(p.total)"></td>
                                    <td class="py-3 px-4 text-center">
                                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-black uppercase tracking-wider"
                                              :class="{
                                                  'bg-emerald-100 text-emerald-800 border border-emerald-200': p.estado === 'entregado',
                                                  'bg-amber-100 text-amber-800 border border-amber-200': p.estado === 'entregado_parcialmente',
                                                  'bg-rose-100 text-rose-800 border border-rose-200': p.estado === 'no_entregado' || p.estado === 'cancelado',
                                                  'bg-blue-100 text-blue-800 border border-blue-200': p.estado === 'en_ruta'
                                              }">
                                            <span x-text="p.estado"></span>
                                        </span>
                                    </td>
                                </tr>
                            </template>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </template>
</div>

<script>
    document.addEventListener('alpine:init', () => {
        Alpine.data('cierreGuiaDetalleApp', (guiaIdParam) => ({
            guiaId: guiaIdParam,
            guia: null,
            resumenCaja: { efectivo: 0, bancos: 0, de_una: 0, total: 0 },
            productosDevueltos: [],
            pedidos: [],
            tabEstado: 'todos',
            cargando: false,
            guardando: false,

            get pedidosFiltrados() {
                if (this.tabEstado === 'todos') return this.pedidos;
                return this.pedidos.filter(p => p.estado === this.tabEstado);
            },

            async init() {
                await this.cargarDetalle();
            },

            async cargarDetalle() {
                this.cargando = true;
                try {
                    const data = await window.api(`/api/guias-remision/${this.guiaId}/detalle-cierre`);
                    if (data) {
                        this.guia = data.guia || {};
                        this.resumenCaja = data.resumen_caja || {};
                        this.productosDevueltos = data.productos_devueltos || [];
                        this.pedidos = data.pedidos || [];
                    }
                } catch (e) {
                    console.error("Error al cargar detalle de guía:", e);
                    window.toast(e.message || "Error al obtener detalle de la guía", "error");
                } finally {
                    this.cargando = false;
                }
            },

            async aprobarRevision() {
                if (!confirm("¿Está seguro de aprobar la revisión de esta guía? Esta acción certifica el cierre financiero y físico.")) return;

                this.guardando = true;
                try {
                    const userId = localStorage.getItem('user_id') || 1;
                    const data = await window.api(`/api/guias-remision/${this.guiaId}/aprobar-revision`, {
                        method: 'POST',
                        body: JSON.stringify({ user_id: userId })
                    });
                    window.toast("Revisión de guía aprobada exitosamente", "success");
                    await this.cargarDetalle();
                } catch (e) {
                    console.error("Error al aprobar revisión:", e);
                    window.toast(e.message || "No se pudo aprobar la revisión de la guía", "error");
                } finally {
                    this.guardando = false;
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
