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
                                <i class="fas fa-exchange-alt fa-2x"></i>
                            </div>
                            <div>
                                <h2 class="mb-1 fw-bold header-title">{{ $pageTitle }}</h2>
                                <p class="mb-0 text-white-75 header-subtitle">
                                    {{ __("Create accounting entry for check endorsement") }}
                                </p>
                            </div>
                        </div>
                        <a href="{{ route($check->type === 'incoming' ? 'checks.incoming' : 'checks.outgoing') }}" class="btn btn-light">
                            <i class="fas fa-arrow-right me-2"></i> {{ __("Return") }}
                        </a>
                    </div>
                </div>

                <!-- Form -->
                <form method="POST" action="{{ route('checks.clear', $check) }}">
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
                                        <strong>{{ __("Please correct the following errors:") }}</strong>
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
                                    <p class="mb-1">
                                        <strong>{{ __("From:") }}</strong> {{ __("Incoming Check Portfolio (Credit)") }}
                                    </p>
                                    <p class="mb-0">
                                        <strong>{{ __("To:") }}</strong> {{ __("Bank Account (Credit)") }}
                                    </p>
                                </div>
                            </div>
                        </div>

                        <!-- {{ __("Endorsement Data") }} -->
                        <div class="row">
                            <div class="col-12">
                                <h5 class="mb-3 text-primary">
                                    <i class="fas fa-edit me-2"></i> {{ __("Endorsement Data") }}
                                </h5>
                            </div>

                            <!-- {{ __("Account") }} -->
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold">{{ __("Account") }} <span class="text-danger">*</span></label>
                                <select name="bank_account_id" id="bank_account_id" class="form-select js-tom-select" required>
                                    <option value="">{{ __("Choose account") }}</option>
                                    @foreach($accounts as $account)
                                        <option value="{{ $account->id }}" {{ old('bank_account_id') == $account->id ? 'selected' : '' }}>
                                            {{ $account->aname }} - {{ $account->code }} ({{ __("Balance:") }} {{ number_format($account->balance ?? 0, 2) }})
                                        </option>
                                    @endforeach
                                </select>
                                @error('bank_account_id')
                                    <div class="text-danger small mt-1">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- {{ __("Endorsement Date") }} -->
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold">{{ __("Endorsement Date") }} <span class="text-danger">*</span></label>
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
                                <i class="fas fa-exchange-alt me-2"></i> {{ __("Endorse Check and Create Entry") }}
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
    // Initialize Tom Select for searchable select
    function initTomSelect() {
        const selectElement = document.getElementById('bank_account_id');
        if (selectElement && window.TomSelect && !selectElement.tomselect) {
            const tomSelect = new TomSelect(selectElement, {
                create: false,
                searchField: ['text'],
                sortField: {field: 'text', direction: 'asc'},
                dropdownInput: true,
                placeholder: __('Search and choose account...'),
                maxOptions: 1000,
                allowEmptyOption: true,
            });
            
            // Set z-index for dropdown
            tomSelect.on('dropdown_open', function() {
                const dropdown = selectElement.parentElement.querySelector('.ts-dropdown');
                if (dropdown) {
                    dropdown.style.zIndex = '99999';
                }
            });
        }
    }
    
    // Initialize when document is ready
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initTomSelect);
    } else {
        initTomSelect();
    }
});
</script>
@endpush

