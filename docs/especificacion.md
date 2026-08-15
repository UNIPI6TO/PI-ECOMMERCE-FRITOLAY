## Sistema E-commerce y GestiÃ³n de Pedidos "Fritolay Ambato" 1. Objetivos del Proyecto Objetivo General

- Construir una aplicaciÃ³n que debe ser web y compatible con PWA.

- Desarrollar un sistema capaz de captar pedidos, ver en tiempo real su entrega, gestionar pedidos, guÃ­as de remisiÃ³n de camiones y rutas.

## Objetivos EspecÃ­ficos de Arquitectura

- La aplicaciÃ³n debe estar en la nube y ser una aplicaciÃ³n orientada a microservicios y dockers.

- EjecuciÃ³n en Desarrollo: Aunque la arquitectura es nativa de la nube, debe garantizarse que la aplicaciÃ³n tambiÃ©n se pueda ejecutar en el local para el desarrollo de la misma.

- Debe existir separaciÃ³n estricta entre front end, back end, MySQL para datos transaccionales y Firestore para datos de geolocalizaciÃ³n del camiÃ³n.

- La aplicaciÃ³n debe ser desarrollada en Laravel con PHP tanto el backend y el front end y de forma independiente en el mismo repositorio de Git.

- Para los anÃ¡lisis y construcciÃ³n de la aplicaciÃ³n debe usar pony tail (https://github.com/dietrichgebert/ponytail) para optimizar el uso de tokens.

- Aplicar los principios de SOLID en la construcciÃ³n con agentes de IA para la aplicaciÃ³n, en especial el de responsabilidad Ãºnica, y aplica tambiÃ©n la ProgramaciÃ³n Orientada a Objetos.

- Las entradas del REST API o backend deben ser validadas para no recibir ataques de XSS o SQL Injection.

- Agregar test en el desarrollo del backend y front end

- 2. UI/UX e Identidad Visual

- DiseÃ±o y Usabilidad: Todo el sistema debe estar enfocado en un diseÃ±o minimalista y adaptable, priorizando la usabilidad y un buen diseÃ±o.

- Identidad Corporativa: La pÃ¡gina debe tener los colores institucionales donde debe existir una combinaciÃ³n entre estas dos pÃ¡ginas https://www.lays.com/ y https://www.fritolay.com/ que son sus productos estrellas.

- 3. Seguridad, GestiÃ³n de Estado y Variables de Entorno (Environment)

- AutenticaciÃ³n API: Implementar JWT y usar Secret Manager en GCP para resguardar los secretos de infraestructura y JWT.


- GestiÃ³n de ContraseÃ±as: Usar hash para comparaciÃ³n de las contraseÃ±as y no estÃ©n en la base de datos en texto claro. La clave para generar el hash debe estar en un archivo environment.

- RecuperaciÃ³n de Credenciales: Todos los usuarios pueden recuperar sus credenciales mediante su correo electrÃ³nico con un pin de 6 dÃ­gitos aleatorios por defecto. La cantidad de dÃ­gitos debe ser configurada con una variable de entorno.

- MensajerÃ­a: Para la configuraciÃ³n del email para mensajerÃ­a debe estar configurado en un environment en el backend para las funcionalidades.

- Persistencia Temporal: Uso de cookies seguras expirables para mantener el estado del carrito.

- CachÃ©: Las imÃ¡genes (de GCS) deben guardarse en cachÃ© del lado del cliente o navegador con una duraciÃ³n configurable (por defecto 4 horas) que sea expirable.

## 4. Roles y Permisos

- Rol Administrador: Este usuario es capaz de crear, inactivar o resetear contraseÃ±as de usuarios tipo empleados u otro rol de administraciÃ³n. Pueden ver el MÃ³dulo Dashboard de GestiÃ³n de Pedidos. TendrÃ¡ acceso al visor de facturas y exportaciÃ³n PDF.

- Rol Operador de Ruta: Este usuario gestionarÃ¡ los pedidos a entregar, como la asignaciÃ³n de los pedidos para un camiÃ³n, aprobaciÃ³n de pedidos, asignaciÃ³n de rutas con la creaciÃ³n de guÃ­a de ruta y la guÃ­a de remisiÃ³n, visor de facturas y cierre de caja del camiÃ³n. PrÃ¡cticamente estarÃ¡ usando el MÃ³dulo de GestiÃ³n de Pedidos.

- Rol Chofer: Este usuario es el chofer del camiÃ³n, Ã©l tendrÃ¡ accesos a su ruta asignada del MÃ³dulo Entregas.

- Rol Cliente: Una funcionalidad que no necesariamente debe ser autentificada es la de agregar productos al carrito de compras, la pÃ¡gina de inicio debe ser esta y debe incluir inicio de sesiÃ³n en el home page.

- 5. MÃ³dulos del Sistema y LÃ³gica de Negocio

- 5.1. Dashboard 1: EstadÃ­sticas y KPIs (MÃ³dulo Dashboard de GestiÃ³n de Pedidos)

- Indicadores: Efectividad de entrega por camiÃ³n, pedidos entregados, efectividad de entrega general y tiempos de entrega promedio.

- Ventas: Debe mostrar las ventas por dÃ­a, por sector y por camiÃ³n.

- RecaudaciÃ³n: RecaudaciÃ³n total y separado en efectivo, depÃ³sitos, cheques, De Una, Tarjeta de CrÃ©dito y Tarjetas de DÃ©bito.

- Carritos Abandonados: Compras no concretadas que el usuario haya creado el carrito y haya cancelado la compra. Al cancelar, debe poner opciones de por quÃ© cancela el pedido; las comunes son: No lo necesito, Era una proforma, Pedido Equivocado, No es lo que requiero, y otros.


- Control de Stock: En el dashboard puede consultar el stock de los productos de las bodegas, de la bodega master y de los vehÃ­culos.

## 5.2. MÃ³dulo de GestiÃ³n de Pedidos (Operativo y Administrativo)

- Rastreo en Vivo: La Ãºltima ubicaciÃ³n estarÃ¡ siempre visible en el MÃ³dulo de GestiÃ³n de Pedidos.

- Filtros de Fechas (Estilo Datadog): Se debe presentar un mapa el cual debe usar filtros de fechas especificando inicio y fin, este inicio y fin no puede ser mayor a un mes. Usar un textbox donde el usuario pueda escribir y las fechas deben cambiar (lÃ­mite es 30 dÃ­as por consulta):

- o Hoy = Fecha de inicio y fin deben estar configuradas como del dÃ­a de hoy.

- o Ayer = Fechas inicio y fin del dÃ­a de ayer.

- o 1d, 2d... 30d = Fechas configuradas de inicio 1 a 30 dÃ­as antes de la fecha y hora actual, y fecha final la fecha y hora actual.

- o 1w, 2w, 3w y 4w = Fechas configuradas de inicio 1 a 4 semanas antes, y fecha final la fecha y hora actual.

- o Cuando haga una consulta custom, el cuadro de texto debe aparecer como custom, la validaciÃ³n que no debe pasarse de 30 dÃ­as.

- Filtros por Estado: Usar tambiÃ©n filtros de estado y por defecto debe aparecer los pedidos en espera de asignaciÃ³n de ruta y los camiones que no tienen asignado rutas.

- Cards Informativos: Debe tener unos cards como tipo informativo de la cantidad de pedidos por estado, y cuando dÃ© un clic en el card este filtre por el estado. Los estados son: En espera de asignaciÃ³n de ruta, No entregado, Entregado Parcialmente, Entregados, Pendiente de AprobaciÃ³n, Listo Para entregar, En Ruta, Todos.

- Ordenamiento: Los pedidos pueden ordenarse por: antigÃ¼edad del pedido (Por defecto), nombre del cliente y valor del pedido.

- Descuentos: El usuario puede asignar descuentos a clientes por tipo de pago, una sola configuraciÃ³n que aplicarÃ¡ en las siguientes compras. El usuario puede configurar descuentos a tipos de pagos para todos los clientes como un descuento adicional, este tiene que tener una fecha de caducidad.

- AsignaciÃ³n de Ruta y Bodegas MÃ³viles:

- o Puede seleccionar del mapa o de una lista de pedidos en espera de asignaciÃ³n de ruta para ir asignando los pedidos a un camiÃ³n activo.

- o Este tambiÃ©n tendrÃ¡ un card donde se ven los vehÃ­culos que se estÃ¡n asignado los pedidos.

- o La asignaciÃ³n termina cuando el gestor de ruta cierre la asignaciÃ³n y esto crea una guÃ­a de remisiÃ³n visual igual a las sugeridas por el SRI y una guÃ­a de ruta con los negocios a visitar, con montos a cobrar y tipos de pagos.


- o Cuando se genere debe crear una transacciÃ³n de ingreso a la bodega del camiÃ³n. Cada camiÃ³n administrarÃ¡ su bodega con transacciones de ingresos y egresos en la base de datos.

- o El mÃ³dulo debe ser capaz de gestionar los vehÃ­culos, crear camiones, cambiar de estado por temas de mantenimiento y averÃ­as, y asignar choferes los cuales son usuarios tipo chofer.

- AprobaciÃ³n de Pagos: Aprobar pedidos con tipo de pagos de depÃ³sito y de la aplicaciÃ³n De Una. Cuando un usuario haya hecho un pedido con estos pagos debe pasar por una aprobaciÃ³n y los requisitos son el comprobante de depÃ³sito para validar el pago. El pedido estÃ¡ en estado en espera por aprobaciÃ³n de pago. Los pagos de TC, TD y Efectivo estos se aprueban automÃ¡ticamente y se dejan en espera de asignaciÃ³n de ruta.

- Cierres de GuÃ­as y Arqueo (Encerar Bodega):

- o Cuando el chofer haga su arqueo de caja, este aparecerÃ¡ en estado de confirmaciÃ³n de cierre en la guÃ­a y en el camiÃ³n.

- o En la pÃ¡gina principal de la gestiÃ³n debe aparecer un card de guÃ­as pendientes por cerrar.

- o El gestor confirma la recepciÃ³n de la mercaderÃ­a devuelta y el dinero para cerrar la caja y encerar la bodega (dejar el inventario del camiÃ³n en cero).

- o Si los productos estÃ¡n en buen estado, debe actualizar el inventario master de los productos y generar transacciones de ingreso por mercaderÃ­a en buen estado.

- o La de mal estado no debe ingresar, esta debe registrarse como mercaderÃ­a mal estado en otra tabla.

- Listado de Facturas (Simuladas): Pantalla con filtros donde Administradores/Operadores visualizan facturas y pueden exportar a PDF (procesado del lado del cliente).

## 5.3. MÃ³dulo de Entregas (Chofer)

- Inventario FÃ­sico: Debe ser capaz de ver los productos del camiÃ³n existentes en el camiÃ³n e ir actualizando el inventario.

- Mapas y NavegaciÃ³n: Al momento de seleccionar la guÃ­a debe desplegarse el mapa donde estarÃ¡n puntillado los pedidos a entregar. Estas deben permitir ordenar por el punto mÃ¡s cercano de la ubicaciÃ³n actual y tambiÃ©n por la antigÃ¼edad que tiene el pedido solicitado, el ordenamiento debe realizarlo el front end. Cuando ocurra la selecciÃ³n del pedido, este pasarÃ¡ a estado Listo a ser entregado. Este debe ser seleccionado en el mapa de la aplicaciÃ³n web y cuando ocurra la aplicaciÃ³n direccionarÃ¡ a Google Maps o a Waze la ubicaciÃ³n y el chofer pueda navegar en estos mapas de aplicaciones externas y dirigirse a la ubicaciÃ³n de entrega.

- Tracking Constante: Al momento de seleccionar la guÃ­a debe compartirse la ubicaciÃ³n y esta debe ser guardada en Firestore cada cierto tiempo configurado en un environment.


- EjecuciÃ³n y FacturaciÃ³n: Cuando se entregue un pedido de forma parcial o individual, generar una factura muy parecida a las autorizadas por el SRI como simulaciÃ³n. Crear una transacciÃ³n para disminuir la cantidad fsica y la cantidad en pedido.

- Devoluciones: Las devoluciones parciales solo aplican en pedidos en efectivo, si el pago es de otro tipo la devoluciÃ³n es total.

- Cierre de Caja: GuÃ­as pendientes de cierre, un reporte visual dinero por cada guÃ­a de ruta para realizar el cierre de la caja del camiÃ³n, tener la opciÃ³n de cierre donde debe declarar el valor en efectivo actual.

## 5.4. MÃ³dulo de Clientes (E-commerce)

- CatÃ¡logo y CachÃ©: Los productos deben mostrarse como una pÃ¡gina e-commerce de fÃ¡cil uso. La foto esta debe obtenerse de GCS y de ser posible que se guarde en cachÃ© del lado del cliente con una duraciÃ³n de 4 horas para ahorrar costos de GCS, el tiempo de permanencia de cachÃ© debe ser configurable.

- Detalles y Alertas: Mostrar informaciÃ³n del producto como peso, tipo de producto (Cheetos, Papas, Doritos, Tostitos, etc.), y una breve descripciÃ³n. TambiÃ©n informaciÃ³n de descuento y, cuando el producto se acerque a un porcentaje de agotamiento de stock, mostrar las unidades disponibles. Cuando no tenga stock este debe mostrar informaciÃ³n que no hay Ã­tems disponibles y solo va a permitir ver informaciÃ³n del producto. Los productos pueden filtrarse por tipo de producto y ordenarse por tipo, por precio, por nombre.

- Carrito de Compras: Un usuario sin autentificarse o autentificado puede seleccionar productos y subir al carrito de compras. Cuando el usuario estÃ© por seleccionar producto al carrito de compras, antes debo especificar la cantidad y debe mostrarme el subtotal. Si escoge el mismo producto debe realizar un merge aumentando la cantidad. Puede modificar el carrito de compras, eliminar productos, agregar cantidades o disminuir.

- Checkout y AutenticaciÃ³n: Cuando quiera hacer el checkout debe pedir autentificarse o crear un usuario.

- Inventario LÃ³gico: En el inventario master habrÃ¡ una funcionalidad, cuando finalice el checkout el inventario debe tener tres campos: CantidadFisica, EnPedidos y la disponible serÃ¡ la cantidad fsica menos la cantidad en pedidos.

- GestiÃ³n de Direcciones y Mapa (Bidireccional):

- o Los datos requeridos para el cliente es informaciÃ³n de facturaciÃ³n, y puede registrar mÃ¡s de una direcciÃ³n con ubicaciÃ³n del mapa.

- o Por defecto debe usar la ubicaciÃ³n actual, mover el punto de entrega y su direcciÃ³n debe aparecer en el cuadro de texto.

- o TambiÃ©n debe permitir buscar una direcciÃ³n o coordenadas con un cuadro de texto, cuando ocurra esto el ping en el mapa debe moverse a lo que indica el cuadro de texto.


- o Puede seleccionar la direcciÃ³n por defecto que aparecerÃ¡ en los pedidos. El usuario debe permitir cambiar con otra o crear, editar o eliminar direcciones en el proceso de ingreso de datos o checkout.

- LiquidaciÃ³n de Pago: En el proceso de check out debe aparecer el detalle de los productos y cÃ¡lculo de descuentos, impuesto IVA, total y sub total. Siempre debe mostrar el total del pedido y el ahorro por los descuentos configurados.

- Pasarela (Simulada): Simular el pago de tarjeta de dÃ©bito, de crÃ©dito y el QR de De Una para pagos con De Una.

- Historial y PDF (Lado del Cliente): MÃ³dulo de historial de pedidos pasados. GeneraciÃ³n de facturas en PDF procesada estrictamente utilizando los recursos del navegador/dispositivo del usuario para no recargar el servidor.

- Rastreo del Cliente: El cliente puede ver y recibir una notificaciÃ³n de la aplicaciÃ³n si el chofer eligiÃ³ el pedido como "Listo para entregar". El cliente solo verÃ¡ la ubicaciÃ³n del camiÃ³n cuando el chofer seleccione el pedido y este pasarÃ¡ a este estado. Puede visualizar en el mapa la ubicaciÃ³n, el refresco de la ubicaciÃ³n del mapa serÃ¡ configurada en el archivo de environment.

## AnÃ¡lisis Previo del Sistema

A continuaciÃ³n, se detalla el anÃ¡lisis estructurado de la informaciÃ³n proporcionada para establecer el contexto de la aplicaciÃ³n.

## Actores Involucrados

- Administrador: Gestiona usuarios (crear, inactivar, resetear contraseÃ±as), visualiza el Dashboard de KPIs y tiene acceso a visores de facturas y exportaciÃ³n PDF.

- Operador de Ruta: Gestiona pedidos, aprueba pagos, asigna rutas a camiones, genera guÃ­as de remisiÃ³n/rutas y supervisa el cierre de caja de camiones.

- Chofer: Gestiona la entrega en ruta, usa navegaciÃ³n en vivo, registra entregas/devoluciones, genera facturas simuladas y realiza su arqueo/cierre de caja.

- Cliente (Invitado/Registrado): Explora el catÃ¡logo, gestiona el carrito de compras, realiza el checkout (requiere registro), sube comprobantes y rastrea su pedido.

## Procesos Principales

- ExploraciÃ³n de catÃ¡logo y gestiÃ³n de carrito de compras.

- Checkout, cÃ¡lculo de totales y selecciÃ³n de mÃ©todo de pago.

- AprobaciÃ³n de pagos (manual para depÃ³sitos/De Una, automÃ¡tica para el resto).

- AsignaciÃ³n de rutas y generaciÃ³n de guÃ­as (remisiÃ³n y ruta).

- NavegaciÃ³n GPS, entrega de pedidos y facturaciÃ³n in situ.

- Cierre de caja, encerado de bodega mÃ³vil y actualizaciÃ³n de inventario mÃ¡ster.

- VisualizaciÃ³n de mÃ©tricas y filtrado de datos (mÃ¡ximo 30 dÃ­as).


## Reglas de Negocio Principales

- RN-A: Los pedidos pagados con DepÃ³sito o "De Una" requieren carga de comprobante y aprobaciÃ³n manual por el Operador de Ruta. Los pagos con TC, TD o Efectivo se aprueban automÃ¡ticamente.

- RN-B: Las devoluciones parciales de mercaderÃ­a durante la entrega solo estÃ¡n permitidas si el mÃ©todo de pago original es Efectivo. Para otros mÃ©todos, la devoluciÃ³n debe ser total.

- RN-C: Las consultas con filtros de fechas personalizadas tienen un lÃ­mite estricto de mÃ¡ximo 30 dÃ­as de rango entre la fecha de inicio y fin.

- RN-D: La mercaderÃ­a devuelta en buen estado incrementa el inventario mÃ¡ster. La mercaderÃ­a en mal estado se registra en una tabla separada y no afecta el stock disponible de venta.

## FÃ³rmulas de CÃ¡lculo

- Inventario LÃ³gico: \$CantidadDisponible = CantidadFisica - EnPedidos\$.

- LiquidaciÃ³n de Pedido: \$TotalPedido = (Subtotal - Descuentos) + ImpuestoIVA\$.

## Excepciones

- Intentar buscar datos con un rango de fechas mayor a 30 dÃ­as.

- Intentar realizar una devoluciÃ³n parcial en un pedido pagado con Tarjeta de CrÃ©dito/DÃ©bito.

- Intentar agregar un producto al carrito cuando la cantidad solicitada supera la \$CantidadDisponible\$.

## Evidencias Requeridas

- GuÃ­as de RemisiÃ³n y GuÃ­as de Ruta.

- Comprobantes de pago (imÃ¡genes/PDF subidos por el cliente).

- Facturas simuladas en formato PDF (procesadas del lado del cliente).

- BitÃ¡coras de auditorÃ­a y tracking GPS guardado en Firestore.

## Reportes Necesarios

- Dashboard de KPIs (Efectividad, Ventas, RecaudaciÃ³n, Carritos Abandonados, Stock).

- Visor y exportaciÃ³n de Facturas.

- Reporte de Cierre de Caja por camiÃ³n.

Ã‰pica: MÃ³dulo de Clientes (E-commerce)

## HU-001 - LiquidaciÃ³n de Pago y Checkout

Como: Cliente registrado Quiero: Visualizar el detalle de mi carrito, aplicar descuentos, calcular el IVA y elegir un mÃ©todo de pago Para: Completar mi compra con total claridad sobre los montos facturados y asegurar el stock de mis productos. Prioridad: Alta


## Reglas de negocio

- RN-01: El sistema debe calcular el total utilizando la fÃ³rmula \$TotalPedido = (Subtotal - Descuentos) + ImpuestoIVA\$.

- RN-02: Si el cliente elige "DepÃ³sito" o "De Una", debe obligatoriamente adjuntar un comprobante. El pedido quedarÃ¡ en estado "En espera por aprobaciÃ³n".

- RN-03: Al finalizar el checkout, el inventario del producto debe actualizarse afectando el campo EnPedidos.

## Criterios de aceptaciÃ³n en Gherkin

CaracterÃ­stica: Checkout y LiquidaciÃ³n de compras

Escenario: Flujo principal exitoso con pago en efectivo Dado que el cliente tiene productos en su carrito de compras Y se encuentra autenticado en el sistema Cuando procede a la pantalla de checkout y selecciona "Efectivo" como mÃ©todo de pago Entonces el sistema calcula y muestra el \$TotalPedido\$ Y el sistema registra el pedido en estado "En espera de asignaciÃ³n de ruta" Y el sistema actualiza el inventario sumando la cantidad solicitada al campo EnPedidos.

Escenario: ValidaciÃ³n obligatoria de comprobante para depÃ³sito Dado que el cliente selecciona "DepÃ³sito" en el checkout Cuando intenta finalizar el pedido sin adjuntar un documento Entonces el sistema bloquea la acciÃ³n Y muestra un mensaje de error "Debe adjuntar el comprobante de depÃ³sito para continuar".

Escenario: ExcepciÃ³n por datos incompletos en la direcciÃ³n Dado que el cliente se encuentra en la pantalla de checkout Cuando no selecciona ni registra una direcciÃ³n de entrega vÃ¡lida en el mapa bidireccional Entonces el botÃ³n de "Finalizar Compra" se deshabilita Y se genera una alerta visual indicando "DirecciÃ³n de entrega obligatoria".

## Datos o campos requeridos

| Campo | Tipo de dato | Obligatorio | ValidaciÃ³n |
| --- | --- | --- | --- |
|   |   |   | Valores permitidos: |
| MetodoPago | Lista desplegable | SÃ­ | Efectivo, DepÃ³sito, De |
|   |   |   | Una, TC, TD |
| Comprobante | Archivo | Condicional | Obligatorio si MetodoPago |
|   | (Imagen/PDF) |   | es DepÃ³sito o De Una |
| DireccionEntrega Coordenadas/Texto SÃ­ |   |   | Debe existir en la base o |
|   |   |   | seleccionarse del mapa |

## Dependencias

- Historia o mÃ³dulo relacionado: MÃ³dulo de GestiÃ³n de Direcciones y Mapa Bidireccional; MÃ³dulo de AutenticaciÃ³n de Usuarios.

## Evidencias esperadas


- Registro generado en la base de datos MySQL (Tabla de Pedidos).

- Documento o archivo adjunto guardado en Google Cloud Storage (GCS).

- BitÃ¡cora de auditorÃ­a detallando la fecha, hora y usuario que generÃ³ el pedido.

Ã‰pica: MÃ³dulo de GestiÃ³n de Pedidos

## HU-002 - AsignaciÃ³n de Rutas y GeneraciÃ³n de GuÃ­as

Como: Operador de Ruta Quiero: Seleccionar pedidos en espera y asignarlos a un camiÃ³n activo Para: Generar automÃ¡ticamente la guÃ­a de remisiÃ³n, la guÃ­a de ruta y registrar el ingreso de inventario en la bodega mÃ³vil del vehÃ­culo. Prioridad: Alta

## Reglas de negocio

- RN-01: Solo los camiones en estado "Activo" pueden recibir asignaciones de pedidos.

- RN-02: Un pedido no puede asignarse a mÃ¡s de un camiÃ³n simultÃ¡neamente.

- RN-03: Al cerrar la asignaciÃ³n, se genera una transacciÃ³n automÃ¡tica de ingreso desde la bodega mÃ¡ster a la bodega del camiÃ³n seleccionado.

## Criterios de aceptaciÃ³n en Gherkin

CaracterÃ­stica: AsignaciÃ³n de pedidos a camiones

Escenario: Flujo principal exitoso de asignaciÃ³n Dado que existen pedidos en estado "En espera de asignaciÃ³n de ruta" Y el Operador de Ruta tiene un camiÃ³n "Activo" seleccionado Cuando asigna los pedidos y hace clic en "Cerrar AsignaciÃ³n" Entonces el sistema genera una GuÃ­a de RemisiÃ³n visual Y genera una GuÃ­a de Ruta con los negocios, montos y tipos de pago Y crea una transacciÃ³n de ingreso de inventario en la bodega del camiÃ³n.

Escenario: PrevenciÃ³n de registros duplicados Dado que un pedido con ID "PED-123" ya fue asignado al "CamiÃ³n A" Cuando el Operador de Ruta intenta asignarlo al "CamiÃ³n B" Entonces el sistema rechaza la operaciÃ³n Y muestra una alerta "El pedido ya se encuentra en ruta con otro vehÃ­culo".

Escenario: Permisos segÃºn el rol del usuario Dado que un usuario con rol "Chofer" inicia sesiÃ³n en el sistema Cuando intenta acceder a la pantalla de AsignaciÃ³n de Rutas Entonces el sistema deniega el acceso Y redirige al usuario a su MÃ³dulo de Entregas con el mensaje "No tiene permisos para esta acciÃ³n".

## Datos o campos requeridos

| Campo | Tipo de | Obligatorio | ValidaciÃ³n |
| --- | --- | --- | --- |
|   | dato |   |   |
| ID_Pedido Entero |   | SÃ­ | Debe existir y estar en estado "En espera |
|   |   |   | de asignaciÃ³n" |
| ID_Camion Entero |   | SÃ­ | Debe existir y estar en estado "Activo" |


## Dependencias

- Historia o mÃ³dulo relacionado: AprobaciÃ³n de Pagos (solo llegan pedidos aprobados o automÃ¡ticos); MÃ³dulo de AutenticaciÃ³n.

## Evidencias esperadas

- Registro generado: GuÃ­a de RemisiÃ³n y GuÃ­a de Ruta en MySQL.

- TransacciÃ³n de inventario en la base de datos de bodegas mÃ³viles.

- BitÃ¡cora de auditorÃ­a con la trazabilidad de la asignaciÃ³n (Operador, CamiÃ³n, Fecha).

Ã‰pica: MÃ³dulo de Entregas (Chofer)

## HU-003 - EjecuciÃ³n de Entrega, DevoluciÃ³n y FacturaciÃ³n

Como: Chofer Quiero: Registrar la entrega parcial o total de un pedido en la ubicaciÃ³n del cliente Para: Descontar el inventario fsico del camiÃ³n y generar la factura simulada en formato PDF. Prioridad: Alta

## Reglas de negocio

- RN-01: Si el pedido fue pagado con Efectivo, el chofer puede registrar una entrega parcial (devoluciÃ³n parcial).

- RN-02: Si el pedido fue pagado por mÃ©todos distintos a Efectivo, cualquier devoluciÃ³n debe ser obligatoriamente total.

- RN-03: Al confirmar la entrega, la factura PDF debe procesarse estrictamente del lado del cliente (navegador/dispositivo) para no recargar el servidor.

## Criterios de aceptaciÃ³n en Gherkin

CaracterÃ­stica: Entrega de pedidos y facturaciÃ³n

Escenario: Flujo principal exitoso de entrega total Dado que el Chofer se encuentra en la ubicaciÃ³n del cliente Y el pedido estÃ¡ en estado "Listo a ser entregado" Cuando marca el pedido como "Entregado totalmente" Entonces el sistema descuenta la cantidad fsica del inventario del camiÃ³n Y descuenta la cantidad en pedido del inventario Y genera

automÃ¡ticamente la factura en PDF procesada en el navegador.

Escenario: Excepciones establecidas por la normativa (DevoluciÃ³n parcial no permitida) Dado que el pedido fue pagado con "Tarjeta de CrÃ©dito" Cuando el Chofer intenta registrar una devoluciÃ³n parcial de mercaderÃ­a Entonces el sistema bloquea la entrada de cantidades menores al total Y muestra el mensaje de error "Las devoluciones parciales solo aplican para

pagos en efectivo. Proceda con devoluciÃ³n total o entrega completa."

Escenario: CÃ¡lculo automÃ¡tico en entrega parcial (Efectivo) Dado que un pedido de 10

unidades fue pagado en Efectivo Cuando el Chofer registra la entrega de 8 unidades y 2 unidades como devoluciÃ³n Entonces el sistema actualiza el valor a cobrar basado en las 8 unidades Y genera la factura Ãºnicamente por el valor recalculado de los artÃ­culos entregados Y el estado del pedido cambia a "Entregado Parcialmente".

## Datos o campos requeridos


| Campo | Tipo de | Obligatorio | ValidaciÃ³n |
| --- | --- | --- | --- |
|   | dato |   |   |
| CantidadEntregada Entero |   | SÃ­ | Debe ser mayor a 0 y menor o igual |
|   |   |   | a lo solicitado |
| MotivoDevolucion Texto |   | Condicional | Obligatorio si CantidadEntregada < |
|   |   |   | CantidadSolicitada |
| EstadoMercaderia Lista |   | Condicional | Valores: Buen estado, Mal estado |
|   |   |   | (si hay devoluciÃ³n) |

## Dependencias

- Historia o mÃ³dulo relacionado: MÃ³dulo de NavegaciÃ³n GPS (Waze/Google Maps); Inventario FÃ­sico de Camiones.

## Evidencias esperadas

- Reporte: Factura simulada generada en PDF.

- Registro de transacciÃ³n restando el inventario fsico del camiÃ³n.

- BitÃ¡cora de auditorÃ­a detallando la ubicaciÃ³n GPS (Firestore) al momento de marcar la entrega.

Ã‰pica: MÃ³dulo de GestiÃ³n de Pedidos (Operativo)

## HU-004 - AprobaciÃ³n Manual de Pagos con Comprobante

Como: Operador de Ruta Quiero: Revisar y aprobar los pedidos que fueron pagados mediante DepÃ³sito o la aplicaciÃ³n "De Una" Para: Validar la legitimidad del pago mediante el comprobante antes de que el pedido pase a la fase de asignaciÃ³n de ruta. Prioridad: Alta

## Reglas de negocio

- RN-01: Los pedidos realizados con pagos de Tarjeta de CrÃ©dito (TC), Tarjeta de DÃ©bito (TD) y Efectivo se aprueban automÃ¡ticamente y pasan directo a espera de asignaciÃ³n de ruta.

- RN-02: Los pedidos con mÃ©todos de pago "DepÃ³sito" o "De Una" deben permanecer en estado "En espera por aprobaciÃ³n de pago" hasta que un operador valide el comprobante.

## Criterios de aceptaciÃ³n en Gherkin

CaracterÃ­stica: AprobaciÃ³n de pagos en pedidos

Escenario: Flujo principal exitoso de aprobaciÃ³n manual Dado que un pedido se encuentra en estado "En espera por aprobaciÃ³n de pago" Y el cliente adjuntÃ³ el comprobante de depÃ³sito Cuando el Operador de Ruta revisa el documento y selecciona "Aprobar Pago" Entonces el


sistema cambia el estado del pedido a "En espera de asignaciÃ³n de ruta" Y genera un registro en la bitÃ¡cora de auditorÃ­a detallando la acciÃ³n.

Escenario: ExcepciÃ³n por mÃ©todo de pago de aprobaciÃ³n automÃ¡tica Dado que un cliente finaliza un checkout con mÃ©todo de pago "Efectivo" Cuando el sistema procesa la orden Entonces el sistema aprueba automÃ¡ticamente el pedido sin requerir intervenciÃ³n del operador Y lo coloca directamente en la lista de pedidos listos para asignaciÃ³n de ruta.

## Datos o campos requeridos

| Campo Tipo de dato ID_Pedido Entero EstadoActual Texto Archivo | Obligatorio SÃ­ SÃ­ | ValidaciÃ³n El pedido debe existir Debe ser "En espera por aprobaciÃ³n de pago" Documento adjunto por el |   |
| --- | --- | --- | --- |
| ComprobantePago (Imagen/PDF) | SÃ­ | cliente en el checkout |   |

## Dependencias

- Historia o mÃ³dulo relacionado: MÃ³dulo de Clientes (LiquidaciÃ³n de Pago y Checkout).

## Evidencias esperadas

- Registro generado con la actualizaciÃ³n del estado del pedido.

- BitÃ¡cora de auditorÃ­a indicando quÃ© operador aprobÃ³ la transacciÃ³n.

Ã‰pica: MÃ³dulo de GestiÃ³n de Pedidos (Operativo)

## HU-005 - Cierre de GuÃ­as, Arqueo y Encerado de Bodega

Como: Operador de Ruta Quiero: Confirmar la recepciÃ³n del dinero en efectivo y procesar la mercaderÃ­a devuelta por los camiones Para: Realizar el cierre de la guÃ­a de ruta, saldar la caja y encerar (dejar en cero) el inventario de la bodega del camiÃ³n. Prioridad: Alta

## Reglas de negocio

- RN-01: La mercaderÃ­a devuelta catalogada en "Buen estado" debe generar transacciones de ingreso y actualizar positivamente el inventario mÃ¡ster de productos.

- RN-02: La mercaderÃ­a devuelta en "Mal estado" no debe ingresar al inventario mÃ¡ster y debe registrarse en una tabla independiente.

## Criterios de aceptaciÃ³n en Gherkin

CaracterÃ­stica: Cierre de caja y encerado de bodegas mÃ³viles

Escenario: Flujo principal exitoso de cierre con mercaderÃ­a en buen estado Dado que un camiÃ³n tiene una guÃ­a en estado de "ConfirmaciÃ³n de cierre" Y el chofer ha declarado el valor en efectivo actual en su arqueo Cuando el Operador de Ruta confirma la recepciÃ³n del dinero y


de los productos devueltos en buen estado Entonces el sistema actualiza el inventario mÃ¡ster sumando los productos recibidos Y genera las transacciones de ingreso correspondientes Y el sistema encera el inventario del camiÃ³n dejÃ¡ndolo en cero.

Escenario: ValidaciÃ³n de mercaderÃ­a en mal estado Dado que el Operador de Ruta procesa el cierre de una guÃ­a con productos devueltos Cuando clasifica una parte de la mercaderÃ­a como "Mal estado" Entonces el sistema registra estos Ã­tems en la tabla exclusiva de mercaderÃ­a en mal estado Y omite la actualizaciÃ³n de estos Ã­tems en el inventario mÃ¡ster disponible.

## Datos o campos requeridos

| Tipo de Campo dato ID_Guia Entero |   | Obligatorio SÃ­ |   |   | ValidaciÃ³n |   | Debe estar en estado "ConfirmaciÃ³n de cierre" |   |
| --- | --- | --- | --- | --- | --- | --- | --- | --- |
| EfectivoRecibido Decimal EstadoMercaderia Lista |   | SÃ­ SÃ­ |   |   |   |   | Monto entregado por el chofer "Buen estado" o "Mal estado" |   |

## Dependencias

- Historia o mÃ³dulo relacionado: MÃ³dulo de Entregas (Cierre de Caja del Chofer).

## Evidencias esperadas

- Registro generado en el Inventario MÃ¡ster (si aplica buen estado).

- Registro generado en la tabla de mercaderÃ­a en mal estado (si aplica).

- Reporte de arqueo de caja cerrado exitosamente.

Ã‰pica: MÃ³dulo Dashboard y EstadÃ­sticas

## HU-006 - Filtros Temporales y de Estado Estilo Datadog

Como: Administrador / Operador de Ruta Quiero: Visualizar los pedidos en un mapa utilizando filtros por rangos de fechas personalizables y tarjetas de estados Para: Monitorizar la operaciÃ³n y localizar rÃ¡pidamente los pedidos segÃºn su situaciÃ³n actual. Prioridad: Media

## Reglas de negocio

- RN-01: El filtro de fechas personalizado no puede exceder un lÃ­mite de consulta mÃ¡ximo de 30 dÃ­as entre la fecha de inicio y la fecha fin.

- RN-02: Al presionar un "Card Informativo" de estado, el sistema debe filtrar automÃ¡ticamente la vista principal mostrando Ãºnicamente los pedidos con dicho estado.

## Criterios de aceptaciÃ³n en Gherkin

CaracterÃ­stica: Filtros de bÃºsqueda estilo Datadog y tarjetas informativas


Escenario: Flujo principal exitoso con atajo de filtro Dado que el usuario se encuentra en el MÃ³dulo de GestiÃ³n de Pedidos Cuando ingresa el comando "1w" en el cuadro de texto de fechas Entonces el sistema configura la fecha de inicio a 1 semana antes de la fecha actual Y configura la fecha final como la fecha y hora actual Y actualiza la informaciÃ³n mostrada en pantalla.

Escenario: ValidaciÃ³n obligatoria de lÃ­mite de 30 dÃ­as en consulta custom Dado que el usuario utiliza el filtro custom ingresando fechas manualmente Cuando define un rango superior a 30 dÃ­as entre inicio y fin Entonces el sistema bloquea la bÃºsqueda Y genera un mensaje de error indicando que la consulta no puede sobrepasar los 30 dÃ­as.

## Datos o campos requeridos

| Tipo de Campo dato | Obligatorio | ValidaciÃ³n Valores como: Hoy, Ayer, 1d-30d, 1w- |   |
| --- | --- | --- | --- |
| FiltroFecha Texto/Atajo SÃ­ |   |   |   |
| RangoFechas Date | Condicional | 4w, o custom La diferencia entre fechas no puede superar 30 dÃ­as |   |
| FiltroEstado BotÃ³n/Card No |   | Estados: En espera, Entregados, En Ruta, etc. |   |

## Dependencias

- Historia o mÃ³dulo relacionado: MÃ³dulo de GestiÃ³n de Pedidos.

## Evidencias esperadas

- Reporte visual filtrado exitosamente en la interfaz (Front end).

Ã‰pica: MÃ³dulo de Clientes (E-commerce)

## HU-007 - GestiÃ³n del Carrito de Compras

Como: Cliente Quiero: Agregar productos al carrito, modificar sus cantidades y visualizar el subtotal de mi pedido Para: Preparar mi orden de compra guardando el estado de mi selecciÃ³n sin necesidad inmediata de iniciar sesiÃ³n. Prioridad: Alta

## Reglas de negocio

- RN-01: Si el usuario selecciona un producto que ya se encuentra en el carrito, el sistema debe realizar un merge (fusiÃ³n) aumentando Ãºnicamente la cantidad de dicho Ã­tem.

- RN-02: El estado del carrito de compras debe preservarse utilizando cookies seguras expirables (Persistencia Temporal).

## Criterios de aceptaciÃ³n en Gherkin


CaracterÃ­stica: AdministraciÃ³n de Ã­tems en el carrito de compras

Escenario: Flujo principal exitoso agregando cantidades al carrito Dado que el usuario estÃ¡ visualizando el catÃ¡logo de productos Y selecciona un Ã­tem disponible Cuando especifica la cantidad deseada y lo aÃ±ade al carrito de compras Entonces el sistema agrega el Ã­tem al listado del carrito Y muestra inmediatamente el subtotal calculado.

Escenario: PrevenciÃ³n de duplicados mediante merge de productos Dado que el cliente tiene 2 unidades de "Papas Lays" en su carrito Cuando vuelve al catÃ¡logo y aÃ±ade 3 unidades mÃ¡s del mismo producto Entonces el sistema no crea una nueva lÃ­nea en el carrito Y realiza un merge actualizando la cantidad del producto a 5 unidades.

## Datos o campos requeridos

| Tipo de Campo dato ID_Producto Entero | Obligatorio SÃ­ | ValidaciÃ³n Debe existir en el catÃ¡logo e-commerce |   |
| --- | --- | --- | --- |
| Cantidad Entero | SÃ­ | Debe ser mayor a cero y menor o igual al stock disponible |   |
| CookieSesion Texto | AutomÃ¡tico | Se gestiona de forma segura expirable en el navegador |   |

## Dependencias

- Historia o mÃ³dulo relacionado: CatÃ¡logo e Inventario LÃ³gico.

## Evidencias esperadas

- Registro temporal generado en la cookie segura del cliente.

---

## 6. Diagramas UML

A continuaciÃ³n se presentan los diagramas UML del sistema **Fritolay Ambato**, modelando su arquitectura, comportamiento y base de datos.

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
    %% â”€â”€ Actores â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€
    CLI(["\nðŸ‘¤\nCliente\n"])
    ADM(["\nðŸ‘¤\nAdministrador\n"])
    OPE(["\nðŸ‘¤\nOperador\nde Ruta\n"])
    CHO(["\nðŸ‘¤\nChofer\n"])

    %% â”€â”€ Frontera del sistema â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€
    subgraph SYS["ðŸ–¥ï¸  Sistema E-commerce Fritolay Ambato"]

        subgraph EC["ðŸ“¦ E-commerce / Clientes"]
            UC01(["Ver CatÃ¡logo\nde Productos"])
            UC02(["HU-007 Â· Gestionar\nCarrito de Compras"])
            UC03(["HU-001 Â· Realizar\nCheckout"])
            UC04(["Adjuntar Comprobante\nde Pago"])
            UC05(["Ver Historial\nde Pedidos"])
            UC06(["Rastrear Pedido\nen Mapa"])
            UC07(["Gestionar Direcciones\nMapa Bidireccional"])
            UC08(["Generar Factura\nPDF â€” lado cliente"])
            UC09(["Autenticarse /\nRegistrarse"])
            UC10(["Recuperar\nCredenciales"])
        end

        subgraph GP["ðŸ—‚ï¸ GestiÃ³n de Pedidos"]
            UC11(["HU-004 Â· Aprobar Pago\ncon Comprobante"])
            UC12(["HU-002 Â· Asignar\nPedidos a CamiÃ³n"])
            UC13(["HU-002 Â· Generar GuÃ­a\nRemisiÃ³n y Ruta"])
            UC14(["HU-006 Â· Filtros\nEstilo Datadog"])
            UC15(["HU-006 Â· Ver Cards\nInformativos por Estado"])
            UC16(["Configurar\nDescuentos"])
            UC17(["HU-005 Â· Confirmar\nCierre y Encerado"])
            UC18(["Ver Visor\nde Facturas PDF"])
            UC19(["Gestionar\nVehÃ­culos CRUD"])
        end

        subgraph EN["ðŸšš Entregas â€” Chofer"]
            UC20(["Ver GuÃ­a de\nRuta Asignada"])
            UC21(["HU-003 Â· Registrar\nEntrega Total/Parcial"])
            UC22(["HU-003 Â· Registrar\nDevoluciÃ³n"])
            UC23(["Ver Inventario\ndel CamiÃ³n"])
            UC24(["HU-003 Â· Generar\nFactura PDF in situ"])
            UC25(["Realizar Cierre\nde Caja"])
            UC26(["Compartir UbicaciÃ³n\nGPS â€” Firestore"])
            UC27(["Navegar Google\nMaps / Waze"])
        end

        subgraph DB["ðŸ“Š Dashboard"]
            UC28(["Ver KPIs\ny EstadÃ­sticas"])
            UC29(["Ver Ventas por\nSector / CamiÃ³n"])
            UC30(["Ver RecaudaciÃ³n\npor MÃ©todo de Pago"])
            UC31(["Ver Carritos\nAbandonados"])
            UC32(["Consultar Stock\nde Bodegas"])
        end

        subgraph ADM_M["âš™ï¸ AdministraciÃ³n"]
            UC33(["Crear / Inactivar\nUsuarios Empleados"])
            UC34(["Resetear\nContraseÃ±as"])
        end

        %% â”€â”€ Relaciones <<include>> y <<extend>> â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€
        UC03 -- "<<include>>" --> UC09
        UC03 -- "<<include>>" --> UC07
        UC03 -- "<<extend>>\n[pago DepÃ³sito/De Una]" --> UC04
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

    %% â”€â”€ Asociaciones Actor â†” Casos de Uso â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€
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

### 6.3 Diagramas de Secuencia â€” HU-001 a HU-007

Cada diagrama modela el flujo de mensajes entre actores y componentes del sistema para cada Historia de Usuario.

---

#### 6.3.1 HU-001 Â· LiquidaciÃ³n de Pago y Checkout

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
    FE->>Cliente: Muestra Ã­tems y subtotal

    Cliente->>FE: Inicia Checkout
    FE->>FE: Verifica token JWT
    alt No autenticado
        FE->>Cliente: Redirige a Login / Registro
        Cliente->>FE: EnvÃ­a credenciales
        FE->>API: POST /auth/login
        API->>DB: Valida hash de contraseÃ±a
        DB-->>API: Usuario vÃ¡lido
        API-->>FE: JWT Token
    end

    FE->>Cliente: Muestra pantalla Checkout
    Cliente->>FE: Selecciona DireccionEntrega (mapa bidireccional)
    Cliente->>FE: Selecciona MetodoPago

    alt MetodoPago = DepÃ³sito | De Una
        Cliente->>FE: Adjunta comprobante (imagen/PDF)
        FE->>GCS: PUT /comprobantes/{filename}
        GCS-->>FE: URL pÃºblica del archivo
    end

    FE->>API: POST /pedidos {items, direcciÃ³n, metodoPago, comprobante}
    API->>API: Sanitiza entrada (anti-XSS, anti-SQLi)
    API->>DB: SELECT cantidad_fisica - en_pedidos (por producto)

    alt Stock insuficiente
        API-->>FE: 422 Unprocessable â€” stock no disponible
        FE->>Cliente: Alerta visual de stock agotado
    else Stock OK
        API->>DB: INSERT pedidos (estado segÃºn mÃ©todo de pago)
        API->>DB: UPDATE productos SET en_pedidos += cantidad
        API->>DB: INSERT items_pedido
        API->>DB: INSERT bitacora_auditoria
        API-->>FE: 201 Created {pedidoId, estado}
        FE->>FE: Genera PDF proforma (lado cliente â€” sin servidor)
        FE->>Cliente: ConfirmaciÃ³n + PDF descargable
        FE->>Email: EnvÃ­a notificaciÃ³n pedido recibido
    end
```

---

#### 6.3.2 HU-002 Â· AsignaciÃ³n de Rutas y GeneraciÃ³n de GuÃ­as

```mermaid
sequenceDiagram
    actor Operador as Operador de Ruta
    participant FE as Frontend
    participant API as Backend API
    participant DB as MySQL

    Operador->>FE: Abre mÃ³dulo GestiÃ³n de Pedidos
    FE->>API: GET /pedidos?estado=en_espera_asignacion
    API->>DB: SELECT pedidos WHERE estado = 'en_espera_asignacion'
    DB-->>API: Lista de pedidos
    API-->>FE: Pedidos pendientes
    FE->>Operador: Muestra lista y mapa con pedidos

    Operador->>FE: Selecciona pedidos del mapa/lista
    Operador->>FE: Selecciona camiÃ³n activo
    FE->>API: GET /camiones?estado=activo
    API->>DB: SELECT camiones WHERE estado = 'activo'
    DB-->>API: Lista de camiones
    API-->>FE: Camiones disponibles
    FE->>Operador: Muestra card de camiÃ³n seleccionado

    Operador->>FE: Clic "Cerrar AsignaciÃ³n"
    FE->>API: POST /asignaciones {pedidoIds[], camionId}
    API->>DB: Verifica pedidos no asignados
    alt Pedido ya asignado
        API-->>FE: 409 Conflict â€” pedido ya en ruta
        FE->>Operador: Alerta pedido duplicado
    else ValidaciÃ³n OK
        API->>DB: INSERT guias_remision
        API->>DB: INSERT guias_ruta
        API->>DB: INSERT asignacion_pedido_camion
        API->>DB: INSERT transacciones_inventario (ingreso bodega camiÃ³n)
        API->>DB: UPDATE bodega_camion
        API->>DB: UPDATE pedidos SET estado = 'listo_para_entregar'
        API->>DB: INSERT bitacora_auditoria
        API-->>FE: 201 {guiaRemisionId, guiaRutaId}
        FE->>FE: Renderiza GuÃ­a RemisiÃ³n PDF (lado cliente)
        FE->>FE: Renderiza GuÃ­a Ruta PDF (lado cliente)
        FE->>Operador: Muestra guÃ­as generadas
    end
```

---

#### 6.3.3 HU-003 Â· EjecuciÃ³n de Entrega, DevoluciÃ³n y FacturaciÃ³n

```mermaid
sequenceDiagram
    actor Chofer
    participant FE as Frontend (PWA)
    participant API as Backend API
    participant DB as MySQL
    participant FS as Firestore GPS
    participant ExtMap as Google Maps / Waze

    Chofer->>FE: Abre mÃ³dulo Entregas
    FE->>API: GET /guias-ruta?estado=activa&choferId={id}
    API->>DB: SELECT guias asignadas al chofer
    DB-->>API: GuÃ­as activas
    API-->>FE: Lista de guÃ­as
    FE->>Chofer: Muestra guÃ­as y mapa con pedidos puntuados

    Chofer->>FE: Selecciona guÃ­a de ruta
    FE->>FS: START watch ubicacion_camion/{camionId}
    Note over FE,FS: GPS se comparte en Firestore cada N segundos (configurable)

    Chofer->>FE: Selecciona pedido del mapa
    FE->>API: PATCH /pedidos/{id} {estado: listo_a_ser_entregado}
    API->>DB: UPDATE pedidos
    API-->>FE: OK
    FE->>ExtMap: Abre Google Maps / Waze con coordenadas cliente

    Chofer->>FE: Llega y registra entrega
    FE->>Chofer: Formulario â€” cantidad entregada / devuelta / estado mercaderÃ­a

    alt Entrega total
        FE->>API: POST /entregas {pedidoId, cantidadEntregada: total, estado: entregado}
        API->>DB: UPDATE pedidos SET estado = 'entregado'
        API->>DB: UPDATE bodega_camion (egreso fÃ­sico)
        API->>DB: UPDATE productos SET en_pedidos -= cantidad
        API->>DB: INSERT transacciones_inventario (egreso)
    else Entrega parcial (solo Efectivo)
        FE->>API: POST /entregas {pedidoId, cantidadEntregada, cantidadDevuelta, motivoDevolucion, estadoMercaderia}
        API->>DB: UPDATE pedidos SET estado = 'entregado_parcialmente'
        API->>DB: UPDATE bodega_camion
        API->>DB: UPDATE productos SET en_pedidos -= cantidadEntregada
        API->>DB: INSERT transacciones_inventario
    else MÃ©todo pago != Efectivo y devoluciÃ³n parcial
        API-->>FE: 422 Error â€” devoluciÃ³n parcial no permitida
        FE->>Chofer: Mensaje â€” solo devoluciÃ³n total permitida
    end

    API->>DB: INSERT bitacora_auditoria {ubicacionGPS}
    API-->>FE: 201 {facturaData}
    FE->>FE: Genera Factura PDF (lado cliente â€” navegador)
    FE->>Chofer: Factura disponible para imprimir/compartir
```

---

#### 6.3.4 HU-004 Â· AprobaciÃ³n Manual de Pagos con Comprobante

```mermaid
sequenceDiagram
    actor Operador as Operador de Ruta
    participant FE as Frontend
    participant API as Backend API
    participant DB as MySQL
    participant GCS as Cloud Storage
    participant Email as Email Service

    Operador->>FE: Abre lista de pedidos pendientes de aprobaciÃ³n
    FE->>API: GET /pedidos?estado=en_espera_aprobacion
    API->>DB: SELECT pedidos WHERE estado = 'en_espera_aprobacion'
    DB-->>API: Pedidos con mÃ©todo DepÃ³sito / De Una
    API-->>FE: Lista de pedidos
    FE->>Operador: Muestra pedidos con botÃ³n "Revisar"

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
        FE->>Email: Notifica al cliente â€” pago aprobado
        FE->>Operador: ConfirmaciÃ³n visual
    else Operador rechaza pago
        Operador->>FE: Clic "Rechazar" + motivo
        FE->>API: PATCH /pedidos/{id}/rechazar {motivo}
        API->>DB: UPDATE pedidos SET estado = 'rechazado'
        API->>DB: UPDATE productos SET en_pedidos -= cantidad
        API->>DB: INSERT bitacora_auditoria
        API-->>FE: 200 OK
        FE->>Email: Notifica al cliente â€” pago rechazado con motivo
        FE->>Operador: ConfirmaciÃ³n visual
    end
```

---

#### 6.3.5 HU-005 Â· Cierre de GuÃ­as, Arqueo y Encerado de Bodega

```mermaid
sequenceDiagram
    actor Chofer
    actor Operador as Operador de Ruta
    participant FE_CHO as Frontend Chofer
    participant FE_OPE as Frontend Operador
    participant API as Backend API
    participant DB as MySQL

    Chofer->>FE_CHO: Abre mÃ³dulo Cierre de Caja
    FE_CHO->>API: GET /guias-ruta/{id}/resumen-caja
    API->>DB: SELECT pedidos entregados + montos por guÃ­a
    DB-->>API: Resumen financiero
    API-->>FE_CHO: Reporte visual por guÃ­a
    FE_CHO->>Chofer: Muestra dinero esperado por guÃ­a

    Chofer->>FE_CHO: Declara efectivo fÃ­sico en mano
    FE_CHO->>API: POST /guias-ruta/{id}/arqueo {efectivoDeclarado}
    API->>DB: UPDATE guias_remision SET estado='confirmacion_cierre', efectivo_declarado
    API-->>FE_CHO: 200 OK â€” esperando confirmaciÃ³n del operador
    FE_CHO->>Chofer: GuÃ­a en estado pendiente de cierre

    Note over FE_OPE,Operador: Operador ve card de guÃ­as pendientes de cierre
    Operador->>FE_OPE: Abre guÃ­a en estado confirmacion_cierre
    FE_OPE->>API: GET /guias-remision/{id}/detalle
    API-->>FE_OPE: Detalle de mercaderÃ­a a recibir y efectivo declarado
    FE_OPE->>Operador: Muestra formulario de recepciÃ³n de mercaderÃ­a

    Operador->>FE_OPE: Clasifica mercaderÃ­a devuelta
    loop Por cada producto devuelto
        alt MercaderÃ­a en buen estado
            FE_OPE->>API: POST /inventario/ingreso {productoId, cantidad, motivo: 'devolucion_buen_estado'}
            API->>DB: UPDATE productos SET cantidad_fisica += cantidad
            API->>DB: INSERT transacciones_inventario (ingreso maestro)
        else MercaderÃ­a en mal estado
            FE_OPE->>API: POST /mercaderia-mal-estado {guiaRutaId, productoId, cantidad}
            API->>DB: INSERT mercaderia_mal_estado
        end
    end

    Operador->>FE_OPE: Confirma cierre
    FE_OPE->>API: PATCH /guias-remision/{id}/cerrar {efectivoRecibido}
    API->>DB: UPDATE guias_remision SET estado = 'cerrada'
    API->>DB: UPDATE bodega_camion SET cantidad_actual = 0 (encerado)
    API->>DB: INSERT bitacora_auditoria
    API-->>FE_OPE: 200 OK â€” bodega encerada
    FE_OPE->>Operador: Reporte de arqueo cerrado
```

---

#### 6.3.6 HU-006 Â· Filtros Temporales y de Estado Estilo Datadog

```mermaid
sequenceDiagram
    actor Usuario as Admin / Operador
    participant FE as Frontend
    participant API as Backend API
    participant DB as MySQL

    Usuario->>FE: Abre MÃ³dulo GestiÃ³n de Pedidos
    FE->>FE: Aplica filtro default: hoy + estado en_espera_asignacion
    FE->>API: GET /pedidos?fechaInicio=hoy&fechaFin=hoy&estado=en_espera_asignacion
    API->>DB: SELECT pedidos WHERE fecha BETWEEN ? AND ? AND estado = ?
    DB-->>API: Pedidos
    API-->>FE: Resultados
    FE->>Usuario: Muestra mapa + cards informativos por estado

    Usuario->>FE: Escribe atajo en textbox (ej. "1w")
    FE->>FE: Interpreta atajo â†’ fechaInicio = hoy-7d, fechaFin = ahora
    FE->>API: GET /pedidos?fechaInicio={hace7d}&fechaFin={ahora}
    API->>DB: SELECT con rango calculado
    DB-->>API: Resultados
    API-->>FE: Pedidos
    FE->>Usuario: Actualiza vista

    alt Usuario ingresa rango custom > 30 dÃ­as
        FE->>FE: Valida diferencia de fechas
        FE->>Usuario: Error â€” rango mÃ¡ximo de 30 dÃ­as
        Note over FE: Bloquea la peticiÃ³n al API
    else Rango vÃ¡lido â‰¤ 30 dÃ­as
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

#### 6.3.7 HU-007 Â· GestiÃ³n del Carrito de Compras

```mermaid
sequenceDiagram
    actor Cliente
    participant FE as Frontend
    participant Cookie as Cookie Segura (navegador)
    participant API as Backend API
    participant DB as MySQL

    Cliente->>FE: Navega catÃ¡logo de productos
    FE->>API: GET /productos?tipo={filtro}&orden={orden}
    API->>DB: SELECT productos WHERE cantidad_fisica - en_pedidos > 0
    DB-->>API: Productos disponibles
    API-->>FE: CatÃ¡logo con stock lÃ³gico
    FE->>Cliente: Muestra catÃ¡logo con precio, tipo y alertas de stock bajo

    Cliente->>FE: Selecciona producto â€” especifica cantidad
    FE->>FE: Calcula subtotal del Ã­tem
    FE->>FE: Â¿Producto ya existe en carrito?

    alt Producto nuevo en carrito
        FE->>Cookie: Agrega item {productoId, cantidad, precio}
        Cookie-->>FE: Carrito actualizado
    else Producto ya en carrito â€” merge
        FE->>Cookie: Actualiza cantidad del item existente (+= nuevaCantidad)
        Cookie-->>FE: Cantidad fusionada
    end

    FE->>Cliente: Actualiza vista del carrito con nuevo subtotal

    Cliente->>FE: Modifica cantidad de Ã­tem en carrito
    FE->>API: GET /productos/{id} â€” verifica stock actual
    API->>DB: SELECT cantidad_fisica - en_pedidos
    DB-->>API: Stock disponible
    alt Cantidad > stock disponible
        API-->>FE: Stock insuficiente
        FE->>Cliente: Alerta â€” cantidad mÃ¡xima disponible: {X}
    else Cantidad vÃ¡lida
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

### 6.4 Diagramas de ColaboraciÃ³n â€” HU-001 a HU-007

Cada diagrama muestra los objetos participantes y los **mensajes numerados** que se intercambian para resolver cada historia de usuario.

---

#### 6.4.1 HU-001 Â· ColaboraciÃ³n â€” Checkout y LiquidaciÃ³n de Pago

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

#### 6.4.2 HU-002 Â· ColaboraciÃ³n â€” AsignaciÃ³n de Rutas y GeneraciÃ³n de GuÃ­as

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

#### 6.4.3 HU-003 Â· ColaboraciÃ³n â€” Entrega, DevoluciÃ³n y FacturaciÃ³n

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

#### 6.4.4 HU-004 Â· ColaboraciÃ³n â€” AprobaciÃ³n Manual de Pagos

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

#### 6.4.5 HU-005 Â· ColaboraciÃ³n â€” Cierre de GuÃ­as y Encerado de Bodega

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

#### 6.4.6 HU-006 Â· ColaboraciÃ³n â€” Filtros Temporales Estilo Datadog

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

#### 6.4.7 HU-007 Â· ColaboraciÃ³n â€” GestiÃ³n del Carrito de Compras

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

Modela el **ciclo de vida completo de un Pedido**, desde su creaciÃ³n hasta su cierre.

```mermaid
stateDiagram-v2
    [*] --> CarritoActivo : Cliente agrega productos

    CarritoActivo --> CheckoutIniciado : Cliente inicia checkout
    CheckoutIniciado --> CarritoAbandonado : Cliente cancela
    CarritoAbandonado --> [*] : Registrado con motivo de cancelaciÃ³n

    CheckoutIniciado --> EsperaAprobacion : Pago = DepÃ³sito / De Una\n(comprobante adjunto)
    CheckoutIniciado --> EsperaAsignacion : Pago = TC / TD / Efectivo\n(aprobaciÃ³n automÃ¡tica)

    EsperaAprobacion --> EsperaAsignacion : Operador aprueba comprobante
    EsperaAprobacion --> Rechazado : Operador rechaza comprobante
    Rechazado --> [*]

    EsperaAsignacion --> ListoParaEntregar : Operador asigna pedido\na camiÃ³n activo

    ListoParaEntregar --> EnRuta : Chofer selecciona pedido\nen mapa (GPS activo)

    EnRuta --> EntregadoTotalmente : Chofer registra entrega\ncompleta del pedido
    EnRuta --> EntregadoParcialmente : Chofer registra entrega\nparcial (solo Efectivo)
    EnRuta --> NoEntregado : Chofer no pudo entregar

    EntregadoTotalmente --> CierrePendiente : Factura PDF generada\nen navegador
    EntregadoParcialmente --> CierrePendiente : Factura recalculada\ngenerada en navegador
    NoEntregado --> CierrePendiente : Registrado como no entregado

    CierrePendiente --> CierreCaja : Operador confirma\nrecepciÃ³n dinero y mercaderÃ­a

    CierreCaja --> [*] : Bodega del camiÃ³n\nencerada (stock = 0)
```

---

### 6.6 Diagrama de Paquetes

Muestra la **arquitectura modular** del sistema con sus dependencias entre capas y componentes.

```mermaid
flowchart TB
    subgraph Cliente_Browser["ðŸŒ Navegador / PWA (Cliente)"]
        direction LR
        PKG_EC["ðŸ“¦ MÃ³dulo E-commerce\n(CatÃ¡logo, Carrito, Checkout,\nHistorial, Rastreo)"]
        PKG_PDF["ðŸ“„ GeneraciÃ³n PDF\n(Factura, lado cliente)"]
        PKG_MAP_CLI["ðŸ—ºï¸ Mapas Cliente\n(Leaflet / Google Maps)"]
        PKG_CACHE["âš¡ CachÃ© de ImÃ¡genes\n(Service Worker / Cache API)"]
    end

    subgraph Frontend["ðŸ–¥ï¸ Frontend Laravel (Blade + JS)"]
        direction LR
        PKG_AUTH_FE["ðŸ” MÃ³dulo AutenticaciÃ³n\n(Login, Registro, RecuperaciÃ³n)"]
        PKG_DASH["ðŸ“Š MÃ³dulo Dashboard\n(KPIs, Ventas, Stock)"]
        PKG_GP_FE["ðŸ—‚ï¸ MÃ³dulo GestiÃ³n Pedidos\n(AsignaciÃ³n, AprobaciÃ³n,\nFiltros Datadog)"]
        PKG_ENT_FE["ðŸšš MÃ³dulo Entregas\n(Mapa Ruta, Entrega, Cierre)"]
        PKG_ADM_FE["âš™ï¸ MÃ³dulo AdministraciÃ³n\n(Usuarios, VehÃ­culos)"]
    end

    subgraph Backend["âš™ï¸ Backend Laravel REST API"]
        direction TB
        PKG_AUTH_BE["ðŸ”‘ AuthService\n(JWT, Hash, Secret Manager)"]
        PKG_PEDIDOS["ðŸ“‹ PedidoService\n(CRUD, Estados, AuditorÃ­a)"]
        PKG_INV["ðŸ“¦ InventarioService\n(Stock, Transacciones, Bodega)"]
        PKG_RUTA["ðŸ—ºï¸ RutaService\n(GuÃ­as, AsignaciÃ³n, GPS)"]
        PKG_NOTIFY["ðŸ“§ NotificacionService\n(Email, Push PWA)"]
        PKG_VALID["ðŸ›¡ï¸ ValidationLayer\n(Anti-XSS, Anti-SQLi)"]
    end

    subgraph Datos["ðŸ’¾ Capa de Datos"]
        subgraph MySQL_DB["ðŸ¬ MySQL (Datos Transaccionales)"]
            T_USERS["usuarios / clientes"]
            T_PROD["productos / inventario"]
            T_PEDIDOS["pedidos / items_pedido"]
            T_GUIAS["guias_remision / guias_ruta"]
            T_BODEGA["bodega_camion / transacciones"]
            T_AUDIT["bitacora_auditoria"]
        end
        subgraph Firestore_DB["ðŸ”¥ Firestore (GeolocalizaciÃ³n)"]
            FS_GPS["ubicaciones_camion\n(lat, lng, timestamp)"]
        end
    end

    subgraph GCP["â˜ï¸ Google Cloud Platform"]
        GCS["ðŸ—„ï¸ Google Cloud Storage\n(ImÃ¡genes productos,\ncomprobantes pago)"]
        GSM["ðŸ”’ Secret Manager\n(JWT Secret, DB Credentials)"]
        GCR["ðŸ³ Container Registry\n(Docker Images)"]
    end

    subgraph Infra["ðŸ³ Infraestructura Docker"]
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

### 6.7 Diagrama de Entidad-RelaciÃ³n

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

### 6.8 Diagrama de Flujo de Datos â€” Infraestructura GCP (Bajo Costo)

Arquitectura diseÃ±ada para **minimizar el costo mensual** en GCP. Se usan Ãºnicamente servicios con **capa gratuita generosa** o costo mÃ­nimo. La seguridad se gestiona con **HTTPS incluido en la URL de Cloud Run** y **JWT** en el backend, sin servicios de red adicionales pagos.

> **Estimado mensual:** ~$8â€“15 USD/mes (dominado por Cloud SQL `db-f1-micro`)

---

#### Nivel 0 â€” Contexto General (bajo costo)

```mermaid
flowchart TD
    CLI(["ðŸ‘¤ Cliente\nNavegador / PWA"])
    OPE(["ðŸ‘¤ Operador de Ruta"])
    CHO(["ðŸ‘¤ Chofer"])
    ADM(["ðŸ‘¤ Administrador"])

    subgraph GCP["â˜ï¸ Google Cloud Platform â€” Fritolay Ambato"]
        FE["ðŸ“„ frontend-service\nhttps://frontend-xxxx-uc.a.run.app"]
        BE["âš™ï¸ backend-api-service\nhttps://api-xxxx-uc.a.run.app"]
    end

    CLI -->|"HTTPS (incluido en Cloud Run URL)"| FE
    OPE -->|"HTTPS + JWT"| FE
    CHO -->|"HTTPS + JWT"| FE
    ADM -->|"HTTPS + JWT"| FE
    FE  -->|"REST API calls + JWT"| BE
    BE  -->|"JSON responses"| FE
```

---

#### Nivel 1 â€” Flujo Detallado por Servicios (bajo costo)

Cada servicio incluye su **costo estimado mensual** y si aplica **capa gratuita**.

```mermaid
flowchart TD
    %% â”€â”€ Actores â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€
    CLI(["ðŸ‘¤ Cliente"])
    OPE(["ðŸ‘¤ Operador / Admin"])
    CHO(["ðŸ‘¤ Chofer"])

    %% â”€â”€ Cloud Run (GRATIS hasta 2M requests/mes) â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€
    subgraph CR["ðŸ³ Cloud Run â€” $0 capa gratuita"]
        direction LR
        FE_SVC["ðŸ“„ frontend-service\n*.run.app â€” HTTPS gratis\n(PWA Â· Service Worker Â· Blade)"]
        BE_SVC["âš™ï¸ backend-api-service\n*.run.app â€” HTTPS gratis\n(Laravel API Â· JWT Â· SOLID)"]
    end

    %% â”€â”€ Cloud SQL MySQL â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€
    subgraph SQL["ðŸ¬ Cloud SQL MySQL â€” ~$8/mes"]
        DB[("`db-f1-micro (1 vCPU / 614 MB)
        10 GB SSD Â· Backups diarios
        pedidos Â· inventario Â· guÃ­as
        auditorÃ­a Â· usuarios`")]
    end

    %% â”€â”€ Firestore â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€
    subgraph FS["ðŸ”¥ Firestore â€” $0 capa gratuita"]
        FS_GPS[("1 GB storage gratis
        50K lecturas/dÃ­a gratis
        ubicaciones_camion GPS
        lat Â· lng Â· timestamp")]
    end

    %% â”€â”€ Cloud Storage â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€
    subgraph GCS["ðŸ—„ï¸ Cloud Storage â€” ~$0.02/GB/mes"]
        GCS_IMG[("Bucket: imÃ¡genes-productos
        Acceso pÃºblico Â· CDN gratis
        Cache-Control: max-age=14400
        (4h en navegador â€” 0 costo extra)")]
        GCS_DOC[("Bucket: comprobantes-pago
        Acceso privado
        URL firmadas (signed URLs)")]
    end

    %% â”€â”€ Secret Manager â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€
    subgraph SM_BOX["ðŸ”‘ Secret Manager â€” ~$0.06/mes"]
        SM["JWT_SECRET Â· DB_PASSWORD
        FIREBASE_KEY Â· MAIL_PASS
        GCS_SA_KEY"]
    end

    %% â”€â”€ CI/CD â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€
    subgraph CICD["ðŸ”„ CI/CD â€” $0 (GitHub Actions gratis)"]
        direction LR
        GH["ðŸ“ GitHub\nRepositorio pÃºblico"]
        GHA["âš™ï¸ GitHub Actions\nbuild Â· test Â· push\n(2000 min/mes gratis)"]
        AR["ðŸ“¦ Artifact Registry\n0.5 GB gratis\nImÃ¡genes Docker"]
    end

    %% â”€â”€ Email â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€
    subgraph EMAIL_BOX["âœ‰ï¸ Email â€” $0 (Gmail SMTP)"]
        GMAIL["Gmail SMTP\nsmtp.gmail.com:587\nApp Password en .env\n(500 emails/dÃ­a gratis)"]
    end

    %% â”€â”€ FCM â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€
    subgraph FCM_BOX["ðŸ”” Push Notifications â€” $0 (FCM)"]
        FCM["Firebase Cloud Messaging\nWeb Push / PWA\n(completamente gratis)"]
    end

    %% â”€â”€ FLUJOS DE ACCESO â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€
    CLI -->|"HTTPS â€” URL *.run.app\n(TLS incluido, sin LB)"| FE_SVC
    OPE -->|"HTTPS + JWT Bearer"| FE_SVC
    CHO -->|"HTTPS + JWT Bearer\n+ GPS coords"| FE_SVC

    %% â”€â”€ FLUJOS FRONTEND â†” BACKEND â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€
    FE_SVC -->|"POST/GET/PATCH JSON\nAuthorization: Bearer JWT"| BE_SVC
    BE_SVC -->|"JSON response\n+ Signed URL GCS"| FE_SVC

    %% â”€â”€ FLUJOS BACKEND â†” DATOS â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€
    BE_SVC -->|"Eloquent ORM queries\n(TCP Â· VPC Connector)"| DB
    DB     -->|"Result sets"| BE_SVC
    BE_SVC -->|"Firebase Admin SDK\nwriteDocument(GPS)"| FS_GPS
    FS_GPS -->|"onSnapshot() realtime\n(WebSocket â€” gratis)"| FE_SVC

    %% â”€â”€ FLUJOS ALMACENAMIENTO â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€
    BE_SVC   -->|"PUT multipart/form-data"| GCS_DOC
    GCS_DOC  -->|"Signed URL (15min TTL)"| BE_SVC
    BE_SVC   -->|"URL pÃºblica guardada en MySQL"| GCS_IMG
    GCS_IMG  -->|"GET imagen\nCache-Control: 4h"| FE_SVC

    %% â”€â”€ FLUJOS SECRETOS â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€
    SM -->|"env vars en startup\n(gratis hasta 6 secretos)"| BE_SVC

    %% â”€â”€ FLUJOS CI/CD â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€
    GH  -->|"push main\nworkflow trigger"| GHA
    GHA -->|"docker push"| AR
    AR  -->|"deploy --image\ngcloud run deploy"| FE_SVC
    AR  -->|"deploy --image\ngcloud run deploy"| BE_SVC

    %% â”€â”€ FLUJOS EMAIL Y PUSH â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€
    BE_SVC -->|"Laravel Mail\nSMTP TLS"| GMAIL
    GMAIL  -->|"Email entregado"| CLI
    BE_SVC -->|"FCM HTTP v1 API"| FCM
    FCM    -->|"Web Push\nService Worker"| CLI
```

---

#### Nivel 2 â€” Flujo por Proceso de Negocio (bajo costo)

```mermaid
flowchart LR
    subgraph P1["ðŸ›’ Checkout"]
        direction TB
        A1["Cliente â†’ FE\n(Cloud Run URL HTTPS)"]
        A2["FE â†’ BE API\n(JWT en header)"]
        A3["BE valida JWT\n(sin servicio externo)"]
        A4["Comprobante â†’ GCS\n(bucket docs privado)"]
        A5["Pedido â†’ Cloud SQL\n(INSERT)"]
        A6["Email â†’ Gmail SMTP\n(gratis)"]
        A1 --> A2 --> A3 --> A4 --> A5 --> A6
    end

    subgraph P2["ðŸšš Tracking GPS"]
        direction TB
        B1["Chofer abre guÃ­a\n(Cloud Run URL)"]
        B2["PWA activa GPS\n(Geolocation API)"]
        B3["Coords â†’ Firestore\n(gratis hasta 50K/dÃ­a)"]
        B4["onSnapshot() listener"]
        B5["Cliente ve mapa\nen tiempo real"]
        B6["FCM Push\n(pedido listo â€” gratis)"]
        B1 --> B2 --> B3 --> B4 --> B5 --> B6
    end

    subgraph P3["ðŸ“¦ ImÃ¡genes â€” CachÃ© sin costo"]
        direction TB
        C1["Admin sube imagen"]
        C2["Backend â†’ PUT GCS\n(bucket pÃºblico)"]
        C3["URL en MySQL"]
        C4["Cliente pide imagen"]
        C5["Â¿Cache navegador?"]
        C6["HIT â†’ 0 costo GCS\n(servido localmente)"]
        C7["MISS â†’ GCS fetch\nâ†’ Cache 4h gratis"]
        C1 --> C2 --> C3 --> C4 --> C5
        C5 -->|"HIT"| C6
        C5 -->|"MISS"| C7
    end

    subgraph P4["ðŸ”„ Deploy â€” GitHub Actions"]
        direction TB
        D1["git push main\n(GitHub gratis)"]
        D2["GitHub Actions\n(2000 min/mes gratis)"]
        D3["docker build + test\nphp artisan test"]
        D4["docker push\nArtifact Registry"]
        D5["gcloud run deploy\n(Cloud Run revision)"]
        D1 --> D2 --> D3 --> D4 --> D5
    end
```

---

#### Nivel 3 â€” Tabla de Costos Estimados

| Servicio GCP | Uso estimado | Capa gratuita | Costo/mes |
|---|---|---|---|
| **Cloud Run** (frontend + backend) | ~500K requests/mes | 2M requests gratis | **$0** |
| **Cloud SQL MySQL** `db-f1-micro` | Siempre activo | Sin capa gratuita | **~$8â€“10** |
| **Firestore** | GPS realtime, <1 GB | 1 GB + 50K reads/dÃ­a gratis | **$0** |
| **Cloud Storage** | ~5 GB imÃ¡genes + docs | 5 GB gratis (primeros 90 dÃ­as) | **~$0.10** |
| **Secret Manager** | 5â€“6 secretos | 6 secretas gratis/mes | **$0** |
| **Artifact Registry** | 2 imÃ¡genes Docker | 0.5 GB gratis | **$0** |
| **GitHub Actions** | CI/CD build + deploy | 2000 min/mes gratis (repo pÃºblico) | **$0** |
| **Gmail SMTP** | Notificaciones email | 500 emails/dÃ­a gratis | **$0** |
| **FCM** | Push notifications PWA | Completamente gratis | **$0** |
| **HTTPS / TLS** | Incluido en Cloud Run URL | Incluido siempre | **$0** |
| | | **Total estimado** | **~$8â€“10/mes** |

> [!TIP]
> **OptimizaciÃ³n adicional:** Configura Cloud SQL con **"Stop instance on schedule"** fuera de horario laboral (ej. 22hâ€“6h) para reducir el costo de Cloud SQL hasta un **~60%**, llevando el total a **~$3â€“5/mes**.

#### Nivel 4 â€” Flujo de Seguridad con Cloud Run URL (sin servicios pagos)

```mermaid
flowchart LR
    subgraph Usuario["ðŸ‘¤ Usuario (cualquier rol)"]
        BROWSER["Navegador / PWA"]
    end

    subgraph CR_SEC["ðŸ”’ Seguridad incluida en Cloud Run (gratis)"]
        direction TB
        TLS["âœ… TLS 1.3 HTTPS\nincluido en *.run.app\n(sin Load Balancer)"]
        JWT_V["âœ… JWT Validation\nMiddleware Laravel\n(sin servicio externo)"]
        VAL["âœ… ValidaciÃ³n inputs\nanti-XSS Â· anti-SQLi\nLaravel FormRequest"]
        HASH["âœ… bcrypt password hash\nLaravel Hash facade"]
        CORS["âœ… CORS configurado\nen Laravel (solo dominios propios)"]
    end

    subgraph DATA["ðŸ’¾ Datos protegidos"]
        SM2["ðŸ”‘ Secret Manager\n(JWT_SECRET Â· DB_PASS)\n~$0.06/mes"]
        DB2[("ðŸ¬ Cloud SQL\nVPC privado\n(sin IP pÃºblica)")]
    end

    BROWSER -->|"HTTPS *.run.app"| TLS
    TLS --> JWT_V
    JWT_V --> VAL
    VAL --> HASH
    HASH --> CORS
    CORS -->|"Solo si JWT vÃ¡lido\ny rol autorizado"| DB2
    SM2 -->|"Secretos en env vars\nal arrancar contenedor"| JWT_V
```