@extends('admin.dashboard')

@section('sidebar')
    @include('components.sidebar.manufacturing')
@endsection

@section('content')
    @include('components.breadcrumb', [
        'title' => __('items.manufacturing_invoice_details'),
        'breadcrumb_items' => [
            ['label' => __('Home'), 'url' => route('admin.dashboard')],
            ['label' => __('common.production_order'), 'url' => route('production-orders.index')],
            ['label' => __('items.manufacturing_invoice_details')],
        ],
    ])

    <div class="container-fluid">
        @livewire('production-orders-management.show-invoice', ['invoiceId' => $invoiceId])
    </div>
@endsection
