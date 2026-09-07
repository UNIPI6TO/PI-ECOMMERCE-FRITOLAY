# 📊 Matriz de Trazabilidad de Casos de Prueba (Ponytail Spec)

La siguiente matriz conecta los Requerimientos Funcionales, Reglas de Negocio e Historias de Usuario documentados en `docs/especificacion.md` con los casos de prueba estructurados en la carpeta `Test/casos_de_prueba/`.

| Módulo / Épica | ID Historia | Requerimiento / Regla de Negocio | ID Caso de Prueba | Tipo | Archivo de Especificación |
| :--- | :--- | :--- | :--- | :--- | :--- |
| **Clientes (E-commerce)** | **HU-001** | Liquidación $TotalPedido = (Subtotal - Descuentos) + IVA 15\%$ | `CP-HU001-POS-001` | Positivo | `casos_de_prueba/HU-001_checkout_liquidacion.md` |
| **Clientes (E-commerce)** | **HU-001** | Obligatoriedad de Comprobante en Depósito / De Una | `CP-HU001-NEG-002` | Negativo | `casos_de_prueba/HU-001_checkout_liquidacion.md` |
| **Clientes (E-commerce)** | **HU-001** | Bloqueo por falta de Dirección de Entrega (Mapa/SoftDelete) | `CP-HU001-NEG-003` | Negativo | `casos_de_prueba/HU-001_checkout_liquidacion.md` |
| **Clientes (E-commerce)** | **HU-001** | Actualización del campo `EnPedidos` en Inventario Máster | `CP-HU001-POS-004` | Positivo | `casos_de_prueba/HU-001_checkout_liquidacion.md` |
| **Gestión de Pedidos** | **HU-002** | Asignación exitosa a camión activo y Guía de Remisión/Ruta | `CP-HU002-POS-001` | Positivo | `casos_de_prueba/HU-002_asignacion_rutas.md` |
| **Gestión de Pedidos** | **HU-002** | Rechazo de asignación duplicada a segundo camión | `CP-HU002-NEG-002` | Negativo | `casos_de_prueba/HU-002_asignacion_rutas.md` |
| **Gestión de Pedidos** | **HU-002** | Control de acceso por rol (Chofer sin acceso a asignación) | `CP-HU002-SEC-003` | Seguridad | `casos_de_prueba/HU-002_asignacion_rutas.md` |
| **Gestión de Pedidos** | **HU-002** | Creación de transacción de ingreso a Bodega del Camión | `CP-HU002-POS-004` | Positivo | `casos_de_prueba/HU-002_asignacion_rutas.md` |
| **Entregas (Chofer)** | **HU-003** | Entrega total, deducción de inventario físico y Factura PDF | `CP-HU003-POS-001` | Positivo | `casos_de_prueba/HU-003_ejecucion_entrega.md` |
| **Entregas (Chofer)** | **HU-003** | Bloqueo de devolución parcial en pago con Tarjeta de Crédito | `CP-HU003-NEG-002` | Negativo | `casos_de_prueba/HU-003_ejecucion_entrega.md` |
| **Entregas (Chofer)** | **HU-003** | Entrega parcial válida con pago en Efectivo y recálculo | `CP-HU003-POS-003` | Positivo | `casos_de_prueba/HU-003_ejecucion_entrega.md` |
| **Gestión de Pedidos** | **HU-004** | Aprobación manual de pago Depósito/De Una por Operador | `CP-HU004-POS-001` | Positivo | `casos_de_prueba/HU-004_aprobacion_pagos.md` |
| **Gestión de Pedidos** | **HU-004** | Aprobación automática instantánea para pago en Efectivo/TC/TD | `CP-HU004-POS-002` | Positivo | `casos_de_prueba/HU-004_aprobacion_pagos.md` |
| **Gestión de Pedidos** | **HU-005** | Finalización jornada (`cerrada` / EN REVISIÓN) y aprobación | `CP-HU005-POS-001` | Positivo | `casos_de_prueba/HU-005_cierre_guias_arqueo.md` |
| **Gestión de Pedidos** | **HU-005** | Registro exclusivo de mercadería en mal estado en tabla propia | `CP-HU005-POS-002` | Positivo | `casos_de_prueba/HU-005_cierre_guias_arqueo.md` |
| **Gestión de Pedidos** | **HU-005** | Encerado de bodega móvil y actualización inventario máster | `CP-HU005-POS-003` | Positivo | `casos_de_prueba/HU-005_cierre_guias_arqueo.md` |
| **Dashboard** | **HU-006** | Aplicación de atajo de fecha Datadog (ej. `1w`) | `CP-HU006-POS-001` | Positivo | `casos_de_prueba/HU-006_filtros_datadog.md` |
| **Dashboard** | **HU-006** | Bloqueo estricto de consulta custom mayor a 30 días | `CP-HU006-LIM-002` | Límites | `casos_de_prueba/HU-006_filtros_datadog.md` |
| **Dashboard** | **HU-006** | Filtrado dinámico al hacer clic en Card de Estado | `CP-HU006-POS-003` | Positivo | `casos_de_prueba/HU-006_filtros_datadog.md` |
| **Clientes (E-commerce)** | **HU-007** | Incorporación de producto y cálculo de subtotal | `CP-HU007-POS-001` | Positivo | `casos_de_prueba/HU-007_carrito_compras.md` |
| **Clientes (E-commerce)** | **HU-007** | Merge automático de producto duplicado incrementando cantidad | `CP-HU007-POS-002` | Positivo | `casos_de_prueba/HU-007_carrito_compras.md` |
| **Clientes (E-commerce)** | **HU-007** | Vaciado de carrito con modal SweetAlert y registro de abandono | `CP-HU007-POS-003` | Positivo | `casos_de_prueba/HU-007_carrito_compras.md` |
| **Clientes (E-commerce)** | **HU-007** | Contador del badge navegación basado en ítems únicos | `CP-HU007-POS-004` | Positivo | `casos_de_prueba/HU-007_carrito_compras.md` |
| **Seguridad y Sistema** | N/A | Expiración JWT por defecto (1 hora / 60 min) sin Recuérdame | `CP-SEC-001` | Seguridad | `casos_de_prueba/seguridad_y_arquitectura.md` |
| **Seguridad y Sistema** | N/A | Expiración JWT extendida (15 días) con Recuérdame activo | `CP-SEC-002` | Seguridad | `casos_de_prueba/seguridad_y_arquitectura.md` |
| **Seguridad y Sistema** | N/A | Sanitización XSS y SQL Injection en endpoints API | `CP-SEC-003` | Seguridad | `casos_de_prueba/seguridad_y_arquitectura.md` |
| **Seguridad y Sistema** | N/A | Validación de Soft Delete en Direcciones de Clientes | `CP-SEC-004` | Arquitectura | `casos_de_prueba/seguridad_y_arquitectura.md` |
| **Seguridad y Sistema** | N/A | Notificaciones UI exclusivas mediante SweetAlert (sin alert nativo) | `CP-SEC-005` | UI/UX | `casos_de_prueba/seguridad_y_arquitectura.md` |
| **Seguridad y Sistema** | N/A | Caché en cliente para imágenes de GCS (4 horas por defecto) | `CP-SEC-006` | Performance | `casos_de_prueba/seguridad_y_arquitectura.md` |
