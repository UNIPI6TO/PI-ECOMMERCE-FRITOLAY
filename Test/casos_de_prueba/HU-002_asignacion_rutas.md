# 🧪 Especificación de Casos de Prueba - HU-002: Asignación de Rutas y Generación de Guías

**Épica:** Módulo de Gestión de Pedidos (Operativo)  
**Historia de Usuario:** HU-002 - Asignación de Rutas y Generación de Guías  
**Referencia:** `docs/especificacion.md` (§ 5.2, HU-002, RN-01, RN-02, RN-03)

---

## 📌 CP-HU002-POS-001: Asignación exitosa de pedidos aprobados a camión activo y cierre de asignación

- **Tipo:** Positivo (Happy Path)
- **Componente:** `AsignacionController` / `GuiaRemisionService`
- **Precondiciones:**
  1. Usuario autenticado con rol `Operador de Ruta`.
  2. Camión con ID `5` (Placa `PBA-1234`) en estado `'Activo'`.
  3. Pedidos `PED-101` y `PED-102` en estado `'En espera de asignación de ruta'`.

- **Datos de Entrada:**
  ```json
  {
    "camion_id": 5,
    "pedidos_ids": [101, 102]
  }
  ```

- **Escenario Gherkin:**
  ```gherkin
  Dado que existen pedidos en estado "En espera de asignación de ruta"
  Y el Operador de Ruta tiene un camión "Activo" seleccionado
  Cuando asigna los pedidos y hace clic en "Cerrar Asignación"
  Entonces el sistema genera una Guía de Remisión visual (cumpliendo estándar SRI 15 dígitos)
  Y genera una Guía de Ruta con el listado de negocios, montos y tipos de pago
  Y crea una transacción de ingreso de inventario en la bodega del camión (BodegaCamion)
  Y actualiza el estado de los pedidos a "En Ruta".
  ```

- **Asserts / Verificaciones Esperadas:**
  - `guia_remision.estado` == `'activa'` (o `'generada'`)
  - `guia_remision.camion_id` == `5`
  - Transacciones insertadas en `transacciones_inventario` con `tipo` = `'INGRESO_BODEGA_CAMION'`.
  - HTTP `200 OK` / `201 Created`.

---

## 📌 CP-HU002-NEG-002: Prevención de asignación duplicada de pedido a segundo camión

- **Tipo:** Negativo (Integridad de Datos)
- **Componente:** `AsignacionRequest` / `AsignacionController`
- **Precondiciones:**
  1. Pedido `PED-123` ya asignado previamente a Camión A (Guía de Ruta activa).

- **Datos de Entrada:**
  ```json
  {
    "camion_id": 8,
    "pedidos_ids": [123]
  }
  ```

- **Escenario Gherkin:**
  ```gherkin
  Dado que un pedido con ID "PED-123" ya fue asignado al "Camión A"
  Cuando el Operador de Ruta intenta asignarlo al "Camión B"
  Entonces el sistema rechaza la operación
  Y muestra una alerta (SweetAlert) "El pedido ya se encuentra en ruta con otro vehículo".
  ```

- **Asserts / Verificaciones Esperadas:**
  - HTTP Status `422 Unprocessable Entity` o `400 Bad Request`.
  - Estado del pedido `PED-123` permanece inalterado.
  - Ninguna nueva guía creada para el Camión B.

---

## 📌 CP-HU002-SEC-003: Restricción de acceso a Asignación de Rutas para rol Chofer (RBAC)

- **Tipo:** Seguridad (Control de Acceso / RBAC)
- **Componente:** `RoleMiddleware`
- **Precondiciones:**
  1. Usuario autenticado con rol `Chofer`.

- **Escenario Gherkin:**
  ```gherkin
  Dado que un usuario con rol "Chofer" inicia sesión en el sistema
  Cuando intenta acceder a la pantalla o API de Asignación de Rutas (/admin/asignacion-rutas)
  Entonces el sistema deniega el acceso (403 Forbidden)
  Y redirige al usuario a su Módulo de Entregas con el mensaje "No tiene permisos para esta acción".
  ```

- **Asserts / Verificaciones Esperadas:**
  - HTTP Status `403 Forbidden`.
  - Redirección a `/chofer/mis-rutas`.

---

## 📌 CP-HU002-POS-004: Transferencia e incremento automático de stock en Bodega Móvil

- **Tipo:** Positivo (Integridad de Inventario)
- **Componente:** `BodegaCamionRepository`
- **Precondiciones:**
  1. Camión ID `5` tiene `0` unidades de "Doritos 50g" en su bodega a bordo.
  2. Pedido asignado solicita `50` unidades de "Doritos 50g".

- **Escenario Gherkin:**
  ```gherkin
  Dado que se cierra la asignación de ruta para el camión ID 5
  Cuando el sistema procesa el despacho desde la bodega máster
  Entonces el registro en `bodega_camion` para ese producto e ID de camión se incrementa a 50 unidades.
  ```

- **Asserts / Verificaciones Esperadas:**
  - `bodega_camion.cantidad_actual` == `50`
  - Transacción registrada en `transacciones_inventario`.
