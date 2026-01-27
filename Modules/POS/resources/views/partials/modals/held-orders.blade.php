{{-- Modal: Held Orders (الفواتير المعلقة) --}}
<div class="modal fade" id="heldOrdersModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content" style="border-radius: 20px; border: none; box-shadow: 0 10px 40px rgba(0,0,0,0.2);">
            <div class="modal-header bg-warning text-dark" style="border-radius: 20px 20px 0 0; border: none; padding: 1.5rem;">
                <h5 class="modal-title fw-bold" style="font-size: 1.5rem;">
                    <i class="fas fa-pause-circle me-2"></i>
                    الفواتير المعلقة
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" style="padding: 2rem;">
                <div id="heldOrdersList" style="max-height: 500px; overflow-y: auto;">
                    <div class="text-center py-4">
                        <i class="fas fa-spinner fa-spin fa-2x text-muted"></i>
                        <p class="mt-2 text-muted">جاري التحميل...</p>
                    </div>
                </div>
            </div>
            <div class="modal-footer bg-light" style="border-radius: 0 0 20px 20px; border: none; padding: 1.5rem;">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal" style="border-radius: 15px; padding: 0.75rem 2rem;">
                    <i class="fas fa-times me-2"></i> إغلاق
                </button>
                <button type="button" class="btn btn-primary" id="refreshHeldOrdersBtn" style="border-radius: 15px; padding: 0.75rem 2rem;">
                    <i class="fas fa-sync-alt me-2"></i> تحديث
                </button>
            </div>
        </div>
    </div>
</div>
