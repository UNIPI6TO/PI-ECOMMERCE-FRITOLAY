@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto py-8 px-4" x-data="historial()">
    <h1 class="text-3xl font-bold mb-8">Historial de Pedidos</h1>
    
    <div class="mb-6 flex gap-4 items-center">
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Desde</label>
            <input type="date" x-model="fechaInicio" class="border rounded px-4 py-2 w-40">
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Hasta</label>
            <input type="date" x-model="fechaFin" class="border rounded px-4 py-2 w-40">
        </div>
        <div class="pt-6">
            <button @click="filtrar" class="bg-gray-800 text-white px-4 py-2 rounded font-semibold hover:bg-gray-700">Aplicar Filtros</button>
            <button @click="limpiarFiltros" class="bg-gray-300 text-black px-4 py-2 rounded font-semibold hover:bg-gray-400 ml-2">Limpiar</button>
        </div>
    </div>

    <div class="bg-white rounded-lg shadow overflow-hidden">
        <table class="w-full text-left">
            <thead class="bg-gray-50 border-b">
                <tr>
                    <th class="p-4"># Pedido</th>
                    <th class="p-4">Fecha</th>
                    <th class="p-4">Estado</th>
                    <th class="p-4">Total</th>
                    <th class="p-4 text-center">Acciones</th>
                </tr>
            </thead>
            <tbody>
                <template x-for="pedido in pedidos" :key="pedido.id">
                    <tr class="border-b hover:bg-gray-50">
                        <td class="p-4 font-medium" x-text="pedido.id"></td>
                        <td class="p-4" x-text="new Date(pedido.creado_en || pedido.created_at).toLocaleDateString()"></td>
                        <td class="p-4">
                            <span class="px-2 py-1 rounded text-xs font-semibold uppercase" 
                                :class="{
                                    'bg-yellow-100 text-yellow-800': pedido.estado.includes('espera'),
                                    'bg-green-100 text-green-800': pedido.estado.includes('entregado'),
                                    'bg-blue-100 text-blue-800': pedido.estado === 'en_ruta' || pedido.estado === 'listo_para_entregar',
                                    'bg-red-100 text-red-800': pedido.estado === 'cancelado' || pedido.estado === 'no_entregado'
                                }"
                                x-text="pedido.estado.replace(/_/g, ' ')"></span>
                        </td>
                        <td class="p-4" x-text="`$${Number(pedido.total).toFixed(2)}`"></td>
                        <td class="p-4 text-center space-x-2">
                            <button @click="verDetalle(pedido)" class="text-sm bg-gray-200 text-gray-800 px-3 py-1 rounded font-medium hover:bg-gray-300">Detalles</button>
                            <button @click="verPdf(pedido)" class="text-sm bg-[#E3001B] text-white px-3 py-1 rounded font-medium hover:bg-red-700">Factura PDF</button>
                            <a :href="`/ecommerce/rastreo/${pedido.id}`" x-show="pedido.estado === 'en_ruta'" class="text-sm bg-[#F5C518] text-black px-3 py-1 rounded font-medium hover:bg-yellow-500">Rastrear</a>
                            <button @click="cancelarPedido(pedido)" x-show="!['en_ruta', 'listo_para_entregar', 'entregado', 'entregado_parcialmente', 'cancelado'].includes(pedido.estado)" class="text-sm bg-gray-800 text-white px-3 py-1 rounded font-medium hover:bg-black">Cancelar</button>
                        </td>
                    </tr>
                </template>
            </tbody>
        </table>
    </div>

    <!-- Modal Detalles -->
    <div x-show="pedidoSeleccionado" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50" style="display: none;">
        <div class="bg-white rounded-lg w-full max-w-2xl p-6 relative max-h-[90vh] overflow-y-auto" @click.away="pedidoSeleccionado = null">
            <button @click="pedidoSeleccionado = null" class="absolute top-4 right-4 text-gray-500 hover:text-black">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
            <h2 class="text-2xl font-bold mb-4">Detalles del Pedido #<span x-text="pedidoSeleccionado?.id"></span></h2>
            
            <div class="mb-4 text-sm text-gray-700 grid grid-cols-2 gap-4">
                <div><span class="font-bold">Fecha:</span> <span x-text="pedidoSeleccionado ? new Date(pedidoSeleccionado.creado_en || pedidoSeleccionado.created_at).toLocaleString() : ''"></span></div>
                <div><span class="font-bold">Estado:</span> <span class="uppercase" x-text="pedidoSeleccionado?.estado.replace(/_/g, ' ')"></span></div>
                <div><span class="font-bold">Método de Pago:</span> <span class="uppercase" x-text="pedidoSeleccionado?.metodo_pago.replace(/_/g, ' ')"></span></div>
                <div><span class="font-bold">Subtotal:</span> $<span x-text="Number(pedidoSeleccionado?.subtotal).toFixed(2)"></span></div>
                <div><span class="font-bold">IVA:</span> $<span x-text="Number(pedidoSeleccionado?.iva).toFixed(2)"></span></div>
                <div><span class="font-bold">Descuento:</span> $<span x-text="Number(pedidoSeleccionado?.descuento).toFixed(2)"></span></div>
                <div class="col-span-2 text-lg font-bold text-[#E3001B]">Total: $<span x-text="Number(pedidoSeleccionado?.total).toFixed(2)"></span></div>
            </div>

            <h3 class="font-bold text-lg mb-2 border-b pb-2">Productos</h3>
            <ul class="space-y-3 mb-6">
                <template x-for="item in pedidoSeleccionado?.items" :key="item.id">
                    <li class="flex justify-between border-b pb-2">
                        <div>
                            <span class="font-semibold text-gray-800" x-text="item.producto ? item.producto.nombre : 'Producto ' + item.producto_id"></span>
                            <div class="text-sm text-gray-500">Cantidad Solicitada: <span x-text="item.cantidad_solicitada"></span></div>
                        </div>
                        <div class="text-right">
                            <div class="font-bold">$<span x-text="Number(item.precio_unitario * item.cantidad_solicitada).toFixed(2)"></span></div>
                            <div class="text-xs text-gray-400">($<span x-text="Number(item.precio_unitario).toFixed(2)"></span> c/u)</div>
                        </div>
                    </li>
                </template>
            </ul>
            
            <div class="flex justify-end">
                <button @click="pedidoSeleccionado = null" class="bg-gray-200 px-4 py-2 rounded font-semibold">Cerrar</button>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('alpine:init', () => {
    Alpine.data('historial', () => ({
        fechaInicio: '',
        fechaFin: '',
        pedidos: [],
        pedidosOriginales: [],
        pedidoSeleccionado: null,
        async init() {
            try {
                // Get current client info to get clienteId
                let clienteData = await window.api('/api/clientes/me');
                if (clienteData && clienteData.data) clienteData = clienteData.data;
                if (clienteData && clienteData.id) {
                    const response = await window.api(`/api/clientes/${clienteData.id}/pedidos`);
                    this.pedidosOriginales = response.data || response || [];
                    this.pedidos = [...this.pedidosOriginales];
                }
            } catch (error) {
                console.error("Error al cargar historial:", error);
            }
        },
        filtrar() {
            if (!this.fechaInicio && !this.fechaFin) {
                this.pedidos = [...this.pedidosOriginales];
                return;
            }
            
            this.pedidos = this.pedidosOriginales.filter(pedido => {
                // The date format from backend might be 'YYYY-MM-DD HH:MM:SS'
                const pedidoFecha = new Date(pedido.creado_en || pedido.fecha || pedido.created_at);
                let valido = true;

                if (this.fechaInicio) {
                    const inicio = new Date(this.fechaInicio + 'T00:00:00');
                    if (pedidoFecha < inicio) valido = false;
                }
                
                if (this.fechaFin) {
                    const fin = new Date(this.fechaFin + 'T23:59:59');
                    if (pedidoFecha > fin) valido = false;
                }

                return valido;
            });
        },
        limpiarFiltros() {
            this.fechaInicio = '';
            this.fechaFin = '';
            this.pedidos = [...this.pedidosOriginales];
        },
        verDetalle(pedido) {
            this.pedidoSeleccionado = pedido;
        },
        async cancelarPedido(pedido) {
            const confirmacion = await Swal.fire({
                title: '¿Cancelar Pedido?',
                text: "Esta acción no se puede deshacer.",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#E3001B',
                cancelButtonColor: '#9CA3AF',
                confirmButtonText: 'Sí, cancelar'
            });

            if (confirmacion.isConfirmed) {
                try {
                    await window.api(`/api/pedidos/${pedido.id}/cancelar`, { method: 'PATCH' });
                    Swal.fire('Cancelado', 'El pedido ha sido cancelado.', 'success');
                    this.init(); // Recargar historial
                } catch (e) {
                    Swal.fire('Error', e.message, 'error');
                }
            }
        },
        verPdf(pedido) {
            if(!window.generateFactura) {
                console.error("Generador de PDF no cargado");
                return;
            }
            const facturaData = {
                numero: pedido.factura ? pedido.factura.numero_factura : pedido.id,
                clienteNombre: pedido.cliente ? pedido.cliente.nombre_cliente : 'Consumidor Final',
                clienteRuc: pedido.cliente ? pedido.cliente.ruc_cedula : '9999999999999',
                clienteDireccion: pedido.direccion ? pedido.direccion.descripcion : 'S/N',
                clienteTelefono: pedido.cliente ? pedido.cliente.telefono : '',
                metodoPago: pedido.metodo_pago,
                fecha: new Date(pedido.creado_en || pedido.created_at).toLocaleDateString('es-EC'),
                subtotal: Number(pedido.subtotal).toFixed(2),
                descuento: Number(pedido.descuento).toFixed(2),
                iva: Number(pedido.iva).toFixed(2),
                total: Number(pedido.total).toFixed(2),
                items: pedido.items.map(item => ({
                    nombre: item.producto ? item.producto.nombre : 'Producto ' + item.producto_id,
                    cantidad: item.cantidad_solicitada,
                    precioUnitario: item.precio_unitario
                }))
            };
            window.generateFactura(facturaData);
        }
    }));
});
</script>
@endsection
