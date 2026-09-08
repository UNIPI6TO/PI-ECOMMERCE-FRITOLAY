# 📋 Calidad de Software — Sistema E-commerce Fritolay Ambato

> **Proyecto:** Sistema E-commerce y Gestión de Pedidos "Fritolay Ambato"  
> **Versión del documento:** 1.0.0  
> **Fecha:** 2026-09-08  
> **Stack:** Laravel 11 / PHP 8.2 · MySQL 8.0 · Google Cloud Platform · Docker · GitHub Actions  

---

## 1. Introducción

Este documento describe las **normas, estándares y buenas prácticas de calidad de software** que fueron aplicadas durante el desarrollo del sistema Fritolay Ambato. El análisis cubre las dimensiones de arquitectura, seguridad, versionamiento, testing, infraestructura, base de datos y documentación.

> **Nota:** El proyecto no referencia explícitamente ninguna norma ISO en su documentación. Sin embargo, las características de calidad implementadas se alinean directamente con el modelo de calidad **ISO/IEC 25010:2011 (SQuaRE)**, que es la norma internacional más apropiada para clasificar y evaluar la calidad del producto software.

---

## 2. Normas de Calidad del Producto — ISO/IEC 25010

Aunque no se menciona por nombre en la especificación, el proyecto cubre implícitamente las siguientes **características de calidad** definidas por esta norma:

| Característica ISO 25010          | Evidencia en el Proyecto                                                                                                                          |
| :-------------------------------- | :------------------------------------------------------------------------------------------------------------------------------------------------ |
| **Adecuación Funcional**          | Historias de usuario con criterios de aceptación en Gherkin (HU-001 a HU-007), reglas de negocio formales (RN-XX), cobertura por módulo completa |
| **Fiabilidad**                    | Bloques `DB::transaction` en todos los servicios críticos → rollback automático ante fallos; MySQL 8.0 ACID compliant                             |
| **Seguridad**                     | JWT, RBAC por roles, cookies `HttpOnly`/`Secure`/`SameSite=Strict`, GCP Secret Manager, Cloud KMS, bcrypt/Argon2                                  |
| **Mantenibilidad**                | Principios SOLID aplicados strictly, Clean Architecture, DTOs, interfaces segregadas por dominio                                             |
| **Portabilidad**                  | Docker + Docker Compose para entorno local, Google Cloud Run para producción, entorno local garantizado sin instalaciones adicionales              |
| **Eficiencia de Desempeño**       | Caché de imágenes GCS configurable (4h por defecto), generación de PDF del lado del cliente, workers PHP configurables por variable de entorno    |
| **Compatibilidad**                | API REST estándar + PWA con Service Workers, soporte multiplataforma (web y móvil)                                                                |
| **Usabilidad**                    | Diseño minimalista, uso obligatorio de SweetAlert (prohibido `alert()` nativo), UX adaptable móvil/desktop, identidad corporativa Fritolay/Lays  |

---

## 3. Normas de Arquitectura y Diseño

### 3.1 Principios SOLID

Declarados explícitamente en el `README.md` y en la especificación del sistema. Se evidencian directamente en el código:

| Principio                                | Implementación                                                                                                                                              |
| :--------------------------------------- | :---------------------------------------------------------------------------------------------------------------------------------------------------------- |
| **S** – Single Responsibility Principle  | 18 Services independientes (`PedidoService`, `AuditoriaService`, `CierreService`, `EntregaService`…), cada uno con una única responsabilidad bien delimitada |
| **O** – Open/Closed Principle            | Los métodos de pago (`Efectivo`, `Depósito`, `De Una`, `TC`, `TD`) extienden una interfaz unificada sin modificar la lógica base del checkout                |
| **L** – Liskov Substitution Principle    | Las bodegas fijas y móviles (camiones) implementan `BodegaRepositoryInterface`, permitiendo transacciones de inventario consistentes e intercambiables        |
| **I** – Interface Segregation Principle  | 7 interfaces separadas por dominio: `PedidoRepositoryInterface`, `ProductoRepositoryInterface`, `GuiaRepositoryInterface`, `BodegaRepositoryInterface`…       |
| **D** – Dependency Inversion Principle   | Constructor injection vía contenedor IoC de Laravel: `private readonly PedidoRepositoryInterface $repo`, los controladores dependen de abstracciones          |

### 3.2 Repository Pattern + Clean Architecture

La aplicación está estructurada en capas con dependencias unidireccionales:

```
Contracts (Interfaces)
    └── Repositories (Implementaciones de acceso a datos)
            └── Services (Lógica de negocio)
                    └── Controllers (Presentación HTTP)
                            └── Requests (Validación de entrada)
```

Esta separación garantiza que los cambios en la infraestructura (base de datos, cloud) no afecten la lógica de negocio.

---

## 4. Normas de Seguridad — OWASP Top 10

El proyecto aplica defensas activas contra las vulnerabilidades más críticas listadas por OWASP:

| Vulnerabilidad OWASP               | Contramedida Implementada                                                                                   |
| :---------------------------------- | :---------------------------------------------------------------------------------------------------------- |
| **A01 – Broken Access Control**     | `RoleMiddleware` con 4 roles (`administrador`, `operador`, `chofer`, `cliente`); JWT obligatorio en rutas protegidas |
| **A02 – Cryptographic Failures**    | Passwords con bcrypt/Argon2; secretos en GCP Secret Manager; conexiones HTTPS; Cloud KMS para cifrado en reposo |
| **A03 – Injection (XSS / SQLi)**    | `SanitizeInputMiddleware` aplica `strip_tags` + `htmlspecialchars(ENT_QUOTES, 'UTF-8')` en todo input; Eloquent ORM/PDO con sentencias preparadas |
| **A05 – Security Misconfiguration** | Zero credenciales en código fuente; `config('jwt.secret')` en lugar de `env()` en ejecución; `config:cache` habilitado |
| **A07 – Authentication Failures**   | JWT con TTL dinámico por rol (1h estándar / 15 días con "Recuérdame"); cookies `HttpOnly`, `Secure`, `SameSite=Strict` |

### 4.1 Validación de Entradas

Se implementaron **21 clases `FormRequest`** de Laravel con reglas estrictas de validación:

- Tipos de datos, existencia en base de datos (`exists:tabla,campo`), formatos permitidos
- Archivos: validación de MIME types (`jpg`, `jpeg`, `png`, `pdf`) y tamaño máximo (2MB)
- Validaciones condicionales: comprobante obligatorio solo si el método de pago es `deposito` o `de_una`

---

## 5. Normas de Versionamiento y Control de Cambios

### 5.1 Semantic Versioning 2.0.0 (SemVer)

El `CHANGELOG.md` declara explícitamente:
> *"Este proyecto se adhiere a Semantic Versioning"*

**Historial de versiones:**

| Versión   | Fecha        | Contenido principal                                             |
| :-------- | :----------- | :-------------------------------------------------------------- |
| `v1.0.0`  | 2026-09-01   | Lanzamiento inicial — arquitectura base, autenticación JWT, módulos core |
| `v1.1.0`  | 2026-09-02   | E-commerce, geolocalización Haversine, carritos abandonados     |
| `v1.2.0`  | 2026-09-03   | PDF offloading, Snapshot Pattern fiscal (SRI), transacciones atómicas |
| `v1.3.0`  | 2026-09-03   | Cierre de guías, encerado de bodega, KPIs financieros           |
| `v1.4.0`  | 2026-09-03   | Dashboard administrativo, gráficos con granularidad dinámica    |
| `v1.5.0`  | 2026-09-05   | Rastreo GPS en tiempo real para clientes, notificaciones push nativas del SO |

El pipeline CI/CD genera automáticamente tags `vMAJOR.MINOR.REVISION` en `main` y `vMAJOR.MINOR.REVISION-SNAPSHOT` en ramas de desarrollo.

### 5.2 Conventional Commits

El archivo `.commitlintrc.json` extiende `@commitlint/config-conventional`, estandarizando todos los mensajes de commit con los siguientes tipos:

| Tipo         | Propósito                                         |
| :----------- | :------------------------------------------------ |
| `feat`       | Nueva funcionalidad                               |
| `fix`        | Corrección de errores                             |
| `docs`       | Cambios en documentación                          |
| `refactor`   | Reestructuración sin cambio de comportamiento     |
| `test`       | Adición o corrección de pruebas                   |
| `ci`         | Cambios en pipelines de CI/CD                     |
| `perf`       | Mejoras de rendimiento                            |
| `style`      | Ajustes de formato/estilo                         |
| `chore`      | Tareas de mantenimiento                           |
| `build`      | Cambios en el sistema de build                    |
| `revert`     | Reversión de un commit anterior                   |

