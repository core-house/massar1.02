@php
    $chartItems = array_map(function($label, $id) {
        return ['id' => $id, 'label' => $label];
    }, $advancedChartData['labels'], $advancedChartData['ids']);
@endphp
<div class="card stat-card border-0 shadow-sm mb-4">
    <div class="card-header bg-transparent py-3 d-flex justify-content-between align-items-center">
        <h5 class="card-title mb-0 fw-bold text-primary">
            <i class="fas fa-chart-bar me-2"></i>{{ __('general.project_progress_overview') }}
        </h5>
        <!-- Actions: Select/Deselect All -->
        <div x-data>
            <button @click="$dispatch('select-all-items')" class="btn btn-sm btn-outline-primary me-2">
                <i class="fas fa-check-double me-1"></i> {{ __('general.select_all') }}
            </button>
            <button @click="$dispatch('deselect-all-items')" class="btn btn-sm btn-outline-secondary">
                <i class="fas fa-times me-1"></i> {{ __('general.deselect_all') }}
            </button>
        </div>
    </div>
    <div class="card-body" 
         x-data="advancedChart()" 
         x-init="initChart()">
        
        <!-- Filters Area -->
        <div class="mb-4 p-3 bg-light rounded border">
            <h6 class="fw-bold mb-3"><i class="fas fa-filter me-2 text-primary"></i>{{ __('general.filter_items') }}</h6>
            <div class="row g-2" style="max-height: 150px; overflow-y: auto;">
                <template x-for="(item, index) in allItems" :key="item.id">
                    <div class="col-md-3 col-sm-6">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" 
                                   :id="'filter-' + item.id" 
                                   :value="item.id" 
                                   x-model="selectedItems"
                                   @change="updateChart()">
                            <label class="form-check-label small text-truncate d-block" 
                                   :for="'filter-' + item.id" 
                                   x-text="item.label" 
                                   :title="item.label">
                            </label>
                        </div>
                    </div>
                </template>
            </div>
        </div>

        <!-- Chart -->
        <div style="height: 400px; width: 100%;">
            <canvas id="advancedProgressChart"></canvas>
        </div>
    </div>
</div>

<script>
    document.addEventListener('alpine:init', () => {
        Alpine.data('advancedChart', () => ({
            chart: null,
            allItems: @json($chartItems),
            allData: @json($advancedChartData),
            selectedItems: @json($advancedChartData['ids']), // Check all by default

            initChart() {
                const ctx = document.getElementById('advancedProgressChart').getContext('2d');
                
                // Color Config
                const plannedColor = 'rgba(108, 117, 125, 0.4)'; // Gray for planned
                const plannedBorder = 'rgba(108, 117, 125, 1)';
                const actualColor = '#4e73df'; // Blue for actual
                
                this.chart = new Chart(ctx, {
                    type: 'bar',
                    data: {
                        labels: this.allData.labels,
                        datasets: [
                            {
                                label: '{{ __("general.planned") }}',
                                data: this.allData.planned,
                                backgroundColor: plannedColor,
                                borderColor: plannedBorder,
                                borderWidth: 1,
                                barPercentage: 0.6,
                                categoryPercentage: 0.8
                            },
                            {
                                label: '{{ __("general.actual") }}',
                                data: this.allData.actual,
                                backgroundColor: actualColor,
                                borderColor: actualColor,
                                borderWidth: 1,
                                barPercentage: 0.6,
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

                // Listeners for global buttons
                window.addEventListener('select-all-items', () => {
                    this.selectedItems = this.allItems.map(i => i.id);
                    this.updateChart();
                });

                window.addEventListener('deselect-all-items', () => {
                    this.selectedItems = [];
                    this.updateChart();
                });
            },

            updateChart() {
                if (!this.chart) return;

                // Filter data based on selectedItems
                const indices = this.allItems
                    .map((item, index) => this.selectedItems.includes(item.id) ? index : -1)
                    .filter(index => index !== -1);

                const newLabels = indices.map(i => this.allData.labels[i]);
                const newPlanned = indices.map(i => this.allData.planned[i]);
                const newActual = indices.map(i => this.allData.actual[i]);

                this.chart.data.labels = newLabels;
                this.chart.data.datasets[0].data = newPlanned;
                this.chart.data.datasets[1].data = newActual;
                
                this.chart.update();
            }
        }));
    });
</script>
