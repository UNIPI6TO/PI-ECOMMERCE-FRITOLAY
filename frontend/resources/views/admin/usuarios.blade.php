@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto py-8 px-4" x-data="usuarios()">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold">Gestión de Usuarios Empleados</h1>
        <div class="flex items-center space-x-4">
            <div class="flex items-center space-x-2 text-sm">
                <span class="text-gray-600">Mostrar:</span>
                <select x-model="perPage" @change="currentPage = 1" class="border-gray-300 rounded-md text-sm py-1 pl-2 pr-8 focus:ring-primary focus:border-primary border">
                    <option :value="5">5</option>
                    <option :value="10">10</option>
                    <option :value="20">20</option>
                    <option :value="50">50</option>
                </select>
            </div>
            <button @click="openModal()" class="bg-primary hover:bg-red-800 text-white px-4 py-2 rounded font-medium transition-colors shadow-sm">+ Nuevo Usuario</button>
        </div>
    </div>

    <div class="bg-white rounded-lg shadow-sm border border-gray-100 overflow-hidden">
        <table class="w-full text-left">
            <thead class="bg-neutral-light border-b border-gray-100">
                <tr>
                    <th class="p-4 text-gray-600 font-medium">Nombre</th>
                    <th class="p-4 text-gray-600 font-medium">Email</th>
                    <th class="p-4 text-gray-600 font-medium">Rol</th>
                    <th class="p-4 text-gray-600 font-medium">Estado</th>
                    <th class="p-4 text-center text-gray-600 font-medium">Acciones</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                <template x-if="paginatedListado.length === 0">
                    <tr><td colspan="5" class="p-4 text-center text-gray-500">No hay usuarios para mostrar</td></tr>
                </template>
                <template x-for="u in paginatedListado" :key="u.id">
                    <tr class="hover:bg-gray-50 transition-colors">
                        <td class="p-4 font-medium text-gray-800" x-text="u.nombre"></td>
                        <td class="p-4 text-gray-600" x-text="u.email"></td>
                        <td class="p-4">
                            <span class="px-2 py-1 text-xs font-semibold rounded-full bg-blue-50 text-blue-700 border border-blue-100" x-text="u.rol"></span>
                        </td>
                        <td class="p-4">
                            <span class="px-2 py-1 text-xs font-semibold rounded-full border" 
                                  :class="u.activo ? 'bg-green-50 text-green-700 border-green-100' : 'bg-red-50 text-red-700 border-red-100'" 
                                  x-text="u.activo ? 'Activo' : 'Inactivo'"></span>
                        </td>
                        <td class="p-4 text-center">
                            <div class="flex items-center justify-center space-x-3">
                                <!-- Botón Editar -->
                                <button @click="openModal(u)" title="Editar" class="text-blue-500 hover:text-blue-700 transition-colors">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                                </button>

                                <!-- Botón Inactivar / Activar -->
                                <button @click="toggleEstado(u)" :title="u.activo ? 'Inactivar' : 'Activar'" class="transition-colors" :class="u.activo ? 'text-red-500 hover:text-red-700' : 'text-green-500 hover:text-green-700'">
                                    <svg x-show="u.activo" xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"></path></svg>
                                    <svg x-show="!u.activo" xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                </button>
                                
                                <!-- Botón Reset Pass -->
                                <button @click="resetPass(u)" title="Resetear Contraseña" class="text-secondary hover:text-yellow-600 transition-colors">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"></path></svg>
                                </button>
                            </div>
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

    <!-- Modal Formulario -->
    <div x-show="modal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 transition-opacity" x-transition>
        <div class="bg-white p-6 rounded-lg w-full max-w-md shadow-xl" @click.away="modal = false">
            <h3 class="font-bold text-xl mb-4 text-gray-800" x-text="form.id ? 'Editar Empleado' : 'Crear Empleado'"></h3>
            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Nombre Completo</label>
                    <input type="text" x-model="form.nombre" class="w-full border-gray-300 rounded-md shadow-sm focus:border-primary focus:ring focus:ring-primary focus:ring-opacity-50 px-3 py-2 border">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                    <input type="email" x-model="form.email" class="w-full border-gray-300 rounded-md shadow-sm focus:border-primary focus:ring focus:ring-primary focus:ring-opacity-50 px-3 py-2 border">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Rol</label>
                    <select x-model="form.rol" class="w-full border-gray-300 rounded-md shadow-sm focus:border-primary focus:ring focus:ring-primary focus:ring-opacity-50 px-3 py-2 border">
                        <option value="administrador">Administrador</option>
                        <option value="operador">Operador</option>
                        <option value="chofer">Chofer</option>
                    </select>
                </div>
                <template x-if="!form.id">
                    <div class="text-xs text-gray-600 bg-gray-50 p-3 rounded border border-gray-200 flex items-start">
                        <svg class="w-4 h-4 text-gray-400 mr-1 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        La contraseña por defecto será igual a su email. Podrá cambiarla luego.
                    </div>
                </template>
            </div>
            <div class="flex justify-end space-x-3 mt-6">
                <button @click="modal = false" class="px-4 py-2 border border-gray-300 text-gray-700 rounded hover:bg-gray-50 transition-colors">Cancelar</button>
                <button @click="guardar" class="px-4 py-2 bg-primary text-white rounded hover:bg-red-800 transition-colors shadow-sm">Guardar</button>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('alpine:init', () => {
    Alpine.data('usuarios', () => ({
        modal: false,
        listado: [],
        form: { id: null, nombre: '', email: '', rol: 'operador' },
        
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
            await this.fetchUsuarios();
        },

        async fetchUsuarios() {
            try {
                const data = await window.api('/api/admin/usuarios');
                this.listado = data;
            } catch (error) {
                console.error("Error al cargar usuarios:", error);
            }
        },

        openModal(usuario = null) {
            if (usuario) {
                this.form = { id: usuario.id, nombre: usuario.nombre, email: usuario.email, rol: usuario.rol.toLowerCase() };
            } else {
                this.form = { id: null, nombre: '', email: '', rol: 'operador' };
            }
            this.modal = true;
        },

        async guardar() {
            try {
                const endpoint = this.form.id ? `/api/admin/usuarios/${this.form.id}` : '/api/admin/usuarios';
                const method = this.form.id ? 'PUT' : 'POST';
                
                await window.api(endpoint, {
                    method: method,
                    body: JSON.stringify(this.form)
                });
                
                this.modal = false;
                await this.fetchUsuarios();
                
                // Mostrar alerta con SweetAlert2
                if (typeof Swal !== 'undefined') {
                    Swal.fire({
                        title: 'Éxito',
                        text: this.form.id ? 'Usuario actualizado correctamente' : 'Usuario creado correctamente',
                        icon: 'success',
                        toast: true, position: 'bottom', showConfirmButton: false, timer: 3000
                    });
                }
            } catch (e) {
                if (typeof Swal !== 'undefined') {
                    Swal.fire({
                        title: 'Error',
                        text: e.message || 'No se pudo guardar',
                        icon: 'error',
                        confirmButtonColor: '#C8102E'
                    });
                } else {
                    console.error('Error: ' + (e.message || 'No se pudo guardar'));
                }
            }
        },

        async toggleEstado(u) {
            const result = await Swal.fire({
                title: `¿Seguro que desea ${u.activo ? 'inactivar' : 'activar'} a ${u.nombre}?`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: u.activo ? '#d33' : '#28a745',
                cancelButtonColor: '#6c757d',
                confirmButtonText: u.activo ? 'Sí, inactivar' : 'Sí, activar',
                cancelButtonText: 'Cancelar'
            });
            if (!result.isConfirmed) return;

            const endpoint = u.activo ? `/api/admin/usuarios/${u.id}/inactivar` : `/api/admin/usuarios/${u.id}/activar`;
            try {
                await window.api(endpoint, { method: 'PATCH' });
                await this.fetchUsuarios();
            } catch (e) {
                Swal.fire('Error', e.message, 'error');
            }
        },

        async resetPass(u) {
            const result = await Swal.fire({
                title: `¿Resetear contraseña de ${u.nombre}?`,
                text: "Se generará una nueva contraseña aleatoria.",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#FCA311',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Sí, resetear',
                cancelButtonText: 'Cancelar'
            });
            if (!result.isConfirmed) return;

            try {
                const response = await window.api(`/api/admin/usuarios/${u.id}/resetear-password`, { method: 'PATCH' });
                Swal.fire({
                    title: 'Éxito',
                    html: `Contraseña reseteada. Nueva contraseña:<br><br><b style="font-size: 1.5em; letter-spacing: 2px;">${response.data.password}</b>`,
                    icon: 'success',
                    confirmButtonColor: '#C8102E'
                });
            } catch (e) {
                Swal.fire('Error', e.message, 'error');
            }
        }
    }));
});
</script>
@endsection
