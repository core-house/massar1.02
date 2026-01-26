{{-- Modal: Pay Out (المصروفات النثرية) --}}
<div class="modal fade" id="payOutModal" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content" style="border-radius: 20px; border: none; box-shadow: 0 10px 40px rgba(0,0,0,0.2);">
            <div class="modal-header bg-danger text-white" style="border-radius: 20px 20px 0 0; border: none; padding: 1.5rem;">
                <h5 class="modal-title fw-bold" style="font-size: 1.5rem;">
                    <i class="fas fa-money-bill-wave me-2"></i>
                    مصروف نثري (Pay Out)
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" style="padding: 2rem;">
                {{-- المبلغ --}}
                <div class="mb-4">
                    <label class="form-label fw-bold mb-2" style="color: #333; font-size: 1rem;">
                        <i class="fas fa-money-bill-wave me-2 text-danger"></i>
                        المبلغ <span class="text-danger">*</span>
                    </label>
                    <div class="input-group input-group-lg">
                        <input type="number" 
                               id="payOutAmount" 
                               class="form-control form-control-lg text-center" 
                               step="0.01" 
                               min="0.01"
                               placeholder="0.00"
                               required
                               style="border-radius: 15px; border: 2px solid #e0e0e0; font-size: 1.5rem; font-weight: bold;">
                        <span class="input-group-text bg-light fw-bold" style="border-radius: 15px;">ريال</span>
                    </div>
                </div>

                {{-- الصندوق --}}
                <div class="mb-4">
                    <label class="form-label fw-bold mb-2" style="color: #333; font-size: 1rem;">
                        <i class="fas fa-cash-register me-2 text-success"></i>
                        الصندوق <span class="text-danger">*</span>
                    </label>
                    <select id="payOutCashAccount" class="form-select form-select-lg" required style="border-radius: 15px; border: 2px solid #e0e0e0; padding: 0.75rem 1rem;">
                        <option value="">اختر الصندوق</option>
                        @if(isset($cashAccounts))
                            @foreach($cashAccounts as $cashAccount)
                                <option value="{{ $cashAccount->id }}">{{ $cashAccount->aname }}</option>
                            @endforeach
                        @endif
                    </select>
                </div>

                {{-- حساب المصروف --}}
                <div class="mb-4">
                    <label class="form-label fw-bold mb-2" style="color: #333; font-size: 1rem;">
                        <i class="fas fa-file-invoice-dollar me-2 text-warning"></i>
                        حساب المصروف <span class="text-danger">*</span>
                    </label>
                    <select id="payOutExpenseAccount" class="form-select form-select-lg" required style="border-radius: 15px; border: 2px solid #e0e0e0; padding: 0.75rem 1rem;">
                        <option value="">اختر حساب المصروف</option>
                        @if(isset($expenseAccounts))
                            @foreach($expenseAccounts as $expenseAccount)
                                <option value="{{ $expenseAccount->id }}">{{ $expenseAccount->aname }}</option>
                            @endforeach
                        @endif
                    </select>
                </div>

                {{-- الوصف --}}
                <div class="mb-4">
                    <label class="form-label fw-bold mb-2" style="color: #333; font-size: 1rem;">
                        <i class="fas fa-align-right me-2 text-primary"></i>
                        الوصف <span class="text-danger">*</span>
                    </label>
                    <input type="text" 
                           id="payOutDescription" 
                           class="form-control form-control-lg" 
                           placeholder="مثال: شراء شاي، مصروفات نقل، إلخ..."
                           maxlength="500"
                           required
                           style="border-radius: 15px; border: 2px solid #e0e0e0; padding: 0.75rem 1rem;">
                    <small class="text-muted">وصف مختصر للمصروف (مطلوب)</small>
                </div>

                {{-- الملاحظات --}}
                <div class="mb-4">
                    <label class="form-label fw-bold mb-2" style="color: #333; font-size: 1rem;">
                        <i class="fas fa-sticky-note me-2 text-secondary"></i>
                        ملاحظات (اختياري)
                    </label>
                    <textarea id="payOutNotes" 
                              class="form-control" 
                              rows="3"
                              maxlength="1000"
                              placeholder="أي ملاحظات إضافية..."
                              style="border-radius: 15px; border: 2px solid #e0e0e0; padding: 0.75rem 1rem;"></textarea>
                </div>

                {{-- تحذير --}}
                <div class="alert alert-warning d-flex align-items-center gap-3" style="border-radius: 15px; border: 2px solid #ffc107;">
                    <i class="fas fa-exclamation-triangle fa-2x"></i>
                    <div>
                        <strong>تنبيه:</strong> سيتم خصم المبلغ من الصندوق المحدد وإنشاء سند صرف تلقائياً.
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
                        id="submitPayOutBtn" 
                        class="btn btn-lg btn-danger"
                        style="border-radius: 15px; padding: 0.75rem 2rem; font-weight: bold; background: linear-gradient(135deg, #dc3545 0%, #c82333 100%); border: none; color: white;">
                    <i class="fas fa-check me-2"></i>
                    تسجيل المصروف
                </button>
            </div>
        </div>
    </div>
</div>
