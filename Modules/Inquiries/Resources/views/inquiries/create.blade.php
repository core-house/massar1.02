@extends('admin.dashboard')

@section('sidebar')
    @include('components.sidebar.inquiries')
@endsection

@section('content')
    @include('components.breadcrumb', [
        'title' => __('inquiries::inquiries.create_new_inquiry'),
        'breadcrumb_items' => [
            ['label' => __('inquiries::inquiries.home'), 'url' => route('admin.dashboard')],
            ['label' => __('inquiries::inquiries.inquiries'), 'url' => route('inquiries.index')],
            ['label' => __('inquiries::inquiries.create_new_inquiry')],
        ],
    ])
    <livewire:inquiries::create-inquiry />
@endsection
