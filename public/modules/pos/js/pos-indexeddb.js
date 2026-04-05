/**
 * POS IndexedDB Helper
 * إدارة التخزين المحلي للمعاملات والأصناف
 */

class POSIndexedDB {
    constructor() {
        this.dbName = 'POSDB';
        this.version = 4;
        this.db = null;
    }

    /**
     * فتح قاعدة البيانات
     */
    async open() {
        return new Promise((resolve, reject) => {
            const request = indexedDB.open(this.dbName, this.version);

            request.onerror = () => reject(request.error);
            request.onsuccess = () => {
                this.db = request.result;
                resolve(this.db);
            };

            request.onupgradeneeded = (event) => {
                const db = event.target.result;

                // Store للأصناف
                if (!db.objectStoreNames.contains('items')) {
                    const itemsStore = db.createObjectStore('items', { keyPath: 'id' });
                    itemsStore.createIndex('code', 'code', { unique: false });
                    itemsStore.createIndex('name', 'name', { unique: false });
                }

                // Store للتصنيفات
                if (!db.objectStoreNames.contains('categories')) {
                    db.createObjectStore('categories', { keyPath: 'id' });
                }

                // Store للمعاملات المعلقة
                if (!db.objectStoreNames.contains('transactions')) {
                    const transactionsStore = db.createObjectStore('transactions', { keyPath: 'local_id', autoIncrement: true });
                    transactionsStore.createIndex('sync_status', 'sync_status', { unique: false });
                    transactionsStore.createIndex('created_at', 'created_at', { unique: false });
                    transactionsStore.createIndex('server_id', 'server_id', { unique: false });
                }

                // Store لـ sync queue
                if (!db.objectStoreNames.contains('sync_queue')) {
                    const syncStore = db.createObjectStore('sync_queue', { keyPath: 'id', autoIncrement: true });
                    syncStore.createIndex('status', 'status', { unique: false });
                }

                // Store للعملاء
                if (!db.objectStoreNames.contains('customers')) {
                    const customersStore = db.createObjectStore('customers', { keyPath: 'id' });
                    customersStore.createIndex('phone', 'phone', { unique: false });
                    customersStore.createIndex('phone2', 'phone2', { unique: false });
                    customersStore.createIndex('name', 'name', { unique: false });
                }

                // Queue لمزامنة العملاء الجدد مع السيرفر
                if (!db.objectStoreNames.contains('customers_queue')) {
                    const queueStore = db.createObjectStore('customers_queue', { keyPath: 'local_id' });
                    queueStore.createIndex('status', 'status', { unique: false });
                }

                // Store للفواتير المعلقة (held orders) - أوفلاين
                if (!db.objectStoreNames.contains('held_orders')) {
                    const heldStore = db.createObjectStore('held_orders', { keyPath: 'local_id' });
                    heldStore.createIndex('created_at', 'created_at', { unique: false });
                    heldStore.createIndex('server_id', 'server_id', { unique: false });
                }

                // Store للمعاملات الأخيرة (كاش)
                if (!db.objectStoreNames.contains('recent_transactions')) {
                    const recentStore = db.createObjectStore('recent_transactions', { keyPath: 'id' });
                    recentStore.createIndex('created_at', 'created_at', { unique: false });
                }

                // Queue للمصروفات النثرية أوفلاين
                if (!db.objectStoreNames.contains('payout_queue')) {
                    const payoutStore = db.createObjectStore('payout_queue', { keyPath: 'local_id' });
                    payoutStore.createIndex('status', 'status', { unique: false });
                }
            };
        });
    }

    /**
     * حفظ صنف
     */
    async saveItem(item) {
        if (!this.db) await this.open();
        
        return new Promise((resolve, reject) => {
            const transaction = this.db.transaction(['items'], 'readwrite');
            const store = transaction.objectStore('items');
            const request = store.put(item);

            request.onsuccess = () => resolve(request.result);
            request.onerror = () => reject(request.error);
        });
    }

