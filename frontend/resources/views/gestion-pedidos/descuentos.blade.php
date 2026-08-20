@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto py-8 px-4" x-data="descuentos()">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold">Gestión de Descuentos</h1>
        <button @click="modal = true" class="bg-blue-600 text-white px-4 py-2 rounded">+ Nuevo Descuento</button>
    </div>

    <div class="bg-white rounded shadow overflow-hidden">
        <table class="w-full text-left">
            <thead class="bg-gray-100">
                <tr>
                    <th class="p-4">Tipo</th>
                    <th class="p-4">Detalle</th>
                    <th class="p-4">Porcentaje</th>
                    <th class="p-4">Caducidad</th>
                    <th class="p-4 text-center">Acciones</th>
                </tr>
            </thead>
            <tbody>
                <template x-for="d in listado" :key="d.id">
                    <tr class="border-b hover:bg-gray-50">
                        <td class="p-4">
                            <span class="px-2 py-1 text-xs rounded bg-purple-100 text-purple-800" x-text="d.tipo"></span>
                        </td>
                        <td class="p-4" x-text="d.detalle"></td>
                        <td class="p-4" x-text="`${d.porcentaje}%`"></td>
                        <td class="p-4" x-text="d.caducidad"></td>
                        <td class="p-4 text-center">
                            <button @click="eliminar(d.id)" class="text-red-600 hover:underline text-sm">Eliminar</button>
                        </td>
                    </tr>
                </template>
            </tbody>
        </table>
    </div>

    <!-- Modal Crear -->
    <div x-show="modal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
        <div class="bg-white p-6 rounded-lg w-96">
            <h3 class="font-bold text-lg mb-4">Crear Descuento</h3>
            <div class="space-y-4">
                <div>
                    <label class="block text-sm mb-1">Tipo</label>
                    <select x-model="nuevo.tipo" class="w-full border rounded px-3 py-2">
                        <option value="GENERAL">General</option>
                        <option value="CLIENTE">Por Cliente</option>
                        <option value="METODO_PAGO">Por Método de Pago</option>
                    </select>
                </div>
                <div x-show="nuevo.tipo === 'CLIENTE'">
                    <label class="block text-sm mb-1">Cliente ID</label>
                    <input type="text" x-model="nuevo.cliente_id" class="w-full border rounded px-3 py-2">
                </div>
                <div x-show="nuevo.tipo === 'METODO_PAGO'">
                    <label class="block text-sm mb-1">Método de Pago</label>
                    <select x-model="nuevo.metodo_pago" class="w-full border rounded px-3 py-2">
                        <option value="EFECTIVO">Efectivo</option>
                        <option value="DE_UNA">De Una</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm mb-1">Porcentaje (%)</label>
                    <input type="number" x-model="nuevo.porcentaje" class="w-full border rounded px-3 py-2">
                </div>
                <div>
                    <label class="block text-sm mb-1">Fecha de Caducidad</label>
                    <input type="date" x-model="nuevo.fecha_caducidad" class="w-full border rounded px-3 py-2">
                </div>
            </div>
            <div class="flex justify-end space-x-2 mt-6">
                <button @click="modal = false" class="px-4 py-2 border rounded">Cancelar</button>
                <button @click="guardar" class="px-4 py-2 bg-blue-600 text-white rounded">Guardar</button>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('alpine:init', () => {
    Alpine.data('descuentos', () => ({
        modal: false,
        listado: [
            {id: 1, tipo: 'GENERAL', detalle: 'Promoción Verano', porcentaje: 5, caducidad: '2024-12-31'},
            {id: 2, tipo: 'METODO_PAGO', detalle: 'Pago en Efectivo', porcentaje: 10, caducidad: '2024-06-30'}
        ],
        nuevo: {tipo: 'GENERAL', cliente_id: '', metodo_pago: 'EFECTIVO', porcentaje: 5, fecha_caducidad: ''},

        guardar() {
            // POST /api/descuentos
            this.modal = false;
        },
        eliminar(id) {
            // DELETE /api/descuentos/{id}
            this.listado = this.listado.filter(d => d.id !== id);
        }
    }));
});
</script>
@endsection
