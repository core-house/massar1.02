{{-- Customer Modal --}}
<div class="modal fade" id="customerModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">اختيار العميل</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label class="form-label">العميل</label>
                    <select id="selectedCustomer" class="form-select">
                        @foreach($clientsAccounts as $client)
                            <option value="{{ $client->id }}">{{ $client->aname }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="alert alert-info" id="customerBalance">
                    <strong>رصيد العميل:</strong> <span id="balanceAmount">0.00</span> ريال
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">إلغاء</button>
                <button type="button" class="btn btn-primary" data-bs-dismiss="modal">حفظ</button>
            </div>
        </div>
    </div>
</div>
