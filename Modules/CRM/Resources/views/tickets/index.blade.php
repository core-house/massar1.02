@extends('admin.dashboard')

@section('sidebar')
    @include('components.sidebar.crm')
@endsection

@section('content')
    @include('components.breadcrumb', [
        'title' => __('crm::crm.tickets'),
        'breadcrumb_items' => [['label' => __('crm::crm.dashboard'), 'url' => route('admin.dashboard')], ['label' => __('crm::crm.tickets')]],
    ])

    <div class="row">
        <div class="col-lg-12">
            <div class="d-flex justify-content-between align-items-center mb-3">
                @can('create Tickets')
                    <a href="{{ route('tickets.create') }}" class="btn btn-main font-hold fw-bold">
                        <i class="fas fa-plus me-2"></i>
                        {{ __('crm::crm.add_new_ticket') }}
                    </a>
                @endcan

                <!-- Statistics Cards -->
                <div class="d-flex gap-2">
                    <div class="card border-0 shadow-sm" style="min-width: 120px;">
                        <div class="card-body p-2 text-center">
                            <h6 class="mb-0 text-muted small">{{ __('crm::crm.total_tickets') }}</h6>
                            <h4 class="mb-0 fw-bold">{{ collect($tickets)->flatten()->count() }}</h4>
                        </div>
                    </div>
                    <div class="card border-0 shadow-sm bg-warning bg-opacity-10" style="min-width: 120px;">
                        <div class="card-body p-2 text-center">
                            <h6 class="mb-0 text-muted small">{{ __('crm::crm.open') }}</h6>
                            <h4 class="mb-0 fw-bold text-warning">
                                {{ isset($tickets['open']) ? count($tickets['open']) : 0 }}</h4>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Filters Section -->
            <div class="card border-0 shadow-sm mb-3">
                <div class="card-header bg-white border-0">
                    <div class="d-flex justify-content-between align-items-center">
                        <h6 class="mb-0 fw-bold">
                            <i class="fas fa-filter me-2"></i>
                            {{ __('crm::crm.filters') }}
                        </h6>

                    </div>
                </div>
                <div class="card-body" x-show="showFilters" x-collapse>
                    <form method="GET" action="{{ route('tickets.index') }}" id="filterForm">
                        <div class="row g-3">
                            <!-- Search -->
                            <div class="col-md-1">
                                <label class="form-label">{{ __('crm::crm.search') }}</label>
                                <input type="text" name="search" class="form-control"
                                    placeholder="{{ __('crm::crm.search_for_client') }}" value="{{ request('search') }}">
                            </div>

                            <!-- Status Filter -->
                            <div class="col-md-1">
                                <label class="form-label">{{ __('crm::crm.status') }}</label>
                                <select name="status" class="form-select">
                                    <option value="all">{{ __('crm::crm.all_statuses') }}</option>
                                    <option value="open" {{ request('status') === 'open' ? 'selected' : '' }}>
                                        {{ __('crm::crm.open') }}</option>
                                    <option value="in_progress"
                                        {{ request('status') === 'in_progress' ? 'selected' : '' }}>
                                        {{ __('crm::crm.in_progress') }}</option>
                                    <option value="resolved" {{ request('status') === 'resolved' ? 'selected' : '' }}>
                                        {{ __('crm::crm.resolved') }}</option>
                                    <option value="closed" {{ request('status') === 'closed' ? 'selected' : '' }}>
                                        {{ __('crm::crm.closed') }}</option>
                                </select>
                            </div>

                            <!-- Priority Filter -->
                            <div class="col-md-1">
                                <label class="form-label">{{ __('crm::crm.priority') }}</label>
                                <select name="priority" class="form-select">
                                    <option value="all">{{ __('crm::crm.all_priorities') }}</option>
                                    <option value="low" {{ request('priority') === 'low' ? 'selected' : '' }}>
                                        {{ __('crm::crm.low') }}</option>
                                    <option value="medium" {{ request('priority') === 'medium' ? 'selected' : '' }}>
                                        {{ __('crm::crm.medium') }}</option>
                                    <option value="high" {{ request('priority') === 'high' ? 'selected' : '' }}>
                                        {{ __('crm::crm.high') }}</option>
                                </select>
                            </div>

                            <!-- Ticket Type Filter -->
                            <div class="col-md-1">
                                <label class="form-label">{{ __('crm::crm.ticket_type') }}</label>
                                <select name="ticket_type" class="form-select">
                                    <option value="all">{{ __('crm::crm.all_types') }}</option>
                                    @foreach ($ticketTypes as $type)
                                        <option value="{{ $type }}"
                                            {{ request('ticket_type') === $type ? 'selected' : '' }}>
                                            {{ $type }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <!-- Client Filter -->
                            <div class="col-md-1">
                                <label class="form-label">{{ __('crm::crm.client') }}</label>
                                <select name="client_id" class="form-select">
                                    <option value="">{{ __('crm::crm.all_clients') }}</option>
                                    @foreach ($clients as $client)
                                        <option value="{{ $client->id }}"
                                            {{ request('client_id') == $client->id ? 'selected' : '' }}>
                                            {{ $client->cname }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <!-- Assigned To Filter -->
                            <div class="col-md-1">
                                <label class="form-label">{{ __('crm::crm.assigned_to') }}</label>
                                <select name="assigned_to" class="form-select">
                                    <option value="">{{ __('crm::crm.all_users') }}</option>
                                    @foreach ($users as $user)
                                        <option value="{{ $user->id }}"
                                            {{ request('assigned_to') == $user->id ? 'selected' : '' }}>
                                            {{ $user->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <!-- Date From -->
                            <div class="col-md-1">
                                <label class="form-label">{{ __('crm::crm.date_from') }}</label>
                                <input type="date" name="date_from" class="form-control"
                                    value="{{ request('date_from') }}">
                            </div>

                            <!-- Date To -->
                            <div class="col-md-1">
                                <label class="form-label">{{ __('crm::crm.date_to') }}</label>
                                <input type="date" name="date_to" class="form-control" value="{{ request('date_to') }}">
                            </div>
                            <div class="col-md-3 d-flex p-4">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-search me-2"></i>
                                    {{ __('crm::crm.apply_filters') }}
                                </button>
                                <a href="{{ route('tickets.index') }}" class="btn btn-outline-secondary">
                                    <i class="fas fa-redo me-2"></i>
                                    {{ __('crm::crm.reset') }}
                                </a>
                            </div>
                        </div>

                    </form>
                </div>
            </div>

            @if (session('message'))
                <div class="alert alert-success alert-dismissible fade show">
                    {{ session('message') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            <!-- Enhanced Kanban Board -->
            <div class="row g-3">
                @foreach (['open', 'in_progress', 'resolved', 'closed'] as $status)
                    @php
                        $statusConfig = [
                            'open' => ['color' => 'primary', 'icon' => 'fa-folder-open', 'title' => __('crm::crm.open')],
                            'in_progress' => ['color' => 'warning', 'icon' => 'fa-spinner', 'title' => __('crm::crm.in_progress')],
                            'resolved' => ['color' => 'success', 'icon' => 'fa-check-circle', 'title' => __('crm::crm.resolved')],
                            'closed' => ['color' => 'secondary', 'icon' => 'fa-times-circle', 'title' => __('crm::crm.closed')],
                        ];
                        $config = $statusConfig[$status];
                        $count = isset($tickets[$status]) ? count($tickets[$status]) : 0;
                    @endphp

                    <div class="col-md-6 col-lg-3">
                        <div class="card border-0 shadow-sm h-100">
                            <div class="card-header bg-{{ $config['color'] }} border-0">
                                <div class="d-flex justify-content-between align-items-center">
                                    <h6 class="mb-0 text-white fw-bold">
                                        <i class="fas {{ $config['icon'] }} me-2"></i>
                                        {{ $config['title'] }}
                                    </h6>
                                    <span
                                        class="badge bg-white text-{{ $config['color'] }} rounded-pill">{{ $count }}</span>
                                </div>
                            </div>
                            <div class="card-body p-2 status-column" data-status="{{ $status }}"
                                style="min-height: 450px; max-height: 450px; overflow-y: auto;">
                                @if (isset($tickets[$status]) && count($tickets[$status]) > 0)
                                    @foreach ($tickets[$status] as $ticket)
                                        <div class="card mb-2 border-0 shadow-sm hover-shadow transition ticket-card"
                                            draggable="true" data-ticket-id="{{ $ticket->id }}">
                                            <div class="card-body p-3">
                                                <div class="d-flex justify-content-between align-items-start mb-2">
                                                    <h6 class="card-title mb-0 flex-grow-1">
                                                        <a href="{{ route('tickets.show', $ticket->id) }}"
                                                            class="text-decoration-none text-dark fw-semibold">
                                                            {{ Str::limit($ticket->subject, 40) }}
                                                        </a>
                                                    </h6>
                                                    <span
                                                        class="badge bg-{{ $ticket->priority === 'high' ? 'danger' : ($ticket->priority === 'medium' ? 'warning' : 'info') }} badge-sm">
                                                        {{ __(ucfirst($ticket->priority)) }}
                                                    </span>
                                                </div>

                                                <div class="d-flex align-items-center text-muted small mb-2">
                                                    <i class="fas fa-user me-1"></i>
                                                    <span>{{ Str::limit($ticket->client->cname ?? 'N/A', 25) }}</span>
                                                </div>

                                                @if ($ticket->ticket_type)
                                                    <div class="d-flex align-items-center text-muted small mb-2">
                                                        <i class="fas fa-tag me-1"></i>
                                                        <span>{{ Str::limit($ticket->ticket_type, 25) }}</span>
                                                    </div>
                                                @endif

                                                @if ($ticket->ticket_reference)
                                                    <div class="d-flex align-items-center text-muted small mb-2">
                                                        <i class="fas fa-hashtag me-1"></i>
                                                        <span>{{ Str::limit($ticket->ticket_reference, 25) }}</span>
                                                    </div>
                                                @endif

                                                @if ($ticket->status_title)
                                                    <div class="d-flex align-items-center text-muted small mb-2">
                                                        <i class="fas fa-info-circle me-1"></i>
                                                        <span>{{ Str::limit($ticket->status_title, 25) }}</span>
                                                    </div>
                                                @endif

                                                @if ($ticket->assignedTo)
                                                    <div class="d-flex align-items-center text-muted small mb-2">
                                                        <i class="fas fa-user-tie me-1"></i>
                                                        <span>{{ Str::limit($ticket->assignedTo->name, 25) }}</span>
                                                    </div>
                                                @endif

                                                <div
                                                    class="d-flex justify-content-between align-items-center mt-2 pt-2 border-top">
                                                    <div class="d-flex align-items-center">
                                                        <small class="text-muted me-3">
                                                            <i class="far fa-clock me-1"></i>
                                                            {{ $ticket->created_at->diffForHumans() }}
                                                        </small>
                                                        @if ($ticket->comments_count ?? 0 > 0)
                                                            <small class="text-muted">
                                                                <i class="far fa-comments me-1"></i>
                                                                {{ $ticket->comments_count }}
                                                            </small>
                                                        @endif
                                                    </div>
                                                    <div
                                                        class="btn-group btn-group-sm opacity-0 hover-opacity-100 transition">
                                                        @can('edit Tickets')
                                                            <a href="{{ route('tickets.edit', $ticket->id) }}"
                                                                class="btn btn-success btn-icon-square-sm d-inline-flex align-items-center justify-content-center"
                                                                data-bs-toggle="tooltip" title="{{ __('crm::crm.edit') }}">
                                                                <i class="las la-edit"></i>
                                                            </a>
                                                        @endcan
                                                        @can('delete Tickets')
                                                            <form action="{{ route('tickets.destroy', $ticket->id) }}"
                                                                method="POST" class="d-inline"
                                                                onsubmit="return confirm('{{ __('crm::crm.are_you_sure') }}');">
                                                                @csrf
                                                                @method('DELETE')
                                                                <button type="submit"
                                                                    class="btn btn-danger btn-icon-square-sm d-inline-flex align-items-center justify-content-center"
                                                                    data-bs-toggle="tooltip" title="{{ __('crm::crm.delete') }}">
                                                                    <i class="las la-trash"></i>
                                                                </button>
                                                            </form>
                                                        @endcan
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                @else
                                    <div class="text-center py-5">
                                        <i class="fas fa-inbox text-muted" style="font-size: 3rem; opacity: 0.3;"></i>
                                        <p class="text-muted mt-2 mb-0">{{ __('crm::crm.no_tickets') }}</p>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>

    <style>
        .hover-shadow {
            transition: all 0.3s ease;
        }

        .hover-shadow:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15) !important;
        }

        .badge-sm {
            font-size: 0.7rem;
            padding: 0.25rem 0.5rem;
        }

        .bg-opacity-10 {
            --bs-bg-opacity: 0.1;
        }

        .opacity-0 {
            opacity: 0;
        }

        .card:hover .opacity-0 {
            opacity: 1;
        }

        .transition {
            transition: all 0.3s ease;
        }

        /* Drag and Drop Styles */
        .ticket-card {
            cursor: grab;
        }

        .ticket-card.dragging {
            opacity: 0.5;
            cursor: grabbing;
        }

        .status-column.dragover {
            background-color: rgba(0, 0, 0, 0.05);
            border-radius: 0.5rem;
        }

        .btn-icon-square-sm {
            width: 32px;
            height: 32px;
            padding: 0;
        }
    </style>

    @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                const draggables = document.querySelectorAll('.ticket-card');
                const containers = document.querySelectorAll('.status-column');

                draggables.forEach(draggable => {
                    draggable.addEventListener('dragstart', () => {
                        draggable.classList.add('dragging');
                    });

                    draggable.addEventListener('dragend', () => {
                        draggable.classList.remove('dragging');
                    });
                });

                containers.forEach(container => {
                    container.addEventListener('dragover', e => {
                        e.preventDefault();
                        const afterElement = getDragAfterElement(container, e.clientY);
                        const draggable = document.querySelector('.dragging');
                        if (afterElement == null) {
                            container.appendChild(draggable);
                        } else {
                            container.insertBefore(draggable, afterElement);
                        }
                        container.classList.add('dragover');
                    });

                    container.addEventListener('dragleave', () => {
                        container.classList.remove('dragover');
                    });

                    container.addEventListener('drop', e => {
                        e.preventDefault();
                        container.classList.remove('dragover');
                        const draggable = document.querySelector('.dragging');
                        const ticketId = draggable.getAttribute('data-ticket-id');
                        const newStatus = container.getAttribute('data-status');

                        updateTicketStatus(ticketId, newStatus);
                    });
                });

                function getDragAfterElement(container, y) {
                    const draggableElements = [...container.querySelectorAll('.ticket-card:not(.dragging)')];

                    return draggableElements.reduce((closest, child) => {
                        const box = child.getBoundingClientRect();
                        const offset = y - box.top - box.height / 2;
                        if (offset < 0 && offset > closest.offset) {
                            return {
                                offset: offset,
                                element: child
                            };
                        } else {
                            return closest;
                        }
                    }, {
                        offset: Number.NEGATIVE_INFINITY
                    }).element;
                }

                function updateTicketStatus(ticketId, newStatus) {
                    fetch('{{ route('tickets.updateStatus') }}', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                'Accept': 'application/json'
                            },
                            body: JSON.stringify({
                                ticket_id: ticketId,
                                new_status: newStatus
                            })
                        })
                        .then(response => response.json())
                        .then(data => {
                            if (data.success) {
                                // Optional: Show success toast
                                // console.log('Status updated');
                            } else {
                                alert('Failed to update status');
                                location.reload(); // Revert changes
                            }
                        })
                        .catch(error => {
                            console.error('Error:', error);
                            alert('An error occurred');
                            location.reload(); // Revert changes
                        });
                }
            });
        </script>
    @endpush
@endsection
