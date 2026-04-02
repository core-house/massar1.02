{{-- Notes Modal --}}
<div class="modal fade" id="notesModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered" style="max-width:420px;">
        <div class="modal-content" style="border-radius:16px;border:none;box-shadow:0 12px 40px rgba(0,0,0,.18);overflow:hidden;">

            <div class="modal-header py-3 px-4" style="background:var(--rpos-dark);border:none;">
                <div class="d-flex align-items-center gap-2">
                    <div style="width:32px;height:32px;background:#3b82f6;border-radius:8px;display:flex;align-items:center;justify-content:center;">
                        <i class="fas fa-sticky-note text-white" style="font-size:.85rem;"></i>
                    </div>
                    <h6 class="modal-title fw-bold mb-0 text-white">{{ __('pos.notes_title') }}</h6>
                </div>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>

            <div class="modal-body p-4" style="background:#f8fafc;">
                <label class="form-label small fw-bold text-muted mb-2">{{ __('pos.invoice_notes_label') }}</label>
                <textarea id="invoiceNotes"
                          class="form-control"
                          rows="5"
                          placeholder="{{ __('pos.invoice_notes_placeholder') }}"
                          style="border-radius:10px;border:1.5px solid var(--rpos-border);font-family:'Cairo',sans-serif;resize:none;font-size:.88rem;"></textarea>
            </div>

            <div class="modal-footer py-2 px-4 gap-2" style="border-top:1px solid var(--rpos-border);background:#fff;">
                <button type="button" class="btn btn-sm btn-light fw-bold border" data-bs-dismiss="modal" style="border-radius:8px;">
                    <i class="fas fa-times me-1"></i>{{ __('pos.cancel') }}
                </button>
                <button type="button" class="btn btn-sm btn-primary fw-bold" data-bs-dismiss="modal" style="border-radius:8px;">
                    <i class="fas fa-check me-1"></i>{{ __('pos.save') }}
                </button>
            </div>

        </div>
    </div>
</div>
