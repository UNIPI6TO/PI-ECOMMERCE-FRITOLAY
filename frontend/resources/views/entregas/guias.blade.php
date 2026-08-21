@extends('layouts.app')

@section('content')
<div class="max-w-4xl mx-auto py-8 px-4" x-data="guiasActivas()">
    <h1 class="text-2xl font-bold mb-6">Mis Rutas Asignadas</h1>

    <div class="space-y-4">
        <template x-for="guia in guias" :key="guia.id">
            <div class="bg-white p-6 rounded shadow border-l-4 border-[#F5C518] flex items-center justify-between">
                <div>
                    <h2 class="text-xl font-bold">Ruta #<span x-text="guia.id"></span></h2>
                    <p class="text-gray-600 text-sm mt-1"><span x-text="guia.pedidos_count"></span> pedidos asignados</p>
                    <p class="text-gray-500 text-xs mt-1">Fecha: <span x-text="guia.fecha"></span></p>
                </div>
                <div>
                    <a :href="`/entregas/mapa/${guia.id}`" class="bg-[#E3001B] text-white px-6 py-2 rounded font-semibold hover:bg-red-700">
                        Iniciar Ruta
                    </a>
                </div>
            </div>
        </template>
        <div x-show="guias.length === 0" class="text-center text-gray-500 py-12">
            No tienes rutas asignadas para hoy.
        </div>
    </div>
</div>

<script>
document.addEventListener('alpine:init', () => {
    Alpine.data('guiasActivas', () => ({
        guias: [],
        async init() {
            try {
                this.guias = await window.api('/api/guias-ruta');
            } catch (error) {
                console.error("Error al cargar guias:", error);
            }
        }
    }));
});
</script>
@endsection
