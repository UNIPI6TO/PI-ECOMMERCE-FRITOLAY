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
            <div class="text-2xl font-bold">$12,450.00</div>
        </div>
        <div class="bg-white p-4 rounded shadow border-l-4 border-green-500">
            <div class="text-sm text-gray-500">Efectividad de Entrega</div>
            <div class="text-2xl font-bold">94.5%</div>
        </div>
        <div class="bg-white p-4 rounded shadow border-l-4 border-yellow-500">
            <div class="text-sm text-gray-500">Pedidos Entregados</div>
            <div class="text-2xl font-bold">845</div>
        </div>
        <div class="bg-white p-4 rounded shadow border-l-4 border-red-500">
            <div class="text-sm text-gray-500">Recaudación Efectivo</div>
            <div class="text-2xl font-bold">$4,120.50</div>
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
        init() {
            setTimeout(() => {
                new Chart(document.getElementById('ventasDia'), {
                    type: 'line',
                    data: {
                        labels: ['Lun', 'Mar', 'Mie', 'Jue', 'Vie', 'Sab', 'Dom'],
                        datasets: [{ label: 'Ventas ($)', data: [1200, 1900, 1500, 2200, 2800, 1800, 1000], borderColor: '#E3001B', tension: 0.1 }]
                    }
                });

                new Chart(document.getElementById('metodosPago'), {
                    type: 'pie',
                    data: {
                        labels: ['Efectivo', 'Depósito', 'De Una', 'Tarjeta', 'Crédito'],
                        datasets: [{ data: [40, 20, 25, 10, 5], backgroundColor: ['#2ecc71', '#3498db', '#9b59b6', '#f1c40f', '#e74c3c'] }]
                    }
                });

                new Chart(document.getElementById('ventasCamion'), {
                    type: 'bar',
                    options: { indexAxis: 'y' },
                    data: {
                        labels: ['CAM-01', 'CAM-02', 'CAM-03', 'CAM-04'],
                        datasets: [{ label: 'Ventas ($)', data: [4500, 3200, 2800, 1950], backgroundColor: '#F5C518' }]
                    }
                });
            }, 200);
        }
    }));
});
</script>
@endsection
