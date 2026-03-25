<script>
window.RPOS_CONFIG = {
    csrfToken: '{{ csrf_token() }}',
    defaultCustomerId: {{ $clientsAccounts->first()->id ?? 0 }},
    firstPriceGroupId: {{ $priceGroups->first()?->id ?? 'null' }},
    itemsData: @json($itemsData),
    initialProductsData: @json($initialProductsData),
    routes: {
        serviceWorker:                 '{{ route("pos.service-worker") }}',
        store:                         '{{ route("pos.api.store") }}',
        ping:                          '{{ route("pos.api.ping") }}',
        print:                         '{{ route("pos.print", ":id") }}',
        allItemsDetails:               '{{ route("pos.api.all-items-details") }}',
        searchCustomerPhone:           '{{ route("pos.api.search-customer-phone") }}',
        customerRecommendations:       '{{ route("pos.api.customer-recommendations", ":id") }}',
        saveDeliveryCustomer:          '{{ route("pos.api.save-delivery-customer") }}',
        updateDeliveryCustomerAddress: '{{ route("pos.api.update-delivery-customer-address") }}',
        recentTransactions:            '{{ route("pos.api.recent-transactions") }}',
        payOut:                        '{{ route("pos.api.petty-cash") }}',
        holdOrder:                     '{{ route("pos.api.hold-order") }}',
        heldOrders:                    '{{ route("pos.api.held-orders") }}',
        recallOrder:                   '{{ route("pos.api.recall-order", ":id") }}',
        deleteHeldOrder:               '{{ route("pos.api.delete-held-order", ":id") }}',
        searchInvoice:                 '{{ route("pos.api.invoice", ":proId") }}',
        returnInvoice:                 '{{ route("pos.api.return-invoice") }}',
    },
    i18n: {
        no_records_found:             '{{ __("pos.no_records_found") }}',
        select_category_prompt:       '{{ __("pos.select_category_prompt") }}',
        cart_empty:                   '{{ __("pos.cart_empty") }}',
        add_items:                    '{{ __("pos.add_items") }}',
        item_note:                    '{{ __("pos.item_note") }}',
        table_free:                   '{{ __("pos.table_free") }}',
        table_occupied:               '{{ __("pos.table_occupied") }}',
        table_selected:               '{{ __("pos.table_selected") }}',
        delivery:                     '{{ __("pos.delivery") }}',
        delivery_fee:                 '{{ __("pos.delivery_fee") }}',
        customer_not_found:           '{{ __("pos.customer_not_found") }}',
        customer_found:               '{{ __("pos.customer_found") }}',
        customer_saved:               '{{ __("pos.customer_saved") }}',
        no_previous_orders:           '{{ __("pos.no_previous_orders") }}',
        no_items:                     '{{ __("pos.no_items") }}',
        enter_customer_name:          '{{ __("pos.enter_customer_name") }}',
        enter_customer_phone:         '{{ __("pos.enter_customer_phone") }}',
        confirm_void:                 '{{ __("pos.confirm_reset_cart") }}',
        order_voided:                 '{{ __("pos.cart_reset_success") }}',
        exact:                        '{{ __("pos.exact") }}',
        order_held:                   '{{ __("pos.order_held") }}',
        order_recalled:               '{{ __("pos.order_recalled") }}',
        no_held_orders:               '{{ __("pos.no_held_orders") }}',
        no_pending:                   '{{ __("pos.no_pending") }}',
        recall:                       '{{ __("pos.recall") }}',
        saved_successfully:           '{{ __("pos.saved_successfully") }}',
        saved_offline:                '{{ __("pos.saved_offline") }}',
        save_error:                   '{{ __("pos.save_error") }}',
        offline_no_db:                '{{ __("pos.offline_no_db") }}',
        items_refreshed:              '{{ __("pos.items_refreshed") }}',
        refresh_failed:               '{{ __("pos.refresh_failed") }}',
        syncing_offline_orders:       '{{ __("pos.syncing_offline_orders") }}',
        error_occurred:               '{{ __("pos.error_occurred") }}',
        saving:                       '{{ __("pos.saving") }}',
        enter_amount:                 '{{ __("pos.enter_valid_amount") }}',
        select_cash_account:          '{{ __("pos.select_cash_account") }}',
        select_expense_account:       '{{ __("pos.select_expense_account") }}',
        enter_description:            '{{ __("pos.enter_description") }}',
        order_type_changed_dining:    '{{ __("pos.order_type_changed_dining") }}',
        order_type_changed_takeaway:  '{{ __("pos.order_type_changed_takeaway") }}',
        order_type_changed_delivery:  '{{ __("pos.order_type_changed_delivery") }}',
        pay_out_success:              '{{ __("pos.pay_out_success") }}',
        submit_pay_out:               '{{ __("pos.submit_pay_out") }}',
        invoice_not_found:            '{{ __("pos.invoice_not_found") }}',
        confirm_return:               '{{ __("pos.confirm_return_msg") }}',
        return_success:               '{{ __("pos.return_success") }}',
        date:                         '{{ __("pos.date") }}',
        customer:                     '{{ __("pos.customer") }}',
        total:                        '{{ __("pos.total") }}',
        item:                         '{{ __("pos.item") }}',
        qty:                          '{{ __("pos.qty") }}',
        price:                        '{{ __("pos.price") }}',
        address_1:                    '{{ __("pos.address_1") }}',
        address_2:                    '{{ __("pos.address_2") }}',
        address_3:                    '{{ __("pos.address_3") }}',
        delivery_modal_title:         '{{ __("pos.delivery_modal_title") }}',
        select_address:               '{{ __("pos.select_address") }}',
        address_label:                '{{ __("pos.address_label") }}',
        enter_address:                '{{ __("pos.enter_address") }}',
    }
};
</script>

