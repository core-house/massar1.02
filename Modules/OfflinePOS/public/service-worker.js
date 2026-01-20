/**
 * Offline POS Service Worker
 * يوفر قدرات offline-first والمزامنة في الخلفية
 */

const CACHE_VERSION = 'offline-pos-v1.0.0';
const CACHE_NAME = `${CACHE_VERSION}`;
const OFFLINE_URL = '/offline-pos/offline';

// الملفات المطلوبة للعمل offline
const STATIC_CACHE_URLS = [
    '/offline-pos',
    '/offline-pos/pos',
    '/offline-pos/install',
    OFFLINE_URL,
    '/css/app.css',
    '/js/app.js',
];

// الملفات الديناميكية (API، صور، إلخ)
const DYNAMIC_CACHE_NAME = `${CACHE_VERSION}-dynamic`;

/**
 * Install Event
 * يتم تفعيله عند تثبيت Service Worker لأول مرة
 */
self.addEventListener('install', (event) => {
    console.log('[Service Worker] Installing...');
    
    event.waitUntil(
        caches.open(CACHE_NAME)
            .then((cache) => {
                console.log('[Service Worker] Caching static assets');
                return cache.addAll(STATIC_CACHE_URLS);
            })
            .then(() => {
                console.log('[Service Worker] Installation complete');
                return self.skipWaiting(); // تفعيل فوري
            })
            .catch((error) => {
                console.error('[Service Worker] Installation failed:', error);
            })
    );
});

/**
 * Activate Event
 * يتم تفعيله عند تنشيط Service Worker
 */
self.addEventListener('activate', (event) => {
    console.log('[Service Worker] Activating...');
    
    event.waitUntil(
        caches.keys()
            .then((cacheNames) => {
                // حذف الكاش القديم
                return Promise.all(
                    cacheNames
                        .filter((cacheName) => {
                            return cacheName.startsWith('offline-pos-') && 
                                   cacheName !== CACHE_NAME &&
                                   cacheName !== DYNAMIC_CACHE_NAME;
                        })
                        .map((cacheName) => {
                            console.log('[Service Worker] Deleting old cache:', cacheName);
                            return caches.delete(cacheName);
                        })
                );
            })
            .then(() => {
                console.log('[Service Worker] Activation complete');
                return self.clients.claim(); // السيطرة على جميع الصفحات
            })
    );
});

/**
 * Fetch Event
 * يعترض جميع الطلبات ويوفر استراتيجية caching
 */
self.addEventListener('fetch', (event) => {
    const { request } = event;
    const url = new URL(request.url);

    // تجاهل طلبات chrome-extension
    if (url.protocol === 'chrome-extension:') {
        return;
    }

    // استراتيجية مختلفة حسب نوع الطلب
    if (request.method === 'GET') {
        event.respondWith(handleFetch(request));
    }
});

/**
 * معالجة طلبات GET
 */
async function handleFetch(request) {
    const url = new URL(request.url);

    // طلبات API - Network First
    if (url.pathname.startsWith('/api/offline-pos/')) {
        return networkFirstStrategy(request);
    }

    // صفحات HTML - Network First مع Offline fallback
    if (request.mode === 'navigate') {
        return navigateStrategy(request);
    }

    // الملفات الثابتة (CSS, JS, صور) - Cache First
    if (
        request.destination === 'style' ||
        request.destination === 'script' ||
        request.destination === 'image' ||
        request.destination === 'font'
    ) {
        return cacheFirstStrategy(request);
    }

    // الافتراضي - Network First
    return networkFirstStrategy(request);
}

/**
 * استراتيجية Network First
 * محاولة من الشبكة أولاً، ثم الكاش
 */
async function networkFirstStrategy(request) {
    try {
        const networkResponse = await fetch(request);
        
        // حفظ في الكاش الديناميكي
        if (networkResponse.ok) {
            const cache = await caches.open(DYNAMIC_CACHE_NAME);
            cache.put(request, networkResponse.clone());
        }
        
        return networkResponse;
    } catch (error) {
        // محاولة من الكاش
        const cachedResponse = await caches.match(request);
        if (cachedResponse) {
            return cachedResponse;
        }
        
        throw error;
    }
}

/**
 * استراتيجية Cache First
 * محاولة من الكاش أولاً، ثم الشبكة
 */
async function cacheFirstStrategy(request) {
    const cachedResponse = await caches.match(request);
    
    if (cachedResponse) {
        return cachedResponse;
    }
    
    try {
        const networkResponse = await fetch(request);
        
        if (networkResponse.ok) {
            const cache = await caches.open(DYNAMIC_CACHE_NAME);
            cache.put(request, networkResponse.clone());
        }
        
        return networkResponse;
    } catch (error) {
        console.error('[Service Worker] Fetch failed:', error);
        throw error;
    }
}

/**
 * استراتيجية Navigate
 * للصفحات HTML
 */
