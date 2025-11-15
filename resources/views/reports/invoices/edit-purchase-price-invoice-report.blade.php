@extends('admin.dashboard')

@section('sidebar')
    @include('components.sidebar.purchases-invoices')
@endsection
@section('content')
    @include('components.breadcrumb', [
        'title' => __('الفواتير'),
        'items' => [
            ['label' => __('الرئيسيه'), 'url' => route('admin.dashboard')],
            ['label' => __('تعديل سعر البيع لاصناف الفاتوره ')],
        ],
    ])
    <br>
    <livewire:reports.purchase-invoice-items-pricing :operationId="$id" />
@endsection
