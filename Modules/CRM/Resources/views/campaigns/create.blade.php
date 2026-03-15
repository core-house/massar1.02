@extends('admin.dashboard')

@section('sidebar')
    @include('components.sidebar.crm')
@endsection

@section('content')
    @include('components.breadcrumb', [
        'title' => __('crm::crm.new_campaign'),
        'items' => [
            ['label' => __('crm::crm.dashboard'), 'url' => route('admin.dashboard')],
            ['label' => __('crm::crm.marketing_campaigns'), 'url' => route('campaigns.index')],
            ['label' => __('crm::crm.new_campaign')]
        ],
    ])
    @livewire('crm::campaign-form')
@endsection
