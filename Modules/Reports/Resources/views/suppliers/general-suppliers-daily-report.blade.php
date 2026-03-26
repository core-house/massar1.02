@extends('admin.dashboard')

@section('sidebar')
    @include('components.sidebar.reports')
@endsection

@section('content')
    <div class="container">
        <div class="card">
            <div class="card-head">
                <h2>{{ __('reports::reports.suppliers_daily_report') }}</h2>
            </div>
            <div class="card-body">
                <div class="row mb-3">
                    <div class="col-md-3">
                        <label for="from_date">{{ __('reports::reports.from_date') }}:</label>
                        <input type="date" id="from_date" class="form-control" wire:model.live="fromDate">
                    </div>
                    <div class="col-md-3">
                        <label for="to_date">{{ __('reports::reports.to_date') }}:</label>
                        <input type="date" id="to_date" class="form-control" wire:model.live="toDate">
                    </div>
                    <div class="col-md-3">
                        <label for="supplier_id">{{ __('reports::reports.supplier') }}:</label>
                        <select id="supplier_id" class="form-control" wire:model.live="supplierId">
                            <option value="">{{ __('reports::reports.all_suppliers') }}</option>
                            @foreach ($suppliers as $supplier)
                                <option value="{{ $supplier->id }}">{{ $supplier->aname }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3 d-flex align-items-end">
                        <button class="btn btn-primary w-100" wire:click="generateReport">
                            <i class="fas fa-chart-line me-2"></i>{{ __('reports::reports.generate_report') }}
                        </button>
                    </div>
                </div>

                <div class="table-responsive">
                    <table class="table table-bordered table-striped table-hover">
                        <thead>
                            <tr>
                                <th>{{ __('reports::reports.date') }}</th>
                                <th>{{ __('reports::reports.supplier') }}</th>
                                <th>{{ __('reports::reports.operation_type') }}</th>
                                <th>{{ __('reports::reports.operation_number') }}</th>
                                <th class="text-end">{{ __('reports::reports.amount') }}</th>
                                <th class="text-end">{{ __('reports::reports.balance') }}</th>
                                <th>{{ __('reports::reports.description') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($supplierTransactions as $transaction)
                                <tr>
                                    <td>
                                        {{ $transaction->pro_date ? \Carbon\Carbon::parse($transaction->pro_date)->format('Y-m-d') : '---' }}
                                    </td>
                                    <td class="fw-bold">
                                        {{ $transaction->accountHead->aname ?? '---' }}
                                    </td>
                                    <td>
                                        <span
                                            class="badge bg-{{ $transaction->type == 'purchase' ? 'success' : ($transaction->type == 'payment' ? 'info' : 'warning') }}">
                                            {{ $transaction->getTransactionTypeText() ?? __('reports::reports.Unknown') }}
                                        </span>
                                    </td>
                                    <td>{{ $transaction->pro_num ?? '---' }}</td>
                                    <td class="text-end fw-bold text-primary">
                                        {{ number_format($transaction->amount ?? 0, 2) }}
                                    </td>
                                    <td
                                        class="text-end fw-bold {{ ($transaction->balance ?? 0) >= 0 ? 'text-success' : 'text-danger' }} fs-6">
                                        {{ number_format($transaction->balance ?? 0, 2) }}
                                    </td>
                                    <td>{{ $transaction->details ?? '---' }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="text-center py-4">
                                        <i class="fas fa-inbox fa-3x text-muted mb-3 opacity-50"></i>
                                        <p class="text-muted mb-0 fw-semibold">
                                            {{ __('reports::reports.no_transactions_available_for_selected_period') }}</p>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                        @if (isset($totalAmount))
                            <tfoot class="table-primary">
                                <tr>
                                    <th colspan="4" class="text-end fw-bold fs-5">{{ __('reports::reports.grand_total') }}</th>
                                    <th class="text-end fw-bold text-primary fs-5">
                                        {{ number_format($totalAmount ?? 0, 2) }}</th>
                                    <th
                                        class="text-end fw-bold {{ ($finalBalance ?? 0) >= 0 ? 'text-success' : 'text-danger' }} fs-5">
                                        {{ number_format($finalBalance ?? 0, 2) }}
                                    </th>
                                    <th></th>
                                </tr>
                            </tfoot>
                        @endif
                    </table>
                </div>

                @if (isset($supplierTransactions) && $supplierTransactions->hasPages())
                    <div class="d-flex justify-content-center mt-3">
                        {{ $supplierTransactions->appends(request()->query())->links() }}
                    </div>
                @endif

                <!-- Summary Cards -->
                @if (isset($totalTransactions))
                    <div class="row mt-4 g-3">
                        <div class="col-md-3">
                            <div class="alert alert-info shadow-sm h-100">
                                <i class="fas fa-exchange-alt fa-2x mb-2 text-info"></i>
                                <strong>{{ __('reports::reports.total_transactions') }}:</strong> {{ $totalTransactions }}
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="alert alert-success shadow-sm h-100">
                                <i class="fas fa-shopping-cart fa-2x mb-2 text-success"></i>
                                <strong>{{ __('reports::reports.total_purchases') }}:</strong> {{ number_format($totalPurchases ?? 0, 2) }}
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="alert alert-warning shadow-sm h-100">
                                <i class="fas fa-money-bill-wave fa-2x mb-2 text-warning"></i>
                                <strong>{{ __('reports::reports.total_payments') }}:</strong> {{ number_format($totalPayments ?? 0, 2) }}
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="alert alert-primary shadow-sm h-100">
                                <i class="fas fa-balance-scale fa-2x mb-2 text-primary"></i>
                                <strong>{{ __('reports::reports.final_balance') }}:</strong> {{ number_format($finalBalance ?? 0, 2) }}
                            </div>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection

