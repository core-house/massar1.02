<?php

use Livewire\Volt\Component;
use App\Models\ProductionOrder;
use App\Models\AccHead;
use Illuminate\Support\Facades\Auth;
use Livewire\WithPagination;
use Livewire\Attributes\Url;

new class extends Component {
    use WithPagination;
    protected $paginationTheme = 'bootstrap';
    
    #[Url]
    public $search = '';
    
    #[Url]
    public $status = '';
    
    #[Url]
    public $customer = '';
    
    #[Url]
    public $start_date = '';
    
    #[Url]
    public $end_date = '';
    
    public $customers;
    
    public function mount()
    {
        $this->customers = AccHead::where('code', 'like', '1103%')
            ->where('is_basic', 0)
            ->orderBy('aname')
            ->get(['id', 'aname', 'code']);
    }
    
    public function updatedSearch()
    {
        $this->resetPage();
    }
    
    public function updatedStatus()
    {
        $this->resetPage();
    }
    
    public function updatedCustomer()
    {
        $this->resetPage();
    }
    
    public function updatedStartDate()
    {
        $this->resetPage();
    }
    
    public function updatedEndDate()
    {
        $this->resetPage();
    }
    
    public function clearFilters()
    {
        $this->reset(['search', 'status', 'customer', 'start_date', 'end_date']);
        $this->resetPage();
    }
    
    public function delete($id)
    {
        $order = ProductionOrder::findOrFail($id);
        
        // Check if order can be deleted (not completed)
        if ($order->status === 'completed') {
            session()->flash('error', 'لا يمكن حذف أمر إنتاج مكتمل');
            return;
        }
        
        try {
            // Delete related items first
            $order->items()->detach();
            
            // Then delete the production order
            $order->delete();
            
            session()->flash('success', 'تم حذف أمر الإنتاج بنجاح');
        } catch (\Exception $e) {
            session()->flash('error', 'حدث خطأ أثناء حذف أمر الإنتاج: ' . $e->getMessage());
        }
    }
    
    public function confirmDelete($id)
    {
        $this->dispatch('confirm-delete', id: $id);
    }
    
    public function getProductionOrdersProperty()
    {
        return ProductionOrder::with(['customer', 'createdBy'])
            ->when($this->search, function ($query) {
                $query->where('order_number', 'like', '%' . $this->search . '%');
            })
            ->when($this->status, function ($query) {
                $query->where('status', $this->status);
            })
            ->when($this->customer, function ($query) {
                $query->where('customer_id', $this->customer);
            })
            ->when($this->start_date, function ($query) {
                $query->whereDate('order_date', '>=', $this->start_date);
            })
            ->when($this->end_date, function ($query) {
                $query->whereDate('order_date', '<=', $this->end_date);
            })
            ->orderBy('created_at', 'desc')
            ->paginate(10);
    }
}; ?>

