@extends('progress::layouts.daily-progress')

@section('title', 'Edit Issue')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="page-title-box d-sm-flex align-items-center justify-content-between">
            <h4 class="mb-sm-0">Edit Issue #{{ $issue->id }}</h4>
            <div class="page-title-right">
                <ol class="breadcrumb m-0">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('issues.index') }}">Issues</a></li>
                    <li class="breadcrumb-item active">Edit</li>
                </ol>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-lg-12">
        <div class="card">
            <div class="card-body">
                <form action="{{ route('issues.update', $issue->id) }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')
                    
                    <div class="row">
                        <div class="col-md-12 mb-3">
                            <label class="form-label">Project</label>
                            <select name="project_id" class="form-select" required>
                                <option value="">Select Project</option>
                                @foreach($projects as $project)
                                    <option value="{{ $project->id }}" {{ $issue->project_id == $project->id ? 'selected' : '' }}>{{ $project->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-12 mb-3">
                            <label class="form-label">Title</label>
                            <input type="text" name="title" class="form-control" value="{{ $issue->title }}" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Priority</label>
                            <select name="priority" class="form-select">
                                <option value="Low" {{ $issue->priority == 'Low' ? 'selected' : '' }}>Low</option>
                                <option value="Medium" {{ $issue->priority == 'Medium' ? 'selected' : '' }}>Medium</option>
                                <option value="High" {{ $issue->priority == 'High' ? 'selected' : '' }}>High</option>
                                <option value="Urgent" {{ $issue->priority == 'Urgent' ? 'selected' : '' }}>Urgent</option>
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Status</label>
                            <select name="status" class="form-select">
                                <option value="New" {{ $issue->status == 'New' ? 'selected' : '' }}>New</option>
                                <option value="In Progress" {{ $issue->status == 'In Progress' ? 'selected' : '' }}>In Progress</option>
                                <option value="Testing" {{ $issue->status == 'Testing' ? 'selected' : '' }}>Testing</option>
                                <option value="Closed" {{ $issue->status == 'Closed' ? 'selected' : '' }}>Closed</option>
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Assigned To</label>
                            <select name="assigned_to" class="form-select">
                                <option value="">Unassigned</option>
                                @foreach($users as $user)
                                    <option value="{{ $user->id }}" {{ $issue->assigned_to == $user->id ? 'selected' : '' }}>{{ $user->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Module</label>
                            <input type="text" name="module" class="form-control" value="{{ $issue->module }}">
                        </div>
                        <div class="col-md-12 mb-3">
                            <label class="form-label">Deadline</label>
                            <input type="date" name="deadline" class="form-control" value="{{ $issue->deadline }}">
                        </div>
                        <div class="col-md-12 mb-3">
                            <label class="form-label">Description</label>
                            <textarea name="description" class="form-control" rows="3">{{ $issue->description }}</textarea>
                        </div>
                         <div class="col-md-12 mb-3">
                            <label class="form-label">Reproduce Steps</label>
                            <textarea name="reproduce_steps" class="form-control" rows="3">{{ $issue->reproduce_steps }}</textarea>
                        </div>
                        <div class="col-md-12 mb-3">
                            <label class="form-label">Attachments</label>
                            
                            @if($issue->attachments->count() > 0)
                                <div class="mb-3">
                                    <div class="vstack gap-2">
                                        @foreach($issue->attachments as $attachment)
                                            <div class="border rounded border-dashed p-2">
                                                <div class="d-flex align-items-center">
                                                    <div class="flex-shrink-0 me-3">
                                                        <div class="avatar-sm">
                                                            <div class="avatar-title bg-light text-secondary rounded fs-24">
                                                                <i class="las la-file"></i>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="flex-grow-1 overflow-hidden">
                                                        <h5 class="fs-13 mb-1">{{ $attachment->file_name }}</h5>
                                                        <div class="text-muted fs-11">{{ number_format($attachment->file_size / 1024, 2) }} KB</div>
                                                    </div>
                                                    <div class="flex-shrink-0 ms-2">
                                                        <!-- Uses separate form for deletion to avoid main form conflict -->
                                                        <button type="button" class="btn btn-icon btn-sm btn-ghost-danger" onclick="if(confirm('Delete this attachment?')) { document.getElementById('delete-attachment-{{ $attachment->id }}').submit(); }">
                                                            <i class="las la-trash"></i>
                                                        </button>
                                                    </div>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            @endif

                            <input type="file" name="attachments[]" class="form-control" multiple>
                            <small class="text-muted">Maximum file size: 10MB. Allowed types: JPG, PNG, PDF, DOC, XLS.</small>
                        </div>
                    </div>

                    <div class="mt-3">
                        <button type="submit" class="btn btn-primary">Update</button>
                        <a href="{{ route('issues.index') }}" class="btn btn-light">Cancel</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

@foreach($issue->attachments as $attachment)
    <form id="delete-attachment-{{ $attachment->id }}" action="{{ route('issues.attachments.destroy', $attachment->id) }}" method="POST" style="display: none;">
        @csrf
        @method('DELETE')
    </form>
@endforeach
@endsection
