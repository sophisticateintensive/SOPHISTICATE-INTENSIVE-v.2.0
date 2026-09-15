/**
 * Sophisticate Intensive Classes - Student Portal Service Worker
 * Version: v2.0.0
 * 
 * Strict Security Architecture:
 * - Dynamic student records, financial information, grades, quizzes, and private messages are NEVER cached.
 * - Sensitive routes follow a Network-First policy with safe fallback to /offline.html on network failure.
 * - Only safe static assets (CSS, JS, Fonts, Icons, Fallback UI) are cached.
 */

const CACHE_NAME = 'sophisticate-pwa-v2';

// Static assets safely precached for fast offline shell loading
const PRECACHE_ASSETS = [
    '/offline.html',
    '/manifest.webmanifest',
    '/manifest.json',
    '/icons/icon-48x48.png',
    '/icons/icon-72x72.png',
    '/icons/icon-96x96.png',
    '/icons/icon-128x128.png',
    '/icons/icon-144x144.png',
    '/icons/icon-152x152.png',
    '/icons/icon-180x180.png',
    '/icons/icon-192x192.png',
    '/icons/icon-384x384.png',
    '/icons/icon-512x512.png',
    '/icons/icon-maskable-192x192.png',
    '/icons/icon-maskable-512x512.png',
    '/icons/favicon-16x16.png',
    '/icons/favicon-32x32.png',
    '/images/logo.png',
    '/js/pwa.js'
];

// External immutable assets safe for caching (fonts, CDNs)
const STATIC_ORIGIN_WHITELIST = [
    'fonts.googleapis.com',
    'fonts.gstatic.com',
    'cdn.jsdelivr.net',
    'cdnjs.cloudflare.com',
    'cdn.tailwindcss.com'
];

/**
 * INSTALL EVENT: Pre-cache critical application shell
 */
self.addEventListener('install', (event) => {
    event.waitUntil(
        caches.open(CACHE_NAME).then((cache) => {
            return cache.addAll(PRECACHE_ASSETS);
        }).then(() => self.skipWaiting())
    );
});

/**
 * ACTIVATE EVENT: Clean up stale caches
 */
self.addEventListener('activate', (event) => {
    event.waitUntil(
        caches.keys().then((cacheNames) => {
            return Promise.all(
                cacheNames.map((cacheName) => {
                    if (cacheName !== CACHE_NAME) {
                        return caches.delete(cacheName);
                    }
                })
            );
        }).then(() => self.clients.claim())
    );
});

/**
 * FETCH EVENT: Intercept network requests securely
 */
self.addEventListener('fetch', (event) => {
    const { request } = event;
    const url = new URL(request.url);

    // 1. Only process GET requests; POST, PUT, DELETE, PATCH always go straight to the server
    if (request.method !== 'GET') {
        return;
    }

    // 2. Resource downloads, streams, and pdfs: Strictly NETWORK ONLY to enforce server fee checks
    if (url.pathname.includes('/resources/') && (url.pathname.includes('/download') || url.pathname.includes('/stream'))) {
        return;
    }
    if (url.pathname.includes('/transcript/download')) {
        return;
    }

    // 3. Admin routes: Strictly NETWORK ONLY
    if (url.pathname.startsWith('/admin')) {
        return;
    }

    // 4. HTML Page Navigations (Student Portal Pages / Auth Pages)
    // Strategy: Network-First. Never store dynamic user records in cache.
    if (request.mode === 'navigate' || (request.headers.get('accept') && request.headers.get('accept').includes('text/html'))) {
        event.respondWith(
            fetch(request)
                .catch(() => {
                    // If offline or network error, serve the beautiful offline fallback screen
                    return caches.match('/offline.html');
                })
        );
        return;
    }

    // 5. External CDN / Font / Static Scripts
    if (STATIC_ORIGIN_WHITELIST.some(origin => url.hostname.includes(origin))) {
        event.respondWith(
            caches.match(request).then((cachedResponse) => {
                if (cachedResponse) {
                    // Return cached asset and update in background (Stale-While-Revalidate)
                    fetch(request).then((networkResponse) => {
                        if (networkResponse && networkResponse.status === 200) {
                            caches.open(CACHE_NAME).then((cache) => cache.put(request, networkResponse));
                        }
                    }).catch(() => {/* Ignore network error for background revalidation */});
                    return cachedResponse;
                }
                return fetch(request).then((networkResponse) => {
                    if (networkResponse && networkResponse.status === 200) {
                        const responseClone = networkResponse.clone();
                        caches.open(CACHE_NAME).then((cache) => cache.put(request, responseClone));
                    }
                    return networkResponse;
                });
            })
        );
        return;
    }

    // 6. Local Static Assets (Images, Icons, CSS, JS)
    if (
        url.pathname.startsWith('/icons/') ||
        url.pathname.startsWith('/images/') ||
        url.pathname.startsWith('/js/') ||
        url.pathname.endsWith('.css') ||
        url.pathname.endsWith('.js') ||
        url.pathname.endsWith('.png') ||
        url.pathname.endsWith('.jpg') ||
        url.pathname.endsWith('.svg') ||
        url.pathname.endsWith('.webmanifest') ||
        url.pathname.endsWith('.json')
    ) {
        event.respondWith(
            caches.match(request).then((cachedResponse) => {
                return cachedResponse || fetch(request).then((networkResponse) => {
                    if (networkResponse && networkResponse.status === 200) {
                        const responseClone = networkResponse.clone();
                        caches.open(CACHE_NAME).then((cache) => cache.put(request, responseClone));
                    }
                    return networkResponse;
                });
            })
        );
        return;
    }

    // Default: Regular network fetch
    event.respondWith(fetch(request));
});

/**
 * Handle custom message commands (e.g. skipWaiting trigger from update prompt)
 */
self.addEventListener('message', (event) => {
    if (event.data && event.data.type === 'SKIP_WAITING') {
        self.skipWaiting();
    }
});
