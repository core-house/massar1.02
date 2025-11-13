@extends('admin.dashboard')

@section('sidebar')
    @include('components.sidebar.installments')
@endsection

@section('content')
    @include('components.breadcrumb', [
        'title' => __('إعداد خطة تقسيط جديدة'),
        'items' => [
            ['label' => __('Dashboard'), 'url' => route('admin.dashboard')],
            ['label' => __('خطط التقسيط'), 'url' => route('installments.plans.index')],
            ['label' => __('إعداد جديد')],
        ],
    ])

    <div class="row">
        <div class="col-12">
            @livewire('installments::create-installment-plan')
        </div>
    </div>
@endsection
