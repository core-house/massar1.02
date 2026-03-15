@canany(['view Daily Activity Analyzer', 'view General Journal'])

    <li class="nav-item has-submenu">
        <a class="nav-link" href="javascript: void(0);">
            <i class="fas fa-chart-pie"></i>
            {{ __('general reports') }}
            <span class="menu-arrow"><i class="mdi mdi-chevron-right"></i></span>
        </a>
        <ul class="sub-menu mm-collapse">

            @can('view Daily Activity Analyzer')
                <li class="nav-item">
                    <a class="nav-link d-flex align-items-center gap-2 font-hold fw-bold transition-base"
                        href="{{ route('reports.overall') }}">
                        <i class="las la-chart-line"></i>{{ __('daily work analyzer') }}
                    </a>
                </li>


                <li class="nav-item">
                    <a class="nav-link d-flex align-items-center gap-2 font-hold fw-bold transition-base"
                        href="{{ route('reports.daily-activity-analyzer') }}">
                        <i class="las la-tasks"></i>{{ __('daily activity analyzer') }}
                    </a>
                </li>
            @endcan

            @can('view General Journal')
                <li class="nav-item">
                    <a class="nav-link d-flex align-items-center gap-2 font-hold fw-bold transition-base"
                        href="{{ route('reports.journal-summery') }}">
                        <i class="las la-book-open"></i>{{ __('general journal') }}
                    </a>
                </li>
            @endcan
        </ul>
    </li>
@endcan

@canany([
    'view Accounts Tree',
    'view Balance Sheet',
    'view Profit Loss Report',
    'view Income Statement Total',
    'view
    Accounts Balance',
    'view Account Movement Report',
    ])
    <li class="nav-item has-submenu">
        <a class="nav-link" href="javascript: void(0);">
            <i class="fas fa-book"></i>
            {{ __('accounts reports') }}
            <span class="menu-arrow"><i class="mdi mdi-chevron-right"></i></span>
        </a>
        <ul class="sub-menu mm-collapse">
            @can('view Accounts Tree')
                <li class="nav-item">
                    <a class="nav-link d-flex align-items-center gap-2 font-hold fw-bold transition-base"
                        href="{{ route('reports.accounts-tree') }}">
                        <i class="las la-sitemap"></i>{{ __('accounts tree') }}
                    </a>
                </li>
            @endcan

            @can('view Balance Sheet')
                <li class="nav-item">
                    <a class="nav-link d-flex align-items-center gap-2 font-hold fw-bold transition-base"
                        href="{{ route('reports.general-balance-sheet') }}">
                        <i class="las la-balance-scale"></i>{{ __('balance sheet') }}
                    </a>
                </li>
            @endcan

            @can('view Profit Loss Report')
                <li class="nav-item">
                    <a class="nav-link d-flex align-items-center gap-2 font-hold fw-bold transition-base"
                        href="{{ route('reports.general-profit-loss-report') }}">
                        <i class="las la-chart-area"></i>{{ __('profit & loss') }}
                    </a>
                </li>
            @endcan

            @can('view Income Statement Total')
                <li class="nav-item">
                    <a class="nav-link d-flex align-items-center gap-2 font-hold fw-bold transition-base"
                        href="{{ route('reports.general-profit-loss-report-total') }}">
                        <i class="las la-file-invoice-dollar"></i>{{ __('income statement total') }}
                    </a>
                </li>
            @endcan

            @can('view Accounts Balance')
                <li class="nav-item">
                    <a class="nav-link d-flex align-items-center gap-2 font-hold fw-bold transition-base"
                        href="{{ route('reports.general-account-balances') }}">
                        <i class="las la-calculator"></i>{{ __('accounts balance') }}
                    </a>
                </li>
            @endcan

            @can('view Account Movement Report')
                <li class="nav-item">
                    <a class="nav-link d-flex align-items-center gap-2 font-hold fw-bold transition-base"
                        href="{{ route('account-movement') }}">
                        <i class="las la-exchange-alt"></i>{{ __('account movement') }}
                    </a>
                </li>
            @endcan
        </ul>
    </li>
@endcan

@can('view Items Report')
    <li class="nav-item has-submenu">
        <a class="nav-link" href="javascript: void(0);">
            <i class="fas fa-boxes"></i>
            {{ __('inventory reports') }}
            <span class="menu-arrow"><i class="mdi mdi-chevron-right"></i></span>
        </a>
        <ul class="sub-menu mm-collapse">
            <li class="nav-item">
                <a class="nav-link d-flex align-items-center gap-2 font-hold fw-bold transition-base"
                    href="{{ route('reports.general-inventory-balances') }}">
                    <i class="las la-boxes"></i>{{ __('items list with balances') }}
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link d-flex align-items-center gap-2 font-hold fw-bold transition-base"
                    href="{{ route('reports.general-inventory-balances-by-store') }}">
                    <i class="las la-warehouse"></i>{{ __('balances by warehouse') }}
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link d-flex align-items-center gap-2 font-hold fw-bold transition-base"
                    href="{{ route('reports.general-inventory-movements') }}">
                    <i class="las la-dolly"></i>{{ __('item movement') }}
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link d-flex align-items-center gap-2 font-hold fw-bold transition-base"
                    href="{{ route('reports.get-items-max-min-quantity') }}">
                    <i class="las la-sort-amount-down"></i>{{ __('min & max order limit') }}
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link d-flex align-items-center gap-2 font-hold fw-bold transition-base"
                    href="{{ route('prices.compare.report') }}">
                    <i class="las la-balance-scale-right"></i>{{ __('price comparison') }}
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link d-flex align-items-center gap-2 font-hold fw-bold transition-base"
                    href="{{ route('reports.items.inactive') }}">
                    <i class="las la-ban"></i>{{ __('inactive items') }}
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link d-flex align-items-center gap-2 font-hold fw-bold transition-base"
                    href="{{ route('reports.items.idle') }}">
                    <i class="las la-pause-circle"></i>{{ __('idle items report') }}
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link d-flex align-items-center gap-2 font-hold fw-bold transition-base"
                    href="{{ route('reports.items.most-expensive') }}">
                    <i class="las la-gem"></i>{{ __('reports.most_expensive_items_report') }}
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link d-flex align-items-center gap-2 font-hold fw-bold transition-base"
                    href="{{ route('reports.items.with-stores') }}">
                    <i class="las la-store"></i>{{ __('items per warehouse') }}
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link d-flex align-items-center gap-2 font-hold fw-bold transition-base"
                    href="{{ route('reports.inventory-discrepancy-report') }}">
                    <i class="las la-exclamation-triangle"></i>{{ __('inventory discrepancy monitoring') }}
                </a>
            </li>
        </ul>
    </li>
@endcan

@can('view Sales Report')
    <li class="nav-item has-submenu">
        <a class="nav-link" href="javascript: void(0);">
            <i class="fas fa-shopping-cart"></i>
            {{ __('sales reports') }}
            <span class="menu-arrow"><i class="mdi mdi-chevron-right"></i></span>
        </a>
        <ul class="sub-menu mm-collapse">
            <li class="nav-item">
                <a class="nav-link d-flex align-items-center gap-2 font-hold fw-bold transition-base"
                    href="{{ route('reports.general-sales-report') }}">
                    <i class="las la-file-alt"></i>{{ __('general sales report') }}
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link d-flex align-items-center gap-2 font-hold fw-bold transition-base"
                    href="{{ route('reports.sales.total') }}">
                    <i class="las la-calculator"></i>{{ __('sales totals') }}
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link d-flex align-items-center gap-2 font-hold fw-bold transition-base"
                    href="{{ route('reports.sales.items') }}">
                    <i class="las la-box"></i>{{ __('sales items') }}
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link d-flex align-items-center gap-2 font-hold fw-bold transition-base"
                    href="{{ route('reports.sales.representative') }}">
                    <i class="las la-user-tie"></i>{{ __('sales by representative') }}
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link d-flex align-items-center gap-2 font-hold fw-bold transition-base"
                    href="{{ route('reports.general-sales-report-by-address') }}">
                    <i class="las la-map-marker-alt"></i>{{ __('sales by address') }}
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link d-flex align-items-center gap-2 font-hold fw-bold transition-base"
                    href="{{ route('reports.item-sales') }}">
                    <i class="las la-shopping-bag"></i>{{ __('item sales') }}
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link d-flex align-items-center gap-2 font-hold fw-bold transition-base"
                    href="{{ route('sales.invoice-report') }}">
                    <i class="las la-file-invoice"></i>{{ __('sales invoices') }}
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link d-flex align-items-center gap-2 font-hold fw-bold transition-base"
                    href="{{ route('sales-orders-report') }}">
                    <i class="las la-clipboard-list"></i>{{ __('sales orders') }}
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link d-flex align-items-center gap-2 font-hold fw-bold transition-base"
                    href="{{ route('purchase-quotations-reports') }}">
                    <i class="las la-file-contract"></i>{{ __('customer quotations') }}
                </a>
            </li>
        </ul>
    </li>
