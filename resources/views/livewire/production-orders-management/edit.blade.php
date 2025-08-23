<?php

use Livewire\Volt\Component;
use App\Models\ProductionOrder;
use App\Models\AccHead;
use App\Models\Item;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Validate;

new class extends Component {
    public ProductionOrder $productionOrder;
    
    #[Validate('required|integer|min:1')]
    public $order_number;
    
    #[Validate('required|date')]
    public $order_date;
    
    #[Validate('required|exists:acc_head,id')]
    public $customer_id;
    
    #[Validate('nullable|string|max:1000')]
    public $notes = '';
    
    #[Validate('required|in:pending,completed,cancelled')]
    public $status = 'pending';
    
    public $items = [];
    public $customers;
    public $availableItems;
    
    public function mount(ProductionOrder $productionOrder)
    {
        $this->productionOrder = $productionOrder->load(['items', 'customer']);
        
        $this->order_number = $this->productionOrder->order_number;
        $this->order_date = $this->productionOrder->order_date->format('Y-m-d');
        $this->customer_id = $this->productionOrder->customer_id;
        $this->notes = $this->productionOrder->notes ?? '';
        $this->status = $this->productionOrder->status;
        
        $this->customers = AccHead::where('code', 'like', '1103%')
            ->where('is_basic', 0)
            ->orderBy('aname')
            ->get(['id', 'aname', 'code']);
            
        $this->availableItems = Item::orderBy('name')->get(['id', 'name', 'code']);
        
        // Load existing items
        foreach ($this->productionOrder->items as $item) {
            $this->items[] = [
                'item_id' => $item->id,
                'quantity' => $item->pivot->quantity,
                'note' => $item->pivot->note ?? ''
            ];
        }
        
        if (empty($this->items)) {
            $this->addItem();
        }
    }
    
    public function addItem()
    {
        $this->items[] = [
            'item_id' => '',
            'quantity' => 1,
            'note' => ''
        ];
    }
    
    public function removeItem($index)
    {
        unset($this->items[$index]);
        $this->items = array_values($this->items);
    }
    
    public function save()
    {
        $this->validate();
        
        // Validate items
        $this->validate([
            'items' => 'required|array|min:1',
            'items.*.item_id' => 'required|exists:items,id',
            'items.*.quantity' => 'required|numeric|min:0.01',
        ], [
            'items.required' => 'يجب إضافة صنف واحد على الأقل',
            'items.*.item_id.required' => 'يجب اختيار الصنف',
            'items.*.item_id.exists' => 'الصنف المختار غير موجود',
            'items.*.quantity.required' => 'يجب إدخال الكمية',
            'items.*.quantity.numeric' => 'الكمية يجب أن تكون رقم',
            'items.*.quantity.min' => 'الكمية يجب أن تكون أكبر من صفر',
        ]);
        
        // Check if order can be edited (not completed)
        if ($this->productionOrder->status === 'completed' && $this->status !== 'completed') {
            session()->flash('error', 'لا يمكن تعديل أمر إنتاج مكتمل');
            return;
        }
        
        try {
            DB::beginTransaction();
            
            $this->productionOrder->update([
                'order_number' => $this->order_number,
                'order_date' => $this->order_date,
                'customer_id' => $this->customer_id,
                'status' => $this->status,
                'notes' => $this->notes,
                'updated_by' => Auth::id(),
            ]);
            
            // Sync items (this will remove old items and add new ones)
            $itemsToSync = [];
            foreach ($this->items as $item) {
                $itemsToSync[$item['item_id']] = [
                    'quantity' => $item['quantity'],
                    'note' => $item['note'] ?? null,
                ];
            }
            $this->productionOrder->items()->sync($itemsToSync);
            
            DB::commit();
            
            session()->flash('success', 'تم تحديث أمر الإنتاج بنجاح');
            return redirect()->route('production-orders.index');
            
        } catch (\Exception $e) {
            DB::rollBack();
            session()->flash('error', 'حدث خطأ أثناء تحديث أمر الإنتاج: ' . $e->getMessage());
        }
    }
    
    public function cancel()
    {
        return redirect()->route('production-orders.index');
    }
}; ?>

