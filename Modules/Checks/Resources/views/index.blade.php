@extends('admin.dashboard')

@section('title', __('checks::checks.checks_management'))

{{-- Dynamic Sidebar: Display only checks and accounts --}}
@section('sidebar')
    @include('components.sidebar.checks')
@endsection

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <!-- رسائل النجاح والأخطاء -->
            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <i class="fas fa-check-circle"></i> {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif
            @if(session('error'))
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <i class="fas fa-exclamation-circle"></i> {{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            <div class="card shadow-sm">
                <!-- Header كبير وواضح -->
                <div class="card-header bg-gradient-{{ $pageType === 'incoming' ? 'success' : 'danger' }} text-white py-4">
                    <div class="d-flex align-items-center justify-content-between">
                        <div class="d-flex align-items-center">
                            <div class="icon-shape bg-white text-{{ $pageType === 'incoming' ? 'success' : 'danger' }} rounded-circle p-3 me-3">
                                <i class="fas fa-{{ $pageType === 'incoming' ? 'arrow-circle-down' : 'arrow-circle-up' }} fa-2x"></i>
                            </div>
                            <div>
                                <h1 class="mb-1 fw-bold header-title text-white">{{ $pageTitle }}</h1>
                                <p class="mb-0 text-white-75 header-subtitle">

                                </p>
                            </div>
                        </div>
                        <!-- التبديل بين الأنواع -->
                        <div class="btn-group" role="group">
                            <a href="{{ route('checks.incoming') }}" class="btn btn-{{ $pageType === 'incoming' ? 'light' : 'outline-light' }} btn-lg">
                                <i class="fas fa-arrow-circle-down me-2"></i> {{ __('checks::checks.incoming_checks') }}
                            </a>
                            <a href="{{ route('checks.outgoing') }}" class="btn btn-{{ $pageType === 'outgoing' ? 'light' : 'outline-light' }} btn-lg">
                                <i class="fas fa-arrow-circle-up me-2"></i> {{ __('checks::checks.outgoing_checks') }}
                            </a>
                        </div>
                    </div>
                </div>

                <!-- شريط العمليات - أزرار كبيرة وموحدة -->
                <div class="card-body border-bottom bg-light py-4">
                    <div class="row g-3">
                        @can('create Checks')
                            <div class="col-auto">
                                <a href="{{ route('checks.' . $pageType . '.create') }}" class="btn btn-lg btn-light shadow-sm px-4 py-3">
                                    <i class="fas fa-plus fa-lg text-success me-2"></i>
                                    <span class="fw-bold">{{ $pageType === 'incoming' ? __('checks::checks.add_incoming_check') : __('checks::checks.add_outgoing_check') }}</span>
                                </a>
                            </div>
                        @endcan
                        @can('view Checks')
                            <div class="col-auto">
                                <button type="button" class="btn btn-lg btn-light shadow-sm px-4 py-3" onclick="openSelectedCheck()">
                                    <i class="fas fa-folder-open fa-lg text-info me-2"></i>
                                    <span class="fw-bold">{{ __('checks::checks.open_selected_check') }}</span>
                                </button>
                            </div>
                        @endcan
                        <div class="col-auto">
                            <button type="button" class="btn btn-lg btn-light shadow-sm px-4 py-3" onclick="exportToExcel()">
                                <i class="fas fa-file-excel fa-lg text-success me-2"></i>
                                <span class="fw-bold">{{ __('checks::checks.export_excel') }}</span>
                            </button>
                        </div>
                        <div class="col-auto">
                            <button type="button" class="btn btn-lg btn-light shadow-sm px-4 py-3" onclick="window.print()">
                                <i class="fas fa-print fa-lg text-secondary me-2"></i>
                                <span class="fw-bold">{{ __('checks::checks.print') }}</span>
                            </button>
                        </div>
                    </div>
                </div>

                <!-- {{ __("checks::checks.search") }} Filters -->
                <div class="card-body">
                    <form method="GET" action="{{ route('checks.' . $pageType) }}" id="filterForm">
                        <!-- First Row -->
                        <div class="row g-3 mb-3">
                            <div class="col-md-2">
                                <label class="form-label fw-bold">{{ __("checks::checks.general_search") }}</label>
                                <input type="text" name="search" class="form-control"
                                       placeholder="{{ __('checks::checks.check_number_bank_or_name') }}"
                                       value="{{ request('search') }}">
                            </div>
                            <div class="col-md-2">
                                <label class="form-label fw-bold">{{ __("checks::checks.check_status") }}</label>
                                <select name="status" class="form-select">
                                    <option value="">{{ __("checks::checks.all") }}</option>
                                    <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>{{ __("checks::checks.pending") }}</option>
                                    <option value="cleared" {{ request('status') == 'cleared' ? 'selected' : '' }}>{{ __("checks::checks.cleared") }}</option>
                                    <option value="bounced" {{ request('status') == 'bounced' ? 'selected' : '' }}>{{ __("checks::checks.bounced") }}</option>
                                    <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>{{ __("checks::checks.cancelled") }}</option>
                                </select>
                            </div>
                            <div class="col-md-2">
                                <label class="form-label fw-bold">{{ __("checks::checks.bank") }}</label>
                                <input type="text" name="bank_name" class="form-control"
                                       placeholder="{{ __('checks::checks.bank_name') }}"
                                       value="{{ request('bank_name') }}">
                            </div>
                            <div class="col-md-2">
                                <label class="form-label fw-bold">{{ __("checks::checks.account_number") }}</label>
                                <input type="text" name="account_number" class="form-control"
                                       placeholder="{{ __('checks::checks.account_number') }}"
                                       value="{{ request('account_number') }}">
                            </div>
                            <div class="col-md-2">
                                <label class="form-label fw-bold">{{ __("checks::checks.payee") }}</label>
                                <input type="text" name="payee_name" class="form-control"
                                       placeholder="{{ __('checks::checks.payee_name') }}"
                                       value="{{ request('payee_name') }}">
                            </div>
                            <div class="col-md-2">
                                <label class="form-label fw-bold">{{ __("checks::checks.payer") }}</label>
                                <input type="text" name="payer_name" class="form-control"
                                       placeholder="{{ __('checks::checks.payer_name') }}"
                                       value="{{ request('payer_name') }}">
                            </div>
                        </div>

                        <!-- Second Row -->
                        <div class="row g-3 mb-3">
                            <div class="col-md-2">
                                <label class="form-label fw-bold">{{ __("checks::checks.from_due_date") }}</label>
                                <input type="date" name="start_date" class="form-control" value="{{ request('start_date') }}">
                            </div>
                            <div class="col-md-2">
                                <label class="form-label fw-bold">{{ __("checks::checks.to_due_date") }}</label>
                                <input type="date" name="end_date" class="form-control" value="{{ request('end_date') }}">
                            </div>
                            <div class="col-md-2">
                                <label class="form-label fw-bold">{{ __("checks::checks.from_issue_date") }}</label>
                                <input type="date" name="issue_date_from" class="form-control" value="{{ request('issue_date_from') }}">
                            </div>
                            <div class="col-md-2">
                                <label class="form-label fw-bold">{{ __("checks::checks.to_issue_date") }}</label>
                                <input type="date" name="issue_date_to" class="form-control" value="{{ request('issue_date_to') }}">
                            </div>
                            <div class="col-md-2">
                                <label class="form-label fw-bold">{{ __("checks::checks.amount_from") }}</label>
                                <input type="number" name="amount_min" class="form-control" step="0.01"
                                       placeholder="0.00" value="{{ request('amount_min') }}">
                            </div>
                            <div class="col-md-2">
                                <label class="form-label fw-bold">{{ __("checks::checks.amount_to") }}</label>
                                <input type="number" name="amount_max" class="form-control" step="0.01"
                                       placeholder="0.00" value="{{ request('amount_max') }}">
                            </div>
                        </div>

                        <!-- Third Row - Quick Filter -->
                        <div class="row g-3">
                            <div class="col-md-3">
                                <label class="form-label fw-bold">{{ __("checks::checks.quick_filter_up_to_days") }}</label>
                                <select name="days_ahead" class="form-select">
                                    <option value="">{{ __("checks::checks.choose") }}</option>
                                    <option value="7" {{ request('days_ahead') == '7' ? 'selected' : '' }}>{{ __("checks::checks.up_to_7_days") }}</option>
                                    <option value="15" {{ request('days_ahead') == '15' ? 'selected' : '' }}>{{ __("checks::checks.up_to_15_days") }}</option>
                                    <option value="30" {{ request('days_ahead') == '30' ? 'selected' : '' }}>{{ __("checks::checks.up_to_30_days") }}</option>
                                    <option value="60" {{ request('days_ahead') == '60' ? 'selected' : '' }}>{{ __("checks::checks.up_to_60_days") }}</option>
                                    <option value="90" {{ request('days_ahead') == '90' ? 'selected' : '' }}>{{ __("checks::checks.up_to_90_days") }}</option>
                                </select>
                            </div>
                            <div class="col-md-3 align-self-end">
                                <button type="submit" class="btn btn-primary w-100">
                                    <i class="fas fa-search"></i> {{ __("checks::checks.search") }}
                                </button>
                            </div>
                            <div class="col-md-3 align-self-end">
                                <a href="{{ route('checks.' . $pageType) }}" class="btn btn-secondary w-100">
                                    <i class="fas fa-redo"></i> {{ __("checks::checks.reset") }}
                                </a>
                            </div>
                        </div>
                    </form>
                </div>

                <!-- Checks Table -->
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover table-striped mb-0" id="checksTable">
                            <thead class="">
                                <tr>
                                    <th width="30">
                                        <input type="checkbox" id="selectAll" class="form-check-input">
                                    </th>
                                    <th>{{ __('checks::checks.check_number') }}</th>
                                    <th>{{ __('checks::checks.bank') }}</th>
                                    <th>{{ __('checks::checks.account_number') }}</th>
                                    <th>{{ __('checks::checks.amount') }}</th>
                                    <th>{{ __('checks::checks.due_date') }}</th>
                                    <th>{{ __('checks::checks.status') }}</th>
                                    <th>{{ __('checks::checks.type') }}</th>
                                    <th>{{ __('checks::checks.account_holder') }}</th>
                                    <th>{{ __('checks::checks.account_name') }}</th>
                                    <th>{{ __('checks::checks.opposite_account') }}</th>
                                    <th>{{ __('checks::checks.actions') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($checks as $check)
                                    <tr class="{{ $check->isOverdue() ? 'table-warning' : '' }}" data-check-id="{{ $check->id }}">
                                        <td>
                                            <input type="checkbox" class="form-check-input check-item" value="{{ $check->id }}">
                                        </td>
                                        <td>
                                            <strong>{{ $check->check_number }}</strong>
                                            @if($check->reference_number)
                                                <br><small class="text-muted">{{ __('checks::checks.reference') }}: {{ $check->reference_number }}</small>
                                            @endif
                                        </td>
                                        <td>
                                            {{ $check->bank_name }}
                                        </td>
                                        <td>{{ $check->account_number }}</td>
                                        <td><strong class="text-primary">{{ number_format($check->amount, 2) }} {{ __('checks::checks.sar') }}</strong></td>
                                        <td>
                                            {{ $check->due_date->format('Y-m-d') }}
                                            @if($check->isOverdue())
                                                <br><small class="text-danger"><i class="fas fa-exclamation-triangle"></i> {{ __('checks::checks.overdue') }}</small>
                                            @endif
                                        </td>
                                        <td>
                                            <span class="badge bg-{{ $check->status_color }}">
                                                @if($check->status == 'pending') {{ __('checks::checks.pending') }}
                                                @elseif($check->status == 'cleared') {{ __('checks::checks.cleared') }}
                                                @elseif($check->status == 'bounced') {{ __('checks::checks.bounced') }}
                                                @else {{ __('checks::checks.cancelled') }}
                                                @endif
                                            </span>
                                        </td>
                                        <td>
                                            <span class="badge bg-{{ $check->type === 'incoming' ? 'success' : 'info' }}">
                                                {{ $check->type === 'incoming' ? __('checks::checks.receipt') : __('checks::checks.payment') }}
                                            </span>
                                        </td>
                                        <td>{{ $check->account_holder_name }}</td>
                                        <td>
                                            {{ $check->operation?->acc1Head?->aname ?? '-' }}
                                        </td>
                                        <td>
                                            {{ $check->operation?->acc2Head?->aname ?? $check->customer?->aname ?? $check->supplier?->aname ?? '-' }}
                                        </td>
                                        <td>
                                            <div class="btn-group btn-group-sm">
                                                @can('view Checks')
                                                    <button class="btn btn-outline-info" onclick="viewCheck({{ $check->id }})" title="{{ __('checks::checks.view') }}">
                                                        <i class="fas fa-eye"></i>
                                                    </button>
                                                @endcan
                                                @can('edit Checks')
                                                    <button class="btn btn-outline-primary" onclick="editCheck({{ $check->id }})" title="{{ __('checks::checks.edit') }}">
                                                        <i class="fas fa-edit"></i>
                                                    </button>
                                                @endcan
                                                @if($check->status == 'pending')
                                                    @can('edit Checks')
                                                        <a href="{{ route('checks.collect', $check) }}" class="btn btn-outline-success" title="{{ __('checks::checks.collect') }}">
                                                            <i class="fas fa-check"></i>
                                                        </a>
                                                        <a href="{{ route('checks.show-clear', $check) }}" class="btn btn-outline-warning" title="{{ __('checks::checks.endorse_check') }}">
                                                            <i class="fas fa-exchange-alt"></i>
                                                        </a>
                                                        <a href="{{ route('checks.show-cancel-reversal', $check) }}" class="btn btn-outline-danger" title="{{ __('checks::checks.cancel_check_with_reversal') }}">
                                                            <i class="fas fa-ban"></i>
                                                        </a>
                                                    @endcan
                                                @endif
                                                @can('delete Checks')
                                                    <button class="btn btn-outline-danger" onclick="deleteCheck({{ $check->id }})" title="{{ __('checks::checks.delete') }}">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                @endcan
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="12" class="text-center py-5">
                                            <i class="fas fa-inbox fa-3x text-muted mb-3 d-block"></i>
                                            <p class="text-muted">{{ __('checks::checks.no_checks_matching') }}</p>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Footer with info and pagination -->
                <div class="card-footer">
                    <div class="row align-items-center">
                        <div class="col-md-6">
                            <div class="d-flex gap-3">
                                <div><strong>{{ __("checks::checks.number_of_records") }}:</strong> <span class="badge bg-primary">{{ $checks->total() }}</span></div>
                                <div><strong>{{ __("checks::checks.selected") }}:</strong> <span id="selectedCount" class="badge bg-info">0</span></div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            {{ $checks->links() }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Include modals -->
@include('checks::modals.add-edit')
@include('checks::modals.view')
@include('checks::modals.collect')
@endsection

@push('styles')
<style>
/* تحسينات التصميم */
.bg-gradient-primary {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
}

.bg-gradient-success {
    background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%);
}

.bg-gradient-danger {
    background: linear-gradient(135deg, #eb3349 0%, #f45c43 100%);
}

.icon-shape {
    width: 60px;
    height: 60px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
}

.header-title {
    font-family: 'Cairo', 'Segoe UI', Tahoma, sans-serif;
    font-size: 2rem;
    font-weight: 800;
    letter-spacing: -0.5px;
    text-shadow: 0 2px 4px rgba(0,0,0,0.1);
}

.header-subtitle {
    font-family: 'Cairo', 'Segoe UI', Tahoma, sans-serif;
    font-size: 1rem;
    font-weight: 500;
    opacity: 0.9;
}

.text-white-75 {
    color: rgba(255, 255, 255, 0.75) !important;
}

.btn-light {
    background-color: #ffffff;
    border: 2px solid #e9ecef;
    transition: all 0.3s ease;
}

.btn-light:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 20px rgba(0,0,0,0.15) !important;
    border-color: #dee2e6;
}

.btn-light:active {
    transform: translateY(0);
}

.card {
    border: none;
    border-radius: 15px;
    overflow: hidden;
}

.card-header {
    border: none;
}

.table-dark th {
    background-color: #2d3748;
    border-color: #2d3748;
    font-weight: 600;
}

.shadow-sm {
    box-shadow: 0 2px 8px rgba(0,0,0,0.08) !important;
}
</style>
@endpush

@push('scripts')
<script>
$(document).ready(function() {
    // Select all checkboxes
    $('#selectAll').on('change', function() {
        $('.check-item').prop('checked', this.checked);
        updateSelectedCount();
    });

    // Individual checkbox
    $('.check-item').on('change', function() {
        updateSelectedCount();
        $('#selectAll').prop('checked', $('.check-item:checked').length === $('.check-item').length);
    });

    function updateSelectedCount() {
        const count = $('.check-item:checked').length;
        $('#selectedCount').text(count);
    }
});

function openAddModal() {
    // سيتم تنفيذها في ملف المودال
    $('#addEditModal').modal('show');
    $('#modalTitle').text('{{ __("checks::checks.add_incoming_check") }}');
    $('#checkForm')[0].reset();
    $('#checkId').val('');
}

function editCheck(id) {
    // Load check data and open modal
    $.get(`/checks/${id}/edit`, function(data) {
        $('#addEditModal').modal('show');
        $('#modalTitle').text('{{ __("checks::checks.edit_check") }}');
        
        // Fill form fields
        $('#checkId').val(data.id);
        $('#checkForm').attr('action', `/checks/${data.id}`);
        $('#formMethod').val('PUT');
        
        $('#checkForm #check_number').val(data.check_number);
        $('#checkForm #bank_name').val(data.bank_name);
        $('#checkForm #account_number').val(data.account_number);
        $('#checkForm #account_holder_name').val(data.account_holder_name);
        $('#checkForm #amount').val(data.amount);
        
        // Set formatted dates
        if (data.issue_date) $('#checkForm #issue_date').val(data.issue_date.split('T')[0]);
        else $('#checkForm #issue_date').val('');
        
        if (data.due_date) $('#checkForm #due_date').val(data.due_date.split('T')[0]);
        else $('#checkForm #due_date').val('');
        
        $('#checkForm #status').val(data.status);
        $('#checkForm #type').val(data.type).trigger('change');
        $('#checkForm #reference_number').val(data.reference_number);
        $('#checkForm #payee_name').val(data.payee_name);
        $('#checkForm #payer_name').val(data.payer_name);
        $('#checkForm #notes').val(data.notes);
    }).fail(function(xhr) {
        alert('{{ __("checks::checks.error_loading_data") }}');
    });
}

function viewCheck(id) {
    $('#viewModal').modal('show');
    $('#checkDetails').html('<div class="text-center py-5"><div class="spinner-border text-primary" role="status"><span class="visually-hidden">{{ __("checks::checks.loading") }}</span></div></div>');
    
    $.get(`/checks/${id}`, function(data) {
        const statusLabels = {
            'pending': '{{ __("checks::checks.pending") }}',
            'cleared': '{{ __("checks::checks.cleared") }}',
            'bounced': '{{ __("checks::checks.bounced") }}',
            'cancelled': '{{ __("checks::checks.cancelled") }}'
        };
        
        const typeLabels = {
            'incoming': '{{ __("checks::checks.receipt") }}',
            'outgoing': '{{ __("checks::checks.payment") }}'
        };
        
        const html = `
            <div class="row g-3">
                <div class="col-md-6">
                    <label class="fw-bold text-muted">{{ __('checks::checks.check_number') }}:</label>
                    <p class="fs-5">${data.check_number || ''}</p>
                </div>
                <div class="col-md-6">
                    <label class="fw-bold text-muted">{{ __("checks::checks.bank") }}:</label>
                    <p class="fs-5">${data.bank_name || ''}</p>
                </div>
                <div class="col-md-6">
                    <label class="fw-bold text-muted">{{ __('checks::checks.account_number') }}:</label>
                    <p>${data.account_number || ''}</p>
                </div>
                <div class="col-md-6">
                    <label class="fw-bold text-muted">{{ __("checks::checks.account_holder") }}:</label>
                    <p>${data.account_holder_name || ''}</p>
                </div>
                <div class="col-md-6">
                    <label class="fw-bold text-muted">{{ __("checks::checks.amount") }}:</label>
                    <p class="fs-4 text-primary"><strong>${parseFloat(data.amount || 0).toLocaleString('ar-EG', {minimumFractionDigits: 2})} {{ __('checks::checks.sar') }}</strong></p>
                </div>
                <div class="col-md-6">
                    <label class="fw-bold text-muted">{{ __('checks::checks.type') }}:</label>
                    <p><span class="badge bg-${data.type === 'incoming' ? 'success' : 'info'}">${typeLabels[data.type] || data.type}</span></p>
                </div>
                <div class="col-md-4">
                    <label class="fw-bold text-muted">{{ __('checks::checks.issue_date') }}:</label>
                    <p>${data.issue_date || ''}</p>
                </div>
                <div class="col-md-4">
                    <label class="fw-bold text-muted">{{ __("checks::checks.due_date") }}:</label>
                    <p>${data.due_date || ''}</p>
                </div>
                <div class="col-md-4">
                    <label class="fw-bold text-muted">{{ __("checks::checks.status") }}:</label>
                    <p><span class="badge bg-${data.status === 'pending' ? 'warning' : data.status === 'cleared' ? 'success' : data.status === 'bounced' ? 'danger' : 'secondary'}">${statusLabels[data.status] || data.status}</span></p>
                </div>
                ${data.payment_date ? `<div class="col-md-4"><label class="fw-bold text-muted">{{ __('checks::checks.payment_date') }}:</label><p>${data.payment_date}</p></div>` : ''}
                ${data.payee_name ? `<div class="col-md-6"><label class="fw-bold text-muted">{{ __('checks::checks.payee_name') }}:</label><p>${data.payee_name}</p></div>` : ''}
                ${data.payer_name ? `<div class="col-md-6"><label class="fw-bold text-muted">{{ __('checks::checks.payer_name') }}:</label><p>${data.payer_name}</p></div>` : ''}
                ${data.reference_number ? `<div class="col-12"><label class="fw-bold text-muted">{{ __('checks::checks.reference_number') }}:</label><p>${data.reference_number}</p></div>` : ''}
                ${data.notes ? `<div class="col-12"><label class="fw-bold text-muted">{{ __('checks::checks.notes') }}:</label><p class="border p-2 bg-light">${data.notes}</p></div>` : ''}
            </div>
        `;
        
        $('#checkDetails').html(html);
    }).fail(function(xhr) {
        let errorMessage = '{{ __("checks::checks.error_loading_data") }}';
        if (xhr.responseJSON && xhr.responseJSON.message) {
            errorMessage = xhr.responseJSON.message;
        }
        $('#checkDetails').html(`<div class="alert alert-danger"><i class="fas fa-exclamation-triangle"></i> ${errorMessage}</div>`);
    });
}

function clearCheck(id) {
    // فتح modal التحصيل لشيك واحد
    $('#selectedChecksInfo').html(`
        <div class="alert alert-success">
            <strong>{{ __('checks::checks.number_of_selected_checks') }}</strong> 1
        </div>
    `);

    $('#collectModal').modal('show');

    $('#collectForm').off('submit').on('submit', function(e) {
        e.preventDefault();

        const formData = {
            _token: '{{ csrf_token() }}',
            bank_account_id: $('#bank_account_id').val(),
            collection_date: $('#collection_date').val(),
            branch_id: $('input[name="branch_id"]').val()
        };

        $.post(`/checks/${id}/clear`, formData, function(response) {
            $('#collectModal').modal('hide');
            if(response.success) {
                location.reload();
            } else {
                alert(response.message || '{{ __("checks::checks.error_occurred") }}');
            }
        }).fail(function(xhr) {
            alert('{{ __("checks::checks.error_occurred") }} ' + (xhr.responseJSON?.message || '{{ __("checks::checks.connection_error") }}'));
        });
    });
}

function deleteCheck(id) {
    if(confirm('{{ __("checks::checks.confirm_delete") }}')) {
        $.ajax({
            url: `/checks/${id}`,
            type: 'DELETE',
            data: {_token: '{{ csrf_token() }}'},
            success: function() {
                location.reload();
            }
        });
    }
}

function openSelectedCheck() {
    const selected = $('.check-item:checked');
    if(selected.length === 0) {
        alert('{{ __("checks::checks.please_select_at_least_one_check") }}');
        return;
    }
    if(selected.length > 1) {
        alert('{{ __("checks::checks.please_select_only_one_check") }}');
        return;
    }
    viewCheck(selected.val());
}

function collectSelected() {
    const selected = $('.check-item:checked').map(function() {
        return this.value;
    }).get();

    if(selected.length === 0) {
        alert('{{ __("checks::checks.please_select_at_least_one_check") }}');
        return;
    }

    const confirmMsg = `{{ __('checks::checks.confirm_collect_checks', ['count' => 'COUNT']) }}`.replace('COUNT', selected.length);
    if(confirm(confirmMsg)) {
        $.post('/checks/batch-collect', {
            _token: '{{ csrf_token() }}',
            ids: selected
        }, function() {
            location.reload();
        });
    }
}

function endorseSelected() {
    const selected = $('.check-item:checked').map(function() {
        return this.value;
    }).get();

    if(selected.length === 0) {
        alert('{{ __("checks::checks.please_select_at_least_one_check") }}');
        return;
    }

    alert('{{ __("checks::checks.endorsement_under_development") }}');
}

function cancelWithReversalEntry() {
    const selected = $('.check-item:checked').map(function() {
        return this.value;
    }).get();

    if(selected.length === 0) {
        alert('{{ __("checks::checks.please_select_at_least_one_check") }}');
        return;
    }

    const confirmMsg = `{{ __('checks::checks.confirm_cancel_checks', ['count' => 'COUNT']) }}`.replace('COUNT', selected.length);
    if(confirm(confirmMsg)) {
        $.post('/checks/batch-cancel-reversal', {
            _token: '{{ csrf_token() }}',
            ids: selected
        }, function() {
            location.reload();
        });
    }
}

function exportToExcel() {
    window.location.href = '/checks/export?' + $('#filterForm').serialize();
}
</script>
@endpush
