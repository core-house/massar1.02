@extends('admin.dashboard')

@section('sidebar')
    @include('components.sidebar-wrapper', ['sections' => ['accounts', 'sales-invoices', 'purchases-invoices', 'items']])
@endsection

@section('content')
    @livewire('reports.general-journal-details')
@endsection 