@extends('admin.dashboard')

@section('sidebar')
    @include('components.sidebar.quality')
@endsection

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h2 class="mb-0"><i class="fas fa-certificate me-2"></i>{{ __("quality::quality.certificate details") }}</h2>
                </div>
                <div>
                    <a href="{{ route('quality.certificates.index') }}" class="btn btn-secondary me-2">
                        <i class="fas fa-arrow-right me-2"></i>{{ __("quality::quality.back to list") }}
                    </a>
                    @can('edit certificates')
                    <a href="{{ route('quality.certificates.edit', $certificate) }}" class="btn btn-warning">
                        <i class="fas fa-edit me-2"></i>{{ __("quality::quality.edit") }}
                    </a>                        
                    @endcan
                </div>
            </div>
        </div>
    </div>
 
    <div class="row">
        <div class="col-md-8">
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0"><i class="fas fa-info-circle me-2"></i>{{ __("quality::quality.basic information") }}</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">{{ __("quality::quality.certificate number") }}:</label>
                            <p class="mb-0">{{ $certificate->certificate_number }}</p>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">{{ __("quality::quality.certificate name") }}:</label>
                            <p class="mb-0">{{ $certificate->certificate_name }}</p>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">{{ __("quality::quality.certificate type") }}:</label>
                            <p class="mb-0">
                                <span class="badge bg-info">{{ __("quality::quality.certificates") }}</span>
                            </p>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">{{ __("quality::quality.issuing authority") }}:</label>
                            <p class="mb-0">{{ $certificate->issuing_authority }}</p>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">{{ __("quality::quality.issue date") }}:</label>
                            <p class="mb-0">{{ $certificate->issue_date ? $certificate->issue_date->format('Y-m-d') : '---' }}</p>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">{{ __("quality::quality.valid until") }}:</label>
                            <p class="mb-0">{{ $certificate->expiry_date ? $certificate->expiry_date->format('Y-m-d') : '---' }}</p>
                        </div>
                        @if($certificate->scope)
                        <div class="col-12 mb-3">
                            <label class="form-label fw-bold">{{ __("quality::quality.scope") }}:</label>
                            <p class="mb-0">{{ $certificate->scope }}</p>
                        </div>
                        @endif
                        @if($certificate->notes)
                        <div class="col-12 mb-3">
                            <label class="form-label fw-bold">{{ __("quality::quality.notes") }}:</label>
                            <p class="mb-0">{{ $certificate->notes }}</p>
                        </div>
                        @endif
                    </div>
                </div>
            </div>

            @if($certificate->certificate_cost || $certificate->renewal_cost)
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0"><i class="fas fa-dollar-sign me-2"></i>{{ __("quality::quality.costs") }}</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        @if($certificate->certificate_cost)
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">{{ __("quality::quality.certificate cost") }}:</label>
                            <p class="mb-0">{{ number_format($certificate->certificate_cost, 2) }}</p>
                        </div>
                        @endif
                        @if($certificate->renewal_cost)
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">{{ __("quality::quality.renewal cost") }}:</label>
                            <p class="mb-0">{{ number_format($certificate->renewal_cost, 2) }}</p>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
            @endif
        </div>

        <div class="col-md-4">
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0"><i class="fas fa-info-circle me-2"></i>{{ __("quality::quality.status") }}</h5>
                </div>
                <div class="card-body text-center">
                    <div class="mb-3">
                        <span class="badge bg-{{ match($certificate->status) {
                            'active' => 'success',
                            'expired' => 'danger',
                            'renewal_pending' => 'warning',
                            'suspended' => 'dark',
                            default => 'secondary'
                        } }} fs-6 px-3 py-2">
                            {{ match($certificate->status) {
                                'active' => __("quality::quality.active"),
                                'expired' => __("quality::quality.expired certificate"),
                                'renewal_pending' => __("quality::quality.renewal pending"),
                                'suspended' => __("quality::quality.suspended"),
                                default => $certificate->status
                            } }}
                        </span>
                    </div>
                    @if($certificate->expiry_date)
                        @php
                            $daysLeft = $certificate->daysUntilExpiry();
                        @endphp
                        <div class="mb-3">
                            <h4 class="text-{{ $daysLeft < 0 ? 'danger' : ($daysLeft < 30 ? 'warning' : 'success') }}">
                                {{ abs($daysLeft) }}
                            </h4>
                            <small class="text-muted">{{ $daysLeft < 0 ? __("quality::quality.days since expiry") : __("quality::quality.days remaining") }}</small>
                        </div>
                        @if($daysLeft < 0)
                        <div class="alert alert-danger">
                            <i class="fas fa-exclamation-triangle me-2"></i>{{ __("quality::quality.expired certificate") }}
                        </div>
                        @elseif($daysLeft < 30)
                        <div class="alert alert-warning">
                            <i class="fas fa-clock me-2"></i>{{ __("quality::quality.expiring soon") }}
                        </div>
                        @endif
                    @endif
                </div>
            </div>

            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0"><i class="fas fa-bell me-2"></i>{{ __("quality::quality.notifications") }}</h5>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label class="form-label fw-bold">{{ __("quality::quality.notification before expiry (days)") }}:</label>
                        <p class="mb-0">{{ $certificate->notification_days }} {{ __("quality::quality.days") }}</p>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">{{ __("quality::quality.enable notifications") }}:</label>
                        <p class="mb-0">
                            <span class="badge bg-{{ $certificate->notify_before_expiry ? 'success' : 'secondary' }}">
                                {{ $certificate->notify_before_expiry ? __("quality::quality.enabled") : __("quality::quality.disabled") }}
                            </span>
                        </p>
                    </div>
                </div>
            </div>

            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0"><i class="fas fa-clock me-2"></i>{{ __("quality::quality.system information") }}</h5>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label class="form-label fw-bold">{{ __("quality::quality.created at") }}:</label>
                        <p class="mb-0">{{ $certificate->created_at ? $certificate->created_at->format('Y-m-d H:i') : '---' }}</p>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">{{ __("quality::quality.last updated") }}:</label>
                        <p class="mb-0">{{ $certificate->updated_at ? $certificate->updated_at->format('Y-m-d H:i') : '---' }}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection