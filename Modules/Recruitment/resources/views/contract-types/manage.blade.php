@extends('admin.dashboard')

@section('sidebar')
    @include('components.sidebar.departments')
@endsection

@section('content')
    @include('components.breadcrumb', [
        'title' => __('recruitment.contract_types'),
        'items' => [
            ['label' => __('الرئيسيه'), 'url' => route('admin.dashboard')],
            ['label' => __('recruitment.recruitment_management')],
            ['label' => __('recruitment.contract_types')]
        ],
    ])

    <livewire:contract-types.manage-typs />
@endsection

