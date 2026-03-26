@extends('admin.dashboard')

{{-- Dynamic Sidebar --}}
@section('sidebar')
    @include('components.sidebar.items')
@endsection
@section('content')
    @include('components.breadcrumb', [
        'title' => __('items.notes_details'),
        'breadcrumb_items' => [['label' => __('items.item_management'), 'url' => route('admin.dashboard')], ['label' => __('items.notes_details')]],
    ])

    <livewire:item-management.notes.note-details :noteId="$noteId" />
@endsection
