@extends('admin.dashboard')

@section('sidebar')
    @include('components.sidebar.service')
@endsection

@section('content')
    <div class="container-fluid">

        <!-- Header -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h1 class="h3 mb-0">{{ __('maintenance::maintenance.dashboard') }}</h1>
                <p class="text-muted mb-0">{{ __('maintenance::maintenance.dashboard_subtitle') }}</p>
            </div>
        </div>

        <!-- Quick Stats Cards -->
        <div class="row g-4 mb-4">
            <div class="col-xl-3 col-md-6">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="flex-shrink-0">
                                <div class="bg-primary bg-opacity-10 rounded p-3">
                                    <i class="fas fa-tools text-primary fa-2x"></i>
                                </div>
                            </div>
                            <div class="flex-grow-1 ms-3">
                                <p class="text-muted mb-1">{{ __('maintenance::maintenance.total_requests') }}</p>
                                <h3 class="mb-0">{{ $stats['overview']['total_maintenances'] }}</h3>
                                <small class="text-muted">
                                    <i class="fas fa-calendar-week me-1"></i>
                                    {{ $stats['overview']['this_week'] }} {{ __('maintenance::maintenance.this_week') }}
                                </small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-3 col-md-6">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="flex-shrink-0">
                                <div class="bg-warning bg-opacity-10 rounded p-3">
                                    <i class="fas fa-clock text-warning fa-2x"></i>
                                </div>
                            </div>
                            <div class="flex-grow-1 ms-3">
                                <p class="text-muted mb-1">{{ __('maintenance::maintenance.pending') }}</p>
                                <h3 class="mb-0">{{ $stats['overview']['pending'] }}</h3>
                                @if ($stats['performance']['pending_urgent'] > 0)
                                    <small class="text-danger">
                                        <i class="fas fa-exclamation-triangle me-1"></i>
                                        {{ $stats['performance']['pending_urgent'] }} {{ __('maintenance::maintenance.urgent') }}
                                    </small>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-3 col-md-6">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="flex-shrink-0">
                                <div class="bg-info bg-opacity-10 rounded p-3">
                                    <i class="fas fa-wrench text-info fa-2x"></i>
                                </div>
                            </div>
                            <div class="flex-grow-1 ms-3">
                                <p class="text-muted mb-1">{{ __('maintenance::maintenance.in_progress') }}</p>
                                <h3 class="mb-0">{{ $stats['overview']['in_progress'] }}</h3>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-3 col-md-6">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="flex-shrink-0">
                                <div class="bg-success bg-opacity-10 rounded p-3">
                                    <i class="fas fa-check-circle text-success fa-2x"></i>
                                </div>
                            </div>
                            <div class="flex-grow-1 ms-3">
                                <p class="text-muted mb-1">{{ __('maintenance::maintenance.completed') }}</p>
                                <h3 class="mb-0">{{ $stats['overview']['completed'] }}</h3>
                                <small class="text-success">
                                    <i class="fas fa-chart-line me-1"></i>
                                    {{ $stats['performance']['completion_rate'] }}% {{ __('maintenance::maintenance.completion_rate') }}
                                </small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Performance Metrics -->
        <div class="row g-4 mb-4">
            <div class="col-xl-4">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-header bg-white py-3">
                        <h5 class="mb-0">{{ __('maintenance::maintenance.monthly_performance') }}</h5>
                    </div>
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <span class="text-muted">{{ __('maintenance::maintenance.current_month') }}</span>
                            <h4 class="mb-0">{{ $stats['performance']['current_month_count'] }}</h4>
                        </div>
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <span class="text-muted">{{ __('maintenance::maintenance.last_month') }}</span>
                            <h4 class="mb-0">{{ $stats['performance']['last_month_count'] }}</h4>
                        </div>
                        <hr>
                        <div class="d-flex justify-content-between align-items-center">
                            <span class="text-muted">{{ __('maintenance::maintenance.change_rate') }}</span>
                            <h4 class="mb-0 {{ $stats['performance']['is_increase'] ? 'text-success' : 'text-danger' }}">
                                <i
                                    class="fas fa-arrow-{{ $stats['performance']['is_increase'] ? 'up' : 'down' }} me-1"></i>
                                {{ abs($stats['performance']['change_percentage']) }}%
                            </h4>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-4">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-header bg-white py-3">
                        <h5 class="mb-0">{{ __('maintenance::maintenance.status_distribution') }}</h5>
                    </div>
                    <div class="card-body">
                        @foreach ($stats['status_breakdown'] as $status => $data)
                            <div class="mb-3">
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <span>
                                        <i class="fas fa-{{ $data['icon'] }} text-{{ $data['color'] }} me-2"></i>
                                        {{ $data['label'] }}
                                    </span>
                                    <strong>{{ $data['count'] }} ({{ $data['percentage'] }}%)</strong>
                                </div>
                                <div class="progress" style="height: 8px;">
                                    <div class="progress-bar bg-{{ $data['color'] }}" role="progressbar"
                                        style="width: {{ $data['percentage'] }}%"></div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>

            <div class="col-xl-4">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-header bg-white py-3">
                        <h5 class="mb-0">{{ __('maintenance::maintenance.performance_indicators') }}</h5>
                    </div>
                    <div class="card-body">
                        <div class="text-center mb-4">
                            @php
                                $rate = max(0, min(100, $stats['performance']['completion_rate']));
                                $circumference = 314.16;
                                $dashArray = round(($rate / 100) * $circumference, 2);
                            @endphp
                            <div class="position-relative d-inline-block">
                                <svg width="120" height="120" viewBox="0 0 120 120">
                                    <circle cx="60" cy="60" r="50" fill="none" stroke="#e9ecef" stroke-width="10" />
                                    <circle cx="60" cy="60" r="50" fill="none" stroke="#28a745"
                                        stroke-width="10"
                                        stroke-dasharray="{{ $dashArray }} {{ $circumference }}"
                                        stroke-linecap="round" transform="rotate(-90 60 60)" />
                                </svg>
                                <div class="position-absolute top-50 start-50 translate-middle">
                                    <h3 class="mb-0">{{ $rate }}%</h3>
                                    <small class="text-muted">{{ __('maintenance::maintenance.completion_rate') }}</small>
                                </div>
                            </div>
                        </div>
                        <div class="text-center">
                            @php $avgDays = max(0, $stats['performance']['avg_completion_days']); @endphp
                            <p class="mb-2">
                                <i class="fas fa-hourglass-half text-info me-2"></i>
                                <strong>{{ $avgDays }}</strong> {{ __('maintenance::maintenance.day_label') }}
                            </p>
                            <small class="text-muted">{{ __('maintenance::maintenance.avg_completion_days') }}</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Service Types Stats -->
        <div class="row g-4 mb-4">
            <div class="col-12">
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-white py-3">
                        <h5 class="mb-0">{{ __('maintenance::maintenance.service_type_stats') }}</h5>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover align-middle">
                                <thead class="table-light">
                                    <tr>
                                        <th>{{ __('maintenance::maintenance.service_type') }}</th>
                                        <th class="text-center">{{ __('maintenance::maintenance.total_requests_col') }}</th>
                                        <th class="text-center">{{ __('maintenance::maintenance.pending') }}</th>
                                        <th class="text-center">{{ __('maintenance::maintenance.in_progress') }}</th>
                                        <th class="text-center">{{ __('maintenance::maintenance.completed') }}</th>
                                        <th class="text-center">{{ __('maintenance::maintenance.cancelled') }}</th>
                                        <th class="text-center">{{ __('maintenance::maintenance.completion_rate') }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($stats['service_types'] as $type)
                                        <tr>
                                            <td>
                                                <strong>{{ $type->name }}</strong>
                                                @if ($type->description)
                                                    <br><small class="text-muted">{{ $type->description }}</small>
                                                @endif
                                            </td>
                                            <td class="text-center">
                                                <span class="badge bg-secondary">{{ $type->total_maintenances }}</span>
                                            </td>
                                            <td class="text-center">
                                                <span class="badge bg-warning">{{ $type->pending }}</span>
                                            </td>
                                            <td class="text-center">
                                                <span class="badge bg-info">{{ $type->in_progress }}</span>
                                            </td>
                                            <td class="text-center">
                                                <span class="badge bg-success">{{ $type->completed }}</span>
                                            </td>
                                            <td class="text-center">
                                                <span class="badge bg-danger">{{ $type->cancelled }}</span>
                                            </td>
                                            <td class="text-center">
                                                <div class="progress" style="height: 20px; min-width: 100px;">
                                                    <div class="progress-bar bg-success" role="progressbar"
                                                        style="width: {{ $type->completion_rate }}%">
                                                        {{ $type->completion_rate }}%
                                                    </div>
                                                </div>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="7" class="text-center text-muted py-4">
                                                {{ __('maintenance::maintenance.no_service_types') }}
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Charts Section --}}
        @include('maintenance::dashboard.charts-section')

        <!-- Recent Maintenances -->
        <div class="row g-4">
            <div class="col-12">
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">{{ __('maintenance::maintenance.recent_maintenances') }}</h5>
                        <a href="{{ route('maintenances.index') }}" class="btn btn-sm btn-outline-primary">
                            {{ __('maintenance::maintenance.view_all') }} <i class="fas fa-arrow-left ms-2"></i>
                        </a>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover align-middle">
                                <thead class="table-light">
                                    <tr>
                                        <th>#</th>
                                        <th>{{ __('maintenance::maintenance.client_name_col') }}</th>
                                        <th>{{ __('maintenance::maintenance.item_col') }}</th>
                                        <th>{{ __('maintenance::maintenance.item_number_col') }}</th>
                                        <th>{{ __('maintenance::maintenance.service_type_col') }}</th>
                                        <th>{{ __('maintenance::maintenance.date') }}</th>
                                        <th class="text-center">{{ __('maintenance::maintenance.status') }}</th>
                                        <th class="text-center">{{ __('maintenance::maintenance.actions') }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($stats['recent_maintenances'] as $maintenance)
                                        <tr>
                                            <td>{{ $maintenance['id'] }}</td>
                                            <td>{{ $maintenance['client_name'] }}</td>
                                            <td>{{ $maintenance['item_name'] }}</td>
                                            <td>{{ $maintenance['item_number'] }}</td>
                                            <td>{{ $maintenance['service_type'] }}</td>
                                            <td>{{ $maintenance['date'] ?? $maintenance['created_at'] }}</td>
                                            <td class="text-center">
                                                <span class="badge bg-{{ $maintenance['status_color'] }}">
                                                    {{ $maintenance['status_label'] }}
                                                </span>
                                            </td>
                                            <td class="text-center">
                                                <a href="{{ route('maintenances.edit', $maintenance['id']) }}"
                                                    class="btn btn-sm btn-outline-primary">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="8" class="text-center text-muted py-4">
                                                {{ __('maintenance::maintenance.no_maintenances') }}
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>
@endsection

@push('styles')
    <style>
        .card {
            transition: transform 0.2s;
        }

        .card:hover {
            transform: translateY(-5px);
        }
    </style>
@endpush

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Monthly Trend Chart
            const monthlyTrendData = @json($stats['monthly_trend']);

            if (monthlyTrendData.labels.length > 0) {
                const ctx = document.getElementById('monthlyTrendChart');
                if (ctx) {
                    new Chart(ctx, {
                        type: 'line',
                        data: {
                            labels: monthlyTrendData.labels,
                            datasets: [{
                                    label: '{{ __('maintenance::maintenance.total_requests_chart') }}',
                                    data: monthlyTrendData.data.total,
                                    borderColor: 'rgb(75, 192, 192)',
                                    backgroundColor: 'rgba(75, 192, 192, 0.1)',
                                    tension: 0.4
                                },
                                {
                                    label: '{{ __('maintenance::maintenance.completed_chart') }}',
                                    data: monthlyTrendData.data.completed,
                                    borderColor: 'rgb(40, 167, 69)',
                                    backgroundColor: 'rgba(40, 167, 69, 0.1)',
                                    tension: 0.4
                                }
                            ]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            plugins: {
                                legend: {
                                    position: 'bottom',
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
            }

            console.log('Maintenance Dashboard loaded successfully');
        });
    </script>
@endpush
