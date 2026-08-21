@extends('layouts.app')

@section('content')
<div class="max-w-2xl mx-auto py-12 px-4" x-data="passwordChange()">
    <div class="bg-white rounded-lg shadow-sm border border-gray-100 overflow-hidden">
        <div class="p-6 border-b border-gray-100">
            <h1 class="text-2xl font-bold text-gray-800">Cambiar Contraseña</h1>
            <p class="text-sm text-gray-500 mt-1">Asegúrate de usar una contraseña larga y segura.</p>
        </div>
        
        <div class="p-6 space-y-6">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Contraseña Actual</label>
                <input type="password" x-model="form.current_password" class="w-full border-gray-300 rounded-md shadow-sm focus:border-primary focus:ring focus:ring-primary focus:ring-opacity-50 px-3 py-2 border">
            </div>
            
            <div class="border-t border-gray-100 pt-4 mt-2">
                <label class="block text-sm font-medium text-gray-700 mb-1">Nueva Contraseña</label>
                <input type="password" x-model="form.new_password" class="w-full border-gray-300 rounded-md shadow-sm focus:border-primary focus:ring focus:ring-primary focus:ring-opacity-50 px-3 py-2 border">
            </div>
            
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Confirmar Nueva Contraseña</label>
                <input type="password" x-model="form.new_password_confirmation" class="w-full border-gray-300 rounded-md shadow-sm focus:border-primary focus:ring focus:ring-primary focus:ring-opacity-50 px-3 py-2 border">
            </div>
            
            <p class="text-xs text-red-500" x-show="form.new_password !== '' && form.new_password !== form.new_password_confirmation">
                Las contraseñas no coinciden.
            </p>
        </div>

        <div class="bg-gray-50 px-6 py-4 border-t border-gray-100 flex justify-end">
            <button @click="guardar" class="bg-primary hover:bg-red-800 text-white px-6 py-2 rounded-md font-medium transition-colors shadow-sm" :disabled="loading || (form.new_password !== form.new_password_confirmation) || !form.new_password || !form.current_password" :class="{'opacity-50 cursor-not-allowed': loading || (form.new_password !== form.new_password_confirmation) || !form.new_password || !form.current_password}">
                <span x-text="loading ? 'Actualizando...' : 'Actualizar Contraseña'"></span>
            </button>
        </div>
    </div>
</div>

<script>
document.addEventListener('alpine:init', () => {
    Alpine.data('passwordChange', () => ({
        loading: false,
        form: { current_password: '', new_password: '', new_password_confirmation: '' },

        async guardar() {
            if (this.form.new_password !== this.form.new_password_confirmation) return;
            
            this.loading = true;
            try {
                const data = await window.api('/api/auth/me/password', {
                    method: 'PUT',
                    body: JSON.stringify({
                        current_password: this.form.current_password,
                        new_password: this.form.new_password,
                        new_password_confirmation: this.form.new_password_confirmation
                    })
                });
                
                if (typeof Swal !== 'undefined') {
                    Swal.fire({
                        title: 'Éxito',
                        text: 'Contraseña actualizada correctamente',
                        icon: 'success',
                        confirmButtonColor: '#C8102E'
                    }).then(() => {
                        window.location.href = '/dashboard';
                    });
                }
                this.form = { current_password: '', new_password: '', new_password_confirmation: '' };
            } catch (e) {
                if (typeof Swal !== 'undefined') {
                    Swal.fire({
                        title: 'Error',
                        text: e.message || 'No se pudo actualizar la contraseña',
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
