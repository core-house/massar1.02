@extends('admin.dashboard')

@section('sidebar')
    @include('components.sidebar.tasks')
@endsection

@section('content')
    @include('components.breadcrumb', [
        'title' => __('crm::crm.deleted_tasks'),
        'breadcrumb_items' => [
            ['label' => __('crm::crm.dashboard'), 'url' => route('admin.dashboard')], 
            ['label' => __('crm::crm.tasks_and_activities'), 'url' => route('tasks.index')],
            ['label' => __('crm::crm.deleted_tasks')]
        ],
    ])

    @push('styles')
    <style>
        .task-title-hover {
            color: #0d6efd;
            font-weight: 500;
        }
        
        .task-title-hover:hover {
            color: #0a58ca;
        }
        
        .tooltip-inner {
            max-width: 400px;
            text-align: right;
            padding: 10px 15px;
            font-size: 14px;
        }
    </style>
    @endpush

    <div class="row">
        <div class="col-lg-12">
            <div class="mb-3 d-flex justify-content-between align-items-center">
                <div>
                    <a href="{{ route('tasks.index') }}" class="btn btn-outline-secondary">
                        <i class="las la-arrow-right"></i> {{ __('crm::crm.back_to_tasks') }}
                    </a>
                </div>
                <div class="alert alert-warning mb-0 py-2 px-3">
                    <i class="las la-exclamation-triangle"></i>
                    {{ __('crm::crm.these_tasks_have_been_deleted') }}
                </div>
            </div>

            <!-- Filters Section -->
            <div class="card mb-3">
                <div class="card-header bg-light">
                    <h5 class="mb-0">
                        <i class="las la-filter"></i> {{ __('crm::crm.filters') }}
                        @if(request()->hasAny(['task_type_id', 'priority', 'status', 'search']))
                            <span class="badge bg-primary">{{ __('crm::crm.active') }}</span>
                        @endif
                    </h5>
                </div>
                <div class="card-body">
                    <form method="GET" action="{{ route('tasks.trashed') }}" id="filterForm">
                        <div class="row g-3">
                            <!-- Search -->
                            <div class="col-md-3">
                                <label class="form-label fw-bold">{{ __('crm::crm.search') }}</label>
                                <input type="text" name="search" class="form-control" 
                                       placeholder="{{ __('crm::crm.search_in_title_or_comments') }}" 
                                       value="{{ request('search') }}">
                            </div>

                            <!-- Task Type Filter -->
                            <div class="col-md-3">
                                <label class="form-label fw-bold">{{ __('crm::crm.task_type') }}</label>
                                <select name="task_type_id" class="form-select">
                                    <option value="">{{ __('crm::crm.all_types') }}</option>
                                    @foreach($taskTypes as $id => $title)
                                        <option value="{{ $id }}" {{ request('task_type_id') == $id ? 'selected' : '' }}>
                                            {{ $title }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <!-- Priority Filter -->
                            <div class="col-md-2">
                                <label class="form-label fw-bold">{{ __('crm::crm.priority') }}</label>
                                <select name="priority" class="form-select">
                                    <option value="">{{ __('crm::crm.all_priorities') }}</option>
                                    @foreach($priorities as $priority)
                                        <option value="{{ $priority->value }}" {{ request('priority') == $priority->value ? 'selected' : '' }}>
                                            {{ $priority->label() }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <!-- Status Filter -->
                            <div class="col-md-2">
                                <label class="form-label fw-bold">{{ __('crm::crm.status') }}</label>
                                <select name="status" class="form-select">
                                    <option value="">{{ __('crm::crm.all_statuses') }}</option>
                                    @foreach($statuses as $status)
                                        <option value="{{ $status->value }}" {{ request('status') == $status->value ? 'selected' : '' }}>
                                            {{ $status->label() }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <!-- Buttons -->
                            <div class="col-md-2 d-flex align-items-end gap-2">
                                <button type="submit" class="btn btn-primary">
                                    <i class="las la-filter"></i> {{ __('crm::crm.apply') }}
                                </button>
                                <a href="{{ route('tasks.trashed') }}" class="btn btn-secondary">
                                    <i class="las la-redo"></i>
                                </a>
                            </div>
                        </div>
                        <div class="row mt-2">
                            <div class="col-md-12">
                                <span class="text-muted">
                                    {{ __('crm::crm.total') }}: <strong>{{ $tasks->total() }}</strong> {{ __('crm::crm.deleted_tasks') }}
                                </span>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <div class="card">
                <div class="card-body">
                    <div class="table-responsive" style="overflow-x: auto;">
                        <table class="table table-striped mb-0" style="min-width: 1200px;">
                            <thead class="table-light text-center align-middle">
                                <tr>
                                    <th>#</th>
                                    <th>{{ __('crm::crm.client') }}</th>
                                    <th>{{ __('crm::crm.assigned_to') }}</th>
                                    <th>{{ __('crm::crm.created_by') }}</th>
                                    <th>{{ __('crm::crm.task_type') }}</th>
                                    <th>{{ __('crm::crm.task_title') }}</th>
                                    <th>{{ __('crm::crm.priority') }}</th>
                                    <th>{{ __('crm::crm.status') }}</th>
                                    <th>{{ __('crm::crm.start_date') }}</th>
                                    <th>{{ __('crm::crm.due_date') }}</th>
                                    <th>{{ __('crm::crm.deleted_at') }}</th>
                                    @can('delete Tasks')
                                        <th>{{ __('crm::crm.actions') }}</th>
                                    @endcan
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($tasks as $task)
                                    <tr class="text-center">
                                        <td>{{ $loop->iteration }}</td>
                                        <td>{{ optional($task->client)->cname }}</td>
                                        <td>{{ optional($task->user)->name }}</td>
                                        <td>{{ optional($task->creator)->name }}</td>
                                        <td>{{ $task->taskType->title }}</td>
                                        <td>
                                            <span class="task-title-hover" 
                                                  data-bs-toggle="tooltip" 
                                                  data-bs-placement="top" 
                                                  data-bs-html="true"
                                                  title="{{ $task->client_comment || $task->user_comment ? '<strong>التعليقات:</strong><br>' . ($task->client_comment ?? '') . '<br>' . ($task->user_comment ?? '') : 'لا توجد تعليقات' }}"
                                                  style="cursor: help; text-decoration: underline dotted;">
                                                {{ $task->title }}
                                            </span>
                                        </td>
                                        <td>
                                            @php
                                                $priority = is_string($task->priority)
                                                    ? \Modules\CRM\Enums\TaskPriorityEnum::tryFrom($task->priority)
                                                    : $task->priority;
                                            @endphp

                                            @if ($priority)
                                                <span class="badge bg-{{ $priority->color() }}">
                                                    {{ $priority->label() }}
                                                </span>
                                            @endif
                                        </td>
                                        <td>
                                            @php
                                                $status = is_string($task->status)
                                                    ? \Modules\CRM\Enums\TaskStatusEnum::tryFrom($task->status)
                                                    : $task->status;
                                            @endphp

                                            @if ($status)
                                                <span class="badge bg-{{ $status->color() }}">
                                                    {{ $status->label() }}
                                                </span>
                                            @endif
                                        </td>
                                        <td>{{ \Carbon\Carbon::parse($task->start_date)->format('Y-m-d') }}</td>
                                        <td>{{ \Carbon\Carbon::parse($task->due_date)->format('Y-m-d') }}</td>
                                        <td>
                                            <span class="badge bg-danger">
                                                {{ \Carbon\Carbon::parse($task->deleted_at)->format('Y-m-d H:i') }}
                                            </span>
                                        </td>
                                        @can('delete Tasks')
                                            <td>
                                                <!-- Restore Button -->
                                                <form action="{{ route('tasks.restore', $task->id) }}" method="POST"
                                                    style="display:inline-block;"
                                                    onsubmit="return confirm('{{ __('crm::crm.confirm_restore_task') }}');">
                                                    @csrf
                                                    <button type="submit" class="btn btn-success btn-icon-square-sm"
                                                        title="{{ __('Restore') }}">
                                                        <i class="las la-undo"></i>
                                                    </button>
                                                </form>

                                                <!-- Permanent Delete Button -->
                                                <form action="{{ route('tasks.forceDelete', $task->id) }}" method="POST"
                                                    style="display:inline-block;"
                                                    onsubmit="return confirm('{{ __('crm::crm.confirm_permanent_delete_task') }}');">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-danger btn-icon-square-sm"
                                                        title="{{ __('Delete Permanently') }}">
                                                        <i class="las la-trash-alt"></i>
                                                    </button>
                                                </form>
                                            </td>
                                        @endcan
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="12" class="text-center">
                                            <div class="alert alert-info py-3 mb-0"
                                                style="font-size: 1.2rem; font-weight: 500;">
                                                <i class="las la-info-circle me-2"></i>
                                                {{ __('crm::crm.no_deleted_tasks_found') }}
                                            </div>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination Links -->
                    <div class="mt-3 d-flex justify-content-center">
                        {{ $tasks->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
<script>
    // Initialize Bootstrap tooltips
    document.addEventListener('DOMContentLoaded', function() {
        var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
        var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl, {
                html: true,
                trigger: 'hover'
            });
        });
    });
</script>
@endpush
