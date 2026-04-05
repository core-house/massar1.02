    // مزامنة المعاملات المعلقة
    async function syncPendingTransactions() {
        if (!isOnline) {
            return;
        }

        try {
            const pending = await db.getPendingTransactions();
            const toSync = pending.filter(t => t.sync_status !== 'held' && !t.server_id);
            if (toSync.length === 0) {
                updatePendingCount();
                return;
            }

            const response = await $.ajax({
                url: '{{ route("pos.api.sync") }}',
                method: 'POST',
                contentType: 'application/json',
                data: JSON.stringify({ transactions: toSync }),
                headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') }
            });

            let syncedCount = 0;
            let failedCount = 0;

            for (const s of (response.synced || [])) {
                await db.updateTransactionServerId(s.local_id, s.server_id).catch(() => {});
                syncedCount++;
            }
            failedCount = (response.failed || []).length;

            updatePendingCount();
            if (syncedCount > 0) showToast(`${POS_TRANS.saved_successfully} (${syncedCount})`, 'success');
            if (failedCount > 0) showToast(`${POS_TRANS.save_error} (${failedCount})`, 'warning');
        } catch (err) {
            console.error('Sync error:', err);
        }
    }

    async function updatePendingCount() {
        try {
            const pending = await db.getPendingTransactions();
            const count = pending.length;
            const badge = $('#pendingTransactionsBadge');
            count > 0 ? badge.text(count).show() : badge.hide();
        } catch (err) {
            console.error('Error updating pending count:', err);
        }
    }

    $('#pendingTransactionsModal').on('show.bs.modal', async function() {
        await loadPendingTransactionsList();
    });

    async function loadPendingTransactionsList() {
        const listContainer = $('#pendingTransactionsList');
        listContainer.html(`<div class="text-center py-4"><i class="fas fa-spinner fa-spin fa-2x text-muted"></i><p class="mt-2 text-muted">${POS_TRANS.loading}</p></div>`);

        try {
            const pending = await db.getPendingTransactions();

            if (pending.length === 0) {
                listContainer.html(`<div class="text-center py-4"><i class="fas fa-check-circle fa-2x text-success"></i><p class="mt-2 text-muted">${POS_TRANS.no_pending}</p></div>`);
                return;
            }

            let html = '<div class="list-group">';
            pending.forEach((transaction) => {
                const total = transaction.items ? transaction.items.reduce((sum, item) => sum + (item.quantity * item.price), 0) : 0;
                const date = new Date(transaction.created_at).toLocaleString('ar-SA');
                const statusBadge = transaction.sync_status === 'pending'
                    ? `<span class="badge bg-warning">${POS_TRANS.pending_label}</span>`
                    : `<span class="badge bg-danger">${POS_TRANS.failed_label}</span>`;

                html += `
                    <div class="list-group-item">
                        <div class="d-flex justify-content-between align-items-start">
                            <div>
                                <h6 class="mb-1">${POS_TRANS.transaction_label}${transaction.local_id}</h6>
                                <p class="mb-1 text-muted small">${date}</p>
                                <p class="mb-0"><strong>${POS_TRANS.total_label_js}</strong> ${total.toFixed(2)} ${POS_TRANS.currency}</p>
                                <p class="mb-0 small"><strong>${POS_TRANS.items_label}</strong> ${transaction.items ? transaction.items.length : 0}</p>
                            </div>
                            <div class="text-end">${statusBadge}</div>
                        </div>
                    </div>
                `;
            });
            html += '</div>';
            listContainer.html(html);
        } catch (err) {
            console.error('Error loading pending transactions:', err);
            listContainer.html(`<div class="alert alert-danger">${POS_TRANS.sync_error}</div>`);
        }
    }

    $('#syncAllPendingBtn').on('click', function() {
        const btn = $(this);
        const originalHtml = btn.html();
        btn.prop('disabled', true).html(`<i class="fas fa-spinner fa-spin me-1"></i> ${POS_TRANS.loading}`);

        syncPendingTransactions().then(() => {
            btn.prop('disabled', false).html(originalHtml);
            loadPendingTransactionsList();
            updatePendingCount();
        }).catch(() => {
            btn.prop('disabled', false).html(originalHtml);
        });
    });
