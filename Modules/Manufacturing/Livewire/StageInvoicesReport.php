<?php

namespace Modules\Manufacturing\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use Modules\Manufacturing\Models\ManufacturingOrder;
use Modules\Manufacturing\Models\ManufacturingStage;
use App\Models\OperHead;

class StageInvoicesReport extends Component
{
    use WithPagination;

    public $selectedOrderId = '';
    public $selectedStageId = '';
    public $dateFrom = '';
    public $dateTo = '';
    public $searchTerm = '';

    protected $queryString = [
        'selectedOrderId' => ['except' => ''],
        'selectedStageId' => ['except' => ''],
        'dateFrom' => ['except' => ''],
        'dateTo' => ['except' => ''],
    ];

    public function mount()
    {
        // Set default date range to current month
        $this->dateFrom = now()->startOfMonth()->format('Y-m-d');
        $this->dateTo = now()->endOfMonth()->format('Y-m-d');
    }

    public function updatingSearchTerm()
    {
        $this->resetPage();
    }

    public function updatingSelectedOrderId()
    {
        $this->resetPage();
        $this->selectedStageId = ''; // Reset stage when order changes
    }

    public function updatingSelectedStageId()
    {
        $this->resetPage();
    }

    public function resetFilters()
    {
        $this->selectedOrderId = '';
        $this->selectedStageId = '';
        $this->dateFrom = now()->startOfMonth()->format('Y-m-d');
        $this->dateTo = now()->endOfMonth()->format('Y-m-d');
        $this->searchTerm = '';
        $this->resetPage();
    }

    public function render()
    {
        // Get all manufacturing orders
        $orders = ManufacturingOrder::with(['stages', 'invoices'])
            ->orders() // Only non-template orders
            ->orderBy('created_at', 'desc')
            ->get();

        // Get stages for selected order
        $stages = collect();
        if ($this->selectedOrderId) {
            $order = ManufacturingOrder::with('stages')->find($this->selectedOrderId);
            if ($order) {
                $stages = $order->stages;
            }
        }

        // Build query for invoices
        $invoicesQuery = OperHead::with(['manufacturingOrder', 'manufacturingStage', 'branch'])
            ->where('pro_type', 59) // Manufacturing invoices
            ->whereNotNull('manufacturing_order_id'); // Only invoices linked to orders

        // Apply filters
        if ($this->selectedOrderId) {
            $invoicesQuery->where('manufacturing_order_id', $this->selectedOrderId);
        }

        if ($this->selectedStageId) {
            $invoicesQuery->where('manufacturing_stage_id', $this->selectedStageId);
        }

        if ($this->dateFrom) {
            $invoicesQuery->whereDate('pro_date', '>=', $this->dateFrom);
        }

        if ($this->dateTo) {
            $invoicesQuery->whereDate('pro_date', '<=', $this->dateTo);
        }

        if ($this->searchTerm) {
            $invoicesQuery->where(function($query) {
                $query->where('pro_id', 'like', '%' . $this->searchTerm . '%')
                    ->orWhere('info', 'like', '%' . $this->searchTerm . '%');
            });
        }

        $invoices = $invoicesQuery->orderBy('pro_date', 'desc')
            ->orderBy('id', 'desc')
            ->paginate(15);

        // Calculate statistics
        $stats = [
            'total_invoices' => $invoicesQuery->count(),
            'total_value' => $invoicesQuery->sum('pro_value'),
            'invoices_by_stage' => OperHead::where('pro_type', 59)
                ->whereNotNull('manufacturing_stage_id')
                ->selectRaw('manufacturing_stage_id, COUNT(*) as count, SUM(pro_value) as total')
                ->groupBy('manufacturing_stage_id')
                ->get()
                ->keyBy('manufacturing_stage_id'),
        ];

        return view('manufacturing::livewire.stage-invoices-report', [
            'orders' => $orders,
            'stages' => $stages,
            'invoices' => $invoices,
            'stats' => $stats,
        ]);
    }
}
