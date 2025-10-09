@extends('admin.dashboard')

{{-- Dynamic Sidebar --}}
@section('sidebar')
    @include('components.sidebar.manufacturing')
@endsection

@section('content')
    <livewire:production-orders-management.create />
@endsection
