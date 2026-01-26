    // مزامنة المعاملات المعلقة
    async function syncPendingTransactions() {
        if (!isOnline) {
            console.log('Offline - cannot sync');
            return;
        }

        try {
            const pending = await db.getPendingTransactions();
            if (pending.length === 0) {
                console.log('No pending transactions');
                updatePendingCount();
                return;
            }

            console.log('Syncing', pending.length, 'transactions');

            let syncedCount = 0;
            let failedCount = 0;

            for (const transaction of pending) {
                try {
                    const response = await $.ajax({
                        url: '{{ route("pos.api.store") }}',
                        method: 'POST',
                        data: {
                            ...transaction,
                            local_id: transaction.local_id
                        },
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        }
                    });

                    if (response.success && response.server_id) {
                        await db.deleteTransaction(transaction.local_id);
                        syncedCount++;
                        console.log('Synced transaction:', transaction.local_id, '-> server_id:', response.server_id);
                    } else {
                        throw new Error('Sync failed: ' + (response.message || 'Unknown error'));
                    }
                } catch (err) {
                    console.error('Failed to sync transaction:', transaction.local_id, err);
                    await db.updateTransactionStatus(transaction.local_id, 'failed');
                    failedCount++;
                }
            }

            updatePendingCount();
            
            if (syncedCount > 0) {
                showToast(`تمت مزامنة ${syncedCount} معاملة بنجاح`, 'success');
            }
            if (failedCount > 0) {
                showToast(`فشلت مزامنة ${failedCount} معاملة`, 'warning');
            }
        } catch (err) {
            console.error('Sync error:', err);
        }
    }

    async function updatePendingCount() {
        try {
            const pending = await db.getPendingTransactions();
            const count = pending.length;
            const badge = $('#pendingTransactionsBadge');
            
            if (count > 0) {
                badge.text(count).show();
            } else {
                badge.hide();
            }
        } catch (err) {
            console.error('Error updating pending count:', err);
        }
    }

    $('#pendingTransactionsModal').on('show.bs.modal', async function() {
        await loadPendingTransactionsList();
    });

    async function loadPendingTransactionsList() {
        const listContainer = $('#pendingTransactionsList');
        listContainer.html('<div class="text-center py-4"><i class="fas fa-spinner fa-spin fa-2x text-muted"></i><p class="mt-2 text-muted">جاري التحميل...</p></div>');

        try {
            const pending = await db.getPendingTransactions();
            
            if (pending.length === 0) {
                listContainer.html('<div class="text-center py-4"><i class="fas fa-check-circle fa-2x text-success"></i><p class="mt-2 text-muted">لا توجد معاملات معلقة</p></div>');
                return;
            }

            let html = '<div class="list-group">';
            pending.forEach((transaction, index) => {
                const total = transaction.items ? transaction.items.reduce((sum, item) => sum + (item.quantity * item.price), 0) : 0;
                const date = new Date(transaction.created_at).toLocaleString('ar-SA');
                const statusBadge = transaction.sync_status === 'pending' ? '<span class="badge bg-warning">معلق</span>' : '<span class="badge bg-danger">فشل</span>';
                
                html += `
                    <div class="list-group-item">
                        <div class="d-flex justify-content-between align-items-start">
                            <div>
                                <h6 class="mb-1">معاملة #${transaction.local_id}</h6>
                                <p class="mb-1 text-muted small">${date}</p>
                                <p class="mb-0"><strong>المجموع:</strong> ${total.toFixed(2)} ريال</p>
                                <p class="mb-0 small"><strong>عدد الأصناف:</strong> ${transaction.items ? transaction.items.length : 0}</p>
                            </div>
                            <div class="text-end">
                                ${statusBadge}
                            </div>
                        </div>
                    </div>
                `;
            });
            html += '</div>';
            listContainer.html(html);
        } catch (err) {
            console.error('Error loading pending transactions:', err);
            listContainer.html('<div class="alert alert-danger">حدث خطأ أثناء تحميل المعاملات</div>');
        }
    }

    $('#syncAllPendingBtn').on('click', function() {
        const btn = $(this);
        btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin me-1"></i> جاري المزامنة...');
        
        syncPendingTransactions().then(() => {
            btn.prop('disabled', false).html('<i class="fas fa-sync-alt me-1"></i> مزامنة الكل');
            loadPendingTransactionsList();
            updatePendingCount();
        }).catch(() => {
            btn.prop('disabled', false).html('<i class="fas fa-sync-alt me-1"></i> مزامنة الكل');
        });
    });
