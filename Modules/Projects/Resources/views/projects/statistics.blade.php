@extends('admin.dashboard')

@section('sidebar')
    @include('components.sidebar.projects')
@endsection

@section('content')
    @include('components.breadcrumb', [
        'title' => __('Projects Statistics Dashboard'),
        'breadcrumb_items' => [
            ['label' => __('Home'), 'url' => route('admin.dashboard')],
            ['label' => __('Projects'), 'url' => route('projects.index')],
            ['label' => __('Projects Statistics Dashboard')]
        ],
    ])
    <div class="container-fluid">
        <!-- Page Header -->
        <div class="card mb-4">
            <div class="card-header bg-primary text-white">
                <h4 class="mb-0">
                    <i class="las la-chart-bar me-2"></i>
                    {{ __('Projects Statistics Dashboard') }}
                </h4>
            </div>
        </div>

        <!-- KPIs Cards -->
        <div class="row mb-4">
            <!-- Total Projects -->
            <div class="col-xl-3 col-md-6 mb-3">
                <div class="card border-start border-primary border-4">
                    <div class="card-body p-3">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <small class="text-muted d-block">{{ __('Total Projects') }}</small>
                                <h3 class="mb-0">{{ number_format($overallTotal) }}</h3>
                            </div>
                            <div class="text-primary">
                                <i class="las la-layer-group" style="font-size: 2.5rem;"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            @foreach ($sortedStatistics as $status => $stats)
                @if ($stats)
                    @php
                        $colorClass = $stats['color'] ?? 'secondary';
                        $iconClass = $stats['icon'] ?? 'la-question-circle';
                        $title = $stats['title'] ?? __('Unspecified');
                        $count = number_format($stats['count'] ?? 0);
                        $avgDuration = $stats['avg_duration'] ?? '0';
                    @endphp
                    <div class="col-xl-3 col-md-6 mb-3">
                        <div class="card border-start border-{{ $colorClass }} border-4">
                            <div class="card-body p-3">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <small class="text-muted d-block">{{ $title }}</small>
                                        <h3 class="mb-0">{{ $count }}</h3>
                                        <small class="text-muted">{{ $avgDuration }} {{ __('days') }}</small>
                                    </div>
                                    <div class="text-{{ $colorClass }}">
                                        <i class="las {{ $iconClass }}" style="font-size: 2.5rem;"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @endif
            @endforeach
        </div>

        <!-- Charts Section -->
        <div class="row mb-4">
            <!-- Pie Chart -->
            <div class="col-lg-6 mb-3">
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">{{ __('Projects Distribution by Status') }}</h5>
                    </div>
                    <div class="card-body">
                        <div style="height: 300px;">
                            <canvas id="statusPieChart"></canvas>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Bar Chart -->
            <div class="col-lg-6 mb-3">
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">{{ __('Average Duration by Status') }}</h5>
                    </div>
                    <div class="card-body">
                        <div style="height: 300px;">
                            <canvas id="durationBarChart"></canvas>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Statistics Table -->
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">{{ __('Projects Status Details') }}</h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-striped table-hover">
                        <thead class="table-light">
                            <tr>
                                <th>{{ __('Status') }}</th>
                                <th class="text-center">{{ __('Projects Count') }}</th>
                                <th class="text-center">{{ __('Average Duration (Days)') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($sortedStatistics as $status => $stats)
                                @if ($stats)
                                    <tr>
                                        <td>
                                            <span class="badge bg-{{ $stats['color'] }}">{{ $stats['title'] }}</span>
                                        </td>
                                        <td class="text-center">{{ number_format($stats['count']) }}</td>
                                        <td class="text-center">{{ $stats['avg_duration'] }}</td>
                                    </tr>
                                @endif
                            @endforeach
                        </tbody>
                        <tfoot class="table-light">
                            <tr class="fw-bold">
                                <td>{{ __('Grand Total') }}</td>
                                <td class="text-center">{{ number_format($overallTotal) }}</td>
                                <td class="text-center">-</td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
        <script src="https://cdn.jsdelivr.net/npm/chart.js@3.9.1/dist/chart.min.js"></script>
        <script>
            function getColorConfig() {
                const statusMap = {
                    'success': {
                        rgb: '40, 167, 69',
                        hex: '#28a745'
                    },
                    'danger': {
                        rgb: '220, 53, 69',
                        hex: '#dc3545'
                    },
                    'warning': {
                        rgb: '255, 193, 7',
                        hex: '#ffc107'
                    },
                    'primary': {
                        rgb: '0, 123, 255',
                        hex: '#007bff'
                    },
                    'secondary': {
                        rgb: '108, 117, 125',
                        hex: '#6c757d'
                    }
                };

                const stats = {!! json_encode(array_values($sortedStatistics)) !!}.filter(s => s !== null);
                const labels = stats.map(s => s.title);
                const counts = stats.map(s => s.count);
                const durations = stats.map(s => parseFloat(s.avg_duration));
                const colors = stats.map(s => statusMap[s.color] || statusMap['secondary']);

                return {
                    labels,
                    counts,
                    durations,
                    colors
                };
            }

            window.onload = function() {
                const config = getColorConfig();

                // Pie Chart
                const pieCtx = document.getElementById('statusPieChart').getContext('2d');
                new Chart(pieCtx, {
                    type: 'pie',
                    data: {
                        labels: config.labels,
                        datasets: [{
                            data: config.counts,
                            backgroundColor: config.colors.map(c => `rgba(${c.rgb}, 0.7)`),
                            borderColor: config.colors.map(c => c.hex),
                            borderWidth: 2
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: {
                                position: 'bottom'
                            }
                        }
                    }
                });

                // Bar Chart
                const barCtx = document.getElementById('durationBarChart').getContext('2d');
                new Chart(barCtx, {
                    type: 'bar',
                    data: {
                        labels: config.labels,
                        datasets: [{
                            label: '{{ __('Days') }}',
                            data: config.durations,
                            backgroundColor: config.colors.map(c => `rgba(${c.rgb}, 0.7)`),
                            borderColor: config.colors.map(c => c.hex),
                            borderWidth: 1
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        scales: {
                            y: {
                                beginAtZero: true,
                                title: {
                                    display: true,
                                    text: '{{ __('Days') }}'
                                }
                            }
                        },
                        plugins: {
                            legend: {
                                display: false
                            }
                        }
                    }
                });
            };
        </script>
    @endpush
@endsection
