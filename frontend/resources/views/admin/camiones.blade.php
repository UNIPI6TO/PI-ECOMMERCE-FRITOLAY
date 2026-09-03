@extends('layouts.app')

@section('title', 'Gestión de Flota (Camiones) - Fritolay')

@section('content')
<div class="max-w-7xl mx-auto py-6 px-4 sm:px-6" x-data="camiones()">

    <!-- Header & Filtros -->
    <div class="bg-white rounded-2xl shadow-xs border border-gray-100 p-6 mb-6">
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div>
                <div class="flex items-center gap-2 mb-1">
                    <span class="px-2.5 py-0.5 rounded-full bg-red-50 text-[#E3001B] border border-red-100 font-extrabold text-[10px] uppercase tracking-wider">
                        Administración de Flota
                    </span>
                </div>
                <h1 class="text-2xl font-black text-gray-900 tracking-tight">Gestión de Flota (Camiones)</h1>
                <p class="text-xs font-semibold text-gray-500 mt-0.5">Control de vehículos de reparto, estado operativo y asignación de conductores.</p>
            </div>
            
            <div class="flex flex-wrap items-center gap-3">
                <button @click="abrirModal()" 
                        class="py-2.5 px-4 bg-slate-900 hover:bg-slate-800 text-white font-bold text-xs rounded-xl shadow-xs transition-all flex items-center gap-2 cursor-pointer">
                    <svg class="w-4 h-4 text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"></path></svg>
                    <span>Nuevo Camión</span>
                </button>
            </div>
        </div>

        <!-- Toolbar de Filtros y Búsqueda -->
        <div class="mt-6 pt-4 border-t border-gray-100 flex flex-col md:flex-row items-center justify-between gap-4">
            <div class="flex flex-1 flex-col sm:flex-row items-center gap-3 w-full md:w-auto">
                <!-- Buscador por placa/descripción -->
                <div class="relative w-full sm:w-72">
                    <input type="text" 
                           x-model="searchTerm" 
                           @input="currentPage = 1" 
                           placeholder="Buscar por placa o modelo..." 
                           class="w-full pl-9 pr-4 py-2 bg-gray-50/60 border border-gray-200 rounded-xl text-xs text-gray-900 font-medium placeholder-gray-400 focus:bg-white focus:ring-2 focus:ring-slate-800 outline-none transition-all">
                    <svg class="w-4 h-4 text-gray-400 absolute left-3 top-2.5 pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                </div>

                <!-- Filtro por Estado -->
                <select x-model="statusFilter" @change="currentPage = 1" class="w-full sm:w-44 border border-gray-200 rounded-xl px-3 py-2 text-xs font-semibold text-gray-700 bg-gray-50/60 focus:bg-white focus:ring-2 focus:ring-slate-800 outline-none cursor-pointer">
                    <option value="">Todos los Estados</option>
                    <option value="ACTIVO">ACTIVO</option>
                    <option value="MANTENIMIENTO">MANTENIMIENTO</option>
                    <option value="INACTIVO">INACTIVO</option>
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

    <!-- Tabla Estilizada de Camiones -->
    <div class="bg-white rounded-2xl shadow-xs border border-gray-100 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead class="bg-gray-50/80 border-b border-gray-100 text-[11px] font-extrabold uppercase tracking-wider text-gray-500">
                    <tr>
                        <th class="py-3.5 px-6">Placa del Vehículo</th>
                        <th class="py-3.5 px-6">Modelo / Descripción</th>
                        <th class="py-3.5 px-6">Chofer Asignado</th>
                        <th class="py-3.5 px-6">Estado Operativo</th>
                        <th class="py-3.5 px-6 text-center">Acciones</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 text-xs">
                    <!-- Sin Resultados -->
                    <template x-if="paginatedListado.length === 0">
                        <tr>
                            <td colspan="5" class="py-12 text-center text-gray-400">
                                <svg class="w-10 h-10 mx-auto text-gray-300 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 16.5a1.5 1.5 0 11-3 0 1.5 1.5 0 013 0zM15 16.5a1.5 1.5 0 11-3 0 1.5 1.5 0 013 0z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 4a1 1 0 00-1 1v10a1 1 0 001 1h1.05a2.5 2.5 0 014.9 0H10a1 1 0 001-1V5a1 1 0 00-1-1H3zM14 7a1 1 0 00-1 1v6.05A2.5 2.5 0 0115.95 16H17a1 1 0 001-1v-5l-4-4h-0.05z" /></svg>
                                No se encontraron camiones registrados.
                            </td>
                        </tr>
                    </template>

                    <!-- Lista de Camiones -->
                    <template x-for="c in paginatedListado" :key="c.id">
                        <tr class="hover:bg-gray-50/80 transition-colors group">
                            <!-- Placa -->
                            <td class="py-4 px-6">
                                <div class="flex items-center gap-3">
                                    <div class="w-9 h-9 rounded-xl bg-slate-900 text-amber-400 flex items-center justify-center font-extrabold text-sm shadow-2xs">
                                        🚚
                                    </div>
                                    <div>
                                        <div class="font-black text-gray-900 text-sm group-hover:text-[#E3001B] transition-colors" x-text="c.placa"></div>
                                        <div class="text-[11px] text-gray-400 font-medium" x-text="`ID Camión #${c.id}`"></div>
                                    </div>
                                </div>
                            </td>

                            <!-- Descripción -->
                            <td class="py-4 px-6 text-gray-700 font-semibold" x-text="c.descripcion || 'Sin descripción'"></td>

                            <!-- Chofer Asignado Selector -->
                            <td class="py-4 px-6">
                                <select x-model="c.chofer_id" class="w-full max-w-xs border border-gray-200 rounded-xl px-3 py-2 text-xs font-semibold text-gray-800 bg-gray-50/60 focus:bg-white focus:ring-2 focus:ring-slate-800 outline-none cursor-pointer">
                                    <option value="">-- Sin Chofer Asignado --</option>
                                    <template x-for="ch in choferes" :key="ch.id">
                                        <option :value="ch.id" x-text="ch.nombre" :selected="ch.id == c.chofer_id"></option>
                                    </template>
                                </select>
                            </td>

                            <!-- Estado Selector -->
                            <td class="py-4 px-6">
                                <select x-model="c.estado" 
                                        class="border border-gray-200 rounded-xl px-3 py-1.5 text-xs font-extrabold bg-gray-50/60 focus:bg-white focus:ring-2 focus:ring-slate-800 outline-none cursor-pointer"
                                        :class="{
                                            'text-emerald-700 bg-emerald-50/60 border-emerald-200': c.estado === 'ACTIVO',
                                            'text-amber-700 bg-amber-50/60 border-amber-200': c.estado === 'MANTENIMIENTO',
                                            'text-rose-700 bg-rose-50/60 border-rose-200': c.estado === 'INACTIVO'
                                        }">
                                    <option value="ACTIVO">🟢 ACTIVO</option>
                                    <option value="MANTENIMIENTO">🟡 MANTENIMIENTO</option>
                                    <option value="INACTIVO">🔴 INACTIVO</option>
                                </select>
                            </td>

                            <!-- Acciones Estilizadas -->
                            <td class="py-4 px-6 text-center">
                                <div class="flex items-center justify-center gap-2">
                                    <button @click="abrirModal(c)" 
                                            title="Editar vehículo" 
                                            class="py-1.5 px-3 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-xl text-xs font-bold transition-all cursor-pointer">
                                        Editar
                                    </button>
                                    <button @click="guardarCambios(c)" 
                                            title="Guardar asignación y estado" 
                                            class="py-1.5 px-3 bg-slate-900 hover:bg-slate-800 text-white rounded-xl text-xs font-bold transition-all shadow-2xs cursor-pointer">
                                        Guardar
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
                Mostrando <span class="font-extrabold text-gray-900" x-text="startRecord"></span> a <span class="font-extrabold text-gray-900" x-text="endRecord"></span> de <span class="font-extrabold text-gray-900" x-text="filteredListado.length"></span> camiones
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

    <!-- Modal Formulario de Camión -->
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
                <h3 class="font-extrabold text-lg text-gray-900" x-text="isEdit ? 'Editar Camión' : 'Registrar Camión'"></h3>
                <button @click="modal = false" class="text-gray-400 hover:text-gray-600 p-1 rounded-lg hover:bg-gray-100">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                </button>
            </div>

            <div class="space-y-4">
                <div>
                    <label class="block text-xs font-bold text-gray-700 mb-1">Placa del Vehículo</label>
                    <input type="text" x-model="nuevo.placa" placeholder="Ej. ABC-1234" class="w-full border border-gray-200 rounded-xl px-3 py-2.5 text-xs text-gray-900 font-bold bg-gray-50/50 focus:bg-white focus:ring-2 focus:ring-slate-800 outline-none uppercase transition-all">
                </div>
                <div>
                    <label class="block text-xs font-bold text-gray-700 mb-1">Descripción / Modelo</label>
                    <input type="text" x-model="nuevo.descripcion" placeholder="Ej. Hino 300 Híbrido 5 Ton" class="w-full border border-gray-200 rounded-xl px-3 py-2.5 text-xs text-gray-900 font-medium bg-gray-50/50 focus:bg-white focus:ring-2 focus:ring-slate-800 outline-none transition-all">
                </div>
            </div>

            <div class="flex justify-end gap-3 mt-6 pt-4 border-t border-gray-100">
                <button @click="modal = false" class="px-4 py-2 border border-gray-200 text-gray-600 rounded-xl text-xs font-bold hover:bg-gray-50 transition-all">Cancelar</button>
                <button @click="guardarCamion()" class="px-4 py-2 bg-slate-900 hover:bg-slate-800 text-white rounded-xl text-xs font-bold transition-all shadow-xs" x-text="isEdit ? 'Actualizar' : 'Registrar'"></button>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('alpine:init', () => {
    Alpine.data('camiones', () => ({
        modal: false,
        isEdit: false,
        editId: null,
        choferes: [],
        listado: [],
        searchTerm: '',
        statusFilter: '',
        nuevo: {placa: '', descripcion: ''},

        currentPage: 1,
        perPage: 10,
        
        get filteredListado() {
            let res = this.listado;
            if (this.statusFilter !== '') {
                res = res.filter(c => (c.estado || '').toUpperCase() === this.statusFilter.toUpperCase());
            }
            if (this.searchTerm.trim() !== '') {
                const term = this.searchTerm.toLowerCase();
                res = res.filter(c => 
                    (c.placa || '').toLowerCase().includes(term) || 
                    (c.descripcion || '').toLowerCase().includes(term)
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

        abrirModal(camion = null) {
            if (camion) {
                this.isEdit = true;
                this.editId = camion.id;
                this.nuevo = { placa: camion.placa, descripcion: camion.descripcion };
            } else {
                this.isEdit = false;
                this.editId = null;
                this.nuevo = { placa: '', descripcion: '' };
            }
            this.modal = true;
        },

        async init() {
            await this.fetchCamiones();
        },

        async fetchCamiones() {
            try {
                const data = await window.api('/api/camiones');
                this.listado = data;
                const users = await window.api('/api/admin/usuarios');
                this.choferes = users.filter(u => u.rol === 'chofer' || u.rol === 'CHOFER');
            } catch (error) {
                console.error("Error al cargar camiones:", error);
            }
        },

        async guardarCamion() {
            try {
                if (this.isEdit) {
                    await window.api(`/api/camiones/${this.editId}`, {
                        method: 'PUT',
                        body: JSON.stringify(this.nuevo)
                    });
                    Swal.fire({ icon: 'success', title: 'Éxito', text: 'Camión actualizado', toast: true, position: 'bottom', showConfirmButton: false, timer: 3000 });
                } else {
                    await window.api('/api/camiones', {
                        method: 'POST',
                        body: JSON.stringify(this.nuevo)
                    });
                    Swal.fire({ icon: 'success', title: 'Éxito', text: 'Camión registrado', toast: true, position: 'bottom', showConfirmButton: false, timer: 3000 });
                }
                this.modal = false;
                this.nuevo = {placa: '', descripcion: ''};
                this.isEdit = false;
                this.editId = null;
                await this.fetchCamiones();
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
