# 🪮 Suite de Pruebas "Fritolay Ambato" - Metodología Ponytail

Esta carpeta contiene la especificación completa y modular de la suite de pruebas para el **Sistema E-commerce y Gestión de Pedidos "Fritolay Ambato"**, estructurada y optimizada bajo el enfoque y filosofía del agente **[Ponytail](https://github.com/dietrichgebert/ponytail)**.

---

## 🎯 Enfoque y Metodología Ponytail

**Ponytail** es un estándar de representación de especificaciones y casos de prueba orientado a maximizar la precisión sintáctica y semántica al tiempo que optimiza el consumo de tokens en modelos de IA.

### Principios Fundamentales Aplicados:
1. **Densidad de Información y Consistencia Sintáctica:** Expresión clara y concisa de escenarios de prueba sin redundancias ni ambigüedades.
2. **Estructura Declarativa (BDD / Gherkin Extendido):** Dado / Cuando / Entonces complementados con datos de entrada explícitos, precondiciones y asserts de estado.
3. **Cobertura Tridimensional:**
   - **Positivos (POS):** Validación de flujos de negocio exitosos (*Happy Paths*).
   - **Negativos (NEG) y Excepciones:** Validación de bloqueos, mensajes de error y reglas de integridad.
   - **Límites (LIM) y Borde:** Evaluación de rangos cuantitativos (ej. límite de 30 días en fechas, umbral de stock, expiración de tokens).
   - **Seguridad (SEC) y Arquitectura:** Verificación de JWT, RBAC, XSS, SQLi, Soft Delete y Principios SOLID.

---

## 📂 Estructura de la Suite de Pruebas

```
Test/
├── README.md                           # Documentación principal de la suite (este archivo)
├── matriz_casos_de_prueba.md           # Matriz de Trazabilidad (Requerimiento vs. Caso de Prueba)
└── casos_de_prueba/
    ├── HU-001_checkout_liquidacion.md  # Pruebas de Checkout, Subtotal, IVA 15%, Comprobante y Dirección
    ├── HU-002_asignacion_rutas.md     # Pruebas de Asignación de Pedidos, Guías Remisión/Ruta y Bodega Móvil
    ├── HU-003_ejecucion_entrega.md    # Pruebas de Entrega, Devoluciones según Método de Pago y Factura PDF
    ├── HU-004_aprobacion_pagos.md     # Pruebas de Aprobación Manual (Depósito/De Una) vs Automatic (Efectivo/TC/TD)
    ├── HU-005_cierre_guias_arqueo.md   # Pruebas de Jornada Cerrada/En Revisión, Arqueo, Buen/Mal Estado y Encerado
    ├── HU-006_filtros_datadog.md      # Pruebas de Filtros Temporales (1d-30d, 1w-4w), Datadog y Cards de Estado
    ├── HU-007_carrito_compras.md      # Pruebas de Carrito, Merge de Ítems, Abandono SweetAlert y Ítems Únicos
    └── seguridad_y_arquitectura.md   # Pruebas de JWT TTL (1h/15d), RBAC, XSS, SQLi, Caché GCS y Soft Delete
```

---

## 🛡️ Seguridad y Aislamiento

- **Aislamiento Total:** Todos los especificaciones, artefactos y scripts de prueba se mantienen dentro del directorio `Test/`.
- **Exclusión de Datos Sensibles:** El archivo `.gitignore` raíz bloquea cualquier intento de incluir credenciales, claves secretas, dumps locales o registros de ejecución (`Test/*.env`, `Test/*.secret`, `Test/credentials/`, `Test/logs/`, etc.).
