@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto py-8 px-4" x-data="camiones()">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold">Gestión de Flota (Camiones)</h1>
        <div class="flex items-center space-x-4">
            <div class="flex items-center space-x-2 text-sm">
                <span class="text-gray-600">Mostrar:</span>
                <select x-model="perPage" @change="currentPage = 1" class="border-gray-300 rounded-md text-sm py-1 pl-2 pr-8 focus:ring-primary focus:border-primary border">
                    <option :value="5">5</option>
                    <option :value="10">10</option>
                    <option :value="20">20</option>
                    <option :value="50">50</option>
                </select>
                <span class="text-gray-600">registros</span>
            </div>
            <button @click="modal = true" class="bg-gray-800 hover:bg-red-800 text-white px-4 py-2 rounded font-medium transition-colors shadow-sm">+ Nuevo Camión</button>
        </div>
    </div>

    <div class="bg-white rounded shadow overflow-hidden">
        <table class="w-full text-left">
            <thead class="bg-gray-100">
                <tr>
                    <th class="p-4">Placa</th>
                    <th class="p-4">Descripción</th>
                    <th class="p-4">Chofer Asignado</th>
                    <th class="p-4">Estado</th>
                    <th class="p-4 text-center">Acciones</th>
                </tr>
            </thead>
            <tbody>
                <template x-if="paginatedListado.length === 0">
                    <tr><td colspan="5" class="p-4 text-center text-gray-500">No hay camiones para mostrar</td></tr>
                </template>
                <template x-for="c in paginatedListado" :key="c.id">
                    <tr class="border-b hover:bg-gray-50">
                        <td class="p-4 font-bold" x-text="c.placa"></td>
                        <td class="p-4" x-text="c.descripcion"></td>
                        <td class="p-4">
                            <select x-model="c.chofer_id" class="border rounded p-1 text-sm w-full bg-white">
                                <option value="">Sin Asignar</option>
                                <template x-for="ch in choferes" :key="ch.id">
                                    <option :value="ch.id" x-text="ch.nombre" :selected="ch.id == c.chofer_id"></option>
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
                            <button @click="guardarCambios(c)" class="bg-blue-600 text-white px-3 py-1 rounded text-sm mb-1 hover:bg-blue-700">Guardar</button>
                        </td>
                    </tr>
                </template>
            </tbody>
        </table>

        <!-- Paginador -->
        <div class="p-4 border-t border-gray-100 flex items-center justify-between bg-gray-50">
            <div class="text-sm text-gray-500">
                Mostrando pág <span class="font-medium text-gray-800" x-text="currentPage"></span> de <span class="font-medium text-gray-800" x-text="totalPages"></span> 
                (<span x-text="listado.length"></span> registros totales)
            </div>
            <div class="flex space-x-2">
                <button @click="prevPage" :disabled="currentPage === 1" class="px-3 py-1 border border-gray-300 rounded-md text-sm font-medium text-gray-700 hover:bg-gray-100 transition-colors disabled:opacity-50 disabled:cursor-not-allowed">Anterior</button>
                <button @click="nextPage" :disabled="currentPage === totalPages" class="px-3 py-1 border border-gray-300 rounded-md text-sm font-medium text-gray-700 hover:bg-gray-100 transition-colors disabled:opacity-50 disabled:cursor-not-allowed">Siguiente</button>
            </div>
        </div>
    </div>

    <!-- Modal Crear -->
    <div x-show="modal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
        <div class="bg-white p-6 rounded-lg w-96">
            <h3 class="font-bold text-lg mb-4">Registrar Camión</h3>
            <div class="space-y-4">
                <div>
                    <label class="block text-sm mb-1">Placa</label>
                    <input type="text" x-model="nuevo.placa" placeholder="ABC-1234" class="w-full border rounded px-3 py-2 uppercase">
                </div>
                <div>
                    <label class="block text-sm mb-1">Descripción / Modelo</label>
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
        choferes: [],
        listado: [],
        nuevo: {placa: '', descripcion: ''},
        
        // Paginación
        currentPage: 1,
        perPage: 10,
        
        get totalPages() {
            return Math.ceil(this.listado.length / this.perPage) || 1;
        },
        
        get paginatedListado() {
            const start = (this.currentPage - 1) * this.perPage;
            return this.listado.slice(start, start + this.perPage);
        },

        nextPage() {
            if (this.currentPage < this.totalPages) this.currentPage++;
        },
        
        prevPage() {
            if (this.currentPage > 1) this.currentPage--;
        },

        async init() {
            await this.fetchCamiones();
        },

        async fetchCamiones() {
            try {
                const data = await window.api('/api/camiones');
                this.listado = data;
                // Also fetch choferes (usuarios con rol chofer)
                const users = await window.api('/api/admin/usuarios');
                this.choferes = users.filter(u => u.rol === 'chofer' || u.rol === 'CHOFER');
            } catch (error) {
                console.error("Error al cargar camiones:", error);
            }
        },

        async crear() {
            try {
                await window.api('/api/camiones', {
                    method: 'POST',
                    body: JSON.stringify(this.nuevo)
                });
                this.modal = false;
                this.nuevo = {placa: '', descripcion: ''};
                await this.fetchCamiones();
                Swal.fire({ icon: 'success', title: 'Éxito', text: 'Camión registrado', toast: true, position: 'bottom', showConfirmButton: false, timer: 3000 });
            } catch (e) {
                Swal.fire('Error', e.message, 'error');
            }
        },

        async guardarCambios(camion) {
            try {
                await window.api(`/api/camiones/${camion.id}/chofer`, {
                    method: 'PATCH',
                    body: JSON.stringify({ chofer_id: camion.chofer_id })
                });
                await window.api(`/api/camiones/${camion.id}/estado`, {
                    method: 'PATCH',
                    body: JSON.stringify({ estado: camion.estado })
                });
                Swal.fire('Éxito', 'Cambios guardados en ' + camion.placa, 'success');
            } catch (e) {
                Swal.fire('Error', e.message, 'error');
            }
        }
    }));
});
</script>
@endsection

