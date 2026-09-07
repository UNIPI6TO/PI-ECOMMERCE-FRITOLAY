# 🧪 Especificación de Casos de Prueba - HU-001: Liquidación de Pago y Checkout

**Épica:** Módulo de Clientes (E-commerce)  
**Historia de Usuario:** HU-001 - Liquidación de Pago y Checkout  
**Referencia:** `docs/especificacion.md` (§ 5.4, HU-001, RN-01, RN-02, RN-03)

---

## 📌 CP-HU001-POS-001: Flujo principal exitoso de checkout con pago en Efectivo

- **Tipo:** Positivo (Happy Path)
- **Componente:** `CheckoutController` / `CarritoManager`
- **Precondiciones:**
  1. Cliente autenticado con token JWT activo.
  2. Carrito contiene 2 unidades de "Papas Lays 100g" ($1.50 c/u, Subtotal: $3.00).
  3. Descuento activo por tipo de cliente: 10% ($0.30).
  4. IVA legal configurado: 15%.
  5. Dirección de entrega por defecto seleccionada.

- **Datos de Entrada:**
  ```json
  {
    "metodo_pago": "Efectivo",
    "direccion_id": 12,
    "comprobante": null
  }
  ```

- **Escenario Gherkin:**
  ```gherkin
  Dado que el cliente tiene productos en su carrito de compras
  Y se encuentra autenticado en el sistema
  Cuando procede a la pantalla de checkout y selecciona "Efectivo" como método de pago
  Entonces el sistema calcula y muestra el TotalPedido = ($3.00 - $0.30) * 1.15 = $3.105 ($3.11)
  Y el sistema registra el pedido en estado "En espera de asignación de ruta"
  Y el sistema actualiza el inventario sumando la cantidad solicitada (2) al campo EnPedidos
  Y el sistema vacía el carrito sin registrar evento de abandono.
  ```

- **Asserts / Verificaciones Esperadas:**
  - `pedido.estado` == `'En espera de asignación de ruta'`
  - `pedido.subtotal` == `3.00`
  - `pedido.descuento` == `0.30`
  - `pedido.iva` == `0.405`
  - `pedido.total` == `3.105`
  - `producto.en_pedidos` incrementado en `2`
  - Respuesta HTTP `201 Created`

---

## 📌 CP-HU001-NEG-002: Validación obligatoria de comprobante para pago con Depósito / De Una

- **Tipo:** Negativo (Excepción / Validación)
- **Componente:** `CheckoutRequest` / `CheckoutController`
- **Precondiciones:**
  1. Cliente autenticado.
  2. Carrito con productos activos.

- **Datos de Entrada:**
  ```json
  {
    "metodo_pago": "Depósito",
    "direccion_id": 12,
    "comprobante": null
  }
  ```

- **Escenario Gherkin:**
  ```gherkin
  Dado que el cliente selecciona "Depósito" en el checkout
  Cuando intenta finalizar el pedido sin adjuntar un documento comprobante
  Entonces el sistema bloquea la acción
  Y muestra un mensaje de error "Debe adjuntar el comprobante de depósito para continuar"
  Y el estado del pedido no se genera en base de datos.
  ```

- **Asserts / Verificaciones Esperadas:**
  - HTTP Status: `422 Unprocessable Entity`
  - Mensaje modal (SweetAlert): `"Debe adjuntar el comprobante de depósito para continuar"`
  - Ninguna transacción o registro insertado en `pedidos`.

---

## 📌 CP-HU001-NEG-003: Bloqueo de checkout por falta de dirección de entrega seleccionada

- **Tipo:** Negativo (Validación UI / Regla de Negocio)
- **Componente:** Frontend Checkout View / `SanitizeInputMiddleware`
- **Precondiciones:**
  1. Cliente autenticado sin direcciones guardadas o sin seleccionar ninguna dirección activa.

- **Datos de Entrada:**
  ```json
  {
    "metodo_pago": "Efectivo",
    "direccion_id": null
  }
  ```

- **Escenario Gherkin:**
  ```gherkin
  Dado que el cliente se encuentra en la pantalla de checkout
  Cuando no selecciona ni registra una dirección de entrega válida en el mapa bidireccional
  Entonces el botón de "Finalizar Compra" se deshabilita
  Y se genera una alerta visual (SweetAlert) indicando "Dirección de entrega obligatoria".
  ```

- **Asserts / Verificaciones Esperadas:**
  - Botón `#btn-finalizar-compra` attribute `disabled` == `true`.
  - Evento SweetAlert disparado: `Swal.fire({ title: 'Error', text: 'Dirección de entrega obligatoria', icon: 'error' })`.

---

## 📌 CP-HU001-POS-004: Registro en estado "En espera por aprobación de pago" al adjuntar comprobante válido

- **Tipo:** Positivo (Flujo Asíncrono / Pago Manual)
- **Componente:** `CheckoutController` / `StorageService`
- **Precondiciones:**
  1. Cliente realiza pago con "De Una".
  2. Adjunta archivo válido (`comprobante_deuna.jpg`, < 5MB).

- **Escenario Gherkin:**
  ```gherkin
  Dado que el cliente selecciona método de pago "De Una"
  Y adjunta una imagen de comprobante válida
  Cuando envía la solicitud de checkout
  Entonces el archivo se almacena en el bucket GCS de comprobantes
  Y el pedido se registra con el estado "En espera por aprobación de pago"
  Y el inventario máster actualiza el campo EnPedidos.
  ```

- **Asserts / Verificaciones Esperadas:**
  - `pedido.metodo_pago` == `'De Una'`
  - `pedido.estado` == `'En espera por aprobación de pago'`
  - `pedido.comprobante_path` contiene la URL/Ruta válida en GCS.
