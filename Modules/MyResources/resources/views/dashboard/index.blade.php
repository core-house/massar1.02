@extends('admin.dashboard')

@section('sidebar')
    @include('components.sidebar.myresources')
@endsection

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card bg-primary">
                <div class="card-body">
                    <h2 class="mb-0 text-white">
                        <i class="fas fa-cubes me-2"></i>
                        {{ __('myresources.resources_dashboard') }}
                    </h2>
                    <p class="mb-0 mt-2 text-white">{{ __('myresources.comprehensive_dashboard') }}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="row g-3 mb-4">
        <div class="col-lg-3 col-md-6">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-muted mb-2">{{ __('myresources.total_resources') }}</h6>
                            <h3 class="mb-0">{{ $totalResources }}</h3>
                            <small class="text-success">
                                <i class="fas fa-check-circle"></i> {{ __('myresources.active') }}: {{ $activeResources }}
                            </small>
                        </div>
                        <div class="text-primary" style="font-size: 3rem;">
                            <i class="fas fa-cubes"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-3 col-md-6">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-muted mb-2">{{ __('myresources.active_assignments') }}</h6>
                            <h3 class="mb-0">{{ $activeAssignments }}</h3>
                            <small class="text-info">
                                <i class="fas fa-clock"></i> {{ __('myresources.scheduled') }}: {{ $scheduledAssignments }}
                            </small>
                        </div>
                        <div class="text-info" style="font-size: 3rem;">
                            <i class="fas fa-tasks"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-3 col-md-6">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-muted mb-2">{{ __('myresources.upcoming_maintenance') }}</h6>
                            <h3 class="mb-0">{{ $upcomingMaintenance->count() }}</h3>
                            <small class="text-warning">
                                <i class="fas fa-calendar-alt"></i> {{ __('myresources.within_7_days') }}
                            </small>
                        </div>
                        <div class="text-warning" style="font-size: 3rem;">
                            <i class="fas fa-wrench"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-3 col-md-6">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-muted mb-2">{{ __('myresources.by_status') }}</h6>
                            <h3 class="mb-0">{{ $resourcesByStatus->count() }}</h3>
                            <small class="text-muted">
                                <i class="fas fa-chart-pie"></i> {{ __('myresources.statuses') }}
                            </small>
                        </div>
                        <div class="text-secondary" style="font-size: 3rem;">
                            <i class="fas fa-chart-bar"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Charts and Recent Activity -->
    <div class="row">
        <div class="col-lg-6 mb-4">
            <div class="card h-100">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="fas fa-chart-pie me-2"></i>
                        {{ __('myresources.resources_by_category') }}
                    </h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>{{ __('myresources.main_category') }}</th>
                                    <th class="text-center">{{ __('common.count') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($resourcesByCategory as $item)
                                <tr>
                                    <td>{{ $item->category->display_name ?? __('common.unspecified') }}</td>
                                    <td class="text-center">
                                        <span class="badge bg-primary">{{ $item->count }}</span>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="2" class="text-center">{{ __('myresources.no_resources_found') }}</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-6 mb-4">
            <div class="card h-100">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="fas fa-chart-bar me-2"></i>
                        {{ __('myresources.resources_by_status') }}
                    </h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>{{ __('myresources.status') }}</th>
                                    <th class="text-center">{{ __('common.count') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($resourcesByStatus as $item)
                                <tr>
                                    <td>{{ $item->status->display_name ?? __('common.unspecified') }}</td>
                                    <td class="text-center">
                                        <span class="badge bg-secondary">{{ $item->count }}</span>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="2" class="text-center">{{ __('myresources.no_statuses_found') }}</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Upcoming Maintenance & Recent Assignments -->
    <div class="row">
        <div class="col-lg-6 mb-4">
            <div class="card h-100">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="fas fa-wrench me-2"></i>
                        {{ __('myresources.upcoming_maintenance_within_7_days') }}
                    </h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>{{ __('myresources.resource') }}</th>
                                    <th>{{ __('myresources.maintenance_date') }}</th>
                                    <th>{{ __('myresources.status') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($upcomingMaintenance as $resource)
                                <tr>
                                    <td>
                                        <strong>{{ $resource->code }}</strong><br>
                                        <small class="text-muted">{{ $resource->name }}</small>
                                    </td>
                                    <td>{{ $resource->next_maintenance_date?->format('Y-m-d') ?? '---' }}</td>
                                    <td>
                                        <span class="badge bg-{{ $resource->status->color ?? 'secondary' }}">
                                            {{ $resource->status->display_name ?? __('common.unspecified') }}
                                        </span>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="3" class="text-center">{{ __('myresources.no_upcoming_maintenance') }}</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-6 mb-4">
            <div class="card h-100">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="fas fa-history me-2"></i>
                        {{ __('myresources.recent_assignments') }}
                    </h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>{{ __('myresources.resource') }}</th>
                                    <th>{{ __('myresources.project') }}</th>
                                    <th>{{ __('myresources.start_date') }}</th>
                                    <th>{{ __('myresources.status') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($recentAssignments as $assignment)
                                <tr>
                                    <td>
                                        @if($assignment->resource)
                                            <strong>{{ $assignment->resource->code }}</strong><br>
                                            <small class="text-muted">{{ $assignment->resource->name }}</small>
                                        @else
                                            <span class="text-muted">---</span>
                                        @endif
                                    </td>
                                    <td>{{ $assignment->project->name ?? '---' }}</td>
                                    <td>{{ $assignment->start_date?->format('Y-m-d') ?? '---' }}</td>
                                    <td>
                                        @if($assignment->status instanceof \Modules\MyResources\Enums\ResourceAssignmentStatus)
                                            <span class="badge bg-{{ $assignment->status->color() }}">
                                                {{ $assignment->status->label() }}
                                            </span>
                                        @else
                                            <span class="badge bg-secondary">{{ $assignment->status ?? '---' }}</span>
                                        @endif
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="4" class="text-center">{{ __('myresources.no_assignments_found') }}</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="fas fa-bolt me-2"></i>
                        {{ __('myresources.quick_actions') }}
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-3">
                            <a href="{{ route('myresources.index') }}" class="btn btn-primary w-100">
                                <i class="fas fa-list me-2"></i>
                                {{ __('myresources.resources_list') }}
                            </a>
                        </div>
                        <div class="col-md-3">
                            <a href="{{ route('myresources.create') }}" class="btn btn-success w-100">
                                <i class="fas fa-plus-circle me-2"></i>
                                {{ __('myresources.new_resource') }}
                            </a>
                        </div>
                        <div class="col-md-3">
                            <a href="{{ route('myresources.assignments.create') }}" class="btn btn-info w-100">
                                <i class="fas fa-tasks me-2"></i>
                                {{ __('myresources.new_assignment') }}
                            </a>
                        </div>
                        <div class="col-md-3">
                            <a href="{{ route('myresources.categories.index') }}" class="btn btn-warning w-100">
                                <i class="fas fa-folder me-2"></i>
                                {{ __('myresources.categories') }}
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