    /**
     * حفظ عدة أصناف
     */
    async saveItems(items) {
        if (!this.db) await this.open();
        
        const transaction = this.db.transaction(['items'], 'readwrite');
        const store = transaction.objectStore('items');
        
        const promises = items.map(item => {
            return new Promise((resolve, reject) => {
                const request = store.put(item);
                request.onsuccess = () => resolve();
                request.onerror = () => reject(request.error);
            });
        });

        return Promise.all(promises);
    }

    /**
     * جلب صنف
     */
    async getItem(itemId) {
        if (!this.db) await this.open();
        
        return new Promise((resolve, reject) => {
            const transaction = this.db.transaction(['items'], 'readonly');
            const store = transaction.objectStore('items');
            const request = store.get(itemId);

            request.onsuccess = () => resolve(request.result);
            request.onerror = () => reject(request.error);
        });
    }

    /**
     * جلب جميع الأصناف
     */
    async getAllItems() {
        if (!this.db) await this.open();
        
        return new Promise((resolve, reject) => {
            const transaction = this.db.transaction(['items'], 'readonly');
            const store = transaction.objectStore('items');
            const request = store.getAll();

            request.onsuccess = () => resolve(request.result);
            request.onerror = () => reject(request.error);
        });
    }

    /**
     * البحث في الأصناف
     */
    async searchItems(term) {
        if (!this.db) await this.open();
        
        return new Promise((resolve, reject) => {
            const transaction = this.db.transaction(['items'], 'readonly');
            const store = transaction.objectStore('items');
            const request = store.getAll();

            request.onsuccess = () => {
                const items = request.result;
                const filtered = items.filter(item => {
                    const searchTerm = term.toLowerCase();
                    const nameMatch = item.name && String(item.name).toLowerCase().includes(searchTerm);
                    const codeMatch = item.code && String(item.code).toLowerCase().includes(searchTerm);
                    return nameMatch || codeMatch;
                });
                resolve(filtered);
            };
            request.onerror = () => reject(request.error);
        });
    }

    /**
     * البحث بالباركود - محسّن للسرعة
     */
    async searchByBarcode(barcode) {
        if (!this.db) await this.open();
        
        return new Promise((resolve, reject) => {
            const transaction = this.db.transaction(['items'], 'readonly');
            const store = transaction.objectStore('items');
            const index = store.index('code');
            
            // البحث الدقيق أولاً
            const exactRequest = index.get(barcode);
            
            exactRequest.onsuccess = () => {
                const exactItem = exactRequest.result;
                if (exactItem) {
                    // إذا وُجد تطابق دقيق، إرجاعه مباشرة
                    resolve([exactItem]);
                    return;
                }
                
                // إذا لم يوجد تطابق دقيق، البحث الجزئي
                // استخدام cursor للبحث بشكل أسرع
                const range = IDBKeyRange.bound(barcode, barcode + '\uffff', false, false);
                const cursorRequest = index.openCursor(range);
                const results = [];
                
                cursorRequest.onsuccess = (event) => {
                    const cursor = event.target.result;
                    if (cursor) {
                        const item = cursor.value;
                        if (item.code) {
                            const codeStr = String(item.code);
                            const barcodeStr = String(barcode);
                            if (codeStr.toLowerCase().includes(barcodeStr.toLowerCase())) {
                                results.push(item);
                            }
                        }
                        cursor.continue();
                    } else {
                        // إذا لم يُوجد في النطاق، البحث في كل الأصناف (fallback)
                        if (results.length === 0) {
                            const getAllRequest = store.getAll();
                            getAllRequest.onsuccess = () => {
                                const allItems = getAllRequest.result;
                                const filtered = allItems.filter(item => {
                                    if (!item.code) return false;
                                    const codeStr = String(item.code);
                                    const barcodeStr = String(barcode);
                                    return codeStr.toLowerCase().includes(barcodeStr.toLowerCase());
                                });
                                resolve(filtered.slice(0, 10)); // حد أقصى 10 نتائج
                            };
                            getAllRequest.onerror = () => reject(getAllRequest.error);
                        } else {
                            resolve(results.slice(0, 10)); // حد أقصى 10 نتائج
                        }
                    }
                };
                
                cursorRequest.onerror = () => {
                    // في حالة الخطأ، البحث في كل الأصناف
                    const getAllRequest = store.getAll();
                    getAllRequest.onsuccess = () => {
                        const allItems = getAllRequest.result;
                        const filtered = allItems.filter(item => {
                            if (!item.code) return false;
                            const codeStr = String(item.code);
                            const barcodeStr = String(barcode);
                            return codeStr.toLowerCase().includes(barcodeStr.toLowerCase());
                        });
                        resolve(filtered.slice(0, 10));
                    };
                    getAllRequest.onerror = () => reject(getAllRequest.error);
                };
            };
            
            exactRequest.onerror = () => reject(exactRequest.error);
        });
    }

    /**
     * حفظ عملاء (bulk)
     */
    async saveCustomers(customers) {
        if (!this.db) await this.open();
        // إزالة التكرار بالـ id
        const unique = Object.values(
            customers.reduce((acc, c) => { acc[c.id] = c; return acc; }, {})
        );
        const tx = this.db.transaction(['customers'], 'readwrite');
        const store = tx.objectStore('customers');
        const promises = unique.map(c => new Promise((resolve, reject) => {
            const req = store.put(c);
            req.onsuccess = () => resolve();
            req.onerror = () => reject(req.error);
        }));
        return Promise.all(promises);
    }

    /**
     * حفظ عميل واحد
     */
    async saveCustomer(customer) {
        if (!this.db) await this.open();
        return new Promise((resolve, reject) => {
            const tx = this.db.transaction(['customers'], 'readwrite');
            const store = tx.objectStore('customers');
            const req = store.put(customer);
            req.onsuccess = () => resolve(req.result);
            req.onerror = () => reject(req.error);
        });
    }

    /**
     * البحث عن عميل بالتليفون
     */
    async searchCustomersByPhone(phone) {
        if (!this.db) await this.open();
        return new Promise((resolve, reject) => {
            const tx = this.db.transaction(['customers'], 'readonly');
            const store = tx.objectStore('customers');
            const req = store.getAll();
            req.onsuccess = () => {
                const term = String(phone).toLowerCase();
                const seen = new Set();
                const results = [];
                for (const c of req.result) {
                    if (seen.has(c.id)) continue;
                    if (
                        (c.phone && String(c.phone).toLowerCase().includes(term)) ||
                        (c.phone2 && String(c.phone2).toLowerCase().includes(term))
                    ) {
                        seen.add(c.id);
                        results.push(c);
                        if (results.length >= 10) break;
                    }
                }
                resolve(results);
            };
            req.onerror = () => reject(req.error);
        });
    }

    /**
     * إضافة عميل جديد لـ queue المزامنة
     */
    async queueNewCustomer(customerData) {
        if (!this.db) await this.open();
        const local_id = 'cust_' + this.generateUUID();
        const record = {
            local_id,
            ...customerData,
            status: 'pending',
            created_at: new Date().toISOString(),
        };
        return new Promise((resolve, reject) => {
            const tx = this.db.transaction(['customers_queue'], 'readwrite');
            const store = tx.objectStore('customers_queue');
            const req = store.put(record);
            req.onsuccess = () => resolve(local_id);
            req.onerror = () => reject(req.error);
        });
    }

