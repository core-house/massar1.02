@extends('admin.dashboard')

@section('sidebar')
    @include('components.sidebar.checks')
@endsection

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card shadow-sm">
                <!-- Header -->
                <div class="card-header bg-gradient-primary text-white py-4">
                    <div class="d-flex align-items-center justify-content-between">
                        <div class="d-flex align-items-center">
                            <div class="icon-shape bg-white text-primary rounded-circle p-3 me-3">
                                <i class="fas fa-check-circle fa-2x"></i>
                            </div>
                            <div>
                                <h2 class="mb-1 fw-bold header-title">{{ $pageTitle }}</h2>
                                <p class="mb-0 text-white-75 header-subtitle">
                                    {{ __('checks::checks.create_accounting_entry_for_collection') }}
                                </p>
                            </div>
                        </div>
                        <a href="{{ route($check->type === 'incoming' ? 'checks.incoming' : 'checks.outgoing') }}" class="btn btn-light">
                            <i class="fas fa-arrow-right me-2"></i> {{ __('checks::checks.return') }}
                        </a>
                    </div>
                </div>

                <!-- Form -->
                <form method="POST" action="{{ route('checks.store-collect', $check) }}">
                    @csrf
                    <input type="hidden" name="branch_id" value="{{ auth()->user()->branch_id ?? 1 }}">

                    <div class="card-body">
                        @if (session('error'))
                            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                <i class="fas fa-exclamation-circle me-2"></i> {{ session('error') }}
                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                            </div>
                        @endif

                        @if ($errors->any())
                            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                <div class="d-flex align-items-center">
                                    <i class="fas fa-exclamation-circle me-2"></i>
                                    <div class="flex-grow-1">
                                        <strong>{{ __('checks::checks.please_correct_the_following_errors:') }}</strong>
                                        <ul class="mb-0 mt-2">
                                            @foreach ($errors->all() as $error)
                                                <li>{{ $error }}</li>
                                            @endforeach
                                        </ul>
                                    </div>
                                </div>
                                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                            </div>
                        @endif

                        <!-- {{ __("Check Information") }} -->
                        <div class="row mb-4">
                            <div class="col-12">
                                <h5 class="mb-3 text-primary">
                                    <i class="fas fa-info-circle me-2"></i> {{ __("checks::checks.check_information") }}
                                </h5>
                            </div>
                            <div class="col-md-6">
                                <div class="card bg-light">
                                    <div class="card-body">
                                        <div class="row g-3">
                                            <div class="col-6">
                                                <label class="form-label fw-bold text-muted">{{ __("checks::checks.check_number") }}:</label>
                                                <p class="mb-0 fw-bold">{{ $check->check_number }}</p>
                                            </div>
                                            <div class="col-6">
                                                <label class="form-label fw-bold text-muted">{{ __("checks::checks.bank_name") }}:</label>
                                                <p class="mb-0">{{ $check->bank_name }}</p>
                                            </div>
                                            <div class="col-6">
                                                <label class="form-label fw-bold text-muted">{{ __("checks::checks.amount") }}:</label>
                                                <p class="mb-0 fw-bold text-primary fs-5">{{ number_format($check->amount, 2) }} {{ __('checks::checks.sar') }}</p>
                                            </div>
                                            <div class="col-6">
                                                <label class="form-label fw-bold text-muted">{{ __("checks::checks.due_date") }}:</label>
                                                <p class="mb-0">{{ $check->due_date->format('Y-m-d') }}</p>
                                            </div>
                                            <div class="col-6">
                                                <label class="form-label fw-bold text-muted">{{ __("checks::checks.account_holder") }}:</label>
                                                <p class="mb-0">{{ $check->account_holder_name }}</p>
                                            </div>
                                            <div class="col-6">
                                                <label class="form-label fw-bold text-muted">{{ __("checks::checks.status") }}:</label>
                                                <p class="mb-0">
                                                    <span class="badge bg-warning">{{ __("checks::checks.pending") }}</span>
                                                </p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="alert alert-info">
                                    <h6 class="fw-bold mb-2">
                                        <i class="fas fa-info-circle me-2"></i> {{ __("checks::checks.accounting_entry") }}
                                    </h6>
                                    @if($check->type === 'incoming')
                                        <p class="mb-1">
                                            <strong>{{ __("checks::checks.from") }}</strong> {{ __("checks::checks.bank_cash_account_debit") }}
                                        </p>
                                        <p class="mb-0">
                                            <strong>{{ __("checks::checks.to") }}</strong> {{ __("checks::checks.incoming_check_portfolio_credit") }}
                                        </p>
                                    @else
                                        <p class="mb-1">
                                            <strong>{{ __("checks::checks.from") }}</strong> {{ __("checks::checks.outgoing_check_portfolio_debit") }}
                                        </p>
                                        <p class="mb-0">
                                            <strong>{{ __("checks::checks.to") }}</strong> {{ __("checks::checks.bank_cash_account_credit") }}
                                        </p>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <!-- {{ __("Collection Data") }} -->
                        <div class="row">
                            <div class="col-12">
                                <h5 class="mb-3 text-primary">
                                    <i class="fas fa-edit me-2"></i> {{ __("checks::checks.collection_data") }}
                                </h5>
                            </div>

                            <!-- {{ __("Account Type") }} -->
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold">{{ __("checks::checks.account_type") }} <span class="text-danger">*</span></label>
                                <select name="account_type" id="account_type" class="form-select" required>
                                    <option value="">{{ __("checks::checks.choose_account_type") }}</option>
                                    <option value="bank" {{ old('checks::checks.account_type') === 'bank' ? 'selected' : '' }}>{{ __("checks::checks.bank") }}</option>
                                    <option value="cash" {{ old('checks::checks.account_type') === 'cash' ? 'selected' : '' }}>{{ __("checks::checks.cash") }}</option>
                                </select>
                                @error('checks::checks.account_type')
                                    <div class="text-danger small mt-1">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- {{ __("Account") }} -->
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold">{{ __("checks::checks.account") }} <span class="text-danger">*</span></label>
                                <select name="account_id" id="account_id" class="form-select" required>
                                    <option value="">{{ __("checks::checks.choose_account") }}</option>
                                </select>
                                @error('account_id')
                                    <div class="text-danger small mt-1">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- {{ __("Collection Date") }} -->
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold">{{ __("checks::checks.collection_date") }} <span class="text-danger">*</span></label>
                                <input type="date" name="collection_date" id="collection_date" 
                                       class="form-control" 
                                       value="{{ old('checks::checks.collection_date', date('Y-m-d')) }}" 
                                       required>
                                @error('checks::checks.collection_date')
                                    <div class="text-danger small mt-1">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="card-footer bg-light">
                        <div class="d-flex justify-content-between">
                            <a href="{{ route($check->type === 'incoming' ? 'checks.incoming' : 'checks.outgoing') }}" class="btn btn-secondary">
                                <i class="fas fa-times me-2"></i> {{ __("checks::checks.cancel") }}
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-check-circle me-2"></i> {{ __("checks::checks.collect_check_and_create_entry") }}
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
$(document).ready(function() {
    const bankAccounts = @json($bankAccounts);
    const cashAccounts = @json($cashAccounts);

    $('#account_type').on('change', function() {
        const accountType = $(this).val();
        const $accountSelect = $('#account_id');
        
        $accountSelect.empty().append('<option value="">{{ __("Choose account") }}</option>');

        if (accountType === 'bank') {
            bankAccounts.forEach(function(account) {
                $accountSelect.append(
                    `<option value="${account.id}">${account.aname} - ${account.code} ({{ __("Balance:") }} ${parseFloat(account.balance).toLocaleString('ar-EG', {minimumFractionDigits: 2})})</option>`
                );
            });
        } else if (accountType === 'cash') {
            cashAccounts.forEach(function(account) {
                $accountSelect.append(
                    `<option value="${account.id}">${account.aname} - ${account.code} ({{ __("Balance:") }} ${parseFloat(account.balance).toLocaleString('ar-EG', {minimumFractionDigits: 2})})</option>`
                );
            });
        }
    });

    // Load accounts if there is old value
    @if(old('account_type'))
        $('#account_type').trigger('change');
        @if(old('account_id'))
            $('#account_id').val('{{ old('account_id') }}');
        @endif
    @endif
});
</script>
@endpush

