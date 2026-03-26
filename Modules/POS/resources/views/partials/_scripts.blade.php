{{-- IndexedDB Helper --}}
<script src="{{ asset('modules/pos/js/pos-indexeddb.js') }}"></script>

{{-- Dark Mode Toggle Script --}}
<script>
    (function() {
        // دالة لتطبيق الوضع الداكن
        function applyDarkMode(isDark) {
            const body = document.body;
            if (isDark) {
                body.classList.add('dark-mode');
            } else {
                body.classList.remove('dark-mode');
            }
        }

        // تهيئة الوضع الداكن عند تحميل الصفحة
        const savedTheme = localStorage.getItem('pos-dark-mode');
        const prefersDark = window.matchMedia('(prefers-color-scheme: dark)').matches;
        const isDark = savedTheme === 'enabled' || (!savedTheme && prefersDark);
        
        applyDarkMode(isDark);

        // إضافة event listener لجميع أزرار toggle
        document.addEventListener('DOMContentLoaded', function() {
            const toggles = document.querySelectorAll('#darkModeToggle');
            
            toggles.forEach(function(toggle) {
                // تعيين الحالة الأولية
                toggle.checked = isDark;
                
                // إضافة event listener للتبديل
                toggle.addEventListener('change', function() {
                    const newDarkState = this.checked;
                    
                    applyDarkMode(newDarkState);
                    
                    // تحديث جميع الـ toggles الأخرى
                    toggles.forEach(function(otherToggle) {
                        if (otherToggle !== toggle) {
                            otherToggle.checked = newDarkState;
                        }
                    });
                    
                    // حفظ التفضيل
                    localStorage.setItem('pos-dark-mode', newDarkState ? 'enabled' : 'disabled');
                });
            });
        });
    })();
</script>

