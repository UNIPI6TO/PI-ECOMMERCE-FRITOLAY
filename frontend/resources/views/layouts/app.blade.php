<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Fritolay Ambato')</title>
    
    <link rel="manifest" href="/manifest.json">
    <meta name="theme-color" content="#E3001B">
    
    <!-- Leaflet CSS -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" crossorigin=""/>
    
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    
    <!-- Leaflet JS -->
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" crossorigin=""></script>

    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body class="bg-gray-50 text-neutral-dark min-h-screen flex flex-col font-sans">
    
    <nav class="bg-primary text-white shadow-md">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16">
                <div class="flex items-center">
                    <a href="/" class="flex-shrink-0 flex items-center font-bold text-2xl tracking-tight">
                        <span class="text-white">Frito</span><span class="text-secondary">lay</span>
                    </a>
                </div>
                
                <div class="flex items-center space-x-4">
                    <!-- Nav links condition based on roles (mocked in JS/Session) -->
                    <div x-data="{ role: localStorage.getItem('role') || 'guest' }" class="hidden md:flex space-x-4">
                        <template x-if="role === 'guest' || role === 'cliente'">
                            <a href="/" class="hover:text-secondary px-3 py-2 rounded-md font-medium">CatÃ¡logo</a>
                        </template>
                        <template x-if="role === 'chofer'">
                            <a href="/rutas" class="hover:text-secondary px-3 py-2 rounded-md font-medium">Mis Rutas</a>
                        </template>
                    </div>

                    <!-- Cart Icon -->
                    <div x-data="cartBadge" class="relative cursor-pointer" @click="$dispatch('toggle-cart')">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" />
                        </svg>
                        <span x-show="count > 0" x-text="count" class="absolute -top-2 -right-2 bg-secondary text-neutral-dark text-xs font-bold rounded-full h-5 w-5 flex items-center justify-center"></span>
                    </div>

                    <!-- Auth Links -->
                    <div x-data="{ token: localStorage.getItem('jwt_token') }">
                        <template x-if="!token">
                            <a href="/auth/login" class="hover:text-secondary px-3 py-2 font-medium">Login</a>
                        </template>
                        <template x-if="token">
                            <button @click="logout()" class="hover:text-secondary px-3 py-2 font-medium">Logout</button>
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
