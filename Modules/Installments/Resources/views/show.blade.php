@extends('admin.dashboard')

@section('sidebar')
    @include('components.sidebar.installments')
@endsection

@section('content')
    @include('components.breadcrumb', [
        'title' => __('installments::installments.view_installment_plan'),
        'breadcrumb_items' => [
            ['label' => __('installments::installments.dashboard'), 'url' => route('admin.dashboard')],
            ['label' => __('installments::installments.installment_plans'), 'url' => route('installments.plans.index')],
            ['label' => __('installments::installments.view_plan_no') . ' ' . $plan->id],
        ],
    ])

    @livewire('installments::show-installment-plan', ['plan' => $plan])
@endsection
