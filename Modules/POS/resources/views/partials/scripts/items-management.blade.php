// دالة موحدة لإظهار الرسائل (تدعم msg و alert)
function notify(message, type = 'success') {
    console.log(`POS Notification [${type}]: ${message}`);
    if (typeof window.msg === 'function') {
        window.msg(message, type);
    } else {
        alert(message);
    }
}

// زر تحديث الأصناف من السيرفر (يمسح القديم ويحمل الجديد)
$('#refreshItemsBtn').on('click', async function() {
    console.log('Refresh items button clicked');
    const btn = $(this);
    const originalHtml = btn.html();
    
    if (!isOnline) {
        notify(POS_TRANS.must_be_online, 'error');
        return;
    }

    if (!confirm(POS_TRANS.confirm_refresh_items)) {
        return;
    }

    try {
        btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin me-1"></i> ' + POS_TRANS.refreshing);
        console.log('Starting update process...');

        // 1. مسح البيانات القديمة
        itemsCache = {};
        if (db) {
            console.log('Clearing IndexedDB items...');
            await db.clearItems();
        }

        // 2. جلب البيانات الجديدة من السيرفر
        console.log('Fetching new items from server...');
        $.ajax({
            url: '{{ route("pos.api.all-items-details") }}',
            method: 'GET',
            success: async function(response) {
                console.log('Server response received', response);
                if (response && response.items) {
                    itemsCache = response.items;
                    
                    if (db) {
                        const itemsArray = Object.values(response.items);
                        await db.saveItems(itemsArray);
                    }

                    notify(POS_TRANS.items_refreshed, 'success');
                    
                    if (typeof loadAllProducts === 'function') {
                        loadAllProducts();
                    }
                } else {
                    notify(POS_TRANS.invalid_server_response, 'error');
                }
            },
            error: function(xhr, status, error) {
                notify(POS_TRANS.items_refresh_error + ': ' + (error || ''), 'error');
            },
            complete: function() {
                btn.prop('disabled', false).html(originalHtml);
                console.log('Update process completed');
            }
        });

    } catch (error) {
        console.error('Critical Error during update:', error);
        notify(POS_TRANS.critical_error, 'error');
        btn.prop('disabled', false).html(originalHtml);
    }
});
