import './bootstrap';
import Alpine from 'alpinejs';

import { CarritoManager } from './carrito.js';
import { dateFilterParser } from './date-filter-parser.js';
import { initMap, addPin, movePinTo } from './mapa-leaflet.js';
import { generateFactura, generateNotaCredito } from './pdf-generator.js';
import { startTracking, stopTracking } from './gps-tracker.js';

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

Alpine.start();
