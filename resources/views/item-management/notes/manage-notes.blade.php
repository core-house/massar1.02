@extends('admin.dashboard')

{{-- Dynamic Sidebar --}}
@section('sidebar')
    @include('components.sidebar.items')
@endsection
@section('content')
    @include('components.breadcrumb', [
        'title' => __('Notes'),
        'breadcrumb_items' => [['label' => __('Home'), 'url' => route('admin.dashboard')], ['label' => __('Notes')]],
    ])

    <livewire:item-management.notes.manage-notes />
@endsection
