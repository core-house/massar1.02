<div>
    <div class="container-fluid">
        {{-- Header --}}
        <div class="row mb-4">
            <div class="col-12">
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-primary text-white">
                        <h4 class="mb-0 fw-bold">
                            <i class="las la-file-invoice me-2"></i>{{ __('Stage Invoices Report') }}
                        </h4>
                        <small>{{ __('Manufacturing invoices grouped by production stages') }}</small>
                    </div>
                </div>
            </div>
        </div>

        {{-- Filters --}}
        <div class="row mb-4">
            <div class="col-12">
                <div class="card border-0 shadow-sm">
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-md-3">
                                <label class="form-label fw-semibold">
                                    <i class="las la-industry me-1"></i>{{ __('Manufacturing Order') }}
                                </label>
                                <select wire:model.live="selectedOrderId" class="form-select">
                                    <option value="">{{ __('All Orders') }}</option>
                                    @foreach($orders as $order)
                                        <option value="{{ $order->id }}">
                                            {{ $order->order_number }} - {{ $order->item->name ?? __('N/A') }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-md-3">
                                <label class="form-label fw-semibold">
                                    <i class="las la-layer-group me-1"></i>{{ __('Manufacturing Stage') }}
                                </label>
                                <select wire:model.live="selectedStageId" class="form-select" 
                                    {{ !$selectedOrderId ? 'disabled' : '' }}>
                                    <option value="">{{ __('All Stages') }}</option>
                                    @foreach($stages as $stage)
                                        <option value="{{ $stage->id }}">{{ $stage->name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-md-2">
                                <label class="form-label fw-semibold">
                                    <i class="las la-calendar me-1"></i>{{ __('From Date') }}
                                </label>
                                <input type="date" wire:model.live="dateFrom" class="form-control">
                            </div>

                            <div class="col-md-2">
                                <label class="form-label fw-semibold">
                                    <i class="las la-calendar me-1"></i>{{ __('To Date') }}
                                </label>
                                <input type="date" wire:model.live="dateTo" class="form-control">
                            </div>

                            <div class="col-md-2">
                                <label class="form-label fw-semibold">
                                    <i class="las la-search me-1"></i>{{ __('Search') }}
                                </label>
                                <input type="text" wire:model.live.debounce.300ms="searchTerm" 
                                    class="form-control" placeholder="{{ __('Invoice number or description') }}">
                            </div>
                        </div>

                        <div class="row mt-3">
                            <div class="col-12">
                                <button wire:click="resetFilters" class="btn btn-secondary btn-sm">
                                    <i class="las la-redo me-1"></i>{{ __('Reset Filters') }}
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Statistics --}}
        <div class="row mb-4">
            <div class="col-md-6">
                <div class="card border-0 shadow-sm">
                    <div class="card-body text-center">
                        <div class="text-muted small mb-1">{{ __('Total Invoices') }}</div>
                        <div class="fs-2 fw-bold text-primary">{{ number_format($stats['total_invoices']) }}</div>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card border-0 shadow-sm">
                    <div class="card-body text-center">
                        <div class="text-muted small mb-1">{{ __('Total Value') }}</div>
                        <div class="fs-2 fw-bold text-success">{{ number_format($stats['total_value'], 2) }} {{ __('EGP') }}</div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Invoices Table --}}
        <div class="row">
            <div class="col-12">
                <div class="card border-0 shadow-sm">
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th>{{ __('Invoice #') }}</th>
                                        <th>{{ __('Date') }}</th>
                                        <th>{{ __('Manufacturing Order') }}</th>
                                        <th>{{ __('Stage') }}</th>
                                        <th>{{ __('Branch') }}</th>
                                        <th class="text-end">{{ __('Value') }}</th>
                                        <th>{{ __('Description') }}</th>
                                        <th class="text-center">{{ __('Actions') }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($invoices as $invoice)
                                        <tr>
                                            <td>
                                                <strong class="text-primary">{{ $invoice->pro_id }}</strong>
                                            </td>
                                            <td>
                                                <i class="las la-calendar text-muted me-1"></i>
                                                {{ \Carbon\Carbon::parse($invoice->pro_date)->format('Y-m-d') }}
                                            </td>
                                            <td>
                                                @if($invoice->manufacturingOrder)
                                                    <span class="badge bg-info">
                                                        {{ $invoice->manufacturingOrder->order_number }}
                                                    </span>
                                                @else
                                                    <span class="text-muted">-</span>
                                                @endif
                                            </td>
                                            <td>
                                                @if($invoice->manufacturingStage)
                                                    <span class="badge bg-secondary">
                                                        {{ $invoice->manufacturingStage->name }}
                                                    </span>
                                                @else
                                                    <span class="text-muted">-</span>
                                                @endif
                                            </td>
                                            <td>
                                                <i class="las la-building text-muted me-1"></i>
                                                {{ $invoice->branch->name ?? '-' }}
                                            </td>
                                            <td class="text-end">
                                                <strong class="text-success">
                                                    {{ number_format($invoice->pro_value, 2) }}
                                                </strong>
                                            </td>
                                            <td>
                                                <small class="text-muted">{{ $invoice->info ?? '-' }}</small>
                                            </td>
                                            <td class="text-center">
                                                <div class="btn-group" role="group">
                                                    <a href="{{ route('manufacturing.show', $invoice->id) }}" 
                                                        class="btn btn-sm btn-info" title="{{ __('View') }}">
                                                        <i class="las la-eye"></i>
                                                    </a>
                                                    <a href="{{ route('manufacturing.edit', $invoice->id) }}" 
                                                        class="btn btn-sm btn-primary" title="{{ __('Edit') }}">
                                                        <i class="las la-edit"></i>
                                                    </a>
                                                </div>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="8" class="text-center py-5">
                                                <i class="las la-inbox fs-1 text-muted d-block mb-3"></i>
                                                <p class="text-muted mb-0">{{ __('No invoices found') }}</p>
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                    @if($invoices->hasPages())
                        <div class="card-footer bg-light">
                            {{ $invoices->links() }}
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
