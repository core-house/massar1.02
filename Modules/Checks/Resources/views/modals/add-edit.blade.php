<!-- Modal {{ __("checks::checks.add_incoming_check") }}/{{ __("checks::checks.edit_check") }} -->
<div class="modal fade" id="addEditModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="modalTitle">{{ __("checks::checks.add_incoming_check") }}</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form id="checkForm" method="POST" action="{{ route('checks.store') }}">
                @csrf
                <input type="hidden" name="_method" id="formMethod" value="POST">
                <input type="hidden" name="id" id="checkId">
                <input type="hidden" name="branch_id" value="{{ auth()->user()->branch_id ?? 1 }}">
                
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">{{ __("checks::checks.check_number") }} <span class="text-danger">*</span></label>
                            <input type="text" name="check_number" id="check_number" class="form-control" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">{{ __("checks::checks.bank_name") }} <span class="text-danger">*</span></label>
                            <input type="text" name="bank_name" id="bank_name" class="form-control" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">{{ __("checks::checks.account_number") }} <span class="text-danger">*</span></label>
                            <input type="text" name="account_number" id="account_number" class="form-control" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">{{ __("checks::checks.account_holder") }} <span class="text-danger">*</span></label>
                            <input type="text" name="account_holder_name" id="account_holder_name" class="form-control" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">{{ __("checks::checks.amount") }} <span class="text-danger">*</span></label>
                            <input type="number" step="0.01" name="amount" id="amount" class="form-control" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">{{ __("checks::checks.issue_date") }} <span class="text-danger">*</span></label>
                            <input type="date" name="issue_date" id="issue_date" class="form-control" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">{{ __("checks::checks.due_date") }} <span class="text-danger">*</span></label>
                            <input type="date" name="due_date" id="due_date" class="form-control" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">{{ __("checks::checks.status") }}</label>
                            <select name="status" id="status" class="form-select">
                                <option value="pending">{{ __("checks::checks.pending") }}</option>
                                <option value="cleared">{{ __("checks::checks.cleared") }}</option>
                                <option value="bounced">{{ __("checks::checks.bounced") }}</option>
                                <option value="cancelled">{{ __("checks::checks.cancelled") }}</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">{{ __("checks::checks.type") }}</label>
                            <select name="type" id="type" class="form-select" onchange="updateAccountLabel()">
                                <option value="incoming" {{ isset($pageType) && $pageType === 'incoming' ? 'selected' : '' }}>{{ __("checks::checks.receipt") }}</option>
                                <option value="outgoing" {{ isset($pageType) && $pageType === 'outgoing' ? 'selected' : '' }}>{{ __("checks::checks.payment") }}</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">{{ __("checks::checks.reference") }}</label>
                            <input type="text" name="reference_number" id="reference_number" class="form-control">
                        </div>
                        <div class="col-md-12">
                            <label class="form-label" id="acc_label">{{ __("checks::checks.account") }} {{ __("checks::checks.related_account") }}</label>
                            <select name="acc_id" id="acc_id" class="form-select">
                                <option value="">{{ __("checks::checks.choose_account") }}</option>
                                @php
                                    $clients = \Modules\Accounts\Models\AccHead::where('is_basic', 0)
                                        ->where('isdeleted', 0)
                                        ->where('code', 'like', '1103%')
                                        ->get();
                                    $suppliers = \Modules\Accounts\Models\AccHead::where('is_basic', 0)
                                        ->where('isdeleted', 0)
                                        ->where('code', 'like', '2101%')
                                        ->get();
                                @endphp
                                <optgroup label="{{ __('checks::checks.customers') }}" id="clients_group">
                                    @foreach($clients as $client)
                                        <option value="{{ $client->id }}">{{ $client->aname }} - {{ $client->code }}</option>
                                    @endforeach
                                </optgroup>
                                <optgroup label="{{ __('checks::checks.suppliers') }}" id="suppliers_group" style="display:none;">
                                    @foreach($suppliers as $supplier)
                                        <option value="{{ $supplier->id }}">{{ $supplier->aname }} - {{ $supplier->code }}</option>
                                    @endforeach
                                </optgroup>
                            </select>
                            <small class="text-muted">{{ __("checks::checks.optional_can_be_empty") }}</small>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">{{ __("checks::checks.payee_name") }}</label>
                            <input type="text" name="payee_name" id="payee_name" class="form-control">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">{{ __("checks::checks.payer_name") }}</label>
                            <input type="text" name="payer_name" id="payer_name" class="form-control">
                        </div>
                        <div class="col-12">
                            <label class="form-label">{{ __("checks::checks.notes") }}</label>
                            <textarea name="notes" id="notes" class="form-control" rows="3"></textarea>
                        </div>
                    </div>
                </div>
                
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="fas fa-times"></i> {{ __("checks::checks.back") }}
                    </button>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i> {{ __("checks::checks.save_check") }}
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function updateAccountLabel() {
    const type = $('#type').val();
    const clientsGroup = $('#clients_group');
    const suppliersGroup = $('#suppliers_group');
    const accLabel = $('#acc_label');
    
    if (type === 'incoming') {
        accLabel.text(__('Customer'));
        clientsGroup.show();
        suppliersGroup.hide();
        // Reset to first client option
        $('#acc_id option').prop('selected', false);
        $('#clients_group option:first').prop('selected', true);
    } else {
        accLabel.text(__('Supplier'));
        clientsGroup.hide();
        suppliersGroup.show();
        // Reset to first supplier option
        $('#acc_id option').prop('selected', false);
        $('#suppliers_group option:first').prop('selected', true);
    }
}

// Initialize on page load
$(document).ready(function() {
    updateAccountLabel();
});
</script>
