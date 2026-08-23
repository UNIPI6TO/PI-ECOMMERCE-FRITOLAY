@extends('layouts.app')

@section('title', 'Login - Fritolay')

@section('content')
<div class="max-w-md mx-auto mt-10 bg-white p-8 rounded-lg shadow-md" x-data="loginForm()">
    <div class="text-center mb-8">
        <h1 class="text-3xl font-bold text-primary">Frito<span class="text-secondary">lay</span></h1>
        <p class="text-neutral-dark mt-2">Iniciar Sesión</p>
    </div>

    <form @submit.prevent="submit" class="space-y-4">
        <template x-if="error">
            <div class="bg-red-100 text-red-700 p-3 rounded" x-text="error"></div>
        </template>

        <div>
            <label class="block text-sm font-medium text-gray-700">Email</label>
            <input type="email" x-model="email" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm p-2 border focus:ring-primary focus:border-primary" required>
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700">Contraseña</label>
            <input type="password" x-model="password" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm p-2 border focus:ring-primary focus:border-primary" required>
        </div>

        <button type="submit" class="w-full flex justify-center py-2 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-primary hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary" :disabled="loading">
            <span x-show="!loading">Ingresar</span>
            <span x-show="loading">Cargando...</span>
        </button>
    </form>

    <div class="mt-4 text-center flex flex-col space-y-2">
        <a href="/auth/recover" class="text-sm text-primary hover:underline">¿Olvidaste tu contraseña?</a>
        <a href="/auth/registro" class="text-sm font-medium text-gray-600 hover:text-primary hover:underline">¿No tienes cuenta? Quiero ser cliente</a>
    </div>
</div>

<script>
function loginForm() {
    return {
        email: '',
        password: '',
        loading: false,
        async submit() {
            this.loading = true;
            try {
                const data = await window.api('/api/auth/login', {
                    method: 'POST',
                    body: JSON.stringify({ email: this.email, password: this.password })
                });
                localStorage.setItem('jwt_token', data.token);
                let role = data.user?.rol || data.role || 'cliente';
                localStorage.setItem('role', role);
                
                if (role === 'cliente') {
                    window.location.href = '/ecommerce/catalogo';
                } else if (role === 'operador') {
                    window.location.href = '/gestion-pedidos';
                } else {
                    window.location.href = '/dashboard';
                }
            } catch (e) {
                Swal.fire({
                    icon: 'error',
                    title: 'Error al iniciar sesión',
                    text: e.message || 'Credenciales inválidas. Verifica tu email y contraseña.',
                    confirmButtonColor: '#E3001B'
                });
            } finally {
                this.loading = false;
            }
        }
    }
}
</script>
@endsection
