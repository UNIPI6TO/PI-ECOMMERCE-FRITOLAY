<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Fritolay Ambato')</title>
    
    <link rel="manifest" href="/manifest.json">
    <meta name="theme-color" content="#FFFFFF">
    
    <!-- Leaflet CSS -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" crossorigin=""/>
    
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    
    <!-- Leaflet JS -->
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" crossorigin=""></script>

    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <!-- Backend API config & helper centralizado -->
    <script>
        window.BACKEND_URL = '{{ env("BACKEND_API_URL", "http://localhost:8000") }}';

        /**
         * api(path, options) - Wrapper centralizado para todas las llamadas al backend.
         * Añade automáticamente:
         *   - URL base correcta
         *   - Content-Type: application/json
         *   - Authorization: Bearer <token> si existe en localStorage
         * Retorna la respuesta parseada como JSON.
         * Lanza un Error con el mensaje del servidor en caso de fallo.
         */
        window.api = async function(path, options = {}) {
            const token = localStorage.getItem('jwt_token');
            const headers = {
                'Content-Type': 'application/json',
                ...(token ? { 'Authorization': `Bearer ${token}` } : {}),
                ...(options.headers || {})
            };

            const response = await fetch(`${window.BACKEND_URL}${path}`, {
                ...options,
                headers
            });

            // Para respuestas sin cuerpo (204 No Content)
            if (response.status === 204) return null;

            const data = await response.json().catch(() => ({}));

            if (!response.ok) {
                // Construye mensaje legible desde errores de validación (422) u otros
                const message = data.message
                    || (data.errors ? Object.values(data.errors).flat().join(' ') : null)
                    || `Error ${response.status}`;
                throw Object.assign(new Error(message), { status: response.status, data });
            }

            return data;
        };
    </script>
</head>
<body class="bg-gray-50 text-neutral-dark min-h-screen flex flex-col font-sans">
    
       <nav class="bg-white text-gray-700 shadow-sm border-b border-gray-100">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16">
                <div class="flex items-center">
                    <a href="/" class="flex-shrink-0 flex items-center font-bold text-2xl tracking-tight">
                        <span class="text-primary">Frito</span><span class="text-secondary">lay</span>
                    </a>
                </div>
                
                <div class="flex items-center space-x-4">
                    <!-- Nav links condition based on roles -->
                    <div x-data="{ role: localStorage.getItem('role') || 'guest' }" class="hidden md:flex space-x-4">
                        <template x-if="role === 'guest' || role === 'cliente'">
                            <a href="/" class="hover:text-primary px-3 py-2 rounded-md font-medium transition-colors">Catálogo</a>
                        </template>
                        <template x-if="role === 'cliente'">
                            <a href="/ecommerce/historial" class="hover:text-primary px-3 py-2 rounded-md font-medium transition-colors">Mis Pedidos</a>
                        </template>
                        <template x-if="role === 'chofer'">
                            <a href="/entregas" class="hover:text-primary px-3 py-2 rounded-md font-medium transition-colors">Mis Rutas</a>
                        </template>
                        <template x-if="role === 'admin' || role === 'administrador' || role === 'operador'">
                            <div class="flex space-x-4">
                                <a href="/dashboard" class="hover:text-primary px-3 py-2 rounded-md font-medium transition-colors">Dashboard</a>
                                <a href="/gestion-pedidos" class="hover:text-primary px-3 py-2 rounded-md font-medium transition-colors">Gestión Pedidos</a>
                            </div>
                        </template>
                        <template x-if="role === 'admin' || role === 'administrador'">
                            <div class="flex space-x-4">
                                <a href="/admin/usuarios" class="hover:text-primary px-3 py-2 rounded-md font-medium transition-colors">Usuarios</a>
                                <a href="/admin/camiones" class="hover:text-primary px-3 py-2 rounded-md font-medium transition-colors">Camiones</a>
                            </div>
                        </template>
                    </div>

                    <!-- Cart Icon -->
                    <div x-data="{ role: localStorage.getItem('role') || 'guest' }">
                        <template x-if="role === 'guest' || role === 'cliente'">
                            <button x-data="cartBadge" @click="$dispatch('toggle-cart')" class="relative p-2 hover:bg-gray-100 rounded-full transition-colors">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-gray-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" />
                                </svg>
                                <span x-show="count > 0" x-text="count" class="absolute top-0 right-0 inline-flex items-center justify-center px-2 py-1 text-xs font-bold leading-none text-white transform translate-x-1/4 -translate-y-1/4 bg-primary rounded-full" id="cart-count"></span>
                            </button>
                        </template>
                    </div>

                    <div x-data="{ token: localStorage.getItem('jwt_token') }">
                        <template x-if="!token">
                            <a href="/auth/login" class="bg-primary hover:bg-red-800 text-white px-4 py-2 rounded-md font-medium ml-4 transition-colors">Login</a>
                        </template>
                        <template x-if="token">
                            <button @click="logout" class="bg-gray-200 hover:bg-gray-300 text-gray-800 px-4 py-2 rounded-md font-medium ml-4 transition-colors">Salir</button>
                        </template>
                    </div>
                </div>
            </div>
        </div>
    </nav>

    <main class="flex-grow container mx-auto px-4 py-8">
        @yield('content')
    </main>

    <footer class="bg-neutral-dark text-white text-center py-4 mt-auto">
        <p class="text-sm">&copy; {{ date('Y') }} Fritolay Ambato. Todos los derechos reservados.</p>
    </footer>

    @include('ecommerce.catalogo._mini_carrito')

    <script>
        document.addEventListener('alpine:init', () => {
            Alpine.data('cartBadge', () => ({
                count: 0,
                init() {
                    this.updateCount();
                    window.addEventListener('cart-updated', () => this.updateCount());
                },
                updateCount() {
                    if (window.CarritoManager) {
                        this.count = window.CarritoManager.getCount();
                    }
                }
            }));
        });
        
        function logout() {
            localStorage.removeItem('jwt_token');
            localStorage.removeItem('role');
            window.location.href = '/auth/login';
        }

        // Register Service Worker
        if ('serviceWorker' in navigator) {
            window.addEventListener('load', () => {
                navigator.serviceWorker.register('/sw.js').then(registration => {
                    console.log('SW registered: ', registration);
                }).catch(registrationError => {
                    console.log('SW registration failed: ', registrationError);
                });
            });
        }
    </script>
</body>
</html>
