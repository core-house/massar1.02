@extends('admin.dashboard')

@section('sidebar')
    @include('components.sidebar.quality')
@endsection

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-12">
            <h2><i class="fas fa-search me-2"></i>{{ __('quality::quality.audit details') }}</h2>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('quality.dashboard') }}">{{ __('quality::quality.quality') }}</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('quality.audits.index') }}">{{ __('quality::quality.audit') }}</a></li>
                    <li class="breadcrumb-item active">{{ __('quality::quality.view') }}</li>
                </ol>
            </nav>
        </div>
    </div>
 
    <div class="row">
        <div class="col-lg-8">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">{{ __('quality::quality.audit information') }}</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">{{ __('quality::quality.audit number') }}</label>
                            <p class="form-control-plaintext">{{ $audit->audit_number }}</p>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label class="form-label">{{ __('quality::quality.audit title') }}</label>
                            <p class="form-control-plaintext">{{ $audit->audit_title }}</p>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label class="form-label">{{ __('quality::quality.audit type') }}</label>
                            <p class="form-control-plaintext">
                                <span class="badge bg-info">
                                    {{ match($audit->audit_type) {
                                        'internal' => __('quality::quality.internal'),
                                        'external' => __('quality::quality.external'),
                                        'supplier' => __('quality::quality.supplier audit'),
                                        'certification' => __('quality::quality.certification'),
                                        'customer' => __('quality::quality.customer'),
                                        default => $audit->audit_type
                                    } }}
                                </span>
                            </p>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label class="form-label">{{ __('quality::quality.status') }}</label>
                            <p class="form-control-plaintext">
                                <span class="badge bg-{{ match($audit->status) {
                                    'completed' => 'success',
                                    'in_progress' => 'warning',
                                    'planned' => 'info',
                                    'cancelled' => 'danger',
                                    default => 'secondary'
                                } }}">
                                    {{ match($audit->status) {
                                        'planned' => __('quality::quality.planned'),
                                        'in_progress' => __('quality::quality.in progress'),
                                        'completed' => __('quality::quality.completed'),
                                        'cancelled' => __('quality::quality.cancelled'),
                                        default => $audit->status
                                    } }}
                                </span>
                            </p>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label class="form-label">{{ __('quality::quality.planned date') }}</label>
                            <p class="form-control-plaintext">{{ $audit->planned_date?->format('Y-m-d') ?? '---' }}</p>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label class="form-label">{{ __('quality::quality.lead auditor') }}</label>
                            <p class="form-control-plaintext">{{ $audit->leadAuditor?->name ?? '---' }}</p>
                        </div>

                        @if($audit->audit_objectives)
                        <div class="col-12 mb-3">
                            <label class="form-label">{{ __('quality::quality.audit objectives') }}</label>
                            <p class="form-control-plaintext">{{ $audit->audit_objectives }}</p>
                        </div>
                        @endif

                        @if($audit->summary)
                        <div class="col-12 mb-3">
                            <label class="form-label">{{ __('quality::quality.summary') }}</label>
                            <p class="form-control-plaintext">{{ $audit->summary }}</p>
                        </div>
                        @endif

                        @if($audit->status == 'completed')
                        <div class="col-12">
                            <h6 class="mb-3">{{ __('quality::quality.results statistics') }}</h6>
                            <div class="row">
                                <div class="col-md-3">
                                    <div class="card bg-info text-white">
                                        <div class="card-body text-center">
                                            <h4>{{ $audit->total_findings ?? 0 }}</h4>
                                            <small>{{ __('quality::quality.total findings') }}</small>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="card bg-danger text-white">
                                        <div class="card-body text-center">
                                            <h4>{{ $audit->critical_findings ?? 0 }}</h4>
                                            <small>{{ __('quality::quality.critical findings') }}</small>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="card bg-warning text-white">
                                        <div class="card-body text-center">
                                            <h4>{{ $audit->major_findings ?? 0 }}</h4>
                                            <small>{{ __('quality::quality.major findings') }}</small>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="card bg-success text-white">
                                        <div class="card-body text-center">
                                            <h4>{{ $audit->minor_findings ?? 0 }}</h4>
                                            <small>{{ __('quality::quality.minor findings') }}</small>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">{{ __('quality::quality.actions') }}</h5>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        @can('edit audits')
                         <a href="{{ route('quality.audits.edit', $audit) }}" class="btn btn-warning">
                            <i class="fas fa-edit me-2"></i>{{ __('quality::quality.edit audit') }}
                        </a>        
                        @endcan
                   @can('delete audits')
                        <button type="button" class="btn btn-danger"
                                data-bs-toggle="modal"
                                data-bs-target="#deleteModal">
                            <i class="fas fa-trash me-2"></i>{{ __('quality::quality.delete audit') }}
                        </button>                       
                   @endcan

                        <a href="{{ route('quality.audits.index') }}" class="btn btn-secondary">
                            <i class="fas fa-arrow-left me-2"></i>{{ __('quality::quality.back to list') }}
                        </a>
                    </div>
                </div>
            </div>

            <div class="card mt-3">
                <div class="card-header">
                    <h5 class="mb-0">{{ __('quality::quality.additional information') }}</h5>
                </div>
                <div class="card-body">
                    <small class="text-muted">
                        <strong>{{ __('quality::quality.created at') }}:</strong><br>
                        {{ $audit->created_at?->format('Y-m-d H:i') }}<br><br>
                        <strong>{{ __('quality::quality.last updated') }}:</strong><br>
                        {{ $audit->updated_at?->format('Y-m-d H:i') }}
                    </small>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Delete Modal -->
<div class="modal fade" id="deleteModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">{{ __('quality::quality.confirm delete') }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                {{ __('quality::quality.are you sure you want to delete audit') }} "{{ $audit->audit_title }}"?
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ __('quality::quality.cancel') }}</button>
                <form action="{{ route('quality.audits.destroy', $audit) }}" method="POST" class="d-inline">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">{{ __('quality::quality.delete') }}</button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
