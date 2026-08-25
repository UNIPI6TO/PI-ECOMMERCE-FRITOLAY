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
            const isFormData = options.body instanceof FormData;
            const headers = {
                ...(isFormData ? {} : { 'Content-Type': 'application/json' }),
                'Accept': 'application/json',
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
                    || data.error
                    || (data.errors ? Object.values(data.errors).flat().join(' ') : null)
                    || `Error ${response.status}`;
                throw Object.assign(new Error(message), { status: response.status, data });
            }

            return data;
        };

        window.formatMoney = function(value) {
            let num = Number(value);
            if (isNaN(num)) return '$0.00';
            return '$' + num.toFixed(2);
        };

        // Guardian de rutas basado en roles
        (function() {
            const role = localStorage.getItem('role') || 'guest';
            const path = window.location.pathname;

            const adminPaths = [
                '/gestion-pedidos',
                '/entregas',
                '/dashboard',
                '/admin'
            ];

            const clientPaths = [
                '/ecommerce'
            ];
            
            const isPathInArray = (p, arr) => arr.some(prefix => p.startsWith(prefix) || p === prefix);

            if (role === 'cliente' || role === 'guest') {
                if (isPathInArray(path, adminPaths)) {
                    window.location.replace('/ecommerce/catalogo');
                }
            } else {
                // Roles administrativos: admin, operador, despachador
                if (isPathInArray(path, clientPaths) || path === '/') {
                    if (role === 'operador') {
                        window.location.replace('/gestion-pedidos');
                    } else {
                        window.location.replace('/dashboard');
                    }
                }
            }
        })();
    </script>
</head>
<body class="bg-gray-50 text-neutral-dark min-h-screen flex flex-col font-sans">
    
           <nav class="bg-white text-gray-700 shadow-sm border-b border-gray-100" x-data="{ mobileMenuOpen: false }">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16">
                <div class="flex items-center">
                    <!-- Mobile menu button -->
                    <button @click="mobileMenuOpen = !mobileMenuOpen" class="md:hidden p-2 mr-2 text-gray-600 hover:text-primary focus:outline-none">
                        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        </svg>
                    </button>
                    <a href="/" class="flex-shrink-0 flex items-center font-bold text-2xl tracking-tight">
                        <span class="text-primary">Frito</span><span class="text-secondary">lay</span>
                    </a>
                </div>
                
                <div class="flex items-center space-x-2 md:space-x-4">
                    <!-- Desktop Nav links -->
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
                                <a href="/gestion-rutas" class="hover:text-primary px-3 py-2 rounded-md font-medium transition-colors">Asignación de Rutas</a>
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
                            <button x-data="cartBadge" @click="$dispatch('toggle-cart')" class="relative flex items-center space-x-2 p-2 hover:bg-gray-100 rounded-md transition-colors">
                                <div class="relative">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-gray-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" />
                                    </svg>
                                    <span x-show="count > 0" x-text="count" class="absolute top-0 right-0 inline-flex items-center justify-center px-2 py-1 text-xs font-bold leading-none text-white transform translate-x-1/4 -translate-y-1/4 bg-primary rounded-full" id="cart-count" style="display: none;"></span>
                                </div>
                                <span x-show="total > 0" x-text="formatMoney(total)" class="font-bold text-sm text-gray-700 hidden sm:inline-block" style="display: none;"></span>
                            </button>
                        </template>
                    </div>

                    <!-- User Dropdown -->
                    <div x-data="{ token: localStorage.getItem('jwt_token'), dropdownOpen: false }" class="relative ml-2 md:ml-4">
                        <template x-if="!token">
                            <a href="/auth/login" class="bg-primary hover:bg-red-800 text-white px-4 py-2 rounded-md font-medium transition-colors">Login</a>
                        </template>
                        <template x-if="token">
                            <div>
                                <button @click="dropdownOpen = !dropdownOpen" @click.away="dropdownOpen = false" class="flex items-center space-x-2 focus:outline-none p-2 hover:bg-gray-100 rounded-full transition-colors">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-gray-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                    </svg>
                                </button>
                                
                                <div x-show="dropdownOpen" x-transition.opacity style="display: none;"
                                     class="absolute right-0 mt-2 w-48 bg-white rounded-md shadow-lg py-1 z-50 border border-gray-100">
                                    <a href="/perfil" class="px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 flex items-center transition-colors">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" /></svg>
                                        Mi Perfil
                                    </a>
                                    <a href="/perfil/password" class="px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 flex items-center transition-colors">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z" /></svg>
                                        Cambiar Contraseña
                                    </a>
                                    <div class="border-t border-gray-100 my-1"></div>
                                    <button @click="logout" class="w-full text-left px-4 py-2 text-sm text-red-600 hover:bg-gray-100 font-medium flex items-center transition-colors">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" /></svg>
                                        Salir
                                    </button>
                                </div>
                            </div>
                        </template>
                    </div>
                </div>
            </div>
        </div>

        <!-- Mobile Menu -->
        <div x-show="mobileMenuOpen" class="md:hidden border-t border-gray-100" style="display: none;">
            <div x-data="{ role: localStorage.getItem('role') || 'guest' }" class="px-2 pt-2 pb-3 space-y-1 sm:px-3">
                <template x-if="role === 'guest' || role === 'cliente'">
                    <a href="/" class="block px-3 py-2 rounded-md text-base font-medium text-gray-700 hover:text-primary hover:bg-gray-50">Catálogo</a>
                </template>
                <template x-if="role === 'cliente'">
                    <a href="/ecommerce/historial" class="block px-3 py-2 rounded-md text-base font-medium text-gray-700 hover:text-primary hover:bg-gray-50">Mis Pedidos</a>
                </template>
                <template x-if="role === 'chofer'">
                    <a href="/entregas" class="block px-3 py-2 rounded-md text-base font-medium text-gray-700 hover:text-primary hover:bg-gray-50">Mis Rutas</a>
                </template>
                <template x-if="role === 'admin' || role === 'administrador' || role === 'operador'">
                    <div>
                        <a href="/dashboard" class="block px-3 py-2 rounded-md text-base font-medium text-gray-700 hover:text-primary hover:bg-gray-50">Dashboard</a>
                        <a href="/gestion-pedidos" class="block px-3 py-2 rounded-md text-base font-medium text-gray-700 hover:text-primary hover:bg-gray-50">Gestión Pedidos</a>
                    </div>
                </template>
                <template x-if="role === 'admin' || role === 'administrador'">
                    <div>
                        <a href="/admin/usuarios" class="block px-3 py-2 rounded-md text-base font-medium text-gray-700 hover:text-primary hover:bg-gray-50">Usuarios</a>
                        <a href="/admin/camiones" class="block px-3 py-2 rounded-md text-base font-medium text-gray-700 hover:text-primary hover:bg-gray-50">Camiones</a>
                    </div>
                </template>
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
                total: 0,
                init() {
                    this.updateCount();
                    window.addEventListener('cart-updated', () => this.updateCount());
                },
                updateCount() {
                    if (window.CarritoManager) {
                        this.count = window.CarritoManager.getCount();
                        this.total = window.CarritoManager.calcularSubtotal();
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
