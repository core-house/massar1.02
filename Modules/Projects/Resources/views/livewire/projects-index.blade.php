<div class="container-fluid p-3 p-md-4">
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center mb-3 mb-md-4 gap-2">
        <h2 class="h4 h3-md mb-0">{{ __('Projects') }}</h2>
        <a href="{{ route('projects.create') }}" class="btn btn-main">
            <i class="las la-plus"></i> <span class="d-none d-sm-inline">{{ __('add_new_project') }}</span>
        </a>
    </div>
    
    @if (session()->has('success'))
        <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 3000)"
            class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"
                x-on:click="show = false"></button>
        </div>
    @endif

    <style>
        @media (min-width: 992px) {
            .kanban-column {
                max-width: 350px !important;
            }
        }
    </style>

    <div class="kanban-board d-flex flex-column flex-lg-row overflow-auto gap-3" style="min-height: 60vh;">
        @php
            $statuses = [
                'pending' => __('status_pending'),
                'in_progress' => __('status_in_progress'),
                'completed' => __('status_completed'),
                'cancelled' => __('status_cancelled'),
            ];
        @endphp
        @foreach ($statuses as $statusKey => $statusLabel)
            <div class="kanban-column card flex-shrink-0" style="width: 100%; min-width: 280px; max-width: 100%;">
                <div class="card-header text-center fw-bold bg-light">{{ $statusLabel }}</div>
                <div class="card-body p-2" style="min-height: 300px;">
                    @php
                        $projectsForStatus = $projects->filter(fn($p) => $p->status === $statusKey);
                    @endphp
                    @forelse($projectsForStatus as $project)
                        @php
                            $budgetStatus = $this->getBudgetStatus($project);
                        @endphp
                        <div class="kanban-card card mb-3 shadow-sm border-{{ $this->getStatusBadgeClass($project->status) }}">
                            <div class="card-body p-2 p-sm-3">
                                <div class="d-flex flex-column flex-sm-row justify-content-between align-items-start align-items-sm-center mb-2 gap-2">
                                    <span class="fw-bold text-break">{{ $project->name }}</span>
                                    <div class="d-flex gap-1 flex-wrap">
                                        <span class="badge {{ $this->getStatusBadgeClass($project->status) }} flex-shrink-0">
                                            {{ $this->getStatusText($project->status) }}
                                        </span>
                                        <span class="badge {{ $this->getPriorityBadgeClass($project->priority ?? 'medium') }} flex-shrink-0">
                                            {{ $this->getPriorityText($project->priority ?? 'medium') }}
                                        </span>
                                    </div>
                                </div>
                                <div class="text-muted mb-2 small">
                                    {{ Str::limit($project->description, 50) }}
                                </div>
                                <div class="mb-2 small">
                                    <div class="mb-1"><strong>{{ __('start_date') }}:</strong> {{ $project->start_date?->format('Y-m-d') ?? __('unspecified') }}</div>
                                    <div class="mb-1"><strong>{{ __('expected_end_date') }}:</strong> {{ $project->end_date?->format('Y-m-d') ?? __('unspecified') }}</div>
                                    <div class="mb-1"><strong>{{ __('actual_end_date') }}:</strong> {{ $project->actual_end_date?->format('Y-m-d') ?? __('unspecified') }}</div>
                                </div>

                                <div class="mb-2 small">
                                    <div class="mb-1"><strong>{{ __('created_by') }}:</strong> {{ $project->createdBy?->name ?? '-' }}</div>
                                    <div><strong>{{ __('updated_by') }}:</strong> {{ $project->updatedBy?->name ?? '-' }}</div>
                                </div>
                                
                                <div class="mb-2">
                                    <span class="badge bg-{{ $budgetStatus['class'] }} small">
                                        <i class="las la-wallet"></i> {{ $budgetStatus['text'] }}
                                    </span>
                                </div>
                               
                                <div class="d-flex flex-wrap gap-2 mt-2">
                                    <a href="{{ route('projects.show', $project) }}" class="btn btn-primary btn-sm flex-fill flex-sm-grow-0">
                                        <i class="las la-eye"></i> <span class="d-none d-sm-inline">{{ __('view') }}</span>
                                    </a>
                                    <a href="{{ route('projects.edit', $project) }}" class="btn btn-success btn-sm flex-fill flex-sm-grow-0">
                                        <i class="las la-edit"></i> <span class="d-none d-sm-inline">{{ __('Edit') }}</span>
                                    </a>
                                    <button type="button" class="btn btn-danger btn-sm flex-fill flex-sm-grow-0"
                                        wire:click="delete({{ $project->id }})"
                                        onclick="confirm('{{ __('confirm_delete_project') }}') || event.stopImmediatePropagation()">
                                        <i class="las la-trash"></i> <span class="d-none d-sm-inline">{{ __('Delete') }}</span>
                                    </button>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="alert alert-info py-2 mb-0 text-center small">
                            <i class="las la-info-circle me-2"></i>
                            {{ __('no_projects_in_status') }}
                        </div>
                    @endforelse
                </div>
            </div>
        @endforeach
    </div>
</div>