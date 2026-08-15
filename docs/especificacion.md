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

Modela las interacciones de los **cuatro actores** con el sistema, con relaciones `<<include>>` y `<<extend>>` que expresan dependencias y extensiones de comportamiento.

```mermaid
flowchart LR
    %% ── Actores ──────────────────────────────────────────────────────────────
    CLI(["\n👤\nCliente\n"])
    ADM(["\n👤\nAdministrador\n"])
    OPE(["\n👤\nOperador\nde Ruta\n"])
    CHO(["\n👤\nChofer\n"])

    %% ── Frontera del sistema ────────────────────────────────────────────────
    subgraph SYS["🖥️  Sistema E-commerce Fritolay Ambato"]

        subgraph EC["📦 E-commerce / Clientes"]
            UC01(["Ver Catálogo\nde Productos"])
            UC02(["HU-007 · Gestionar\nCarrito de Compras"])
            UC03(["HU-001 · Realizar\nCheckout"])
            UC04(["Adjuntar Comprobante\nde Pago"])
            UC05(["Ver Historial\nde Pedidos"])
            UC06(["Rastrear Pedido\nen Mapa"])
            UC07(["Gestionar Direcciones\nMapa Bidireccional"])
            UC08(["Generar Factura\nPDF — lado cliente"])
            UC09(["Autenticarse /\nRegistrarse"])
            UC10(["Recuperar\nCredenciales"])
        end

        subgraph GP["🗂️ Gestión de Pedidos"]
            UC11(["HU-004 · Aprobar Pago\ncon Comprobante"])
            UC12(["HU-002 · Asignar\nPedidos a Camión"])
            UC13(["HU-002 · Generar Guía\nRemisión y Ruta"])
            UC14(["HU-006 · Filtros\nEstilo Datadog"])
            UC15(["HU-006 · Ver Cards\nInformativos por Estado"])
            UC16(["Configurar\nDescuentos"])
            UC17(["HU-005 · Confirmar\nCierre y Encerado"])
            UC18(["Ver Visor\nde Facturas PDF"])
            UC19(["Gestionar\nVehículos CRUD"])
        end

        subgraph EN["🚚 Entregas — Chofer"]
            UC20(["Ver Guía de\nRuta Asignada"])
            UC21(["HU-003 · Registrar\nEntrega Total/Parcial"])
            UC22(["HU-003 · Registrar\nDevolución"])
            UC23(["Ver Inventario\ndel Camión"])
            UC24(["HU-003 · Generar\nFactura PDF in situ"])
            UC25(["Realizar Cierre\nde Caja"])
            UC26(["Compartir Ubicación\nGPS — Firestore"])
            UC27(["Navegar Google\nMaps / Waze"])
        end

        subgraph DB["📊 Dashboard"]
            UC28(["Ver KPIs\ny Estadísticas"])
            UC29(["Ver Ventas por\nSector / Camión"])
            UC30(["Ver Recaudación\npor Método de Pago"])
            UC31(["Ver Carritos\nAbandonados"])
            UC32(["Consultar Stock\nde Bodegas"])
        end

        subgraph ADM_M["⚙️ Administración"]
            UC33(["Crear / Inactivar\nUsuarios Empleados"])
            UC34(["Resetear\nContraseñas"])
        end

        %% ── Relaciones <<include>> y <<extend>> ─────────────────────────
        UC03 -- "<<include>>" --> UC09
        UC03 -- "<<include>>" --> UC07
        UC03 -- "<<extend>>\n[pago Depósito/De Una]" --> UC04
        UC03 -- "<<include>>" --> UC08
        UC02 -- "<<include>>" --> UC01
        UC21 -- "<<extend>>\n[entrega parcial Efectivo]" --> UC22
        UC21 -- "<<include>>" --> UC24
        UC21 -- "<<include>>" --> UC26
        UC20 -- "<<include>>" --> UC27
        UC12 -- "<<include>>" --> UC13
        UC12 -- "<<include>>" --> UC11
        UC05 -- "<<include>>" --> UC09
        UC06 -- "<<include>>" --> UC09
        UC09 -- "<<extend>>\n[credenciales olvidadas]" --> UC10
        UC17 -- "<<include>>" --> UC13
        UC14 -- "<<include>>" --> UC15
    end

    %% ── Asociaciones Actor ↔ Casos de Uso ───────────────────────────────
    CLI --- UC01
    CLI --- UC02
    CLI --- UC03
    CLI --- UC05
    CLI --- UC06
    CLI --- UC09

    OPE --- UC11
    OPE --- UC12
    OPE --- UC14
    OPE --- UC15
    OPE --- UC16
    OPE --- UC17
    OPE --- UC18
    OPE --- UC19

    CHO --- UC20
    CHO --- UC21
    CHO --- UC23
    CHO --- UC25

    ADM --- UC18
    ADM --- UC28
    ADM --- UC29
    ADM --- UC30
    ADM --- UC31
    ADM --- UC32
    ADM --- UC33
    ADM --- UC34
    ADM --- UC14
    ADM --- UC15
```

---

### 6.3 Diagramas de Secuencia — HU-001 a HU-007

Cada diagrama modela el flujo de mensajes entre actores y componentes del sistema para cada Historia de Usuario.

---

#### 6.3.1 HU-001 · Liquidación de Pago y Checkout

```mermaid
sequenceDiagram
    actor Cliente
    participant FE as Frontend
    participant API as Backend API
    participant DB as MySQL
    participant GCS as Cloud Storage
    participant Email as Email Service

    Cliente->>FE: Accede al carrito de compras
    FE->>FE: Lee cookie segura del carrito
    FE->>Cliente: Muestra ítems y subtotal

    Cliente->>FE: Inicia Checkout
    FE->>FE: Verifica token JWT
    alt No autenticado
        FE->>Cliente: Redirige a Login / Registro
        Cliente->>FE: Envía credenciales
        FE->>API: POST /auth/login
        API->>DB: Valida hash de contraseña
        DB-->>API: Usuario válido
        API-->>FE: JWT Token
    end

    FE->>Cliente: Muestra pantalla Checkout
    Cliente->>FE: Selecciona DireccionEntrega (mapa bidireccional)
    Cliente->>FE: Selecciona MetodoPago

    alt MetodoPago = Depósito | De Una
        Cliente->>FE: Adjunta comprobante (imagen/PDF)
        FE->>GCS: PUT /comprobantes/{filename}
        GCS-->>FE: URL pública del archivo
    end

    FE->>API: POST /pedidos {items, dirección, metodoPago, comprobante}
    API->>API: Sanitiza entrada (anti-XSS, anti-SQLi)
    API->>DB: SELECT cantidad_fisica - en_pedidos (por producto)

    alt Stock insuficiente
        API-->>FE: 422 Unprocessable — stock no disponible
        FE->>Cliente: Alerta visual de stock agotado
    else Stock OK
        API->>DB: INSERT pedidos (estado según método de pago)
        API->>DB: UPDATE productos SET en_pedidos += cantidad
        API->>DB: INSERT items_pedido
        API->>DB: INSERT bitacora_auditoria
        API-->>FE: 201 Created {pedidoId, estado}
        FE->>FE: Genera PDF proforma (lado cliente — sin servidor)
        FE->>Cliente: Confirmación + PDF descargable
        FE->>Email: Envía notificación pedido recibido
    end
```

---

#### 6.3.2 HU-002 · Asignación de Rutas y Generación de Guías

```mermaid
sequenceDiagram
    actor Operador as Operador de Ruta
    participant FE as Frontend
    participant API as Backend API
    participant DB as MySQL

    Operador->>FE: Abre módulo Gestión de Pedidos
    FE->>API: GET /pedidos?estado=en_espera_asignacion
    API->>DB: SELECT pedidos WHERE estado = 'en_espera_asignacion'
    DB-->>API: Lista de pedidos
    API-->>FE: Pedidos pendientes
    FE->>Operador: Muestra lista y mapa con pedidos

    Operador->>FE: Selecciona pedidos del mapa/lista
    Operador->>FE: Selecciona camión activo
    FE->>API: GET /camiones?estado=activo
    API->>DB: SELECT camiones WHERE estado = 'activo'
    DB-->>API: Lista de camiones
    API-->>FE: Camiones disponibles
    FE->>Operador: Muestra card de camión seleccionado

    Operador->>FE: Clic "Cerrar Asignación"
    FE->>API: POST /asignaciones {pedidoIds[], camionId}
    API->>DB: Verifica pedidos no asignados
    alt Pedido ya asignado
        API-->>FE: 409 Conflict — pedido ya en ruta
        FE->>Operador: Alerta pedido duplicado
    else Validación OK
        API->>DB: INSERT guias_remision
        API->>DB: INSERT guias_ruta
        API->>DB: INSERT asignacion_pedido_camion
        API->>DB: INSERT transacciones_inventario (ingreso bodega camión)
        API->>DB: UPDATE bodega_camion
        API->>DB: UPDATE pedidos SET estado = 'listo_para_entregar'
        API->>DB: INSERT bitacora_auditoria
        API-->>FE: 201 {guiaRemisionId, guiaRutaId}
        FE->>FE: Renderiza Guía Remisión PDF (lado cliente)
        FE->>FE: Renderiza Guía Ruta PDF (lado cliente)
        FE->>Operador: Muestra guías generadas
    end
```

---

#### 6.3.3 HU-003 · Ejecución de Entrega, Devolución y Facturación

```mermaid
sequenceDiagram
    actor Chofer
    participant FE as Frontend (PWA)
    participant API as Backend API
    participant DB as MySQL
    participant FS as Firestore GPS
    participant ExtMap as Google Maps / Waze

    Chofer->>FE: Abre módulo Entregas
    FE->>API: GET /guias-ruta?estado=activa&choferId={id}
    API->>DB: SELECT guias asignadas al chofer
    DB-->>API: Guías activas
    API-->>FE: Lista de guías
    FE->>Chofer: Muestra guías y mapa con pedidos puntuados

    Chofer->>FE: Selecciona guía de ruta
    FE->>FS: START watch ubicacion_camion/{camionId}
    Note over FE,FS: GPS se comparte en Firestore cada N segundos (configurable)

    Chofer->>FE: Selecciona pedido del mapa
    FE->>API: PATCH /pedidos/{id} {estado: listo_a_ser_entregado}
    API->>DB: UPDATE pedidos
    API-->>FE: OK
    FE->>ExtMap: Abre Google Maps / Waze con coordenadas cliente

    Chofer->>FE: Llega y registra entrega
    FE->>Chofer: Formulario — cantidad entregada / devuelta / estado mercadería

    alt Entrega total
        FE->>API: POST /entregas {pedidoId, cantidadEntregada: total, estado: entregado}
        API->>DB: UPDATE pedidos SET estado = 'entregado'
        API->>DB: UPDATE bodega_camion (egreso físico)
        API->>DB: UPDATE productos SET en_pedidos -= cantidad
        API->>DB: INSERT transacciones_inventario (egreso)
    else Entrega parcial (solo Efectivo)
        FE->>API: POST /entregas {pedidoId, cantidadEntregada, cantidadDevuelta, motivoDevolucion, estadoMercaderia}
        API->>DB: UPDATE pedidos SET estado = 'entregado_parcialmente'
        API->>DB: UPDATE bodega_camion
        API->>DB: UPDATE productos SET en_pedidos -= cantidadEntregada
        API->>DB: INSERT transacciones_inventario
    else Método pago != Efectivo y devolución parcial
        API-->>FE: 422 Error — devolución parcial no permitida
        FE->>Chofer: Mensaje — solo devolución total permitida
    end

    API->>DB: INSERT bitacora_auditoria {ubicacionGPS}
    API-->>FE: 201 {facturaData}
    FE->>FE: Genera Factura PDF (lado cliente — navegador)
    FE->>Chofer: Factura disponible para imprimir/compartir
```

---

#### 6.3.4 HU-004 · Aprobación Manual de Pagos con Comprobante

```mermaid
sequenceDiagram
    actor Operador as Operador de Ruta
    participant FE as Frontend
    participant API as Backend API
    participant DB as MySQL
    participant GCS as Cloud Storage
    participant Email as Email Service

    Operador->>FE: Abre lista de pedidos pendientes de aprobación
    FE->>API: GET /pedidos?estado=en_espera_aprobacion
    API->>DB: SELECT pedidos WHERE estado = 'en_espera_aprobacion'
    DB-->>API: Pedidos con método Depósito / De Una
    API-->>FE: Lista de pedidos
    FE->>Operador: Muestra pedidos con botón "Revisar"

    Operador->>FE: Selecciona pedido y abre comprobante
    FE->>API: GET /pedidos/{id}/comprobante
    API->>GCS: GET URL firmada del archivo
    GCS-->>API: URL
    API-->>FE: URL del comprobante
    FE->>Operador: Muestra imagen/PDF del comprobante

    alt Operador aprueba pago
        Operador->>FE: Clic "Aprobar Pago"
        FE->>API: PATCH /pedidos/{id}/aprobar
        API->>DB: UPDATE pedidos SET estado = 'en_espera_asignacion'
        API->>DB: INSERT bitacora_auditoria {operadorId, accion: 'pago_aprobado'}
        API-->>FE: 200 OK
        FE->>Email: Notifica al cliente — pago aprobado
        FE->>Operador: Confirmación visual
    else Operador rechaza pago
        Operador->>FE: Clic "Rechazar" + motivo
        FE->>API: PATCH /pedidos/{id}/rechazar {motivo}
        API->>DB: UPDATE pedidos SET estado = 'rechazado'
        API->>DB: UPDATE productos SET en_pedidos -= cantidad
        API->>DB: INSERT bitacora_auditoria
        API-->>FE: 200 OK
        FE->>Email: Notifica al cliente — pago rechazado con motivo
        FE->>Operador: Confirmación visual
    end
```

---

#### 6.3.5 HU-005 · Cierre de Guías, Arqueo y Encerado de Bodega

```mermaid
sequenceDiagram
    actor Chofer
    actor Operador as Operador de Ruta
    participant FE_CHO as Frontend Chofer
    participant FE_OPE as Frontend Operador
    participant API as Backend API
    participant DB as MySQL

    Chofer->>FE_CHO: Abre módulo Cierre de Caja
    FE_CHO->>API: GET /guias-ruta/{id}/resumen-caja
    API->>DB: SELECT pedidos entregados + montos por guía
    DB-->>API: Resumen financiero
    API-->>FE_CHO: Reporte visual por guía
    FE_CHO->>Chofer: Muestra dinero esperado por guía

    Chofer->>FE_CHO: Declara efectivo físico en mano
    FE_CHO->>API: POST /guias-ruta/{id}/arqueo {efectivoDeclarado}
    API->>DB: UPDATE guias_remision SET estado='confirmacion_cierre', efectivo_declarado
    API-->>FE_CHO: 200 OK — esperando confirmación del operador
    FE_CHO->>Chofer: Guía en estado pendiente de cierre

    Note over FE_OPE,Operador: Operador ve card de guías pendientes de cierre
    Operador->>FE_OPE: Abre guía en estado confirmacion_cierre
    FE_OPE->>API: GET /guias-remision/{id}/detalle
    API-->>FE_OPE: Detalle de mercadería a recibir y efectivo declarado
    FE_OPE->>Operador: Muestra formulario de recepción de mercadería

    Operador->>FE_OPE: Clasifica mercadería devuelta
    loop Por cada producto devuelto
        alt Mercadería en buen estado
            FE_OPE->>API: POST /inventario/ingreso {productoId, cantidad, motivo: 'devolucion_buen_estado'}
            API->>DB: UPDATE productos SET cantidad_fisica += cantidad
            API->>DB: INSERT transacciones_inventario (ingreso maestro)
        else Mercadería en mal estado
            FE_OPE->>API: POST /mercaderia-mal-estado {guiaRutaId, productoId, cantidad}
            API->>DB: INSERT mercaderia_mal_estado
        end
    end

    Operador->>FE_OPE: Confirma cierre
    FE_OPE->>API: PATCH /guias-remision/{id}/cerrar {efectivoRecibido}
    API->>DB: UPDATE guias_remision SET estado = 'cerrada'
    API->>DB: UPDATE bodega_camion SET cantidad_actual = 0 (encerado)
    API->>DB: INSERT bitacora_auditoria
    API-->>FE_OPE: 200 OK — bodega encerada
    FE_OPE->>Operador: Reporte de arqueo cerrado
```

---

#### 6.3.6 HU-006 · Filtros Temporales y de Estado Estilo Datadog

```mermaid
sequenceDiagram
    actor Usuario as Admin / Operador
    participant FE as Frontend
    participant API as Backend API
    participant DB as MySQL

    Usuario->>FE: Abre Módulo Gestión de Pedidos
    FE->>FE: Aplica filtro default: hoy + estado en_espera_asignacion
    FE->>API: GET /pedidos?fechaInicio=hoy&fechaFin=hoy&estado=en_espera_asignacion
    API->>DB: SELECT pedidos WHERE fecha BETWEEN ? AND ? AND estado = ?
    DB-->>API: Pedidos
    API-->>FE: Resultados
    FE->>Usuario: Muestra mapa + cards informativos por estado

    Usuario->>FE: Escribe atajo en textbox (ej. "1w")
    FE->>FE: Interpreta atajo → fechaInicio = hoy-7d, fechaFin = ahora
    FE->>API: GET /pedidos?fechaInicio={hace7d}&fechaFin={ahora}
    API->>DB: SELECT con rango calculado
    DB-->>API: Resultados
    API-->>FE: Pedidos
    FE->>Usuario: Actualiza vista

    alt Usuario ingresa rango custom > 30 días
        FE->>FE: Valida diferencia de fechas
        FE->>Usuario: Error — rango máximo de 30 días
        Note over FE: Bloquea la petición al API
    else Rango válido ≤ 30 días
        FE->>API: GET /pedidos?fechaInicio={fi}&fechaFin={ff}
        API->>DB: SELECT con rango custom
        DB-->>API: Pedidos
        API-->>FE: Resultados
        FE->>FE: Muestra textbox como "custom"
        FE->>Usuario: Vista actualizada
    end

    Usuario->>FE: Clic en Card Informativo de estado (ej. "En Ruta")
    FE->>FE: Aplica filtro estado = en_ruta
    FE->>API: GET /pedidos?estado=en_ruta&fechaInicio={fi}&fechaFin={ff}
    API->>DB: SELECT con estado filtrado
    DB-->>API: Pedidos en ruta
    API-->>FE: Resultados
    FE->>Usuario: Filtra lista y mapa por estado seleccionado
```

---

#### 6.3.7 HU-007 · Gestión del Carrito de Compras

```mermaid
sequenceDiagram
    actor Cliente
    participant FE as Frontend
    participant Cookie as Cookie Segura (navegador)
    participant API as Backend API
    participant DB as MySQL

    Cliente->>FE: Navega catálogo de productos
    FE->>API: GET /productos?tipo={filtro}&orden={orden}
    API->>DB: SELECT productos WHERE cantidad_fisica - en_pedidos > 0
    DB-->>API: Productos disponibles
    API-->>FE: Catálogo con stock lógico
    FE->>Cliente: Muestra catálogo con precio, tipo y alertas de stock bajo

    Cliente->>FE: Selecciona producto — especifica cantidad
    FE->>FE: Calcula subtotal del ítem
    FE->>FE: ¿Producto ya existe en carrito?

    alt Producto nuevo en carrito
        FE->>Cookie: Agrega item {productoId, cantidad, precio}
        Cookie-->>FE: Carrito actualizado
    else Producto ya en carrito — merge
        FE->>Cookie: Actualiza cantidad del item existente (+= nuevaCantidad)
        Cookie-->>FE: Cantidad fusionada
    end

    FE->>Cliente: Actualiza vista del carrito con nuevo subtotal

    Cliente->>FE: Modifica cantidad de ítem en carrito
    FE->>API: GET /productos/{id} — verifica stock actual
    API->>DB: SELECT cantidad_fisica - en_pedidos
    DB-->>API: Stock disponible
    alt Cantidad > stock disponible
        API-->>FE: Stock insuficiente
        FE->>Cliente: Alerta — cantidad máxima disponible: {X}
    else Cantidad válida
        FE->>Cookie: UPDATE item {nueva cantidad}
        FE->>Cliente: Subtotal recalculado
    end

    Cliente->>FE: Elimina producto del carrito
    FE->>Cookie: DELETE item {productoId}
    Cookie-->>FE: Item eliminado
    FE->>Cliente: Carrito actualizado

    Cliente->>FE: Abandona carrito (cierra/cancela)
    FE->>API: POST /carritos-abandonados {items, valorTotal, motivo}
    API->>DB: INSERT carritos_abandonados
    API-->>FE: Registrado
```

---

### 6.4 Diagramas de Colaboración — HU-001 a HU-007

Cada diagrama muestra los objetos participantes y los **mensajes numerados** que se intercambian para resolver cada historia de usuario.

---

#### 6.4.1 HU-001 · Colaboración — Checkout y Liquidación de Pago

```mermaid
flowchart LR
    CLI(["Cliente"])

    subgraph Sistema
        direction TB
        FE["PaginaCheckout\n(Frontend)"]
        AUTH["AuthController"]
        CTRL["PedidoController"]
        SRV["PedidoService"]
        REP_P["PedidoRepository"]
        REP_PROD["ProductoRepository"]
        GCS[("Cloud Storage")]
        DB[("MySQL")]
        EMAIL["EmailService"]
    end

    CLI -- "1: abrirCarrito()" --> FE
    FE -- "2: leerCookieCarrito()" --> FE
    FE -- "3: mostrarCarrito(items)" --> CLI
    CLI -- "4: iniciarCheckout()" --> FE
    FE -- "5: verificarJWT()" --> AUTH
    AUTH -- "6: validarToken(db)" --> DB
    DB -- "7: tokenOK" --> AUTH
    CLI -- "8: subirComprobante()" --> FE
    FE -- "9: PUT /comprobantes" --> GCS
    GCS -- "10: urlArchivo" --> FE
    CLI -- "11: confirmarPedido(datos)" --> FE
    FE -- "12: POST /pedidos" --> CTRL
    CTRL -- "13: sanitizarEntrada()" --> CTRL
    CTRL -- "14: crearPedido(datos)" --> SRV
    SRV -- "15: verificarStock(ids)" --> REP_PROD
    REP_PROD -- "16: SELECT disponible" --> DB
    DB -- "17: stockOK" --> REP_PROD
    SRV -- "18: insertarPedido()" --> REP_P
    REP_P -- "19: INSERT pedidos" --> DB
    SRV -- "20: actualizarEnPedidos()" --> REP_PROD
    REP_PROD -- "21: UPDATE productos" --> DB
    SRV -- "22: registrarAuditoria()" --> DB
    CTRL -- "23: 201 pedidoId" --> FE
    FE -- "24: generarPDF()" --> FE
    FE -- "25: mostrarConfirmacion()" --> CLI
    FE -- "26: enviarEmail()" --> EMAIL
```

---

#### 6.4.2 HU-002 · Colaboración — Asignación de Rutas y Generación de Guías

```mermaid
flowchart LR
    OPE(["Operador de Ruta"])

    subgraph Sistema
        direction TB
        PANT["PantallaAsignacion\n(Frontend)"]
        CTRL["PedidoController"]
        SRV["RutaService"]
        REP_P["PedidoRepository"]
        REP_C["CamionRepository"]
        REP_G["GuiaRepository"]
        REP_B["BodegaRepository"]
        DB[("MySQL")]
    end

    OPE -- "1: seleccionarPedidos(ids[])" --> PANT
    OPE -- "2: seleccionarCamion(id)" --> PANT
    PANT -- "3: POST /asignaciones" --> CTRL
    CTRL -- "4: validarPedidos(ids[])" --> REP_P
    REP_P -- "5: SELECT pendientes" --> DB
    DB -- "6: pedidosOK" --> REP_P
    CTRL -- "7: validarCamion(id)" --> REP_C
    REP_C -- "8: SELECT activo" --> DB
    DB -- "9: camionOK" --> REP_C
    CTRL -- "10: crearAsignacion()" --> SRV
    SRV -- "11: insertGuiaRemision()" --> REP_G
    SRV -- "12: insertGuiaRuta(pedidos)" --> REP_G
    REP_G -- "13: INSERT guias" --> DB
    SRV -- "14: crearTransaccionIngreso()" --> REP_B
    REP_B -- "15: INSERT transacciones" --> DB
    REP_B -- "16: UPDATE bodega_camion" --> DB
    SRV -- "17: updateEstadoPedidos()" --> REP_P
    REP_P -- "18: UPDATE pedidos" --> DB
    CTRL -- "19: auditoria()" --> DB
    CTRL -- "20: retornaGuias" --> PANT
    PANT -- "21: renderizaPDF(remision)" --> OPE
    PANT -- "22: renderizaPDF(ruta)" --> OPE
```

---

#### 6.4.3 HU-003 · Colaboración — Entrega, Devolución y Facturación

