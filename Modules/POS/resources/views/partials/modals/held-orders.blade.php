{{-- Modal: Held Orders --}}
<div class="modal fade" id="heldOrdersModal" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content" style="border-radius:16px;border:none;box-shadow:0 12px 40px rgba(0,0,0,.18);overflow:hidden;">

            <div class="modal-header py-3 px-4" style="background:var(--rpos-dark);border:none;">
                <div class="d-flex align-items-center gap-2">
                    <div style="width:32px;height:32px;background:#f59e0b;border-radius:8px;display:flex;align-items:center;justify-content:center;">
                        <i class="fas fa-pause-circle text-white" style="font-size:.85rem;"></i>
                    </div>
                    <h6 class="modal-title fw-bold mb-0 text-white">{{ __('pos.held_invoices') }}</h6>
                </div>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>

            <div class="modal-body p-4" style="background:#f8fafc;">
                <div id="heldOrdersList" style="max-height:460px;overflow-y:auto;">
                    <div class="text-center py-5">
                        <i class="fas fa-spinner fa-spin fa-2x text-muted"></i>
                        <p class="mt-2 text-muted small">{{ __('pos.loading') }}</p>
                    </div>
                </div>
            </div>

            <div class="modal-footer py-2 px-4 gap-2" style="border-top:1px solid var(--rpos-border);background:#fff;">
                <button type="button" class="btn btn-sm btn-light fw-bold border" data-bs-dismiss="modal" style="border-radius:8px;">
                    <i class="fas fa-times me-1"></i>{{ __('pos.close') }}
                </button>
                <button type="button" class="btn btn-sm btn-primary fw-bold" id="refreshHeldOrdersBtn" style="border-radius:8px;">
                    <i class="fas fa-sync-alt me-1"></i>{{ __('pos.refresh') }}
                </button>
            </div>

        </div>
    </div>
</div>
