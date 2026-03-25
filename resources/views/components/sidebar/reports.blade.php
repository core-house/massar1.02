@canany(['view Daily Activity Analyzer', 'view General Journal'])

    <li class="nav-item has-submenu">
        <a class="nav-link" href="javascript: void(0);">
            <i class="fas fa-chart-pie"></i>
            {{ __('reports::sidebar.general_reports') }}
            <span class="menu-arrow"><i class="mdi mdi-chevron-right"></i></span>
        </a>
        <ul class="sub-menu mm-collapse">

            @can('view Daily Activity Analyzer')
                <li class="nav-item">
                    <a class="nav-link d-flex align-items-center gap-2 font-hold fw-bold transition-base"
                        href="{{ route('reports.overall') }}">
                        <i class="las la-chart-line"></i>{{ __('reports::sidebar.daily_work_analyzer') }}
                    </a>
                </li>


                <li class="nav-item">
                    <a class="nav-link d-flex align-items-center gap-2 font-hold fw-bold transition-base"
                        href="{{ route('reports.daily-activity-analyzer') }}">
                        <i class="las la-tasks"></i>{{ __('reports::sidebar.daily_activity_analyzer') }}
                    </a>
                </li>
            @endcan

            @can('view General Journal')
                <li class="nav-item">
                    <a class="nav-link d-flex align-items-center gap-2 font-hold fw-bold transition-base"
                        href="{{ route('reports.journal-summery') }}">
                        <i class="las la-book-open"></i>{{ __('reports::sidebar.general_journal') }}
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
            {{ __('reports::sidebar.accounts_reports') }}
            <span class="menu-arrow"><i class="mdi mdi-chevron-right"></i></span>
        </a>
        <ul class="sub-menu mm-collapse">
            @can('view Accounts Tree')
                <li class="nav-item">
                    <a class="nav-link d-flex align-items-center gap-2 font-hold fw-bold transition-base"
                        href="{{ route('reports.accounts-tree') }}">
                        <i class="las la-sitemap"></i>{{ __('reports::sidebar.accounts_tree') }}
                    </a>
                </li>
            @endcan

            @can('view Balance Sheet')
                <li class="nav-item">
                    <a class="nav-link d-flex align-items-center gap-2 font-hold fw-bold transition-base"
                        href="{{ route('reports.general-balance-sheet') }}">
                        <i class="las la-balance-scale"></i>{{ __('reports::sidebar.balance_sheet') }}
                    </a>
                </li>
            @endcan

            @can('view Profit Loss Report')
                <li class="nav-item">
                    <a class="nav-link d-flex align-items-center gap-2 font-hold fw-bold transition-base"
                        href="{{ route('reports.general-profit-loss-report') }}">
                        <i class="las la-chart-area"></i>{{ __('reports::sidebar.profit_and_loss') }}
                    </a>
                </li>
            @endcan

            @can('view Income Statement Total')
                <li class="nav-item">
                    <a class="nav-link d-flex align-items-center gap-2 font-hold fw-bold transition-base"
                        href="{{ route('reports.general-profit-loss-report-total') }}">
                        <i class="las la-file-invoice-dollar"></i>{{ __('reports::sidebar.income_statement_total') }}
                    </a>
                </li>
            @endcan

            @can('view Accounts Balance')
                <li class="nav-item">
                    <a class="nav-link d-flex align-items-center gap-2 font-hold fw-bold transition-base"
                        href="{{ route('reports.general-account-balances') }}">
                        <i class="las la-calculator"></i>{{ __('reports::sidebar.accounts_balance') }}
                    </a>
                </li>
            @endcan

            @can('view Account Movement Report')
                <li class="nav-item">
                    <a class="nav-link d-flex align-items-center gap-2 font-hold fw-bold transition-base"
                        href="{{ route('account-movement') }}">
                        <i class="las la-exchange-alt"></i>{{ __('reports::sidebar.account_movement') }}
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
            {{ __('reports::sidebar.inventory_reports') }}
            <span class="menu-arrow"><i class="mdi mdi-chevron-right"></i></span>
        </a>
        <ul class="sub-menu mm-collapse">
            <li class="nav-item">
                <a class="nav-link d-flex align-items-center gap-2 font-hold fw-bold transition-base"
                    href="{{ route('reports.general-inventory-balances') }}">
                    <i class="las la-boxes"></i>{{ __('reports::sidebar.items_list_with_balances') }}
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link d-flex align-items-center gap-2 font-hold fw-bold transition-base"
                    href="{{ route('reports.general-inventory-balances-by-store') }}">
                    <i class="las la-warehouse"></i>{{ __('reports::sidebar.balances_by_warehouse') }}
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link d-flex align-items-center gap-2 font-hold fw-bold transition-base"
                    href="{{ route('reports.general-inventory-movements') }}">
                    <i class="las la-dolly"></i>{{ __('reports::sidebar.item_movement') }}
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link d-flex align-items-center gap-2 font-hold fw-bold transition-base"
                    href="{{ route('reports.get-items-max-min-quantity') }}">
                    <i class="las la-sort-amount-down"></i>{{ __('reports::sidebar.min_max_order_limit') }}
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link d-flex align-items-center gap-2 font-hold fw-bold transition-base"
                    href="{{ route('prices.compare.report') }}">
                    <i class="las la-balance-scale-right"></i>{{ __('reports::sidebar.price_comparison') }}
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link d-flex align-items-center gap-2 font-hold fw-bold transition-base"
                    href="{{ route('reports.items.inactive') }}">
                    <i class="las la-ban"></i>{{ __('reports::sidebar.inactive_items') }}
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link d-flex align-items-center gap-2 font-hold fw-bold transition-base"
                    href="{{ route('reports.items.idle') }}">
                    <i class="las la-pause-circle"></i>{{ __('reports::sidebar.idle_items_report') }}
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link d-flex align-items-center gap-2 font-hold fw-bold transition-base"
                    href="{{ route('reports.items.most-expensive') }}">
                    <i class="las la-gem"></i>{{ __('reports::sidebar.most_expensive_items_report') }}
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link d-flex align-items-center gap-2 font-hold fw-bold transition-base"
                    href="{{ route('reports.items.with-stores') }}">
                    <i class="las la-store"></i>{{ __('reports::sidebar.items_per_warehouse') }}
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link d-flex align-items-center gap-2 font-hold fw-bold transition-base"
                    href="{{ route('reports.inventory-discrepancy-report') }}">
                    <i class="las la-exclamation-triangle"></i>{{ __('reports::sidebar.inventory_discrepancy_monitoring') }}
                </a>
            </li>
        </ul>
    </li>
@endcan

@can('view Sales Report')
    <li class="nav-item has-submenu">
        <a class="nav-link" href="javascript: void(0);">
            <i class="fas fa-shopping-cart"></i>
            {{ __('reports::sidebar.sales_reports') }}
            <span class="menu-arrow"><i class="mdi mdi-chevron-right"></i></span>
        </a>
        <ul class="sub-menu mm-collapse">
            <li class="nav-item">
                <a class="nav-link d-flex align-items-center gap-2 font-hold fw-bold transition-base"
                    href="{{ route('reports.general-sales-report') }}">
                    <i class="las la-file-alt"></i>{{ __('reports::sidebar.general_sales_report') }}
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link d-flex align-items-center gap-2 font-hold fw-bold transition-base"
                    href="{{ route('reports.sales.total') }}">
                    <i class="las la-calculator"></i>{{ __('reports::sidebar.sales_totals') }}
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link d-flex align-items-center gap-2 font-hold fw-bold transition-base"
                    href="{{ route('reports.sales.items') }}">
                    <i class="las la-box"></i>{{ __('reports::sidebar.sales_items') }}
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link d-flex align-items-center gap-2 font-hold fw-bold transition-base"
                    href="{{ route('reports.sales.representative') }}">
                    <i class="las la-user-tie"></i>{{ __('reports::sidebar.sales_by_representative') }}
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link d-flex align-items-center gap-2 font-hold fw-bold transition-base"
                    href="{{ route('reports.general-sales-report-by-address') }}">
                    <i class="las la-map-marker-alt"></i>{{ __('reports::sidebar.sales_by_address') }}
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link d-flex align-items-center gap-2 font-hold fw-bold transition-base"
                    href="{{ route('reports.item-sales') }}">
                    <i class="las la-shopping-bag"></i>{{ __('reports::sidebar.item_sales') }}
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link d-flex align-items-center gap-2 font-hold fw-bold transition-base"
                    href="{{ route('sales.invoice-report') }}">
                    <i class="las la-file-invoice"></i>{{ __('reports::sidebar.sales_invoices') }}
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link d-flex align-items-center gap-2 font-hold fw-bold transition-base"
                    href="{{ route('sales-orders-report') }}">
                    <i class="las la-clipboard-list"></i>{{ __('reports::sidebar.sales_orders') }}
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link d-flex align-items-center gap-2 font-hold fw-bold transition-base"
                    href="{{ route('purchase-quotations-reports') }}">
                    <i class="las la-file-contract"></i>{{ __('reports::sidebar.customer_quotations') }}
                </a>
            </li>
        </ul>
    </li>
@endcan

