@extends('admin.dashboard')

@section('sidebar')
    @include('components.sidebar.shipping')
@endsection

@section('content')
    <div class="container-fluid">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h1 class="h3 mb-0">{{ __('shipping::shipping.dashboard_title') }}</h1>
                <p class="text-muted mb-0">{{ __('shipping::shipping.dashboard_subtitle') }}</p>
            </div>
        </div>

        <!-- Widget Stats -->
        @include('shipping::components.stats-widget', ['stats' => $widgetStats, 'topDrivers' => $topDrivers, 'recentShipments' => $recentShipments])

        <!-- Quick Stats Cards -->
        <div class="row g-4 mb-4">
            <div class="col-xl-3 col-md-6">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="flex-shrink-0">
                                <div class="bg-primary bg-opacity-10 rounded p-3">
                                    <i class="las la-shipping-fast text-primary la-2x"></i>
                                </div>
                            </div>
                            <div class="flex-grow-1 ms-3">
                                <p class="text-muted mb-1">{{ __('shipping::shipping.total_shipments') }}</p>
                                <h3 class="mb-0">{{ $stats['overview']['total_shipments'] }}</h3>
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
                                    <i class="las la-box text-info la-2x"></i>
                                </div>
                            </div>
                            <div class="flex-grow-1 ms-3">
                                <p class="text-muted mb-1">{{ __('shipping::shipping.total_orders') }}</p>
                                <h3 class="mb-0">{{ $stats['overview']['total_orders'] }}</h3>
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
                                    <i class="las la-user-tie text-success la-2x"></i>
                                </div>
                            </div>
                            <div class="flex-grow-1 ms-3">
                                <p class="text-muted mb-1">{{ __('shipping::shipping.total_drivers') }}</p>
                                <h3 class="mb-0">{{ $stats['overview']['total_drivers'] }}</h3>
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
                                    <i class="las la-building text-warning la-2x"></i>
                                </div>
                            </div>
                            <div class="flex-grow-1 ms-3">
                                <p class="text-muted mb-1">{{ __('shipping::shipping.total_companies') }}</p>
                                <h3 class="mb-0">{{ $stats['overview']['total_companies'] }}</h3>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Shipment Status Breakdown -->
        <div class="row g-4 mb-4">
            <div class="col-xl-6">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-header bg-white py-3">
                        <h5 class="mb-0">{{ __('shipping::shipping.shipment_status_distribution') }}</h5>
                    </div>
                    <div class="card-body">
                        @foreach ($stats['shipment_status'] as $status => $data)
                            <div class="mb-3">
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <span>{{ $data['label'] }}</span>
                                    <strong>{{ $data['count'] }} ({{ $data['percentage'] }}%)</strong>
                                </div>
                                <div class="progress" style="height: 8px;">
                                    <div class="progress-bar bg-primary" role="progressbar"
                                        style="width: {{ $data['percentage'] }}%"></div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
            <div class="col-xl-6">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-header bg-white py-3">
                        <h5 class="mb-0">{{ __('shipping::shipping.order_delivery_status_distribution') }}</h5>
                    </div>
                    <div class="card-body">
                        @foreach ($stats['delivery_status'] as $status => $data)
                            <div class="mb-3">
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <span>{{ $data['label'] }}</span>
                                    <strong>{{ $data['count'] }} ({{ $data['percentage'] }}%)</strong>
                                </div>
                                <div class="progress" style="height: 8px;">
                                    <div class="progress-bar bg-info" role="progressbar"
                                        style="width: {{ $data['percentage'] }}%"></div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>

        <!-- Driver & Company Stats -->
        <div class="row g-4 mb-4">
            <div class="col-xl-6">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-header bg-white py-3">
                        <h5 class="mb-0">{{ __('shipping::shipping.driver_statistics') }}</h5>
                    </div>
                    <div class="card-body">
                        <ul class="list-unstyled mb-0">
                            <li class="mb-2">
                                <span class="badge bg-success">{{ __('shipping::shipping.available') }}</span>
                                <span class="ms-2">{{ $stats['drivers']['available'] }}</span>
                            </li>
                            <li>
                                <span class="badge bg-secondary">{{ __('shipping::shipping.busy') }}</span>
                                <span class="ms-2">{{ $stats['drivers']['busy'] }}</span>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
            <div class="col-xl-6">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-header bg-white py-3">
                        <h5 class="mb-0">{{ __('shipping::shipping.company_statistics') }}</h5>
                    </div>
                    <div class="card-body">
                        <ul class="list-unstyled mb-0">
                            <li class="mb-2">
                                <span class="badge bg-warning">{{ __('shipping::shipping.active') }}</span>
                                <span class="ms-2">{{ $stats['companies']['active'] }}</span>
                            </li>
                            <li>
                                <span class="badge bg-danger">{{ __('shipping::shipping.inactive') }}</span>
                                <span class="ms-2">{{ $stats['companies']['inactive'] }}</span>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>

    </div>
@endsection
