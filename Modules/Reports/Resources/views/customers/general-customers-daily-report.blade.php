@extends('admin.dashboard')

@section('sidebar')
    @include('components.sidebar.reports')
@endsection

@section('content')
    <div class="container">
        <div class="card">
            <div class="card-head">
                <h2>{{ __('reports::reports.customers_daily_report') }}</h2>
            </div>
            <div class="card-body">
                <div class="row mb-3">
                    <div class="col-md-3">
                        <label for="from_date">{{ __('reports::reports.from_date') }}:</label>
                        <input type="date" id="from_date" class="form-control" wire:model="fromDate">
                    </div>
                    <div class="col-md-3">
                        <label for="to_date">{{ __('reports::reports.to_date') }}:</label>
                        <input type="date" id="to_date" class="form-control" wire:model="toDate">
                    </div>
                    <div class="col-md-3">
                        <label for="customer_id">{{ __('reports::reports.customer') }}:</label>
                        <select id="customer_id" class="form-control" wire:model="customerId">
                            <option value="">{{ __('reports::reports.all_customers') }}</option>
                            @foreach ($customers as $customer)
                                <option value="{{ $customer->id }}">{{ $customer->aname }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3">
                        <button class="btn btn-primary mt-4"
                            wire:click="generateReport">{{ __('reports::reports.generate_report') }}</button>
                    </div>
                </div>

                <div class="table-responsive">
                    <table class="table table-bordered table-striped">
                        <thead>
                            <tr>
                                <th>{{ __('reports::reports.date') }}</th>
                                <th>{{ __('reports::reports.customer') }}</th>
                                <th>{{ __('reports::reports.operation_type') }}</th>
                                <th>{{ __('reports::reports.operation_number') }}</th>
                                <th class="text-end">{{ __('reports::reports.amount') }}</th>
                                <th class="text-end">{{ __('reports::reports.balance') }}</th>
                                <th>{{ __('reports::reports.description') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($customerTransactions as $transaction)
                                <tr>
                                    <td>{{ $transaction->pro_date ? \Carbon\Carbon::parse($transaction->pro_date)->format('Y-m-d') : '---' }}
                                    </td>
                                    <td>{{ $transaction->accHead->aname ?? '---' }}</td>
                                    <td>
                                        <span
                                            class="badge bg-{{ $transaction->type == 'sale' ? 'success' : ($transaction->type == 'payment' ? 'info' : 'warning') }}">
                                            {{ $transaction->type == 'sale' ? __('reports::reports.Sale') : ($transaction->type == 'payment' ? __('reports::reports.Payment') : __('reports::reports.other')) }}
                                        </span>
                                    </td>
                                    <td>{{ $transaction->pro_num ?? '---' }}</td>
                                    <td class="text-end">{{ number_format($transaction->amount, 2) }}</td>
                                    <td class="text-end">{{ number_format($transaction->balance, 2) }}</td>
                                    <td>{{ $transaction->details ?? '---' }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="text-center">{{ __('reports::reports.no_data_available') }}</td>
                                </tr>
                            @endforelse
                        </tbody>
                        <tfoot>
                            <tr class="table-primary">
                                <th colspan="4">{{ __('reports::reports.total') }}</th>
                                <th class="text-end">{{ number_format($totalAmount, 2) }}</th>
                                <th class="text-end">{{ number_format($finalBalance, 2) }}</th>
                                <th></th>
                            </tr>
                        </tfoot>
                    </table>
                </div>

                @if ($customerTransactions->hasPages())
                    <div class="d-flex justify-content-center">
                        {{ $customerTransactions->links() }}
                    </div>
                @endif

                <!-- Summary -->
                <div class="row mt-3">
                    <div class="col-md-3">
                        <div class="alert alert-info">
                            <strong>{{ __('reports::reports.total_transactions') }}:</strong> {{ $totalTransactions }}
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="alert alert-success">
                            <strong>{{ __('reports::reports.total_sales') }}:</strong> {{ number_format($totalSales, 2) }}
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="alert alert-warning">
                            <strong>{{ __('reports::reports.total_payments') }}:</strong> {{ number_format($totalPayments, 2) }}
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="alert alert-primary">
                            <strong>{{ __('reports::reports.final_balance') }}:</strong> {{ number_format($finalBalance, 2) }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

