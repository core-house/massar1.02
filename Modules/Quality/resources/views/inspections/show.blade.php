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
                    <h2 class="mb-1">{{ $inspection->inspection_number }}</h2>
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{ route('quality.dashboard') }}">{{ __("quality::quality.quality") }}</a></li>
                            <li class="breadcrumb-item"><a href="{{ route('quality.inspections.index') }}">{{ __("quality::quality.inspections") }}</a></li>
                            <li class="breadcrumb-item active">{{ $inspection->inspection_number }}</li>
                        </ol>
                    </nav>
                </div>
                <div>
                    @can('edit inspections')
                    <a href="{{ route('quality.inspections.edit', $inspection) }}" class="btn btn-warning">
                        <i class="fas fa-edit me-2"></i>{{ __("quality::quality.edit") }}
                    </a>
                    @endcan
                    <a href="{{ route('quality.inspections.index') }}" class="btn btn-secondary">
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
                    <h5 class="mb-0"><i class="fas fa-info-circle me-2"></i>{{ __("quality::quality.inspection information") }}</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="text-muted small">{{ __("quality::quality.item") }}</label>
                            <div class="fw-bold">{{ $inspection->item?->name ?? __("quality::quality.not specified") }}</div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="text-muted small">{{ __("quality::quality.inspection type") }}</label>
                            <div class="fw-bold">
                                {{ match($inspection->inspection_type) {
                                    'receiving' => __("quality::quality.receiving inspection"),
                                    'in_process' => __("quality::quality.in-process inspection"),
                                    'final' => __("quality::quality.final inspection"),
                                    'random' => __("quality::quality.random inspection"),
                                    'customer_complaint' => __("quality::quality.customer complaint inspection"),
                                    default => $inspection->inspection_type
                                } }}
                            </div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="text-muted small">{{ __("quality::quality.inspection date") }}</label>
                            <div class="fw-bold">{{ $inspection->inspection_date?->format('Y-m-d') }}</div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="text-muted small">{{ __("quality::quality.inspector") }}</label>
                            <div class="fw-bold">{{ $inspection->inspector?->name ?? __("quality::quality.not specified") }}</div>
                        </div>
                        @if($inspection->supplier)
                        <div class="col-md-6 mb-3">
                            <label class="text-muted small">{{ __("quality::quality.supplier") }}</label>
                            <div class="fw-bold">{{ $inspection->supplier->aname }}</div>
                        </div>
                        @endif
                        @if($inspection->batch_number)
                        <div class="col-md-6 mb-3">
                            <label class="text-muted small">{{ __("quality::quality.batch number") }}</label>
                            <div class="fw-bold">{{ $inspection->batch_number }}</div>
                        </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- المرفقات والصور -->
            @if(!empty($inspection->attachments))
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0"><i class="fas fa-paperclip me-2"></i>{{ __("quality::quality.attachments") }}</h5>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        @foreach($inspection->attachments as $attachment)
                            @php
                                $ext = strtolower(pathinfo($attachment, PATHINFO_EXTENSION));
                                $isImage = in_array($ext, ['jpg', 'jpeg', 'png', 'gif', 'webp', 'bmp']);
                                $url = asset("storage/{$attachment}");
                            @endphp
                            <div class="col-6 col-md-4 col-lg-3">
                                @if($isImage)
                                    <a href="{{ $url }}" target="_blank">
                                        <img src="{{ $url }}" 
                                             alt="{{ __('quality::quality.attachments') }}"
                                             class="img-fluid rounded border"
                                             style="width:100%; height:150px; object-fit:cover;">
                                    </a>
                                @else
                                    <a href="{{ $url }}" target="_blank" class="btn btn-outline-secondary w-100 h-100 d-flex align-items-center justify-content-center gap-2">
                                        <i class="fas fa-file fa-2x"></i>
                                        <span class="small">{{ basename($attachment) }}</span>
                                    </a>
                                @endif
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
            @endif

            <!-- العيوب والملاحظات -->
            @if($inspection->defects_found || $inspection->inspector_notes)
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0"><i class="fas fa-exclamation-triangle me-2"></i>{{ __("quality::quality.defects found") }} {{ __("quality::quality.and") }} {{ __("quality::quality.notes") }}</h5>
                </div>
                <div class="card-body">
                    @if($inspection->defects_found)
                    <div class="mb-3">
                        <label class="text-muted small">{{ __("quality::quality.defects found") }}</label>
                        <div class="alert alert-warning mb-0">{{ $inspection->defects_found }}</div>
                    </div>
                    @endif
                    @if($inspection->inspector_notes)
                    <div class="mb-0">
                        <label class="text-muted small">{{ __("quality::quality.inspector notes") }}</label>
                        <div class="alert alert-info mb-0">{{ $inspection->inspector_notes }}</div>
                    </div>
                    @endif
                </div>
            </div>
            @endif
        </div>

        <!-- النتائج والإحصائيات -->
        <div class="col-lg-4">
            <!-- النتيجة النهائية -->
            <div class="card mb-4">
                <div class="card-body text-center">
                    <div class="mb-3">
                        @if($inspection->result == 'pass')
                            <i class="fas fa-check-circle text-success" style="font-size: 3rem;"></i>
                        @elseif($inspection->result == 'fail')
                            <i class="fas fa-times-circle text-danger" style="font-size: 3rem;"></i>
                        @else
                            <i class="fas fa-exclamation-circle text-warning" style="font-size: 3rem;"></i>
                        @endif
                    </div>
                    <h4 class="mb-2">
                        {{ match($inspection->result) {
                            'pass' => __("quality::quality.pass"),
                            'fail' => __("quality::quality.fail"),
                            'conditional' => __("quality::quality.conditional"),
                            default => $inspection->result
                        } }}
                    </h4>
                    <div class="text-muted">{{ __("quality::quality.result") }} {{ __("quality::quality.final") }}</div>
                </div>
            </div>

            <!-- النتائج والإحصائيات -->
            <div class="card mb-4">
                <div class="card-header">
                    <h6 class="mb-0">{{ __("quality::quality.quantity") }} {{ __("quality::quality.statistics") }}</h6>
                </div>
                <div class="card-body p-0">
                    <div class="list-group list-group-flush">
                        <div class="list-group-item d-flex justify-content-between">
                            <span>{{ __("quality::quality.inspected quantity") }}</span>
                            <strong>{{ number_format($inspection->quantity_inspected, 2) }}</strong>
                        </div>
                        <div class="list-group-item d-flex justify-content-between text-success">
                            <span>{{ __("quality::quality.passed quantity") }}</span>
                            <strong>{{ number_format($inspection->pass_quantity, 2) }}</strong>
                        </div>
                        <div class="list-group-item d-flex justify-content-between text-danger">
                            <span>{{ __("quality::quality.failed quantity") }}</span>
                            <strong>{{ number_format($inspection->fail_quantity, 2) }}</strong>
                        </div>
                        <div class="list-group-item d-flex justify-content-between bg-light">
                            <span class="fw-bold">{{ __("quality::quality.pass percentage") }}</span>
                            <strong class="{{ $inspection->pass_percentage >= 95 ? 'text-success' : 'text-danger' }}">
                                {{ number_format($inspection->pass_percentage, 1) }}%
                            </strong>
                        </div>
                    </div>
                </div>
            </div>

            <!-- الإجراء المتخذ -->
            <div class="card mb-4">
                <div class="card-header">
                    <h6 class="mb-0">{{ __("quality::quality.action taken") }}</h6>
                </div>
                <div class="card-body">
                    <div class="text-center">
                        <span class="badge bg-{{ match($inspection->action_taken) {
                            'accepted' => 'success',
                            'rejected' => 'danger',
                            'rework' => 'warning',
                            'conditional_accept' => 'info',
                            default => 'secondary'
                        } }} fs-6 px-3 py-2">
                            {{ match($inspection->action_taken) {
                                'accepted' => __("quality::quality.accepted"),
                                'rejected' => __("quality::quality.rejected"),
                                'rework' => __("quality::quality.rework"),
                                'conditional_accept' => __("quality::quality.conditional_accept"),
                                'pending_review' => __("quality::quality.pending_review"),
                                default => $inspection->action_taken
                            } }}
                        </span>
                    </div>
                </div>
            </div>

            <!-- معلومات إضافية -->
            <div class="card">
                <div class="card-header">
                    <h6 class="mb-0">{{ __("quality::quality.additional information") }}</h6>
                </div>
                <div class="card-body">
                    <div class="small text-muted mb-2">{{ __("quality::quality.creation date") }}</div>
                    <div class="mb-3">{{ $inspection->created_at?->format('Y-m-d H:i') }}</div>
                    
                    @if($inspection->updated_at != $inspection->created_at)
                    <div class="small text-muted mb-2">{{ __("quality::quality.last update") }}</div>
                    <div class="mb-3">{{ $inspection->updated_at?->format('Y-m-d H:i') }}</div>
                    @endif

                    <div class="small text-muted mb-2">{{ __("quality::quality.status") }}</div>
                    <span class="badge bg-{{ $inspection->status == 'completed' ? 'success' : 'warning' }}">
                        {{ match($inspection->status) {
                            'pending' => __("quality::quality.pending"),
                            'in_progress' => __("quality::quality.in_progress"),
                            'completed' => __("quality::quality.completed"),
                            default => $inspection->status
                        } }}
                    </span>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection