@extends('admin.dashboard')

@section('sidebar')
    @include('components.sidebar.departments')
@endsection

@section('content')
    @include('components.breadcrumb', [
        'title' => __('recruitment.recruitment_statistics'),
        'items' => [
            ['label' => __('الرئيسيه'), 'url' => route('admin.dashboard')],
            ['label' => __('recruitment.recruitment_management')]
        ],
    ])

    <div style="font-family: 'Cairo', sans-serif; direction: rtl;">
        <!-- Main Statistics Cards -->
        <div class="row mb-4">
            <div class="col-md-3">
                <div class="card border-0 shadow-sm">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="flex-shrink-0">
                                <i class="mdi mdi-briefcase text-primary" style="font-size: 2.5rem;"></i>
                            </div>
                            <div class="flex-grow-1 ms-3">
                                <h6 class="text-muted mb-1">{{ __('recruitment.total_job_postings') }}</h6>
                                <h3 class="mb-0">{{ $stats['total_job_postings'] }}</h3>
                                <small class="text-muted">
                                    <span class="badge bg-success">{{ $stats['active_job_postings'] }} {{ __('recruitment.active') }}</span>
                                    <span class="badge bg-secondary">{{ $stats['closed_job_postings'] }} {{ __('recruitment.closed') }}</span>
                                </small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card border-0 shadow-sm">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="flex-shrink-0">
                                <i class="mdi mdi-file-document-multiple text-info" style="font-size: 2.5rem;"></i>
                            </div>
                            <div class="flex-grow-1 ms-3">
                                <h6 class="text-muted mb-1">{{ __('recruitment.total_cvs') }}</h6>
                                <h3 class="mb-0">{{ $stats['total_cvs'] }}</h3>
                                <small class="text-muted">
                                    {{ $stats['cvs_this_month'] }} {{ __('recruitment.this_month') }}
                                </small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card border-0 shadow-sm">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="flex-shrink-0">
                                <i class="mdi mdi-calendar-clock text-warning" style="font-size: 2.5rem;"></i>
                            </div>
                            <div class="flex-grow-1 ms-3">
                                <h6 class="text-muted mb-1">{{ __('recruitment.total_interviews') }}</h6>
                                <h3 class="mb-0">{{ $stats['total_interviews'] }}</h3>
                                <small class="text-muted">
                                    <span class="badge bg-primary">{{ $stats['scheduled_interviews'] }} {{ __('recruitment.scheduled') }}</span>
                                    <span class="badge bg-success">{{ $stats['completed_interviews'] }} {{ __('recruitment.completed') }}</span>
                                </small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card border-0 shadow-sm">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="flex-shrink-0">
                                <i class="mdi mdi-file-contract text-success" style="font-size: 2.5rem;"></i>
                            </div>
                            <div class="flex-grow-1 ms-3">
                                <h6 class="text-muted mb-1">{{ __('recruitment.total_contracts') }}</h6>
                                <h3 class="mb-0">{{ $stats['total_contracts'] }}</h3>
                                <small class="text-muted">
                                    {{ $stats['contracts_this_month'] }} {{ __('recruitment.this_month') }}
                                </small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Secondary Statistics Cards -->
        <div class="row mb-4">
            <div class="col-md-3">
                <div class="card border-0 shadow-sm">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="flex-shrink-0">
                                <i class="mdi mdi-account-plus text-primary" style="font-size: 2.5rem;"></i>
                            </div>
                            <div class="flex-grow-1 ms-3">
                                <h6 class="text-muted mb-1">{{ __('recruitment.total_onboardings') }}</h6>
                                <h3 class="mb-0">{{ $stats['total_onboardings'] }}</h3>
                                <small class="text-muted">
                                    <span class="badge bg-success">{{ $stats['completed_onboardings'] }} {{ __('recruitment.completed') }}</span>
                                </small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card border-0 shadow-sm">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="flex-shrink-0">
                                <i class="mdi mdi-account-check text-success" style="font-size: 2.5rem;"></i>
                            </div>
                            <div class="flex-grow-1 ms-3">
                                <h6 class="text-muted mb-1">{{ __('recruitment.employees_created') }}</h6>
                                <h3 class="mb-0">{{ $stats['employees_created_from_onboarding'] }}</h3>
                                <small class="text-muted">
                                    {{ $stats['onboarding_to_employee_rate'] }}% {{ __('recruitment.conversion_rate') }}
                                </small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card border-0 shadow-sm">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="flex-shrink-0">
                                <i class="mdi mdi-account-remove text-danger" style="font-size: 2.5rem;"></i>
                            </div>
                            <div class="flex-grow-1 ms-3">
                                <h6 class="text-muted mb-1">{{ __('recruitment.total_terminations') }}</h6>
                                <h3 class="mb-0">{{ $stats['total_terminations'] }}</h3>
                                <small class="text-muted">
                                    {{ $stats['terminations_this_month'] }} {{ __('recruitment.this_month') }}
                                </small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card border-0 shadow-sm">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="flex-shrink-0">
                                <i class="mdi mdi-chart-line text-info" style="font-size: 2.5rem;"></i>
                            </div>
                            <div class="flex-grow-1 ms-3">
                                <h6 class="text-muted mb-1">{{ __('recruitment.acceptance_rate') }}</h6>
                                <h3 class="mb-0">{{ $stats['interview_to_contract_rate'] }}%</h3>
                                <small class="text-muted">
                                    {{ __('recruitment.interview_to_contract') }}
                                </small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Charts Row -->
        <div class="row mb-4">
            <!-- Monthly Statistics Chart -->
            <div class="col-md-6">
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-white">
                        <h5 class="mb-0">{{ __('recruitment.monthly_statistics') }}</h5>
                    </div>
                    <div class="card-body">
                        <canvas id="monthlyChart" height="300"></canvas>
                    </div>
                </div>
            </div>

            <!-- Interview Results Chart -->
            <div class="col-md-6">
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-white">
                        <h5 class="mb-0">{{ __('recruitment.interview_results_distribution') }}</h5>
                    </div>
                    <div class="card-body">
                        <canvas id="interviewResultsChart" height="300"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <!-- Status Distribution Row -->
        <div class="row mb-4">
            <!-- Job Postings Status -->
            <div class="col-md-4">
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-white">
                        <h5 class="mb-0">{{ __('recruitment.job_postings_status') }}</h5>
                    </div>
                    <div class="card-body">
                        <canvas id="jobPostingsStatusChart" height="250"></canvas>
                    </div>
                </div>
            </div>

            <!-- Onboardings Status -->
            <div class="col-md-4">
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-white">
                        <h5 class="mb-0">{{ __('recruitment.onboardings_status') }}</h5>
                    </div>
                    <div class="card-body">
                        <canvas id="onboardingsStatusChart" height="250"></canvas>
                    </div>
                </div>
            </div>

            <!-- Termination Types -->
            <div class="col-md-4">
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-white">
                        <h5 class="mb-0">{{ __('recruitment.termination_types') }}</h5>
                    </div>
                    <div class="card-body">
                        <canvas id="terminationTypesChart" height="250"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <!-- Recent Data Tables -->
        <div class="row mb-4">
            <!-- Recent Onboardings -->
            <div class="col-md-6">
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-white">
                        <h5 class="mb-0">{{ __('recruitment.recent_onboardings') }}</h5>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-sm">
                                <thead>
                                    <tr>
                                        <th>{{ __('recruitment.candidate') }}</th>
                                        <th>{{ __('recruitment.status') }}</th>
                                        <th>{{ __('recruitment.created_at') }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($recentOnboardings as $onboarding)
                                        <tr>
                                            <td>{{ $onboarding->cv?->name ?? __('recruitment.no_candidate') }}</td>
                                            <td>
                                                <span class="badge bg-{{ $onboarding->status === 'completed' ? 'success' : ($onboarding->status === 'cancelled' ? 'danger' : ($onboarding->status === 'in_progress' ? 'warning' : 'secondary')) }}">
                                                    {{ __('recruitment.' . $onboarding->status) }}
                                                </span>
                                            </td>
                                            <td>{{ $onboarding->created_at->format('Y-m-d') }}</td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="3" class="text-center text-muted">{{ __('recruitment.no_onboardings_found') }}</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Recent Contracts -->
            <div class="col-md-6">
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-white">
                        <h5 class="mb-0">{{ __('recruitment.recent_contracts') }}</h5>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-sm">
                                <thead>
                                    <tr>
                                        <th>{{ __('recruitment.employee') }}</th>
                                        <th>{{ __('recruitment.contract_type') }}</th>
                                        <th>{{ __('recruitment.created_at') }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($recentContracts as $contract)
                                        <tr>
                                            <td>{{ $contract->employee?->name ?? __('recruitment.no_employee') }}</td>
                                            <td>{{ $contract->contractType?->name ?? '-' }}</td>
                                            <td>{{ $contract->created_at->format('Y-m-d') }}</td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="3" class="text-center text-muted">{{ __('recruitment.no_contracts_found') }}</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Upcoming Interviews & Recent Job Postings -->
        <div class="row">
            <div class="col-md-6">
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-white">
                        <h5 class="mb-0">{{ __('recruitment.upcoming_interviews') }}</h5>
                    </div>
                    <div class="card-body">
                        @forelse($upcomingInterviews as $interview)
                            <div class="d-flex align-items-center mb-3 pb-3 border-bottom">
                                <div class="flex-shrink-0">
                                    <i class="mdi mdi-calendar-clock text-primary" style="font-size: 2rem;"></i>
                                </div>
                                <div class="flex-grow-1 ms-3">
                                    <h6 class="mb-1">{{ $interview->cv->name ?? __('recruitment.unknown') }}</h6>
                                    <small class="text-muted">
                                        {{ $interview->scheduled_at->format('Y-m-d H:i') }}
                                        @if($interview->interviewer)
                                            | {{ $interview->interviewer->name }}
                                        @endif
                                    </small>
                                </div>
                            </div>
                        @empty
                            <p class="text-muted text-center">{{ __('recruitment.no_upcoming_interviews') }}</p>
                        @endforelse
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-white">
                        <h5 class="mb-0">{{ __('recruitment.recent_job_postings') }}</h5>
                    </div>
                    <div class="card-body">
                        @forelse($recentJobPostings as $posting)
                            <div class="d-flex align-items-center mb-3 pb-3 border-bottom">
                                <div class="flex-shrink-0">
                                    <i class="mdi mdi-briefcase text-info" style="font-size: 2rem;"></i>
                                </div>
                                <div class="flex-grow-1 ms-3">
                                    <h6 class="mb-1">{{ $posting->title }}</h6>
                                    <small class="text-muted">
                                        {{ $posting->job->title ?? __('recruitment.unknown') }}
                                        | <span class="badge bg-{{ $posting->status === 'active' ? 'success' : 'secondary' }}">{{ __('recruitment.' . $posting->status) }}</span>
                                    </small>
                                </div>
                            </div>
                        @empty
                            <p class="text-muted text-center">{{ __('recruitment.no_job_postings') }}</p>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
    <script>
        // Monthly Statistics Chart
        const monthlyCtx = document.getElementById('monthlyChart');
        if (monthlyCtx) {
            new Chart(monthlyCtx, {
                type: 'line',
                data: {
                    labels: @json(array_column($monthlyStats, 'month')),
                    datasets: [
                        {
                            label: '{{ __('recruitment.cvs') }}',
                            data: @json(array_column($monthlyStats, 'cvs')),
                            borderColor: 'rgb(75, 192, 192)',
                            backgroundColor: 'rgba(75, 192, 192, 0.2)',
                            tension: 0.1
                        },
                        {
                            label: '{{ __('recruitment.interviews') }}',
                            data: @json(array_column($monthlyStats, 'interviews')),
                            borderColor: 'rgb(255, 99, 132)',
                            backgroundColor: 'rgba(255, 99, 132, 0.2)',
                            tension: 0.1
                        },
                        {
                            label: '{{ __('recruitment.contracts') }}',
                            data: @json(array_column($monthlyStats, 'contracts')),
                            borderColor: 'rgb(54, 162, 235)',
                            backgroundColor: 'rgba(54, 162, 235, 0.2)',
                            tension: 0.1
                        },
                        {
                            label: '{{ __('recruitment.onboardings') }}',
                            data: @json(array_column($monthlyStats, 'onboardings')),
                            borderColor: 'rgb(255, 206, 86)',
                            backgroundColor: 'rgba(255, 206, 86, 0.2)',
                            tension: 0.1
                        }
                    ]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'top',
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true
                        }
                    }
                }
            });
        }

        // Interview Results Chart
        const interviewResultsCtx = document.getElementById('interviewResultsChart');
        if (interviewResultsCtx) {
            new Chart(interviewResultsCtx, {
                type: 'doughnut',
                data: {
                    labels: [
                        '{{ __('recruitment.accepted') }}',
                        '{{ __('recruitment.rejected') }}',
                        '{{ __('recruitment.pending') }}',
                        '{{ __('recruitment.on_hold') }}'
                    ],
                    datasets: [{
                        data: [
                            {{ $interviewResults['accepted'] }},
                            {{ $interviewResults['rejected'] }},
                            {{ $interviewResults['pending'] }},
                            {{ $interviewResults['on_hold'] }}
                        ],
                        backgroundColor: [
                            'rgb(75, 192, 192)',
                            'rgb(255, 99, 132)',
                            'rgb(255, 206, 86)',
                            'rgb(153, 102, 255)'
                        ]
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'bottom',
                        }
                    }
                }
            });
        }

        // Job Postings Status Chart
        const jobPostingsStatusCtx = document.getElementById('jobPostingsStatusChart');
        if (jobPostingsStatusCtx) {
            new Chart(jobPostingsStatusCtx, {
                type: 'pie',
                data: {
                    labels: [
                        '{{ __('recruitment.active') }}',
                        '{{ __('recruitment.closed') }}',
                        '{{ __('recruitment.expired') }}'
                    ],
                    datasets: [{
                        data: [
                            {{ $statusDistribution['job_postings']['active'] }},
                            {{ $statusDistribution['job_postings']['closed'] }},
                            {{ $statusDistribution['job_postings']['expired'] }}
                        ],
                        backgroundColor: [
                            'rgb(75, 192, 192)',
                            'rgb(201, 203, 207)',
                            'rgb(255, 99, 132)'
                        ]
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'bottom',
                        }
                    }
                }
            });
        }

        // Onboardings Status Chart
        const onboardingsStatusCtx = document.getElementById('onboardingsStatusChart');
        if (onboardingsStatusCtx) {
            new Chart(onboardingsStatusCtx, {
                type: 'pie',
                data: {
                    labels: [
                        '{{ __('recruitment.pending') }}',
                        '{{ __('recruitment.in_progress') }}',
                        '{{ __('recruitment.completed') }}',
                        '{{ __('recruitment.cancelled') }}'
                    ],
                    datasets: [{
                        data: [
                            {{ $statusDistribution['onboardings']['pending'] }},
                            {{ $statusDistribution['onboardings']['in_progress'] }},
                            {{ $statusDistribution['onboardings']['completed'] }},
                            {{ $statusDistribution['onboardings']['cancelled'] }}
                        ],
                        backgroundColor: [
                            'rgb(201, 203, 207)',
                            'rgb(255, 206, 86)',
                            'rgb(75, 192, 192)',
                            'rgb(255, 99, 132)'
                        ]
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'bottom',
                        }
                    }
                }
            });
        }

        // Termination Types Chart
        const terminationTypesCtx = document.getElementById('terminationTypesChart');
        if (terminationTypesCtx) {
            new Chart(terminationTypesCtx, {
                type: 'bar',
                data: {
                    labels: [
                        '{{ __('recruitment.resignation') }}',
                        '{{ __('recruitment.dismissal') }}',
                        '{{ __('recruitment.death') }}',
                        '{{ __('recruitment.retirement') }}'
                    ],
                    datasets: [{
                        label: '{{ __('recruitment.count') }}',
                        data: [
                            {{ $terminationTypes['resignation'] }},
                            {{ $terminationTypes['dismissal'] }},
                            {{ $terminationTypes['death'] }},
                            {{ $terminationTypes['retirement'] }}
                        ],
                        backgroundColor: [
                            'rgba(54, 162, 235, 0.6)',
                            'rgba(255, 99, 132, 0.6)',
                            'rgba(201, 203, 207, 0.6)',
                            'rgba(255, 206, 86, 0.6)'
                        ]
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            display: false
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true
                        }
                    }
                }
            });
        }
    </script>
    @endpush
@endsection
