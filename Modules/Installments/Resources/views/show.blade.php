@extends('admin.dashboard')

@section('sidebar')
    @include('components.sidebar.installments')
@endsection

@section('content')
    @include('components.breadcrumb', [
        'title' => __('عرض خطة التقسيط'),
        'items' => [
            ['label' => __('Dashboard'), 'url' => route('admin.dashboard')],
            ['label' => __('خطط التقسيط'), 'url' => route('installments.plans.index')],
            ['label' => __('عرض الخطة رقم') . ' ' . $plan->id],
        ],
    ])

    {{-- استدعاء مكون اللايف واير وتمرير الخطة له --}}
    @livewire('installments::show-installment-plan', ['plan' => $plan])
@endsection