```mermaid
flowchart LR
    CHO(["Chofer"])

    subgraph Sistema
        direction TB
        FE["ModuloEntregas\n(Frontend PWA)"]
        CTRL["EntregaController"]
        SRV_E["EntregaService"]
        SRV_I["InventarioService"]
        REP_P["PedidoRepository"]
        REP_B["BodegaRepository"]
        REP_PROD["ProductoRepository"]
        FS[("Firestore GPS")]
        DB[("MySQL")]
        EXTMAP["Google Maps / Waze"]
    end

    CHO -- "1: abrirGuiaRuta(id)" --> FE
    FE -- "2: GET /guias/{id}" --> CTRL
    CTRL -- "3: consultarGuia()" --> DB
    DB -- "4: datosGuia" --> CTRL
    CTRL -- "5: retornaGuia" --> FE
    FE -- "6: iniciarGPS()" --> FS
    FS -- "7: ubicacion cada N seg" --> FE
    CHO -- "8: seleccionarPedido(id)" --> FE
    FE -- "9: PATCH estado=listo" --> CTRL
    CTRL -- "10: UPDATE pedidos" --> DB
    FE -- "11: abrirNavegacion(coords)" --> EXTMAP
    CHO -- "12: registrarEntrega(datos)" --> FE
    FE -- "13: POST /entregas" --> CTRL
    CTRL -- "14: procesarEntrega()" --> SRV_E
    SRV_E -- "15: validarReglaDevolucion()" --> SRV_E
    SRV_E -- "16: updateEstadoPedido()" --> REP_P
    REP_P -- "17: UPDATE pedidos" --> DB
    SRV_E -- "18: egresoInventario()" --> SRV_I
    SRV_I -- "19: UPDATE bodega_camion" --> REP_B
    REP_B -- "20: UPDATE" --> DB
    SRV_I -- "21: UPDATE en_pedidos" --> REP_PROD
    REP_PROD -- "22: UPDATE" --> DB
    SRV_E -- "23: insertTransaccion()" --> DB
    SRV_E -- "24: insertAuditoria()" --> DB
    CTRL -- "25: 201 facturaData" --> FE
    FE -- "26: generarFacturaPDF()" --> FE
    FE -- "27: mostrarFactura()" --> CHO
```

---

#### 6.4.4 HU-004 · Colaboración — Aprobación Manual de Pagos

```mermaid
flowchart LR
    OPE(["Operador de Ruta"])

    subgraph Sistema
        direction TB
        FE["PantallaAprobacion\n(Frontend)"]
        CTRL["PagoController"]
        SRV["AprobacionService"]
        REP_P["PedidoRepository"]
        GCS[("Cloud Storage")]
        DB[("MySQL")]
        EMAIL["EmailService"]
    end

    OPE -- "1: abrirListaPendientes()" --> FE
    FE -- "2: GET /pedidos?estado=en_espera_aprobacion" --> CTRL
    CTRL -- "3: SELECT pendientes" --> DB
    DB -- "4: listaPedidos" --> CTRL
    CTRL -- "5: retornaPedidos" --> FE
    FE -- "6: mostrarLista" --> OPE
    OPE -- "7: seleccionarPedido(id)" --> FE
    FE -- "8: GET /pedidos/{id}/comprobante" --> CTRL
    CTRL -- "9: getUrlFirmada()" --> GCS
    GCS -- "10: urlFirmada" --> CTRL
    CTRL -- "11: retornaUrl" --> FE
    FE -- "12: mostrarComprobante" --> OPE
    OPE -- "13: aprobarPago()" --> FE
    FE -- "14: PATCH /pedidos/{id}/aprobar" --> CTRL
    CTRL -- "15: aprobarPedido()" --> SRV
    SRV -- "16: updateEstado('en_espera_asignacion')" --> REP_P
    REP_P -- "17: UPDATE pedidos" --> DB
    SRV -- "18: insertAuditoria()" --> DB
    CTRL -- "19: 200 OK" --> FE
    FE -- "20: notificarCliente()" --> EMAIL
    FE -- "21: confirmacion" --> OPE
```

---

#### 6.4.5 HU-005 · Colaboración — Cierre de Guías y Encerado de Bodega

```mermaid
flowchart LR
    CHO(["Chofer"])
    OPE(["Operador de Ruta"])

    subgraph Sistema
        direction TB
        FE_C["FrontendChofer"]
        FE_O["FrontendOperador"]
        CTRL["CierreController"]
        SRV["CierreService"]
        REP_G["GuiaRepository"]
        REP_B["BodegaRepository"]
        REP_PROD["ProductoRepository"]
        REP_MAL["MercaderiaRepository"]
        DB[("MySQL")]
    end

    CHO -- "1: abrirCierreCaja()" --> FE_C
    FE_C -- "2: GET /guias/{id}/resumen" --> CTRL
    CTRL -- "3: calcularResumen()" --> DB
    DB -- "4: resumenFinanciero" --> CTRL
    CTRL -- "5: retornaResumen" --> FE_C
    FE_C -- "6: mostrarReporte" --> CHO
    CHO -- "7: declararEfectivo(monto)" --> FE_C
    FE_C -- "8: POST /guias/{id}/arqueo" --> CTRL
    CTRL -- "9: updateEstado('confirmacion_cierre')" --> REP_G
    REP_G -- "10: UPDATE guias_remision" --> DB
    OPE -- "11: abrirGuiaPendiente(id)" --> FE_O
    FE_O -- "12: GET /guias/{id}/detalle" --> CTRL
    CTRL -- "13: retornaDetalle" --> FE_O
    OPE -- "14: clasificarMercaderia()" --> FE_O
    FE_O -- "15: POST /inventario/ingreso (buen estado)" --> CTRL
    CTRL -- "16: ingresoMaestro()" --> SRV
    SRV -- "17: UPDATE cantidad_fisica" --> REP_PROD
    REP_PROD -- "18: UPDATE productos" --> DB
    FE_O -- "19: POST /mercaderia-mal-estado" --> CTRL
    CTRL -- "20: insertMalEstado()" --> REP_MAL
    REP_MAL -- "21: INSERT mercaderia_mal_estado" --> DB
    OPE -- "22: confirmarCierre()" --> FE_O
    FE_O -- "23: PATCH /guias/{id}/cerrar" --> CTRL
    CTRL -- "24: enceraBodega()" --> SRV
    SRV -- "25: UPDATE bodega_camion SET cantidad=0" --> REP_B
    REP_B -- "26: UPDATE" --> DB
    CTRL -- "27: updateEstado('cerrada')" --> REP_G
    REP_G -- "28: UPDATE guias" --> DB
    CTRL -- "29: insertAuditoria()" --> DB
    CTRL -- "30: 200 OK" --> FE_O
    FE_O -- "31: reporteArqueo" --> OPE
```

---

#### 6.4.6 HU-006 · Colaboración — Filtros Temporales Estilo Datadog

