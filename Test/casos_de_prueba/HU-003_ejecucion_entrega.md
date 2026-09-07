# 🧪 Especificación de Casos de Prueba - HU-003: Ejecución de Entrega, Devolución y Facturación

**Épica:** Módulo de Entregas (Chofer)  
**Historia de Usuario:** HU-003 - Ejecución de Entrega, Devolución y Facturación  
**Referencia:** `docs/especificacion.md` (§ 5.3, HU-003, RN-01, RN-02, RN-03)

---

## 📌 CP-HU003-POS-001: Entrega total exitosa de pedido y deducción de inventario del camión

- **Tipo:** Positivo (Happy Path)
- **Componente:** `EntregaController` / `FacturaService`
- **Precondiciones:**
  1. Chofer autenticado en su ruta asignada.
  2. Pedido `PED-200` en estado `'Listo a ser entregado'`.
  3. Cantidad solicitada: 10 unidades de "Cheetos 30g".
  4. Método de Pago: Cualquier método válido.

- **Datos de Entrada:**
  ```json
  {
    "pedido_id": 200,
    "tipo_entrega": "TOTAL",
    "cantidad_entregada": 10,
    "motivo_devolucion": null
  }
  ```

- **Escenario Gherkin:**
  ```gherkin
  Dado que el Chofer se encuentra en la ubicación del cliente
  Y el pedido está en estado "Listo a ser entregado"
  Cuando marca el pedido como "Entregado totalmente"
  Entonces el sistema descuenta la cantidad física (10) del inventario del camión (BodegaCamion)
  Y descuenta la cantidad en pedido (10) del inventario máster (EnPedidos)
  Y genera automáticamente la factura simulada en PDF procesada del lado del navegador del usuario
  Y el estado del pedido cambia a "Entregado".
  ```

- **Asserts / Verificaciones Esperadas:**
  - `bodega_camion.cantidad_actual` disminuida en `10`.
  - `producto.en_pedidos` disminuido en `10`.
  - Generación cliente PDF ejecutada (sin recarga del servidor).
  - `pedido.estado` == `'Entregado'`.

---

## 📌 CP-HU003-NEG-002: Bloqueo de devolución parcial en pedidos pagados con Tarjeta de Crédito / Débito / Depósito

- **Tipo:** Negativo (Regla de Negocio RN-02)
- **Componente:** `EntregaRequest` / `EntregaController`
- **Precondiciones:**
  1. Pedido `PED-205` pagado con "Tarjeta de Crédito".
  2. Cantidad solicitada: 5 unidades.

- **Datos de Entrada:**
  ```json
  {
    "pedido_id": 205,
    "tipo_entrega": "PARCIAL",
    "cantidad_entregada": 3,
    "motivo_devolucion": "Cliente rechaza 2 unidades por empaque doblado"
  }
  ```

- **Escenario Gherkin:**
  ```gherkin
  Dado que el pedido fue pagado con "Tarjeta de Crédito"
  Cuando el Chofer intenta registrar una devolución parcial de mercadería (entregando 3 de 5)
  Entonces el sistema bloquea la entrada de cantidades menores al total
  Y muestra el mensaje de error "Las devoluciones parciales solo aplican para pagos en efectivo. Proceda con devolución total o entrega completa."
  Y no se modifica el inventario ni la factura.
  ```

- **Asserts / Verificaciones Esperadas:**
  - HTTP Status `422 Unprocessable Entity`.
  - Alerta SweetAlert con el mensaje especificado en la RN-02.

---

## 📌 CP-HU003-POS-003: Entrega parcial válida en pedido pagado con Efectivo y recálculo de factura

- **Tipo:** Positivo (Regla de Negocio RN-01 / Excepción Permitida)
- **Componente:** `EntregaController` / `FacturaService`
- **Precondiciones:**
  1. Pedido `PED-210` de 10 unidades a $2.00 c/u ($20.00 total) pagado en "Efectivo".

- **Datos de Entrada:**
  ```json
  {
    "pedido_id": 210,
    "tipo_entrega": "PARCIAL",
    "cantidad_entregada": 8,
    "motivo_devolucion": "Cliente no cuenta con suficiente efectivo",
    "estado_mercaderia": "Buen estado"
  }
  ```

- **Escenario Gherkin:**
  ```gherkin
  Dado que un pedido de 10 unidades fue pagado en Efectivo
  Cuando el Chofer registra la entrega de 8 unidades y 2 unidades como devolución
  Entonces el sistema actualiza el valor a cobrar basado en las 8 unidades ($16.00 + IVA)
  Y genera la factura únicamente por el valor recalculado de los artículos entregados
  Y el estado del pedido cambia a "Entregado Parcialmente"
  Y las 2 unidades devueltas permanecen en la bodega del camión para el arqueo final.
  ```

- **Asserts / Verificaciones Esperadas:**
  - `pedido.estado` == `'Entregado Parcialmente'`
  - `factura.subtotal` recalculado para 8 unidades.
  - HTTP Status `200 OK`.
