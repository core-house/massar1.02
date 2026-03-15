{{-- Manufacturing Module Sidebar --}}

{{-- Main Operations --}}
@can('view Manufacturing Invoices')
    <li class="nav-item">
        <a class="nav-link d-flex align-items-center gap-2 font-hold fw-bold transition-base" href="{{ route('manufacturing.index') }}">
            <i class="las la-industry"></i>{{ trans_str('manufacturing invoices') }}
        </a>
    </li>
@endcan

@can('create Manufacturing Invoices')
    <li class="nav-item">
        <a class="nav-link d-flex align-items-center gap-2 font-hold fw-bold transition-base" href="{{ route('manufacturing.create') }}">
            <i class="las la-plus-circle"></i>{{ trans_str('create manufacturing invoice') }}
        </a>
    </li>
@endcan

{{-- Manufacturing Orders --}}
@can('view Manufacturing Orders')
    <li class="nav-item">
        <a class="nav-link d-flex align-items-center gap-2 font-hold fw-bold transition-base" href="{{ route('manufacturing.orders.create') }}">
            <i class="las la-clipboard-list"></i>{{ trans_str('manufacturing orders') }}
        </a>
    </li>
@endcan

{{-- Templates & Stages --}}
@can('view Manufacturing Invoices')
    <li class="nav-item">
        <a class="nav-link d-flex align-items-center gap-2 font-hold fw-bold transition-base" href="{{ route('manufacturing.templates.index') }}">
            <i class="las la-file-alt"></i>{{ trans_str('manufacturing invoice templates') }}
        </a>
    </li>
@endcan

@can('view Manufacturing Stages')
    <li class="nav-item">
        <a class="nav-link d-flex align-items-center gap-2 font-hold fw-bold transition-base" href="{{ route('manufacturing.stages.index') }}">
            <i class="las la-layer-group"></i>{{ trans_str('manufacturing stages') }}
        </a>
    </li>
@endcan

{{-- Reports & Statistics --}}
@can('view Manufacturing Invoices')
    <li class="nav-item">
        <a class="nav-link d-flex align-items-center gap-2 font-hold fw-bold transition-base" href="{{ route('manufacturing.stage-invoices-report') }}">
            <i class="las la-file-invoice"></i>{{ trans_str('stage invoices report') }}
        </a>
    </li>
@endcan

@can('view Manufacturing Invoices')
    <li class="nav-item">
        <a class="nav-link d-flex align-items-center gap-2 font-hold fw-bold transition-base" href="{{ route('manufacturing.statistics') }}">
            <i class="las la-chart-pie"></i>{{ trans_str('manufacturing statistics') }}
        </a>
    </li>
@endcan
