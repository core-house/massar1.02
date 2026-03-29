@extends('admin.dashboard')
@section('sidebar')
    @include('components.sidebar.projects')
@endsection

@section('content')
    @include('components.breadcrumb', [
        'title' => __('projects::projects.edit_project'),
        'breadcrumb_items' => [
            ['label' => __('projects::projects.home'), 'url' => route('admin.dashboard')],
            ['label' => __('projects::projects.projects'), 'url' => route('projects.index')],
            ['label' => __('projects::projects.edit_project')]
        ],
    ])
    @livewire('projects::projects-edit', ['project' => $project])
@endsection
