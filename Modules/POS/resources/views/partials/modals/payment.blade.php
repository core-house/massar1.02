{{-- Payment Modal --}}
<div class="modal fade" id="paymentModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered" style="max-width:480px">
        <div class="modal-content" style="border-radius:14px;border:none;box-shadow:0 8px 30px rgba(0,0,0,0.18);">
            <div class="modal-header bg-primary text-white py-2 px-3" style="border-radius:14px 14px 0 0;border:none;">
                <h6 class="modal-title fw-bold mb-0">
                    <i class="fas fa-cash-register me-1"></i>
                    {{ __('pos.payment_title') }}
                </h6>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body py-2 px-3">

                {{-- الإجمالي --}}
                <div class="mb-2">
                    <label class="form-label fw-semibold mb-1 small text-muted">
                        <i class="fas fa-calculator me-1 text-primary"></i>{{ __('pos.total_amount') }}
                    </label>
                    <div class="input-group">
                        <input type="text" class="form-control fw-bold text-center fs-4"
                               id="paymentTotal" readonly
                               style="color:#27ae60;background:#f8f9fa;border:2px solid #27ae60;border-radius:10px;">
                    </div>
                </div>

                {{-- طريقة الدفع --}}
                <div class="mb-2">
                    <label class="form-label fw-semibold mb-1 small text-muted">
                        <i class="fas fa-credit-card me-1 text-primary"></i>{{ __('pos.payment_method') }}
                    </label>
                    <select id="paymentMethod" class="form-select form-select-sm" style="border-radius:8px;">
                        <option value="cash">💵 {{ __('pos.cash') }}</option>
                        <option value="card">💳 {{ __('pos.card') }}</option>
                        <option value="mixed">💰 {{ __('pos.mixed') }}</option>
                    </select>
                </div>

                {{-- الصندوق --}}
                <div class="mb-2" id="cashAccountDiv">
                    <label class="form-label fw-semibold mb-1 small text-muted">
                        <i class="fas fa-cash-register me-1 text-success"></i>{{ __('pos.cash_account') }}
                    </label>
                    <select id="cashAccountId" class="form-select form-select-sm" style="border-radius:8px;">
                        @if(isset($cashAccounts))
                            @foreach($cashAccounts as $cashAccount)
                                @if($cashAccount->id)
                                    <option value="{{ $cashAccount->id }}" {{ $loop->first ? 'selected' : '' }}>{{ $cashAccount->aname }}</option>
                                @endif
                            @endforeach
                        @endif
                    </select>
                </div>

                {{-- البنك --}}
                <div class="mb-2" id="bankAccountDiv" style="display:none;">
                    <label class="form-label fw-semibold mb-1 small text-muted">
                        <i class="fas fa-university me-1 text-info"></i>{{ __('pos.bank_account') }}
                    </label>
                    <select id="bankAccountId" class="form-select form-select-sm" style="border-radius:8px;">
                        @if(isset($bankAccounts))
                            @foreach($bankAccounts as $bankAccount)
                                @if($bankAccount->id)
                                    <option value="{{ $bankAccount->id }}" {{ $loop->first ? 'selected' : '' }}>{{ $bankAccount->aname }}</option>
                                @endif
                            @endforeach
                        @endif
                    </select>
                </div>

                {{-- المدفوع --}}
                <div class="mb-2" id="cashAmountDiv">
                    <label class="form-label fw-semibold mb-1 small text-muted">
                        <i class="fas fa-money-bill-wave me-1 text-success"></i>{{ __('pos.amount_paid') }}
                    </label>
                    <input type="number" id="cashAmount" class="form-control text-center fw-bold"
                           step="0.01" min="0" placeholder="0.00"
                           style="border-radius:8px;font-size:1.3rem;"
                           onclick="this.select()">
                    <div class="rpos-quick-amounts mt-1" id="rposQuickAmounts"></div>
                </div>

                {{-- مبلغ البطاقة --}}
                <div class="mb-2" id="cardAmountDiv" style="display:none;">
                    <label class="form-label fw-semibold mb-1 small text-muted">
                        <i class="fas fa-credit-card me-1 text-info"></i>{{ __('pos.card_amount') }}
                    </label>
                    <input type="number" id="cardAmount" class="form-control text-center fw-bold"
                           step="0.01" min="0" placeholder="0.00"
                           style="border-radius:8px;font-size:1.3rem;">
                </div>

                {{-- الباقي --}}
                <div class="alert alert-success d-flex align-items-center gap-2 py-2 px-3 mb-0"
                     id="changeAmountDiv" style="display:none!important;border-radius:10px;">
                    <i class="fas fa-coins"></i>
                    <div>
                        <div class="small fw-semibold">{{ __('pos.change_for_customer') }}</div>
                        <div class="fw-bold fs-5"><span id="changeAmount">0.00</span></div>
                    </div>
                </div>

            </div>
            <div class="modal-footer py-2 px-3 gap-1" style="border-radius:0 0 14px 14px;border-top:1px solid #eee;">
                <button type="button" id="holdOrderBtn" class="btn btn-sm btn-warning fw-bold"
                        style="border-radius:8px;">
                    <i class="fas fa-pause me-1"></i>{{ __('pos.hold_invoice') }}
                </button>
                <button type="button" class="btn btn-sm btn-secondary fw-bold"
                        data-bs-dismiss="modal" style="border-radius:8px;">
                    <i class="fas fa-times me-1"></i>{{ __('pos.cancel') }}
                </button>
                <button type="button" id="saveOnlyBtn" class="btn btn-sm btn-success fw-bold"
                        style="border-radius:8px;">
                    <i class="fas fa-save me-1"></i>{{ __('pos.save_only') }}
                </button>
                <button type="button" id="saveAndPrintBtn" class="btn btn-sm btn-primary fw-bold"
                        style="border-radius:8px;">
                    <i class="fas fa-print me-1"></i>{{ __('pos.pay_and_print') }}
                </button>
            </div>
        </div>
    </div>
</div>
