@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto py-8 px-4" x-data="aprobacion()">
    <h1 class="text-2xl font-bold mb-6">Aprobación de Pagos</h1>
    
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Lista de Pedidos -->
        <div class="lg:col-span-2 bg-white rounded shadow overflow-hidden">
            <table class="w-full text-left">
                <thead class="bg-gray-100">
                    <tr>
                        <th class="p-4">Pedido</th>
                        <th class="p-4">Cliente</th>
                        <th class="p-4">Método</th>
                        <th class="p-4">Total</th>
                        <th class="p-4">Acción</th>
                    </tr>
                </thead>
                <tbody>
                    <template x-for="p in pedidos" :key="p.id">
                        <tr class="border-b hover:bg-gray-50 cursor-pointer" @click="selected = p" :class="{'bg-blue-50': selected?.id === p.id}">
                            <td class="p-4 font-medium" x-text="p.id"></td>
                            <td class="p-4" x-text="p.cliente"></td>
                            <td class="p-4" x-text="p.metodo"></td>
                            <td class="p-4" x-text="`$${p.total}`"></td>
                            <td class="p-4">
                                <button class="text-blue-600 text-sm">Revisar</button>
                            </td>
                        </tr>
                    </template>
                </tbody>
            </table>
        </div>

        <!-- Panel Lateral Comprobante -->
        <div class="bg-white p-6 rounded shadow" x-show="selected">
            <h3 class="font-bold text-lg mb-4">Revisión: <span x-text="selected?.id"></span></h3>
            <div class="bg-gray-100 h-64 rounded mb-4 flex items-center justify-center text-gray-500">
                [Imagen/PDF del Comprobante]
            </div>
            <div class="flex space-x-3">
                <button @click="aprobar(selected.id)" class="flex-1 bg-green-600 text-white py-2 rounded font-semibold hover:bg-green-700">Aprobar</button>
                <button @click="rechazarModal = true" class="flex-1 bg-red-600 text-white py-2 rounded font-semibold hover:bg-red-700">Rechazar</button>
            </div>
        </div>
    </div>

    <!-- Modal Rechazo -->
    <div x-show="rechazarModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
        <div class="bg-white p-6 rounded-lg w-96">
            <h3 class="font-bold text-lg mb-4">Motivo de Rechazo</h3>
            <textarea x-model="motivo" class="w-full border rounded p-2 mb-4 h-24" placeholder="Escriba el motivo..."></textarea>
            <div class="flex justify-end space-x-2">
                <button @click="rechazarModal = false" class="px-4 py-2 border rounded">Cancelar</button>
                <button @click="rechazar(selected.id)" class="px-4 py-2 bg-red-600 text-white rounded">Confirmar Rechazo</button>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('alpine:init', () => {
    Alpine.data('aprobacion', () => ({
        pedidos: [],
        selected: null,
        rechazarModal: false,
        motivo: '',

        async init() {
            await this.fetchPedidos();
        },

        async fetchPedidos() {
            try {
                this.pedidos = await window.api('/api/pedidos/pendientes-aprobacion');
            } catch (e) {
                console.error(e);
            }
        },

        async aprobar(id) {
            try {
                await window.api(`/api/pedidos/${id}/aprobar`, { method: 'PATCH' });
                this.pedidos = this.pedidos.filter(p => p.id !== id);
                this.selected = null;
                Swal.fire({ icon: 'success', title: 'Éxito', text: 'Pedido aprobado', toast: true, position: 'bottom', showConfirmButton: false, timer: 3000 });
            } catch (e) {
                Swal.fire('Error', e.message, 'error');
            }
        },

        async rechazar(id) {
            try {
                await window.api(`/api/pedidos/${id}/rechazar`, {
                    method: 'PATCH',
                    body: JSON.stringify({ motivo: this.motivo })
                });
                this.pedidos = this.pedidos.filter(p => p.id !== id);
                this.rechazarModal = false;
                this.selected = null;
                this.motivo = '';
                Swal.fire({ icon: 'success', title: 'Éxito', text: 'Pedido rechazado', toast: true, position: 'bottom', showConfirmButton: false, timer: 3000 });
            } catch (e) {
                Swal.fire('Error', e.message, 'error');
            }
        }
    }));
});
</script>
@endsection
