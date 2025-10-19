@extends('admin.dashboard')
@section('sidebar')
    @include('components.sidebar.manufacturing')
@endsection
@section('content')
    <livewire:manufacturing::manufacturing-invoice />
@endsection
