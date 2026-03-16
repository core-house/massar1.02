@extends('admin.dashboard')

{{-- Dynamic Sidebar --}}
@section('sidebar')
    @if(str_contains(request()->path(), 'reports') || str_contains(request()->url(), 'reports'))
        @include('components.sidebar.reports')
    @else
        @include('components.sidebar.items')
    @endif
@endsection
@section('content')
    @include('components.breadcrumb', [
        'title' => __('items.items'),
        'items' => [['label' => __('general.dashboard'), 'url' => route('admin.dashboard')], ['label' => __('items.items')]],
    ])


<livewire:item-management.items.index />
 
@endsection