<div class="container-fluid">
    <!-- Header -->
    <div class="row mb-4 d-flex justify-content-between ">
        <div class="col-md-6">
            <h2 class="mb-1">أوامر الإنتاج</h2>
            <p class="text-muted">إدارة أوامر الإنتاج والتصنيع</p>
        </div>
        <div class="col-md-6 text-end mt-4">
            <a href="{{ route('production-orders.create') }}" class="btn btn-primary font-family-cairo fw-bold">
                <i class="fas fa-plus"></i>
                أمر إنتاج جديد
            </a>
        </div>
    </div>

    <!-- Filters -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="form-group">
                <label for="search">البحث</label>
                <input type="text" id="search" class="form-control" wire:model.live.debounce.300ms="search" placeholder="البحث برقم الأمر...">
            </div>
        </div>
        
        <div class="col-md-2">
            <div class="form-group">
                <label for="status">الحالة</label>
                <select id="status" class="form-control" wire:model.live="status">
                    <option value="">جميع الحالات</option>
                    <option value="pending">قيد الانتظار</option>
                    <option value="completed">مكتمل</option>
                    <option value="cancelled">ملغي</option>
                </select>
            </div>
        </div>
        
        <div class="col-md-2">
            <div class="form-group">
                <label for="customer">العميل</label>
                <select id="customer" class="form-control" wire:model.live="customer">
                    <option value="">جميع العملاء</option>
                    @foreach($this->customers as $customer)
                        <option value="{{ $customer->id }}">{{ $customer->aname }}</option>
                    @endforeach
                </select>
            </div>
        </div>
        
        <div class="col-md-2">
            <div class="form-group">
                <label for="start_date">من تاريخ</label>
                <input type="date" id="start_date" class="form-control" wire:model.live="start_date">
            </div>
        </div>
        
        <div class="col-md-2">
            <div class="form-group">
                <label for="end_date">إلى تاريخ</label>
                <input type="date" id="end_date" class="form-control" wire:model.live="end_date">
            </div>
        </div>
        
        <div class="col-md-1 d-flex align-items-end mb-4">
            <button wire:click="clearFilters" class="btn btn-outline-secondary font-family-cairo fw-bold" title="مسح الفلاتر">
                مسح الفلاتر
            </button>
        </div>
    </div>

    <!-- Table -->
    <div class="card">
        <div class="card-body">
            @if($this->productionOrders->count() > 0)
                <div class="table-responsive">
                    <table class="table table-striped table-hover">
                        <thead class="table-dark">
                            <tr>
                                <th class= "text-white">رقم الأمر</th>
                                <th class= "text-white">التاريخ</th>
                                <th class= "text-white">العميل</th>
                                <th class= "text-white">المبلغ الإجمالي</th>
                                <th class= "text-white">الحالة</th>
                                <th class= "text-white">أنشئ بواسطة</th>
                                <th class= "text-white">الإجراءات</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($this->productionOrders as $order)
                                <tr>
                                    <td class="font-family-cairo fw-bold">{{ $order->order_number }}</td>
                                    <td class="font-family-cairo fw-bold">{{ $order->order_date->format('Y-m-d') }}</td>
                                    <td class="font-family-cairo fw-bold">{{ $order->customer->aname ?? '-' }}</td>
                                    <td class="font-family-cairo fw-bold">{{ number_format($order->total_amount, 2) }} ريال</td>
                                    <td class="font-family-cairo fw-bold">
                                        @php
                                            $statusColors = [
                                                'pending' => 'badge bg-warning',
                                                'completed' => 'badge bg-success',
                                                'cancelled' => 'badge bg-danger',
                                            ];
                                            $statusLabels = [
                                                'pending' => 'قيد الانتظار',
                                                'completed' => 'مكتمل',
                                                'cancelled' => 'ملغي',
                                            ];
                                        @endphp
                                        <span class="{{ $statusColors[$order->status] }}">
                                            {{ $statusLabels[$order->status] }}
                                        </span>
                                    </td>
                                    <td class="font-family-cairo fw-bold">{{ $order->createdBy->name ?? '-' }}</td>
                                    <td class="font-family-cairo fw-bold">
                                        <div class="btn-group" role="group">
                                            <a href="{{ route('production-orders.show', $order->id) }}" class="btn btn-sm btn-info font-family-cairo fw-bold">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <a href="{{ route('production-orders.edit', $order->id) }}" class="btn btn-sm btn-warning font-family-cairo fw-bold">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            @if($order->status !== 'completed')
                                                <button wire:click="confirmDelete({{ $order->id }})" class="btn btn-sm btn-danger">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                
                <!-- Pagination -->
                <div class="mt-3">
                    {{ $this->productionOrders->links() }}
                </div>
            @else
                <div class="text-center py-5">
                    <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
                    <h5>لا توجد أوامر إنتاج</h5>
                    <p class="text-muted">ابدأ بإنشاء أمر إنتاج جديد</p>
                </div>
            @endif
        </div>
    </div>
</div>

<script>
document.addEventListener('livewire:init', () => {
    Livewire.on('confirm-delete', (event) => {
        if (confirm('هل أنت متأكد من حذف هذا الأمر؟')) {
            @this.delete(event.id);
        }
    });
});
</script>  