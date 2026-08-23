@extends('layouts.app')

@section('content')
<div class="max-w-3xl mx-auto py-12 px-4" x-data="perfil()">
    <div class="bg-white rounded-lg shadow-sm border border-gray-100 overflow-hidden">
        <div class="p-6 border-b border-gray-100">
            <h1 class="text-2xl font-bold text-gray-800">Mi Perfil</h1>
            <p class="text-sm text-gray-500 mt-1">Actualiza tu información personal y correo electrónico.</p>
        </div>
        
        <div class="p-6 space-y-6">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Nombre Completo</label>
                <input type="text" x-model="form.nombre" class="w-full border-gray-300 rounded-md shadow-sm focus:border-primary focus:ring focus:ring-primary focus:ring-opacity-50 px-3 py-2 border">
            </div>
            
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Correo Electrónico</label>
                <input type="email" x-model="form.email" class="w-full border-gray-300 rounded-md shadow-sm focus:border-primary focus:ring focus:ring-primary focus:ring-opacity-50 px-3 py-2 border">
            </div>
            
            <template x-if="form.rol === 'cliente'">
                <div>
                    <div class="space-y-6 pt-4 border-t">
                        <h2 class="text-xl font-bold text-gray-800">Datos de Facturación (Cliente)</h2>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Nombre del Cliente (Contacto Comercial)</label>
                            <input type="text" x-model="form.nombre_cliente" class="w-full border-gray-300 rounded-md shadow-sm focus:border-primary focus:ring focus:ring-primary focus:ring-opacity-50 px-3 py-2 border">
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Razón Social o Nombre Legal</label>
                            <input type="text" x-model="form.razon_social" class="w-full border-gray-300 rounded-md shadow-sm focus:border-primary focus:ring focus:ring-primary focus:ring-opacity-50 px-3 py-2 border">
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">RUC/Cédula</label>
                            <input type="text" x-model="form.ruc_cedula" class="w-full border-gray-300 rounded-md shadow-sm focus:border-primary focus:ring focus:ring-primary focus:ring-opacity-50 px-3 py-2 border">
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Teléfono</label>
                            <input type="text" x-model="form.telefono" class="w-full border-gray-300 rounded-md shadow-sm focus:border-primary focus:ring focus:ring-primary focus:ring-opacity-50 px-3 py-2 border">
                        </div>
                    </div>

                    <div class="space-y-6 pt-4 border-t mt-6">
                        <div class="flex justify-between items-center">
                            <h2 class="text-xl font-bold text-gray-800">Mis Direcciones</h2>
                            <button @click="nuevaDireccion()" class="text-[#E3001B] font-medium text-sm">+ Nueva Dirección</button>
                        </div>
                        
                        <div class="space-y-3">
                            <template x-for="addr in direcciones" :key="addr.id">
                                <div class="flex justify-between items-center p-3 border rounded">
                                    <div>
                                        <span class="font-medium" x-text="addr.descripcion"></span>
                                        <span class="text-sm text-gray-500 block" x-text="addr.referencia ? 'Ref: ' + addr.referencia : ''"></span>
                                    </div>
                                    <div class="flex space-x-3 text-sm">
                                        <button @click="editarDireccion(addr)" class="text-blue-600 hover:underline">Ver/Editar</button>
                                        <button @click="eliminarDireccion(addr.id)" class="text-red-600 hover:underline">Eliminar</button>
                                    </div>
                                </div>
                            </template>
                            <div x-show="direcciones.length === 0" class="text-gray-500 text-sm">
                                No tienes direcciones guardadas.
                            </div>
                        </div>
                    </div>
                </div>
            </template>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Rol en el sistema</label>
                <input type="text" x-model="form.rol" disabled class="w-full bg-gray-50 text-gray-500 border-gray-300 rounded-md px-3 py-2 border cursor-not-allowed uppercase text-sm font-semibold">
                <p class="text-xs text-gray-500 mt-1">El rol no puede ser modificado desde el perfil.</p>
            </div>
        </div>

        <div class="bg-gray-50 px-6 py-4 border-t border-gray-100 flex justify-end">
            <button @click="guardar" class="bg-primary hover:bg-red-800 text-white px-6 py-2 rounded-md font-medium transition-colors shadow-sm" :disabled="loading" :class="{'opacity-50': loading}">
                <span x-text="loading ? 'Guardando...' : 'Guardar Cambios'"></span>
            </button>
        </div>
    </div>

    <!-- Modal Dirección -->
    <div x-show="showAddressModal" @update-dir-data.window="newAddressData = $event.detail" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
        <div class="bg-white p-6 rounded-lg w-full max-w-2xl">
            <h3 class="text-lg font-bold mb-4">Agregar Dirección</h3>
            @include('ecommerce.mapa-direccion')
            <div class="mt-4 flex justify-end space-x-3">
                <button @click="showAddressModal = false" class="px-4 py-2 border rounded text-gray-600 hover:bg-gray-50">Cancelar</button>
                <button @click="guardarDireccion" class="px-4 py-2 bg-[#E3001B] text-white rounded hover:bg-red-700">Guardar</button>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('alpine:init', () => {
    Alpine.data('perfil', () => ({
        loading: false,
        form: { nombre: '', email: '', rol: '', nombre_cliente: '', razon_social: '', ruc_cedula: '', telefono: '' },
        direcciones: [],
        clienteId: null,
        showAddressModal: false,
        newAddressData: null,
        editingAddressId: null,

        async init() {
            try {
                const data = await window.api('/api/auth/me');
                this.form.nombre = data.nombre;
                this.form.email = data.email;
                this.form.rol = data.rol;

                if (this.form.rol === 'cliente') {
                    const clienteData = await window.api('/api/clientes/me');
                    if (clienteData && clienteData.data) {
                        this.clienteId = clienteData.data.id;
                        this.form.nombre_cliente = clienteData.data.nombre_cliente || '';
                        this.form.razon_social = clienteData.data.razon_social || '';
                        this.form.ruc_cedula = clienteData.data.ruc_cedula || '';
                        this.form.telefono = clienteData.data.telefono || '';
                        
                        await this.loadDirecciones();
                    }
                }
            } catch (error) {
                console.error("Error al cargar perfil:", error);
            }
        },

        async loadDirecciones() {
            if (this.clienteId) {
                this.direcciones = await window.api(`/api/clientes/${this.clienteId}/direcciones`);
            }
        },

        nuevaDireccion() {
            this.editingAddressId = null;
            this.showAddressModal = true;
            this.$dispatch('load-address', null);
        },

        editarDireccion(addr) {
            this.editingAddressId = addr.id;
            this.showAddressModal = true;
            this.$dispatch('load-address', addr);
        },

        async eliminarDireccion(id) {
            const result = await Swal.fire({
                title: '¿Eliminar dirección?',
                text: 'Esta acción desactivará la dirección seleccionada.',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#E3001B',
                cancelButtonColor: '#6b7280',
                confirmButtonText: 'Sí, eliminar',
                cancelButtonText: 'Cancelar'
            });
            if (!result.isConfirmed) return;
            try {
                await window.api(`/api/clientes/${this.clienteId}/direcciones/${id}`, { method: 'DELETE' });
                await this.loadDirecciones();
                Swal.fire({ icon: 'success', title: 'Eliminada', toast: true, position: 'top-end', showConfirmButton: false, timer: 3000 });
            } catch (e) {
                Swal.fire('Error', 'No se pudo eliminar la dirección', 'error');
            }
        },

        async guardarDireccion() {
            if (!this.clienteId) {
                return Swal.fire('Atención', 'Primero debes guardar tus Datos de Facturación antes de agregar una dirección.', 'warning');
            }
            if (!this.newAddressData || !this.newAddressData.descripcion) {
                return Swal.fire('Error', 'Selecciona una dirección válida', 'error');
            }
            try {
                const method = this.editingAddressId ? 'PUT' : 'POST';
                const url = this.editingAddressId 
                    ? `/api/clientes/${this.clienteId}/direcciones/${this.editingAddressId}`
                    : `/api/clientes/${this.clienteId}/direcciones`;
                    
                await window.api(url, {
                    method: method,
                    body: JSON.stringify({
                        descripcion: this.newAddressData.descripcion,
                        referencia: this.newAddressData.referencia,
                        latitud: this.newAddressData.lat,
                        longitud: this.newAddressData.lng,
                        es_por_defecto: false
                    })
                });
                await this.loadDirecciones();
                this.showAddressModal = false;
                if (typeof Swal !== 'undefined') {
                    Swal.fire({ icon: 'success', title: 'Dirección guardada', toast: true, position: 'top-end', showConfirmButton: false, timer: 3000 });
                }
            } catch (e) {
                if (typeof Swal !== 'undefined') {
                    Swal.fire('Error', 'No se pudo guardar la dirección', 'error');
                } else {
                    console.error('Error al guardar dirección');
                }
            }
        },

        async guardar() {
            this.loading = true;
            try {
                await window.api('/api/auth/me', {
                    method: 'PUT',
                    body: JSON.stringify({
                        nombre: this.form.nombre,
                        email: this.form.email
                    })
                });

                if (this.form.rol === 'cliente') {
                    const res = await window.api('/api/clientes/me', {
                        method: 'PUT',
                        body: JSON.stringify({
                            nombre_cliente: this.form.nombre_cliente,
                            razon_social: this.form.razon_social,
                            ruc_cedula: this.form.ruc_cedula,
                            telefono: this.form.telefono
                        })
                    });
                    if (res && res.data) {
                        this.clienteId = res.data.id;
                    }
                }
                
                if (typeof Swal !== 'undefined') {
                    Swal.fire({
                        title: 'Éxito',
                        text: 'Perfil actualizado correctamente',
                        icon: 'success',
                        confirmButtonColor: '#C8102E'
                    });
                }
            } catch (e) {
                if (typeof Swal !== 'undefined') {
                    Swal.fire({
                        title: 'Error',
                        text: e.message || 'Error al procesar la solicitud',
                        icon: 'error',
                        confirmButtonColor: '#C8102E'
                    });
                } else {
                    console.error('Error: ' + e.message);
                }
            } finally {
                this.loading = false;
            }
        }
    }));
});
</script>
@endsection
