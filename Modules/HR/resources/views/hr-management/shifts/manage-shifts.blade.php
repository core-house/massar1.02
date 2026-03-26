@extends('admin.dashboard')

{{-- Dynamic Sidebar --}}
@section('sidebar')
    @include('components.sidebar.departments')
@endsection
@section('content')
    @include('components.breadcrumb', [
        'title' => __('Shifts'),
        'breadcrumb_items' => [['label' => __('Home'), 'url' => route('admin.dashboard')], ['label' => __('Shifts')]],
    ])


<livewire:hr::hr-management.shifts.manage-shifts />
 
@endsection
