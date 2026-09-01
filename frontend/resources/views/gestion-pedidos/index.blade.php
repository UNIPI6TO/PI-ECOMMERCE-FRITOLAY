@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto py-8 px-4" x-data="gestionPedidos()">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold">Gestión de Pedidos</h1>
        <div class="flex space-x-2">
            <input type="text" placeholder="Filtro (ej: last 24h)" class="border px-4 py-2 rounded w-64">
        </div>
    </div>

    <!-- KPI Cards -->
    <div class="grid grid-cols-2 md:grid-cols-6 gap-4 mb-8">
        <template x-for="estado in estados" :key="estado.nombre">
            <div @click="filtroEstado = (estado.nombre === 'TODOS' ? null : estado.nombre); currentPage = 1" 
                 class="bg-white p-4 rounded shadow cursor-pointer transition-all border-l-4" 
                 :class="[
                    estado.color, 
                    (filtroEstado === estado.nombre || (filtroEstado === null && estado.nombre === 'TODOS')) 
                        ? 'bg-blue-50 transform scale-105 ring-2 ring-blue-200' 
                        : 'hover:bg-gray-50 opacity-80 hover:opacity-100'
                 ]">
                <div class="text-sm font-medium" :class="(filtroEstado === estado.nombre || (filtroEstado === null && estado.nombre === 'TODOS')) ? 'text-blue-800' : 'text-gray-500'" x-text="estado.nombre"></div>
                <div class="text-2xl font-bold" x-text="estado.count"></div>
            </div>
        </template>
    </div>

    <!-- Mapa -->
    <div class="bg-white p-4 rounded shadow mb-8">
        <h2 class="font-semibold mb-4">Vista Geográfica</h2>
        <div id="mapa-gestion" style="height: 400px;" class="rounded border z-0"></div>
    </div>

    <div class="flex justify-between items-center mb-4">
        <h2 class="font-semibold text-lg">Listado de Pedidos</h2>
        <div class="flex items-center space-x-4">
            <button x-show="hayPedidosParaAutoAprobar()" @click="autoAprobarMasivo" class="bg-green-600 hover:bg-green-700 text-white px-4 py-1.5 rounded-md text-sm font-semibold shadow transition-colors flex items-center">
                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                Auto Aprobar Efectivo/TC/Débito
            </button>
            <div class="flex items-center space-x-2 text-sm border-l pl-4">
                <span class="text-gray-600">Mostrar:</span>
                <select x-model="perPage" @change="currentPage = 1" class="border-gray-300 rounded-md text-sm py-1 pl-2 pr-8 focus:ring-primary focus:border-primary border">
                    <option :value="5">5</option>
                    <option :value="10">10</option>
                    <option :value="20">20</option>
                    <option :value="50">50</option>
                </select>
                <span class="text-gray-600">registros</span>
            </div>
        </div>
    </div>
    <div class="bg-white rounded shadow overflow-x-auto">
        <table class="w-full text-left">
            <thead class="bg-gray-100">
                <tr>
                    <th class="p-3 cursor-pointer hover:bg-gray-200" @click="sort('id')">ID ⇕</th>
                    <th class="p-3 cursor-pointer hover:bg-gray-200" @click="sort('cliente')">Nombre Comercial ⇕</th>
                    <th class="p-3">Método Pago</th>
                    <th class="p-3 cursor-pointer hover:bg-gray-200" @click="sort('distancia')">Distancia ⇕</th>
                    <th class="p-3">Total</th>
                    <th class="p-3">Estado</th>
                    <th class="p-3 cursor-pointer hover:bg-gray-200" @click="sort('raw_fecha')">Tiempo Transcurrido ⇕</th>
                    <th class="p-3">Acciones</th>
                </tr>
            </thead>
            <tbody>
                <template x-if="paginatedPedidos.length === 0">
                    <tr><td colspan="8" class="p-4 text-center text-gray-500">No hay pedidos para mostrar</td></tr>
                </template>
                <template x-for="p in paginatedPedidos" :key="p.id">
                    <tr class="border-b hover:bg-gray-50">
                        <td class="p-3" x-text="p.id"></td>
                        <td class="p-3 font-semibold text-gray-800" x-text="p.cliente"></td>
                        <td class="p-3" x-text="p.pago"></td>
                        <td class="p-3 text-sm font-medium text-blue-600" x-text="(p.distancia && p.distancia !== 999999) ? p.distancia + ' km' : '-'"></td>
                        <td class="p-3 font-medium" x-text="`$${Number(p.total).toFixed(2)}`"></td>
                        <td class="p-3"><span class="px-2 py-1 text-xs font-semibold rounded-full bg-gray-100 text-gray-700" x-text="p.estado"></span></td>
                        <td class="p-3 text-sm text-gray-500" x-text="timeAgo(p.fecha)"></td>
                        <td class="p-3">
                            <button @click="
                                if(p.estado === 'PENDIENTE') verDetalle(p); 
                                else if(p.estado === 'APROBADO') abrirAsignarRuta(p); 
                                else verDetalle(p)
                            " class="text-primary hover:text-red-800 font-medium text-sm transition-colors" x-text="p.estado === 'PENDIENTE' ? 'Revisión' : (p.estado === 'APROBADO' ? 'Asignar Ruta' : 'Ver Detalle')"></button>
                        </td>
                    </tr>
                </template>
            </tbody>
        </table>
        
        <!-- Paginador -->
        <div class="p-4 border-t border-gray-100 flex items-center justify-between bg-gray-50">
            <div class="text-sm text-gray-500">
                Mostrando pág <span class="font-medium text-gray-800" x-text="currentPage"></span> de <span class="font-medium text-gray-800" x-text="totalPages"></span> 
                (<span x-text="filteredPedidos.length"></span> registros totales)
            </div>
            <div class="flex space-x-2">
                <button @click="prevPage" :disabled="currentPage === 1" class="px-3 py-1 border border-gray-300 rounded-md text-sm font-medium text-gray-700 hover:bg-gray-100 transition-colors disabled:opacity-50 disabled:cursor-not-allowed">Anterior</button>
                <button @click="nextPage" :disabled="currentPage === totalPages" class="px-3 py-1 border border-gray-300 rounded-md text-sm font-medium text-gray-700 hover:bg-gray-100 transition-colors disabled:opacity-50 disabled:cursor-not-allowed">Siguiente</button>
            </div>
        </div>
    </div>

    <!-- Modal Revisión -->
    <div x-show="revisarModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50" style="display: none;">
        <div class="bg-white p-6 rounded-lg w-full max-w-2xl max-h-[90vh] overflow-y-auto">
            <div class="flex justify-between items-center mb-4">
                <h3 class="font-bold text-xl">Revisión de Pedido #<span x-text="selectedPedido?.id"></span></h3>
                <button @click="cerrarRevision()" class="text-gray-500 hover:text-gray-700">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                </button>
            </div>
            
            <div class="grid grid-cols-2 gap-4 mb-4 text-sm bg-gray-50 p-4 rounded border">
                <div><strong>Comercio:</strong> <span x-text="selectedPedido?.cliente"></span></div>
                <div><strong>Contacto:</strong> <span x-text="selectedPedido?.nombre_persona"></span></div>
                <div><strong>Método:</strong> <span x-text="selectedPedido?.pago"></span></div>
                <div><strong>Total:</strong> $<span x-text="selectedPedido?.total"></span></div>
            </div>

            <div class="mb-6">
                <h4 class="font-semibold mb-2 text-gray-700">Comprobante / Documento</h4>
                <div class="bg-gray-100 rounded flex items-center justify-center min-h-[200px] relative border border-gray-200">
                    <!-- Loading state -->
                    <div x-show="loadingComprobante" class="absolute inset-0 flex items-center justify-center">
                        <span class="text-gray-500 font-medium">Cargando comprobante...</span>
                    </div>
                    <!-- Error or no comprobante -->
                    <div x-show="!loadingComprobante && !comprobanteUrl" class="text-gray-500 text-center p-4">
                        <svg class="w-12 h-12 mx-auto text-gray-300 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                        No hay comprobante disponible o es pago directo.
                    </div>
                    <!-- Image preview -->
                    <template x-if="!loadingComprobante && comprobanteUrl">
                        <div class="text-center">
                            <template x-if="comprobanteUrl.split('?')[0].toLowerCase().endsWith('.pdf')">
                                <div class="bg-gray-100 p-4 rounded mb-2 border border-gray-200">
                                    <svg class="w-12 h-12 mx-auto text-red-500 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path></svg>
                                    <p class="text-sm text-gray-700 mb-2">El comprobante es un documento PDF.</p>
                                    <a :href="comprobanteUrl" target="_blank" class="inline-block bg-primary text-white px-4 py-2 rounded font-medium hover:bg-red-800 transition-colors">
                                        Abrir Documento PDF
                                    </a>
                                </div>
                            </template>
                            <template x-if="!comprobanteUrl.split('?')[0].toLowerCase().endsWith('.pdf')">
                                <a :href="comprobanteUrl" target="_blank" title="Click para abrir en otra pestaña">
                                    <img :src="comprobanteUrl" class="max-w-full max-h-[350px] object-contain rounded shadow hover:opacity-90 transition-opacity mx-auto" />
                                </a>
                            </template>
                        </div>
                    </template>
                </div>
            </div>

            <!-- Modivo rechazo -->
            <div class="mb-4" x-show="mostrarRechazo" style="display: none;">
                <label class="block text-sm font-medium text-red-700 mb-1">Motivo de Cancelación</label>
                <textarea x-model="motivoRechazo" class="w-full border-red-300 rounded p-2 focus:ring-red-500 focus:border-red-500 border" rows="3" placeholder="Especifique el motivo para el cliente..."></textarea>
            </div>

            <div class="flex justify-end space-x-3 border-t pt-4">
                <button @click="cerrarRevision()" class="px-4 py-2 border border-gray-300 rounded text-gray-700 hover:bg-gray-50 font-medium transition-colors">
                    Mantener Pendiente
                </button>
                
                <template x-if="!mostrarRechazo">
                    <button @click="mostrarRechazo = true" class="px-4 py-2 bg-red-600 text-white rounded hover:bg-red-700 font-medium transition-colors">
                        Cancelar Pedido
                    </button>
                </template>

                <template x-if="mostrarRechazo">
                    <button @click="confirmarRechazo()" class="px-4 py-2 bg-red-700 text-white rounded font-bold hover:bg-red-800 shadow transition-colors">
                        Confirmar Cancelación
                    </button>
                </template>

                <button @click="confirmarAprobacion()" x-show="!mostrarRechazo" class="px-4 py-2 bg-green-600 text-white rounded hover:bg-green-700 font-bold shadow transition-colors">
                    Aprobar Pedido
                </button>
            </div>
        </div>
    </div>

    <!-- Modal Asignar Ruta -->
    <div x-show="asignarModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50" style="display: none;">
        <div class="bg-white rounded-lg w-11/12 md:w-1/3 p-6 shadow-2xl relative">
            <button @click="asignarModal = false" class="absolute top-4 right-4 text-gray-500 hover:text-red-600 transition-colors">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
            </button>
            <h2 class="text-xl font-bold mb-4">Asignar a Ruta / Camión</h2>
            <p class="text-sm text-gray-600 mb-4" x-text="`Pedido #${selectedPedido?.id} - ${selectedPedido?.cliente || ''}`"></p>
            
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-1">Seleccionar Camión Disponible</label>
                <select x-model="selectedCamionId" class="w-full border-gray-300 rounded shadow-sm p-2 focus:border-primary focus:ring focus:ring-primary focus:ring-opacity-50">
                    <option value="">-- Seleccione un camión --</option>
                    <template x-for="camion in camiones" :key="camion.id">
                        <option :value="camion.id" x-text="`${camion.placa} - ${camion.descripcion} (${camion.estado})`"></option>
                    </template>
                </select>
            </div>
            
            <div class="flex justify-end space-x-3">
                <button @click="asignarModal = false" class="px-4 py-2 border border-gray-300 text-gray-600 font-medium rounded hover:bg-gray-100 transition-colors">Cancelar</button>
                <button @click="confirmarAsignacion" :disabled="!selectedCamionId" class="px-4 py-2 bg-primary text-white font-medium rounded hover:bg-red-800 transition-colors disabled:opacity-50">Asignar y Enviar</button>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('alpine:init', () => {
    Alpine.data('gestionPedidos', () => ({
        filtroEstado: null,
        estados: [
            {nombre: 'TODOS', count: 0, color: 'border-gray-500'},
            {nombre: 'CANCELADO', count: 0, color: 'border-red-500'},
            {nombre: 'PENDIENTE', count: 0, color: 'border-yellow-500'},
            {nombre: 'APROBADO', count: 0, color: 'border-blue-500'},
            {nombre: 'EN_RUTA', count: 0, color: 'border-indigo-500'},
            {nombre: 'ENTREGADO', count: 0, color: 'border-green-500'}
        ],
        pedidos: [],
        camiones: [],
        map: null,
        markersLayer: null,
        currentLat: null,
        currentLng: null,
        
        // Paginación
        currentPage: 1,
        perPage: 10,
        sortCol: 'raw_fecha',
        sortDesc: true,

        // Variables del Modal de Revisión
        revisarModal: false,
        selectedPedido: null,
        comprobanteUrl: null,
        loadingComprobante: false,
        mostrarRechazo: false,
        motivoRechazo: '',

        // Variables del Modal de Asignar Ruta
        asignarModal: false,
        selectedCamionId: '',
        
        async init() {
            try {
                // Load Pedidos and Camiones in parallel
                const [pedidosRes, camionesRes] = await Promise.all([
                    window.api('/api/pedidos'),
                    window.api('/api/camiones')
                ]);
                
                this.pedidos = pedidosRes;
                this.camiones = camionesRes;
                if(this.camiones.data) this.camiones = this.camiones.data;
                
                this.updateCounts();
            } catch (error) {
                console.error("Error al cargar pedidos:", error);
            }

            if (navigator.geolocation) {
                navigator.geolocation.getCurrentPosition((pos) => {
                    this.currentLat = pos.coords.latitude;
                    this.currentLng = pos.coords.longitude;
                    
                    // Inyectar la distancia en el array para poder ordenar la tabla
                    this.pedidos.forEach(p => {
                        if (p.lat && p.lng) {
                            p.distancia = parseFloat(this.getDist(this.currentLat, this.currentLng, p.lat, p.lng));
                        } else {
                            p.distancia = 999999; // Fallback para que salgan al fondo al ordenar
                        }
                    });

                    this.renderMarkers();
                });
            }
            
            if (!this.map) {
                setTimeout(() => {
                    this.map = L.map('mapa-gestion').setView([-1.249, -78.616], 13);
                    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png').addTo(this.map);
                    this.markersLayer = L.layerGroup().addTo(this.map);
                    this.renderMarkers();
                }, 100);
            } else {
                this.renderMarkers();
            }

            this.$watch('filtroEstado', () => {
                this.currentPage = 1;
                this.renderMarkers();
            });
        },
        
        renderMarkers() {
            if(!this.markersLayer) return;
            this.markersLayer.clearLayers();
            
            // Agrupar pedidos con las mismas coordenadas
            const grouped = {};
            this.filteredPedidos.forEach(p => {
                if (p.lat && p.lng) {
                    const key = `${p.lat},${p.lng}`;
                    if (!grouped[key]) grouped[key] = [];
                    grouped[key].push(p);
                }
            });

            for (const key in grouped) {
                const pedidos = grouped[key];
                const [lat, lng] = key.split(',');
                
                let distanceHtml = '';
                if (this.currentLat && this.currentLng) {
                    const dist = this.getDist(this.currentLat, this.currentLng, lat, lng);
                    distanceHtml = `<div class="text-xs text-blue-600 font-bold mb-2">📍 a ${dist} km de tu ubicación actual</div>`;
                }

                let ordersHtml = `<div style="max-height: 150px; overflow-y: auto; padding-right: 5px;">`;
                pedidos.forEach(p => {
                    ordersHtml += `
                        <div style="border-bottom: 1px solid #eee; padding-bottom: 8px; margin-bottom: 8px;">
                            <b>Pedido #${p.id}</b> <span style="font-size: 11px; color: #666;">(hace ${this.timeAgo(p.fecha)})</span><br>
                            <b>Comercio:</b> ${p.cliente}<br>
                            <b>Contacto:</b> ${p.nombre_persona}<br>
                            <b>Estado:</b> ${p.estado}<br>
                            <b>Total:</b> $${Number(p.total).toFixed(2)}
                        </div>
                    `;
                });
                ordersHtml += `</div>`;

                const marker = L.marker([lat, lng]).bindPopup(`
                    <div style="min-w: 220px;">
                        <h4 style="font-weight: bold; margin-bottom: 5px; font-size: 14px;">${pedidos.length} pedido(s) aquí</h4>
                        ${distanceHtml}
                        ${ordersHtml}
                    </div>
                `);
                this.markersLayer.addLayer(marker);
            }
        },

        getDist(lat1, lon1, lat2, lon2) {
            const R = 6371; // radio de la Tierra en km
            const dLat = (lat2 - lat1) * Math.PI / 180;
            const dLon = (lon2 - lon1) * Math.PI / 180;
            const a = Math.sin(dLat/2) * Math.sin(dLat/2) +
                      Math.cos(lat1 * Math.PI / 180) * Math.cos(lat2 * Math.PI / 180) *
                      Math.sin(dLon/2) * Math.sin(dLon/2);
            const c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1-a));
            return (R * c).toFixed(2);
        },

        timeAgo(dateStr) {
            if (!dateStr) return 'Desconocido';
            const date = new Date(dateStr.replace(/-/g, '/'));
            const now = new Date();
            const diffSecs = Math.floor((now - date) / 1000);
            
            if (diffSecs < 60) return `${diffSecs} segs`;
            const mins = Math.floor(diffSecs / 60);
            if (mins < 60) return `${mins} mins`;
            const hours = Math.floor(mins / 60);
            if (hours < 24) return `${hours} horas`;
            const days = Math.floor(hours / 24);
            return `${days} días`;
        },

        updateCounts() {
            this.estados.forEach(e => {
                if (e.nombre === 'TODOS') {
                    e.count = this.pedidos.filter(p => p.estado !== 'CANCELADO').length;
                } else {
                    e.count = this.pedidos.filter(p => p.estado === e.nombre).length;
                }
            });
        },

        async verDetalle(p) {
            if (p.estado === 'PENDIENTE') {
                this.selectedPedido = p;
                this.revisarModal = true;
                this.mostrarRechazo = false;
                this.motivoRechazo = '';
                
                // Only fetch comprobante if it's DE_UNA or DEPOSITO
                if (p.pago === 'DE_UNA' || p.pago === 'DEPOSITO') {
                    this.loadingComprobante = true;
                    this.comprobanteUrl = null;
                    try {
                        const res = await window.api(`/api/pedidos/${p.id}/comprobante`);
                        this.comprobanteUrl = res.data.url;
                    } catch (e) {
                        console.error("No se pudo cargar el comprobante", e);
                    }
                    this.loadingComprobante = false;
                } else {
                    this.comprobanteUrl = null;
                    this.loadingComprobante = false;
                }
            } else {
                Swal.fire('Detalle', `Pedido #${p.id} pagado con ${p.pago}. Estado actual: ${p.estado}`, 'info');
            }
        },

        cerrarRevision() {
            this.revisarModal = false;
            this.selectedPedido = null;
            this.comprobanteUrl = null;
        },

        abrirAsignarRuta(p) {
            this.selectedPedido = p;
            this.selectedCamionId = '';
            this.asignarModal = true;
        },
        
        async confirmarAsignacion() {
            if(!this.selectedCamionId) return;
            try {
                await window.api('/api/asignaciones', {
                    method: 'POST',
                    body: JSON.stringify({
                        pedido_ids: [this.selectedPedido.id],
                        camion_id: this.selectedCamionId
                    })
                });
                
                Swal.fire({
                    toast: true,
                    position: 'top-end',
                    icon: 'success',
                    title: 'Ruta asignada con éxito',
                    showConfirmButton: false,
                    timer: 3000
                });
                
                this.asignarModal = false;
                await this.init();
            } catch(e) {
                Swal.fire('Error', e.message || 'Error al asignar la ruta', 'error');
            }
        },

        async confirmarAprobacion() {
            try {
                await window.api(`/api/pedidos/${this.selectedPedido.id}/aprobar`, { method: 'PATCH' });
                await Swal.fire({ icon: 'success', title: 'Éxito', text: 'Pedido aprobado', toast: true, position: 'bottom', showConfirmButton: false, timer: 2000 });
                this.revisarModal = false;
                await this.init();
            } catch (e) {
                Swal.fire('Error', e.message || 'Error al aprobar', 'error');
            }
        },

        async confirmarRechazo() {
            if (!this.motivoRechazo.trim()) {
                Swal.fire('Atención', 'Debe especificar un motivo de cancelación', 'warning');
                return;
            }
            try {
                await window.api(`/api/pedidos/${this.selectedPedido.id}/rechazar`, {
                    method: 'PATCH',
                    body: JSON.stringify({ motivo: this.motivoRechazo })
                });
                await Swal.fire({ icon: 'success', title: 'Éxito', text: 'Pedido cancelado', toast: true, position: 'bottom', showConfirmButton: false, timer: 2000 });
                this.revisarModal = false;
                this.mostrarRechazo = false;
                await this.init();
            } catch (e) {
                Swal.fire('Error', e.message || 'Error al cancelar', 'error');
            }
        },

        async autoAprobarMasivo() {
            const result = await Swal.fire({
                title: '¿Auto Aprobar pagos directos?',
                text: 'Se aprobarán automáticamente todos los pedidos en estado PENDIENTE que hayan sido pagados con Efectivo, Tarjeta de Crédito o Débito.',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#16a34a',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Sí, aprobar masivamente',
                cancelButtonText: 'Cancelar'
            });
            
            if (result.isConfirmed) {
                try {
                    const res = await window.api('/api/pedidos/bulk-aprobar-directos', { method: 'POST' });
                    await Swal.fire({ icon: 'success', title: '¡Aprobados!', text: res.message, toast: true, position: 'bottom', showConfirmButton: false, timer: 2000 });
                    
                    await this.init();
                } catch (e) {
                    Swal.fire('Error', e.message || 'No se pudo aprobar masivamente', 'error');
                }
            }
        },

        hayPedidosParaAsignar() {
            return this.pedidos.some(p => p.estado === 'en_espera_asignacion');
        },
        hayPedidosParaAutoAprobar() {
            const pagosValidos = ['efectivo', 'tc', 'td', 'tarjeta', 'debito', 'de_una'];
            // Convert everything to uppercase strings or safely check lowercase
            return this.pedidos.some(p => {
                if (p.estado !== 'PENDIENTE' || !p.pago) return false;
                const metodo = p.pago.toLowerCase();
                return pagosValidos.some(pv => metodo.includes(pv));
            });
        },

        get filteredPedidos() {
            let filtered = this.pedidos;
            if(this.filtroEstado) {
                filtered = filtered.filter(p => p.estado === this.filtroEstado);
            } else {
                filtered = filtered.filter(p => p.estado !== 'CANCELADO');
            }
            
            return filtered.sort((a, b) => {
                let mod = this.sortAsc ? 1 : -1;
                return a[this.sortCol] > b[this.sortCol] ? mod : -mod;
            });
        },
        
        get totalPages() {
            return Math.ceil(this.filteredPedidos.length / this.perPage) || 1;
        },
        
        get paginatedPedidos() {
            const start = (this.currentPage - 1) * this.perPage;
            return this.filteredPedidos.slice(start, start + this.perPage);
        },

        sort(col) {
            if(this.sortCol === col) this.sortAsc = !this.sortAsc;
            else { this.sortCol = col; this.sortAsc = true; }
        },
        
        nextPage() {
            if (this.currentPage < this.totalPages) this.currentPage++;
        },
        
        prevPage() {
            if (this.currentPage > 1) this.currentPage--;
        }
    }));
});
</script>
@endsection
