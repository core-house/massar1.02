<div class="row">
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-primary shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">{{ __('shipping::shipping.total_shipments') }}</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $stats['total'] }}</div>
                    </div>
                    <div class="col-auto">
                        <i class="las la-box la-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-warning shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">{{ __('shipping::shipping.in_transit') }}</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $stats['in_transit'] }}</div>
                    </div>
                    <div class="col-auto">
                        <i class="las la-truck la-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-success shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-success text-uppercase mb-1">{{ __('shipping::shipping.delivered') }}</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $stats['delivered'] }}</div>
                    </div>
                    <div class="col-auto">
                        <i class="las la-check-circle la-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-info shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-info text-uppercase mb-1">{{ __('shipping::shipping.pending') }}</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $stats['pending'] }}</div>
                    </div>
                    <div class="col-auto">
                        <i class="las la-clock la-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-lg-6 mb-4">
        <div class="card shadow">
            <div class="card-header py-3 bg-primary text-white">
                <h6 class="m-0 font-weight-bold"><i class="las la-star"></i> {{ __('shipping::shipping.top_rated_drivers') }}</h6>
            </div>
            <div class="card-body">
                @if($topDrivers->count() > 0)
                    @foreach($topDrivers as $driver)
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <div>
                                <strong>{{ $driver->name }}</strong>
                                <br><small class="text-muted">{{ $driver->completed_deliveries }} {{ __('shipping::shipping.deliveries') }}</small>
                            </div>
                            <span class="badge bg-warning">
                                <i class="las la-star"></i> {{ number_format($driver->rating, 1) }}
                            </span>
                        </div>
                    @endforeach
                @else
                    <p class="text-muted text-center">{{ __('shipping::shipping.no_data_available') }}</p>
                @endif
            </div>
        </div>
    </div>

    <div class="col-lg-6 mb-4">
        <div class="card shadow">
            <div class="card-header py-3 bg-success text-white">
                <h6 class="m-0 font-weight-bold"><i class="las la-history"></i> {{ __('shipping::shipping.recent_shipments') }}</h6>
            </div>
            <div class="card-body">
                @if($recentShipments->count() > 0)
                    @foreach($recentShipments as $shipment)
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <div>
                                <a href="{{ route('shipments.show', $shipment->id) }}">
                                    <strong>{{ $shipment->tracking_number }}</strong>
                                </a>
                                <br><small class="text-muted">{{ $shipment->customer_name }}</small>
                            </div>
                            <span class="badge bg-{{ $shipment->status == 'delivered' ? 'success' : 'primary' }}">
                                {{ __('shipping::shipping.' . $shipment->status) }}
                            </span>
                        </div>
                    @endforeach
                    <div class="text-center mt-3">
                        <a href="{{ route('shipments.index') }}" class="btn btn-primary btn-sm">{{ __('shipping::shipping.view_all_shipments') }}</a>
                    </div>
                @else
                    <p class="text-muted text-center">{{ __('shipping::shipping.no_data_available') }}</p>
                @endif
            </div>
        </div>
    </div>
</div>

<style>
.border-left-primary { border-left: 0.25rem solid #4e73df !important; }
.border-left-success { border-left: 0.25rem solid #1cc88a !important; }
.border-left-info { border-left: 0.25rem solid #36b9cc !important; }
.border-left-warning { border-left: 0.25rem solid #f6c23e !important; }
</style>
