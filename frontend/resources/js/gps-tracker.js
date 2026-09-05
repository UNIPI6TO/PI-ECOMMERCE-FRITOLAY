import { db, doc, getDoc, setDoc, updateDoc, arrayUnion } from './firebase-config.js';

// Variables de Entorno (Environment) con fallback sensato de Ponytail
const LOCATION_REFRESH_MINUTES = parseFloat(import.meta.env.VITE_LOCATION_REFRESH_MINUTES || '5');
const ENABLE_PROXIMITY_FILTER = (import.meta.env.VITE_ENABLE_PROXIMITY_FILTER ?? 'true') === 'true';
const LOCATION_MIN_DISTANCE_METERS = parseFloat(import.meta.env.VITE_LOCATION_MIN_DISTANCE_METERS || '20');
const MAX_HISTORIC_LOCATIONS = parseInt(import.meta.env.VITE_MAX_HISTORIC_LOCATIONS || '96', 10);

let trackingTimer = null;
let lastKnownLocation = null;

/**
 * Fórmula de Haversine para calcular distancia ortodrómica en metros entre dos coordenadas.
 */
export const calculateHaversineDistanceMeters = (lat1, lon1, lat2, lon2) => {
    const R = 6371000; // Radio de la Tierra en metros
    const dLat = (lat2 - lat1) * (Math.PI / 180);
    const dLon = (lon2 - lon1) * (Math.PI / 180);
    const a =
        Math.sin(dLat / 2) * Math.sin(dLat / 2) +
        Math.cos(lat1 * (Math.PI / 180)) *
        Math.cos(lat2 * (Math.PI / 180)) *
        Math.sin(dLon / 2) * Math.sin(dLon / 2);
    const c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1 - a));
    return R * c;
};

/**
 * Procesa la coordenada actual y la guarda en Firestore si supera la distancia mínima (cuando el filtro está activo).
 */
const processAndSaveLocation = async (camionId, position) => {
    const lat = position.coords.latitude;
    const lng = position.coords.longitude;
    const nowIso = new Date().toISOString();

    // Lógica de Optimización por Distancia Haversine (condicionada por la bandera de entorno ENABLE_PROXIMITY_FILTER)
    if (ENABLE_PROXIMITY_FILTER && lastKnownLocation) {
        const distanceMeters = calculateHaversineDistanceMeters(
            lastKnownLocation.lat,
            lastKnownLocation.lng,
            lat,
            lng
        );

        if (distanceMeters < LOCATION_MIN_DISTANCE_METERS) {
            console.log(`[GPS Tracker] Filtro de proximidad ACTIVO: Distancia recorrida (${distanceMeters.toFixed(1)}m) menor al umbral (${LOCATION_MIN_DISTANCE_METERS}m). Omitiendo escritura.`);
            return;
        }
    } else if (!ENABLE_PROXIMITY_FILTER) {
        console.log(`[GPS Tracker] Filtro de proximidad INACTIVO: Guardando posición únicamente por frecuencia temporal (${LOCATION_REFRESH_MINUTES} min).`);
    }

    const docId = `camion_${camionId}`;
    const docRef = doc(db, 'ubicaciones_camion', docId);

    const newPoint = {
        lat: Number(lat.toFixed(7)),
        lng: Number(lng.toFixed(7)),
        timestamp: nowIso
    };

    try {
        const payload = {
            camionId: Number(camionId),
            ultima_ubicacion: newPoint,
            ultima_actualizacion: nowIso,
            historial: arrayUnion(newPoint)
        };

        await setDoc(docRef, payload, { merge: true });

        // Actualizar referencia local
        lastKnownLocation = { lat, lng, timestamp: nowIso };
        console.log(`[GPS Tracker] Ubicación guardada exitosamente en Firestore para camion_${camionId}:`, newPoint);

    } catch (err) {
        console.warn(`[GPS Tracker] Transmisión en directo en pausa. Guardando respaldo local:`, err.message);
        // Fallback local en sessionStorage para evitar pérdida de datos si la red o credencial falla
        const localKey = `gps_camion_${camionId}`;
        const prevLocal = JSON.parse(sessionStorage.getItem(localKey) || '[]');
        prevLocal.push(newPoint);
        if (prevLocal.length > MAX_HISTORIC_LOCATIONS) {
            prevLocal.shift();
        }
        sessionStorage.setItem(localKey, JSON.stringify(prevLocal));
        lastKnownLocation = { lat, lng, timestamp: nowIso };
    }
};

/**
 * Inicia la captura de ubicación para el chofer autenticado.
 */
export const startTracking = async (camionId, intervalMinutes = LOCATION_REFRESH_MINUTES) => {
    if (!navigator.geolocation) {
        console.error('La geolocalización no está soportada por el navegador.');
        return;
    }

    if (!camionId) {
        console.warn('ID de camión no provisto. No se iniciará el rastreo.');
        return;
    }

    if (trackingTimer) {
        clearInterval(trackingTimer);
    }

    console.log(`[GPS Tracker] Iniciando rastreo automático para Camión #${camionId}. Intervalo: ${intervalMinutes} min. Filtro de Proximidad: ${ENABLE_PROXIMITY_FILTER ? 'ACTIVADO (' + LOCATION_MIN_DISTANCE_METERS + 'm)' : 'DESACTIVADO'}. Max historial: ${MAX_HISTORIC_LOCATIONS}`);

    const captureLocation = () => {
        navigator.geolocation.getCurrentPosition(
            (pos) => processAndSaveLocation(camionId, pos),
            (err) => console.error('[GPS Tracker] Error de permisos o lectura GPS:', err.message),
            { enableHighAccuracy: true, timeout: 15000, maximumAge: 0 }
        );
    };

    // Captura inicial inmediata
    captureLocation();

    // Intervalo periódico
    const intervalMs = Math.max(1, intervalMinutes) * 60 * 1000;
    trackingTimer = setInterval(captureLocation, intervalMs);
};

/**
 * Detiene el rastreo activo.
 */
export const stopTracking = () => {
    if (trackingTimer) {
        clearInterval(trackingTimer);
        trackingTimer = null;
        lastKnownLocation = null;
        console.log('[GPS Tracker] Rastreo detenido.');
    }
};
