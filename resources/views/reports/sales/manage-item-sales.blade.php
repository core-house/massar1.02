@extends('admin.dashboard')

@section('sidebar')
    @include('components.sidebar.accounts')
    @include('components.sidebar.sales-invoices')
    @include('components.sidebar.purchases-invoices')
    @include('components.sidebar.items')
@endsection
@section('content')
    @include('components.breadcrumb', [
        'title' => __('Item Sales'),
        'items' => [['label' => __('Home'), 'url' => route('admin.dashboard')], ['label' => __('Item Sales')]],
    ])


<livewire:reports.sales.manage-item-sales />
 
@endsection
