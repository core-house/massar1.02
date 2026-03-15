@extends('admin.dashboard')

@section('sidebar')
    @include('components.sidebar.tasks')
@endsection

@section('content')
    @push('styles')
        <style>
            .body {
                background-color: #f8f9fa;
                font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            }

            .stats-card {
                transition: transform 0.2s, box-shadow 0.2s;
                border: none;
                border-radius: 12px;
            }

            .stats-card:hover {
                transform: translateY(-5px);
                box-shadow: 0 8px 25px rgba(0, 0, 0, 0.1);
            }

            .stats-icon {
                width: 60px;
                height: 60px;
                border-radius: 50%;
                display: flex;
                align-items: center;
                justify-content: center;
                font-size: 24px;
                color: white;
            }

            .chart-container {
                background: white;
                border-radius: 12px;
                padding: 20px;
                margin-bottom: 20px;
                box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            }

            .status-badge {
                padding: 8px 16px;
                border-radius: 20px;
                font-size: 12px;
                font-weight: bold;
                text-align: center;
                margin: 2px;
                display: inline-block;
            }

            .bg-gradient-primary {
                background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            }

            .bg-gradient-success {
                background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
            }

            .bg-gradient-info {
                background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
            }

            .bg-gradient-warning {
                background: linear-gradient(135deg, #43e97b 0%, #38f9d7 100%);
            }

            .bg-gradient-danger {
                background: linear-gradient(135deg, #fa709a 0%, #fee140 100%);
            }

            .progress-custom {
                height: 8px;
                border-radius: 10px;
            }

            .table-responsive {
                border-radius: 12px;
                overflow: hidden;
            }
        </style>
    @endpush
    <div class="container-fluid py-4">
        <!-- Header -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="d-flex justify-content-between align-items-center">
                    <h1 class="h2 mb-0">
                        <i class="fas fa-chart-bar me-2"></i>
                        {{ __('crm::crm.tasks_statistics') }}
                    </h1>
                    <div class="d-flex gap-2">
                        <button class="btn btn-outline-primary" onclick="refreshData()">
                            <i class="fas fa-sync-alt"></i> {{ __('crm::crm.refresh') }}
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Filters -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <form method="GET" action="{{ route('tasks.statistics') }}" class="row g-3">
                            <div class="col-md-12">
                                <label class="form-label"><i class="fas fa-calendar me-2"></i>{{ __('crm::crm.time_period') }}</label>
                                <select name="date_filter" class="form-select" onchange="this.form.submit()">
                                    <option value="week" {{ $dateFilter == 'week' ? 'selected' : '' }}>{{ __('crm::crm.this_week') }}</option>
                                    <option value="month" {{ $dateFilter == 'month' ? 'selected' : '' }}>{{ __('crm::crm.this_month') }}</option>
                                    <option value="year" {{ $dateFilter == 'year' ? 'selected' : '' }}>{{ __('crm::crm.this_year') }}</option>
                                </select>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <!-- Overview Cards -->
        <div class="row mb-4">
            <div class="col-md-2 mb-3">
                <div class="card stats-card h-100">
                    <div class="card-body text-center">
                        <div class="stats-icon bg-gradient-primary mx-auto mb-2"><i class="fas fa-tasks"></i></div>
                        <h6 class="text-muted mb-1">{{ __('crm::crm.total_tasks') }}</h6>
                        <h3 class="mb-0">{{ $stats['total'] }}</h3>
                    </div>
                </div>
            </div>

            <div class="col-md-2 mb-3">
                <div class="card stats-card h-100">
                    <div class="card-body text-center">
                        <div class="stats-icon bg-gradient-warning mx-auto mb-2"><i class="fas fa-clock"></i></div>
                        <h6 class="text-muted mb-1">{{ __('crm::crm.pending') }}</h6>
                        <h3 class="mb-0">{{ $stats['pending'] }}</h3>
                    </div>
                </div>
            </div>

            <div class="col-md-2 mb-3">
                <div class="card stats-card h-100">
                    <div class="card-body text-center">
                        <div class="stats-icon bg-gradient-info mx-auto mb-2"><i class="fas fa-spinner"></i></div>
                        <h6 class="text-muted mb-1">{{ __('crm::crm.in_progress') }}</h6>
                        <h3 class="mb-0">{{ $stats['in_progress'] }}</h3>
                    </div>
                </div>
            </div>

            <div class="col-md-2 mb-3">
                <div class="card stats-card h-100">
                    <div class="card-body text-center">
                        <div class="stats-icon bg-gradient-success mx-auto mb-2"><i class="fas fa-check-circle"></i></div>
                        <h6 class="text-muted mb-1">{{ __('crm::crm.completed') }}</h6>
                        <h3 class="mb-0">{{ $stats['completed'] }}</h3>
                    </div>
                </div>
            </div>

            <div class="col-md-2 mb-3">
                <div class="card stats-card h-100">
                    <div class="card-body text-center">
                        <div class="stats-icon bg-gradient-danger mx-auto mb-2"><i class="fas fa-times-circle"></i></div>
                        <h6 class="text-muted mb-1">{{ __('crm::crm.cancelled') }}</h6>
                        <h3 class="mb-0">{{ $stats['cancelled'] }}</h3>
                    </div>
                </div>
            </div>

            <div class="col-md-2 mb-3">
                <div class="card stats-card h-100">
                    <div class="card-body text-center">
                        <div class="stats-icon bg-danger mx-auto mb-2"><i class="fas fa-exclamation-triangle"></i></div>
                        <h6 class="text-muted mb-1">{{ __('crm::crm.overdue') }}</h6>
                        <h3 class="mb-0">{{ $stats['overdue'] }}</h3>
                    </div>
                </div>
            </div>
        </div>

        <!-- Completion Rate -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <h6 class="mb-3"><i class="fas fa-percentage me-2"></i>{{ __('crm::crm.completion_rate') }}</h6>
                        <div class="progress progress-custom" style="height: 30px;">
                            <div class="progress-bar bg-success" role="progressbar" style="width: {{ $completionRate }}%;"
                                 aria-valuenow="{{ $completionRate }}" aria-valuemin="0" aria-valuemax="100">
                                <strong>{{ $completionRate }}%</strong>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <!-- Priority Breakdown -->
            <div class="col-lg-4 mb-4">
                <div class="card">
                    <div class="card-header bg-info text-white">
                        <h6 class="mb-0"><i class="fas fa-exclamation-triangle me-2"></i>{{ __('crm::crm.tasks_by_priority') }}</h6>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <span>{{ __('crm::crm.high_priority') }}</span>
                                <span class="status-badge bg-danger text-white">{{ $priorityStats['high'] }}</span>
                            </div>
                            <div class="progress progress-custom">
                                <div class="progress-bar bg-danger"
                                     style="width: {{ $stats['total'] > 0 ? ($priorityStats['high'] / $stats['total']) * 100 : 0 }}%"></div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <span>{{ __('crm::crm.medium_priority') }}</span>
                                <span class="status-badge bg-warning text-white">{{ $priorityStats['medium'] }}</span>
                            </div>
                            <div class="progress progress-custom">
                                <div class="progress-bar bg-warning"
                                     style="width: {{ $stats['total'] > 0 ? ($priorityStats['medium'] / $stats['total']) * 100 : 0 }}%"></div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <span>{{ __('crm::crm.low_priority') }}</span>
                                <span class="status-badge bg-success text-white">{{ $priorityStats['low'] }}</span>
                            </div>
                            <div class="progress progress-custom">
                                <div class="progress-bar bg-success"
                                     style="width: {{ $stats['total'] > 0 ? ($priorityStats['low'] / $stats['total']) * 100 : 0 }}%"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Tasks by Type -->
            <div class="col-lg-4 mb-4">
                <div class="card">
                    <div class="card-header bg-primary text-white">
                        <h6 class="mb-0"><i class="fas fa-list-alt me-2"></i>{{ __('crm::crm.tasks_by_type') }}</h6>
                    </div>
                    <div class="card-body">
                        @forelse($tasksByType as $item)
                            <div class="mb-3">
                                <div class="d-flex justify-content-between align-items-center mb-1">
                                    <span>{{ $item->taskType->name ?? __('crm::crm.no_type') }}</span>
                                    <span class="badge bg-primary">{{ $item->count }}</span>
                                </div>
                                <div class="progress progress-custom">
                                    <div class="progress-bar bg-primary"
                                         style="width: {{ $stats['total'] > 0 ? ($item->count / $stats['total']) * 100 : 0 }}%"></div>
                                </div>
                            </div>
                        @empty
                            <p class="text-muted text-center">{{ __('crm::crm.no_data_available') }}</p>
                        @endforelse
                    </div>
                </div>
            </div>

            <!-- Tasks by User -->
            <div class="col-lg-4 mb-4">
                <div class="card">
                    <div class="card-header bg-success text-white">
                        <h6 class="mb-0"><i class="fas fa-user me-2"></i>{{ __('crm::crm.tasks_by_assigned_user') }}</h6>
                    </div>
                    <div class="card-body">
                        @forelse($tasksByUser as $item)
                            <div class="mb-3">
                                <div class="d-flex justify-content-between align-items-center mb-1">
                                    <span>{{ $item->targetUser->name ?? __('crm::crm.unassigned') }}</span>
                                    <span class="badge bg-success">{{ $item->count }}</span>
                                </div>
                                <div class="progress progress-custom">
                                    <div class="progress-bar bg-success"
                                         style="width: {{ $stats['total'] > 0 ? ($item->count / $stats['total']) * 100 : 0 }}%"></div>
                                </div>
                            </div>
                        @empty
                            <p class="text-muted text-center">{{ __('crm::crm.no_data_available') }}</p>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>

        <!-- Overdue Tasks -->
        @if($overdueTasks->count() > 0)
        <div class="row mb-4">
            <div class="col-12">
                <div class="card">
                    <div class="card-header bg-danger text-white">
                        <h6 class="mb-0"><i class="fas fa-exclamation-circle me-2"></i>{{ __('crm::crm.overdue_tasks') }}</h6>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>{{ __('crm::crm.task') }}</th>
                                        <th>{{ __('crm::crm.client') }}</th>
                                        <th>{{ __('crm::crm.assigned_to') }}</th>
                                        <th>{{ __('crm::crm.end_date') }}</th>
                                        <th>{{ __('crm::crm.priority') }}</th>
                                        <th>{{ __('crm::crm.status') }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($overdueTasks as $task)
                                        <tr>
                                            <td>{{ $task->title }}</td>
                                            <td>{{ $task->client->name ?? '-' }}</td>
                                            <td>{{ $task->targetUser->name ?? '-' }}</td>
                                            <td>
                                                <span class="text-danger">
                                                    {{ $task->due_date->format('Y-m-d') }}
                                                    <small>({{ $task->due_date->diffForHumans() }})</small>
                                                </span>
                                            </td>
                                            <td>
                                                @if($task->priority->value === 'high')
                                                    <span class="badge bg-danger">{{ __('crm::crm.high_priority') }}</span>
                                                @elseif($task->priority->value === 'medium')
                                                    <span class="badge bg-warning">{{ __('crm::crm.medium_priority') }}</span>
                                                @else
                                                    <span class="badge bg-success">{{ __('crm::crm.low_priority') }}</span>
                                                @endif
                                            </td>
                                            <td>
                                                @if($task->status->value === 'pending')
                                                    <span class="badge bg-warning">{{ __('crm::crm.pending') }}</span>
                                                @elseif($task->status->value === 'in_progress')
                                                    <span class="badge bg-info">{{ __('crm::crm.in_progress') }}</span>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @endif

        <!-- Recent Tasks -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="card">
                    <div class="card-header bg-primary text-white">
                        <h6 class="mb-0"><i class="fas fa-history me-2"></i>{{ __('crm::crm.recent_tasks') }}</h6>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>{{ __('crm::crm.task') }}</th>
                                        <th>{{ __('crm::crm.client') }}</th>
                                        <th>{{ __('crm::crm.type') }}</th>
                                        <th>{{ __('crm::crm.assigned_to') }}</th>
                                        <th>{{ __('crm::crm.created_by') }}</th>
                                        <th>{{ __('crm::crm.due_date') }}</th>
                                        <th>{{ __('crm::crm.status') }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($recentTasks as $task)
                                        <tr>
                                            <td>{{ $task->title }}</td>
                                            <td>{{ $task->client->name ?? '-' }}</td>
                                            <td>{{ $task->taskType->name ?? '-' }}</td>
                                            <td>{{ $task->targetUser->name ?? '-' }}</td>
                                            <td>{{ $task->user->name ?? '-' }}</td>
                                            <td>{{ $task->due_date ? $task->due_date->format('Y-m-d') : '-' }}</td>
                                            <td>
                                                @if($task->status->value === 'pending')
                                                    <span class="badge bg-warning">{{ __('crm::crm.pending') }}</span>
                                                @elseif($task->status->value === 'in_progress')
                                                    <span class="badge bg-info">{{ __('crm::crm.in_progress') }}</span>
                                                @elseif($task->status->value === 'completed')
                                                    <span class="badge bg-success">{{ __('crm::crm.completed') }}</span>
                                                @else
                                                    <span class="badge bg-danger">{{ __('crm::crm.cancelled') }}</span>
                                                @endif
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="7" class="text-center text-muted">{{ __('crm::crm.no_tasks_found') }}</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Monthly Trend -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="card">
                    <div class="card-header bg-info text-white">
                        <h6 class="mb-0"><i class="fas fa-chart-line me-2"></i>{{ __('crm::crm.monthly_trend_last_6_months') }}</h6>
                    </div>
                    <div class="card-body">
                        <canvas id="monthlyTrendChart" height="80"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/4.3.0/chart.min.js"></script>
    <script>
        // Monthly Trend Chart
        const monthlyTrendData = @json($monthlyTrend);
        const monthNames = ['{{ __("crm::crm.january") }}', '{{ __("crm::crm.february") }}', '{{ __("crm::crm.march") }}', '{{ __("crm::crm.april") }}',
                           '{{ __("crm::crm.may") }}', '{{ __("crm::crm.june") }}', '{{ __("crm::crm.july") }}', '{{ __("crm::crm.august") }}',
                           '{{ __("crm::crm.september") }}', '{{ __("crm::crm.october") }}', '{{ __("crm::crm.november") }}', '{{ __("crm::crm.december") }}'];

        const labels = monthlyTrendData.map(item => monthNames[item.month - 1] + ' ' + item.year);
        const data = monthlyTrendData.map(item => item.count);

        const ctx = document.getElementById('monthlyTrendChart').getContext('2d');
        new Chart(ctx, {
            type: 'line',
            data: {
                labels: labels,
                datasets: [{
                    label: '{{ __("crm::crm.tasks_created") }}',
                    data: data,
                    borderColor: 'rgb(75, 192, 192)',
                    backgroundColor: 'rgba(75, 192, 192, 0.2)',
                    tension: 0.1
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: true,
                plugins: {
                    legend: {
                        display: true,
                        position: 'top',
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            stepSize: 1
                        }
                    }
                }
            }
        });

        // Refresh data function
        function refreshData() {
            const btn = document.querySelector('[onclick="refreshData()"]');
            const icon = btn.querySelector('i');

            btn.disabled = true;
            icon.classList.add('fa-spin');

            fetch(window.location.href, {
                method: 'GET',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(response => {
                if (response.ok) {
                    window.location.reload();
                }
            })
            .catch(error => {
                alert("{{ __('crm::crm.an_error_occurred_while_refreshing_data') }}");
            })
            .finally(() => {
                btn.disabled = false;
                icon.classList.remove('fa-spin');
            });
        }
    </script>
@endpush
