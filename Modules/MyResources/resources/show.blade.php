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
                    <h4 class="page-title">{{ __('myresources.resource_details') }}: {{ $resource->name }}</h4>
                    <div class="d-flex gap-2">
                        @can('edit MyResources')
                        <a href="{{ route('myresources.edit', $resource) }}" class="btn btn-success">
                            <i class="fas fa-edit"></i> {{ __('myresources.edit') }}
                        </a>
                        @endcan
                        <button onclick="window.print()" class="btn btn-info">
                            <i class="fas fa-print"></i> {{ __('common.print') }}
                        </button>
                        <a href="{{ route('myresources.index') }}" class="btn btn-secondary">
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
                    <h5 class="mb-0"><i class="fas fa-box"></i> {{ __('myresources.resource_information') }}</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">{{ __('myresources.name') }}:</label>
                            <div class="form-control-static">{{ $resource->name }}</div>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">{{ __('myresources.status') }}:</label>
                            <div class="form-control-static">
                                @if($resource->status)
                                    <span class="badge" style="background-color: {{ $resource->status->color ?? '#007bff' }}">
                                        {{ $resource->status->display_name }}
                                    </span>
                                @else
                                    {{ __('common.not_available') }}
                                @endif
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">{{ __('myresources.main_category') }}:</label>
                            <div class="form-control-static">{{ $resource->category->display_name ?? __('common.not_available') }}</div>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">{{ __('myresources.type') }}:</label>
                            <div class="form-control-static">{{ $resource->type->display_name ?? __('common.not_available') }}</div>
                        </div>
                    </div>

                    @if($resource->description)
                    <div class="row">
                        <div class="col-12 mb-3">
                            <label class="form-label fw-bold">{{ __('myresources.description') }}:</label>
                            <div class="form-control-static">{{ $resource->description }}</div>
                        </div>
                    </div>
                    @endif

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">{{ __('myresources.branch') }}:</label>
                            <div class="form-control-static">{{ $resource->branch->name ?? __('common.not_available') }}</div>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">{{ __('myresources.assigned_by') }}:</label>
                            <div class="form-control-static">{{ $resource->employee->aname ?? __('common.not_available') }}</div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label class="form-label fw-bold">{{ __('myresources.serial_number') }}:</label>
                            <div class="form-control-static">{{ $resource->serial_number ?? __('common.not_available') }}</div>
                        </div>

                        <div class="col-md-4 mb-3">
                            <label class="form-label fw-bold">{{ __('myresources.model_number') }}:</label>
                            <div class="form-control-static">{{ $resource->model_number ?? __('common.not_available') }}</div>
                        </div>

                        <div class="col-md-4 mb-3">
                            <label class="form-label fw-bold">{{ __('myresources.manufacturer') }}:</label>
                            <div class="form-control-static">{{ $resource->manufacturer ?? __('common.not_available') }}</div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label class="form-label fw-bold">{{ __('myresources.purchase_date') }}:</label>
                            <div class="form-control-static">{{ $resource->purchase_date ? \Carbon\Carbon::parse($resource->purchase_date)->format('Y-m-d') : __('common.not_available') }}</div>
                        </div>

                        <div class="col-md-4 mb-3">
                            <label class="form-label fw-bold">{{ __('myresources.purchase_cost') }}:</label>
                            <div class="form-control-static">{{ $resource->purchase_cost ? number_format($resource->purchase_cost, 2) : __('common.not_available') }}</div>
                        </div>

                        <div class="col-md-4 mb-3">
                            <label class="form-label fw-bold">{{ __('myresources.current_location') }}:</label>
                            <div class="form-control-static">{{ $resource->current_location ?? __('common.not_available') }}</div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label class="form-label fw-bold">{{ __('myresources.daily_rate') }}:</label>
                            <div class="form-control-static">{{ $resource->daily_rate ? number_format($resource->daily_rate, 2) : __('common.not_available') }}</div>
                        </div>

                        <div class="col-md-4 mb-3">
                            <label class="form-label fw-bold">{{ __('myresources.hourly_rate') }}:</label>
                            <div class="form-control-static">{{ $resource->hourly_rate ? number_format($resource->hourly_rate, 2) : __('common.not_available') }}</div>
                        </div>

                        <div class="col-md-4 mb-3">
                            <label class="form-label fw-bold">{{ __('myresources.warranty_expiry') }}:</label>
                            <div class="form-control-static">{{ $resource->warranty_expiry ? \Carbon\Carbon::parse($resource->warranty_expiry)->format('Y-m-d') : __('common.not_available') }}</div>
                        </div>
                    </div>

                    @if($resource->notes)
                    <div class="row">
                        <div class="col-12 mb-3">
                            <label class="form-label fw-bold">{{ __('myresources.notes') }}:</label>
                            <div class="form-control-static">{{ $resource->notes }}</div>
                        </div>
                    </div>
                    @endif

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">{{ __('myresources.active') }}:</label>
                            <div class="form-control-static">
                                @if($resource->is_active)
                                    <span class="badge bg-success">{{ __('myresources.active') }}</span>
                                @else
                                    <span class="badge bg-danger">{{ __('common.inactive') }}</span>
                                @endif
                            </div>
                        </div>
                    </div>

                    @if($resource->assignments->count() > 0)
                    <div class="row mt-4">
                        <div class="col-12">
                            <h6 class="fw-bold mb-3">{{ __('myresources.assignments') }}:</h6>
                            <div class="table-responsive">
                                <table class="table table-bordered">
                                    <thead>
                                        <tr>
                                            <th>{{ __('myresources.project') }}</th>
                                            <th>{{ __('myresources.start_date') }}</th>
                                            <th>{{ __('myresources.end_date') }}</th>
                                            <th>{{ __('myresources.assigned_by') }}</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($resource->assignments as $assignment)
                                        <tr>
                                            <td>{{ $assignment->project->name ?? __('common.not_available') }}</td>
                                            <td>{{ $assignment->start_date ? \Carbon\Carbon::parse($assignment->start_date)->format('Y-m-d') : __('common.not_available') }}</td>
                                            <td>{{ $assignment->end_date ? \Carbon\Carbon::parse($assignment->end_date)->format('Y-m-d') : __('common.not_available') }}</td>
                                            <td>{{ $assignment->assignedBy->name ?? __('common.not_available') }}</td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
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
        .table { font-size: 10px; }
    }
</style>
@endpush
@endsection
