@extends('admin.dashboard')

@section('sidebar')
    @include('components.sidebar.purchases-invoices')
@endsection

@section('content')
    @include('components.breadcrumb', [
        'title' => __('reports::reports.Invoices'),
        'breadcrumb_items' => [['label' => __('reports::reports.Home'), 'url' => route('admin.dashboard')], ['label' => __('reports::reports.Print Barcode')]],
    ])

    <br>

    <livewire:reports.barcode-printing-report :operationId="$id" />
@endsection

