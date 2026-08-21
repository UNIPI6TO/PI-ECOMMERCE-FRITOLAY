@extends('layouts.app')

@section('content')
<div class="max-w-4xl mx-auto py-8 px-4" x-data="cierreGuia()">
    <h1 class="text-2xl font-bold mb-6">Cierre de GuÃ­a de RemisiÃ³n</h1>

    <div class="bg-white p-6 rounded shadow mb-6">
        <h2 class="font-bold text-lg mb-4">ClasificaciÃ³n de MercaderÃ­a</h2>
        <div class="space-y-4">
            <template x-for="item in productos" :key="item.id">
                <div class="flex items-center justify-between border-b pb-4">
                    <div>
                        <div class="font-medium" x-text="item.nombre"></div>
                        <div class="text-sm text-gray-500">Devueltos: <span x-text="item.devueltos"></span></div>
                    </div>
                    <div class="flex gap-4">
                        <label class="flex flex-col items-center">
                            <span class="text-sm mb-1 text-green-600">Buen Estado</span>
                            <input type="number" x-model.number="item.buen_estado" class="w-20 border rounded px-2 py-1 text-center" min="0" :max="item.devueltos">
                        </label>
                        <label class="flex flex-col items-center">
                            <span class="text-sm mb-1 text-red-600">Mal Estado</span>
                            <input type="number" :value="item.devueltos - item.buen_estado" disabled class="w-20 border rounded px-2 py-1 text-center bg-gray-100">
                        </label>
                    </div>
                </div>
            </template>
        </div>
    </div>

    <div class="bg-white p-6 rounded shadow mb-6">
        <h2 class="font-bold text-lg mb-4">Resumen Financiero</h2>
        <div class="grid grid-cols-2 gap-4 text-sm">
            <div>Total Ventas Efectivo:</div>
            <div class="text-right font-bold text-green-600">$<span x-text="totales.efectivo"></span></div>
            <div>Total Ventas DepÃ³sito/Transferencia:</div>
            <div class="text-right font-bold text-blue-600">$<span x-text="totales.bancos"></span></div>
            <hr class="col-span-2">
            <div class="font-bold">Total Recaudado:</div>
            <div class="text-right font-bold">$<span x-text="totales.efectivo + totales.bancos"></span></div>
        </div>
    </div>

    <button @click="confirmarCierre" class="w-full bg-[#E3001B] hover:bg-red-700 text-white font-bold py-3 rounded">
        Confirmar Cierre de GuÃ­a
    </button>
</div>

<script>
document.addEventListener('alpine:init', () => {
    Alpine.data('cierreGuia', () => ({
        productos: [
            {id: 1, nombre: 'Papas Fritas 50g', devueltos: 10, buen_estado: 10},
            {id: 2, nombre: 'Doritos 100g', devueltos: 5, buen_estado: 3}
        ],
        totales: {
            efectivo: 150.50,
            bancos: 300.00
        },

        async confirmarCierre() {
            // PATCH /api/guias-remision/{id}/cerrar
            Swal.fire('Éxito', 'Guía cerrada exitosamente', 'success');
            window.location.href = '/gestion-pedidos';
        }
    }));
});
</script>
@endsection


