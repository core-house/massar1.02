@extends('admin.dashboard')

@section('sidebar')
    @include('components.sidebar.myresources')
@endsection

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="page-title-box">
                <h4 class="page-title">{{ __('myresources.edit_assignment') }}</h4>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <form action="{{ route('myresources.assignments.update', $assignment) }}" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="resource_id" class="form-label">{{ __('myresources.resource') }}</label>
                                <select name="resource_id" id="resource_id" class="form-select @error('resource_id') is-invalid @enderror">
                                    <option value="">{{ __('myresources.select_category') }}</option>
                                    @foreach($resources as $resource)
                                        <option value="{{ $resource->id }}" {{ old('resource_id', $assignment->resource_id) == $resource->id ? 'selected' : '' }}>
                                            {{ $resource->code }} - {{ $resource->name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('resource_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="project_id" class="form-label">{{ __('myresources.project') }} <span class="text-danger">*</span></label>
                                <select name="project_id" id="project_id" class="form-select @error('project_id') is-invalid @enderror" required>
                                    <option value="">{{ __('myresources.select_project') }}</option>
                                    @foreach($projects as $project)
                                        <option value="{{ $project->id }}" {{ old('project_id', $assignment->project_id) == $project->id ? 'selected' : '' }}>
                                            {{ $project->name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('project_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <label for="start_date" class="form-label">{{ __('myresources.start_date') }} <span class="text-danger">*</span></label>
                                <input type="date" name="start_date" id="start_date" class="form-control @error('start_date') is-invalid @enderror"
                                       value="{{ old('start_date', $assignment->start_date?->format('Y-m-d')) }}" required>
                                @error('start_date')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-4 mb-3">
                                <label for="end_date" class="form-label">{{ __('myresources.end_date') }}</label>
                                <input type="date" name="end_date" id="end_date" class="form-control @error('end_date') is-invalid @enderror"
                                       value="{{ old('end_date', $assignment->end_date?->format('Y-m-d')) }}">
                                @error('end_date')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-4 mb-3">
                                <label for="daily_cost" class="form-label">{{ __('myresources.daily_cost') }}</label>
                                <input type="number" step="0.01" name="daily_cost" id="daily_cost" class="form-control @error('daily_cost') is-invalid @enderror"
                                       value="{{ old('daily_cost', $assignment->daily_cost) }}">
                                @error('daily_cost')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="status" class="form-label">{{ __('myresources.status') }} <span class="text-danger">*</span></label>
                                <select name="status" id="status" class="form-select @error('status') is-invalid @enderror" required>
                                    <option value="">{{ __('myresources.select_status') }}</option>
                                    @php $currentStatus = old('status', $assignment->status->value ?? $assignment->status); @endphp
                                    <option value="scheduled" {{ $currentStatus == 'scheduled' ? 'selected' : '' }}>{{ __('myresources.scheduled') }}</option>
                                    <option value="active"    {{ $currentStatus == 'active'    ? 'selected' : '' }}>{{ __('myresources.active') }}</option>
                                    <option value="completed" {{ $currentStatus == 'completed' ? 'selected' : '' }}>{{ __('common.completed') }}</option>
                                    <option value="cancelled" {{ $currentStatus == 'cancelled' ? 'selected' : '' }}>{{ __('common.cancelled') }}</option>
                                </select>
                                @error('status')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="assignment_type" class="form-label">{{ __('myresources.assignment_type') }} <span class="text-danger">*</span></label>
                                <select name="assignment_type" id="assignment_type" class="form-select @error('assignment_type') is-invalid @enderror" required>
                                    <option value="">{{ __('myresources.select_type') }}</option>
                                    @php $currentType = old('assignment_type', $assignment->assignment_type->value ?? $assignment->assignment_type); @endphp
                                    <option value="current"  {{ $currentType == 'current'  ? 'selected' : '' }}>{{ __('myresources.current') }}</option>
                                    <option value="upcoming" {{ $currentType == 'upcoming' ? 'selected' : '' }}>{{ __('myresources.upcoming') }}</option>
                                    <option value="past"     {{ $currentType == 'past'     ? 'selected' : '' }}>{{ __('myresources.historical') }}</option>
                                </select>
                                @error('assignment_type')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-12 mb-3">
                                <label for="notes" class="form-label">{{ __('myresources.notes') }}</label>
                                <textarea name="notes" id="notes" rows="3" class="form-control @error('notes') is-invalid @enderror">{{ old('notes', $assignment->notes) }}</textarea>
                                @error('notes')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i> {{ __('myresources.save') }}
                            </button>
                            <a href="{{ route('myresources.assignments.index') }}" class="btn btn-secondary">
                                <i class="fas fa-times"></i> {{ __('myresources.cancel') }}
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
