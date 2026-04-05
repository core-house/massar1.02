@php
    $purchases = [
        11 => ['label' => 'Purchase Invoice', 'label_key' => 'invoices::invoices.purchase_invoice', 'icon' => 'las la-file-invoice'],
        13 => ['label' => 'Purchase Return', 'label_key' => 'invoices::invoices.purchase_return', 'icon' => 'las la-undo'],
        15 => ['label' => 'Purchase Order', 'label_key' => 'invoices::invoices.purchase_order', 'icon' => 'las la-shopping-basket'],
        17 => ['label' => 'Quotation from Supplier', 'label_key' => 'invoices::invoices.quotation_from_supplier', 'icon' => 'las la-file-contract'],
        24 => ['label' => 'Service Invoice', 'label_key' => 'invoices::invoices.service_invoice', 'icon' => 'las la-tools'],
        25 => ['label' => 'Requisition', 'label_key' => 'invoices::invoices.requisition', 'icon' => 'las la-clipboard-list'],
    ];
    $currentType = request('type');
@endphp

<li class="menu-title mt-2">{{ __('invoices::invoices.purchases_module') }}</li>

@can('view Discounts')
    <li class="nav-item">
        <a class="nav-link d-flex align-items-center gap-2 font-hold fw-bold transition-base {{ request()->routeIs('discounts.general-statistics') ? 'active' : '' }}" 
           href="{{ route('discounts.general-statistics') }}"
           style="{{ request()->routeIs('discounts.general-statistics') ? 'background-color: rgba(52, 211, 163, 0.1); color: #34d3a3;' : '' }}">
            <i class="las la-percentage font-18"></i>{{ __('invoices::invoices.general_discounts_statistics') }}
        </a>
    </li>
@endcan

@can('view Earned Discounts')
    <li class="nav-item">
        <a class="nav-link d-flex align-items-center gap-2 font-hold fw-bold transition-base {{ request()->routeIs('discounts.index') && request('type') == 31 ? 'active' : '' }}" 
           href="{{ route('discounts.index', ['type' => 31]) }}"
           style="{{ request()->routeIs('discounts.index') && request('type') == 31 ? 'background-color: rgba(52, 211, 163, 0.1); color: #34d3a3;' : '' }}">
            <i class="las la-percent font-18"></i>{{ __('navigation.earned_discounts') }}
        </a>
    </li>
@endcan

<li class="nav-item">
    <a class="nav-link d-flex align-items-center gap-2 font-hold fw-bold transition-base {{ request()->routeIs('purchases.statistics') ? 'active' : '' }}" 
       href="{{ route('purchases.statistics') }}"
       style="{{ request()->routeIs('purchases.statistics') ? 'background-color: rgba(52, 211, 163, 0.1); color: #34d3a3;' : '' }}">
        <i class="las la-broadcast-tower font-18"></i>{{ __('invoices::invoices.purchases_statistics') }}
    </a>
</li>

@can('view Invoice Templates')
    <li class="nav-item">
        <a class="nav-link d-flex align-items-center gap-2 font-hold fw-bold transition-base {{ request()->routeIs('invoice-templates.*') ? 'active' : '' }}" 
           href="{{ route('invoice-templates.index') }}"
           style="{{ request()->routeIs('invoice-templates.*') ? 'background-color: rgba(52, 211, 163, 0.1); color: #34d3a3;' : '' }}">
            <i class="las la-layer-group font-18"></i>{{ __('invoices::templates.invoice_templates') }}
        </a>
    </li>
@endcan

<li class="menu-title mt-3">{{ __('invoices::invoices.invoices_sidebar') }}</li>

@foreach ($purchases as $t => $data)
    @can('view ' . $data['label'])
        <li class="nav-item">
            <a class="nav-link d-flex align-items-center gap-2 font-hold fw-bold transition-base {{ $currentType == $t ? 'active' : '' }}" 
               href="{{ route('invoices.index', ['type' => $t]) }}"
               style="{{ $currentType == $t ? 'background-color: rgba(52, 211, 163, 0.1); color: #34d3a3;' : '' }}">
                <i class="{{ $data['icon'] }} font-18"></i> {{ __($data['label_key']) }}
            </a>
        </li>
    @endcan
@endforeach

<li class="nav-item">
    <a class="nav-link d-flex align-items-center gap-2 font-hold fw-bold transition-base {{ request()->routeIs('invoices.track.search') ? 'active' : '' }}" 
       href="{{ route('invoices.track.search') }}"
       style="{{ request()->routeIs('invoices.track.search') ? 'background-color: rgba(52, 211, 163, 0.1); color: #34d3a3;' : '' }}">
        <i class="las la-search-location font-18"></i> {{ __('invoices::invoices.track_invoice_path') }}
    </a>
</li>