```mermaid
flowchart LR
    USR(["Admin / Operador"])

    subgraph Sistema
        direction TB
        FE["ModuloGestionPedidos\n(Frontend)"]
        PARSE["DateFilterParser\n(Frontend JS)"]
        CTRL["PedidoController"]
        REP["PedidoRepository"]
        DB[("MySQL")]
    end

    USR -- "1: abrirModulo()" --> FE
    FE -- "2: parsearFiltroDefault('hoy')" --> PARSE
    PARSE -- "3: {fechaInicio, fechaFin}" --> FE
    FE -- "4: GET /pedidos?estado=en_espera_asignacion&fi=...&ff=..." --> CTRL
    CTRL -- "5: consultarPedidos(filtros)" --> REP
    REP -- "6: SELECT con WHERE" --> DB
    DB -- "7: resultados" --> REP
    CTRL -- "8: retornaPedidos" --> FE
    FE -- "9: renderizaMapa+Cards" --> USR
    USR -- "10: escribeAtajo('2w')" --> FE
    FE -- "11: parsearAtajo('2w')" --> PARSE
    PARSE -- "12: {hoy-14d, ahora}" --> FE
    FE -- "13: validarRango(fi, ff)" --> FE
    FE -- "14: GET /pedidos?fi=...&ff=..." --> CTRL
    CTRL -- "15: consultarPedidos()" --> REP
    REP -- "16: SELECT" --> DB
    DB -- "17: resultados" --> REP
    CTRL -- "18: retorna" --> FE
    FE -- "19: actualizaVista" --> USR
    USR -- "20: clickCard(estado)" --> FE
    FE -- "21: filtrarPorEstado(estado)" --> FE
    FE -- "22: GET /pedidos?estado={e}&fi=...&ff=..." --> CTRL
    CTRL -- "23: consultarFiltrado()" --> REP
    REP -- "24: SELECT filtrado" --> DB
    DB -- "25: resultados" --> REP
    CTRL -- "26: retorna" --> FE
    FE -- "27: vistaPorEstado" --> USR
```

---

#### 6.4.7 HU-007 · Colaboración — Gestión del Carrito de Compras

```mermaid
flowchart LR
    CLI(["Cliente"])

    subgraph Sistema
        direction TB
        FE["CatalogoCarrito\n(Frontend)"]
        COOKIE["CookieManager\n(Navegador)"]
        CTRL["ProductoController"]
        REP_PROD["ProductoRepository"]
        REP_CA["CarritoAbandonadoRepository"]
        DB[("MySQL")]
    end

    CLI -- "1: navegarCatalogo()" --> FE
    FE -- "2: GET /productos?filtros" --> CTRL
    CTRL -- "3: consultarDisponibles()" --> REP_PROD
    REP_PROD -- "4: SELECT con stock logico" --> DB
    DB -- "5: productos" --> REP_PROD
    CTRL -- "6: retornaCatalogo" --> FE
    FE -- "7: mostrarCatalogo" --> CLI
    CLI -- "8: seleccionarProducto(id, cantidad)" --> FE
    FE -- "9: calcularSubtotal()" --> FE
    FE -- "10: existeEnCarrito?(id)" --> COOKIE
    COOKIE -- "11: respuesta boolean" --> FE
    FE -- "12: agregarOmerge(item)" --> COOKIE
    COOKIE -- "13: carritoActualizado" --> FE
    FE -- "14: mostrarSubtotal" --> CLI
    CLI -- "15: modificarCantidad(id, qty)" --> FE
    FE -- "16: GET /productos/{id}/stock" --> CTRL
    CTRL -- "17: verificarStock()" --> REP_PROD
    REP_PROD -- "18: SELECT disponible" --> DB
    DB -- "19: stockActual" --> REP_PROD
    CTRL -- "20: retornaStock" --> FE
    FE -- "21: updateItem(qty)" --> COOKIE
    FE -- "22: mostrarNuevoSubtotal" --> CLI
    CLI -- "23: eliminarItem(id)" --> FE
    FE -- "24: removeItem(id)" --> COOKIE
    COOKIE -- "25: carritoActualizado" --> FE
    FE -- "26: mostrarCarritoActualizado" --> CLI
    CLI -- "27: abandonarCarrito(motivo)" --> FE
    FE -- "28: POST /carritos-abandonados" --> CTRL
    CTRL -- "29: insertAbandonado()" --> REP_CA
    REP_CA -- "30: INSERT carritos_abandonados" --> DB
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

---

### 6.8 Diagrama de Flujo de Datos — Infraestructura GCP

Representa el flujo completo de datos entre los usuarios, los contenedores desplegados en GCP y todos los servicios cloud que consume el sistema.

#### Nivel 0 — Contexto General

Vista de alto nivel de los flujos de datos entre actores externos y el sistema en GCP.

```mermaid
flowchart TD
    %% ── Actores externos ──────────────────────────────────────────────────
    CLI(["👤 Cliente\n(Navegador / PWA"])
    OPE(["👤 Operador\nde Ruta"])
    ADM(["👤 Administrador"])
    CHO(["👤 Chofer\n(Móvil / PWA)"])
    EXT_MAP(["🗺️ Google Maps\n/ Waze"])

    %% ── Sistema GCP ───────────────────────────────────────────────────────
    subgraph GCP["☁️  Google Cloud Platform — Fritolay Ambato"]
        SISTEMA["⬛ Sistema E-commerce\nFritolay Ambato"]
    end

    %% ── Flujos de datos ────────────────────────────────────────────────────
    CLI  -->|"HTTPS: búsqueda, carrito,\ncheckout, comprobante"| SISTEMA
    SISTEMA -->|"Catálogo, estado pedido,\nubicación camión, factura PDF"| CLI

    OPE  -->|"HTTPS + JWT: aprobar pagos,\nasignar rutas, cerrar guías"| SISTEMA
    SISTEMA -->|"Pedidos, guías, reportes,\nKPIs, mapa en vivo"| OPE

    CHO  -->|"HTTPS + JWT: entrega,\ndevolución, arqueo, GPS"| SISTEMA
    SISTEMA -->|"Guía de ruta, inventario\ncamión, facturas PDF"| CHO

    ADM  -->|"HTTPS + JWT: gestión\nusuarios, dashboard"| SISTEMA
    SISTEMA -->|"KPIs, estadísticas,\nvisor facturas"| ADM

    SISTEMA -->|"Coordenadas destino\n(deeplink)"| EXT_MAP