<script>
// ===== POS_TRANS (نصوص الـ modal scripts) =====
window.POS_TRANS = window.POS_TRANS || {
    loading:                  '{{ __("pos.loading") }}',
    currency:                 '',
    no_held_orders:           '{{ __("pos.no_held_orders") }}',
    held_orders_hint:         '{{ __("pos.held_orders_can_hold_hint") }}',
    held_orders_load_error:   '{{ __("pos.held_orders_load_error") }}',
    held_invoice_label:       '{{ __("pos.held_invoice_label") }}',
    customer_label:           '{{ __("pos.customer_label") }}',
    store_label:              '{{ __("pos.store_label") }}',
    items_count_label:        '{{ __("pos.items_count_label") }}',
    cashier_label:            '{{ __("pos.cashier_label") }}',
    notes_label:              '{{ __("pos.notes_label") }}',
    recall_order:             '{{ __("pos.recall_order") }}',
    complete_order:           '{{ __("pos.complete_order") }}',
    confirm_recall_order:     '{{ __("pos.confirm_recall_order") }}',
    confirm_complete_order:   '{{ __("pos.confirm_complete_order") }}',
    confirm_delete_held:      '{{ __("pos.confirm_delete_held") }}',
    recall_success:           '{{ __("pos.recall_success") }}',
    recall_error:             '{{ __("pos.recall_error") }}',
    complete_success:         '{{ __("pos.complete_success") }}',
    complete_error:           '{{ __("pos.complete_error") }}',
    delete_held_success:      '{{ __("pos.delete_held_success") }}',
    delete_held_error:        '{{ __("pos.delete_held_error") }}',
    hold_success:             '{{ __("pos.hold_success") }}',
    hold_error:               '{{ __("pos.hold_error") }}',
    cart_empty_hold:          '{{ __("pos.cart_empty_hold") }}',
    confirm_hold_order:       '{{ __("pos.confirm_hold_order") }}',
    no_operations:            '{{ __("pos.no_operations") }}',
    sync_error:               '{{ __("pos.sync_error") }}',
    invoice_number_col:       '{{ __("pos.invoice_number_col") }}',
    date_col:                 '{{ __("pos.date_col") }}',
    customer_col:             '{{ __("pos.customer_col") }}',
    store_col:                '{{ __("pos.store_col") }}',
    user_col:                 '{{ __("pos.user_col") }}',
    items_count_col:          '{{ __("pos.items_count_col") }}',
    total_col:                '{{ __("pos.total_col") }}',
    paid_col:                 '{{ __("pos.paid_col") }}',
    actions_col:              '{{ __("pos.actions_col") }}',
    cart_empty_alert:         '{{ __("pos.cart_empty_alert") }}',
    enter_valid_amount:       '{{ __("pos.enter_valid_amount") }}',
    select_cash_account:      '{{ __("pos.select_cash_account") }}',
    select_expense_account:   '{{ __("pos.select_expense_account") }}',
    enter_description:        '{{ __("pos.enter_description") }}',
    processing:               '{{ __("pos.processing") }}',
    pay_out_success:          '{{ __("pos.pay_out_success") }}',
    voucher_number_label:     '{{ __("pos.voucher_number_label") }}',
    pay_out_error:            '{{ __("pos.pay_out_error") }}',
    enter_invoice_number:     '{{ __("pos.enter_invoice_number") }}',
    invoice_not_found:        '{{ __("pos.invoice_not_found") }}',
    search_error:             '{{ __("pos.search_error") }}',
    invoice_number_label:     '{{ __("pos.invoice_number_label") }}',
    invoice_date_label:       '{{ __("pos.invoice_date_label") }}',
    invoice_customer_label:   '{{ __("pos.invoice_customer_label") }}',
    invoice_total_label:      '{{ __("pos.invoice_total_label") }}',
    invoice_items_label:      '{{ __("pos.invoice_items_label") }}',
    search_first:             '{{ __("pos.search_first") }}',
    confirm_return:           '{{ __("pos.confirm_return") }}',
    returning:                '{{ __("pos.returning") }}',
    return_success:           '{{ __("pos.return_success") }}',
    return_error:             '{{ __("pos.return_error") }}',
    balance_unavailable:      '{{ __("pos.balance_unavailable") }}',
    offline_mode:             'وضع أوفلاين',
    no_data_offline:          'لا توجد بيانات محفوظة محلياً',
    load_error:               'حدث خطأ أثناء التحميل',
    delete_confirm:           'هل تريد حذف هذا العنصر؟',
    recall_requires_online:   'استرجاع الفاتورة يتطلب الاتصال بالسيرفر',
    synced_badge:             'متزامن',
    offline_badge:            'أوفلاين',
    pending_badge:            'معلق',
    items_unit:               'صنف',
    no_pending_transactions:  'لا توجد معاملات معلقة',
    db_unavailable:           'قاعدة البيانات المحلية غير متاحة',
    sync_requires_online:     'المزامنة تتطلب الاتصال بالسيرفر',
};
</script>

<script>
$(document).ready(function () {

    // ===== HELD ORDERS MODAL =====
    const heldOrdersModalEl = document.getElementById('heldOrdersModal');
    if (heldOrdersModalEl) {
        heldOrdersModalEl.addEventListener('show.bs.modal', function () { loadHeldOrders(); });
    }
    $('#refreshHeldOrdersBtn').on('click', function () { loadHeldOrders(); });

    function renderHeldOrdersList(orders) {
        const list = $('#heldOrdersList');
        if (!orders || orders.length === 0) {
            list.html('<div class="text-center py-5 text-muted"><i class="fas fa-pause-circle fa-3x mb-3 d-block opacity-25"></i>' + window.POS_TRANS.no_held_orders + '</div>');
            return;
        }
        let html = '<div class="list-group">';
        orders.forEach(function (order) {
            const isLocal = !order.server_id && order.local_id;
            const displayId = order.server_id ? '#' + order.id : '(محلي)';
            const itemsCount = order.items_count || (order.items ? order.items.length : 0);
            const total = parseFloat(order.total || 0).toFixed(2);
            const dateStr = order.held_at_formatted || (order.created_at ? new Date(order.created_at).toLocaleString('ar-SA') : '');
            const localBadge = isLocal ? '<span class="badge bg-secondary ms-1">' + window.POS_TRANS.offline_badge + '</span>' : '';
            html += '<div class="list-group-item">' +
                '<div class="d-flex justify-content-between align-items-center">' +
                    '<div>' +
                        '<strong>' + displayId + '</strong>' + localBadge + ' &nbsp;' +
                        '<span class="badge bg-warning text-dark">' + dateStr + '</span><br>' +
                        '<small class="text-muted">' + (order.customer_name || '') + ' &bull; ' + itemsCount + ' ' + window.POS_TRANS.items_unit + '</small>' +
                    '</div>' +
                    '<div class="d-flex align-items-center gap-2">' +
                        '<strong class="text-success">' + total + '</strong>' +
                        (order.server_id ? '<button class="btn btn-sm btn-outline-primary rpos-recall-held" data-id="' + order.server_id + '"><i class="fas fa-undo"></i></button>' : '') +
                        '<button class="btn btn-sm btn-outline-danger rpos-delete-held" data-id="' + (order.server_id || '') + '" data-local-id="' + (order.local_id || '') + '"><i class="fas fa-trash"></i></button>' +
                    '</div>' +
                '</div>' +
            '</div>';
        });
        html += '</div>';
        list.html(html);
    }

    function loadHeldOrders() {
        const list = $('#heldOrdersList');
        list.html('<div class="text-center py-4"><i class="fas fa-spinner fa-spin fa-2x text-muted"></i></div>');
        const db = window._rposDb;

        function mergeAndRender(serverOrders) {
            if (!db) { renderHeldOrdersList(serverOrders); return; }
            db.getHeldOrders().then(function (localOrders) {
                const localOnly = (localOrders || []).filter(function (l) { return !l.server_id; });
                const merged = serverOrders.concat(localOnly);
                renderHeldOrdersList(merged);
                if (serverOrders.length > 0 && db) {
                    serverOrders.forEach(function (o) {
                        db.saveHeldOrder(Object.assign({}, o, { local_id: 'srv_' + o.id, server_id: o.id, sync_status: 'synced' })).catch(function () {});
                    });
                }
            }).catch(function () { renderHeldOrdersList(serverOrders); });
        }

        if (window.rposIsOnline) {
            $.get(window.RPOS_CONFIG.routes.heldOrders, function (res) {
                mergeAndRender(res.success && res.held_orders ? res.held_orders : []);
            }).fail(function () {
                if (db) {
                    db.getHeldOrders().then(renderHeldOrdersList).catch(function () {
                        list.html('<div class="alert alert-danger m-3">' + window.POS_TRANS.load_error + '</div>');
                    });
                } else {
                    list.html('<div class="alert alert-danger m-3">' + window.POS_TRANS.load_error + '</div>');
                }
            });
        } else {
            if (db) {
                db.getHeldOrders().then(renderHeldOrdersList).catch(function () {
                    list.html('<div class="text-center py-4 text-muted">' + window.POS_TRANS.no_data_offline + '</div>');
                });
            } else {
                list.html('<div class="text-center py-4 text-muted">' + window.POS_TRANS.db_unavailable + '</div>');
            }
        }
    }

    // حذف فاتورة معلقة (محلي + سيرفر)
    $(document).on('click', '.rpos-delete-held', function () {
        const serverId = $(this).data('id');
        const localId = $(this).data('local-id');
        if (!confirm(window.POS_TRANS.delete_confirm)) { return; }
        const db = window._rposDb;
        if (localId && db) { db.deleteHeldOrder(localId).catch(function () {}); }
        if (serverId && window.rposIsOnline) {
            $.ajax({
                url: window.RPOS_CONFIG.routes.deleteHeldOrder.replace(':id', serverId),
                method: 'DELETE',
                headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
                complete: function () { loadHeldOrders(); }
            });
        } else {
            loadHeldOrders();
        }
    });

    // استرجاع فاتورة معلقة
    $(document).on('click', '.rpos-recall-held', function () {
        const id = $(this).data('id');
        if (!id || !window.rposIsOnline) {
            showToast(window.POS_TRANS.recall_requires_online, 'warning');
            return;
        }
        $.get(window.RPOS_CONFIG.routes.recallOrder.replace(':id', id), function (res) {
            if (res.success) {
                bootstrap.Modal.getInstance(document.getElementById('heldOrdersModal'))?.hide();
                showToast(window.POS_TRANS.recall_success, 'success');
            }
        }).fail(function () { showToast(window.POS_TRANS.recall_error, 'error'); });
    });

    // تعليق الطلب الحالي من نافذة الدفع
    $('#holdOrderBtn').on('click', function () {
        if (!window.rCart || window.rCart.length === 0) { return; }
        if (!confirm(window.POS_TRANS.confirm_hold_order)) { return; }
        const db = window._rposDb;
        const orderData = {
            items: window.rCart,
            notes: '',
            customer_name: '',
            total: window.rCart.reduce(function (s, i) { return s + (i.price * i.quantity); }, 0),
        };

        if (window.rposIsOnline) {
            $.ajax({
                url: window.RPOS_CONFIG.routes.holdOrder,
                method: 'POST',
                headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
                data: { items: window.rCart, notes: '' },
                success: function (res) {
                    if (res.success) {
                        if (db) {
                            db.saveHeldOrder(Object.assign({}, orderData, {
                                local_id: 'srv_' + res.held_order_id,
                                server_id: res.held_order_id,
                                sync_status: 'synced'
                            })).catch(function () {});
                        }
                        bootstrap.Modal.getInstance(document.getElementById('paymentModal'))?.hide();
                        showToast(window.POS_TRANS.hold_success, 'success');
                    }
                },
                error: function () {
                    if (db) {
                        db.saveHeldOrder(orderData).then(function () {
                            bootstrap.Modal.getInstance(document.getElementById('paymentModal'))?.hide();
                            showToast(window.POS_TRANS.hold_success + ' (' + window.POS_TRANS.offline_badge + ')', 'info');
                        }).catch(function () { showToast(window.POS_TRANS.hold_error, 'error'); });
                    }
                }
            });
        } else {
            if (db) {
                db.saveHeldOrder(orderData).then(function () {
                    bootstrap.Modal.getInstance(document.getElementById('paymentModal'))?.hide();
                    showToast(window.POS_TRANS.hold_success + ' (' + window.POS_TRANS.offline_badge + ')', 'info');
                }).catch(function () { showToast(window.POS_TRANS.hold_error, 'error'); });
            } else {
                showToast(window.POS_TRANS.db_unavailable, 'error');
            }
        }
    });

    // ===== RECENT TRANSACTIONS MODAL =====
    const recentModalEl = document.getElementById('recentTransactionsModal');
    if (recentModalEl) {
        recentModalEl.addEventListener('show.bs.modal', function () { loadRecentTransactions(); });
    }
    $('#refreshRecentTransactionsBtn').on('click', function () { loadRecentTransactions(); });

    function loadRecentTransactions() {
        const list = $('#recentTransactionsList');
        list.html('<div class="text-center py-4"><i class="fas fa-spinner fa-spin fa-2x text-muted"></i></div>');
        const db = window._rposDb;

        function renderTransactions(transactions) {
            if (!transactions || transactions.length === 0) {
                list.html('<div class="text-center py-4 text-muted"><i class="fas fa-inbox fa-3x mb-3 d-block opacity-25"></i>' + window.POS_TRANS.no_operations + '</div>');
                return;
            }
            let html = '<div class="table-responsive"><table class="table table-hover table-sm">';
            html += '<thead class="table-light"><tr>' +
                '<th>#</th>' +
                '<th>' + window.POS_TRANS.invoice_number_col + '</th>' +
                '<th>' + window.POS_TRANS.date_col + '</th>' +
                '<th>' + window.POS_TRANS.customer_col + '</th>' +
                '<th>' + window.POS_TRANS.total_col + '</th>' +
                '<th></th>' +
            '</tr></thead><tbody>';
            transactions.forEach(function (t, i) {
                const printUrl = window.RPOS_CONFIG.routes.print.replace(':id', t.id);
                const showUrl  = '{{ route("pos.show", ":id") }}'.replace(':id', t.id);
                html += '<tr>' +
                    '<td>' + (i + 1) + '</td>' +
                    '<td><strong>' + t.pro_id + '</strong></td>' +
                    '<td><small>' + new Date(t.created_at).toLocaleString('ar-SA') + '</small></td>' +
                    '<td>' + (t.customer_name || '') + '</td>' +
                    '<td class="text-success fw-bold">' + parseFloat(t.total).toFixed(2) + '</td>' +
                    '<td>' +
                        '<a href="' + showUrl + '" target="_blank" class="btn btn-xs btn-outline-primary btn-sm"><i class="fas fa-eye"></i></a> ' +
                        '<a href="' + printUrl + '" target="_blank" class="btn btn-xs btn-outline-secondary btn-sm"><i class="fas fa-print"></i></a>' +
                    '</td>' +
                '</tr>';
            });
            html += '</tbody></table></div>';
            list.html(html);
        }

        if (window.rposIsOnline) {
            $.get(window.RPOS_CONFIG.routes.recentTransactions, { limit: 50 }, function (res) {
                const transactions = res.success && res.transactions ? res.transactions : [];
                renderTransactions(transactions);
                if (db && transactions.length > 0) {
                    db.saveRecentTransactions(transactions).catch(function () {});
                }
            }).fail(function () {
                if (db) {
                    db.getRecentTransactions(50).then(renderTransactions).catch(function () {
                        list.html('<div class="alert alert-warning m-3">' + window.POS_TRANS.load_error + '</div>');
                    });
                } else {
                    list.html('<div class="alert alert-danger m-3">' + window.POS_TRANS.load_error + '</div>');
                }
            });
        } else {
            if (db) {
                db.getRecentTransactions(50).then(function (cached) {
                    if (cached && cached.length > 0) {
                        renderTransactions(cached);
                    } else {
                        list.html('<div class="text-center py-4 text-muted"><i class="fas fa-wifi-slash fa-2x mb-2 d-block"></i>' + window.POS_TRANS.no_data_offline + '</div>');
                    }
                }).catch(function () {
                    list.html('<div class="text-center py-4 text-muted">' + window.POS_TRANS.db_unavailable + '</div>');
                });
            } else {
                list.html('<div class="text-center py-4 text-muted">' + window.POS_TRANS.db_unavailable + '</div>');
            }
        }
    }

    // ===== PENDING TRANSACTIONS MODAL (أوفلاين) =====
    const pendingModalEl = document.getElementById('pendingTransactionsModal');
    if (pendingModalEl) {
        pendingModalEl.addEventListener('show.bs.modal', function () { loadPendingTransactions(); });
    }

    $('#syncAllPendingBtn').on('click', function () {
        const btn = $(this);
        btn.prop('disabled', true);
        // ping السيرفر أولاً قبل القرار (مش نعتمد على rposIsOnline القديم)
        const pingUrl = window.RPOS_CONFIG.routes.ping;
        $.get(pingUrl).done(function () {
            window.rposIsOnline = true;
            window.rposSyncPendingTransactions && window.rposSyncPendingTransactions(true);
            if (window._rposDb && window._rposDb.getPendingPayouts) {
                window._rposDb.getPendingPayouts().then(function (payouts) {
                    (payouts || []).forEach(function (p) {
                        $.ajax({
                            url: window.RPOS_CONFIG.routes.payOut,
                            method: 'POST',
                            headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
                            data: {
                                amount: p.amount,
                                cash_account_id: p.cash_account_id,
                                expense_account_id: p.expense_account_id,
                                description: p.description,
                                notes: p.notes || ''
                            },
                            success: function () {
                                window._rposDb.markPayoutSynced && window._rposDb.markPayoutSynced(p.local_id).catch(function () {});
                            }
                        });
                    });
                }).catch(function () {});
            }
            setTimeout(loadPendingTransactions, 1500);
        }).fail(function () {
            window.rposIsOnline = false;
            showToast(window.POS_TRANS.sync_requires_online, 'warning');
        }).always(function () {
            btn.prop('disabled', false);
        });
    });

    function loadPendingTransactions() {
        const list = $('#pendingTransactionsList');
        const db = window._rposDb;
        if (!db) {
            list.html('<div class="text-center py-4 text-muted">' + window.POS_TRANS.db_unavailable + '</div>');
            return;
        }
        db.getPendingTransactions().then(function (pending) {
            if (!pending || pending.length === 0) {
                list.html('<div class="text-center py-4 text-muted"><i class="fas fa-check-circle fa-3x mb-3 d-block text-success opacity-50"></i>' + window.POS_TRANS.no_pending_transactions + '</div>');
                return;
            }
            let html = '<div class="list-group">';
            pending.forEach(function (tx) {
                const itemsCount = tx.items ? tx.items.length : 0;
                const dateStr = tx.created_at ? new Date(tx.created_at).toLocaleString('ar-SA') : '';
                html += '<div class="list-group-item">' +
                    '<div class="d-flex justify-content-between">' +
                        '<div>' +
                            '<span class="badge bg-warning text-dark me-2">' + window.POS_TRANS.pending_badge + '</span>' +
                            '<small class="text-muted">' + dateStr + '</small><br>' +
                            '<small>' + itemsCount + ' ' + window.POS_TRANS.items_unit + '</small>' +
                        '</div>' +
                        '<strong class="text-warning">' + (tx.local_id ? tx.local_id.substring(0, 8) : '') + '...</strong>' +
                    '</div>' +
                '</div>';
            });
            html += '</div>';
            list.html(html);
        }).catch(function () {
            list.html('<div class="alert alert-danger m-3">' + window.POS_TRANS.load_error + '</div>');
        });
    }

    // ===== NOTES MODAL =====
    const notesModalEl = document.getElementById('notesModal');
    if (notesModalEl) {
        notesModalEl.addEventListener('hidden.bs.modal', function () {
            window._rposInvoiceNotes = $('#invoiceNotes').val() || '';
        });
    }

    // ===== RETURN INVOICE MODAL =====
    $('#searchInvoiceBtn, #returnInvoiceNumber').on('click keypress', function (e) {
        if (e.type === 'keypress' && e.which !== 13) { return; }
        e.preventDefault();
        const proId = $('#returnInvoiceNumber').val();
        if (!proId) {
            showToast(window.POS_TRANS.enter_invoice_number, 'warning');
            return;
        }
        $.get(window.RPOS_CONFIG.routes.searchInvoice.replace(':proId', proId), function (res) {
            if (res.success) {
                const inv = res.invoice;
                $('#invoiceInfo').html(
                    '<div class="card p-3">' +
                        '<p><strong>' + window.POS_TRANS.invoice_number_label + '</strong> ' + inv.pro_id + '</p>' +
                        '<p><strong>' + window.POS_TRANS.invoice_date_label + '</strong> ' + inv.pro_date + '</p>' +
                        '<p><strong>' + window.POS_TRANS.invoice_customer_label + '</strong> ' + (inv.customer_name || '') + '</p>' +
                        '<p><strong>' + window.POS_TRANS.invoice_total_label + '</strong> ' + parseFloat(inv.total).toFixed(2) + '</p>' +
                    '</div>'
                );
                $('#invoiceDetails').data('invoice-id', inv.id).show();
                $('#invoiceError').hide();
            } else {
                $('#invoiceError').text(res.message || window.POS_TRANS.invoice_not_found).show();
                $('#invoiceDetails').hide();
            }
        }).fail(function () {
            $('#invoiceError').text(window.POS_TRANS.search_error).show();
        });
    });

    $('#confirmReturnBtn').on('click', function () {
        const invoiceId = $('#invoiceDetails').data('invoice-id');
        if (!invoiceId) {
            showToast(window.POS_TRANS.search_first, 'warning');
            return;
        }
        if (!confirm(window.POS_TRANS.confirm_return)) { return; }
        const btn = $(this);
        btn.prop('disabled', true).text(window.POS_TRANS.returning);
        $.ajax({
            url: window.RPOS_CONFIG.routes.returnInvoice,
            method: 'POST',
            headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
            data: { original_invoice_id: invoiceId },
            success: function (res) {
                if (res.success) {
                    showToast(window.POS_TRANS.return_success + ' #' + res.return_invoice_number, 'success');
                    bootstrap.Modal.getInstance(document.getElementById('returnInvoiceModal'))?.hide();
                } else {
                    showToast(res.message || window.POS_TRANS.return_error, 'error');
                }
            },
            error: function () { showToast(window.POS_TRANS.return_error, 'error'); },
            complete: function () { btn.prop('disabled', false).text(window.POS_TRANS.confirm_return); }
        });
    });

    // ===== PAY OUT MODAL =====
    $('#submitPayOutBtn').on('click', function () {
        const amount = parseFloat($('#payOutAmount').val());
        const cashAccountId = $('#payOutCashAccount').val();
        const expenseAccountId = $('#payOutExpenseAccount').val();
        const description = $('#payOutDescription').val().trim();

        if (!amount || amount <= 0) { showToast(window.POS_TRANS.enter_valid_amount, 'warning'); return; }
        if (!cashAccountId) { showToast(window.POS_TRANS.select_cash_account, 'warning'); return; }
        if (!expenseAccountId) { showToast(window.POS_TRANS.select_expense_account, 'warning'); return; }
        if (!description) { showToast(window.POS_TRANS.enter_description, 'warning'); return; }

        const btn = $(this);
        btn.prop('disabled', true).text(window.POS_TRANS.processing);

        const payoutData = {
            amount: amount,
            cash_account_id: cashAccountId,
            expense_account_id: expenseAccountId,
            description: description,
            notes: $('#payOutNotes').val() || ''
        };

        if (!window.rposIsOnline) {
            const db = window._rposDb;
            if (db) {
                db.queuePayout(payoutData).then(function () {
                    showToast(window.POS_TRANS.pay_out_success + ' (' + window.POS_TRANS.offline_badge + ')', 'info');
                    bootstrap.Modal.getInstance(document.getElementById('payOutModal'))?.hide();
                    $('#payOutAmount, #payOutDescription, #payOutNotes').val('');
                    btn.prop('disabled', false).text(window.POS_TRANS.submit_pay_out);
                }).catch(function () {
                    showToast(window.POS_TRANS.pay_out_error, 'error');
                    btn.prop('disabled', false).text(window.POS_TRANS.submit_pay_out);
                });
            } else {
                showToast(window.POS_TRANS.db_unavailable, 'error');
                btn.prop('disabled', false).text(window.POS_TRANS.submit_pay_out);
            }
            return;
        }

        $.ajax({
            url: window.RPOS_CONFIG.routes.payOut,
            method: 'POST',
            headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
            data: payoutData,
            success: function (res) {
                if (res.success) {
                    let msg = window.POS_TRANS.pay_out_success;
                    if (res.voucher_number) { msg += ' - ' + window.POS_TRANS.voucher_number_label + ' ' + res.voucher_number; }
                    showToast(msg, 'success');
                    bootstrap.Modal.getInstance(document.getElementById('payOutModal'))?.hide();
                    $('#payOutAmount, #payOutDescription, #payOutNotes').val('');
                } else {
                    showToast(res.message || window.POS_TRANS.pay_out_error, 'error');
                }
            },
            error: function () { showToast(window.POS_TRANS.pay_out_error, 'error'); },
            complete: function () { btn.prop('disabled', false).text(window.POS_TRANS.submit_pay_out); }
        });
    });

});
</script>

<script>
// ===== DIAGNOSTICS MODAL =====
(function () {
    const diagModal = document.getElementById('rposDiagModal');
    if (!diagModal) return;

    const bsDiag = new bootstrap.Modal(diagModal);

    // فتح المودال بالزر أو بالنقر على الدوت
    document.getElementById('rposDiagBtn')?.addEventListener('click', function () {
        runDiagnostics();
        bsDiag.show();
    });
    document.getElementById('rposOnlineStatus')?.addEventListener('click', function () {
        runDiagnostics();
        bsDiag.show();
    });
    document.getElementById('rposDiagRefreshBtn')?.addEventListener('click', runDiagnostics);
    document.getElementById('rposDiagSyncBtn')?.addEventListener('click', function () {
        const btn = this;
        btn.disabled = true;
        const pingUrl = window.RPOS_CONFIG.routes.ping;
        $.get(pingUrl).done(function () {
            window.rposIsOnline = true;
            window.rposSyncPendingTransactions && window.rposSyncPendingTransactions(true);
            setTimeout(runDiagnostics, 1500);
        }).fail(function () {
            showDiagError('السيرفر غير متاح — تعذّرت المزامنة');
        }).always(function () { btn.disabled = false; });
    });

    async function runDiagnostics() {
        const content = document.getElementById('rposDiagContent');
        content.innerHTML = '<div class="text-center py-4"><i class="fas fa-spinner fa-spin fa-2x text-muted"></i></div>';

        // 1. ping السيرفر
        let pingOk = false;
        let pingMs = 0;
        try {
            const t0 = performance.now();
            const ctrl = new AbortController();
            const timeout = setTimeout(() => ctrl.abort(), 4000);
            const res = await fetch(window.RPOS_CONFIG.routes.ping, {
                cache: 'no-store', credentials: 'include', signal: ctrl.signal
            });
            clearTimeout(timeout);
            pingMs = Math.round(performance.now() - t0);
            pingOk = res.ok;
        } catch (e) {
            pingOk = false;
        }

        // تحديث الـ state الفعلي
        window.rposIsOnline = pingOk;

        // 2. IndexedDB state
        const db = window._rposDb;
        let pending = [], held = [], payouts = [], recent = [];
        if (db) {
            try { pending  = await db.getPendingTransactions(); } catch(e) {}
            try { held     = await db.getHeldOrders();          } catch(e) {}
            try { payouts  = await db.getPendingPayouts ? await db.getPendingPayouts() : []; } catch(e) {}
            try { recent   = await db.getRecentTransactions(5); } catch(e) {}
        }

        const pendingCount  = (pending  || []).filter(t => !t.server_id).length;
        const heldCount     = (held     || []).filter(h => !h.server_id).length;
        const payoutsCount  = (payouts  || []).filter(p => p.status === 'pending').length;

        // 3. Service Worker
        let swStatus = 'غير مدعوم';
        if ('serviceWorker' in navigator) {
            const regs = await navigator.serviceWorker.getRegistrations();
            swStatus = regs.length > 0 ? 'مسجّل (' + regs.length + ')' : 'غير مسجّل';
        }

        // 4. IndexedDB support
        const idbOk = !!window.indexedDB;

        // بناء الـ HTML
        const row = (label, val, ok) =>
            `<tr>
                <td class="text-muted" style="width:45%">${label}</td>
                <td><span class="badge ${ok === true ? 'bg-success' : ok === false ? 'bg-danger' : 'bg-secondary'}">${val}</span></td>
            </tr>`;

        const warn = (count, label) => count > 0
            ? `<span class="badge bg-warning text-dark">${count} ${label}</span>`
            : `<span class="badge bg-success">لا يوجد</span>`;

        content.innerHTML = `
        <table class="table table-sm table-bordered mb-0">
            <tbody>
                <tr class="table-dark"><td colspan="2"><strong>🌐 الاتصال بالسيرفر</strong></td></tr>
                ${row('Ping إلى السيرفر', pingOk ? `متصل (${pingMs}ms)` : 'غير متصل', pingOk)}
                ${row('window.rposIsOnline', window.rposIsOnline ? 'true' : 'false', window.rposIsOnline)}
                ${row('navigator.onLine', navigator.onLine ? 'true' : 'false', navigator.onLine)}
                ${row('Ping URL', '<small class="text-break">' + window.RPOS_CONFIG.routes.ping + '</small>', null)}

                <tr class="table-dark"><td colspan="2"><strong>💾 IndexedDB</strong></td></tr>
                ${row('IndexedDB مدعوم', idbOk ? 'نعم' : 'لا', idbOk)}
                ${row('DB مفتوحة', db ? 'نعم' : 'لا', !!db)}
                ${row('فواتير معلقة (غير مزامنة)', warn(pendingCount, 'فاتورة'), pendingCount === 0)}
                ${row('Hold orders محلية', warn(heldCount, 'طلب'), heldCount === 0)}
                ${row('مصروفات نثرية معلقة', warn(payoutsCount, 'مصروف'), payoutsCount === 0)}
                ${row('آخر 5 معاملات في الكاش', recent.length + ' سجل', null)}

                <tr class="table-dark"><td colspan="2"><strong>⚙️ Service Worker</strong></td></tr>
                ${row('حالة SW', swStatus, swStatus.includes('مسجّل'))}

                <tr class="table-dark"><td colspan="2"><strong>🔧 الإعدادات</strong></td></tr>
                ${row('CSRF Token', window.RPOS_CONFIG.csrfToken ? 'موجود' : 'مفقود', !!window.RPOS_CONFIG.csrfToken)}
                ${row('Default Customer ID', window.RPOS_CONFIG.defaultCustomerId || 'غير محدد', !!window.RPOS_CONFIG.defaultCustomerId)}
            </tbody>
        </table>
        <div class="p-2 text-muted" style="font-size:11px">آخر تحديث: ${new Date().toLocaleTimeString('ar-SA')}</div>`;
    }

    function showDiagError(msg) {
        const content = document.getElementById('rposDiagContent');
        if (content) content.innerHTML = `<div class="alert alert-danger m-3">${msg}</div>`;
    }
})();
</script>
