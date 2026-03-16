@php
declare(strict_types=1);

use Livewire\Volt\Component;
use App\Models\ProductionOrder;
use App\Models\OperHead;

new class extends Component {
    public ?OperHead $invoice = null;
    public ?ProductionOrder $productionOrder = null;
    public array $products = [];
    public array $rawMaterials = [];
    public array $totals = [];

    public function mount(int $invoiceId): void
    {
        $this->invoice = OperHead::with([
            'operationItems.item.unit',
            'operationItems.unit',
            'acc1Head',
            'acc2Head',
            'store',
            'employee',
            'branch',
            'productionOrder',
        ])->findOrFail($invoiceId);

        $this->productionOrder = $this->invoice->productionOrder;
        $this->loadInvoiceData();
    }

    private function loadInvoiceData(): void
    {
        $this->products = [];
        $this->rawMaterials = [];
        $productsTotal = 0;
        $rawMaterialsTotal = 0;

        foreach ($this->invoice->operationItems as $item) {
            $totalCost = $item->quantity * $item->cost;
            
            if ($item->item_type === 1) {
                // منتج نهائي
                $this->products[] = [
                    'name' => $item->item->iname ?? __('common.unknown'),
                    'quantity' => $item->quantity,
                    'unit_name' => $item->unit->uname ?? '',
                    'unit_cost' => $item->cost,
                    'total_cost' => $totalCost,
                ];
                $productsTotal += $totalCost;
            } else {
                // مادة خام
                $this->rawMaterials[] = [
                    'name' => $item->item->iname ?? __('common.unknown'),
                    'quantity' => $item->quantity,
                    'unit_name' => $item->unit->uname ?? '',
                    'unit_cost' => $item->cost,
                    'total_cost' => $totalCost,
                ];
                $rawMaterialsTotal += $totalCost;
            }
        }

        $this->totals = [
            'products' => $productsTotal,
            'raw_materials' => $rawMaterialsTotal,
            'total' => $productsTotal + $rawMaterialsTotal,
        ];
    }
}; ?>

