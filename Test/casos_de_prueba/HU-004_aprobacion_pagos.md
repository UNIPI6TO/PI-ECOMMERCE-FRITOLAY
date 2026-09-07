# 🧪 Especificación de Casos de Prueba - HU-004: Aprobación Manual de Pagos con Comprobante

**Épica:** Módulo de Gestión de Pedidos (Operativo)  
**Historia de Usuario:** HU-004 - Aprobación Manual de Pagos con Comprobante  
**Referencia:** `docs/especificacion.md` (§ 5.2, HU-004, RN-01, RN-02)

---

## 📌 CP-HU004-POS-001: Aprobación manual exitosa de pago por Operador para Depósito / De Una

- **Tipo:** Positivo (Happy Path Operativo)
- **Componente:** `PagoController` / `PedidoRepository`
- **Precondiciones:**
  1. Usuario autenticado con rol `Operador de Ruta`.
  2. Pedido `PED-300` en estado `'En espera por aprobación de pago'`.
  3. Comprobante adjunto visible y legible.

- **Datos de Entrada:**
  ```json
  {
    "pedido_id": 300,
    "accion": "APROBAR",
    "observacion": "Comprobante verificado en cuenta bancaria"
  }
  ```

- **Escenario Gherkin:**
  ```gherkin
  Dado que un pedido se encuentra en estado "En espera por aprobación de pago"
  Y el cliente adjuntó el comprobante de depósito
  Cuando el Operador de Ruta revisa el documento y selecciona "Aprobar Pago"
  Entonces el sistema cambia el estado del pedido a "En espera de asignación de ruta"
  Y genera un registro en la bitácora de auditoría detallando el usuario y fecha de aprobación.
  ```

- **Asserts / Verificaciones Esperadas:**
  - `pedido.estado` == `'En espera de asignación de ruta'`.
  - Bitácora de auditoría insertada con `user_id`, `accion` = `'APROBACION_PAGO'`, `timestamp`.
  - HTTP Status `200 OK`.

---

## 📌 CP-HU004-POS-002: Aprobación automática instantánea para checkout con Efectivo, TC y TD

- **Tipo:** Positivo (Regla de Negocio RN-01)
- **Componente:** `CheckoutController` / `PagoAutoService`
- **Precondiciones:**
  1. Cliente realiza checkout seleccionando "Tarjeta de Débito" o "Efectivo".

- **Escenario Gherkin:**
  ```gherkin
  Dado que un cliente finaliza un checkout con método de pago "Efectivo" (o "TC" / "TD")
  Cuando el sistema procesa la orden
  Entonces el sistema aprueba automáticamente el pedido sin requerir intervención del operador
  Y lo coloca directamente en la lista de pedidos en estado "En espera de asignación de ruta".
  ```

- **Asserts / Verificaciones Esperadas:**
  - `pedido.estado` == `'En espera de asignación de ruta'` en forma inmediata tras la respuesta de la orden.
  - Ninguna tarea creada en la bandeja de aprobación manual.

---

## 📌 CP-HU004-NEG-003: Rechazo manual de comprobante inválido o ilegible

- **Tipo:** Negativo (Rechazo Operativo)
- **Componente:** `PagoController`
- **Precondiciones:**
  1. Pedido `PED-305` en estado `'En espera por aprobación de pago'`.
  2. Comprobante ilegible o borroso.

- **Datos de Entrada:**
  ```json
  {
    "pedido_id": 305,
    "accion": "RECHAZAR",
    "motivo_rechazo": "Comprobante ilisible, no se distingue el número de transferencia"
  }
  ```

- **Escenario Gherkin:**
  ```gherkin
  Dado que el Operador de Ruta revisa un comprobante ilegible
  Cuando selecciona la opción "Rechazar Pago" e ingresa el motivo
  Entonces el sistema cambia el estado del pedido a "Pago Rechazado"
  Y libera el inventario retenido en el campo EnPedidos del producto.
  ```

- **Asserts / Verificaciones Esperadas:**
  - `pedido.estado` == `'Pago Rechazado'`.
  - `producto.en_pedidos` restado el monto retenido.
  - Notificación enviada al cliente.
