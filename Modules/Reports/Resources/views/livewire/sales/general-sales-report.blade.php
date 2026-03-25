?php

declare(strict_types=1);

use Livewire\Volt\Component;
use Livewire\WithPagination;
use Modules\Accounts\Models\AccHead;
use App\Models\OperHead;
use Carbon\Carbon;

new class extends Component {
    use WithPagination;

    protected $paginationTheme = 'bootstrap';

    public $fromDate;
    public $toDate;
    public $customerId = null;
    public $customers = [];

    public function mount(): void
    {
        $this->fromDate = today()->format('Y-m-d');
        $this->toDate = today()->format('Y-m-d');
        $this->loadCustomers();
        $this->generateReport();
    }

    public function loadCustomers(): void
    {
        $this->customers = AccHead::where('code', 'like', '1103%')->where('isdeleted', 0)->orderBy('aname')->get();
    }

    public function generateReport(): void
    {
        $this->resetPage();
    }

    public function updatedFromDate(): void
    {
        $this->resetPage();
    }

    public function updatedToDate(): void
    {
        $this->resetPage();
    }

    public function updatedCustomerId(): void
    {
        $this->resetPage();
    }

    public function getSalesProperty()
    {
        $query = OperHead::where('pro_type', 10)
            ->with(['acc1Head', 'operationItems'])
            ->when($this->fromDate, function ($q) {
                $q->whereDate('pro_date', '>=', $this->fromDate);
            })
            ->when($this->toDate, function ($q) {
                $q->whereDate('pro_date', '<=', $this->toDate);
            })
            ->when($this->customerId, function ($q) {
                $q->where('acc1', $this->customerId);
            })
            ->orderBy('pro_date', 'desc');

        $sales = $query->paginate(50);

        // Calculate additional fields for each sale
        $sales->getCollection()->transform(function ($sale) {
            $sale->items_count = $sale->operationItems->count() ?? 0;
            $sale->total_quantity = $sale->operationItems->sum('qty_out') ?? 0;
            $sale->total_sales = $sale->fat_total ?? 0;
            $sale->discount = $sale->fat_disc ?? 0;
            $sale->net_sales = $sale->fat_net ?? 0;
            $sale->status = $sale->isdeleted == 0 ? 'completed' : 'pending';
            return $sale;
        });

        return $sales;
    }

    public function getTotalQuantityProperty(): float
    {
        $query = OperHead::where('pro_type', 10)
            ->with('operationItems')
            ->when($this->fromDate, function ($q) {
                $q->whereDate('pro_date', '>=', $this->fromDate);
            })
            ->when($this->toDate, function ($q) {
                $q->whereDate('pro_date', '<=', $this->toDate);
            })
            ->when($this->customerId, function ($q) {
                $q->where('acc1', $this->customerId);
            })
            ->get();

        return (float) $query->sum(function ($sale) {
            return (float) ($sale->operationItems->sum('qty_out') ?? 0);
        });
    }

    public function getTotalSalesProperty(): float
    {
        return (float) (OperHead::where('pro_type', 10)
            ->when($this->fromDate, function ($q) {
                $q->whereDate('pro_date', '>=', $this->fromDate);
            })
            ->when($this->toDate, function ($q) {
                $q->whereDate('pro_date', '<=', $this->toDate);
            })
            ->when($this->customerId, function ($q) {
                $q->where('acc1', $this->customerId);
            })
            ->sum('fat_total') ?? 0);
    }

    public function getTotalDiscountProperty(): float
    {
        return (float) (OperHead::where('pro_type', 10)
            ->when($this->fromDate, function ($q) {
                $q->whereDate('pro_date', '>=', $this->fromDate);
            })
            ->when($this->toDate, function ($q) {
                $q->whereDate('pro_date', '<=', $this->toDate);
            })
            ->when($this->customerId, function ($q) {
                $q->where('acc1', $this->customerId);
            })
            ->sum('fat_disc') ?? 0);
    }

    public function getTotalNetSalesProperty(): float
    {
        return (float) (OperHead::where('pro_type', 10)
            ->when($this->fromDate, function ($q) {
                $q->whereDate('pro_date', '>=', $this->fromDate);
            })
            ->when($this->toDate, function ($q) {
                $q->whereDate('pro_date', '<=', $this->toDate);
            })
            ->when($this->customerId, function ($q) {
                $q->where('acc1', $this->customerId);
            })
            ->sum('fat_net') ?? 0);
    }

    public function getTotalInvoicesProperty(): int
    {
        return OperHead::where('pro_type', 10)
            ->when($this->fromDate, function ($q) {
                $q->whereDate('pro_date', '>=', $this->fromDate);
            })
            ->when($this->toDate, function ($q) {
                $q->whereDate('pro_date', '<=', $this->toDate);
            })
            ->when($this->customerId, function ($q) {
                $q->where('acc1', $this->customerId);
            })
            ->count();
    }

    public function getAverageInvoiceValueProperty(): float
    {
        $totalInvoices = $this->totalInvoices;
        $totalNetSales = $this->totalNetSales;

        return $totalInvoices > 0 ? (float) ($totalNetSales / $totalInvoices) : 0.0;
    }
}; ?>

<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">{{ __('reports::reports.general_sales_report') }}</h4>
                </div>
                <div class="card-body">
                    <!-- Filters -->
                    <div class="row mb-3">
                        <div class="col-md-3">
                            <label for="from_date" class="form-label fw-bold">{{ __('reports::reports.from_date') }}</label>
                            <input type="date" wire:model.live="fromDate" class="form-control" id="from_date">
                        </div>
                        <div class="col-md-3">
                            <label for="to_date" class="form-label fw-bold">{{ __('reports::reports.to_date') }}</label>
                            <input type="date" wire:model.live="toDate" class="form-control" id="to_date">
                        </div>
                        <div class="col-md-3">
                            <label for="customer_id" class="form-label fw-bold">{{ __('reports::reports.customer') }}</label>
                            <select wire:model.live="customerId" class="form-select" id="customer_id">
                                <option value="">{{ __('reports::reports.all_customers') }}</option>
                                @foreach ($customers as $customer)
                                    <option value="{{ $customer->id }}">{{ $customer->aname }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-3 d-flex align-items-end">
                            <button wire:click="generateReport" class="btn btn-primary w-100">
                                <i class="fas fa-chart-line me-2"></i>{{ __('reports::reports.generate_report') }}
                            </button>
                        </div>
                    </div>

                    <!-- Summary Cards -->
                    <div class="row mb-4 g-3">
                        <div class="col-md-2">
                            <div class="card bg-primary text-white shadow-sm h-100">
                                <div class="card-body text-center">
                                    <i class="fas fa-boxes fa-2x mb-2 opacity-75"></i>
                                    <h6 class="card-title fw-bold">{{ __('reports::reports.total_quantity') }}</h6>
                                    <h4 class="fw-bold mb-0">{{ number_format($totalQuantity ?? 0, 2) }}</h4>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="card bg-success text-white shadow-sm h-100">
                                <div class="card-body text-center">
                                    <i class="fas fa-dollar-sign fa-2x mb-2 opacity-75"></i>
                                    <h6 class="card-title fw-bold">{{ __('reports::reports.total_sales') }}</h6>
                                    <h4 class="fw-bold mb-0">{{ number_format($totalSales ?? 0, 2) }}</h4>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="card bg-warning text-white shadow-sm h-100">
                                <div class="card-body text-center">
                                    <i class="fas fa-tags fa-2x mb-2 opacity-75"></i>
                                    <h6 class="card-title fw-bold">{{ __('reports::reports.total_discount') }}</h6>
                                    <h4 class="fw-bold mb-0">{{ number_format($totalDiscount ?? 0, 2) }}</h4>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="card bg-info text-white shadow-sm h-100">
                                <div class="card-body text-center">
                                    <i class="fas fa-calculator fa-2x mb-2 opacity-75"></i>
                                    <h6 class="card-title fw-bold">{{ __('reports::reports.net_sales') }}</h6>
                                    <h4 class="fw-bold mb-0">{{ number_format($totalNetSales ?? 0, 2) }}</h4>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="card bg-secondary text-white shadow-sm h-100">
                                <div class="card-body text-center">
                                    <i class="fas fa-file-invoice fa-2x mb-2 opacity-75"></i>
                                    <h6 class="card-title fw-bold">{{ __('reports::reports.total_invoices') }}</h6>
                                    <h4 class="fw-bold mb-0">{{ $totalInvoices ?? 0 }}</h4>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="card shadow-sm h-100">
                                <div class="card-body text-center">
                                    <i class="fas fa-chart-bar fa-2x mb-2 opacity-75"></i>
                                    <h6 class="card-title fw-bold">{{ __('reports::reports.average_invoice_value') }}</h6>
                                    <h4 class="fw-bold mb-0">{{ number_format($averageInvoiceValue ?? 0, 2) }}</h4>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Data Table -->
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped table-hover">
                            <thead >
                                <tr>
                                    <th class="text-center">{{ __('reports::reports.date') }}</th>
                                    <th class="text-center">{{ __('reports::reports.operation_number') }}</th>
                                    <th>{{ __('reports::reports.customer') }}</th>
                                    <th class="text-end fw-bold">{{ __('reports::reports.items_count') }}</th>
                                    <th class="text-end fw-bold">{{ __('reports::reports.total_quantity') }}</th>
                                    <th class="text-end fw-bold text-success">{{ __('reports::reports.total_sales') }}</th>
                                    <th class="text-end fw-bold text-warning">{{ __('reports::reports.total_discount') }}</th>
                                    <th class="text-end fw-bold text-info">{{ __('reports::reports.net_sales') }}</th>
                                    <th class="text-center">{{ __('reports::reports.status') }}</th>
                                    <th class="text-center">{{ __('reports::reports.actions') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($sales ?? collect() as $sale)
                                    <tr>
                                        <td class="text-center fw-semibold">
                                            {{ $sale->pro_date ? \Carbon\Carbon::parse($sale->pro_date)->format('Y-m-d') : '---' }}
                                        </td>
                                        <td class="text-center">
                                            <span class="badge bg-primary">{{ $sale->pro_num ?? '---' }}</span>
                                        </td>
                                        <td>
                                            <strong>{{ $sale->acc1Head->aname ?? '---' }}</strong>
                                        </td>
                                        <td class="text-end">
                                            <span class="badge bg-info">{{ $sale->items_count ?? 0 }}</span>
                                        </td>
                                        <td class="text-end fw-bold text-primary">
                                            {{ number_format($sale->total_quantity ?? 0, 2) }}
                                        </td>
                                        <td class="text-end fw-bold text-success fs-6">
                                            {{ number_format($sale->total_sales ?? 0, 2) }}
                                        </td>
                                        <td class="text-end fw-bold text-warning">
                                            {{ number_format($sale->discount ?? 0, 2) }}
                                        </td>
                                        <td class="text-end fw-bold text-info fs-6">
                                            {{ number_format($sale->net_sales ?? 0, 2) }}
                                        </td>
                                        <td class="text-center">
                                            @if ($sale->status == 'completed')
                                                <span class="badge bg-success fs-6">{{ __('reports::reports.completed') }}</span>
                                            @elseif($sale->status == 'pending')
                                                <span class="badge bg-warning fs-6">{{ __('reports::reports.pending') }}</span>
                                            @elseif($sale->status == 'cancelled')
                                                <span class="badge bg-danger fs-6">{{ __('reports::reports.cancelled') }}</span>
                                            @else
                                                <span class="badge bg-secondary">{{ __('reports::reports.unspecified') }}</span>
                                            @endif
                                        </td>
                                        <td class="text-center">
                                            <div class="btn-group btn-group-sm" role="group">
                                                <a href="#" class="btn btn-outline-info"
                                                    title="{{ __('reports::reports.view') }}">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                                <a href="#" class="btn btn-outline-primary"
                                                    title="{{ __('reports::reports.view_edit') }}">
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
                                        <td colspan="10" class="text-center py-4">
                                            <div class="alert alert-info mb-0">
                                                <i class="fas fa-inbox fa-2x mb-3 d-block"></i>
                                                {{ __('reports::reports.no_sales_data_available') }}
                                            </div>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    @if (isset($sales) && $sales->hasPages())
                        <div class="d-flex justify-content-center mt-4">
                            {{ $sales->links() }}
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

