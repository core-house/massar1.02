@can('view Tasks')
    <li class="nav-item">
        <a class="nav-link d-flex align-items-center gap-2 font-hold fw-bold transition-base"
            href="{{ route('tasks.statistics') }}">
            <i class="las la-chart-bar"></i>{{ __('crm::crm.tasks_statistics') }}
        </a>
    </li>
@endcan

@can('view Tasks')
    <li class="nav-item">
        <a class="nav-link d-flex align-items-center gap-2 font-hold fw-bold transition-base"
            href="{{ route('tasks.index') }}">
            <i class="las la-tasks"></i>{{ __('crm::crm.tasks_and_activities') }}
        </a>
    </li>
@endcan

@can('view Task Types')
    <li class="nav-item">
        <a class="nav-link d-flex align-items-center gap-2 font-hold fw-bold transition-base"
            href="{{ route('tasks.types.index') }}">
            <i class="las la-list-alt"></i>{{ __('crm::crm.tasks_and_activities_types') }}
        </a>
    </li>
@endcan
