@extends('admin.dashboard')

@section('sidebar')
    @include('components.sidebar.myresources')
@endsection

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="page-title-box no-print">
                <div class="d-flex justify-content-between align-items-center">
                    <h4 class="page-title">{{ __('myresources.assignment_details') }}</h4>
                    <div class="d-flex gap-2">
                        <a href="{{ route('myresources.assignments.edit', $assignment) }}" class="btn btn-success">
                            <i class="fas fa-edit"></i> {{ __('myresources.edit') }}
                        </a>
                        <button onclick="window.print()" class="btn btn-info">
                            <i class="fas fa-print"></i> {{ __('common.print') }}
                        </button>
                        <a href="{{ route('myresources.assignments.index') }}" class="btn btn-secondary">
                            <i class="fas fa-arrow-right"></i> {{ __('common.back') }}
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card printable-content">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0"><i class="fas fa-link"></i> {{ __('myresources.assignment_information') }}</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">{{ __('myresources.resource') }}:</label>
                            <div class="form-control-static">
                                {{ $assignment->resource ? $assignment->resource->name . ' (' . ($assignment->resource->code ?? '') . ')' : __('common.unspecified') }}
                            </div>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">{{ __('myresources.project') }}:</label>
                            <div class="form-control-static">
                                {{ $assignment->project->name ?? __('common.unspecified') }}
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label class="form-label fw-bold">{{ __('myresources.start_date') }}:</label>
                            <div class="form-control-static">
                                {{ $assignment->start_date?->format('Y-m-d') ?? __('common.unspecified') }}
                            </div>
                        </div>

                        <div class="col-md-4 mb-3">
                            <label class="form-label fw-bold">{{ __('myresources.end_date') }}:</label>
                            <div class="form-control-static">
                                {{ $assignment->end_date?->format('Y-m-d') ?? __('common.unspecified') }}
                            </div>
                        </div>

                        <div class="col-md-4 mb-3">
                            <label class="form-label fw-bold">{{ __('myresources.daily_cost') }}:</label>
                            <div class="form-control-static">
                                {{ $assignment->daily_cost ? number_format($assignment->daily_cost, 2) : __('common.unspecified') }}
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">{{ __('myresources.status') }}:</label>
                            <div class="form-control-static">
                                @php
                                    $statusValue = $assignment->status->value ?? $assignment->status;
                                    $statusMap = [
                                        'scheduled' => ['label' => __('myresources.scheduled'), 'color' => 'info'],
                                        'active'    => ['label' => __('myresources.active'),    'color' => 'success'],
                                        'completed' => ['label' => __('common.completed'),       'color' => 'primary'],
                                        'cancelled' => ['label' => __('common.cancelled'),       'color' => 'danger'],
                                    ];
                                    $s = $statusMap[$statusValue] ?? ['label' => $statusValue, 'color' => 'secondary'];
                                @endphp
                                <span class="badge bg-{{ $s['color'] }}">{{ $s['label'] }}</span>
                            </div>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">{{ __('myresources.assignment_type') }}:</label>
                            <div class="form-control-static">
                                @php
                                    $typeValue = $assignment->assignment_type->value ?? $assignment->assignment_type;
                                    $typeLabels = [
                                        'current'  => __('myresources.current'),
                                        'upcoming' => __('myresources.upcoming'),
                                        'past'     => __('myresources.historical'),
                                    ];
                                @endphp
                                {{ $typeLabels[$typeValue] ?? $typeValue }}
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-12 mb-3">
                            <label class="form-label fw-bold">{{ __('myresources.notes') }}:</label>
                            <div class="form-control-static">{{ $assignment->notes ?? __('common.no_notes') }}</div>
                        </div>
                    </div>

                    @if($assignment->assignedBy)
                    <div class="row">
                        <div class="col-12 mb-3">
                            <label class="form-label fw-bold">{{ __('myresources.assigned_by') }}:</label>
                            <div class="form-control-static">{{ $assignment->assignedBy->name ?? __('common.unspecified') }}</div>
                        </div>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

@push('styles')
<style>
    .form-control-static {
        padding: 0.5rem;
        background-color: #f8f9fa;
        border: 1px solid #dee2e6;
        border-radius: 0.375rem;
        min-height: 2.5rem;
        display: flex;
        align-items: center;
    }
    @media print {
        .no-print { display: none !important; }
        .card { border: 1px solid #000 !important; box-shadow: none !important; }
        .card-header { background: #f1f1f1 !important; color: #000 !important; }
        body { font-size: 12px; }
        .form-control-static { background: #fff !important; border: 1px solid #000 !important; }
    }
</style>
@endpush
@endsection
