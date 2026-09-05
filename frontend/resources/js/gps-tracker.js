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
/**
 * Procesa la coordenada actual y la guarda en Firestore si supera la distancia mínima (cuando el filtro está activo).
 */
/**
 * Procesa la coordenada actual y la guarda en Firestore si supera la distancia mínima (cuando el filtro está activo).
 */
/**
 * Procesa la coordenada actual y la guarda en la estructura particionada por día:
 * - Subcolección diaria: camiones/{idCamion}/historial/{YYYY-MM-DD}
 * - Documento global de telemetría: ubicaciones_camion/camion_{idCamion}
 */
const processAndSaveLocation = async (camionId, position, estado = 'En Ruta', ignoreProximity = false) => {
    const lat = position.coords.latitude;
    const lng = position.coords.longitude;
    const now = new Date();
    const nowIso = now.toISOString();
    const todayStr = nowIso.split('T')[0]; // Formato YYYY-MM-DD

    // Lógica de Optimización por Distancia Haversine (omitida si ignoreProximity es true por ser un evento forzado)
    if (!ignoreProximity && ENABLE_PROXIMITY_FILTER && lastKnownLocation) {
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
    }

    const newPoint = {
        lat: Number(lat.toFixed(7)),
        lng: Number(lng.toFixed(7)),
        timestamp: nowIso,
        estado: estado
    };

    // Referencias a los documentos de Firestore
    // 1. Documento de partición diaria: camiones/{idCamion}/historial/{YYYY-MM-DD}
    const dailyDocRef = doc(db, 'camiones', String(camionId), 'historial', todayStr);
    
    // 2. Documento global para estado de telemetría en vivo: ubicaciones_camion/camion_{idCamion}
    const globalDocRef = doc(db, 'ubicaciones_camion', `camion_${camionId}`);

    try {
        // Ejecución de la ventana deslizante FIFO a nivel de día específico
        const dailySnap = await getDoc(dailyDocRef);
        let dailyPoints = [];

        if (dailySnap.exists() && Array.isArray(dailySnap.data().puntos)) {
            dailyPoints = dailySnap.data().puntos;
        }

        dailyPoints.push(newPoint);

        // Ventana deslizante FIFO por día (si supera MAX_HISTORIC_LOCATIONS elimina la ubicación más antigua del día)
        if (dailyPoints.length > MAX_HISTORIC_LOCATIONS) {
            dailyPoints.shift();
        }

        // 1. Guardar en el documento particionado del día
        await setDoc(dailyDocRef, {
            fecha: todayStr,
            camionId: Number(camionId),
            ultima_actualizacion: nowIso,
            puntos: dailyPoints
        }, { merge: true });

        // 2. Guardar en el documento global de telemetría en vivo
        await setDoc(globalDocRef, {
            camionId: Number(camionId),
            ultima_ubicacion: newPoint,
            ultima_actualizacion: nowIso,
            historial: arrayUnion(newPoint)
        }, { merge: true });

        // Actualizar referencia local
        lastKnownLocation = { lat, lng, timestamp: nowIso, estado: estado };
        console.log(`[GPS Tracker] Ubicación (${estado}) guardada en partición diaria [${todayStr}] para Camión #${camionId}:`, newPoint);

    } catch (err) {
        console.warn(`[GPS Tracker] Transmisión en directo en pausa. Guardando respaldo local (${estado}):`, err.message);
        const localKey = `gps_camion_${camionId}_${todayStr}`;
        const prevLocal = JSON.parse(sessionStorage.getItem(localKey) || '[]');
        prevLocal.push(newPoint);
        if (prevLocal.length > MAX_HISTORIC_LOCATIONS) {
            prevLocal.shift();
        }
        sessionStorage.setItem(localKey, JSON.stringify(prevLocal));
        lastKnownLocation = { lat, lng, timestamp: nowIso, estado: estado };
    }
};

/**
 * Guarda un punto de control forzado e inmediato con el estado operativo del chofer ('En Camino' o 'Entregando').
 */
export const saveEventCheckpointLocation = async (camionId, estadoOperativo) => {
    if (!navigator.geolocation || !camionId) return;

    return new Promise((resolve) => {
        navigator.geolocation.getCurrentPosition(
            async (pos) => {
                await processAndSaveLocation(camionId, pos, estadoOperativo, true);
                resolve(true);
            },
            (err) => {
                console.error(`[GPS Tracker] Error al capturar evento (${estadoOperativo}):`, err.message);
                resolve(false);
            },
            { enableHighAccuracy: true, timeout: 15000, maximumAge: 0 }
        );
    });
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

    // Inicializar lastKnownLocation desde la partición diaria o global si aún no se ha cargado
    if (!lastKnownLocation) {
        try {
            const todayStr = new Date().toISOString().split('T')[0];
            const dailyDocRef = doc(db, 'camiones', String(camionId), 'historial', todayStr);
            const dailySnap = await getDoc(dailyDocRef);

            if (dailySnap.exists() && Array.isArray(dailySnap.data().puntos) && dailySnap.data().puntos.length > 0) {
                const pts = dailySnap.data().puntos;
                lastKnownLocation = pts[pts.length - 1];
            } else {
                const globalDocRef = doc(db, 'ubicaciones_camion', `camion_${camionId}`);
                const globalSnap = await getDoc(globalDocRef);
                if (globalSnap.exists() && globalSnap.data().ultima_ubicacion) {
                    lastKnownLocation = globalSnap.data().ultima_ubicacion;
                }
            }
            if (lastKnownLocation) {
                console.log(`[GPS Tracker] Referencia previa cargada para Camión #${camionId}:`, lastKnownLocation);
            }
        } catch (e) {
            console.warn('[GPS Tracker] No se pudo obtener referencia previa de Firestore:', e.message);
        }
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
