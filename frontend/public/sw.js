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

    // Estrategia: Cache-First para imÃ¡genes (GCS)
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
        fetch(event.request).catch(() => caches.match(event.request))
    );
});
