@extends('admin.dashboard')

{{-- Dynamic Sidebar --}}
@section('sidebar')
    @include('components.sidebar.reports')
@endsection

@section('content')
    <div class="container">
        <div class="card">
            <div class="card-head">
                <h2>{{ __('reports::reports.trial_balance') }}</h2>
                <div class="text-muted">{{ __('reports::reports.as_of_date') }}
                    {{ $asOfDate ? \Carbon\Carbon::parse($asOfDate)->format('Y-m-d') : now()->format('Y-m-d') }}</div>
            </div>
            <div class="card-body">
                <div class="row mb-3">
                    <div class="col-md-3">
                        <label for="as_of_date">{{ __('reports::reports.until_date') }}</label>
                        <input type="date" id="as_of_date" class="form-control" wire:model="asOfDate">
                    </div>
                    <div class="col-md-3">
                        <label for="account_group">{{ __('reports::reports.account_group') }}</label>
                        <select id="account_group" class="form-control" wire:model="accountGroup">
                            <option value="">{{ __('reports::reports.all') }}</option>
                            <option value="1">{{ __('reports::reports.assets') }}</option>
                            <option value="2">{{ __('reports::reports.liabilities') }}</option>
                            <option value="3">{{ __('reports::reports.equity') }}</option>
                            <option value="4">{{ __('reports::reports.revenue') }}</option>
                            <option value="5">{{ __('reports::reports.expenses') }}</option>
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
                                <th>{{ __('reports::reports.account_number') }}</th>
                                <th>{{ __('reports::reports.account_name') }}</th>
                                <th class="text-end">{{ __('reports::reports.debit') }}</th>
                                <th class="text-end">{{ __('reports::reports.credit') }}</th>
                                <th class="text-end">{{ __('reports::reports.balance') }}</th>
                                <th>{{ __('reports::reports.balance_type') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($accountBalances as $balance)
                                <tr>
                                    <td>{{ $balance->code }}</td>
                                    <td>{{ $balance->aname }}</td>
                                    <td class="text-end">
                                        {{ $balance->debit > 0 ? number_format($balance->debit, 2) : '---' }}</td>
                                    <td class="text-end">
                                        {{ $balance->credit > 0 ? number_format($balance->credit, 2) : '---' }}</td>
                                    <td class="text-end">{{ number_format($balance->balance, 2) }}</td>
                                    <td>
                                        @if ($balance->balance > 0)
                                            <span class="badge bg-primary">{{ __('reports::reports.debit') }}</span>
                                        @elseif($balance->balance < 0)
                                            <span class="badge bg-success">{{ __('reports::reports.credit') }}</span>
                                        @else
                                            <span class="badge bg-secondary">{{ __('reports::reports.zero') }}</span>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center">{{ __('reports::reports.no_data_available') }}</td>
                                </tr>
                            @endforelse
                        </tbody>
                        <tfoot>
                            <tr class="table-primary">
                                <th colspan="2">{{ __('reports::reports.total') }}</th>
                                <th class="text-end">{{ number_format($totalDebit, 2) }}</th>
                                <th class="text-end">{{ number_format($totalCredit, 2) }}</th>
                                <th class="text-end">{{ number_format($totalBalance, 2) }}</th>
                                <th></th>
                            </tr>
                        </tfoot>
                    </table>
                </div>

                @if ($accountBalances->hasPages())
                    <div class="d-flex justify-content-center">
                        {{ $accountBalances->links() }}
                    </div>
                @endif

                <!-- Ù…Ù„Ø®Øµ -->
                <div class="row mt-3">
                    <div class="col-12">
                        <div class="alert {{ $totalDebit == $totalCredit ? 'alert-success' : 'alert-warning' }}">
                            <strong>{{ __('reports::reports.report_summary') }}:</strong>
                            @if ($totalDebit == $totalCredit)
                                {{ __('reports::reports.trial_balance_is_balanced') }} âœ“
                            @else
                                {{ __('reports::reports.trial_balance_is_not_balanced') }}
                                {{ number_format(abs($totalDebit - $totalCredit), 2) }}
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

