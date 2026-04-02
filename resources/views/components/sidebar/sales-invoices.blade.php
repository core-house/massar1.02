@php
    $sales = [
        10 => ['label' => 'Sales Invoice', 'label_key' => 'invoices::invoices.sales_invoice', 'icon' => 'las la-file-invoice'],
        12 => ['label' => 'Sales Return', 'label_key' => 'invoices::invoices.sales_return', 'icon' => 'las la-undo-alt'],
        14 => ['label' => 'Sales Order', 'label_key' => 'invoices::invoices.sales_order', 'icon' => 'las la-shopping-cart'],
        16 => ['label' => 'Quotation to Customer', 'label_key' => 'invoices::invoices.quotation_to_customer', 'icon' => 'las la-file-contract'],
        22 => ['label' => 'Booking Order', 'label_key' => 'invoices::invoices.booking_order', 'icon' => 'las la-calendar-check'],
        26 => ['label' => 'Pricing Agreement', 'label_key' => 'invoices::invoices.pricing_agreement', 'icon' => 'las la-handshake'],
    ];
    $currentType = request('type');
@endphp

<li class="menu-title mt-2">{{ __('invoices::invoices.sales_module') }}</li>

@can('view Discounts')
    <li class="nav-item">
        <a class="nav-link d-flex align-items-center gap-2 font-hold fw-bold transition-base {{ request()->routeIs('discounts.general-statistics') ? 'active' : '' }}" 
           href="{{ route('discounts.general-statistics') }}"
           style="{{ request()->routeIs('discounts.general-statistics') ? 'background-color: rgba(52, 211, 163, 0.1); color: #34d3a3;' : '' }}">
            <i class="las la-percentage font-18"></i>{{ __('invoices::invoices.general_discounts_statistics') }}
        </a>
    </li>
@endcan

@can('view Allowed Discounts')
    <li class="nav-item">
        <a class="nav-link d-flex align-items-center gap-2 font-hold fw-bold transition-base {{ request()->routeIs('discounts.index') && request('type') == 30 ? 'active' : '' }}" 
           href="{{ route('discounts.index', ['type' => 30]) }}"
           style="{{ request()->routeIs('discounts.index') && request('type') == 30 ? 'background-color: rgba(52, 211, 163, 0.1); color: #34d3a3;' : '' }}">
            <i class="las la-percent font-18"></i>{{ __('navigation.allowed_discounts') }}
        </a>
    </li>
@endcan

@can('create Allowed Discounts')
    <li class="nav-item">
        <a class="nav-link d-flex align-items-center gap-2 font-hold fw-bold transition-base {{ request()->routeIs('discounts.create') && request('type') == 30 ? 'active' : '' }}" 
           href="{{ route('discounts.create', ['type' => 30, 'q' => md5(30)]) }}"
           style="{{ request()->routeIs('discounts.create') && request('type') == 30 ? 'background-color: rgba(52, 211, 163, 0.1); color: #34d3a3;' : '' }}">
            <i class="las la-plus-circle font-18"></i>{{ __('navigation.allowed_discount') }}
        </a>
    </li>
@endcan

<li class="nav-item">
    <a class="nav-link d-flex align-items-center gap-2 font-hold fw-bold transition-base {{ request()->routeIs('sales.statistics') ? 'active' : '' }}" 
       href="{{ route('sales.statistics') }}"
       style="{{ request()->routeIs('sales.statistics') ? 'background-color: rgba(52, 211, 163, 0.1); color: #34d3a3;' : '' }}">
        <i class="las la-chart-line font-18"></i>{{ __('invoices::invoices.sales_statistics') }}
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

@foreach ($sales as $t => $data)
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
