@extends('admin.dashboard')
@section('sidebar')
    @include('components.sidebar.manufacturing')
@endsection
@section('content')
    <livewire:manufacturing::manufacturing-order-manager :viewing_order_id="$order_id" view_mode="stages" />
@endsection
