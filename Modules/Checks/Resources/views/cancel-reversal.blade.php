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
                <div class="card-header bg-gradient-danger text-white py-4">
                    <div class="d-flex align-items-center justify-content-between">
                        <div class="d-flex align-items-center">
                            <div class="icon-shape bg-white text-danger rounded-circle p-3 me-3">
                                <i class="fas fa-ban fa-2x"></i>
                            </div>
                            <div>
                                <h2 class="mb-1 fw-bold header-title">{{ $pageTitle }}</h2>
                                <p class="mb-0 text-white-75 header-subtitle">
                                    {{ __("Create reversal accounting entry to cancel check") }}
                                </p>
                            </div>
                        </div>
                        <a href="{{ route($check->type === 'incoming' ? 'checks.incoming' : 'checks.outgoing') }}" class="btn btn-light">
                            <i class="fas fa-arrow-right me-2"></i> {{ __("Return") }}
                        </a>
                    </div>
                </div>

                <!-- Form -->
                <form method="POST" action="{{ route('checks.cancel-reversal', $check) }}">
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

                        <!-- {{ __("Warning") }} -->
                        <div class="alert alert-warning">
                            <h5 class="alert-heading">
                                <i class="fas fa-exclamation-triangle me-2"></i> {{ __("Warning") }}
                            </h5>
                            <p class="mb-0">
                                {{ __("This check will be cancelled and a reversal accounting entry will be created for the original entry. This action cannot be undone.") }}
                            </p>
                        </div>

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
                                                    <span class="badge bg-{{ $check->status_color }}">{{ $check->status }}</span>
                                                </p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="alert alert-info">
                                    <h6 class="fw-bold mb-2">
                                        <i class="fas fa-info-circle me-2"></i> {{ __("Reversal Accounting Entry:") }}
                                    </h6>
                                    @if($check->type === 'incoming')
                                        <p class="mb-1">
                                            <strong>{{ __("From:") }}</strong> {{ __("Incoming Check Portfolio (Credit)") }}
                                        </p>
                                        <p class="mb-0">
                                            <strong>{{ __("To:") }}</strong> {{ __("Opposite Account (Debit)") }}
                                        </p>
                                    @else
                                        <p class="mb-1">
                                            <strong>{{ __("From:") }}</strong> {{ __("Opposite Account (Credit)") }}
                                        </p>
                                        <p class="mb-0">
                                            <strong>{{ __("To:") }}</strong> {{ __("Outgoing Check Portfolio (Debit)") }}
                                        </p>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="card-footer bg-light">
                        <div class="d-flex justify-content-between">
                            <a href="{{ route($check->type === 'incoming' ? 'checks.incoming' : 'checks.outgoing') }}" class="btn btn-secondary">
                                <i class="fas fa-times me-2"></i> {{ __("Cancel") }}
                            </a>
                            <button type="submit" class="btn btn-danger" onclick="return confirm(__('Are you sure you want to cancel this check with a reversal entry? This action cannot be undone.'))">
                                <i class="fas fa-ban me-2"></i> {{ __("Cancel with Reversal Entry") }}
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