@can('view Purchases Report')
    <li class="nav-item has-submenu">
        <a class="nav-link" href="javascript: void(0);">
            <i class="fas fa-shopping-basket"></i>
            {{ __('reports::sidebar.purchase_reports') }}
            <span class="menu-arrow"><i class="mdi mdi-chevron-right"></i></span>
        </a>
        <ul class="sub-menu mm-collapse">
            <li class="nav-item">
                <a class="nav-link d-flex align-items-center gap-2 font-hold fw-bold transition-base"
                    href="{{ route('reports.purchasing.dashboard') }}">
                    <i class="las la-tachometer-alt"></i>{{ __('reports::sidebar.purchasing_dashboard') }}
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link d-flex align-items-center gap-2 font-hold fw-bold transition-base"
                    href="{{ route('reports.general-purchases-report') }}">
                    <i class="las la-file-alt"></i>{{ __('reports::sidebar.general_purchases_report') }}
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link d-flex align-items-center gap-2 font-hold fw-bold transition-base"
                    href="{{ route('reports.general-purchases-daily-report') }}">
                    <i class="las la-calendar-day"></i>{{ __('reports::sidebar.daily_purchases') }}
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link d-flex align-items-center gap-2 font-hold fw-bold transition-base"
                    href="{{ route('reports.purchases.total') }}">
                    <i class="las la-calculator"></i>{{ __('reports::sidebar.purchases_totals') }}
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link d-flex align-items-center gap-2 font-hold fw-bold transition-base"
                    href="{{ route('reports.purchases.items') }}">
                    <i class="las la-box"></i>{{ __('reports::sidebar.purchases_items') }}
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link d-flex align-items-center gap-2 font-hold fw-bold transition-base"
                    href="{{ route('reports.item-purchase') }}">
                    <i class="las la-shopping-bag"></i>{{ __('reports::sidebar.item_purchase') }}
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link d-flex align-items-center gap-2 font-hold fw-bold transition-base"
                    href="{{ route('billing.invoice-report') }}">
                    <i class="las la-file-invoice"></i>{{ __('reports::sidebar.purchase_invoices') }}
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link d-flex align-items-center gap-2 font-hold fw-bold transition-base"
                    href="{{ route('supplier-rfqs-report') }}">
                    <i class="las la-file-contract"></i>{{ __('reports::sidebar.supplier_quotations') }}
                </a>
            </li>
        </ul>
    </li>
@endcan

@can('view Customer Quotation Report')
    <li class="nav-item has-submenu">
        <a class="nav-link" href="javascript: void(0);">
            <i class="fas fa-users"></i>
            {{ __('reports::sidebar.customers_reports') }}
            <span class="menu-arrow"><i class="mdi mdi-chevron-right"></i></span>
        </a>
        <ul class="sub-menu mm-collapse">
            <li class="nav-item">
                <a class="nav-link d-flex align-items-center gap-2 font-hold fw-bold transition-base"
                    href="{{ route('reports.general-customers-report') }}">
                    <i class="las la-file-alt"></i>{{ __('reports::sidebar.general_customers_report') }}
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link d-flex align-items-center gap-2 font-hold fw-bold transition-base"
                    href="{{ route('reports.general-customers-daily-report') }}">
                    <i class="las la-calendar-day"></i>{{ __('reports::sidebar.daily_customers_report') }}
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link d-flex align-items-center gap-2 font-hold fw-bold transition-base"
                    href="{{ route('reports.general-customers-items-report') }}">
                    <i class="las la-box"></i>{{ __('reports::sidebar.customers_items_report') }}
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link d-flex align-items-center gap-2 font-hold fw-bold transition-base"
                    href="{{ route('reports.general-customers-total-report') }}">
                    <i class="las la-calculator"></i>{{ __('reports::sidebar.customers_totals_report') }}
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link d-flex align-items-center gap-2 font-hold fw-bold transition-base"
                    href="{{ route('reports.customer-debt-history') }}">
                    <i class="las la-history"></i>{{ __('reports::sidebar.customer_debt_aging') }}
                </a>
            </li>
        </ul>
    </li>
@endcan

@can('view Supplier Quotation Report')
    <li class="nav-item has-submenu">
        <a class="nav-link" href="javascript: void(0);">
            <i class="fas fa-truck"></i>
            {{ __('reports::sidebar.suppliers_reports') }}
            <span class="menu-arrow"><i class="mdi mdi-chevron-right"></i></span>
        </a>
        <ul class="sub-menu mm-collapse">
            <li class="nav-item">
                <a class="nav-link d-flex align-items-center gap-2 font-hold fw-bold transition-base"
                    href="{{ route('reports.general-suppliers-report') }}">
                    <i class="las la-file-alt"></i>{{ __('reports::sidebar.general_suppliers_report') }}
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link d-flex align-items-center gap-2 font-hold fw-bold transition-base"
                    href="{{ route('reports.general-suppliers-daily-report') }}">
                    <i class="las la-calendar-day"></i>{{ __('reports::sidebar.daily_suppliers_report') }}
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link d-flex align-items-center gap-2 font-hold fw-bold transition-base"
                    href="{{ route('reports.general-suppliers-items-report') }}">
                    <i class="las la-box"></i>{{ __('reports::sidebar.suppliers_items_report') }}
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link d-flex align-items-center gap-2 font-hold fw-bold transition-base"
                    href="{{ route('reports.general-suppliers-total-report') }}">
                    <i class="las la-calculator"></i>{{ __('reports::sidebar.suppliers_totals_report') }}
                </a>
            </li>
        </ul>
    </li>
