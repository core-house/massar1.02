@extends('admin.dashboard')
@section('sidebar')
    @include('components.sidebar.manufacturing')
@endsection
@section('content')
    <livewire:manufacturing::manufacturing-order-manager :order_id="$order_id" />
@endsection
