@extends('admin.dashboard')

{{-- Dynamic Sidebar --}}
@section('sidebar')
    @include('components.sidebar.items')
@endsection
@section('content')
    @include('components.breadcrumb', [
        'title' => __('Prices'),
        'items' => [['label' => __('Home'), 'url' => route('admin.dashboard')], ['label' => __('Prices')]],
    ])

    <livewire:item-management.prices.manage-prices />
@endsection
