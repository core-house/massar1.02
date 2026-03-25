@extends('admin.dashboard')

{{-- Dynamic Sidebar --}}
@section('sidebar')
    @include('components.sidebar.items')
@endsection
@section('content')
    @include('components.breadcrumb', [
        'title' => __('items.add_new_item'),
        'breadcrumb_items' => [['label' => __('general.dashboard'), 'url' => route('admin.dashboard')], ['label' => __('items.items'), 'url' => route('items.index')],['label' => __('items.add_new_item')]],
    ]) 

<livewire:item-management.items.create-item />
 
@endsection
