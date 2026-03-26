<!-- Modal {{ __('checks::checks.collect_check') }} -->
<div class="modal fade" id="collectModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title"><i class="fas fa-university"></i> {{ __('checks::checks.collect_check') }}</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form id="collectForm">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label fw-bold">{{ __('checks::checks.choose_account') }} <span class="text-danger">*</span></label>
                        <select name="bank_account_id" id="bank_account_id" class="form-select" required>
                            <option value="">{{ __('checks::checks.choose_account') }}</option>
                            @php
                                $banks = \Modules\Accounts\Models\AccHead::where('is_basic', 0)
                                    ->where('isdeleted', 0)
                                    ->where('code', 'like', '1102%')
                                    ->select('id', 'aname', 'code', 'balance')
                                    ->get();
                            @endphp
                            @foreach($banks as $bank)
                                <option value="{{ $bank->id }}">
                                    {{ $bank->aname }} - {{ $bank->code }} 
                                    ({{ __('checks::checks.balance_before') }}: {{ number_format($bank->balance, 2) }})
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold">{{ __('checks::checks.collection_date') }} <span class="text-danger">*</span></label>
                        <input type="date" name="collection_date" id="collection_date" 
                               class="form-control" value="{{ date('Y-m-d') }}" required>
                    </div>

                    <div class="alert alert-info">
                        <i class="fas fa-info-circle"></i>
                        {{ __('checks::checks.selected_checks_transfer_info') }}
                    </div>

                    <div id="selectedChecksInfo"></div>
                    <input type="hidden" name="branch_id" value="{{ auth()->user()->branch_id ?? 1 }}">
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="fas fa-times"></i> {{ __('checks::checks.back') }}
                    </button>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-check"></i> {{ __('checks::checks.collect_now') }}
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
