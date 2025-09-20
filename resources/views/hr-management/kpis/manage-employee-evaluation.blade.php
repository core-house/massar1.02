@extends('admin.dashboard')
@section('content')
    {{-- @include('components.breadcrumb', [
        'title' => __('Employee Evaluations'),
        'items' => [['label' => __('Home'), 'url' => route('admin.dashboard')], ['label' => __('HR Management')], ['label' => __('Employee Evaluations')]],
    ]) --}}


<livewire:hr-management.kpis.manage-employee-evaluation />
 
@endsection
