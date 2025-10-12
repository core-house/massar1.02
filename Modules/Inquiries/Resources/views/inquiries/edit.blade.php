@extends('admin.dashboard')

@section('sidebar')
    @include('components.sidebar.inquiries')
    @include('components.sidebar.crm')
    @include('components.sidebar.accounts')
@endsection

@section('content')
    @include('components.breadcrumb', [
        'title' => 'تعديل استفسار ',
        'items' => [
            ['label' => 'الرئيسية', 'url' => route('admin.dashboard')],
            ['label' => 'الاستفسارات', 'url' => route('inquiries.index')],
            ['label' => 'تعديل'],
        ],
    ])
    <livewire:inquiries::edit-inquiry :id="$id" />
@endsection
