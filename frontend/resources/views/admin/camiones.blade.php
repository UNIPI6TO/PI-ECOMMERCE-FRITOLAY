@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto py-8 px-4" x-data="camiones()">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold">GestiÃ³n de Camiones</h1>
        <button @click="modal = true" class="bg-gray-800 text-white px-4 py-2 rounded font-medium">+ Nuevo CamiÃ³n</button>
    </div>

    <div class="bg-white rounded shadow overflow-hidden">
        <table class="w-full text-left">
            <thead class="bg-gray-100">
                <tr>
                    <th class="p-4">Placa</th>
                    <th class="p-4">DescripciÃ³n</th>
                    <th class="p-4">Chofer Asignado</th>
                    <th class="p-4">Estado</th>
                    <th class="p-4 text-center">Acciones</th>
                </tr>
            </thead>
            <tbody>
                <template x-for="c in listado" :key="c.id">
                    <tr class="border-b hover:bg-gray-50">
                        <td class="p-4 font-bold" x-text="c.placa"></td>
                        <td class="p-4" x-text="c.descripcion"></td>
                        <td class="p-4">
                            <select x-model="c.chofer_id" class="border rounded p-1 text-sm w-full bg-white">
                                <option value="">Sin Asignar</option>
                                <template x-for="ch in choferes" :key="ch.id">
                                    <option :value="ch.id" x-text="ch.nombre"></option>
                                </template>
                            </select>
                        </td>
                        <td class="p-4">
                            <select x-model="c.estado" class="border rounded p-1 text-sm font-semibold" :class="{'text-green-600': c.estado === 'ACTIVO', 'text-yellow-600': c.estado === 'MANTENIMIENTO', 'text-red-600': c.estado === 'INACTIVO'}">
                                <option value="ACTIVO">ACTIVO</option>
                                <option value="MANTENIMIENTO">MANTENIMIENTO</option>
                                <option value="INACTIVO">INACTIVO</option>
                            </select>
                        </td>
                        <td class="p-4 text-center">
                            <button @click="guardarCambios(c)" class="text-sm bg-blue-100 text-blue-700 px-3 py-1 rounded font-medium hover:bg-blue-200">Guardar</button>
                        </td>
                    </tr>
                </template>
            </tbody>
        </table>
    </div>

    <!-- Modal Crear -->
    <div x-show="modal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
        <div class="bg-white p-6 rounded-lg w-96">
            <h3 class="font-bold text-lg mb-4">Registrar CamiÃ³n</h3>
            <div class="space-y-4">
                <div>
                    <label class="block text-sm mb-1">Placa</label>
                    <input type="text" x-model="nuevo.placa" placeholder="ABC-1234" class="w-full border rounded px-3 py-2 uppercase">
                </div>
                <div>
                    <label class="block text-sm mb-1">DescripciÃ³n / Modelo</label>
                    <input type="text" x-model="nuevo.descripcion" class="w-full border rounded px-3 py-2">
                </div>
            </div>
            <div class="flex justify-end space-x-2 mt-6">
                <button @click="modal = false" class="px-4 py-2 border rounded">Cancelar</button>
                <button @click="crear" class="px-4 py-2 bg-gray-800 text-white rounded">Registrar</button>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('alpine:init', () => {
    Alpine.data('camiones', () => ({
        modal: false,
        choferes: [
            {id: 1, nombre: 'Luis (CHOFER)'},
            {id: 2, nombre: 'Mario (CHOFER)'}
        ],
        listado: [
            {id: 1, placa: 'PBA-7890', descripcion: 'Hino 300', chofer_id: 1, estado: 'ACTIVO'},
            {id: 2, placa: 'PCX-1234', descripcion: 'NPR', chofer_id: '', estado: 'MANTENIMIENTO'}
        ],
        nuevo: {placa: '', descripcion: ''},

        crear() {
            this.modal = false;
            // POST api/camiones
        },
        guardarCambios(camion) {
            // PUT api/camiones/{id}
            Swal.fire('Éxito', 'Cambios guardados en ' + camion.placa, 'success');
        }
    }));
});
</script>
@endsection

