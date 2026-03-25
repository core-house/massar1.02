<!-- Modal {{ __("checks::checks.check_information") }} -->
<div class="modal fade" id="viewModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-info text-white">
                <h5 class="modal-title"><i class="fas fa-eye"></i> {{ __("checks::checks.check_information") }}</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="checkDetails">
                <div class="text-center py-5">
                    <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">{{ __("checks::checks.loading") }}</span>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ __("Close") }}</button>
            </div>
        </div>
    </div>
</div>

<script>
function viewCheck(id) {
    $('#viewModal').modal('show');
    $('#checkDetails').html('<div class="text-center py-5"><div class="spinner-border text-primary" role="status"><span class="visually-hidden">' + __('Loading...') + '</span></div></div>');
    
    $.get(`/checks/${id}`, function(data) {
        const statusLabels = {
            'pending': __('Pending'),
            'cleared': __('Cleared'),
            'bounced': __('Bounced'),
            'cancelled': __('Cancelled')
        };
        
        const typeLabels = {
            'incoming': __('Receipt'),
            'outgoing': __('Payment')
        };
        
        const html = `
            <div class="row g-3">
                <div class="col-md-6">
                    <label class="fw-bold text-muted">${__('Check Number')}:</label>
                    <p class="fs-5">${data.check_number}</p>
                </div>
                <div class="col-md-6">
                    <label class="fw-bold text-muted">${__('Bank')}:</label>
                    <p class="fs-5">${data.bank_name}</p>
                </div>
                <div class="col-md-6">
                    <label class="fw-bold text-muted">${__('Account Number')}:</label>
                    <p>${data.account_number}</p>
                </div>
                <div class="col-md-6">
                    <label class="fw-bold text-muted">${__('Account Holder')}:</label>
                    <p>${data.account_holder_name}</p>
                </div>
                <div class="col-md-6">
                    <label class="fw-bold text-muted">${__('Amount')}:</label>
                    <p class="fs-4 text-primary"><strong>${parseFloat(data.amount).toLocaleString('ar-EG', {minimumFractionDigits: 2})} ${__('SAR')}</strong></p>
                </div>
                <div class="col-md-6">
                    <label class="fw-bold text-muted">${__('Type')}:</label>
                    <p><span class="badge bg-${data.type === 'incoming' ? 'success' : 'info'}">${typeLabels[data.type]}</span></p>
                </div>
                <div class="col-md-4">
                    <label class="fw-bold text-muted">${__('Issue Date')}:</label>
                    <p>${data.issue_date}</p>
                </div>
                <div class="col-md-4">
                    <label class="fw-bold text-muted">${__('Due Date')}:</label>
                    <p>${data.due_date}</p>
                </div>
                <div class="col-md-4">
                    <label class="fw-bold text-muted">${__('Status')}:</label>
                    <p><span class="badge bg-${data.status === 'pending' ? 'warning' : data.status === 'cleared' ? 'success' : data.status === 'bounced' ? 'danger' : 'secondary'}">${statusLabels[data.status]}</span></p>
                </div>
                ${data.payee_name ? `<div class="col-md-6"><label class="fw-bold text-muted">${__('Payee')}:</label><p>${data.payee_name}</p></div>` : ''}
                ${data.payer_name ? `<div class="col-md-6"><label class="fw-bold text-muted">${__('Payer')}:</label><p>${data.payer_name}</p></div>` : ''}
                ${data.reference_number ? `<div class="col-12"><label class="fw-bold text-muted">${__('Reference Number')}:</label><p>${data.reference_number}</p></div>` : ''}
                ${data.notes ? `<div class="col-12"><label class="fw-bold text-muted">${__('Notes')}:</label><p class="border p-2 bg-light">${data.notes}</p></div>` : ''}
                <div class="col-12 border-top pt-3">
                    <small class="text-muted">
                        ${__('Created By')}: ${data.creator ? data.creator.name : __('Unknown')} 
                        ${__('at')} ${new Date(data.created_at).toLocaleString('ar-EG')}
                    </small>
                </div>
            </div>
        `;
        
        $('#checkDetails').html(html);
    }).fail(function() {
        $('#checkDetails').html('<div class="alert alert-danger"><i class="fas fa-exclamation-triangle"></i> ' + __('Error loading data') + '</div>');
    });
}

function editCheck(id) {
    $.get(`/checks/${id}/edit`, function(data) {
        $('#addEditModal').modal('show');
        $('#modalTitle').text(__('Edit Check'));
        $('#formMethod').val('PUT');
        $('#checkForm').attr('action', `/checks/${id}`);
        $('#checkId').val(data.id);
        $('#check_number').val(data.check_number);
        $('#bank_name').val(data.bank_name);
        $('#account_number').val(data.account_number);
        $('#account_holder_name').val(data.account_holder_name);
        $('#amount').val(data.amount);
        $('#issue_date').val(data.issue_date);
        $('#due_date').val(data.due_date);
        $('#status').val(data.status);
        $('#type').val(data.type);
        $('#payee_name').val(data.payee_name || '');
        $('#payer_name').val(data.payer_name || '');
        $('#reference_number').val(data.reference_number || '');
        $('#notes').val(data.notes || '');
    });
}
</script>
