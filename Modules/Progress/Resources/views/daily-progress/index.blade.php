@extends('admin.dashboard')

@section('sidebar')
    @include('components.sidebar.daily_progress')
@endsection

@section('title', __('general.daily_progress_list'))

@section('content')
    <div class="container">
        <div class="main-card card shadow-lg border-0">
            <div class="card-header text-white d-flex justify-content-between align-items-center"
                style="background: linear-gradient(120deg, #2c7be5 0%, #1a56ce 100%); border-radius: 0.75rem 0.75rem 0 0;">
                <h5 class="mb-0"><i class="fas fa-list me-2"></i> {{ __('general.daily_progress_list') }}</h5>
                <a href="{{ route('daily.progress.create') }}" class="btn btn-light btn-sm">
                    <i class="fas fa-plus me-1"></i> {{ __('general.add_progress') }}
                </a>
            </div>

            <div class="card-body bg-light">
                <!-- فلترة -->
                <form method="GET" action="{{ route('daily.progress.index') }}" class="row g-3 mb-4">
                    <input type="hidden" name="view_all" value="{{ request('view_all') }}">
                    <div class="col-md-4">
                        <label class="form-label">{{ __('general.project') }}</label>
                        <select name="project_id" class="form-select">
                            <option value="">{{ __('general.all_projects') }}</option>
                            @foreach ($projects as $project)
                                <option value="{{ $project->id }}"
                                    {{ request('project_id') == $project->id ? 'selected' : '' }}>
                                    {{ $project->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">{{ __('general.date') }}</label>
                        <input type="date" name="progress_date" value="{{ request('progress_date') }}"
                            class="form-control">
                    </div>
                    <div class="col-md-4 d-flex align-items-end">
                        <button type="submit" class="btn btn-primary me-2"><i class="fas fa-filter me-1"></i>
                            {{ __('general.filter') }}</button>
                        <a href="{{ route('daily.progress.index', ['view_all' => 1]) }}" class="btn btn-secondary">
                            <i class="fas fa-list me-1"></i> {{ __('general.view_all') }}
                        </a>
                    </div>
                </form>

                <!-- جدول -->
                <div class="table-responsive">
                    <table class="table table-hover table-striped align-middle shadow-sm">
                        <thead class="table-primary">
                            <tr>
                                <th>#</th>
                                <th>{{ __('general.project') }}</th>
                                <th>{{ __('general.work_item') }}</th>
                                <th>{{ __('general.quantity') }}</th>
                                <th>{{ __('general.completion_percentage') }}</th>
                                <th>{{ __('general.date') }}</th>
                                <th>{{ __('general.employee') }}</th>
                                <th>{{ __('general.notes') }}</th>
                                @canany(['dailyprogress-edit', 'dailyprogress-delete'])
                                    <th>{{ __('general.actions') }}</th>
                                @endcanany
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($dailyProgress as $progress)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td>{{ $progress->project->name ?? '-' }}</td>
                                    <td>{{ $progress->projectItem->workItem->name ?? '-' }}</td>
                                    <td>{{ $progress->quantity }}</td>
                                    <td>
                                        <span class="badge bg-info">
                                            {{ $progress->completion_percentage }}%
                                        </span>
                                    </td>
                                    <td>{{ \Carbon\Carbon::parse($progress->progress_date)->format('Y-m-d') }}</td>
                                    <td>{{ $progress->employee->name ?? '-' }}</td>
                                    <td>{{ $progress->notes ?? '-' }}</td>
                                    @canany(['dailyprogress-edit', 'dailyprogress-delete'])
                                        <td>
                                            @can('dailyprogress-edit')
                                                <a href="{{ route('daily.progress.edit', $progress) }}"
                                                    class="btn btn-sm btn-success">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                            @endcan
                                            @can('dailyprogress-delete')
                                                <form action="{{ route('daily.progress.destroy', $progress) }}" method="POST"
                                                    class="d-inline">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button onclick="return confirm('{{ __('general.confirm_delete') }}')"
                                                        type="submit" class="btn btn-sm btn-danger">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </form>
                                            @endcan

                                        </td>
                                    @endcanany
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="9" class="text-center text-muted">{{ __('general.no_data') }}</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <!-- pagination -->
                <div class="d-flex justify-content-center mt-4">
                    {{ $dailyProgress->links('pagination::bootstrap-5') }}
                </div>
            </div>
        </div>
    </div>
@endsection
