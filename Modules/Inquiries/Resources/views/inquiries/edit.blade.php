@extends('admin.dashboard')

@section('sidebar')
    @include('components.sidebar.inquiries')
@endsection

@section('content')
    @include('components.breadcrumb', [
        'title' => __('inquiries::inquiries.edit_inquiry'),
        'breadcrumb_items' => [
            ['label' => __('Home'), 'url' => route('admin.dashboard')],
            ['label' => __('inquiries::inquiries.inquiries'), 'url' => route('inquiries.index')],
            ['label' => __('Edit')],
        ],
    ])
    <livewire:inquiries::edit-inquiry :id="$id" />
@endsection
