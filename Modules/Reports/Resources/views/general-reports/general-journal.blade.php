@extends('admin.dashboard')

{{-- Dynamic Sidebar --}}
@section('sidebar')
    @include('components.sidebar.reports')
@endsection

@section('content')
    <div class="container">
        <div class="card">
            <div class="card-head">
                <h2>{{ __('reports::reports.general_journal') }}</h2>
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
                        <label for="account_id">{{ __('reports::reports.account') }}:</label>
                        <select id="account_id" class="form-control" wire:model="accountId">
                            <option value="">{{ __('reports::reports.all') }}</option>
                            @foreach ($accounts as $account)
                                <option value="{{ $account->id }}">{{ $account->code }} - {{ $account->aname }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label for="journal_type">{{ __('reports::reports.Journal Type') }}:</label>
                        <select id="journal_type" class="form-control" wire:model="journalType">
                            <option value="">{{ __('reports::reports.all') }}</option>
                            <option value="7">{{ __('reports::reports.journal_entry') }}</option>
                            <option value="8">{{ __('reports::reports.account_journal_entry') }}</option>
                        </select>
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-3">
                        <button class="btn btn-primary" wire:click="generateReport">{{ __('reports::reports.generate_report') }}</button>
                    </div>
                </div>

                <div class="table-responsive">
                    <table class="table table-bordered table-striped">
                        <thead>
                            <tr>
                                <th>{{ __('reports::reports.date') }}</th>
                                <th>{{ __('reports::reports.entry_number') }}</th>
                                <th>{{ __('reports::reports.account') }}</th>
                                <th class="text-end">{{ __('reports::reports.debit') }}</th>
                                <th class="text-end">{{ __('reports::reports.credit') }}</th>
                                <th>{{ __('reports::reports.description') }}</th>
                                <th>{{ __('reports::reports.Cost Center') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($journalDetails as $detail)
                                <tr>
                                    <td>{{ $detail->head->date ? \Carbon\Carbon::parse($detail->head->date)->format('Y-m-d') : '---' }}
                                    </td>
                                    <td>{{ $detail->head->journal_id ?? '---' }}</td>
                                    <td>{{ $detail->accountHead->code ?? '---' }} -
                                        {{ $detail->accountHead->aname ?? '---' }}</td>
                                    <td class="text-end">
                                        @if ($detail->debit > 0)
                                            <span class="text-danger">{{ number_format($detail->debit, 2) }}</span>
                                        @else
                                            <span class="text-muted">---</span>
                                        @endif
                                    </td>
                                    <td class="text-end">
                                        @if ($detail->credit > 0)
                                            <span class="text-success">{{ number_format($detail->credit, 2) }}</span>
                                        @else
                                            <span class="text-muted">---</span>
                                        @endif
                                    </td>
                                    <td>{{ $detail->info ?? '---' }}</td>
                                    <td>{{ $detail->costCenter->name ?? '---' }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="text-center">{{ __('reports::reports.no_data_available') }}</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if ($journalDetails->hasPages())
                    <div class="d-flex justify-content-center">
                        {{ $journalDetails->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection

