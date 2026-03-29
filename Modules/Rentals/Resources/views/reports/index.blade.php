@extends('admin.dashboard')

@section('sidebar')
    @include('components.sidebar.rentals')
@endsection

@section('content')
    @include('components.breadcrumb', [
        'title' => __('rentals::rentals.reports'),
        'breadcrumb_items' => [
            ['label' => __('navigation.home'), 'url' => route('admin.dashboard')],
            ['label' => __('rentals::rentals.reports')],
        ],
    ])

    <div class="container-fluid px-4">
        <ul class="nav nav-tabs mb-4" role="tablist">
            <li class="nav-item">
                <button class="nav-link active" data-bs-toggle="tab" data-bs-target="#activeRentalsPane" type="button">
                    <i class="fas fa-file-contract me-1"></i> {{ __('rentals::rentals.active_rentals') }}
                </button>
            </li>
            <li class="nav-item">
                <button class="nav-link" data-bs-toggle="tab" data-bs-target="#availableUnitsPane" type="button">
                    <i class="fas fa-door-open me-1"></i> {{ __('rentals::rentals.available_units_items') }}
                </button>
            </li>
        </ul>

        <div class="tab-content" id="reportsTabsContent">
            {{-- Active Rentals Pane --}}
            <div class="tab-pane fade show active" id="activeRentalsPane" role="tabpanel">
                <div class="card shadow-sm border-0">
                    <div class="card-header bg-white">
                        <h5 class="mb-0 text-success"><i class="fas fa-check-circle me-2"></i>{{ __('rentals::rentals.active_rentals_list') }}</h5>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-striped table-hover align-middle">
                                <thead class="table-light">
                                    <tr>
                                        <th>#</th>
                                        <th>{{ __('rentals::rentals.client') }}</th>
                                        <th>{{ __('rentals::rentals.unit_item') }}</th>
                                        <th>{{ __('rentals::rentals.type') }}</th>
                                        <th>{{ __('rentals::rentals.start_date') }}</th>
                                        <th>{{ __('rentals::rentals.end_date') }}</th>
                                        <th>{{ __('rentals::rentals.remaining_days') }}</th>
                                        <th>{{ __('rentals::rentals.amount') }}</th>
                                        <th>{{ __('rentals::rentals.actions') }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($activeLeases as $lease)
                                        <tr>
                                            <td>{{ $loop->iteration }}</td>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <div class="avatar-sm rounded-circle bg-light-primary text-primary me-2 d-flex align-items-center justify-content-center" style="width: 35px; height: 35px;">
                                                        <i class="fas fa-user-circle"></i>
                                                    </div>
                                                    <div>
                                                        <div class="fw-bold">{{ optional($lease->client)->cname }}</div>
                                                        <small class="text-muted">{{ optional($lease->client)->phone }}</small>
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                <span class="fw-bold">
                                                    @if(($lease->unit->unit_type ?? 'building') === 'item' && $lease->unit->item)
                                                        {{ $lease->unit->item->name }}
                                                    @else
                                                        {{ optional($lease->unit)->name }}
                                                    @endif
                                                </span>
                                            </td>
                                            <td>
                                                <span class="badge {{ ($lease->unit->unit_type ?? 'building') === 'item' ? 'bg-info' : 'bg-primary' }}">
                                                    {{ ($lease->unit->unit_type ?? 'building') === 'item' ? __('rentals::rentals.item') : __('rentals::rentals.unit') }}
                                                </span>
                                            </td>
                                            <td>{{ $lease->start_date?->format('Y/m/d') }}</td>
                                            <td>{{ $lease->end_date?->format('Y/m/d') }}</td>
                                            <td>
                                                @php
                                                    $days = now()->diffInDays($lease->end_date, false);
                                                @endphp
                                                <span class="badge {{ $days < 7 ? 'bg-danger' : 'bg-success' }}">
                                                    {{ $days }} {{ __('rentals::rentals.days') }}
                                                </span>
                                            </td>
                                            <td>{{ number_format($lease->rent_amount, 2) }}</td>
                                            <td>
                                                <a href="{{ route('rentals.leases.show', $lease->id) }}" class="btn btn-sm btn-outline-primary">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="9" class="text-center py-4 text-muted">
                                                <i class="fas fa-inbox fa-3x mb-3"></i>
                                                <p>{{ __('rentals::rentals.no_active_rentals') }}</p>
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Available Units Pane --}}
            <div class="tab-pane fade" id="availableUnitsPane" role="tabpanel">
                <div class="card shadow-sm border-0">
                    <div class="card-header bg-white">
                        <h5 class="mb-0 text-primary"><i class="fas fa-box-open me-2"></i>{{ __('rentals::rentals.available_units_items_list') }}</h5>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-striped table-hover align-middle">
                                <thead class="table-light">
                                    <tr>
                                        <th>#</th>
                                        <th>{{ __('rentals::rentals.name') }}</th>
                                        <th>{{ __('rentals::rentals.type') }}</th>
                                        <th>{{ __('rentals::rentals.building_code') }}</th>
                                        <th>{{ __('rentals::rentals.floor') }}</th>
                                        <th>{{ __('rentals::rentals.area_m2') }}</th>
                                        <th>{{ __('rentals::rentals.actions') }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($availableUnits as $unit)
                                        <tr>
                                            <td>{{ $loop->iteration }}</td>
                                            <td class="fw-bold">{{ $unit->name }}</td>
                                            <td>
                                                <span class="badge {{ ($unit->unit_type ?? 'building') === 'item' ? 'bg-info' : 'bg-primary' }}">
                                                    {{ ($unit->unit_type ?? 'building') === 'item' ? __('rentals::rentals.item') : __('rentals::rentals.unit') }}
                                                </span>
                                            </td>
                                            <td>
                                                @if(($unit->unit_type ?? 'building') === 'item' && $unit->item)
                                                    <span class="text-muted"><i class="fas fa-barcode me-1"></i> {{ $unit->item->code }}</span>
                                                @elseif(($unit->unit_type ?? 'building') === 'building' && $unit->building)
                                                    <span class="text-muted"><i class="fas fa-building me-1"></i> {{ $unit->building->name }}</span>
                                                @else
                                                    -
                                                @endif
                                            </td>
                                            <td>{{ $unit->floor ?? '-' }}</td>
                                            <td>{{ $unit->area ? $unit->area . ' m²' : '-' }}</td>
                                            <td>
                                                <div class="btn-group">
                                                    @can('create Leases')
                                                        <a href="{{ route('rentals.leases.create', ['unit_id' => $unit->id]) }}" class="btn btn-sm btn-success">
                                                            <i class="fas fa-file-contract me-1"></i> {{ __('rentals::rentals.lease') }}
                                                        </a>
                                                    @endcan
                                                    @can('edit Unit')
                                                        <a href="{{ route('rentals.units.edit', $unit->id) }}" class="btn btn-sm btn-outline-info">
                                                            <i class="fas fa-edit"></i>
                                                        </a>
                                                    @endcan
                                                </div>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="7" class="text-center py-4 text-muted">
                                                <i class="fas fa-box-open fa-3x mb-3"></i>
                                                <p>{{ __('rentals::rentals.no_available_units') }}</p>
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
