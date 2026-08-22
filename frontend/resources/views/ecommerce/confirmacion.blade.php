@extends('layouts.app')

@section('content')
<div class="max-w-2xl mx-auto py-16 px-4 text-center" x-data="confirmacion()">
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
        <p class="text-md text-gray-500 mb-8">Número de pedido: <span class="font-bold text-black" x-text="numeroPedido ? `#PED-${numeroPedido}` : 'Cargando...'"></span></p>
        
        <div class="flex justify-center space-x-4">
            <a href="/ecommerce/historial" class="px-6 py-3 bg-[#E3001B] text-white font-bold rounded hover:bg-red-700">
                Ver mis pedidos
            </a>
            <button @click="descargarUltimaFactura" x-show="ultimoPedido" class="px-6 py-3 border border-gray-300 text-gray-700 font-bold rounded hover:bg-gray-50">
                Descargar Factura
            </button>
        </div>
    </div>
</div>

<script>
document.addEventListener('alpine:init', () => {
    Alpine.data('confirmacion', () => ({
        numeroPedido: null,
        ultimoPedido: null,
        async init() {
            try {
                let clienteData = await window.api('/api/clientes/me');
                if (clienteData && clienteData.data) clienteData = clienteData.data;
                if (clienteData && clienteData.id) {
                    const response = await window.api(`/api/clientes/${clienteData.id}/pedidos`);
                    const pedidos = response.data || response || [];
                    if (pedidos.length > 0) {
                        this.ultimoPedido = pedidos[0];
                        this.numeroPedido = this.ultimoPedido.id;
                    }
                }
            } catch (error) {
                console.error("Error cargando último pedido:", error);
            }
        },
        descargarUltimaFactura() {
            if (!this.ultimoPedido) return;
            const pedido = this.ultimoPedido;
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
                    nombre: item.producto ? item.producto.nombre : 'Producto',
                    cantidad: item.cantidad_solicitada,
                    precioUnitario: item.precio_unitario
                }))
            };
            if(window.generateFactura) window.generateFactura(facturaData);
        }
    }));
});
</script>
@endsection
