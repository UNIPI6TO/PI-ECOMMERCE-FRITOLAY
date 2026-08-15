## Sistema E-commerce y Gestión de Pedidos "Fritolay Ambato" 1. Objetivos del Proyecto Objetivo General

- Construir una aplicación que debe ser web y compatible con PWA.

- Desarrollar un sistema capaz de captar pedidos, ver en tiempo real su entrega, gestionar pedidos, guías de remisión de camiones y rutas.

## Objetivos Específicos de Arquitectura

- La aplicación debe estar en la nube y ser una aplicación orientada a microservicios y dockers.

- Ejecución en Desarrollo: Aunque la arquitectura es nativa de la nube, debe garantizarse que la aplicación también se pueda ejecutar en el local para el desarrollo de la misma.

- Debe existir separación estricta entre front end, back end, MySQL para datos transaccionales y Firestore para datos de geolocalización del camión.

- La aplicación debe ser desarrollada en Laravel con PHP tanto el backend y el front end y de forma independiente en el mismo repositorio de Git.

- Para los análisis y construcción de la aplicación debe usar pony tail (https://github.com/dietrichgebert/ponytail) para optimizar el uso de tokens.

- Aplicar los principios de SOLID en la construcción con agentes de IA para la aplicación, en especial el de responsabilidad única, y aplica también la Programación Orientada a Objetos.

- Las entradas del REST API o backend deben ser validadas para no recibir ataques de XSS o SQL Injection.

- Agregar test en el desarrollo del backend y front end

- 2. UI/UX e Identidad Visual

- Diseño y Usabilidad: Todo el sistema debe estar enfocado en un diseño minimalista y adaptable, priorizando la usabilidad y un buen diseño.

- Identidad Corporativa: La página debe tener los colores institucionales donde debe existir una combinación entre estas dos páginas https://www.lays.com/ y https://www.fritolay.com/ que son sus productos estrellas.

- 3. Seguridad, Gestión de Estado y Variables de Entorno (Environment)

- Autenticación API: Implementar JWT y usar Secret Manager en GCP para resguardar los secretos de infraestructura y JWT.


- Gestión de Contraseñas: Usar hash para comparación de las contraseñas y no estén en la base de datos en texto claro. La clave para generar el hash debe estar en un archivo environment.

- Recuperación de Credenciales: Todos los usuarios pueden recuperar sus credenciales mediante su correo electrónico con un pin de 6 dígitos aleatorios por defecto. La cantidad de dígitos debe ser configurada con una variable de entorno.

- Mensajería: Para la configuración del email para mensajería debe estar configurado en un environment en el backend para las funcionalidades.

- Persistencia Temporal: Uso de cookies seguras expirables para mantener el estado del carrito.

- Caché: Las imágenes (de GCS) deben guardarse en caché del lado del cliente o navegador con una duración configurable (por defecto 4 horas) que sea expirable.

## 4. Roles y Permisos

- Rol Administrador: Este usuario es capaz de crear, inactivar o resetear contraseñas de usuarios tipo empleados u otro rol de administración. Pueden ver el Módulo Dashboard de Gestión de Pedidos. Tendrá acceso al visor de facturas y exportación PDF.

- Rol Operador de Ruta: Este usuario gestionará los pedidos a entregar, como la asignación de los pedidos para un camión, aprobación de pedidos, asignación de rutas con la creación de guía de ruta y la guía de remisión, visor de facturas y cierre de caja del camión. Prácticamente estará usando el Módulo de Gestión de Pedidos.

- Rol Chofer: Este usuario es el chofer del camión, él tendrá accesos a su ruta asignada del Módulo Entregas.

- Rol Cliente: Una funcionalidad que no necesariamente debe ser autentificada es la de agregar productos al carrito de compras, la página de inicio debe ser esta y debe incluir inicio de sesión en el home page.

- 5. Módulos del Sistema y Lógica de Negocio

- 5.1. Dashboard 1: Estadísticas y KPIs (Módulo Dashboard de Gestión de Pedidos)

- Indicadores: Efectividad de entrega por camión, pedidos entregados, efectividad de entrega general y tiempos de entrega promedio.

- Ventas: Debe mostrar las ventas por día, por sector y por camión.

- Recaudación: Recaudación total y separado en efectivo, depósitos, cheques, De Una, Tarjeta de Crédito y Tarjetas de Débito.

- Carritos Abandonados: Compras no concretadas que el usuario haya creado el carrito y haya cancelado la compra. Al cancelar, debe poner opciones de por qué cancela el pedido; las comunes son: No lo necesito, Era una proforma, Pedido Equivocado, No es lo que requiero, y otros.


- Control de Stock: En el dashboard puede consultar el stock de los productos de las bodegas, de la bodega master y de los vehículos.

## 5.2. Módulo de Gestión de Pedidos (Operativo y Administrativo)

- Rastreo en Vivo: La última ubicación estará siempre visible en el Módulo de Gestión de Pedidos.

- Filtros de Fechas (Estilo Datadog): Se debe presentar un mapa el cual debe usar filtros de fechas especificando inicio y fin, este inicio y fin no puede ser mayor a un mes. Usar un textbox donde el usuario pueda escribir y las fechas deben cambiar (límite es 30 días por consulta):

- o Hoy = Fecha de inicio y fin deben estar configuradas como del día de hoy.

- o Ayer = Fechas inicio y fin del día de ayer.

- o 1d, 2d... 30d = Fechas configuradas de inicio 1 a 30 días antes de la fecha y hora actual, y fecha final la fecha y hora actual.

- o 1w, 2w, 3w y 4w = Fechas configuradas de inicio 1 a 4 semanas antes, y fecha final la fecha y hora actual.

- o Cuando haga una consulta custom, el cuadro de texto debe aparecer como custom, la validación que no debe pasarse de 30 días.

- Filtros por Estado: Usar también filtros de estado y por defecto debe aparecer los pedidos en espera de asignación de ruta y los camiones que no tienen asignado rutas.

- Cards Informativos: Debe tener unos cards como tipo informativo de la cantidad de pedidos por estado, y cuando dé un clic en el card este filtre por el estado. Los estados son: En espera de asignación de ruta, No entregado, Entregado Parcialmente, Entregados, Pendiente de Aprobación, Listo Para entregar, En Ruta, Todos.

- Ordenamiento: Los pedidos pueden ordenarse por: antigüedad del pedido (Por defecto), nombre del cliente y valor del pedido.

- Descuentos: El usuario puede asignar descuentos a clientes por tipo de pago, una sola configuración que aplicará en las siguientes compras. El usuario puede configurar descuentos a tipos de pagos para todos los clientes como un descuento adicional, este tiene que tener una fecha de caducidad.

- Asignación de Ruta y Bodegas Móviles:

- o Puede seleccionar del mapa o de una lista de pedidos en espera de asignación de ruta para ir asignando los pedidos a un camión activo.

- o Este también tendrá un card donde se ven los vehículos que se están asignado los pedidos.

- o La asignación termina cuando el gestor de ruta cierre la asignación y esto crea una guía de remisión visual igual a las sugeridas por el SRI y una guía de ruta con los negocios a visitar, con montos a cobrar y tipos de pagos.


- o Cuando se genere debe crear una transacción de ingreso a la bodega del camión. Cada camión administrará su bodega con transacciones de ingresos y egresos en la base de datos.

- o El módulo debe ser capaz de gestionar los vehículos, crear camiones, cambiar de estado por temas de mantenimiento y averías, y asignar choferes los cuales son usuarios tipo chofer.

- Aprobación de Pagos: Aprobar pedidos con tipo de pagos de depósito y de la aplicación De Una. Cuando un usuario haya hecho un pedido con estos pagos debe pasar por una aprobación y los requisitos son el comprobante de depósito para validar el pago. El pedido está en estado en espera por aprobación de pago. Los pagos de TC, TD y Efectivo estos se aprueban automáticamente y se dejan en espera de asignación de ruta.

- Cierres de Guías y Arqueo (Encerar Bodega):

- o Cuando el chofer haga su arqueo de caja, este aparecerá en estado de confirmación de cierre en la guía y en el camión.

- o En la página principal de la gestión debe aparecer un card de guías pendientes por cerrar.

- o El gestor confirma la recepción de la mercadería devuelta y el dinero para cerrar la caja y encerar la bodega (dejar el inventario del camión en cero).

- o Si los productos están en buen estado, debe actualizar el inventario master de los productos y generar transacciones de ingreso por mercadería en buen estado.

- o La de mal estado no debe ingresar, esta debe registrarse como mercadería mal estado en otra tabla.

- Listado de Facturas (Simuladas): Pantalla con filtros donde Administradores/Operadores visualizan facturas y pueden exportar a PDF (procesado del lado del cliente).

## 5.3. Módulo de Entregas (Chofer)

- Inventario Físico: Debe ser capaz de ver los productos del camión existentes en el camión e ir actualizando el inventario.

- Mapas y Navegación: Al momento de seleccionar la guía debe desplegarse el mapa donde estarán puntillado los pedidos a entregar. Estas deben permitir ordenar por el punto más cercano de la ubicación actual y también por la antigüedad que tiene el pedido solicitado, el ordenamiento debe realizarlo el front end. Cuando ocurra la selección del pedido, este pasará a estado Listo a ser entregado. Este debe ser seleccionado en el mapa de la aplicación web y cuando ocurra la aplicación direccionará a Google Maps o a Waze la ubicación y el chofer pueda navegar en estos mapas de aplicaciones externas y dirigirse a la ubicación de entrega.

- Tracking Constante: Al momento de seleccionar la guía debe compartirse la ubicación y esta debe ser guardada en Firestore cada cierto tiempo configurado en un environment.


- Ejecución y Facturación: Cuando se entregue un pedido de forma parcial o individual, generar una factura muy parecida a las autorizadas por el SRI como simulación. Crear una transacción para disminuir la cantidad fsica y la cantidad en pedido.

- Devoluciones: Las devoluciones parciales solo aplican en pedidos en efectivo, si el pago es de otro tipo la devolución es total.

- Cierre de Caja: Guías pendientes de cierre, un reporte visual dinero por cada guía de ruta para realizar el cierre de la caja del camión, tener la opción de cierre donde debe declarar el valor en efectivo actual.

## 5.4. Módulo de Clientes (E-commerce)

- Catálogo y Caché: Los productos deben mostrarse como una página e-commerce de fácil uso. La foto esta debe obtenerse de GCS y de ser posible que se guarde en caché del lado del cliente con una duración de 4 horas para ahorrar costos de GCS, el tiempo de permanencia de caché debe ser configurable.

- Detalles y Alertas: Mostrar información del producto como peso, tipo de producto (Cheetos, Papas, Doritos, Tostitos, etc.), y una breve descripción. También información de descuento y, cuando el producto se acerque a un porcentaje de agotamiento de stock, mostrar las unidades disponibles. Cuando no tenga stock este debe mostrar información que no hay ítems disponibles y solo va a permitir ver información del producto. Los productos pueden filtrarse por tipo de producto y ordenarse por tipo, por precio, por nombre.

- Carrito de Compras: Un usuario sin autentificarse o autentificado puede seleccionar productos y subir al carrito de compras. Cuando el usuario esté por seleccionar producto al carrito de compras, antes debo especificar la cantidad y debe mostrarme el subtotal. Si escoge el mismo producto debe realizar un merge aumentando la cantidad. Puede modificar el carrito de compras, eliminar productos, agregar cantidades o disminuir.

- Checkout y Autenticación: Cuando quiera hacer el checkout debe pedir autentificarse o crear un usuario.

- Inventario Lógico: En el inventario master habrá una funcionalidad, cuando finalice el checkout el inventario debe tener tres campos: CantidadFisica, EnPedidos y la disponible será la cantidad fsica menos la cantidad en pedidos.

- Gestión de Direcciones y Mapa (Bidireccional):

- o Los datos requeridos para el cliente es información de facturación, y puede registrar más de una dirección con ubicación del mapa.

- o Por defecto debe usar la ubicación actual, mover el punto de entrega y su dirección debe aparecer en el cuadro de texto.

- o También debe permitir buscar una dirección o coordenadas con un cuadro de texto, cuando ocurra esto el ping en el mapa debe moverse a lo que indica el cuadro de texto.


- o Puede seleccionar la dirección por defecto que aparecerá en los pedidos. El usuario debe permitir cambiar con otra o crear, editar o eliminar direcciones en el proceso de ingreso de datos o checkout.

- Liquidación de Pago: En el proceso de check out debe aparecer el detalle de los productos y cálculo de descuentos, impuesto IVA, total y sub total. Siempre debe mostrar el total del pedido y el ahorro por los descuentos configurados.

- Pasarela (Simulada): Simular el pago de tarjeta de débito, de crédito y el QR de De Una para pagos con De Una.

- Historial y PDF (Lado del Cliente): Módulo de historial de pedidos pasados. Generación de facturas en PDF procesada estrictamente utilizando los recursos del navegador/dispositivo del usuario para no recargar el servidor.

- Rastreo del Cliente: El cliente puede ver y recibir una notificación de la aplicación si el chofer eligió el pedido como "Listo para entregar". El cliente solo verá la ubicación del camión cuando el chofer seleccione el pedido y este pasará a este estado. Puede visualizar en el mapa la ubicación, el refresco de la ubicación del mapa será configurada en el archivo de environment.

## Análisis Previo del Sistema

A continuación, se detalla el análisis estructurado de la información proporcionada para establecer el contexto de la aplicación.

## Actores Involucrados

- Administrador: Gestiona usuarios (crear, inactivar, resetear contraseñas), visualiza el Dashboard de KPIs y tiene acceso a visores de facturas y exportación PDF.

- Operador de Ruta: Gestiona pedidos, aprueba pagos, asigna rutas a camiones, genera guías de remisión/rutas y supervisa el cierre de caja de camiones.

- Chofer: Gestiona la entrega en ruta, usa navegación en vivo, registra entregas/devoluciones, genera facturas simuladas y realiza su arqueo/cierre de caja.

- Cliente (Invitado/Registrado): Explora el catálogo, gestiona el carrito de compras, realiza el checkout (requiere registro), sube comprobantes y rastrea su pedido.

## Procesos Principales

- Exploración de catálogo y gestión de carrito de compras.

- Checkout, cálculo de totales y selección de método de pago.

- Aprobación de pagos (manual para depósitos/De Una, automática para el resto).

- Asignación de rutas y generación de guías (remisión y ruta).

- Navegación GPS, entrega de pedidos y facturación in situ.

- Cierre de caja, encerado de bodega móvil y actualización de inventario máster.

- Visualización de métricas y filtrado de datos (máximo 30 días).


## Reglas de Negocio Principales

- RN-A: Los pedidos pagados con Depósito o "De Una" requieren carga de comprobante y aprobación manual por el Operador de Ruta. Los pagos con TC, TD o Efectivo se aprueban automáticamente.

- RN-B: Las devoluciones parciales de mercadería durante la entrega solo están permitidas si el método de pago original es Efectivo. Para otros métodos, la devolución debe ser total.

- RN-C: Las consultas con filtros de fechas personalizadas tienen un límite estricto de máximo 30 días de rango entre la fecha de inicio y fin.

- RN-D: La mercadería devuelta en buen estado incrementa el inventario máster. La mercadería en mal estado se registra en una tabla separada y no afecta el stock disponible de venta.

## Fórmulas de Cálculo

- Inventario Lógico: \$CantidadDisponible = CantidadFisica - EnPedidos\$.

- Liquidación de Pedido: \$TotalPedido = (Subtotal - Descuentos) + ImpuestoIVA\$.

## Excepciones

- Intentar buscar datos con un rango de fechas mayor a 30 días.

- Intentar realizar una devolución parcial en un pedido pagado con Tarjeta de Crédito/Débito.

- Intentar agregar un producto al carrito cuando la cantidad solicitada supera la \$CantidadDisponible\$.

## Evidencias Requeridas

- Guías de Remisión y Guías de Ruta.

- Comprobantes de pago (imágenes/PDF subidos por el cliente).

- Facturas simuladas en formato PDF (procesadas del lado del cliente).

- Bitácoras de auditoría y tracking GPS guardado en Firestore.

## Reportes Necesarios

- Dashboard de KPIs (Efectividad, Ventas, Recaudación, Carritos Abandonados, Stock).

- Visor y exportación de Facturas.

- Reporte de Cierre de Caja por camión.

Épica: Módulo de Clientes (E-commerce)

## HU-001 - Liquidación de Pago y Checkout

Como: Cliente registrado Quiero: Visualizar el detalle de mi carrito, aplicar descuentos, calcular el IVA y elegir un método de pago Para: Completar mi compra con total claridad sobre los montos facturados y asegurar el stock de mis productos. Prioridad: Alta


## Reglas de negocio

- RN-01: El sistema debe calcular el total utilizando la fórmula \$TotalPedido = (Subtotal - Descuentos) + ImpuestoIVA\$.

- RN-02: Si el cliente elige "Depósito" o "De Una", debe obligatoriamente adjuntar un comprobante. El pedido quedará en estado "En espera por aprobación".

- RN-03: Al finalizar el checkout, el inventario del producto debe actualizarse afectando el campo EnPedidos.

## Criterios de aceptación en Gherkin

Característica: Checkout y Liquidación de compras

Escenario: Flujo principal exitoso con pago en efectivo Dado que el cliente tiene productos en su carrito de compras Y se encuentra autenticado en el sistema Cuando procede a la pantalla de checkout y selecciona "Efectivo" como método de pago Entonces el sistema calcula y muestra el \$TotalPedido\$ Y el sistema registra el pedido en estado "En espera de asignación de ruta" Y el sistema actualiza el inventario sumando la cantidad solicitada al campo EnPedidos.

Escenario: Validación obligatoria de comprobante para depósito Dado que el cliente selecciona "Depósito" en el checkout Cuando intenta finalizar el pedido sin adjuntar un documento Entonces el sistema bloquea la acción Y muestra un mensaje de error "Debe adjuntar el comprobante de depósito para continuar".

Escenario: Excepción por datos incompletos en la dirección Dado que el cliente se encuentra en la pantalla de checkout Cuando no selecciona ni registra una dirección de entrega válida en el mapa bidireccional Entonces el botón de "Finalizar Compra" se deshabilita Y se genera una alerta visual indicando "Dirección de entrega obligatoria".

## Datos o campos requeridos

| Campo | Tipo de dato | Obligatorio | Validación |
| --- | --- | --- | --- |
|   |   |   | Valores permitidos: |
| MetodoPago | Lista desplegable | Sí | Efectivo, Depósito, De |
|   |   |   | Una, TC, TD |
| Comprobante | Archivo | Condicional | Obligatorio si MetodoPago |
|   | (Imagen/PDF) |   | es Depósito o De Una |
| DireccionEntrega Coordenadas/Texto Sí |   |   | Debe existir en la base o |
|   |   |   | seleccionarse del mapa |

## Dependencias

- Historia o módulo relacionado: Módulo de Gestión de Direcciones y Mapa Bidireccional; Módulo de Autenticación de Usuarios.

## Evidencias esperadas


- Registro generado en la base de datos MySQL (Tabla de Pedidos).

- Documento o archivo adjunto guardado en Google Cloud Storage (GCS).

- Bitácora de auditoría detallando la fecha, hora y usuario que generó el pedido.

Épica: Módulo de Gestión de Pedidos

## HU-002 - Asignación de Rutas y Generación de Guías

Como: Operador de Ruta Quiero: Seleccionar pedidos en espera y asignarlos a un camión activo Para: Generar automáticamente la guía de remisión, la guía de ruta y registrar el ingreso de inventario en la bodega móvil del vehículo. Prioridad: Alta

## Reglas de negocio

- RN-01: Solo los camiones en estado "Activo" pueden recibir asignaciones de pedidos.

- RN-02: Un pedido no puede asignarse a más de un camión simultáneamente.

- RN-03: Al cerrar la asignación, se genera una transacción automática de ingreso desde la bodega máster a la bodega del camión seleccionado.

## Criterios de aceptación en Gherkin

Característica: Asignación de pedidos a camiones

Escenario: Flujo principal exitoso de asignación Dado que existen pedidos en estado "En espera de asignación de ruta" Y el Operador de Ruta tiene un camión "Activo" seleccionado Cuando asigna los pedidos y hace clic en "Cerrar Asignación" Entonces el sistema genera una Guía de Remisión visual Y genera una Guía de Ruta con los negocios, montos y tipos de pago Y crea una transacción de ingreso de inventario en la bodega del camión.

Escenario: Prevención de registros duplicados Dado que un pedido con ID "PED-123" ya fue asignado al "Camión A" Cuando el Operador de Ruta intenta asignarlo al "Camión B" Entonces el sistema rechaza la operación Y muestra una alerta "El pedido ya se encuentra en ruta con otro vehículo".

Escenario: Permisos según el rol del usuario Dado que un usuario con rol "Chofer" inicia sesión en el sistema Cuando intenta acceder a la pantalla de Asignación de Rutas Entonces el sistema deniega el acceso Y redirige al usuario a su Módulo de Entregas con el mensaje "No tiene permisos para esta acción".

## Datos o campos requeridos

| Campo | Tipo de | Obligatorio | Validación |
| --- | --- | --- | --- |
|   | dato |   |   |
| ID_Pedido Entero |   | Sí | Debe existir y estar en estado "En espera |
|   |   |   | de asignación" |
| ID_Camion Entero |   | Sí | Debe existir y estar en estado "Activo" |


## Dependencias

- Historia o módulo relacionado: Aprobación de Pagos (solo llegan pedidos aprobados o automáticos); Módulo de Autenticación.

## Evidencias esperadas

- Registro generado: Guía de Remisión y Guía de Ruta en MySQL.

- Transacción de inventario en la base de datos de bodegas móviles.

- Bitácora de auditoría con la trazabilidad de la asignación (Operador, Camión, Fecha).

Épica: Módulo de Entregas (Chofer)

## HU-003 - Ejecución de Entrega, Devolución y Facturación

Como: Chofer Quiero: Registrar la entrega parcial o total de un pedido en la ubicación del cliente Para: Descontar el inventario fsico del camión y generar la factura simulada en formato PDF. Prioridad: Alta

## Reglas de negocio

- RN-01: Si el pedido fue pagado con Efectivo, el chofer puede registrar una entrega parcial (devolución parcial).

- RN-02: Si el pedido fue pagado por métodos distintos a Efectivo, cualquier devolución debe ser obligatoriamente total.

- RN-03: Al confirmar la entrega, la factura PDF debe procesarse estrictamente del lado del cliente (navegador/dispositivo) para no recargar el servidor.

## Criterios de aceptación en Gherkin

Característica: Entrega de pedidos y facturación

Escenario: Flujo principal exitoso de entrega total Dado que el Chofer se encuentra en la ubicación del cliente Y el pedido está en estado "Listo a ser entregado" Cuando marca el pedido como "Entregado totalmente" Entonces el sistema descuenta la cantidad fsica del inventario del camión Y descuenta la cantidad en pedido del inventario Y genera

automáticamente la factura en PDF procesada en el navegador.

Escenario: Excepciones establecidas por la normativa (Devolución parcial no permitida) Dado que el pedido fue pagado con "Tarjeta de Crédito" Cuando el Chofer intenta registrar una devolución parcial de mercadería Entonces el sistema bloquea la entrada de cantidades menores al total Y muestra el mensaje de error "Las devoluciones parciales solo aplican para

pagos en efectivo. Proceda con devolución total o entrega completa."

Escenario: Cálculo automático en entrega parcial (Efectivo) Dado que un pedido de 10

unidades fue pagado en Efectivo Cuando el Chofer registra la entrega de 8 unidades y 2 unidades como devolución Entonces el sistema actualiza el valor a cobrar basado en las 8 unidades Y genera la factura únicamente por el valor recalculado de los artículos entregados Y el estado del pedido cambia a "Entregado Parcialmente".

## Datos o campos requeridos


| Campo | Tipo de | Obligatorio | Validación |
| --- | --- | --- | --- |
|   | dato |   |   |
| CantidadEntregada Entero |   | Sí | Debe ser mayor a 0 y menor o igual |
|   |   |   | a lo solicitado |
| MotivoDevolucion Texto |   | Condicional | Obligatorio si CantidadEntregada < |
|   |   |   | CantidadSolicitada |
| EstadoMercaderia Lista |   | Condicional | Valores: Buen estado, Mal estado |
|   |   |   | (si hay devolución) |

## Dependencias

- Historia o módulo relacionado: Módulo de Navegación GPS (Waze/Google Maps); Inventario Físico de Camiones.

## Evidencias esperadas

- Reporte: Factura simulada generada en PDF.

- Registro de transacción restando el inventario fsico del camión.

- Bitácora de auditoría detallando la ubicación GPS (Firestore) al momento de marcar la entrega.

Épica: Módulo de Gestión de Pedidos (Operativo)

## HU-004 - Aprobación Manual de Pagos con Comprobante

Como: Operador de Ruta Quiero: Revisar y aprobar los pedidos que fueron pagados mediante Depósito o la aplicación "De Una" Para: Validar la legitimidad del pago mediante el comprobante antes de que el pedido pase a la fase de asignación de ruta. Prioridad: Alta

## Reglas de negocio

- RN-01: Los pedidos realizados con pagos de Tarjeta de Crédito (TC), Tarjeta de Débito (TD) y Efectivo se aprueban automáticamente y pasan directo a espera de asignación de ruta.

- RN-02: Los pedidos con métodos de pago "Depósito" o "De Una" deben permanecer en estado "En espera por aprobación de pago" hasta que un operador valide el comprobante.

## Criterios de aceptación en Gherkin

Característica: Aprobación de pagos en pedidos

Escenario: Flujo principal exitoso de aprobación manual Dado que un pedido se encuentra en estado "En espera por aprobación de pago" Y el cliente adjuntó el comprobante de depósito Cuando el Operador de Ruta revisa el documento y selecciona "Aprobar Pago" Entonces el


sistema cambia el estado del pedido a "En espera de asignación de ruta" Y genera un registro en la bitácora de auditoría detallando la acción.

Escenario: Excepción por método de pago de aprobación automática Dado que un cliente finaliza un checkout con método de pago "Efectivo" Cuando el sistema procesa la orden Entonces el sistema aprueba automáticamente el pedido sin requerir intervención del operador Y lo coloca directamente en la lista de pedidos listos para asignación de ruta.

## Datos o campos requeridos

| Campo Tipo de dato ID_Pedido Entero EstadoActual Texto Archivo | Obligatorio Sí Sí | Validación El pedido debe existir Debe ser "En espera por aprobación de pago" Documento adjunto por el |   |
| --- | --- | --- | --- |
| ComprobantePago (Imagen/PDF) | Sí | cliente en el checkout |   |

## Dependencias

- Historia o módulo relacionado: Módulo de Clientes (Liquidación de Pago y Checkout).

## Evidencias esperadas

- Registro generado con la actualización del estado del pedido.

- Bitácora de auditoría indicando qué operador aprobó la transacción.

Épica: Módulo de Gestión de Pedidos (Operativo)

## HU-005 - Cierre de Guías, Arqueo y Encerado de Bodega

Como: Operador de Ruta Quiero: Confirmar la recepción del dinero en efectivo y procesar la mercadería devuelta por los camiones Para: Realizar el cierre de la guía de ruta, saldar la caja y encerar (dejar en cero) el inventario de la bodega del camión. Prioridad: Alta

## Reglas de negocio

- RN-01: La mercadería devuelta catalogada en "Buen estado" debe generar transacciones de ingreso y actualizar positivamente el inventario máster de productos.

- RN-02: La mercadería devuelta en "Mal estado" no debe ingresar al inventario máster y debe registrarse en una tabla independiente.

## Criterios de aceptación en Gherkin

Característica: Cierre de caja y encerado de bodegas móviles

Escenario: Flujo principal exitoso de cierre con mercadería en buen estado Dado que un camión tiene una guía en estado de "Confirmación de cierre" Y el chofer ha declarado el valor en efectivo actual en su arqueo Cuando el Operador de Ruta confirma la recepción del dinero y


de los productos devueltos en buen estado Entonces el sistema actualiza el inventario máster sumando los productos recibidos Y genera las transacciones de ingreso correspondientes Y el sistema encera el inventario del camión dejándolo en cero.

Escenario: Validación de mercadería en mal estado Dado que el Operador de Ruta procesa el cierre de una guía con productos devueltos Cuando clasifica una parte de la mercadería como "Mal estado" Entonces el sistema registra estos ítems en la tabla exclusiva de mercadería en mal estado Y omite la actualización de estos ítems en el inventario máster disponible.

## Datos o campos requeridos

| Tipo de Campo dato ID_Guia Entero |   | Obligatorio Sí |   |   | Validación |   | Debe estar en estado "Confirmación de cierre" |   |
| --- | --- | --- | --- | --- | --- | --- | --- | --- |
| EfectivoRecibido Decimal EstadoMercaderia Lista |   | Sí Sí |   |   |   |   | Monto entregado por el chofer "Buen estado" o "Mal estado" |   |

## Dependencias

- Historia o módulo relacionado: Módulo de Entregas (Cierre de Caja del Chofer).

## Evidencias esperadas

- Registro generado en el Inventario Máster (si aplica buen estado).

- Registro generado en la tabla de mercadería en mal estado (si aplica).

- Reporte de arqueo de caja cerrado exitosamente.

Épica: Módulo Dashboard y Estadísticas

## HU-006 - Filtros Temporales y de Estado Estilo Datadog

Como: Administrador / Operador de Ruta Quiero: Visualizar los pedidos en un mapa utilizando filtros por rangos de fechas personalizables y tarjetas de estados Para: Monitorizar la operación y localizar rápidamente los pedidos según su situación actual. Prioridad: Media

## Reglas de negocio

- RN-01: El filtro de fechas personalizado no puede exceder un límite de consulta máximo de 30 días entre la fecha de inicio y la fecha fin.

- RN-02: Al presionar un "Card Informativo" de estado, el sistema debe filtrar automáticamente la vista principal mostrando únicamente los pedidos con dicho estado.

## Criterios de aceptación en Gherkin

Característica: Filtros de búsqueda estilo Datadog y tarjetas informativas


Escenario: Flujo principal exitoso con atajo de filtro Dado que el usuario se encuentra en el Módulo de Gestión de Pedidos Cuando ingresa el comando "1w" en el cuadro de texto de fechas Entonces el sistema configura la fecha de inicio a 1 semana antes de la fecha actual Y configura la fecha final como la fecha y hora actual Y actualiza la información mostrada en pantalla.

Escenario: Validación obligatoria de límite de 30 días en consulta custom Dado que el usuario utiliza el filtro custom ingresando fechas manualmente Cuando define un rango superior a 30 días entre inicio y fin Entonces el sistema bloquea la búsqueda Y genera un mensaje de error indicando que la consulta no puede sobrepasar los 30 días.

## Datos o campos requeridos

| Tipo de Campo dato | Obligatorio | Validación Valores como: Hoy, Ayer, 1d-30d, 1w- |   |
| --- | --- | --- | --- |
| FiltroFecha Texto/Atajo Sí |   |   |   |
| RangoFechas Date | Condicional | 4w, o custom La diferencia entre fechas no puede superar 30 días |   |
| FiltroEstado Botón/Card No |   | Estados: En espera, Entregados, En Ruta, etc. |   |

## Dependencias

- Historia o módulo relacionado: Módulo de Gestión de Pedidos.

## Evidencias esperadas

- Reporte visual filtrado exitosamente en la interfaz (Front end).

Épica: Módulo de Clientes (E-commerce)

## HU-007 - Gestión del Carrito de Compras

Como: Cliente Quiero: Agregar productos al carrito, modificar sus cantidades y visualizar el subtotal de mi pedido Para: Preparar mi orden de compra guardando el estado de mi selección sin necesidad inmediata de iniciar sesión. Prioridad: Alta

## Reglas de negocio

- RN-01: Si el usuario selecciona un producto que ya se encuentra en el carrito, el sistema debe realizar un merge (fusión) aumentando únicamente la cantidad de dicho ítem.

- RN-02: El estado del carrito de compras debe preservarse utilizando cookies seguras expirables (Persistencia Temporal).

## Criterios de aceptación en Gherkin


Característica: Administración de ítems en el carrito de compras

Escenario: Flujo principal exitoso agregando cantidades al carrito Dado que el usuario está visualizando el catálogo de productos Y selecciona un ítem disponible Cuando especifica la cantidad deseada y lo añade al carrito de compras Entonces el sistema agrega el ítem al listado del carrito Y muestra inmediatamente el subtotal calculado.

Escenario: Prevención de duplicados mediante merge de productos Dado que el cliente tiene 2 unidades de "Papas Lays" en su carrito Cuando vuelve al catálogo y añade 3 unidades más del mismo producto Entonces el sistema no crea una nueva línea en el carrito Y realiza un merge actualizando la cantidad del producto a 5 unidades.

## Datos o campos requeridos

| Tipo de Campo dato ID_Producto Entero | Obligatorio Sí | Validación Debe existir en el catálogo e-commerce |   |
| --- | --- | --- | --- |
| Cantidad Entero | Sí | Debe ser mayor a cero y menor o igual al stock disponible |   |
| CookieSesion Texto | Automático | Se gestiona de forma segura expirable en el navegador |   |

## Dependencias

- Historia o módulo relacionado: Catálogo e Inventario Lógico.

## Evidencias esperadas

- Registro temporal generado en la cookie segura del cliente.

---

## 6. Diagramas UML

A continuación se presentan los diagramas UML del sistema **Fritolay Ambato**, modelando su arquitectura, comportamiento y base de datos.

---

### 6.1 Diagrama de Clases

Representa la estructura orientada a objetos del sistema, con sus entidades principales, atributos y relaciones.

```mermaid
classDiagram
    direction TB

    class Usuario {
        +int id
        +string nombre
        +string email
        +string passwordHash
        +string rol
        +bool activo
        +datetime creadoEn
        +login()
        +recuperarCredenciales()
        +cambiarPassword()
    }

    class Cliente {
        +int id
        +int usuarioId
        +string ruc_cedula
        +string razonSocial
        +string telefono
        +verHistorialPedidos()
        +obtenerDireccionDefecto()
    }

    class DireccionCliente {
        +int id
        +int clienteId
        +string descripcion
        +float latitud
        +float longitud
        +bool esPorDefecto
    }

    class Producto {
        +int id
        +string nombre
        +string tipo
        +string descripcion
        +float precio
        +string imagenGCS
        +float cantidadFisica
        +float enPedidos
        +calcularDisponible()
        +estaAgotado()
    }

    class Carrito {
        +string cookieId
        +int clienteId
        +datetime expiresAt
        +agregarItem(productoId, cantidad)
        +eliminarItem(productoId)
        +mergItem(productoId, cantidad)
        +calcularSubtotal()
        +vaciar()
    }

    class ItemCarrito {
        +int id
        +string carritoId
        +int productoId
        +int cantidad
        +float precioUnitario
        +calcularSubtotal()
    }

    class Pedido {
        +int id
        +int clienteId
        +int direccionId
        +string estado
        +string metodoPago
        +string comprobantePath
        +float subtotal
        +float descuento
        +float iva
        +float total
        +datetime creadoEn
        +calcularTotal()
        +aprobar()
        +cancelar()
        +asignarRuta()
    }

    class ItemPedido {
        +int id
        +int pedidoId
        +int productoId
        +int cantidadSolicitada
        +int cantidadEntregada
        +float precioUnitario
        +float descuento
    }

    class Camion {
        +int id
        +string placa
        +string descripcion
        +string estado
        +int choferId
        +cambiarEstado(nuevoEstado)
        +asignarChofer(userId)
    }

    class GuiaRemision {
        +int id
        +int camionId
        +int operadorId
        +datetime fechaGeneracion
        +string estado
        +cerrar()
        +generarPDF()
    }

    class GuiaRuta {
        +int id
        +int guiaRemisionId
        +datetime fechaCreacion
        +string estado
        +obtenerPedidos()
        +cerrarRuta()
    }

    class AsignacionPedidoCamion {
        +int id
        +int pedidoId
        +int guiaRutaId
        +int orden
        +string estado
    }

    class Factura {
        +int id
        +int pedidoId
        +string numero
        +datetime fechaEmision
        +float subtotal
        +float iva
        +float total
        +generarPDF()
    }

    class TransaccionInventario {
        +int id
        +int productoId
        +int camionId
        +string tipo
        +float cantidad
        +string motivo
        +datetime fechaTransaccion
    }

    class BodegaCamion {
        +int id
        +int camionId
        +int productoId
        +float cantidadActual
        +actualizarStock(delta)
        +encerar()
    }

    class Descuento {
        +int id
        +string tipo
        +float porcentaje
        +string metodoPagoAplicable
        +int clienteId
        +datetime fechaCaducidad
        +bool estaVigente()
    }

    class MercaderiamalEstado {
        +int id
        +int guiaRutaId
        +int productoId
        +float cantidad
        +string motivo
        +datetime registradoEn
    }

    class UbicacionGPS {
        +string camionId
        +float latitud
        +float longitud
        +datetime timestamp
    }

    Usuario <|-- Cliente : es un
    Cliente "1" --> "0..*" DireccionCliente : tiene
    Cliente "1" --> "0..*" Pedido : realiza
    Cliente "1" --> "0..1" Carrito : posee
    Carrito "1" --> "1..*" ItemCarrito : contiene
    ItemCarrito --> Producto : referencia
    Pedido "1" --> "1..*" ItemPedido : contiene
    ItemPedido --> Producto : referencia
    Pedido --> DireccionCliente : entrega en
    Pedido --> Factura : genera
    Pedido --> Descuento : aplica
    GuiaRemision "1" --> "1" Camion : asignada a
    GuiaRemision "1" --> "1..*" GuiaRuta : tiene
    GuiaRuta "1" --> "1..*" AsignacionPedidoCamion : incluye
    AsignacionPedidoCamion --> Pedido : asigna
    Camion "1" --> "1..*" BodegaCamion : administra
    BodegaCamion --> Producto : almacena
    TransaccionInventario --> Producto : afecta
    TransaccionInventario --> Camion : relacionada a
    GuiaRuta --> MercaderiamalEstado : registra
    Camion --> UbicacionGPS : transmite
    Usuario --> Camion : conduce
```

---

### 6.2 Diagrama de Casos de Uso

Describe las interacciones de los cuatro actores con las funcionalidades del sistema.

```mermaid
flowchart TD
    subgraph Actores
        CLI(["👤 Cliente"])
        ADM(["👤 Administrador"])
        OPE(["👤 Operador de Ruta"])
        CHO(["👤 Chofer"])
    end

    subgraph EC["📦 Módulo E-commerce"]
        UC01["Ver Catálogo de Productos"]
        UC02["Gestionar Carrito de Compras"]
        UC03["Realizar Checkout"]
        UC04["Adjuntar Comprobante de Pago"]
        UC05["Ver Historial de Pedidos"]
        UC06["Rastrear Pedido en Mapa"]
        UC07["Gestionar Direcciones (Mapa Bidireccional)"]
        UC08["Generar Factura PDF (cliente)"]
    end

    subgraph GP["🗂️ Módulo Gestión de Pedidos"]
        UC09["Aprobar Pago con Comprobante"]
        UC10["Asignar Pedidos a Camión"]
        UC11["Generar Guía de Remisión y Ruta"]
        UC12["Ver Mapa en Vivo con Filtros Datadog"]
        UC13["Ver Cards Informativos por Estado"]
        UC14["Configurar Descuentos"]
        UC15["Confirmar Cierre de Guía y Encerado"]
        UC16["Ver Visor de Facturas y Exportar PDF"]
    end

    subgraph EN["🚚 Módulo Entregas (Chofer)"]
        UC17["Ver Guía de Ruta Asignada"]
        UC18["Navegar con Google Maps / Waze"]
        UC19["Registrar Entrega Total o Parcial"]
        UC20["Registrar Devolución"]
        UC21["Ver Inventario del Camión"]
        UC22["Realizar Cierre de Caja"]
        UC23["Compartir Ubicación GPS (Firestore)"]
    end

    subgraph DB["📊 Módulo Dashboard"]
        UC24["Ver KPIs y Estadísticas"]
        UC25["Ver Ventas por Sector / Camión"]
        UC26["Ver Recaudación por Método de Pago"]
        UC27["Ver Carritos Abandonados"]
        UC28["Consultar Stock de Bodegas"]
    end

    subgraph ADM_MOD["⚙️ Módulo Administración"]
        UC29["Crear / Inactivar Usuarios Empleados"]
        UC30["Resetear Contraseñas"]
        UC31["Gestionar Vehículos (CRUD)"]
    end

    CLI --> UC01
    CLI --> UC02
    CLI --> UC03
    CLI --> UC04
    CLI --> UC05
    CLI --> UC06
    CLI --> UC07
    CLI --> UC08

    OPE --> UC09
    OPE --> UC10
    OPE --> UC11
    OPE --> UC12
    OPE --> UC13
    OPE --> UC14
    OPE --> UC15
    OPE --> UC16
    OPE --> UC31

    CHO --> UC17
    CHO --> UC18
    CHO --> UC19
    CHO --> UC20
    CHO --> UC21
    CHO --> UC22
    CHO --> UC23

    ADM --> UC24
    ADM --> UC25
    ADM --> UC26
    ADM --> UC27
    ADM --> UC28
    ADM --> UC29
    ADM --> UC30
    ADM --> UC16
    ADM --> UC12
    ADM --> UC13
```

---

### 6.3 Diagrama de Secuencia

Modela el flujo completo del **Checkout y Liquidación de Pago** (HU-001) entre el cliente y los distintos componentes del sistema.

```mermaid
sequenceDiagram
    actor Cliente
    participant FE as Frontend (Laravel Blade)
    participant API as Backend REST API
    participant DB as MySQL
    participant GCS as Google Cloud Storage
    participant FS as Firestore (GPS)
    participant Email as Servicio Email

    Cliente->>FE: Accede al carrito de compras
    FE->>FE: Lee cookie de sesión del carrito
    FE->>Cliente: Muestra items del carrito y subtotal

    Cliente->>FE: Inicia checkout
    FE->>FE: ¿Está autenticado?
    alt No autenticado
        FE->>Cliente: Redirige a Login / Registro
        Cliente->>FE: Envía credenciales
        FE->>API: POST /auth/login
        API->>DB: Verifica hash de contraseña
        DB-->>API: OK
        API-->>FE: JWT Token
    end

    FE->>Cliente: Muestra pantalla de checkout
    Cliente->>FE: Selecciona dirección de entrega (mapa bidireccional)
    Cliente->>FE: Selecciona método de pago

    alt Método de pago = Depósito o De Una
        Cliente->>FE: Adjunta comprobante de pago
        FE->>GCS: PUT /upload comprobante
        GCS-->>FE: URL del archivo
    end

    FE->>API: POST /pedidos (items, dirección, método, comprobante)
    API->>API: Valida campos (anti XSS / SQL Injection)
    API->>DB: Verifica stock disponible (CantidadFisica - EnPedidos)

    alt Stock insuficiente
        API-->>FE: 422 Error "Stock no disponible"
        FE->>Cliente: Muestra alerta de stock
    else Stock OK
        API->>DB: Crea registro Pedido
        API->>DB: Incrementa EnPedidos del Producto
        API->>DB: Crea ItemsPedido

        alt Pago = TC / TD / Efectivo
            API->>DB: Estado pedido = "En espera de asignación de ruta"
        else Pago = Depósito / De Una
            API->>DB: Estado pedido = "En espera por aprobación de pago"
        end

        API->>DB: Registra en bitácora de auditoría
        API-->>FE: 201 Created (id pedido)
        FE->>FE: Genera factura proforma PDF (lado cliente)
        FE->>Cliente: Muestra confirmación del pedido y PDF
        FE->>Email: Notificación de pedido recibido
    end
```

---

### 6.4 Diagrama de Colaboración

Representa las interacciones entre los objetos del sistema durante el proceso de **Asignación de Rutas y Generación de Guías** (HU-002).

```mermaid
flowchart LR
    OPE(["Operador de Ruta"])

    subgraph Sistema
        direction TB
        PantAsig["PantallaAsignacion\n(Frontend)"]
        APICtrl["PedidoController\n(REST API)"]
        SrvRuta["RutaService"]
        RepPedido["PedidoRepository"]
        RepCamion["CamionRepository"]
        RepGuia["GuiaRepository"]
        RepBodega["BodegaRepository"]
        MySQL[("MySQL")]
    end

    OPE -- "1: seleccionarPedidos(ids[])" --> PantAsig
    OPE -- "2: seleccionarCamion(id)" --> PantAsig
    PantAsig -- "3: POST /asignaciones" --> APICtrl
    APICtrl -- "4: validarPedidos(ids[])" --> RepPedido
    RepPedido -- "5: SELECT pedidos activos" --> MySQL
    MySQL -- "6: retorna pedidos" --> RepPedido
    APICtrl -- "7: validarCamion(id)" --> RepCamion
    RepCamion -- "8: SELECT camion activo" --> MySQL
    MySQL -- "9: retorna camion" --> RepCamion
    APICtrl -- "10: crearAsignacion()" --> SrvRuta
    SrvRuta -- "11: crearGuiaRemision()" --> RepGuia
    SrvRuta -- "12: crearGuiaRuta(pedidos)" --> RepGuia
    RepGuia -- "13: INSERT guias" --> MySQL
    SrvRuta -- "14: crearTransaccionIngreso(productos, camion)" --> RepBodega
    RepBodega -- "15: INSERT transacciones_inventario" --> MySQL
    RepBodega -- "16: UPDATE bodega_camion" --> MySQL
    SrvRuta -- "17: actualizarEstadoPedidos('Listo para entregar')" --> RepPedido
    RepPedido -- "18: UPDATE pedidos" --> MySQL
    APICtrl -- "19: registrarAuditoria()" --> MySQL
    APICtrl -- "20: retorna guias generadas" --> PantAsig
    PantAsig -- "21: renderizaGuiaRemisionPDF()" --> OPE
    PantAsig -- "22: renderizaGuiaRutaPDF()" --> OPE
```

---

### 6.5 Diagrama de Estado

Modela el **ciclo de vida completo de un Pedido**, desde su creación hasta su cierre.

```mermaid
stateDiagram-v2
    [*] --> CarritoActivo : Cliente agrega productos

    CarritoActivo --> CheckoutIniciado : Cliente inicia checkout
    CheckoutIniciado --> CarritoAbandonado : Cliente cancela
    CarritoAbandonado --> [*] : Registrado con motivo de cancelación

    CheckoutIniciado --> EsperaAprobacion : Pago = Depósito / De Una\n(comprobante adjunto)
    CheckoutIniciado --> EsperaAsignacion : Pago = TC / TD / Efectivo\n(aprobación automática)

    EsperaAprobacion --> EsperaAsignacion : Operador aprueba comprobante
    EsperaAprobacion --> Rechazado : Operador rechaza comprobante
    Rechazado --> [*]

    EsperaAsignacion --> ListoParaEntregar : Operador asigna pedido\na camión activo

    ListoParaEntregar --> EnRuta : Chofer selecciona pedido\nen mapa (GPS activo)

    EnRuta --> EntregadoTotalmente : Chofer registra entrega\ncompleta del pedido
    EnRuta --> EntregadoParcialmente : Chofer registra entrega\nparcial (solo Efectivo)
    EnRuta --> NoEntregado : Chofer no pudo entregar

    EntregadoTotalmente --> CierrePendiente : Factura PDF generada\nen navegador
    EntregadoParcialmente --> CierrePendiente : Factura recalculada\ngenerada en navegador
    NoEntregado --> CierrePendiente : Registrado como no entregado

    CierrePendiente --> CierreCaja : Operador confirma\nrecepción dinero y mercadería

    CierreCaja --> [*] : Bodega del camión\nencerada (stock = 0)
```

---

### 6.6 Diagrama de Paquetes

Muestra la **arquitectura modular** del sistema con sus dependencias entre capas y componentes.

```mermaid
flowchart TB
    subgraph Cliente_Browser["🌐 Navegador / PWA (Cliente)"]
        direction LR
        PKG_EC["📦 Módulo E-commerce\n(Catálogo, Carrito, Checkout,\nHistorial, Rastreo)"]
        PKG_PDF["📄 Generación PDF\n(Factura, lado cliente)"]
        PKG_MAP_CLI["🗺️ Mapas Cliente\n(Leaflet / Google Maps)"]
        PKG_CACHE["⚡ Caché de Imágenes\n(Service Worker / Cache API)"]
    end

    subgraph Frontend["🖥️ Frontend Laravel (Blade + JS)"]
        direction LR
        PKG_AUTH_FE["🔐 Módulo Autenticación\n(Login, Registro, Recuperación)"]
        PKG_DASH["📊 Módulo Dashboard\n(KPIs, Ventas, Stock)"]
        PKG_GP_FE["🗂️ Módulo Gestión Pedidos\n(Asignación, Aprobación,\nFiltros Datadog)"]
        PKG_ENT_FE["🚚 Módulo Entregas\n(Mapa Ruta, Entrega, Cierre)"]
        PKG_ADM_FE["⚙️ Módulo Administración\n(Usuarios, Vehículos)"]
    end

    subgraph Backend["⚙️ Backend Laravel REST API"]
        direction TB
        PKG_AUTH_BE["🔑 AuthService\n(JWT, Hash, Secret Manager)"]
        PKG_PEDIDOS["📋 PedidoService\n(CRUD, Estados, Auditoría)"]
        PKG_INV["📦 InventarioService\n(Stock, Transacciones, Bodega)"]
        PKG_RUTA["🗺️ RutaService\n(Guías, Asignación, GPS)"]
        PKG_NOTIFY["📧 NotificacionService\n(Email, Push PWA)"]
        PKG_VALID["🛡️ ValidationLayer\n(Anti-XSS, Anti-SQLi)"]
    end

    subgraph Datos["💾 Capa de Datos"]
        subgraph MySQL_DB["🐬 MySQL (Datos Transaccionales)"]
            T_USERS["usuarios / clientes"]
            T_PROD["productos / inventario"]
            T_PEDIDOS["pedidos / items_pedido"]
            T_GUIAS["guias_remision / guias_ruta"]
            T_BODEGA["bodega_camion / transacciones"]
            T_AUDIT["bitacora_auditoria"]
        end
        subgraph Firestore_DB["🔥 Firestore (Geolocalización)"]
            FS_GPS["ubicaciones_camion\n(lat, lng, timestamp)"]
        end
    end

    subgraph GCP["☁️ Google Cloud Platform"]
        GCS["🗄️ Google Cloud Storage\n(Imágenes productos,\ncomprobantes pago)"]
        GSM["🔒 Secret Manager\n(JWT Secret, DB Credentials)"]
        GCR["🐳 Container Registry\n(Docker Images)"]
    end

    subgraph Infra["🐳 Infraestructura Docker"]
        D_FE["Contenedor: Frontend"]
        D_BE["Contenedor: Backend API"]
        D_DB["Contenedor: MySQL"]
    end

    Cliente_Browser -->|"HTTPS / JWT"| Frontend
    Frontend -->|"REST API calls"| Backend
    Backend -->|"Queries ORM"| MySQL_DB
    Backend -->|"SDK Firebase"| Firestore_DB
    Backend -->|"SDK GCS"| GCS
    Backend -->|"SDK Secret Manager"| GSM
    PKG_MAP_CLI -->|"Firestore realtime"| Firestore_DB
    D_FE --> Frontend
    D_BE --> Backend
    D_DB --> MySQL_DB
    GCR --> D_FE
    GCR --> D_BE
```

---

### 6.7 Diagrama de Entidad-Relación

Esquema completo de la **base de datos MySQL** con todas las tablas, campos clave y relaciones del sistema.

```mermaid
erDiagram
    USUARIOS {
        int id PK
        string nombre
        string email UK
        string password_hash
        enum rol "administrador|operador|chofer|cliente"
        boolean activo
        datetime creado_en
    }

    CLIENTES {
        int id PK
        int usuario_id FK
        string ruc_cedula UK
        string razon_social
        string telefono
    }

    DIRECCIONES_CLIENTE {
        int id PK
        int cliente_id FK
        string descripcion
        decimal latitud
        decimal longitud
        boolean es_por_defecto
    }

    PRODUCTOS {
        int id PK
        string nombre
        string tipo
        string descripcion
        decimal precio
        string imagen_gcs_path
        decimal cantidad_fisica
        decimal en_pedidos
    }

    DESCUENTOS {
        int id PK
        int cliente_id FK
        enum tipo "individual|global"
        decimal porcentaje
        enum metodo_pago "efectivo|deposito|de_una|tc|td|todos"
        datetime fecha_caducidad
    }

    PEDIDOS {
        int id PK
        int cliente_id FK
        int direccion_id FK
        enum estado "en_espera_aprobacion|en_espera_asignacion|listo_para_entregar|en_ruta|entregado|parcial|no_entregado|cancelado"
        enum metodo_pago "efectivo|deposito|de_una|tc|td"
        string comprobante_path
        decimal subtotal
        decimal descuento
        decimal iva
        decimal total
        string motivo_cancelacion
        datetime creado_en
    }

    ITEMS_PEDIDO {
        int id PK
        int pedido_id FK
        int producto_id FK
        int cantidad_solicitada
        int cantidad_entregada
        decimal precio_unitario
        decimal descuento_aplicado
    }

    CAMIONES {
        int id PK
        string placa UK
        string descripcion
        enum estado "activo|mantenimiento|averia|inactivo"
        int chofer_id FK
    }

    GUIAS_REMISION {
        int id PK
        int camion_id FK
        int operador_id FK
        datetime fecha_generacion
        enum estado "abierta|confirmacion_cierre|cerrada"
        decimal efectivo_declarado
    }

    GUIAS_RUTA {
        int id PK
        int guia_remision_id FK
        datetime fecha_creacion
        enum estado "activa|cerrada"
    }

    ASIGNACION_PEDIDO_CAMION {
        int id PK
        int pedido_id FK
        int guia_ruta_id FK
        int orden
        enum estado "asignado|en_ruta|entregado|no_entregado"
    }

    BODEGA_CAMION {
        int id PK
        int camion_id FK
        int producto_id FK
        decimal cantidad_actual
    }

    TRANSACCIONES_INVENTARIO {
        int id PK
        int producto_id FK
        int camion_id FK
        enum tipo "ingreso|egreso"
        decimal cantidad
        string motivo
        datetime fecha_transaccion
    }

    FACTURAS {
        int id PK
        int pedido_id FK
        string numero_factura UK
        datetime fecha_emision
        decimal subtotal
        decimal iva
        decimal total
    }

    MERCADERIA_MAL_ESTADO {
        int id PK
        int guia_ruta_id FK
        int producto_id FK
        decimal cantidad
        string motivo
        datetime registrado_en
    }

    BITACORA_AUDITORIA {
        int id PK
        int usuario_id FK
        string accion
        string tabla_afectada
        int registro_id
        json datos_anteriores
        json datos_nuevos
        datetime fecha_accion
    }

    CARRITOS_ABANDONADOS {
        int id PK
        int cliente_id FK
        string motivo_cancelacion
        decimal valor_total
        datetime fecha_abandono
    }

    USUARIOS ||--o| CLIENTES : "tiene perfil"
    USUARIOS ||--o| CAMIONES : "conduce"
    CLIENTES ||--o{ DIRECCIONES_CLIENTE : "tiene"
    CLIENTES ||--o{ PEDIDOS : "realiza"
    CLIENTES ||--o{ DESCUENTOS : "tiene descuento individual"
    CLIENTES ||--o{ CARRITOS_ABANDONADOS : "genera"
    PEDIDOS ||--o{ ITEMS_PEDIDO : "contiene"
    PEDIDOS ||--o{ FACTURAS : "genera"
    PEDIDOS }o--|| DIRECCIONES_CLIENTE : "entrega en"
    ITEMS_PEDIDO }o--|| PRODUCTOS : "referencia"
    PRODUCTOS ||--o{ TRANSACCIONES_INVENTARIO : "afectado en"
    PRODUCTOS ||--o{ BODEGA_CAMION : "almacenado en"
    CAMIONES ||--o{ GUIAS_REMISION : "asignado a"
    CAMIONES ||--o{ BODEGA_CAMION : "administra"
    CAMIONES ||--o{ TRANSACCIONES_INVENTARIO : "relacionada a"
    GUIAS_REMISION ||--o{ GUIAS_RUTA : "contiene"
    GUIAS_RUTA ||--o{ ASIGNACION_PEDIDO_CAMION : "incluye"
    ASIGNACION_PEDIDO_CAMION }o--|| PEDIDOS : "asigna"
    GUIAS_RUTA ||--o{ MERCADERIA_MAL_ESTADO : "registra"
    MERCADERIA_MAL_ESTADO }o--|| PRODUCTOS : "producto"
    USUARIOS ||--o{ BITACORA_AUDITORIA : "genera"
    DESCUENTOS }o--|| PEDIDOS : "aplicado en"
```

