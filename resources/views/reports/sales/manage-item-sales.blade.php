@extends('admin.dashboard')

@section('sidebar')
    @include('components.sidebar-wrapper', ['sections' => ['accounts', 'sales-invoices', 'purchases-invoices', 'items']])
@endsection
@section('content')
    @include('components.breadcrumb', [
        'title' => __('Item Sales'),
        'items' => [['label' => __('Home'), 'url' => route('admin.dashboard')], ['label' => __('Item Sales')]],
    ])


<livewire:reports.sales.manage-item-sales />
 
@endsection
