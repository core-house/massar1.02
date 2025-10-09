@extends('admin.dashboard')

{{-- Dynamic Sidebar --}}
@section('sidebar')
    @include('components.sidebar.items')
@endsection
@section('content')
    {{-- @include('components.breadcrumb', [
        'title' => __('Items'),
        'items' => [['label' => __('Home'), 'url' => route('admin.dashboard')], ['label' => __('Items')]],
    ]) --}}


<livewire:item-management.items.index />
 
@endsection
