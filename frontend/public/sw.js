const CACHE_NAME = 'fritolay-v1';
const GCS_CACHE_NAME = 'fritolay-gcs-v1';

const URLS_TO_CACHE = [
    '/',
    '/auth/login',
    '/manifest.json'
];

self.addEventListener('install', (event) => {
    event.waitUntil(
        caches.open(CACHE_NAME)
            .then((cache) => cache.addAll(URLS_TO_CACHE))
    );
});

self.addEventListener('activate', (event) => {
    event.waitUntil(
        caches.keys().then((cacheNames) => {
            return Promise.all(
                cacheNames.map((name) => {
                    if (name !== CACHE_NAME && name !== GCS_CACHE_NAME) {
                        return caches.delete(name);
                    }
                })
            );
        })
    );
});

self.addEventListener('fetch', (event) => {
    const url = new URL(event.request.url);

    // Estrategia: Cache-First para imÃƒÂ¡genes (GCS)
    if (url.origin.includes('storage.googleapis.com')) {
        event.respondWith(
            caches.open(GCS_CACHE_NAME).then(async (cache) => {
                const response = await cache.match(event.request);
                if (response) {
                    return response; // max-age=14400 handled by checking dates or just trusting cache
                }
                const networkResponse = await fetch(event.request);
                // Save to cache for 4 hours
                cache.put(event.request, networkResponse.clone());
                return networkResponse;
            })
        );
        return;
    }

    // Estrategia: Network-First para API y HTML/JS
    event.respondWith(
        fetch(event.request).catch(async () => {
            const cachedResponse = await caches.match(event.request);
            if (cachedResponse) {
                return cachedResponse;
            }
            // Retorno por defecto si falla la red y no estÃ¡ en cachÃ©
            if (event.request.headers.get('accept') && event.request.headers.get('accept').includes('application/json')) {
                return new Response(JSON.stringify({ message: "Servicio no disponible u offline." }), {
                    status: 503,
                    headers: { 'Content-Type': 'application/json' }
                });
            }
            return new Response("Offline", { status: 503 });
        })
    );
});

// Manejador de Notificaciones Push nativas del Sistema Operativo
self.addEventListener('push', (event) => {
    let data = { title: 'Fritolay Ambato', body: 'Tu pedido está próximo a entregarse', url: '/ecommerce/historial' };
    if (event.data) {
        try {
            data = Object.assign(data, event.data.json());
        } catch (e) {
            data.body = event.data.text();
        }
    }

    const options = {
        body: data.body,
        icon: '/icons/icon-192.png',
        badge: '/icons/icon-192.png',
        vibrate: [200, 100, 200],
        data: { url: data.url || '/ecommerce/historial' },
        actions: [
            { action: 'open', title: 'Ver Entrega 🚚' }
        ]
    };

    event.waitUntil(
        self.registration.showNotification(data.title, options)
    );
});

self.addEventListener('notificationclick', (event) => {
    event.notification.close();
    const targetUrl = event.notification.data?.url || '/ecommerce/historial';

    event.waitUntil(
        clients.matchAll({ type: 'window', includeUncontrolled: true }).then((windowClients) => {
            for (let client of windowClients) {
                if (client.url.includes(targetUrl) && 'focus' in client) {
                    return client.focus();
                }
            }
            if (clients.openWindow) {
                return clients.openWindow(targetUrl);
            }
        })
    );
});
