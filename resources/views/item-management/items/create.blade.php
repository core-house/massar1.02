@extends('admin.dashboard')

{{-- Dynamic Sidebar --}}
@section('sidebar')
    @include('components.sidebar.items')
@endsection
@section('content')
    {{-- @include('components.breadcrumb', [
        'title' => __('Create Items'),
        'items' => [['label' => __('Home'), 'url' => route('admin.dashboard')], ['label' => __('Items'), 'url' => route('items.index')],['label' => __('Create Items')]],
    ]) --}}


<livewire:item-management.items.create-item />
 
@endsection
