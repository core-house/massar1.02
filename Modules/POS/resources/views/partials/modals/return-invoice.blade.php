{{-- Return Invoice Modal --}}
<div class="modal fade" id="returnInvoiceModal" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content" style="border-radius: 20px; border: none; box-shadow: 0 10px 40px rgba(0,0,0,0.2);">
            <div class="modal-header bg-warning text-white" style="border-radius: 20px 20px 0 0; border: none; padding: 1.5rem;">
                <h5 class="modal-title fw-bold" style="font-size: 1.5rem;">
                    <i class="fas fa-undo me-2"></i>
                    إرجاع فاتورة
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" style="padding: 2rem;">
                <div class="mb-4">
                    <label class="form-label fw-bold mb-2">
                        <i class="fas fa-search me-2"></i>
                        رقم الفاتورة
                    </label>
                    <div class="input-group">
                        <input type="number" 
                               class="form-control form-control-lg" 
                               id="returnInvoiceNumber" 
                               placeholder="أدخل رقم الفاتورة"
                               autofocus>
                        <button class="btn btn-primary" type="button" id="searchInvoiceBtn">
                            <i class="fas fa-search me-2"></i> بحث
                        </button>
                    </div>
                </div>

                <div id="invoiceDetails" style="display: none;">
                    <hr>
                    <h6 class="fw-bold mb-3">تفاصيل الفاتورة:</h6>
                    <div id="invoiceInfo" class="mb-3"></div>
                    <div class="d-grid">
                        <button class="btn btn-warning btn-lg" id="confirmReturnBtn">
                            <i class="fas fa-undo me-2"></i> تأكيد الإرجاع
                        </button>
                    </div>
                </div>

                <div id="invoiceError" class="alert alert-danger" style="display: none;"></div>
            </div>
        </div>
    </div>
</div>
