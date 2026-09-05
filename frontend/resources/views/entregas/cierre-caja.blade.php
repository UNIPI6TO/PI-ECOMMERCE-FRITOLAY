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

    <div x-show="!loading && !loadError" class="space-y-6">
        
        <!-- Panel de Balance General (Ecuación Financiera de Jornada - Tema Claro y Legible) -->
        <div class="bg-white rounded-3xl p-6 shadow-sm border border-slate-200/80 relative overflow-hidden">
            <div class="flex items-center justify-between border-b border-slate-100 pb-3.5 mb-4">
                <span class="text-xs font-black uppercase tracking-wider text-slate-800 flex items-center gap-2">
                    <span class="p-1.5 rounded-lg bg-amber-100 text-amber-700">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg>
                    </span>
                    Balance General de la Jornada
                </span>
                <span class="text-xs font-black text-slate-700 bg-slate-100 px-3 py-1 rounded-full border border-slate-200" x-text="`Guía #${guiaId}`"></span>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 text-center md:text-left items-center">
                <!-- Monto Original cargado -->
                <div class="bg-slate-50 p-4 rounded-2xl border border-slate-200/80 shadow-2xs">
                    <span class="text-[11px] font-black uppercase text-slate-500 block mb-1">Monto Original Guía</span>
                    <span class="text-2xl font-black text-slate-900" x-text="formatMoney(financiero.monto_original)"></span>
                    <span class="text-[11px] text-slate-500 font-semibold block mt-0.5" x-text="`${totales.total} pedido(s) despachado(s)`"></span>
                </div>

                <!-- Devoluciones -->
                <div class="bg-rose-50/80 p-4 rounded-2xl border border-rose-200/80 shadow-2xs">
                    <span class="text-[11px] font-black uppercase text-rose-800 block mb-1">Total Devoluciones (N/C)</span>
                    <span class="text-2xl font-black text-rose-600" x-text="`-${formatMoney(financiero.total_devoluciones)}`"></span>
                    <span class="text-[11px] text-rose-700 font-bold block mt-0.5">Mercadería no entregada</span>
                </div>

                <!-- Total Recaudado Esperado -->
                <div class="bg-emerald-50/80 p-4 rounded-2xl border border-emerald-200/80 shadow-2xs">
                    <span class="text-[11px] font-black uppercase text-emerald-800 block mb-1">Total Recaudado</span>
                    <span class="text-2xl sm:text-3xl font-black text-emerald-700" x-text="formatMoney(financiero.total_recaudado)"></span>
                    <span class="text-[11px] text-emerald-700 font-bold block mt-0.5">Ingreso efectivo + bancos</span>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <!-- Resumen de Guía (Conteo + Valores) -->
            <div class="bg-white p-6 rounded-3xl shadow-sm border border-slate-200/80 flex flex-col justify-between">
                <div>
                    <h2 class="font-extrabold text-base text-slate-900 mb-4 pb-2 border-b border-slate-100 flex items-center justify-between">
                        <span>Resumen de Entregas</span>
                        <span class="text-xs font-bold text-slate-400 uppercase tracking-wider">Estado | Valor</span>
                    </h2>
                    <ul class="space-y-3 text-xs">
                        <li class="flex items-center justify-between">
                            <span class="font-bold text-slate-600">Pedidos Totales:</span>
                            <div class="text-right">
                                <span class="font-black text-slate-900" x-text="totales.total"></span>
                                <span class="text-slate-400 font-bold ml-1.5" x-text="`(${formatMoney(totales.monto_total)})`"></span>
                            </div>
                        </li>
                        <li class="flex items-center justify-between bg-emerald-50/60 p-2 rounded-xl border border-emerald-100/80">
                            <span class="font-extrabold text-emerald-800 flex items-center gap-1.5">
                                <span class="w-2 h-2 rounded-full bg-emerald-500"></span> Entregados
                            </span>
                            <div class="text-right">
                                <span class="font-black text-emerald-800" x-text="totales.entregados"></span>
                                <span class="text-emerald-700 font-extrabold ml-1.5" x-text="`| ${formatMoney(totales.monto_entregados)}`"></span>
                            </div>
                        </li>
                        <li class="flex items-center justify-between bg-amber-50/60 p-2 rounded-xl border border-amber-100/80">
                            <span class="font-extrabold text-amber-800 flex items-center gap-1.5">
                                <span class="w-2 h-2 rounded-full bg-amber-500"></span> Entregas Parciales
                            </span>
                            <div class="text-right">
                                <span class="font-black text-amber-800" x-text="totales.parciales"></span>
                                <span class="text-amber-700 font-extrabold ml-1.5" x-text="`| ${formatMoney(totales.monto_parciales)}`"></span>
                            </div>
                        </li>
                        <li class="flex items-center justify-between bg-rose-50/60 p-2 rounded-xl border border-rose-100/80">
                            <span class="font-extrabold text-rose-800 flex items-center gap-1.5">
                                <span class="w-2 h-2 rounded-full bg-rose-500"></span> No Entregados
                            </span>
                            <div class="text-right">
                                <span class="font-black text-rose-800" x-text="totales.no_entregados"></span>
                                <span class="text-rose-700 font-extrabold ml-1.5" x-text="`| ${formatMoney(totales.monto_no_entregados)}`"></span>
                            </div>
                        </li>
                    </ul>
                </div>
            </div>

            <!-- Recaudación por Sistema (desde BD) -->
            <div class="bg-white p-6 rounded-3xl shadow-sm border border-slate-200/80 flex flex-col justify-between">
                <div>
                    <h2 class="font-extrabold text-base text-slate-900 mb-4 pb-2 border-b border-slate-100 flex items-center justify-between">
                        <span>Recaudación por Método</span>
                        <span class="text-xs font-bold text-slate-400 uppercase tracking-wider">Sistema</span>
                    </h2>
                    <ul class="space-y-3 text-xs">
                        <li class="flex justify-between items-center bg-slate-50 p-2 rounded-xl border border-slate-100">
                            <span class="font-bold text-slate-700">Efectivo a entregar:</span>
                            <span class="font-black text-emerald-600 text-sm" x-text="formatMoney(sistema.efectivo)"></span>
                        </li>
                        <li class="flex justify-between items-center p-2">
                            <span class="font-semibold text-slate-600">Depósitos / Transferencias:</span>
                            <span class="font-extrabold text-slate-800" x-text="formatMoney(sistema.bancos)"></span>
                        </li>
                        <li class="flex justify-between items-center p-2">
                            <span class="font-semibold text-slate-600">De Una:</span>
                            <span class="font-extrabold text-slate-800" x-text="formatMoney(sistema.de_una)"></span>
                        </li>
                    </ul>
                </div>
                <div class="mt-4 pt-3 border-t border-slate-100 flex justify-between items-center">
                    <span class="font-extrabold text-slate-900 text-sm">Total Recaudado:</span>
                    <span class="font-black text-xl text-slate-900" x-text="formatMoney(sistema.total)"></span>
                </div>
            </div>
        </div>

        <!-- Declaración de Efectivo -->
        <div class="bg-white p-8 rounded-3xl shadow-sm border border-slate-200/80 text-center">
            <h2 class="text-xl font-black text-slate-900 mb-1">Declaración de Efectivo</h2>
            <p class="text-xs font-semibold text-slate-500 mb-3">Ingrese el monto exacto de billetes y monedas que tiene en mano.</p>
            
            <!-- Indicador de Efectivo Esperado a declarar -->
            <div class="inline-flex items-center gap-2 bg-emerald-50 border border-emerald-200/80 px-3.5 py-1.5 rounded-full text-xs font-black text-emerald-800 mb-6">
                <span>Efectivo esperado a declarar:</span>
                <span class="text-emerald-700 text-sm font-black" x-text="formatMoney(sistema.efectivo)"></span>
            </div>

            <div class="flex items-center justify-center">
                <span class="text-3xl font-black text-slate-300 mr-2">$</span>
                <input type="tel" 
                       class="text-4xl w-52 text-center border-b-2 border-slate-300 focus:border-slate-900 focus:outline-none py-2 font-black text-slate-900 tracking-tight" 
                       x-on:focus="moveCursorToEnd($event)"
                       x-on:click="moveCursorToEnd($event)"
                       x-on:input="handleInput($event)"
                       :value="displayValue">
            </div>

            <div class="mt-6 p-4 rounded-2xl transition-all" 
                 :class="Math.abs(diferencia) < 0.01 ? 'bg-emerald-50 text-emerald-900 border border-emerald-200/80' : (diferencia < 0 ? 'bg-rose-50 text-rose-900 border border-rose-200/80' : 'bg-amber-50 text-amber-900 border border-amber-200/80')">
                <div class="font-black text-base">Diferencia: <span x-text="formatMoney(Math.abs(diferencia))"></span></div>
                <div class="text-xs font-bold mt-0.5" x-text="mensajeDiferencia"></div>
            </div>
        </div>

        <button @click="declarar" 
                :disabled="submitting || Math.abs(diferencia) >= 0.01" 
                class="w-full bg-slate-900 hover:bg-slate-800 active:scale-[0.99] text-white font-extrabold py-4 rounded-2xl text-base shadow-md disabled:opacity-40 disabled:cursor-not-allowed cursor-pointer transition-all">
            <span x-show="!submitting">Finalizar Jornada</span>
            <span x-show="submitting">Finalizando jornada...</span>
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
            financiero: { monto_original: 0, total_devoluciones: 0, total_recaudado: 0 },
            totales: { total: 0, monto_total: 0, entregados: 0, monto_entregados: 0, parciales: 0, monto_parciales: 0, no_entregados: 0, monto_no_entregados: 0 },
            sistema: { efectivo: 0, bancos: 0, de_una: 0, total: 0 },
            declaradoStr: '0',

            async init() {
                if (!this.guiaId) { this.loading = false; return; }
                try {
                    const res = await window.api(`/api/guias-ruta/${this.guiaId}/resumen-caja`);
                    this.financiero = res.financiero || { monto_original: 0, total_devoluciones: 0, total_recaudado: 0 };
                    this.totales    = res.totales    || { total: 0, monto_total: 0, entregados: 0, monto_entregados: 0, parciales: 0, monto_parciales: 0, no_entregados: 0, monto_no_entregados: 0 };
                    this.sistema    = res.recaudacion || { efectivo: 0, bancos: 0, de_una: 0, total: 0 };
                    // Ensure numbers
                    this.financiero.monto_original     = parseFloat(this.financiero.monto_original) || 0;
                    this.financiero.total_devoluciones = parseFloat(this.financiero.total_devoluciones) || 0;
                    this.financiero.total_recaudado    = parseFloat(this.financiero.total_recaudado) || 0;
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

            moveCursorToEnd(e) {
                const el = e.target;
                requestAnimationFrame(() => {
                    if (el && typeof el.setSelectionRange === 'function') {
                        const len = el.value.length;
                        el.setSelectionRange(len, len);
                    }
                });
            },

            handleInput(e) {
                let val = e.target.value.replace(/\D/g, '');
                val = val.replace(/^0+/, '');
                if (val === '') val = '0';
                this.declaradoStr = val;
                e.target.value = this.displayValue;
                this.moveCursorToEnd(e);
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
                if (Math.abs(this.diferencia) >= 0.01) {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Arqueo Descuadrado',
                        text: 'No es posible finalizar la jornada si existe diferencia (faltante o sobrante) en la declaración de efectivo.',
                        toast: true,
                        position: 'bottom',
                        showConfirmButton: false,
                        timer: 3500
                    });
                    return;
                }

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
