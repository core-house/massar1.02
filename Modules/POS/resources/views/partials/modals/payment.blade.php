{{-- Payment Modal --}}
<div class="modal fade" id="paymentModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered" style="max-width:460px">
        <div class="modal-content" style="border-radius:16px;border:none;box-shadow:0 12px 40px rgba(0,0,0,.18);overflow:hidden;">

            <div class="modal-header py-3 px-4" style="background:var(--rpos-dark);border:none;">
                <div class="d-flex align-items-center gap-2">
                    <div style="width:32px;height:32px;background:var(--rpos-accent);border-radius:8px;display:flex;align-items:center;justify-content:center;">
                        <i class="fas fa-cash-register text-white" style="font-size:.85rem;"></i>
                    </div>
                    <h6 class="modal-title fw-bold mb-0 text-white">{{ __('pos.payment_title') }}</h6>
                </div>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>

            <div class="modal-body py-3 px-4" style="background:#f8fafc;">

                {{-- Total --}}
                <div class="mb-3 text-center">
                    <div class="small text-muted fw-semibold mb-1">{{ __('pos.total_amount') }}</div>
                    <div style="background:#fff;border:2px solid var(--rpos-accent);border-radius:12px;padding:.6rem 1rem;">
                        <span id="paymentTotal" style="font-size:2rem;font-weight:800;color:var(--rpos-accent);letter-spacing:.5px;">0.00</span>
                    </div>
                </div>

                {{-- Payment Method --}}
                <div class="mb-3">
                    <label class="form-label small fw-bold text-muted mb-1">
                        <i class="fas fa-credit-card me-1"></i>{{ __('pos.payment_method') }}
                    </label>
                    <select id="paymentMethod" class="form-select form-select-sm" style="border-radius:8px;border:1.5px solid var(--rpos-border);">
                        <option value="cash">💵 {{ __('pos.cash') }}</option>
                        <option value="card">💳 {{ __('pos.card') }}</option>
                        <option value="mixed">💰 {{ __('pos.mixed') }}</option>
                    </select>
                </div>

                {{-- Cash Account --}}
                <div class="mb-3" id="cashAccountDiv">
                    <label class="form-label small fw-bold text-muted mb-1">
                        <i class="fas fa-cash-register me-1 text-success"></i>{{ __('pos.cash_account') }}
                    </label>
                    <select id="cashAccountId" class="form-select form-select-sm" style="border-radius:8px;border:1.5px solid var(--rpos-border);">
                        @if(isset($cashAccounts))
                            @foreach($cashAccounts as $cashAccount)
                                @if($cashAccount->id)
                                    <option value="{{ $cashAccount->id }}" {{ $loop->first ? 'selected' : '' }}>{{ $cashAccount->aname }}</option>
                                @endif
                            @endforeach
                        @endif
                    </select>
                </div>

                {{-- Bank Account --}}
                <div class="mb-3" id="bankAccountDiv" style="display:none;">
                    <label class="form-label small fw-bold text-muted mb-1">
                        <i class="fas fa-university me-1 text-info"></i>{{ __('pos.bank_account') }}
                    </label>
                    <select id="bankAccountId" class="form-select form-select-sm" style="border-radius:8px;border:1.5px solid var(--rpos-border);">
                        @if(isset($bankAccounts))
                            @foreach($bankAccounts as $bankAccount)
                                @if($bankAccount->id)
                                    <option value="{{ $bankAccount->id }}" {{ $loop->first ? 'selected' : '' }}>{{ $bankAccount->aname }}</option>
                                @endif
                            @endforeach
                        @endif
                    </select>
                </div>

                {{-- Amount Paid --}}
                <div class="mb-3" id="cashAmountDiv">
                    <label class="form-label small fw-bold text-muted mb-1">
                        <i class="fas fa-money-bill-wave me-1 text-success"></i>{{ __('pos.amount_paid') }}
                    </label>
                    <input type="number" id="cashAmount" class="form-control text-center fw-bold"
                           step="0.01" min="0" placeholder="0.00"
                           style="border-radius:8px;font-size:1.4rem;border:1.5px solid var(--rpos-border);"
                           onclick="this.select()">
                    <div class="rpos-quick-amounts mt-2" id="rposQuickAmounts"></div>
                </div>

                {{-- Card Amount --}}
                <div class="mb-3" id="cardAmountDiv" style="display:none;">
                    <label class="form-label small fw-bold text-muted mb-1">
                        <i class="fas fa-credit-card me-1 text-info"></i>{{ __('pos.card_amount') }}
                    </label>
                    <input type="number" id="cardAmount" class="form-control text-center fw-bold"
                           step="0.01" min="0" placeholder="0.00"
                           style="border-radius:8px;font-size:1.4rem;border:1.5px solid var(--rpos-border);">
                </div>

                {{-- Change --}}
                <div id="changeAmountDiv" style="display:none!important;">
                    <div style="background:#f0fdf4;border:1.5px solid #86efac;border-radius:10px;padding:.6rem .85rem;display:flex;align-items:center;gap:.6rem;">
                        <i class="fas fa-coins text-success"></i>
                        <div>
                            <div class="small fw-bold text-success">{{ __('pos.change_for_customer') }}</div>
                            <div class="fw-bold" style="font-size:1.2rem;color:var(--rpos-accent);"><span id="changeAmount">0.00</span></div>
                        </div>
                    </div>
                </div>

            </div>

            {{-- Print Options --}}
            {{-- moved outside modal to cart sidebar --}}

            <div class="modal-footer py-2 px-4 gap-2" style="border-top:1px solid var(--rpos-border);background:#fff;">
                <button type="button" id="holdOrderBtn" class="btn btn-sm fw-bold"
                        style="border-radius:8px;background:#fef3c7;color:#92400e;border:1.5px solid #fcd34d;">
                    <i class="fas fa-pause me-1"></i>{{ __('pos.hold_invoice') }}
                </button>
                <button type="button" class="btn btn-sm btn-light fw-bold border"
                        data-bs-dismiss="modal" style="border-radius:8px;">
                    <i class="fas fa-times me-1"></i>{{ __('pos.cancel') }}
                </button>
                <button type="button" id="rposConfirmPayBtn" class="btn btn-sm btn-primary fw-bold"
                        style="border-radius:8px;min-width:110px;">
                    <i class="fas fa-check me-1"></i><span id="rposConfirmPayLabel">{{ __('pos.save_only') ?? 'تأكيد' }}</span>
                </button>
            </div>

        </div>
    </div>
</div>
