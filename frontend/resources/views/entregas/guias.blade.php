@extends('layouts.app')

@section('title', 'Mis Rutas de Entrega')

@section('content')
<!-- Importar pdfmake para generación de PDFs del lado del cliente -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/pdfmake.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/vfs_fonts.js"></script>

<div class="max-w-4xl mx-auto py-4 px-3 sm:px-6 pb-24" x-data="guiasActivas()">
    <!-- Header Fijo con Indicador de Rol y Estado del Chofer -->
    <div class="mb-6 flex items-center justify-between gap-3 flex-wrap">
        <div>
            <h1 class="text-2xl font-black text-slate-900 tracking-tight">Mis Rutas Asignadas</h1>
            <p class="text-xs text-slate-500 font-semibold mt-0.5">Seleccione la ruta activa para iniciar la navegación y entregas</p>
        </div>

        <!-- Badge Estado Fase del Chofer -->
        <div class="px-3.5 py-1.5 rounded-full text-xs font-black uppercase tracking-wider border shadow-2xs flex items-center gap-2"
             :class="{
                 'bg-emerald-100 text-emerald-800 border-emerald-300 animate-pulse': estadoChofer.fase === 'LIBRE',
                 'bg-amber-100 text-amber-800 border-amber-300 animate-pulse': estadoChofer.fase === 'EN_CAMINO',
                 'bg-blue-100 text-blue-800 border-blue-300 animate-pulse': estadoChofer.fase === 'ENTREGANDO'
             }">
            <span class="w-2.5 h-2.5 rounded-full"
                  :class="{
                      'bg-emerald-500': estadoChofer.fase === 'LIBRE',
                      'bg-amber-500': estadoChofer.fase === 'EN_CAMINO',
                      'bg-blue-500': estadoChofer.fase === 'ENTREGANDO'
                  }"></span>
            <span x-text="`Estado: ${estadoChofer.label || 'Cargando...'}`"></span>
        </div>
    </div>

    <!-- Lista de Rutas Asignadas en Tarjetas Mobile-First -->
    <div class="space-y-4">
        <template x-for="guia in guias" :key="guia.id">
            <div class="bg-white p-5 rounded-3xl shadow-2xs border border-slate-200/80 relative overflow-hidden transition-all hover:shadow-md">
                <!-- Banner Superior Decorativo de Marca -->
                <div class="absolute top-0 left-0 right-0 h-1.5 bg-gradient-to-r from-[#E3001B] via-[#F5C518] to-slate-900"></div>

                <!-- Encabezado y Estado de la Ruta -->
                <div class="flex items-center justify-between mb-4 pt-1">
                    <div>
                        <div class="flex items-center gap-2">
                            <span class="w-8 h-8 rounded-xl bg-slate-900 text-white font-black text-xs flex items-center justify-center shadow-2xs">🚚</span>
                            <h2 class="text-xl font-black text-slate-900 tracking-tight">Ruta #<span x-text="guia.id"></span></h2>
                        </div>
                        <p class="text-xs text-slate-500 font-semibold mt-1 flex items-center gap-1.5">
                            <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                            <span x-text="`Fecha: ${guia.fecha}`"></span>
                            <span class="text-slate-300">•</span>
                            <span class="font-extrabold text-slate-800" x-text="`${guia.pedidos_count} pedidos`"></span>
                        </p>
                    </div>

                    <a :href="`/entregas/mapa/${guia.id}`" 
                       class="h-12 px-6 bg-[#E3001B] hover:bg-red-700 active:scale-95 text-white font-black text-sm rounded-2xl flex items-center justify-center gap-2 shadow-md transition-all border border-red-500">
                        <span>Iniciar Ruta</span>
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                    </a>
                </div>

                <!-- Resumen Financiero Destacado -->
                <template x-if="guia.recaudacion_esperada">
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 my-4">
                        <div class="bg-slate-50 p-3.5 rounded-2xl border border-slate-200/80">
                            <h3 class="font-black text-slate-500 mb-2 text-[10px] uppercase tracking-wider">Resumen de Pagos Digitales</h3>
                            <div class="space-y-1.5 text-xs font-semibold">
                                <div class="flex justify-between">
                                    <span class="text-slate-500">Depósitos / De Una:</span>
                                    <span class="font-bold text-slate-900" x-text="formatMoney(guia.recaudacion_esperada.transferencia)"></span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-slate-500">Tarjetas Crédito/Débito:</span>
                                    <span class="font-bold text-slate-900" x-text="formatMoney(guia.recaudacion_esperada.tarjeta)"></span>
                                </div>
                            </div>
                        </div>

                        <!-- Card Destacado Efectivo Real esperable -->
                        <div class="bg-emerald-50 border border-emerald-200 p-3.5 rounded-2xl flex flex-col justify-center items-center shadow-2xs">
                            <h3 class="font-black text-emerald-800 text-[10px] uppercase tracking-wider">Total Recaudado en Efectivo</h3>
                            <p class="text-2xl font-black text-emerald-700 my-0.5" x-text="formatMoney(guia.recaudacion_esperada.efectivo)"></p>
                            <p class="text-[10px] text-emerald-600 font-extrabold">Físico a entregar en caja</p>
                        </div>
                    </div>
                </template>

                <!-- Acciones Secundarias (Descarga de PDFs Client-Side) -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-2.5 pt-3 border-t border-slate-100">
                    <button @click="generarGuiaRemision(guia.id)" 
                            class="h-12 bg-slate-100 hover:bg-slate-200 active:bg-slate-300 text-slate-800 rounded-xl text-xs font-black transition-all flex items-center justify-center gap-2 border border-slate-200 disabled:opacity-50" 
                            :disabled="loadingPdf === guia.id">
                        <svg x-show="loadingPdf !== guia.id" class="w-4 h-4 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                        <svg x-show="loadingPdf === guia.id" class="animate-spin h-4 w-4 text-red-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                        <span>Guía de Remisión (SRI)</span>
                    </button>

                    <button @click="generarGuiaRuta(guia.id)" 
                            class="h-12 bg-slate-100 hover:bg-slate-200 active:bg-slate-300 text-slate-800 rounded-xl text-xs font-black transition-all flex items-center justify-center gap-2 border border-slate-200 disabled:opacity-50" 
                            :disabled="loadingPdf === guia.id">
                        <svg x-show="loadingPdf !== guia.id" class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17a2 2 0 11-4 0 2 2 0 014 0zM19 17a2 2 0 11-4 0 2 2 0 014 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16V6a1 1 0 00-1-1H4a1 1 0 00-1 1v10a1 1 0 001 1h1m8-1a1 1 0 01-1 1H9m4-1V8a1 1 0 011-1h2.586a1 1 0 01.707.293l3.414 3.414a1 1 0 01.293.707V16a1 1 0 01-1 1h-1m-6-1a1 1 0 001 1h1M5 17a2 2 0 104 0m-4 0a2 2 0 114 0m6 0a2 2 0 104 0m-4 0a2 2 0 114 0"></path></svg>
                        <svg x-show="loadingPdf === guia.id" class="animate-spin h-4 w-4 text-blue-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                        <span>Listado de Ruta Detallado</span>
                    </button>
                </div>
            </div>
        </template>

        <!-- Empty State en caso de no tener rutas asignadas -->
        <div x-show="guias.length === 0" class="text-center text-slate-500 py-16 bg-white rounded-3xl border border-slate-200/80 shadow-2xs p-6">
            <div class="w-16 h-16 bg-slate-100 text-slate-400 rounded-3xl flex items-center justify-center mx-auto mb-3 text-2xl">🚚</div>
            <h3 class="text-base font-black text-slate-800 mb-1">Sin Rutas Asignadas</h3>
            <p class="text-xs text-slate-500 font-semibold max-w-sm mx-auto">No tienes rutas de entrega programadas para hoy. Contacta al operador de ruta si necesitas asignaciones.</p>
        </div>
    </div>
