<!-- Charts Section -->
<div class="row g-4 mb-4">
    <div class="col-xl-8">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white py-3">
                <h5 class="mb-0">اتجاه طلبات الصيانة الشهري</h5>
            </div>
            <div class="card-body">
                <div style="height: 350px;">
                    <canvas id="monthlyTrendChart"></canvas>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-4">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white py-3">
                <h5 class="mb-0">توزيع أنواع الصيانة</h5>
            </div>
            <div class="card-body">
                <div style="height: 350px;">
                    <canvas id="serviceTypesChart"></canvas>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Status Distribution Chart -->
<div class="row g-4 mb-4">
    <div class="col-xl-6">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white py-3">
                <h5 class="mb-0">توزيع حالات الطلبات</h5>
            </div>
            <div class="card-body">
                <div style="height: 300px;">
                    <canvas id="statusDistributionChart"></canvas>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-6">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white py-3">
                <h5 class="mb-0">أفضل أنواع الصيانة (حسب عدد الطلبات)</h5>
            </div>
            <div class="card-body">
                <div style="height: 300px;">
                    <canvas id="topServiceTypesChart"></canvas>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const monthlyTrendData = @json($stats['monthly_trend']);
            const serviceTypesData = @json($stats['service_types']);
            const statusBreakdown = @json($stats['status_breakdown']);

            // 1. Monthly Trend Chart
            const monthlyCtx = document.getElementById('monthlyTrendChart');
            if (monthlyCtx && monthlyTrendData.labels.length > 0) {
                new Chart(monthlyCtx, {
                    type: 'line',
                    data: {
                        labels: monthlyTrendData.labels,
                        datasets: [{
                                label: 'إجمالي الطلبات',
                                data: monthlyTrendData.data.total,
                                borderColor: 'rgb(13, 110, 253)',
                                backgroundColor: 'rgba(13, 110, 253, 0.1)',
                                tension: 0.4,
                                fill: true
                            },
                            {
                                label: 'قيد الانتظار',
                                data: monthlyTrendData.data.pending,
                                borderColor: 'rgb(255, 193, 7)',
                                backgroundColor: 'rgba(255, 193, 7, 0.1)',
                                tension: 0.4
                            },
                            {
                                label: 'قيد التنفيذ',
                                data: monthlyTrendData.data.in_progress,
                                borderColor: 'rgb(13, 202, 240)',
                                backgroundColor: 'rgba(13, 202, 240, 0.1)',
                                tension: 0.4
                            },
                            {
                                label: 'مكتملة',
                                data: monthlyTrendData.data.completed,
                                borderColor: 'rgb(25, 135, 84)',
                                backgroundColor: 'rgba(25, 135, 84, 0.1)',
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
                                labels: {
                                    font: {
                                        size: 12
                                    },
                                    padding: 15
                                }
                            },
                            tooltip: {
                                mode: 'index',
                                intersect: false
                            }
                        },
                        scales: {
                            y: {
                                beginAtZero: true,
                                ticks: {
                                    stepSize: 1
                                }
                            }
                        },
                        interaction: {
                            mode: 'nearest',
                            axis: 'x',
                            intersect: false
                        }
                    }
                });
            }

            // 2. Service Types Pie Chart
            const serviceTypesCtx = document.getElementById('serviceTypesChart');
            if (serviceTypesCtx && serviceTypesData.length > 0) {
                const topServices = serviceTypesData.slice(0, 5);
                new Chart(serviceTypesCtx, {
                    type: 'doughnut',
                    data: {
                        labels: topServices.map(s => s.name),
                        datasets: [{
                            data: topServices.map(s => s.total_maintenances),
                            backgroundColor: [
                                'rgba(13, 110, 253, 0.8)',
                                'rgba(25, 135, 84, 0.8)',
                                'rgba(255, 193, 7, 0.8)',
                                'rgba(220, 53, 69, 0.8)',
                                'rgba(13, 202, 240, 0.8)'
                            ],
                            borderWidth: 2,
                            borderColor: '#fff'
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: {
                                position: 'bottom',
                                labels: {
                                    font: {
                                        size: 11
                                    },
                                    padding: 10
                                }
                            },
                            tooltip: {
                                callbacks: {
                                    label: function(context) {
                                        const total = context.dataset.data.reduce((a, b) => a + b, 0);
                                        const percentage = ((context.parsed / total) * 100).toFixed(1);
                                        return context.label + ': ' + context.parsed + ' (' +
                                            percentage + '%)';
                                    }
                                }
                            }
                        }
                    }
                });
            }

            // 3. Status Distribution Chart
            const statusCtx = document.getElementById('statusDistributionChart');
            if (statusCtx) {
                const statusLabels = Object.values(statusBreakdown).map(s => s.label);
                const statusCounts = Object.values(statusBreakdown).map(s => s.count);
                const statusColors = Object.values(statusBreakdown).map(s => {
                    const colors = {
                        'warning': 'rgba(255, 193, 7, 0.8)',
                        'info': 'rgba(13, 202, 240, 0.8)',
                        'success': 'rgba(25, 135, 84, 0.8)',
                        'danger': 'rgba(220, 53, 69, 0.8)'
                    };
                    return colors[s.color] || 'rgba(108, 117, 125, 0.8)';
                });

                new Chart(statusCtx, {
                    type: 'pie',
                    data: {
                        labels: statusLabels,
                        datasets: [{
                            data: statusCounts,
                            backgroundColor: statusColors,
                            borderWidth: 2,
                            borderColor: '#fff'
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: {
                                position: 'bottom',
                                labels: {
                                    font: {
                                        size: 12
                                    },
                                    padding: 15
                                }
                            },
                            tooltip: {
                                callbacks: {
                                    label: function(context) {
                                        const total = context.dataset.data.reduce((a, b) => a + b, 0);
                                        const percentage = ((context.parsed / total) * 100).toFixed(1);
                                        return context.label + ': ' + context.parsed + ' (' +
                                            percentage + '%)';
                                    }
                                }
                            }
                        }
                    }
                });
            }

            // 4. Top Service Types Bar Chart
            const topServicesCtx = document.getElementById('topServiceTypesChart');
            if (topServicesCtx && serviceTypesData.length > 0) {
                const topServices = serviceTypesData.slice(0, 5);
                new Chart(topServicesCtx, {
                    type: 'bar',
                    data: {
                        labels: topServices.map(s => s.name),
                        datasets: [{
                            label: 'عدد الطلبات',
                            data: topServices.map(s => s.total_maintenances),
                            backgroundColor: 'rgba(13, 110, 253, 0.8)',
                            borderColor: 'rgb(13, 110, 253)',
                            borderWidth: 1
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        indexAxis: 'y',
                        plugins: {
                            legend: {
                                display: false
                            },
                            tooltip: {
                                callbacks: {
                                    label: function(context) {
                                        return 'عدد الطلبات: ' + context.parsed.x;
                                    }
                                }
                            }
                        },
                        scales: {
                            x: {
                                beginAtZero: true,
                                ticks: {
                                    stepSize: 1
                                }
                            }
                        }
                    }
                });
            }
        });
    </script>
@endpush
