@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto py-8 px-4" x-data="dashboard()">
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-6">
        <div>
            <h1 class="text-3xl font-bold text-gray-800">Dashboard Gerencial</h1>
            <p class="text-sm text-gray-500 mt-0.5">Control de métricas y rendimiento de entregas</p>
        </div>
        
        <!-- Filtro de Rango de Fechas (Persistente en Sesión, Máx. 1 Mes) -->
        <div class="bg-white p-3 rounded-lg shadow-sm border border-gray-200 flex flex-wrap items-center gap-3">
            <div class="flex items-center gap-2 text-sm text-gray-700">
                <label class="font-semibold text-gray-600">Fecha Inicial:</label>
                <input type="date" 
                       x-model="fechaInicio" 
                       @change="onFechaInicioChange()" 
                       class="border border-gray-300 rounded px-2.5 py-1 text-sm font-medium text-gray-800 focus:ring-2 focus:ring-red-500 focus:border-red-500 outline-none">
            </div>
            
            <div class="flex items-center gap-2 text-sm text-gray-700">
                <label class="font-semibold text-gray-600">Fecha Final:</label>
                <input type="date" 
                       x-model="fechaFin" 
                       :min="fechaInicio"
                       :max="maxFechaFin" 
                       @change="onFechaFinChange()" 
                       class="border border-gray-300 rounded px-2.5 py-1 text-sm font-medium text-gray-800 focus:ring-2 focus:ring-red-500 focus:border-red-500 outline-none">
            </div>

            <button @click="resetFechasHoy()" 
                    class="text-xs bg-gray-100 hover:bg-gray-200 text-gray-700 font-bold px-3 py-1.5 rounded transition-colors"
                    title="Restablecer a hoy">
                Hoy
            </button>
        </div>
    </div>

    <!-- Indicador de Carga (Spinner) -->
    <div x-show="loading" class="flex flex-col items-center justify-center py-24 bg-white rounded-xl shadow-sm border border-gray-100 my-4">
        <svg class="animate-spin h-12 w-12 text-[#E3001B] mb-3" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8z"></path>
        </svg>
        <span class="text-base font-semibold text-gray-700">Cargando datos del dashboard...</span>
        <span class="text-xs text-gray-400 mt-1">Por favor espera un momento</span>
    </div>

    <!-- Contenido del Dashboard -->
    <div x-show="!loading" x-transition.opacity>

    <!-- KPIs -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-8">
        <div class="bg-white p-4 rounded shadow border-l-4 border-blue-500">
            <div class="text-sm text-gray-500">Ventas Totales</div>
            <div class="text-2xl font-bold">$<span x-text="kpis.ventas_totales || '0.00'"></span></div>
        </div>
        <div class="bg-white p-4 rounded shadow border-l-4 border-green-500">
            <div class="text-sm text-gray-500">Efectividad de Entrega</div>
            <div class="text-2xl font-bold"><span x-text="kpis.efectividad || '0'"></span>%</div>
        </div>
        <div class="bg-white p-4 rounded shadow border-l-4 border-yellow-500">
            <div class="text-sm text-gray-500">Pedidos Entregados</div>
            <div class="text-2xl font-bold" x-text="kpis.pedidos_entregados || '0'"></div>
        </div>
        <div class="bg-white p-4 rounded shadow border-l-4 border-red-500">
            <div class="text-sm text-gray-500">Recaudación Efectivo</div>
            <div class="text-2xl font-bold">$<span x-text="kpis.recaudacion_efectivo || '0.00'"></span></div>
        </div>
    </div>

    <!-- Charts -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-8">
        <div class="bg-white p-4 rounded shadow">
            <h3 class="font-bold mb-4">Ventas por Día</h3>
            <canvas id="ventasDia"></canvas>
        </div>
        <div class="bg-white p-4 rounded shadow">
            <h3 class="font-bold mb-4">Recaudación por Método</h3>
            <div class="w-2/3 mx-auto">
                <canvas id="metodosPago"></canvas>
            </div>
        </div>
    </div>
    
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8 mb-8">
        <div class="lg:col-span-2 bg-white p-4 rounded shadow">
            <h3 class="font-bold mb-4">Ventas por Camión</h3>
            <canvas id="ventasCamion"></canvas>
        </div>
        <div class="bg-white p-4 rounded shadow overflow-hidden flex flex-col">
            <h3 class="font-bold mb-4">Carritos Abandonados</h3>
            <div class="flex-1 overflow-y-auto">
                <table class="w-full text-sm text-left">
                    <thead>
                        <tr class="bg-gray-50">
                            <th class="p-2">Cliente</th>
                            <th class="p-2 text-right">Monto</th>
                        </tr>
                    </thead>
                    <tbody>
                        <template x-if="carritos.length === 0">
                            <tr><td colspan="2" class="p-4 text-center text-gray-500">No hay carritos abandonados</td></tr>
                        </template>
                        <template x-for="carrito in carritos" :key="carrito.cliente">
                            <tr class="border-b">
                                <td class="p-2" x-text="carrito.cliente"></td>
                                <td class="p-2 text-right" x-text="'$' + Number(carrito.monto).toFixed(2)"></td>
                            </tr>
                        </template>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Stock -->
    <div class="bg-white p-4 rounded shadow">
        <div class="flex items-center justify-between mb-4">
            <h3 class="font-bold">Control de Stock en Ruta</h3>
            <div class="flex gap-2">
                <button @click="stockTab = 'maestro'" :class="stockTab === 'maestro' ? 'bg-red-600 text-white' : 'bg-gray-100 text-gray-700'" class="text-xs px-3 py-1 rounded font-semibold transition-colors">Bodega Central</button>
                <button @click="stockTab = 'camiones'" :class="stockTab === 'camiones' ? 'bg-red-600 text-white' : 'bg-gray-100 text-gray-700'" class="text-xs px-3 py-1 rounded font-semibold transition-colors">Por Camión</button>
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
                                <td class="py-2 font-medium" x-text="p.nombre"></td>
                                <td class="py-2 text-right" x-text="parseFloat(p.disponible || p.cantidad_fisica || 0).toFixed(0)"></td>
                                <td class="py-2 text-right text-blue-600" x-text="parseFloat(p.en_pedidos || 0).toFixed(0)"></td>
                                <td class="py-2 text-right">
                                    <span class="text-xs px-2 py-0.5 rounded font-semibold"
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

