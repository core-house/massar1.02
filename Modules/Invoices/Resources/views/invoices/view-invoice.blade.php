@extends('admin.dashboard')

@section('sidebar')
    @if (in_array($type, [10, 12, 14, 16, 22, 26]))
        @include('components.sidebar.sales-invoices')
    @elseif (in_array($type, [11, 13, 15, 17, 24, 25]))
        @include('components.sidebar.purchases-invoices')
    @elseif (in_array($type, [18, 19, 20, 21]))
        @include('components.sidebar.inventory-invoices')
    @endif
@endsection

@section('content')
    @php
        $titles = [
            10 => __('invoices::invoices.sales_invoice'),
            11 => __('invoices::invoices.purchase_invoice'),
            12 => __('invoices::invoices.sales_return'),
            13 => __('invoices::invoices.purchase_return'),
            14 => __('invoices::invoices.sales_order'),
            15 => __('invoices::invoices.purchase_order'),
            16 => __('invoices::invoices.quotation_to_customer'),
            17 => __('invoices::invoices.quotation_from_supplier'),
            18 => __('invoices::invoices.damaged_goods_invoice'),
            19 => __('invoices::invoices.dispatch_order'),
            20 => __('invoices::invoices.addition_order'),
            21 => __('invoices::invoices.store_to_store_transfer'),
            22 => __('invoices::invoices.booking_order'),
            24 => __('invoices::invoices.service_invoice'),
            25 => __('invoices::invoices.requisition'),
            26 => __('invoices::invoices.pricing_agreement'),
        ];

        $permissionName = 'view ' . ($titles[$type] ?? __('invoices::invoices.unknown'));
    @endphp


    <livewire:invoices.view-invoice :operationId="$operationId" />
@endsection