```

---

#### Nivel 1 — Flujo Detallado por Servicios GCP

Muestra cómo fluyen los datos entre los contenedores, la base de datos, el almacenamiento y los demás servicios gestionados de GCP.

```mermaid
flowchart TD
    %% ═══════════════════════════════════════════════════════════════════
    %% USUARIOS EXTERNOS
    %% ═══════════════════════════════════════════════════════════════════
    CLI(["👤 Cliente\nNavegador/PWA"])
    OPE(["👤 Operador\nde Ruta"])
    CHO(["👤 Chofer"])
    ADM(["👤 Administrador"])

    %% ═══════════════════════════════════════════════════════════════════
    %% CAPA DE RED Y SEGURIDAD
    %% ═══════════════════════════════════════════════════════════════════
    subgraph NET["🔒 Capa de Red y Acceso"]
        LB["⚖️ Cloud Load Balancer\n(HTTPS global)"]
        IAP["🛡️ Cloud Armor / IAP\n(WAF · DDoS · IP allowlist)"]
    end

    %% ═══════════════════════════════════════════════════════════════════
    %% CONTENEDORES — CLOUD RUN
    %% ═══════════════════════════════════════════════════════════════════
    subgraph CR["🐳 Cloud Run — Contenedores"]
        direction TB
        FE_SVC["📄 frontend-service\nLaravel Blade + JS\n(PWA · Service Worker)"]
        BE_SVC["⚙️ backend-api-service\nLaravel REST API\n(JWT · Validación · SOLID)"]
    end

    %% ═══════════════════════════════════════════════════════════════════
    %% DATOS TRANSACCIONALES
    %% ═══════════════════════════════════════════════════════════════════
    subgraph DB_LAYER["💾 Capa de Datos"]
        direction LR
        subgraph MYSQL_INST["🐬 Cloud SQL — MySQL"]
            DB_TX[("Datos Transaccionales\npedidos · productos\nbodegas · guías\nauditoria")]
        end
        subgraph FS_INST["🔥 Firestore (NoSQL)"]
            FS_GPS[("Geolocalización\nubicaciones_camion\nlat · lng · timestamp")]
        end
    end

    %% ═══════════════════════════════════════════════════════════════════
    %% ALMACENAMIENTO DE OBJETOS
    %% ═══════════════════════════════════════════════════════════════════
    subgraph STORAGE["🗄️ Cloud Storage (GCS)"]
        direction LR
        GCS_IMG[("📦 Bucket: Imágenes\nimágenes de productos\n(CDN · caché 4h cliente)")]
        GCS_DOC[("📎 Bucket: Documentos\ncomprobantes de pago\n(URL firmadas · privado)")]
    end

    %% ═══════════════════════════════════════════════════════════════════
    %% SECRETOS Y CONFIGURACIÓN
    %% ═══════════════════════════════════════════════════════════════════
    subgraph SECRETS["🔑 Gestión de Secretos"]
        SM["🔒 Secret Manager\nJWT_SECRET\nDB_PASSWORD\nFIREBASE_KEY\nGCS_CREDENTIALS\nMAIL_CONFIG"]
    end

    %% ═══════════════════════════════════════════════════════════════════
    %% CI/CD Y ARTEFACTOS
    %% ═══════════════════════════════════════════════════════════════════
    subgraph CICD["🔄 CI/CD Pipeline"]
        direction LR
        GIT["📁 GitHub\nRepositorio"]
        CB["🏗️ Cloud Build\nBuild · Test · Push"]
        GCR_REG["📦 Artifact Registry\nImágenes Docker"]
    end

    %% ═══════════════════════════════════════════════════════════════════
    %% OBSERVABILIDAD
    %% ═══════════════════════════════════════════════════════════════════
    subgraph OBS["📊 Observabilidad"]
        direction LR
        LOGS["📋 Cloud Logging\nLogs de app y acceso"]
        MON["📈 Cloud Monitoring\nMétricas · Alertas"]
        TRACE["🔍 Cloud Trace\nLatencia de requests"]
    end

    %% ═══════════════════════════════════════════════════════════════════
    %% MENSAJERÍA Y NOTIFICACIONES
    %% ═══════════════════════════════════════════════════════════════════
    subgraph MSG["📧 Mensajería"]
        SMTP["✉️ SMTP / SendGrid\nEmail transaccional\n(configurable en .env)"]
        FCM["🔔 FCM\nPush Notifications\n(PWA / Web Push)"]
    end

    %% ═══════════════════════════════════════════════════════════════════
    %% FLUJOS DE DATOS — RED Y ACCESO
    %% ═══════════════════════════════════════════════════════════════════
    CLI -->|"HTTPS requests"| LB
    OPE -->|"HTTPS + JWT"| LB
    CHO -->|"HTTPS + JWT\n(GPS data)"| LB
    ADM -->|"HTTPS + JWT"| LB
    LB  -->|"Tráfico filtrado"| IAP
    IAP -->|"Requests validados"| FE_SVC

    %% ═══════════════════════════════════════════════════════════════════
    %% FLUJOS — FRONTEND ↔ BACKEND
    %% ═══════════════════════════════════════════════════════════════════
    FE_SVC -->|"REST API calls\nPOST/GET/PATCH\n(JSON + JWT)"| BE_SVC
    BE_SVC -->|"JSON responses\n(datos, tokens, URLs)"| FE_SVC

    %% ═══════════════════════════════════════════════════════════════════
    %% FLUJOS — BACKEND ↔ DATOS
    %% ═══════════════════════════════════════════════════════════════════
    BE_SVC -->|"SQL queries\n(Eloquent ORM)"| DB_TX
    DB_TX  -->|"Result sets"| BE_SVC
    BE_SVC -->|"SDK Firebase\nwrite GPS location"| FS_GPS
    FS_GPS -->|"Realtime listener\n(ubicación camión)"| FE_SVC

    %% ═══════════════════════════════════════════════════════════════════
    %% FLUJOS — ALMACENAMIENTO
    %% ═══════════════════════════════════════════════════════════════════
    BE_SVC   -->|"PUT comprobante\n(multipart upload)"| GCS_DOC
    GCS_DOC  -->|"URL firmada (GET)\n(solo operadores)"| BE_SVC
    BE_SVC   -->|"GET metadata\nURL pública imagen"| GCS_IMG
    GCS_IMG  -->|"Imagen cacheada\n(Cache-Control: 4h)"| FE_SVC

    %% ═══════════════════════════════════════════════════════════════════
    %% FLUJOS — SECRETOS
    %% ═══════════════════════════════════════════════════════════════════
    SM -->|"Secretos inyectados\nen arranque (env vars)"| BE_SVC
    SM -->|"Secretos inyectados\nen arranque"| FE_SVC

    %% ═══════════════════════════════════════════════════════════════════
    %% FLUJOS — CI/CD
    %% ═══════════════════════════════════════════════════════════════════
    GIT -->|"push main branch\n(webhook trigger)"| CB
    CB  -->|"docker build + test\ndocker push"| GCR_REG
    GCR_REG -->|"deploy imagen\n(Cloud Run revision)"| FE_SVC
    GCR_REG -->|"deploy imagen\n(Cloud Run revision)"| BE_SVC

    %% ═══════════════════════════════════════════════════════════════════
    %% FLUJOS — OBSERVABILIDAD
    %% ═══════════════════════════════════════════════════════════════════
    FE_SVC  -->|"stdout/stderr logs"| LOGS
    BE_SVC  -->|"stdout/stderr logs\naudit trail"| LOGS
    BE_SVC  -->|"request traces"| TRACE
    FE_SVC  -->|"métricas de uso"| MON
    BE_SVC  -->|"métricas latencia\nerror rates"| MON

    %% ═══════════════════════════════════════════════════════════════════
    %% FLUJOS — MENSAJERÍA
    %% ═══════════════════════════════════════════════════════════════════
    BE_SVC -->|"Envío email\n(pedido, aprobación)"| SMTP
    SMTP   -->|"Email entregado"| CLI
    BE_SVC -->|"Push notification\n(estado pedido)"| FCM
    FCM    -->|"Notificación web\n(Service Worker)"| CLI
