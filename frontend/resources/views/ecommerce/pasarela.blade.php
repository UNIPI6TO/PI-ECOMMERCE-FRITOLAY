@extends('layouts.app')

@section('content')
<div class="max-w-3xl mx-auto py-12 px-4" x-data="{ metodo: 'TARJETA', procesando: false, mensaje: '' }">
    <div class="bg-white p-8 rounded-lg shadow text-center">
        <h1 class="text-3xl font-bold mb-6">Pasarela de Pago</h1>

        <!-- Tarjeta -->
        <div x-show="metodo === 'TARJETA'" class="max-w-md mx-auto text-left">
            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-medium mb-1">Número de Tarjeta</label>
                    <input type="text" maxlength="16" class="w-full border rounded px-3 py-2" placeholder="1234 5678 9101 1121">
                </div>
                <div>
                    <label class="block text-sm font-medium mb-1">Nombre en la tarjeta</label>
                    <input type="text" class="w-full border rounded px-3 py-2">
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium mb-1">Vencimiento</label>
                        <input type="text" placeholder="MM/YY" class="w-full border rounded px-3 py-2">
                    </div>
                    <div>
                        <label class="block text-sm font-medium mb-1">CVV</label>
                        <input type="text" maxlength="3" class="w-full border rounded px-3 py-2">
                    </div>
                </div>
                <button @click="procesando = true; setTimeout(() => { procesando=false; mensaje='Pago confirmado' }, 2000)" class="w-full bg-blue-600 hover:bg-blue-700 text-white font-bold py-3 rounded mt-4">
                    <span x-show="!procesando">Pagar Ahora</span>
                    <span x-show="procesando">Procesando...</span>
                </button>
            </div>
        </div>

        <!-- De Una -->
        <div x-show="metodo === 'DE_UNA'" class="py-8">
            <svg class="mx-auto w-48 h-48 text-gray-800" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm14 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z" />
            </svg>
            <p class="mt-4 font-medium">Escanea el QR para pagar</p>
        </div>

        <!-- Mensaje Éxito -->
        <div x-show="mensaje" class="mt-6 p-4 bg-green-100 text-green-700 rounded font-semibold text-lg">
            <span x-text="mensaje"></span>
        </div>
    </div>
</div>
@endsection
