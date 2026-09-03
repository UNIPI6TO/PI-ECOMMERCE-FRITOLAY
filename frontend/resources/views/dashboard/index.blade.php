@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto py-8 px-4" x-data="dashboard()">
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-6">
        <div>
            <h1 class="text-3xl font-extrabold text-gray-900 tracking-tight">Dashboard Gerencial</h1>
            <p class="text-sm text-gray-500 mt-0.5">Control de métricas, rendimiento de entregas y analítica de pérdidas</p>
        </div>
        
        <!-- Filtro de Rango de Fechas (Persistente en Sesión, Defecto Hoy, Máx. 1 Mes) -->
        <div class="bg-white p-3 rounded-xl border border-gray-100 shadow-sm flex flex-wrap items-center gap-3">
            <div class="flex items-center gap-2">
                <span class="text-xs font-bold uppercase tracking-wider text-gray-500">Desde:</span>
                <input type="date" 
                       x-model="fechaInicio" 
                       @change="onFechaInicioChange()" 
                       class="border border-gray-300 rounded-lg px-3 py-1.5 text-sm font-medium text-gray-800 focus:ring-2 focus:ring-slate-800 focus:border-slate-800 outline-none shadow-xs">
            </div>
            <div class="flex items-center gap-2">
                <span class="text-xs font-bold uppercase tracking-wider text-gray-500">Hasta:</span>
                <input type="date" 
                       x-model="fechaFin" 
                       :min="fechaInicio"
                       :max="maxFechaFin" 
                       @change="onFechaFinChange()" 
                       class="border border-gray-300 rounded-lg px-3 py-1.5 text-sm font-medium text-gray-800 focus:ring-2 focus:ring-slate-800 focus:border-slate-800 outline-none shadow-xs">
            </div>

            <!-- Presets -->
            <div class="flex items-center bg-gray-100 p-1 rounded-lg text-xs font-semibold">
                <button @click="presetPeriodo('MES')" class="px-3 py-1.5 rounded-md transition-all" :class="esPeriodo('MES') ? 'bg-white text-gray-900 shadow-xs font-bold' : 'text-gray-600 hover:text-gray-900'">Último Mes</button>
                <button @click="presetPeriodo('SEMANA')" class="px-3 py-1.5 rounded-md transition-all" :class="esPeriodo('SEMANA') ? 'bg-white text-gray-900 shadow-xs font-bold' : 'text-gray-600 hover:text-gray-900'">Última Semana</button>
                <button @click="presetPeriodo('HOY')" class="px-3 py-1.5 rounded-md transition-all" :class="esPeriodo('HOY') ? 'bg-white text-gray-900 shadow-xs font-bold' : 'text-gray-600 hover:text-gray-900'">Hoy</button>
            </div>
        </div>
    </div>

    <!-- Indicador de Carga (Spinner) -->
    <div x-show="loading" class="flex flex-col items-center justify-center py-24 bg-white rounded-xl shadow-sm border border-gray-100 my-4">
        <svg class="animate-spin h-12 w-12 text-slate-800 mb-3" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8z"></path>
        </svg>
        <span class="text-base font-semibold text-gray-700">Cargando datos del dashboard...</span>
        <span class="text-xs text-gray-400 mt-1">Por favor espera un momento</span>
    </div>

    <!-- Contenido del Dashboard -->
    <div x-show="!loading" x-transition.opacity>

    <!-- Top KPIs Grid -->
    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-4 mb-8">
        <div class="bg-white p-4 rounded-xl shadow-xs border-l-4 border-blue-500 border-y border-r border-gray-100">
            <div class="text-xs font-bold uppercase tracking-wider text-gray-400">Total Pedidos (#)</div>
            <div class="text-2xl font-black text-gray-900 mt-1" x-text="kpis.cantidad_total_pedidos || '0'"></div>
        </div>
        <div class="bg-white p-4 rounded-xl shadow-xs border-l-4 border-indigo-500 border-y border-r border-gray-100">
            <div class="text-xs font-bold uppercase tracking-wider text-gray-400">Valor Total Pedidos</div>
            <div class="text-2xl font-black text-gray-900 mt-1">$<span x-text="kpis.valor_total_pedidos || '0.00'"></span></div>
        </div>
        <div class="bg-white p-4 rounded-xl shadow-xs border-l-4 border-emerald-500 border-y border-r border-gray-100">
            <div class="text-xs font-bold uppercase tracking-wider text-gray-400">Ventas Entregadas</div>
            <div class="text-2xl font-black text-gray-900 mt-1">$<span x-text="kpis.ventas_totales || '0.00'"></span></div>
        </div>
        <div class="bg-white p-4 rounded-xl shadow-xs border-l-4 border-teal-500 border-y border-r border-gray-100">
            <div class="text-xs font-bold uppercase tracking-wider text-gray-400">Efectividad Entrega</div>
            <div class="text-2xl font-black text-gray-900 mt-1"><span x-text="kpis.efectividad || '0'"></span>%</div>
        </div>
        <div class="bg-white p-4 rounded-xl shadow-xs border-l-4 border-amber-500 border-y border-r border-gray-100">
            <div class="text-xs font-bold uppercase tracking-wider text-gray-400">Pedidos Entregados</div>
            <div class="text-2xl font-black text-gray-900 mt-1" x-text="kpis.pedidos_entregados || '0'"></div>
        </div>
        <div class="bg-white p-4 rounded-xl shadow-xs border-l-4 border-rose-500 border-y border-r border-gray-100">
            <div class="text-xs font-bold uppercase tracking-wider text-gray-400">Recaudación Efectivo</div>
            <div class="text-2xl font-black text-gray-900 mt-1">$<span x-text="kpis.recaudacion_efectivo || '0.00'"></span></div>
        </div>
    </div>

    <!-- Charts Row 1: Ventas por Día & Métodos de Pago (ORIGINAL CLEAN LINE CHART) -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8 mb-8">
        <div class="lg:col-span-2 bg-white p-5 rounded-xl shadow-xs border border-gray-100">
            <div class="flex items-center justify-between mb-4">
                <div>
                    <h3 class="font-bold text-gray-900 text-base">Ventas por Día</h3>
                    <p class="text-xs text-gray-400">Evolución del monto facturado ($) en el periodo seleccionado</p>
                </div>
            </div>
            <div style="height: 300px;">
                <canvas id="ventasDia"></canvas>
            </div>
        </div>
        <div class="bg-white p-5 rounded-xl shadow-xs border border-gray-100 flex flex-col justify-between">
            <div>
                <h3 class="font-bold text-gray-900 text-base mb-1">Recaudación por Método</h3>
                <p class="text-xs text-gray-400 mb-4">Distribución porcentual por canal de cobro</p>
            </div>
            <div class="w-full flex-1 flex items-center justify-center min-h-[250px]">
                <canvas id="metodosPago"></canvas>
            </div>
        </div>
    </div>

    <!-- Charts Row 2: SECCIÓN DEDICADA A PÉRDIDAS DE VENTAS Y DEVOLUCIONES ($) -->
    <div class="mb-8 bg-rose-50/40 p-6 rounded-2xl border border-rose-100">
        <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between mb-6 pb-4 border-b border-rose-100 gap-2">
            <div>
                <h2 class="text-xl font-black text-rose-950 flex items-center gap-2">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-rose-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 17h8m0 0V9m0 8l-8-8-4 4-6-6" />
                    </svg>
                    Analítica de Pérdidas y Devoluciones ($)
                </h2>
                <p class="text-xs text-rose-700 mt-0.5">Seguimiento exclusivo del impacto económico por carritos abandonados, cancelaciones y devoluciones</p>
            </div>
            <div class="bg-white px-4 py-2 rounded-xl shadow-xs border border-rose-200 text-right">
                <span class="text-[10px] font-bold text-rose-500 uppercase block tracking-wider">Total Pérdida en Rango</span>
                <span class="text-xl font-black text-rose-700">$<span x-text="Number(totalPerdido).toFixed(2)"></span></span>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
            <!-- Tendencia de Pérdidas por Día -->
            <div class="bg-white p-5 rounded-xl shadow-xs border border-rose-100">
                <h3 class="font-bold text-gray-900 text-sm mb-1">Tendencia de Dinero Perdido por Día ($)</h3>
                <p class="text-xs text-gray-400 mb-4">Evolución diaria de pérdidas monetarias acumuladas</p>
                <div style="height: 280px;">
                    <canvas id="perdidasDia"></canvas>
                </div>
            </div>

            <!-- Top 10 Motivos de Pérdida ($) -->
            <div class="bg-white p-5 rounded-xl shadow-xs border border-rose-100">
                <h3 class="font-bold text-gray-900 text-sm mb-1">Top 10 Motivos de Pérdidas ($)</h3>
                <p class="text-xs text-gray-400 mb-4">Ranking de motivos ordenados por dinero perdido</p>
                <div style="height: 280px;">
                    <canvas id="topPerdidas"></canvas>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Charts Row 3: Ventas por Camión & Carritos Abandonados Recientes -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8 mb-8">
        <!-- Ventas por Camión -->
        <div class="lg:col-span-2 bg-white p-5 rounded-xl shadow-xs border border-gray-100">
            <div class="mb-4">
                <h3 class="font-bold text-gray-900 text-base">Ventas por Camión en Ruta</h3>
                <p class="text-xs text-gray-400">Total monetario facturado y entregado por vehículo asignado</p>
            </div>
            <div style="height: 300px;">
                <canvas id="ventasCamion"></canvas>
            </div>
        </div>

        <!-- Tabla Carritos Abandonados Recientes -->
        <div class="bg-white p-5 rounded-xl shadow-xs border border-gray-100 flex flex-col">
            <div class="flex items-center justify-between mb-3">
                <div>
                    <h3 class="font-bold text-gray-900 text-base">Carritos Abandonados</h3>
                    <p class="text-xs text-gray-400">Registros recientes de carritos vaciados</p>
                </div>
                <span class="px-2.5 py-1 text-xs font-bold rounded-full bg-rose-100 text-rose-700" x-text="carritos.length"></span>
            </div>
            
            <div class="flex-1 overflow-y-auto max-h-[280px]">
                <table class="w-full text-xs text-left">
                    <thead>
                        <tr class="bg-gray-50 border-b text-gray-500 uppercase font-bold text-[10px]">
                            <th class="p-2">Cliente</th>
                            <th class="p-2">Motivo</th>
                            <th class="p-2 text-right">Monto</th>
                        </tr>
                    </thead>
                    <tbody>
                        <template x-if="carritos.length === 0">
                            <tr><td colspan="3" class="p-4 text-center text-gray-400">No hay carritos abandonados en este rango</td></tr>
                        </template>
                        <template x-for="carrito in carritos" :key="carrito.id || carrito.cliente">
                            <tr class="border-b hover:bg-gray-50/80 transition-colors">
                                <td class="p-2 font-bold text-gray-800" x-text="carrito.cliente"></td>
                                <td class="p-2 text-gray-500" x-text="carrito.motivo || 'Sin motivo'"></td>
                                <td class="p-2 text-right font-extrabold text-rose-600" x-text="'$' + Number(carrito.monto).toFixed(2)"></td>
                            </tr>
                        </template>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Stock Section -->
    <div class="bg-white p-5 rounded-xl shadow-xs border border-gray-100">
        <div class="flex items-center justify-between mb-4">
            <h3 class="font-bold text-gray-900">Control de Stock en Ruta</h3>
            <div class="flex gap-2">
                <button @click="stockTab = 'maestro'" :class="stockTab === 'maestro' ? 'bg-slate-900 text-white' : 'bg-gray-100 text-gray-700'" class="text-xs px-3 py-1.5 rounded-lg font-bold transition-colors">Bodega Central</button>
                <button @click="stockTab = 'camiones'" :class="stockTab === 'camiones' ? 'bg-slate-900 text-white' : 'bg-gray-100 text-gray-700'" class="text-xs px-3 py-1.5 rounded-lg font-bold transition-colors">Por Camión</button>
            </div>
        </div>

        <!-- Tab Bodega Central -->
        <div x-show="stockTab === 'maestro'">
            <template x-if="stock.maestro && stock.maestro.length === 0">
                <p class="text-gray-400 text-sm text-center py-4">Sin productos registrados.</p>
            </template>
            <div class="overflow-x-auto" x-show="stock.maestro && stock.maestro.length > 0">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="text-xs text-gray-500 border-b uppercase">
                            <th class="text-left py-2">Producto</th>
                            <th class="text-right py-2">Disponible</th>
                            <th class="text-right py-2">En Pedidos</th>
                            <th class="text-right py-2">Estado</th>
                        </tr>
                    </thead>
                    <tbody>
                        <template x-for="p in stock.maestro" :key="p.id">
                            <tr class="border-b hover:bg-gray-50">
                                <td class="py-2 font-bold text-gray-800" x-text="p.nombre"></td>
                                <td class="py-2 text-right font-bold text-gray-900" x-text="parseFloat(p.disponible || p.cantidad_fisica || 0).toFixed(0)"></td>
                                <td class="py-2 text-right text-gray-500" x-text="parseFloat(p.en_pedidos || 0).toFixed(0)"></td>
                                <td class="py-2 text-right">
                                    <span class="px-2 py-0.5 text-[10px] font-extrabold rounded-full uppercase"
                                           :class="parseFloat(p.disponible || p.cantidad_fisica || 0) < 10 ? 'bg-red-100 text-red-700' : 'bg-green-100 text-green-700'"
                                           x-text="parseFloat(p.disponible || p.cantidad_fisica || 0) < 10 ? 'BAJO' : 'OK'">
                                     </span>
                                </td>
                            </tr>
                        </template>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Tab Por Camión -->
        <div x-show="stockTab === 'camiones'">
            <template x-if="stock.por_camion && stock.por_camion.length === 0">
                <p class="text-gray-400 text-sm text-center py-4">No hay camiones con stock cargado actualmente.</p>
            </template>
            <div class="overflow-x-auto" x-show="stock.por_camion && stock.por_camion.length > 0">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="text-xs text-gray-500 border-b uppercase">
                            <th class="text-left py-2">Camión</th>
                            <th class="text-left py-2">Producto</th>
                            <th class="text-right py-2">Cantidad</th>
                        </tr>
                    </thead>
                    <tbody>
                        <template x-for="(r, idx) in stock.por_camion" :key="idx">
                            <tr class="border-b hover:bg-gray-50">
                                <td class="py-2 font-bold text-gray-600" x-text="r.placa"></td>
                                <td class="py-2" x-text="r.nombre"></td>
                                <td class="py-2 text-right font-semibold" x-text="parseFloat(r.cantidad_actual || r.cantidad_fisica || 0).toFixed(0)"></td>
                            </tr>
                        </template>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('alpine:init', () => {
    Alpine.data('dashboard', () => {
        const getFormattedDate = (dateObj) => {
            const year = dateObj.getFullYear();
            const month = String(dateObj.getMonth() + 1).padStart(2, '0');
            const day = String(dateObj.getDate()).padStart(2, '0');
            return `${year}-${month}-${day}`;
        };

        const todayStr = getFormattedDate(new Date());

        const initialFechaInicio = sessionStorage.getItem('dashboard_fecha_inicio') || todayStr;
        const initialFechaFin = sessionStorage.getItem('dashboard_fecha_fin') || todayStr;

        return {
            fechaInicio: initialFechaInicio,
            fechaFin: initialFechaFin,
            loading: true,
            kpis: {
                ventas_totales: '0.00',
                cantidad_total_pedidos: 0,
                valor_total_pedidos: '0.00',
                efectividad: '0',
                pedidos_entregados: 0,
                recaudacion_efectivo: '0.00'
            },
            totalPerdido: 0,
            carritos: [],
            stock: { maestro: [], por_camion: [] },
            stockTab: 'maestro',

            chartVentasDia: null,
            chartMetodosPago: null,
            chartPerdidasDia: null,
            chartTopPerdidas: null,
            chartVentasCamion: null,

            get maxFechaFin() {
                if (!this.fechaInicio) return '';
                const parts = this.fechaInicio.split('-').map(Number);
                const dateObj = new Date(parts[0], parts[1] - 1, parts[2]);
                dateObj.setMonth(dateObj.getMonth() + 1);
                return getFormattedDate(dateObj);
            },

            async init() {
                this.validarRangoFechas();
                await this.cargarDashboard();
            },

            validarRangoFechas() {
                if (!this.fechaInicio) this.fechaInicio = todayStr;
                if (!this.fechaFin) this.fechaFin = this.fechaInicio;

                if (this.fechaFin < this.fechaInicio) {
                    this.fechaFin = this.fechaInicio;
                }

                const maxPermitida = this.maxFechaFin;
                if (maxPermitida && this.fechaFin > maxPermitida) {
                    this.fechaFin = maxPermitida;
                }

                sessionStorage.setItem('dashboard_fecha_inicio', this.fechaInicio);
                sessionStorage.setItem('dashboard_fecha_fin', this.fechaFin);
            },

            onFechaInicioChange() {
                this.validarRangoFechas();
                this.cargarDashboard();
            },

            onFechaFinChange() {
                this.validarRangoFechas();
                this.cargarDashboard();
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
                this.cargarDashboard();
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

            async cargarDashboard() {
                this.loading = true;
                let ventasData = null;
                let recData = null;
                let perdidasData = null;
                try {
                    const params = `?fecha_inicio=${this.fechaInicio}&fecha_fin=${this.fechaFin}`;

                    const kpisData = await window.api(`/api/dashboard/kpis${params}`).catch(e => {
                        if (e.message === 'Forbidden' || e.message === 'Unauthorized' || e.message.includes('permisos')) {
                            Swal.fire('Acceso Denegado', 'Tu rol actual no tiene permisos para ver el Dashboard Administrativo. Redirigiendo...', 'error');
                            setTimeout(() => window.location.href = '/', 2000);
                        }
                        return null;
                    });
                    ventasData = await window.api(`/api/dashboard/ventas${params}`).catch(() => null);
                    recData = await window.api(`/api/dashboard/recaudacion${params}`).catch(() => null);
                    perdidasData = await window.api(`/api/dashboard/perdidas${params}`).catch(() => null);
                    const carritosData = await window.api(`/api/dashboard/carritos-abandonados${params}`).catch(() => []);
                    const stockData = await window.api('/api/dashboard/stock').catch(() => ({ maestro: [], por_camion: [] }));
                    
                    this.carritos = carritosData || [];
                    this.stock = stockData || { maestro: [], por_camion: [] };
                    this.totalPerdido = perdidasData ? (perdidasData.total_acumulado_perdido || 0) : 0;

                    let ventasTotales = '0.00';
                    let efectividad = '0';
                    let pedidosEntregados = 0;
                    let recEfvo = '0.00';

                    if (ventasData && ventasData.total_periodo !== undefined && ventasData.total_periodo !== null) {
                        ventasTotales = Number(ventasData.total_periodo).toFixed(2);
                    }

                    if (kpisData) {
                        efectividad = kpisData.efectividad_general || 0;
                        pedidosEntregados = (kpisData.pedidos_por_estado?.entregado || 0) + (kpisData.pedidos_por_estado?.entregado_parcialmente || 0);
                    }

                    if (recData && recData.por_metodo_pago) {
                        const efectivoItem = recData.por_metodo_pago.find(m => String(m.metodo_pago).toLowerCase() === 'efectivo');
                        if (efectivoItem) {
                            recEfvo = Number(efectivoItem.total).toFixed(2);
                        }
                    }

                    this.kpis = {
                        ventas_totales: ventasTotales,
                        cantidad_total_pedidos: kpisData ? (kpisData.cantidad_total_pedidos || 0) : 0,
                        valor_total_pedidos: kpisData ? Number(kpisData.valor_total_pedidos || 0).toFixed(2) : '0.00',
                        efectividad: efectividad,
                        pedidos_entregados: pedidosEntregados,
                        recaudacion_efectivo: recEfvo
                    };
                } catch (error) {
                    console.error("Error al cargar el dashboard", error);
                } finally {
                    this.loading = false;
                    this.$nextTick(() => {
                        this.renderCharts(ventasData, recData, perdidasData);
                    });
                }
            },

            renderCharts(ventasData, recData, perdidasData) {
                if (this.chartVentasDia) this.chartVentasDia.destroy();
                if (this.chartMetodosPago) this.chartMetodosPago.destroy();
                if (this.chartPerdidasDia) this.chartPerdidasDia.destroy();
                if (this.chartTopPerdidas) this.chartTopPerdidas.destroy();
                if (this.chartVentasCamion) this.chartVentasCamion.destroy();

                // Chart 1: Ventas por Día (CLEAN LINE CHART ORIGINAL)
                let labelsVentasDia = [];
                let dataVentasDia = [];
                if (ventasData && ventasData.por_dia && ventasData.por_dia.length > 0) {
                    labelsVentasDia = ventasData.por_dia.map(i => i.fecha);
                    dataVentasDia = ventasData.por_dia.map(i => parseFloat(i.total) || 0);
                } else {
                    labelsVentasDia = [this.fechaInicio, this.fechaFin];
                    dataVentasDia = [0, 0];
                }

                const ctx1 = document.getElementById('ventasDia');
                if (ctx1) {
                    this.chartVentasDia = new Chart(ctx1, {
                        type: 'line',
                        data: {
                            labels: labelsVentasDia,
                            datasets: [{ 
                                label: 'Ventas ($)', 
                                data: dataVentasDia, 
                                borderColor: '#E3001B', 
                                backgroundColor: 'rgba(227, 0, 27, 0.08)',
                                fill: true,
                                tension: 0.2 
                            }]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            plugins: {
                                legend: { display: true }
                            }
                        }
                    });
                }

                // Chart 2: Métodos de Pago
                let labelsMetodos = [];
                let dataMetodos = [];
                const colors = ['#2ecc71', '#3498db', '#9b59b6', '#f1c40f', '#e74c3c'];
                if (recData && recData.por_metodo_pago && recData.por_metodo_pago.length > 0) {
                    labelsMetodos = recData.por_metodo_pago.map(i => String(i.metodo_pago).toUpperCase().replace(/_/g, ' '));
                    dataMetodos = recData.por_metodo_pago.map(i => parseFloat(i.total) || 0);
                }

                const ctx2 = document.getElementById('metodosPago');
                if (ctx2) {
                    this.chartMetodosPago = new Chart(ctx2, {
                        type: 'pie',
                        data: {
                            labels: labelsMetodos.length ? labelsMetodos : ['Sin Ventas en este Rango'],
                            datasets: [{ 
                                data: dataMetodos.length ? dataMetodos : [1], 
                                backgroundColor: dataMetodos.length ? colors : ['#e5e7eb']
                            }]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false
                        }
                    });
                }

                // Chart 3: Tendencia de Dinero Perdido por Día ($) (EXCLUSIVO DE PÉRDIDAS)
                let labelsPerdidasDia = [];
                let dataPerdidasDia = [];
                if (perdidasData && perdidasData.por_dia && perdidasData.por_dia.length > 0) {
                    labelsPerdidasDia = perdidasData.por_dia.map(i => i.fecha);
                    dataPerdidasDia = perdidasData.por_dia.map(i => parseFloat(i.total_perdido) || 0);
                } else {
                    labelsPerdidasDia = [this.fechaInicio, this.fechaFin];
                    dataPerdidasDia = [0, 0];
                }

                const ctxPerdidasDia = document.getElementById('perdidasDia');
                if (ctxPerdidasDia) {
                    this.chartPerdidasDia = new Chart(ctxPerdidasDia, {
                        type: 'line',
                        data: {
                            labels: labelsPerdidasDia,
                            datasets: [{
                                label: 'Dinero Perdido ($)',
                                data: dataPerdidasDia,
                                borderColor: '#e11d48',
                                backgroundColor: 'rgba(225, 29, 72, 0.1)',
                                fill: true,
                                tension: 0.3
                            }]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            plugins: {
                                legend: { display: true }
                            }
                        }
                    });
                }

                // Chart 4: Top 10 Motivos de Pérdidas ($)
                let labelsPerdidas = [];
                let dataPerdidas = [];

                if (perdidasData && perdidasData.top_motivos && perdidasData.top_motivos.length > 0) {
                    labelsPerdidas = perdidasData.top_motivos.map(i => i.motivo);
                    dataPerdidas = perdidasData.top_motivos.map(i => parseFloat(i.total_perdido) || 0);
                }

                const ctxPerdidas = document.getElementById('topPerdidas');
                if (ctxPerdidas) {
                    this.chartTopPerdidas = new Chart(ctxPerdidas, {
                        type: 'bar',
                        data: {
                            labels: labelsPerdidas.length ? labelsPerdidas : ['Sin Pérdidas Registradas'],
                            datasets: [{
                                label: 'Dinero Perdido ($)',
                                data: dataPerdidas.length ? dataPerdidas : [0],
                                backgroundColor: 'rgba(225, 29, 72, 0.75)',
                                borderColor: '#e11d48',
                                borderWidth: 1
                            }]
                        },
                        options: {
                            indexAxis: 'y',
                            responsive: true,
                            maintainAspectRatio: false,
                            plugins: {
                                legend: { display: false }
                            },
                            scales: {
                                x: {
                                    title: { display: true, text: 'Monto Perdido ($)' }
                                }
                            }
                        }
                    });
                }

                // Chart 5: Ventas por Camión
                let labelsCamion = [];
                let dataCamion = [];
                if (ventasData && ventasData.por_camion && ventasData.por_camion.length > 0) {
                    labelsCamion = ventasData.por_camion.map(i => i.placa ? `Camión (${i.placa})` : `Camión #${i.camion_id}`);
                    dataCamion = ventasData.por_camion.map(i => parseFloat(i.total) || 0);
                }

                const ctx3 = document.getElementById('ventasCamion');
                if (ctx3) {
                    this.chartVentasCamion = new Chart(ctx3, {
                        type: 'bar',
                        data: {
                            labels: labelsCamion.length ? labelsCamion : ['Sin Datos'], 
                            datasets: [{ 
                                label: 'Total Vendido ($)', 
                                data: dataCamion.length ? dataCamion : [0], 
                                backgroundColor: '#3498db' 
                            }]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false
                        }
                    });
                }
            }
        };
    });
});
</script>
@endsection
