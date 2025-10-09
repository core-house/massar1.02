@extends('admin.dashboard')

{{-- Dynamic Sidebar --}}
@section('sidebar')
    @include('components.sidebar.items')
@endsection
@section('content')
    @include('components.breadcrumb', [
        'title' => __('Units'),
        'items' => [['label' => __('Home'), 'url' => route('admin.dashboard')], ['label' => __('Units')]],
    ])

    <livewire:item-management.units.manage-units />
@endsection
