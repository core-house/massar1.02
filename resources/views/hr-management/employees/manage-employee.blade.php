@extends('admin.dashboard')
@section('content')
    {{-- @include('components.breadcrumb', [
        'title' => __('Employees'),
        'items' => [['label' => __('Home'), 'url' => route('admin.dashboard')], ['label' => __('Employees')]],
    ]) --}}

<livewire:hr-management.employees.manage-employee />
 
@endsection
