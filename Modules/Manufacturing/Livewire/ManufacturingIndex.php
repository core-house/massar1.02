<?php

namespace Modules\Manufacturing\Livewire;

use App\Models\Expense;
use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Support\Facades\DB;
use App\Models\{OperHead, AccHead};
use Modules\Branches\Models\Branch;

class ManufacturingIndex extends Component
{
    use WithPagination;

    protected $paginationTheme = 'bootstrap';

    public $search = '';
    public $dateFrom = '';
    public $dateTo = '';
    public $branchFilter = '';
    public $statusFilter = '';
    public $perPage = 15;
    public $sortField = 'pro_date';
    public $sortDirection = 'desc';

    protected $queryString = [
        'search' => ['except' => ''],
        'dateFrom' => ['except' => ''],
        'dateTo' => ['except' => ''],
        'branchFilter' => ['except' => ''],
    ];

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function sortBy($field)
    {
        if ($this->sortField === $field) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortField = $field;
            $this->sortDirection = 'asc';
        }
    }

    public function deleteInvoice($invoiceId)
    {
        try {
            DB::beginTransaction();

            $invoice = OperHead::findOrFail($invoiceId);
            $invoice->operationItems()->delete();
            Expense::where('op_id', $invoiceId)->delete();

            if ($invoice->journalHead) {
                $invoice->journalHead->dets()->delete();
                $invoice->journalHead->delete();
            }
            $invoice->delete();
            DB::commit();
            $this->dispatch('success-swal', [
                'title' => 'تم الحذف!',
                'text' => 'تم حذف فاتورة التصنيع بنجاح',
                'icon' => 'success'
            ]);
        } catch (\Exception) {
            DB::rollBack();
            $this->dispatch('error-swal', [
                'title' => 'خطأ!',
                'text' => 'حدث خطأ أثناء الحذف: ',
                'icon' => 'error'
            ]);
        }
    }

    public function confirmDelete($invoiceId)
    {
        $this->dispatch('confirm-delete', [
            'title' => 'هل أنت متأكد؟',
            'text' => 'سيتم حذف فاتورة التصنيع وجميع البيانات المرتبطة بها',
            'icon' => 'warning',
            'confirmButtonText' => 'نعم، احذف',
            'cancelButtonText' => 'إلغاء',
            'invoiceId' => $invoiceId
        ]);
        $this->deleteInvoice($invoiceId);
    }

    public function render()
    {
        $query = OperHead::where('pro_type', 59)
            ->with(['acc1Head', 'acc2Head', 'employee', 'branch']);

        // البحث
        if ($this->search) {
            $query->where(function ($q) {
                $q->where('pro_id', 'like', '%' . $this->search . '%')
                    ->orWhere('info', 'like', '%' . $this->search . '%')
                    ->orWhereHas('acc1Head', function ($subQuery) {
                        $subQuery->where('aname', 'like', '%' . $this->search . '%');
                    })
                    ->orWhereHas('acc2Head', function ($subQuery) {
                        $subQuery->where('aname', 'like', '%' . $this->search . '%');
                    });
            });
        }

        // فلتر التاريخ
        if ($this->dateFrom) {
            $query->whereDate('pro_date', '>=', $this->dateFrom);
        }
        if ($this->dateTo) {
            $query->whereDate('pro_date', '<=', $this->dateTo);
        }

        // فلتر الفرع
        if ($this->branchFilter) {
            $query->where('branch_id', $this->branchFilter);
        }

        // الترتيب
        $query->orderBy($this->sortField, $this->sortDirection);

        $invoices = $query->paginate($this->perPage);

        // إحصائيات سريعة
        $statistics = [
            'total' => OperHead::where('pro_type', 59)->count(),
            'thisMonth' => OperHead::where('pro_type', 59)
                ->whereYear('pro_date', date('Y'))
                ->whereMonth('pro_date', date('m'))
                ->count(),
            'totalValue' => OperHead::where('pro_type', 59)->sum('pro_value'),
            'avgValue' => OperHead::where('pro_type', 59)->avg('pro_value'),
        ];

        $branches = Branch::all();

        return view('manufacturing::livewire.manufacturing-index', [
            'invoices' => $invoices,
            'statistics' => $statistics,
            'branches' => $branches
        ]);
    }
}
