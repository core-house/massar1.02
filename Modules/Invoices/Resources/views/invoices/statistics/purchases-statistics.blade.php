@extends('admin.dashboard')

@section('sidebar')
    @include('components.sidebar.purchases-invoices')
@endsection

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-12">
                <h2 class="mb-4">{{ __('invoices.purchases_statistics') }}</h2>
            </div>
        </div>
        <div class="row">
            <div class="col-md-4">
                <div class="card stats-card">
                    <div class="card-body">
                        <h5 class="card-title">{{ __('invoices.total_purchases') }}</h5>
                        <p class="card-text">{{ number_format($stats['total_purchases'], 2) }} {{ __('EGP') }}</p>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card stats-card">
                    <div class="card-body">
                        <h5 class="card-title">{{ __('invoices.total_returns') }}</h5>
                        <p class="card-text">{{ number_format($stats['total_returns'], 2) }} {{ __('EGP') }}</p>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card stats-card">
                    <div class="card-body">
                        <h5 class="card-title">{{ __('invoices.total_orders') }}</h5>
                        <p class="card-text">{{ $stats['total_orders'] }}</p>
                    </div>
                </div>
            </div>
        </div>
        <div class="row mt-4">
            <div class="col-md-4">
                <div class="card stats-card">
                    <div class="card-body">
                        <h5 class="card-title">{{ __('invoices.total_quotations') }}</h5>
                        <p class="card-text">{{ $stats['total_quotations'] }}</p>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card stats-card">
                    <div class="card-body">
                        <h5 class="card-title">{{ __('invoices.today_purchases') }}</h5>
                        <p class="card-text">{{ number_format($stats['today_purchases'], 2) }} {{ __('EGP') }}</p>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card stats-card">
                    <div class="card-body">
                        <h5 class="card-title">{{ __('invoices.pending_payments') }}</h5>
                        <p class="card-text">{{ number_format($stats['pending_payments'], 2) }} {{ __('EGP') }}</p>
                    </div>
                </div>
            </div>
        </div>
        <div class="row mt-4">
            <div class="col-md-4">
                <div class="card stats-card">
                    <div class="card-body">
                        <h5 class="card-title">{{ __('invoices.highest_purchase_invoice') }}</h5>
                        <p class="card-text">{{ number_format($stats['highest_purchase'], 2) }} {{ __('EGP') }}</p>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card stats-card">
                    <div class="card-body">
                        <h5 class="card-title">{{ __('invoices.active_suppliers') }}</h5>
                        <p class="card-text">{{ $stats['active_suppliers'] }}</p>
                    </div>
                </div>
            </div>
        </div>
        <div class="row mt-4">
            <div class="col-md-6">
                <div class="card stats-card">
                    <div class="card-body">
                        <h5 class="card-title">{{ __('invoices.purchases_returns_distribution') }}</h5>
                        <canvas id="purchasesPieChart" class="chart-canvas"></canvas>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card stats-card">
                    <div class="card-body">
                        <h5 class="card-title">{{ __('invoices.purchases_during_week') }}</h5>
                        <canvas id="purchasesBarChart" class="chart-canvas"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <style>
        .stats-card {
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            background-color: #f8f9fa;
            border: none;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        .stats-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 6px 12px rgba(0, 0, 0, 0.15);
        }

        .card-title { font-size: 1.1rem; font-weight: 600; color: #343a40; }
        .card-text { font-size: 1.3rem; font-weight: bold; color: #007bff; }
        h2 { font-weight: 700; color: #212529; }
        .chart-canvas { max-height: 200px !important; max-width: 100% !important; }
    </style>

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        const pieCtx = document.getElementById('purchasesPieChart').getContext('2d');
        new Chart(pieCtx, {
            type: 'pie',
            data: {
                labels: ["{{ __('Purchases') }}", "{{ __('Returns') }}"],
                datasets: [{
                    data: [@json($stats['total_purchases']), @json($stats['total_returns'])],
                    backgroundColor: ['#007bff', '#dc3545'],
                    borderColor: ['#0056b3', '#c82333'],
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: { position: 'top', labels: { font: { size: 12 } } },
                    title: { display: true, text: "{{ __('invoices.purchases_returns_distribution') }}", font: { size: 14 } }
                }
            }
        });

        const barCtx = document.getElementById('purchasesBarChart').getContext('2d');
        new Chart(barCtx, {
            type: 'bar',
            data: {
                labels: @json(array_keys($stats['purchases_by_day']->toArray())),
                datasets: [{
                    label: "{{ __('Purchases') }}",
                    data: @json(array_values($stats['purchases_by_day']->toArray())),
                    backgroundColor: '#007bff',
                    borderColor: '#0056b3',
                    borderWidth: 1
                }, {
                    label: "{{ __('Returns') }}",
                    data: @json(array_values($stats['returns_by_day']->toArray())),
                    backgroundColor: '#dc3545',
                    borderColor: '#c82333',
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                scales: {
                    y: { beginAtZero: true, title: { display: true, text: "{{ __('invoices.value_egp') }}", font: { size: 12 } } },
                    x: { title: { display: true, text: "{{ __('invoices.date') }}", font: { size: 12 } } }
                },
                plugins: {
                    legend: { position: 'top', labels: { font: { size: 12 } } },
                    title: { display: true, text: "{{ __('invoices.purchases_returns_during_week') }}", font: { size: 14 } }
                }
            }
        });
    </script>
@endsection

