@extends('admin.dashboard')

@section('sidebar')
    @include('components.sidebar.daily_progress')
    @include('components.sidebar.projects')
    @include('components.sidebar.accounts')
@endsection

@section('title', __('general.edit_daily_progress'))

@section('content')
<style>
    :root {
        --primary-color: #2c7be5;
        --light-bg: #f8f9fa;
        --card-shadow: 0 0.5rem 1.5rem rgba(0, 0, 0, 0.08);
    }

    .main-card { border: none; border-radius: 0.75rem; box-shadow: var(--card-shadow); margin-top: 2rem; }
    .card-header { border-radius: 0.75rem 0.75rem 0 0 !important; padding: 1.2rem 1.5rem; background: linear-gradient(120deg, #4a3ef6 0%, #3936ff 100%) !important; border: none; }
    .card-body { padding: 2rem; }
    .form-label { font-weight: 600; margin-bottom: 0.5rem; color: #344050; }
    .form-control { border-radius: 0.5rem; padding: 0.75rem 1rem; border: 1px solid #e3ebf6; background-color: #fff; }
    .form-control:focus { border-color: var(--primary-color); box-shadow: 0 0 0 0.25rem rgba(44,123,229,0.15); }
    .input-group-text { background-color: #f5f7f9; border-radius: 0.5rem 0 0 0.5rem; border: 1px solid #e3ebf6; }
    .btn-primary { background: linear-gradient(120deg, #2c7be5 0%, #1a56ce 100%); border: none; border-radius: 0.5rem; padding: 0.75rem 2rem; font-weight: 600; }
    .btn-secondary { border-radius: 0.5rem; padding: 0.75rem 2rem; font-weight: 600; }
</style>

<div class="container">
    <div class="main-card card">
        <div class="card-header text-white">
            <h5 class="mb-0"><i class="fas fa-calendar-edit me-2"></i> {{ __('general.edit_daily_progress') }}</h5>
        </div>
        <div class="card-body">
            <form action="{{ route('daily.progress.update', $dailyProgress) }}" method="POST">
                @csrf
                @method('PUT')

                <div class="row mb-4">
                    <!-- readonly: project -->
                    <div class="col-md-4 mb-3">
                        <label class="form-label">{{ __('general.project') }}</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="fas fa-project-diagram"></i></span>
                            <input type="text" class="form-control" value="{{ $dailyProgress->project->name }}" readonly>
                        </div>
                    </div>

                    <!-- readonly: work item -->
                    <div class="col-md-4 mb-3">
                        <label class="form-label">{{ __('general.work_item') }}</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="fas fa-tasks"></i></span>
                            <input type="text" class="form-control" value="{{ $dailyProgress->projectItem->workItem->name }}" readonly>
                        </div>
                    </div>

                    <!-- readonly: employee -->
                    <div class="col-md-4 mb-3">
                        <label class="form-label">{{ __('general.employee') }}</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="fas fa-user"></i></span>
                            <input type="text" class="form-control" value="{{ $dailyProgress->employee->name }}" readonly>
                        </div>
                    </div>
                </div>

                <div class="row mb-4">
                    <!-- editable: date -->
                    <div class="col-md-4 mb-3">
                        <label class="form-label">{{ __('general.date') }}</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="fas fa-calendar"></i></span>
                            <input type="date" name="progress_date" class="form-control"
                                   value="{{ $dailyProgress->progress_date->format('Y-m-d') }}" required>
                        </div>
                    </div>

                    <!-- editable: quantity -->
                    <div class="col-md-4 mb-3">
                        <label class="form-label">{{ __('general.quantity') }}</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="fas fa-sort-numeric-up"></i></span>
                            <input type="number" name="quantity" step="0.01" min="0" class="form-control"
                                   value="{{ $dailyProgress->quantity }}" required>
                        </div>
                    </div>
                </div>

                <!-- editable: notes -->
                <div class="mb-4">
                    <label class="form-label">{{ __('general.notes') }}</label>
                    <textarea name="notes" class="form-control" rows="3">{{ $dailyProgress->notes }}</textarea>
                </div>

                <div class="text-end">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save me-2"></i> {{ __('general.save_changes') }}
                    </button>
                    <a href="{{ route('daily.progress.index') }}" class="btn btn-secondary ms-2">
                        <i class="fas fa-times me-2"></i> {{ __('general.cancel') }}
                    </a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
