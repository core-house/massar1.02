@extends('admin.dashboard')

{{-- Dynamic Sidebar --}}
@section('sidebar')
    @include('components.sidebar.items')
@endsection
@section('content')
    @include('components.breadcrumb', [
        'title' => __('items.units'),
        'breadcrumb_items' => [['label' => __('items.item_management'), 'url' => route('admin.dashboard')], ['label' => __('items.units')]],
    ])

    <livewire:item-management.units.manage-units />
@endsection
