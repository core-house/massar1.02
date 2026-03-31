@extends('admin.dashboard')
@section('sidebar')
    @include('components.sidebar.projects')
@endsection
@section('content')
    @include('components.breadcrumb', [
        'title' => __('projects::projects.projects'),
        'breadcrumb_items' => [
            ['label' => __('projects::projects.home'), 'url' => route('admin.dashboard')],
            ['label' => __('projects::projects.projects')]
        ],
    ])
    @livewire('projects::projects-index')
@endsection
