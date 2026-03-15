@can('view Resources Dashboard')
    <li class="nav-item">
        <a class="nav-link d-flex align-items-center gap-2 font-hold fw-bold transition-base" href="{{ route('myresources.dashboard') }}">
            <i class="las la-tachometer-alt"></i>{{ __('sidebar.resources_dashboard') }}
        </a>
    </li>
@endcan

@can('view Resources')
    <li class="nav-item">
        <a class="nav-link d-flex align-items-center gap-2 font-hold fw-bold transition-base" href="{{ route('myresources.index') }}">
            <i class="las la-toolbox"></i>{{ __('sidebar.resources_management') }}
        </a>
    </li>
@endcan

@can('view Resource Assignments')
    <li class="nav-item">
        <a class="nav-link d-flex align-items-center gap-2 font-hold fw-bold transition-base" href="{{ route('myresources.assignments.index') }}">
            <i class="las la-user-check"></i>{{ __('sidebar.resource_assignments') }}
        </a>
    </li>
@endcan

@can('view Resource Categories')
    <li class="nav-item">
        <a class="nav-link d-flex align-items-center gap-2 font-hold fw-bold transition-base" href="{{ route('myresources.categories.index') }}">
            <i class="las la-tags"></i>{{ __('sidebar.resource_categories') }}
        </a>
    </li>
@endcan

@can('view Resource Types')
    <li class="nav-item">
        <a class="nav-link d-flex align-items-center gap-2 font-hold fw-bold transition-base" href="{{ route('myresources.types.index') }}">
            <i class="las la-list-alt"></i>{{ __('sidebar.resource_types') }}
        </a>
    </li>
@endcan

@can('view Resource Statuses')
    <li class="nav-item">
        <a class="nav-link d-flex align-items-center gap-2 font-hold fw-bold transition-base" href="{{ route('myresources.statuses.index') }}">
            <i class="las la-flag"></i>{{ __('sidebar.resource_statuses') }}
        </a>
    </li>
@endcan
