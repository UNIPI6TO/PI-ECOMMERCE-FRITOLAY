@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto py-8 px-4">
    <h1 class="text-3xl font-bold mb-8">Administración del Sistema</h1>
    
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <a href="/admin/usuarios" class="bg-white p-6 rounded-lg shadow hover:shadow-lg transition flex flex-col items-center text-center group">
            <div class="w-16 h-16 bg-blue-100 text-blue-600 rounded-full flex items-center justify-center mb-4 group-hover:bg-blue-600 group-hover:text-white transition">
                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>
            </div>
            <h2 class="font-bold text-lg">Usuarios Empleados</h2>
            <p class="text-sm text-gray-500 mt-2">Gestión de operadores y choferes</p>
        </a>
        
        <a href="/admin/camiones" class="bg-white p-6 rounded-lg shadow hover:shadow-lg transition flex flex-col items-center text-center group">
            <div class="w-16 h-16 bg-green-100 text-green-600 rounded-full flex items-center justify-center mb-4 group-hover:bg-green-600 group-hover:text-white transition">
                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"></path></svg>
            </div>
            <h2 class="font-bold text-lg">Gestión de Flota</h2>
            <p class="text-sm text-gray-500 mt-2">Camiones y asignaciones</p>
        </a>

        <a href="/gestion-pedidos/descuentos" class="bg-white p-6 rounded-lg shadow hover:shadow-lg transition flex flex-col items-center text-center group">
            <div class="w-16 h-16 bg-purple-100 text-purple-600 rounded-full flex items-center justify-center mb-4 group-hover:bg-purple-600 group-hover:text-white transition">
                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"></path></svg>
            </div>
            <h2 class="font-bold text-lg">Descuentos</h2>
            <p class="text-sm text-gray-500 mt-2">Reglas de precios y promociones</p>
        </a>

        <a href="/dashboard" class="bg-white p-6 rounded-lg shadow hover:shadow-lg transition flex flex-col items-center text-center group">
            <div class="w-16 h-16 bg-yellow-100 text-yellow-600 rounded-full flex items-center justify-center mb-4 group-hover:bg-yellow-500 group-hover:text-white transition">
                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path></svg>
            </div>
            <h2 class="font-bold text-lg">Dashboard KPIs</h2>
            <p class="text-sm text-gray-500 mt-2">Métricas e informes gerenciales</p>
        </a>
    </div>

    <div class="bg-white rounded-lg shadow p-6">
        <h2 class="font-bold text-lg mb-4 border-b pb-2">Últimas Actividades (Bitácora)</h2>
        <ul class="space-y-3 text-sm">
            <li class="flex text-gray-600"><span class="w-32 font-medium">10:45 AM</span> <span>Admin aprobó pago pedido #PED-101</span></li>
            <li class="flex text-gray-600"><span class="w-32 font-medium">09:30 AM</span> <span>Operador cerró guía #45</span></li>
            <li class="flex text-gray-600"><span class="w-32 font-medium">08:15 AM</span> <span>Chofer Luis inició ruta CAM-1</span></li>
        </ul>
    </div>
</div>
@endsection
