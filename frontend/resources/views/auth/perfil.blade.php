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
</div>

<script>
document.addEventListener('alpine:init', () => {
    Alpine.data('perfil', () => ({
        loading: false,
        form: { nombre: '', email: '', rol: '' },

        async init() {
            try {
                const data = await window.api('/api/auth/me');
                this.form.nombre = data.nombre;
                this.form.email = data.email;
                this.form.rol = data.rol;
            } catch (error) {
                console.error("Error al cargar perfil:", error);
            }
        },

        async guardar() {
            this.loading = true;
            try {
                const data = await window.api('/api/auth/me', {
                    method: 'PUT',
                    body: JSON.stringify({
                        nombre: this.form.nombre,
                        email: this.form.email
                    })
                });
                
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
                        text: e.message || 'No se pudo actualizar el perfil',
                        icon: 'error',
                        confirmButtonColor: '#C8102E'
                    });
                } else {
                    alert('Error: ' + e.message);
                }
            } finally {
                this.loading = false;
            }
        }
    }));
});
</script>
@endsection
