{{-- Manufacturing Module Sidebar --}}

{{-- Main Operations --}}
@can('view Manufacturing Invoices')
    <li class="nav-item">
        <a class="nav-link d-flex align-items-center gap-2 font-hold fw-bold transition-base" href="{{ route('manufacturing.index') }}">
            <i class="las la-industry"></i>{{ __('manufacturing::manufacturing.manufacturing invoices') }}
        </a>
    </li>
@endcan

@can('create Manufacturing Invoices')
    <li class="nav-item">
        <a class="nav-link d-flex align-items-center gap-2 font-hold fw-bold transition-base" href="{{ route('manufacturing.create') }}">
            <i class="las la-plus-circle"></i>{{ __('manufacturing::manufacturing.create manufacturing invoice') }}
        </a>
    </li>
@endcan

{{-- Manufacturing Orders --}}
@can('view Manufacturing Orders')
    <li class="nav-item">
        <a class="nav-link d-flex align-items-center gap-2 font-hold fw-bold transition-base" href="{{ route('manufacturing.orders.create') }}">
            <i class="las la-clipboard-list"></i>{{ __('manufacturing::manufacturing.manufacturing orders') }}
        </a>
    </li>
@endcan

{{-- Templates & Stages --}}
@can('view Manufacturing Invoices')
    <li class="nav-item">
        <a class="nav-link d-flex align-items-center gap-2 font-hold fw-bold transition-base" href="{{ route('manufacturing.templates.index') }}">
            <i class="las la-file-alt"></i>{{ __('manufacturing::manufacturing.manufacturing invoice templates') }}
        </a>
    </li>
@endcan

@can('view Manufacturing Stages')
    <li class="nav-item">
        <a class="nav-link d-flex align-items-center gap-2 font-hold fw-bold transition-base" href="{{ route('manufacturing.stages.index') }}">
            <i class="las la-layer-group"></i>{{ __('manufacturing::manufacturing.manufacturing stages') }}
        </a>
    </li>
@endcan

{{-- Reports & Statistics --}}
@can('view Manufacturing Invoices')
    <li class="nav-item">
        <a class="nav-link d-flex align-items-center gap-2 font-hold fw-bold transition-base" href="{{ route('manufacturing.stage-invoices-report') }}">
            <i class="las la-file-invoice"></i>{{ __('manufacturing::manufacturing.stage_invoices_report') }}
        </a>
    </li>
@endcan

@can('view Manufacturing Invoices')
    <li class="nav-item">
        <a class="nav-link d-flex align-items-center gap-2 font-hold fw-bold transition-base" href="{{ route('manufacturing.statistics') }}">
            <i class="las la-chart-pie"></i>{{ __('manufacturing::manufacturing.manufacturing statistics') }}
        </a>
    </li>
@endcan
