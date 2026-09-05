import './bootstrap';
import Alpine from 'alpinejs';

import { CarritoManager } from './carrito.js';
import { dateFilterParser } from './date-filter-parser.js';
import { initMap, addPin, movePinTo } from './mapa-leaflet.js';
import { generateFactura, generateNotaCredito } from './pdf-generator.js';
import { startTracking, stopTracking, saveEventCheckpointLocation } from './gps-tracker.js';

import { db, doc, getDoc, setDoc, collection, getDocs, query } from './firebase-config.js';

window.Alpine = Alpine;
window.CarritoManager = CarritoManager;
window.dateFilterParser = dateFilterParser;
window.initMap = initMap;
window.addPin = addPin;
window.movePinTo = movePinTo;
window.generateFactura = generateFactura;
window.generateNotaCredito = generateNotaCredito;
window.startTracking = startTracking;
window.stopTracking = stopTracking;
window.saveEventCheckpointLocation = saveEventCheckpointLocation;
window.firestoreDb = db;
window.firestoreDoc = doc;
window.firestoreGetDoc = getDoc;
window.firestoreSetDoc = setDoc;
window.firestoreCollection = collection;
window.firestoreGetDocs = getDocs;
window.VITE_LOCATION_REFRESH_MINUTES = import.meta.env.VITE_LOCATION_REFRESH_MINUTES || '5';

Alpine.start();
