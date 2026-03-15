@can('view service-agreement')
    <li class="nav-item">
        <a class="nav-link d-flex align-items-center gap-2 font-hold fw-bold transition-base" href="{{ route('multi-vouchers.create', ['type' => 'contract']) }}">
            <i class="las la-file-contract"></i>{{ __('navigation.service_agreement') }}
        </a>
    </li>
@endcan

@can('view accured-expenses')
    <li class="nav-item">
        <a class="nav-link d-flex align-items-center gap-2 font-hold fw-bold transition-base" href="{{ route('multi-vouchers.create', ['type' => 'accured_expense']) }}">
            <i class="las la-money-bill-wave"></i>{{ __('navigation.accured_expenses') }}
        </a>
    </li>
@endcan

@can('view accured-revenues')
    <li class="nav-item">
        <a class="nav-link d-flex align-items-center gap-2 font-hold fw-bold transition-base" href="{{ route('multi-vouchers.create', ['type' => 'accured_income']) }}">
            <i class="las la-hand-holding-usd"></i>{{ __('navigation.accured_revenues') }}
        </a>
    </li>
@endcan

@can('view bank-commission')
    <li class="nav-item">
        <a class="nav-link d-flex align-items-center gap-2 font-hold fw-bold transition-base" href="{{ route('multi-vouchers.create', ['type' => 'bank_commission']) }}">
            <i class="las la-percentage"></i>{{ __('navigation.bank_commission_calculation') }}
        </a>
    </li>
@endcan

@can('view sales-contract')
    <li class="nav-item">
        <a class="nav-link d-flex align-items-center gap-2 font-hold fw-bold transition-base" href="{{ route('multi-vouchers.create', ['type' => 'sales_contract']) }}">
            <i class="las la-file-signature"></i>{{ __('navigation.sales_contract') }}
        </a>
    </li>
@endcan

@can('view partner-profit-sharing')
    <li class="nav-item">
        <a class="nav-link d-flex align-items-center gap-2 font-hold fw-bold transition-base" href="{{ route('multi-vouchers.create', ['type' => 'partner_profit_sharing']) }}">
            <i class="las la-handshake"></i>{{ __('navigation.partner_profit_sharing') }}
        </a>
    </li>
@endcan
