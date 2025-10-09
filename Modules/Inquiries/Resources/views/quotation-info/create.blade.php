@extends('admin.dashboard')

@section('sidebar')
    @include('components.sidebar-wrapper', ['sections' => ['inquiries', 'crm', 'accounts']])
@endsection

@section('content')
    @include('components.breadcrumb', [
        'title' => 'Create Quotation Info',
        'items' => [['label' => 'Home', 'url' => route('admin.dashboard')], ['label' => 'Create Quotation Info']],
    ])
    <livewire:inquiries::quotation-info />
@endsection
