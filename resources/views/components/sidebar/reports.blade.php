@canany(['view Daily Activity Analyzer', 'view General Journal'])

    <li class="nav-item has-submenu">
        <a class="nav-link" href="javascript: void(0);">
            <i class="fas fa-chart-pie"></i>
            {{ trans_str('general reports') }}
            <span class="menu-arrow"><i class="mdi mdi-chevron-right"></i></span>
        </a>
        <ul class="sub-menu mm-collapse">

            @can('view Daily Activity Analyzer')
                <li class="nav-item">
                    <a class="nav-link d-flex align-items-center gap-2 font-hold fw-bold transition-base"
                        href="{{ route('reports.overall') }}">
                        <i class="las la-chart-line"></i>{{ trans_str('daily work analyzer') }}
                    </a>
                </li>


                <li class="nav-item">
                    <a class="nav-link d-flex align-items-center gap-2 font-hold fw-bold transition-base"
                        href="{{ route('reports.daily-activity-analyzer') }}">
                        <i class="las la-tasks"></i>{{ trans_str('daily activity analyzer') }}
                    </a>
                </li>
            @endcan

            @can('view General Journal')
                <li class="nav-item">
                    <a class="nav-link d-flex align-items-center gap-2 font-hold fw-bold transition-base"
                        href="{{ route('reports.journal-summery') }}">
                        <i class="las la-book-open"></i>{{ trans_str('general journal') }}
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
            {{ trans_str('accounts reports') }}
            <span class="menu-arrow"><i class="mdi mdi-chevron-right"></i></span>
        </a>
        <ul class="sub-menu mm-collapse">
            @can('view Accounts Tree')
                <li class="nav-item">
                    <a class="nav-link d-flex align-items-center gap-2 font-hold fw-bold transition-base"
                        href="{{ route('reports.accounts-tree') }}">
                        <i class="las la-sitemap"></i>{{ trans_str('accounts tree') }}
                    </a>
                </li>
            @endcan

            @can('view Balance Sheet')
                <li class="nav-item">
                    <a class="nav-link d-flex align-items-center gap-2 font-hold fw-bold transition-base"
                        href="{{ route('reports.general-balance-sheet') }}">
                        <i class="las la-balance-scale"></i>{{ trans_str('balance sheet') }}
                    </a>
                </li>
            @endcan

            @can('view Profit Loss Report')
                <li class="nav-item">
                    <a class="nav-link d-flex align-items-center gap-2 font-hold fw-bold transition-base"
                        href="{{ route('reports.general-profit-loss-report') }}">
                        <i class="las la-chart-area"></i>{{ trans_str('profit & loss') }}
                    </a>
                </li>
            @endcan

            @can('view Income Statement Total')
                <li class="nav-item">
                    <a class="nav-link d-flex align-items-center gap-2 font-hold fw-bold transition-base"
                        href="{{ route('reports.general-profit-loss-report-total') }}">
                        <i class="las la-file-invoice-dollar"></i>{{ trans_str('income statement total') }}
                    </a>
                </li>
            @endcan

            @can('view Accounts Balance')
                <li class="nav-item">
                    <a class="nav-link d-flex align-items-center gap-2 font-hold fw-bold transition-base"
                        href="{{ route('reports.general-account-balances') }}">
                        <i class="las la-calculator"></i>{{ trans_str('accounts balance') }}
                    </a>
                </li>
            @endcan

            @can('view Account Movement Report')
                <li class="nav-item">
                    <a class="nav-link d-flex align-items-center gap-2 font-hold fw-bold transition-base"
                        href="{{ route('account-movement') }}">
                        <i class="las la-exchange-alt"></i>{{ trans_str('account movement') }}
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
            {{ trans_str('inventory reports') }}
            <span class="menu-arrow"><i class="mdi mdi-chevron-right"></i></span>
        </a>
        <ul class="sub-menu mm-collapse">
            <li class="nav-item">
                <a class="nav-link d-flex align-items-center gap-2 font-hold fw-bold transition-base"
                    href="{{ route('reports.general-inventory-balances') }}">
                    <i class="las la-boxes"></i>{{ trans_str('items list with balances') }}
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link d-flex align-items-center gap-2 font-hold fw-bold transition-base"
                    href="{{ route('reports.general-inventory-balances-by-store') }}">
                    <i class="las la-warehouse"></i>{{ trans_str('balances by warehouse') }}
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link d-flex align-items-center gap-2 font-hold fw-bold transition-base"
                    href="{{ route('reports.general-inventory-movements') }}">
                    <i class="las la-dolly"></i>{{ trans_str('item movement') }}
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link d-flex align-items-center gap-2 font-hold fw-bold transition-base"
                    href="{{ route('reports.get-items-max-min-quantity') }}">
                    <i class="las la-sort-amount-down"></i>{{ trans_str('min & max order limit') }}
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link d-flex align-items-center gap-2 font-hold fw-bold transition-base"
                    href="{{ route('prices.compare.report') }}">
                    <i class="las la-balance-scale-right"></i>{{ trans_str('price comparison') }}
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link d-flex align-items-center gap-2 font-hold fw-bold transition-base"
                    href="{{ route('reports.items.inactive') }}">
                    <i class="las la-ban"></i>{{ trans_str('inactive items') }}
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link d-flex align-items-center gap-2 font-hold fw-bold transition-base"
                    href="{{ route('reports.items.idle') }}">
                    <i class="las la-pause-circle"></i>{{ trans_str('idle items report') }}
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
                    <i class="las la-store"></i>{{ trans_str('items per warehouse') }}
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link d-flex align-items-center gap-2 font-hold fw-bold transition-base"
                    href="{{ route('reports.inventory-discrepancy-report') }}">
                    <i class="las la-exclamation-triangle"></i>{{ trans_str('inventory discrepancy monitoring') }}
                </a>
            </li>
        </ul>
    </li>
