@extends('admin.dashboard')

@section('sidebar')
    @include('components.sidebar.installments')
@endsection

@section('content')
    @include('components.breadcrumb', [
        'title' => __('installments::installments.overdue_installments'),
        'breadcrumb_items' => [
            ['label' => __('installments::installments.dashboard'), 'url' => route('admin.dashboard')],
            ['label' => __('installments::installments.overdue_installments')],
        ],
    ])
    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">{{ __('installments::installments.list_of_all_overdue_installments') }}</h4>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped mb-0">
                            <thead class="table-light text-center align-middle">
                                <tr>
                                    <th>{{ __('installments::installments.client') }}</th>
                                    <th>{{ __('installments::installments.plan_number') }}</th>
                                    <th>{{ __('installments::installments.installment_number') }}</th>
                                    <th>{{ __('installments::installments.amount_due') }}</th>
                                    <th>{{ __('installments::installments.due_date') }}</th>
                                    <th>{{ __('installments::installments.days_overdue') }}</th>
                                    <th>{{ __('installments::installments.actions') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($overduePayments as $payment)
                                    <tr class="text-center">
                                        <td>{{ $payment->plan->client->name ?? __('installments::installments.not_applicable') }}</td>
                                        <td>{{ $payment->plan->id }}</td>
                                        <td>{{ $payment->installment_number }}</td>
                                        <td>{{ number_format($payment->amount_due, 2) }}</td>
                                        <td>{{ $payment->due_date->format('Y-m-d') }}</td>
                                        <td>
                                            <span class="badge bg-danger">
                                                {{ $payment->due_date->diffInDays(today()) }}
                                                {{ __('installments::installments.day_s') }}
                                            </span>
                                        </td>
                                        <td>
                                            @can('view Installment Plans')
                                                <a class="btn btn-info btn-sm"
                                                    href="{{ route('installments.plans.show', $payment->plan->id) }}">
                                                    {{ __('installments::installments.view_plan') }}
                                                </a>
                                            @endcan
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="text-center">
                                            <div class="alert alert-success py-3 mb-0">
                                                <i class="las la-check-circle me-2"></i>
                                                {{ __('installments::installments.no_overdue_installments') }}
                                            </div>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    <div class="mt-3">
                        {{ $overduePayments->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
