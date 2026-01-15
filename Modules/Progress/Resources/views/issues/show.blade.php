@extends('progress::layouts.daily-progress')

@section('title', $issue->title)

@section('content')
<div class="container-fluid p-4">
    
    <!-- 1. Header Section -->
    <div class="row mb-3">
        <div class="col-12">
            <!-- Breadcrumb -->
            <div class="mb-2">
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb m-0 text-muted small">
                        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}" class="text-muted">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('issues.index') }}" class="text-muted">Issues</a></li>
                        <li class="breadcrumb-item active text-dark" aria-current="page">{{ $issue->title }}</li>
                    </ol>
                </nav>
            </div>

            <div class="d-flex justify-content-between align-items-start">
                <div>
                    <!-- Title -->
                    <h2 class="fw-bold text-dark mb-2">{{ $issue->title }}</h2>
                    <!-- Badges -->
                    <div class="d-flex gap-2">
                        @php
                            $priorityClass = match($issue->priority) {
                                'Medium' => 'bg-warning text-dark',
                                'High' => 'bg-danger text-white',
                                'Low' => 'bg-info text-white',
                                default => 'bg-secondary text-white'
                            };
                            $statusClass = match($issue->status) {
                                'New' => 'bg-primary text-white',
                                'In Progress' => 'bg-info text-white',
                                'Closed' => 'bg-success text-white',
                                default => 'bg-secondary text-white'
                            };
                            $isOverdue = $issue->deadline && \Carbon\Carbon::parse($issue->deadline)->isPast() && $issue->status !== 'Closed';
                        @endphp
                        
                        <span class="badge {{ $priorityClass }} rounded-1">{{ $issue->priority }}</span>
                        <span class="badge {{ $statusClass }} rounded-1">{{ $issue->status }}</span>
                        @if($isOverdue)
                            <span class="badge bg-danger text-white rounded-1">Overdue</span>
                        @endif
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="d-flex gap-2">
                    <a href="{{ route('issues.edit', $issue->id) }}" class="btn btn-warning fw-bold text-dark shadow-sm">
                        <i class="las la-edit me-1"></i> Edit
                    </a>
                    <a href="{{ route('issues.index') }}" class="btn btn-secondary shadow-sm">
                        <i class="las la-arrow-left me-1"></i> Back
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- 2. Meta Stats Row -->
    <div class="row p-4 mb-4 bg-white rounded border-bottom border-top border-light">
        <div class="col-md-3">
            <div class="d-flex gap-3">
                <i class="las la-project-diagram fs-3 mt-1 text-dark"></i>
                <div>
                    <div class="fw-bold text-dark mb-1">Project:</div>
                    <div class="text-secondary small mb-3">{{ $issue->project->name ?? 'N/A' }}</div>
                    
                    <div class="fw-bold text-dark mb-1"><i class="las la-cubes me-1"></i> Module:</div>
                    <div class="text-secondary small">{{ $issue->module ?? 'N/A' }}</div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="d-flex gap-3">
                <i class="las la-user fs-3 mt-1 text-dark"></i>
                <div>
                    <div class="fw-bold text-dark mb-1">Reporter:</div>
                    <div class="text-secondary small mb-3">{{ $issue->reporter->name ?? 'Unknown' }}</div>
                    
                    <div class="fw-bold text-dark mb-1"><i class="las la-clock me-1"></i> Created At:</div>
                    <div class="text-secondary small">{{ $issue->created_at->format('Y-m-d H:i') }}</div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="d-flex gap-3">
                <i class="las la-user-check fs-3 mt-1 text-dark"></i>
                <div>
                    <div class="fw-bold text-dark mb-1">Assigned To:</div>
                    <div class="text-secondary small">{{ $issue->assignee->name ?? 'Unassigned' }}</div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="d-flex gap-3">
                <i class="las la-calendar fs-3 mt-1 text-dark"></i>
                <div>
                    <div class="fw-bold text-dark mb-1">Deadline:</div>
                    <div class="text-secondary small">{{ $issue->deadline ?? 'N/A' }}</div>
                </div>
            </div>
        </div>
    </div>

    <!-- 3. Main Content & Sidebar -->
    <div class="row">
        <!-- Main Content -->
        <div class="col-lg-9">
            
            <!-- Description -->
            <div class="card shadow-none border mb-4">
                <div class="card-header bg-white py-3 border-bottom">
                    <h5 class="mb-0 fw-bold text-dark"><i class="las la-align-left me-2"></i> Description</h5>
                </div>
                <div class="card-body text-secondary">
                    {{ $issue->description ?? 'No description provided' }}
                </div>
            </div>

            <!-- Attachments -->
            @if($issue->attachments->count() > 0)
            <div class="card shadow-none border mb-4">
                <div class="card-header bg-white py-3 border-bottom">
                    <h5 class="mb-0 fw-bold text-dark"><i class="las la-paperclip me-2"></i> Attachments ({{ $issue->attachments->count() }})</h5>
                </div>
                <div class="card-body">
                    <div class="list-group list-group-flush border rounded-0">
                        @foreach($issue->attachments as $attachment)
                        <div class="list-group-item d-flex justify-content-between align-items-center p-3 border-bottom-0">
                            <div class="d-flex align-items-center">
                                <i class="las la-file-alt fs-3 text-secondary me-3"></i>
                                <div>
                                    <a href="{{ Storage::url($attachment->file_path) }}" target="_blank" class="text-primary fw-bold text-decoration-none">{{ $attachment->file_name }}</a>
                                    <div class="small text-muted">{{ number_format($attachment->file_size / 1024, 2) }} KB</div>
                                </div>
                            </div>
                            <a href="{{ Storage::url($attachment->file_path) }}" target="_blank" class="btn btn-outline-secondary btn-sm"><i class="las la-download"></i></a>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
            @endif

            <!-- Comments -->
            <div class="card shadow-none border mb-4">
                <div class="card-header bg-white py-3 border-bottom">
                    <h5 class="mb-0 fw-bold text-dark"><i class="las la-comments me-2"></i> Comments ({{ $issue->comments->count() }})</h5>
                </div>
                <div class="card-body">
                    <!-- Comment Form -->
                    <div class="mb-4">
                        <form action="{{ route('issues.comments.store', $issue->id) }}" method="POST">
                            @csrf
                            <textarea name="comment" class="form-control mb-2" rows="3" placeholder="Add Comment" required></textarea>
                            <button type="submit" class="btn btn-primary shadow-sm"><i class="las la-paper-plane me-1"></i> Add Comment</button>
                        </form>
                    </div>

                    <!-- Comments List -->
                    <div class="d-flex flex-column gap-3">
                        @forelse($issue->comments as $comment)
                        <div class="border rounded p-3 bg-light">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <h6 class="fw-bold mb-0 text-dark">{{ $comment->user->name }}</h6>
                                <small class="text-muted">{{ $comment->created_at->diffForHumans() }}</small>
                            </div>
                            <p class="mb-0 text-secondary">{{ $comment->comment }}</p>
                            @if(auth()->id() == $comment->user_id || auth()->user()->hasRole('admin'))
                                <div class="text-end mt-2">
                                    <form action="{{ route('issues.comments.destroy', $comment->id) }}" method="POST" onsubmit="return confirm('Delete this comment?')" class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                        <button class="btn btn-link text-danger p-0 small text-decoration-none">Delete</button>
                                    </form>
                                </div>
                            @endif
                        </div>
                        @empty
                        <div class="text-center text-muted py-3">No comments yet.</div>
                        @endforelse
                    </div>
                </div>
            </div>

        </div>

        <!-- Sidebar -->
        <div class="col-lg-3">
            <div class="card shadow-none border">
                <div class="card-header bg-white py-3 border-bottom">
                    <h5 class="mb-0 fw-bold text-dark"><i class="las la-info-circle me-2"></i> Issue Information</h5>
                </div>
                <div class="card-body">
                    <ul class="list-unstyled mb-0 d-flex flex-column gap-3">
                        <li>
                            <div class="fw-bold text-dark mb-1">Issue ID:</div>
                            <div class="text-secondary">#{{ $issue->id }}</div>
                        </li>
                        <li>
                            <div class="fw-bold text-dark mb-1">Created:</div>
                            <div class="text-secondary">{{ $issue->created_at->format('Y-m-d H:i') }}</div>
                        </li>
                        <li>
                            <div class="fw-bold text-dark mb-1">Last Updated:</div>
                            <div class="text-secondary">{{ $issue->updated_at->format('Y-m-d H:i') }}</div>
                        </li>
                    </ul>
                </div>
            </div>
            
          
        </div>
    </div>

</div>
@endsection
