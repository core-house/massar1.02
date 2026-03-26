@extends('admin.dashboard')

{{-- Dynamic Sidebar --}}
@section('sidebar')
    @include('components.sidebar.items')
@endsection
@section('content')
    @include('components.breadcrumb', [
        'title' => __('items.prices'),
        'breadcrumb_items' => [['label' => __('items.item_management'), 'url' => route('admin.dashboard')], ['label' => __('items.prices')]],
    ])

    <livewire:item-management.prices.manage-prices />
@endsection
