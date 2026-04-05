/**
 * POS Service Worker - Offline First v1.3.0
 * يوفر قدرات offline-first للمعاملات والمزامنة
 */

const CACHE_VERSION = 'pos-v1.4.0';
const CACHE_NAME = `${CACHE_VERSION}`;
const DYNAMIC_CACHE_NAME = `${CACHE_VERSION}-dynamic`;

// POS pages to cache for offline use
const POS_PAGES = [
    '/pos',
    '/pos/create',
    '/pos/restaurant',
];

// CDN assets to cache for offline use
const CDN_CACHE_URLS = [
    'https://code.jquery.com/jquery-3.7.0.min.js',
    'https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js',
    'https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.rtl.min.css',
    'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css',
    'https://cdn.jsdelivr.net/npm/sweetalert2@11',
];

/**
 * Install Event - cache CDN assets only
 * ملاحظة: صفحات POS بتتكاش من المتصفح مباشرة (مع الـ session cookies) عبر كود في resturant-pos.js
 */
self.addEventListener('install', (event) => {
    console.log('[POS SW] جاري التثبيت v1.4.0...');

    event.waitUntil(
        caches.open(CACHE_NAME).then((cache) => {
            return Promise.allSettled(
                CDN_CACHE_URLS.map(url =>
                    fetch(url, { mode: 'cors' })
                        .then(res => { if (res.ok) cache.put(url, res); })
                        .catch(() => {})
                )
            );
        }).then(() => {
            console.log('[POS SW] اكتمل التثبيت');
            return self.skipWaiting();
        })
    );
});

/**
 * Activate Event - clean old caches
 */
self.addEventListener('activate', (event) => {
    console.log('[POS SW] Activating...');

    event.waitUntil(
        caches.keys()
            .then((cacheNames) => {
                return Promise.all(
                    cacheNames
                        .filter(name =>
                            name.startsWith('pos-') &&
                            name !== CACHE_NAME &&
                            name !== DYNAMIC_CACHE_NAME
                        )
                        .map(name => {
                            console.log('[POS SW] Deleting old cache:', name);
                            return caches.delete(name);
                        })
                );
            })
            .then(() => {
                console.log('[POS SW] Activation complete');
                return self.clients.claim();
            })
    );
});

/**
 * Check if a URL belongs to a POS page (not login/redirect)
 */
function isPosPage(url) {
    try {
        const u = new URL(url);
        return u.pathname.startsWith('/pos') && !u.pathname.startsWith('/login');
    } catch {
        return false;
    }
}

/**
 * Fetch Event
 */
self.addEventListener('fetch', (event) => {
    const { request } = event;
    const url = new URL(request.url);

    // Ignore chrome-extension requests
    if (url.protocol === 'chrome-extension:') return;

    // API calls - Network First (offline: return cached or fail gracefully)
    if (url.pathname.startsWith('/pos/api/')) {
        // ping دايماً يروح للسيرفر مباشرة بدون كاش
        if (url.pathname === '/pos/api/ping') return;
        event.respondWith(networkFirstStrategy(request));
        return;
    }

    // Static assets - Cache First
    if (
        request.destination === 'style' ||
        request.destination === 'script' ||
        request.destination === 'image' ||
        request.destination === 'font'
    ) {
        event.respondWith(cacheFirstStrategy(request));
        return;
    }

    // POS page navigation - Cache First with network update
    if (request.mode === 'navigate' && isPosPage(request.url)) {
        event.respondWith(navigateStrategy(request));
        return;
    }

    // Default - Network First
    event.respondWith(networkFirstStrategy(request));
});

/**
 * Navigate Strategy for POS pages:
 * - Online: fetch from network, cache the result, return it
 * - Offline: return cached version
 */
async function navigateStrategy(request) {
    const cache = await caches.open(DYNAMIC_CACHE_NAME);

    try {
        const networkResponse = await fetch(request, { credentials: 'include' });

        // Only cache if it's the actual POS page (not a redirect to login)
        if (networkResponse.ok && isPosPage(networkResponse.url)) {
            cache.put(request, networkResponse.clone());
        }

        return networkResponse;
    } catch (error) {
        console.log('[POS SW] Offline - serving from cache:', request.url);

        // Try exact URL match first
        const cachedResponse = await cache.match(request);
        if (cachedResponse) return cachedResponse;

        // Try matching by pathname only (handles query string differences)
        const url = new URL(request.url);
        const pathMatch = await cache.match(url.pathname);
        if (pathMatch) return pathMatch;

        // Fallback: try /pos/restaurant or /pos/create
        for (const page of POS_PAGES) {
            const fallback = await cache.match(page);
            if (fallback) return fallback;
        }

        // Last resort: return a minimal offline page
        return new Response(
            `<!DOCTYPE html><html dir="rtl" lang="ar">
            <head><meta charset="utf-8"><title>POS - غير متصل</title>
            <style>body{font-family:Cairo,sans-serif;display:flex;align-items:center;justify-content:center;height:100vh;margin:0;background:#1a1a2e;color:#fff;flex-direction:column;gap:1rem}
            .icon{font-size:4rem}.msg{font-size:1.2rem;opacity:.8}.sub{font-size:.9rem;opacity:.5}</style></head>
            <body>
                <div class="icon">📡</div>
                <div class="msg">لا يوجد اتصال بالإنترنت</div>
                <div class="sub">افتح الصفحة مرة واحدة وأنت متصل لتفعيل الوضع الغير متصل</div>
                <button onclick="location.reload()" style="margin-top:1rem;padding:.5rem 1.5rem;border:none;border-radius:8px;background:#25b900;color:#fff;cursor:pointer;font-size:1rem">إعادة المحاولة</button>
            </body></html>`,
            { headers: { 'Content-Type': 'text/html; charset=utf-8' } }
        );
    }
}

/**
 * Network First Strategy
 */
async function networkFirstStrategy(request) {
    try {
        const networkResponse = await fetch(request);

        if (networkResponse.ok && request.method === 'GET') {
            const cache = await caches.open(DYNAMIC_CACHE_NAME);
            cache.put(request, networkResponse.clone());
        }

        return networkResponse;
    } catch (error) {
        const cachedResponse = await caches.match(request);
        if (cachedResponse) return cachedResponse;
        throw error;
    }
}

/**
 * Cache First Strategy
 */
async function cacheFirstStrategy(request) {
    const cachedResponse = await caches.match(request);
    if (cachedResponse) return cachedResponse;

    try {
        const networkResponse = await fetch(request);

        if (networkResponse.ok && request.method === 'GET') {
            const cache = await caches.open(CACHE_NAME);
            cache.put(request, networkResponse.clone());
        }

        return networkResponse;
    } catch (error) {
        throw error;
    }
}

/**
 * Background Sync
 */
self.addEventListener('sync', (event) => {
    if (event.tag === 'sync-pos-transactions') {
        event.waitUntil(syncPendingTransactions());
    }
});

async function syncPendingTransactions() {
    try {
        const clients = await self.clients.matchAll();
        clients.forEach(client => client.postMessage({ type: 'SYNC_TRANSACTIONS' }));
    } catch (error) {
        console.error('[POS SW] Sync failed:', error);
        throw error;
    }
}

/**
 * Message Handler
 */
self.addEventListener('message', (event) => {
    if (event.data?.type === 'SKIP_WAITING') {
        self.skipWaiting();
    }
});
