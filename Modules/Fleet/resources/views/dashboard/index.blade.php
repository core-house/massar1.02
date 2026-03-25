@extends('admin.dashboard')

@section('sidebar')
    @include('components.sidebar.fleet')
@endsection

@section('content')
    @include('components.breadcrumb', [
        'title' => __('fleet::fleet.Fleet Dashboard'),
        'breadcrumb_items' => [['label' => __('fleet::fleet.Home'), 'url' => route('admin.dashboard')], ['label' => __('fleet::fleet.Fleet Dashboard')]],
    ])

    <div class="row">
        <!-- Vehicle Statistics -->
        <div class="col-xl-3 col-md-6">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-muted mb-1">{{ __('fleet::fleet.Total Vehicles') }}</h6>
                            <h3 class="mb-0">{{ $totalVehicles }}</h3>
                        </div>
                        <div class="avatar-sm">
                            <span class="avatar-title bg-primary rounded-circle">
                                <i class="las la-car"></i>
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-muted mb-1">{{ __('fleet::fleet.Available Vehicles') }}</h6>
                            <h3 class="mb-0 text-success">{{ $availableVehicles }}</h3>
                        </div>
                        <div class="avatar-sm">
                            <span class="avatar-title bg-success rounded-circle">
                                <i class="las la-check-circle"></i>
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-muted mb-1">{{ __('fleet::fleet.In Use Vehicles') }}</h6>
                            <h3 class="mb-0 text-primary">{{ $inUseVehicles }}</h3>
                        </div>
                        <div class="avatar-sm">
                            <span class="avatar-title bg-primary rounded-circle">
                                <i class="las la-road"></i>
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-muted mb-1">{{ __('fleet::fleet.Maintenance Vehicles') }}</h6>
                            <h3 class="mb-0 text-warning">{{ $maintenanceVehicles }}</h3>
                        </div>
                        <div class="avatar-sm">
                            <span class="avatar-title bg-warning rounded-circle">
                                <i class="las la-tools"></i>
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Trip Statistics -->
        <div class="col-xl-3 col-md-6">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-muted mb-1">{{ __('fleet::fleet.Today Trips') }}</h6>
                            <h3 class="mb-0">{{ $todayTrips }}</h3>
                        </div>
                        <div class="avatar-sm">
                            <span class="avatar-title bg-info rounded-circle">
                                <i class="las la-calendar-day"></i>
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-muted mb-1">{{ __('fleet::fleet.Month Trips') }}</h6>
                            <h3 class="mb-0 text-primary">{{ $monthTrips }}</h3>
                        </div>
                        <div class="avatar-sm">
                            <span class="avatar-title bg-primary rounded-circle">
                                <i class="las la-calendar"></i>
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-muted mb-1">{{ __('fleet::fleet.Completed Trips') }}</h6>
                            <h3 class="mb-0 text-success">{{ $completedTrips }}</h3>
                        </div>
                        <div class="avatar-sm">
                            <span class="avatar-title bg-success rounded-circle">
                                <i class="las la-flag-checkered"></i>
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-muted mb-1">{{ __('fleet::fleet.In Progress Trips') }}</h6>
                            <h3 class="mb-0 text-warning">{{ $inProgressTrips }}</h3>
                        </div>
                        <div class="avatar-sm">
                            <span class="avatar-title bg-warning rounded-circle">
                                <i class="las la-spinner"></i>
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Fuel Statistics -->
        <div class="col-xl-4 col-md-6">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-muted mb-1">{{ __('fleet::fleet.Today Fuel Cost') }}</h6>
                            <h3 class="mb-0">{{ number_format($todayFuelCost, 2) }} {{ __('fleet::fleet.egp') }}</h3>
                        </div>
                        <div class="avatar-sm">
                            <span class="avatar-title bg-danger rounded-circle">
                                <i class="las la-gas-pump"></i>
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-4 col-md-6">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-muted mb-1">{{ __('fleet::fleet.Month Fuel Cost') }}</h6>
                            <h3 class="mb-0 text-danger">{{ number_format($monthFuelCost, 2) }} {{ __('fleet::fleet.egp') }}</h3>
                        </div>
                        <div class="avatar-sm">
                            <span class="avatar-title bg-danger rounded-circle">
                                <i class="las la-money-bill-wave"></i>
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-4 col-md-12">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-muted mb-1">{{ __('fleet::fleet.Total Fuel Quantity') }}</h6>
                            <h3 class="mb-0 text-info">{{ number_format($totalFuelQuantity, 2) }} {{ __('fleet::fleet.L') }}</h3>
                        </div>
                        <div class="avatar-sm">
                            <span class="avatar-title bg-info rounded-circle">
                                <i class="las la-fill-drip"></i>
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Recent Trips -->
        <div class="col-lg-6">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">{{ __('fleet::fleet.Recent Trips') }}</h5>
                    <a href="{{ route('fleet.trips.index') }}" class="btn btn-sm btn-outline-primary">{{ __('fleet::fleet.View') }}</a>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>{{ __('fleet::fleet.Trip') }} #</th>
                                    <th>{{ __('fleet::fleet.Vehicle') }}</th>
                                    <th>{{ __('fleet::fleet.Status') }}</th>
                                    <th>{{ __('fleet::fleet.Date') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($recentTrips as $trip)
                                    <tr>
                                        <td>{{ $trip->trip_number }}</td>
                                        <td>{{ $trip->vehicle->plate_number }}</td>
                                        <td>
                                            <span class="badge bg-{{ $trip->status->color() }}">
                                                {{ $trip->status->label() }}
                                            </span>
                                        </td>
                                        <td>{{ $trip->start_date->format('Y-m-d') }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Recent Fuel Records -->
        <div class="col-lg-6">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">{{ __('fleet::fleet.Recent Fuel Records') }}</h5>
                    <a href="{{ route('fleet.fuel-records.index') }}" class="btn btn-sm btn-outline-primary">{{ __('fleet::fleet.View') }}</a>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>{{ __('fleet::fleet.Vehicle') }}</th>
                                    <th>{{ __('fleet::fleet.Fuel Type') }}</th>
                                    <th>{{ __('fleet::fleet.Cost') }}</th>
                                    <th>{{ __('fleet::fleet.Date') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($recentFuelRecords as $record)
                                    <tr>
                                        <td>{{ $record->vehicle->plate_number }}</td>
                                        <td>{{ $record->fuel_type->label() }}</td>
                                        <td>{{ number_format($record->cost, 2) }}</td>
                                        <td>{{ $record->fuel_date->format('Y-m-d') }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

