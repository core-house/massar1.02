<li class="sidebar-main-title">
    <div>
        <h6 class="lan-1">{{ trans_str('fleet management') }}</h6>
    </div>
</li>

@can('view Fleet Dashboard')
    <li class="nav-item">
        <a class="nav-link d-flex align-items-center gap-2 font-hold fw-bold transition-base" href="{{ route('fleet.dashboard.index') }}">
            <i class="las la-tachometer-alt"></i>{{ trans_str('fleet dashboard') }}
        </a>
    </li>
@endcan

@can('view Vehicle Types')
    <li class="nav-item">
        <a class="nav-link d-flex align-items-center gap-2 font-hold fw-bold transition-base" href="{{ route('fleet.vehicle-types.index') }}">
            <i class="las la-car-side"></i>{{ trans_str('vehicle types') }}
        </a>
    </li>
@endcan

@can('view Vehicles')
    <li class="nav-item">
        <a class="nav-link d-flex align-items-center gap-2 font-hold fw-bold transition-base" href="{{ route('fleet.vehicles.index') }}">
            <i class="las la-truck"></i>{{ trans_str('vehicles') }}
        </a>
    </li>
@endcan

@can('view Trips')
    <li class="nav-item">
        <a class="nav-link d-flex align-items-center gap-2 font-hold fw-bold transition-base" href="{{ route('fleet.trips.index') }}">
            <i class="las la-route"></i>{{ trans_str('trips') }}
        </a>
    </li>
@endcan

@can('view Fuel Records')
    <li class="nav-item">
        <a class="nav-link d-flex align-items-center gap-2 font-hold fw-bold transition-base" href="{{ route('fleet.fuel-records.index') }}">
            <i class="las la-gas-pump"></i>{{ trans_str('fuel records') }}
        </a>
    </li>
@endcan
