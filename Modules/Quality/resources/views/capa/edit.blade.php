@extends('admin.dashboard')

@section('sidebar')
    @include('components.sidebar.quality')
@endsection

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-12">
            <h2><i class="fas fa-edit me-2"></i>{{ __("quality::quality.edit capa") }}</h2>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('quality.dashboard') }}">{{ __("quality::quality.quality") }}</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('quality.capa.index') }}">{{ __("quality::quality.capa") }}</a></li>
                    <li class="breadcrumb-item active">{{ __("quality::quality.edit") }}</li>
                </ol>
            </nav>
        </div>
    </div>
 
    <form action="{{ route('quality.capa.update', $capa) }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')
        <div class="row">
            <div class="col-lg-8">
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="mb-0">{{ __("quality::quality.capa details") }}</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">{{ __("quality::quality.related ncr") }}</label>
                                <input type="text" class="form-control" value="{{ $capa->nonConformanceReport->ncr_number ?? '---' }}" readonly>
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="form-label">{{ __("quality::quality.capa type") }}</label>
                                <input type="text" class="form-control" value="{{ $capa->action_type == 'corrective' ? __("quality::quality.corrective") : __("quality::quality.preventive") }}" readonly>
                            </div>

                            <div class="col-12 mb-3">
                                <label class="form-label required">{{ __("quality::quality.action description") }}</label>
                                <textarea name="action_description" rows="4" 
                                          class="form-control @error('action_description') is-invalid @enderror" 
                                          placeholder="{{ __("quality::quality.explain the required action...") }}" required>{{ old('action_description', $capa->action_description) }}</textarea>
                                @error('action_description')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>

                            <div class="col-12 mb-3">
                                <label class="form-label">{{ __("quality::quality.root cause") }}</label>
                                <textarea name="root_cause_analysis" rows="3" class="form-control" 
                                          placeholder="{{ __("quality::quality.what is the root cause of the problem?") }}">{{ old('root_cause_analysis', $capa->root_cause_analysis) }}</textarea>
                            </div>

                            <div class="col-12 mb-3">
                                <label class="form-label">{{ __("quality::quality.preventive measures") }}</label>
                                <textarea name="preventive_measures" rows="3" class="form-control" 
                                          placeholder="{{ __("quality::quality.what are the preventive measures to prevent recurrence?") }}">{{ old('preventive_measures', $capa->preventive_measures) }}</textarea>
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="form-label">{{ __("quality::quality.planned start date") }}</label>
                                <input type="text" class="form-control" value="{{ $capa->planned_start_date->format('Y-m-d') }}" readonly>
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="form-label required">{{ __("quality::quality.planned completion date") }}</label>
                                <input type="date" name="planned_completion_date" 
                                       class="form-control @error('planned_completion_date') is-invalid @enderror" 
                                       value="{{ old('planned_completion_date', $capa->planned_completion_date->format('Y-m-d')) }}" required>
                                @error('planned_completion_date')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-4">
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="mb-0">{{ __("quality::quality.assignment and follow-up") }}</h5>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label class="form-label required">{{ __("quality::quality.assigned to") }}</label>
                            <select name="responsible_person" class="form-select @error('responsible_person') is-invalid @enderror" required>
                                <option value="">{{ __("quality::quality.select responsible") }}</option>
                                @foreach($users as $user)
                                    <option value="{{ $user->id }}" {{ old('responsible_person', $capa->responsible_person) == $user->id ? 'selected' : '' }}>{{ $user->name }}</option>
                                @endforeach
                            </select>
                            @error('responsible_person')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label">{{ __("quality::quality.priority") }}</label>
                            <select name="priority" class="form-select">
                                <option value="low" {{ old('priority', $capa->priority) == 'low' ? 'selected' : '' }}>{{ __("quality::quality.low") }}</option>
                                <option value="medium" {{ old('priority', $capa->priority) == 'medium' ? 'selected' : '' }}>{{ __("quality::quality.medium") }}</option>
                                <option value="high" {{ old('priority', $capa->priority) == 'high' ? 'selected' : '' }}>{{ __("quality::quality.high") }}</option>
                                <option value="critical" {{ old('priority', $capa->priority) == 'critical' ? 'selected' : '' }}>{{ __("quality::quality.critical") }}</option>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">{{ __("quality::quality.completion percentage") }}</label>
                            <input type="number" name="completion_percentage" 
                                   class="form-control" value="{{ old('completion_percentage', $capa->completion_percentage) }}" 
                                   min="0" max="100">
                        </div>

                        <div class="mb-3">
                            <label class="form-label required">{{ __("quality::quality.status") }}</label>
                            <select name="status" class="form-select @error('status') is-invalid @enderror" required>
                                <option value="planned" {{ old('status', $capa->status) == 'planned' ? 'selected' : '' }}>{{ __("quality::quality.planned") }}</option>
                                <option value="in_progress" {{ old('status', $capa->status) == 'in_progress' ? 'selected' : '' }}>{{ __("quality::quality.in progress") }}</option>
                                <option value="completed" {{ old('status', $capa->status) == 'completed' ? 'selected' : '' }}>{{ __("quality::quality.completed") }}</option>
                                <option value="verified" {{ old('status', $capa->status) == 'verified' ? 'selected' : '' }}>{{ __("quality::quality.verified") }}</option>
                            </select>
                            @error('status')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label">{{ __("quality::quality.implementation details") }}</label>
                            <textarea name="implementation_notes" rows="3" class="form-control" 
                                      placeholder="{{ __("quality::quality.notes about implementation...") }}">{{ old('implementation_notes', $capa->implementation_notes) }}</textarea>
                        </div>
                    </div>
                </div>

                <div class="d-grid gap-2">
                    <button type="submit" class="btn btn-success btn-lg">
                        <i class="fas fa-save me-2"></i>{{ __("quality::quality.save changes") }}
                    </button>
                    <a href="{{ route('quality.capa.show', $capa) }}" class="btn btn-secondary">
                        <i class="fas fa-times me-2"></i>{{ __("quality::quality.cancel") }}
                    </a>
                </div>
            </div>
        </div>
    </form>
</div>
@endsection