@extends('layouts.app')

@section('content')
<div class="max-w-3xl mx-auto py-8 px-4" x-data="cierreCaja()">
    <h1 class="text-2xl font-bold mb-6 text-center">Arqueo y Cierre de Caja</h1>

    <!-- Loading state -->
    <div x-show="loadError" class="text-center py-12 text-red-500">
        <p class="font-bold">Error: no se pudo identificar la guía de ruta.</p>
        <p class="text-sm mt-2">Vuelve a <a href="/entregas" class="underline">Mis Rutas</a> y presiona "Terminar Ruta".</p>
    </div>
    <div x-show="loading && !loadError" class="text-center py-12 text-gray-500">
        <svg class="animate-spin h-8 w-8 mx-auto mb-2 text-gray-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8z"></path>
        </svg>
        Cargando resumen...
    </div>

    <div x-show="!loading && !loadError">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
            <!-- Resumen de Guía -->
            <div class="bg-white p-6 rounded shadow border-t-4 border-blue-500">
                <h2 class="font-bold mb-4 text-gray-700">Resumen de Guía</h2>
                <ul class="space-y-2 text-sm">
                    <li class="flex justify-between"><span>Pedidos Totales:</span> <span class="font-bold" x-text="totales.total"></span></li>
                    <li class="flex justify-between text-green-600"><span>Entregados:</span> <span class="font-bold" x-text="totales.entregados"></span></li>
                    <li class="flex justify-between text-yellow-600"><span>Entregas Parciales:</span> <span class="font-bold" x-text="totales.parciales"></span></li>
                    <li class="flex justify-between text-red-600"><span>No Entregados:</span> <span class="font-bold" x-text="totales.no_entregados"></span></li>
                </ul>
            </div>

            <!-- Recaudación por Sistema (desde BD) -->
            <div class="bg-white p-6 rounded shadow border-t-4 border-green-500">
                <h2 class="font-bold mb-4 text-gray-700">Recaudación por Sistema</h2>
                <ul class="space-y-2 text-sm">
                    <li class="flex justify-between">
                        <span>Efectivo:</span>
                        <span class="font-bold text-green-700">$<span x-text="sistema.efectivo.toFixed(2)"></span></span>
                    </li>
                    <li class="flex justify-between">
                        <span>Depósitos/Transf:</span>
                        <span class="font-bold">$<span x-text="sistema.bancos.toFixed(2)"></span></span>
                    </li>
                    <li class="flex justify-between">
                        <span>De Una:</span>
                        <span class="font-bold">$<span x-text="sistema.de_una.toFixed(2)"></span></span>
                    </li>
                </ul>
                <div class="mt-4 pt-2 border-t flex justify-between font-bold text-lg">
                    <span>Total:</span>
                    <span>$<span x-text="sistema.total.toFixed(2)"></span></span>
                </div>
            </div>
        </div>

        <!-- Declaración de Efectivo -->
        <div class="bg-white p-8 rounded shadow text-center mb-6">
            <h2 class="text-xl font-bold mb-2">Declaración de Efectivo</h2>
            <p class="text-sm text-gray-600 mb-6">Ingrese el monto exacto de billetes y monedas que tiene en mano.</p>
            
            <div class="flex items-center justify-center">
                <span class="text-3xl font-bold text-gray-400 mr-2">$</span>
                <input type="tel" 
                       class="text-4xl w-48 text-center border-b-2 border-gray-300 focus:border-green-500 focus:outline-none py-2 font-bold text-green-700" 
                       x-on:input="handleInput($event)"
                       :value="displayValue">
            </div>

            <div class="mt-6 p-4 rounded" :class="diferencia === 0 ? 'bg-green-100 text-green-800' : (diferencia < 0 ? 'bg-red-100 text-red-800' : 'bg-yellow-100 text-yellow-800')">
                <div class="font-bold">Diferencia: $<span x-text="Math.abs(diferencia).toFixed(2)"></span></div>
                <div class="text-sm" x-text="mensajeDiferencia"></div>
            </div>
        </div>

        <button @click="declarar" :disabled="submitting" class="w-full bg-gray-900 hover:bg-black text-white font-bold py-4 rounded text-lg disabled:opacity-50 transition-colors">
            <span x-show="!submitting">Confirmar Arqueo y Finalizar Jornada</span>
            <span x-show="submitting">Registrando...</span>
        </button>
    </div>
</div>

<script>
document.addEventListener('alpine:init', () => {
    Alpine.data('cierreCaja', () => {
        const params = new URLSearchParams(window.location.search);
        const guiaId = params.get('guia') || null;

        return {
            guiaId: guiaId,
            loading: true,
            loadError: !guiaId,
            submitting: false,
            totales: { total: 0, entregados: 0, parciales: 0, no_entregados: 0 },
            sistema: { efectivo: 0, bancos: 0, de_una: 0, total: 0 },
            declaradoStr: '0',

            async init() {
                if (!this.guiaId) { this.loading = false; return; }
                try {
                    const res = await window.api(`/api/guias-ruta/${this.guiaId}/resumen-caja`);
                    this.totales  = res.totales  || { total: 0, entregados: 0, parciales: 0, no_entregados: 0 };
                    this.sistema  = res.recaudacion || { efectivo: 0, bancos: 0, de_una: 0, total: 0 };
                    // Ensure numbers
                    this.sistema.efectivo = parseFloat(this.sistema.efectivo) || 0;
                    this.sistema.bancos   = parseFloat(this.sistema.bancos)   || 0;
                    this.sistema.de_una   = parseFloat(this.sistema.de_una)   || 0;
                    this.sistema.total    = parseFloat(this.sistema.total)    || 0;
                } catch(e) {
                    console.error('Error cargando resumen:', e);
                    Swal.fire({ icon: 'error', title: 'Error', text: 'No se pudo cargar el resumen de la guía.', toast: true, position: 'bottom', showConfirmButton: false, timer: 3000 });
                } finally {
                    this.loading = false;
                }
            },

            get declarado() {
                return (parseInt(this.declaradoStr, 10) / 100) || 0;
            },

            get displayValue() {
                return this.declarado.toFixed(2);
            },

            handleInput(e) {
                let val = e.target.value.replace(/\D/g, '');
                val = val.replace(/^0+/, '');
                if (val === '') val = '0';
                this.declaradoStr = val;
                e.target.value = this.displayValue;
            },

            // Diferencia = efectivo declarado - efectivo esperado por sistema
            get diferencia() {
                return this.declarado - this.sistema.efectivo;
            },

            get mensajeDiferencia() {
                if (this.diferencia === 0) return 'Caja cuadrada perfectamente.';
                if (this.diferencia < 0)  return 'Faltante de caja. Se reportará al administrador.';
                return 'Sobrante de caja. Se reportará al administrador.';
            },

            async declarar() {
                if (this.submitting) return;
                this.submitting = true;
                try {
                    await window.api(`/api/guias-ruta/${this.guiaId}/arqueo`, {
                        method: 'POST',
                        body: JSON.stringify({ efectivo_declarado: this.declarado })
                    });
                    await Swal.fire({ icon: 'success', title: '¡Buen trabajo!', text: 'Arqueo registrado exitosamente.', toast: true, position: 'bottom', showConfirmButton: false, timer: 3000 });
                    window.location.href = '/entregas';
                } catch(e) {
                    Swal.fire({ icon: 'error', title: 'Error', text: e.message || 'Error al declarar arqueo', toast: true, position: 'bottom', showConfirmButton: false, timer: 3000 });
                } finally {
                    this.submitting = false;
                }
            }
        };
    });
});
</script>
@endsection