async function navigateStrategy(request) {
    try {
        const networkResponse = await fetch(request);
        
        if (networkResponse.ok) {
            const cache = await caches.open(DYNAMIC_CACHE_NAME);
            cache.put(request, networkResponse.clone());
        }
        
        return networkResponse;
    } catch (error) {
        // محاولة من الكاش
        const cachedResponse = await caches.match(request);
        if (cachedResponse) {
            return cachedResponse;
        }
        
        // عرض صفحة offline
        const offlineResponse = await caches.match(OFFLINE_URL);
        if (offlineResponse) {
            return offlineResponse;
        }
        
        throw error;
    }
}

/**
 * Background Sync Event
 * المزامنة في الخلفية عند عودة الاتصال
 */
self.addEventListener('sync', (event) => {
    console.log('[Service Worker] Background sync triggered:', event.tag);
    
    if (event.tag === 'sync-transactions') {
        event.waitUntil(syncPendingTransactions());
    }
});

/**
 * مزامنة المعاملات المعلقة
 */
async function syncPendingTransactions() {
    try {
        console.log('[Service Worker] Starting transaction sync...');
        
        // فتح IndexedDB
        const db = await openIndexedDB();
        
        // جلب المعاملات المعلقة
        const transactions = await getPendingTransactions(db);
        
        console.log(`[Service Worker] Found ${transactions.length} pending transactions`);
        
        // مزامنة كل معاملة
        for (const transaction of transactions) {
            try {
                await syncSingleTransaction(transaction);
            } catch (error) {
                console.error('[Service Worker] Transaction sync failed:', transaction.local_id, error);
            }
        }
        
        console.log('[Service Worker] Transaction sync complete');
    } catch (error) {
        console.error('[Service Worker] Sync failed:', error);
        throw error;
    }
}

/**
 * فتح IndexedDB
 */
function openIndexedDB() {
    return new Promise((resolve, reject) => {
        const request = indexedDB.open('OfflinePOSDB', 1);
        
        request.onsuccess = () => {
            resolve(request.result);
        };
        
        request.onerror = () => {
            reject(request.error);
        };
    });
}

/**
 * جلب المعاملات المعلقة
 */
function getPendingTransactions(db) {
    return new Promise((resolve, reject) => {
        const transaction = db.transaction('transactions', 'readonly');
        const store = transaction.objectStore('transactions');
        const index = store.index('sync_status');
        const request = index.getAll('pending');
        
        request.onsuccess = () => {
            resolve(request.result);
        };
        
        request.onerror = () => {
            reject(request.error);
        };
    });
}

/**
 * مزامنة معاملة واحدة
 */
async function syncSingleTransaction(transaction) {
    const response = await fetch('/api/offline-pos/sync-transaction', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-Requested-With': 'XMLHttpRequest',
        },
        body: JSON.stringify({
            local_id: transaction.local_id,
            transaction: transaction,
        }),
    });
    
    if (!response.ok) {
        throw new Error(`Sync failed: ${response.status}`);
    }
    
    const result = await response.json();
    
    // تحديث حالة المعاملة في IndexedDB
    const db = await openIndexedDB();
    await updateTransactionStatus(db, transaction.local_id, 'synced', result.data.server_transaction_id);
}

/**
 * تحديث حالة المعاملة
 */
function updateTransactionStatus(db, localId, status, serverId = null) {
    return new Promise((resolve, reject) => {
        const transaction = db.transaction('transactions', 'readwrite');
        const store = transaction.objectStore('transactions');
        const request = store.get(localId);
        
        request.onsuccess = () => {
            const data = request.result;
            data.sync_status = status;
            data.updated_at = new Date().toISOString();
            
            if (serverId) {
                data.server_id = serverId;
                data.synced_at = new Date().toISOString();
            }
            
            const updateRequest = store.put(data);
            
            updateRequest.onsuccess = () => {
                resolve();
            };
            
            updateRequest.onerror = () => {
                reject(updateRequest.error);
            };
        };
        
        request.onerror = () => {
            reject(request.error);
        };
    });
}

/**
 * Message Event
 * للتواصل مع الصفحة
 */
self.addEventListener('message', (event) => {
    console.log('[Service Worker] Message received:', event.data);
    
    if (event.data && event.data.type === 'SKIP_WAITING') {
        self.skipWaiting();
    }
    
    if (event.data && event.data.type === 'SYNC_NOW') {
        syncPendingTransactions()
            .then(() => {
                event.ports[0].postMessage({ success: true });
            })
            .catch((error) => {
                event.ports[0].postMessage({ success: false, error: error.message });
            });
    }
});

/**
 * Push Notification Event (للمستقبل)
 */
self.addEventListener('push', (event) => {
    const data = event.data ? event.data.json() : {};
    
    const options = {
        body: data.body || 'لديك إشعار جديد',
        icon: '/modules/offlinepos/icons/icon-192x192.png',
        badge: '/modules/offlinepos/icons/icon-72x72.png',
        vibrate: [200, 100, 200],
        data: data,
    };
    
    event.waitUntil(
        self.registration.showNotification(data.title || 'Offline POS', options)
    );
});

console.log('[Service Worker] Service Worker loaded successfully');
