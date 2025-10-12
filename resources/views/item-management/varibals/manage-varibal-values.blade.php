@extends('admin.dashboard')

{{-- Dynamic Sidebar --}}
@section('sidebar')
    @include('components.sidebar.items')
@endsection
@section('content')
    @include('components.breadcrumb', [
        'title' => __('Varibal Values'),
        'items' => [['label' => __('Home'), 'url' => route('admin.dashboard')], ['label' => __('Varibal Values')]],
    ])

    <livewire:varibal-value-management :varibalId="$varibalId" />
@endsection
