@extends('admin.dashboard')

{{-- Dynamic Sidebar --}}
@section('sidebar')
    @include('components.sidebar.departments')
@endsection
@section('content')
    @include('components.breadcrumb', [
        'title' => __('Countries'),
        'breadcrumb_items' => [['label' => __('Home'), 'url' => route('admin.dashboard')], ['label' => __('Countries')]],
    ])


<livewire:hr::hr-management.addresses.manage-countries />

@endsection
