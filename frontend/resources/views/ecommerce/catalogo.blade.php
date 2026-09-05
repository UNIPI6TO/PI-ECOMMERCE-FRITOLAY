@extends('layouts.app')

@section('title', 'Catálogo de Productos')

@section('content')
<div class="max-w-7xl mx-auto py-4 px-2 sm:px-4" x-data="catalogo()">

    <!-- Banner Superior / Header -->
    <div class="bg-gradient-to-r from-slate-900 via-slate-800 to-slate-900 rounded-2xl p-6 sm:p-8 mb-8 text-white shadow-lg relative overflow-hidden flex flex-col md:flex-row items-start md:items-center justify-between gap-6">
        <div class="relative z-10 max-w-2xl">
            <span class="inline-block px-3 py-1 rounded-full bg-white/10 text-amber-300 text-xs font-extrabold uppercase tracking-wider mb-2 border border-white/10">
                Fritolay E-Commerce
            </span>
            <h1 class="text-2xl sm:text-3xl font-black tracking-tight">Catálogo Oficial de Productos</h1>
            <p class="text-sm text-gray-300 mt-1">Realiza tu pedido en línea por unidades o pacas con precios preferenciales.</p>
        </div>
        <div class="relative z-10 w-full md:w-auto">
            <div class="relative">
                <input type="text" 
                       x-model="searchTerm" 
                       @input="applyFilters()" 
                       placeholder="Buscar snacks, marca o categoría..." 
                       class="w-full md:w-72 pl-10 pr-4 py-2.5 bg-white/10 backdrop-blur-md border border-white/20 rounded-xl text-sm text-white placeholder-gray-400 focus:outline-none focus:bg-white focus:text-gray-900 focus:placeholder-gray-400 transition-all shadow-inner">
                <svg class="w-4 h-4 text-gray-300 absolute left-3.5 top-3 pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                </svg>
            </div>
        </div>
    </div>

    <div class="flex flex-col lg:flex-row gap-8">
        
        <!-- Sidebar / Filtros -->
        <aside class="w-full lg:w-64 flex-shrink-0">
            <div class="bg-white p-5 rounded-2xl shadow-xs border border-gray-100 sticky top-4 space-y-6">
                <div class="flex items-center justify-between border-b pb-3">
                    <h2 class="font-extrabold text-base text-gray-900 flex items-center gap-2">
                        <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"></path></svg>
                        Filtros
                    </h2>
                    <button x-show="filters.marcas.length > 0 || filters.categorias.length > 0 || searchTerm !== ''" 
                            @click="clearFilters()" 
                            class="text-xs font-bold text-[#E3001B] hover:underline">
                        Limpiar
                    </button>
                </div>
                
                <!-- Ordenar por -->
                <div>
                    <label class="block text-xs font-extrabold uppercase tracking-wider text-gray-500 mb-2">Ordenar por</label>
                    <select x-model="sortBy" @change="sortLocalProducts()" class="w-full border border-gray-200 rounded-xl p-2.5 text-xs font-semibold text-gray-800 bg-gray-50 focus:bg-white focus:ring-2 focus:ring-slate-800 focus:border-slate-800 outline-none cursor-pointer">
                        <option value="name_asc">Nombre (A-Z)</option>
                        <option value="price_asc">Precio: Menor a Mayor</option>
                        <option value="price_desc">Precio: Mayor a Menor</option>
                    </select>
                </div>

                <!-- Filtro: Marca -->
                <div class="border-t border-gray-100 pt-4" x-data="{ expanded: true }">
                    <button @click="expanded = !expanded" class="flex justify-between items-center w-full font-bold text-xs uppercase tracking-wider text-gray-700 mb-2 focus:outline-none">
                        <span>Marca</span>
                        <svg :class="{'rotate-180': expanded}" class="w-4 h-4 transition-transform text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                    </button>
                    <div x-show="expanded" class="space-y-1.5 mt-3 max-h-48 overflow-y-auto pr-1">
                        <template x-for="marca in getUniqueMarcas()" :key="marca">
                            <label class="flex items-center hover:bg-gray-50 p-1.5 rounded-lg cursor-pointer transition-colors">
                                <input type="checkbox" :value="marca" x-model="filters.marcas" @change="applyFilters()" 
                                       class="text-slate-900 focus:ring-slate-800 h-4 w-4 rounded border-gray-300">
                                <span class="ml-2.5 text-xs font-medium text-gray-700" x-text="marca"></span>
                            </label>
                        </template>
                    </div>
                </div>

                <!-- Filtro: Categoría -->
                <div class="border-t border-gray-100 pt-4" x-data="{ expanded: true }">
                    <button @click="expanded = !expanded" class="flex justify-between items-center w-full font-bold text-xs uppercase tracking-wider text-gray-700 mb-2 focus:outline-none">
                        <span>Categoría</span>
                        <svg :class="{'rotate-180': expanded}" class="w-4 h-4 transition-transform text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                    </button>
                    <div x-show="expanded" class="space-y-1.5 mt-3 max-h-48 overflow-y-auto pr-1">
                        <template x-for="cat in getUniqueCategorias()" :key="cat">
                            <label class="flex items-center hover:bg-gray-50 p-1.5 rounded-lg cursor-pointer transition-colors">
                                <input type="checkbox" :value="cat" x-model="filters.categorias" @change="applyFilters()" 
                                       class="text-slate-900 focus:ring-slate-800 h-4 w-4 rounded border-gray-300">
                                <span class="ml-2.5 text-xs font-medium text-gray-700" x-text="cat"></span>
                            </label>
                        </template>
                    </div>
                </div>
            </div>
        </aside>

        <!-- Product Grid Container -->
        <div class="flex-1">
            
            <!-- Contador de resultados -->
            <div class="flex items-center justify-between mb-4 px-1">
                <span class="text-xs font-semibold text-gray-500">
                    Mostrando <span class="font-extrabold text-gray-900" x-text="displayedProducts.length"></span> de <span class="font-extrabold text-gray-900" x-text="allProducts.length"></span> productos
                </span>
            </div>

            <!-- Loader -->
            <div x-show="loading" class="flex flex-col items-center justify-center py-24 bg-white rounded-2xl border border-gray-100 shadow-xs">
                <svg class="animate-spin h-10 w-10 text-slate-800 mb-3" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8z"></path>
                </svg>
                <span class="text-sm font-semibold text-gray-700">Cargando catálogo de productos...</span>
            </div>

            <!-- Sin resultados -->
            <div x-show="!loading && displayedProducts.length === 0" class="text-center py-16 bg-white rounded-2xl border border-gray-100 shadow-xs px-4">
                <svg class="w-12 h-12 text-gray-300 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                <h3 class="font-bold text-gray-800 text-base">No se encontraron productos</h3>
                <p class="text-xs text-gray-400 mt-1">Prueba cambiando los criterios de búsqueda o filtros.</p>
                <button @click="clearFilters()" class="mt-4 px-4 py-2 bg-slate-900 text-white rounded-xl text-xs font-bold hover:bg-slate-800 transition-all">Restablecer Filtros</button>
            </div>

            <!-- Grid de Tarjetas de Productos Modernas -->
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6" x-show="!loading && displayedProducts.length > 0">
                <template x-for="product in displayedProducts" :key="product.id">
                    <div class="bg-white rounded-2xl border border-gray-100 shadow-xs hover:shadow-xl transition-all duration-300 hover:-translate-y-1 overflow-hidden flex flex-col group">
                        
                        <!-- Header de la imagen con badges -->
                        <div class="h-52 bg-gray-50/80 p-4 flex items-center justify-center relative overflow-hidden border-b border-gray-100/60">
                            <!-- Badges de Stock -->
                            <template x-if="getStockDisponible(product) <= 0">
                                <span class="absolute top-3 right-3 bg-rose-100 text-rose-700 border border-rose-200 text-[10px] font-extrabold px-2.5 py-1 rounded-full uppercase tracking-wider shadow-2xs z-10">Agotado</span>
                            </template>
                            <template x-if="getStockDisponible(product) > 0 && getStockDisponible(product) <= 5">
                                <span class="absolute top-3 right-3 bg-amber-100 text-amber-800 border border-amber-200 text-[10px] font-extrabold px-2.5 py-1 rounded-full uppercase tracking-wider shadow-2xs z-10">¡Pocas unidades!</span>
                            </template>

                            <img :src="product.imagen_gcs_path || 'https://via.placeholder.com/200?text=Fritolay'" 
                                 :alt="product.nombre" 
                                 loading="lazy" 
                                 class="max-h-44 w-auto object-contain transform group-hover:scale-105 transition-transform duration-300">
                        </div>
                        
                        <!-- Cuerpo de la Tarjeta -->
                        <div class="p-5 flex-grow flex flex-col justify-between">
                            <div>
                                <p class="text-[11px] font-extrabold text-gray-400 uppercase tracking-wider mb-1">
                                    <span x-text="product.marca"></span> <span class="text-gray-300">•</span> <span class="text-gray-500 font-semibold" x-text="product.categoria"></span>
                                </p>
                                <h3 class="font-bold text-base text-gray-900 group-hover:text-[#E3001B] transition-colors line-clamp-1" x-text="product.nombre"></h3>
                                
                                <div class="mt-2 flex items-baseline justify-between">
                                    <div class="flex items-baseline gap-1">
                                        <span class="text-2xl font-black text-slate-900" x-text="formatMoney(product.precio)"></span>
                                        <span class="text-[11px] text-gray-400 font-semibold">/ unidad</span>
                                    </div>
                                    <!-- Visualización de Stock Disponible -->
                                    <span class="text-xs font-bold px-2 py-0.5 rounded-md"
                                          :class="{
                                              'bg-rose-50 text-rose-700 border border-rose-200': getStockDisponible(product) <= 0,
                                              'bg-amber-50 text-amber-700 border border-amber-200': getStockDisponible(product) > 0 && getStockDisponible(product) <= 5,
                                              'bg-emerald-50 text-emerald-700 border border-emerald-200': getStockDisponible(product) > 5
                                          }"
                                          x-text="`Stock: ${getStockDisponible(product)}`">
                                    </span>
                                </div>
                            </div>

                            <!-- Selector de Cantidad y Botón de Compra -->
                            <div class="mt-5 pt-4 border-t border-gray-100 space-y-3" x-data="{ qty: 1, tipoCompra: 'unidad' }">
                                <!-- Selector Unidad vs Paca -->
                                <template x-if="product.unidades_por_paca > 1">
                                    <select x-model="tipoCompra" class="w-full border border-gray-200 rounded-xl px-2.5 py-1.5 text-xs font-medium text-gray-700 bg-gray-50/50 focus:bg-white focus:ring-2 focus:ring-slate-800 outline-none cursor-pointer">
                                        <option value="unidad">Por Unidad</option>
                                        <option value="paca" x-text="`Por Paca (${product.unidades_por_paca} unds) - ${formatMoney(product.precio * product.unidades_por_paca)}`"></option>
                                    </select>
                                </template>
                                
                                <div class="flex items-center gap-2">
                                    <!-- Counter Minus / Plus / Input -->
                                    <div class="flex items-center border border-gray-200 rounded-xl bg-gray-50/50 overflow-hidden">
                                        <button type="button" 
                                                @click="if(qty > 1) qty--" 
                                                class="px-2.5 py-2 text-gray-600 hover:bg-gray-200 transition-colors font-bold text-xs disabled:opacity-40 cursor-pointer"
                                                :disabled="qty <= 1 || getStockDisponible(product) <= 0">-</button>
                                        <input type="number" 
                                               x-model.number="qty" 
                                               min="1" 
                                               :max="tipoCompra === 'paca' ? Math.floor(getStockDisponible(product) / (product.unidades_por_paca || 1)) : getStockDisponible(product)"
                                               @input="
                                                   let maxVal = tipoCompra === 'paca' ? Math.floor(getStockDisponible(product) / (product.unidades_por_paca || 1)) : getStockDisponible(product);
                                                   if (qty > maxVal) qty = maxVal > 0 ? maxVal : 1;
                                                   if (qty < 1 || isNaN(qty)) qty = 1;
                                               "
                                               class="w-12 text-center text-xs font-extrabold text-gray-900 bg-white border-x border-gray-200 py-1 focus:outline-none focus:ring-1 focus:ring-slate-800">
                                        <button type="button" 
                                                @click="
                                                    let maxVal = tipoCompra === 'paca' ? Math.floor(getStockDisponible(product) / (product.unidades_por_paca || 1)) : getStockDisponible(product);
                                                    if(qty < maxVal) qty++;
                                                " 
                                                class="px-2.5 py-2 text-gray-600 hover:bg-gray-200 transition-colors font-bold text-xs disabled:opacity-40 cursor-pointer"
                                                :disabled="qty >= (tipoCompra === 'paca' ? Math.floor(getStockDisponible(product) / (product.unidades_por_paca || 1)) : getStockDisponible(product))">+</button>
                                    </div>
                                    
                                    <!-- Botón Agregar al Carrito -->
                                    <button @click="
                                        let finalQty = qty;
                                        if(tipoCompra === 'paca') finalQty = qty * (product.unidades_por_paca || 1);
                                        let res = window.CarritoManager.agregarItemConValidacion(product.id, product.nombre, finalQty, parseFloat(product.precio), getStockDisponible(product), product.unidades_por_paca, product.imagen_gcs_path);
                                        if (res.exito) {
                                            $dispatch('cart-updated');
                                            if(typeof Swal !== 'undefined') Swal.fire({icon: 'success', title: '¡Agregado al carrito!', toast: true, position: 'bottom', showConfirmButton: false, timer: 1800});
                                        } else {
                                            if(typeof Swal !== 'undefined') Swal.fire({icon: 'warning', title: 'Stock Insuficiente', text: res.mensaje, toast: true, position: 'bottom', showConfirmButton: false, timer: 2500});
                                        }
                                    " 
                                            class="flex-1 py-2.5 px-3 bg-slate-900 hover:bg-slate-800 text-white font-bold text-xs rounded-xl shadow-xs transition-all flex items-center justify-center gap-1.5 disabled:opacity-40 disabled:cursor-not-allowed cursor-pointer"
                                            :disabled="getStockDisponible(product) <= 0 || (tipoCompra === 'paca' && getStockDisponible(product) < product.unidades_por_paca)">
                                        <svg class="w-4 h-4 text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path></svg>
                                        <span>Agregar</span>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </template>
            </div>
        </div>
    </div>
</div>

<script>
function catalogo() {
    return {
        allProducts: [],
        displayedProducts: [],
        searchTerm: '',
        sortBy: 'name_asc',
        loading: true,
        filters: {
            marcas: [],
            categorias: []
        },
        cartItems: [],
        getStockDisponible(p) {
            if (!p) return 0;
            let disponibleBackend = 0;
            if (p.disponible !== undefined && p.disponible !== null) {
                disponibleBackend = parseFloat(p.disponible);
            } else {
                const cantFisica = parseFloat(p.cantidad_fisica || 0);
                const enPedidos = parseFloat(p.en_pedidos || 0);
                disponibleBackend = Math.max(0, cantFisica - enPedidos);
            }

            // Descontar la cantidad que el usuario ya tiene agregada en el carrito local
            const itemEnCarrito = (this.cartItems || []).find(i => i.productoId === p.id);
            const enCarritoLocal = itemEnCarrito ? parseFloat(itemEnCarrito.cantidad || 0) : 0;

            return Math.max(0, disponibleBackend - enCarritoLocal);
        },
        init() {
            this.updateCartItems();
            window.addEventListener('cart-updated', () => {
                this.updateCartItems();
            });
            this.fetchProducts();
        },
        updateCartItems() {
            if (window.CarritoManager) {
                this.cartItems = window.CarritoManager.getItems() || [];
            }
        },
        getUniqueMarcas() {
            const marcas = this.allProducts.map(p => p.marca).filter(Boolean);
            return [...new Set(marcas)].sort();
        },
        getUniqueCategorias() {
            let categorias = [];
            this.allProducts.forEach(p => {
                if(p.categoria) {
                    p.categoria.split(',').forEach(c => categorias.push(c.trim()));
                }
            });
            return [...new Set(categorias)].sort();
        },
        async fetchProducts() {
            this.loading = true;
            try {
                const data = await window.api('/api/productos');
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

            if (this.searchTerm.trim() !== '') {
                const term = this.searchTerm.toLowerCase();
                filtered = filtered.filter(p => {
                    const name = (p.nombre || '').toLowerCase();
                    const brand = (p.marca || '').toLowerCase();
                    const cat = (p.categoria || '').toLowerCase();
                    return name.includes(term) || brand.includes(term) || cat.includes(term);
                });
            }
            
            if (this.filters.marcas.length > 0) {
                filtered = filtered.filter(p => this.filters.marcas.includes(p.marca));
            }
            
            if (this.filters.categorias.length > 0) {
                filtered = filtered.filter(p => {
                    if(!p.categoria) return false;
                    const prodCats = p.categoria.split(',').map(c => c.trim());
                    return this.filters.categorias.some(c => prodCats.includes(c));
                });
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
        },
        clearFilters() {
            this.searchTerm = '';
            this.filters.marcas = [];
            this.filters.categorias = [];
            this.applyFilters();
        }
    }
}
</script>
@endsection
