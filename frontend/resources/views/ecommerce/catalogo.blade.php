@extends('layouts.app')

@section('title', 'Catálogo - Fritolay')

@section('content')
<div class="flex flex-col md:flex-row gap-6" x-data="catalogo()">
    
    <!-- Sidebar / Filtros -->
    <aside class="w-full md:w-1/4 bg-white p-4 rounded shadow">
        <h2 class="font-bold text-lg mb-4 border-b pb-2">Filtros</h2>
        
        <div class="mb-4">
            <label class="block font-medium mb-1">Ordenar por</label>
            <select x-model="sortBy" @change="sortLocalProducts" class="w-full border-gray-300 rounded p-2 text-sm border focus:ring-primary">
                <option value="name_asc">Nombre (A-Z)</option>
                <option value="price_asc">Menor Precio</option>
                <option value="price_desc">Mayor Precio</option>
            </select>
        </div>

        <div>
            <label class="block font-medium mb-1">Tipo de Producto</label>
            <div class="space-y-2">
                <label class="flex items-center">
                    <input type="checkbox" value="Snack" x-model="filters.types" @change="applyFilters" class="text-primary focus:ring-primary h-4 w-4 rounded border-gray-300">
                    <span class="ml-2 text-sm">Snacks (Papas, Tortillas)</span>
                </label>
                <label class="flex items-center">
                    <input type="checkbox" value="Dips" x-model="filters.types" @change="applyFilters" class="text-primary focus:ring-primary h-4 w-4 rounded border-gray-300">
                    <span class="ml-2 text-sm">Dips y Salsas</span>
                </label>
            </div>
        </div>
    </aside>

    <!-- Product Grid -->
    <div class="w-full md:w-3/4">
        
        <div x-show="loading" class="text-center py-10">
            <p class="text-gray-500">Cargando productos desde el servidor...</p>
        </div>

        <div x-show="!loading && displayedProducts.length === 0" class="text-center py-10">
            <p class="text-gray-500">No se encontraron productos.</p>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6" x-show="!loading && displayedProducts.length > 0">
            <template x-for="product in displayedProducts" :key="product.id">
                <div class="bg-white rounded-lg shadow overflow-hidden relative flex flex-col">
                    
                    <!-- Badges -->
                    <template x-if="product.cantidad_fisica <= 0">
                        <span class="absolute top-2 right-2 bg-red-600 text-white text-xs font-bold px-2 py-1 rounded">Agotado</span>
                    </template>
                    <template x-if="product.cantidad_fisica > 0 && product.cantidad_fisica <= 5">
                        <span class="absolute top-2 right-2 bg-yellow-500 text-white text-xs font-bold px-2 py-1 rounded">¡Pocas unidades!</span>
                    </template>

                    <img :src="product.imagen_gcs_path || 'https://via.placeholder.com/150'" alt="Producto" loading="lazy" class="w-full h-48 object-cover">
                    
                    <div class="p-4 flex-grow flex flex-col">
                        <h3 class="font-bold text-lg text-neutral-dark" x-text="product.nombre"></h3>
                        <p class="text-sm text-gray-500 mb-2" x-text="product.tipo"></p>
                        <p class="text-xl font-bold text-primary mb-4" x-text="'$' + parseFloat(product.precio).toFixed(2)"></p>
                        
                        <div class="mt-auto flex flex-col space-y-2" x-data="{ qty: 1, tipoCompra: 'unidad' }">
                            <template x-if="product.unidades_por_paca > 1">
                                <select x-model="tipoCompra" class="w-full border rounded p-1 text-sm bg-gray-50">
                                    <option value="unidad">Por Unidad</option>
                                    <option value="paca" x-text="`Por Paca (${product.unidades_por_paca} unds) - $${(parseFloat(product.precio) * product.unidades_por_paca).toFixed(2)}`"></option>
                                </select>
                            </template>
                            
                            <div class="flex items-center space-x-2">
                                <input type="number" x-model.number="qty" min="1" 
                                    :max="tipoCompra === 'paca' ? Math.floor(product.cantidad_fisica / product.unidades_por_paca) : product.cantidad_fisica" 
                                    class="w-16 border rounded p-1 text-center" 
                                    :disabled="product.cantidad_fisica <= 0 || (tipoCompra === 'paca' && product.cantidad_fisica < product.unidades_por_paca)">
                                
                                <button @click="
                                    let finalQty = qty;
                                    if(tipoCompra === 'paca') finalQty = qty * product.unidades_por_paca;
                                    window.CarritoManager.agregarItem(product.id, product.nombre, finalQty, parseFloat(product.precio), product.unidades_por_paca); 
                                    $dispatch('cart-updated');
                                    if(typeof Swal !== 'undefined') Swal.fire({icon: 'success', title: 'Agregado al carrito', toast: true, position: 'top-end', showConfirmButton: false, timer: 2000});
                                " 
                                        class="flex-grow bg-primary text-white py-1 px-3 rounded hover:bg-red-700 disabled:opacity-50"
                                        :disabled="product.cantidad_fisica <= 0 || (tipoCompra === 'paca' && product.cantidad_fisica < product.unidades_por_paca)">
                                    Agregar
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </template>
        </div>
    </div>
</div>

<script>
function catalogo() {
    return {
        allProducts: [],
        displayedProducts: [],
        sortBy: 'name_asc',
        loading: true,
        filters: {
            types: []
        },
        init() {
            this.fetchProducts();
        },
        async fetchProducts() {
            this.loading = true;
            try {
                const data = await window.api('/api/productos');
                // La respuesta viene como { data: [...] }
                this.allProducts = Array.isArray(data) ? data : (data.data || []);
                this.applyFilters();
            } catch (error) {
                console.error('Error al cargar productos:', error.message);
                Swal.fire({ icon: 'error', title: 'Sin conexión', text: 'No se pudo cargar el catálogo.', confirmButtonColor: '#E3001B' });
            } finally {
                this.loading = false;
            }
        },
        applyFilters() {
            let filtered = this.allProducts;
            if (this.filters.types.length > 0) {
                filtered = filtered.filter(p => this.filters.types.includes(p.tipo));
            }
            this.displayedProducts = filtered;
            this.sortLocalProducts();
        },
        sortLocalProducts() {
            if (this.sortBy === 'price_asc') {
                this.displayedProducts.sort((a, b) => parseFloat(a.precio) - parseFloat(b.precio));
            } else if (this.sortBy === 'price_desc') {
                this.displayedProducts.sort((a, b) => parseFloat(b.precio) - parseFloat(a.precio));
            } else {
                this.displayedProducts.sort((a, b) => a.nombre.localeCompare(b.nombre));
            }
        }
    }
}
</script>
@endsection