```

---

#### Nivel 2 — Flujo de Datos por Proceso de Negocio

Muestra el recorrido de los datos para cada proceso crítico del sistema a través de los servicios GCP.

```mermaid
flowchart LR
    subgraph P1["🛒 Proceso: Checkout"]
        direction TB
        P1A["1. Cliente envía\npedido + comprobante"]
        P1B["2. Backend valida\nJWT · XSS · SQLi"]
        P1C["3. Comprobante → GCS\n(Bucket docs privado)"]
        P1D["4. Pedido → Cloud SQL\n(INSERT pedidos)"]
        P1E["5. Stock → Cloud SQL\n(UPDATE en_pedidos)"]
        P1F["6. Auditoría → Cloud SQL\n(INSERT bitacora)"]
        P1G["7. Email → SMTP\n(confirmación)"]
        P1A --> P1B --> P1C --> P1D --> P1E --> P1F --> P1G
    end

    subgraph P2["🚚 Proceso: Tracking GPS"]
        direction TB
        P2A["1. Chofer abre\nguía de ruta"]
        P2B["2. PWA activa\nGeolocation API"]
        P2C["3. Coords → Firestore\n(cada N seg · env config)"]
        P2D["4. Firestore realtime\nlistener activo"]
        P2E["5. Cliente ve\nubicación en mapa"]
        P2F["6. Push → FCM\n('pedido listo')"]
        P2A --> P2B --> P2C --> P2D --> P2E --> P2F
    end

    subgraph P3["📦 Proceso: Imágenes Producto"]
        direction TB
        P3A["1. Admin sube\nimagen producto"]
        P3B["2. Backend → PUT GCS\n(Bucket imágenes)"]
        P3C["3. URL pública\nguardada en MySQL"]
        P3D["4. Cliente solicita\nimagen catálogo"]
        P3E["5. Service Worker\n¿En caché local?"]
        P3F["6. Cache HIT →\nServir desde caché\n(0 costo GCS)"]
        P3G["6. Cache MISS →\nGCS → Cache 4h\n(Cache-Control header)"]
        P3A --> P3B --> P3C --> P3D --> P3E
        P3E -->|"HIT"| P3F
        P3E -->|"MISS"| P3G
    end

    subgraph P4["🔐 Proceso: Autenticación y Secretos"]
        direction TB
        P4A["1. Contenedor arranca\nen Cloud Run"]
        P4B["2. Secret Manager\ninyecta variables de entorno"]
        P4C["3. JWT_SECRET, DB_PASS\nGCS_KEY, MAIL_CONFIG"]
        P4D["4. Usuario hace login"]
        P4E["5. Backend verifica\nhash bcrypt en MySQL"]
        P4F["6. Genera JWT firmado\ncon JWT_SECRET"]
        P4G["7. JWT → Cookie\nhttpOnly · Secure · SameSite"]
        P4A --> P4B --> P4C
        P4D --> P4E --> P4F --> P4G
        P4B -.->|"provee secreto"| P4F
    end

    subgraph P5["🔄 Proceso: CI/CD Deploy"]
        direction TB
        P5A["1. git push main\n(GitHub)"]
        P5B["2. Cloud Build\ntriggered (webhook)"]
        P5C["3. docker build\nLaravel app"]
        P5D["4. php artisan test\n(unit + feature)"]
        P5E["5. docker push\nArtifact Registry"]
        P5F["6. Cloud Run\ndeploy nueva revision"]
        P5G["7. Traffic split\n0% → 100% gradual"]
        P5H["8. Cloud Monitoring\nalerta si error rate > 1%"]
        P5A --> P5B --> P5C --> P5D --> P5E --> P5F --> P5G --> P5H
    end
```

---

#### Nivel 3 — Matriz de Flujos de Datos y Clasificación

Resumen de todos los flujos de datos del sistema con su clasificación de seguridad y dirección.

| Origen | Destino | Dato | Protocolo | Seguridad |
|---|---|---|---|---|
| Cliente/Operador/Chofer | Cloud Load Balancer | Requests HTTP | HTTPS TLS 1.3 | Cloud Armor WAF |
| Load Balancer | frontend-service | Tráfico filtrado | HTTP interno | IAP · VPC |
| frontend-service | backend-api-service | REST API calls | HTTP/2 interno | JWT Bearer Token |
| backend-api-service | Cloud SQL MySQL | Queries SQL | TCP privado | VPC · SSL · IAM |
| backend-api-service | Firestore | GPS coordinates | gRPC | Service Account · IAM |
| backend-api-service | GCS Docs Bucket | Comprobantes (PUT) | HTTPS | Service Account · ACL privado |
| backend-api-service | GCS Imgs Bucket | Metadata URLs | HTTPS | Service Account · ACL público |
| GCS Imgs Bucket | frontend-service | Imágenes | HTTPS CDN | Cache-Control · CORS |
| Firestore | frontend-service | Ubicación realtime | WebSocket | API Key · Rules |
| Secret Manager | Cloud Run | Secretos (env vars) | gRPC interno | Service Account · IAM binding |
| GitHub | Cloud Build | Código fuente | HTTPS webhook | OAuth2 · Secret trigger |
| Cloud Build | Artifact Registry | Docker images | HTTPS | Service Account · IAM |
| Artifact Registry | Cloud Run | Imagen contenedor | HTTPS | Service Account · IAM |
| backend-api-service | Cloud Logging | Logs app/auditoría | stdout | Auto-recolectado |
| backend-api-service | SMTP/SendGrid | Emails | SMTP TLS | API Key (Secret Manager) |
| backend-api-service | FCM | Push notifications | HTTPS | Firebase Admin SDK |
