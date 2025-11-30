<li class="sidebar-main-title">
    <div>
        <h6 class="lan-1">{{ __('Maintenance') }}</h6>
    </div>
</li>

@can('view Maintenance Statistics')
    <li class="nav-item">
        <a class="nav-link" href="{{ route('maintenance.dashboard.index') }}">
            <i class="ti-control-record"></i>{{ __('Maintenance Statistics') }}
        </a>
    </li>
@endcan

@can('view Service Types')
    <li class="nav-item">
        <a class="nav-link" href="{{ route('service.types.index') }}">
            <i class="ti-control-record"></i>{{ __('Service Types') }}
        </a>
    </li>
@endcan

@can('view Maintenances')
    <li class="nav-item">
        <a class="nav-link" href="{{ route('maintenances.index') }}">
            <i class="ti-control-record"></i>{{ __('Maintenances') }}
        </a>
    </li>
@endcan

@can('view Periodic Maintenance')
    <li class="nav-item">
        <a class="nav-link" href="{{ route('periodic.maintenances.index') }}">
            <i class="ti-control-record"></i>{{ __('Periodic Maintenance') }}
        </a>
    </li>
@endcan
