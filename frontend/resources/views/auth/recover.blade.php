@extends('layouts.app')

@section('title', 'Recuperar Contraseña - Fritolay')

@section('content')
<div class="max-w-md mx-auto mt-10 bg-white p-8 rounded-lg shadow-md" x-data="recoverForm()">
    <div class="text-center mb-8">
        <h1 class="text-3xl font-bold text-primary">Frito<span class="text-secondary">lay</span></h1>
        <p class="text-neutral-dark mt-2">Recuperar Contraseña</p>
    </div>

    <!-- Step 1: Request PIN -->
    <form x-show="step === 1" @submit.prevent="requestPin" class="space-y-4">
        <template x-if="message">
            <div class="bg-green-100 text-green-700 p-3 rounded" x-text="message"></div>
        </template>
        <template x-if="error">
            <div class="bg-red-100 text-red-700 p-3 rounded" x-text="error"></div>
        </template>

        <div>
            <label class="block text-sm font-medium text-gray-700">Email</label>
            <input type="email" x-model="email" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm p-2 border focus:ring-primary focus:border-primary" required>
        </div>

        <button type="submit" class="w-full py-2 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-primary hover:bg-red-700">
            Enviar PIN
        </button>
    </form>

    <!-- Step 2: Reset Password -->
    <form x-show="step === 2" @submit.prevent="resetPassword" class="space-y-4">
        <template x-if="error">
            <div class="bg-red-100 text-red-700 p-3 rounded" x-text="error"></div>
        </template>

        <div>
            <label class="block text-sm font-medium text-gray-700">PIN de Recuperación</label>
            <input type="text" x-model="pin" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm p-2 border focus:ring-primary" required>
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700">Nueva Contraseña</label>
            <input type="password" x-model="newPassword" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm p-2 border focus:ring-primary" required>
        </div>

        <button type="submit" class="w-full py-2 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-primary hover:bg-red-700">
            Restablecer Contraseña
        </button>
    </form>
    
    <div class="mt-4 text-center">
        <a href="/login" class="text-sm text-primary hover:underline">Volver al login</a>
    </div>
</div>

<script>
function recoverForm() {
    return {
        step: 1,
        email: '',
        pin: '',
        newPassword: '',
        message: '',
        error: '',
        async requestPin() {
            this.error = '';
            // Mock api call
            this.message = 'PIN enviado a su correo.';
            this.step = 2;
        },
        async resetPassword() {
            this.error = '';
            // Mock api call
            Swal.fire('Éxito', 'Contraseña restablecida exitosamente', 'success');
            window.location.href = '/login';
        }
    }
}
</script>
@endsection