@endcan

@can('view Sales Report')
    <li class="nav-item has-submenu">
        <a class="nav-link" href="javascript: void(0);">
            <i class="fas fa-shopping-cart"></i>
            {{ trans_str('sales reports') }}
            <span class="menu-arrow"><i class="mdi mdi-chevron-right"></i></span>
        </a>
        <ul class="sub-menu mm-collapse">
            <li class="nav-item">
                <a class="nav-link d-flex align-items-center gap-2 font-hold fw-bold transition-base"
                    href="{{ route('reports.general-sales-report') }}">
                    <i class="las la-file-alt"></i>{{ trans_str('general sales report') }}
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link d-flex align-items-center gap-2 font-hold fw-bold transition-base"
                    href="{{ route('reports.sales.total') }}">
                    <i class="las la-calculator"></i>{{ trans_str('sales totals') }}
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link d-flex align-items-center gap-2 font-hold fw-bold transition-base"
                    href="{{ route('reports.sales.items') }}">
                    <i class="las la-box"></i>{{ trans_str('sales items') }}
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link d-flex align-items-center gap-2 font-hold fw-bold transition-base"
                    href="{{ route('reports.sales.representative') }}">
                    <i class="las la-user-tie"></i>{{ trans_str('sales by representative') }}
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link d-flex align-items-center gap-2 font-hold fw-bold transition-base"
                    href="{{ route('reports.general-sales-report-by-address') }}">
                    <i class="las la-map-marker-alt"></i>{{ trans_str('sales by address') }}
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link d-flex align-items-center gap-2 font-hold fw-bold transition-base"
                    href="{{ route('reports.item-sales') }}">
                    <i class="las la-shopping-bag"></i>{{ trans_str('item sales') }}
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link d-flex align-items-center gap-2 font-hold fw-bold transition-base"
                    href="{{ route('sales.invoice-report') }}">
                    <i class="las la-file-invoice"></i>{{ trans_str('sales invoices') }}
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link d-flex align-items-center gap-2 font-hold fw-bold transition-base"
                    href="{{ route('sales-orders-report') }}">
                    <i class="las la-clipboard-list"></i>{{ trans_str('sales orders') }}
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link d-flex align-items-center gap-2 font-hold fw-bold transition-base"
                    href="{{ route('purchase-quotations-reports') }}">
                    <i class="las la-file-contract"></i>{{ trans_str('customer quotations') }}
                </a>
            </li>
        </ul>
    </li>
@endcan

@can('view Purchases Report')
    <li class="nav-item has-submenu">
        <a class="nav-link" href="javascript: void(0);">
            <i class="fas fa-shopping-basket"></i>
            {{ trans_str('purchase reports') }}
            <span class="menu-arrow"><i class="mdi mdi-chevron-right"></i></span>
        </a>
        <ul class="sub-menu mm-collapse">
            <li class="nav-item">
                <a class="nav-link d-flex align-items-center gap-2 font-hold fw-bold transition-base"
                    href="{{ route('reports.purchasing.dashboard') }}">
                    <i class="las la-tachometer-alt"></i>{{ trans_str('purchasing dashboard') }}
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link d-flex align-items-center gap-2 font-hold fw-bold transition-base"
                    href="{{ route('reports.general-purchases-report') }}">
                    <i class="las la-file-alt"></i>{{ trans_str('general purchases report') }}
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link d-flex align-items-center gap-2 font-hold fw-bold transition-base"
                    href="{{ route('reports.general-purchases-daily-report') }}">
                    <i class="las la-calendar-day"></i>{{ trans_str('daily purchases') }}
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link d-flex align-items-center gap-2 font-hold fw-bold transition-base"
                    href="{{ route('reports.purchases.total') }}">
                    <i class="las la-calculator"></i>{{ trans_str('purchases totals') }}
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link d-flex align-items-center gap-2 font-hold fw-bold transition-base"
                    href="{{ route('reports.purchases.items') }}">
                    <i class="las la-box"></i>{{ trans_str('purchases items') }}
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link d-flex align-items-center gap-2 font-hold fw-bold transition-base"
                    href="{{ route('reports.item-purchase') }}">
                    <i class="las la-shopping-bag"></i>{{ trans_str('item purchase') }}
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link d-flex align-items-center gap-2 font-hold fw-bold transition-base"
                    href="{{ route('billing.invoice-report') }}">
                    <i class="las la-file-invoice"></i>{{ trans_str('purchase invoices') }}
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link d-flex align-items-center gap-2 font-hold fw-bold transition-base"
                    href="{{ route('supplier-rfqs-report') }}">
                    <i class="las la-file-contract"></i>{{ trans_str('supplier quotations') }}
                </a>
            </li>
        </ul>
    </li>
@endcan

@can('view Customer Quotation Report')
    <li class="nav-item has-submenu">
        <a class="nav-link" href="javascript: void(0);">
            <i class="fas fa-users"></i>
            {{ trans_str('customers reports') }}
            <span class="menu-arrow"><i class="mdi mdi-chevron-right"></i></span>
        </a>
        <ul class="sub-menu mm-collapse">
            <li class="nav-item">
                <a class="nav-link d-flex align-items-center gap-2 font-hold fw-bold transition-base"
                    href="{{ route('reports.general-customers-report') }}">
                    <i class="las la-file-alt"></i>{{ trans_str('general customers report') }}
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link d-flex align-items-center gap-2 font-hold fw-bold transition-base"
                    href="{{ route('reports.general-customers-daily-report') }}">
                    <i class="las la-calendar-day"></i>{{ trans_str('daily customers report') }}
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link d-flex align-items-center gap-2 font-hold fw-bold transition-base"
                    href="{{ route('reports.general-customers-items-report') }}">
                    <i class="las la-box"></i>{{ trans_str('customers items report') }}
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link d-flex align-items-center gap-2 font-hold fw-bold transition-base"
                    href="{{ route('reports.general-customers-total-report') }}">
                    <i class="las la-calculator"></i>{{ trans_str('customers totals report') }}
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link d-flex align-items-center gap-2 font-hold fw-bold transition-base"
                    href="{{ route('reports.customer-debt-history') }}">
                    <i class="las la-history"></i>{{ trans_str('customer debt aging') }}
                </a>
            </li>
        </ul>
    </li>
@endcan

@can('view Supplier Quotation Report')
    <li class="nav-item has-submenu">
        <a class="nav-link" href="javascript: void(0);">
            <i class="fas fa-truck"></i>
            {{ trans_str('suppliers reports') }}
            <span class="menu-arrow"><i class="mdi mdi-chevron-right"></i></span>
        </a>
        <ul class="sub-menu mm-collapse">
            <li class="nav-item">
                <a class="nav-link d-flex align-items-center gap-2 font-hold fw-bold transition-base"
                    href="{{ route('reports.general-suppliers-report') }}">
                    <i class="las la-file-alt"></i>{{ trans_str('general suppliers report') }}
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link d-flex align-items-center gap-2 font-hold fw-bold transition-base"
                    href="{{ route('reports.general-suppliers-daily-report') }}">
                    <i class="las la-calendar-day"></i>{{ trans_str('daily suppliers report') }}
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link d-flex align-items-center gap-2 font-hold fw-bold transition-base"
                    href="{{ route('reports.general-suppliers-items-report') }}">
                    <i class="las la-box"></i>{{ trans_str('suppliers items report') }}
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link d-flex align-items-center gap-2 font-hold fw-bold transition-base"
                    href="{{ route('reports.general-suppliers-total-report') }}">
                    <i class="las la-calculator"></i>{{ trans_str('suppliers totals report') }}
                </a>
            </li>
        </ul>
    </li>
@endcan

@can('view Expenses Report')
    <li class="nav-item has-submenu">
        <a class="nav-link" href="javascript: void(0);">
            <i class="fas fa-file-invoice-dollar"></i>
            {{ trans_str('expenses reports') }}
            <span class="menu-arrow"><i class="mdi mdi-chevron-right"></i></span>
        </a>
        <ul class="sub-menu mm-collapse">
            <li class="nav-item">
                <a class="nav-link d-flex align-items-center gap-2 font-hold fw-bold transition-base"
                    href="{{ route('reports.general-expenses-report') }}">
                    <i class="las la-file-alt"></i>{{ trans_str('general expenses report') }}
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link d-flex align-items-center gap-2 font-hold fw-bold transition-base"
                    href="{{ route('reports.general-expenses-daily-report') }}">
                    <i class="las la-calendar-day"></i>{{ trans_str('expense account statement') }}
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link d-flex align-items-center gap-2 font-hold fw-bold transition-base"
                    href="{{ route('reports.expenses-balance-report') }}">
                    <i class="las la-balance-scale"></i>{{ trans_str('expenses balance') }}
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link d-flex align-items-center gap-2 font-hold fw-bold transition-base"
                    href="{{ route('reports.general-cost-centers-report') }}">
                    <i class="las la-sitemap"></i>{{ trans_str('cost centers report') }}
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link d-flex align-items-center gap-2 font-hold fw-bold transition-base"
                    href="{{ route('reports.general-cost-center-account-statement') }}">
                    <i class="las la-file-invoice"></i>{{ trans_str('cost center account statement') }}
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link d-flex align-items-center gap-2 font-hold fw-bold transition-base"
                    href="{{ route('reports.general-cost-centers-list') }}">
                    <i class="las la-list"></i>{{ trans_str('cost centers list') }}
                </a>
            </li>
        </ul>
    </li>
@endcan

@can('view General Cashbox Movement Report')
    <li class="nav-item has-submenu">
        <a class="nav-link" href="javascript: void(0);">
            <i class="fas fa-money-bill-wave"></i>
            {{ trans_str('cash & bank') }}
            <span class="menu-arrow"><i class="mdi mdi-chevron-right"></i></span>
        </a>
        <ul class="sub-menu mm-collapse">
            <li class="nav-item">
                <a class="nav-link d-flex align-items-center gap-2 font-hold fw-bold transition-base"
                    href="{{ route('reports.general-cashbox-movement-report') }}">
                    <i class="las la-cash-register"></i>{{ trans_str('cashbox movement') }}
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link d-flex align-items-center gap-2 font-hold fw-bold transition-base"
                    href="{{ route('reports.general-cash-bank-report') }}">
                    <i class="las la-university"></i>{{ trans_str('bank movement') }}
                </a>
            </li>
        </ul>
    </li>
@endcan

@can('view Manufacturing Invoices Report')
    <li class="nav-item has-submenu">
        <a class="nav-link" href="javascript: void(0);">
            <i class="fas fa-money-bill-wave"></i>
            {{ trans_str('manufacturing reports') }}
            <span class="menu-arrow"><i class="mdi mdi-chevron-right"></i></span>
        </a>
        <ul class="sub-menu mm-collapse">

            @can('view Manufacturing Invoices Report')
                <li class="nav-item">
                    <a class="nav-link" href="{{ route('manufacturing.invoice.report') }}">
                        <i class="fas fa-industry"></i>
                        <span>{{ trans_str('manufacturing reports') }}</span>
                    </a>
                </li>
            @endcan

            @can('view Manufacturing Invoices')
                <li class="nav-item">
                    <a class="nav-link" href="{{ route('manufacturing.stage-invoices-report') }}">
                        <i class="fas fa-file-invoice"></i>
                        <span>{{ trans_str('stage invoices report') }}</span>
                    </a>
                </li>
            @endcan
        </ul>
    </li>
@endcan
