@extends('admin.dashboard')

@section('sidebar')
    @include('components.sidebar.reports')
@endsection

@section('content')
    <div class="container">
        <div class="card">
            <div class="card-header">
                <h2 class="mb-0">{{ __('reports::reports.items_list_with_balances_specific_warehouse') }}</h2>
            </div>

            <div class="card-body">
                <form method="GET" action="{{ request()->url() }}" class="mb-4">
                    <div class="row g-3 align-items-end">
                        <div class="col-md-3">
                            <label for="warehouse_id" class="form-label">{{ __('reports::reports.warehouse') }}</label>
                            <select name="warehouse_id" id="warehouse_id" class="form-select">
                                <option value="">{{ __('reports::reports.select_warehouse') }}</option>
                                @foreach ($warehouses as $warehouse)
                                    <option value="{{ $warehouse->id }}"
                                        {{ request('warehouse_id') == $warehouse->id ? 'selected' : '' }}>
                                        {{ $warehouse->aname }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        @foreach ($notes as $note)
                            <div class="col-md-2">
                                <label for="note_{{ $note->id }}" class="form-label">
                                    {{ $note->name }}:
                                </label>
                                <select name="note_{{ $note->id }}" id="note_{{ $note->id }}" class="form-select">
                                    <option value="">{{ __('reports::reports.all') }}</option>
                                    @foreach ($note->noteDetails as $detail)
                                        <option value="{{ $detail->name }}"
                                            {{ request("note_{$note->id}") == $detail->name ? 'selected' : '' }}>
                                            {{ $detail->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        @endforeach

                        <div class="col-md-3">
                            <label for="search" class="form-label">{{ __('reports::reports.search') }}</label>
                            <input type="text" name="search" id="search" class="form-control"
                                value="{{ request('search') }}" placeholder="{{ __('reports::reports.search_by_item_name_or_code') }}">
                        </div>

                        <div class="col-md-2">
                            <button type="submit" class="btn btn-primary w-100 mt-4">
                                {{ __('reports::reports.generate_report') }}
                            </button>
                        </div>
                    </div>
                </form>

                @if ($selectedWarehouse)
                    <div class="alert alert-info mb-4">
                        <strong>{{ __('reports::reports.selected_warehouse') }}:</strong> {{ $selectedWarehouse->aname }}<br>
                        <strong>{{ __('reports::reports.address') }}:</strong> {{ $selectedWarehouse->address ?? '—' }}
                    </div>
                @endif

                <div class="table-responsive">
                    <table class="table table-bordered table-striped table-hover">
                        <thead class="table-light">
                            <tr>
                                <th>{{ __('reports::reports.item_code') }}</th>
                                <th>{{ __('reports::reports.item_name') }}</th>
                                <th>{{ __('reports::reports.unit') }}</th>
                                <th class="text-end">{{ __('reports::reports.current_balance') }}</th>
                                <th class="text-end">{{ __('reports::reports.minimum') }}</th>
                                <th class="text-end">{{ __('reports::reports.maximum') }}</th>
                                <th class="text-end">{{ __('reports::reports.balance_value') }}</th>
                                <th>{{ __('reports::reports.status') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($inventoryBalances as $balance)
                                <tr>
                                    <td>{{ $balance->code ?? '—' }}</td>
                                    <td>{{ $balance->name ?? '—' }}</td>
                                    <td>{{ $balance->units->first()?->name ?? '—' }}</td>
                                    <td class="text-end">{{ number_format($balance->current_balance ?? 0, 2) }}</td>
                                    <td class="text-end">{{ number_format($balance->min_balance ?? 0, 2) }}</td>
                                    <td class="text-end">{{ number_format($balance->max_balance ?? 0, 2) }}</td>
                                    <td class="text-end">{{ number_format($balance->value ?? 0, 2) }}</td>
                                    <td>
                                        @if ($balance->current_balance <= ($balance->min_balance ?? 0))
                                            <span class="badge bg-danger">{{ __('reports::reports.low_stock') }}</span>
                                        @elseif($balance->current_balance >= ($balance->max_balance ?? 999999))
                                            <span class="badge bg-warning">{{ __('reports::reports.high_stock') }}</span>
                                        @else
                                            <span class="badge bg-success">{{ __('reports::reports.normal') }}</span>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" class="text-center py-4">
                                        {{ __('reports::reports.no_data_available') }}
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                        <tfoot class="table-primary">
                            <tr>
                                <th colspan="3">{{ __('reports::reports.total') }}</th>
                                <th class="text-end">{{ number_format($totalBalance ?? 0, 2) }}</th>
                                <th colspan="2"></th>
                                <th class="text-end">{{ number_format($totalValue ?? 0, 2) }}</th>
                                <th></th>
                            </tr>
                        </tfoot>
                    </table>
                </div>

                @if ($inventoryBalances->hasPages())
                    <div class="d-flex justify-content-center mt-4">
                        {{ $inventoryBalances->appends(request()->query())->links() }}
                    </div>
                @endif

                <!-- ملخص -->
                <div class="row mt-4 g-3">
                    <div class="col-md-3">
                        <div class="alert alert-info mb-0">
                            <strong>{{ __('reports::reports.total_items') }}:</strong> {{ $totalItems ?? 0 }}
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="alert alert-danger mb-0">
                            <strong>{{ __('reports::reports.low_stock') }}:</strong> {{ $lowStockItems ?? 0 }}
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="alert alert-warning mb-0">
                            <strong>{{ __('reports::reports.high_stock') }}:</strong> {{ $highStockItems ?? 0 }}
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="alert alert-success mb-0">
                            <strong>{{ __('reports::reports.normal') }}:</strong> {{ $normalStockItems ?? 0 }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

