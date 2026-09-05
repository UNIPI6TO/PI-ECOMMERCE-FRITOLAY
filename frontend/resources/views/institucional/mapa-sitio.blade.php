@extends('layouts.app')

@section('title', 'Mapa del Sitio - Fritolay Ambato')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8" x-data="mapaSitioApp()">
    <!-- Encabezado -->
    <div class="mb-8 border-b border-gray-200 pb-5">
        <div class="flex items-center gap-3">
            <div class="p-3 bg-red-50 text-[#E3001B] rounded-2xl border border-red-100">
                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7"/>
                </svg>
            </div>
            <div>
                <h1 class="text-3xl font-black text-slate-900 tracking-tight">Mapa del Sitio</h1>
                <p class="text-sm font-semibold text-gray-500 mt-0.5">
                    Navegación dinámica ajustada automáticamente a tu rol activo (<span class="text-[#E3001B] font-extrabold" x-text="roleLabel"></span>).
                </p>
            </div>
        </div>
    </div>

    <!-- Secciones Dinámicas -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        <template x-for="cat in modulosPermitidos" :key="cat.titulo">
            <div class="bg-white rounded-2xl p-6 border border-gray-100 shadow-2xs hover:shadow-md transition-all flex flex-col justify-between">
                <div>
                    <div class="flex items-center gap-3 mb-4">
                        <div class="p-2.5 rounded-xl bg-slate-900 text-white font-extrabold text-xs" x-html="cat.icono"></div>
                        <div>
                            <h3 class="font-black text-slate-900 text-base" x-text="cat.titulo"></h3>
                            <p class="text-xs text-gray-400 font-medium" x-text="cat.descripcion"></p>
                        </div>
                    </div>

                    <div class="space-y-2 border-t border-gray-100 pt-3">
                        <template x-for="item in cat.items" :key="item.url">
                            <a :href="item.url" 
                               class="group flex items-center justify-between p-2.5 rounded-xl hover:bg-red-50/60 transition-colors border border-transparent hover:border-red-100">
                                <div class="flex items-center gap-2.5">
                                    <span class="w-2 h-2 rounded-full bg-gray-300 group-hover:bg-[#E3001B] transition-colors"></span>
                                    <span class="text-xs font-bold text-gray-700 group-hover:text-slate-900" x-text="item.nombre"></span>
                                </div>
                                <svg class="w-4 h-4 text-gray-400 group-hover:text-[#E3001B] group-hover:translate-x-0.5 transition-all" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                                </svg>
                            </a>
                        </template>
                    </div>
                </div>

                <div class="mt-4 pt-3 border-t border-gray-100 text-[10px] font-extrabold uppercase text-gray-400 text-right" x-text="cat.items.length + ' enlaces disponibles'"></div>
            </div>
        </template>
    </div>
</div>

<script>
    document.addEventListener('alpine:init', () => {
        Alpine.data('mapaSitioApp', () => ({
            role: localStorage.getItem('role') || 'guest',

            get roleLabel() {
                const map = {
                    'admin': 'Administrador',
                    'administrador': 'Administrador',
                    'operador': 'Operador',
                    'chofer': 'Chofer / Conductor',
                    'cliente': 'Cliente Registrado',
                    'guest': 'Invitado'
                };
                return map[this.role] || this.role;
            },

            // Definición central del menú y permisos
            get modulosPermitidos() {
                const todos = [
                    {
                        titulo: 'E-Commerce y Tienda',
                        descripcion: 'Catálogo de productos, carrito de compra e historial.',
                        icono: '<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/></svg>',
                        roles: ['guest', 'cliente', 'admin', 'administrador', 'operador'],
                        items: [
                            { nombre: 'Catálogo de Productos', url: '/ecommerce/catalogo' },
                            { nombre: 'Historial de Pedidos', url: '/ecommerce/historial', roles: ['cliente', 'admin', 'administrador'] }
                        ]
                    },
                    {
                        titulo: 'Gestión y Analítica',
                        descripcion: 'Métricas de ventas, efectividad y control de pérdidas.',
                        icono: '<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>',
                        roles: ['admin', 'administrador', 'operador'],
                        items: [
                            { nombre: 'Dashboard Gerencial', url: '/dashboard' }
                        ]
                    },
                    {
                        titulo: 'Administración de Ventas',
                        descripcion: 'Gestión integral de órdenes, rutas y arqueo de caja.',
                        icono: '<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>',
                        roles: ['admin', 'administrador', 'operador'],
                        items: [
                            { nombre: 'Gestión de Pedidos', url: '/gestion-pedidos' },
                            { nombre: 'Asignación de Rutas', url: '/gestion-rutas' },
                            { nombre: 'Cierre de Guías', url: '/admin/cierre-guias' }
                        ]
                    },
                    {
                        titulo: 'Módulo de Entregas',
                        descripcion: 'Rutas asignadas, GPS y liquidación en camionetas.',
                        icono: '<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 4H6a2 2 0 00-2 2v12a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2h-2m-4-1v8m0 0l3-3m-3 3L9 8"/></svg>',
                        roles: ['chofer', 'admin', 'administrador'],
                        items: [
                            { nombre: 'Mis Rutas de Entrega', url: '/entregas' }
                        ]
                    },
                    {
                        titulo: 'Administración del Sistema',
                        descripcion: 'Control de usuarios, flota de vehículos y permisos.',
                        icono: '<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>',
                        roles: ['admin', 'administrador'],
                        items: [
                            { nombre: 'Gestión de Usuarios', url: '/admin/usuarios' },
                            { nombre: 'Flota - Camiones', url: '/admin/camiones' },
                            { nombre: 'Flota - Ubicaciones (GPS)', url: '/admin/flota/ubicaciones' }
                        ]
                    },
                    {
                        titulo: 'Cuenta y Ajustes',
                        descripcion: 'Perfil personal, seguridad y autenticación.',
                        icono: '<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>',
                        roles: ['guest', 'cliente', 'chofer', 'operador', 'admin', 'administrador'],
                        items: [
                            { nombre: 'Mi Perfil', url: '/perfil', roles: ['cliente', 'chofer', 'operador', 'admin', 'administrador'] },
                            { nombre: 'Cambiar Contraseña', url: '/perfil/password', roles: ['cliente', 'chofer', 'operador', 'admin', 'administrador'] },
                            { nombre: 'Iniciar Sesión', url: '/auth/login', roles: ['guest'] },
                            { nombre: 'Registrarse', url: '/auth/registro', roles: ['guest'] }
                        ]
                    },
                    {
                        titulo: 'Información Institucional',
                        descripcion: 'Políticas legales, empresa y mapa del sitio.',
                        icono: '<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>',
                        roles: ['guest', 'cliente', 'chofer', 'operador', 'admin', 'administrador'],
                        items: [
                            { nombre: 'Acerca de Nosotros', url: '/acerca-de' },
                            { nombre: 'Políticas de Privacidad', url: '/politicas-privacidad' },
                            { nombre: 'Mapa del Sitio (Actual)', url: '/mapa-del-sitio' }
                        ]
                    }
                ];

                return todos.filter(cat => {
                    const tieneRolCat = cat.roles.includes(this.role);
                    if (!tieneRolCat) return false;

                    // Filtrar items individuales por rol si aplica
                    cat.items = cat.items.filter(it => !it.roles || it.roles.includes(this.role));
                    return cat.items.length > 0;
                });
            }
        }));
    });
</script>
@endsection
