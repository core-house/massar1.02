<?php

namespace Modules\Manufacturing\Livewire;

use Livewire\Component;
use App\Models\{OperHead, OperationItems, Expense};

class ManufacturingShow extends Component
{
    public $invoice;
    public $products = [];
    public $rawMaterials = [];
    public $expenses = [];
    public $totals = [];

    public function mount($id)
    {
        $this->loadInvoice($id);
    }

    private function loadInvoice($id)
    {
        $this->invoice = OperHead::with([
            'acc1Head',
            'acc2Head',
            'employee',
            'store',
            'branch',
            'operationItems.item',
            'operationItems.unit'
        ])->findOrFail($id);

        // تحميل المنتجات
        $this->products = $this->invoice->operationItems()
            ->whereNotNull('item_id')
            ->whereNull('unit_id')
            // ->where('fat_tax', '!=', 999)
            ->where('detail_store', $this->invoice->acc1)
            ->with('item')
            ->get()
            ->map(function ($item) {
                return [
                    'name' => $item->item->name ?? '-',
                    'quantity' => $item->qty_in ?? 0,
                    'unit_cost' => $item->cost_price ?? 0,
                    'cost_percentage' => $item->additional ?? 0,
                    'total_cost' => ($item->qty_in ?? 0) * ($item->cost_price ?? 0),
                ];
            });

        // تحميل المواد الخام
        $this->rawMaterials = $this->invoice->operationItems()
            ->whereNotNull('item_id')
            ->whereNotNull('unit_id')
            ->with(['item', 'unit'])
            ->get()
            ->map(function ($item) {
                return [
                    'name' => $item->item->name ?? '-',
                    'quantity' => $item->qty_out ?? 0,
                    'unit_name' => $item->unit->name ?? '-',
                    'unit_cost' => $item->cost_price ?? 0,
                    'total_cost' => $item->detail_value ?? 0,
                ];
            });

        // تحميل المصروفات
        $this->expenses = Expense::where('op_id', $this->invoice->id)
            ->with('account')
            ->get()
            ->map(function ($expense) {
                $description = str_replace('مصروف إضافي: ', '', $expense->description);
                $description = preg_replace('/ - فاتورة:.*$/', '', $description);

                return [
                    'description' => trim($description),
                    'account_name' => $expense->account->aname ?? '-',
                    'amount' => $expense->amount ?? 0,
                ];
            });

        // حساب الإجماليات
        $this->calculateTotals();
    }

    private function calculateTotals()
    {
        $this->totals = [
            'products' => collect($this->products)->sum('total_cost'),
            'raw_materials' => collect($this->rawMaterials)->sum('total_cost'),
            'expenses' => collect($this->expenses)->sum('amount'),
            'manufacturing_cost' => 0,
        ];

        $this->totals['manufacturing_cost'] =
            $this->totals['raw_materials'] + $this->totals['expenses'];
    }

    public function printInvoice()
    {
        $this->dispatch('print-invoice');
    }

    public function render()
    {
        return view('manufacturing::livewire.manufacturing-show');
    }
}
