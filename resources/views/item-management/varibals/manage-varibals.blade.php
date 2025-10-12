@extends('admin.dashboard')

{{-- Dynamic Sidebar --}}
@section('sidebar')
    @include('components.sidebar.items')
@endsection
@section('content')
    @include('components.breadcrumb', [
        'title' => __('Varibals'),
        'items' => [['label' => __('Home'), 'url' => route('admin.dashboard')], ['label' => __('Varibals')]],
    ])

    <livewire:varibal-management />
@endsection
