{{-- Modal: Recent Transactions --}}
<div class="modal fade" id="recentTransactionsModal" tabindex="-1">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="fas fa-history me-2"></i> آخر 50 عملية
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div id="recentTransactionsList" style="max-height: 600px; overflow-y: auto;">
                    <div class="text-center py-4">
                        <i class="fas fa-spinner fa-spin fa-2x text-muted"></i>
                        <p class="mt-2 text-muted">جاري التحميل...</p>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">إغلاق</button>
                <button type="button" class="btn btn-primary" id="refreshRecentTransactionsBtn">
                    <i class="fas fa-sync-alt me-1"></i> تحديث
                </button>
            </div>
        </div>
    </div>
</div>
