@extends('admin.dashboard')

@section('sidebar')
    @include('components.sidebar.inquiries')
@endsection

@section('content')
    @include('components.breadcrumb', [
        'title' => __('inquiries::inquiries.create_quotation_info'),
        'breadcrumb_items' => [['label' => __('inquiries::inquiries.home') ,'url' => route('admin.dashboard')], ['label' => __('inquiries::inquiries.create_quotation_info')]],
    ])
    <livewire:inquiries::quotation-info />
@endsection
