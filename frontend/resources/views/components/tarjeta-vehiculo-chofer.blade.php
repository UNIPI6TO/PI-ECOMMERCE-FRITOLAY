<!-- Componente Blade: Tarjeta de Vehículo Asignado al Chofer -->
@props([
    'vehiculo' => null,
    'placa' => null,
    'descripcion' => null,
])

@php
    $placaDisplay = $vehiculo['placa'] ?? ($vehiculo->placa ?? $placa ?? 'N/A');
    $descripcionDisplay = $vehiculo['descripcion'] ?? ($vehiculo->descripcion ?? $descripcion ?? 'Camión de Reparto Fritolay');
@endphp

<div class="bg-slate-900 text-white rounded-2xl p-3.5 sm:p-4 shadow-md border border-slate-800 mb-4 flex items-center justify-between gap-3">
    <div class="flex items-center gap-3 min-w-0">
        <!-- Ícono del Vehículo -->
        <div class="w-10 h-10 sm:w-11 sm:h-11 rounded-xl bg-amber-400/10 border border-amber-400/20 text-amber-400 flex items-center justify-center font-black text-lg shrink-0 shadow-2xs">
            🚚
        </div>

        <!-- Placa y Descripción -->
        <div class="min-w-0">
            <div class="flex items-center gap-2">
                <span class="text-[10px] uppercase tracking-wider font-extrabold text-slate-400">Vehículo:</span>
                <span class="px-2 py-0.5 rounded-md bg-amber-400 text-slate-950 font-black text-xs tracking-wider uppercase shadow-2xs font-mono">
                    {{ $placaDisplay }}
                </span>
            </div>
            <p class="text-xs font-semibold text-slate-300 truncate mt-0.5" title="{{ $descripcionDisplay }}">
                {{ $descripcionDisplay }}
            </p>
        </div>
    </div>

    <!-- Indicador LED Estado Activo -->
    <div class="flex items-center gap-1.5 shrink-0 bg-slate-800/80 px-2.5 py-1 rounded-full border border-slate-700">
        <span class="w-2 h-2 rounded-full bg-emerald-400 animate-pulse"></span>
        <span class="text-[10px] font-black uppercase text-emerald-300 tracking-wider">Asignado</span>
    </div>
</div>
