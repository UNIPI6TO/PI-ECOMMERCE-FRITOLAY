@extends('layouts.app')

@section('content')
<div class="max-w-2xl mx-auto py-16 px-4 text-center">
    <div class="bg-white p-10 rounded-lg shadow-lg">
        <div class="flex justify-center mb-6">
            <div class="w-24 h-24 bg-green-100 rounded-full flex items-center justify-center">
                <svg class="w-12 h-12 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path>
                </svg>
            </div>
        </div>
        
        <h1 class="text-4xl font-bold text-gray-800 mb-4">¡Compra Confirmada!</h1>
        <p class="text-lg text-gray-600 mb-2">Tu pedido ha sido procesado exitosamente.</p>
        <p class="text-md text-gray-500 mb-8">Número de pedido: <span class="font-bold text-black">#PED-8899</span></p>
        
        <div class="flex justify-center space-x-4">
            <a href="/ecommerce/historial" class="px-6 py-3 bg-[#E3001B] text-white font-bold rounded hover:bg-red-700">
                Ver mis pedidos
            </a>
            <button onclick="if(window.pdfGenerator) window.pdfGenerator.generateFactura()" class="px-6 py-3 border border-gray-300 text-gray-700 font-bold rounded hover:bg-gray-50">
                Descargar Factura
            </button>
        </div>
    </div>
</div>
@endsection
