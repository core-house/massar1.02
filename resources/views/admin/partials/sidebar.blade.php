@php
    $section = request('sidebar') ?? session('sidebar', 'all');
    $map = [
        'main' => null,
        'accounts' => ['components.sidebar.accounts'],
        'items' => ['components.sidebar.items'],
        'discounts' => ['components.sidebar.discounts'],
        'manufacturing' => ['components.sidebar.manufacturing'],
        'permissions' => ['components.sidebar.permissions'],
        'crm' => ['components.sidebar.crm'],
        'sales-invoices' => ['components.sidebar.sales-invoices'],
        'purchases-invoices' => ['components.sidebar.purchases-invoices'],
        'inventory-invoices' => ['components.sidebar.inventory-invoices'],
        'vouchers' => ['components.sidebar.vouchers'],
        'transfers' => ['components.sidebar.transfers'],
        'multi-vouchers' => ['components.sidebar.merit-vouchers'],
        'contract-journals' => ['components.sidebar.contract-journals'],
        'Assets-operations' => ['components.sidebar.multi-vouchers'],
        'depreciation' => ['components.sidebar.depreciation'],
        'basic_journal-journals' => ['components.sidebar.journals'],
        'projects' => ['components.sidebar.projects'],
        'departments' => ['components.sidebar.departments'],
        'settings' => ['components.sidebar.settings'],
        'rentals' => ['components.sidebar.rentals'],
        'service' => ['components.sidebar.service'],
        'shipping' => ['components.sidebar.shipping'],
        'fleet' => ['components.sidebar.fleet'],
        'POS' => ['components.sidebar.POS'],
        'daily_progress' => ['components.sidebar.daily_progress'],
        'inquiries' => ['components.sidebar.inquiries'],
        'checks' => ['components.sidebar.checks'],
    ];
    $allowed = $section === 'all' ? 'all' : $map[$section] ?? [];
@endphp

