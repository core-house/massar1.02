@extends('admin.dashboard')

@section('sidebar')
    @include('components.sidebar.installments')
@endsection

@section('content')
    @include('components.breadcrumb', [
        'title' => __('خطط التقسيط'),
        'items' => [
            ['label' => __('Dashboard'), 'url' => route('admin.dashboard')],
            ['label' => __('خطط التقسيط')],
        ],
    ])
    <div class="row">
        <div class="col-lg-12">
            <a href="{{ route('installments.plans.create') }}" type="button" class="btn btn-primary fw-bold">
                [translate:إضافة خطة تقسيط جديدة]
                <i class="fas fa-plus me-2"></i>
            </a>
            <br>
            <br>
            <div class="card">
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped mb-0">
                            <thead class="table-light text-center align-middle">
                                <tr>
                                    <th>#</th>
                                    <th>[translate:العميل]</th>
                                    <th>[translate:المبلغ الإجمالي]</th>
                                    <th>[translate:عدد الأقساط]</th>
                                    <th>[translate:تاريخ البدء]</th>
                                    <th>[translate:الحالة]</th>
                                    <th>[translate:الإجراءات]</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($installmentPlans as $plan)
                                    <tr class="text-center">
                                        <td> {{ $plan->id }} </td>
                                        <td>{{ $plan->client->name ?? 'N/A' }}</td>
                                        <td>{{ number_format($plan->total_amount, 2) }}</td>
                                        <td>{{ $plan->number_of_installments }}</td>
                                        <td>{{ $plan->start_date }}</td>
                                        <td><span class="badge bg-success">{{ $plan->status }}</span></td>
                                        <td>
                                            <a class="btn btn-info btn-icon-square-sm"
                                                href="{{ route('installments.plans.show', $plan->id) }}">
                                                <i class="las la-eye"></i>
                                            </a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="text-center">
                                            <div class="alert alert-info py-3 mb-0">
                                                <i class="las la-info-circle me-2"></i>
                                                {{ __('No data available') }}
                                            </div>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
