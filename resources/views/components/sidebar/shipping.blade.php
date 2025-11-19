@can('view Shipping Statistics')
    <li class="nav-item">
        <a class="nav-link" href="{{ route('shipping.dashboard.statistics') }}">
            <i class="ti-bar-chart"></i>{{ __('Shipping Statistics') }}
        </a>
    </li>
@endcan

@can('view Shipping Companies')
    <li class="nav-item">
        <a class="nav-link" href="{{ route('companies.index') }}">
            <i class="ti-control-record"></i>{{ __('Shipping Companies') }}
        </a>
    </li>
@endcan

@can('view Drivers')
    <li class="nav-item">
        <a class="nav-link" href="{{ route('drivers.index') }}">
            <i class="ti-control-record"></i>{{ __('Drivers') }}
        </a>
    </li>
@endcan

@can('view Orders')
    <li class="nav-item">
        <a class="nav-link" href="{{ route('orders.index') }}">
            <i class="ti-control-record"></i>{{ __('Orders') }}
        </a>
    </li>
@endcan

@can('view Shipments')
    <li class="nav-item">
        <a class="nav-link" href="{{ route('shipments.index') }}">
            <i class="ti-control-record"></i>{{ __('Shipments') }}
        </a>
    </li>
@endcan
