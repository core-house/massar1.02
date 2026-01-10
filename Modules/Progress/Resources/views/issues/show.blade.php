@extends('admin.dashboard')
@section('sidebar')
    @include('components.sidebar.daily_progress')
@endsection
@section('title', $issue->title)

@section('content')
<div class="row">
    <div class="col-12">
        <div class="page-title-box d-sm-flex align-items-center justify-content-between">
            <h4 class="mb-sm-0">View Issue</h4>
            <div class="page-title-right">
                <ol class="breadcrumb m-0">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('issues.index') }}">Issues</a></li>
                    <li class="breadcrumb-item active">#{{ $issue->id }}</li>
                </ol>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-lg-9">
        <div class="card">
            <div class="card-body">
                <div class="d-flex align-items-center mb-4">
                    <div class="flex-grow-1">
                        <h5 class="card-title mb-0 fs-18">{{ $issue->title }}</h5>
                    </div>
                    <div class="flex-shrink-0">
                         <a href="{{ route('issues.edit', $issue->id) }}" class="btn btn-warning btn-sm"><i class="las la-pen"></i> Edit</a>
                         <a href="{{ route('issues.index') }}" class="btn btn-soft-secondary btn-sm"><i class="las la-arrow-left"></i> Back</a>
                    </div>
                </div>

                <div class="mb-4">
                    <h6 class="fw-semibold text-uppercase mb-2 fs-13 text-muted">Description</h6>
                     <div class="p-3 bg-light rounded text-dark">
                        {{ $issue->description ?? 'No description provided.' }}
                     </div>
                </div>

                <div class="mb-4">
                    <h6 class="fw-semibold text-uppercase mb-2 fs-13 text-muted">Reproduce Steps</h6>
                     <div class="p-3 bg-light rounded text-dark">
                        {{ $issue->reproduce_steps ?? 'No steps provided.' }}
                     </div>
                </div>

                @if($issue->attachments->count() > 0)
                <div class="mb-4">
                    <h6 class="fw-semibold text-uppercase mb-2 fs-13 text-muted">Attachments ({{ $issue->attachments->count() }})</h6>
                    <div class="row g-2">
                        @foreach($issue->attachments as $attachment)
                        <div class="col-xxl-3 col-lg-4 col-sm-6">
                            <div class="border rounded border-dashed p-2 h-100">
                                <div class="d-flex align-items-center">
                                    <div class="flex-shrink-0 me-2">
                                        <div class="avatar-sm">
                                            <div class="avatar-title bg-light text-secondary rounded fs-20">
                                                <i class="las la-file"></i>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="flex-grow-1 overflow-hidden">
                                        <h5 class="fs-13 mb-1"><a href="{{ Storage::url($attachment->file_path) }}" target="_blank" class="text-body text-truncate d-block">{{ $attachment->file_name }}</a></h5>
                                        <div class="text-muted fs-11">{{ number_format($attachment->file_size / 1024, 2) }} KB</div>
                                    </div>
                                    <div class="flex-shrink-0 ms-2">
                                        <a href="{{ Storage::url($attachment->file_path) }}" target="_blank" class="btn btn-icon btn-sm btn-soft-info"><i class="las la-download"></i></a>
                                    </div>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
                @endif
            </div>
        </div>

        <div class="card">
            <div class="card-header align-items-center d-flex">
                 <h4 class="card-title mb-0 flex-grow-1">Comments</h4>
                 <span class="badge bg-soft-info text-info">{{ $issue->comments->count() }}</span>
            </div>
            <div class="card-body">
                 <div class="mb-4">
                     <form action="{{ route('issues.comments.store', $issue->id) }}" method="POST">
                        @csrf
                        <div class="d-flex align-items-start gap-3">
                            <div class="flex-grow-1">
                                <textarea name="comment" class="form-control" rows="3" placeholder="Add a comment..." required></textarea>
                            </div>
                            <div class="flex-shrink-0">
                                <button type="submit" class="btn btn-primary"><i class="las la-paper-plane"></i></button>
                            </div>
                        </div>
                     </form>
                 </div>

                 <div class="vstack gap-3">
                    @forelse($issue->comments as $comment)
                    <div class="d-flex">
                        <div class="flex-shrink-0 me-3">
                             <div class="avatar-sm">
                                <span class="avatar-title bg-soft-primary text-primary rounded-circle fs-16">
                                    {{ strtoupper(substr($comment->user->name, 0, 1)) }}
                                </span>
                            </div>
                        </div>
                        <div class="flex-grow-1">
                            <div class="p-3 bg-light rounded">
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <h6 class="fs-13 mb-0 fw-bold">{{ $comment->user->name }}</h6>
                                    <div class="d-flex align-items-center gap-2">
                                        <small class="text-muted">{{ $comment->created_at->diffForHumans() }}</small>
                                        @if($comment->user_id == auth()->id() || auth()->user()->hasRole('admin'))
                                        <form action="{{ route('issues.comments.destroy', $comment->id) }}" method="POST" onsubmit="return confirm('Are you sure?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-ghost-danger btn-icon fs-12"><i class="las la-trash"></i></button>
                                        </form>
                                        @endif
                                    </div>
                                </div>
                                <p class="text-muted mb-0">{{ $comment->comment }}</p>
                            </div>
                        </div>
                    </div>
                    @if(!$loop->last)
                        <hr class="border-dashed my-2">
                    @endif
                    @empty
                    <div class="text-center text-muted">No comments yet. Be the first to start the discussion!</div>
                    @endforelse
                 </div>
            </div>
        </div>
    </div>

    <div class="col-lg-3">
        <div class="card">
            <div class="card-body">
                <h6 class="card-title mb-3">Details</h6>
                <div class="table-responsive">
                    <table class="table table-borderless mb-0">
                        <tbody>
                            <tr>
                                <td class="ps-0 text-muted fw-semibold">Status</td>
                                <td class="text-end">
                                    @php
                                        $statusClass = match($issue->status) {
                                            'New' => 'bg-soft-primary text-primary',
                                            'In Progress' => 'bg-soft-info text-info',
                                            'Closed' => 'bg-soft-success text-success',
                                            default => 'bg-soft-secondary text-secondary'
                                        };
                                    @endphp
                                    <span class="badge {{ $statusClass }}">{{ $issue->status }}</span>
                                </td>
                            </tr>
                            <tr>
                                <td class="ps-0 text-muted fw-semibold">Priority</td>
                                <td class="text-end">
                                    @php
                                        $priorityClass = match($issue->priority) {
                                            'Low' => 'bg-soft-info text-info',
                                            'Medium' => 'bg-soft-warning text-warning',
                                            'High' => 'bg-soft-danger text-danger',
                                            'Urgent' => 'bg-dark text-white',
                                            default => 'bg-soft-secondary text-secondary'
                                        };
                                    @endphp
                                    <span class="badge {{ $priorityClass }}">{{ $issue->priority }}</span>
                                </td>
                            </tr>
                            <tr>
                                <td class="ps-0 text-muted fw-semibold">Project</td>
                                <td class="text-end text-truncate" style="max-width: 150px;">{{ $issue->project->name ?? 'N/A' }}</td>
                            </tr>
                             <tr>
                                <td class="ps-0 text-muted fw-semibold">Module</td>
                                <td class="text-end">{{ $issue->module ?? 'N/A' }}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <hr>
                
                <h6 class="card-title mb-3">People</h6>
                 <div class="d-flex align-items-center mb-3">
                    <div class="flex-shrink-0">
                        <div class="avatar-xs">
                             <span class="avatar-title bg-soft-info text-info rounded-circle fs-16">
                                <i class="las la-user"></i>
                            </span>
                        </div>
                    </div>
                    <div class="flex-grow-1 ms-2">
                        <p class="mb-0 fs-12 text-muted">Reporter</p>
                        <h6 class="mb-0 fs-13">{{ $issue->reporter->name ?? 'Unknown' }}</h6>
                    </div>
                </div>
                 <div class="d-flex align-items-center">
                    <div class="flex-shrink-0">
                        <div class="avatar-xs">
                             <span class="avatar-title bg-soft-success text-success rounded-circle fs-16">
                                <i class="las la-user-check"></i>
                            </span>
                        </div>
                    </div>
                    <div class="flex-grow-1 ms-2">
                        <p class="mb-0 fs-12 text-muted">Assignee</p>
                        <h6 class="mb-0 fs-13">{{ $issue->assignee->name ?? 'Unassigned' }}</h6>
                    </div>
                </div>

                 <hr>

                 <h6 class="card-title mb-3">Dates</h6>
                  <div class="mb-2">
                    <p class="mb-0 fs-12 text-muted">Deadline</p>
                    <h6 class="mb-0 fs-13">{{ $issue->deadline ?? 'N/A' }}</h6>
                </div>
                <div class="mb-2">
                    <p class="mb-0 fs-12 text-muted">Created</p>
                    <h6 class="mb-0 fs-13">{{ $issue->created_at->format('d M, Y') }}</h6>
                </div>
                 <div>
                    <p class="mb-0 fs-12 text-muted">Last Updated</p>
                    <h6 class="mb-0 fs-13">{{ $issue->updated_at->diffForHumans() }}</h6>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
