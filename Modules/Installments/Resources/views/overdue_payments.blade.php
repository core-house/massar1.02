@extends('admin.dashboard')

@section('sidebar')
    {{-- @include('components.sidebar.your-sidebar') --}}
@endsection

@section('content')
    @include('components.breadcrumb', [
        'title' => __('الأقساط المتأخرة'),
        'items' => [
            ['label' => __('Dashboard'), 'url' => route('admin.dashboard')],
            ['label' => __('الأقساط المتأخرة')],
        ],
    ])
    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">[translate:قائمة بجميع الأقساط المتأخرة في النظام]</h4>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped mb-0">
                            <thead class="table-light text-center align-middle">
                                <tr>
                                    <th>[translate:العميل]</th>
                                    <th>[translate:رقم الخطة]</th>
                                    <th>[translate:رقم القسط]</th>
                                    <th>[translate:المبلغ المستحق]</th>
                                    <th>[translate:تاريخ الاستحقاق]</th>
                                    <th>[translate:أيام التأخير]</th>
                                    <th>[translate:الإجراءات]</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($overduePayments as $payment)
                                    <tr class="text-center">
                                        <td>{{ $payment->plan->client->name ?? 'N/A' }}</td>
                                        <td>{{ $payment->plan->id }}</td>
                                        <td>{{ $payment->installment_number }}</td>
                                        <td>{{ number_format($payment->amount_due, 2) }}</td>
                                        <td>{{ $payment->due_date }}</td>
                                        <td>
                                            <span class="badge bg-danger">
                                                {{ \Carbon\Carbon::parse($payment->due_date)->diffInDays(now()) }}
                                                [translate:يوم]
                                            </span>
                                        </td>
                                        <td>
                                            <a class="btn btn-info btn-sm"
                                                href="{{ route('installments.plans.show', $payment->plan->id) }}">
                                                [translate:عرض الخطة]
                                            </a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="text-center">
                                            <div class="alert alert-success py-3 mb-0">
                                                <i class="las la-check-circle me-2"></i>
                                                [translate:لا توجد أقساط متأخرة حالياً.]
                                            </div>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    <!-- روابط التقسيم للصفحات -->
                    <div class="mt-3">
                        {{ $overduePayments->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
