# MATERIAL PARA LÁMINA DE DEFENSA

---

## Demostración Automatizada de Flujos Críticos con Selenium WebDriver

- **Caso 1: Flujo Completo E-Commerce (Catálogo, Mapa y Checkout)** | Automatización de inicio de sesión, adición de productos con merge dinámico en carrito, selección de punto de entrega en mapa interactivo Leaflet y liquidación final ($Subtotal - Descuentos + IVA 15\%$) | **Criterio de Éxito:** Badge de carrito contabiliza únicamente 1 ítem único, cálculo exacto de impuestos y confirmación modal SweetAlert de pedido registrado en estado *"En espera de asignación de ruta"*.

- **Caso 2: Operación Logística (Asignación de Rutas y Guía SRI de 15 dígitos)** | Automatización de selección de camión activo por el Operador de Ruta, marcado de pedidos en espera y cierre de asignación para despacho logístico | **Criterio de Éxito:** Generación automática de Guía de Remisión cumpliendo el formato legal del SRI (`001-001-000000001`), emisión de Guía de Ruta y transferencia transaccional de inventario hacia la Bodega Móvil del vehículo.

- **Caso 3: Cierre de Ruta, Arqueo de Caja y Encerado de Bodega Móvil** | Automatización de auditoría de guías en estado `cerrada` (**EN REVISIÓN**), verificación de efectivo declarado por el chofer, clasificación de devoluciones (Buen/Mal Estado) y aprobación final | **Criterio de Éxito:** Transición a estado `revisada`, aislamiento de mercadería dañada en tabla dedicada `mercaderia_mal_estado`, reingreso de mercadería en buen estado al inventario máster y encerado ($Stock = 0$) de la Bodega Móvil.

---

### 📌 Notas de Apoyo para el Expositor (Defensa ante Tribunal - Lectura < 30s)

> *"Estimado Tribunal: Esta suite automatizada en Selenium demuestra la integridad de nuestro sistema de principio a fin. Validamos desde la experiencia interactiva del cliente en el mapa e-commerce, pasando por el cumplimiento tributario ecuatoriano (SRI) en la logística de despacho, hasta el control financiero estricto con arqueo de caja y encerado de bodegas móviles."*
