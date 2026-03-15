@can('view Rentals Statistics')
    <li class="nav-item">
        <a class="nav-link d-flex align-items-center gap-2 font-hold fw-bold transition-base" href="{{ route('rentals.statistics') }}">
            <i class="las la-chart-pie"></i>{{ __('sidebar.rentals_statistics') }}
        </a>
    </li>
    <li class="nav-item">
        <a class="nav-link d-flex align-items-center gap-2 font-hold fw-bold transition-base" href="{{ route('rentals.reports') }}">
            <i class="las la-file-alt"></i>{{ __('sidebar.reports') }}
        </a>
    </li>
@endcan

@can('view Buildings')
    <li class="nav-item">
        <a class="nav-link d-flex align-items-center gap-2 font-hold fw-bold transition-base" href="{{ route('rentals.buildings.index') }}">
            <i class="las la-building"></i>{{ __('navigation.rent_building') }}
        </a>
    </li>
@endcan

@can('view Leases')
    <li class="nav-item">
        <a class="nav-link d-flex align-items-center gap-2 font-hold fw-bold transition-base" href="{{ route('rentals.leases.index') }}">
            <i class="las la-file-contract"></i>{{ __('navigation.rent_contracts') }}
        </a>
    </li>
@endcan
