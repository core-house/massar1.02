@extends('admin.dashboard')

@section('sidebar')
    @include('components.sidebar.reports')
@endsection

@section('content')
    <div class="container">
        <div class="card">
            <div class="card-head">
                <h2>{{ __('reports::reports.cost_center_account_statement') }}</h2>
            </div>
            <div class="card-body">
                <div class="row mb-3">
                    <div class="col-md-3">
                        <label for="cost_center_id">{{ __('reports::reports.cost_center') }}:</label>
                        <select id="cost_center_id" class="form-control" wire:model="costCenterId">
                            <option value="">{{ __('reports::reports.select_cost_center') }}</option>
                            @foreach ($costCenters as $center)
                                <option value="{{ $center->id }}">{{ $center->code }} - {{ $center->aname }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label for="from_date">{{ __('reports::reports.from_date') }}:</label>
                        <input type="date" id="from_date" class="form-control" wire:model="fromDate">
                    </div>
                    <div class="col-md-3">
                        <label for="to_date">{{ __('reports::reports.to_date') }}:</label>
                        <input type="date" id="to_date" class="form-control" wire:model="toDate">
                    </div>
                    <div class="col-md-3">
                        <button class="btn btn-primary mt-4"
                            wire:click="generateReport">{{ __('reports::reports.generate_report') }}</button>
                    </div>
                </div>

                @if ($selectedCostCenter)
                    <div class="row mb-3">
                        <div class="col-12">
                            <div class="alert alert-info">
                                <strong>{{ __('reports::reports.selected_cost_center') }}:</strong> {{ $selectedCostCenter->code }} -
                                {{ $selectedCostCenter->aname }}
                                <br>
                                <strong>{{ __('reports::reports.type') }}:</strong> {{ $selectedCostCenter->type ?? '---' }}
                                <br>
                                <strong>{{ __('reports::reports.opening_balance') }}:</strong> {{ number_format($openingBalance, 2) }}
                            </div>
                        </div>
                    </div>
                @endif

                <div class="table-responsive">
                    <table class="table table-bordered table-striped">
                        <thead>
                            <tr>
                                <th>{{ __('reports::reports.date') }}</th>
                                <th>{{ __('reports::reports.operation_number') }}</th>
                                <th>{{ __('reports::reports.account') }}</th>
                                <th>{{ __('reports::reports.description') }}</th>
                                <th class="text-end">{{ __('reports::reports.debit') }}</th>
                                <th class="text-end">{{ __('reports::reports.credit') }}</th>
                                <th class="text-end">{{ __('reports::reports.balance') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($costCenterTransactions as $transaction)
                                <tr>
                                    <td>{{ $transaction->crtime ? \Carbon\Carbon::parse($transaction->crtime)->format('Y-m-d') : '---' }}</td>
                                    <td>{{ $transaction->head->journal_id ?? '---' }}</td>
                                    <td>{{ $transaction->accountHead->code ?? '---' }} -
                                        {{ $transaction->accountHead->aname ?? '---' }}</td>
                                    <td>{{ $transaction->info ?? '---' }}</td>
                                    <td class="text-end">
                                        {{ $transaction->debit > 0 ? number_format($transaction->debit, 2) : '---' }}</td>
                                    <td class="text-end">
                                        {{ $transaction->credit > 0 ? number_format($transaction->credit, 2) : '---' }}
                                    </td>
                                    <td class="text-end">{{ number_format($transaction->running_balance, 2) }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="text-center">{{ __('reports::reports.no_data_available') }}</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if ($costCenterTransactions->hasPages())
                    <div class="d-flex justify-content-center">
                        {{ $costCenterTransactions->links() }}
                    </div>
                @endif

                @if ($selectedCostCenter)
                    <div class="row mt-3">
                        <div class="col-12">
                            <div class="alert alert-success">
                                <strong>{{ __('reports::reports.closing_balance') }}:</strong> {{ number_format($closingBalance, 2) }}
                            </div>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection
