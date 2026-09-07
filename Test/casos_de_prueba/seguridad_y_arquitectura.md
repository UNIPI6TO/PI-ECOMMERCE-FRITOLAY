# 🧪 Especificación de Casos de Prueba - Seguridad, Arquitectura y Principios SOLID

**Sección:** Seguridad, Gestión de Estado, Environment y Arquitectura  
**Referencia:** `docs/especificacion.md` (§ 3, § 5.4, Principios SOLID)

---

## 📌 CP-SEC-001: TTL Dinámico de Sesión JWT sin "Recuérdame" (Expiración en 1 hora)

- **Tipo:** Seguridad (JWT / Token TTL)
- **Componente:** `JwtMiddleware` / `AuthController`
- **Precondiciones:**
  1. Usuario inicia sesión sin marcar la casilla "Recuérdame".

- **Escenario Gherkin:**
  ```gherkin
  Dado que un usuario (Administrador, Operador, Chofer o Cliente) inicia sesión sin activar "Recuérdame"
  Cuando el sistema genera el token JWT
  Entonces el TTL del token se configura exactamente en 3,600 segundos (1 hora)
  Y transcurridos 60 minutos, cualquier petición posterior retorna HTTP 401 Unauthorized.
  ```

- **Asserts / Verificaciones Esperadas:**
  - Token payload `exp` - `iat` == `3600`.
  - HTTP `401 Unauthorized` al expirar.

---

## 📌 CP-SEC-002: TTL Dinámico de Sesión JWT con "Recuérdame" Activo (Expiración en 15 días)

- **Tipo:** Seguridad (JWT / Persistent Session)
- **Componente:** `JwtMiddleware` / `AuthController`
- **Precondiciones:**
  1. Usuario inicia sesión marcando la opción "Recuérdame" (`remember_me`: `true`).

- **Escenario Gherkin:**
  ```gherkin
  Dado que el usuario inicia sesión activando la opción "Recuérdame"
  Cuando el sistema emite el JWT
  Entonces el token asignado tiene una vigencia extendida de 1,296,000 segundos (15 días)
  Y la sesión se mantiene activa en el navegador durante ese periodo.
  ```

- **Asserts / Verificaciones Esperadas:**
  - Token payload `exp` - `iat` == `1296000`.

---

## 📌 CP-SEC-003: Sanitización contra Inyecciones SQL y ataques Cross-Site Scripting (XSS)

- **Tipo:** Seguridad (Sanitización API)
- **Componente:** `SanitizeInputMiddleware` / Form Requests
- **Precondiciones:**
  1. Petición POST o PUT enviada a cualquier endpoint REST API del backend.

- **Datos de Entrada (Ataque XSS & SQLi payload):**
  ```json
  {
    "nombre": "Juan <script>alert('xss')</script>",
    "razon_social": "Empresa ' OR '1'='1"
  }
  ```

- **Escenario Gherkin:**
  ```gherkin
  Dado un atacante intentando enviar código scripts XSS o fragmentos SQL Injection en las entradas REST API
  Cuando el `SanitizeInputMiddleware` procesa la petición
  Entonces los caracteres especiales son escapados o desinfectados antes de llegar al controlador/ORM
  Y la consulta a la base de datos se ejecuta mediante sentencias preparadas (Eloquent/PDO) impidiendo la inyección.
  ```

- **Asserts / Verificaciones Esperadas:**
  - No execution of script tags in rendered Blade views.
  - Eloquent/PDO prepared statements execute parameterized query cleanly.

---

## 📌 CP-SEC-004: Borrado Lógico (Soft Delete) en Direcciones de Cliente

- **Tipo:** Arquitectura / Regla de Negocio § 5.4
- **Componente:** `DireccionClienteController` / `DireccionCliente` Model
- **Precondiciones:**
  1. Cliente posee una dirección guardada con ID `15`.

- **Escenario Gherkin:**
  ```gherkin
  Dado que el cliente presiona la opción de eliminar una de sus direcciones guardadas
  Cuando el sistema procesa la solicitud
  Entonces NO se ejecuta una sentencia DELETE física en la base de datos
  Y el campo `estado` (o `deleted_at`) cambia a desactivado (`false` / `timestamp`), preservando el histórico para pedidos pasados.
  ```

- **Asserts / Verificaciones Esperadas:**
  - Registro con ID `15` sigue existiendo en la tabla `direcciones_clientes`.
  - Columna `estado` == `false`.

---

## 📌 CP-SEC-005: Estándar de Notificaciones UI mediante SweetAlert (Prohibición de `alert()` nativo)

- **Tipo:** UI/UX & Calidad
- **Componente:** Frontend JavaScript Assets
- **Escenario Gherkin:**
  ```gherkin
  Dado cualquier formulario o acción del usuario en el frontend (validaciones, errores, confirmaciones)
  Cuando se activa una alerta o mensaje de respuesta
  Entonces el sistema utiliza exclusivamente la librería SweetAlert (`Swal.fire`)
  Y queda estrictamente prohibida la invocación del método nativo `alert()`.
  ```

- **Asserts / Verificaciones Esperadas:**
  - Búsqueda estática en código JS no arroja llamadas a `alert(`.
  - Invocación de SweetAlert verificada en componentes Alpine.js.

---

## 📌 CP-SEC-006: Configuración de Caché en Cliente para imágenes de Google Cloud Storage (4 horas)

- **Tipo:** Performance / Infraestructura
- **Componente:** `ProductoController` / Cache Headers
- **Escenario Gherkin:**
  ```gherkin
  Dado que un cliente navega por el catálogo de productos visualizando imágenes alojadas en GCS
  Cuando el navegador solicita el recurso de imagen
  Entonces los encabezados de respuesta HTTP incluyen `Cache-Control: max-age=14400` (4 horas)
  Y las subsiguientes peticiones del mismo recurso se sirven desde el caché local del cliente ahorrando costos de red.
  ```

- **Asserts / Verificaciones Esperadas:**
  - Response Header `Cache-Control` contiene `max-age=14400` (configurable por `.env`).
