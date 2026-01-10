@extends('progress::layouts.daily-progress')

@section('title', 'Project Drafts')

@section('content')
<div class="container-fluid p-4">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-1">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}" class="text-muted">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('progress.project.index') }}" class="text-muted">{{ __('projects.list') }}</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Drafts</li>
                </ol>
            </nav>
            <h4 class="fw-bold text-dark"><i class="las la-file-alt me-2"></i>Project Drafts</h4>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('progress.project.index') }}" class="btn btn-outline-primary rounded-pill px-4 shadow-sm">
                <i class="las la-arrow-left me-1"></i> All Projects
            </a>
            <a href="{{ route('progress.project.create') }}" class="btn btn-primary rounded-pill px-4 shadow-sm">
                <i class="las la-plus me-1"></i> New Project
            </a>
        </div>
    </div>

    <!-- Drafts Table Card -->
    <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-light">
                        <tr>
                            <th class="ps-4 py-3 text-uppercase small fw-bold text-muted border-0">#</th>
                            <th class="py-3 text-uppercase small fw-bold text-muted border-0">Project Name</th>
                            <th class="py-3 text-uppercase small fw-bold text-muted border-0">Client</th>
                            <th class="py-3 text-uppercase small fw-bold text-muted border-0">Status</th>
                            <th class="py-3 text-uppercase small fw-bold text-muted border-0">Type of Project</th>
                            <th class="py-3 text-uppercase small fw-bold text-muted border-0">Created At</th>
                            <th class="py-3 text-uppercase small fw-bold text-muted border-0">Completion</th>
                            <th class="pe-4 py-3 text-uppercase small fw-bold text-muted border-0 text-end">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($projects as $index => $project)
                        @php
                            // Calculate Progress
                            $totalQty = $project->items->sum('total_quantity');
                            $completedQty = $project->items->sum('completed_quantity');
                            $progressPercent = $totalQty > 0 ? round(($completedQty / $totalQty) * 100) : 0;
                            
                            $progressColor = 'primary';
                            if($progressPercent < 25) $progressColor = 'danger';
                            elseif($progressPercent < 50) $progressColor = 'warning';
                            elseif($progressPercent < 75) $progressColor = 'info';
                            else $progressColor = 'success';
                        @endphp
                        <tr>
                            <td class="ps-4 fw-bold text-muted">{{ $loop->iteration }}</td>
                            <td>
                                <div class="d-flex align-items-center">
                                    <div class="rounded-circle bg-warning bg-opacity-10 text-warning p-2 me-2 d-flex align-items-center justify-content-center" style="width: 35px; height: 35px;">
                                        <i class="las la-file-alt"></i>
                                    </div>
                                    <span class="fw-bold text-dark">{{ $project->name }}</span>
                                </div>
                            </td>
                            <td>{{ $project->client->name ?? 'N/A' }}</td>
                            <td>
                                <span class="badge bg-warning text-dark border border-warning">
                                    <i class="las la-pen me-1"></i> Draft
                                </span>
                            </td>
                            <td>
                                <span class="text-muted">{{ $project->type->name ?? 'Not set' }}</span>
                            </td>
                            <td class="text-muted">
                                {{ $project->created_at->format('d-m-Y H:i') }}
                            </td>
                            <td style="min-width: 150px;">
                                @php
                                    $completionPercent = $project->completion_percentage;
                                    $progressColor = 'primary';
                                    if($completionPercent < 50) $progressColor = 'warning'; // Matches screenshot yellow/orange
                                    elseif($completionPercent < 100) $progressColor = 'info';
                                    else $progressColor = 'success';
                                @endphp
                                <div class="d-flex align-items-center gap-2">
                                    <div class="progress flex-grow-1" style="height: 6px;">
                                        <div class="progress-bar bg-{{ $progressColor }}" role="progressbar" style="width: {{ $completionPercent }}%"></div>
                                    </div>
                                    <span class="small fw-bold {{ 'text-'.$progressColor }}">{{ $completionPercent }}%</span>
                                </div>
                            </td>
                            <td class="pe-4 text-end">
                                <div class="d-flex justify-content-end gap-1">
                                    <a href="{{ route('progress.project.edit', $project->id) }}" class="btn btn-warning btn-sm text-dark shadow-sm" title="Edit Draft">
                                        <i class="las la-pen"></i>
                                    </a>
                                    
                                    @if($completionPercent >= 100)
                                    <!-- Publish Button -->
                                    <form action="{{ route('progress.project.publish', $project->id) }}" method="POST" class="d-inline">
                                        @csrf
                                        @method('PUT')
                                        <button type="submit" class="btn btn-success btn-sm shadow-sm" title="Publish Project">
                                            <i class="las la-check-circle"></i>
                                        </button>
                                    </form>
                                    @else
                                    <!-- Locked Button -->
                                    <button type="button" class="btn btn-secondary btn-sm shadow-sm" disabled title="Complete project to publish">
                                        <i class="las la-lock"></i>
                                    </button>
                                    @endif

                                    <!-- Delete Button -->
                                    <form action="{{ route('progress.project.destroy', $project->id) }}" method="POST" class="d-inline" onsubmit="return confirm('{{ __('general.delete_confirm') }}')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-danger btn-sm shadow-sm" title="Delete Draft">
                                            <i class="las la-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="8" class="text-center py-5">
                                <div class="text-muted">
                                    <i class="las la-inbox fs-1 mb-3"></i>
                                    <p class="mb-0">No drafts found.</p>
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <!-- Pagination if needed -->
            @if($projects instanceof \Illuminate\Pagination\LengthAwarePaginator)
            <div class="p-3 border-top">
                {{ $projects->links() }}
            </div>
            @endif
        </div>
    </div>
</div>
@endsection
