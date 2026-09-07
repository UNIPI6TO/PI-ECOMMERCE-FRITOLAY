# 🧪 Especificación de Casos de Prueba - HU-007: Gestión del Carrito de Compras

**Épica:** Módulo de Clientes (E-commerce)  
**Historia de Usuario:** HU-007 - Gestión del Carrito de Compras  
**Referencia:** `docs/especificacion.md` (§ 5.1, § 5.4, HU-007, RN-01, RN-02)

---

## 📌 CP-HU007-POS-001: Incorporación de productos al carrito y cálculo de subtotal

- **Tipo:** Positivo (Happy Path)
- **Componente:** `CarritoController` / `CarritoManager`
- **Precondiciones:**
  1. Usuario (autenticado o invitado) navega por el catálogo.
  2. Producto "Tostitos Queso 150g" disponible con precio $2.50.

- **Datos de Entrada:**
  ```json
  {
    "producto_id": 15,
    "cantidad": 3
  }
  ```

- **Escenario Gherkin:**
  ```gherkin
  Dado que el usuario está visualizando el catálogo de productos
  Y selecciona un ítem disponible
  Cuando especifica la cantidad deseada (3) y lo añade al carrito de compras
  Entonces el sistema agrega el ítem al listado del carrito
  Y muestra inmediatamente el subtotal calculado ($7.50)
  Y guarda el estado en una cookie segura expirable (`HttpOnly`, `SameSite`).
  ```

- **Asserts / Verificaciones Esperadas:**
  - Item agregado con `cantidad` = `3`.
  - `subtotal` = `7.50`.
  - Cookie de sesión de carrito emitida con directivas de seguridad.

---

## 📌 CP-HU007-POS-002: Merge automático de producto duplicado incrementando cantidad

- **Tipo:** Positivo (Regla de Negocio RN-01)
- **Componente:** `CarritoManager` (`mergItem`)
- **Precondiciones:**
  1. Carrito contiene 2 unidades de "Papas Lays".

- **Datos de Entrada:**
  - Añadir nuevamente "Papas Lays" con cantidad = 3.

- **Escenario Gherkin:**
  ```gherkin
  Dado que el cliente tiene 2 unidades de "Papas Lays" en su carrito
  Cuando vuelve al catálogo y añade 3 unidades más del mismo producto
  Entonces el sistema no crea una nueva línea en el carrito
  Y realiza un merge actualizando la cantidad del producto a 5 unidades.
  ```

- **Asserts / Verificaciones Esperadas:**
  - Número de líneas únicas en el carrito se mantiene en `1`.
  - Cantidad del ítem "Papas Lays" es exactamente `5`.

---

## 📌 CP-HU007-POS-003: Vaciado de carrito con modal SweetAlert y registro de abandono

- **Tipo:** Positivo (Regla de Negocio § 5.1 Carritos Abandonados)
- **Componente:** `CarritoAbandonadoController` / Frontend Modal
- **Precondiciones:**
  1. Carrito contiene productos por valor total de $18.50.

- **Escenario Gherkin:**
  ```gherkin
  Dado que el usuario decide vaciar su carrito
  Cuando presiona el botón "Vaciar Carrito"
  Entonces el sistema muestra un modal de confirmación SweetAlert
  Y al confirmar, registra automáticamente un POST a `/api/carritos-abandonados` con el valor total ($18.50) y motivo 'Carrito vaciado manualmente por el usuario'
  Y limpia la cookie de sesión del carrito.
  ```

- **Asserts / Verificaciones Esperadas:**
  - Modal SweetAlert disparado.
  - Registro en tabla `carritos_abandonados` con `valor_total` = `18.50` y `motivo` = `'Carrito vaciado manualmente por el usuario'`.
  - Cookie vaciada.

---

## 📌 CP-HU007-POS-004: Badge de carrito en barra de navegación reflejando únicamente ítems únicos

- **Tipo:** Positivo (UI Especificación § 5.1)
- **Componente:** Frontend Nav Bar Component
- **Precondiciones:**
  1. Carrito contiene 10 unidades de "Cheetos" y 5 unidades de "Doritos".

- **Escenario Gherkin:**
  ```gherkin
  Dado que el usuario tiene 10 unidades de Cheetos y 5 unidades de Doritos en su carrito (15 unidades en total)
  Cuando observa el ícono (badge) del carrito en la barra de navegación
  Entonces el número mostrado en la insignia es 2 (dos productos distintos), NO la suma total de unidades (15).
  ```

- **Asserts / Verificaciones Esperadas:**
  - `#cart-badge-count.textContent` == `'2'`.
