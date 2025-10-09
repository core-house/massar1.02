@extends('admin.dashboard')

{{-- Dynamic Sidebar --}}
@section('sidebar')
    @include('components.sidebar.sales-invoices')
    @include('components.sidebar.purchases-invoices')
    @include('components.sidebar.inventory-invoices')
@endsection
@section('content')
    <livewire:invoices.view-invoice :operationId="$operationId" />
@endsection
