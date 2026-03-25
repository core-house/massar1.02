@extends('admin.dashboard')

@section('sidebar')
    @include('components.sidebar.reports')
@endsection

@section('content')
    <div class="container">
        <div class="card">
            <div class="card-head">
                <h2>{{ __('reports::reports.daily_purchases_report') }}</h2>
            </div>
            <div class="card-body">
                <!-- Filters -->
                <div class="row mb-4 g-3">
                    <div class="col-md-3">
                        <label for="from_date" class="form-label fw-bold">{{ __('reports::reports.from_date') }}:</label>
                        <input type="date" id="from_date" class="form-control" wire:model.live="fromDate">
                    </div>
                    <div class="col-md-3">
                        <label for="to_date" class="form-label fw-bold">{{ __('reports::reports.to_date') }}:</label>
                        <input type="date" id="to_date" class="form-control" wire:model.live="toDate">
                    </div>
                    <div class="col-md-3">
                        <label for="supplier_id" class="form-label fw-bold">{{ __('reports::reports.supplier') }}:</label>
                        <select id="supplier_id" class="form-select" wire:model.live="supplierId">
                            <option value="">{{ __('reports::reports.all_suppliers') }}</option>
                            @foreach ($suppliers as $supplier)
                                <option value="{{ $supplier->id }}">{{ $supplier->aname }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3 d-flex align-items-end">
                        <button class="btn btn-primary w-100 h-100" wire:click="generateReport">
                            <i class="fas fa-chart-bar me-2"></i>{{ __('reports::reports.generate_report') }}
                        </button>
                    </div>
                </div>

                <!-- Summary Cards -->
                <div class="row mb-4 g-3">
                    <div class="col-md-3">
                        <div class="card bg-info text-white shadow-sm h-100">
                            <div class="card-body text-center">
                                <i class="fas fa-file-invoice fa-2x mb-2 opacity-75"></i>
                                <h6 class="fw-bold">{{ __('reports::reports.total_invoices') }}</h6>
                                <h4 class="fw-bold mb-0">{{ $totalInvoices ?? 0 }}</h4>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card bg-success text-white shadow-sm h-100">
                            <div class="card-body text-center">
                                <i class="fas fa-boxes fa-2x mb-2 opacity-75"></i>
                                <h6 class="fw-bold">{{ __('reports::reports.total_quantity') }}</h6>
                                <h4 class="fw-bold mb-0">{{ number_format($totalQuantity ?? 0, 2) }}</h4>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card bg-primary text-white shadow-sm h-100">
                            <div class="card-body text-center">
                                <i class="fas fa-shopping-cart fa-2x mb-2 opacity-75"></i>
                                <h6 class="fw-bold">{{ __('reports::reports.total_purchases') }}</h6>
                                <h4 class="fw-bold mb-0">{{ number_format($totalPurchases ?? 0, 2) }}</h4>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card bg-warning text-white shadow-sm h-100">
                            <div class="card-body text-center">
                                <i class="fas fa-calculator fa-2x mb-2 opacity-75"></i>
                                <h6 class="fw-bold">{{ __('reports::reports.total_net_purchases') }}</h6>
                                <h4 class="fw-bold mb-0">{{ number_format($totalNetPurchases ?? 0, 2) }}</h4>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="table-responsive">
                    <table class="table table-bordered table-striped table-hover">
                        <thead>
                            <tr>
                                <th class="text-center">{{ __('reports::reports.date') }}</th>
                                <th class="text-center">{{ __('reports::reports.invoice_number') }}</th>
                                <th>{{ __('reports::reports.supplier') }}</th>
                                <th class="text-end fw-bold">{{ __('reports::reports.items_count') }}</th>
                                <th class="text-end fw-bold">{{ __('reports::reports.total_quantity') }}</th>
                                <th class="text-end fw-bold text-primary">{{ __('reports::reports.total_purchases') }}</th>
                                <th class="text-end fw-bold text-warning">{{ __('reports::reports.discount') }}</th>
                                <th class="text-end fw-bold text-success">{{ __('reports::reports.net_purchases') }}</th>
                                <th class="text-center">{{ __('reports::reports.status') }}</th>
                                <th class="text-center">{{ __('reports::reports.actions') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($purchases as $purchase)
                                <tr>
                                    <td class="text-center fw-semibold">
                                        {{ $purchase->pro_date ? \Carbon\Carbon::parse($purchase->pro_date)->format('Y-m-d') : '---' }}
                                    </td>
                                    <td class="text-center">
                                        <span class="badge bg-info">{{ $purchase->pro_num ?? '---' }}</span>
                                    </td>
                                    <td>
                                        <strong>{{ $purchase->acc1Head->aname ?? __('reports::reports.Unspecified') }}</strong>
                                    </td>
                                    <td class="text-end">
                                        <span class="badge bg-secondary">{{ $purchase->items_count ?? 0 }}</span>
                                    </td>
                                    <td class="text-end fw-bold">{{ number_format($purchase->total_quantity ?? 0, 2) }}
                                    </td>
                                    <td class="text-end fw-bold text-primary fs-6">
                                        {{ number_format($purchase->total_purchases ?? 0, 2) }}
                                    </td>
                                    <td class="text-end fw-bold text-warning">
                                        {{ number_format($purchase->discount ?? 0, 2) }}
                                    </td>
                                    <td class="text-end fw-bold text-success fs-6">
                                        {{ number_format($purchase->net_purchases ?? 0, 2) }}
                                    </td>
                                    <td class="text-center">
                                        @if ($purchase->status == 'completed')
                                            <span class="badge bg-success fs-6">{{ __('reports::reports.Completed') }}</span>
                                        @elseif($purchase->status == 'pending')
                                            <span class="badge bg-warning fs-6">{{ __('reports::reports.Pending') }}</span>
                                        @elseif($purchase->status == 'cancelled')
                                            <span class="badge bg-danger fs-6">{{ __('reports::reports.Cancelled') }}</span>
                                        @else
                                            <span class="badge bg-secondary">{{ __('reports::reports.Unspecified') }}</span>
                                        @endif
                                    </td>
                                    <td class="text-center">
                                        <div class="btn-group btn-group-sm" role="group">
                                            <a href="#" class="btn btn-outline-info" title="{{ __('reports::reports.View') }}">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <a href="#" class="btn btn-outline-primary" title="{{ __('reports::reports.Edit') }}">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <button class="btn btn-outline-success" title="{{ __('reports::reports.print') }}">
                                                <i class="fas fa-print"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="10" class="text-center py-5">
                                        <div class="alert alert-info mb-0">
                                            <i class="fas fa-inbox fa-3x mb-3 d-block opacity-75"></i>
                                            {{ __('reports::reports.no_purchases_data_available') }}
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                        <tfoot class="table-primary">
                            <tr>
                                <th colspan="4" class="text-end fw-bold">{{ __('reports::reports.grand_total') }}</th>
                                <th class="text-end fw-bold fs-5">{{ number_format($totalQuantity ?? 0, 2) }}</th>
                                <th class="text-end fw-bold text-primary fs-5">
                                    {{ number_format($totalPurchases ?? 0, 2) }}</th>
                                <th class="text-end fw-bold text-warning fs-5">{{ number_format($totalDiscount ?? 0, 2) }}
                                </th>
                                <th class="text-end fw-bold text-success fs-5">
                                    {{ number_format($totalNetPurchases ?? 0, 2) }}</th>
                                <th colspan="2"></th>
                            </tr>
                        </tfoot>
                    </table>
                </div>

                @if (isset($purchases) && $purchases->hasPages())
                    <div class="d-flex justify-content-center mt-4">
                        {{ $purchases->links() }}
                    </div>
                @endif

                <!-- Analytics Summary -->
                @if (isset($purchases) && $purchases->count() > 0)
                    <div class="row mt-4 g-3">
                        <div class="col-md-3">
                            <div class="alert alert-info shadow-sm">
                                <i class="fas fa-file-invoice fa-2x float-start me-2 mb-2"></i>
                                <strong>{{ __('reports::reports.total_invoices') }}:</strong> {{ $totalInvoices ?? 0 }}
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="alert alert-success shadow-sm">
                                <i class="fas fa-calculator fa-2x float-start me-2 mb-2"></i>
                                <strong>{{ __('reports::reports.average_invoice_value') }}:</strong>
                                {{ number_format($averageInvoiceValue ?? 0, 2) }}
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="alert alert-warning shadow-sm">
                                <i class="fas fa-tags fa-2x float-start me-2 mb-2"></i>
                                <strong>{{ __('reports::reports.total_discounts') }}:</strong> {{ number_format($totalDiscount ?? 0, 2) }}
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="alert alert-primary shadow-sm">
                                <i class="fas fa-wallet fa-2x float-start me-2 mb-2"></i>
                                <strong>{{ __('reports::reports.net_purchases') }}:</strong>
                                {{ number_format($totalNetPurchases ?? 0, 2) }}
                            </div>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection

