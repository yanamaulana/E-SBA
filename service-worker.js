const CACHE_NAME = 'esba-static-v6';
const OFFLINE_URL = './offline.html';

const STATIC_ASSETS = [
    './offline.html',
    './assets/E-SBA_assets/web-logo/favicon-ori-192x192.png',
    './assets/E-SBA_assets/web-logo/favicon-ori-512x512.png',
    './assets/Metronic/dist/assets/css/style.bundle.css',
    './assets/Metronic/dist/assets/plugins/global/plugins.bundle.css',
    './assets/Metronic/dist/assets/js/scripts.bundle.js',
    './assets/Metronic/dist/assets/plugins/global/plugins.bundle.js'
];

self.addEventListener('install', (event) => {
    event.waitUntil(
        caches.open(CACHE_NAME)
            .then((cache) => cache.addAll(STATIC_ASSETS))
            .then(() => self.skipWaiting())
    );
});

self.addEventListener('activate', (event) => {
    event.waitUntil(
        caches.keys()
            .then((cacheNames) => Promise.all(
                cacheNames
                    .filter((cacheName) => cacheName !== CACHE_NAME)
                    .map((cacheName) => caches.delete(cacheName))
            ))
            .then(() => self.clients.claim())
    );
});

self.addEventListener('fetch', (event) => {
    const requestUrl = new URL(event.request.url);

    if (event.request.method !== 'GET' || requestUrl.origin !== self.location.origin) {
        return;
    }

    if (event.request.mode === 'navigate') {
        event.respondWith(fetch(event.request).catch(() => caches.match(OFFLINE_URL)));
        return;
    }

    event.respondWith(
        caches.match(event.request).then((cachedResponse) => cachedResponse || fetch(event.request).then((networkResponse) => {
            if (networkResponse.ok) {
                const responseToCache = networkResponse.clone();
                caches.open(CACHE_NAME).then((cache) => cache.put(event.request, responseToCache));
            }
            return networkResponse;
        }))
    );
});