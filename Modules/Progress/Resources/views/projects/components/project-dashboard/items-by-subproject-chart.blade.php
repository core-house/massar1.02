<div class="card stat-card border-0 shadow-sm mb-4">
    <div class="card-header bg-transparent py-3 d-flex justify-content-between align-items-center">
        <h5 class="card-title mb-0 fw-bold text-primary">
            <i class="fas fa-list me-2"></i>{{ __('general.items_by_subproject') }}
        </h5>
        <!-- Dropdown Filter -->
        <div style="min-width: 200px;" x-data>
            <select class="form-select form-select-sm" 
                    id="subprojectSelect"
                    @change="$dispatch('subproject-changed', $el.value)">
            </select>
        </div>
    </div>
    <div class="card-body" 
         x-data="itemsBySubprojectChart()" 
         x-init="initChart()">
        
        <!-- Legend Explanation -->
        <div class="mb-3 small text-muted">
            <span class="me-3"><strong style="color: #ff6384;">{{ __('general.progress') }}</strong> ({{ __('general.actual') }})</span>
            <span><strong style="color: #36a2eb;">{{ __('general.planned_progress') }}</strong> ({{ __('general.planned') }})</span>
        </div>

        <!-- Chart -->
        <div style="height: 400px; width: 100%;">
            <canvas id="itemsBySubprojectChart"></canvas>
        </div>
    </div>
</div>

<script>
    document.addEventListener('alpine:init', () => {
        Alpine.data('itemsBySubprojectChart', () => ({
            chart: null,
            allData: @json($itemsBySubproject),
            currentSubproject: null,

            initChart() {
                const ctx = document.getElementById('itemsBySubprojectChart').getContext('2d');
                const select = document.getElementById('subprojectSelect');
                
                // Populate Dropdown
                const subprojects = Object.keys(this.allData);
                if (subprojects.length === 0) {
                     select.innerHTML = '<option disabled>No Data</option>';
                     return;
                }

                subprojects.forEach(sub => {
                    const option = document.createElement('option');
                    option.value = sub;
                    option.text = sub;
                    select.appendChild(option);
                });

                // Set Default
                this.currentSubproject = subprojects[0];
                select.value = this.currentSubproject;

                // Initialize Chart
                const initialData = this.getDataForSubproject(this.currentSubproject);

                this.chart = new Chart(ctx, {
                    type: 'bar',
                    data: {
                        labels: initialData.labels,
                        datasets: [
                            {
                                label: '{{ __("general.progress") }}',
                                data: initialData.progress,
                                backgroundColor: '#ff6384', // Pink/Red
                                borderColor: '#ff6384',
                                borderWidth: 1,
                                barPercentage: 0.6,
                                categoryPercentage: 0.8
                            },
                            {
                                label: '{{ __("general.planned_progress") }}',
                                data: initialData.planned,
                                backgroundColor: '#36a2eb', // Blue
                                borderColor: '#36a2eb',
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
                            legend: {
                                display: false // Custom legend above
                            }
                        }
                    }
                });

                // Listen for changes
                window.addEventListener('subproject-changed', (e) => {
                    this.updateChart(e.detail);
                });
            },

            getDataForSubproject(subName) {
                const items = this.allData[subName]?.items || [];
                return {
                    labels: items.map(i => i.label),
                    progress: items.map(i => i.progress),
                    planned: items.map(i => i.planned)
                };
            },

            updateChart(subName) {
                if (!this.chart) return;
                
                const newData = this.getDataForSubproject(subName);
                
                this.chart.data.labels = newData.labels;
                this.chart.data.datasets[0].data = newData.progress;
                this.chart.data.datasets[1].data = newData.planned;
                
                this.chart.update();
            }
        }));
    });
</script>
