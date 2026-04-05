@can('view Check Portfolios Incoming')
    <li class="nav-item">
        <a class="nav-link font-hold fw-bold" href="{{ route('checks.incoming') }}">
            <i class="fas fa-arrow-circle-down" style="color:#28a745"></i> {{ __('checks::checks.incoming_checks') }}
        </a>
    </li>
@endcan

@can('create Check Portfolios Incoming')
    <li class="nav-item">
        <a class="nav-link font-hold fw-bold" href="{{ route('checks.incoming.create') }}">
            <i class="fas fa-plus-circle" style="color:#28a745"></i> {{ __('checks::checks.add_incoming_check') }}
        </a>
    </li>
@endcan

<li class="nav-item">
    <hr class="my-2">
</li>

@can('view Check Portfolios Outgoing')
    <li class="nav-item">
        <a class="nav-link font-hold fw-bold" href="{{ route('checks.outgoing') }}">
            <i class="fas fa-arrow-circle-up" style="color:#dc3545"></i> {{ __('checks::checks.outgoing_checks') }}
        </a>
    </li>
@endcan

@can('create Check Portfolios Outgoing')
    <li class="nav-item">
        <a class="nav-link font-hold fw-bold" href="{{ route('checks.outgoing.create') }}">
            <i class="fas fa-plus-circle" style="color:#dc3545"></i> {{ __('checks::checks.add_outgoing_check') }}
        </a>
    </li>
@endcan

<li class="nav-item">
    <hr class="my-2">
</li>

@can('view Checks')
    <li class="nav-item">
        <a class="nav-link font-hold fw-bold" href="{{ route('checks.dashboard') }}">
            <i class="fas fa-chart-line" style="color:#667eea"></i> {{ __('checks::checks.checks_statistics') }}
        </a>
    </li>
@endcan
