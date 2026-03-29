@extends('admin.dashboard')
@section('sidebar')
    @include('components.sidebar.projects')
@endsection

@section('content')
    @include('components.breadcrumb', [
        'title' => __('projects::projects.add_new_project'),
        'breadcrumb_items' => [
            ['label' => __('projects::projects.home'), 'url' => route('admin.dashboard')],
            ['label' => __('projects::projects.projects'), 'url' => route('projects.index')],
            ['label' => __('projects::projects.add_new_project')]
        ],
    ])
    @livewire('projects::projects-create')
@endsection
