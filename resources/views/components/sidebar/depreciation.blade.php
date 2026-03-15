@can('view asset-depreciation')
    <li class="li-main">
        <a href="javascript: void(0);" class="has-arrow waves-effect waves-dark">
            <i data-feather="calculator" style="color:#28a745" class="align-self-center menu-icon"></i>
            <span>{{ __('sidebar.asset_depreciation_management') }}</span>
            <span class="menu-arrow"><i class="mdi mdi-chevron-right"></i></span>
        </a>
        <ul class="sub-menu mm-collapse" aria-expanded="false">
            @can('view depreciation-management')
                <li class="nav-item">
                    <a class="nav-link d-flex align-items-center gap-2 font-hold fw-bold transition-base" href="{{ route('depreciation.index') }}">
                        <i class="las la-calculator"></i>{{ __('sidebar.depreciation_management') }}
                    </a>
                </li>
            @endcan

            @can('view Depreciation Schedules')
                <li class="nav-item">
                    <a class="nav-link d-flex align-items-center gap-2 font-hold fw-bold transition-base" href="{{ route('depreciation.schedule') }}">
                        <i class="las la-calendar-alt"></i>{{ __('sidebar.depreciation_schedule') }}
                    </a>
                </li>
            @endcan

            @can('view Depreciation Schedules')
                <li class="nav-item">
                    <a class="nav-link d-flex align-items-center gap-2 font-hold fw-bold transition-base" href="{{ route('depreciation.report') }}">
                        <i class="las la-file-alt"></i>{{ __('sidebar.depreciation_report') }}
                    </a>
                </li>
            @endcan

            @can('create depreciation')
                <li class="nav-item">
                    <a class="nav-link d-flex align-items-center gap-2 font-hold fw-bold transition-base" href="{{ route('multi-vouchers.create', ['type' => 'depreciation']) }}">
                        <i class="las la-file-invoice"></i>{{ __('sidebar.depreciation_entry') }}
                    </a>
                </li>
            @endcan
        </ul>
    </li>
@endcan
