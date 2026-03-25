@extends('admin.dashboard')

@section('sidebar')
    @include('components.sidebar.items')
@endsection

@section('content')
    <livewire:item-management.items.manage-prices />
@endsection
