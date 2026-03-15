@can('view depreciation')
    <li class="nav-item">
        <a class="nav-link d-flex align-items-center gap-2 font-hold fw-bold transition-base" href="{{ route('depreciation.index') }}">
            <i class="las la-list"></i>{{ __('sidebar.assets_list') }}
        </a>
    </li>
@endcan

@can('view depreciation')
    <li class="nav-item">
        <a class="nav-link d-flex align-items-center gap-2 font-hold fw-bold transition-base" href="{{ route('multi-vouchers.create', ['type' => 'depreciation']) }}">
            <i class="las la-chart-line-down"></i>{{ __('navigation.depreciation') }}
        </a>
    </li>
@endcan

@can('view sell-asset')
    <li class="nav-item">
        <a class="nav-link d-flex align-items-center gap-2 font-hold fw-bold transition-base" href="{{ route('multi-vouchers.create', ['type' => 'sell_asset']) }}">
            <i class="las la-hand-holding-usd"></i>{{ __('navigation.sell_asset') }}
        </a>
    </li>
@endcan

@can('view buy-asset')
    <li class="nav-item">
        <a class="nav-link d-flex align-items-center gap-2 font-hold fw-bold transition-base" href="{{ route('multi-vouchers.create', ['type' => 'buy_asset']) }}">
            <i class="las la-shopping-cart"></i>{{ __('navigation.buy_asset') }}
        </a>
    </li>
@endcan

@can('view increase-asset-value')
    <li class="nav-item">
        <a class="nav-link d-flex align-items-center gap-2 font-hold fw-bold transition-base" href="{{ route('multi-vouchers.create', ['type' => 'increase_asset_value']) }}">
            <i class="las la-arrow-up"></i>{{ __('navigation.increase_asset_value') }}
        </a>
    </li>
@endcan

@can('view decrease-asset-value')
    <li class="nav-item">
        <a class="nav-link d-flex align-items-center gap-2 font-hold fw-bold transition-base" href="{{ route('multi-vouchers.create', ['type' => 'decrease_asset_value']) }}">
            <i class="las la-arrow-down"></i>{{ __('navigation.decrease_asset_value') }}
        </a>
    </li>
@endcan
