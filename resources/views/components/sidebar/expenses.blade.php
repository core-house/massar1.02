@can('view Expenses')
    <li class="nav-item">
        <a class="nav-link {{ request()->routeIs('expenses.dashboard') ? 'active' : '' }}"
            href="{{ route('expenses.dashboard') }}">
            <i class="fas fa-tachometer-alt"></i>
            <span>{{ __('expenses dashboard') }}</span>
        </a>
    </li>
@endcan

@can('create Expenses')
    <li class="nav-item">
        <a class="nav-link {{ request()->routeIs('expenses.create') ? 'active' : '' }}" href="{{ route('expenses.create') }}">
            <i class="fas fa-plus-circle"></i>
            <span>{{ __('new expense record') }}</span>
        </a>
    </li>
@endcan

<li class="nav-divider"></li>

@can('view Expenses Reports')
    <li class="nav-item has-submenu">
        <a class="nav-link" href="javascript: void(0);">
            <i class="fas fa-file-invoice-dollar"></i>
            {{ __('expenses reports') }}
            <span class="menu-arrow"><i class="mdi mdi-chevron-right"></i></span>
        </a>
        <ul class="sub-menu mm-collapse">
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('reports.general-expenses-report') ? 'active' : '' }}"
                    href="{{ route('reports.general-expenses-report') }}">
                    <i class="ti-control-record"></i>{{ __('general expenses report') }}
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('reports.general-expenses-daily-report') ? 'active' : '' }}"
                    href="{{ route('reports.general-expenses-daily-report') }}">
                    <i class="ti-control-record"></i>{{ __('expense account statement') }}
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('reports.expenses-balance-report') ? 'active' : '' }}"
                    href="{{ route('reports.expenses-balance-report') }}">
                    <i class="ti-control-record"></i>{{ __('expenses balance sheet') }}
                </a>
            </li>
        </ul>
    </li>
@endcan

@can('view Cost Centers Report')
    <li class="nav-item has-submenu">
        <a class="nav-link" href="javascript: void(0);">
            <i class="fas fa-sitemap"></i>
            {{ __('cost centers') }}
            <span class="menu-arrow"><i class="mdi mdi-chevron-right"></i></span>
        </a>
        <ul class="sub-menu mm-collapse">
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('reports.general-cost-centers-report') ? 'active' : '' }}"
                    href="{{ route('reports.general-cost-centers-report') }}">
                    <i class="ti-control-record"></i>{{ __('cost centers report') }}
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('reports.general-cost-center-account-statement') ? 'active' : '' }}"
                    href="{{ route('reports.general-cost-center-account-statement') }}">
                    <i class="ti-control-record"></i>{{ __('cost center account statement') }}
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('reports.general-cost-centers-list') ? 'active' : '' }}"
                    href="{{ route('reports.general-cost-centers-list') }}">
                    <i class="ti-control-record"></i>{{ __('cost centers list') }}
                </a>
            </li>
        </ul>
    </li>
@endcan
<li class="nav-divider"></li>

<li class="nav-item">
    <a class="nav-link" href="{{ route('reports.overall') }}">
        <i class="fas fa-arrow-right"></i>
        <span>{{ __('back to reports') }}</span>
    </a>
</li>

<li class="nav-item">
    <a class="nav-link" href="{{ route('admin.dashboard') }}">
        <i class="fas fa-home"></i>
        <span>{{ __('home page') }}</span>
    </a>
</li>
