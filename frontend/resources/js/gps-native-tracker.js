/**
 * gps-native-tracker.js
 * Ponytail — GPS Tracker Híbrido con Soporte de Segundo Plano
 *
 * Arquitectura de capas:
 *  1. Capacitor nativo (Android/iOS): BackgroundGeolocation plugin
 *     → Sigue rastreando con pantalla apagada / app minimizada
 *  2. Browser fallback (desktop / web puro): gps-tracker.js original
 *     → setInterval + navigator.geolocation (solo funciona en primer plano)
 *
 * El módulo detecta el entorno en runtime y elige la capa correcta.
 * La API pública (startTracking / stopTracking) es idéntica en ambas capas,
 * por lo que las vistas no necesitan saber cuál se usa.
 *
 * Flujo de autenticación:
 *  - Lee el JWT de localStorage['auth_token'] (guardado en AuthController.login)
 *  - Lo incluye como "Authorization: Bearer <token>" en cada POST al backend
 *  - Si no hay token: las coordenadas se escriben solo en Firestore (JS SDK, sin backend)
 *
 * Cola offline:
 *  - Si el POST al backend falla por red → guarda el punto en localStorage
 *  - Al recuperar la conexión (evento 'online') → flush automático de la cola
 */

import {
    startTracking as startTrackingBrowser,
    stopTracking as stopTrackingBrowser,
    saveEventCheckpointLocation as saveCheckpointBrowser,
} from './gps-tracker.js';

// ─── Constantes de Entorno ────────────────────────────────────────────────────
const LOCATION_REFRESH_MINUTES = parseFloat(
    import.meta.env.VITE_LOCATION_REFRESH_MINUTES || '1'
);
const API_BASE_URL = (import.meta.env.VITE_API_BASE_URL || '').replace(/\/$/, '');
const GPS_ENDPOINT = `${API_BASE_URL}/api/gps/ubicacion`;

// ─── Detección de Entorno Capacitor ──────────────────────────────────────────
/**
 * Retorna true solo cuando el código se ejecuta dentro de un shell nativo
 * de Capacitor (APK de Android o IPA de iOS).
 * En un navegador web normal (Chrome, Safari) siempre retorna false.
 */
const isCapacitorNative = () =>
    typeof window !== 'undefined' &&
    typeof window.Capacitor !== 'undefined' &&
    window.Capacitor.isNativePlatform();

// ─── Token JWT ────────────────────────────────────────────────────────────────
/**
 * Lee el JWT del storage. La clave 'auth_token' es la que graba
 * el AuthController de Laravel al responder el login exitoso.
 */
const getAuthToken = () =>
    localStorage.getItem('auth_token') ||
    sessionStorage.getItem('auth_token') ||
    null;

// ─── Comunicación con Backend Laravel ────────────────────────────────────────
/**
 * Envía una coordenada al endpoint POST /api/gps/ubicacion con JWT.
 * Maneja los campos que acepta GpsRequest:
 *   camion_id, guia_ruta_id, latitud, longitud, estado
 *
 * @param {number} camionId
 * @param {number} guiaRutaId
 * @param {number} lat
 * @param {number} lng
 * @param {string} estado  — 'en_movimiento' | 'detenido' | 'entregando'
 * @returns {Promise<boolean>} — true si el servidor devolvió 2xx
 */
const postLocationToBackend = async (camionId, guiaRutaId, lat, lng, estado = 'en_movimiento') => {
    const token = getAuthToken();
    if (!token) {
        console.warn('[GPS Native] Sin token JWT. Coordenada solo en Firestore JS.');
        return false;
    }

    if (!API_BASE_URL) {
        console.warn('[GPS Native] VITE_API_BASE_URL no definido. Omitiendo POST al backend.');
        return false;
    }

    try {
        const response = await fetch(GPS_ENDPOINT, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'Authorization': `Bearer ${token}`,
                'X-Requested-With': 'XMLHttpRequest',  // Compatibilidad Sanctum
            },
            body: JSON.stringify({
                camion_id: camionId,
                guia_ruta_id: guiaRutaId,
                latitud: parseFloat(lat.toFixed(7)),
                longitud: parseFloat(lng.toFixed(7)),
                estado: estado,
            }),
        });

        if (!response.ok) {
            const errorText = await response.text();
            console.error(`[GPS Native] Backend rechazó ubicación (HTTP ${response.status}):`, errorText);
            // 401 = token expirado → no encolar, pedir re-login
            if (response.status === 401) {
                console.error('[GPS Native] Token expirado. El chofer debe re-autenticarse.');
            }
            return false;
        }

        return true;

    } catch (networkErr) {
        console.warn('[GPS Native] Sin conectividad. Ubicación encolada localmente:', networkErr.message);
        enqueueOfflineLocation(camionId, guiaRutaId, lat, lng, estado);
        return false;
    }
};

