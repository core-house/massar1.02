<li class="sidebar-main-title">
    <div>
        <h6 class="lan-1">{{ trans_str('maintenance') }}</h6>
    </div>
</li>

@can('view Maintenance Statistics')
    <li class="nav-item">
        <a class="nav-link d-flex align-items-center gap-2 font-hold fw-bold transition-base" href="{{ route('maintenance.dashboard.index') }}">
            <i class="las la-chart-pie"></i>{{ trans_str('maintenance statistics') }}
        </a>
    </li>
@endcan

@can('view Service Types')
    <li class="nav-item">
        <a class="nav-link d-flex align-items-center gap-2 font-hold fw-bold transition-base" href="{{ route('service.types.index') }}">
            <i class="las la-list-alt"></i>{{ trans_str('service types') }}
        </a>
    </li>
@endcan

@can('view Maintenances')
    <li class="nav-item">
        <a class="nav-link d-flex align-items-center gap-2 font-hold fw-bold transition-base" href="{{ route('maintenances.index') }}">
            <i class="las la-wrench"></i>{{ trans_str('maintenances') }}
        </a>
    </li>
@endcan

@can('view Periodic Maintenance')
    <li class="nav-item">
        <a class="nav-link d-flex align-items-center gap-2 font-hold fw-bold transition-base" href="{{ route('periodic.maintenances.index') }}">
            <i class="las la-calendar-check"></i>{{ trans_str('periodic maintenance') }}
        </a>
    </li>
@endcan
