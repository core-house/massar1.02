<div class="card stat-card border-0 shadow-sm mb-4">
    <div class="card-header bg-transparent py-3 d-flex justify-content-between align-items-center">
        <h5 class="card-title mb-0 fw-bold text-primary">
            <i class="fas fa-sitemap me-2"></i>{{ __('general.subprojects_progress') }}
        </h5>
    </div>
    <div class="card-body" 
         x-data="subprojectChart()" 
         x-init="initChart()">
        
        <div class="mb-3 small text-muted">
            <span class="me-3"><strong class="text-primary">{{ __('general.progress') }}</strong> = ({{ __('general.completed_qty') }} / {{ __('general.total_qty') }}) × 100</span>
            <span><strong class="text-info">{{ __('general.planned_progress') }}</strong> = ({{ __('general.planned_total_qty') }} / {{ __('general.total_qty') }}) × 100</span>
        </div>

        <!-- Chart -->
        <div style="height: 400px; width: 100%;">
            <canvas id="subprojectsChart"></canvas>
        </div>
    </div>
</div>

<script>
    document.addEventListener('alpine:init', () => {
        Alpine.data('subprojectChart', () => ({
            chart: null,
            chartData: @json($subprojectChartData),
            
            initChart() {
                const ctx = document.getElementById('subprojectsChart').getContext('2d');
                
                // Color Config
                const progressColor = '#858796'; // Secondary/Gray-purple equivalent
                const plannedColor = '#4e73df'; // Primary Blue

                this.chart = new Chart(ctx, {
                    type: 'bar',
                    data: {
                        labels: this.chartData.labels,
                        datasets: [
                            {
                                label: '{{ __("general.progress") }}',
                                data: this.chartData.progress,
                                backgroundColor: 'rgba(133, 135, 150, 0.8)', // Muted Purple/Gray
                                borderColor: 'rgba(133, 135, 150, 1)',
                                borderWidth: 1,
                                barPercentage: 0.7,
                                categoryPercentage: 0.8
                            },
                            {
                                label: '{{ __("general.planned_progress") }}',
                                data: this.chartData.planned_progress,
                                backgroundColor: 'rgba(78, 115, 223, 0.8)', // Blue
                                borderColor: 'rgba(78, 115, 223, 1)',
                                borderWidth: 1,
                                barPercentage: 0.7,
                                categoryPercentage: 0.8
                            }
                        ]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        interaction: {
                            mode: 'index',
                            intersect: false,
                        },
                        scales: {
                            y: {
                                beginAtZero: true,
                                max: 100,
                                ticks: {
                                    callback: function(value) {
                                        return value + '%';
                                    }
                                }
                            }
                        },
                        plugins: {
                            legend: {
                                position: 'top',
                            },
                            tooltip: {
                                callbacks: {
                                    label: function(context) {
                                        let label = context.dataset.label || '';
                                        if (label) {
                                            label += ': ';
                                        }
                                        if (context.parsed.y !== null) {
                                            label += context.parsed.y + '%';
                                        }
                                        return label;
                                    }
                                }
                            }
                        }
                    }
                });
            }
        }));
    });
</script>
