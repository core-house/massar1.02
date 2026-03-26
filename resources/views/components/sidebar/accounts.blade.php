@php
    // Permission mapping for authorization checks
    $permissionMap = [
        'clients' => 'Clients',
        'suppliers' => 'Suppliers',
        'funds' => 'Funds',
        'banks' => 'Banks',
        'employees' => 'Employees',
        'warhouses' => 'warhouses',
        'expenses' => 'Expenses',
        'revenues' => 'Revenues',
        'creditors' => 'various_creditors',
        'debtors' => 'various_debtors',
        'partners' => 'partners',
        'current-partners' => 'current_partners',
        'assets' => 'assets',
        'rentables' => 'rentables',
        'check-portfolios-incoming' => 'check-portfolios-incoming',
        'check-portfolios-outgoing' => 'check-portfolios-outgoing',
    ];

    $accountTypes = [
        'clients' => ['label' => 'accounts::sidebar.clients', 'icon' => 'las la-user-tag'],
        'suppliers' => ['label' => 'accounts::sidebar.suppliers', 'icon' => 'las la-truck-loading'],
        'funds' => ['label' => 'accounts::sidebar.funds', 'icon' => 'las la-wallet'],
        'banks' => ['label' => 'accounts::sidebar.banks', 'icon' => 'las la-university'],
        'employees' => ['label' => 'accounts::sidebar.employees', 'icon' => 'las la-user-tie'],
        'warhouses' => ['label' => 'accounts::sidebar.warehouses', 'icon' => 'las la-warehouse'],
        'expenses' => ['label' => 'accounts::sidebar.expenses', 'icon' => 'las la-file-invoice-dollar'],
        'revenues' => ['label' => 'accounts::sidebar.revenues', 'icon' => 'las la-hand-holding-usd'],
        'creditors' => ['label' => 'accounts::sidebar.other_creditors', 'icon' => 'las la-user-minus'],
        'debtors' => ['label' => 'accounts::sidebar.other_debtors', 'icon' => 'las la-user-plus'],
        'partners' => ['label' => 'accounts::sidebar.partners', 'icon' => 'las la-users'],
        'current-partners' => ['label' => 'accounts::sidebar.partner_current_account', 'icon' => 'las la-user-friends'],
        'assets' => ['label' => 'accounts::sidebar.assets', 'icon' => 'las la-building'],
        'rentables' => ['label' => 'accounts::sidebar.rentable_properties', 'icon' => 'las la-key'],
        'check-portfolios-incoming' => ['label' => 'accounts::sidebar.incoming_check_portfolios', 'icon' => 'las la-file-alt'],
        'check-portfolios-outgoing' => ['label' => 'accounts::sidebar.outgoing_check_portfolios', 'icon' => 'las la-file-signature'],
    ];

    $currentType = request('type');
@endphp
@can('view basicData-statistics')
    <li class="nav-item">
        <a class="nav-link d-flex align-items-center gap-2 font-hold fw-bold transition-base {{ request()->routeIs('accounts.basic-data-statistics') ? 'active' : '' }}" 
           href="{{ route('accounts.basic-data-statistics') }}"
           style="{{ request()->routeIs('accounts.basic-data-statistics') ? 'background-color: rgba(52, 211, 163, 0.1); color: #34d3a3;' : '' }}">
            <i class="las la-chart-bar font-18"></i>{{ __('accounts::sidebar.basic_data_statistics') }}
        </a>
    </li>
@endcan

@foreach ($accountTypes as $type => $data)
    @can('view ' . (isset($permissionMap[$type]) ? $permissionMap[$type] : $type))
        <li class="nav-item">
            <a class="nav-link d-flex align-items-center gap-2 font-hold fw-bold transition-base {{ $currentType == $type ? 'active' : '' }}" 
               href="{{ route('accounts.index', ['type' => $type]) }}"
               style="{{ $currentType == $type ? 'background-color: rgba(52, 211, 163, 0.1); color: #34d3a3;' : '' }}">
                <i class="{{ $data['icon'] }} font-18"></i>{{ __($data['label']) }}
            </a>
        </li>
    @endcan
@endforeach
