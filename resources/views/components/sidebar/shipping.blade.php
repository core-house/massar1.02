@can('view Shipping Statistics')
    <li class="nav-item">
        <a class="nav-link d-flex align-items-center gap-2 font-hold fw-bold transition-base" href="{{ route('shipping.dashboard.statistics') }}">
            <i class="las la-chart-bar"></i>{{ __('shipping statistics') }}
        </a>
    </li>
@endcan

@can('view Shipping Companies')
    <li class="nav-item">
        <a class="nav-link d-flex align-items-center gap-2 font-hold fw-bold transition-base" href="{{ route('companies.index') }}">
            <i class="las la-building"></i>{{ __('shipping companies') }}
        </a>
    </li>
@endcan

@can('view Drivers')
    <li class="nav-item">
        <a class="nav-link d-flex align-items-center gap-2 font-hold fw-bold transition-base" href="{{ route('drivers.index') }}">
            <i class="las la-id-card"></i>{{ __('drivers') }}
        </a>
    </li>
@endcan

@can('view Orders')
    <li class="nav-item">
        <a class="nav-link d-flex align-items-center gap-2 font-hold fw-bold transition-base" href="{{ route('orders.index') }}">
            <i class="las la-clipboard-list"></i>{{ __('orders') }}
        </a>
    </li>
@endcan

@can('view Shipments')
    <li class="nav-item">
        <a class="nav-link d-flex align-items-center gap-2 font-hold fw-bold transition-base" href="{{ route('shipments.index') }}">
            <i class="las la-shipping-fast"></i>{{ __('shipments') }}
        </a>
    </li>
@endcan
