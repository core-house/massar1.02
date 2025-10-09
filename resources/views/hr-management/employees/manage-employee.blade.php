@extends('admin.dashboard')

{{-- Dynamic Sidebar --}}
@section('sidebar')
    @include('components.sidebar-wrapper', ['sections' => ['departments', 'permissions']])
@endsection
@section('content')
    {{-- @include('components.breadcrumb', [
        'title' => __('Employees'),
        'items' => [['label' => __('Home'), 'url' => route('admin.dashboard')], ['label' => __('Employees')]],
    ]) --}}

<livewire:hr-management.employees.manage-employee />
 
@endsection