</div>

<script>
document.addEventListener('alpine:init', () => {
    Alpine.data('guiasActivas', () => ({
        guias: [],
        loadingPdf: null,
        estadoChofer: { fase: 'LIBRE', label: 'Cargando...', mensaje: '' },
        
        async init() {
            try {
                this.guias = await window.api('/api/guias-ruta');
                if (this.guias && this.guias.length > 0 && typeof window.startTracking === 'function') {
                    const primerCamionId = this.guias[0].camion_id;
                    if (primerCamionId) {
                        window.startTracking(primerCamionId);
                    }
                }
                const resFase = await window.api('/api/chofer/fase');
                if (resFase) this.estadoChofer = resFase;
            } catch (error) {
                console.error("Error al cargar guias:", error);
            }
        },

        // Client-Side Rendering: Guía de Remisión
        async generarGuiaRemision(guiaId) {
            this.loadingPdf = guiaId;
            try {
                // Fetch lightweight JSON data (NO PDF buffer from backend)
                const pedidos = await window.api(`/api/guias-ruta/${guiaId}/pedidos`);
                if(!pedidos || pedidos.length === 0) throw new Error("No hay pedidos en la ruta.");

                // Generar tabla de items global
                const bodyTable = [
                    [{text: 'Cantidad', style: 'tableHeader'}, {text: 'Descripción', style: 'tableHeader'}, {text: 'P. Unitario', style: 'tableHeader'}, {text: 'Total', style: 'tableHeader'}]
                ];
                
                let subtotalGlobal = 0;
                let ivaGlobal = 0;

                pedidos.forEach(p => {
                    subtotalGlobal += parseFloat(p.subtotal) || 0;
                    ivaGlobal += parseFloat(p.iva) || 0;
                    if(p.items && p.items.length) {
                        p.items.forEach(item => {
                            const cant = parseInt(item.cantidad) || 0;
                            const pUnit = parseFloat(item.precio_unitario) || 0;
                            const subT = parseFloat(item.subtotal) || (cant * pUnit);
                            bodyTable.push([
                                cant,
                                `${item.producto} (Pedido #${p.id})`,
                                `$${pUnit.toFixed(2)}`,
                                `$${subT.toFixed(2)}`
                            ]);
                        });
                    }
                });

                const totalGlobal = subtotalGlobal + ivaGlobal;

                // Definición declarativa del PDF usando pdfMake
                const docDefinition = {
                    content: [
                        { text: 'GUÍA DE REMISIÓN', style: 'header' },
                        { text: `R.U.C.: 0990000000001`, style: 'subheader' },
                        { text: `N° Guía Ruta: 001-001-000000${guiaId}`, margin: [0, 5, 0, 15], alignment: 'right', fontSize: 12, bold: true },
                        
                        {
                            columns: [
                                {
                                    width: '50%',
                                    text: [
                                        { text: 'Razón Social: ', bold: true }, 'FRITOLAY S.A.\n',
                                        { text: 'Dirección Matriz: ', bold: true }, 'Av. Principal 123\n',
                                        { text: 'Obligado a llevar contabilidad: ', bold: true }, 'SI\n'
                                    ]
                                },
                                {
                                    width: '50%',
                                    text: [
                                        { text: 'Fecha de Emisión: ', bold: true }, new Date().toLocaleDateString() + '\n',
                                        { text: 'Motivo Traslado: ', bold: true }, 'Venta\n',
                                        { text: 'Punto de Partida: ', bold: true }, 'Bodega Central\n'
                                    ]
                                }
                            ],
                            margin: [0, 0, 0, 20]
                        },
                        
                        { text: 'Destinatarios (Clientes)', style: 'subheader' },
                        {
                            ul: pedidos.map(p => `${p.cliente} (CI/RUC: ${p.identificacion}) - ${p.direccion}`)
                        },
                        
                        { text: '\nDetalle de Mercadería', style: 'subheader', margin: [0, 15, 0, 5] },
                        {
                            table: {
                                headerRows: 1,
                                widths: ['auto', '*', 'auto', 'auto'],
                                body: bodyTable
                            },
                            layout: 'lightHorizontalLines'
                        },
                        
                        {
                            columns: [
                                { width: '*', text: '' },
                                {
                                    width: 'auto',
                                    table: {
                                        body: [
                                            [{text: 'Subtotal:', bold: true}, `$${subtotalGlobal.toFixed(2)}`],
                                            [{text: 'IVA 15%:', bold: true}, `$${ivaGlobal.toFixed(2)}`],
                                            [{text: 'TOTAL:', bold: true}, `$${totalGlobal.toFixed(2)}`]
                                        ]
                                    },
                                    margin: [0, 15, 0, 0]
                                }
                            ]
                        }
                    ],
                    styles: {
                        header: { fontSize: 22, bold: true, alignment: 'center', margin: [0, 0, 0, 5] },
                        subheader: { fontSize: 14, bold: true, margin: [0, 10, 0, 5] },
                        tableHeader: { bold: true, fontSize: 13, color: 'black', fillColor: '#eeeeee' }
                    }
                };

                pdfMake.createPdf(docDefinition).download(`Guia_Remision_Ruta_${guiaId}.pdf`);

            } catch (e) {
                console.error(e);
                Swal.fire('Error', 'No se pudo generar la guía de remisión.', 'error');
            } finally {
                this.loadingPdf = null;
            }
        },

        // Client-Side Rendering: Guía de Ruta
        async generarGuiaRuta(guiaId) {
            this.loadingPdf = guiaId;
            try {
                const pedidos = await window.api(`/api/guias-ruta/${guiaId}/pedidos`);
                if(!pedidos || pedidos.length === 0) throw new Error("No hay pedidos en la ruta.");

                const bodyTable = [
                    [{text: 'Orden', style: 'tableHeader'}, {text: 'Cliente', style: 'tableHeader'}, {text: 'Dirección', style: 'tableHeader'}, {text: 'Contacto', style: 'tableHeader'}, {text: 'Método Pago', style: 'tableHeader'}, {text: 'A Cobrar', style: 'tableHeader'}]
                ];

                pedidos.forEach(p => {
                    bodyTable.push([
                        p.orden,
                        p.cliente,
                        p.direccion,
                        p.telefono || 'N/A',
                        p.metodo_pago ? p.metodo_pago.toUpperCase() : 'N/A',
                        `$${parseFloat(p.total).toFixed(2)}`
                    ]);
                });

                const docDefinition = {
                    pageOrientation: 'landscape',
                    content: [
                        { text: 'Listado de Ruta Detallado', style: 'header' },
                        { text: `Ruta #${guiaId} - Fecha: ${new Date().toLocaleDateString()}`, style: 'subheader', alignment: 'center', margin: [0, 0, 0, 20] },
                        
                        {
                            table: {
                                headerRows: 1,
                                widths: ['auto', 'auto', '*', 'auto', 'auto', 'auto'],
                                body: bodyTable
                            },
                            layout: {
                                hLineWidth: function (i, node) { return (i === 0 || i === node.table.body.length) ? 2 : 1; },
                                vLineWidth: function (i, node) { return 0; },
                                hLineColor: function (i, node) { return (i === 0 || i === node.table.body.length) ? 'black' : 'gray'; },
                                paddingLeft: function(i, node) { return 5; },
                                paddingRight: function(i, node) { return 5; },
                                paddingTop: function(i, node) { return 8; },
                                paddingBottom: function(i, node) { return 8; }
                            }
                        }
                    ],
                    styles: {
                        header: { fontSize: 22, bold: true, alignment: 'center' },
                        subheader: { fontSize: 14, color: 'gray' },
                        tableHeader: { bold: true, fontSize: 12, color: 'white', fillColor: '#E3001B' }
                    }
                };

                // Offloading procesamiento: El navegador renderiza el PDF localmente
                pdfMake.createPdf(docDefinition).download(`Listado_Ruta_${guiaId}.pdf`);

            } catch (e) {
                console.error(e);
                Swal.fire('Error', 'No se pudo generar la guía de ruta.', 'error');
            } finally {
                this.loadingPdf = null;
            }
        }
    }));
});
</script>
@endsection
