@extends('layouts.app')

@section('title', 'Catálogo - Fritolay')

@section('content')
<div class="flex flex-col md:flex-row gap-6" x-data="catalogo()">
    
    <!-- Sidebar / Filtros -->
    <aside class="w-full md:w-1/4 bg-white p-4 rounded shadow">
        <h2 class="font-bold text-lg mb-4 border-b pb-2">Filtros</h2>
        
        <div class="mb-4">
            <label class="block font-medium mb-1">Ordenar por</label>
            <select x-model="sortBy" @change="fetchProducts" class="w-full border-gray-300 rounded p-2 text-sm border focus:ring-primary">
                <option value="name_asc">Nombre (A-Z)</option>
                <option value="price_asc">Menor Precio</option>
                <option value="price_desc">Mayor Precio</option>
            </select>
        </div>

        <div>
            <label class="block font-medium mb-1">Tipo de Producto</label>
            <div class="space-y-2">
                <label class="flex items-center">
                    <input type="checkbox" value="snacks" x-model="filters.types" @change="fetchProducts" class="text-primary focus:ring-primary h-4 w-4 rounded border-gray-300">
                    <span class="ml-2 text-sm">Snacks (Papas, Tortillas)</span>
                </label>
                <label class="flex items-center">
                    <input type="checkbox" value="nuts" x-model="filters.types" @change="fetchProducts" class="text-primary focus:ring-primary h-4 w-4 rounded border-gray-300">
                    <span class="ml-2 text-sm">Nueces y Semillas</span>
                </label>
                <label class="flex items-center">
                    <input type="checkbox" value="dips" x-model="filters.types" @change="fetchProducts" class="text-primary focus:ring-primary h-4 w-4 rounded border-gray-300">
                    <span class="ml-2 text-sm">Dips y Salsas</span>
                </label>
            </div>
        </div>
    </aside>

    <!-- Product Grid -->
    <div class="w-full md:w-3/4">
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
            <template x-for="product in products" :key="product.id">
                <div class="bg-white rounded-lg shadow overflow-hidden relative flex flex-col">
                    
                    <!-- Badges -->
                    <template x-if="product.stock === 0">
                        <span class="badge-out-of-stock">Agotado</span>
                    </template>
                    <template x-if="product.stock > 0 && product.stock <= 5">
                        <span class="badge-low-stock">¡Pocas unidades!</span>
                    </template>

                    <img :src="product.image || 'https://via.placeholder.com/150'" alt="Producto" loading="lazy" class="w-full h-48 object-cover">
                    
                    <div class="p-4 flex-grow flex flex-col">
                        <h3 class="font-bold text-lg text-neutral-dark" x-text="product.name"></h3>
                        <p class="text-sm text-gray-500 mb-2" x-text="product.type"></p>
                        <p class="text-xl font-bold text-primary mb-4" x-text="'$' + product.price.toFixed(2)"></p>
                        
                        <div class="mt-auto flex items-center space-x-2" x-data="{ qty: 1 }">
                            <input type="number" x-model="qty" min="1" :max="product.stock" class="w-16 border rounded p-1 text-center" :disabled="product.stock === 0">
                            
                            <button @click="window.CarritoManager.agregarItem(product.id, product.name, qty, product.price); $dispatch('cart-updated')" 
                                    class="flex-grow bg-primary text-white py-1 px-3 rounded hover:bg-red-700 disabled:opacity-50"
                                    :disabled="product.stock === 0">
                                Agregar
                            </button>
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
        products: [],
        sortBy: 'name_asc',
        filters: {
            types: []
        },
        init() {
            this.fetchProducts();
        },
        async fetchProducts() {
            // Mock data fetch. In reality, call the API with this.filters and this.sortBy
            this.products = [
                { id: 1, name: 'Doritos Nacho', type: 'Snacks', price: 0.60, stock: 100, image: 'https://storage.googleapis.com/fritolay/doritos.png' },
                { id: 2, name: 'Lays Clásicas', type: 'Snacks', price: 0.50, stock: 3, image: 'https://storage.googleapis.com/fritolay/lays.png' },
                { id: 3, name: 'Tostitos Salsa', type: 'Dips', price: 2.50, stock: 0, image: 'https://storage.googleapis.com/fritolay/tostitos.png' },
                { id: 4, name: 'Cheetos Queso', type: 'Snacks', price: 0.40, stock: 50, image: 'https://storage.googleapis.com/fritolay/cheetos.png' }
            ];
            
            // Simple client side sort for demo
            if (this.sortBy === 'price_asc') {
                this.products.sort((a,b) => a.price - b.price);
            } else if (this.sortBy === 'price_desc') {
                this.products.sort((a,b) => b.price - a.price);
            } else {
                this.products.sort((a,b) => a.name.localeCompare(b.name));
            }
        }
    }
}
</script>
@endsection
