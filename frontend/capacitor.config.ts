// capacitor.config.ts
// Ponytail — Estrategia A: WebView remoto apuntando al servidor Laravel
// El APK embebe la web en un WebView nativo con acceso a plugins de Capacitor.

import type { CapacitorConfig } from '@capacitor/cli';

const isProd = process.env.NODE_ENV === 'production';

const config: CapacitorConfig = {
  // ── Identidad de la App ───────────────────────────────────────────────────────
  appId: 'com.fritolay.ambato',
  appName: 'Fritolay Chofer',

  // ── WebDir: solo necesario si NO se usa server.url ───────────────────────────
  // Con Estrategia A (server.url), el contenido se sirve desde Laravel.
  // webDir apunta a un index.html mínimo de fallback (offline / splash).
  webDir: 'public',

  // ── Servidor Remoto (Estrategia A: LAN / GCP) ────────────────────────────────
  server: isProd
    ? {
        // Producción: dominio HTTPS del backend GCP (cambiar por tu dominio real)
        url: 'https://backend-api.fritolay-ambato.com',
        cleartext: false,
      }
    : {
        // Desarrollo local: IP LAN de la PC que corre Laravel
        // Ejecutar: ipconfig → buscar IPv4 Address en WiFi
        // Cambiar 192.168.1.XXX por tu IP real antes de empaquetar
        url: 'http://192.168.1.XXX:8000',
        cleartext: true, // Permite HTTP en Android (solo dev)
      },

  // ── Configuración de Plugins ──────────────────────────────────────────────────
  plugins: {
    // Plugin de Geolocalización en Segundo Plano
    BackgroundGeolocation: {
      // Android: Texto de la notificación persistente (OBLIGATORIA en Android 8+)
      notificationTitle: 'Fritolay — GPS Activo',
      notificationText: 'Rastreando tu ruta de entrega',
      // Color del ícono en la barra de estado (hex sin #)
      notificationIconColor: '#E3001B',
    },
  },

  // ── Configuración Android ─────────────────────────────────────────────────────
  android: {
    // Permite texto claro (HTTP) solo en dev. En prod se usa HTTPS.
    allowMixedContent: !isProd,
  },

  // ── Configuración iOS ─────────────────────────────────────────────────────────
  ios: {
    contentInset: 'automatic',
  },
};

export default config;
