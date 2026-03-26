@extends('admin.dashboard')

@section('sidebar')
    @include('components.sidebar.quality')
@endsection

@section('content')
    <div class="container-fluid">
        <!-- Header -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h2 class="mb-1">{{ $standard->standard_name }}</h2>
                        <nav aria-label="breadcrumb">
                            <ol class="breadcrumb">
                                <li class="breadcrumb-item">
                                    <a href="{{ route('quality.dashboard') }}">{{ __('quality::quality.quality') }}</a>
                                </li>
                                <li class="breadcrumb-item">
                                    <a href="{{ route('quality.standards.index') }}">{{ __('quality::quality.quality standards') }}</a>
                                </li>
                                <li class="breadcrumb-item active" aria-current="page">{{ $standard->standard_code }}</li>
                            </ol>
                        </nav>
                    </div>
                    <div>
                        @can('edit standards')
                        <a href="{{ route('quality.standards.edit', $standard) }}" class="btn btn-warning">
                            <i class="fas fa-edit me-2"></i>{{ __('quality::quality.edit') }}
                        </a>
                        @endcan
                        <a href="{{ route('quality.standards.index') }}" class="btn btn-secondary">
                            <i class="fas fa-arrow-left me-2"></i>{{ __('quality::quality.back') }}
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <!-- Basic Information -->
            <div class="col-lg-8">
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="mb-0">
                            <i class="fas fa-info-circle me-2"></i>{{ __('quality::quality.standard information') }}
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="text-muted small">{{ __('quality::quality.standard code') }}</label>
                                <div class="fw-bold">{{ $standard->standard_code }}</div>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="text-muted small">{{ __('quality::quality.standard name') }}</label>
                                <div class="fw-bold">{{ $standard->standard_name }}</div>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="text-muted small">{{ __('quality::quality.item') }}</label>
                                <div class="fw-bold">{{ $standard->item?->name ?? __('quality::quality.not specified') }}</div>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="text-muted small">{{ __('quality::quality.branch') }}</label>
                                <div class="fw-bold">{{ $standard->branch?->name ?? __('quality::quality.not specified') }}</div>
                            </div>
                            @if ($standard->description)
                                <div class="col-12 mb-3">
                                    <label class="text-muted small">{{ __('quality::quality.description') }}</label>
                                    <div>{{ $standard->description }}</div>
                                </div>
                            @endif
                            @if ($standard->test_method)
                                <div class="col-12 mb-3">
                                    <label class="text-muted small">{{ __('quality::quality.test method') }}</label>
                                    <div>{{ $standard->test_method }}</div>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

                @if ($standard->notes)
                    <div class="card mb-4">
                        <div class="card-header">
                            <h5 class="mb-0">
                                <i class="fas fa-sticky-note me-2"></i>{{ __('quality::quality.notes') }}
                            </h5>
                        </div>
                        <div class="card-body">
                            <div class="alert alert-info mb-0">{{ $standard->notes }}</div>
                        </div>
                    </div>
                @endif
            </div>

            <!-- Statistics & Status -->
            <div class="col-lg-4">
                <!-- Status -->
                <div class="card mb-4">
                    <div class="card-body text-center">
                        <div class="mb-3">
                            @if ($standard->is_active)
                                <i class="fas fa-check-circle text-success" style="font-size: 3rem;"></i>
                            @else
                                <i class="fas fa-pause-circle text-secondary" style="font-size: 3rem;"></i>
                            @endif
                        </div>
                        <h4 class="mb-2">
                            {{ $standard->is_active ? __('quality::quality.active') : __('quality::quality.inactive') }}
                        </h4>
                        <div class="text-muted">{{ __('quality::quality.standard status') }}</div>
                    </div>
                </div>

                <!-- Test Criteria -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h6 class="mb-0">{{ __('quality::quality.test criteria') }}</h6>
                    </div>
                    <div class="card-body p-0">
                        <div class="list-group list-group-flush">
                            <div class="list-group-item d-flex justify-content-between">
                                <span>{{ __('quality::quality.sample size') }}</span>
                                <strong>{{ $standard->sample_size }}</strong>
                            </div>
                            <div class="list-group-item d-flex justify-content-between">
                                <span>{{ __('quality::quality.test frequency') }}</span>
                                <strong>
                                    {{ match ($standard->test_frequency) {
                                        'per_batch' => __('quality::quality.per batch'),
                                        'daily' => __('quality::quality.daily'),
                                        'weekly' => __('quality::quality.weekly'),
                                        'monthly' => __('quality::quality.monthly'),
                                        default => $standard->test_frequency,
                                    } }}
                                </strong>
                            </div>
                            <div class="list-group-item d-flex justify-content-between text-success">
                                <span>{{ __('quality::quality.acceptance threshold') }}</span>
                                <strong>{{ number_format($standard->acceptance_threshold, 1) }}%</strong>
                            </div>
                            <div class="list-group-item d-flex justify-content-between text-danger">
                                <span>{{ __('quality::quality.max allowed defects') }}</span>
                                <strong>{{ $standard->max_defects_allowed }}</strong>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Additional Information -->
                <div class="card">
                    <div class="card-header">
                        <h6 class="mb-0">{{ __('quality::quality.additional information') }}</h6>
                    </div>
                    <div class="card-body">
                        <div class="small text-muted mb-2">{{ __('quality::quality.created') }} {{ __('quality::quality.date') }}</div>
                        <div class="mb-3">{{ $standard->created_at?->format('Y-m-d H:i') }}</div>

                        @if ($standard->updated_at != $standard->created_at)
                            <div class="small text-muted mb-2">{{ __('quality::quality.last updated') }}</div>
                            <div>{{ $standard->updated_at?->format('Y-m-d H:i') }}</div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
