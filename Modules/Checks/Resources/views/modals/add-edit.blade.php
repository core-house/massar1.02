<!-- Modal {{ __("Add Incoming Check") }}/{{ __("Edit Check") }} -->
<div class="modal fade" id="addEditModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="modalTitle">{{ __("Add Incoming Check") }}</h5>
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
                            <label class="form-label">{{ __("Check Number") }} <span class="text-danger">*</span></label>
                            <input type="text" name="check_number" id="check_number" class="form-control" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">{{ __("Bank Name") }} <span class="text-danger">*</span></label>
                            <input type="text" name="bank_name" id="bank_name" class="form-control" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">{{ __("Account Number") }} <span class="text-danger">*</span></label>
                            <input type="text" name="account_number" id="account_number" class="form-control" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">{{ __("Account Holder") }} <span class="text-danger">*</span></label>
                            <input type="text" name="account_holder_name" id="account_holder_name" class="form-control" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">{{ __("Amount") }} <span class="text-danger">*</span></label>
                            <input type="number" step="0.01" name="amount" id="amount" class="form-control" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">{{ __("Issue Date") }} <span class="text-danger">*</span></label>
                            <input type="date" name="issue_date" id="issue_date" class="form-control" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">{{ __("Due Date") }} <span class="text-danger">*</span></label>
                            <input type="date" name="due_date" id="due_date" class="form-control" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">{{ __("Status") }}</label>
                            <select name="status" id="status" class="form-select">
                                <option value="pending">{{ __("Pending") }}</option>
                                <option value="cleared">{{ __("Cleared") }}</option>
                                <option value="bounced">{{ __("Bounced") }}</option>
                                <option value="cancelled">{{ __("Cancelled") }}</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">{{ __("Type") }}</label>
                            <select name="type" id="type" class="form-select" onchange="updateAccountLabel()">
                                <option value="incoming" {{ isset($pageType) && $pageType === 'incoming' ? 'selected' : '' }}>{{ __("Receipt") }}</option>
                                <option value="outgoing" {{ isset($pageType) && $pageType === 'outgoing' ? 'selected' : '' }}>{{ __("Payment") }}</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">{{ __("Reference Number") }}</label>
                            <input type="text" name="reference_number" id="reference_number" class="form-control">
                        </div>
                        <div class="col-md-12">
                            <label class="form-label" id="acc_label">{{ __("Account") }} {{ __("Related Account (Customer/Supplier)") }}</label>
                            <select name="acc_id" id="acc_id" class="form-select">
                                <option value="">{{ __("Choose account") }}</option>
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
                                <optgroup label="{{ __('Customers') }}" id="clients_group">
                                    @foreach($clients as $client)
                                        <option value="{{ $client->id }}">{{ $client->aname }} - {{ $client->code }}</option>
                                    @endforeach
                                </optgroup>
                                <optgroup label="{{ __('Suppliers') }}" id="suppliers_group" style="display:none;">
                                    @foreach($suppliers as $supplier)
                                        <option value="{{ $supplier->id }}">{{ $supplier->aname }} - {{ $supplier->code }}</option>
                                    @endforeach
                                </optgroup>
                            </select>
                            <small class="text-muted">{{ __("Optional - can be left empty") }}</small>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">{{ __("Payee Name") }}</label>
                            <input type="text" name="payee_name" id="payee_name" class="form-control">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">{{ __("Payer Name") }}</label>
                            <input type="text" name="payer_name" id="payer_name" class="form-control">
                        </div>
                        <div class="col-12">
                            <label class="form-label">{{ __("Notes") }}</label>
                            <textarea name="notes" id="notes" class="form-control" rows="3"></textarea>
                        </div>
                    </div>
                </div>
                
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="fas fa-times"></i> {{ __("Cancel") }}
                    </button>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i> {{ __("Save Check") }}
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
