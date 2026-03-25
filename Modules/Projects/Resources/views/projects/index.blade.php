@extends('admin.dashboard')
@section('sidebar')
    @include('components.sidebar.projects')
@endsection
@section('content')
    @include('components.breadcrumb', [
        'title' => __('Projects'),
        'breadcrumb_items' => [
            ['label' => __('Home'), 'url' => route('admin.dashboard')],
            ['label' => __('Projects')]
        ],
    ])
    @livewire('projects::projects-index')
@endsection
