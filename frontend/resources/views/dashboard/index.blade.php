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
                        <tr class="border-b"><td class="p-2">Juan Perez</td><td class="p-2 text-right">$45.00</td></tr>
                        <tr class="border-b"><td class="p-2">Maria Lopez</td><td class="p-2 text-right">$12.50</td></tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Stock -->
    <div class="bg-white p-4 rounded shadow">
        <h3 class="font-bold mb-4">Control de Stock en Ruta</h3>
        <!-- Implementación de tabs Alpine para ver inventario de bodega vs camiones -->
        <div class="text-gray-500 text-sm p-4 border rounded text-center">Vista de stock detallada</div>
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

        async init() {
            try {
                // Fetch KPIs
                const kpisData = await window.api('/api/dashboard/kpis').catch(() => null);
                if (kpisData) {
                    this.kpis = kpisData;
                }

                // Fetch Ventas
                const ventasData = await window.api('/api/dashboard/ventas').catch(() => null);
                
                new Chart(document.getElementById('ventasDia'), {
                    type: 'line',
                    data: {
                        labels: ventasData ? ventasData.labels : [],
                        datasets: [{ 
                            label: 'Ventas ($)', 
                            data: ventasData ? ventasData.data : [], 
                            borderColor: '#E3001B', 
                            tension: 0.1 
                        }]
                    }
                });

                new Chart(document.getElementById('metodosPago'), {
                    type: 'pie',
                    data: {
                        labels: ['Efectivo', 'Depósito', 'De Una', 'Tarjeta', 'Crédito'],
                        datasets: [{ 
                            data: [], // Replace with real data when recaudacion API is implemented
                            backgroundColor: ['#2ecc71', '#3498db', '#9b59b6', '#f1c40f', '#e74c3c'] 
                        }]
                    }
                });

                new Chart(document.getElementById('ventasCamion'), {
                    type: 'bar',
                    data: {
                        labels: [], // Replace with real data 
                        datasets: [{ 
                            label: 'Total Vendido ($)', 
                            data: [], 
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
