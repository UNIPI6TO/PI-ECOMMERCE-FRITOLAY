@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto py-8 px-4" x-data="dashboard()">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-3xl font-bold">Dashboard Gerencial</h1>
        <input type="text" placeholder="last 30 days" class="border px-4 py-2 rounded w-64">
    </div>

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

<!-- Cargar Chart.js por CDN en layout normalmente, aquí simulamos inicialización -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('alpine:init', () => {
    Alpine.data('dashboard', () => ({
        kpis: {
            ingresos_mes: 0,
            pedidos_pendientes: 0,
            entregas_hoy: 0,
            productos_bajo_stock: 0
        },
        carritos: [],
        stock: { maestro: [], por_camion: [] },
        stockTab: 'maestro',

        async init() {
            try {
                // Obtener datos
                const kpisData = await window.api('/api/dashboard/kpis').catch(e => {
                    if (e.message === 'Forbidden' || e.message === 'Unauthorized' || e.message.includes('permisos')) {
                        Swal.fire('Acceso Denegado', 'Tu rol actual no tiene permisos para ver el Dashboard Administrativo. Redirigiendo...', 'error');
                        setTimeout(() => window.location.href = '/', 2000);
                    }
                    return null;
                });
                const ventasData = await window.api('/api/dashboard/ventas').catch(() => null);
                const recData = await window.api('/api/dashboard/recaudacion').catch(() => null);
                const carritosData = await window.api('/api/dashboard/carritos-abandonados').catch(() => []);
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

                // Chart: Ventas por Día
                let labelsVentasDia = [];
                let dataVentasDia = [];
                if (ventasData && ventasData.por_dia) {
                    labelsVentasDia = ventasData.por_dia.map(i => i.fecha);
                    dataVentasDia = ventasData.por_dia.map(i => i.total);
                }

                new Chart(document.getElementById('ventasDia'), {
                    type: 'line',
                    data: {
                        labels: labelsVentasDia,
                        datasets: [{ 
                            label: 'Ventas ($)', 
                            data: dataVentasDia, 
                            borderColor: '#E3001B', 
                            tension: 0.1 
                        }]
                    }
                });

                // Chart: Metodos de Pago
                let labelsMetodos = [];
                let dataMetodos = [];
                const colors = ['#2ecc71', '#3498db', '#9b59b6', '#f1c40f', '#e74c3c'];
                if (recData && recData.por_metodo_pago) {
                    labelsMetodos = recData.por_metodo_pago.map(i => String(i.metodo_pago).toUpperCase());
                    dataMetodos = recData.por_metodo_pago.map(i => i.total);
                }

                new Chart(document.getElementById('metodosPago'), {
                    type: 'pie',
                    data: {
                        labels: labelsMetodos.length ? labelsMetodos : ['Sin Datos'],
                        datasets: [{ 
                            data: dataMetodos.length ? dataMetodos : [1], 
                            backgroundColor: colors
                        }]
                    }
                });

                // Chart: Ventas por Camión
                let labelsCamion = [];
                let dataCamion = [];
                if (ventasData && ventasData.por_camion) {
                    labelsCamion = ventasData.por_camion.map(i => 'Camión ' + i.camion_id);
                    dataCamion = ventasData.por_camion.map(i => i.total);
                }

                new Chart(document.getElementById('ventasCamion'), {
                    type: 'bar',
                    data: {
                        labels: labelsCamion, 
                        datasets: [{ 
                            label: 'Total Vendido ($)', 
                            data: dataCamion, 
                            backgroundColor: '#3498db' 
                        }]
                    }
                });
            } catch (error) {
                console.error("Error al cargar el dashboard", error);
            }
        }
    }));
});
</script>
@endsection
