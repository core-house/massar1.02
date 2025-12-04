@extends('admin.dashboard')

@section('sidebar')
    @include('components.sidebar.manufacturing')
@endsection

@section('content')
    <livewire:manufacturing::stage-invoices-report />
@endsection
