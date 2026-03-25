@can('view MyResources')
    <li class="nav-item">
        <a class="nav-link d-flex align-items-center gap-2 font-hold fw-bold transition-base" href="{{ route('myresources.index') }}">
            <i class="las la-toolbox"></i>{{ trans_str('resources management') }}
        </a>
    </li>
@endcan

@can('view MyResources')
    <li class="nav-item">
        <a class="nav-link d-flex align-items-center gap-2 font-hold fw-bold transition-base" href="{{ route('myresources.dashboard') }}">
            <i class="las la-tachometer-alt"></i>{{ trans_str('resources dashboard') }}
        </a>
    </li>
@endcan

@can('view Resource Assignments')
    <li class="nav-item">
        <a class="nav-link d-flex align-items-center gap-2 font-hold fw-bold transition-base" href="{{ route('myresources.assignments.index') }}">
            <i class="las la-user-check"></i>{{ trans_str('resource assignments') }}
        </a>
    </li>
@endcan

@can('view Resource Categories')
    <li class="nav-item">
        <a class="nav-link d-flex align-items-center gap-2 font-hold fw-bold transition-base" href="{{ route('myresources.categories.index') }}">
            <i class="las la-tags"></i>{{ trans_str('resource categories') }}
        </a>
    </li>
@endcan

@can('view Resource Types')
    <li class="nav-item">
        <a class="nav-link d-flex align-items-center gap-2 font-hold fw-bold transition-base" href="{{ route('myresources.types.index') }}">
            <i class="las la-list-alt"></i>{{ trans_str('resource types') }}
        </a>
    </li>
@endcan

@can('view Resource Statuses')
    <li class="nav-item">
        <a class="nav-link d-flex align-items-center gap-2 font-hold fw-bold transition-base" href="{{ route('myresources.statuses.index') }}">
            <i class="las la-flag"></i>{{ trans_str('resource statuses') }}
        </a>
    </li>
@endcan
