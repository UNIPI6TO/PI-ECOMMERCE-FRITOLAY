@extends('layouts.app')

@section('title', 'Gestión de Usuarios Empleados - Fritolay')

@section('content')
<div class="max-w-7xl mx-auto py-6 px-4 sm:px-6" x-data="usuarios()">

    <!-- Header & Filtros -->
    <div class="bg-white rounded-2xl shadow-xs border border-gray-100 p-6 mb-6">
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div>
                <div class="flex items-center gap-2 mb-1">
                    <span class="px-2.5 py-0.5 rounded-full bg-red-50 text-[#E3001B] border border-red-100 font-extrabold text-[10px] uppercase tracking-wider">
                        Administración
                    </span>
                </div>
                <h1 class="text-2xl font-black text-gray-900 tracking-tight">Gestión de Usuarios Empleados</h1>
                <p class="text-xs font-semibold text-gray-500 mt-0.5">Administra los accesos y roles del personal de distribución y operarios.</p>
            </div>
            
            <div class="flex flex-wrap items-center gap-3">
                <button @click="openModal()" 
                        class="py-2.5 px-4 bg-slate-900 hover:bg-slate-800 text-white font-bold text-xs rounded-xl shadow-xs transition-all flex items-center gap-2 cursor-pointer">
                    <svg class="w-4 h-4 text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"></path></svg>
                    <span>Nuevo Usuario</span>
                </button>
            </div>
        </div>

        <!-- Toolbar de Filtros y Búsqueda -->
        <div class="mt-6 pt-4 border-t border-gray-100 flex flex-col md:flex-row items-center justify-between gap-4">
            <div class="flex flex-1 flex-col sm:flex-row items-center gap-3 w-full md:w-auto">
                <!-- Buscador por nombre/email -->
                <div class="relative w-full sm:w-72">
                    <input type="text" 
                           x-model="searchTerm" 
                           @input="currentPage = 1" 
                           placeholder="Buscar por nombre o email..." 
                           class="w-full pl-9 pr-4 py-2 bg-gray-50/60 border border-gray-200 rounded-xl text-xs text-gray-900 font-medium placeholder-gray-400 focus:bg-white focus:ring-2 focus:ring-slate-800 outline-none transition-all">
                    <svg class="w-4 h-4 text-gray-400 absolute left-3 top-2.5 pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                </div>

                <!-- Filtro por Rol -->
                <select x-model="roleFilter" @change="currentPage = 1" class="w-full sm:w-44 border border-gray-200 rounded-xl px-3 py-2 text-xs font-semibold text-gray-700 bg-gray-50/60 focus:bg-white focus:ring-2 focus:ring-slate-800 outline-none cursor-pointer">
                    <option value="">Todos los Roles</option>
                    <option value="administrador">Administrador</option>
                    <option value="operador">Operador</option>
                    <option value="chofer">Chofer</option>
                </select>
            </div>

            <!-- Selector de Cantidad por Página -->
            <div class="flex items-center gap-2 text-xs font-semibold text-gray-500 self-end md:self-center">
                <span>Mostrar:</span>
                <select x-model.number="perPage" @change="currentPage = 1" class="border border-gray-200 rounded-xl px-3 py-1.5 text-xs font-bold text-gray-800 bg-gray-50/60 focus:bg-white focus:ring-2 focus:ring-slate-800 outline-none cursor-pointer">
                    <option :value="5">5</option>
                    <option :value="10">10</option>
                    <option :value="20">20</option>
                    <option :value="50">50</option>
                </select>
            </div>
        </div>
    </div>

    <!-- Tabla Estilizada de Usuarios -->
    <div class="bg-white rounded-2xl shadow-xs border border-gray-100 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead class="bg-gray-50/80 border-b border-gray-100 text-[11px] font-extrabold uppercase tracking-wider text-gray-500">
                    <tr>
                        <th class="py-3.5 px-6">Empleado / Usuario</th>
                        <th class="py-3.5 px-6">Email</th>
                        <th class="py-3.5 px-6">Rol de Acceso</th>
                        <th class="py-3.5 px-6">Estado</th>
                        <th class="py-3.5 px-6 text-center">Acciones</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 text-xs">
                    <!-- Sin Resultados -->
                    <template x-if="paginatedListado.length === 0">
                        <tr>
                            <td colspan="5" class="py-12 text-center text-gray-400">
                                <svg class="w-10 h-10 mx-auto text-gray-300 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                                No se encontraron usuarios empleados registrados.
                            </td>
                        </tr>
                    </template>

                    <!-- Lista de Usuarios -->
                    <template x-for="u in paginatedListado" :key="u.id">
                        <tr class="hover:bg-gray-50/80 transition-colors group">
                            <!-- Nombre e Iniciales -->
                            <td class="py-4 px-6">
                                <div class="flex items-center gap-3">
                                    <div class="w-9 h-9 rounded-xl bg-slate-900 text-white font-extrabold text-xs flex items-center justify-center shadow-2xs uppercase">
                                        <span x-text="getInitials(u.nombre)"></span>
                                    </div>
                                    <div>
                                        <div class="font-bold text-gray-900 group-hover:text-[#E3001B] transition-colors" x-text="u.nombre"></div>
                                        <div class="text-[11px] text-gray-400 font-medium" x-text="`ID #${u.id}`"></div>
                                    </div>
                                </div>
                            </td>

                            <!-- Email -->
                            <td class="py-4 px-6 text-gray-600 font-medium" x-text="u.email"></td>

                            <!-- Rol Badge -->
                            <td class="py-4 px-6">
                                <span class="px-2.5 py-1 rounded-full text-[11px] font-extrabold border capitalize tracking-wider" 
                                      :class="getRoleBadgeClass(u.rol)" 
                                      x-text="u.rol"></span>
                            </td>

                            <!-- Estado Badge -->
                            <td class="py-4 px-6">
                                <span class="px-2.5 py-1 rounded-full text-[11px] font-extrabold border inline-flex items-center gap-1.5"
                                      :class="u.activo ? 'bg-emerald-50 text-emerald-700 border-emerald-200' : 'bg-rose-50 text-rose-700 border-rose-200'">
                                    <span class="w-1.5 h-1.5 rounded-full" :class="u.activo ? 'bg-emerald-500' : 'bg-rose-500'"></span>
                                    <span x-text="u.activo ? 'Activo' : 'Inactivo'"></span>
                                </span>
                            </td>

                            <!-- Acciones Estilizadas -->
                            <td class="py-4 px-6 text-center">
                                <div class="flex items-center justify-center gap-1.5">
                                    <!-- Editar -->
                                    <button @click="openModal(u)" 
                                            title="Editar información" 
                                            class="p-1.5 rounded-lg text-gray-400 hover:text-slate-900 hover:bg-gray-100 transition-all cursor-pointer">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                                    </button>

                                    <!-- Inactivar / Activar -->
                                    <button @click="toggleEstado(u)" 
                                            :title="u.activo ? 'Inactivar usuario' : 'Activar usuario'" 
                                            class="p-1.5 rounded-lg transition-all cursor-pointer"
                                            :class="u.activo ? 'text-gray-400 hover:text-rose-600 hover:bg-rose-50' : 'text-gray-400 hover:text-emerald-600 hover:bg-emerald-50'">
                                        <svg x-show="u.activo" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"></path></svg>
                                        <svg x-show="!u.activo" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                    </button>
                                    
                                    <!-- Resetear Contraseña -->
                                    <button @click="resetPass(u)" 
                                            title="Resetear contraseña" 
                                            class="p-1.5 rounded-lg text-gray-400 hover:text-amber-600 hover:bg-amber-50 transition-all cursor-pointer">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"></path></svg>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    </template>
                </tbody>
            </table>
        </div>
        
        <!-- Paginador Estandarizado Slate -->
        <div class="px-6 py-4 border-t border-gray-100 flex flex-col sm:flex-row items-center justify-between gap-4 bg-gray-50/50">
            <div class="text-xs font-semibold text-gray-500">
                Mostrando <span class="font-extrabold text-gray-900" x-text="startRecord"></span> a <span class="font-extrabold text-gray-900" x-text="endRecord"></span> de <span class="font-extrabold text-gray-900" x-text="filteredListado.length"></span> usuarios
            </div>
            <div class="flex items-center gap-1.5">
                <button @click="prevPage()" 
                        :disabled="currentPage === 1" 
                        class="px-3 py-1.5 border border-gray-200 rounded-xl text-xs font-bold text-gray-700 bg-white hover:bg-gray-50 transition-all disabled:opacity-40 disabled:cursor-not-allowed shadow-2xs">
                    Anterior
                </button>
                
                <template x-for="p in totalPages" :key="p">
                    <button @click="currentPage = p" 
                            class="w-8 h-8 rounded-xl text-xs font-bold transition-all shadow-2xs"
                            :class="currentPage === p ? 'bg-slate-900 text-white shadow-xs' : 'bg-white border border-gray-200 text-gray-700 hover:bg-gray-50'"
                            x-text="p"></button>
                </template>

                <button @click="nextPage()" 
                        :disabled="currentPage === totalPages" 
                        class="px-3 py-1.5 border border-gray-200 rounded-xl text-xs font-bold text-gray-700 bg-white hover:bg-gray-50 transition-all disabled:opacity-40 disabled:cursor-not-allowed shadow-2xs">
                    Siguiente
                </button>
            </div>
        </div>
    </div>

    <!-- Modal Formulario de Usuario -->
    <div x-show="modal" 
         class="fixed inset-0 bg-slate-900/60 backdrop-blur-xs flex items-center justify-center z-50 p-4" 
         x-transition:enter="ease-out duration-200"
         x-transition:enter-start="opacity-0 scale-95"
         x-transition:enter-end="opacity-100 scale-100"
         x-transition:leave="ease-in duration-150"
         x-transition:leave-start="opacity-100 scale-100"
         x-transition:leave-end="opacity-0 scale-95"
         style="display: none;">
        <div class="bg-white p-6 sm:p-8 rounded-2xl w-full max-w-md shadow-2xl border border-gray-100" @click.away="modal = false">
            <div class="flex items-center justify-between mb-5">
                <h3 class="font-extrabold text-lg text-gray-900" x-text="form.id ? 'Editar Empleado' : 'Crear Empleado'"></h3>
                <button @click="modal = false" class="text-gray-400 hover:text-gray-600 p-1 rounded-lg hover:bg-gray-100">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                </button>
            </div>

            <div class="space-y-4">
                <div>
                    <label class="block text-xs font-bold text-gray-700 mb-1">Nombre Completo</label>
                    <input type="text" x-model="form.nombre" placeholder="Ej. Juan Pérez" class="w-full border border-gray-200 rounded-xl px-3 py-2.5 text-xs text-gray-900 font-medium bg-gray-50/50 focus:bg-white focus:ring-2 focus:ring-slate-800 outline-none transition-all">
                </div>
                <div>
                    <label class="block text-xs font-bold text-gray-700 mb-1">Correo Electrónico</label>
                    <input type="email" x-model="form.email" placeholder="ejemplo@mail.com" class="w-full border border-gray-200 rounded-xl px-3 py-2.5 text-xs text-gray-900 font-medium bg-gray-50/50 focus:bg-white focus:ring-2 focus:ring-slate-800 outline-none transition-all">
                </div>
                <div>
                    <label class="block text-xs font-bold text-gray-700 mb-1">Rol de Acceso</label>
                    <select x-model="form.rol" class="w-full border border-gray-200 rounded-xl px-3 py-2.5 text-xs font-semibold text-gray-800 bg-gray-50/50 focus:bg-white focus:ring-2 focus:ring-slate-800 outline-none cursor-pointer">
                        <option value="administrador">Administrador</option>
                        <option value="operador">Operador</option>
                        <option value="chofer">Chofer</option>
                    </select>
                </div>
                <template x-if="!form.id">
                    <div class="text-[11px] text-amber-800 bg-amber-50 p-3 rounded-xl border border-amber-200 flex items-start gap-2">
                        <svg class="w-4 h-4 text-amber-600 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        <span>La contraseña inicial predeterminada será <b class="underline">password123</b>. Podrá restablecerla posteriormente.</span>
                    </div>
                </template>
            </div>

            <div class="flex justify-end gap-3 mt-6 pt-4 border-t border-gray-100">
                <button @click="modal = false" class="px-4 py-2 border border-gray-200 text-gray-600 rounded-xl text-xs font-bold hover:bg-gray-50 transition-all">Cancelar</button>
                <button @click="guardar()" class="px-4 py-2 bg-slate-900 hover:bg-slate-800 text-white rounded-xl text-xs font-bold transition-all shadow-xs">Guardar Cambios</button>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('alpine:init', () => {
    Alpine.data('usuarios', () => ({
        modal: false,
        listado: [],
        searchTerm: '',
        roleFilter: '',
        form: { id: null, nombre: '', email: '', rol: 'operador' },
        
        // Paginación
        currentPage: 1,
        perPage: 10,
        
        get filteredListado() {
            let res = this.listado;
            if (this.roleFilter !== '') {
                res = res.filter(u => (u.rol || '').toLowerCase() === this.roleFilter.toLowerCase());
            }
            if (this.searchTerm.trim() !== '') {
                const term = this.searchTerm.toLowerCase();
                res = res.filter(u => 
                    (u.nombre || '').toLowerCase().includes(term) || 
                    (u.email || '').toLowerCase().includes(term)
                );
            }
            return res;
        },

        get totalPages() {
            return Math.ceil(this.filteredListado.length / this.perPage) || 1;
        },
        
        get paginatedListado() {
            const start = (this.currentPage - 1) * this.perPage;
            return this.filteredListado.slice(start, start + this.perPage);
        },

        get startRecord() {
            if (this.filteredListado.length === 0) return 0;
            return (this.currentPage - 1) * this.perPage + 1;
        },

        get endRecord() {
            return Math.min(this.currentPage * this.perPage, this.filteredListado.length);
        },

        nextPage() {
            if (this.currentPage < this.totalPages) this.currentPage++;
        },
        
        prevPage() {
            if (this.currentPage > 1) this.currentPage--;
        },

        getInitials(name) {
            if (!name) return 'US';
            const parts = name.trim().split(' ');
            if (parts.length >= 2) return (parts[0][0] + parts[1][0]).toUpperCase();
            return parts[0].substring(0, 2).toUpperCase();
        },

        getRoleBadgeClass(rol) {
            const r = (rol || '').toLowerCase();
            if (r === 'administrador') return 'bg-purple-50 text-purple-700 border-purple-200';
            if (r === 'operador') return 'bg-blue-50 text-blue-700 border-blue-200';
            if (r === 'chofer') return 'bg-amber-50 text-amber-700 border-amber-200';
            return 'bg-gray-50 text-gray-700 border-gray-200';
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
                        confirmButtonColor: '#E3001B'
                    });
                }
            }
        },

        async toggleEstado(u) {
            const result = await Swal.fire({
                title: `¿Seguro que desea ${u.activo ? 'inactivar' : 'activar'} a ${u.nombre}?`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: u.activo ? '#E3001B' : '#10b981',
                cancelButtonColor: '#6b7280',
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
                confirmButtonColor: '#f59e0b',
                cancelButtonColor: '#6b7280',
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
                    confirmButtonColor: '#E3001B'
                });
            } catch (e) {
                Swal.fire('Error', e.message, 'error');
            }
        }
    }));
});
</script>
@endsection
