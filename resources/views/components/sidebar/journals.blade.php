@can('view journals-statistics')
    <li class="nav-item">
        <a class="nav-link d-flex align-items-center gap-2 font-hold fw-bold transition-base" href="{{ route('journal.statistics') }}">
            <i class="las la-chart-bar"></i>{{ trans_str('journal statistics') }}
        </a>
    </li>
@endcan

@can('create journals')
    <li class="nav-item">
        <a class="nav-link d-flex align-items-center gap-2 font-hold fw-bold transition-base" href="{{ route('journals.create', ['type' => 'basic_journal']) }}">
            <i class="las la-book"></i>{{ __('navigation.daily_journal') }}
        </a>
    </li>
@endcan

@can('create multi-journals')
    <li class="nav-item">
        <a class="nav-link d-flex align-items-center gap-2 font-hold fw-bold transition-base" href="{{ route('multi-journals.create') }}">
            <i class="las la-books"></i>{{ __('navigation.multi_journal') }}
        </a>
    </li>
@endcan

@can('view journals')
    <li class="nav-item">
        <a class="nav-link d-flex align-items-center gap-2 font-hold fw-bold transition-base" href="{{ route('journals.index') }}">
            <i class="las la-list-alt"></i>{{ __('navigation.daily_ledgers_operations') }}
        </a>
    </li>
@endcan

@can('view multi-journals')
    <li class="nav-item">
        <a class="nav-link d-flex align-items-center gap-2 font-hold fw-bold transition-base" href="{{ route('multi-journals.index') }}">
            <i class="las la-clipboard-list"></i>{{ __('navigation.multi_daily_ledgers_operations') }}
        </a>
    </li>
@endcan

@can('create inventory-balance')
    <li class="nav-item">
        <a class="nav-link d-flex align-items-center gap-2 font-hold fw-bold transition-base" href="{{ route('inventory-balance.create') }}">
            <i class="las la-balance-scale"></i>{{ __('navigation.opening_inventory_balance') }}
        </a>
    </li>
@endcan

@can('view opening-balance-accounts')
    <li class="nav-item">
        <a class="nav-link d-flex align-items-center gap-2 font-hold fw-bold transition-base" href="{{ route('accounts.startBalance') }}">
            <i class="las la-calculator"></i>{{ __('navigation.opening_balance_accounts') }}
        </a>
    </li>
@endcan

@can('view balance-sheet')
    <li class="nav-item">
        <a class="nav-link d-flex align-items-center gap-2 font-hold fw-bold transition-base" href="{{ route('accounts.balanceSheet') }}">
            <i class="las la-file-invoice-dollar"></i>{{ __('navigation.balance_sheet') }}
        </a>
    </li>
@endcan