### 5.3 Keep a Changelog

El formato del `CHANGELOG.md` sigue la especificación de [keepachangelog.com](https://keepachangelog.com/es-ES/1.1.0/), organizando los cambios por versión con etiquetas `Added`, `Fixed`, `Security`, `Refactor`.

---

## 6. Normas de Testing

### 6.1 PHPUnit — Pruebas Unitarias e Integración

La suite de pruebas está organizada en dos categorías:

**`tests/Feature/`** — Pruebas de integración por módulo:
- `Admin/` — Tests del panel administrativo
- `Auth/` — Tests de autenticación y sesiones
- `Checkout/` — Tests del proceso de compra
- `Ecommerce/` — Tests del catálogo y carrito
- `Entregas/` — Tests del módulo de entregas del chofer
- `GestionPedidos/` — Tests de asignación de rutas y guías

**`tests/Unit/`** — Pruebas unitarias:
- `Middleware/` — Tests de autenticación y sanitización
- `Services/` — Tests de lógica de negocio aislada

### 6.2 Behavior-Driven Development (BDD) — Gherkin

Todas las historias de usuario (HU-001 a HU-007) en `docs/especificacion.md` tienen criterios de aceptación escritos en formato **Gherkin** `Dado/Cuando/Entonces`, cubriendo:

- Flujo principal exitoso
- Escenarios de excepción y validación
- Condiciones de borde de negocio

### 6.3 Test Gate en CI/CD

El job `test-backend` es un **prerequisito obligatorio** del job `deploy-backend`. Ningún código llega a producción sin pasar la suite de pruebas completa.

---

## 7. Normas de DevOps e Infraestructura

### 7.1 Infrastructure as Code (IaC) — Terraform

Todos los recursos de GCP se gestionan declarativamente con módulos Terraform independientes:

| Módulo Terraform       | Recurso GCP Gestionado               |
| :--------------------- | :----------------------------------- |
| `infra/mysql/main.tf`  | Cloud SQL MySQL 8.0                  |
| `infra/firestore/main.tf` | Google Cloud Firestore            |
| `infra/gcs/main.tf`    | Buckets de Google Cloud Storage      |
| `infra/kms/main.tf`    | Cloud Key Management Service (KMS)   |
| `infra/sa/main.tf`     | Service Accounts e IAM               |

### 7.2 Multi-Stage Docker Builds

El `Dockerfile.backend` implementa construcción en dos etapas para minimizar el tamaño de imagen de producción:

- **Stage 1 (`composer_stage`):** Instala únicamente dependencias PHP de producción (`--no-dev --optimize-autoloader`)
- **Stage 2 (producción):** Imagen limpia que solo copia los artefactos compilados del Stage 1

### 7.3 Metodología 12-Factor App

| Factor                 | Implementación                                                        |
| :--------------------- | :-------------------------------------------------------------------- |
| **Configuración**      | 100% por variables de entorno (`.env`), nunca hardcodeada             |
| **Dependencias**       | Declaradas explícitamente en `composer.json` y `package.json`         |
| **Procesos**           | Contenedores stateless en Cloud Run                                   |
| **Paridad Dev/Prod**   | Docker Compose garantiza mismo entorno local y productivo             |
| **Logs**               | Salida a `stdout`/`stderr` (Laravel Log + Cloud Run logging)          |

### 7.4 GitOps — GitHub Actions

El repositorio cuenta con **11 workflows** de CI/CD separados por responsabilidad:

| Workflow                       | Propósito                                             |
| :----------------------------- | :---------------------------------------------------- |
| `ci-cd.yml`                    | Auto-tagging con Semantic Versioning                  |
| `backend-ci-cd.yml`            | Test → Build → Deploy del Backend API                 |
| `frontend-ci-cd.yml`           | Build → Deploy del Frontend/PWA                       |
| `deploy-mysql.yml`             | Provisioning de Cloud SQL                             |
| `deploy-firestore.yml`         | Provisioning de Firestore                             |
| `deploy-gcs.yml`               | Provisioning de buckets GCS                           |
| `deploy-kms.yml`               | Provisioning de Cloud KMS                             |
| `deploy-sa.yml`                | Provisioning de Service Accounts + IAM                |
| `deploy-secret-manager.yml`    | Gestión de secretos en Secret Manager                 |
| `deploy-artifact-registry.yml` | Registro de imágenes Docker                           |
| `deploy-firebase.yml`          | Despliegue de reglas Firestore                        |

---

## 8. Normas de Base de Datos

### 8.1 ACID — Transacciones Atómicas

Se implementan bloques `DB::transaction()` en todos los servicios que modifican múltiples tablas, garantizando rollback automático ante fallos:

- `PedidoService::crearPedido()` — Pedido + ítems + actualización de inventario
- `RutaService` — Guía de remisión + asignación de pedidos + transacción de bodega
- `CierreService` — Cierre de guía + encerado de bodega + actualización de inventario master
- `AprobacionService` — Cambio de estado de pedido + bitácora de auditoría

### 8.2 Soft Delete (Borrado Lógico)

Las direcciones de clientes **nunca se eliminan físicamente** de la base de datos. Se implementa un borrado lógico mediante el flag `estado = false`, preservando la integridad histórica de los pedidos asociados.

### 8.3 Snapshot Pattern — Inmutabilidad Fiscal

Los campos `nombre_producto` y `descripcion_producto` se **congelan en el momento de crear el pedido** dentro de la tabla `items_pedido`. Esto garantiza que las facturas y documentos fiscales del SRI no se alteren si el nombre del producto cambia posteriormente en el catálogo.

### 8.4 Auditoría con Bitácora

El `AuditoriaService` registra automáticamente en la tabla `bitacora_auditoria` cada acción sensible:

```
usuario_id | accion | tabla_afectada | registro_id | datos_anteriores | datos_nuevos | fecha_accion
```

---

## 9. Normas de Documentación

### 9.1 Especificación de Requisitos (IEEE 830 — implícito)

El documento `docs/especificacion.md` sigue la estructura de una **SRS (Software Requirements Specification)** con:

- Objetivos del proyecto (general y específicos)
- Actores involucrados y sus roles
- Procesos principales del sistema
- Reglas de negocio formales (`RN-A`, `RN-B`…)
- Historias de usuario con criterios Gherkin (`HU-001` a `HU-007`)
- Fórmulas de cálculo explícitas
- Excepciones del sistema
- Evidencias requeridas

### 9.2 Modelado UML 2.x

La especificación incluye diagramas UML completos del sistema:

- **Diagrama de Clases** — Estructura orientada a objetos con entidades y relaciones
- **Diagramas de Secuencia** — Flujos de interacción entre actores y el sistema
- **Diagrama de Casos de Uso** — Funcionalidades por actor
- **Modelo Entidad-Relación (ERD)** — Diccionario de datos y estructura de tablas

---

## 10. Resumen Ejecutivo

| Categoría                  | Norma / Estándar Aplicado                              | Estado     |
| :------------------------- | :----------------------------------------------------- | :--------- |
| Calidad del producto       | ISO/IEC 25010 (implícito)                              | ✅ Aplicado |
| Arquitectura               | Principios SOLID + Clean Architecture                  | ✅ Aplicado |
| Seguridad                  | OWASP Top 10                                           | ✅ Aplicado |
| Versionamiento             | Semantic Versioning 2.0.0 (SemVer)                     | ✅ Aplicado |
| Commits                    | Conventional Commits                                   | ✅ Aplicado |
| Changelog                  | Keep a Changelog                                       | ✅ Aplicado |
| Testing                    | PHPUnit + BDD/Gherkin                                  | ✅ Aplicado |
| Infraestructura como Código| Terraform (IaC)                                        | ✅ Aplicado |
| Contenedores               | Multi-Stage Docker Builds                              | ✅ Aplicado |
| Despliegue                 | 12-Factor App + GitOps (GitHub Actions)                | ✅ Aplicado |
| Base de datos              | ACID + Soft Delete + Snapshot Pattern                  | ✅ Aplicado |
| Documentación              | IEEE 830 (SRS implícito) + UML 2.x                     | ✅ Aplicado |

---

*Documento generado automáticamente mediante análisis del repositorio PI-ECOMMERCE-FRITOLAY.*
