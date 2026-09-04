@extends('layouts.app')

@section('title', 'Acerca de Nosotros - Fritolay Ambato')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10">
    <!-- Hero Banner -->
    <div class="bg-gradient-to-r from-slate-900 via-slate-800 to-[#E3001B] rounded-3xl p-8 sm:p-12 text-white shadow-xl mb-12 relative overflow-hidden">
        <div class="relative z-10 max-w-3xl">
            <span class="bg-[#F5C518] text-slate-900 font-black text-xs px-3.5 py-1 rounded-full uppercase tracking-wider mb-4 inline-block shadow-2xs">
                Distribución y Logística de Excelencia
            </span>
            <h1 class="text-3xl sm:text-5xl font-black tracking-tight leading-tight mb-4">
                Llevamos el sabor y frescura de <span class="text-[#F5C518]">Fritolay</span> a toda la región.
            </h1>
            <p class="text-gray-200 text-sm sm:text-base font-medium leading-relaxed">
                Somos el centro autorizado de distribución logístico y comercial en Ambato, impulsando a pequeños, medianos y grandes comercios mediante tecnología e-commerce de vanguardia y rutas optimizadas.
            </p>
        </div>
        <div class="absolute -right-10 -bottom-10 opacity-10 pointer-events-none">
            <svg class="w-96 h-96 text-white" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2L2 7l10 5 10-5-10-5zM2 17l10 5 10-5M2 12l10 5 10-5"/></svg>
        </div>
    </div>

    <!-- Misión y Visión Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-8 mb-16">
        <div class="bg-white rounded-2xl p-8 border border-gray-100 shadow-2xs hover:shadow-md transition-all flex flex-col justify-between">
            <div>
                <div class="w-12 h-12 rounded-2xl bg-red-50 text-[#E3001B] flex items-center justify-center font-black text-xl mb-5 border border-red-100">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                </div>
                <h2 class="text-2xl font-black text-slate-900 mb-3 tracking-tight">Nuestra Misión</h2>
                <p class="text-gray-600 text-sm font-medium leading-relaxed">
                    Garantizar el abastecimiento continuo, puntual y eficiente de toda la línea de bocadillos y snacks Fritolay a nuestra red de clientes comerciales en Ambato y zonas aledañas. Combinamos tecnología de trazabilidad en tiempo real, transparencia en cobros y una atención personalizada que impulsa el crecimiento de cada negocio.
                </p>
            </div>
            <div class="mt-6 pt-4 border-t border-gray-100 flex items-center gap-2 text-xs font-extrabold text-[#E3001B]">
                <span>Compromiso con la Calidad</span>
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
            </div>
        </div>

        <div class="bg-white rounded-2xl p-8 border border-gray-100 shadow-2xs hover:shadow-md transition-all flex flex-col justify-between">
            <div>
                <div class="w-12 h-12 rounded-2xl bg-amber-50 text-amber-600 flex items-center justify-center font-black text-xl mb-5 border border-amber-100">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                </div>
                <h2 class="text-2xl font-black text-slate-900 mb-3 tracking-tight">Nuestra Visión</h2>
                <p class="text-gray-600 text-sm font-medium leading-relaxed">
                    Ser reconocidos como el modelo de distribución omnicanal de consumo masivo más innovador, sostenible y automatizado del centro del país para el año 2030, digitalizando el 100% de la cadena de pedidos, despacho e inventarios en ruta.
                </p>
            </div>
            <div class="mt-6 pt-4 border-t border-gray-100 flex items-center gap-2 text-xs font-extrabold text-amber-600">
                <span>Innovación en Logística</span>
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
            </div>
        </div>
    </div>

    <!-- Pilares Corporativos -->
    <div class="mb-16">
        <div class="text-center max-w-2xl mx-auto mb-10">
            <h2 class="text-2xl font-black text-slate-900 tracking-tight">Nuestros Pilares Operativos</h2>
            <p class="text-xs text-gray-500 font-semibold mt-1">Valores que sustentan nuestra operación diaria en Ambato</p>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-3 gap-6">
            <div class="bg-white p-6 rounded-2xl border border-gray-100 text-center shadow-2xs">
                <div class="w-10 h-10 mx-auto rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center font-bold mb-3">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </div>
                <h3 class="font-black text-slate-900 text-sm mb-1">Entregas en Tiempo Rápido</h3>
                <p class="text-xs text-gray-500 font-medium">Algoritmos de ordenamiento espacial (Greedy TSP) para entregas oportunas.</p>
            </div>

            <div class="bg-white p-6 rounded-2xl border border-gray-100 text-center shadow-2xs">
                <div class="w-10 h-10 mx-auto rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center font-bold mb-3">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </div>
                <h3 class="font-black text-slate-900 text-sm mb-1">Transparencia Financiera</h3>
                <p class="text-xs text-gray-500 font-medium">Cuadre exacto de caja, valorización real recaudada y emisión inmediata de notas de crédito.</p>
            </div>

            <div class="bg-white p-6 rounded-2xl border border-gray-100 text-center shadow-2xs">
                <div class="w-10 h-10 mx-auto rounded-xl bg-purple-50 text-purple-600 flex items-center justify-center font-bold mb-3">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                </div>
                <h3 class="font-black text-slate-900 text-sm mb-1">Red Comercial Cercana</h3>
                <p class="text-xs text-gray-500 font-medium">Atención a tenderos y mayoristas con condiciones comerciales adaptadas a su escala.</p>
            </div>
        </div>
    </div>

    <!-- Contacto e Información de Planta -->
    <div class="bg-white rounded-3xl p-8 border border-gray-100 shadow-2xs">
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <div class="lg:col-span-1">
                <h3 class="text-xl font-black text-slate-900 mb-2">Contacto Institucional</h3>
                <p class="text-xs text-gray-500 font-medium mb-4">¿Tienes dudas sobre pedidos, distribución o alianzas comerciales en Ambato?</p>
                <div class="space-y-3 text-xs font-semibold text-gray-700">
                    <div class="flex items-center gap-2.5">
                        <svg class="w-4 h-4 text-[#E3001B]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                        <span>Av. Atahualpa y Av. Los Molinos, Ambato - Ecuador</span>
                    </div>
                    <div class="flex items-center gap-2.5">
                        <svg class="w-4 h-4 text-[#E3001B]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/></svg>
                        <span>+593 3 284-5900 / 1800-FRITOLAY</span>
                    </div>
                    <div class="flex items-center gap-2.5">
                        <svg class="w-4 h-4 text-[#E3001B]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                        <span>contacto.ambato@fritolay-distribucion.ec</span>
                    </div>
                </div>
            </div>

            <div class="lg:col-span-2 bg-gray-50/70 p-6 rounded-2xl border border-gray-100">
                <h4 class="font-extrabold text-slate-900 text-sm mb-2 uppercase tracking-wider text-xs">Horarios de Atención Comercial</h4>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-xs font-semibold text-gray-600 mb-4">
                    <div>
                        <span class="block text-gray-400 font-bold uppercase text-[10px]">Atención Administrativa:</span>
                        <span>Lunes a Viernes: 07:30 - 18:00</span>
                    </div>
                    <div>
                        <span class="block text-gray-400 font-bold uppercase text-[10px]">Despacho y Carga de Camiones:</span>
                        <span>Lunes a Sábado: 05:00 - 15:00</span>
                    </div>
                </div>
                <div class="bg-white p-3 rounded-xl border border-gray-200 text-gray-500 text-[11px] font-medium">
                    Plataforma operativa 24/7 para recepción de pedidos digitales e-commerce.
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
