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
                    <h2 class="mb-0"><i class="fas fa-barcode me-2"></i>{{ __("quality::quality.batch details") }}</h2>
                </div>
                <div>
                    <a href="{{ route('quality.batches.index') }}" class="btn btn-secondary me-2">
                        <i class="fas fa-arrow-right me-2"></i>{{ __("quality::quality.back to list") }}
                    </a>
                    @can('edit batches')
                    <a href="{{ route('quality.batches.edit', $batch) }}" class="btn btn-warning">
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
                            <label class="form-label fw-bold">{{ __("quality::quality.batch number") }}:</label>
                            <p class="mb-0">{{ $batch->batch_number }}</p>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">{{ __("quality::quality.item") }}:</label>
                            <p class="mb-0">{{ $batch->item?->name ?? '---' }}</p>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">{{ __("quality::quality.production date") }}:</label>
                            <p class="mb-0">{{ $batch->production_date ? $batch->production_date->format('Y-m-d') : '---' }}</p>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">{{ __("quality::quality.expiry date") }}:</label>
                            <p class="mb-0">{{ $batch->expiry_date ? $batch->expiry_date->format('Y-m-d') : '---' }}</p>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">{{ __("quality::quality.quantity") }}:</label>
                            <p class="mb-0">{{ number_format($batch->quantity, 3) }}</p>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">{{ __("quality::quality.remaining quantity") }}:</label>
                            <p class="mb-0">{{ number_format($batch->remaining_quantity, 3) }}</p>
                        </div>
                        @if($batch->supplier)
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">{{ __("quality::quality.supplier") }}:</label>
                            <p class="mb-0">{{ $batch->supplier->aname ?? '---' }}</p>
                        </div>
                        @endif
                        @if($batch->warehouse)
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">{{ __("quality::quality.warehouse") }}:</label>
                            <p class="mb-0">{{ $batch->warehouse->aname ?? '---' }}</p>
                        </div>
                        @endif
                        @if($batch->location)
                        <div class="col-12 mb-3">
                            <label class="form-label fw-bold">{{ __("quality::quality.storage location") }}:</label>
                            <p class="mb-0">{{ $batch->location }}</p>
                        </div>
                        @endif
                        @if($batch->notes)
                        <div class="col-12 mb-3">
                            <label class="form-label fw-bold">{{ __("quality::quality.notes") }}:</label>
                            <p class="mb-0">{{ $batch->notes }}</p>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0"><i class="fas fa-info-circle me-2"></i>{{ __("quality::quality.status") }}</h5>
                </div>
                <div class="card-body text-center">
                    <div class="mb-3">
                        <span class="badge bg-{{ $batch->status == 'active' ? 'success' : ($batch->status == 'consumed' ? 'secondary' : 'danger') }} fs-6 px-3 py-2">
                            @switch($batch->status)
                                @case('active') {{ __("quality::quality.active") }} @break
                                @case('consumed') {{ __("quality::quality.consumed") }} @break
                                @case('expired') {{ __("quality::quality.expired certificate") }} @break
                                @case('rejected') {{ __("quality::quality.rejected") }} @break
                                @default {{ $batch->status }}
                            @endswitch
                        </span>
                    </div>
                </div>
            </div>

            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0"><i class="fas fa-check-circle me-2"></i>{{ __("quality::quality.quality status") }}</h5>
                </div>
                <div class="card-body text-center">
                    <span class="badge bg-{{ $batch->quality_status == 'passed' ? 'success' : ($batch->quality_status == 'failed' ? 'danger' : 'warning') }} fs-6 px-3 py-2">
                        @switch($batch->quality_status)
                            @case('passed') {{ __("quality::quality.pass") }} @break
                            @case('failed') {{ __("quality::quality.fail") }} @break
                            @case('conditional') {{ __("quality::quality.conditional") }} @break
                            @case('quarantine') {{ __("quality::quality.quarantine") }} @break
                            @default {{ $batch->quality_status }}
                        @endswitch
                    </span>
                </div>
            </div>

            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0"><i class="fas fa-clock me-2"></i>{{ __("quality::quality.system information") }}</h5>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label class="form-label fw-bold">{{ __("quality::quality.created at") }}:</label>
                        <p class="mb-0">{{ $batch->created_at ? $batch->created_at->format('Y-m-d H:i') : '---' }}</p>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">{{ __("quality::quality.last updated") }}:</label>
                        <p class="mb-0">{{ $batch->updated_at ? $batch->updated_at->format('Y-m-d H:i') : '---' }}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

