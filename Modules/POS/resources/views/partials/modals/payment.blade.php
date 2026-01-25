{{-- Payment Modal --}}
<div class="modal fade" id="paymentModal" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content" style="border-radius: 20px; border: none; box-shadow: 0 10px 40px rgba(0,0,0,0.2);">
            <div class="modal-header bg-primary text-white" style="border-radius: 20px 20px 0 0; border: none; padding: 1.5rem;">
                <h5 class="modal-title fw-bold" style="font-size: 1.5rem;">
                    <i class="fas fa-cash-register me-2"></i>
                    الدفع
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" style="padding: 2rem;">
                {{-- الإجمالي --}}
                <div class="mb-4">
                    <label class="form-label fw-bold mb-2" style="color: #333; font-size: 1rem;">
                        <i class="fas fa-calculator me-2 text-primary"></i>
                        الإجمالي
                    </label>
                    <div class="input-group input-group-lg">
                        <input type="text" 
                               class="form-control form-control-lg fw-bold text-center" 
                               id="paymentTotal" 
                               readonly
                               style="font-size: 2rem; color: #27ae60; background: #f8f9fa; border: 2px solid #27ae60; border-radius: 15px;">
                        <span class="input-group-text bg-success text-white fw-bold" style="border: 2px solid #27ae60; border-radius: 15px;">ريال</span>
                    </div>
                </div>

                {{-- طريقة الدفع --}}
                <div class="mb-4">
                    <label class="form-label fw-bold mb-2" style="color: #333; font-size: 1rem;">
                        <i class="fas fa-credit-card me-2 text-primary"></i>
                        طريقة الدفع
                    </label>
                    <select id="paymentMethod" class="form-select form-select-lg" style="border-radius: 15px; border: 2px solid #e0e0e0; padding: 0.75rem 1rem;">
                        <option value="cash">💵 نقدي</option>
                        <option value="card">💳 بطاقة</option>
                        <option value="mixed">💰 مختلط</option>
                    </select>
                </div>

                {{-- المبلغ النقدي --}}
                <div class="mb-4" id="cashAmountDiv">
                    <label class="form-label fw-bold mb-2" style="color: #333; font-size: 1rem;">
                        <i class="fas fa-money-bill-wave me-2 text-success"></i>
                        المبلغ النقدي
                    </label>
                    <div class="input-group input-group-lg">
                        <input type="number" 
                               id="cashAmount" 
                               class="form-control form-control-lg text-center" 
                               step="0.01" 
                               min="0"
                               placeholder="0.00"
                               style="border-radius: 15px; border: 2px solid #e0e0e0; font-size: 1.5rem; font-weight: bold;">
                        <span class="input-group-text bg-light fw-bold" style="border-radius: 15px;">ريال</span>
                    </div>
                </div>

                {{-- مبلغ البطاقة --}}
                <div class="mb-4" id="cardAmountDiv" style="display: none;">
                    <label class="form-label fw-bold mb-2" style="color: #333; font-size: 1rem;">
                        <i class="fas fa-credit-card me-2 text-info"></i>
                        مبلغ البطاقة
                    </label>
                    <div class="input-group input-group-lg">
                        <input type="number" 
                               id="cardAmount" 
                               class="form-control form-control-lg text-center" 
                               step="0.01" 
                               min="0"
                               placeholder="0.00"
                               style="border-radius: 15px; border: 2px solid #e0e0e0; font-size: 1.5rem; font-weight: bold;">
                        <span class="input-group-text bg-light fw-bold" style="border-radius: 15px;">ريال</span>
                    </div>
                </div>

                {{-- المبلغ المتبقي --}}
                <div class="alert alert-success d-flex align-items-center gap-3" 
                     id="changeAmountDiv" 
                     style="display: none; border-radius: 15px; border: 2px solid #27ae60; background: linear-gradient(135deg, #27ae60 0%, #2ecc71 100%); color: white; padding: 1.5rem;">
                    <i class="fas fa-coins fa-2x"></i>
                    <div class="flex-grow-1">
                        <div class="fw-bold mb-1" style="font-size: 1.1rem;">المبلغ المتبقي للعميل</div>
                        <div class="fw-bold" style="font-size: 2rem;">
                            <span id="changeAmount">0.00</span> ريال
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer bg-light" style="border-radius: 0 0 20px 20px; border: none; padding: 1.5rem; gap: 1rem;">
                <button type="button" 
                        class="btn btn-lg btn-secondary" 
                        data-bs-dismiss="modal"
                        style="border-radius: 15px; padding: 0.75rem 2rem; font-weight: bold;">
                    <i class="fas fa-times me-2"></i>
                    إلغاء
                </button>
                <button type="button" 
                        id="saveOnlyBtn" 
                        class="btn btn-lg btn-success"
                        style="border-radius: 15px; padding: 0.75rem 2rem; font-weight: bold; background: linear-gradient(135deg, #27ae60 0%, #2ecc71 100%); border: none;">
                    <i class="fas fa-save me-2"></i>
                    حفظ فقط
                </button>
                <button type="button" 
                        id="saveAndPrintBtn" 
                        class="btn btn-lg btn-primary"
                        style="border-radius: 15px; padding: 0.75rem 2rem; font-weight: bold; background: linear-gradient(135deg, #3498db 0%, #74b9ff 100%); border: none;">
                    <i class="fas fa-print me-2"></i>
                    دفع وطباعة
                </button>
            </div>
        </div>
    </div>
</div>