<!-- Cargar Chart.js por CDN en layout normalmente, aquí simulamos inicialización -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('alpine:init', () => {
    Alpine.data('dashboard', () => {
        // Función auxiliar para obtener fecha YYYY-MM-DD
        const getFormattedDate = (dateObj) => {
            const year = dateObj.getFullYear();
            const month = String(dateObj.getMonth() + 1).padStart(2, '0');
            const day = String(dateObj.getDate()).padStart(2, '0');
            return `${year}-${month}-${day}`;
        };

        const todayStr = getFormattedDate(new Date());

        // Cargar desde el estado de la sesión (sessionStorage) o default al día actual (hoy)
        const initialFechaInicio = sessionStorage.getItem('dashboard_fecha_inicio') || todayStr;
        const initialFechaFin = sessionStorage.getItem('dashboard_fecha_fin') || todayStr;

        return {
            fechaInicio: initialFechaInicio,
            fechaFin: initialFechaFin,
            loading: true,
            kpis: {
                ventas_totales: '0.00',
                efectividad: '0',
                pedidos_entregados: 0,
                recaudacion_efectivo: '0.00'
            },
            carritos: [],
            stock: { maestro: [], por_camion: [] },
            stockTab: 'maestro',

            // Instancias de Chart.js
            chartVentasDia: null,
            chartMetodosPago: null,
            chartVentasCamion: null,

            // CÁLCULO DE FECHA MÁXIMA PERMITIDA (Máximo 1 Mes a partir de Fecha Inicial)
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

                // 1. La Fecha Final no puede ser anterior a la Fecha Inicial
                if (this.fechaFin < this.fechaInicio) {
                    this.fechaFin = this.fechaInicio;
                }

                // 2. La Fecha Final no puede exceder el límite de 1 mes desde la Fecha Inicial
                const maxPermitida = this.maxFechaFin;
                if (maxPermitida && this.fechaFin > maxPermitida) {
                    this.fechaFin = maxPermitida;
                }

                // Guardar en el estado de la sesión
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

            resetFechasHoy() {
                this.fechaInicio = todayStr;
                this.fechaFin = todayStr;
                this.validarRangoFechas();
                this.cargarDashboard();
            },

            async cargarDashboard() {
                this.loading = true;
                try {
                    const params = `?fecha_inicio=${this.fechaInicio}&fecha_fin=${this.fechaFin}`;

                    const kpisData = await window.api(`/api/dashboard/kpis${params}`).catch(e => {
                        if (e.message === 'Forbidden' || e.message === 'Unauthorized' || e.message.includes('permisos')) {
                            Swal.fire('Acceso Denegado', 'Tu rol actual no tiene permisos para ver el Dashboard Administrativo. Redirigiendo...', 'error');
                            setTimeout(() => window.location.href = '/', 2000);
                        }
                        return null;
                    });
                    const ventasData = await window.api(`/api/dashboard/ventas${params}`).catch(() => null);
                    const recData = await window.api(`/api/dashboard/recaudacion${params}`).catch(() => null);
                    const carritosData = await window.api(`/api/dashboard/carritos-abandonados${params}`).catch(() => []);
                    const stockData = await window.api('/api/dashboard/stock').catch(() => ({ maestro: [], por_camion: [] }));
                    
                    this.carritos = carritosData || [];
                    this.stock = stockData || { maestro: [], por_camion: [] };

                    // Mapear KPIs
                    let ventasTotales = '0.00';
                    let efectividad = '0';
                    let pedidosEntregados = 0;
                    let recEfvo = '0.00';

                    if (ventasData && ventasData.total_periodo) {
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
                        efectividad: efectividad,
                        pedidos_entregados: pedidosEntregados,
                        recaudacion_efectivo: recEfvo
                    };

                    this.renderCharts(ventasData, recData);
                } catch (error) {
                    console.error("Error al cargar el dashboard", error);
                } finally {
                    this.loading = false;
                }
            },

            renderCharts(ventasData, recData) {
                // Prevenir fugas de memoria y errores de canvas destruyendo instancias previas
                if (this.chartVentasDia) this.chartVentasDia.destroy();
                if (this.chartMetodosPago) this.chartMetodosPago.destroy();
                if (this.chartVentasCamion) this.chartVentasCamion.destroy();

                // Chart 1: Ventas por Día
                let labelsVentasDia = [];
                let dataVentasDia = [];
                if (ventasData && ventasData.por_dia && ventasData.por_dia.length > 0) {
                    labelsVentasDia = ventasData.por_dia.map(i => i.fecha);
                    dataVentasDia = ventasData.por_dia.map(i => i.total);
                } else {
                    labelsVentasDia = [this.fechaInicio];
                    dataVentasDia = [0];
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
                            plugins: {
                                legend: { display: true }
                            }
                        }
                    });
                }

                // Chart 2: Metodos de Pago
                let labelsMetodos = [];
                let dataMetodos = [];
                const colors = ['#2ecc71', '#3498db', '#9b59b6', '#f1c40f', '#e74c3c'];
                if (recData && recData.por_metodo_pago && recData.por_metodo_pago.length > 0) {
                    labelsMetodos = recData.por_metodo_pago.map(i => String(i.metodo_pago).toUpperCase());
                    dataMetodos = recData.por_metodo_pago.map(i => i.total);
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
                            responsive: true
                        }
                    });
                }

                // Chart 3: Ventas por Camión
                let labelsCamion = [];
                let dataCamion = [];
                if (ventasData && ventasData.por_camion && ventasData.por_camion.length > 0) {
                    labelsCamion = ventasData.por_camion.map(i => 'Camión ' + i.camion_id);
                    dataCamion = ventasData.por_camion.map(i => i.total);
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
                            responsive: true
                        }
                    });
                }
            }
        };
    });
});
</script>
@endsection
