@extends('admin.dashboard')

@section('sidebar')
    @include('components.sidebar.tasks')
@endsection

@section('content')
    <div class="row mb-3">
        <div class="col-md-12 d-flex justify-content-between align-items-center">
            <div>
                @include('components.breadcrumb', [
                    'title' => __('crm::crm.tasks_kanban'),
                    'items' => [
                        ['label' => __('crm::crm.dashboard'), 'url' => route('admin.dashboard')],
                        ['label' => __('crm::crm.tasks'), 'url' => route('tasks.index')],
                        ['label' => __('crm::crm.kanban')],
                    ],
                ])
            </div>
            <div>
                <a href="{{ route('tasks.index') }}" class="btn btn-outline-secondary">
                    <i class="fas fa-list"></i> {{ __('crm::crm.list_view') }}
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
    <div class="card mb-3">
        <div class="card-body p-2">
            <form method="GET" action="{{ route('tasks.kanban') }}" id="filterForm">
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

                    <!-- Priority Filter -->
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
                                                {{ in_array($priority->value, (array)request('priority', [])) ? 'checked' : '' }}>
                                            {{ $priority->label() }}
                                        </label>
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                    </div>

                    <!-- Status Filter -->
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
                                                {{ in_array($status->value, (array)request('status', [])) ? 'checked' : '' }}>
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
                        <a href="{{ route('tasks.kanban') }}" class="btn btn-secondary btn-sm" style="font-size: 0.9rem; padding: 0.375rem 0.6rem; white-space: nowrap;">
                            <i class="las la-redo"></i> {{ __('crm::crm.reset') }}
                        </a>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <div class="kanban-board-container" style="overflow-x: auto; min-height: 70vh;" x-data="kanbanBoard()">
        <div class="d-flex flex-nowrap pb-3" style="gap: 1.5rem;">
            @foreach ($statuses as $status)
                <div class="kanban-column card border-top-0"
                    style="flex: 0 0 calc(25% - 1.125rem); min-width: 280px; background-color: #f8f9fa;">
                    <div class="card-header bg-white border-bottom-0 border-top-3 border-{{ $status->color() }} pt-3 pb-2">
                        <div class="d-flex justify-content-between align-items-center">
                            <h6 class="fw-bold mb-0 text-{{ $status->color() }}">
                                {{ $status->label() }}
                            </h6>
                            <span class="badge bg-light text-dark rounded-pill border">
                                {{ $tasks->where('status', $status->value)->count() }}
                            </span>
                        </div>
                    </div>

                    <div class="card-body p-2" @dragover.prevent="dragOver($event)"
                        @drop="drop($event, '{{ $status->value }}')"
                        style="overflow-y: auto; max-height: calc(100vh - 250px);">

                        @forelse ($tasks->where('status', $status->value) as $task)
                            <div class="card mb-2 shadow-sm task-card" draggable="true"
                                @dragstart="dragStart($event, {{ $task->id }})"
                                style="cursor: grab; border-left: 3px solid var(--bs-{{ (is_string($task->priority) ? \Modules\CRM\Enums\TaskPriorityEnum::tryFrom($task->priority)?->color() : $task->priority?->color()) ?? 'secondary' }});">
                                <div class="card-body p-3">
                                    <div class="d-flex justify-content-between mb-2">
                                        <small class="text-muted text-uppercase" style="font-size: 0.7rem;">
                                            {{ $task->taskType->title ?? __('crm::crm.task') }}
                                        </small>

                                        @php
                                            $priority = is_string($task->priority)
                                                ? \Modules\CRM\Enums\TaskPriorityEnum::tryFrom($task->priority)
                                                : $task->priority;
                                            $pColor = $priority ? $priority->color() : 'secondary';
                                            $pLabel = $priority ? $priority->label() : __('crm::crm.normal');
                                        @endphp
                                        <span class="badge bg-soft-{{ $pColor }} text-{{ $pColor }}"
                                            style="font-size: 0.65rem;">
                                            {{ $pLabel }}
                                        </span>
                                    </div>

                                    <h6 class="card-title mb-2 text-truncate" title="{{ $task->title }}">
                                        {{ $task->title }}
                                    </h6>

                                    <div class="mb-2">
                                        <small class="text-muted d-block">
                                            <i class="las la-user-circle"></i>
                                            {{ optional($task->client)->cname ?? __('crm::crm.no_client') }}
                                        </small>
                                        <small class="text-muted d-block">
                                            <i class="las la-user-tag"></i> {{ __('crm::crm.assigned_to') }}: {{ optional($task->user)->name ?? __('crm::crm.unassigned') }}
                                        </small>
                                        <small class="text-muted d-block">
                                            <i class="las la-user-plus"></i> {{ __('crm::crm.created_by') }}: {{ optional($task->creator)->name ?? __('crm::crm.unknown') }}
                                        </small>
                                    </div>

                                    <div class="d-flex justify-content-between align-items-center mt-3 pt-2 border-top">
                                        <div class="d-flex align-items-center gap-1">
                                            @can('view Tasks')
                                                <a href="{{ route('tasks.show', $task->id) }}" 
                                                   class="btn btn-sm btn-outline-primary btn-icon-square-sm"
                                                   title="{{ __('crm::crm.view') }}">
                                                    <i class="las la-eye"></i>
                                                </a>
                                            @endcan
                                            @can('edit Tasks')
                                                <a href="{{ route('tasks.edit', $task->id) }}" 
                                                   class="btn btn-sm btn-outline-success btn-icon-square-sm"
                                                   title="{{ __('crm::crm.edit') }}">
                                                    <i class="las la-edit"></i>
                                                </a>
                                            @endcan
                                        </div>

                                        <div class="d-flex align-items-center gap-2">
                                            <div class="d-flex align-items-center">
                                                @if (optional($task->user)->avatar)
                                                    <img src="{{ optional($task->user)->avatar }}" class="rounded-circle me-1"
                                                        width="20" height="20">
                                                @else
                                                    <div class="rounded-circle bg-secondary text-white d-flex align-items-center justify-content-center me-1"
                                                        style="width: 20px; height: 20px; font-size: 10px;">
                                                        {{ substr(optional($task->user)->name ?? 'U', 0, 1) }}
                                                    </div>
                                                @endif
                                                <small class="text-muted" style="font-size: 0.75rem;">
                                                    {{ Str::limit(optional($task->user)->name, 10) }}
                                                </small>
                                            </div>

                                            <small
                                                class="text-{{ \Carbon\Carbon::parse($task->due_date)->isPast() && $status->value != 'مكتملة' ? 'danger' : 'muted' }}"
                                                style="font-size: 0.75rem;">
                                                <i class="las la-calendar"></i>
                                                {{ \Carbon\Carbon::parse($task->due_date)->format('M d') }}
                                            </small>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div class="text-center p-3 text-muted dashed-border">
                                <small>{{ __('crm::crm.no_tasks') }}</small>
                            </div>
                        @endforelse
                    </div>
                </div>
            @endforeach
        </div>
    </div>

    <script>
        function kanbanBoard() {
            return {
                draggingTaskId: null,

                dragStart(event, taskId) {
                    this.draggingTaskId = taskId;
                    event.dataTransfer.effectAllowed = 'move';
                    event.dataTransfer.setData('text/plain', taskId);
                    // Visual feedback
                    event.target.style.opacity = '0.5';
                },

                dragOver(event) {
                    event.preventDefault(); // Necessary to allow dropping
                    event.dataTransfer.dropEffect = 'move';
                    return false;
                },

                drop(event, newStatus) {
                    event.preventDefault();
                    // Reset opacity
                    document.querySelectorAll('.task-card').forEach(el => el.style.opacity = '1');

                    const taskId = this
                        .draggingTaskId; // event.dataTransfer.getData('text/plain'); // Sometimes dataTransfer is empty in Chrome on drop if not handled right

                    if (!taskId) return;

                    // AJAX Update
                    fetch('{{ route('tasks.updateStatus') }}', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': '{{ csrf_token() }}'
                            },
                            body: JSON.stringify({
                                task_id: taskId,
                                status: newStatus
                            })
                        })
                        .then(response => response.json())
                        .then(data => {
                            if (data.success) {
                                // Reload to reflect changes
                                window.location.reload();
                            } else {
                                alert('Error updating status');
                            }
                        })
                        .catch(error => {
                            console.error('Error:', error);
                            alert('Something went wrong');
                        });
                }
            }
        }
    </script>

    <style>
        .kanban-column {
            transition: all 0.3s ease;
        }

        .bg-soft-danger {
            background-color: rgba(220, 53, 69, 0.1);
        }

        .bg-soft-warning {
            background-color: rgba(255, 193, 7, 0.1);
        }

        .bg-soft-success {
            background-color: rgba(25, 135, 84, 0.1);
        }

        .bg-soft-info {
            background-color: rgba(13, 202, 240, 0.1);
        }

        .bg-soft-secondary {
            background-color: rgba(108, 117, 125, 0.1);
        }
    </style>
@endsection
