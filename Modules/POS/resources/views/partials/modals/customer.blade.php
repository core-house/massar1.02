{{-- Customer Modal --}}
<div class="modal fade" id="customerModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">{{ __('pos.select_customer') }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label class="form-label">{{ __('pos.customer_label') }}</label>
                    <select id="selectedCustomer" class="form-select">
                        @foreach($clientsAccounts as $client)
                            <option value="{{ $client->id }}" {{ $loop->first ? 'selected' : '' }}>{{ $client->aname }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="alert alert-info" id="customerBalance">
                    <strong>{{ __('pos.customer_balance') }}</strong> <span id="balanceAmount">0.00</span>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ __('pos.cancel') }}</button>
                <button type="button" class="btn btn-primary" data-bs-dismiss="modal">{{ __('pos.save') }}</button>
            </div>
        </div>
    </div>
</div>
