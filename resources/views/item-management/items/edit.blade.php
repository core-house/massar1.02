@extends('admin.dashboard')

{{-- Dynamic Sidebar --}}
@section('sidebar')
    @include('components.sidebar.items')
@endsection
@section('content')
    @include('components.breadcrumb', [
        'title' => __('items.edit_item'),
        'items' => [['label' => __('general.dashboard'), 'url' => route('admin.dashboard')], ['label' => __('items.items'), 'url' => route('items.index')],['label' => __('items.edit_item')]],
    ]) 


<livewire:item-management.items.edit-item :itemModel="$itemModel" />
 
@endsection
