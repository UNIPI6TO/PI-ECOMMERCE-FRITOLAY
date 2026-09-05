# 🍿 Sistema E-commerce y Gestión de Pedidos "Fritolay Ambato"

[![Fritolay CI/CD](https://github.com/UNIPI6TO/PI-ECOMMERCE-FRITOLAY/actions/workflows/ci-cd.yml/badge.svg)](https://github.com/UNIPI6TO/PI-ECOMMERCE-FRITOLAY/actions/workflows/ci-cd.yml)
[![PHP Version](https://img.shields.io/badge/PHP-8.2-777BB4?logo=php&logoColor=white)](https://www.php.net/)
[![Laravel](https://img.shields.io/badge/Laravel-11.x-FF2D20?logo=laravel&logoColor=white)](https://laravel.com/)
[![Docker](https://img.shields.io/badge/Docker-Ready-2496ED?logo=docker&logoColor=white)](https://www.docker.com/)
[![Google Cloud](https://img.shields.io/badge/GCP-Cloud_Run_|_Firestore_|_GCS-4285F4?logo=googlecloud&logoColor=white)](https://cloud.google.com/)

Plataforma integral web y PWA para la captación de pedidos e-commerce, gestión logística de distribución, control de inventario de bodegas fijas y móviles (camiones), emisión de guías de remisión bajo lineamientos SRI y rastreo GPS en tiempo real de unidades de reparto para **Fritolay Ambato**.

---

## 🚀 Tabla de Contenidos

1. [Descripción del Proyecto](#-descripción-del-proyecto)
2. [Stack Tecnológico](#-stack-tecnológico)
3. [Arquitectura del Sistema](#-arquitectura-del-sistema)
4. [Prerrequisitos](#-prerrequisitos)
5. [Quick Start (Desarrollo Local con Docker)](#-quick-start-desarrollo-local-con-docker)
6. [Estructura del Repositorio](#-estructura-del-repositorio)
7. [Variables de Entorno](#-variables-de-entorno)
8. [CI/CD y Secrets de GitHub](#-cicd-y-secrets-de-github)
9. [Principios SOLID y Buenas Prácticas](#-principios-solid-y-buenas-prácticas)
10. [Optimización con Ponytail para IA](#-optimización-con-ponytail-para-ia)
11. [Seguridad](#-seguridad)
12. [Registro de Cambios (Changelog)](#-registro-de-cambios-changelog)

---

## 📖 Descripción del Proyecto

El sistema **Fritolay Ambato** resuelve la cadena completa de valor de distribución de snacks y productos de consumo masivo:

- **E-commerce B2B/B2C (Módulo Clientes):** Catálogo dinámico con optimización de caché para imágenes desde Google Cloud Storage (GCS), carrito de compras reactivo, cálculo automático de IVA (15%), validación de stock disponible, rastreo en vivo de entregas en mapa Leaflet con notificaciones push nativas del SO, y sesión extendida de 15 días con opción "Recuérdame".
- **Gestión Logística y Despacho (Módulo Operador de Ruta):** Asignación de pedidos a camiones, generación automática de guías de remisión y notas de crédito en formato estándar del SRI (15 dígitos: `EST-PTO-SECUENCIAL`), guías de ruta, validación de comprobantes de pago (De Una, transferencias bancarias) y control de navegación con guardián de ruta.
- **Entregas y Liquidación (Módulo Chofer):** Visualización de ruta optimizada, integración directa con Google Maps y Waze, tracking GPS periódico hacia Cloud Firestore con partición diaria, emisión de estados de entrega y arqueo de caja con encerado de bodega móvil.
- **Métricas y Analítica (Dashboard Administrativo):** KPIs de efectividad de entrega, tiempos promedio, ventas por sector/camión, conciliación de recaudación multicanal y análisis de carritos abandonados.

---

## 🛠️ Stack Tecnológico

| Capa | Tecnologías |
| :--- | :--- |
| **Backend REST API** | Laravel 11, PHP 8.2, JWT Authentication, Eloquent ORM, PHPUnit |
| **Frontend Web / PWA** | Laravel Blade, Alpine.js, Tailwind CSS, Vite, PWA Service Workers |
| **Base de Datos Transaccional** | MySQL 8.0 (InnoDB, ACID compliant, UTF8MB4) |
| **Base de Datos NoSQL / GPS** | Google Cloud Firestore (Tracking en tiempo real de geolocalización) |
| **Almacenamiento de Objetos** | Google Cloud Storage (GCS) con caché de cliente (4h configurable) |
| **Seguridad y Criptografía** | GCP Secret Manager, Cloud KMS, Passwords con Bcrypt/Argon2, RBAC |
| **Infraestructura como Código** | Terraform (Módulos para MySQL, Firestore, GCS, KMS, Service Accounts) |
| **Contenedores y Orquestación**| Docker, Docker Compose v2, Google Cloud Run |
| **CI/CD** | GitHub Actions con Workload Identity Federation (WIF) |

---

## 🏛️ Arquitectura del Sistema

```
                         ┌─────────────────────────────────┐
                         │       Cliente / Navegador       │
                         │    (Laravel Blade + Alpine.js)  │
                         └───────────────┬─────────────────┘
                                         │ HTTP / REST
                                         ▼
                         ┌─────────────────────────────────┐
                         │       Backend REST API          │
                         │       (Laravel 11 / PHP 8.2)    │
                         └───────┬───────────────┬─────────┘
                                 │               │
                 ┌───────────────┴────┐     ┌────┴────────────────┐
                 │                    │     │                     │
                 ▼                    ▼     ▼                     ▼
        ┌─────────────────┐ ┌─────────────┐ ┌─────────────┐ ┌─────────────┐
        │     MySQL 8     │ │  Firestore  │ │ Google GCS  │ │Secret Mgr / │
        │ (Transaccional) │ │ (GPS Live)  │ │ (Imágenes)  │ │  Cloud KMS  │
        └─────────────────┘ └─────────────┘ └─────────────┘ └─────────────┘
```

---

## 📋 Prerrequisitos

Para ejecutar el entorno de desarrollo local completo, **únicamente necesitas**:

- **[Docker Desktop](https://www.docker.com/products/docker-desktop/)** (versión 24.0+ con Docker Compose v2).
- **Git** (para clonar el repositorio).

> 💡 **Nota:** No es obligatorio tener PHP, Composer, Node.js ni MySQL instalados en tu máquina local. Todos los servicios corren dentro de contenedores Docker aislados y preconfigurados.

---

## ⚡ Quick Start (Desarrollo Local con Docker)

Sigue estos sencillos pasos para levantar todo el ecosistema en menos de 2 minutos:

```bash
# 1. Clona el repositorio si aún no lo has hecho
git clone https://github.com/UNIPI6TO/PI-ECOMMERCE-FRITOLAY.git
cd PI-ECOMMERCE-FRITOLAY

# 2. Copia el archivo de variables de entorno de ejemplo
cp .env.example .env

# 3. (Opcional) Edita .env si deseas personalizar puertos o credenciales locales

# 4. Levanta todos los servicios con Docker Compose
docker-compose -f infra/docker-compose.yml up -d
```

### 🌐 Servicios Disponibles

Una vez iniciados los contenedores, puedes acceder a:

- **Frontend (Web / PWA):** [http://localhost:8080](http://localhost:8080)
- **Backend (REST API):** [http://localhost:8000](http://localhost:8000)
- **MySQL 8:** `localhost:3307` (Base de datos: `fritolay_db`, Usuario: `fritolay`, Password: `dev_password_change_me`)

### 🛑 Comandos Útiles de Docker

```bash
# Ver logs en tiempo real
docker-compose -f infra/docker-compose.yml logs -f

# Detener los contenedores
docker-compose -f infra/docker-compose.yml down

# Reconstruir las imágenes si hay cambios en Dockerfiles
docker-compose -f infra/docker-compose.yml up -d --build
```

---

## 📂 Estructura del Repositorio

```
PI-ECOMMERCE-FRITOLAY/
├── .github/
│   └── workflows/
│       └── ci-cd.yml             # Pipeline de CI/CD automatizado con GitHub Actions
├── backend/                      # API REST construida en Laravel 11 / PHP 8.2
│   ├── app/                      # Controladores, Modelos, Servicios, Middleware, DTOs
│   ├── config/                   # Configuraciones de Laravel
│   ├── database/                 # Migraciones, seeders y factories
│   ├── routes/                   # Definición de rutas API protegidas con JWT
│   └── tests/                    # Tests unitarios y de integración con PHPUnit
├── frontend/                     # Aplicación Web y PWA (Laravel Blade + Alpine.js)
│   ├── app/                      # Controladores y lógica de presentación
│   ├── resources/
│   │   ├── css/                  # Estilos Tailwind CSS
│   │   ├── js/                   # Componentes Alpine.js y scripts
│   │   └── views/                # Vistas Blade (Catálogo, Checkout, Dashboards, Guías)
│   └── routes/                   # Rutas web del frontend
├── infra/                        # Infraestructura como Código (IaC) y Docker
│   ├── Dockerfile.backend        # Imagen de producción para el Backend
│   ├── Dockerfile.frontend       # Imagen multi-stage de producción para el Frontend
│   ├── docker-compose.yml        # Orquestación de entorno local (Backend, Frontend, MySQL)
│   ├── firestore/main.tf         # Módulo Terraform para Google Cloud Firestore
│   ├── gcs/main.tf               # Módulo Terraform para buckets de Cloud Storage
│   ├── kms/main.tf               # Módulo Terraform para Cloud Key Management Service
│   ├── mysql/main.tf             # Módulo Terraform para Cloud SQL MySQL
│   └── sa/main.tf                # Módulo Terraform para Service Accounts y permisos IAM
├── docs/                         # Documentación y especificaciones funcionales
│   └── especificacion.md         # Documento maestro de requerimientos
├── .env.example                  # Plantilla de variables de entorno para desarrollo local
├── .gitignore                    # Reglas de exclusión de archivos para Git
└── README.md                     # Documentación principal del proyecto
```

---

## ⚙️ Variables de Entorno

El archivo `.env.example` centraliza la configuración requerida para el funcionamiento de los microservicios:

| Variable | Descripción | Valor por defecto (Local) |
| :--- | :--- | :--- |
| `DB_DATABASE` | Nombre de la base de datos MySQL | `fritolay_db` |
| `DB_USERNAME` | Usuario de MySQL | `fritolay` |
| `DB_PASSWORD` | Contraseña del usuario MySQL | `dev_password_change_me` |
| `MYSQL_ROOT_PASSWORD` | Contraseña root de MySQL | `root_dev_change_me` |
| `JWT_SECRET` | Clave secreta para firma de tokens JWT | *(Clave de desarrollo >= 32 chars)* |
| `JWT_EXPIRY_MINUTES` | Tiempo de expiración del token JWT | `60` |
| `GCS_BUCKET_IMAGENES` | Bucket GCS para catálogo de productos | `fritolay-imagenes-dev` |
| `GCS_BUCKET_COMPROBANTES`| Bucket GCS para comprobantes de pago | `fritolay-comprobantes-dev` |
| `GCS_IMAGE_CACHE_HOURS` | Tiempo de caché en navegador de imágenes | `4` |
| `FIREBASE_PROJECT_ID` | ID del proyecto Firebase/Firestore | *(Opcional en local)* |
| `GPS_UPDATE_INTERVAL_SECONDS` | Frecuencia de envío de telemetría GPS | `5` |
| `MAIL_HOST` | Host SMTP para recuperación de contraseñas | `smtp.gmail.com` |
| `MAIL_PORT` | Puerto del servidor SMTP | `587` |
| `PIN_DIGITS` | Longitud del PIN OTP de recuperación | `6` |
| `IVA_PORCENTAJE` | Porcentaje de IVA legal aplicado | `15` |
| `STOCK_ALERT_THRESHOLD_PERCENT` | Porcentaje para alerta de stock bajo | `10` |

---

## 🔄 CI/CD y Secrets de GitHub

El proyecto cuenta con un flujo automatizado en `.github/workflows/ci-cd.yml` que ejecuta:

1. **Job `test-backend`:** Levanta un contenedor de servicio MySQL 8.0, prepara el entorno PHP 8.2, instala dependencias con Composer y corre la suite completa de pruebas unitarias e integración (`php artisan test`).
2. **Job `build-backend`:** Se autentica en Google Cloud Platform sin claves estáticas usando **Workload Identity Federation (WIF)**, construye la imagen Docker del backend, la publica en **Google Artifact Registry** y la despliega en **Google Cloud Run**.
3. **Job `build-frontend`:** Construye los assets frontend (Node/Vite) y la imagen Docker, la publica en Artifact Registry y despliega el servicio en Cloud Run.

### 🔐 Secrets Requeridos en GitHub Actions

Configura los siguientes secrets en el repositorio (**Settings > Secrets and variables > Actions**):

| Secret | Descripción | Ejemplo |
| :--- | :--- | :--- |
| `WIF_PROVIDER` | Pool de Workload Identity Provider de GCP | `projects/123456/locations/global/workloadIdentityPools/github-pool/providers/github-provider` |
| `WIF_SERVICE_ACCOUNT`| Cuenta de servicio para GitHub Actions | `github-actions-deployer@project-id.iam.gserviceaccount.com` |
| `GCP_PROJECT_ID` | ID del proyecto de Google Cloud | `fritolay-ambato-prod` |
| `GCP_REGION` | Región para Artifact Registry y Cloud Run | `us-central1` |
| `JWT_SECRET` | Clave secreta JWT administrada en Secret Manager | `prod_jwt_super_secret_key_...` |
| `DB_PASSWORD` | Contraseña de producción de Cloud SQL | `prod_strong_sql_password_...` |

---

## 🧱 Principios SOLID y Buenas Prácticas

El código de la aplicación está diseñado aplicando estrictamente los **Principios SOLID** y patrones de **Clean Architecture**:

- **S - Single Responsibility Principle (SRP):** Cada controlador, servicio y DTO tiene un único propósito. Por ejemplo, la liquidación de guías y el encerado de bodega móvil están desacoplados de la generación de comprobantes SRI.
- **O - Open/Closed Principle (OCP):** Los métodos de pago (`Efectivo`, `De Una`, `Transferencia`, `Tarjeta`) extienden una interfaz unificada de procesamiento sin modificar la lógica base del checkout.
- **L - Liskov Substitution Principle (LSP):** Las bodegas fijas y móviles (camiones) implementan contratos comunes de inventario (`InventoryRepositoryInterface`), permitiendo transacciones consistentes.
- **I - Interface Segregation Principle (ISP):** Se definen interfaces específicas para geolocalización, facturación y autenticación, evitando que las clases dependan de métodos que no utilizan.
- **D - Dependency Inversion Principle (DIP):** Los controladores dependen de abstracciones (interfaces y servicios inyectados en el contenedor de Laravel), no de implementaciones concretas de base de datos o almacenamiento en nube.

---

## 🪮 Optimización con Ponytail para IA

Para el desarrollo colaborativo y construcción de características con agentes de Inteligencia Artificial (LLMs), se utiliza **[Ponytail](https://github.com/dietrichgebert/ponytail)**.

- **Objetivo:** Empaquetar y optimizar la estructura del código del proyecto en archivos de contexto concisos y de bajo consumo de tokens.
- **Beneficio:** Permite a los agentes de IA comprender la arquitectura completa, modelos, servicios y rutas con la máxima precisión, evitando alucinaciones y respetando las directrices de seguridad y diseño establecidas.

---

## 🛡️ Seguridad

- **Protección contra Inyecciones:** Todas las consultas a base de datos utilizan sentencias preparadas mediante Eloquent ORM / PDO, previniendo ataques de SQL Injection.
- **Sanitización de Entradas:** Validación estricta en Form Requests de Laravel y saneamiento contra Cross-Site Scripting (XSS).
- **Gestión de Secretos:** Cero credenciales sensibles en el código fuente. En producción se integran mediante **Google Cloud Secret Manager** y **Cloud KMS**.
- **Control de Acceso Basado en Roles (RBAC):** Middleware de autorización para roles `Administrador`, `Operador de Ruta`, `Chofer` y `Cliente`.
- **Cookies y Sesiones:** Flags `Secure`, `HttpOnly` y `SameSite=Strict` habilitados para la persistencia temporal del carrito y tokens.

---

## 📜 Registro de Cambios (Changelog)

Para consultar el historial detallado de versiones, características implementadas, refactorizaciones y correcciones en cada commit, revisa el archivo [CHANGELOG.md](file:///d:/UNIANDES/8VO/HERRAMIENTAS%20DE%20DESARROLLO%20DE%20SOFTWARE/PI-ECOMMERCE-FRITOLAY/CHANGELOG.md).