<div class="container-fluid" id="printable-invoice">
    <!-- Header -->
    <div class="row mb-4 no-print">
        <div class="col-12">
            <div class="card shadow-sm">
                <div class="card-header bg-light">
                    <div class="d-flex justify-content-between align-items-center">
                        <h4 class="mb-0">
                            <i class="fas fa-file-invoice me-2"></i>
                            {{ __('items.manufacturing_invoice_details') }}
                        </h4>
                        <div class="btn-group">
                            <a href="{{ route('production-orders.index') }}" class="btn btn-light btn-sm">
                                <i class="fas fa-arrow-left me-1"></i> {{ __('common.back') }}
                            </a>
                            <button onclick="window.print()" class="btn btn-info btn-sm">
                                <i class="fas fa-print me-1"></i> {{ __('common.print') }}
                            </button>
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
                <div class="card-header bg-primary text-white">
                    <h6 class="mb-0"><i class="fas fa-info-circle me-2"></i>{{ __('common.invoice_information') }}</h6>
                </div>
                <div class="card-body">
                    <table class="table table-sm table-borderless mb-0">
                        <tr>
                            <td width="40%" class="text-muted">{{ __('common.invoice_number') }}:</td>
                            <td><strong class="text-primary">{{ $invoice->pro_id }}</strong></td>
                        </tr>
                        <tr>
                            <td class="text-muted">{{ __('common.date') }}:</td>
                            <td><strong>{{ $invoice->pro_date }}</strong></td>
                        </tr>
                        <tr>
                            <td class="text-muted">{{ __('common.serial_number') }}:</td>
                            <td>{{ $invoice->pro_serial ?: '-' }}</td>
                        </tr>
                        @if($invoice->info)
                        <tr>
                            <td class="text-muted">{{ __('common.description') }}:</td>
                            <td>{{ $invoice->info }}</td>
                        </tr>
                        @endif
                    </table>
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="card shadow-sm h-100">
                <div class="card-header bg-primary text-white">
                    <h6 class="mb-0"><i class="fas fa-building me-2"></i>{{ __('common.additional_information') }}</h6>
                </div>
                <div class="card-body">
                    <table class="table table-sm table-borderless mb-0">
                        @if($productionOrder)
                        <tr>
                            <td width="40%" class="text-muted">{{ __('common.production_order') }}:</td>
                            <td><strong>{{ $productionOrder->order_number }}</strong></td>
                        </tr>
                        @endif
                        <tr>
                            <td class="text-muted">{{ __('common.employee') }}:</td>
                            <td>{{ $invoice->employee->aname ?? '-' }}</td>
                        </tr>
                        <tr>
                            <td class="text-muted">{{ __('common.branch') }}:</td>
                            <td>{{ $invoice->branch->name ?? '-' }}</td>
                        </tr>
                        <tr>
                            <td class="text-muted">{{ __('common.products_account') }}:</td>
                            <td>{{ $invoice->acc1Head->aname ?? '-' }}</td>
                        </tr>
                        <tr>
                            <td class="text-muted">{{ __('common.raw_materials_account') }}:</td>
                            <td>{{ $invoice->acc2Head->aname ?? '-' }}</td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- المنتجات المصنعة -->
    @if(count($products) > 0)
    <div class="row mb-4">
        <div class="col-12">
            <div class="card shadow-sm">
                <div class="card-header bg-success text-white">
                    <h6 class="mb-0">
                        <i class="fas fa-box me-2"></i>
                        {{ __('items.manufactured_products') }}
                    </h6>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover align-middle mb-0">
                            <thead class="table-light">
                                <tr class="text-center">
                                    <th width="5%">#</th>
                                    <th width="40%">{{ __('items.product_name') }}</th>
                                    <th width="15%">{{ __('items.quantity') }}</th>
                                    <th width="15%">{{ __('items.unit') }}</th>
                                    <th width="15%">{{ __('items.unit_cost') }}</th>
                                    <th width="15%">{{ __('common.total') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($products as $index => $product)
                                <tr>
                                    <td class="text-center">{{ $loop->iteration }}</td>
                                    <td><strong>{{ $product['name'] }}</strong></td>
                                    <td class="text-center">{{ number_format($product['quantity'], 2) }}</td>
                                    <td class="text-center">{{ $product['unit_name'] }}</td>
                                    <td class="text-end">{{ number_format($product['unit_cost'], 2) }}</td>
                                    <td class="text-end">
                                        <strong class="text-success">{{ number_format($product['total_cost'], 2) }}</strong>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                            <tfoot class="table-light">
                                <tr>
                                    <td colspan="5" class="text-end"><strong>{{ __('common.total') }}:</strong></td>
                                    <td class="text-end">
                                        <strong class="text-success fs-5">{{ number_format($totals['products'], 2) }}</strong>
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

    <!-- المواد الخام -->
    @if(count($rawMaterials) > 0)
    <div class="row mb-4">
        <div class="col-12">
            <div class="card shadow-sm">
                <div class="card-header bg-info text-white">
                    <h6 class="mb-0">
                        <i class="fas fa-cubes me-2"></i>
                        {{ __('items.raw_materials') }}
                    </h6>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover align-middle mb-0">
                            <thead class="table-light">
                                <tr class="text-center">
                                    <th width="5%">#</th>
                                    <th width="40%">{{ __('items.material_name') }}</th>
                                    <th width="15%">{{ __('items.quantity') }}</th>
                                    <th width="15%">{{ __('items.unit') }}</th>
                                    <th width="15%">{{ __('items.cost_price') }}</th>
                                    <th width="15%">{{ __('common.total') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($rawMaterials as $index => $material)
                                <tr>
                                    <td class="text-center">{{ $loop->iteration }}</td>
                                    <td>{{ $material['name'] }}</td>
                                    <td class="text-center">{{ number_format($material['quantity'], 2) }}</td>
                                    <td class="text-center">{{ $material['unit_name'] }}</td>
                                    <td class="text-end">{{ number_format($material['unit_cost'], 2) }}</td>
                                    <td class="text-end">
                                        <strong class="text-info">{{ number_format($material['total_cost'], 2) }}</strong>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                            <tfoot class="table-light">
                                <tr>
                                    <td colspan="5" class="text-end"><strong>{{ __('common.total') }}:</strong></td>
                                    <td class="text-end">
                                        <strong class="text-info fs-5">{{ number_format($totals['raw_materials'], 2) }}</strong>
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
            <div class="card shadow-sm">
                <div class="card-body">
                    <div class="row text-center">
                        <div class="col-md-4">
                            <div class="d-flex align-items-center justify-content-center gap-3">
                                <i class="fas fa-box fa-2x text-success"></i>
                                <div class="text-start">
                                    <small class="text-muted d-block">{{ __('items.total_products_value') }}</small>
                                    <h4 class="text-success mb-0">{{ number_format($totals['products'], 2) }}</h4>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="d-flex align-items-center justify-content-center gap-3">
                                <i class="fas fa-cubes fa-2x text-info"></i>
                                <div class="text-start">
                                    <small class="text-muted d-block">{{ __('items.total_raw_materials') }}</small>
                                    <h4 class="text-info mb-0">{{ number_format($totals['raw_materials'], 2) }}</h4>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="d-flex align-items-center justify-content-center gap-3">
                                <i class="fas fa-calculator fa-2x text-primary"></i>
                                <div class="text-start">
                                    <small class="text-muted d-block">{{ __('items.total_invoice_cost') }}</small>
                                    <h4 class="text-primary mb-0">{{ number_format($totals['total'], 2) }}</h4>
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
            margin-bottom: 10px !important;
        }

        .card-header {
            -webkit-print-color-adjust: exact;
            print-color-adjust: exact;
            padding: 8px 12px !important;
        }

        .card-body {
            padding: 10px !important;
        }

        body {
            font-size: 11px;
        }

        .table {
            font-size: 10px;
            margin-bottom: 5px !important;
        }

        .table th,
        .table td {
            padding: 4px 6px !important;
        }

        /* تقليل المسافات في معلومات الفاتورة */
        .table-sm tr {
            line-height: 1.2;
        }

        .table-sm td {
            padding: 2px 4px !important;
        }

        h4, h5, h6 {
            margin-bottom: 5px !important;
            font-size: 12px !important;
        }

        .row.mb-4 {
            margin-bottom: 10px !important;
        }

        /* تقليل ارتفاع الصفوف في قسم المعلومات */
        .row.mb-4 .col-md-6 {
            margin-bottom: 5px !important;
        }

        /* ملخص التكاليف في سطر واحد */
        .row.text-center .col-md-4 {
            padding: 5px !important;
        }

        .row.text-center h4 {
            font-size: 14px !important;
        }

        .row.text-center small {
            font-size: 9px !important;
        }

        .row.text-center i {
            font-size: 1.2rem !important;
        }
    }
</style>
@endpush
