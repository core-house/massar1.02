@extends('admin.dashboard')

{{-- Dynamic Sidebar --}}
@section('sidebar')
    @include('components.sidebar.journals')
@endsection

@section('content')
    @include('components.breadcrumb', [
        'title' => __('Start Balance'),
        'breadcrumb_items' => [['label' => __('Home'), 'url' => route('admin.dashboard')], ['label' => __('Start Balance')]],
    ])
<livewire:accounts.startBalance.manage-start-balance />
@endsection
