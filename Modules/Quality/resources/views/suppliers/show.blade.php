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
                        <h2 class="mb-0">
                            <i class="fas fa-star me-2"></i>{{ __('quality::quality.supplier evaluation details') }}
                        </h2>
                    </div>
                    <div>
                        <a href="{{ route('quality.suppliers.index') }}" class="btn btn-secondary me-2">
                            <i class="fas fa-arrow-left me-2"></i>{{ __('quality::quality.back to list') }}
                        </a>
                    </div>
                </div>
            </div>
        </div>
 
        <div class="row">
            <div class="col-md-8">
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="mb-0">
                            <i class="fas fa-info-circle me-2"></i>{{ __('quality::quality.evaluation information') }}
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold">{{ __('quality::quality.supplier') }}:</label>
                                <p class="mb-0">{{ $rating->supplier->aname ?? '---' }}</p>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold">{{ __('quality::quality.period type') }}:</label>
                                <p class="mb-0">
                                    {{ match ($rating->period_type) {
                                        'monthly' => __('quality::quality.monthly'),
                                        'quarterly' => __('quality::quality.quarterly'),
                                        'annual' => __('quality::quality.annual'),
                                        default => $rating->period_type,
                                    } }}
                                </p>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold">{{ __('quality::quality.period start') }}:</label>
                                <p class="mb-0">
                                    {{ $rating->period_start ? $rating->period_start->format('Y-m-d') : '---' }}</p>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold">{{ __('quality::quality.period end') }}:</label>
                                <p class="mb-0">{{ $rating->period_end ? $rating->period_end->format('Y-m-d') : '---' }}
                                </p>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold">{{ __('quality::quality.rating date') }}:</label>
                                <p class="mb-0">
                                    {{ $rating->rating_date ? $rating->rating_date->format('Y-m-d') : '---' }}</p>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold">{{ __('quality::quality.rated by') }}:</label>
                                <p class="mb-0">{{ $rating->ratedBy->name ?? '---' }}</p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="mb-0">
                            <i class="fas fa-chart-bar me-2"></i>{{ __('quality::quality.evaluation scores') }}
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <label class="form-label fw-bold">{{ __('quality::quality.quality score') }}:</label>
                                <div class="progress mb-2" style="height: 25px;">
                                    <div class="progress-bar bg-primary" style="width: {{ $rating->quality_score }}%">
                                        {{ number_format($rating->quality_score, 1) }}%
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label fw-bold">{{ __('quality::quality.delivery score') }}:</label>
                                <div class="progress mb-2" style="height: 25px;">
                                    <div class="progress-bar bg-info" style="width: {{ $rating->delivery_score }}%">
                                        {{ number_format($rating->delivery_score, 1) }}%
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label fw-bold">{{ __('quality::quality.documentation score') }}:</label>
                                <div class="progress mb-2" style="height: 25px;">
                                    <div class="progress-bar bg-success"
                                        style="width: {{ $rating->documentation_score }}%">
                                        {{ number_format($rating->documentation_score, 1) }}%
                                    </div>
                                </div>
                            </div>
                            <div class="col-12 mb-3">
                                <label class="form-label fw-bold">{{ __('quality::quality.overall score') }}:</label>
                                <div class="progress" style="height: 30px;">
                                    <div class="progress-bar bg-{{ $rating->overall_score >= 80 ? 'success' : ($rating->overall_score >= 60 ? 'warning' : 'danger') }}"
                                        style="width: {{ $rating->overall_score }}%">
                                        {{ number_format($rating->overall_score, 1) }}/100
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">
                            <i class="fas fa-chart-line me-2"></i>{{ __('quality::quality.operational metrics') }}
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-3 mb-3">
                                <div class="text-center">
                                    <h4 class="text-primary">{{ $rating->total_inspections }}</h4>
                                    <small class="text-muted">{{ __('quality::quality.total inspections') }}</small>
                                </div>
                            </div>
                            <div class="col-md-3 mb-3">
                                <div class="text-center">
                                    <h4 class="text-success">{{ $rating->passed_inspections }}</h4>
                                    <small class="text-muted">{{ __('quality::quality.passed inspections') }}</small>
                                </div>
                            </div>
                            <div class="col-md-3 mb-3">
                                <div class="text-center">
                                    <h4 class="text-danger">{{ $rating->failed_inspections }}</h4>
                                    <small class="text-muted">{{ __('quality::quality.failed inspections') }}</small>
                                </div>
                            </div>
                            <div class="col-md-3 mb-3">
                                <div class="text-center">
                                    <h4 class="text-warning">{{ $rating->ncrs_raised }}</h4>
                                    <small class="text-muted">{{ __('quality::quality.non-conformance reports') }}</small>
                                </div>
                            </div>
                        </div>
                        @if ($rating->ncrs_raised > 0)
                            <hr>
                            <div class="row">
                                <div class="col-md-4 mb-3">
                                    <div class="text-center">
                                        <h5 class="text-danger">{{ $rating->critical_ncrs }}</h5>
                                        <small class="text-muted">{{ __('quality::quality.critical') }}</small>
                                    </div>
                                </div>
                                <div class="col-md-4 mb-3">
                                    <div class="text-center">
                                        <h5 class="text-warning">{{ $rating->major_ncrs }}</h5>
                                        <small class="text-muted">{{ __('quality::quality.major') }}</small>
                                    </div>
                                </div>
                                <div class="col-md-4 mb-3">
                                    <div class="text-center">
                                        <h5 class="text-info">{{ $rating->minor_ncrs }}</h5>
                                        <small class="text-muted">{{ __('quality::quality.minor') }}</small>
                                    </div>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <div class="col-md-4">
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="mb-0">
                            <i class="fas fa-award me-2"></i>{{ __('quality::quality.final rating') }}
                        </h5>
                    </div>
                    <div class="card-body text-center">
                        <div class="mb-3">
                            <span
                                class="badge bg-{{ match ($rating->rating) {
                                    'excellent' => 'success',
                                    'good' => 'info',
                                    'acceptable' => 'warning',
                                    'poor' => 'danger',
                                    'unacceptable' => 'primary',
                                    default => 'secondary',
                                } }} fs-4 px-4 py-3">
                                {{ match ($rating->rating) {
                                    'excellent' => __('quality::quality.excellent'),
                                    'good' => __('quality::quality.good'),
                                    'acceptable' => __('quality::quality.acceptable'),
                                    'poor' => __('quality::quality.poor'),
                                    'unacceptable' => __('quality::quality.unacceptable'),
                                    default => $rating->rating,
                                } }}
                            </span>
                        </div>
                        <h3 class="mb-0">{{ number_format($rating->overall_score, 1) }}/100</h3>
                    </div>
                </div>

                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="mb-0">
                            <i class="fas fa-check-circle me-2"></i>{{ __('quality::quality.supplier status') }}
                        </h5>
                    </div>
                    <div class="card-body text-center">
                        <span
                            class="badge bg-{{ $rating->supplier_status == 'approved' ? 'success' : 'danger' }} fs-6 px-3 py-2">
                            {{ $rating->supplier_status == 'approved' ? __('quality::quality.approved') : __('quality::quality.not approved') }}
                        </span>
                    </div>
                </div>

                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">
                            <i class="fas fa-clock me-2"></i>{{ __('quality::quality.system information') }}
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label class="form-label fw-bold">{{ __('quality::quality.created') }} {{ __('quality::quality.date') }}:</label>
                            <p class="mb-0">{{ $rating->created_at ? $rating->created_at->format('Y-m-d H:i') : '---' }}
                            </p>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold">{{ __('quality::quality.last updated') }}:</label>
                            <p class="mb-0">{{ $rating->updated_at ? $rating->updated_at->format('Y-m-d H:i') : '---' }}
                            </p>
                        </div>
                        @if ($rating->approvedBy)
                            <div class="mb-3">
                                <label class="form-label fw-bold">{{ __('quality::quality.approved by') }}:</label>
                                <p class="mb-0">{{ $rating->approvedBy->name }}</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
