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
                    <h2 class="mb-0"><i class="fas fa-tools me-2"></i>{{ __("quality::quality.capa details") }}</h2>
                </div>
                <div>
                    <a href="{{ route('quality.capa.index') }}" class="btn btn-secondary me-2">
                        <i class="fas fa-arrow-right me-2"></i>{{ __("quality::quality.back to list") }}
                    </a>
                    @can('edit capa')
                    <a href="{{ route('quality.capa.edit', $capa) }}" class="btn btn-warning">
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
                            <label class="form-label fw-bold">{{ __("quality::quality.capa number") }}:</label>
                            <p class="mb-0">{{ $capa->capa_number }}</p>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">{{ __("quality::quality.capa type") }}:</label>
                            <p class="mb-0">
                                <span class="badge bg-{{ $capa->action_type == 'corrective' ? 'warning' : 'info' }}">
                                    {{ $capa->action_type == 'corrective' ? __("quality::quality.corrective") : __("quality::quality.preventive") }}
                                </span>
                            </p>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">{{ __("quality::quality.related ncr") }}:</label>
                            <p class="mb-0">{{ $capa->nonConformanceReport->ncr_number ?? '---' }}</p>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">{{ __("quality::quality.priority") }}:</label>
                            <p class="mb-0">
                                <span class="badge bg-{{ match($capa->priority) {
                                    'high' => 'danger',
                                    'medium' => 'warning',
                                    'low' => 'success',
                                    default => 'secondary'
                                } }}">
                                    {{ match($capa->priority) {
                                        'high' => __("quality::quality.high"),
                                        'medium' => __("quality::quality.medium"),
                                        'low' => __("quality::quality.low"),
                                        default => $capa->priority
                                    } }}
                                </span>
                            </p>
                        </div>
                        <div class="col-12 mb-3">
                            <label class="form-label fw-bold">{{ __("quality::quality.problem description") }}:</label>
                            <p class="mb-0">{{ $capa->problem_description }}</p>
                        </div>
                        <div class="col-12 mb-3">
                            <label class="form-label fw-bold">{{ __("quality::quality.root cause") }}:</label>
                            <p class="mb-0">{{ $capa->root_cause_analysis }}</p>
                        </div>
                        <div class="col-12 mb-3">
                            <label class="form-label fw-bold">{{ __("quality::quality.action plan") }}:</label>
                            <p class="mb-0">{{ $capa->proposed_action }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0"><i class="fas fa-calendar-alt me-2"></i>{{ __("quality::quality.dates and implementation") }}</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">{{ __("quality::quality.planned start date") }}:</label>
                            <p class="mb-0">{{ $capa->planned_start_date->format('Y-m-d') }}</p>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">{{ __("quality::quality.planned completion date") }}:</label>
                            <p class="mb-0">{{ $capa->planned_completion_date->format('Y-m-d') }}</p>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">{{ __("quality::quality.actual start date") }}:</label>
                            <p class="mb-0">{{ $capa->actual_start_date ? $capa->actual_start_date->format('Y-m-d') : '---' }}</p>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">{{ __("quality::quality.actual completion date") }}:</label>
                            <p class="mb-0">{{ $capa->actual_completion_date ? $capa->actual_completion_date->format('Y-m-d') : '---' }}</p>
                        </div>
                        <div class="col-12 mb-3">
                            <label class="form-label fw-bold">{{ __("quality::quality.completion percentage") }}:</label>
                            <div class="progress" style="height: 25px;">
                                <div class="progress-bar bg-{{ $capa->completion_percentage >= 100 ? 'success' : 'primary' }}" 
                                     style="width: {{ $capa->completion_percentage }}%">
                                    {{ $capa->completion_percentage }}%
                                </div>
                            </div>
                        </div>
                        @if($capa->implementation_details)
                        <div class="col-12 mb-3">
                            <label class="form-label fw-bold">{{ __("quality::quality.implementation details") }}:</label>
                            <p class="mb-0">{{ $capa->implementation_details }}</p>
                        </div>
                        @endif
                    </div>
                </div>
            </div>

            @if($capa->verification_details || $capa->effectiveness_review)
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0"><i class="fas fa-check-circle me-2"></i>{{ __("quality::quality.verification method") }}</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        @if($capa->verification_details)
                        <div class="col-12 mb-3">
                            <label class="form-label fw-bold">{{ __("quality::quality.verification details") }}:</label>
                            <p class="mb-0">{{ $capa->verification_details }}</p>
                        </div>
                        @endif
                        @if($capa->effectiveness_review)
                        <div class="col-12 mb-3">
                            <label class="form-label fw-bold">{{ __("quality::quality.effectiveness review") }}:</label>
                            <p class="mb-0">{{ $capa->effectiveness_review }}</p>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
            @endif

            <!-- المرفقات والصور -->
            @if(!empty($capa->attachments))
            <div class="card mt-4">
                <div class="card-header">
                    <h5 class="mb-0"><i class="fas fa-paperclip me-2"></i>{{ __("quality::quality.attachments") }}</h5>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        @foreach($capa->attachments as $attachment)
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

        <div class="col-md-4">
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0"><i class="fas fa-info-circle me-2"></i>{{ __("quality::quality.status") }}</h5>
                </div>
                <div class="card-body">
                    <div class="text-center mb-3">
                        <span class="badge bg-{{ match($capa->status) {
                            'completed' => 'success',
                            'in_progress' => 'warning',
                            'verified' => 'info',
                            default => 'secondary'
                        } }} fs-6 px-3 py-2">
                            {{ match($capa->status) {
                                'completed' => __("quality::quality.completed"),
                                'in_progress' => __("quality::quality.in_progress"),
                                'verified' => __("quality::quality.verified"),
                                default => $capa->status
                            } }}
                        </span>
                    </div>
                    @if($capa->isOverdue())
                    <div class="alert alert-danger text-center">
                        <i class="fas fa-exclamation-triangle me-2"></i>{{ __("quality::quality.overdue") }}
                    </div>
                    @endif
                </div>
            </div>

            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0"><i class="fas fa-users me-2"></i>{{ __("quality::quality.responsibilities") }}</h5>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label class="form-label fw-bold">{{ __("quality::quality.assigned to") }}:</label>
                        <p class="mb-0">{{ $capa->responsiblePerson->name ?? '---' }}</p>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">{{ __("quality::quality.verified by") }}:</label>
                        <p class="mb-0">{{ $capa->verifiedBy->name ?? '---' }}</p>
                    </div>
                    @if($capa->verification_date)
                    <div class="mb-3">
                        <label class="form-label fw-bold">{{ __("quality::quality.verification date") }}:</label>
                        <p class="mb-0">{{ $capa->verification_date->format('Y-m-d') }}</p>
                    </div>
                    @endif
                </div>
            </div>

            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0"><i class="fas fa-clock me-2"></i>{{ __("quality::quality.system information") }}</h5>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label class="form-label fw-bold">{{ __("quality::quality.created at") }}:</label>
                        <p class="mb-0">{{ $capa->created_at->format('Y-m-d H:i') }}</p>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">{{ __("quality::quality.last updated") }}:</label>
                        <p class="mb-0">{{ $capa->updated_at->format('Y-m-d H:i') }}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection