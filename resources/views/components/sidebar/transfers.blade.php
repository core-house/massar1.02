@can('view transfers')
    <li class="nav-item">
        <a class="nav-link d-flex align-items-center gap-2 font-hold fw-bold transition-base" href="{{ route('transfers.index') }}">
            <i class="las la-exchange-alt"></i>{{ __('navigation.cash_transfers') }}
        </a>
    </li>
@endcan
@can('view transfer-statistics')
    <li class="nav-item">
        <a class="nav-link d-flex align-items-center gap-2 font-hold fw-bold transition-base" href="{{ route('transfers.statistics') }}">
            <i class="las la-chart-pie"></i>{{ __('transfers statistics') }}
        </a>
    </li>
@endcan

@canany(['view cash-to-cash', 'create cash-to-cash'])
    <li class="nav-item">
        <a class="nav-link d-flex align-items-center gap-2 font-hold fw-bold transition-base" href="{{ route('transfers.create', ['type' => 'cash_to_cash']) }}">
            <i class="las la-money-bill-wave"></i>{{ __('navigation.cash_to_cash_transfer') }}
        </a>
    </li>
@endcanany

@canany(['view cash-to-bank', 'create cash-to-bank'])
    <li class="nav-item">
        <a class="nav-link d-flex align-items-center gap-2 font-hold fw-bold transition-base" href="{{ route('transfers.create', ['type' => 'cash_to_bank']) }}">
            <i class="las la-university"></i>{{ __('navigation.cash_to_bank_transfer') }}
        </a>
    </li>
@endcanany

@canany(['view bank-to-cash', 'create bank-to-cash'])
    <li class="nav-item">
        <a class="nav-link d-flex align-items-center gap-2 font-hold fw-bold transition-base" href="{{ route('transfers.create', ['type' => 'bank_to_cash']) }}">
            <i class="las la-hand-holding-usd"></i>{{ __('navigation.bank_to_cash_transfer') }}
        </a>
    </li>
@endcanany
@canany(['view bank-to-bank', 'create bank-to-bank'])
    <li class="nav-item">
        <a class="nav-link d-flex align-items-center gap-2 font-hold fw-bold transition-base" href="{{ route('transfers.create', ['type' => 'bank_to_bank']) }}">
            <i class="las la-building"></i>{{ __('navigation.bank_to_bank_transfer') }}
        </a>
    </li>
@endcanany
