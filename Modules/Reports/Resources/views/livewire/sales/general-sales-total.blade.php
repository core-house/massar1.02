?php

declare(strict_types=1);

use Livewire\Volt\Component;
use Livewire\WithPagination;
use Illuminate\Support\Facades\DB;
use Modules\Accounts\Models\AccHead;

new class extends Component {
    use WithPagination;

    protected $paginationTheme = 'bootstrap';

    public $fromDate;
    public $toDate;
    public $groupBy = 'day';

    public function mount(): void
    {
        $this->groupBy = request('group_by', 'day');
        $this->fromDate = request('from_date') ?? today()->format('Y-m-d');
        $this->toDate = request('to_date') ?? today()->format('Y-m-d');
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

    public function updatedGroupBy(): void
    {
        $this->resetPage();
    }

    public function getSalesTotalsProperty()
    {
        $query = DB::table('operhead')->join('operation_items', 'operhead.id', '=', 'operation_items.pro_id')->where('operhead.pro_type', 10);

        if ($this->fromDate) {
            $query->whereDate('operhead.pro_date', '>=', $this->fromDate);
        }
        if ($this->toDate) {
            $query->whereDate('operhead.pro_date', '<=', $this->toDate);
        }

        if ($this->groupBy === 'day') {
            $salesTotals = $query
                ->selectRaw(
                    '
                DATE(operhead.pro_date) as period_name,
                COUNT(DISTINCT operhead.id) as invoices_count,
                SUM(operation_items.qty_out) as total_quantity,
                SUM(operation_items.qty_out * operation_items.item_price) as total_sales,
                SUM(operhead.fat_disc) as total_discount,
                SUM(operhead.fat_net) as net_sales
            ',
                )
                ->groupBy(DB::raw('DATE(operhead.pro_date)'))
                ->orderBy(DB::raw('DATE(operhead.pro_date)'), 'desc')
                ->paginate(50);
        } elseif ($this->groupBy === 'week') {
            $salesTotals = $query
                ->selectRaw(
                    '
                YEAR(operhead.pro_date) as year,
                WEEK(operhead.pro_date, 1) as week,
                CONCAT(YEAR(operhead.pro_date), "-W", LPAD(WEEK(operhead.pro_date, 1), 2, "0")) as period_name,
                COUNT(DISTINCT operhead.id) as invoices_count,
                SUM(operation_items.qty_out) as total_quantity,
                SUM(operation_items.qty_out * operation_items.item_price) as total_sales,
                SUM(operhead.fat_disc) as total_discount,
                SUM(operhead.fat_net) as net_sales
            ',
                )
                ->groupBy(DB::raw('YEAR(operhead.pro_date)'), DB::raw('WEEK(operhead.pro_date, 1)'), DB::raw('CONCAT(YEAR(operhead.pro_date), "-W", LPAD(WEEK(operhead.pro_date, 1), 2, "0"))'))
                ->orderBy(DB::raw('YEAR(operhead.pro_date)'), 'desc')
                ->orderBy(DB::raw('WEEK(operhead.pro_date, 1)'), 'desc')
                ->paginate(50);
        } elseif ($this->groupBy === 'month') {
            $salesTotals = $query
                ->selectRaw(
                    '
                YEAR(operhead.pro_date) as year,
                MONTH(operhead.pro_date) as month,
                CONCAT(YEAR(operhead.pro_date), "-", LPAD(MONTH(operhead.pro_date), 2, "0")) as period_name,
                COUNT(DISTINCT operhead.id) as invoices_count,
                SUM(operation_items.qty_out) as total_quantity,
                SUM(operation_items.qty_out * operation_items.item_price) as total_sales,
                SUM(operhead.fat_disc) as total_discount,
                SUM(operhead.fat_net) as net_sales
            ',
                )
                ->groupBy(DB::raw('YEAR(operhead.pro_date)'), DB::raw('MONTH(operhead.pro_date)'), DB::raw('CONCAT(YEAR(operhead.pro_date), "-", LPAD(MONTH(operhead.pro_date), 2, "0"))'))
                ->orderBy(DB::raw('YEAR(operhead.pro_date)'), 'desc')
                ->orderBy(DB::raw('MONTH(operhead.pro_date)'), 'desc')
                ->paginate(50);
        } elseif ($this->groupBy === 'customer') {
            $salesTotals = $query
                ->join('acc_head', 'operhead.acc1', '=', 'acc_head.id')
                ->selectRaw(
                    '
                    operhead.acc1 as customer_id,
                    acc_head.aname as customer_name,
                    acc_head.aname as period_name,
                    COUNT(DISTINCT operhead.id) as invoices_count,
                    SUM(operation_items.qty_out) as total_quantity,
                    SUM(operation_items.qty_out * operation_items.item_price) as total_sales,
                    SUM(operhead.fat_disc) as total_discount,
                    SUM(operhead.fat_net) as net_sales
                ',
                )
                ->groupBy('operhead.acc1', 'acc_head.aname')
                ->orderBy('net_sales', 'desc')
                ->paginate(50);
        } else {
            $salesTotals = $query
                ->selectRaw(
                    '   "Total" as period_name,
        COUNT(DISTINCT operhead.id) as invoices_count,
        SUM(operation_items.qty_out) as total_quantity,
        SUM(operation_items.qty_out * operation_items.item_price) as total_sales,
        SUM(operhead.fat_disc) as total_discount,
        SUM(operhead.fat_net) as net_sales',
                )
                ->paginate(50);
        }

        // أضف متوسط الفاتورة لكل صف
        foreach ($salesTotals->getCollection() as $row) {
            $row->average_invoice = $row->invoices_count > 0 ? $row->net_sales / $row->invoices_count : 0;
        }

        return $salesTotals;
    }

    public function getGrandTotalInvoicesProperty(): int
    {
        return $this->salesTotals->sum('invoices_count');
    }

    public function getGrandTotalQuantityProperty(): float
    {
        return (float) $this->salesTotals->sum('total_quantity');
    }

    public function getGrandTotalSalesProperty(): float
    {
        return (float) $this->salesTotals->sum('total_sales');
    }

    public function getGrandTotalDiscountProperty(): float
    {
        return (float) $this->salesTotals->sum('total_discount');
    }

    public function getGrandTotalNetSalesProperty(): float
    {
        return (float) $this->salesTotals->sum('net_sales');
    }

    public function getGrandAverageInvoiceProperty(): float
    {
        $grandTotalInvoices = $this->grandTotalInvoices;
        $grandTotalNetSales = $this->grandTotalNetSales;

        return $grandTotalInvoices > 0 ? $grandTotalNetSales / $grandTotalInvoices : 0;
    }

    public function getTotalPeriodsProperty(): int
    {
        return $this->salesTotals->count();
    }

    public function getHighestSalesProperty(): float
    {
        return (float) ($this->salesTotals->max('net_sales') ?? 0);
    }

    public function getLowestSalesProperty(): float
    {
        return (float) ($this->salesTotals->min('net_sales') ?? 0);
    }

    public function getAverageSalesProperty(): float
    {
        $totalPeriods = $this->totalPeriods;
        $grandTotalNetSales = $this->grandTotalNetSales;

        return $totalPeriods > 0 ? $grandTotalNetSales / $totalPeriods : 0;
    }
}; ?>
<div class="container">
    <div class="card">
        <div class="card-head">
            <h2>{{ __('reports::reports.Sales Totals Report') }}</h2>
        </div>
        <div class="card-body">
            <div class="row mb-3">
                <div class="col-md-3">
                    <label for="from_date" class="form-label fw-bold">{{ __('reports::reports.from_date') }}:</label>
                    <input type="date" id="from_date" class="form-control" wire:model.live="fromDate">
                </div>
                <div class="col-md-3">
                    <label for="to_date" class="form-label fw-bold">{{ __('reports::reports.to_date') }}:</label>
                    <input type="date" id="to_date" class="form-control" wire:model.live="toDate">
                </div>
                <div class="col-md-3">
                    <label for="group_by" class="form-label fw-bold">{{ __('reports::reports.group_by') }}:</label>
                    <select id="group_by" class="form-select" wire:model.live="groupBy">
                        <option value="day">{{ __('reports::reports.day') }}</option>
                        <option value="week">{{ __('reports::reports.week') }}</option>
                        <option value="month">{{ __('reports::reports.month') }}</option>
                        <option value="customer">{{ __('reports::reports.customer') }}</option>
                    </select>
                </div>
                <div class="col-md-3 d-flex align-items-end">
                    <button class="btn btn-primary w-100" wire:click="generateReport">
                        <i class="fas fa-chart-pie me-2"></i>{{ __('reports::reports.generate_report') }}
                    </button>
                </div>
            </div>

            <!-- Summary Cards -->
            <div class="row mb-4 g-3">
                <div class="col-md-3">
                    <div class="card bg-info text-white shadow-sm h-100">
                        <div class="card-body text-center">
                            <i class="fas fa-file-invoice fa-2x mb-2 opacity-75"></i>
                            <h6 class="fw-bold">{{ __('reports::reports.invoices_count') }}</h6>
                            <h4 class="fw-bold mb-0">{{ $grandTotalInvoices ?? 0 }}</h4>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card bg-success text-white shadow-sm h-100">
                        <div class="card-body text-center">
                            <i class="fas fa-boxes fa-2x mb-2 opacity-75"></i>
                            <h6 class="fw-bold">{{ __('reports::reports.total_quantity') }}</h6>
                            <h4 class="fw-bold mb-0">{{ number_format($grandTotalQuantity ?? 0, 2) }}</h4>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card bg-primary text-white shadow-sm h-100">
                        <div class="card-body text-center">
                            <i class="fas fa-dollar-sign fa-2x mb-2 opacity-75"></i>
                            <h6 class="fw-bold">{{ __('reports::reports.total_sales') }}</h6>
                            <h4 class="fw-bold mb-0">{{ number_format($grandTotalSales ?? 0, 2) }}</h4>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card bg-warning text-white shadow-sm h-100">
                        <div class="card-body text-center">
                            <i class="fas fa-calculator fa-2x mb-2 opacity-75"></i>
                            <h6 class="fw-bold">{{ __('reports::reports.grand_total_net_sales') }}</h6>
                            <h4 class="fw-bold mb-0">{{ number_format($grandTotalNetSales ?? 0, 2) }}</h4>
                        </div>
                    </div>
                </div>
            </div>

            <div class="table-responsive">
                <table class="table table-bordered table-striped table-hover">
                    <thead>
                        <tr>
                            <th class="text-center fw-bold">
                                {{ $groupBy == 'customer' ? __('reports::reports.customer') : __('reports::reports.period') }}
                            </th>
                            <th class="text-end fw-bold">{{ __('reports::reports.invoices_count') }}</th>
                            <th class="text-end fw-bold">{{ __('reports::reports.total_quantity') }}</th>
                            <th class="text-end fw-bold text-success">{{ __('reports::reports.total_sales') }}</th>
                            <th class="text-end fw-bold text-warning">{{ __('reports::reports.total_discount') }}</th>
                            <th class="text-end fw-bold text-info">{{ __('reports::reports.net_sales') }}</th>
                            <th class="text-end fw-bold text-primary">{{ __('reports::reports.average_invoice') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($salesTotals ?? collect() as $total)
                            <tr class="{{ $total->net_sales > 0 ? 'table-light' : 'table-secondary' }}">
                                <td class="fw-semibold">
                                    @if ($groupBy == 'customer')
                                        <i class="fas fa-user me-2 text-info"></i>
                                        {{ $total->customer_name ?? '---' }}
                                    @else
                                        <i class="fas fa-calendar-alt me-2 text-primary"></i>
                                        {{ $total->period_name ?? '---' }}
                                    @endif
                                </td>
                                <td class="text-end">
                                    <span class="badge bg-info">{{ $total->invoices_count ?? 0 }}</span>
                                </td>
                                <td class="text-end fw-bold">{{ number_format($total->total_quantity ?? 0, 2) }}</td>
                                <td class="text-end fw-bold text-success fs-6">
                                    {{ number_format($total->total_sales ?? 0, 2) }}
                                </td>
                                <td class="text-end fw-bold text-warning">
                                    {{ number_format($total->total_discount ?? 0, 2) }}
                                </td>
                                <td class="text-end fw-bold text-info fs-6">
                                    {{ number_format($total->net_sales ?? 0, 2) }}
                                </td>
                                <td class="text-end fw-bold text-primary">
                                    {{ number_format($total->average_invoice ?? 0, 2) }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center py-4">
                                    <div class="alert alert-info mb-0">
                                        <i class="fas fa-chart-line fa-2x mb-3 d-block"></i>
                                        {{ __('reports::reports.no_data_available') }}
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                    <tfoot class="table-primary">
                        <tr>
                            <th class="text-end fw-bold fs-5">{{ __('reports::reports.grand_total') }}</th>
                            <th class="text-end fw-bold fs-5">{{ $grandTotalInvoices ?? 0 }}</th>
                            <th class="text-end fw-bold fs-5">{{ number_format($grandTotalQuantity ?? 0, 2) }}</th>
                            <th class="text-end fw-bold text-success fs-5">
                                {{ number_format($grandTotalSales ?? 0, 2) }}</th>
                            <th class="text-end fw-bold text-warning fs-5">
                                {{ number_format($grandTotalDiscount ?? 0, 2) }}</th>
                            <th class="text-end fw-bold text-info fs-5">
                                {{ number_format($grandTotalNetSales ?? 0, 2) }}</th>
                            <th class="text-end fw-bold text-primary fs-5">
                                {{ number_format($grandAverageInvoice ?? 0, 2) }}</th>
                        </tr>
                    </tfoot>
                </table>
            </div>

            @if (isset($salesTotals) && $salesTotals->hasPages())
                <div class="d-flex justify-content-center mt-4">
                    {{ $salesTotals->links() }}
                </div>
            @endif

            <!-- Analytics Summary -->
            @if (isset($salesTotals) && $salesTotals->count() > 0)
                <div class="row mt-4 g-3">
                    <div class="col-md-3">
                        <div class="alert alert-info shadow-sm">
                            <i class="fas fa-calendar-week fa-2x float-start me-2 mb-2"></i>
                            <strong>{{ __('reports::reports.total_periods') }}:</strong> {{ $totalPeriods ?? 0 }}
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="alert alert-success shadow-sm">
                            <i class="fas fa-trophy fa-2x float-start me-2 mb-2"></i>
                            <strong>{{ __('reports::reports.Highest Sales') }}:</strong> {{ number_format($highestSales ?? 0, 2) }}
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="alert alert-warning shadow-sm">
                            <i class="fas fa-chart-line fa-2x float-start me-2 mb-2"></i>
                            <strong>{{ __('reports::reports.Lowest Sales') }}:</strong> {{ number_format($lowestSales ?? 0, 2) }}
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="alert alert-primary shadow-sm">
                            <i class="fas fa-calculator fa-2x float-start me-2 mb-2"></i>
                            <strong>{{ __('reports::reports.average_sales_per_customer') }}:</strong> {{ number_format($averageSales ?? 0, 2) }}
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>