// ─── Cola Offline ─────────────────────────────────────────────────────────────
const OFFLINE_QUEUE_KEY = 'gps_offline_queue';
const OFFLINE_QUEUE_MAX = 200; // ≈ 3.3h a 1 punto/min

const enqueueOfflineLocation = (camionId, guiaRutaId, lat, lng, estado) => {
    try {
        const queue = JSON.parse(localStorage.getItem(OFFLINE_QUEUE_KEY) || '[]');
        queue.push({
            camionId, guiaRutaId, lat, lng, estado,
            timestamp: new Date().toISOString(),
        });
        if (queue.length > OFFLINE_QUEUE_MAX) queue.shift(); // FIFO
        localStorage.setItem(OFFLINE_QUEUE_KEY, JSON.stringify(queue));
    } catch (e) {
        console.warn('[GPS Native] No se pudo encolar localmente:', e.message);
    }
};

const flushOfflineQueue = async () => {
    try {
        const queue = JSON.parse(localStorage.getItem(OFFLINE_QUEUE_KEY) || '[]');
        if (queue.length === 0) return;

        console.log(`[GPS Native] 📡 Reconectado. Enviando ${queue.length} ubicaciones offline...`);
        const failed = [];

        for (const point of queue) {
            const ok = await postLocationToBackend(
                point.camionId, point.guiaRutaId,
                point.lat, point.lng, point.estado
            );
            if (!ok) {
                failed.push(point); // Reencolar solo los que aún fallan
            }
        }

        localStorage.setItem(OFFLINE_QUEUE_KEY, JSON.stringify(failed));
        console.log(`[GPS Native] Flush: ${queue.length - failed.length} enviados, ${failed.length} pendientes.`);
    } catch (e) {
        console.warn('[GPS Native] Error en flush de cola offline:', e.message);
    }
};

// Flush automático al recuperar conexión
if (typeof window !== 'undefined') {
    window.addEventListener('online', flushOfflineQueue);
}

// ─── Estado del Tracker ───────────────────────────────────────────────────────
let nativeWatcher = null;
let activeContext = null; // { camionId, guiaRutaId }

// ─── Modo Nativo: Capacitor BackgroundGeolocation ─────────────────────────────
/**
 * Inicia el rastreo usando el plugin nativo de Capacitor.
 * Se ejecuta en segundo plano incluso con pantalla apagada.
 *
 * @param {number} camionId
 * @param {number} guiaRutaId
 */
const startNativeTracking = async (camionId, guiaRutaId) => {
    try {
        // Import dinámico: solo disponible en contexto Capacitor (no falla en browser)
        const { BackgroundGeolocation } = await import('@capacitor-community/background-geolocation');

        // ── Gestión de Permisos ──────────────────────────────────────────────
        const permStatus = await BackgroundGeolocation.checkPermissions();
        if (permStatus.location !== 'granted') {
            const reqResult = await BackgroundGeolocation.requestPermissions();
            if (reqResult.location !== 'granted') {
                console.error('[GPS Native] Permisos de ubicación denegados por el usuario.');
                return false;
            }
        }

        // ── Limpiar watcher previo ────────────────────────────────────────────
        if (nativeWatcher) {
            await nativeWatcher.remove();
            nativeWatcher = null;
        }

        // ── Registrar watcher de segundo plano ────────────────────────────────
        nativeWatcher = await BackgroundGeolocation.addWatcher(
            {
                // Mensaje en la barra de notificaciones de Android (obligatorio)
                backgroundMessage: 'Fritolay está rastreando tu ruta de entrega.',
                backgroundTitle: '📍 GPS Activo — Fritolay',
                requestPermissions: false,  // Ya gestionados arriba
                stale: false,               // No usar caché antigua
                distanceFilter: 15,         // Capturar si se movió ≥ 15 metros
            },
            async (location, error) => {
                if (error) {
                    if (error.code === 'NOT_AUTHORIZED') {
                        // El usuario revocó el permiso en Ajustes → detener
                        console.error('[GPS Native] Permiso de background revocado. Deteniendo rastreo nativo.');
                        await stopNativeTracking();
                        // Intentar fallback a browser tracker
                        startTrackingBrowser(camionId, LOCATION_REFRESH_MINUTES);
                    } else {
                        console.warn('[GPS Native] Error GPS nativo:', error.message);
                    }
                    return;
                }

                if (!location) return;

                const { latitude: lat, longitude: lng, accuracy } = location;
                console.log(
                    `[GPS Native] 📍 lat=${lat.toFixed(5)}, lng=${lng.toFixed(5)}, acc=${accuracy?.toFixed(0) ?? '?'}m`
                );

                // Determinar estado según precisión (heurística simple)
                const gpsEstado = (accuracy && accuracy > 50) ? 'detenido' : 'en_movimiento';

                // Enviar al backend Laravel con JWT
                await postLocationToBackend(camionId, guiaRutaId, lat, lng, gpsEstado);
            }
        );

        activeContext = { camionId, guiaRutaId };
        console.log(`[GPS Native] ✅ Rastreo nativo iniciado — Camión #${camionId} | Guía #${guiaRutaId}`);
        return true;

    } catch (err) {
        console.error('[GPS Native] Error al iniciar BackgroundGeolocation nativo:', err?.message ?? err);
        return false;
    }
};

