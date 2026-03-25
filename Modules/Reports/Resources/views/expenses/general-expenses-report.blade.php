@extends('admin.dashboard')

@section('sidebar')
    @include('components.sidebar.reports')
@endsection

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title">{{ __('reports::reports.general_expenses_report') }}</h4>
                    </div>
                    <div class="card-body">
                        <!-- Filters -->
                        <form method="GET" action="{{ route('reports.general-expenses-report') }}">
                            <div class="row mb-3">
                                <div class="col-md-3">
                                    <label for="from_date" class="form-label">{{ __('reports::reports.from_date') }}</label>
                                    <input type="date" name="from_date" value="{{ request('from_date') }}"
                                        class="form-control" id="from_date">
                                </div>
                                <div class="col-md-3">
                                    <label for="to_date" class="form-label">{{ __('reports::reports.to_date') }}</label>
                                    <input type="date" name="to_date" value="{{ request('to_date') }}"
                                        class="form-control" id="to_date">
                                </div>
                                <div class="col-md-3">
                                    <label for="expense_account" class="form-label">{{ __('reports::reports.expense_account') }}</label>
                                    <select name="expense_account" class="form-select" id="expense_account">
                                        <option value="">{{ __('reports::reports.all_expenses') }}</option>
                                        @foreach ($expenseAccounts as $account)
                                            <option value="{{ $account->id }}"
                                                {{ request('expense_account') == $account->id ? 'selected' : '' }}>
                                                {{ $account->aname }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label">&nbsp;</label>
                                    <button type="submit" class="btn btn-primary d-block w-100">
                                        <i class="fas fa-search"></i> {{ __('reports::reports.search') }}
                                    </button>
                                </div>
                            </div>
                        </form>

                        <!-- Summary Cards -->
                        <div class="row mb-4">
                            <div class="col-md-3">
                                <div class="card bg-primary text-white">
                                    <div class="card-body">
                                        <h6 class="card-title">{{ __('reports::reports.total_expenses') }}</h6>
                                        <h4 class="card-text">{{ number_format($totalExpenses, 2) }}</h4>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="card bg-success text-white">
                                    <div class="card-body">
                                        <h6 class="card-title">{{ __('reports::reports.total_payments') }}</h6>
                                        <h4 class="card-text">{{ number_format($totalPayments, 2) }}</h4>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="card bg-info text-white">
                                    <div class="card-body">
                                        <h6 class="card-title">{{ __('reports::reports.net_expenses') }}</h6>
                                        <h4 class="card-text">{{ number_format($netExpenses, 2) }}</h4>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="card bg-secondary text-white">
                                    <div class="card-body">
                                        <h6 class="card-title">{{ __('reports::reports.total_transactions') }}</h6>
                                        <h4 class="card-text">{{ $totalTransactions }}</h4>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Data Table -->
                        <div class="table-responsive">
                            <table class="table table-bordered table-striped">
                                <thead>
                                    <tr>
                                        <th>{{ __('reports::reports.date') }}</th>
                                        <th>{{ __('reports::reports.journal_number') }}</th>
                                        <th>{{ __('reports::reports.expense_account') }}</th>
                                        <th>{{ __('reports::reports.cost_center') }}</th>
                                        <th>{{ __('reports::reports.description') }}</th>
                                        <th class="text-end">{{ __('reports::reports.debit') }}</th>
                                        <th class="text-end">{{ __('reports::reports.credit') }}</th>
                                        <th class="text-end">{{ __('reports::reports.balance') }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($expenseTransactions as $transaction)
                                        <tr>
                                            <td>{{ $transaction->crtime ? \Carbon\Carbon::parse($transaction->crtime)->format('Y-m-d') : '---' }}
                                            </td>
                                            <td>{{ $transaction->head->journal_id ?? '---' }}</td>
                                            <td>{{ $transaction->accHead->aname ?? '---' }}</td>
                                            <td>{{ $transaction->costCenter->cname ?? '---' }}</td>
                                            <td>{{ $transaction->info ?? '---' }}</td>
                                            <td class="text-end">
                                                @if ($transaction->debit > 0)
                                                    <span
                                                        class="text-danger">{{ number_format($transaction->debit, 2) }}</span>
                                                @else
                                                    <span class="text-muted">0.00</span>
                                                @endif
                                            </td>
                                            <td class="text-end">
                                                @if ($transaction->credit > 0)
                                                    <span
                                                        class="text-success">{{ number_format($transaction->credit, 2) }}</span>
                                                @else
                                                    <span class="text-muted">0.00</span>
                                                @endif
                                            </td>
                                            <td class="text-end">
                                                @php
                                                    $balance = $transaction->debit - $transaction->credit;
                                                @endphp
                                                @if ($balance > 0)
                                                    <span class="text-danger">{{ number_format($balance, 2) }}</span>
                                                @elseif($balance < 0)
                                                    <span class="text-success">{{ number_format(abs($balance), 2) }}</span>
                                                @else
                                                    <span class="text-muted">0.00</span>
                                                @endif
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="8" class="text-center">{{ __('reports::reports.no_data_available') }}</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                                @if ($expenseTransactions->count() > 0)
                                    <tfoot>
                                        <tr class="table-primary fw-bold">
                                            <td colspan="5" class="text-start">{{ __('reports::reports.total') }}</td>
                                            <td class="text-end text-danger">
                                                {{ number_format($expenseTransactions->sum('debit'), 2) }}</td>
                                            <td class="text-end text-success">
                                                {{ number_format($expenseTransactions->sum('credit'), 2) }}</td>
                                            <td class="text-end">{{ number_format($netExpenses, 2) }}</td>
                                        </tr>
                                    </tfoot>
                                @endif
                            </table>
                        </div>

                        <!-- Pagination -->
                        @if ($expenseTransactions->hasPages())
                            <div class="d-flex justify-content-center mt-3">
                                {{ $expenseTransactions->appends(request()->query())->links() }}
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

