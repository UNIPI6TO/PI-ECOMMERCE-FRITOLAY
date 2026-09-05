# 📜 Changelog - Sistema Fritolay Ambato

Todos los cambios notables en este proyecto serán documentados en este archivo. El formato está basado en [Keep a Changelog](https://keepachangelog.com/es-ES/1.1.0/) y este proyecto se adhiere a [Semantic Versioning](https://semver.org/lang/es/).

---

## [Unreleased]

---

## [1.5.0] - 2026-09-05

### 🚚 Rastreo de Entrega en Vivo para Clientes & Notificaciones Push Nativas del SO
- **Feat (Rastreo Cliente Live):** Implementada la vista `/ecommerce/rastreo` con mapa interactivo Leaflet para clientes con pedidos en estado `en_ruta`. Muestra la ubicación GPS en tiempo real del chofer, marcador del destino de entrega y cálculo dinámico de distancia restante por fórmula Haversine.
- **Feat (Notificaciones Push del SO):** Integración de alertas nativas del SO mediante `ServiceWorkerRegistration.showNotification()` y Web Notifications API. Al activarse o hacer clic en la notificación del sistema operativo, el usuario es redirigido automáticamente a la vista de rastreo en vivo de la entrega (`/ecommerce/rastreo`).
- **Feat (Guardián de Navegación & Expulsión):** Implementada protección contra el botón "Atrás" (`history.replaceState`) y redirección automática/expulsión hacia la vista de historial (`/ecommerce/historial`) una vez que el pedido es entregado o finalizado.
- **Feat (Botón Reactivo Navbar):** Agregado botón animado *"🚚 Ver entrega"* en el Navbar para clientes autenticados que posean un pedido activo en ruta (`/api/clientes/entrega-activa`).

### 📑 Formato Fiscal SRI & Correcciones en Historial de Pedidos
- **Fix (Notas de Crédito SRI):** Estandarizada la codificación de notas de crédito en backend (`NotaCredito::generarNumero`) y frontend (`pdf-generator.js`) al formato oficial de 15 dígitos exigido por el SRI: `EST-PTO-SECUENCIAL` (ej. `003-001-000000037`).
- **Fix (Historial de Clientes):** Corregida la condición `x-if` en el modal de detalle de `historial.blade.php` para asegurar que los pedidos con estado `ENTREGADO` no muestren por error alertas o banners de devolución/cancelación.
- **Feat (Puntos de Control Firestore):** Emisión de eventos de entrega (`Entregado` y `Entregado (Parcial)`) en Firestore al registrar la entrega, permitiendo visualizarlos con insignias y marcadores de color en el Timeline de Flota.

### 🔒 Autenticación, Títulos & Seguridad
- **Fix (Duración Recuérdame):** Ajustada la función `AuthService::calculateTtl` para otorgar un token JWT con vigencia de **15 días (1,296,000 segundos)** únicamente cuando el usuario marca la casilla *"Recuérdame"*. Si no la marca, se mantiene la sesión estándar de 1 hora.
- **Style (Títulos de Ventana):** Estandarizadas las etiquetas `<title>` en `app.blade.php` y todas las vistas hijas bajo el formato unificado `Fritolay - VISTA` (ej. `Fritolay - Catálogo`, `Fritolay - Rastreo de Entrega`).
- **Security (Hardening GCP):** Eliminación total de credenciales en duro en `firebase-config.js` y sincronización con GCP Secret Manager (`fritolay-frontend-env`, `fritolay-backend-env`).

---

## [Unreleased] - 2026-09-03

### 📌 Módulos Recientes e Institucionales
- **Feat (Institucional):** Agregadas vistas corporativas dinámicas: Mapa del Sitio (`/mapa-del-sitio`), Acerca de Nosotros (`/acerca-de`) y Políticas de Privacidad (`/politicas-privacidad`).
- **Feat (UI/Navbar):** Agregado menú desplegable *"Ayuda / Info"* en el Navbar principal y enlaces institucionales globales en el Footer.
- **Docs:** Actualización integral de la especificación técnica (`docs/especificacion.md`), incluyendo Diccionario de Datos (`NOTAS_CREDITO`, `TRANSACCIONES_INVENTARIO`, `CARRITOS_ABANDONADOS`, `DESCUENTOS` y atributo `valor_entrega`).

---

## [1.4.0] - 2026-09-03

### 📊 Dashboard Administrativo & Formato Financiero
- **Format (Dashboard):** Estandarizados los helpers `window.formatMoney()` y `window.formatNumber()` con `Intl.NumberFormat('en-US')` para incluir separadores de miles (comas `,`) y decimales (puntos `.`) en todos los KPIs y tablas.
- **Feat (Dashboard):** Nuevos KPIs logísticos y financieros:
  - Tarjetas de conteo por estado de guías (*Abiertas, Cerradas, Aprobadas*).
  - Totalizadores de stock por Marca y Categoría.
  - Valorización del Capital Inmovilizado en Bodega Central y Camiones.
- **Feat (Timeline Charts):** Granularidad dinámica (Horaria para $\le 2$ días, Diaria para $\ge 3$ días) en gráficos de ventas y pérdidas del Dashboard.

---

## [1.3.0] - 2026-09-03

### 🚚 Administración de Ventas, Cierre de Guías & Inventario
- **Feat (Cierre de Guías):** Creada la vista de Cierre de Guías (`/admin/cierre-guias`) dentro del nuevo menú *"Administración de Ventas"*.
- **Feat (Inventario/Cierre):** Al aprobar la revisión de una guía (`POST /api/cierre-guias/{id}/aprobar-revision`), el sistema reduce automáticamente las unidades entregadas de `en_pedidos` en la bodega master y genera un egreso en `transacciones_inventario`.
- **Style (Filtro de Fechas):** Rediseñado el filtro de fechas en Cierre de Guías con botones de presets (*Hoy, Última Semana, Último Mes*), estableciendo por defecto **1 semana** de consulta y eliminando la opción de limpiar filtros.
- **Feat (UI/Detalle):** Agregada la sección *"Detalle de Devolución"* en el modal de detalle del pedido para visualizar productos devueltos, cantidades y motivo global de la entrega.
- **Refactor (Financiero):** Formalizado el atributo `valor_entrega` en `pedidos` para asegurar la paridad exacta en el arqueo de caja del chofer (`Total - NC SRI = Valor Entrega`).

---

## [1.2.0] - 2026-09-03

### 📑 Documentos Fiscales, PDF & Inmutabilidad (Snapshot Pattern)
- **Feat (Snapshots):** Congelamiento estático de `nombre_producto` y `descripcion_producto` en `items_pedido` al crear órdenes para garantizar la inmutabilidad fiscal del SRI.
- **Feat (PDF Offloading):** Generación de Facturas, Guías de Remisión y Notas de Crédito desacoplada del servidor, procesada exclusivamente en el navegador del cliente mediante `jsPDF` / `pdfmake`.
- **Feat (Atomic Transactions):** Bloques `DB::transaction` implementados en todos los servicios (`PedidoService`, `CierreService`, `AprobacionService`, `RutaService`) para garantizar rollback automático ante fallos.
- **Feat (Notas de Crédito):** Creación automática de `NotaCredito` al anular pedidos facturados y despliegue del desglose SRI en el historial del cliente.

---

## [1.1.0] - 2026-09-02

### 🛍️ E-commerce & Experiencia del Cliente
- **Feat (Geolocalización):** Integración de autocompletado con fórmula Haversine para ordenar direcciones sugeridas por cercanía y Reverse Geocoding en mapa Leaflet.
- **Feat (Carritos Abandonados):** Registro automático de carritos vaciados manualmente en `carritos_abandonados` con justificación y valor total.
- **Feat (Seguridad/Cookies):** Implementación de cookies seguras (`HttpOnly`, `SameSite=Strict`) y expiración de sesión dinámica (TTL por rol: Administrador 8h, Chofer 12h, Cliente 30 días).

---

## [1.0.0] - 2026-09-01

### 🚀 Lanzamiento Inicial (Core Baseline)
- Arquitectura de microservicios desarticulados: Backend Laravel 11 API REST, Frontend Laravel Blade + Alpine.js, MySQL 8.0 y Google Cloud Firestore.
- Módulos base: Autenticación JWT, Catálogo e-commerce, Asignación de Rutas a Camiones, Módulo de Entregas del Chofer y Control de Flota.
- CI/CD automatizado con GitHub Actions y desplegable en Google Cloud Run.