<!-- Left Sidenav -->
<div class="left-sidenav">

    <div class="menu-content h-100" data-simplebar>
        <ul class="metismenu left-sidenav-menu">

            <li class="menu-label my-2"><a href="{{ route('home') }}">{{ config('public_settings.campany_name') }}</a>
            </li>

            <li class="nav-item border-bottom pb-1 mb-2">
                <a href="{{ route('admin.dashboard') }}"
                    class="nav-link d-flex align-items-center gap-2 font-hold fw-bold">
                    <i data-feather="home" style="color:#4e73df" class="menu-icon"></i>
                    {{ __('navigation.home') }}
                </a>

            </li>

            @if ($section !== 'all')
                <li class="nav-item mb-2">
                    <div class="alert alert-info d-flex align-items-center justify-content-between"
                        style="margin: 0; padding: 0.5rem 0.75rem;">
                        <small class="mb-0">
                            <i data-feather="filter" style="width: 14px; height: 14px;" class="me-1"></i>
                            عرض: {{ $section }}
                        </small>
                        {{-- <a href="{{ request()->url() }}?sidebar=all" class="btn btn-sm btn-outline-primary" style="font-size: 0.75rem; padding: 0.25rem 0.5rem;">
                            عرض الجميع
                        </a> --}}
                    </div>
                </li>
            @endif

            @if (View::hasSection('sidebar-filter'))
                @yield('sidebar-filter')
            @else
                @php
                    $isTenant = function_exists('tenant') && tenant();
                    $moduleMap = [
                        'components.sidebar.accounts' => 'accounts',
                        'components.sidebar.vouchers' => 'accounts',
                        'components.sidebar.journals' => 'accounts',
                        'components.sidebar.checks' => 'accounts',
                        'components.sidebar.items' => 'inventory',
                        'components.sidebar.transfers' => 'inventory',
                        'components.sidebar.discounts' => 'inventory',
                        'components.sidebar.manufacturing' => 'manufacturing',
                        'components.sidebar.crm' => 'crm',
                        'components.sidebar.sales-invoices' => 'invoices',
                        'components.sidebar.purchases-invoices' => 'invoices',
                        'components.sidebar.inventory-invoices' => 'invoices',
                        'components.sidebar.rentals' => 'rentals',
                        'components.sidebar.service' => 'rentals',
                        'components.sidebar.fleet' => 'fleet',
                        'components.sidebar.shipping' => 'fleet',
                        'components.sidebar.POS' => 'pos',
                        'components.sidebar.projects' => 'projects',
                        'components.sidebar.daily_progress' => 'projects',
                        'components.sidebar.inquiries' => 'inquiries',
                        'components.sidebar.installments' => 'installments',
                        'components.sidebar.departments' => 'hr',
                        'components.sidebar.permissions' => true, // Always allowed or controlled by permissions
                        'components.sidebar.settings' => true,
                        'components.sidebar.merit-vouchers' => 'accounts',
                        'components.sidebar.contract-journals' => 'accounts',
                        'components.sidebar.multi-vouchers' => 'accounts',
                        'components.sidebar.depreciation' => 'accounts',
                    ];

                    $isAllowed = function($comp) use ($isTenant, $allowed, $moduleMap) {
                        // 1. Basic Sidebar Filter check
                        if ($allowed !== 'all' && !in_array($comp, $allowed)) return false;
                        
                        // 2. Tenant Module check
                        if ($isTenant) {
                            $module = $moduleMap[$comp] ?? true;
                            if ($module !== true && !tenant()->hasModule($module)) return false;
                        }
                        
                        return true;
                    };
                @endphp

                @if ($isAllowed('components.sidebar.accounts')) @include('components.sidebar.accounts') @endif
                @if ($isAllowed('components.sidebar.items')) @include('components.sidebar.items') @endif
                @if ($isAllowed('components.sidebar.discounts')) @include('components.sidebar.discounts') @endif
                @if ($isAllowed('components.sidebar.manufacturing')) @include('components.sidebar.manufacturing') @endif
                @if ($isAllowed('components.sidebar.permissions')) @include('components.sidebar.permissions') @endif
                @if ($isAllowed('components.sidebar.crm')) @include('components.sidebar.crm') @endif
                @if ($isAllowed('components.sidebar.sales-invoices')) @include('components.sidebar.sales-invoices') @endif
                @if ($isAllowed('components.sidebar.purchases-invoices')) @include('components.sidebar.purchases-invoices') @endif
                @if ($isAllowed('components.sidebar.inventory-invoices')) @include('components.sidebar.inventory-invoices') @endif
                @if ($isAllowed('components.sidebar.vouchers')) @include('components.sidebar.vouchers') @endif
                @if ($isAllowed('components.sidebar.transfers')) @include('components.sidebar.transfers') @endif
                @if ($isAllowed('components.sidebar.merit-vouchers')) @include('components.sidebar.merit-vouchers') @endif
                @if ($isAllowed('components.sidebar.contract-journals')) @include('components.sidebar.contract-journals') @endif
                @if ($isAllowed('components.sidebar.multi-vouchers')) @include('components.sidebar.multi-vouchers') @endif
                @if ($isAllowed('components.sidebar.depreciation')) @include('components.sidebar.depreciation') @endif
                @if ($isAllowed('components.sidebar.journals')) @include('components.sidebar.journals') @endif
                @if ($isAllowed('components.sidebar.projects')) @include('components.sidebar.projects') @endif
                @if ($isAllowed('components.sidebar.departments')) @include('components.sidebar.departments') @endif
                @if ($isAllowed('components.sidebar.settings')) @include('components.sidebar.settings') @endif
                @if ($isAllowed('components.sidebar.rentals')) @include('components.sidebar.rentals') @endif
                @if ($isAllowed('components.sidebar.service')) @include('components.sidebar.service') @endif
                @if ($isAllowed('components.sidebar.shipping')) @include('components.sidebar.shipping') @endif
                @if ($isAllowed('components.sidebar.fleet')) @include('components.sidebar.fleet') @endif
                @if ($isAllowed('components.sidebar.POS')) @include('components.sidebar.POS') @endif
                @if ($isAllowed('components.sidebar.daily_progress')) @include('components.sidebar.daily_progress') @endif
                @if ($isAllowed('components.sidebar.inquiries')) @include('components.sidebar.inquiries') @endif
                @if ($isAllowed('components.sidebar.checks')) @include('components.sidebar.checks') @endif
                @if ($isAllowed('components.sidebar.installments')) @include('components.sidebar.installments') @endif
            @endif

        </ul>
    </div>
</div>
