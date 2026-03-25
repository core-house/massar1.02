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
                        <h2 class="mb-0"><i class="fas fa-tools me-2"></i>{{ __("quality::quality.capa") }}</h2>
                    </div>
                    <div>
                        @can('create capa')
                            <a href="{{ route('quality.capa.create') }}" class="btn btn-success">
                                <i class="fas fa-plus-circle me-2"></i>{{ __("quality::quality.new capa") }}
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
                        <h6 class="text-muted">{{ __("quality::quality.total actions") }}</h6>
                        <h3>{{ $stats['total'] }}</h3>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card">
                    <div class="card-body">
                        <h6 class="text-muted">{{ __("quality::quality.in progress") }}</h6>
                        <h3 class="text-warning">{{ $stats['in_progress'] }}</h3>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card">
                    <div class="card-body">
                        <h6 class="text-muted">{{ __("quality::quality.completed") }}</h6>
                        <h3 class="text-success">{{ $stats['completed'] }}</h3>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card">
                    <div class="card-body">
                        <h6 class="text-muted">{{ __("quality::quality.overdue") }}</h6>
                        <h3 class="text-danger">{{ $stats['overdue'] }}</h3>
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
                                <th>{{ __("quality::quality.capa number") }}</th>
                                <th>{{ __("quality::quality.type") }}</th>
                                <th>{{ __("quality::quality.assigned to") }}</th>
                                <th>{{ __("quality::quality.planned start date") }}</th>
                                <th>{{ __("quality::quality.planned completion date") }}</th>
                                <th>{{ __("quality::quality.completion percentage") }}</th>
                                <th>{{ __("quality::quality.status") }}</th>
                                @canany(['edit capa', 'delete capa', 'view capa'])
                                    <th>{{ __("quality::quality.actions") }}</th>
                                @endcanany
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($capas as $capa)
                                <tr class="{{ $capa->isOverdue() ? 'table-danger' : '' }}">
                                    <td><a href="{{ route('quality.capa.show', $capa) }}">{{ $capa->capa_number }}</a></td>
                                    <td>
                                        <span
                                            class="badge bg-{{ $capa->action_type == 'corrective' ? 'warning' : 'info' }}">
                                            {{ $capa->action_type == 'corrective' ? __("quality::quality.corrective") : __("quality::quality.preventive") }}
                                        </span>
                                    </td>
                                    <td>{{ $capa->responsiblePerson->name ?? '---' }}</td>
                                    <td>{{ $capa->planned_start_date->format('Y-m-d') }}</td>
                                    <td>{{ $capa->planned_completion_date->format('Y-m-d') }}</td>
                                    <td>
                                        <div class="progress" style="height: 20px;">
                                            <div class="progress-bar bg-{{ $capa->completion_percentage >= 100 ? 'success' : 'primary' }}"
                                                style="width: {{ $capa->completion_percentage }}%">
                                                {{ $capa->completion_percentage }}%
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <span
                                            class="badge bg-{{ match ($capa->status) {
                                                'completed' => 'success',
                                                'in_progress' => 'warning',
                                                'verified' => 'info',
                                                default => 'secondary',
                                            } }}">
                                            {{ $capa->status }}
                                        </span>
                                    </td>
                                    @canany(['edit capa', 'delete capa', 'view capa'])
                                        <td>
                                            <div class="btn-group" role="group">
                                                @can('view capa')
                                                    <a href="{{ route('quality.capa.show', $capa) }}" class="btn btn-sm btn-info"
                                                        title="{{ __("quality::quality.view") }}">
                                                        <i class="fas fa-eye"></i>
                                                    </a>
                                                @endcan
                                                @can('edit capa')
                                                    <a href="{{ route('quality.capa.edit', $capa) }}"
                                                        class="btn btn-sm btn-warning" title="{{ __("quality::quality.edit") }}">
                                                        <i class="fas fa-edit"></i>
                                                    </a>
                                                @endcan
                                                @can('delete capa')
                                                    <button type="button" class="btn btn-sm btn-danger" data-bs-toggle="modal"
                                                        data-bs-target="#deleteModal{{ $capa->id }}" title="{{ __("quality::quality.delete") }}">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                @endcan
                                            </div>

                                            <!-- Delete Modal -->
                                            <div class="modal fade" id="deleteModal{{ $capa->id }}" tabindex="-1">
                                                <div class="modal-dialog">
                                                    <div class="modal-content">
                                                        <div class="modal-header">
                                                            <h5 class="modal-title">{{ __("quality::quality.confirm delete") }}</h5>
                                                            <button type="button" class="btn-close"
                                                                data-bs-dismiss="modal"></button>
                                                        </div>
                                                        <div class="modal-body">
                                                            {{ __("quality::quality.are you sure you want to delete capa") }} "{{ $capa->capa_number }}"?
                                                        </div>
                                                        <div class="modal-footer">
                                                            <button type="button" class="btn btn-secondary"
                                                                data-bs-dismiss="modal">{{ __("quality::quality.cancel") }}</button>
                                                            <form action="{{ route('quality.capa.destroy', $capa) }}"
                                                                method="POST" class="d-inline">
                                                                @csrf
                                                                @method('DELETE')
                                                                <button type="submit" class="btn btn-danger">{{ __("quality::quality.delete") }}</button>
                                                            </form>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </td>
                                    @endcanany
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" class="text-center py-4">{{ __("quality::quality.no capas") }}</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                @if ($capas->hasPages())
                    <div class="d-flex justify-content-center mt-4">
                        {{ $capas->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection
