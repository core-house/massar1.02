@extends('admin.dashboard')

@section('sidebar')
    @include('components.sidebar.rentals')
@endsection

@section('content')
    @include('components.breadcrumb', [
        'title' => __('rentals::rentals.buildings_and_units'),
        'breadcrumb_items' => [
            ['label' => __('navigation.home'), 'url' => route('admin.dashboard')],
            ['label' => __('rentals::rentals.buildings_and_units')],
        ],
    ])
    <div class="container-fluid px-4">
        @can('view Rentals Statistics')
            <div class="row g-3 mb-4">
                @php
                    $stats = [
                        ['title' => __('rentals::rentals.total_buildings'), 'value' => $buildings->count(), 'icon' => 'building', 'color' => 'primary'],
                        ['title' => __('rentals::rentals.total_units'), 'value' => $units->count(), 'icon' => 'home', 'color' => 'success'],
                        ['title' => __('rentals::rentals.rented_units'), 'value' => $units->where('status', \Modules\Rentals\Enums\UnitStatus::RENTED)->count(), 'icon' => 'key', 'color' => 'warning'],
                        ['title' => __('rentals::rentals.available_units'), 'value' => $units->where('status', \Modules\Rentals\Enums\UnitStatus::AVAILABLE)->count(), 'icon' => 'door-open', 'color' => 'info'],
                    ];
                @endphp
                @foreach($stats as $stat)
                    <div class="col-xl-3 col-md-6">
                        <div class="card border-start border-{{ $stat['color'] }} border-3 shadow-sm h-100">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <div class="text-xs fw-bold text-{{ $stat['color'] }} text-uppercase mb-1">{{ $stat['title'] }}</div>
                                        <div class="h5 mb-0 fw-bold">{{ $stat['value'] }}</div>
                                    </div>
                                    <i class="fas fa-{{ $stat['icon'] }} fa-2x text-gray-300"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @endcan

        <ul class="nav nav-tabs mb-4" role="tablist">
            <li class="nav-item">
                <button class="nav-link active" data-bs-toggle="tab" data-bs-target="#buildingsPane" type="button">
                    <i class="fas fa-building me-1"></i> {{ __('rentals::rentals.buildings') }}
                </button>
            </li>
            <li class="nav-item">
                <button class="nav-link" data-bs-toggle="tab" data-bs-target="#itemsPane" type="button">
                    <i class="fas fa-tshirt me-1"></i> {{ __('rentals::rentals.rental_items') }}
                </button>
            </li>
        </ul>

        <div class="tab-content">
            <div class="tab-pane fade show active" id="buildingsPane">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h5 class="mb-0"><i class="fas fa-city text-primary me-2"></i>{{ __('rentals::rentals.buildings') }}</h5>
                    @can('create Buildings')
                        <a href="{{ route('rentals.buildings.create') }}" class="btn btn-primary btn-sm">
                            <i class="fas fa-plus me-1"></i>{{ __('rentals::rentals.add') }}
                        </a>
                    @endcan
                </div>

                <div class="row g-3">
                    @forelse ($buildings as $building)
                        <div class="col-lg-4 col-md-6">
                            <div class="card h-100 shadow-sm">
                                <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                                    <h6 class="mb-0"><i class="fas fa-building me-2"></i>{{ $building->name }}</h6>
                                    <div class="btn-group btn-group-sm">
                                        @can('create Unit')
                                            <a href="{{ route('rentals-units.create', $building->id) }}" class="btn btn-light btn-sm" title="{{ __('rentals::rentals.add_unit') }}">
                                                <i class="fas fa-plus"></i>
                                            </a>
                                        @endcan
                                        @can('edit Buildings')
                                            <a href="{{ route('rentals.buildings.edit', $building->id) }}" class="btn btn-light btn-sm" title="{{ __('rentals::rentals.edit') }}">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                        @endcan
                                    </div>
                                </div>
                                <div class="card-body">
                                    <p class="text-muted small mb-3">
                                        <i class="fas fa-map-marker-alt me-1"></i>{{ $building->address ?: __('rentals::rentals.no_address') }}
                                    </p>
                                    <div class="row text-center mb-3">
                                        <div class="col-6 border-end">
                                            <div class="h5 mb-0">{{ $building->floors ?: 0 }}</div>
                                            <small class="text-muted">{{ __('rentals::rentals.floors') }}</small>
                                        </div>
                                        <div class="col-6">
                                            <div class="h5 mb-0">{{ $building->units->count() }}</div>
                                            <small class="text-muted">{{ __('rentals::rentals.units') }}</small>
                                        </div>
                                    </div>
                                    <div class="row g-2 text-center">
                                        @foreach (\Modules\Rentals\Enums\UnitStatus::cases() as $status)
                                            <div class="col-4">
                                                <div class="p-2 bg-light rounded">
                                                    <div class="fw-bold">{{ $building->units->where('status', $status)->count() }}</div>
                                                    <small class="text-muted">{{ $status->label() }}</small>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                                @can('view Unit')
                                    <div class="card-footer">
                                        <a href="{{ route('rentals.buildings.show', $building->id) }}" class="btn btn-outline-primary btn-sm w-100">
                                            <i class="fas fa-eye me-1"></i>{{ __('rentals::rentals.view_details') }}
                                        </a>
                                    </div>
                                @endcan
                            </div>
                        </div>
                    @empty
                        <div class="col-12">
                            <div class="alert alert-info text-center">
                                <i class="fas fa-info-circle fa-2x mb-2"></i>
                                <p>{{ __('rentals::rentals.no_buildings') }}</p>
                                @can('create Buildings')
                                    <a href="{{ route('rentals.buildings.create') }}" class="btn btn-primary btn-sm">
                                        <i class="fas fa-plus me-1"></i>{{ __('rentals::rentals.add') }}
                                    </a>
                                @endcan
                            </div>
                        </div>
                    @endforelse
                </div>
            </div>

            <div class="tab-pane fade" id="itemsPane">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h5 class="mb-0"><i class="fas fa-tshirt text-primary me-2"></i>{{ __('rentals::rentals.rental_items') }}</h5>
                    @can('create Unit')
                        <a href="{{ route('rentals-units.create') }}" class="btn btn-success btn-sm">
                            <i class="fas fa-plus me-1"></i>{{ __('rentals::rentals.add') }}
                        </a>
                    @endcan
                </div>

                <div class="row g-3">
                    @forelse ($itemUnits as $unit)
                    <div class="col-lg-3 col-md-4 col-sm-6">
                        <div class="card h-100 shadow-sm">
                            <div class="card-header bg-white d-flex justify-content-between align-items-center">
                                <span class="badge bg-{{ $unit->status == \Modules\Rentals\Enums\UnitStatus::AVAILABLE ? 'success' : 'danger' }}">
                                    {{ $unit->status->label() }}
                                </span>
                                <div class="dropdown">
                                    <button class="btn btn-sm btn-link p-0" data-bs-toggle="dropdown">
                                        <i class="fas fa-ellipsis-v"></i>
                                    </button>
                                    <ul class="dropdown-menu dropdown-menu-end">
                                        @can('edit Unit')
                                            <li><a class="dropdown-item" href="{{ route('rentals.units.edit', $unit->id) }}"><i class="fas fa-edit me-2"></i>{{ __('rentals::rentals.edit') }}</a></li>
                                        @endcan
                                        @can('delete Unit')
                                            <li>
                                                <form action="{{ route('rentals.units.destroy', $unit->id) }}" method="POST" onsubmit="return confirm('{{ __('rentals::rentals.confirm_delete') }}')">
                                                    @csrf @method('DELETE')
                                                    <button type="submit" class="dropdown-item text-danger"><i class="fas fa-trash me-2"></i>{{ __('rentals::rentals.delete') }}</button>
                                                </form>
                                            </li>
                                        @endcan
                                    </ul>
                                </div>
                            </div>
                            <div class="card-body text-center">
                                <div class="mb-3">
                                    <i class="fas fa-tshirt fa-3x text-primary"></i>
                                </div>
                                <h6 class="fw-bold mb-1">{{ $unit->name }}</h6>
                                <p class="text-muted small mb-2">
                                    <i class="fas fa-barcode me-1"></i>{{ $unit->item->code ?? __('rentals::rentals.no_code') }}
                                </p>
                                @if($unit->details)
                                    <p class="text-muted small text-truncate" title="{{ $unit->details }}">{{ $unit->details }}</p>
                                @endif
                            </div>
                            <div class="card-footer">
                                @if($unit->status == \Modules\Rentals\Enums\UnitStatus::AVAILABLE)
                                    <a href="{{ route('rentals.leases.create', ['unit_id' => $unit->id]) }}" class="btn btn-primary btn-sm w-100">
                                        <i class="fas fa-file-contract me-1"></i>{{ __('rentals::rentals.create_lease') }}
                                    </a>
                                @else
                                    <span class="text-muted small">{{ __('rentals::rentals.currently_rented') }}</span>
                                @endif
                            </div>
                        </div>
                    </div>
                    @empty
                        <div class="col-12">
                            <div class="alert alert-info text-center">
                                <i class="fas fa-tshirt fa-2x mb-2"></i>
                                <p>{{ __('rentals::rentals.no_items') }}</p>
                                @can('create Unit')
                                    <a href="{{ route('rentals-units.create') }}" class="btn btn-success btn-sm">
                                        <i class="fas fa-plus me-1"></i>{{ __('rentals::rentals.add') }}
                                    </a>
                                @endcan
                            </div>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
@endsection

@section('styles')
    <link href="{{ asset('assets/css/custom-css/rentals.css') }}" rel="stylesheet" />
@endsection


