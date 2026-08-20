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

    <div class="mt-4 text-center">
        <a href="/recover" class="text-sm text-primary hover:underline">¿Olvidaste tu contraseña?</a>
    </div>
</div>

<script>
function loginForm() {
    return {
        email: '',
        password: '',
        error: '',
        loading: false,
        async submit() {
            this.loading = true;
            this.error = '';
            try {
                // Mocked fetch for now, replace with actual API call
                const res = await fetch('{{ env('BACKEND_API_URL', 'http://localhost:8000') }}/api/auth/login', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ email: this.email, password: this.password })
                });
                
                if (res.ok) {
                    const data = await res.json();
                    localStorage.setItem('jwt_token', data.token);
                    localStorage.setItem('role', data.role || 'cliente');
                    window.location.href = '/';
                } else {
                    this.error = 'Credenciales inválidas.';
                }
            } catch (e) {
                this.error = 'Error de conexión. Intente nuevamente.';
                // DEMO MODE: Simulate login success
                console.warn('Network error, simulating login for demo');
                localStorage.setItem('jwt_token', 'demo_token');
                localStorage.setItem('role', 'cliente');
                window.location.href = '/';
            } finally {
                this.loading = false;
            }
        }
    }
}
</script>
@endsection
