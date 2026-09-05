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

    <!-- Estilos compactos responsivos para Toast flotante en móviles (Fondo Blanco) -->
    <style>
        .swal2-container.swal2-bottom {
            bottom: 16px !important;
        }
        .swal2-popup.swal2-toast {
            padding: 8px 16px !important;
            font-size: 12px !important;
            max-width: calc(100vw - 32px) !important;
            width: auto !important;
            border-radius: 9999px !important;
            border: 1px solid #e2e8f0 !important;
            box-shadow: 0 4px 16px rgba(0, 0, 0, 0.12) !important;
            background: #ffffff !important;
            color: #0f172a !important;
            display: inline-flex !important;
            align-items: center !important;
        }
        .swal2-popup.swal2-toast .swal2-title,
        .swal2-popup.swal2-toast .swal2-html-container {
            font-size: 12px !important;
            font-weight: 600 !important;
            color: #0f172a !important;
            margin: 0 4px !important;
            padding: 0 !important;
            line-height: 1.2 !important;
        }
        .swal2-popup.swal2-toast .swal2-icon {
            width: 18px !important;
            height: 18px !important;
            margin: 0 6px 0 0 !important;
            min-width: 18px !important;
        }
    </style>

    <!-- Backend API config & helper centralizado -->
    <script>
        window.BACKEND_URL = '{{ env("BACKEND_API_URL", "http://localhost:8000") }}';

        /**
         * window.toast(message, icon, position) - Toast flotante compacto y responsivo al centro inferior
         */
        window.toast = function(message, icon = 'error', position = 'bottom') {
            if (typeof Swal !== 'undefined') {
                Swal.fire({
                    icon: icon,
                    text: message,
                    toast: true,
                    position: position,
                    showConfirmButton: false,
                    timer: 3000
                });
            } else {
                console.log(`[Toast ${icon}]`, message);
            }
        };

        /**
         * api(path, options) - Wrapper centralizado para todas las llamadas al backend.
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

            if (response.status === 204) return null;

            const data = await response.json().catch(() => ({}));

            if (!response.ok) {
                if (response.status === 401 && !path.includes('/auth/login')) {
                    localStorage.removeItem('jwt_token');
                    localStorage.removeItem('role');
                    window.location.replace('/auth/login');
                    throw new Error('Sesión expirada. Por favor inicie sesión nuevamente.');
                }
                
                if (response.status === 403) {
                    const role = localStorage.getItem('role') || 'guest';
                    const homePages = {
                        'admin': '/dashboard',
                        'administrador': '/dashboard',
                        'operador': '/dashboard',
                        'chofer': '/entregas',
                        'cliente': '/ecommerce/catalogo',
                        'guest': '/ecommerce/catalogo'
                    };
                    window.location.replace(homePages[role] || '/ecommerce/catalogo');
                    throw new Error('Acceso denegado a este recurso.');
                }

                let message = data.message;
                if (!message && data.error) {
                    message = data.error;
                }
                if (!message && data.errors) {
                    if (typeof data.errors === 'object') {
                        message = Object.values(data.errors).flat().join(' ');
                    } else {
                        message = String(data.errors);
                    }
                }

                if (!message || message === 'Unprocessable Content' || message.includes('422')) {
                    if (response.status === 422 || response.status === 401) {
                        message = 'Credenciales no válidas o datos incorrectos. Revisa la información e inténtalo de nuevo.';
                    } else {
                        message = `Error al procesar la solicitud (${response.status}).`;
                    }
                }

                throw Object.assign(new Error(message), { status: response.status, data });
            }

            return data;
        };

        window.formatMoney = function(value) {
            let num = Number(value);
            if (isNaN(num)) return '$0.00';
            return '$' + num.toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
        };

        window.formatNumber = function(value, decimals = 0) {
            let num = Number(value);
            if (isNaN(num)) return '0';
            return num.toLocaleString('en-US', { minimumFractionDigits: decimals, maximumFractionDigits: decimals });
        };

        // Guardian de rutas basado en roles (Role-Based Routing)
        (function() {
            const role = localStorage.getItem('role') || 'guest';
            const path = window.location.pathname;

            const homePages = {
                'admin': '/dashboard',
                'administrador': '/dashboard',
                'operador': '/dashboard',
                'chofer': '/entregas',
                'cliente': '/ecommerce/catalogo',
                'guest': '/ecommerce/catalogo'
            };

            if (path === '/') {
                window.location.replace(homePages[role] || '/ecommerce/catalogo');
                return;
            }

            const publicPaths = ['/ecommerce/catalogo', '/auth/login', '/auth/registro', '/auth/recover'];
            const isPublic = publicPaths.some(p => path === p || path.startsWith(p));
            
            if (role === 'guest' && !isPublic) {
                window.location.replace('/auth/login');
                return;
            }

            if (role !== 'guest' && path.startsWith('/auth/')) {
                window.location.replace(homePages[role]);
                return;
            }

            const adminPaths = ['/dashboard', '/gestion-pedidos', '/gestion-rutas', '/admin', '/entregas'];
            const isPathInArray = (p, arr) => arr.some(prefix => p.startsWith(prefix) || p === prefix);

            if (role === 'cliente') {
                if (isPathInArray(path, adminPaths)) {
                    window.location.replace(homePages['cliente']);
                }
            } else if (role === 'chofer') {
                const allowedForChofer = ['/entregas', '/perfil'];
                if (!allowedForChofer.some(prefix => path.startsWith(prefix))) {
                    window.location.replace(homePages['chofer']);
                }
            } else if (role === 'operador') {
                const allowedForOperador = ['/dashboard', '/gestion-pedidos', '/gestion-rutas', '/admin/cierre-guias', '/perfil'];
                if (!allowedForOperador.some(prefix => path.startsWith(prefix))) {
                    window.location.replace(homePages['operador']);
                }
            }
        })();
    </script>
</head>
<body class="bg-gray-50 text-neutral-dark min-h-screen flex flex-col font-sans">
    
    <nav class="bg-white text-gray-700 shadow-2xs border-b border-gray-100 sticky top-0 z-40" x-data="{ mobileMenuOpen: false }">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16">
                <div class="flex items-center">
                    <!-- Mobile menu button -->
                    <button @click="mobileMenuOpen = !mobileMenuOpen" class="md:hidden p-2 mr-2 text-gray-600 hover:text-[#E3001B] focus:outline-none">
                        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        </svg>
                    </button>
                    <a href="/" class="flex-shrink-0 flex items-center font-black text-2xl tracking-tight">
                        <span class="text-[#E3001B]">Frito</span><span class="text-[#F5C518]">lay</span>
                    </a>
                </div>
                
                <div class="flex items-center space-x-2 md:space-x-4">
                    <!-- Desktop Nav links con indicador activo sutil -->
                    <div x-data="{ 
                        role: localStorage.getItem('role') || 'guest',
                        currentPath: window.location.pathname,
                        isActive(target) {
                            if (target === '/' || target === '/ecommerce/catalogo') {
                                return this.currentPath === '/' || this.currentPath.startsWith('/ecommerce/catalogo');
                            }
                            return this.currentPath === target || this.currentPath.startsWith(target);
                        } 
                    }" class="hidden md:flex items-center space-x-1 sm:space-x-2">

                        <template x-if="role === 'guest' || role === 'cliente'">
                            <a href="/ecommerce/catalogo" 
                               :class="isActive('/ecommerce/catalogo') ? 'bg-red-50 text-[#E3001B] border border-red-100 font-extrabold shadow-2xs' : 'text-gray-600 hover:text-gray-900 hover:bg-gray-100/70 font-bold'"
                               class="px-3.5 py-2 rounded-xl text-xs transition-all">Catálogo</a>
                        </template>

                        <template x-if="role === 'cliente'">
                            <a href="/ecommerce/historial" 
                               :class="isActive('/ecommerce/historial') ? 'bg-red-50 text-[#E3001B] border border-red-100 font-extrabold shadow-2xs' : 'text-gray-600 hover:text-gray-900 hover:bg-gray-100/70 font-bold'"
                               class="px-3.5 py-2 rounded-xl text-xs transition-all">Mis Pedidos</a>
                        </template>

                        <template x-if="role === 'chofer'">
                            <a href="/entregas" 
                               :class="isActive('/entregas') ? 'bg-red-50 text-[#E3001B] border border-red-100 font-extrabold shadow-2xs' : 'text-gray-600 hover:text-gray-900 hover:bg-gray-100/70 font-bold'"
                               class="px-3.5 py-2 rounded-xl text-xs transition-all">Mis Rutas</a>
                        </template>

                        <template x-if="role === 'admin' || role === 'administrador' || role === 'operador'">
                            <div class="flex items-center space-x-1">
                                <a href="/dashboard" 
                                   :class="isActive('/dashboard') ? 'bg-slate-900 text-white font-extrabold shadow-2xs' : 'text-gray-600 hover:text-gray-900 hover:bg-gray-100/70 font-bold'"
                                   class="px-3.5 py-2 rounded-xl text-xs transition-all">Dashboard</a>
                                
                                <!-- Dropdown Administración de Ventas -->
                                <div class="relative" x-data="{ openVentas: false }">
                                    <button @click="openVentas = !openVentas" @click.away="openVentas = false"
                                            :class="(isActive('/gestion-pedidos') || isActive('/gestion-rutas') || isActive('/admin/cierre-guias')) ? 'bg-slate-900 text-white font-extrabold shadow-2xs' : 'text-gray-600 hover:text-gray-900 hover:bg-gray-100/70 font-bold'"
                                            class="px-3.5 py-2 rounded-xl text-xs transition-all flex items-center gap-1 cursor-pointer">
                                        <span>Administración de Ventas</span>
                                        <svg class="w-3.5 h-3.5 transition-transform" :class="{ 'rotate-180': openVentas }" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                                    </button>
                                    <div x-show="openVentas" x-transition.opacity style="display: none;"
                                         class="absolute left-0 mt-2 w-52 bg-white rounded-2xl shadow-xl py-2 z-50 border border-gray-100">
                                        <a href="/gestion-pedidos" class="px-4 py-2 text-xs font-bold text-gray-700 hover:bg-gray-50 flex items-center transition-colors">
                                            Gestión Pedidos
                                        </a>
                                        <a href="/gestion-rutas" class="px-4 py-2 text-xs font-bold text-gray-700 hover:bg-gray-50 flex items-center transition-colors">
                                            Asignación Rutas
                                        </a>
                                        <a href="/admin/cierre-guias" class="px-4 py-2 text-xs font-bold text-gray-700 hover:bg-gray-50 flex items-center transition-colors">
                                            Cierre de Guías
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </template>

                        <template x-if="role === 'admin' || role === 'administrador'">
                            <div class="flex items-center space-x-1">
                                <a href="/admin/usuarios" 
                                   :class="isActive('/admin/usuarios') ? 'bg-slate-900 text-white font-extrabold shadow-2xs' : 'text-gray-600 hover:text-gray-900 hover:bg-gray-100/70 font-bold'"
                                   class="px-3.5 py-2 rounded-xl text-xs transition-all">Usuarios</a>

                                <!-- Dropdown Categoría Flota -->
                                <div class="relative" x-data="{ openFlota: false }">
                                    <button @click="openFlota = !openFlota" @click.away="openFlota = false"
                                            :class="(isActive('/admin/camiones') || isActive('/admin/flota/ubicaciones')) ? 'bg-slate-900 text-white font-extrabold shadow-2xs' : 'text-gray-600 hover:text-gray-900 hover:bg-gray-100/70 font-bold'"
                                            class="px-3.5 py-2 rounded-xl text-xs transition-all flex items-center gap-1 cursor-pointer">
                                        <span>Flota</span>
                                        <svg class="w-3.5 h-3.5 transition-transform" :class="{ 'rotate-180': openFlota }" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                                    </button>
                                    <div x-show="openFlota" x-transition.opacity style="display: none;"
                                         class="absolute left-0 mt-2 w-52 bg-white rounded-2xl shadow-xl py-2 z-50 border border-gray-100">
                                        <a href="/admin/camiones" class="px-4 py-2 text-xs font-bold text-gray-700 hover:bg-gray-50 flex items-center gap-2 transition-colors">
                                            <svg class="w-4 h-4 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16.5a1.5 1.5 0 11-3 0 1.5 1.5 0 013 0zM15 16.5a1.5 1.5 0 11-3 0 1.5 1.5 0 013 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 00-1 1v10a1 1 0 001 1h1.05a2.5 2.5 0 014.9 0H10a1 1 0 001-1V5a1 1 0 00-1-1H3zM14 7a1 1 0 00-1 1v6.05A2.5 2.5 0 0115.95 16H17a1 1 0 001-1v-5l-4-4h-0.05z"/></svg>
                                            Camiones
                                        </a>
                                        <a href="/admin/flota/ubicaciones" class="px-4 py-2 text-xs font-bold text-gray-700 hover:bg-gray-50 flex items-center gap-2 transition-colors">
                                            <svg class="w-4 h-4 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                                            Ubicaciones
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </template>

                        <!-- Dropdown Ayuda / Información Corporativa (Solo Icono Elegante) -->
                        <div class="relative" x-data="{ openAyuda: false }">
                            <button @click="openAyuda = !openAyuda" @click.away="openAyuda = false"
                                    :class="(isActive('/mapa-del-sitio') || isActive('/acerca-de') || isActive('/politicas-privacidad')) ? 'bg-slate-900 text-white shadow-2xs' : 'text-gray-600 hover:text-gray-900 hover:bg-gray-100/70'"
                                    class="p-2 rounded-xl transition-all flex items-center justify-center cursor-pointer relative group"
                                    title="Ayuda e Información"
                                    aria-label="Ayuda e Información">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                            </button>
                            <div x-show="openAyuda" x-transition.opacity style="display: none;"
                                 class="absolute right-0 mt-2 w-52 bg-white rounded-2xl shadow-xl py-2 z-50 border border-gray-100 text-left">
                                <a href="/mapa-del-sitio" class="px-4 py-2 text-xs font-bold text-gray-700 hover:bg-gray-50 flex items-center transition-colors">
                                    <svg class="w-4 h-4 mr-2 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7"/></svg>
                                    Mapa del Sitio
                                </a>
                                <a href="/acerca-de" class="px-4 py-2 text-xs font-bold text-gray-700 hover:bg-gray-50 flex items-center transition-colors">
                                    <svg class="w-4 h-4 mr-2 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                    Acerca de Nosotros
                                </a>
                                <a href="/politicas-privacidad" class="px-4 py-2 text-xs font-bold text-gray-700 hover:bg-gray-50 flex items-center transition-colors">
                                    <svg class="w-4 h-4 mr-2 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                                    Políticas de Privacidad
                                </a>
                            </div>
                        </div>
                    </div>

                    <!-- Cart Icon -->
                    <div x-data="{ role: localStorage.getItem('role') || 'guest' }">
                        <template x-if="role === 'guest' || role === 'cliente'">
                            <button x-data="cartBadge" @click="$dispatch('toggle-cart')" class="relative flex items-center space-x-2 p-2 hover:bg-gray-100 rounded-xl transition-colors cursor-pointer">
                                <div class="relative">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-gray-700" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" />
                                    </svg>
                                    <span x-show="count > 0" x-text="count" class="absolute top-0 right-0 inline-flex items-center justify-center px-1.5 py-0.5 text-[10px] font-black leading-none text-white transform translate-x-1/3 -translate-y-1/3 bg-[#E3001B] rounded-full shadow-2xs" id="cart-count" style="display: none;"></span>
                                </div>
                                <span x-show="total > 0" x-text="formatMoney(total)" class="font-extrabold text-xs text-gray-800 hidden sm:inline-block" style="display: none;"></span>
                            </button>
                        </template>
                    </div>

                    <!-- User Dropdown (Solo Iniciales) -->
                    <div x-data="userHeaderWidget()" class="relative ml-2 md:ml-4 flex items-center">
                        <template x-if="!token">
                            <a href="/auth/login" class="bg-[#E3001B] hover:bg-red-700 text-white px-4 py-2 rounded-xl font-bold text-xs transition-colors shadow-2xs">Login</a>
                        </template>
                        <template x-if="token">
                            <div class="relative">
                                <!-- Botón con Iniciales del Usuario -->
                                <button @click="dropdownOpen = !dropdownOpen" @click.away="dropdownOpen = false" 
                                        class="w-9 h-9 rounded-xl bg-slate-900 hover:bg-slate-800 text-white font-extrabold flex items-center justify-center text-xs shadow-2xs transition-all focus:outline-none cursor-pointer uppercase tracking-wider border-2 border-white"
                                        title="Opciones de cuenta">
                                    <span x-text="userInitials"></span>
                                </button>
                                
                                <div x-show="dropdownOpen" x-transition.opacity style="display: none;"
                                     class="absolute right-0 mt-2 w-52 bg-white rounded-2xl shadow-xl py-2 z-50 border border-gray-100">
                                    <div class="px-4 py-2 border-b border-gray-100">
                                        <p class="text-[10px] font-extrabold uppercase text-gray-400">Conectado como</p>
                                        <p class="text-xs font-bold text-gray-900 truncate" x-text="userNombre"></p>
                                        <span class="inline-block mt-1 px-2 py-0.5 rounded-full text-[9px] font-extrabold uppercase bg-gray-100 text-gray-700" x-text="roleLabel"></span>
                                    </div>

                                    <a href="/perfil" class="px-4 py-2 text-xs font-bold text-gray-700 hover:bg-gray-50 flex items-center transition-colors">
                                        <svg class="h-4 w-4 mr-2 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" /></svg>
                                        Mi Perfil
                                    </a>
                                    <a href="/perfil/password" class="px-4 py-2 text-xs font-bold text-gray-700 hover:bg-gray-50 flex items-center transition-colors">
                                        <svg class="h-4 w-4 mr-2 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z" /></svg>
                                        Cambiar Contraseña
                                    </a>
                                    <div class="border-t border-gray-100 my-1"></div>
                                    <button @click="logout" class="w-full text-left px-4 py-2 text-xs text-rose-600 hover:bg-rose-50 font-bold flex items-center transition-colors cursor-pointer">
                                        <svg class="h-4 w-4 mr-2 text-rose-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" /></svg>
                                        Cerrar Sesión
                                    </button>
                                </div>
                            </div>
                        </template>
                    </div>
                </div>
            </div>
        </div>

        <!-- Mobile Menu con indicador activo sutil -->
        <div x-show="mobileMenuOpen" class="md:hidden border-t border-gray-100" style="display: none;">
            <div x-data="{ 
                role: localStorage.getItem('role') || 'guest',
                currentPath: window.location.pathname,
                isActive(target) {
                    if (target === '/' || target === '/ecommerce/catalogo') {
                        return this.currentPath === '/' || this.currentPath.startsWith('/ecommerce/catalogo');
                    }
                    return this.currentPath === target || this.currentPath.startsWith(target);
                } 
            }" class="px-2 pt-2 pb-3 space-y-1 sm:px-3">
                
                <template x-if="role === 'guest' || role === 'cliente'">
                    <a href="/ecommerce/catalogo" 
                       :class="isActive('/ecommerce/catalogo') ? 'bg-red-50 text-[#E3001B] font-extrabold border border-red-100' : 'text-gray-700 hover:bg-gray-50 font-bold'"
                       class="block px-3 py-2 rounded-xl text-sm transition-all">Catálogo</a>
                </template>
                <template x-if="role === 'cliente'">
                    <a href="/ecommerce/historial" 
                       :class="isActive('/ecommerce/historial') ? 'bg-red-50 text-[#E3001B] font-extrabold border border-red-100' : 'text-gray-700 hover:bg-gray-50 font-bold'"
                       class="block px-3 py-2 rounded-xl text-sm transition-all">Mis Pedidos</a>
                </template>
                <template x-if="role === 'chofer'">
                    <a href="/entregas" 
                       :class="isActive('/entregas') ? 'bg-red-50 text-[#E3001B] font-extrabold border border-red-100' : 'text-gray-700 hover:bg-gray-50 font-bold'"
                       class="block px-3 py-2 rounded-xl text-sm transition-all">Mis Rutas</a>
                </template>
                <template x-if="role === 'admin' || role === 'administrador' || role === 'operador'">
                    <div class="space-y-1">
                        <a href="/dashboard" 
                           :class="isActive('/dashboard') ? 'bg-slate-900 text-white font-extrabold' : 'text-gray-700 hover:bg-gray-50 font-bold'"
                           class="block px-3 py-2 rounded-xl text-sm transition-all">Dashboard</a>
                        <div class="px-3 py-1 text-[11px] font-black uppercase text-gray-400">Administración de Ventas</div>
                        <a href="/gestion-pedidos" 
                           :class="isActive('/gestion-pedidos') ? 'bg-slate-900 text-white font-extrabold' : 'text-gray-700 hover:bg-gray-50 font-bold'"
                           class="block px-3 py-2 pl-6 rounded-xl text-sm transition-all">Gestión Pedidos</a>
                        <a href="/gestion-rutas" 
                           :class="isActive('/gestion-rutas') ? 'bg-slate-900 text-white font-extrabold' : 'text-gray-700 hover:bg-gray-50 font-bold'"
                           class="block px-3 py-2 pl-6 rounded-xl text-sm transition-all">Asignación Rutas</a>
                        <div class="px-3 py-1 text-[11px] font-black uppercase text-gray-400">Información Corporativa</div>
                        <a href="/mapa-del-sitio" 
                           :class="isActive('/mapa-del-sitio') ? 'bg-slate-900 text-white font-extrabold' : 'text-gray-700 hover:bg-gray-50 font-bold'"
                           class="block px-3 py-2 pl-6 rounded-xl text-sm transition-all">Mapa del Sitio</a>
                        <a href="/acerca-de" 
                           :class="isActive('/acerca-de') ? 'bg-slate-900 text-white font-extrabold' : 'text-gray-700 hover:bg-gray-50 font-bold'"
                           class="block px-3 py-2 pl-6 rounded-xl text-sm transition-all">Acerca de Nosotros</a>
                        <a href="/politicas-privacidad" 
                           :class="isActive('/politicas-privacidad') ? 'bg-slate-900 text-white font-extrabold' : 'text-gray-700 hover:bg-gray-50 font-bold'"
                           class="block px-3 py-2 pl-6 rounded-xl text-sm transition-all">Políticas de Privacidad</a>
                    </div>
                </template>
                <template x-if="role === 'admin' || role === 'administrador'">
                    <div class="space-y-1 pt-1 border-t border-gray-100">
                        <a href="/admin/usuarios" 
                           :class="isActive('/admin/usuarios') ? 'bg-slate-900 text-white font-extrabold' : 'text-gray-700 hover:bg-gray-50 font-bold'"
                           class="block px-3 py-2 rounded-xl text-sm transition-all">Usuarios</a>
                        <a href="/admin/camiones" 
                           :class="isActive('/admin/camiones') ? 'bg-slate-900 text-white font-extrabold' : 'text-gray-700 hover:bg-gray-50 font-bold'"
                           class="block px-3 py-2 rounded-xl text-sm transition-all">Camiones</a>
                    </div>
                </template>
            </div>
        </div>
    </nav>

    <!-- Banner de Bienvenida y Rol (Siempre Visible abajo de la Navbar) -->
    <div x-data="userHeaderWidget()" x-show="token" class="bg-gray-100/90 border-b border-gray-200/80 py-2.5 px-4 sm:px-6 lg:px-8">
        <div class="max-w-7xl mx-auto flex items-center justify-between">
            <div class="flex items-center gap-3">
                <div class="flex items-center gap-2 flex-wrap">
                    <span class="text-xs font-semibold text-gray-500">Bienvenido,</span>
                    <span class="text-xs font-bold text-gray-900" x-text="userNombre || 'Usuario'"></span>
                    <span class="text-gray-300 hidden sm:inline">•</span>
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[10px] font-extrabold uppercase tracking-wide"
                          :class="{
                              'bg-purple-100 text-purple-800 border border-purple-200': role === 'administrador' || role === 'admin',
                              'bg-blue-100 text-blue-800 border border-blue-200': role === 'operador',
                              'bg-amber-100 text-amber-800 border border-amber-200': role === 'chofer',
                              'bg-emerald-100 text-emerald-800 border border-emerald-200': role === 'cliente'
                          }"
                          x-text="roleLabel">
                    </span>
                </div>
            </div>

            <!-- Estado de Sesión -->
            <div class="hidden md:flex items-center text-xs text-gray-500 font-semibold gap-1.5 bg-white px-2.5 py-1 rounded-full border border-gray-200 shadow-2xs">
                <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
                <span>Sesión Activa</span>
            </div>
        </div>
    </div>

    <main class="flex-grow container mx-auto px-4 py-8">
        @yield('content')
    </main>

    <footer class="bg-slate-900 text-white py-8 mt-auto border-t border-slate-800">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 flex flex-col md:flex-row items-center justify-between gap-4 text-xs font-semibold text-gray-400">
            <div>
                <p>&copy; {{ date('Y') }} Fritolay Ambato. Todos los derechos reservados.</p>
            </div>
            <div class="flex items-center space-x-6">
                <a href="/mapa-del-sitio" class="hover:text-white transition-colors">Mapa del Sitio</a>
                <a href="/acerca-de" class="hover:text-white transition-colors">Acerca de Nosotros</a>
                <a href="/politicas-privacidad" class="hover:text-white transition-colors">Políticas de Privacidad</a>
            </div>
        </div>
    </footer>

    @include('ecommerce.catalogo._mini_carrito')

    <script>
        document.addEventListener('alpine:init', () => {
            Alpine.data('userHeaderWidget', () => ({
                token: localStorage.getItem('jwt_token'),
                role: localStorage.getItem('role') || 'guest',
                userNombre: localStorage.getItem('user_nombre') || '',
                dropdownOpen: false,

                get userInitials() {
                    if (!this.userNombre) return 'U';
                    const parts = this.userNombre.trim().split(' ');
                    if (parts.length >= 2) {
                        return (parts[0][0] + parts[1][0]).toUpperCase();
                    }
                    return parts[0].substring(0, 2).toUpperCase();
                },

                get roleLabel() {
                    const labels = {
                        'admin': 'Administrador',
                        'administrador': 'Administrador',
                        'operador': 'Operador',
                        'chofer': 'Chofer',
                        'cliente': 'Cliente'
                    };
                    return labels[this.role] || this.role;
                },

                async init() {
                    if (this.token && !this.userNombre) {
                        try {
                            const res = await window.api('/api/auth/me');
                            if (res && (res.user || res.nombre)) {
                                const userObj = res.user || res;
                                this.userNombre = userObj.nombre || userObj.email || 'Usuario';
                                this.role = userObj.rol || this.role;
                                localStorage.setItem('user_nombre', this.userNombre);
                                localStorage.setItem('role', this.role);
                            }
                        } catch (e) {
                            console.warn("No se pudo obtener la información del usuario activo.");
                        }
                    }
                }
            }));

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
        
        async function logout() {
            try {
                await window.api('/api/auth/logout', { method: 'POST' });
            } catch (_) {}
            localStorage.removeItem('jwt_token');
            localStorage.removeItem('role');
            localStorage.removeItem('user_nombre');
            document.cookie = "jwt_token=; expires=Thu, 01 Jan 1970 00:00:00 UTC; path=/;";
            window.location.href = '/auth/login';
        }

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
