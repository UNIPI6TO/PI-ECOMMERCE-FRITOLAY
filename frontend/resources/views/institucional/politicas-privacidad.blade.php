@extends('layouts.app')

@section('title', 'Políticas de Privacidad - Fritolay Ambato')

@section('content')
<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-10">
    <!-- Encabezado -->
    <div class="mb-8 border-b border-gray-200 pb-6">
        <div class="flex items-center gap-3 mb-2">
            <span class="bg-red-50 text-[#E3001B] font-extrabold text-xs px-3 py-1 rounded-full uppercase tracking-wider border border-red-100">
                Documento Legal y de Protección de Datos
            </span>
            <span class="text-xs font-bold text-gray-400">Última actualización: Septiembre 2026</span>
        </div>
        <h1 class="text-3xl sm:text-4xl font-black text-slate-900 tracking-tight">Políticas de Privacidad y Tratamiento de Datos Personales</h1>
        <p class="text-xs sm:text-sm font-semibold text-gray-500 mt-2 leading-relaxed">
            Conforme a la Ley Orgánica de Protección de Datos Personales de la República del Ecuador y normativas aplicables de comercio electrónico y logística.
        </p>
    </div>

    <!-- Contenido Legal -->
    <div class="bg-white rounded-3xl p-6 sm:p-10 border border-gray-100 shadow-2xs space-y-8 text-xs sm:text-sm font-medium text-gray-700 leading-relaxed">
        
        <!-- Sección 1 -->
        <section>
            <h2 class="text-base sm:text-lg font-black text-slate-900 mb-3 flex items-center gap-2">
                <span class="w-6 h-6 rounded-lg bg-slate-900 text-white text-xs flex items-center justify-center font-bold">1</span>
                Recopilación de Datos Personales y Geolocalización
            </h2>
            <p class="mb-3">
                El sistema de distribución y e-commerce de <strong>Fritolay Ambato</strong> recopila información estrictamente necesaria para garantizar la prestación transparente y eficiente de nuestros servicios de venta y despacho. Los datos capturados incluyen:
            </p>
            <ul class="list-disc pl-5 space-y-1.5 text-gray-600 font-normal">
                <li><strong>Identificación Comercial:</strong> Nombres completos, RUC o Cédula de Identidad, Razón Social del negocio y número telefónico de contacto.</li>
                <li><strong>Ubicación y Geolocalización:</strong> Coordenadas geográficas exactas (Latitud y Longitud) de los locales comerciales mediante marcadores (pines) en mapas interactivos Leaflet, necesarias para la planificación de rutas de despacho.</li>
                <li><strong>Historial Transaccional:</strong> Registro detallado de pedidos solicitados, montos facturados, devoluciones parciales/totales, formas de pago y comprobantes adjuntos.</li>
                <li><strong>Monitoreo Logístico:</strong> Coordenadas GPS en tiempo real transmitidas durante el horario operativo por la aplicación del Chofer/Conductor asignado al vehículo de reparto.</li>
            </ul>
        </section>

        <!-- Sección 2 -->
        <section class="pt-6 border-t border-gray-100">
            <h2 class="text-base sm:text-lg font-black text-slate-900 mb-3 flex items-center gap-2">
                <span class="w-6 h-6 rounded-lg bg-slate-900 text-white text-xs flex items-center justify-center font-bold">2</span>
                Uso y Finalidad de la Información
            </h2>
            <p class="mb-3">
                Toda la información recolectada es procesada exclusivamente con los siguientes fines operativos y legales:
            </p>
            <ol class="list-decimal pl-5 space-y-1.5 text-gray-600 font-normal">
                <li>Procesar, aprobar, despachar y entregar órdenes comerciales de productos Fritolay.</li>
                <li>Optimizar las secuencias de viaje y rutas mediante algoritmos de ordenamiento espacial (Greedy TSP) para reducir tiempos de entrega.</li>
                <li>Realizar el arqueo de caja, verificación de validez de notas de crédito y consolidación de ingresos monetarios por conductor y por vehículo.</li>
                <li>Alimentar el Dashboard Gerencial para analítica de ventas, efectividad de entregas y detección de patrones de pérdidas por carritos abandonados o cancelaciones.</li>
            </ol>
        </section>

        <!-- Sección 3 -->
        <section class="pt-6 border-t border-gray-100">
            <h2 class="text-base sm:text-lg font-black text-slate-900 mb-3 flex items-center gap-2">
                <span class="w-6 h-6 rounded-lg bg-slate-900 text-white text-xs flex items-center justify-center font-bold">3</span>
                Uso de Cookies, Almacenamiento Local y Sesiones
            </h2>
            <p class="mb-3">
                Nuestra plataforma utiliza mecanismos de almacenamiento técnico en el navegador cliente para asegurar el correcto funcionamiento del sistema:
            </p>
            <ul class="list-disc pl-5 space-y-1.5 text-gray-600 font-normal">
                <li><strong>Cookies de Autenticación JWT (`jwt_token`):</strong> Se emplean cookies seguras y tokens en LocalStorage para validar la identidad y el rol activo del usuario (Administrador, Operador, Chofer o Cliente) de forma apátrida (Stateless REST API).</li>
                <li><strong>Cookie de Carrito de Compras (`fritolay_cart`):</strong> Almacena de forma temporal y encriptada los ítems seleccionados por el cliente antes de confirmar su orden, permitiendo recuperar el estado del pedido.</li>
                <li><strong>Persistencia de Preferencias:</strong> Almacenamiento de filtros de fecha y estado en `sessionStorage` para optimizar la experiencia en pantallas como Dashboard y Cierre de Guías.</li>
            </ul>
        </section>

        <!-- Sección 4 -->
        <section class="pt-6 border-t border-gray-100">
            <h2 class="text-base sm:text-lg font-black text-slate-900 mb-3 flex items-center gap-2">
                <span class="w-6 h-6 rounded-lg bg-slate-900 text-white text-xs flex items-center justify-center font-bold">4</span>
                Seguridad, Terceros y No Comercialización
            </h2>
            <p class="mb-3">
                <strong>Fritolay Ambato garantiza en forma categórica que NO vende, alquila, cede ni comercializa información personal o comercial de sus clientes a ningún tercero bajo ninguna circunstancia.</strong>
            </p>
            <p class="text-gray-600 font-normal leading-relaxed">
                La información solo podrá ser compartida con autoridades regulatorias o tributarias (Servicio de Rentas Internas - SRI) en cumplimiento de obligaciones fiscales aplicables a la facturación electrónica. Todos los canales de comunicación emplean protocolos de transmisión cifrada HTTPS/SSL y la base de datos cuenta con controles de acceso por roles estrictos (RBAC) e historial de auditoría permanente.
            </p>
        </section>

        <!-- Sección 5 -->
        <section class="pt-6 border-t border-gray-100">
            <h2 class="text-base sm:text-lg font-black text-slate-900 mb-3 flex items-center gap-2">
                <span class="w-6 h-6 rounded-lg bg-slate-900 text-white text-xs flex items-center justify-center font-bold">5</span>
                Derechos del Titular de los Datos (ARCO)
            </h2>
            <p class="mb-3">
                En concordancia con la legislación de protección de datos, los usuarios y representantes comerciales pueden ejercer en cualquier momento sus derechos de Acceso, Rectificación, Cancelación y Oposición (ARCO):
            </p>
            <ul class="list-disc pl-5 space-y-1.5 text-gray-600 font-normal">
                <li>Solicitar un reporte con todos los datos registrados a su nombre o razón social.</li>
                <li>Actualizar o rectificar datos personales o direcciones de entrega desactualizadas desde la sección <strong>Mi Perfil</strong>.</li>
                <li>Solicitar la eliminación o desactivación de su cuenta comercial mediante solicitud formal dirigida al departamento legal y de privacidad en <code>privacidad@fritolay-distribucion.ec</code>.</li>
            </ul>
        </section>

        <!-- Contacto Privacidad -->
        <div class="bg-gray-50 p-5 rounded-2xl border border-gray-200 mt-8 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
            <div>
                <p class="font-extrabold text-slate-900 text-xs uppercase tracking-wider">Oficial de Protección de Datos</p>
                <p class="text-xs text-gray-500 font-medium mt-0.5">Fritolay Ambato - Departamento de Control Interno y Legal</p>
            </div>
            <a href="mailto:privacidad@fritolay-distribucion.ec" class="bg-slate-900 hover:bg-slate-800 text-white text-xs font-extrabold px-4 py-2 rounded-xl transition-all shadow-2xs">
                Contactar Privacidad
            </a>
        </div>
    </div>
</div>
@endsection
