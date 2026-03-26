@extends('admin.dashboard')

{{-- Dynamic Sidebar --}}
@section('sidebar')
    @include('components.sidebar.departments')
@endsection
@section('content')
    {{-- @include('components.breadcrumb', [
        'title' => __('KPIs'),
        'breadcrumb_items' => [['label' => __('Home'), 'url' => route('admin.dashboard')], ['label' => __('KPIs')]],
    ]) --}}


<livewire:hr::hr-management.kpis.manage-kpi />
 
@endsection
