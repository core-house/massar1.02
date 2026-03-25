@extends('admin.dashboard')

@section('sidebar')
    @include('components.sidebar.reports')
@endsection

@section('content')
    <div class="container">
        <div class="card">
            <div class="card-header">
                <h2 class="mb-0">{{ __('reports::reports.Items List With Balances - All Warehouses') }}</h2>
            </div>

            <div class="card-body">
                <div class="row g-3 align-items-end mb-4">
                    <div class="col-md-3">
                        <label for="item_category" class="form-label">{{ __('reports::reports.Category') }}</label>
                        <select id="item_category" class="form-select" wire:model.live="itemCategory">
                            <option value="">{{ __('reports::reports.all') }}</option>
                            @foreach ($notes as $note)
                                <option value="{{ $note->id }}">{{ $note->aname }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-3">
                        <label for="note_detail_id" class="form-label">{{ __('reports::reports.Details') }}</label>
                        <select id="note_detail_id" class="form-select" wire:model.live="noteDetailId">
                            <option value="">{{ __('reports::reports.all') }}</option>
                            @foreach ($notes as $note)
                                @foreach ($note->noteDetails as $detail)
                                    <option value="{{ $detail->id }}">
                                        {{ $note->aname }} - {{ $detail->aname }}
                                    </option>
                                @endforeach
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-3">
                        <label for="warehouse_id" class="form-label">{{ __('reports::reports.Warehouse') }}</label>
                        <select id="warehouse_id" class="form-select" wire:model.live="warehouseId">
                            <option value="">{{ __('reports::reports.all') }}</option>
                            @foreach ($warehouses as $warehouse)
                                <option value="{{ $warehouse->id }}">{{ $warehouse->aname }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-2">
                        <label for="search" class="form-label">{{ __('reports::reports.search') }}</label>
                        <input type="text" id="search" class="form-control" wire:model.live.debounce.500ms="search"
                            placeholder="{{ __('reports::reports.item_name or Code') }}">
                    </div>

                    <div class="col-md-1">
                        <button class="btn btn-primary w-100 mt-4" wire:click="generateReport">
                            {{ __('reports::reports.generate_report') }}
                        </button>
                    </div>
                </div>

                <div class="table-responsive">
                    <table class="table table-bordered table-striped table-hover">
                        <thead class="table-light">
                            <tr>
                                <th>{{ __('reports::reports.item_code') }}</th>
                                <th>{{ __('reports::reports.item_name') }}</th>
                                <th>{{ __('reports::reports.unit') }}</th>
                                <th>{{ __('reports::reports.Warehouse') }}</th>
                                <th class="text-end">{{ __('reports::reports.current_balance') }}</th>
                                <th class="text-end">{{ __('reports::reports.minimum') }}</th>
                                <th class="text-end">{{ __('reports::reports.maximum') }}</th>
                                <th>{{ __('reports::reports.status') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($inventoryBalances as $balance)
                                <tr>
                                    <td>{{ $balance->code ?? '—' }}</td>
                                    <td>{{ $balance->aname ?? '—' }}</td>
                                    <td>{{ $balance->main_unit?->aname ?? '—' }}</td>
                                    <td>{{ $balance->warehouse?->aname ?? __('reports::reports.all Warehouses') }}</td>
                                    <td class="text-end">{{ number_format($balance->current_balance ?? 0, 2) }}</td>
                                    <td class="text-end">{{ number_format($balance->min_balance ?? 0, 2) }}</td>
                                    <td class="text-end">{{ number_format($balance->max_balance ?? 0, 2) }}</td>
                                    <td>
                                        @if ($balance->current_balance <= ($balance->min_balance ?? 0))
                                            <span class="badge bg-danger">{{ __('reports::reports.Low Stock') }}</span>
                                        @elseif($balance->current_balance >= ($balance->max_balance ?? 999999))
                                            <span class="badge bg-warning">{{ __('reports::reports.High Stock') }}</span>
                                        @else
                                            <span class="badge bg-success">{{ __('reports::reports.Normal') }}</span>
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
                                <th colspan="4">{{ __('reports::reports.total') }}</th>
                                <th class="text-end">{{ number_format($totalBalance ?? 0, 2) }}</th>
                                <th colspan="3"></th>
                            </tr>
                        </tfoot>
                    </table>
                </div>

                @if ($inventoryBalances->hasPages())
                    <div class="d-flex justify-content-center mt-4">
                        {{ $inventoryBalances->links() }}
                    </div>
                @endif

                <!-- ملخص -->
                <div class="row mt-4 g-3">
                    <div class="col-md-4">
                        <div class="alert alert-info mb-0">
                            <strong>{{ __('reports::reports.total_items') }}:</strong> {{ $totalItems ?? 0 }}
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="alert alert-warning mb-0">
                            <strong>{{ __('reports::reports.Low Stock Items') }}:</strong> {{ $lowStockItems ?? 0 }}
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="alert alert-success mb-0">
                            <strong>{{ __('reports::reports.Normal Stock Items') }}:</strong> {{ $normalStockItems ?? 0 }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