@endcan

@can('view Purchases Report')
    <li class="nav-item has-submenu">
        <a class="nav-link" href="javascript: void(0);">
            <i class="fas fa-shopping-basket"></i>
            {{ __('purchase reports') }}
            <span class="menu-arrow"><i class="mdi mdi-chevron-right"></i></span>
        </a>
        <ul class="sub-menu mm-collapse">
            <li class="nav-item">
                <a class="nav-link d-flex align-items-center gap-2 font-hold fw-bold transition-base"
                    href="{{ route('reports.purchasing.dashboard') }}">
                    <i class="las la-tachometer-alt"></i>{{ __('purchasing dashboard') }}
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link d-flex align-items-center gap-2 font-hold fw-bold transition-base"
                    href="{{ route('reports.general-purchases-report') }}">
                    <i class="las la-file-alt"></i>{{ __('general purchases report') }}
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link d-flex align-items-center gap-2 font-hold fw-bold transition-base"
                    href="{{ route('reports.general-purchases-daily-report') }}">
                    <i class="las la-calendar-day"></i>{{ __('daily purchases') }}
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link d-flex align-items-center gap-2 font-hold fw-bold transition-base"
                    href="{{ route('reports.purchases.total') }}">
                    <i class="las la-calculator"></i>{{ __('purchases totals') }}
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link d-flex align-items-center gap-2 font-hold fw-bold transition-base"
                    href="{{ route('reports.purchases.items') }}">
                    <i class="las la-box"></i>{{ __('purchases items') }}
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link d-flex align-items-center gap-2 font-hold fw-bold transition-base"
                    href="{{ route('reports.item-purchase') }}">
                    <i class="las la-shopping-bag"></i>{{ __('item purchase') }}
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link d-flex align-items-center gap-2 font-hold fw-bold transition-base"
                    href="{{ route('billing.invoice-report') }}">
                    <i class="las la-file-invoice"></i>{{ __('purchase invoices') }}
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link d-flex align-items-center gap-2 font-hold fw-bold transition-base"
                    href="{{ route('supplier-rfqs-report') }}">
                    <i class="las la-file-contract"></i>{{ __('supplier quotations') }}
                </a>
            </li>
        </ul>
    </li>
@endcan

@can('view Customer Quotation Report')
    <li class="nav-item has-submenu">
        <a class="nav-link" href="javascript: void(0);">
            <i class="fas fa-users"></i>
            {{ __('customers reports') }}
            <span class="menu-arrow"><i class="mdi mdi-chevron-right"></i></span>
        </a>
        <ul class="sub-menu mm-collapse">
            <li class="nav-item">
                <a class="nav-link d-flex align-items-center gap-2 font-hold fw-bold transition-base"
                    href="{{ route('reports.general-customers-report') }}">
                    <i class="las la-file-alt"></i>{{ __('general customers report') }}
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link d-flex align-items-center gap-2 font-hold fw-bold transition-base"
                    href="{{ route('reports.general-customers-daily-report') }}">
                    <i class="las la-calendar-day"></i>{{ __('daily customers report') }}
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link d-flex align-items-center gap-2 font-hold fw-bold transition-base"
                    href="{{ route('reports.general-customers-items-report') }}">
                    <i class="las la-box"></i>{{ __('customers items report') }}
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link d-flex align-items-center gap-2 font-hold fw-bold transition-base"
                    href="{{ route('reports.general-customers-total-report') }}">
                    <i class="las la-calculator"></i>{{ __('customers totals report') }}
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link d-flex align-items-center gap-2 font-hold fw-bold transition-base"
                    href="{{ route('reports.customer-debt-history') }}">
                    <i class="las la-history"></i>{{ __('customer debt aging') }}
                </a>
            </li>
        </ul>
    </li>
@endcan

@can('view Supplier Quotation Report')
    <li class="nav-item has-submenu">
        <a class="nav-link" href="javascript: void(0);">
            <i class="fas fa-truck"></i>
            {{ __('suppliers reports') }}
            <span class="menu-arrow"><i class="mdi mdi-chevron-right"></i></span>
        </a>
        <ul class="sub-menu mm-collapse">
            <li class="nav-item">
                <a class="nav-link d-flex align-items-center gap-2 font-hold fw-bold transition-base"
                    href="{{ route('reports.general-suppliers-report') }}">
                    <i class="las la-file-alt"></i>{{ __('general suppliers report') }}
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link d-flex align-items-center gap-2 font-hold fw-bold transition-base"
                    href="{{ route('reports.general-suppliers-daily-report') }}">
                    <i class="las la-calendar-day"></i>{{ __('daily suppliers report') }}
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link d-flex align-items-center gap-2 font-hold fw-bold transition-base"
                    href="{{ route('reports.general-suppliers-items-report') }}">
                    <i class="las la-box"></i>{{ __('suppliers items report') }}
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link d-flex align-items-center gap-2 font-hold fw-bold transition-base"
                    href="{{ route('reports.general-suppliers-total-report') }}">
                    <i class="las la-calculator"></i>{{ __('suppliers totals report') }}
                </a>
            </li>
        </ul>
    </li>
@endcan

@can('view Expenses Report')
    <li class="nav-item has-submenu">
        <a class="nav-link" href="javascript: void(0);">
            <i class="fas fa-file-invoice-dollar"></i>
            {{ __('expenses reports') }}
            <span class="menu-arrow"><i class="mdi mdi-chevron-right"></i></span>
        </a>
        <ul class="sub-menu mm-collapse">
            <li class="nav-item">
                <a class="nav-link d-flex align-items-center gap-2 font-hold fw-bold transition-base"
                    href="{{ route('reports.general-expenses-report') }}">
                    <i class="las la-file-alt"></i>{{ __('general expenses report') }}
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link d-flex align-items-center gap-2 font-hold fw-bold transition-base"
                    href="{{ route('reports.general-expenses-daily-report') }}">
                    <i class="las la-calendar-day"></i>{{ __('expense account statement') }}
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link d-flex align-items-center gap-2 font-hold fw-bold transition-base"
                    href="{{ route('reports.expenses-balance-report') }}">
                    <i class="las la-balance-scale"></i>{{ __('expenses balance') }}
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link d-flex align-items-center gap-2 font-hold fw-bold transition-base"
                    href="{{ route('reports.general-cost-centers-report') }}">
                    <i class="las la-sitemap"></i>{{ __('cost centers report') }}
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link d-flex align-items-center gap-2 font-hold fw-bold transition-base"
                    href="{{ route('reports.general-cost-center-account-statement') }}">
                    <i class="las la-file-invoice"></i>{{ __('cost center account statement') }}
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link d-flex align-items-center gap-2 font-hold fw-bold transition-base"
                    href="{{ route('reports.general-cost-centers-list') }}">
                    <i class="las la-list"></i>{{ __('cost centers list') }}
                </a>
            </li>
        </ul>
    </li>
@endcan

@can('view General Cashbox Movement Report')
    <li class="nav-item has-submenu">
        <a class="nav-link" href="javascript: void(0);">
            <i class="fas fa-money-bill-wave"></i>
            {{ __('cash & bank') }}
            <span class="menu-arrow"><i class="mdi mdi-chevron-right"></i></span>
        </a>
        <ul class="sub-menu mm-collapse">
            <li class="nav-item">
                <a class="nav-link d-flex align-items-center gap-2 font-hold fw-bold transition-base"
                    href="{{ route('reports.general-cashbox-movement-report') }}">
                    <i class="las la-cash-register"></i>{{ __('cashbox movement') }}
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link d-flex align-items-center gap-2 font-hold fw-bold transition-base"
                    href="{{ route('reports.general-cash-bank-report') }}">
                    <i class="las la-university"></i>{{ __('bank movement') }}
                </a>
            </li>
        </ul>
    </li>
@endcan

@can('view Manufacturing Invoices Report')
    <li class="nav-item has-submenu">
        <a class="nav-link" href="javascript: void(0);">
            <i class="fas fa-money-bill-wave"></i>
            {{ __('manufacturing reports') }}
            <span class="menu-arrow"><i class="mdi mdi-chevron-right"></i></span>
        </a>
        <ul class="sub-menu mm-collapse">

            @can('view Manufacturing Invoices Report')
                <li class="nav-item">
                    <a class="nav-link" href="{{ route('manufacturing.invoice.report') }}">
                        <i class="fas fa-industry"></i>
                        <span>{{ __('manufacturing reports') }}</span>
                    </a>
                </li>
            @endcan

            @can('view Manufacturing Invoices')
                <li class="nav-item">
                    <a class="nav-link" href="{{ route('manufacturing.stage-invoices-report') }}">
                        <i class="fas fa-file-invoice"></i>
                        <span>{{ __('stage invoices report') }}</span>
                    </a>
                </li>
            @endcan
        </ul>
    </li>
@endcan
