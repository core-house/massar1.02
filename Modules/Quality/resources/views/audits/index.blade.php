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
                        <h2 class="mb-0"><i class="fas fa-search me-2"></i>{{ __('quality::quality.internal audits') }}</h2>
                    </div>
                    <div>
                        @can('create audits')
                            <a href="{{ route('quality.audits.create') }}" class="btn btn-primary">
                                <i class="fas fa-plus-circle me-2"></i>{{ __('quality::quality.new audit') }}
                            </a>
                        @endcan
                    </div>
                </div>
            </div>
        </div>
 
        <div class="row g-3 mb-4">
            <div class="col-md-3">
                <div class="card">
                    <div class="card-body">
                        <h6 class="text-muted">{{ __('quality::quality.planned audits') }}</h6>
                        <h3 class="text-info">{{ $stats['planned'] }}</h3>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card">
                    <div class="card-body">
                        <h6 class="text-muted">{{ __('quality::quality.in progress') }}</h6>
                        <h3 class="text-warning">{{ $stats['in_progress'] }}</h3>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card">
                    <div class="card-body">
                        <h6 class="text-muted">{{ __('quality::quality.completed') }}</h6>
                        <h3 class="text-success">{{ $stats['completed'] }}</h3>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card">
                    <div class="card-body">
                        <h6 class="text-muted">{{ __('quality::quality.total') }}</h6>
                        <h3>{{ $stats['total'] }}</h3>
                    </div>
                </div>
            </div>
        </div>

        <div class="card">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>{{ __('quality::quality.audit number') }}</th>
                                <th>{{ __('quality::quality.title') }}</th>
                                <th>{{ __('quality::quality.audit type') }}</th>
                                <th>{{ __('quality::quality.lead auditor') }}</th>
                                <th>{{ __('quality::quality.planned date') }}</th>
                                <th>{{ __('quality::quality.result') }}</th>
                                <th>{{ __('quality::quality.status') }}</th>
                                @canany(['view audits', 'edit audits', 'delete audits'])
                                    <th>{{ __('quality::quality.actions') }}</th>
                                @endcanany
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($audits as $audit)
                                <tr>
                                    <td><a href="{{ route('quality.audits.show', $audit) }}">{{ $audit->audit_number }}</a></td>
                                    <td>{{ $audit->audit_title }}</td>
                                    <td>
                                        <span class="badge bg-info">
                                            {{ match($audit->audit_type) {
                                                'internal' => __('quality::quality.internal'),
                                                'external' => __('quality::quality.external'),
                                                'supplier' => __('quality::quality.supplier'),
                                                'certification' => __('quality::quality.certification'),
                                                'customer' => __('quality::quality.customer'),
                                                default => $audit->audit_type,
                                            } }}
                                        </span>
                                    </td>
                                    <td>{{ $audit->leadAuditor->name ?? '---' }}</td>
                                    <td>{{ $audit->planned_date->format('Y-m-d') }}</td>
                                    <td>
                                        @if($audit->overall_result)
                                            <span class="badge bg-{{ match($audit->overall_result) {
                                                'pass' => 'success',
                                                'fail' => 'danger',
                                                default => 'warning',
                                            } }}">
                                                {{ $audit->overall_result }}
                                            </span>
                                        @else
                                            <span class="text-muted">---</span>
                                        @endif
                                    </td>
                                    <td>
                                        <span class="badge bg-{{ match($audit->status) {
                                            'completed' => 'success',
                                            'in_progress' => 'warning',
                                            'planned' => 'info',
                                            default => 'secondary',
                                        } }}">
                                            {{ match($audit->status) {
                                                'planned' => __('quality::quality.planned'),
                                                'in_progress' => __('quality::quality.in progress'),
                                                'completed' => __('quality::quality.completed'),
                                                'cancelled' => __('quality::quality.cancelled'),
                                                default => $audit->status,
                                            } }}
                                        </span>
                                    </td>
                                    <td>
                                        <div class="btn-group" role="group">
                                            @can('view audits')
                                                <a href="{{ route('quality.audits.show', $audit) }}"
                                                    class="btn btn-sm btn-info" title="{{ __('quality::quality.view') }}">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                            @endcan
                                            @can('edit audits')
                                                <a href="{{ route('quality.audits.edit', $audit) }}"
                                                    class="btn btn-sm btn-warning" title="{{ __('quality::quality.edit') }}">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                            @endcan
                                            @can('delete audits')
                                                <button type="button" class="btn btn-sm btn-danger" data-bs-toggle="modal"
                                                    data-bs-target="#deleteModal{{ $audit->id }}" title="{{ __('quality::quality.delete') }}">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            @endcan
                                        </div>

                                        <!-- Delete Modal -->
                                        <div class="modal fade" id="deleteModal{{ $audit->id }}" tabindex="-1">
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
                                                        <button type="button" class="btn btn-secondary"
                                                            data-bs-dismiss="modal">{{ __('quality::quality.cancel') }}</button>
                                                        <form action="{{ route('quality.audits.destroy', $audit) }}"
                                                            method="POST" class="d-inline">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="submit" class="btn btn-danger">{{ __('quality::quality.delete') }}</button>
                                                        </form>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" class="text-center py-4">{{ __('quality::quality.no audits') }}</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                @if($audits->hasPages())
                    <div class="d-flex justify-content-center mt-4">
                        {{ $audits->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection
