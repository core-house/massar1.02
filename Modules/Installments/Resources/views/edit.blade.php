@extends('admin.dashboard')
@section('sidebar')
    @include('components.sidebar.installments')
@endsection

@section('content')
    @include('components.breadcrumb', [
        'title' => __('installments::installments.edit_installment_plan'),
        'breadcrumb_items' => [
            ['label' => __('installments::installments.dashboard'), 'url' => route('admin.dashboard')],
            [
                'label' => __('installments::installments.installment_plans'),
                'url' => route('installments.plans.index'),
            ],
            ['label' => __('installments::installments.edit_setup')],
        ],
    ])

    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                @livewire('installments::edit-installment-plan', ['plan' => $plan])
            </div>
        </div>
    </div>
@endsection
