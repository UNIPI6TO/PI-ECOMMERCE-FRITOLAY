@extends('layouts.app')

@section('title', 'Registro - Fritolay')

@section('content')
<div class="max-w-md mx-auto mt-10 bg-white p-8 rounded-lg shadow-md" x-data="registroForm()">
    <div class="text-center mb-8">
        <h1 class="text-3xl font-bold text-primary">Frito<span class="text-secondary">lay</span></h1>
        <p class="text-neutral-dark mt-2">Crear cuenta de cliente</p>
    </div>

    <form @submit.prevent="submit" class="space-y-4">
        <template x-if="error">
            <div class="bg-red-100 text-red-700 p-3 rounded" x-text="error"></div>
        </template>

        <div>
            <label class="block text-sm font-medium text-gray-700">Nombre Completo</label>
            <input type="text" x-model="nombre" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm p-2 border focus:ring-primary focus:border-primary" required>
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700">Cédula / RUC</label>
            <input type="text" x-model="ruc_cedula" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm p-2 border focus:ring-primary focus:border-primary" required>
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700">Email</label>
            <input type="email" x-model="email" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm p-2 border focus:ring-primary focus:border-primary" required>
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700">Teléfono</label>
            <input type="text" x-model="telefono" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm p-2 border focus:ring-primary focus:border-primary" required>
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700">Dirección de Entrega</label>
            <input type="text" x-model="direccion" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm p-2 border focus:ring-primary focus:border-primary" required>
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700">Contraseña</label>
            <input type="password" x-model="password" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm p-2 border focus:ring-primary focus:border-primary" required minlength="6">
        </div>

        <button type="submit" class="w-full flex justify-center py-2 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-primary hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary" :disabled="loading">
            <span x-show="!loading">Registrarme</span>
            <span x-show="loading">Cargando...</span>
        </button>
    </form>

    <div class="mt-4 text-center">
        <a href="/auth/login" class="text-sm text-primary hover:underline">¿Ya tienes cuenta? Inicia sesión</a>
    </div>
</div>

<script>
function registroForm() {
    return {
        nombre: '',
        email: '',
        password: '',
        ruc_cedula: '',
        telefono: '',
        direccion: '',
        error: '',
        loading: false,
        async submit() {
            this.loading = true;
            this.error = '';
            try {
                const data = await window.api('/api/auth/registro', {
                    method: 'POST',
                    body: JSON.stringify({
                        nombre: this.nombre,
                        email: this.email,
                        password: this.password,
                        ruc_cedula: this.ruc_cedula,
                        telefono: this.telefono,
                        direccion: this.direccion
                    })
                });
                localStorage.setItem('jwt_token', data.token);
                localStorage.setItem('role', data.user?.rol || data.role || 'cliente');
                Swal.fire({
                    icon: 'success',
                    title: '¡Registro Exitoso!',
                    text: 'Bienvenido a Fritolay',
                    confirmButtonColor: '#E3001B'
                }).then(() => {
                    window.location.href = '/';
                });
            } catch (e) {
                this.error = e.message || 'Error al registrar la cuenta. Verifica tus datos.';
            } finally {
                this.loading = false;
            }
        }
    }
}
</script>
@endsection