const stopNativeTracking = async () => {
    if (nativeWatcher) {
        try {
            const { BackgroundGeolocation } = await import('@capacitor-community/background-geolocation');
            await nativeWatcher.remove();
        } catch (e) {
            console.warn('[GPS Native] Error al remover watcher:', e?.message);
        } finally {
            nativeWatcher = null;
            activeContext = null;
            console.log('[GPS Native] Rastreo nativo detenido.');
        }
    }
};

// ─── API Pública ──────────────────────────────────────────────────────────────

/**
 * Inicia el rastreo GPS del chofer.
 *
 * Selecciona automáticamente la capa correcta:
 *  - Capacitor nativo → segundo plano real (Android/iOS)
 *  - Browser fallback → primer plano (desktop / test)
 *
 * @param {number} camionId    — ID del camión asignado al chofer
 * @param {number} [guiaRutaId=0] — ID de la guía de ruta activa (requerido por GpsRequest)
 */
export const startTracking = async (camionId, guiaRutaId = 0) => {
    if (!camionId) {
        console.warn('[GPS Native] camionId no provisto. Rastreo cancelado.');
        return;
    }

    // Flush previo de puntos offline antes de arrancar
    await flushOfflineQueue();

    if (isCapacitorNative()) {
        console.log('[GPS Native] Entorno Capacitor detectado → plugin nativo.');
        const nativeOk = await startNativeTracking(camionId, guiaRutaId);
        if (!nativeOk) {
            console.warn('[GPS Native] Plugin nativo falló → fallback a browser tracker.');
            startTrackingBrowser(camionId, LOCATION_REFRESH_MINUTES);
        }
    } else {
        console.log('[GPS Native] Entorno browser → gps-tracker.js estándar.');
        startTrackingBrowser(camionId, LOCATION_REFRESH_MINUTES);
    }
};

/**
 * Detiene el rastreo GPS (nativo + browser).
 */
export const stopTracking = async () => {
    await stopNativeTracking();
    stopTrackingBrowser();
    console.log('[GPS Native] Todos los trackers detenidos.');
};

/**
 * Guarda un checkpoint puntual e inmediato (evento de estado del chofer).
 * Siempre usa navigator.geolocation para captura instantánea.
 * También intenta sincronizar con el backend si hay token.
 *
 * @param {number} camionId
 * @param {string} estadoOperativo — Ej: 'En Camino', 'Entregando', 'Entregado'
 */
export const saveEventCheckpointLocation = async (camionId, estadoOperativo) => {
    // Delegar captura + escritura en Firestore al tracker browser original
    const ok = await saveCheckpointBrowser(camionId, estadoOperativo);

    // Adicionalmente notificar al backend Laravel si tenemos contexto
    if (ok && activeContext?.guiaRutaId) {
        const gpsEstado = estadoOperativo.toLowerCase().includes('entregando')
            ? 'entregando'
            : 'en_movimiento';

        if (navigator.geolocation) {
            navigator.geolocation.getCurrentPosition(
                async (pos) => {
                    await postLocationToBackend(
                        camionId,
                        activeContext.guiaRutaId,
                        pos.coords.latitude,
                        pos.coords.longitude,
                        gpsEstado
                    );
                },
                (err) => console.warn('[GPS Native] Checkpoint: error GPS:', err.message),
                { enableHighAccuracy: true, timeout: 10000, maximumAge: 0 }
            );
        }
    }
    return ok;
};

// ─── Exposición Global (compatibilidad con llamadas window.startTracking) ─────
// Las vistas Blade llaman window.startTracking(camionId) directamente.
// Este módulo reemplaza esa función con la versión híbrida.
if (typeof window !== 'undefined') {
    window.startTracking = startTracking;
    window.stopTracking  = stopTracking;
    window.saveEventCheckpointLocation = saveEventCheckpointLocation;
}
