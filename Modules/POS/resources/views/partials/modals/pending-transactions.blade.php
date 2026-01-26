{{-- Modal: Pending Transactions --}}
<div class="modal fade" id="pendingTransactionsModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">المعاملات المحفوظة محلياً</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div id="pendingTransactionsList" style="max-height: 400px; overflow-y: auto;">
                    <div class="text-center py-4">
                        <i class="fas fa-spinner fa-spin fa-2x text-muted"></i>
                        <p class="mt-2 text-muted">جاري التحميل...</p>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">إغلاق</button>
                <button type="button" class="btn btn-primary" id="syncAllPendingBtn">
                    <i class="fas fa-sync-alt me-1"></i> مزامنة الكل
                </button>
            </div>
        </div>
    </div>
</div>
