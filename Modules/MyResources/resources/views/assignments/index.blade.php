@extends('admin.dashboard')

@section('sidebar')
    @include('components.sidebar.myresources')
@endsection

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="page-title-box">
                <h4 class="page-title">{{ __('myresources.resource_assignments') }}</h4>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h4 class="header-title">{{ __('myresources.assignments_list') }}</h4>
                        <div class="d-flex gap-2">
                            <a href="{{ route('myresources.assignments.create') }}" class="btn btn-primary">
                                <i class="fas fa-plus"></i> {{ __('myresources.add_new_assignment') }}
                            </a>
                            <a href="{{ route('myresources.index') }}" class="btn btn-secondary">
                                <i class="fas fa-arrow-right"></i> {{ __('myresources.resources_management') }}
                            </a>
                        </div>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-striped table-bordered">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>{{ __('myresources.resource') }}</th>
                                    <th>{{ __('myresources.project') }}</th>
                                    <th>{{ __('myresources.start_date') }}</th>
                                    <th>{{ __('myresources.end_date') }}</th>
                                    <th>{{ __('myresources.status') }}</th>
                                    <th>{{ __('myresources.assignment_type') }}</th>
                                    <th>{{ __('myresources.daily_cost') }}</th>
                                    <th>{{ __('myresources.assigned_by') }}</th>
                                    <th>{{ __('myresources.actions') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($assignments as $assignment)
                                    <tr>
                                        <td>{{ $assignment->id }}</td>
                                        <td>
                                            <strong>{{ $assignment->resource->name ?? __('common.not_available') }}</strong>
                                            <br>
                                            <small class="text-muted">{{ $assignment->resource->code ?? '' }}</small>
                                        </td>
                                        <td>{{ $assignment->project->name ?? __('common.not_available') }}</td>
                                        <td>{{ $assignment->start_date?->format('Y-m-d') ?? __('common.not_available') }}</td>
                                        <td>{{ $assignment->end_date?->format('Y-m-d') ?? __('common.not_available') }}</td>
                                        <td>
                                            @if($assignment->status instanceof \Modules\MyResources\Enums\ResourceAssignmentStatus)
                                                <span class="badge bg-{{ $assignment->status->color() }}">
                                                    {{ $assignment->status->label() }}
                                                </span>
                                            @else
                                                <span class="badge bg-secondary">{{ $assignment->status }}</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if($assignment->assignment_type instanceof \Modules\MyResources\Enums\AssignmentType)
                                                {{ $assignment->assignment_type->label() }}
                                            @else
                                                @php
                                                    $typeLabels = [
                                                        'current'  => __('myresources.current'),
                                                        'upcoming' => __('myresources.upcoming'),
                                                        'past'     => __('myresources.historical'),
                                                    ];
                                                @endphp
                                                {{ $typeLabels[$assignment->assignment_type] ?? $assignment->assignment_type }}
                                            @endif
                                        </td>
                                        <td>{{ $assignment->daily_cost ? number_format($assignment->daily_cost, 2) : __('common.not_available') }}</td>
                                        <td>{{ $assignment->assignedBy->name ?? __('common.not_available') }}</td>
                                        <td>
                                            <div class="btn-group" role="group">
                                                <a href="{{ route('myresources.assignments.edit', $assignment) }}" class="btn btn-sm btn-success">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                                <form action="{{ route('myresources.assignments.destroy', $assignment) }}" method="POST" class="d-inline"
                                                      onsubmit="return confirm('{{ __('common.confirm_delete') }}');">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-sm btn-danger">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="10" class="text-center">{{ __('myresources.no_assignments_found') }}</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