@endcan

@can('view Expenses Report')
    <li class="nav-item has-submenu">
        <a class="nav-link" href="javascript: void(0);">
            <i class="fas fa-file-invoice-dollar"></i>
            {{ __('reports::sidebar.expenses_reports') }}
            <span class="menu-arrow"><i class="mdi mdi-chevron-right"></i></span>
        </a>
        <ul class="sub-menu mm-collapse">
            <li class="nav-item">
                <a class="nav-link d-flex align-items-center gap-2 font-hold fw-bold transition-base"
                    href="{{ route('reports.general-expenses-report') }}">
                    <i class="las la-file-alt"></i>{{ __('reports::sidebar.general_expenses_report') }}
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link d-flex align-items-center gap-2 font-hold fw-bold transition-base"
                    href="{{ route('reports.general-expenses-daily-report') }}">
                    <i class="las la-calendar-day"></i>{{ __('reports::sidebar.expense_account_statement') }}
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link d-flex align-items-center gap-2 font-hold fw-bold transition-base"
                    href="{{ route('reports.expenses-balance-report') }}">
                    <i class="las la-balance-scale"></i>{{ __('reports::sidebar.expenses_balance') }}
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link d-flex align-items-center gap-2 font-hold fw-bold transition-base"
                    href="{{ route('reports.general-cost-centers-report') }}">
                    <i class="las la-sitemap"></i>{{ __('reports::sidebar.cost_centers_report') }}
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link d-flex align-items-center gap-2 font-hold fw-bold transition-base"
                    href="{{ route('reports.general-cost-center-account-statement') }}">
                    <i class="las la-file-invoice"></i>{{ __('reports::sidebar.cost_center_account_statement') }}
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link d-flex align-items-center gap-2 font-hold fw-bold transition-base"
                    href="{{ route('reports.general-cost-centers-list') }}">
                    <i class="las la-list"></i>{{ __('reports::sidebar.cost_centers_list') }}
                </a>
            </li>
        </ul>
    </li>
@endcan

@can('view General Cashbox Movement Report')
    <li class="nav-item has-submenu">
        <a class="nav-link" href="javascript: void(0);">
            <i class="fas fa-money-bill-wave"></i>
            {{ __('reports::sidebar.cash_and_bank') }}
            <span class="menu-arrow"><i class="mdi mdi-chevron-right"></i></span>
        </a>
        <ul class="sub-menu mm-collapse">
            <li class="nav-item">
                <a class="nav-link d-flex align-items-center gap-2 font-hold fw-bold transition-base"
                    href="{{ route('reports.general-cashbox-movement-report') }}">
                    <i class="las la-cash-register"></i>{{ __('reports::sidebar.cashbox_movement') }}
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link d-flex align-items-center gap-2 font-hold fw-bold transition-base"
                    href="{{ route('reports.general-cash-bank-report') }}">
                    <i class="las la-university"></i>{{ __('reports::sidebar.bank_movement') }}
                </a>
            </li>
        </ul>
    </li>
@endcan

@can('view Manufacturing Invoices Report')
    <li class="nav-item has-submenu">
        <a class="nav-link" href="javascript: void(0);">
            <i class="fas fa-money-bill-wave"></i>
            {{ __('reports::sidebar.manufacturing_reports') }}
            <span class="menu-arrow"><i class="mdi mdi-chevron-right"></i></span>
        </a>
        <ul class="sub-menu mm-collapse">

            @can('view Manufacturing Invoices Report')
                <li class="nav-item">
                    <a class="nav-link" href="{{ route('manufacturing.invoice.report') }}">
                        <i class="fas fa-industry"></i>
                        <span>{{ __('reports::sidebar.manufacturing_reports') }}</span>
                    </a>
                </li>
            @endcan

            @can('view Manufacturing Invoices')
                <li class="nav-item">
                    <a class="nav-link" href="{{ route('manufacturing.stage-invoices-report') }}">
                        <i class="fas fa-file-invoice"></i>
                        <span>{{ __('reports::sidebar.stage_invoices_report') }}</span>
                    </a>
                </li>
            @endcan
        </ul>
    </li>
@endcan

