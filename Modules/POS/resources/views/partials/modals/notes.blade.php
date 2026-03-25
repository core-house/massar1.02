{{-- Notes Modal --}}
<div class="modal fade" id="notesModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">{{ __('pos.notes_title') }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label class="form-label">{{ __('pos.invoice_notes_label') }}</label>
                    <textarea id="invoiceNotes" class="form-control" rows="5" placeholder="{{ __('pos.invoice_notes_placeholder') }}"></textarea>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ __('pos.cancel') }}</button>
                <button type="button" class="btn btn-primary" data-bs-dismiss="modal">{{ __('pos.save') }}</button>
            </div>
        </div>
    </div>
</div>
