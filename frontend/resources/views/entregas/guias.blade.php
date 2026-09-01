@extends('layouts.app')

@section('content')
<!-- Importar pdfmake para generación de PDFs del lado del cliente -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/pdfmake.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/vfs_fonts.js"></script>

<div class="max-w-4xl mx-auto py-8 px-4" x-data="guiasActivas()">
    <h1 class="text-2xl font-bold mb-6">Mis Rutas Asignadas</h1>

    <div class="space-y-6">
        <template x-for="guia in guias" :key="guia.id">
            <div class="bg-white p-6 rounded shadow border-l-4 border-[#F5C518]">
                <!-- Encabezado y Acción Principal -->
                <div class="flex items-center justify-between mb-4">
                    <div>
                        <h2 class="text-xl font-bold">Ruta #<span x-text="guia.id"></span></h2>
                        <p class="text-gray-600 text-sm mt-1"><span x-text="guia.pedidos_count"></span> pedidos asignados</p>
                        <p class="text-gray-500 text-xs mt-1">Fecha: <span x-text="guia.fecha"></span></p>
                    </div>
                    <div>
                        <a :href="`/entregas/mapa/${guia.id}`" class="bg-[#E3001B] text-white px-8 py-3 rounded font-bold hover:bg-red-700 shadow-sm transition-colors text-lg inline-block">
                            Iniciar Ruta
                        </a>
                    </div>
                </div>

                <!-- Resumen Financiero -->
                <template x-if="guia.recaudacion_esperada">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-4 mb-6">
                        <div class="bg-gray-50 p-4 rounded border border-gray-200">
                            <h3 class="font-bold text-gray-700 mb-3 text-sm">Resumen de Pagos Digitales</h3>
                            <div class="space-y-2 text-sm">
                                <div class="flex justify-between">
                                    <span class="text-gray-600">Transferencias / Depósitos:</span>
                                    <span class="font-semibold" x-text="formatMoney(guia.recaudacion_esperada.transferencia)"></span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-gray-600">Tarjetas de Crédito/Débito:</span>
                                    <span class="font-semibold" x-text="formatMoney(guia.recaudacion_esperada.tarjeta)"></span>
                                </div>
                            </div>
                        </div>
                        <div class="bg-green-50 border border-green-300 p-4 rounded flex flex-col justify-center items-center shadow-inner">
                            <h3 class="font-bold text-green-800 mb-1 text-sm uppercase tracking-wider">Total Recaudado en Efectivo</h3>
                            <p class="text-3xl font-black text-green-700" x-text="formatMoney(guia.recaudacion_esperada.efectivo)"></p>
                            <p class="text-xs text-green-600 mt-1 font-semibold">Requerido para el cuadre final</p>
                        </div>
                    </div>
                </template>

                <!-- Acciones Secundarias (PDFs Client-Side) -->
                <div class="flex flex-col sm:flex-row gap-3 pt-4 border-t border-gray-100">
                    <button @click="generarGuiaRemision(guia.id)" class="flex-1 bg-gray-100 hover:bg-gray-200 text-gray-800 py-2 px-4 rounded text-sm font-bold transition-colors flex items-center justify-center gap-2 border border-gray-300 disabled:opacity-50" :disabled="loadingPdf === guia.id">
                        <svg x-show="loadingPdf !== guia.id" class="w-5 h-5 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                        <svg x-show="loadingPdf === guia.id" class="animate-spin h-5 w-5 text-red-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                        Guía de Remisión (Formato SRI)
                    </button>
                    <button @click="generarGuiaRuta(guia.id)" class="flex-1 bg-gray-100 hover:bg-gray-200 text-gray-800 py-2 px-4 rounded text-sm font-bold transition-colors flex items-center justify-center gap-2 border border-gray-300 disabled:opacity-50" :disabled="loadingPdf === guia.id">
                        <svg x-show="loadingPdf !== guia.id" class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17a2 2 0 11-4 0 2 2 0 014 0zM19 17a2 2 0 11-4 0 2 2 0 014 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16V6a1 1 0 00-1-1H4a1 1 0 00-1 1v10a1 1 0 001 1h1m8-1a1 1 0 01-1 1H9m4-1V8a1 1 0 011-1h2.586a1 1 0 01.707.293l3.414 3.414a1 1 0 01.293.707V16a1 1 0 01-1 1h-1m-6-1a1 1 0 001 1h1M5 17a2 2 0 104 0m-4 0a2 2 0 114 0m6 0a2 2 0 104 0m-4 0a2 2 0 114 0"></path></svg>
                        <svg x-show="loadingPdf === guia.id" class="animate-spin h-5 w-5 text-blue-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                        Listado de Ruta Detallado
                    </button>
                </div>
            </div>
        </template>
        <div x-show="guias.length === 0" class="text-center text-gray-500 py-12 bg-white rounded shadow border border-gray-100">
            No tienes rutas asignadas para hoy.
        </div>
    </div>
</div>

<script>
document.addEventListener('alpine:init', () => {
    Alpine.data('guiasActivas', () => ({
        guias: [],
        loadingPdf: null,
        
        async init() {
            try {
                this.guias = await window.api('/api/guias-ruta');
            } catch (error) {
                console.error("Error al cargar guias:", error);
            }
        },

        // Client-Side Rendering: Guía de Remisión (Formato SRI)
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
                    subtotalGlobal += parseFloat(p.subtotal);
                    ivaGlobal += parseFloat(p.iva);
                    if(p.items && p.items.length) {
                        p.items.forEach(item => {
                            bodyTable.push([
                                item.cantidad,
                                `${item.producto} (Pedido #${p.id})`,
                                `$${parseFloat(item.precio_unitario).toFixed(2)}`,
                                `$${parseFloat(item.subtotal).toFixed(2)}`
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

                // Offloading procesamiento: El navegador renderiza el PDF localmente
                pdfMake.createPdf(docDefinition).download(`Guia_Remision_SRI_Ruta_${guiaId}.pdf`);

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
