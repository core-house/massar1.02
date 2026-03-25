@extends('admin.dashboard')

@section('sidebar')
    @include('components.sidebar.manufacturing')
@endsection

@section('content')
<div class="container-fluid" id="printable-invoice">
    <!-- Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card shadow-sm">
                <div class="card-header bg-light">
                    <div class="d-flex justify-content-between align-items-center">
                        <h4 class="mb-0">
                            <i class="fas fa-file-invoice me-2"></i>
                            {{ __('manufacturing::manufacturing.manufacturing invoice details') }}
                        </h4>
                        <div class="btn-group no-print">
                            <a href="{{ route('manufacturing.index') }}" class="btn btn-light btn-sm">
                                <i class="fas fa-arrow-left me-1"></i> {{ __('manufacturing::manufacturing.back') }}
                            </a>
                            @can('edit Manufacturing Invoices')
                                <a href="{{ route('manufacturing.edit', $invoice->id) }}" class="btn btn-warning btn-sm">
                                    <i class="fas fa-edit me-1"></i> {{ __('manufacturing::manufacturing.edit') }}
                                </a>
                            @endcan
                            @can('print Manufacturing Invoices')
                                <button onclick="window.print()" class="btn btn-info btn-sm">
                                    <i class="fas fa-print me-1"></i> {{ __('manufacturing::manufacturing.print') }}
                                </button>
                            @endcan
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- معلومات الفاتورة الأساسية -->
    <div class="row mb-4">
        <div class="col-md-6">
            <div class="card shadow-sm h-100">
                <div class="card-header bg-light">
                    <h6 class="mb-0"><i class="fas fa-info-circle me-2"></i>{{ __('manufacturing::manufacturing.invoice information') }}</h6>
                </div>
                <div class="card-body">
                    <table class="table table-sm table-borderless">
                        <tr>
                            <td width="40%" class="text-muted">{{ __('manufacturing::manufacturing.invoice number') }}:</td>
                            <td><strong class="text-primary">{{ $invoice->pro_id }}</strong></td>
                        </tr>
                        <tr>
                            <td class="text-muted">{{ __('manufacturing::manufacturing.date') }}:</td>
                            <td><strong>{{ $invoice->pro_date }}</strong></td>
                        </tr>
                        <tr>
                            <td class="text-muted">{{ __('manufacturing::manufacturing.description') }}:</td>
                            <td>{{ $invoice->info ?: '-' }}</td>
                        </tr>
                        <tr>
                            <td class="text-muted">{{ __('manufacturing::manufacturing.batch number') }}:</td>
                            <td>{{ $invoice->pro_serial ?: '-' }}</td>
                        </tr>
                        @if ($invoice->expected_time)
                            <tr>
                                <td class="text-muted">{{ __('manufacturing::manufacturing.expected time') }}:</td>
                                <td>{{ $invoice->expected_time }} {{ __('manufacturing::manufacturing.hours') }}</td>
                            </tr>
                        @endif
                    </table>
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="card shadow-sm h-100">
                <div class="card-header bg-light">
                    <h6 class="mb-0"><i class="fas fa-building me-2"></i>{{ __('manufacturing::manufacturing.additional information') }}</h6>
                </div>
                <div class="card-body">
                    <table class="table table-sm table-borderless">
                        <tr>
                            <td width="40%" class="text-muted">{{ __('manufacturing::manufacturing.employee') }}:</td>
                            <td><strong>{{ $invoice->employee->aname ?? '-' }}</strong></td>
                        </tr>
                        <tr>
                            <td class="text-muted">{{ __('manufacturing::manufacturing.branch') }}:</td>
                            <td>{{ $invoice->branch->name ?? '-' }}</td>
                        </tr>
                        <tr>
                            <td class="text-muted">{{ __('manufacturing::manufacturing.products account') }}:</td>
                            <td>{{ $invoice->acc1Head->aname ?? '-' }}</td>
                        </tr>
                        <tr>
                            <td class="text-muted">{{ __('manufacturing::manufacturing.raw materials account') }}:</td>
                            <td>{{ $invoice->acc2Head->aname ?? '-' }}</td>
                        </tr>
                        <tr>
                            <td class="text-muted">{{ __('manufacturing::manufacturing.operational account') }}:</td>
                            <td>{{ $invoice->acc3Head->aname ?? __('manufacturing::manufacturing.operational center main') }}</td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- المنتجات المصنعة -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card shadow-sm">
                <div class="card-header bg-light">
                    <h6 class="mb-0">
                        <i class="fas fa-box me-2"></i>
                        {{ __('manufacturing::manufacturing.manufactured products') }}
                    </h6>
                </div>
                <div class="card-body">
                    @if (count($products) > 0)
                        <div class="table-responsive">
                            <table class="table table-bordered table-hover align-middle">
                                <thead class="table-light">
                                    <tr class="text-center">
                                        <th width="5%">#</th>
                                        <th width="35%">{{ __('manufacturing::manufacturing.product name') }}</th>
                                        <th width="15%">{{ __('manufacturing::manufacturing.quantity') }}</th>
                                        <th width="15%">{{ __('manufacturing::manufacturing.unit cost') }}</th>
                                        <th width="15%">{{ __('manufacturing::manufacturing.cost percentage') }} %</th>
                                        <th width="15%">{{ __('manufacturing::manufacturing.total') }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($products as $index => $product)
                                        <tr>
                                            <td class="text-center">{{ $loop->iteration }}</td>
                                            <td><strong>{{ $product['name'] }}</strong></td>
                                            <td class="text-center">{{ number_format($product['quantity'], 2) }}</td>
                                            <td class="text-end">{{ number_format($product['unit_cost'], 2) }}
                                                {{ __('manufacturing::manufacturing.egp') }}</td>
                                            <td class="text-center">
                                                <span
                                                    class="badge bg-info">{{ number_format($product['cost_percentage'], 2) }}%</span>
                                            </td>
                                            <td class="text-end">
                                                <strong
                                                    class="text-success">{{ number_format($product['total_cost'], 2) }}
                                                    {{ __('manufacturing::manufacturing.egp') }}</strong>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                                <tfoot class="table-light">
                                    <tr>
                                        <td colspan="4" class="text-end"><strong>{{ __('manufacturing::manufacturing.total') }}:</strong></td>
                                        <td class="text-center">
                                            <strong
                                                class="badge bg-primary">{{ number_format(collect($products)->sum('cost_percentage'), 2) }}%</strong>
                                        </td>
                                        <td class="text-end">
                                            <strong
                                                class="text-success fs-5">{{ number_format($totals['products'], 2) }}
                                                {{ __('manufacturing::manufacturing.egp') }}</strong>
                                        </td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    @else
                        <div class="text-center py-5 text-muted">
                            <i class="fas fa-box-open fa-3x mb-3"></i>
                            <p>{{ __('manufacturing::manufacturing.no manufactured products') }}</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- المواد الخام -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card shadow-sm">
                <div class="card-header bg-light">
                    <h6 class="mb-0">
                        <i class="fas fa-cubes me-2"></i>
                        {{ __('manufacturing::manufacturing.raw materials') }}
                    </h6>
                </div>
                <div class="card-body">
                    @if (count($rawMaterials) > 0)
                        <div class="table-responsive">
                            <table class="table table-bordered table-hover align-middle">
                                <thead class="table-light">
                                    <tr class="text-center">
                                        <th width="5%">#</th>
                                        <th width="35%">{{ __('manufacturing::manufacturing.material name') }}</th>
                                        <th width="15%">{{ __('manufacturing::manufacturing.quantity') }}</th>
                                        <th width="15%">{{ __('manufacturing::manufacturing.unit') }}</th>
                                        <th width="15%">{{ __('manufacturing::manufacturing.cost price') }}</th>
                                        <th width="15%">{{ __('manufacturing::manufacturing.total') }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($rawMaterials as $index => $material)
                                        <tr>
                                            <td class="text-center">{{ $loop->iteration }}</td>
                                            <td>{{ $material['name'] }}</td>
                                            <td class="text-center">{{ number_format($material['quantity'], 2) }}</td>
                                            <td class="text-center">{{ $material['unit_name'] }}</td>
                                            <td class="text-end">{{ number_format($material['unit_cost'], 2) }}
                                                {{ __('manufacturing::manufacturing.egp') }}</td>
                                            <td class="text-end">
                                                <strong
                                                    class="text-info">{{ number_format($material['total_cost'], 2) }}
                                                    {{ __('manufacturing::manufacturing.egp') }}</strong>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                                <tfoot class="table-light">
                                    <tr>
                                        <td colspan="5" class="text-end"><strong>{{ __('manufacturing::manufacturing.total') }}:</strong>
                                        </td>
                                        <td class="text-end">
                                            <strong
                                                class="text-info fs-5">{{ number_format($totals['raw_materials'], 2) }}
                                                {{ __('manufacturing::manufacturing.egp') }}</strong>
                                        </td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    @else
                        <div class="text-center py-5 text-muted">
                            <i class="fas fa-cubes fa-3x mb-3"></i>
                            <p>{{ __('manufacturing::manufacturing.no raw materials') }}</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- المصروفات الإضافية -->
    @if (count($expenses) > 0)
        <div class="row mb-4">
            <div class="col-12">
                <div class="card shadow-sm">
                    <div class="card-header bg-light">
                        <h6 class="mb-0">
                            <i class="fas fa-money-bill-wave me-2"></i>
                            {{ __('manufacturing::manufacturing.additional expenses') }}
                        </h6>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-bordered table-hover align-middle">
                                <thead class="table-light">
                                    <tr class="text-center">
                                        <th width="5%">#</th>
                                        <th width="40%">{{ __('manufacturing::manufacturing.description') }}</th>
                                        <th width="35%">{{ __('manufacturing::manufacturing.account') }}</th>
                                        <th width="20%">{{ __('manufacturing::manufacturing.amount') }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($expenses as $index => $expense)
                                        <tr>
                                            <td class="text-center">{{ $loop->iteration }}</td>
                                            <td>{{ $expense['description'] }}</td>
                                            <td>{{ $expense['account_name'] }}</td>
                                            <td class="text-end">
                                                <strong
                                                    class="text-warning">{{ number_format($expense['amount'], 2) }}
                                                    {{ __('manufacturing::manufacturing.egp') }}</strong>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                                <tfoot class="table-light">
                                    <tr>
                                        <td colspan="3" class="text-end"><strong>{{ __('manufacturing::manufacturing.total') }}:</strong>
                                        </td>
                                        <td class="text-end">
                                            <strong
                                                class="text-warning fs-5">{{ number_format($totals['expenses'], 2) }}
                                                {{ __('manufacturing::manufacturing.egp') }}</strong>
                                        </td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif

    <!-- ملخص التكاليف -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="row gx-2 mb-3">
                <div class="col-md-3">
                    <div class="card border-0 shadow-sm h-100">
                        <div class="card-body p-3 text-center">
                            <div class="d-flex align-items-center justify-content-between">
                                <div class="text-start flex-grow-1">
                                    <small class="text-muted d-block mb-1">{{ __('manufacturing::manufacturing.total raw materials') }}</small>
                                    <h5 class="mb-0 text-info fw-bold">{{ number_format($totals['raw_materials'], 2) }}</h5>
                                    <small class="text-muted">{{ __('manufacturing::manufacturing.egp') }}</small>
                                </div>
                                <div class="bg-info bg-opacity-10 rounded p-3">
                                    <i class="fas fa-box fa-2x text-info"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-md-3">
                    <div class="card border-0 shadow-sm h-100">
                        <div class="card-body p-3 text-center">
                            <div class="d-flex align-items-center justify-content-between">
                                <div class="text-start flex-grow-1">
                                    <small class="text-muted d-block mb-1">{{ __('manufacturing::manufacturing.total expenses') }}</small>
                                    <h5 class="mb-0 text-warning fw-bold">{{ number_format($totals['expenses'], 2) }}</h5>
                                    <small class="text-muted">{{ __('manufacturing::manufacturing.egp') }}</small>
                                </div>
                                <div class="bg-warning bg-opacity-10 rounded p-3">
                                    <i class="fas fa-receipt fa-2x text-warning"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-md-3">
                    <div class="card border-0 shadow-sm h-100">
                        <div class="card-body p-3 text-center">
                            <div class="d-flex align-items-center justify-content-between">
                                <div class="text-start flex-grow-1">
                                    <small class="text-muted d-block mb-1">{{ __('manufacturing::manufacturing.total invoice cost') }}</small>
                                    <h5 class="mb-0 text-danger fw-bold">{{ number_format($totals['manufacturing_cost'], 2) }}</h5>
                                    <small class="text-muted">{{ __('manufacturing::manufacturing.egp') }}</small>
                                </div>
                                <div class="bg-danger bg-opacity-10 rounded p-3">
                                    <i class="fas fa-calculator fa-2x text-danger"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-md-3">
                    <div class="card border-0 shadow-sm h-100">
                        <div class="card-body p-3 text-center">
                            <div class="d-flex align-items-center justify-content-between">
                                <div class="text-start flex-grow-1">
                                    <small class="text-muted d-block mb-1">{{ __('manufacturing::manufacturing.total products value') }}</small>
                                    <h5 class="mb-0 text-success fw-bold">{{ number_format($totals['products'], 2) }}</h5>
                                    <small class="text-muted">{{ __('manufacturing::manufacturing.egp') }}</small>
                                </div>
                                <div class="bg-success bg-opacity-10 rounded p-3">
                                    <i class="fas fa-industry fa-2x text-success"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row gx-2">
                <div class="col-md-6">
                    <div class="card border-0 shadow-sm h-100">
                        <div class="card-body p-3">
                            <div class="d-flex align-items-center justify-content-between">
                                <div class="text-start flex-grow-1">
                                    <small class="text-muted d-block mb-1">{{ __('manufacturing::manufacturing.standard cost (template)') }}</small>
                                    <h5 class="mb-0 text-primary fw-bold">{{ number_format($totals['manufacturing_cost'], 2) }}</h5>
                                    <small class="text-muted">{{ __('manufacturing::manufacturing.egp') }}</small>
                                </div>
                                <div class="bg-primary bg-opacity-10 rounded p-3">
                                    <i class="fas fa-star fa-2x text-primary"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="card border-0 shadow-sm h-100">
                        <div class="card-body p-3">
                            @php
                                $variance = $totals['products'] - $totals['manufacturing_cost'];
                                $variancePercentage = $totals['manufacturing_cost'] > 0 
                                    ? ($variance / $totals['manufacturing_cost']) * 100 
                                    : 0;
                                $color = $variance >= 0 ? 'success' : 'danger';
                                $icon = $variance >= 0 ? 'arrow-up' : 'arrow-down';
                            @endphp
                            <div class="d-flex align-items-center justify-content-between">
                                <div class="text-start flex-grow-1">
                                    <small class="text-muted d-block mb-1">{{ __('manufacturing::manufacturing.variance (difference)') }}</small>
                                    <h5 class="mb-0 text-{{ $color }} fw-bold">
                                        <i class="fas fa-{{ $icon }} me-1"></i>
                                        {{ number_format(abs($variance), 2) }}
                                    </h5>
                                    <small class="text-muted">{{ __('manufacturing::manufacturing.egp') }}</small>
                                    <span class="badge bg-{{ $color }} ms-2">{{ number_format(abs($variancePercentage), 2) }}%</span>
                                </div>
                                <div class="bg-{{ $color }} bg-opacity-10 rounded p-3">
                                    <i class="fas fa-exchange-alt fa-2x text-{{ $color }}"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('styles')
<style>
    @media print {
        .no-print {
            display: none !important;
        }

        .card {
            border: 1px solid #dee2e6 !important;
            box-shadow: none !important;
            page-break-inside: avoid;
        }

        .card-header {
            -webkit-print-color-adjust: exact;
            print-color-adjust: exact;
        }

        body {
            font-size: 12px;
        }
    }
</style>
@endpush
@endsection
