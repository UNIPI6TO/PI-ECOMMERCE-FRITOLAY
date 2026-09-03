@extends('layouts.app')

@section('title', 'Iniciar Sesión - Fritolay')

@section('content')
<div class="min-h-[75vh] flex items-center justify-center py-8 px-4" x-data="loginForm()">
    <div class="max-w-md w-full bg-white rounded-2xl shadow-xl shadow-gray-200/60 border border-gray-100 p-8 transition-all">
        
        <!-- Header con Logo Fritolay -->
        <div class="text-center mb-8">
            <div class="inline-flex items-center justify-center w-12 h-12 rounded-xl bg-red-50 text-[#E3001B] font-black text-2xl mb-3 shadow-2xs border border-red-100">
                F
            </div>
            <h1 class="text-2xl font-black text-gray-900 tracking-tight">
                Frito<span class="text-[#F5C518]">lay</span>
            </h1>
            <p class="text-xs font-semibold text-gray-500 mt-1 uppercase tracking-wider">Sistema de Comercio y Distribución</p>
        </div>

        <!-- Alerta de Error -->
        <template x-if="error">
            <div class="mb-5 p-3.5 rounded-xl bg-red-50 border border-red-200 text-red-700 text-xs font-semibold flex items-start gap-2.5 shadow-2xs">
                <svg class="w-4 h-4 text-red-600 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                <span x-text="error"></span>
            </div>
        </template>

        <!-- Formulario de Login -->
        <form @submit.prevent="submit" class="space-y-4">
            <!-- Campo Email -->
            <div>
                <label class="block text-xs font-bold text-gray-700 mb-1">Correo Electrónico</label>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-gray-400">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 12a4 4 0 10-8 0 4 4 0 008 0zm0 0v1.5a2.5 2.5 0 005 0V12a9 9 0 10-9 9m4.5-1.206a8.959 8.959 0 01-4.5 1.207"></path>
                        </svg>
                    </div>
                    <input type="email" 
                           x-model="email" 
                           placeholder="ejemplo@mail.com"
                           class="block w-full pl-9 pr-3 py-2.5 bg-gray-50/50 border border-gray-200 rounded-xl text-sm text-gray-900 font-medium placeholder-gray-400 focus:bg-white focus:ring-2 focus:ring-[#E3001B] focus:border-[#E3001B] outline-none transition-all"
                           required>
                </div>
            </div>

            <!-- Campo Contraseña -->
            <div>
                <div class="flex items-center justify-between mb-1">
                    <label class="block text-xs font-bold text-gray-700">Contraseña</label>
                    <a href="/auth/recover" class="text-xs font-bold text-[#E3001B] hover:underline transition-all">¿Olvidaste tu contraseña?</a>
                </div>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-gray-400">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                        </svg>
                    </div>
                    <input :type="showPassword ? 'text' : 'password'" 
                           x-model="password" 
                           placeholder="••••••••"
                           class="block w-full pl-9 pr-10 py-2.5 bg-gray-50/50 border border-gray-200 rounded-xl text-sm text-gray-900 font-medium placeholder-gray-400 focus:bg-white focus:ring-2 focus:ring-[#E3001B] focus:border-[#E3001B] outline-none transition-all"
                           required>
                    <button type="button" 
                            @click="showPassword = !showPassword" 
                            class="absolute inset-y-0 right-0 pr-3 flex items-center text-gray-400 hover:text-gray-600 focus:outline-none">
                        <template x-if="!showPassword">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                        </template>
                        <template x-if="showPassword">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858-5.908a8.972 8.972 0 013.122-.643c4.478 0 8.268 2.943 9.542 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21m-2.102-2.102L3 3"></path></svg>
                        </template>
                    </button>
                </div>
            </div>

            <!-- Opción Recuérdame -->
            <div class="flex items-center justify-between pt-1">
                <label class="flex items-center cursor-pointer select-none">
                    <input type="checkbox" 
                           x-model="remember" 
                           class="rounded border-gray-300 text-[#E3001B] focus:ring-[#E3001B] h-4 w-4 cursor-pointer">
                    <span class="ml-2 text-xs font-semibold text-gray-600">Recuérdame</span>
                </label>
                <span class="text-[11px] text-gray-400 italic">Extiende tu sesión de forma segura</span>
            </div>

            <!-- Botón Submit -->
            <button type="submit" 
                    :disabled="loading" 
                    class="w-full py-2.5 px-4 bg-[#E3001B] hover:bg-red-700 text-white font-bold rounded-xl shadow-sm transition-all duration-200 flex items-center justify-center gap-2 cursor-pointer disabled:opacity-50 disabled:cursor-not-allowed mt-2">
                <template x-if="!loading">
                    <span>Ingresar</span>
                </template>
                <template x-if="loading">
                    <span class="flex items-center gap-2 text-xs">
                        <svg class="animate-spin h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8z"></path>
                        </svg>
                        Verificando...
                    </span>
                </template>
            </button>
        </form>

        <!-- Footer del Formulario -->
        <div class="mt-6 text-center">
            <p class="text-xs text-gray-500">
                ¿No tienes una cuenta? 
                <a href="/auth/registro" class="font-bold text-[#E3001B] hover:underline ml-0.5">Quiero ser cliente</a>
            </p>
        </div>

    </div>
</div>

<script>
function loginForm() {
    return {
        email: '',
        password: '',
        remember: false,
        showPassword: false,
        error: '',
        loading: false,

        async submit() {
            this.loading = true;
            this.error = '';
            try {
                const data = await window.api('/api/auth/login', {
                    method: 'POST',
                    body: JSON.stringify({ 
                        email: this.email, 
                        password: this.password,
                        remember: this.remember
                    })
                });
                localStorage.setItem('jwt_token', data.token);
                let role = data.user?.rol || data.role || 'cliente';
                let nombre = data.user?.nombre || data.nombre || data.user?.email || '';
                localStorage.setItem('role', role);
                localStorage.setItem('user_nombre', nombre);
                
                if (role === 'cliente') {
                    window.location.href = '/ecommerce/catalogo';
                } else if (role === 'operador') {
                    window.location.href = '/gestion-pedidos';
                } else if (role === 'chofer') {
                    window.location.href = '/entregas';
                } else {
                    window.location.href = '/dashboard';
                }
            } catch (e) {
                this.error = e.message || 'Credenciales inválidas. Verifica tu email y contraseña.';
                Swal.fire({
                    icon: 'error',
                    title: 'Error al iniciar sesión',
                    text: this.error,
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
