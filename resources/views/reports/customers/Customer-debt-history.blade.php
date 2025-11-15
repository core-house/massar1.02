@extends('admin.dashboard')

@section('sidebar')
    @include('components.sidebar.accounts')
    @include('components.sidebar.sales-invoices')
    @include('components.sidebar.purchases-invoices')
    @include('components.sidebar.items')
@endsection
@section('content')
    @include('components.breadcrumb', [
        'title' => __('Customer Debt History'),
        'items' => [['label' => __('Home'), 'url' => route('admin.dashboard')], ['label' => __('Customer Debt History')]],
    ])


<livewire:reports.customers.customer-debt-history />
 
@endsection
