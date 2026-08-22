@extends('layouts.app')

@section('content')
<div x-data="checkout()" class="max-w-7xl mx-auto py-8 px-4 sm:px-6 lg:px-8">
    <h1 class="text-3xl font-bold mb-8">Checkout</h1>
    
    <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
        <div class="md:col-span-2 space-y-8">
            <!-- Carrito -->
            <section class="bg-white p-6 rounded-lg shadow">
                <h2 class="text-xl font-semibold mb-4">Resumen del Carrito</h2>
                <table class="w-full text-left">
                    <thead>
                        <tr class="border-b text-sm text-gray-600">
                            <th class="pb-2">Producto</th>
                            <th class="pb-2 text-center">Cant.</th>
                            <th class="pb-2 text-right">Precio</th>
                            <th class="pb-2 text-right">Subtotal</th>
                        </tr>
                    </thead>
                    <tbody>
                        <template x-for="item in items" :key="item.id">
                            <tr class="border-b">
                                <td class="py-4" x-text="item.nombre"></td>
                                <td class="py-4 text-center" x-text="item.cantidad"></td>
                                <td class="py-4 text-right" x-text="`$${item.precioUnitario.toFixed(2)}`"></td>
                                <td class="py-4 text-right" x-text="`$${(item.precioUnitario * item.cantidad).toFixed(2)}`"></td>
                            </tr>
                        </template>
                    </tbody>
                </table>
            </section>

            <!-- Direcciones -->
            <section class="bg-white p-6 rounded-lg shadow">
                <div class="flex justify-between items-center mb-4">
                    <h2 class="text-xl font-semibold">Dirección de Envío</h2>
                    <button @click="showAddressModal = true; $dispatch('load-address', null)" class="text-[#E3001B] font-medium">+ Nueva Dirección</button>
                </div>
                <div class="space-y-3">
                    <template x-for="addr in direcciones" :key="addr.id">
                        <label class="flex items-center p-3 border rounded cursor-pointer hover:bg-gray-50">
                            <input type="radio" x-model="selectedDireccion" :value="addr.id" class="text-[#E3001B] focus:ring-[#E3001B]">
                            <span class="ml-3" x-text="addr.descripcion + (addr.referencia ? ' - Ref: ' + addr.referencia : '')"></span>
                        </label>
                    </template>
                </div>
            </section>

            <!-- Pago -->
            <section class="bg-white p-6 rounded-lg shadow">
                <h2 class="text-xl font-semibold mb-4">Método de Pago</h2>
                <div class="grid grid-cols-2 gap-3 mb-4">
                    <template x-for="metodo in metodosPago" :key="metodo.id">
                        <label class="flex items-center p-3 border rounded cursor-pointer hover:bg-gray-50">
                            <input type="radio" x-model="selectedPago" :value="metodo.id" class="text-[#E3001B] focus:ring-[#E3001B]">
                            <span class="ml-3" x-text="metodo.nombre"></span>
                        </label>
                    </template>
                </div>
                <div x-show="selectedPago === 'DEPOSITO' || selectedPago === 'DE_UNA'" class="mt-4 p-4 border rounded bg-gray-50">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Subir Comprobante</label>
                    <input type="file" @change="handleFile" class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded file:border-0 file:text-sm file:font-semibold file:bg-[#E3001B] file:text-white hover:file:bg-red-700">
                </div>
            </section>
        </div>

        <!-- Resumen Financiero -->
        <div class="bg-white p-6 rounded-lg shadow h-fit">
            <h2 class="text-xl font-semibold mb-4">Total</h2>
            <div class="space-y-3 text-sm">
                <div class="flex justify-between">
                    <span>Subtotal</span>
                    <span x-text="`$${subtotal.toFixed(2)}`"></span>
                </div>
                <div class="flex justify-between text-green-600">
                    <span>Descuento</span>
                    <span x-text="`-$${descuento.toFixed(2)}`"></span>
                </div>
                <div class="flex justify-between">
                    <span>IVA (15%)</span>
                    <span x-text="`$${iva.toFixed(2)}`"></span>
                </div>
                <hr>
                <div class="flex justify-between font-bold text-lg">
                    <span>Total</span>
                    <span x-text="`$${total.toFixed(2)}`"></span>
                </div>
                <div x-show="descuento > 0" class="text-xs text-center text-green-600 mt-2">
                    ¡Ahorraste $<span x-text="descuento.toFixed(2)"></span>!
                </div>
            </div>
            <button 
                @click="finalizarCompra" 
                x-bind:disabled="!selectedDireccion || items.length === 0 || ((selectedPago === 'DEPOSITO' || selectedPago === 'DE_UNA') && !comprobante)"
                class="w-full mt-6 bg-[#F5C518] hover:bg-yellow-500 text-black font-bold py-3 px-4 rounded disabled:opacity-50 disabled:cursor-not-allowed">
                Finalizar Compra
            </button>
        </div>
    </div>

    <!-- Modal Dirección -->
    <div x-show="showAddressModal" @update-dir-data.window="newAddressData = $event.detail" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
        <div class="bg-white p-6 rounded-lg w-full max-w-2xl">
            <h3 class="text-lg font-bold mb-4">Agregar Dirección</h3>
            @include('ecommerce.mapa-direccion')
            <div class="mt-4 flex justify-end space-x-3">
                <button @click="showAddressModal = false" class="px-4 py-2 border rounded text-gray-600 hover:bg-gray-50">Cancelar</button>
                <button @click="guardarDireccion" class="px-4 py-2 bg-[#E3001B] text-white rounded hover:bg-red-700">Guardar</button>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('alpine:init', () => {
    Alpine.data('checkout', () => ({
        items: [],
        direcciones: [],
        metodosPago: [
            {id: 'EFECTIVO', nombre: 'Efectivo'},
            {id: 'DEPOSITO', nombre: 'Depósito/Transferencia'},
            {id: 'DE_UNA', nombre: 'De Una'},
            {id: 'TARJETA', nombre: 'Tarjeta Crédito/Débito'}
        ],
        selectedDireccion: null,
        selectedPago: 'EFECTIVO',
        comprobante: null,
        newAddressData: null,
        showAddressModal: false,

        async init() {
            if(window.CarritoManager) {
                this.items = window.CarritoManager.getItems();
            }
            try {
                this.clienteData = await window.api('/api/clientes/me');
                if (this.clienteData && this.clienteData.data) {
                    this.clienteData = this.clienteData.data;
                    await this.loadDirecciones();
                }
            } catch(e) {
                console.error(e);
            }
        },

        async loadDirecciones() {
            this.direcciones = await window.api(`/api/clientes/${this.clienteData.id}/direcciones`);
            if(this.direcciones.length > 0) {
                this.selectedDireccion = this.direcciones[0].id;
            }
        },

        get subtotal() { return this.items.reduce((acc, item) => acc + (item.precioUnitario * item.cantidad), 0); },
        get descuento() { return 0; }, // Logica de descuento
        get iva() { return (this.subtotal - this.descuento) * 0.15; },
        get total() { return this.subtotal - this.descuento + this.iva; },

        handleFile(e) {
            this.comprobante = e.target.files[0];
        },

        async finalizarCompra() {
            try {
                // Map items to match backend requirements
                const itemsForBackend = this.items.map(i => ({
                    producto_id: i.productoId,
                    cantidad: i.cantidad
                }));

                const metodoPagoLower = this.selectedPago.toLowerCase();
                // Map TARJETA to tc
                const metodoPagoFinal = metodoPagoLower === 'tarjeta' ? 'tc' : metodoPagoLower;

                const formData = new FormData();
                // append items as json string or array
                itemsForBackend.forEach((item, index) => {
                    formData.append(`items[${index}][producto_id]`, item.producto_id);
                    formData.append(`items[${index}][cantidad]`, item.cantidad);
                });
                formData.append('direccion_id', this.selectedDireccion);
                formData.append('metodo_pago', metodoPagoFinal);
                formData.append('total', this.total);
                if (this.comprobante) {
                    formData.append('comprobante', this.comprobante);
                }

                const data = await window.api('/api/pedidos', {
                    method: 'POST',
                    body: formData
                });
                if (window.pdfGenerator) window.pdfGenerator.generateFactura();
                Swal.fire({ icon: 'success', title: '¡Pedido realizado!', text: 'Tu pedido fue registrado exitosamente.', confirmButtonColor: '#E3001B' })
                    .then(() => window.location.href = '/ecommerce/confirmacion');
            } catch (error) {
                Swal.fire({ icon: 'error', title: 'Error al procesar pedido', text: error.message, confirmButtonColor: '#E3001B' });
            }
        },
        
        async guardarDireccion() {
            if (!this.newAddressData || !this.newAddressData.descripcion) {
                return Swal.fire('Error', 'Selecciona una dirección válida', 'error');
            }
            try {
                await window.api(`/api/clientes/${this.clienteData.id}/direcciones`, {
                    method: 'POST',
                    body: JSON.stringify({
                        descripcion: this.newAddressData.descripcion,
                        referencia: this.newAddressData.referencia,
                        latitud: this.newAddressData.lat,
                        longitud: this.newAddressData.lng,
                        es_por_defecto: false
                    })
                });
                await this.loadDirecciones();
                this.showAddressModal = false;
                Swal.fire({ icon: 'success', title: 'Dirección guardada', toast: true, position: 'top-end', showConfirmButton: false, timer: 3000 });
            } catch (e) {
                Swal.fire('Error', 'No se pudo guardar la dirección', 'error');
            }
        }
    }));
});
</script>
@endsection
