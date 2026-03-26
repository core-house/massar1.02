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
                    <h2 class="mb-1">{{ $ncr->ncr_number }}</h2>
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{ route('quality.dashboard') }}">{{ __("quality::quality.quality") }}</a></li>
                            <li class="breadcrumb-item"><a href="{{ route('quality.ncr.index') }}">{{ __("quality::quality.ncr") }}</a></li>
                            <li class="breadcrumb-item active">{{ $ncr->ncr_number }}</li>
                        </ol>
                    </nav>
                </div>
                <div>
                    @can('edit ncr')
                    <a href="{{ route('quality.ncr.edit', $ncr) }}" class="btn btn-warning">
                        <i class="fas fa-edit me-2"></i>{{ __("quality::quality.edit") }}
                    </a>
                    @endcan
                    <a href="{{ route('quality.ncr.index') }}" class="btn btn-secondary">
                        <i class="fas fa-arrow-right me-2"></i>{{ __("quality::quality.back") }}
                    </a>
                </div>
            </div>
        </div>
    </div>
 
    <div class="row">
        <!-- معلومات أساسية -->
        <div class="col-lg-8">
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0"><i class="fas fa-info-circle me-2"></i>{{ __("quality::quality.report information") }}</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="text-muted small">{{ __("quality::quality.item") }}</label>
                            <div class="fw-bold">{{ $ncr->item?->name ?? __("quality::quality.not specified") }}</div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="text-muted small">{{ __("quality::quality.source") }}</label>
                            <div class="fw-bold">
                                {{ match($ncr->source) {
                                    'receiving_inspection' => __("quality::quality.receiving inspection"),
                                    'in_process' => __("quality::quality.in-process inspection"),
                                    'final_inspection' => __("quality::quality.final inspection"),
                                    'customer_complaint' => __("quality::quality.customer complaint inspection"),
                                    'internal_audit' => __("quality::quality.internal"),
                                    'supplier_notification' => __("quality::quality.supplier notification"),
                                    default => $ncr->source
                                } }}
                            </div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="text-muted small">{{ __("quality::quality.detection date") }}</label>
                            <div class="fw-bold">{{ $ncr->detected_date?->format('Y-m-d') }}</div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="text-muted small">{{ __("quality::quality.affected quantity") }}</label>
                            <div class="fw-bold">{{ number_format($ncr->affected_quantity, 3) }}</div>
                        </div>
                        @if($ncr->batch_number)
                        <div class="col-md-6 mb-3">
                            <label class="text-muted small">{{ __("quality::quality.batch number") }}</label>
                            <div class="fw-bold">{{ $ncr->batch_number }}</div>
                        </div>
                        @endif
                        @if($ncr->inspection)
                        <div class="col-md-6 mb-3">
                            <label class="text-muted small">{{ __("quality::quality.related inspection") }}</label>
                            <div class="fw-bold">
                                <a href="{{ route('quality.inspections.show', $ncr->inspection) }}" class="text-decoration-none">
                                    {{ $ncr->inspection->inspection_number }}
                                </a>
                            </div>
                        </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- الوصف والتفاصيل -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0"><i class="fas fa-file-alt me-2"></i>{{ __("quality::quality.description and details") }}</h5>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label class="text-muted small">{{ __("quality::quality.problem description") }}</label>
                        <div class="alert alert-danger mb-0">{{ $ncr->problem_description }}</div>
                    </div>
                    @if($ncr->immediate_action)
                    <div class="mb-0">
                        <label class="text-muted small">{{ __("quality::quality.immediate action") }}</label>
                        <div class="alert alert-info mb-0">{{ $ncr->immediate_action }}</div>
                    </div>
                    @endif
                </div>
            </div>

            <!-- المرفقات والصور -->
            @if(!empty($ncr->attachments))
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0"><i class="fas fa-paperclip me-2"></i>{{ __("quality::quality.attachments") }}</h5>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        @foreach($ncr->attachments as $attachment)
                            @php
                                $path = is_array($attachment) ? $attachment['path'] : $attachment;
                                $name = is_array($attachment) ? $attachment['original_name'] : basename($attachment);
                                $ext = strtolower(pathinfo($path, PATHINFO_EXTENSION));
                                $isImage = in_array($ext, ['jpg', 'jpeg', 'png', 'gif', 'webp', 'bmp']);
                                $url = asset('storage/' . $path);
                            @endphp
                            <div class="col-6 col-md-4 col-lg-3">
                                @if($isImage)
                                    <a href="{{ $url }}" target="_blank">
                                        <img src="{{ $url }}"
                                             alt="{{ $name }}"
                                             class="img-fluid rounded border"
                                             style="width:100%; height:150px; object-fit:cover;">
                                    </a>
                                    <div class="small text-muted text-truncate mt-1">{{ $name }}</div>
                                @else
                                    <a href="{{ $url }}" target="_blank"
                                       class="btn btn-outline-secondary w-100 d-flex flex-column align-items-center justify-content-center gap-1 p-3">
                                        <i class="fas fa-file fa-2x"></i>
                                        <span class="small text-truncate w-100 text-center">{{ $name }}</span>
                                    </a>
                                @endif
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
            @endif
        </div>

        <!-- الحالة والإحصائيات -->
        <div class="col-lg-4">
            <!-- الحالة -->
            <div class="card mb-4">
                <div class="card-body text-center">
                    <div class="mb-3">
                        @if($ncr->status == 'closed')
                            <i class="fas fa-check-circle text-success" style="font-size: 3rem;"></i>
                        @elseif($ncr->status == 'open')
                            <i class="fas fa-exclamation-circle text-danger" style="font-size: 3rem;"></i>
                        @else
                            <i class="fas fa-clock text-warning" style="font-size: 3rem;"></i>
                        @endif
                    </div>
                    <h4 class="mb-2">
                        {{ match($ncr->status) {
                            'open' => __("quality::quality.open"),
                            'in_progress' => __("quality::quality.in progress"),
                            'closed' => __("quality::quality.closed"),
                            'cancelled' => __("quality::quality.cancelled"),
                            default => $ncr->status
                        } }}
                    </h4>
                    <div class="text-muted">{{ __("quality::quality.report status") }}</div>
                </div>
            </div>

            <!-- التصنيف -->
            <div class="card mb-4">
                <div class="card-header">
                    <h6 class="mb-0">{{ __("quality::quality.classification") }}</h6>
                </div>
                <div class="card-body p-0">
                    <div class="list-group list-group-flush">
                        <div class="list-group-item d-flex justify-content-between">
                            <span>{{ __("quality::quality.severity") }}</span>
                            <span class="badge bg-{{ $ncr->severity == 'critical' ? 'danger' : ($ncr->severity == 'major' ? 'warning' : 'info') }}">
                                {{ match($ncr->severity) {
                                    'critical' => __("quality::quality.critical"),
                                    'major' => __("quality::quality.major"),
                                    'minor' => __("quality::quality.minor"),
                                    default => $ncr->severity
                                } }}
                            </span>
                        </div>
                        @if($ncr->disposition)
                        <div class="list-group-item d-flex justify-content-between">
                            <span>{{ __("quality::quality.disposition") }}</span>
                            <strong>
                                {{ match($ncr->disposition) {
                                    'rework' => __("quality::quality.rework"),
                                    'scrap' => __("quality::quality.scrap"),
                                    'return_to_supplier' => __("quality::quality.return to supplier"),
                                    'use_as_is' => __("quality::quality.use as is"),
                                    'repair' => __("quality::quality.repair"),
                                    'downgrade' => __("quality::quality.downgrade"),
                                    default => $ncr->disposition
                                } }}
                            </strong>
                        </div>
                        @endif
                        @if($ncr->estimated_cost)
                        <div class="list-group-item d-flex justify-content-between">
                            <span>{{ __("quality::quality.estimated cost") }}</span>
                            <strong class="text-warning">{{ number_format($ncr->estimated_cost, 2) }}</strong>
                        </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- المسؤولون -->
            <div class="card mb-4">
                <div class="card-header">
                    <h6 class="mb-0">{{ __("quality::quality.responsibilities") }}</h6>
                </div>
                <div class="card-body">
                    <div class="small text-muted mb-2">{{ __("quality::quality.detected by") }}</div>
                    <div class="mb-3">{{ $ncr->detectedBy?->name ?? __("quality::quality.not specified") }}</div>
                    
                    @if($ncr->assignedTo)
                    <div class="small text-muted mb-2">{{ __("quality::quality.assigned to") }}</div>
                    <div class="mb-3">{{ $ncr->assignedTo->name }}</div>
                    @endif

                    @if($ncr->target_closure_date)
                    <div class="small text-muted mb-2">{{ __("quality::quality.target closure date") }}</div>
                    <div>{{ $ncr->target_closure_date->format('Y-m-d') }}</div>
                    @endif
                </div>
            </div>

            <!-- معلومات إضافية -->
            <div class="card">
                <div class="card-header">
                    <h6 class="mb-0">{{ __("quality::quality.additional information") }}</h6>
                </div>
                <div class="card-body">
                    <div class="small text-muted mb-2">{{ __("quality::quality.creation date") }}</div>
                    <div class="mb-3">{{ $ncr->created_at?->format('Y-m-d H:i') }}</div>
                    
                    @if($ncr->updated_at != $ncr->created_at)
                    <div class="small text-muted mb-2">{{ __("quality::quality.last updated") }}</div>
                    <div>{{ $ncr->updated_at?->format('Y-m-d H:i') }}</div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection