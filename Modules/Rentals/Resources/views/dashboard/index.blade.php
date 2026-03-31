@extends('admin.dashboard')

@section('sidebar')
    @include('components.sidebar.rentals')
@endsection

@section('content')
    @include('components.breadcrumb', [
        'title' => __('rentals::rentals.rentals_dashboard'),
        'breadcrumb_items' => [
            ['label' => __('navigation.home'), 'url' => route('admin.dashboard')],
            ['label' => __('rentals::rentals.rentals_dashboard')],
        ],
    ])

    <div class="container-fluid px-4">

        @can('view Rentals Statistics')
            <!-- Quick Stats Cards -->
            <div class="row g-3 mb-4">
                <div class="col-xl-3 col-md-6">
                    <div class="card border-0 shadow-sm h-100">
                        <div class="card-body">
                            <div class="d-flex align-items-center">
                                <div class="flex-shrink-0">
                                    <div class="bg-primary bg-opacity-10 rounded p-3">
                                        <i class="fas fa-building text-primary fa-2x"></i>
                                    </div>
                                </div>
                                <div class="flex-grow-1 ms-3">
                                    <p class="text-muted mb-1">{{ __('rentals::rentals.total_buildings') }}</p>
                                    <h3 class="mb-0">{{ $stats['overview']['total_buildings'] }}</h3>
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
                                        <i class="fas fa-door-open text-success fa-2x"></i>
                                    </div>
                                </div>
                                <div class="flex-grow-1 ms-3">
                                    <p class="text-muted mb-1">{{ __('rentals::rentals.total_units') }}</p>
                                    <h3 class="mb-0">{{ $stats['overview']['total_units'] }}</h3>
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
                                        <i class="fas fa-file-contract text-info fa-2x"></i>
                                    </div>
                                </div>
                                <div class="flex-grow-1 ms-3">
                                    <p class="text-muted mb-1">{{ __('rentals::rentals.active_leases_count') }}</p>
                                    <h3 class="mb-0">{{ $stats['overview']['active_leases'] }}</h3>
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
                                        <i class="fas fa-users text-warning fa-2x"></i>
                                    </div>
                                </div>
                                <div class="flex-grow-1 ms-3">
                                    <p class="text-muted mb-1">{{ __('rentals::rentals.total_clients') }}</p>
                                    <h3 class="mb-0">{{ $stats['overview']['total_clients'] }}</h3>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Financial Overview -->
            <div class="row g-3 mb-4">
                <div class="col-12">
                    <div class="card border-0 shadow-sm">
                        <div class="card-header bg-white py-3">
                            <h5 class="mb-0">{{ __('rentals::rentals.financial_revenue') }}</h5>
                        </div>
                        <div class="card-body">
                            <div class="row g-3 text-center">
                                <div class="col-md-4">
                                    <div class="border-end">
                                        <p class="text-muted mb-2">{{ __('rentals::rentals.monthly_revenue') }}</p>
                                        <h4 class="text-success mb-0">
                                            {{ number_format($stats['financial']['total_monthly_revenue'], 2) }} {{ __('rentals::rentals.currency') }}</h4>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="border-end">
                                        <p class="text-muted mb-2">{{ __('rentals::rentals.expected_annual_revenue') }}</p>
                                        <h4 class="text-primary mb-0">
                                            {{ number_format($stats['financial']['total_yearly_revenue'], 2) }} {{ __('rentals::rentals.currency') }}</h4>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <p class="text-muted mb-2">{{ __('rentals::rentals.average_rent') }}</p>
                                    <h4 class="text-info mb-0">{{ number_format($stats['financial']['average_rent'], 2) }} {{ __('rentals::rentals.currency') }}</h4>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Buildings Stats -->
            <div class="row g-3 mb-4">
                <div class="col-12">
                    <div class="card border-0 shadow-sm">
                        <div class="card-header bg-white py-3">
                            <h5 class="mb-0">{{ __('rentals::rentals.buildings_statistics') }}</h5>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-hover align-middle">
                                    <thead class="table-light">
                                        <tr>
                                            <th>{{ __('rentals::rentals.building_name') }}</th>
                                            <th>{{ __('rentals::rentals.address') }}</th>
                                            <th class="text-center">{{ __('rentals::rentals.floors') }}</th>
                                            <th class="text-center">{{ __('rentals::rentals.total_units') }}</th>
                                            <th class="text-center">{{ __('rentals::rentals.rented') }}</th>
                                            <th class="text-center">{{ __('rentals::rentals.available') }}</th>
                                            <th class="text-center">{{ __('rentals::rentals.occupancy_rate') }}</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($stats['buildings'] as $building)
                                            <tr>
                                                <td><strong>{{ $building->name }}</strong></td>
                                                <td>{{ $building->address }}</td>
                                                <td class="text-center">{{ $building->floors }}</td>
                                                <td class="text-center"><span
                                                        class="badge bg-secondary">{{ $building->total_units }}</span></td>
                                                <td class="text-center"><span
                                                        class="badge bg-success">{{ $building->rented_units }}</span></td>
                                                <td class="text-center"><span
                                                        class="badge bg-primary">{{ $building->available_units }}</span></td>
                                                <td class="text-center">
                                                    <div class="progress" style="height: 20px; min-width: 100px;">
                                                        <div class="progress-bar bg-success" role="progressbar"
                                                            style="width: {{ $building->occupancy_rate }}%">
                                                            {{ $building->occupancy_rate }}%
                                                        </div>
                                                    </div>
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="7" class="text-center text-muted py-4">
                                                    {{ __('rentals::rentals.no_buildings_registered') }}</td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Recent Leases & Lease Stats -->
            <div class="row g-3">
                <div class="col-xl-8">
                    <div class="card border-0 shadow-sm">
                        <div class="card-header bg-white py-3">
                            <h5 class="mb-0">{{ __('rentals::rentals.recent_leases') }}</h5>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-hover align-middle">
                                    <thead class="table-light">
                                        <tr>
                                            <th>{{ __('rentals::rentals.client') }}</th>
                                            <th>{{ __('rentals::rentals.building') }}</th>
                                            <th>{{ __('rentals::rentals.unit') }}</th>
                                            <th>{{ __('rentals::rentals.rent_amount') }}</th>
                                            <th>{{ __('rentals::rentals.start_date') }}</th>
                                            <th>{{ __('rentals::rentals.end_date') }}</th>
                                            <th>{{ __('rentals::rentals.status') }}</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($stats['leases']['recent_leases'] as $lease)
                                            <tr>
                                                <td>{{ $lease['client_name'] }}</td>
                                                <td>{{ $lease['building_name'] }}</td>
                                                <td>{{ $lease['unit_name'] }}</td>
                                                <td><strong>{{ number_format($lease['rent_amount'], 2) }} {{ __('rentals::rentals.currency') }}</strong></td>
                                                <td>{{ $lease['start_date'] }}</td>
                                                <td>{{ $lease['end_date'] }}</td>
                                                <td><span class="badge bg-success">{{ $lease['status'] }}</span></td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="7" class="text-center text-muted py-4">
                                                    {{ __('rentals::rentals.no_leases_registered') }}</td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-xl-4">
                    <div class="card border-0 shadow-sm">
                        <div class="card-header bg-white py-3">
                            <h5 class="mb-0">{{ __('rentals::rentals.leases_summary') }}</h5>
                        </div>
                        <div class="card-body">
                            <div class="mb-3">
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <span class="text-muted">{{ __('rentals::rentals.active_leases_count') }}</span>
                                    <h4 class="mb-0 text-success">{{ $stats['leases']['active'] }}</h4>
                                </div>
                                <hr>
                            </div>

                            <div class="mb-3">
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <span class="text-muted">{{ __('rentals::rentals.expired_leases') }}</span>
                                    <h4 class="mb-0 text-danger">{{ $stats['leases']['expired'] }}</h4>
                                </div>
                                <hr>
                            </div>

                            <div class="mb-3">
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <span class="text-muted">{{ __('rentals::rentals.upcoming_leases') }}</span>
                                    <h4 class="mb-0 text-info">{{ $stats['leases']['upcoming'] }}</h4>
                                </div>
                                <hr>
                            </div>

                            <div class="alert alert-warning mb-0">
                                <i class="fas fa-exclamation-triangle me-2"></i>
                                <strong>{{ $stats['leases']['expiring_soon'] }}</strong>
                                {{ __('rentals::rentals.leases_expire_30_days') }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @else
            <div class="alert alert-danger">{{ __('rentals::rentals.no_permission') }}</div>
        @endcan

    </div>
@endsection

@push('scripts')
    <script>
        // JS for charts can be added here
        document.addEventListener('DOMContentLoaded', function() {
            console.log('Dashboard loaded successfully');
        });
    </script>
@endpush
