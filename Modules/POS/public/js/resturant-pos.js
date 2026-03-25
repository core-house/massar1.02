/**
 * Restaurant POS - Main JavaScript (Complete Version)
 */

// ===== SERVICE WORKER =====
if ('serviceWorker' in navigator) {
    window.addEventListener('load', function () {
        navigator.serviceWorker.getRegistrations().then(function (registrations) {
            registrations.forEach(reg => {
                if (reg.scope !== window.location.origin + '/pos/') reg.unregister();
            });
        });
        navigator.serviceWorker.register(window.RPOS_CONFIG.routes.serviceWorker, {
            scope: '/pos/', updateViaCache: 'none'
        }).then(reg => {
            reg.addEventListener('updatefound', function () {
                const nw = reg.installing;
                nw.addEventListener('statechange', function () {
                    if (nw.state === 'installed' && navigator.serviceWorker.controller)
                        nw.postMessage({ type: 'SKIP_WAITING' });
                });
            });
            navigator.serviceWorker.addEventListener('message', event => {
                if (event.data?.type === 'SYNC_TRANSACTIONS') rposSyncPendingTransactions();
            });

            // كاش الصفحة الحالية مباشرة من المتصفح (مع الـ session cookies)
            function cacheSelfPage() {
                if (!('caches' in window)) return;
                const cacheName = 'pos-v1.3.0-dynamic';
                const pageUrl = window.location.pathname; // e.g. /pos/restaurant
                caches.open(cacheName).then(function (cache) {
                    fetch(pageUrl, { credentials: 'include' })
                        .then(function (res) {
                            if (res.ok) {
                                cache.put(pageUrl, res);
                                console.log('[RPOS] Page cached for offline:', pageUrl);
                            }
                        })
                        .catch(function () {});
                });
            }

            // شغّل الكاش بعد ثانيتين (بعد ما الصفحة تتحمل كاملاً)
            setTimeout(cacheSelfPage, 2000);

        }).catch(err => console.warn('[RPOS] SW failed:', err));
    });
}

(function () {
    "use strict";
    const CFG = window.RPOS_CONFIG;

    // ===== STATE =====
    let rCart = [];
    let rSelectedCategory = '';
    let rItemsCache = CFG.itemsData || {};
    let rInitialItems = CFG.initialProductsData || [];
    let rIsOnline = navigator.onLine;
    let rDb = null;
    let rOrderType = 'takeaway';
    let rPreviousOrderType = 'takeaway';
    let rCurrentPriceGroupId = CFG.firstPriceGroupId || null;
    let rDeliveryFee = 0;
    let rSelectedTable = null;
    let rSelectedTableName = '';
    let rInvoiceNotes = '';
    let rDeliveryCustomerId = null;
    let rDeliveryCustomerName = '';
    let rDeliveryAddress = '';
    let rDeliveryDriverId = null;

    // ===== SYNC =====
    window.rposSyncPendingTransactions = function () {
        if (!rDb || !navigator.onLine) return;
        rDb.getPendingTransactions().then(pending => {
            // sync_status هو الـ field الصح في IndexedDB (مش status)
            const toSync = (pending || []).filter(t => t.sync_status !== 'held' && !t.server_id);
            if (!toSync.length) return;
            showToast(CFG.i18n.syncing_offline_orders, 'info');
            toSync.forEach(tx => {
                $.ajax({
                    url: CFG.routes.store,
                    method: 'POST',
                    contentType: 'application/json',
                    data: JSON.stringify(tx),
                    headers: { 'X-CSRF-TOKEN': CFG.csrfToken },
                    success: function (res) {
                        const serverId = res.operhead_id || res.transaction_id || null;
                        rDb.updateTransactionServerId(tx.local_id, serverId).catch(() => {});
                    },
                    error: function () {
                        // فشل الإرسال — يبقى pending للمحاولة التالية
                    }
                });
            });
        }).catch(() => {});
    };

    function syncPendingCustomers() {
        if (!rDb || !navigator.onLine) return;
        rDb.getPendingCustomers().then(pending => {
            if (!pending.length) return;
            pending.forEach(c => {
                $.ajax({
                    url: CFG.routes.saveDeliveryCustomer,
                    method: 'POST',
                    headers: { 'X-CSRF-TOKEN': CFG.csrfToken },
                    data: { name: c.name, phone: c.phone, address: c.address || '' },
                    success: function (res) {
                        if (res.success && res.customer?.id) {
                            rDb.markCustomerSynced(c.local_id, res.customer.id).catch(() => {});
                            // لو الـ state لسه بيشاور على الـ temp id، حدّثه
                            if (rDeliveryCustomerId === c.id) {
                                rDeliveryCustomerId = res.customer.id;
                            }
                        }
                    }
                });
            });
        }).catch(() => {});
    }

    // ===== HELPERS =====
    window.showToast = function (msg, type) {
        const bg = type === 'success' ? '#10b981' : type === 'error' ? '#ef4444' : '#f59e0b';
        const el = $(`<div style="position:fixed;top:20px;left:50%;transform:translateX(-50%);background:${bg};color:#fff;padding:.75rem 1.5rem;border-radius:8px;z-index:99999;font-family:Cairo,sans-serif;box-shadow:0 4px 12px rgba(0,0,0,.2)">${msg}</div>`);
        $('body').append(el);
        setTimeout(() => el.fadeOut(() => el.remove()), 3000);
    };

    const generateUUID = () => 'xxxxxxxx-xxxx-4xxx-yxxx-xxxxxxxxxxxx'.replace(/[xy]/g, c => {
        const r = Math.random() * 16 | 0, v = c === 'x' ? r : (r & 0x3 | 0x8);
        return v.toString(16);
    });

    const getModal = id => {
        const el = document.getElementById(id);
        if (!el) return { show: () => {}, hide: () => {} };
        return bootstrap.Modal.getInstance(el) || new bootstrap.Modal(el);
    };

    const hideModal = id => {
        const m = bootstrap.Modal.getInstance(document.getElementById(id));
        if (m) m.hide();
    };

    // ===== PRODUCTS =====
    function getItemDisplayPrice(item) {
        const cached = rItemsCache[item.id] || item;
        if (rCurrentPriceGroupId && cached.prices) {
            const pg = cached.prices.find(p => p.id == rCurrentPriceGroupId);
            if (pg) return parseFloat(pg.value);
        }
        return parseFloat(cached.sale_price || 0);
    }

    function renderProducts(items) {
        const grid = $('#rposProductsGrid');
        grid.empty();
        if (!items || items.length === 0) {
            grid.html(`<div class="text-center text-muted py-5 w-100"><i class="fas fa-box-open fa-2x mb-2"></i><p>${CFG.i18n.no_records_found}</p></div>`);
            return;
        }
        let html = '';
        items.forEach(item => {
            const cached = rItemsCache[item.id] || item;
            const icon = cached.is_weight_scale ? 'fa-weight' : 'fa-utensils';
            const scaleBadge = cached.is_weight_scale ? '<span class="rpos-product-card__scale-badge"><i class="fas fa-weight"></i></span>' : '';
            const desc = item.notes ? `<p class="rpos-product-card__desc">${item.notes.substring(0, 30)}</p>` : '';
            const displayPrice = getItemDisplayPrice(item);
            const imgHtml = cached.image
                ? `<img src="${cached.image}" alt="${item.name}" loading="lazy" style="width:100%;height:100%;object-fit:cover;" onerror="this.style.display='none';this.nextElementSibling.style.display='block';"><i class="fas ${icon}" style="display:none"></i>`
                : `<i class="fas ${icon}"></i>`;
            html += `<div class="rpos-product-card" data-item-id="${item.id}">
                <div class="rpos-product-card__icon">${imgHtml}${scaleBadge}</div>
                <div class="rpos-product-card__body">
                    <h6 class="rpos-product-card__name">${item.name}</h6>
                    ${desc}
                    <div class="rpos-product-card__footer">
                        <span class="rpos-product-card__price">${displayPrice.toFixed(2)}</span>
                        <button class="rpos-product-card__add" data-item-id="${item.id}"><i class="fas fa-plus"></i></button>
                    </div>
                </div>
            </div>`;
        });
        grid.html(html);
    }

    function applyFilters() {
        const term = $('#rpos-search').val().trim().toLowerCase();

        // لو مفيش تصنيف مختار ومفيش بحث → الـ grid فاضي
        if (!rSelectedCategory && !term) {
            $('#rposProductsGrid').empty();
            return;
        }

        let filtered = rInitialItems;
        if (rSelectedCategory) filtered = filtered.filter(i => i.category_id == rSelectedCategory);
        if (term) filtered = filtered.filter(i => i.name.toLowerCase().includes(term) || (i.code && i.code.toLowerCase().includes(term)));
        renderProducts(filtered);
    }

    // ===== CART =====
    function addToCart(itemId) {
        const item = rItemsCache[itemId];
        if (!item) return;
        const price = getItemDisplayPrice(item);
        const existing = rCart.find(i => i.id == itemId);
        if (existing) { existing.quantity++; } else {
            rCart.push({ id: item.id, name: item.name, price, quantity: 1, note: '' });
        }
        renderCart(itemId);
    }

    function renderCart(focusId = null) {
        const container = $('#rposCartItems');
        if (rCart.length === 0) {
            container.html(`<div class="rpos-cart__empty"><i class="fas fa-utensils"></i><p>${CFG.i18n.cart_empty}</p></div>`);
            updateTotals();
            return;
        }
        const html = rCart.map((item, idx) => `
            <div class="rpos-cart-item" data-idx="${idx}">
                <div class="rpos-cart-item__main">
                    <div class="rpos-cart-item__info">
                        <div class="rpos-cart-item__name">${item.name}</div>
                        <div class="rpos-cart-item__price">${(item.price * item.quantity).toFixed(2)}</div>
                    </div>
                    <div class="rpos-cart-item__qty">
                        <button class="rpos-cart-item__qty-btn rpos-qty-minus" data-idx="${idx}"><i class="fas fa-minus"></i></button>
                        <span class="rpos-cart-item__qty-val">${item.quantity}</span>
                        <button class="rpos-cart-item__qty-btn rpos-qty-plus" data-idx="${idx}"><i class="fas fa-plus"></i></button>
                    </div>
                    <button class="rpos-cart-item__remove-btn rpos-remove-item" data-idx="${idx}"><i class="fas fa-times"></i></button>
                </div>
                <div class="rpos-cart-item__note-row visible">
                    <input type="text" class="rpos-cart-item__note-input rpos-note-input" data-idx="${idx}" value="${item.note || ''}" placeholder="${CFG.i18n.item_note}">
                </div>
            </div>`).join('');
        container.html(html);
        if (focusId) {
            const fIdx = rCart.findIndex(i => i.id == focusId);
            if (fIdx !== -1) container.find(`.rpos-cart-item[data-idx="${fIdx}"] input`).focus();
        }
        updateTotals();
    }

    function updateTotals() {
        const subtotal = rCart.reduce((s, i) => s + (i.price * i.quantity), 0);
        $('#rposSubtotal').text(subtotal.toFixed(2));
        $('.rpos-cart__total-row.delivery-fee-row').remove();
        if (rDeliveryFee > 0) {
            $('#rposSubtotal').parent().after(`<div class="rpos-cart__total-row delivery-fee-row"><span>${CFG.i18n.delivery_fee}</span><span>${rDeliveryFee.toFixed(2)}</span></div>`);
        }
        const total = subtotal + rDeliveryFee;
        $('#rposTotal').text(total.toFixed(2));
        $('#paymentTotal, #cashAmount').val(total.toFixed(2));
    }

    // ===== INVOICE =====
    function buildInvoiceData() {
        return {
            local_id: generateUUID(),
            items: rCart,
            customer_id: rDeliveryCustomerId || CFG.defaultCustomerId,
            table_id: rSelectedTable,
            order_type: rOrderType,
            payment_method: $('#paymentMethod').val(),
            cash_amount: $('#cashAmount').val() || 0,
            card_amount: $('#cardAmount').val() || 0,
            cash_account_id: $('#cashAccountId').val() || null,
            bank_account_id: $('#bankAccountId').val() || null,
            delivery_fee: rDeliveryFee,
            delivery_address: rDeliveryAddress,
            notes: rInvoiceNotes,
            price_group_id: rCurrentPriceGroupId,
            invoice_type: 103
        };
    }

    window.saveRPosInvoice = function (print = false) {
        if (rCart.length === 0) return;
        const data = buildInvoiceData();

        // فتح نافذة فارغة فوراً (في نفس لحظة الكليك) لتجنب حجب المتصفح للـ popup
        const printWindow = print ? window.open('', '_blank') : null;

        const finalize = (res = null) => {
            rposResetOrder();
            showToast(CFG.i18n.saved_successfully, 'success');
            if (printWindow && res?.operhead_id) {
                printWindow.location.href = CFG.routes.print.replace(':id', res.operhead_id);
            } else if (printWindow) {
                printWindow.close();
            }
        };
        if (!rIsOnline) {
            if (printWindow) printWindow.close();
            rDb.saveTransaction(data).then(() => finalize()).catch(() => showToast(CFG.i18n.save_error, 'error'));
            return;
        }
        $.ajax({
            url: CFG.routes.store, method: 'POST', contentType: 'application/json',
            data: JSON.stringify(data), headers: { 'X-CSRF-TOKEN': CFG.csrfToken },
            success: res => finalize(res),
            error: () => {
                if (printWindow) printWindow.close();
                rDb.saveTransaction(data).then(() => finalize());
            }
        });
    };

    function rposResetOrder() {
        rCart = []; rSelectedTable = null; rInvoiceNotes = '';
        rDeliveryCustomerId = null; rDeliveryCustomerName = ''; rDeliveryAddress = '';
        renderCart();
        hideModal('paymentModal');
        $('#rposCartTableTag').hide().text('');
        $('#rposTableText').text(CFG.i18n.table_free);
        $('.rpos-table-badge__dot').removeClass('rpos-table-badge__dot--busy').addClass('rpos-table-badge__dot--free');
    }

    // ===== INIT =====
    $(document).ready(function () {
        rDb = new POSIndexedDB();
        window._rposDb = rDb;
        rDb.open().then(() => {
            const itemsToCache = Object.values(rItemsCache);
            if (itemsToCache.length > 0) {
                rDb.saveItems(itemsToCache).catch(() => {});
            } else {
                rDb.getAllItems().then(items => {
                    if (items.length > 0) {
                        rInitialItems = items;
                        items.forEach(i => { rItemsCache[i.id] = i; });
                    }
                });
            }
            // تحميل العملاء في IndexedDB لو أونلاين
            if (rIsOnline) {
                rposSyncPendingTransactions();
                syncPendingCustomers();
                $.get(CFG.routes.searchCustomerPhone, { phone: '', load_all: 1 }, function (res) {
                    if (res.customers?.length > 0) rDb.saveCustomers(res.customers).catch(() => {});
                }).fail(() => {});
            }
        });

        // DateTime
        function updateTime() {
            const now = new Date();
            $('#rposDateTime').text(now.toLocaleDateString('ar-SA') + ' ' + now.toLocaleTimeString('ar-SA', { hour: '2-digit', minute: '2-digit' }));
        }
        updateTime(); setInterval(updateTime, 60000);

        // Online status
        const updateOnline = () => {
            rIsOnline = navigator.onLine;
            $('#rposOnlineStatus').toggleClass('rpos-online-dot--on', rIsOnline).toggleClass('rpos-online-dot--off', !rIsOnline);
            $('#rposFooterDot').toggleClass('rpos-footer__dot--on', rIsOnline).toggleClass('rpos-footer__dot--off', !rIsOnline);
            if (rIsOnline) { rposSyncPendingTransactions(); syncPendingCustomers(); }
        };
        updateOnline();
        window.addEventListener('online', updateOnline);
        window.addEventListener('offline', updateOnline);

        // Search & Categories
        let rSearchTimer;
        $('#rpos-search').on('input', function () { clearTimeout(rSearchTimer); rSearchTimer = setTimeout(applyFilters, 200); });

        $(document).on('click', '.rpos-cat-item', function () {
            $('.rpos-cat-item').removeClass('rpos-cat-item--active');
            $(this).addClass('rpos-cat-item--active');
            rSelectedCategory = $(this).data('category') || '';
            applyFilters();
        });

        // Price Group
        $(document).on('click', '.rpos-pg-btn', function () {
            $('.rpos-pg-btn').removeClass('rpos-pg-btn--active');
            $(this).addClass('rpos-pg-btn--active');
            rCurrentPriceGroupId = $(this).data('pg-id') || null;
            rCart.forEach(cartItem => {
                const cached = rItemsCache[cartItem.id];
                if (cached) cartItem.price = getItemDisplayPrice(cached);
            });
            renderCart(); updateTotals();
            if (rSelectedCategory) applyFilters();
        });

        // Add to cart — stop propagation on the + button to avoid double-add
        $(document).on('click', '.rpos-product-card__add', function (e) {
            e.stopPropagation();
            const itemId = parseInt($(this).data('item-id'));
            if (itemId) addToCart(itemId);
        });
        $(document).on('click', '.rpos-product-card', function () {
            const itemId = parseInt($(this).data('item-id'));
            if (itemId) addToCart(itemId);
        });

        // Cart controls
        $(document).on('click', '.rpos-qty-plus', e => { rCart[$(e.currentTarget).data('idx')].quantity++; renderCart(); });
        $(document).on('click', '.rpos-qty-minus', e => {
            const idx = $(e.currentTarget).data('idx');
            if (--rCart[idx].quantity <= 0) rCart.splice(idx, 1);
            renderCart();
        });
        $(document).on('click', '.rpos-remove-item', e => { rCart.splice($(e.currentTarget).data('idx'), 1); renderCart(); });
        $(document).on('input', '.rpos-note-input', e => { rCart[$(e.currentTarget).data('idx')].note = $(e.currentTarget).val(); });

        // Void
        $('#rposVoidBtn, #rposVoidIconBtn').on('click', function () {
            if (rCart.length === 0) return;
            if (confirm(CFG.i18n.confirm_void)) { rCart = []; renderCart(); showToast(CFG.i18n.order_voided, 'info'); }
        });

        // Notes
        $('#rposNotesBtn').on('click', () => getModal('notesModal').show());
        document.getElementById('notesModal')?.addEventListener('hidden.bs.modal', () => {
            rInvoiceNotes = $('#invoiceNotes').val() || '';
        });

        // Print btn in cart
        $('#rposPrintBtn').on('click', function () {
            if (rCart.length === 0) { showToast(CFG.i18n.cart_empty, 'warning'); return; }
            // kitchen print / receipt preview — open checkout with print flag
            openCheckout(true);
        });

        // ===== ORDER TYPE TABS =====
        $('.rpos-order-type').on('click', function () {
            $('.rpos-order-type').removeClass('rpos-order-type--active');
            $(this).addClass('rpos-order-type--active');
            const newType = $(this).data('type');
            if (newType === 'delivery') rPreviousOrderType = rOrderType;
            rOrderType = newType;

            if (rOrderType === 'dining') {
                rDeliveryFee = 0; updateTotals();
                $('#rposDeliveryPanel').hide();
                getModal('orderTypeModal').show();
            } else if (rOrderType === 'delivery') {
                rDeliveryFee = 0; rSelectedTable = null; rSelectedTableName = '';
                $('#rposCartTableTag').hide().text('');
                $('#rposTableText').text(CFG.i18n.table_free);
                $('.rpos-table-badge__dot').removeClass('rpos-table-badge__dot--busy').addClass('rpos-table-badge__dot--free');
                $('#rposDeliveryPanel').show();
                getModal('deliveryCustomerModal').show();
            } else {
                rDeliveryFee = 0; rSelectedTable = null; rSelectedTableName = '';
                $('#rposCartTableTag').hide().text('');
                $('#rposTableText').text(CFG.i18n.table_free);
                $('.rpos-table-badge__dot').removeClass('rpos-table-badge__dot--busy').addClass('rpos-table-badge__dot--free');
                $('#rposDeliveryPanel').hide();
                updateTotals();
            }
        });

        // Table modal
        $(document).on('click', '.rpos-table-card', function () {
            if ($(this).data('table-status') === 'occupied') { showToast(CFG.i18n.table_occupied, 'warning'); return; }
            $('.rpos-table-card').removeClass('rpos-table-card--selected');
            $(this).addClass('rpos-table-card--selected');
            rSelectedTable = $(this).data('table-id');
            rSelectedTableName = $(this).data('table-name');
            $('#confirmTableBtn').prop('disabled', false);
        });

        $('#confirmTableBtn').on('click', function () {
            if (!rSelectedTable) return;
            $('#rposTableText').text(rSelectedTableName);
            $('.rpos-table-badge__dot').removeClass('rpos-table-badge__dot--free').addClass('rpos-table-badge__dot--busy');
            $('#rposCartTableTag').text(rSelectedTableName).show();
            hideModal('orderTypeModal');
            showToast(CFG.i18n.table_selected + ' ' + rSelectedTableName, 'success');
        });

        $('#rposTableBadge').on('click', function () {
            if (rOrderType === 'dining') getModal('orderTypeModal').show();
        });

        // Delivery modal
        document.getElementById('deliveryCustomerModal')?.addEventListener('hidden.bs.modal', function () {
            if (!rDeliveryCustomerId && !$('#deliveryCustomerName, #rposDeliveryName').val()?.trim()) {
                rOrderType = rPreviousOrderType;
                $('.rpos-order-type').removeClass('rpos-order-type--active');
                $(`.rpos-order-type[data-type="${rPreviousOrderType}"]`).addClass('rpos-order-type--active');
                $('#rposDeliveryPanel').hide();
            } else {
                $('#rposDeliveryPanel').show();
            }
        });

        // ===== DELIVERY MODAL: phone search =====
        let rDeliveryModalPhoneTimer;
        $(document).on('input', '#deliveryPhoneSearch', function () {
            clearTimeout(rDeliveryModalPhoneTimer);
            const phone = $(this).val().trim();
            $('#deliveryCustomerStatus').hide().html('');
            $('#deliverySearchResults').hide().empty();
            $('#deliveryCustomerName').val('');
            $('#deliveryCustomerId').val('');
            $('#deliveryAddressOptions').hide();
            $('#deliveryAddressBtns').empty();
            $('#deliveryAddressInput').val('');
            rDeliveryCustomerId = null;
            rDeliveryCustomerName = '';

            if (phone.length < 3) return;

            $('#deliveryPhoneSpinner').css('display', 'flex');

            function showDeliveryResults(customers) {
                $('#deliveryPhoneSpinner').hide();
                if (customers.length > 0) {
                    let html = '';
                    customers.forEach(c => {
                        html += `<button type="button" class="list-group-item list-group-item-action rpos-delivery-modal-result"
                            data-id="${c.id}" data-name="${c.name}" data-phone="${c.phone}"
                            data-addr="${c.address || ''}" data-addr2="${c.address2 || ''}" data-addr3="${c.address3 || ''}">
                            <span class="fw-bold">${c.name}</span>
                            <small class="text-muted ms-2" dir="ltr">${c.phone}</small>
                        </button>`;
                    });
                    $('#deliverySearchResults').html(html).show();
                    $('#deliveryCustomerStatus')
                        .html('<span class="badge bg-success"><i class="fas fa-check-circle me-1"></i>العميل موجود</span>')
                        .show();
                } else {
                    $('#deliveryCustomerStatus')
                        .html('<span class="badge bg-warning text-dark"><i class="fas fa-user-plus me-1"></i>عميل جديد — أدخل الاسم والعنوان</span>')
                        .show();
                    $('#deliveryCustomerName').focus();
                }
            }

            rDeliveryModalPhoneTimer = setTimeout(function () {
                // البحث في IndexedDB أولاً
                rDb.searchCustomersByPhone(phone).then(function (localResults) {
                    if (localResults.length > 0) {
                        showDeliveryResults(localResults);
                    } else if (rIsOnline) {
                        // fallback للسيرفر لو مش موجود محلياً
                        $.get(CFG.routes.searchCustomerPhone, { phone }, function (res) {
                            showDeliveryResults(res.customers || []);
                            // حفظ النتائج في IndexedDB
                            if (res.customers?.length > 0) rDb.saveCustomers(res.customers).catch(() => {});
                        }).fail(() => {
                            $('#deliveryPhoneSpinner').hide();
                            $('#deliveryCustomerStatus')
                                .html('<span class="badge bg-danger"><i class="fas fa-exclamation-circle me-1"></i>حدث خطأ في البحث</span>')
                                .show();
                        });
                    } else {
                        showDeliveryResults([]);
                    }
                }).catch(() => {
                    // لو IndexedDB فشل روح للسيرفر
                    if (rIsOnline) {
                        $.get(CFG.routes.searchCustomerPhone, { phone }, function (res) {
                            showDeliveryResults(res.customers || []);
                        }).fail(() => { $('#deliveryPhoneSpinner').hide(); });
                    } else {
                        $('#deliveryPhoneSpinner').hide();
                    }
                });
            }, 400);
        });

        // اختيار عميل من نتائج البحث
        $(document).on('click', '.rpos-delivery-modal-result', function () {
            rDeliveryCustomerId = $(this).data('id');
            rDeliveryCustomerName = $(this).data('name');
            $('#deliveryCustomerId').val(rDeliveryCustomerId);
            $('#deliveryCustomerName').val(rDeliveryCustomerName);
            $('#deliveryPhoneSearch').val($(this).data('phone'));
            $('#deliverySearchResults').hide().empty();
            $('#deliveryCustomerStatus')
                .html('<span class="badge bg-success"><i class="fas fa-check-circle me-1"></i>تم اختيار: ' + rDeliveryCustomerName + '</span>')
                .show();

            // عرض العناوين المحفوظة
            const addrs = [
                { label: 'العنوان 1', val: $(this).data('addr') },
                { label: 'العنوان 2', val: $(this).data('addr2') },
                { label: 'العنوان 3', val: $(this).data('addr3') },
            ].filter(a => a.val && a.val.trim() !== '');

            if (addrs.length > 0) {
                let btnsHtml = '';
                addrs.forEach(a => {
                    btnsHtml += `<button type="button" class="btn btn-outline-secondary btn-sm rpos-addr-pick" data-addr="${a.val}">${a.label}: ${a.val}</button>`;
                });
                $('#deliveryAddressBtns').html(btnsHtml);
                $('#deliveryAddressOptions').show();
                $('#deliveryAddressInput').val(addrs[0].val);
                rDeliveryAddress = addrs[0].val;
                $('#deliveryAddressBtns .rpos-addr-pick').first().addClass('active btn-secondary').removeClass('btn-outline-secondary');
            } else {
                $('#deliveryAddressOptions').hide();
                $('#deliveryAddressInput').val('').focus();
            }
        });

        // اختيار عنوان محفوظ
        $(document).on('click', '.rpos-addr-pick', function () {
            $('.rpos-addr-pick').removeClass('active btn-secondary').addClass('btn-outline-secondary');
            $(this).addClass('active btn-secondary').removeClass('btn-outline-secondary');
            rDeliveryAddress = $(this).data('addr');
            $('#deliveryAddressInput').val(rDeliveryAddress);
        });

        $(document).on('input', '#deliveryAddressInput', function () {
            rDeliveryAddress = $(this).val().trim();
        });

        // تأكيد الـ delivery
        $(document).on('click', '#confirmDeliveryBtn', function () {
            const name = $('#deliveryCustomerName').val().trim();
            const phone = $('#deliveryPhoneSearch').val().trim();
            const address = $('#deliveryAddressInput').val().trim();

            if (!name) { showToast(CFG.i18n.enter_customer_name, 'warning'); return; }
            if (!phone) { showToast(CFG.i18n.enter_customer_phone, 'warning'); return; }

            rDeliveryAddress = address;
            rDeliveryCustomerName = name;
            rDeliveryDriverId = $('#rposDeliveryDriver').val() || null;

            const btn = $(this).prop('disabled', true);

            if (rDeliveryCustomerId) {
                // عميل موجود — حدّث العنوان لو اتغير
                if (address && CFG.routes.updateDeliveryCustomerAddress) {
                    $.ajax({
                        url: CFG.routes.updateDeliveryCustomerAddress,
                        method: 'POST',
                        headers: { 'X-CSRF-TOKEN': CFG.csrfToken },
                        data: { customer_id: rDeliveryCustomerId, address, field: 'address' }
                    });
                }
                $('#rposCartTableTag').text(`${CFG.i18n.delivery}: ${name}`).show();
                hideModal('deliveryCustomerModal');
                updateTotals();
                btn.prop('disabled', false);
            } else {
                // عميل جديد — حفظ في IndexedDB أولاً
                const tempId = 'temp_' + Date.now();
                const customerData = { id: tempId, name, phone, address: address || '', address2: '' };

                rDb.saveCustomer(customerData).then(() => {
                    return rDb.queueNewCustomer(customerData);
                }).then(() => {
                    rDeliveryCustomerId = tempId;
                    $('#deliveryCustomerId').val(tempId);
                    $('#rposCartTableTag').text(`${CFG.i18n.delivery}: ${name}`).show();
                    hideModal('deliveryCustomerModal');
                    updateTotals();
                    btn.prop('disabled', false);

                    if (rIsOnline) {
                        // زامن فوراً مع السيرفر وحدّث الـ id
                        $.ajax({
                            url: CFG.routes.saveDeliveryCustomer,
                            method: 'POST',
                            headers: { 'X-CSRF-TOKEN': CFG.csrfToken },
                            data: { name, phone, address },
                            success: function (res) {
                                if (res.success && res.customer?.id) {
                                    rDb.markCustomerSynced(tempId, res.customer.id).catch(() => {});
                                    if (rDeliveryCustomerId === tempId) rDeliveryCustomerId = res.customer.id;
                                    showToast(CFG.i18n.customer_saved, 'success');
                                }
                            }
                        });
                    } else {
                        showToast(CFG.i18n.customer_saved + ' (offline)', 'info');
                    }
                }).catch(() => {
                    showToast(CFG.i18n.error_occurred, 'error');
                    btn.prop('disabled', false);
                });
            }
        });

        // reset المودال عند الفتح
        document.getElementById('deliveryCustomerModal')?.addEventListener('show.bs.modal', function () {
            $('#deliveryPhoneSearch').val('');
            $('#deliveryCustomerName').val('');
            $('#deliveryCustomerId').val('');
            $('#deliveryAddressInput').val('');
            $('#deliveryCustomerStatus').hide().html('');
            $('#deliverySearchResults').hide().empty();
            $('#deliveryAddressOptions').hide();
            $('#deliveryAddressBtns').empty();
        });

        // Delivery driver (cart panel)
        $(document).on('change', '#rposDeliveryDriver', function () {
            rDeliveryDriverId = $(this).val() || null;
        });

        // Header buttons → modals
        $('#rposHeldBtn').on('click', () => getModal('heldOrdersModal').show());
        $('#rposRecentBtn').on('click', () => getModal('recentTransactionsModal').show());
        $('#rposPendingBtn').on('click', () => getModal('pendingTransactionsModal').show());
        $('#rposPayOutBtn').on('click', e => { e.preventDefault(); getModal('payOutModal').show(); });
        $('#rposReturnInvoiceBtn').on('click', e => { e.preventDefault(); getModal('returnInvoiceModal').show(); });

        // ===== CHECKOUT =====
        function openCheckout(autoPrint = false) {
            const total = parseFloat($('#rposTotal').text()) || 0;
            $('#paymentTotal').val(total.toFixed(2));
            $('#cashAmount').val(total.toFixed(2));
            $('#cardAmount').val(0);
            $('#changeAmountDiv').hide();
            $('#changeAmount').text('0.00');
            $('#cardAmountDiv, #bankAccountDiv').hide();
            $('#cashAmountDiv, #cashAccountDiv').show();
            // Quick amounts
            const amounts = [Math.ceil(total / 5) * 5, Math.ceil(total / 10) * 10, Math.ceil(total / 50) * 50].filter((v, i, a) => v > total && a.indexOf(v) === i).slice(0, 3);
            let quickHtml = `<button class="rpos-quick-amt rpos-quick-amt--exact" data-amt="${total.toFixed(2)}">${CFG.i18n.exact} ${total.toFixed(2)}</button>`;
            amounts.forEach(a => { quickHtml += `<button class="rpos-quick-amt" data-amt="${a}">${a}</button>`; });
            $('#rposQuickAmounts').html(quickHtml);
            if (autoPrint) { window._rposPrintOnSave = true; }
            getModal('paymentModal').show();
        }

        $('#rposCheckoutBtn').on('click', function () {
            if (rCart.length === 0) { showToast(CFG.i18n.cart_empty, 'warning'); return; }
            openCheckout(false);
        });

        // Payment method toggle
        $('#paymentMethod').on('change', function () {
            const method = $(this).val();
            $('#cashAmountDiv, #cashAccountDiv').toggle(method !== 'card');
            $('#cardAmountDiv, #bankAccountDiv').toggle(method !== 'cash');
            if (method === 'card') {
                const total = parseFloat($('#paymentTotal').val()) || 0;
                $('#cardAmount').val(total.toFixed(2));
                $('#cashAmount').val(0);
            } else if (method === 'cash') {
                const total = parseFloat($('#paymentTotal').val()) || 0;
                $('#cashAmount').val(total.toFixed(2));
                $('#cardAmount').val(0);
            }
            $('#changeAmountDiv').hide();
        });

        // Quick amounts
        $(document).on('click', '.rpos-quick-amt', function () {
            const amt = parseFloat($(this).data('amt'));
            $('#cashAmount').val(amt.toFixed(2));
            const total = parseFloat($('#paymentTotal').val()) || 0;
            const change = amt - total;
            if (change >= 0) {
                $('#changeAmount').text(change.toFixed(2));
                $('#changeAmountDiv').show();
            }
        });

        // Cash amount change → show change
        $('#cashAmount').on('input', function () {
            const paid = parseFloat($(this).val()) || 0;
            const total = parseFloat($('#paymentTotal').val()) || 0;
            const change = paid - total;
            if (change >= 0) { $('#changeAmount').text(change.toFixed(2)); $('#changeAmountDiv').show(); }
            else { $('#changeAmountDiv').hide(); }
        });

        // Save buttons
        $('#saveAndPrintBtn').on('click', () => saveRPosInvoice(true));
        $('#saveOnlyBtn').on('click', () => saveRPosInvoice(false));

        // Refresh items
        $('#rposRefreshBtn').on('click', function () {
            const btn = $(this).prop('disabled', true);
            btn.find('i').addClass('fa-spin');
            $.get(CFG.routes.allItemsDetails, res => {
                if (res?.items) {
                    rInitialItems = res.items;
                    res.items.forEach(i => rItemsCache[i.id] = i);
                    rDb.saveItems(res.items);
                    applyFilters();
                    showToast(CFG.i18n.items_refreshed, 'success');
                }
            }).always(() => btn.prop('disabled', false).find('i').removeClass('fa-spin'));
        });

        // Pending badge update
        function updatePendingBadge() {
            if (!rDb) return;
            rDb.getPendingTransactions().then(pending => {
                const count = (pending || []).filter(t => t.status !== 'held' && !t.server_id).length;
                if (count > 0) { $('#rposPendingBadge').text(count).show(); } else { $('#rposPendingBadge').hide(); }
            }).catch(() => {});
        }
        setInterval(updatePendingBadge, 10000);

    }); // end document.ready

})(); // end IIFE
