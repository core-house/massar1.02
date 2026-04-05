@extends('admin.dashboard')

{{-- Dynamic Sidebar --}}
@section('sidebar')
    @include('components.sidebar.departments')
@endsection
@section('content')
    @include('components.breadcrumb', [
        'title' => __('hr::hr.departments'),
        'breadcrumb_items' => [['label' => __('hr::hr.home'), 'url' => route('admin.dashboard')], ['label' => __('hr::hr.departments')]],
    ])


<livewire:hr::hr-management.departments.manage-department />

@endsection
