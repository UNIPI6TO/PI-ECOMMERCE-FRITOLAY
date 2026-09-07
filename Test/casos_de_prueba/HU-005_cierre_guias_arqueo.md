# 🧪 Especificación de Casos de Prueba - HU-005: Cierre de Guías, Arqueo, Declaración de Efectivo y Encerado de Bodega

**Épica:** Módulo de Gestión de Pedidos (Operativo y Cierre)  
**Historia de Usuario:** HU-005 - Cierre de Guías, Arqueo, Declaración de Efectivo y Encerado de Bodega  
**Referencia:** `docs/especificacion.md` (§ 5.2, HU-005, RN-01, RN-02, RN-03, RN-04, RN-05)

---

## 📌 CP-HU005-POS-001: Flujo principal exitoso de finalización de jornada (`cerrada` / EN REVISIÓN) y aprobación final (`revisada`)

- **Tipo:** Positivo (Happy Path Auditoría)
- **Componente:** `CierreController` / `GuiaRemisionRepository`
- **Precondiciones:**
  1. Chofer completa entregas y presiona "Finalizar Jornada" declarando $150.00 en efectivo.
  2. Guía pasa a estado `cerrada` (mostrada en la interfaz `/admin/cierre-guias` con badge **EN REVISIÓN**).
  3. Administrador / Operador ingresa al detalle `/admin/cierre-guias/{id}`.

- **Datos de Entrada:**
  ```json
  {
    "guia_id": 42,
    "efectivo_declarado": 150.00,
    "accion": "APROBAR_REVISION"
  }
  ```

- **Escenario Gherkin:**
  ```gherkin
  Dado que un camión tiene una guía en estado `cerrada` (mostrada en la interfaz como "EN REVISIÓN")
  Y el chofer ha finalizado jornada declarando el valor en efectivo
  Cuando el Administrador / Operador revisa el detalle y presiona "Aprobar Revisión"
  Entonces el sistema cambia el estado final de la guía a `revisada` (mostrado como **REVISADA / APROBADA**)
  Y registra el usuario revisor (`revisada_por`) y la fecha (`fecha_revision`)
  Y actualiza el inventario máster restando la reserva `en_pedidos` e ingresando devoluciones en buen estado
  Y encera el inventario a bordo del camión (BodegaCamion).
  ```

- **Asserts / Verificaciones Esperadas:**
  - `guia_remision.estado` == `'revisada'`.
  - `guia_remision.revisada_por` != `null`.
  - `guia_remision.fecha_revision` != `null`.
  - Inventory on `BodegaCamion` set to `0` (encerado).
  - HTTP Status `200 OK`.

---

## 📌 CP-HU005-POS-002: Clasificación y aislamiento de mercadería en mal estado

- **Tipo:** Positivo (Integridad de Inventario RN-04)
- **Componente:** `CierreController` / `MercaderiaMalEstadoRepository`
- **Precondiciones:**
  1. Durante la devolución en ruta, 3 bolsas de "Papas Lays" resultaron aplastadas/dañadas.

- **Datos de Entrada:**
  ```json
  {
    "guia_id": 42,
    "devoluciones": [
      {
        "producto_id": 10,
        "cantidad": 3,
        "estado": "Mal estado",
        "motivo": "Empaque roto/aplastado durante transporte"
      }
    ]
  }
  ```

- **Escenario Gherkin:**
  ```gherkin
  Dado que el Operador de Ruta procesa el cierre de una guía con productos devueltos
  Cuando clasifica una parte de la mercadería como "Mal estado"
  Entonces el sistema registra estos ítems en la tabla exclusiva `mercaderia_mal_estado`
  Y omite la actualización de estos ítems en el inventario máster disponible para venta.
  ```

- **Asserts / Verificaciones Esperadas:**
  - Inserción exitosa en la tabla `mercaderia_mal_estado`.
  - Stock disponible de venta en `productos` NO se incrementa para esos 3 ítems.
  - HTTP Status `200 OK`.

---

## 📌 CP-HU005-POS-003: Reingreso de mercadería en buen estado al inventario máster

- **Tipo:** Positivo (Regla de Negocio RN-03)
- **Componente:** `ProductoRepository` / `CierreService`
- **Precondiciones:**
  1. 5 unidades de "Doritos" devueltas por cliente por falta de cambio en efectivo, marcadas como "Buen estado".

- **Escenario Gherkin:**
  ```gherkin
  Dado que se aprueba la revisión de una guía con 5 unidades devueltas en "Buen estado"
  Cuando el sistema ejecuta el encerado de bodega
  Entonces el inventario máster de "Doritos" incrementa su `cantidad_fisica` en 5 unidades.
  ```

- **Asserts / Verificaciones Esperadas:**
  - `producto.cantidad_fisica` incrementado en `5`.
  - Transacción registrada con `tipo` = `'REINGRESO_DEVOLUCION_BUEN_ESTADO'`.
