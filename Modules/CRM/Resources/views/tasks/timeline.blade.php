@extends('admin.dashboard')

@section('sidebar')
    @include('components.sidebar.tasks')
@endsection

@section('content')
    <div class="row mb-3">
        <div class="col-md-12 d-flex justify-content-between align-items-center">
            <div>
                @include('components.breadcrumb', [
                    'title' => __('crm::crm.tasks_timeline'),
                    'items' => [
                        ['label' => __('crm::crm.dashboard'), 'url' => route('admin.dashboard')],
                        ['label' => __('crm::crm.tasks'), 'url' => route('tasks.index')],
                        ['label' => __('crm::crm.timeline')],
                    ],
                ])
            </div>
            <div>
                <a href="{{ route('tasks.index') }}" class="btn btn-outline-secondary">
                    <i class="fas fa-list"></i> {{ __('crm::crm.list_view') }}
                </a>
                <a href="{{ route('tasks.kanban') }}" class="btn btn-outline-primary">
                    <i class="las la-th-large"></i> {{ __('crm::crm.kanban_view') }}
                </a>
                @can('create Tasks')
                    <a href="{{ route('tasks.create') }}" class="btn btn-main">
                        <i class="fas fa-plus"></i> {{ __('crm::crm.add_new_task') }}
                    </a>
                @endcan
            </div>
        </div>
    </div>

    <!-- Filters Section -->
    <div class="row mb-3">
        <div class="col-lg-12">
            <div class="card shadow-sm">
                <div class="card-body p-3">
                    <form method="GET" action="{{ route('tasks.timeline') }}" id="filterForm">
                        <div style="display: flex; flex-wrap: wrap; gap: 1rem; align-items: flex-end;">
                            <!-- Date Filter -->
                            <div style="display: flex; flex-direction: column;">
                                <label class="form-label fw-bold" style="font-size: 0.9rem; margin-bottom: 0.25rem;">{{ __('crm::crm.date') }}</label>
                                <select name="date_filter" class="form-select form-select-sm" style="width: 140px; font-size: 0.9rem;">
                                    <option value="today" {{ request('date_filter', 'today') == 'today' ? 'selected' : '' }}>{{ __('crm::crm.today') }}</option>
                                    <option value="week" {{ request('date_filter') == 'week' ? 'selected' : '' }}>{{ __('crm::crm.this_week') }}</option>
                                    <option value="month" {{ request('date_filter') == 'month' ? 'selected' : '' }}>{{ __('crm::crm.this_month') }}</option>
                                    <option value="overdue" {{ request('date_filter') == 'overdue' ? 'selected' : '' }}>{{ __('crm::crm.overdue') }}</option>
                                    <option value="upcoming" {{ request('date_filter') == 'upcoming' ? 'selected' : '' }}>{{ __('crm::crm.upcoming') }}</option>
                                    <option value="all" {{ request('date_filter') == 'all' ? 'selected' : '' }}>{{ __('crm::crm.all') }}</option>
                                </select>
                            </div>

                            <!-- Task Type Filter -->
                            <div style="display: flex; flex-direction: column;">
                                <label class="form-label fw-bold" style="font-size: 0.9rem; margin-bottom: 0.25rem;">{{ __('crm::crm.type') }}</label>
                                <select name="task_type_id" class="form-select form-select-sm" style="width: 140px; font-size: 0.9rem;">
                                    <option value="">{{ __('crm::crm.all') }}</option>
                                    @foreach($taskTypes as $id => $title)
                                        <option value="{{ $id }}" {{ request('task_type_id') == $id ? 'selected' : '' }}>{{ $title }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <!-- User Filter -->
                            <div style="display: flex; flex-direction: column;">
                                <label class="form-label fw-bold" style="font-size: 0.9rem; margin-bottom: 0.25rem;">{{ __('crm::crm.user') }}</label>
                                <select name="user_id" class="form-select form-select-sm" style="width: 140px; font-size: 0.9rem;">
                                    <option value="">{{ __('crm::crm.all') }}</option>
                                    @foreach($users as $id => $name)
                                        <option value="{{ $id }}" {{ request('user_id') == $id ? 'selected' : '' }}>{{ $name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <!-- Priority Filter Collapse -->
                            <div style="display: flex; flex-direction: column;">
                                <label class="form-label fw-bold" style="font-size: 0.9rem; margin-bottom: 0.25rem;">{{ __('crm::crm.priority') }}</label>
                                <div class="dropdown">
                                    <button class="btn btn-outline-secondary btn-sm dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false" style="font-size: 0.9rem; width: 140px; padding: 0.375rem 0.5rem;">
                                        {{ __('crm::crm.select') }}
                                    </button>
                                    <ul class="dropdown-menu" style="max-height: 200px; overflow-y: auto; min-width: 140px;">
                                        @foreach($priorities as $priority)
                                            <li>
                                                <label class="dropdown-item" style="cursor: pointer; padding: 0.4rem 0.8rem; font-size: 0.9rem;">
                                                    <input class="form-check-input me-2" type="checkbox" name="priority[]"
                                                        value="{{ $priority->value }}"
                                                        {{ in_array($priority->value, (array)request('priority', [])) ? 'checked' : '' }}
                                                        onchange="">
                                                    {{ $priority->label() }}
                                                </label>
                                            </li>
                                        @endforeach
                                    </ul>
                                </div>
                            </div>

                            <!-- Status Filter Collapse -->
                            <div style="display: flex; flex-direction: column;">
                                <label class="form-label fw-bold" style="font-size: 0.9rem; margin-bottom: 0.25rem;">{{ __('crm::crm.status') }}</label>
                                <div class="dropdown">
                                    <button class="btn btn-outline-secondary btn-sm dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false" style="font-size: 0.9rem; width: 140px; padding: 0.375rem 0.5rem;">
                                        {{ __('crm::crm.select') }}
                                    </button>
                                    <ul class="dropdown-menu" style="max-height: 200px; overflow-y: auto; min-width: 140px;">
                                        @foreach($statuses as $status)
                                            <li>
                                                <label class="dropdown-item" style="cursor: pointer; padding: 0.4rem 0.8rem; font-size: 0.9rem;">
                                                    <input class="form-check-input me-2" type="checkbox" name="status[]"
                                                        value="{{ $status->value }}"
                                                        {{ in_array($status->value, (array)request('status', [])) ? 'checked' : '' }}
                                                        onchange="">
                                                    {{ $status->label() }}
                                                </label>
                                            </li>
                                        @endforeach
                                    </ul>
                                </div>
                            </div>

                            <!-- Start Date -->
                            <div style="display: flex; flex-direction: column;">
                                <label class="form-label fw-bold" style="font-size: 0.9rem; margin-bottom: 0.25rem;">{{ __('crm::crm.from') }}</label>
                                <input type="date" name="start_date" class="form-control form-control-sm" value="{{ request('start_date') }}" style="width: 140px; font-size: 0.9rem;">
                            </div>

                            <!-- End Date -->
                            <div style="display: flex; flex-direction: column;">
                                <label class="form-label fw-bold" style="font-size: 0.9rem; margin-bottom: 0.25rem;">{{ __('crm::crm.to') }}</label>
                                <input type="date" name="end_date" class="form-control form-control-sm" value="{{ request('end_date') }}" style="width: 140px; font-size: 0.9rem;">
                            </div>

                            <!-- Apply Button -->
                            <div style="display: flex; flex-direction: column;">
                                <label style="font-size: 0.9rem; margin-bottom: 0.25rem; visibility: hidden;">{{ __('crm::crm.apply') }}</label>
                                <button type="submit" class="btn btn-primary btn-sm" style="font-size: 0.9rem; padding: 0.375rem 0.6rem; white-space: nowrap;">
                                    <i class="las la-filter"></i> {{ __('crm::crm.apply') }}
                                </button>
                            </div>

                            <!-- Reset Button -->
                            <div style="display: flex; flex-direction: column;">
                                <label style="font-size: 0.9rem; margin-bottom: 0.25rem; visibility: hidden;">{{ __('crm::crm.reset') }}</label>
                                <a href="{{ route('tasks.timeline') }}" class="btn btn-secondary btn-sm" style="font-size: 0.9rem; padding: 0.375rem 0.6rem; white-space: nowrap;">
                                    <i class="las la-redo"></i> {{ __('crm::crm.reset') }}
                                </a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-body">
                    @if($tasksByDate->isEmpty())
                        <div class="text-center py-5">
                            <i class="las la-calendar-times" style="font-size: 4rem; color: #ccc;"></i>
                            <h5 class="text-muted mt-3">{{ __('crm::crm.no_tasks_found') }}</h5>
                            <p class="text-muted">{{ __('crm::crm.try_changing_date_filter_or_create_new_task') }}</p>
                        </div>
                    @else
                        <div class="timeline">
                            @foreach($tasksByDate as $date => $tasks)
                                <!-- Date Header -->
                                <div class="timeline-date">
                                    <div class="timeline-date-badge">
                                        <i class="las la-calendar"></i>
                                    </div>
                                    <h5 class="timeline-date-text">
                                        {{ \Carbon\Carbon::parse($date)->format('l, F d, Y') }}
                                        <span class="badge bg-secondary ms-2">{{ $tasks->count() }} {{ __('crm::crm.tasks') }}</span>
                                    </h5>
                                </div>

                                <!-- Tasks for this date -->
                                @foreach($tasks as $task)
                                    <div class="timeline-item">
                                        <div class="timeline-marker bg-{{ $task->status->color() }}"></div>
                                        <div class="timeline-content">
                                            <div class="card shadow-sm border-start border-{{ $task->priority->color() }}" style="border-width: 3px !important;">
                                                <div class="card-body">
                                                    <div class="row">
                                                        <div class="col-md-8">
                                                            <div class="d-flex align-items-start gap-3">
                                                                <div class="flex-grow-1">
                                                                    <h6 class="mb-2">
                                                                        <a href="{{ route('tasks.show', $task->id) }}" class="text-decoration-none text-dark">
                                                                            {{ $task->title }}
                                                                        </a>
                                                                    </h6>

                                                                    <div class="d-flex flex-wrap gap-2 mb-2">
                                                                        <span class="badge bg-{{ $task->priority->color() }}">
                                                                            <i class="las la-flag"></i> {{ $task->priority->label() }}
                                                                        </span>
                                                                        <span class="badge bg-{{ $task->status->color() }}">
                                                                            {{ $task->status->label() }}
                                                                        </span>
                                                                        <span class="badge bg-info">
                                                                            <i class="las la-tag"></i> {{ $task->taskType->title }}
                                                                        </span>
                                                                        @if($task->duration)
                                                                            <span class="badge bg-secondary">
                                                                                <i class="las la-clock"></i> {{ $task->formatted_duration }} {{ __('crm::crm.hours') }}
                                                                            </span>
                                                                        @endif
                                                                    </div>

                                                                    <div class="text-muted small">
                                                                        <i class="las la-user-circle"></i>
                                                                        <strong>{{ __('crm::crm.client') }}:</strong> {{ optional($task->client)->cname ?? __('crm::crm.na') }}
                                                                        <span class="mx-2">|</span>
                                                                        <i class="las la-user-tag"></i>
                                                                        <strong>{{ __('crm::crm.assigned_to') }}:</strong> {{ optional($task->user)->name ?? __('crm::crm.na') }}
                                                                        <span class="mx-2">|</span>
                                                                        <i class="las la-user-plus"></i>
                                                                        <strong>{{ __('crm::crm.created_by') }}:</strong> {{ optional($task->creator)->name ?? __('crm::crm.unknown') }}
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>

                                                        <div class="col-md-4 text-end">
                                                            <div class="mb-2">
                                                                <small class="text-muted">
                                                                    <i class="las la-calendar-check"></i> {{ __('crm::crm.end_date') }}:
                                                                </small>
                                                                <br>
                                                                <span class="badge bg-{{ \Carbon\Carbon::parse($task->due_date)->isPast() && $task->status->value != 'completed' ? 'danger' : 'light' }} text-dark">
                                                                    {{ \Carbon\Carbon::parse($task->due_date)->format('Y-m-d') }}
                                                                </span>
                                                            </div>

                                                            <div class="d-flex justify-content-end gap-1 mt-3">
                                                                @can('view Tasks')
                                                                    <a href="{{ route('tasks.show', $task->id) }}"
                                                                       class="btn btn-sm btn-outline-primary"
                                                                       title="{{ __('crm::crm.view') }}">
                                                                        <i class="las la-eye"></i>
                                                                    </a>
                                                                @endcan
                                                                @can('edit Tasks')
                                                                    <a href="{{ route('tasks.edit', $task->id) }}"
                                                                       class="btn btn-sm btn-outline-success"
                                                                       title="{{ __('crm::crm.edit') }}">
                                                                        <i class="las la-edit"></i>
                                                                    </a>
                                                                @endcan
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection

@push('styles')
<style>
    .timeline {
        position: relative;
        padding: 20px 0;
    }

    .timeline::before {
        content: '';
        position: absolute;
        left: 30px;
        top: 0;
        bottom: 0;
        width: 2px;
        background: #e0e0e0;
    }

    .timeline-date {
        position: relative;
        margin-bottom: 30px;
        padding-left: 70px;
    }

    .timeline-date-badge {
        position: absolute;
        left: 15px;
        width: 32px;
        height: 32px;
        background: #007bff;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-size: 1.2rem;
        z-index: 1;
    }

    .timeline-date-text {
        margin: 0;
        padding: 5px 15px;
        background: #f8f9fa;
        border-radius: 5px;
        display: inline-block;
    }

    .timeline-item {
        position: relative;
        margin-bottom: 20px;
        padding-left: 70px;
    }

    .timeline-marker {
        position: absolute;
        left: 22px;
        width: 16px;
        height: 16px;
        border-radius: 50%;
        border: 3px solid white;
        box-shadow: 0 0 0 2px #e0e0e0;
        z-index: 1;
    }

    .timeline-content {
        position: relative;
    }

    .timeline-content .card {
        transition: all 0.3s ease;
    }

    .timeline-content .card:hover {
        transform: translateX(5px);
        box-shadow: 0 4px 12px rgba(0,0,0,0.15) !important;
    }
</style>
@endpush