{{-- POS Translations Object - مفاتيح الترجمة للـ JavaScript --}}
<script>
    window.POS_TRANS = {
        // سلة التسوق
        cart_empty_msg:         '{{ __('pos.cart_empty_msg') }}',
        cart_add_hint:          '{{ __('pos.cart_add_hint') }}',
        confirm_remove_item:    '{{ __('pos.confirm_remove_item') }}',
        confirm_reset_cart:     '{{ __('pos.confirm_reset_cart') }}',
        cart_reset_success:     '{{ __('pos.cart_reset_success') }}',
        item_fetch_error:       '{{ __('pos.item_fetch_error') }}',
        invalid_quantity:       '{{ __('pos.invalid_quantity') }}',
        weight_label:           '{{ __('pos.weight_label') }}',
        quantity_label:         '{{ __('pos.quantity_label') }}',
        weight_scale:           '{{ __('pos.weight_scale') }}',
        currency:               '',
        unit_piece:             '{{ __('pos.unit_piece') }}',
        unit_kilo:              '{{ __('pos.unit_kilo') }}',
        cart_empty_alert:       '{{ __('pos.cart_empty_alert') }}',

        // الفاتورة
        saved_locally:          '{{ __('pos.saved_locally') }}',
        saved_successfully:     '{{ __('pos.saved_successfully') }}',
        save_local_error:       '{{ __('pos.save_local_error') }}',
        save_error:             '{{ __('pos.save_error') }}',
        saved_locally_later:    '{{ __('pos.saved_locally_later') }}',

        // المصروف النثري
        enter_valid_amount:     '{{ __('pos.enter_valid_amount') }}',
        select_cash_account:    '{{ __('pos.select_cash_account') }}',
        select_expense_account: '{{ __('pos.select_expense_account') }}',
        enter_description:      '{{ __('pos.enter_description') }}',
        pay_out_success:        '{{ __('pos.pay_out_success') }}',
        pay_out_error:          '{{ __('pos.pay_out_error') }}',
        processing:             '{{ __('pos.processing') }}',

        // إرجاع الفاتورة
        enter_invoice_number:   '{{ __('pos.enter_invoice_number') }}',
        invoice_not_found:      '{{ __('pos.invoice_not_found') }}',
        search_error:           '{{ __('pos.search_error') }}',
        confirm_return:         '{{ __('pos.confirm_return_msg') }}',
        return_success:         '{{ __('pos.return_success') }}',
        return_error:           '{{ __('pos.return_error') }}',
        returning:              '{{ __('pos.returning') }}',
        search_first:           '{{ __('pos.search_first') }}',

        // الأصناف
        must_be_online:         '{{ __('pos.must_be_online') }}',
        confirm_refresh_items:  '{{ __('pos.confirm_refresh_items') }}',
        refreshing:             '{{ __('pos.refreshing') }}',
        items_refreshed:        '{{ __('pos.items_refreshed') }}',
        invalid_server_response:'{{ __('pos.invalid_server_response') }}',
        items_refresh_error:    '{{ __('pos.items_refresh_error') }}',
        critical_error:         '{{ __('pos.critical_error') }}',

        // الميزان
        scale_not_supported:    '{{ __('pos.scale_not_supported') }}',
        scale_disconnected:     '{{ __('pos.scale_disconnected') }}',
        scale_connected:        '{{ __('pos.scale_connected') }}',
        scale_connect_failed:   '{{ __('pos.scale_connect_failed') }}',
        scale_status_connected: '{{ __('pos.scale_status_connected') }}',
        scale_status_disconnected: '{{ __('pos.scale_status_disconnected') }}',
        scale_disconnect_btn:   '{{ __('pos.scale_disconnect_btn') }}',
        scale_connect_btn:      '{{ __('pos.scale_connect_btn') }}',

        // الفواتير المعلقة
        customer_label:         '{{ __('pos.customer_label') }}',
        store_label:            '{{ __('pos.store_label') }}',
        items_count_label:      '{{ __('pos.items_count_label') }}',
        cashier_label:          '{{ __('pos.cashier_label') }}',
        notes_label:            '{{ __('pos.notes_label') }}',
        recall_order:           '{{ __('pos.recall_order') }}',
        complete_order:         '{{ __('pos.complete_order') }}',
        loading:                '{{ __('pos.loading') }}',
        no_held_orders:         '{{ __('pos.no_held_orders') }}',
        held_invoice_label:     '{{ __('pos.held_invoice_label') }}',
        held_orders_load_error: '{{ __('pos.held_orders_load_error') }}',
        confirm_recall_order:   '{{ __('pos.confirm_recall_order') }}',
        recall_success:         '{{ __('pos.recall_success') }}',
        recall_error:           '{{ __('pos.recall_error') }}',
        confirm_complete_order: '{{ __('pos.confirm_complete_order') }}',
        complete_success:       '{{ __('pos.complete_success') }}',
        complete_error:         '{{ __('pos.complete_error') }}',
        confirm_delete_held:    '{{ __('pos.confirm_delete_held') }}',
        delete_held_success:    '{{ __('pos.delete_held_success') }}',
        delete_held_error:      '{{ __('pos.delete_held_error') }}',
        cart_empty_hold:        '{{ __('pos.cart_empty_hold') }}',
        confirm_hold_order:     '{{ __('pos.confirm_hold_order') }}',
        hold_success:           '{{ __('pos.hold_success') }}',
        hold_error:             '{{ __('pos.hold_error') }}',
        held_orders_can_hold_hint: '{{ __('pos.held_orders_can_hold_hint') }}',

        // المعاملات الأخيرة
        invoice_number_col:     '{{ __('pos.invoice_number_col') }}',
        date_col:               '{{ __('pos.date_col') }}',
        customer_col:           '{{ __('pos.customer_col') }}',
        store_col:              '{{ __('pos.store_col') }}',
        user_col:               '{{ __('pos.user_col') }}',
        items_count_col:        '{{ __('pos.items_count_col') }}',
        total_col:              '{{ __('pos.total_col') }}',
        paid_col:               '{{ __('pos.paid_col') }}',
        actions_col:            '{{ __('pos.actions_col') }}',
        no_operations:          '{{ __('pos.no_operations') }}',

        // المزامنة
        pending_label:          '{{ __('pos.pending_label') }}',
        failed_label:           '{{ __('pos.failed_label') }}',
        no_pending:             '{{ __('pos.no_pending') }}',
        transaction_label:      '{{ __('pos.transaction_label') }}',
        total_label_js:         '{{ __('pos.total_label_js') }}',
        items_label:            '{{ __('pos.items_label') }}',
        sync_error:             '{{ __('pos.sync_error') }}',

        // الاتصال
        online_status:          '{{ __('pos.system_online') }}',
        offline_status:         '{{ __('pos.system_offline') }}',

        // تفاصيل المنتج
        name_label:             '{{ __('common.name') }}',
        notes_label_common:     '{{ __('common.notes') }}',
        no_notes:               '{{ __('common.no_notes') }}',
        error_loading_data:     '{{ __('common.error_loading_data') }}',

        // عام
        balance_unavailable:    '{{ __('pos.balance_unavailable') }}',
        voucher_number_label:   '{{ __('pos.voucher_number_label') }}',
        invoice_number_label:   '{{ __('pos.invoice_number_label') }}',
        invoice_date_label:     '{{ __('pos.invoice_date_label') }}',
        invoice_customer_label: '{{ __('pos.invoice_customer_label') }}',
        invoice_total_label:    '{{ __('pos.invoice_total_label') }}',
        invoice_items_label:    '{{ __('pos.invoice_items_label') }}',
    };
</script>

{{-- POS Main Scripts - يتم تنفيذها بعد تحميل jQuery و Bootstrap --}}
@include('pos::partials.scripts.main')
