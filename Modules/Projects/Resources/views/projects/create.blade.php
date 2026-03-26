@extends('admin.dashboard')
@section('sidebar')
    @include('components.sidebar.projects')
@endsection

@section('content')
    @include('components.breadcrumb', [
        'title' => __('add_new_project'),
        'breadcrumb_items' => [
            ['label' => __('Home'), 'url' => route('admin.dashboard')],
            ['label' => __('Projects'), 'url' => route('projects.index')],
            ['label' => __('add_new_project')]
        ],
    ])
    @livewire('projects::projects-create')
@endsection
