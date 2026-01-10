@extends('admin.dashboard')

{{-- Dynamic Sidebar --}}
@section('sidebar')
    @include('components.sidebar.departments')
@endsection

@section('content')
    @include('components.breadcrumb', [
        'title' => __('navigation.flexible_salary_processing'),
        'items' => [
            ['label' => __('الرئيسية'), 'url' => route('admin.dashboard')],
            ['label' => __('الموارد البشريه')],
            ['label' => __('navigation.flexible_salary_processing'), 'url' => route('hr.flexible-salary.processing.index')],
            ['label' => 'تعديل المعالجة'],
        ],
    ])

    @php
        $processing = \Modules\HR\Models\FlexibleSalaryProcessing::with('employee')->findOrFail(request()->route('processing'));
    @endphp
    <livewire:hr::flexible-salary-processing.edit :processing="$processing" />

@endsection

