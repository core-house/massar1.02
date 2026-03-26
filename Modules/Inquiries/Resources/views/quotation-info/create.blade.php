@extends('admin.dashboard')

@section('sidebar')
    @include('components.sidebar.inquiries')
@endsection

@section('content')
    @include('components.breadcrumb', [
        'title' => 'Create Quotation Info',
        'breadcrumb_items' => [['label' => 'Home', 'url' => route('admin.dashboard')], ['label' => 'Create Quotation Info']],
    ])
    <livewire:inquiries::quotation-info />
@endsection
