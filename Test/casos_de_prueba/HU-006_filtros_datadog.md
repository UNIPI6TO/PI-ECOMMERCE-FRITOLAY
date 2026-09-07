# 🧪 Especificación de Casos de Prueba - HU-006: Filtros Temporales y de Estado Estilo Datadog

**Épica:** Módulo Dashboard y Estadísticas  
**Historia de Usuario:** HU-006 - Filtros Temporales y de Estado Estilo Datadog  
**Referencia:** `docs/especificacion.md` (§ 5.2, HU-006, RN-01, RN-02)

---

## 📌 CP-HU006-POS-001: Aplicación de atajos de filtro temporal Datadog (`Hoy`, `Ayer`, `1d-30d`, `1w-4w`)

- **Tipo:** Positivo (Happy Path Filtros)
- **Componente:** `DashboardController` / Frontend Datadog Filter Component
- **Precondiciones:**
  1. Usuario autenticado como `Administrador` u `Operador de Ruta`.
  2. En el panel de pedidos se ubica el textbox de filtro de fechas.

- **Datos de Entrada:**
  - Valor ingresado en el textbox: `"1w"`

- **Escenario Gherkin:**
  ```gherkin
  Dado que el usuario se encuentra en el Módulo de Gestión de Pedidos
  Cuando ingresa el comando "1w" en el cuadro de texto de fechas
  Entonces el sistema configura la fecha de inicio a 1 semana antes (7 días) de la fecha/hora actual
  Y configura la fecha final como la fecha y hora actual
  Y actualiza la lista y el mapa de pedidos reflejando los registros de ese intervalo.
  ```

- **Asserts / Verificaciones Esperadas:**
  - `query.fecha_inicio` == `NOW() - 7 days`.
  - `query.fecha_fin` == `NOW()`.
  - Respuesta HTTP `200 OK` con data filtrada.

---

## 📌 CP-HU006-LIM-002: Límite estricto de máximo 30 días en filtros de fecha personalizados (Custom)

- **Tipo:** Límites / Excepción (Regla de Negocio RN-01)
- **Componente:** `DashboardFiltroRequest` / `DashboardController`
- **Precondiciones:**
  1. Usuario intenta consulta personalizada con rango amplio.

- **Datos de Entrada:**
  ```json
  {
    "fecha_inicio": "2026-01-01T00:00:00",
    "fecha_fin": "2026-02-15T23:59:59"
  }
  ```
  *(Diferencia: 46 días, superior al límite de 30 días)*

- **Escenario Gherkin:**
  ```gherkin
  Dado que el usuario utiliza el filtro custom ingresando fechas manualmente
  Cuando define un rango superior a 30 días entre inicio y fin (ej. 46 días)
  Entonces el sistema bloquea la búsqueda
  Y genera un mensaje de error (SweetAlert) indicando que la consulta no puede sobrepasar los 30 días.
  ```

- **Asserts / Verificaciones Esperadas:**
  - HTTP Status `422 Unprocessable Entity` o error de validación en frontend.
  - Mensaje de validación: `"El rango de fechas no puede exceder los 30 días"`.

---

## 📌 CP-HU006-POS-003: Filtrado dinámico por estado al hacer clic en Cards Informativas

- **Tipo:** Positivo (UI Reactiva)
- **Componente:** Frontend Dashboard View
- **Precondiciones:**
  1. Pantalla del Dashboard muestra los cards informativos con contadores por estado:
     - *En espera de asignación de ruta*
     - *En Ruta*
     - *Entregados*
     - *Pendiente de Aprobación*

- **Escenario Gherkin:**
  ```gherkin
  Dado que el usuario visualiza los cards informativos de pedidos por estado
  Cuando hace clic en el card "Pendiente de Aprobación"
  Entonces el sistema aplica automáticamente el filtro mostrando en la lista y en el mapa únicamente los pedidos con el estado "Pendiente de Aprobación".
  ```

- **Asserts / Verificaciones Esperadas:**
  - URL / State query contiene `estado=PENDIENTE_APROBACION`.
  - Elementos visibles en la tabla coinciden 100% con el estado filtrado.