    /**
     * جلب العملاء المعلقة في الـ queue
     */
    async getPendingCustomers() {
        if (!this.db) await this.open();
        return new Promise((resolve, reject) => {
            const tx = this.db.transaction(['customers_queue'], 'readonly');
            const store = tx.objectStore('customers_queue');
            const index = store.index('status');
            const req = index.getAll('pending');
            req.onsuccess = () => resolve(req.result);
            req.onerror = () => reject(req.error);
        });
    }

    /**
     * تحديث حالة العميل في الـ queue بعد المزامنة
     */
    async markCustomerSynced(local_id, server_id) {
        if (!this.db) await this.open();
        return new Promise((resolve, reject) => {
            const tx = this.db.transaction(['customers_queue', 'customers'], 'readwrite');
            const queueStore = tx.objectStore('customers_queue');
            const getReq = queueStore.get(local_id);
            getReq.onsuccess = () => {
                const record = getReq.result;
                if (record) {
                    record.status = 'synced';
                    record.server_id = server_id;
                    queueStore.put(record);
                    // تحديث الـ id في customers store
                    const custStore = tx.objectStore('customers');
                    const delReq = custStore.delete(record.id);
                    delReq.onsuccess = () => {
                        record.id = server_id;
                        custStore.put(record);
                    };
                }
                resolve();
            };
            getReq.onerror = () => reject(getReq.error);
        });
    }

    /**
     * توليد UUID v4
     */
    generateUUID() {
        return 'xxxxxxxx-xxxx-4xxx-yxxx-xxxxxxxxxxxx'.replace(/[xy]/g, function(c) {
            const r = Math.random() * 16 | 0;
            const v = c === 'x' ? r : (r & 0x3 | 0x8);
            return v.toString(16);
        });
    }

    /**
     * حفظ معاملة معلقة
     */
    async saveTransaction(transaction) {
        if (!this.db) await this.open();
        
        // توليد UUID إذا لم يكن موجوداً
        const localId = transaction.local_id || this.generateUUID();
        
        const transactionData = {
            ...transaction,
            local_id: localId,
            server_id: null, // سيتم تحديثه بعد المزامنة
            sync_status: 'pending',
            created_at: new Date().toISOString(),
            updated_at: new Date().toISOString()
        };

        return new Promise((resolve, reject) => {
            const tx = this.db.transaction(['transactions'], 'readwrite');
            const store = tx.objectStore('transactions');
            const request = store.put(transactionData); // استخدام put بدلاً من add لتحديث إذا كان موجوداً

            request.onsuccess = () => resolve(localId);
            request.onerror = () => reject(request.error);
        });
    }

    /**
     * جلب المعاملات المعلقة
     */
    async getPendingTransactions() {
        if (!this.db) await this.open();
        
        return new Promise((resolve, reject) => {
            const transaction = this.db.transaction(['transactions'], 'readonly');
            const store = transaction.objectStore('transactions');
            const index = store.index('sync_status');
            const request = index.getAll('pending');

            request.onsuccess = () => resolve(request.result);
            request.onerror = () => reject(request.error);
        });
    }

    /**
     * تحديث حالة المعاملة بعد المزامنة
     */
    async updateTransactionStatus(localId, status) {
        if (!this.db) await this.open();
        
        return new Promise((resolve, reject) => {
            const tx = this.db.transaction(['transactions'], 'readwrite');
            const store = tx.objectStore('transactions');
            const getRequest = store.get(localId);

            getRequest.onsuccess = () => {
                const transaction = getRequest.result;
                if (transaction) {
                    transaction.sync_status = status;
                    transaction.updated_at = new Date().toISOString();
                    const updateRequest = store.put(transaction);
                    updateRequest.onsuccess = () => resolve();
                    updateRequest.onerror = () => reject(updateRequest.error);
                } else {
                    resolve();
                }
            };
            getRequest.onerror = () => reject(getRequest.error);
        });
    }

    /**
     * تحديث server_id للمعاملة بعد المزامنة
     */
    async updateTransactionServerId(localId, serverId) {
        if (!this.db) await this.open();
        
        return new Promise((resolve, reject) => {
            const tx = this.db.transaction(['transactions'], 'readwrite');
            const store = tx.objectStore('transactions');
            const getRequest = store.get(localId);

            getRequest.onsuccess = () => {
                const transaction = getRequest.result;
                if (transaction) {
                    transaction.server_id = serverId;
                    transaction.sync_status = 'synced';
                    transaction.updated_at = new Date().toISOString();
                    const updateRequest = store.put(transaction);
                    updateRequest.onsuccess = () => resolve();
                    updateRequest.onerror = () => reject(updateRequest.error);
                } else {
                    resolve();
                }
            };
            getRequest.onerror = () => reject(getRequest.error);
        });
    }

    /**
     * حذف معاملة بعد المزامنة الناجحة
     */
    async deleteTransaction(localId) {
        if (!this.db) await this.open();
        
        return new Promise((resolve, reject) => {
            const transaction = this.db.transaction(['transactions'], 'readwrite');
            const store = transaction.objectStore('transactions');
            const request = store.delete(localId);

            request.onsuccess = () => resolve();
            request.onerror = () => reject(request.error);
        });
    }

    /**
     * حفظ التصنيفات
     */
    async saveCategories(categories) {
        if (!this.db) await this.open();
        
        const transaction = this.db.transaction(['categories'], 'readwrite');
        const store = transaction.objectStore('categories');
        
        const promises = categories.map(category => {
            return new Promise((resolve, reject) => {
                const request = store.put(category);
                request.onsuccess = () => resolve();
                request.onerror = () => reject(request.error);
            });
        });

        return Promise.all(promises);
    }

    /**
     * جلب التصنيفات
     */
    async getCategories() {
        if (!this.db) await this.open();
        
        return new Promise((resolve, reject) => {
            const transaction = this.db.transaction(['categories'], 'readonly');
            const store = transaction.objectStore('categories');
            const request = store.getAll();

            request.onsuccess = () => resolve(request.result);
            request.onerror = () => reject(request.error);
        });
    }

    // ===== HELD ORDERS (أوفلاين) =====

    /**
     * حفظ فاتورة معلقة محلياً
     */
    async saveHeldOrder(orderData) {
        if (!this.db) await this.open();
        const local_id = orderData.local_id || ('held_' + this.generateUUID());
        const record = {
            ...orderData,
            local_id,
            server_id: null,
            sync_status: 'pending',
            created_at: orderData.created_at || new Date().toISOString(),
        };
        return new Promise((resolve, reject) => {
            const tx = this.db.transaction(['held_orders'], 'readwrite');
            const store = tx.objectStore('held_orders');
            const req = store.put(record);
            req.onsuccess = () => resolve(local_id);
            req.onerror = () => reject(req.error);
        });
    }

    /**
     * جلب كل الفواتير المعلقة
     */
    async getHeldOrders() {
        if (!this.db) await this.open();
        return new Promise((resolve, reject) => {
            const tx = this.db.transaction(['held_orders'], 'readonly');
            const store = tx.objectStore('held_orders');
            const req = store.getAll();
            req.onsuccess = () => resolve(req.result);
            req.onerror = () => reject(req.error);
        });
    }

    /**
     * جلب فاتورة معلقة بالـ local_id
     */
    async getHeldOrder(local_id) {
        if (!this.db) await this.open();
        return new Promise((resolve, reject) => {
            const tx = this.db.transaction(['held_orders'], 'readonly');
            const store = tx.objectStore('held_orders');
            const req = store.get(local_id);
            req.onsuccess = () => resolve(req.result);
            req.onerror = () => reject(req.error);
        });
    }

    /**
     * حذف فاتورة معلقة
     */
    async deleteHeldOrder(local_id) {
        if (!this.db) await this.open();
        return new Promise((resolve, reject) => {
            const tx = this.db.transaction(['held_orders'], 'readwrite');
            const store = tx.objectStore('held_orders');
            const req = store.delete(local_id);
            req.onsuccess = () => resolve();
            req.onerror = () => reject(req.error);
        });
    }

    /**
     * تحديث server_id للفاتورة المعلقة بعد المزامنة
     */
    async updateHeldOrderServerId(local_id, server_id) {
        if (!this.db) await this.open();
        return new Promise((resolve, reject) => {
            const tx = this.db.transaction(['held_orders'], 'readwrite');
            const store = tx.objectStore('held_orders');
            const getReq = store.get(local_id);
            getReq.onsuccess = () => {
                const record = getReq.result;
                if (record) {
                    record.server_id = server_id;
                    record.sync_status = 'synced';
                    store.put(record);
                }
                resolve();
            };
            getReq.onerror = () => reject(getReq.error);
        });
    }

    // ===== RECENT TRANSACTIONS (كاش) =====

    /**
     * حفظ المعاملات الأخيرة (كاش)
     */
    async saveRecentTransactions(transactions) {
        if (!this.db) await this.open();
        const tx = this.db.transaction(['recent_transactions'], 'readwrite');
        const store = tx.objectStore('recent_transactions');
        const promises = transactions.map(t => new Promise((resolve, reject) => {
            const req = store.put(t);
            req.onsuccess = () => resolve();
            req.onerror = () => reject(req.error);
        }));
        return Promise.all(promises);
    }

    /**
     * جلب المعاملات الأخيرة من الكاش
     */
    async getRecentTransactions(limit = 50) {
        if (!this.db) await this.open();
        return new Promise((resolve, reject) => {
            const tx = this.db.transaction(['recent_transactions'], 'readonly');
            const store = tx.objectStore('recent_transactions');
            const req = store.getAll();
            req.onsuccess = () => {
                const sorted = (req.result || []).sort((a, b) => new Date(b.created_at) - new Date(a.created_at));
                resolve(sorted.slice(0, limit));
            };
            req.onerror = () => reject(req.error);
        });
    }

    // ===== PAYOUT QUEUE (أوفلاين) =====

    /**
     * إضافة مصروف نثري لـ queue المزامنة
     */
    async queuePayout(payoutData) {
        if (!this.db) await this.open();
        const local_id = 'payout_' + this.generateUUID();
        const record = {
            local_id,
            ...payoutData,
            status: 'pending',
            created_at: new Date().toISOString(),
        };
        return new Promise((resolve, reject) => {
            const tx = this.db.transaction(['payout_queue'], 'readwrite');
            const store = tx.objectStore('payout_queue');
            const req = store.put(record);
            req.onsuccess = () => resolve(local_id);
            req.onerror = () => reject(req.error);
        });
    }

    /**
     * جلب المصروفات المعلقة
     */
    async getPendingPayouts() {
        if (!this.db) await this.open();
        return new Promise((resolve, reject) => {
            const tx = this.db.transaction(['payout_queue'], 'readonly');
            const store = tx.objectStore('payout_queue');
            const index = store.index('status');
            const req = index.getAll('pending');
            req.onsuccess = () => resolve(req.result);
            req.onerror = () => reject(req.error);
        });
    }

    /**
     * تحديث حالة المصروف بعد المزامنة
     */
    async markPayoutSynced(local_id) {
        if (!this.db) await this.open();
        return new Promise((resolve, reject) => {
            const tx = this.db.transaction(['payout_queue'], 'readwrite');
            const store = tx.objectStore('payout_queue');
            const getReq = store.get(local_id);
            getReq.onsuccess = () => {
                const record = getReq.result;
                if (record) { record.status = 'synced'; store.put(record); }
                resolve();
            };
            getReq.onerror = () => reject(getReq.error);
        });
    }
}

// Export للاستخدام
window.POSIndexedDB = POSIndexedDB;
