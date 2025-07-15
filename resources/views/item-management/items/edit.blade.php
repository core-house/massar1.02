@extends('admin.dashboard')
@section('content')
    @include('components.breadcrumb', [
        'title' => __('Edit Item'),
        'items' => [['label' => __('Home'), 'url' => route('admin.dashboard')], ['label' => __('Items'), 'url' => route('items.index')],['label' => __('Edit Item')]],
    ])


<livewire:item-management.items.edit-item :itemModel="$itemModel" />
 
@endsection
