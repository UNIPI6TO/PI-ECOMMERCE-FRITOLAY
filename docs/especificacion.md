## Sistema E-commerce y Gesti├│n de Pedidos "Fritolay Ambato" 1. Objetivos del Proyecto Objetivo General

- Construir una aplicaci├│n que debe ser web y compatible con PWA.

- Desarrollar un sistema capaz de captar pedidos, ver en tiempo real su entrega, gestionar pedidos, gu├¡as de remisi├│n de camiones y rutas.

## Objetivos Espec├¡ficos de Arquitectura

- La aplicaci├│n debe estar en la nube y ser una aplicaci├│n orientada a microservicios y dockers.

- Ejecuci├│n en Desarrollo: Aunque la arquitectura es nativa de la nube, debe garantizarse que la aplicaci├│n tambi├®n se pueda ejecutar en el local para el desarrollo de la misma.

- Debe existir separaci├│n estricta entre front end, back end, MySQL para datos transaccionales y Firestore para datos de geolocalizaci├│n del cami├│n.

- La aplicaci├│n debe ser desarrollada en Laravel con PHP tanto el backend y el front end y de forma independiente en el mismo repositorio de Git.

- Para los an├ílisis y construcci├│n de la aplicaci├│n debe usar pony tail (https://github.com/dietrichgebert/ponytail) para optimizar el uso de tokens.

- Aplicar los principios de SOLID en la construcci├│n con agentes de IA para la aplicaci├│n, en especial el de responsabilidad ├║nica, y aplica tambi├®n la Programaci├│n Orientada a Objetos.

- Las entradas del REST API o backend deben ser validadas para no recibir ataques de XSS o SQL Injection.

- Agregar test en el desarrollo del backend y front end

- 2. UI/UX e Identidad Visual

- Dise├▒o y Usabilidad: Todo el sistema debe estar enfocado en un dise├▒o minimalista y adaptable, priorizando la usabilidad y un buen dise├▒o.

- Identidad Corporativa: La p├ígina debe tener los colores institucionales donde debe existir una combinaci├│n entre estas dos p├íginas https://www.lays.com/ y https://www.fritolay.com/ que son sus productos estrellas.

- 3. Seguridad, Gesti├│n de Estado y Variables de Entorno (Environment)

- Autenticaci├│n API: Implementar JWT y usar Secret Manager en GCP para resguardar los secretos de infraestructura y JWT.


- Gesti├│n de Contrase├▒as: Usar hash para comparaci├│n de las contrase├▒as y no est├®n en la base de datos en texto claro. La clave para generar el hash debe estar en un archivo environment.

- Recuperaci├│n de Credenciales: Todos los usuarios pueden recuperar sus credenciales mediante su correo electr├│nico con un pin de 6 d├¡gitos aleatorios por defecto. La cantidad de d├¡gitos debe ser configurada con una variable de entorno.

- Mensajer├¡a: Para la configuraci├│n del email para mensajer├¡a debe estar configurado en un environment en el backend para las funcionalidades.

- Persistencia Temporal: Uso de cookies seguras expirables para mantener el estado del carrito.

- Cach├®: Las im├ígenes (de GCS) deben guardarse en cach├® del lado del cliente o navegador con una duraci├│n configurable (por defecto 4 horas) que sea expirable.

## 4. Roles y Permisos

- Rol Administrador: Este usuario es capaz de crear, inactivar o resetear contrase├▒as de usuarios tipo empleados u otro rol de administraci├│n. Pueden ver el M├│dulo Dashboard de Gesti├│n de Pedidos. Tendr├í acceso al visor de facturas y exportaci├│n PDF.

- Rol Operador de Ruta: Este usuario gestionar├í los pedidos a entregar, como la asignaci├│n de los pedidos para un cami├│n, aprobaci├│n de pedidos, asignaci├│n de rutas con la creaci├│n de gu├¡a de ruta y la gu├¡a de remisi├│n, visor de facturas y cierre de caja del cami├│n. Pr├ícticamente estar├í usando el M├│dulo de Gesti├│n de Pedidos.

- Rol Chofer: Este usuario es el chofer del cami├│n, ├®l tendr├í accesos a su ruta asignada del M├│dulo Entregas.

- Rol Cliente: Una funcionalidad que no necesariamente debe ser autentificada es la de agregar productos al carrito de compras, la p├ígina de inicio debe ser esta y debe incluir inicio de sesi├│n en el home page.

- 5. M├│dulos del Sistema y L├│gica de Negocio

- 5.1. Dashboard 1: Estad├¡sticas y KPIs (M├│dulo Dashboard de Gesti├│n de Pedidos)

- Indicadores: Efectividad de entrega por cami├│n, pedidos entregados, efectividad de entrega general y tiempos de entrega promedio.

- Ventas: Debe mostrar las ventas por d├¡a, por sector y por cami├│n.

- Recaudaci├│n: Recaudaci├│n total y separado en efectivo, dep├│sitos, cheques, De Una, Tarjeta de Cr├®dito y Tarjetas de D├®bito.

- Carritos Abandonados: Compras no concretadas que el usuario haya creado el carrito y haya cancelado la compra. Al cancelar, debe poner opciones de por qu├® cancela el pedido; las comunes son: No lo necesito, Era una proforma, Pedido Equivocado, No es lo que requiero, y otros.


- Control de Stock: En el dashboard puede consultar el stock de los productos de las bodegas, de la bodega master y de los veh├¡culos.

## 5.2. M├│dulo de Gesti├│n de Pedidos (Operativo y Administrativo)

- Rastreo en Vivo: La ├║ltima ubicaci├│n estar├í siempre visible en el M├│dulo de Gesti├│n de Pedidos.

- Filtros de Fechas (Estilo Datadog): Se debe presentar un mapa el cual debe usar filtros de fechas especificando inicio y fin, este inicio y fin no puede ser mayor a un mes. Usar un textbox donde el usuario pueda escribir y las fechas deben cambiar (l├¡mite es 30 d├¡as por consulta):

- o Hoy = Fecha de inicio y fin deben estar configuradas como del d├¡a de hoy.

- o Ayer = Fechas inicio y fin del d├¡a de ayer.

- o 1d, 2d... 30d = Fechas configuradas de inicio 1 a 30 d├¡as antes de la fecha y hora actual, y fecha final la fecha y hora actual.

- o 1w, 2w, 3w y 4w = Fechas configuradas de inicio 1 a 4 semanas antes, y fecha final la fecha y hora actual.

- o Cuando haga una consulta custom, el cuadro de texto debe aparecer como custom, la validaci├│n que no debe pasarse de 30 d├¡as.

- Filtros por Estado: Usar tambi├®n filtros de estado y por defecto debe aparecer los pedidos en espera de asignaci├│n de ruta y los camiones que no tienen asignado rutas.

- Cards Informativos: Debe tener unos cards como tipo informativo de la cantidad de pedidos por estado, y cuando d├® un clic en el card este filtre por el estado. Los estados son: En espera de asignaci├│n de ruta, No entregado, Entregado Parcialmente, Entregados, Pendiente de Aprobaci├│n, Listo Para entregar, En Ruta, Todos.

- Ordenamiento: Los pedidos pueden ordenarse por: antig├╝edad del pedido (Por defecto), nombre del cliente y valor del pedido.

- Descuentos: El usuario puede asignar descuentos a clientes por tipo de pago, una sola configuraci├│n que aplicar├í en las siguientes compras. El usuario puede configurar descuentos a tipos de pagos para todos los clientes como un descuento adicional, este tiene que tener una fecha de caducidad.

- Asignaci├│n de Ruta y Bodegas M├│viles:

- o Puede seleccionar del mapa o de una lista de pedidos en espera de asignaci├│n de ruta para ir asignando los pedidos a un cami├│n activo.

- o Este tambi├®n tendr├í un card donde se ven los veh├¡culos que se est├ín asignado los pedidos.

- o La asignaci├│n termina cuando el gestor de ruta cierre la asignaci├│n y esto crea una gu├¡a de remisi├│n visual igual a las sugeridas por el SRI y una gu├¡a de ruta con los negocios a visitar, con montos a cobrar y tipos de pagos.


- o Cuando se genere debe crear una transacci├│n de ingreso a la bodega del cami├│n. Cada cami├│n administrar├í su bodega con transacciones de ingresos y egresos en la base de datos.

- o El m├│dulo debe ser capaz de gestionar los veh├¡culos, crear camiones, cambiar de estado por temas de mantenimiento y aver├¡as, y asignar choferes los cuales son usuarios tipo chofer.

- Aprobaci├│n de Pagos: Aprobar pedidos con tipo de pagos de dep├│sito y de la aplicaci├│n De Una. Cuando un usuario haya hecho un pedido con estos pagos debe pasar por una aprobaci├│n y los requisitos son el comprobante de dep├│sito para validar el pago. El pedido est├í en estado en espera por aprobaci├│n de pago. Los pagos de TC, TD y Efectivo estos se aprueban autom├íticamente y se dejan en espera de asignaci├│n de ruta.

- Cierres de Gu├¡as y Arqueo (Encerar Bodega):

- o Cuando el chofer haga su arqueo de caja, este aparecer├í en estado de confirmaci├│n de cierre en la gu├¡a y en el cami├│n.

- o En la p├ígina principal de la gesti├│n debe aparecer un card de gu├¡as pendientes por cerrar.

- o El gestor confirma la recepci├│n de la mercader├¡a devuelta y el dinero para cerrar la caja y encerar la bodega (dejar el inventario del cami├│n en cero).

- o Si los productos est├ín en buen estado, debe actualizar el inventario master de los productos y generar transacciones de ingreso por mercader├¡a en buen estado.

- o La de mal estado no debe ingresar, esta debe registrarse como mercader├¡a mal estado en otra tabla.

- Listado de Facturas (Simuladas): Pantalla con filtros donde Administradores/Operadores visualizan facturas y pueden exportar a PDF (procesado del lado del cliente).

## 5.3. M├│dulo de Entregas (Chofer)

- Inventario F├¡sico: Debe ser capaz de ver los productos del cami├│n existentes en el cami├│n e ir actualizando el inventario.

- Mapas y Navegaci├│n: Al momento de seleccionar la gu├¡a debe desplegarse el mapa donde estar├ín puntillado los pedidos a entregar. Estas deben permitir ordenar por el punto m├ís cercano de la ubicaci├│n actual y tambi├®n por la antig├╝edad que tiene el pedido solicitado, el ordenamiento debe realizarlo el front end. Cuando ocurra la selecci├│n del pedido, este pasar├í a estado Listo a ser entregado. Este debe ser seleccionado en el mapa de la aplicaci├│n web y cuando ocurra la aplicaci├│n direccionar├í a Google Maps o a Waze la ubicaci├│n y el chofer pueda navegar en estos mapas de aplicaciones externas y dirigirse a la ubicaci├│n de entrega.

- Tracking Constante: Al momento de seleccionar la gu├¡a debe compartirse la ubicaci├│n y esta debe ser guardada en Firestore cada cierto tiempo configurado en un environment.


- Ejecuci├│n y Facturaci├│n: Cuando se entregue un pedido de forma parcial o individual, generar una factura muy parecida a las autorizadas por el SRI como simulaci├│n. Crear una transacci├│n para disminuir la cantidad fsica y la cantidad en pedido.

- Devoluciones: Las devoluciones parciales solo aplican en pedidos en efectivo, si el pago es de otro tipo la devoluci├│n es total.

- Cierre de Caja: Gu├¡as pendientes de cierre, un reporte visual dinero por cada gu├¡a de ruta para realizar el cierre de la caja del cami├│n, tener la opci├│n de cierre donde debe declarar el valor en efectivo actual.

## 5.4. M├│dulo de Clientes (E-commerce)

- Cat├ílogo y Cach├®: Los productos deben mostrarse como una p├ígina e-commerce de f├ícil uso. La foto esta debe obtenerse de GCS y de ser posible que se guarde en cach├® del lado del cliente con una duraci├│n de 4 horas para ahorrar costos de GCS, el tiempo de permanencia de cach├® debe ser configurable.

- Detalles y Alertas: Mostrar informaci├│n del producto como peso, tipo de producto (Cheetos, Papas, Doritos, Tostitos, etc.), y una breve descripci├│n. Tambi├®n informaci├│n de descuento y, cuando el producto se acerque a un porcentaje de agotamiento de stock, mostrar las unidades disponibles. Cuando no tenga stock este debe mostrar informaci├│n que no hay ├¡tems disponibles y solo va a permitir ver informaci├│n del producto. Los productos pueden filtrarse por tipo de producto y ordenarse por tipo, por precio, por nombre.

- Carrito de Compras: Un usuario sin autentificarse o autentificado puede seleccionar productos y subir al carrito de compras. Cuando el usuario est├® por seleccionar producto al carrito de compras, antes debo especificar la cantidad y debe mostrarme el subtotal. Si escoge el mismo producto debe realizar un merge aumentando la cantidad. Puede modificar el carrito de compras, eliminar productos, agregar cantidades o disminuir.

- Checkout y Autenticaci├│n: Cuando quiera hacer el checkout debe pedir autentificarse o crear un usuario.

- Inventario L├│gico: En el inventario master habr├í una funcionalidad, cuando finalice el checkout el inventario debe tener tres campos: CantidadFisica, EnPedidos y la disponible ser├í la cantidad fsica menos la cantidad en pedidos.

- Gesti├│n de Direcciones y Mapa (Bidireccional):

- o Los datos requeridos para el cliente es informaci├│n de facturaci├│n, y puede registrar m├ís de una direcci├│n con ubicaci├│n del mapa.

- o Por defecto debe usar la ubicaci├│n actual, mover el punto de entrega y su direcci├│n debe aparecer en el cuadro de texto.

- o Tambi├®n debe permitir buscar una direcci├│n o coordenadas con un cuadro de texto, cuando ocurra esto el ping en el mapa debe moverse a lo que indica el cuadro de texto.


- o Puede seleccionar la direcci├│n por defecto que aparecer├í en los pedidos. El usuario debe permitir cambiar con otra o crear, editar o eliminar direcciones en el proceso de ingreso de datos o checkout.

- Liquidaci├│n de Pago: En el proceso de check out debe aparecer el detalle de los productos y c├ílculo de descuentos, impuesto IVA, total y sub total. Siempre debe mostrar el total del pedido y el ahorro por los descuentos configurados.

- Pasarela (Simulada): Simular el pago de tarjeta de d├®bito, de cr├®dito y el QR de De Una para pagos con De Una.

- Historial y PDF (Lado del Cliente): M├│dulo de historial de pedidos pasados. Generaci├│n de facturas en PDF procesada estrictamente utilizando los recursos del navegador/dispositivo del usuario para no recargar el servidor.

- Rastreo del Cliente: El cliente puede ver y recibir una notificaci├│n de la aplicaci├│n si el chofer eligi├│ el pedido como "Listo para entregar". El cliente solo ver├í la ubicaci├│n del cami├│n cuando el chofer seleccione el pedido y este pasar├í a este estado. Puede visualizar en el mapa la ubicaci├│n, el refresco de la ubicaci├│n del mapa ser├í configurada en el archivo de environment.

## An├ílisis Previo del Sistema

A continuaci├│n, se detalla el an├ílisis estructurado de la informaci├│n proporcionada para establecer el contexto de la aplicaci├│n.

## Actores Involucrados

- Administrador: Gestiona usuarios (crear, inactivar, resetear contrase├▒as), visualiza el Dashboard de KPIs y tiene acceso a visores de facturas y exportaci├│n PDF.

- Operador de Ruta: Gestiona pedidos, aprueba pagos, asigna rutas a camiones, genera gu├¡as de remisi├│n/rutas y supervisa el cierre de caja de camiones.

- Chofer: Gestiona la entrega en ruta, usa navegaci├│n en vivo, registra entregas/devoluciones, genera facturas simuladas y realiza su arqueo/cierre de caja.

- Cliente (Invitado/Registrado): Explora el cat├ílogo, gestiona el carrito de compras, realiza el checkout (requiere registro), sube comprobantes y rastrea su pedido.

## Procesos Principales

- Exploraci├│n de cat├ílogo y gesti├│n de carrito de compras.

- Checkout, c├ílculo de totales y selecci├│n de m├®todo de pago.

- Aprobaci├│n de pagos (manual para dep├│sitos/De Una, autom├ítica para el resto).

- Asignaci├│n de rutas y generaci├│n de gu├¡as (remisi├│n y ruta).

- Navegaci├│n GPS, entrega de pedidos y facturaci├│n in situ.

- Cierre de caja, encerado de bodega m├│vil y actualizaci├│n de inventario m├íster.

- Visualizaci├│n de m├®tricas y filtrado de datos (m├íximo 30 d├¡as).


## Reglas de Negocio Principales

- RN-A: Los pedidos pagados con Dep├│sito o "De Una" requieren carga de comprobante y aprobaci├│n manual por el Operador de Ruta. Los pagos con TC, TD o Efectivo se aprueban autom├íticamente.

- RN-B: Las devoluciones parciales de mercader├¡a durante la entrega solo est├ín permitidas si el m├®todo de pago original es Efectivo. Para otros m├®todos, la devoluci├│n debe ser total.

- RN-C: Las consultas con filtros de fechas personalizadas tienen un l├¡mite estricto de m├íximo 30 d├¡as de rango entre la fecha de inicio y fin.

- RN-D: La mercader├¡a devuelta en buen estado incrementa el inventario m├íster. La mercader├¡a en mal estado se registra en una tabla separada y no afecta el stock disponible de venta.

## F├│rmulas de C├ílculo

- Inventario L├│gico: \$CantidadDisponible = CantidadFisica - EnPedidos\$.

- Liquidaci├│n de Pedido: \$TotalPedido = (Subtotal - Descuentos) + ImpuestoIVA\$.

## Excepciones

- Intentar buscar datos con un rango de fechas mayor a 30 d├¡as.

- Intentar realizar una devoluci├│n parcial en un pedido pagado con Tarjeta de Cr├®dito/D├®bito.

- Intentar agregar un producto al carrito cuando la cantidad solicitada supera la \$CantidadDisponible\$.

## Evidencias Requeridas

- Gu├¡as de Remisi├│n y Gu├¡as de Ruta.

- Comprobantes de pago (im├ígenes/PDF subidos por el cliente).

- Facturas simuladas en formato PDF (procesadas del lado del cliente).

- Bit├ícoras de auditor├¡a y tracking GPS guardado en Firestore.

## Reportes Necesarios

- Dashboard de KPIs (Efectividad, Ventas, Recaudaci├│n, Carritos Abandonados, Stock).

- Visor y exportaci├│n de Facturas.

- Reporte de Cierre de Caja por cami├│n.

├ëpica: M├│dulo de Clientes (E-commerce)

## HU-001 - Liquidaci├│n de Pago y Checkout

Como: Cliente registrado Quiero: Visualizar el detalle de mi carrito, aplicar descuentos, calcular el IVA y elegir un m├®todo de pago Para: Completar mi compra con total claridad sobre los montos facturados y asegurar el stock de mis productos. Prioridad: Alta


## Reglas de negocio

- RN-01: El sistema debe calcular el total utilizando la f├│rmula \$TotalPedido = (Subtotal - Descuentos) + ImpuestoIVA\$.

- RN-02: Si el cliente elige "Dep├│sito" o "De Una", debe obligatoriamente adjuntar un comprobante. El pedido quedar├í en estado "En espera por aprobaci├│n".

- RN-03: Al finalizar el checkout, el inventario del producto debe actualizarse afectando el campo EnPedidos.

## Criterios de aceptaci├│n en Gherkin

Caracter├¡stica: Checkout y Liquidaci├│n de compras

Escenario: Flujo principal exitoso con pago en efectivo Dado que el cliente tiene productos en su carrito de compras Y se encuentra autenticado en el sistema Cuando procede a la pantalla de checkout y selecciona "Efectivo" como m├®todo de pago Entonces el sistema calcula y muestra el \$TotalPedido\$ Y el sistema registra el pedido en estado "En espera de asignaci├│n de ruta" Y el sistema actualiza el inventario sumando la cantidad solicitada al campo EnPedidos.

Escenario: Validaci├│n obligatoria de comprobante para dep├│sito Dado que el cliente selecciona "Dep├│sito" en el checkout Cuando intenta finalizar el pedido sin adjuntar un documento Entonces el sistema bloquea la acci├│n Y muestra un mensaje de error "Debe adjuntar el comprobante de dep├│sito para continuar".

Escenario: Excepci├│n por datos incompletos en la direcci├│n Dado que el cliente se encuentra en la pantalla de checkout Cuando no selecciona ni registra una direcci├│n de entrega v├ílida en el mapa bidireccional Entonces el bot├│n de "Finalizar Compra" se deshabilita Y se genera una alerta visual indicando "Direcci├│n de entrega obligatoria".

## Datos o campos requeridos

| Campo | Tipo de dato | Obligatorio | Validaci├│n |
| --- | --- | --- | --- |
|   |   |   | Valores permitidos: |
| MetodoPago | Lista desplegable | S├¡ | Efectivo, Dep├│sito, De |
|   |   |   | Una, TC, TD |
| Comprobante | Archivo | Condicional | Obligatorio si MetodoPago |
|   | (Imagen/PDF) |   | es Dep├│sito o De Una |
| DireccionEntrega Coordenadas/Texto S├¡ |   |   | Debe existir en la base o |
|   |   |   | seleccionarse del mapa |

## Dependencias

- Historia o m├│dulo relacionado: M├│dulo de Gesti├│n de Direcciones y Mapa Bidireccional; M├│dulo de Autenticaci├│n de Usuarios.

## Evidencias esperadas


- Registro generado en la base de datos MySQL (Tabla de Pedidos).

- Documento o archivo adjunto guardado en Google Cloud Storage (GCS).

- Bit├ícora de auditor├¡a detallando la fecha, hora y usuario que gener├│ el pedido.

├ëpica: M├│dulo de Gesti├│n de Pedidos

## HU-002 - Asignaci├│n de Rutas y Generaci├│n de Gu├¡as

Como: Operador de Ruta Quiero: Seleccionar pedidos en espera y asignarlos a un cami├│n activo Para: Generar autom├íticamente la gu├¡a de remisi├│n, la gu├¡a de ruta y registrar el ingreso de inventario en la bodega m├│vil del veh├¡culo. Prioridad: Alta

## Reglas de negocio

- RN-01: Solo los camiones en estado "Activo" pueden recibir asignaciones de pedidos.

- RN-02: Un pedido no puede asignarse a m├ís de un cami├│n simult├íneamente.

- RN-03: Al cerrar la asignaci├│n, se genera una transacci├│n autom├ítica de ingreso desde la bodega m├íster a la bodega del cami├│n seleccionado.

## Criterios de aceptaci├│n en Gherkin

Caracter├¡stica: Asignaci├│n de pedidos a camiones

Escenario: Flujo principal exitoso de asignaci├│n Dado que existen pedidos en estado "En espera de asignaci├│n de ruta" Y el Operador de Ruta tiene un cami├│n "Activo" seleccionado Cuando asigna los pedidos y hace clic en "Cerrar Asignaci├│n" Entonces el sistema genera una Gu├¡a de Remisi├│n visual Y genera una Gu├¡a de Ruta con los negocios, montos y tipos de pago Y crea una transacci├│n de ingreso de inventario en la bodega del cami├│n.

Escenario: Prevenci├│n de registros duplicados Dado que un pedido con ID "PED-123" ya fue asignado al "Cami├│n A" Cuando el Operador de Ruta intenta asignarlo al "Cami├│n B" Entonces el sistema rechaza la operaci├│n Y muestra una alerta "El pedido ya se encuentra en ruta con otro veh├¡culo".

Escenario: Permisos seg├║n el rol del usuario Dado que un usuario con rol "Chofer" inicia sesi├│n en el sistema Cuando intenta acceder a la pantalla de Asignaci├│n de Rutas Entonces el sistema deniega el acceso Y redirige al usuario a su M├│dulo de Entregas con el mensaje "No tiene permisos para esta acci├│n".

## Datos o campos requeridos

| Campo | Tipo de | Obligatorio | Validaci├│n |
| --- | --- | --- | --- |
|   | dato |   |   |
| ID_Pedido Entero |   | S├¡ | Debe existir y estar en estado "En espera |
|   |   |   | de asignaci├│n" |
| ID_Camion Entero |   | S├¡ | Debe existir y estar en estado "Activo" |


## Dependencias

- Historia o m├│dulo relacionado: Aprobaci├│n de Pagos (solo llegan pedidos aprobados o autom├íticos); M├│dulo de Autenticaci├│n.

## Evidencias esperadas

- Registro generado: Gu├¡a de Remisi├│n y Gu├¡a de Ruta en MySQL.

- Transacci├│n de inventario en la base de datos de bodegas m├│viles.

- Bit├ícora de auditor├¡a con la trazabilidad de la asignaci├│n (Operador, Cami├│n, Fecha).

├ëpica: M├│dulo de Entregas (Chofer)

## HU-003 - Ejecuci├│n de Entrega, Devoluci├│n y Facturaci├│n

Como: Chofer Quiero: Registrar la entrega parcial o total de un pedido en la ubicaci├│n del cliente Para: Descontar el inventario fsico del cami├│n y generar la factura simulada en formato PDF. Prioridad: Alta

## Reglas de negocio

- RN-01: Si el pedido fue pagado con Efectivo, el chofer puede registrar una entrega parcial (devoluci├│n parcial).

- RN-02: Si el pedido fue pagado por m├®todos distintos a Efectivo, cualquier devoluci├│n debe ser obligatoriamente total.

- RN-03: Al confirmar la entrega, la factura PDF debe procesarse estrictamente del lado del cliente (navegador/dispositivo) para no recargar el servidor.

## Criterios de aceptaci├│n en Gherkin

Caracter├¡stica: Entrega de pedidos y facturaci├│n

Escenario: Flujo principal exitoso de entrega total Dado que el Chofer se encuentra en la ubicaci├│n del cliente Y el pedido est├í en estado "Listo a ser entregado" Cuando marca el pedido como "Entregado totalmente" Entonces el sistema descuenta la cantidad fsica del inventario del cami├│n Y descuenta la cantidad en pedido del inventario Y genera

autom├íticamente la factura en PDF procesada en el navegador.

Escenario: Excepciones establecidas por la normativa (Devoluci├│n parcial no permitida) Dado que el pedido fue pagado con "Tarjeta de Cr├®dito" Cuando el Chofer intenta registrar una devoluci├│n parcial de mercader├¡a Entonces el sistema bloquea la entrada de cantidades menores al total Y muestra el mensaje de error "Las devoluciones parciales solo aplican para

pagos en efectivo. Proceda con devoluci├│n total o entrega completa."

Escenario: C├ílculo autom├ítico en entrega parcial (Efectivo) Dado que un pedido de 10

unidades fue pagado en Efectivo Cuando el Chofer registra la entrega de 8 unidades y 2 unidades como devoluci├│n Entonces el sistema actualiza el valor a cobrar basado en las 8 unidades Y genera la factura ├║nicamente por el valor recalculado de los art├¡culos entregados Y el estado del pedido cambia a "Entregado Parcialmente".

## Datos o campos requeridos


| Campo | Tipo de | Obligatorio | Validaci├│n |
| --- | --- | --- | --- |
|   | dato |   |   |
| CantidadEntregada Entero |   | S├¡ | Debe ser mayor a 0 y menor o igual |
|   |   |   | a lo solicitado |
| MotivoDevolucion Texto |   | Condicional | Obligatorio si CantidadEntregada < |
|   |   |   | CantidadSolicitada |
| EstadoMercaderia Lista |   | Condicional | Valores: Buen estado, Mal estado |
|   |   |   | (si hay devoluci├│n) |

## Dependencias

- Historia o m├│dulo relacionado: M├│dulo de Navegaci├│n GPS (Waze/Google Maps); Inventario F├¡sico de Camiones.

## Evidencias esperadas

- Reporte: Factura simulada generada en PDF.

- Registro de transacci├│n restando el inventario fsico del cami├│n.

- Bit├ícora de auditor├¡a detallando la ubicaci├│n GPS (Firestore) al momento de marcar la entrega.

├ëpica: M├│dulo de Gesti├│n de Pedidos (Operativo)

## HU-004 - Aprobaci├│n Manual de Pagos con Comprobante

Como: Operador de Ruta Quiero: Revisar y aprobar los pedidos que fueron pagados mediante Dep├│sito o la aplicaci├│n "De Una" Para: Validar la legitimidad del pago mediante el comprobante antes de que el pedido pase a la fase de asignaci├│n de ruta. Prioridad: Alta

## Reglas de negocio

- RN-01: Los pedidos realizados con pagos de Tarjeta de Cr├®dito (TC), Tarjeta de D├®bito (TD) y Efectivo se aprueban autom├íticamente y pasan directo a espera de asignaci├│n de ruta.

- RN-02: Los pedidos con m├®todos de pago "Dep├│sito" o "De Una" deben permanecer en estado "En espera por aprobaci├│n de pago" hasta que un operador valide el comprobante.

## Criterios de aceptaci├│n en Gherkin

Caracter├¡stica: Aprobaci├│n de pagos en pedidos

Escenario: Flujo principal exitoso de aprobaci├│n manual Dado que un pedido se encuentra en estado "En espera por aprobaci├│n de pago" Y el cliente adjunt├│ el comprobante de dep├│sito Cuando el Operador de Ruta revisa el documento y selecciona "Aprobar Pago" Entonces el


sistema cambia el estado del pedido a "En espera de asignaci├│n de ruta" Y genera un registro en la bit├ícora de auditor├¡a detallando la acci├│n.

Escenario: Excepci├│n por m├®todo de pago de aprobaci├│n autom├ítica Dado que un cliente finaliza un checkout con m├®todo de pago "Efectivo" Cuando el sistema procesa la orden Entonces el sistema aprueba autom├íticamente el pedido sin requerir intervenci├│n del operador Y lo coloca directamente en la lista de pedidos listos para asignaci├│n de ruta.

## Datos o campos requeridos

| Campo Tipo de dato ID_Pedido Entero EstadoActual Texto Archivo | Obligatorio S├¡ S├¡ | Validaci├│n El pedido debe existir Debe ser "En espera por aprobaci├│n de pago" Documento adjunto por el |   |
| --- | --- | --- | --- |
| ComprobantePago (Imagen/PDF) | S├¡ | cliente en el checkout |   |

## Dependencias

- Historia o m├│dulo relacionado: M├│dulo de Clientes (Liquidaci├│n de Pago y Checkout).

## Evidencias esperadas

- Registro generado con la actualizaci├│n del estado del pedido.

- Bit├ícora de auditor├¡a indicando qu├® operador aprob├│ la transacci├│n.

├ëpica: M├│dulo de Gesti├│n de Pedidos (Operativo)

## HU-005 - Cierre de Gu├¡as, Arqueo y Encerado de Bodega

Como: Operador de Ruta Quiero: Confirmar la recepci├│n del dinero en efectivo y procesar la mercader├¡a devuelta por los camiones Para: Realizar el cierre de la gu├¡a de ruta, saldar la caja y encerar (dejar en cero) el inventario de la bodega del cami├│n. Prioridad: Alta

## Reglas de negocio

- RN-01: La mercader├¡a devuelta catalogada en "Buen estado" debe generar transacciones de ingreso y actualizar positivamente el inventario m├íster de productos.

- RN-02: La mercader├¡a devuelta en "Mal estado" no debe ingresar al inventario m├íster y debe registrarse en una tabla independiente.

## Criterios de aceptaci├│n en Gherkin

Caracter├¡stica: Cierre de caja y encerado de bodegas m├│viles

Escenario: Flujo principal exitoso de cierre con mercader├¡a en buen estado Dado que un cami├│n tiene una gu├¡a en estado de "Confirmaci├│n de cierre" Y el chofer ha declarado el valor en efectivo actual en su arqueo Cuando el Operador de Ruta confirma la recepci├│n del dinero y


de los productos devueltos en buen estado Entonces el sistema actualiza el inventario m├íster sumando los productos recibidos Y genera las transacciones de ingreso correspondientes Y el sistema encera el inventario del cami├│n dej├índolo en cero.

Escenario: Validaci├│n de mercader├¡a en mal estado Dado que el Operador de Ruta procesa el cierre de una gu├¡a con productos devueltos Cuando clasifica una parte de la mercader├¡a como "Mal estado" Entonces el sistema registra estos ├¡tems en la tabla exclusiva de mercader├¡a en mal estado Y omite la actualizaci├│n de estos ├¡tems en el inventario m├íster disponible.

## Datos o campos requeridos

| Tipo de Campo dato ID_Guia Entero |   | Obligatorio S├¡ |   |   | Validaci├│n |   | Debe estar en estado "Confirmaci├│n de cierre" |   |
| --- | --- | --- | --- | --- | --- | --- | --- | --- |
| EfectivoRecibido Decimal EstadoMercaderia Lista |   | S├¡ S├¡ |   |   |   |   | Monto entregado por el chofer "Buen estado" o "Mal estado" |   |

## Dependencias

- Historia o m├│dulo relacionado: M├│dulo de Entregas (Cierre de Caja del Chofer).

## Evidencias esperadas

- Registro generado en el Inventario M├íster (si aplica buen estado).

- Registro generado en la tabla de mercader├¡a en mal estado (si aplica).

- Reporte de arqueo de caja cerrado exitosamente.

├ëpica: M├│dulo Dashboard y Estad├¡sticas

## HU-006 - Filtros Temporales y de Estado Estilo Datadog

Como: Administrador / Operador de Ruta Quiero: Visualizar los pedidos en un mapa utilizando filtros por rangos de fechas personalizables y tarjetas de estados Para: Monitorizar la operaci├│n y localizar r├ípidamente los pedidos seg├║n su situaci├│n actual. Prioridad: Media

## Reglas de negocio

- RN-01: El filtro de fechas personalizado no puede exceder un l├¡mite de consulta m├íximo de 30 d├¡as entre la fecha de inicio y la fecha fin.

- RN-02: Al presionar un "Card Informativo" de estado, el sistema debe filtrar autom├íticamente la vista principal mostrando ├║nicamente los pedidos con dicho estado.

## Criterios de aceptaci├│n en Gherkin

Caracter├¡stica: Filtros de b├║squeda estilo Datadog y tarjetas informativas


Escenario: Flujo principal exitoso con atajo de filtro Dado que el usuario se encuentra en el M├│dulo de Gesti├│n de Pedidos Cuando ingresa el comando "1w" en el cuadro de texto de fechas Entonces el sistema configura la fecha de inicio a 1 semana antes de la fecha actual Y configura la fecha final como la fecha y hora actual Y actualiza la informaci├│n mostrada en pantalla.

Escenario: Validaci├│n obligatoria de l├¡mite de 30 d├¡as en consulta custom Dado que el usuario utiliza el filtro custom ingresando fechas manualmente Cuando define un rango superior a 30 d├¡as entre inicio y fin Entonces el sistema bloquea la b├║squeda Y genera un mensaje de error indicando que la consulta no puede sobrepasar los 30 d├¡as.

## Datos o campos requeridos

| Tipo de Campo dato | Obligatorio | Validaci├│n Valores como: Hoy, Ayer, 1d-30d, 1w- |   |
| --- | --- | --- | --- |
| FiltroFecha Texto/Atajo S├¡ |   |   |   |
| RangoFechas Date | Condicional | 4w, o custom La diferencia entre fechas no puede superar 30 d├¡as |   |
| FiltroEstado Bot├│n/Card No |   | Estados: En espera, Entregados, En Ruta, etc. |   |

## Dependencias

- Historia o m├│dulo relacionado: M├│dulo de Gesti├│n de Pedidos.

## Evidencias esperadas

- Reporte visual filtrado exitosamente en la interfaz (Front end).

├ëpica: M├│dulo de Clientes (E-commerce)

## HU-007 - Gesti├│n del Carrito de Compras

Como: Cliente Quiero: Agregar productos al carrito, modificar sus cantidades y visualizar el subtotal de mi pedido Para: Preparar mi orden de compra guardando el estado de mi selecci├│n sin necesidad inmediata de iniciar sesi├│n. Prioridad: Alta

## Reglas de negocio

- RN-01: Si el usuario selecciona un producto que ya se encuentra en el carrito, el sistema debe realizar un merge (fusi├│n) aumentando ├║nicamente la cantidad de dicho ├¡tem.

- RN-02: El estado del carrito de compras debe preservarse utilizando cookies seguras expirables (Persistencia Temporal).

## Criterios de aceptaci├│n en Gherkin


Caracter├¡stica: Administraci├│n de ├¡tems en el carrito de compras

Escenario: Flujo principal exitoso agregando cantidades al carrito Dado que el usuario est├í visualizando el cat├ílogo de productos Y selecciona un ├¡tem disponible Cuando especifica la cantidad deseada y lo a├▒ade al carrito de compras Entonces el sistema agrega el ├¡tem al listado del carrito Y muestra inmediatamente el subtotal calculado.

Escenario: Prevenci├│n de duplicados mediante merge de productos Dado que el cliente tiene 2 unidades de "Papas Lays" en su carrito Cuando vuelve al cat├ílogo y a├▒ade 3 unidades m├ís del mismo producto Entonces el sistema no crea una nueva l├¡nea en el carrito Y realiza un merge actualizando la cantidad del producto a 5 unidades.

## Datos o campos requeridos

| Tipo de Campo dato ID_Producto Entero | Obligatorio S├¡ | Validaci├│n Debe existir en el cat├ílogo e-commerce |   |
| --- | --- | --- | --- |
| Cantidad Entero | S├¡ | Debe ser mayor a cero y menor o igual al stock disponible |   |
| CookieSesion Texto | Autom├ítico | Se gestiona de forma segura expirable en el navegador |   |

## Dependencias

- Historia o m├│dulo relacionado: Cat├ílogo e Inventario L├│gico.

## Evidencias esperadas

- Registro temporal generado en la cookie segura del cliente.

---

## 6. Diagramas UML

A continuaci├│n se presentan los diagramas UML del sistema **Fritolay Ambato**, modelando su arquitectura, comportamiento y base de datos.

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
    %% ÔöÇÔöÇ Actores ÔöÇÔöÇÔöÇÔöÇÔöÇÔöÇÔöÇÔöÇÔöÇÔöÇÔöÇÔöÇÔöÇÔöÇÔöÇÔöÇÔöÇÔöÇÔöÇÔöÇÔöÇÔöÇÔöÇÔöÇÔöÇÔöÇÔöÇÔöÇÔöÇÔöÇÔöÇÔöÇÔöÇÔöÇÔöÇÔöÇÔöÇÔöÇÔöÇÔöÇÔöÇÔöÇÔöÇÔöÇÔöÇÔöÇÔöÇÔöÇÔöÇÔöÇÔöÇÔöÇÔöÇÔöÇÔöÇÔöÇÔöÇÔöÇÔöÇÔöÇÔöÇÔöÇ
    CLI(["\n­ƒæñ\nCliente\n"])
    ADM(["\n­ƒæñ\nAdministrador\n"])
    OPE(["\n­ƒæñ\nOperador\nde Ruta\n"])
    CHO(["\n­ƒæñ\nChofer\n"])

    %% ÔöÇÔöÇ Frontera del sistema ÔöÇÔöÇÔöÇÔöÇÔöÇÔöÇÔöÇÔöÇÔöÇÔöÇÔöÇÔöÇÔöÇÔöÇÔöÇÔöÇÔöÇÔöÇÔöÇÔöÇÔöÇÔöÇÔöÇÔöÇÔöÇÔöÇÔöÇÔöÇÔöÇÔöÇÔöÇÔöÇÔöÇÔöÇÔöÇÔöÇÔöÇÔöÇÔöÇÔöÇÔöÇÔöÇÔöÇÔöÇÔöÇÔöÇÔöÇÔöÇ
    subgraph SYS["­ƒûÑ´©Å  Sistema E-commerce Fritolay Ambato"]

        subgraph EC["­ƒôª E-commerce / Clientes"]
            UC01(["Ver Cat├ílogo\nde Productos"])
            UC02(["HU-007 ┬À Gestionar\nCarrito de Compras"])
            UC03(["HU-001 ┬À Realizar\nCheckout"])
            UC04(["Adjuntar Comprobante\nde Pago"])
            UC05(["Ver Historial\nde Pedidos"])
            UC06(["Rastrear Pedido\nen Mapa"])
            UC07(["Gestionar Direcciones\nMapa Bidireccional"])
            UC08(["Generar Factura\nPDF ÔÇö lado cliente"])
            UC09(["Autenticarse /\nRegistrarse"])
            UC10(["Recuperar\nCredenciales"])
        end

        subgraph GP["­ƒùé´©Å Gesti├│n de Pedidos"]
            UC11(["HU-004 ┬À Aprobar Pago\ncon Comprobante"])
            UC12(["HU-002 ┬À Asignar\nPedidos a Cami├│n"])
            UC13(["HU-002 ┬À Generar Gu├¡a\nRemisi├│n y Ruta"])
            UC14(["HU-006 ┬À Filtros\nEstilo Datadog"])
            UC15(["HU-006 ┬À Ver Cards\nInformativos por Estado"])
            UC16(["Configurar\nDescuentos"])
            UC17(["HU-005 ┬À Confirmar\nCierre y Encerado"])
            UC18(["Ver Visor\nde Facturas PDF"])
            UC19(["Gestionar\nVeh├¡culos CRUD"])
        end

        subgraph EN["­ƒÜÜ Entregas ÔÇö Chofer"]
            UC20(["Ver Gu├¡a de\nRuta Asignada"])
            UC21(["HU-003 ┬À Registrar\nEntrega Total/Parcial"])
            UC22(["HU-003 ┬À Registrar\nDevoluci├│n"])
            UC23(["Ver Inventario\ndel Cami├│n"])
            UC24(["HU-003 ┬À Generar\nFactura PDF in situ"])
            UC25(["Realizar Cierre\nde Caja"])
            UC26(["Compartir Ubicaci├│n\nGPS ÔÇö Firestore"])
            UC27(["Navegar Google\nMaps / Waze"])
        end

        subgraph DB["­ƒôè Dashboard"]
            UC28(["Ver KPIs\ny Estad├¡sticas"])
            UC29(["Ver Ventas por\nSector / Cami├│n"])
            UC30(["Ver Recaudaci├│n\npor M├®todo de Pago"])
            UC31(["Ver Carritos\nAbandonados"])
            UC32(["Consultar Stock\nde Bodegas"])
        end

        subgraph ADM_M["ÔÜÖ´©Å Administraci├│n"]
            UC33(["Crear / Inactivar\nUsuarios Empleados"])
            UC34(["Resetear\nContrase├▒as"])
        end

        %% ÔöÇÔöÇ Relaciones <<include>> y <<extend>> ÔöÇÔöÇÔöÇÔöÇÔöÇÔöÇÔöÇÔöÇÔöÇÔöÇÔöÇÔöÇÔöÇÔöÇÔöÇÔöÇÔöÇÔöÇÔöÇÔöÇÔöÇÔöÇÔöÇÔöÇÔöÇ
        UC03 -- "<<include>>" --> UC09
        UC03 -- "<<include>>" --> UC07
        UC03 -- "<<extend>>\n[pago Dep├│sito/De Una]" --> UC04
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

    %% ÔöÇÔöÇ Asociaciones Actor Ôåö Casos de Uso ÔöÇÔöÇÔöÇÔöÇÔöÇÔöÇÔöÇÔöÇÔöÇÔöÇÔöÇÔöÇÔöÇÔöÇÔöÇÔöÇÔöÇÔöÇÔöÇÔöÇÔöÇÔöÇÔöÇÔöÇÔöÇÔöÇÔöÇÔöÇÔöÇÔöÇÔöÇ
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

### 6.3 Diagramas de Secuencia ÔÇö HU-001 a HU-007

Cada diagrama modela el flujo de mensajes entre actores y componentes del sistema para cada Historia de Usuario.

---

#### 6.3.1 HU-001 ┬À Liquidaci├│n de Pago y Checkout

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
    FE->>Cliente: Muestra ├¡tems y subtotal

    Cliente->>FE: Inicia Checkout
    FE->>FE: Verifica token JWT
    alt No autenticado
        FE->>Cliente: Redirige a Login / Registro
        Cliente->>FE: Env├¡a credenciales
        FE->>API: POST /auth/login
        API->>DB: Valida hash de contrase├▒a
        DB-->>API: Usuario v├ílido
        API-->>FE: JWT Token
    end

    FE->>Cliente: Muestra pantalla Checkout
    Cliente->>FE: Selecciona DireccionEntrega (mapa bidireccional)
    Cliente->>FE: Selecciona MetodoPago

    alt MetodoPago = Dep├│sito | De Una
        Cliente->>FE: Adjunta comprobante (imagen/PDF)
        FE->>GCS: PUT /comprobantes/{filename}
        GCS-->>FE: URL p├║blica del archivo
    end

    FE->>API: POST /pedidos {items, direcci├│n, metodoPago, comprobante}
    API->>API: Sanitiza entrada (anti-XSS, anti-SQLi)
    API->>DB: SELECT cantidad_fisica - en_pedidos (por producto)

    alt Stock insuficiente
        API-->>FE: 422 Unprocessable ÔÇö stock no disponible
        FE->>Cliente: Alerta visual de stock agotado
    else Stock OK
        API->>DB: INSERT pedidos (estado seg├║n m├®todo de pago)
        API->>DB: UPDATE productos SET en_pedidos += cantidad
        API->>DB: INSERT items_pedido
        API->>DB: INSERT bitacora_auditoria
        API-->>FE: 201 Created {pedidoId, estado}
        FE->>FE: Genera PDF proforma (lado cliente ÔÇö sin servidor)
        FE->>Cliente: Confirmaci├│n + PDF descargable
        FE->>Email: Env├¡a notificaci├│n pedido recibido
    end
```

---

#### 6.3.2 HU-002 ┬À Asignaci├│n de Rutas y Generaci├│n de Gu├¡as

```mermaid
sequenceDiagram
    actor Operador as Operador de Ruta
    participant FE as Frontend
    participant API as Backend API
    participant DB as MySQL

    Operador->>FE: Abre m├│dulo Gesti├│n de Pedidos
    FE->>API: GET /pedidos?estado=en_espera_asignacion
    API->>DB: SELECT pedidos WHERE estado = 'en_espera_asignacion'
    DB-->>API: Lista de pedidos
    API-->>FE: Pedidos pendientes
    FE->>Operador: Muestra lista y mapa con pedidos

    Operador->>FE: Selecciona pedidos del mapa/lista
    Operador->>FE: Selecciona cami├│n activo
    FE->>API: GET /camiones?estado=activo
    API->>DB: SELECT camiones WHERE estado = 'activo'
    DB-->>API: Lista de camiones
    API-->>FE: Camiones disponibles
    FE->>Operador: Muestra card de cami├│n seleccionado

    Operador->>FE: Clic "Cerrar Asignaci├│n"
    FE->>API: POST /asignaciones {pedidoIds[], camionId}
    API->>DB: Verifica pedidos no asignados
    alt Pedido ya asignado
        API-->>FE: 409 Conflict ÔÇö pedido ya en ruta
        FE->>Operador: Alerta pedido duplicado
    else Validaci├│n OK
        API->>DB: INSERT guias_remision
        API->>DB: INSERT guias_ruta
        API->>DB: INSERT asignacion_pedido_camion
        API->>DB: INSERT transacciones_inventario (ingreso bodega cami├│n)
        API->>DB: UPDATE bodega_camion
        API->>DB: UPDATE pedidos SET estado = 'listo_para_entregar'
        API->>DB: INSERT bitacora_auditoria
        API-->>FE: 201 {guiaRemisionId, guiaRutaId}
        FE->>FE: Renderiza Gu├¡a Remisi├│n PDF (lado cliente)
        FE->>FE: Renderiza Gu├¡a Ruta PDF (lado cliente)
        FE->>Operador: Muestra gu├¡as generadas
    end
```

---

#### 6.3.3 HU-003 ┬À Ejecuci├│n de Entrega, Devoluci├│n y Facturaci├│n

```mermaid
sequenceDiagram
    actor Chofer
    participant FE as Frontend (PWA)
    participant API as Backend API
    participant DB as MySQL
    participant FS as Firestore GPS
    participant ExtMap as Google Maps / Waze

    Chofer->>FE: Abre m├│dulo Entregas
    FE->>API: GET /guias-ruta?estado=activa&choferId={id}
    API->>DB: SELECT guias asignadas al chofer
    DB-->>API: Gu├¡as activas
    API-->>FE: Lista de gu├¡as
    FE->>Chofer: Muestra gu├¡as y mapa con pedidos puntuados

    Chofer->>FE: Selecciona gu├¡a de ruta
    FE->>FS: START watch ubicacion_camion/{camionId}
    Note over FE,FS: GPS se comparte en Firestore cada N segundos (configurable)

    Chofer->>FE: Selecciona pedido del mapa
    FE->>API: PATCH /pedidos/{id} {estado: listo_a_ser_entregado}
    API->>DB: UPDATE pedidos
    API-->>FE: OK
    FE->>ExtMap: Abre Google Maps / Waze con coordenadas cliente

    Chofer->>FE: Llega y registra entrega
    FE->>Chofer: Formulario ÔÇö cantidad entregada / devuelta / estado mercader├¡a

    alt Entrega total
        FE->>API: POST /entregas {pedidoId, cantidadEntregada: total, estado: entregado}
        API->>DB: UPDATE pedidos SET estado = 'entregado'
        API->>DB: UPDATE bodega_camion (egreso f├¡sico)
        API->>DB: UPDATE productos SET en_pedidos -= cantidad
        API->>DB: INSERT transacciones_inventario (egreso)
    else Entrega parcial (solo Efectivo)
        FE->>API: POST /entregas {pedidoId, cantidadEntregada, cantidadDevuelta, motivoDevolucion, estadoMercaderia}
        API->>DB: UPDATE pedidos SET estado = 'entregado_parcialmente'
        API->>DB: UPDATE bodega_camion
        API->>DB: UPDATE productos SET en_pedidos -= cantidadEntregada
        API->>DB: INSERT transacciones_inventario
    else M├®todo pago != Efectivo y devoluci├│n parcial
        API-->>FE: 422 Error ÔÇö devoluci├│n parcial no permitida
        FE->>Chofer: Mensaje ÔÇö solo devoluci├│n total permitida
    end

    API->>DB: INSERT bitacora_auditoria {ubicacionGPS}
    API-->>FE: 201 {facturaData}
    FE->>FE: Genera Factura PDF (lado cliente ÔÇö navegador)
    FE->>Chofer: Factura disponible para imprimir/compartir
```

---

#### 6.3.4 HU-004 ┬À Aprobaci├│n Manual de Pagos con Comprobante

```mermaid
sequenceDiagram
    actor Operador as Operador de Ruta
    participant FE as Frontend
    participant API as Backend API
    participant DB as MySQL
    participant GCS as Cloud Storage
    participant Email as Email Service

    Operador->>FE: Abre lista de pedidos pendientes de aprobaci├│n
    FE->>API: GET /pedidos?estado=en_espera_aprobacion
    API->>DB: SELECT pedidos WHERE estado = 'en_espera_aprobacion'
    DB-->>API: Pedidos con m├®todo Dep├│sito / De Una
    API-->>FE: Lista de pedidos
    FE->>Operador: Muestra pedidos con bot├│n "Revisar"

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
        FE->>Email: Notifica al cliente ÔÇö pago aprobado
        FE->>Operador: Confirmaci├│n visual
    else Operador rechaza pago
        Operador->>FE: Clic "Rechazar" + motivo
        FE->>API: PATCH /pedidos/{id}/rechazar {motivo}
        API->>DB: UPDATE pedidos SET estado = 'rechazado'
        API->>DB: UPDATE productos SET en_pedidos -= cantidad
        API->>DB: INSERT bitacora_auditoria
        API-->>FE: 200 OK
        FE->>Email: Notifica al cliente ÔÇö pago rechazado con motivo
        FE->>Operador: Confirmaci├│n visual
    end
```

---

#### 6.3.5 HU-005 ┬À Cierre de Gu├¡as, Arqueo y Encerado de Bodega

```mermaid
sequenceDiagram
    actor Chofer
    actor Operador as Operador de Ruta
    participant FE_CHO as Frontend Chofer
    participant FE_OPE as Frontend Operador
    participant API as Backend API
    participant DB as MySQL

    Chofer->>FE_CHO: Abre m├│dulo Cierre de Caja
    FE_CHO->>API: GET /guias-ruta/{id}/resumen-caja
    API->>DB: SELECT pedidos entregados + montos por gu├¡a
    DB-->>API: Resumen financiero
    API-->>FE_CHO: Reporte visual por gu├¡a
    FE_CHO->>Chofer: Muestra dinero esperado por gu├¡a

    Chofer->>FE_CHO: Declara efectivo f├¡sico en mano
    FE_CHO->>API: POST /guias-ruta/{id}/arqueo {efectivoDeclarado}
    API->>DB: UPDATE guias_remision SET estado='confirmacion_cierre', efectivo_declarado
    API-->>FE_CHO: 200 OK ÔÇö esperando confirmaci├│n del operador
    FE_CHO->>Chofer: Gu├¡a en estado pendiente de cierre

    Note over FE_OPE,Operador: Operador ve card de gu├¡as pendientes de cierre
    Operador->>FE_OPE: Abre gu├¡a en estado confirmacion_cierre
    FE_OPE->>API: GET /guias-remision/{id}/detalle
    API-->>FE_OPE: Detalle de mercader├¡a a recibir y efectivo declarado
    FE_OPE->>Operador: Muestra formulario de recepci├│n de mercader├¡a

    Operador->>FE_OPE: Clasifica mercader├¡a devuelta
    loop Por cada producto devuelto
        alt Mercader├¡a en buen estado
            FE_OPE->>API: POST /inventario/ingreso {productoId, cantidad, motivo: 'devolucion_buen_estado'}
            API->>DB: UPDATE productos SET cantidad_fisica += cantidad
            API->>DB: INSERT transacciones_inventario (ingreso maestro)
        else Mercader├¡a en mal estado
            FE_OPE->>API: POST /mercaderia-mal-estado {guiaRutaId, productoId, cantidad}
            API->>DB: INSERT mercaderia_mal_estado
        end
    end

    Operador->>FE_OPE: Confirma cierre
    FE_OPE->>API: PATCH /guias-remision/{id}/cerrar {efectivoRecibido}
    API->>DB: UPDATE guias_remision SET estado = 'cerrada'
    API->>DB: UPDATE bodega_camion SET cantidad_actual = 0 (encerado)
    API->>DB: INSERT bitacora_auditoria
    API-->>FE_OPE: 200 OK ÔÇö bodega encerada
    FE_OPE->>Operador: Reporte de arqueo cerrado
```

---

#### 6.3.6 HU-006 ┬À Filtros Temporales y de Estado Estilo Datadog

```mermaid
sequenceDiagram
    actor Usuario as Admin / Operador
    participant FE as Frontend
    participant API as Backend API
    participant DB as MySQL

    Usuario->>FE: Abre M├│dulo Gesti├│n de Pedidos
    FE->>FE: Aplica filtro default: hoy + estado en_espera_asignacion
    FE->>API: GET /pedidos?fechaInicio=hoy&fechaFin=hoy&estado=en_espera_asignacion
    API->>DB: SELECT pedidos WHERE fecha BETWEEN ? AND ? AND estado = ?
    DB-->>API: Pedidos
    API-->>FE: Resultados
    FE->>Usuario: Muestra mapa + cards informativos por estado

    Usuario->>FE: Escribe atajo en textbox (ej. "1w")
    FE->>FE: Interpreta atajo ÔåÆ fechaInicio = hoy-7d, fechaFin = ahora
    FE->>API: GET /pedidos?fechaInicio={hace7d}&fechaFin={ahora}
    API->>DB: SELECT con rango calculado
    DB-->>API: Resultados
    API-->>FE: Pedidos
    FE->>Usuario: Actualiza vista

    alt Usuario ingresa rango custom > 30 d├¡as
        FE->>FE: Valida diferencia de fechas
        FE->>Usuario: Error ÔÇö rango m├íximo de 30 d├¡as
        Note over FE: Bloquea la petici├│n al API
    else Rango v├ílido Ôëñ 30 d├¡as
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

#### 6.3.7 HU-007 ┬À Gesti├│n del Carrito de Compras

```mermaid
sequenceDiagram
    actor Cliente
    participant FE as Frontend
    participant Cookie as Cookie Segura (navegador)
    participant API as Backend API
    participant DB as MySQL

    Cliente->>FE: Navega cat├ílogo de productos
    FE->>API: GET /productos?tipo={filtro}&orden={orden}
    API->>DB: SELECT productos WHERE cantidad_fisica - en_pedidos > 0
    DB-->>API: Productos disponibles
    API-->>FE: Cat├ílogo con stock l├│gico
    FE->>Cliente: Muestra cat├ílogo con precio, tipo y alertas de stock bajo

    Cliente->>FE: Selecciona producto ÔÇö especifica cantidad
    FE->>FE: Calcula subtotal del ├¡tem
    FE->>FE: ┬┐Producto ya existe en carrito?

    alt Producto nuevo en carrito
        FE->>Cookie: Agrega item {productoId, cantidad, precio}
        Cookie-->>FE: Carrito actualizado
    else Producto ya en carrito ÔÇö merge
        FE->>Cookie: Actualiza cantidad del item existente (+= nuevaCantidad)
        Cookie-->>FE: Cantidad fusionada
    end

    FE->>Cliente: Actualiza vista del carrito con nuevo subtotal

    Cliente->>FE: Modifica cantidad de ├¡tem en carrito
    FE->>API: GET /productos/{id} ÔÇö verifica stock actual
    API->>DB: SELECT cantidad_fisica - en_pedidos
    DB-->>API: Stock disponible
    alt Cantidad > stock disponible
        API-->>FE: Stock insuficiente
        FE->>Cliente: Alerta ÔÇö cantidad m├íxima disponible: {X}
    else Cantidad v├ílida
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

### 6.4 Diagramas de Colaboraci├│n ÔÇö HU-001 a HU-007

Cada diagrama muestra los objetos participantes y los **mensajes numerados** que se intercambian para resolver cada historia de usuario.

---

#### 6.4.1 HU-001 ┬À Colaboraci├│n ÔÇö Checkout y Liquidaci├│n de Pago

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

#### 6.4.2 HU-002 ┬À Colaboraci├│n ÔÇö Asignaci├│n de Rutas y Generaci├│n de Gu├¡as

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

#### 6.4.3 HU-003 ┬À Colaboraci├│n ÔÇö Entrega, Devoluci├│n y Facturaci├│n

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

#### 6.4.4 HU-004 ┬À Colaboraci├│n ÔÇö Aprobaci├│n Manual de Pagos

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

#### 6.4.5 HU-005 ┬À Colaboraci├│n ÔÇö Cierre de Gu├¡as y Encerado de Bodega

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

#### 6.4.6 HU-006 ┬À Colaboraci├│n ÔÇö Filtros Temporales Estilo Datadog

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

#### 6.4.7 HU-007 ┬À Colaboraci├│n ÔÇö Gesti├│n del Carrito de Compras

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

Modela el **ciclo de vida completo de un Pedido**, desde su creaci├│n hasta su cierre.

```mermaid
stateDiagram-v2
    [*] --> CarritoActivo : Cliente agrega productos

    CarritoActivo --> CheckoutIniciado : Cliente inicia checkout
    CheckoutIniciado --> CarritoAbandonado : Cliente cancela
    CarritoAbandonado --> [*] : Registrado con motivo de cancelaci├│n

    CheckoutIniciado --> EsperaAprobacion : Pago = Dep├│sito / De Una\n(comprobante adjunto)
    CheckoutIniciado --> EsperaAsignacion : Pago = TC / TD / Efectivo\n(aprobaci├│n autom├ítica)

    EsperaAprobacion --> EsperaAsignacion : Operador aprueba comprobante
    EsperaAprobacion --> Rechazado : Operador rechaza comprobante
    Rechazado --> [*]

    EsperaAsignacion --> ListoParaEntregar : Operador asigna pedido\na cami├│n activo

    ListoParaEntregar --> EnRuta : Chofer selecciona pedido\nen mapa (GPS activo)

    EnRuta --> EntregadoTotalmente : Chofer registra entrega\ncompleta del pedido
    EnRuta --> EntregadoParcialmente : Chofer registra entrega\nparcial (solo Efectivo)
    EnRuta --> NoEntregado : Chofer no pudo entregar

    EntregadoTotalmente --> CierrePendiente : Factura PDF generada\nen navegador
    EntregadoParcialmente --> CierrePendiente : Factura recalculada\ngenerada en navegador
    NoEntregado --> CierrePendiente : Registrado como no entregado

    CierrePendiente --> CierreCaja : Operador confirma\nrecepci├│n dinero y mercader├¡a

    CierreCaja --> [*] : Bodega del cami├│n\nencerada (stock = 0)
```

---

### 6.6 Diagrama de Paquetes

Muestra la **arquitectura modular** del sistema con sus dependencias entre capas y componentes.

```mermaid
flowchart TB
    subgraph Cliente_Browser["­ƒîÉ Navegador / PWA (Cliente)"]
        direction LR
        PKG_EC["­ƒôª M├│dulo E-commerce\n(Cat├ílogo, Carrito, Checkout,\nHistorial, Rastreo)"]
        PKG_PDF["­ƒôä Generaci├│n PDF\n(Factura, lado cliente)"]
        PKG_MAP_CLI["­ƒù║´©Å Mapas Cliente\n(Leaflet / Google Maps)"]
        PKG_CACHE["ÔÜí Cach├® de Im├ígenes\n(Service Worker / Cache API)"]
    end

    subgraph Frontend["­ƒûÑ´©Å Frontend Laravel (Blade + JS)"]
        direction LR
        PKG_AUTH_FE["­ƒöÉ M├│dulo Autenticaci├│n\n(Login, Registro, Recuperaci├│n)"]
        PKG_DASH["­ƒôè M├│dulo Dashboard\n(KPIs, Ventas, Stock)"]
        PKG_GP_FE["­ƒùé´©Å M├│dulo Gesti├│n Pedidos\n(Asignaci├│n, Aprobaci├│n,\nFiltros Datadog)"]
        PKG_ENT_FE["­ƒÜÜ M├│dulo Entregas\n(Mapa Ruta, Entrega, Cierre)"]
        PKG_ADM_FE["ÔÜÖ´©Å M├│dulo Administraci├│n\n(Usuarios, Veh├¡culos)"]
    end

    subgraph Backend["ÔÜÖ´©Å Backend Laravel REST API"]
        direction TB
        PKG_AUTH_BE["­ƒöæ AuthService\n(JWT, Hash, Secret Manager)"]
        PKG_PEDIDOS["­ƒôï PedidoService\n(CRUD, Estados, Auditor├¡a)"]
        PKG_INV["­ƒôª InventarioService\n(Stock, Transacciones, Bodega)"]
        PKG_RUTA["­ƒù║´©Å RutaService\n(Gu├¡as, Asignaci├│n, GPS)"]
        PKG_NOTIFY["­ƒôº NotificacionService\n(Email, Push PWA)"]
        PKG_VALID["­ƒøí´©Å ValidationLayer\n(Anti-XSS, Anti-SQLi)"]
    end

    subgraph Datos["­ƒÆ¥ Capa de Datos"]
        subgraph MySQL_DB["­ƒÉ¼ MySQL (Datos Transaccionales)"]
            T_USERS["usuarios / clientes"]
            T_PROD["productos / inventario"]
            T_PEDIDOS["pedidos / items_pedido"]
            T_GUIAS["guias_remision / guias_ruta"]
            T_BODEGA["bodega_camion / transacciones"]
            T_AUDIT["bitacora_auditoria"]
        end
        subgraph Firestore_DB["­ƒöÑ Firestore (Geolocalizaci├│n)"]
            FS_GPS["ubicaciones_camion\n(lat, lng, timestamp)"]
        end
    end

    subgraph GCP["Ôÿü´©Å Google Cloud Platform"]
        GCS["­ƒùä´©Å Google Cloud Storage\n(Im├ígenes productos,\ncomprobantes pago)"]
        GSM["­ƒöÆ Secret Manager\n(JWT Secret, DB Credentials)"]
        GCR["­ƒÉ│ Container Registry\n(Docker Images)"]
    end

    subgraph Infra["­ƒÉ│ Infraestructura Docker"]
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

### 6.7 Diagrama de Entidad-Relaci├│n

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

### 6.8 Diagrama de Flujo de Datos — Infraestructura GCP (Bajo Costo)

Arquitectura diseñada para **minimizar el costo mensual** en GCP. Se usan únicamente servicios con **capa gratuita generosa** o costo mínimo. La seguridad se gestiona con **HTTPS incluido en la URL de Cloud Run** y **JWT** en el backend, sin servicios de red adicionales pagos.

> **Estimado mensual:** ~$8–10 USD/mes (dominado por Cloud SQL `db-f1-micro`)

---

#### Nivel 0 — Contexto General

Vista de alto nivel: los cuatro actores acceden directamente a los servicios de Cloud Run por su URL pública con HTTPS incluido — sin Load Balancer ni firewall de pago.

```mermaid
flowchart TD
    CLI(["👤 Cliente\nNavegador / PWA"])
    OPE(["👤 Operador de Ruta"])
    CHO(["👤 Chofer"])
    ADM(["👤 Administrador"])

    subgraph GCP["☁️ Google Cloud Platform — Fritolay Ambato"]
        FE["📄 frontend-service\nhttps://frontend-xxxx-uc.a.run.app\n(HTTPS incluido gratis)"]
        BE["⚙️ backend-api-service\nhttps://api-xxxx-uc.a.run.app\n(HTTPS incluido gratis)"]
    end

    CLI -->|"HTTPS · Cloud Run URL"| FE
    OPE -->|"HTTPS + JWT"| FE
    CHO -->|"HTTPS + JWT · GPS"| FE
    ADM -->|"HTTPS + JWT"| FE
    FE  -->|"REST API calls + JWT Bearer"| BE
    BE  -->|"JSON responses"| FE
```

---

#### Nivel 1 — Flujo Detallado por Servicios (bajo costo)

Cada servicio muestra su costo mensual estimado y si aplica capa gratuita.

```mermaid
flowchart TD
    CLI(["👤 Cliente"])
    OPE(["👤 Operador / Admin"])
    CHO(["👤 Chofer"])

    subgraph CR["🐳 Cloud Run — $0 capa gratuita"]
        direction LR
        FE_SVC["📄 frontend-service\n*.run.app — HTTPS gratis\n(PWA · Service Worker · Blade)"]
        BE_SVC["⚙️ backend-api-service\n*.run.app — HTTPS gratis\n(Laravel API · JWT · SOLID)"]
    end

    subgraph SQL["🐬 Cloud SQL MySQL — ~$8/mes"]
        DB[("db-f1-micro · 1 vCPU · 614 MB\n10 GB SSD · Backups diarios\npedidos · inventario · guias\nauditoria · usuarios")]
    end

    subgraph FS["🔥 Firestore — $0 capa gratuita"]
        FS_GPS[("1 GB storage gratis\n50K lecturas/dia gratis\nubicaciones_camion GPS\nlat · lng · timestamp")]
    end

    subgraph GCS["🗄️ Cloud Storage — ~$0.02/GB/mes"]
        GCS_IMG[("Bucket: imagenes-productos\nAcceso publico · CDN gratis\nCache-Control: max-age=14400\n(4h en navegador — 0 costo extra)")]
        GCS_DOC[("Bucket: comprobantes-pago\nAcceso privado\nURL firmadas — Signed URLs")]
    end

    subgraph SM_BOX["🔑 Secret Manager — ~$0.06/mes"]
        SM["JWT_SECRET · DB_PASSWORD\nFIREBASE_KEY · MAIL_PASS\nGCS_SA_KEY"]
    end

    subgraph CICD["🔄 CI/CD — $0 GitHub Actions gratis"]
        direction LR
        GH["📁 GitHub\nRepositorio"]
        GHA["⚙️ GitHub Actions\nbuild · test · push\n2000 min/mes gratis"]
        AR["📦 Artifact Registry\n0.5 GB gratis\nImagenes Docker"]
    end

    subgraph EMAIL_BOX["✉️ Email — $0 Gmail SMTP"]
        GMAIL["Gmail SMTP\nsmtp.gmail.com:587\nApp Password en .env\n500 emails/dia gratis"]
    end

    subgraph FCM_BOX["🔔 Push Notifications — $0 FCM"]
        FCM["Firebase Cloud Messaging\nWeb Push / PWA\ncompletamente gratis"]
    end

    CLI -->|"HTTPS — URL *.run.app\nTLS incluido, sin LB"| FE_SVC
    OPE -->|"HTTPS + JWT Bearer"| FE_SVC
    CHO -->|"HTTPS + JWT Bearer\n+ GPS coords"| FE_SVC

    FE_SVC -->|"POST/GET/PATCH JSON\nAuthorization: Bearer JWT"| BE_SVC
    BE_SVC -->|"JSON response + Signed URL GCS"| FE_SVC

    BE_SVC -->|"Eloquent ORM queries\nTCP · VPC Connector"| DB
    DB     -->|"Result sets"| BE_SVC
    BE_SVC -->|"Firebase Admin SDK\nwriteDocument GPS"| FS_GPS
    FS_GPS -->|"onSnapshot realtime\nWebSocket — gratis"| FE_SVC

    BE_SVC   -->|"PUT multipart/form-data"| GCS_DOC
    GCS_DOC  -->|"Signed URL 15min TTL"| BE_SVC
    BE_SVC   -->|"URL publica guardada en MySQL"| GCS_IMG
    GCS_IMG  -->|"GET imagen\nCache-Control: 4h"| FE_SVC

    SM -->|"env vars en startup\ngratis hasta 6 secretos"| BE_SVC

    GH  -->|"push main\nworkflow trigger"| GHA
    GHA -->|"docker push"| AR
    AR  -->|"deploy --image\ngcloud run deploy"| FE_SVC
    AR  -->|"deploy --image\ngcloud run deploy"| BE_SVC

    BE_SVC -->|"Laravel Mail\nSMTP TLS"| GMAIL
    GMAIL  -->|"Email entregado"| CLI
    BE_SVC -->|"FCM HTTP v1 API"| FCM
    FCM    -->|"Web Push\nService Worker"| CLI
```

---

#### Nivel 2 — Flujo por Proceso de Negocio (bajo costo)

```mermaid
flowchart LR
    subgraph P1["🛒 Checkout"]
        direction TB
        A1["Cliente accede\nCloud Run URL HTTPS"]
        A2["FE llama BE API\nJWT en header"]
        A3["BE valida JWT\nsin servicio externo"]
        A4["Comprobante a GCS\nbucket docs privado"]
        A5["Pedido a Cloud SQL\nINSERT"]
        A6["Email via Gmail SMTP\ngratis"]
        A1 --> A2 --> A3 --> A4 --> A5 --> A6
    end

    subgraph P2["🚚 Tracking GPS"]
        direction TB
        B1["Chofer abre guia\nCloud Run URL"]
        B2["PWA activa GPS\nGeolocation API"]
        B3["Coords a Firestore\ngratis hasta 50K/dia"]
        B4["onSnapshot listener"]
        B5["Cliente ve mapa\nen tiempo real"]
        B6["FCM Push\npedido listo — gratis"]
        B1 --> B2 --> B3 --> B4 --> B5 --> B6
    end

    subgraph P3["📦 Imagenes — Cache sin costo"]
        direction TB
        C1["Admin sube imagen"]
        C2["Backend PUT GCS\nbucket publico"]
        C3["URL guardada en MySQL"]
        C4["Cliente pide imagen"]
        C5["En cache navegador?"]
        C6["HIT: 0 costo GCS\nservido localmente"]
        C7["MISS: GCS fetch\nCache 4h gratis"]
        C1 --> C2 --> C3 --> C4 --> C5
        C5 -->|"HIT"| C6
        C5 -->|"MISS"| C7
    end

    subgraph P4["🔄 Deploy — GitHub Actions"]
        direction TB
        D1["git push main\nGitHub gratis"]
        D2["GitHub Actions\n2000 min/mes gratis"]
        D3["docker build + test\nphp artisan test"]
        D4["docker push\nArtifact Registry"]
        D5["gcloud run deploy\nCloud Run revision"]
        D1 --> D2 --> D3 --> D4 --> D5
    end
```

---

#### Nivel 3 — Tabla de Costos Estimados

| Servicio GCP | Uso estimado | Capa gratuita | Costo/mes |
|---|---|---|---|
| **Cloud Run** (frontend + backend) | ~500K requests/mes | 2M requests gratis | **$0** |
| **Cloud SQL MySQL** `db-f1-micro` | Horario laboral | Sin capa gratuita | **~$8–10** |
| **Firestore** | GPS realtime, <1 GB | 1 GB + 50K reads/dia gratis | **$0** |
| **Cloud Storage** | ~5 GB imagenes + docs | 5 GB gratis primeros 90 dias | **~$0.10** |
| **Secret Manager** | 5 secretos | 6 secretos gratis/mes | **$0** |
| **Artifact Registry** | 2 imagenes Docker | 0.5 GB gratis | **$0** |
| **GitHub Actions** | CI/CD build + deploy | 2000 min/mes gratis | **$0** |
| **Gmail SMTP** | Notificaciones email | 500 emails/dia gratis | **$0** |
| **FCM** | Push notifications PWA | Completamente gratis | **$0** |
| **HTTPS / TLS** | Incluido en Cloud Run URL | Incluido siempre | **$0** |
| | | **Total estimado** | **~$8–10/mes** |

> [!TIP]
> **Optimizacion de costo:** Configura Cloud SQL con parada programada fuera de horario laboral (22h–6h) para reducir el costo hasta **~$3–5/mes**.

---

#### Nivel 4 — Seguridad con Cloud Run URL (sin servicios pagos)

La seguridad del sistema se implementa dentro de la propia aplicacion Laravel — sin Cloud Armor, sin IAP, sin Load Balancer.

```mermaid
flowchart LR
    subgraph Usuario["👤 Usuario — cualquier rol"]
        BROWSER["Navegador / PWA"]
    end

    subgraph CR_SEC["🔒 Seguridad en Cloud Run — gratis"]
        direction TB
        TLS["TLS 1.3 HTTPS\nincluido en *.run.app\nsin Load Balancer"]
        JWT_V["JWT Validation\nMiddleware Laravel\nsin servicio externo"]
        VAL["Validacion inputs\nanti-XSS · anti-SQLi\nLaravel FormRequest"]
        HASH["bcrypt password hash\nLaravel Hash facade"]
        CORS["CORS configurado\nen Laravel\nsolo dominios propios"]
    end

    subgraph DATA["💾 Datos protegidos"]
        SM2["Secret Manager\nJWT_SECRET · DB_PASS\n~$0.06/mes"]
        DB2[("Cloud SQL MySQL\nVPC privado\nsin IP publica")]
    end

    BROWSER -->|"HTTPS *.run.app"| TLS
    TLS --> JWT_V
    JWT_V --> VAL
    VAL --> HASH
    HASH --> CORS
    CORS -->|"Solo si JWT valido\ny rol autorizado"| DB2
    SM2 -->|"Secretos en env vars\nal arrancar contenedor"| JWT_V
```