<div class="container-fluid">
    <!-- Header -->
    <div class="row mb-4">
        <div class="col-md-6">
            <h2 class="mb-1">تعديل أمر الإنتاج</h2>
            <p class="text-muted">تعديل أمر الإنتاج رقم {{ $this->productionOrder->order_number }}</p>
        </div>
        <div class="col-md-6 text-end mt-4">
            <a href="{{ route('production-orders.index') }}" class="btn btn-secondary font-family-cairo fw-bold">
                <i class="fas fa-arrow-left"></i>
                العودة
            </a>
        </div>
    </div>

    <form wire:submit="save">
        <!-- Basic Information -->
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="mb-0">المعلومات الأساسية</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-3">
                        <div class="form-group mb-3">
                            <label for="order_number">رقم الأمر</label>
                            <input type="number" id="order_number" class="form-control @error('order_number') is-invalid @enderror" wire:model="order_number">
                            @error('order_number')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    
                    <div class="col-md-3">
                        <div class="form-group mb-3">
                            <label for="order_date">تاريخ الأمر</label>
                            <input type="date" id="order_date" class="form-control @error('order_date') is-invalid @enderror" wire:model="order_date">
                            @error('order_date')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    
                    <div class="col-md-3">
                        <div class="form-group mb-3">
                            <label for="customer_id">العميل</label>
                            <select id="customer_id" class="form-control @error('customer_id') is-invalid @enderror" wire:model="customer_id">
                                <option value="">اختر العميل</option>
                                @foreach($this->customers as $customer)
                                    <option value="{{ $customer->id }}">{{ $customer->aname }} ({{ $customer->code }})</option>
                                @endforeach
                            </select>
                            @error('customer_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    
                    <div class="col-md-6">
                        <div class="form-group mb-3">
                            <label for="status">الحالة</label>
                            <select id="status" class="form-control @error('status') is-invalid @enderror" wire:model="status">
                                <option value="pending">قيد الانتظار</option>
                                <option value="completed">مكتمل</option>
                                <option value="cancelled">ملغي</option>
                            </select>
                            @error('status')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    
                    <div class="col-md-12">
                        <div class="form-group mb-3">
                            <label for="notes">ملاحظات</label>
                            <textarea id="notes" class="form-control @error('notes') is-invalid @enderror" wire:model="notes" rows="3" placeholder="أي ملاحظات إضافية..."></textarea>
                            @error('notes')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Items -->
        <div class="card mb-4">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">الأصناف المطلوبة</h5>
                <button type="button" wire:click="addItem" class="btn btn-sm btn-outline-primary font-family-cairo fw-bold font-14">
                    <i class="fas fa-plus"></i>
                    إضافة صنف
                </button>
            </div>
            <div class="card-body">
                @if(count($this->items) > 0)
                    <div class="space-y-3">
                        @foreach($this->items as $index => $item)
                            <div class="row border rounded p-3 mb-3">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label>الصنف</label>
                                        <select class="form-control @error("items.{$index}.item_id") is-invalid @enderror" wire:model="items.{{ $index }}.item_id">
                                            <option value="">اختر الصنف</option>
                                            @foreach($this->availableItems as $availableItem)
                                                <option value="{{ $availableItem->id }}">{{ $availableItem->name }} ({{ $availableItem->code }})</option>
                                            @endforeach
                                        </select>
                                        @error("items.{$index}.item_id")
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label>الكمية</label>
                                        <input type="number" class="form-control @error("items.{$index}.quantity") is-invalid @enderror" wire:model="items.{{ $index }}.quantity" step="0.01" min="0.01">
                                        @error("items.{$index}.quantity")
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label>ملاحظة</label>
                                        <input type="text" class="form-control" wire:model="items.{{ $index }}.note" placeholder="ملاحظة اختيارية...">
                                    </div>
                                </div>
                                
                                <div class="col-md-1 d-flex align-items-end mb-4">
                                    @if(count($this->items) > 1)
                                        <button type="button" wire:click="removeItem({{ $index }})" class="btn btn-sm btn-outline-danger">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="text-center py-5">
                        <i class="fas fa-box fa-3x text-muted mb-3"></i>
                        <h5>لا توجد أصناف</h5>
                        <p class="text-muted">اضغط على "إضافة صنف" لبدء إضافة الأصناف المطلوبة</p>
                    </div>
                @endif
            </div>
        </div>

        <!-- Actions -->
        <div class="row">
            <div class="col-12 d-flex justify-content-center">
                    <button type="button" wire:click="cancel" class="btn btn-secondary me-2 font-family-cairo fw-bold">
                    إلغاء
                </button>
                <button type="submit" class="btn btn-primary font-family-cairo fw-bold">
                    <i class="fas fa-check"></i>
                    حفظ التعديلات
                </button>
            </div>
        </div>
    </form>
</div>  