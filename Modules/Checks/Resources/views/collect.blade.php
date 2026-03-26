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
                                    {{ __('Create accounting entry for check collection') }}
                                </p>
                            </div>
                        </div>
                        <a href="{{ route($check->type === 'incoming' ? 'checks.incoming' : 'checks.outgoing') }}" class="btn btn-light">
                            <i class="fas fa-arrow-right me-2"></i> {{ __('Return') }}
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
                                        <strong>{{ __('Please correct the following errors:') }}</strong>
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
                                    <i class="fas fa-info-circle me-2"></i> {{ __("Check Information") }}
                                </h5>
                            </div>
                            <div class="col-md-6">
                                <div class="card bg-light">
                                    <div class="card-body">
                                        <div class="row g-3">
                                            <div class="col-6">
                                                <label class="form-label fw-bold text-muted">{{ __("Check Number") }}:</label>
                                                <p class="mb-0 fw-bold">{{ $check->check_number }}</p>
                                            </div>
                                            <div class="col-6">
                                                <label class="form-label fw-bold text-muted">{{ __("Bank") }}:</label>
                                                <p class="mb-0">{{ $check->bank_name }}</p>
                                            </div>
                                            <div class="col-6">
                                                <label class="form-label fw-bold text-muted">{{ __("Amount") }}:</label>
                                                <p class="mb-0 fw-bold text-primary fs-5">{{ number_format($check->amount, 2) }} {{ __('SAR') }}</p>
                                            </div>
                                            <div class="col-6">
                                                <label class="form-label fw-bold text-muted">{{ __("Due Date") }}:</label>
                                                <p class="mb-0">{{ $check->due_date->format('Y-m-d') }}</p>
                                            </div>
                                            <div class="col-6">
                                                <label class="form-label fw-bold text-muted">{{ __("Account Holder") }}:</label>
                                                <p class="mb-0">{{ $check->account_holder_name }}</p>
                                            </div>
                                            <div class="col-6">
                                                <label class="form-label fw-bold text-muted">{{ __("Status") }}:</label>
                                                <p class="mb-0">
                                                    <span class="badge bg-warning">{{ __("Pending") }}</span>
                                                </p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="alert alert-info">
                                    <h6 class="fw-bold mb-2">
                                        <i class="fas fa-info-circle me-2"></i> {{ __("Accounting Entry:") }}
                                    </h6>
                                    @if($check->type === 'incoming')
                                        <p class="mb-1">
                                            <strong>{{ __("From:") }}</strong> {{ __("Bank/Cash Account (Debit)") }}
                                        </p>
                                        <p class="mb-0">
                                            <strong>{{ __("To:") }}</strong> {{ __("Incoming Check Portfolio (Credit)") }}
                                        </p>
                                    @else
                                        <p class="mb-1">
                                            <strong>{{ __("From:") }}</strong> {{ __("Outgoing Check Portfolio (Debit)") }}
                                        </p>
                                        <p class="mb-0">
                                            <strong>{{ __("To:") }}</strong> {{ __("Bank/Cash Account (Credit)") }}
                                        </p>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <!-- {{ __("Collection Data") }} -->
                        <div class="row">
                            <div class="col-12">
                                <h5 class="mb-3 text-primary">
                                    <i class="fas fa-edit me-2"></i> {{ __("Collection Data") }}
                                </h5>
                            </div>

                            <!-- {{ __("Account Type") }} -->
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold">{{ __("Account Type") }} <span class="text-danger">*</span></label>
                                <select name="account_type" id="account_type" class="form-select" required>
                                    <option value="">{{ __("Choose account type") }}</option>
                                    <option value="bank" {{ old('account_type') === 'bank' ? 'selected' : '' }}>{{ __("Bank") }}</option>
                                    <option value="cash" {{ old('account_type') === 'cash' ? 'selected' : '' }}>{{ __("Cash") }}</option>
                                </select>
                                @error('account_type')
                                    <div class="text-danger small mt-1">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- {{ __("Account") }} -->
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold">{{ __("Account") }} <span class="text-danger">*</span></label>
                                <select name="account_id" id="account_id" class="form-select" required>
                                    <option value="">{{ __("Choose account") }}</option>
                                </select>
                                @error('account_id')
                                    <div class="text-danger small mt-1">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- {{ __("Collection Date") }} -->
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold">{{ __("Collection Date") }} <span class="text-danger">*</span></label>
                                <input type="date" name="collection_date" id="collection_date" 
                                       class="form-control" 
                                       value="{{ old('collection_date', date('Y-m-d')) }}" 
                                       required>
                                @error('collection_date')
                                    <div class="text-danger small mt-1">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="card-footer bg-light">
                        <div class="d-flex justify-content-between">
                            <a href="{{ route($check->type === 'incoming' ? 'checks.incoming' : 'checks.outgoing') }}" class="btn btn-secondary">
                                <i class="fas fa-times me-2"></i> {{ __("Cancel") }}
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-check-circle me-2"></i> {{ __("Collect Check and Create Entry") }}
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

