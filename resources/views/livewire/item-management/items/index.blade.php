<?php

use Livewire\Volt\Component;
use App\Models\Item;
use App\Models\Price;
use App\Models\Note;
use App\Models\NoteDetails;
use App\Helpers\ItemViewModel;
use App\Models\AccHead;
use App\Models\OperationItems;
use Livewire\WithPagination;

new class extends Component {
    use WithPagination;
    protected $paginationTheme = 'bootstrap';

    public $selectedUnit = [];
    public $displayItemData = [];
    public $priceTypes;
    public $noteTypes;
    public $search = '';
    public $warehouses;
    public $selectedWarehouse = null;
    public $selectedPriceType = '';
    public $groups;
    public $selectedGroup = null;
    public $categories;
    public $selectedCategory = null;
    
    // Column visibility settings
    public $visibleColumns = [
        'code' => true,
        'name' => true,
        'units' => true,
        'quantity' => true,
        'average_cost' => true,
        'quantity_average_cost' => true,
        'last_cost' => true,
        'quantity_cost' => true,
        'barcode' => true,
        'actions' => true,
    ];
    
    // Individual note visibility settings
    public $visibleNotes = [];
    
    // Individual price visibility settings
    public $visiblePrices = [];

    public function mount()
    {
        $this->priceTypes = Price::all()->pluck('name', 'id');
        $this->noteTypes = Note::all()->pluck('name', 'id');
        $this->warehouses = AccHead::where('code', 'like', '1104%')->where('is_basic', 0)->orderBy('id')->get();
        $this->groups = NoteDetails::where('note_id', 1)->pluck('name', 'id');
        $this->categories = NoteDetails::where('note_id', 2)->pluck('name', 'id');
        
        // Initialize note visibility - all notes visible by default
        foreach ($this->noteTypes as $noteId => $noteName) {
            $this->visibleNotes[$noteId] = true;
        }
        
        // Initialize price visibility - all prices visible by default
        foreach ($this->priceTypes as $priceId => $priceName) {
            $this->visiblePrices[$priceId] = true;
        }
    }

    public function getItemsProperty()
    {
        return Item::with([
            'units' => function ($query) {
                $query->orderBy('pivot_u_val');
            },
            'prices',
            'barcodes',
            'notes',
        ])
            ->when($this->search, function ($query) {
                $query
                    ->where('name', 'like', '%' . $this->search . '%')
                    ->orWhere('code', 'like', '%' . $this->search . '%')
                    ->orWhereHas('barcodes', function ($q) {
                        $q->where('barcode', 'like', '%' . $this->search . '%');
                    });
            })
            ->when($this->selectedGroup, function ($query) {
                $query->whereHas('notes', function ($q) {
                    $q->where('note_id', 1) // Groups have note_id = 1
                        ->where('note_detail_name', function ($subQuery) {
                            $subQuery->select('name')->from('note_details')->where('id', $this->selectedGroup);
                        });
                });
            })
            ->when($this->selectedCategory, function ($query) {
                $query->whereHas('notes', function ($q) {
                    $q->where('note_id', 2) // Categories have note_id = 2
                        ->where('note_detail_name', function ($subQuery) {
                            $subQuery->select('name')->from('note_details')->where('id', $this->selectedCategory);
                        });
                });
            })
            ->paginate(100);
    }

    public function getTotalQuantityProperty()
    {
        if (!$this->selectedPriceType) {
            return 0;
        }

        // Get all filtered items without pagination
        $allFilteredItems = Item::with([
            'units' => function ($query) {
                $query->orderBy('pivot_u_val');
            },
            'prices',
            'barcodes',
            'notes',
        ])
            ->when($this->search, function ($query) {
                $query
                    ->where('name', 'like', '%' . $this->search . '%')
                    ->orWhere('code', 'like', '%' . $this->search . '%')
                    ->orWhereHas('barcodes', function ($q) {
                        $q->where('barcode', 'like', '%' . $this->search . '%');
                    });
            })
            ->when($this->selectedGroup, function ($query) {
                $query->whereHas('notes', function ($q) {
                    $q->where('note_id', 1) // Groups have note_id = 1
                        ->where('note_detail_name', function ($subQuery) {
                            $subQuery->select('name')->from('note_details')->where('id', $this->selectedGroup);
                        });
                });
            })
            ->when($this->selectedCategory, function ($query) {
                $query->whereHas('notes', function ($q) {
                    $q->where('note_id', 2) // Categories have note_id = 2
                        ->where('note_detail_name', function ($subQuery) {
                            $subQuery->select('name')->from('note_details')->where('id', $this->selectedCategory);
                        });
                });
            })
            ->get();

        $total = 0;
        foreach ($allFilteredItems as $item) {
            // Get default unit for this item
            $defaultUnit = $item->units->sortBy('pivot.u_val')->first();
            $selectedUnitId = $defaultUnit ? $defaultUnit->id : null;

            // Create ItemViewModel for this item
            $viewModel = new ItemViewModel($this->selectedWarehouse, $item, $selectedUnitId);
            $formattedQuantity = $viewModel->getFormattedQuantity();

            if (isset($formattedQuantity['quantity']['integer'])) {
                $total += $formattedQuantity['quantity']['integer'];
            }
        }
        return $total;
    }

    public function getTotalAmountProperty()
    {
        if (!$this->selectedPriceType) {
            return 0;
        }

        // Get all filtered items without pagination
        $allFilteredItems = Item::with([
            'units' => function ($query) {
                $query->orderBy('pivot_u_val');
            },
            'prices',
            'barcodes',
            'notes',
        ])
            ->when($this->search, function ($query) {
                $query
                    ->where('name', 'like', '%' . $this->search . '%')
                    ->orWhere('code', 'like', '%' . $this->search . '%')
                    ->orWhereHas('barcodes', function ($q) {
                        $q->where('barcode', 'like', '%' . $this->search . '%');
                    });
            })
            ->when($this->selectedGroup, function ($query) {
                $query->whereHas('notes', function ($q) {
                    $q->where('note_id', 1) // Groups have note_id = 1
                        ->where('note_detail_name', function ($subQuery) {
                            $subQuery->select('name')->from('note_details')->where('id', $this->selectedGroup);
                        });
                });
            })
            ->when($this->selectedCategory, function ($query) {
                $query->whereHas('notes', function ($q) {
                    $q->where('note_id', 2) // Categories have note_id = 2
                        ->where('note_detail_name', function ($subQuery) {
                            $subQuery->select('name')->from('note_details')->where('id', $this->selectedCategory);
                        });
                });
            })
            ->get();

        $total = 0;
        foreach ($allFilteredItems as $item) {
            // Get default unit for this item
            $defaultUnit = $item->units->sortBy('pivot.u_val')->first();
            $selectedUnitId = $defaultUnit ? $defaultUnit->id : null;

            // Create ItemViewModel for this item
            $viewModel = new ItemViewModel($this->selectedWarehouse, $item, $selectedUnitId);
            $formattedQuantity = $viewModel->getFormattedQuantity();
            $quantity = $formattedQuantity['quantity']['integer'] ?? 0;

            // Get unit price based on selected price type
            if ($this->selectedPriceType === 'cost') {
                $unitPrice = $viewModel->getUnitCostPrice() ?? 0;
            } elseif ($this->selectedPriceType === 'average_cost') {
                $unitPrice = $viewModel->getUnitAverageCost() ?? 0;
            } else {
                $unitSalePrices = $viewModel->getUnitSalePrices();
                $unitPrice = $unitSalePrices[$this->selectedPriceType]['price'] ?? 0;
            }

            $total += $quantity * $unitPrice;
        }
        return $total;
    }

    public function getTotalItemsProperty()
    {
        if (!$this->selectedPriceType) {
            return 0;
        }

        // Get all filtered items without pagination
        $allFilteredItems = Item::with([
            'units' => function ($query) {
                $query->orderBy('pivot_u_val');
            },
            'prices',
            'barcodes',
            'notes',
        ])
            ->when($this->search, function ($query) {
                $query
                    ->where('name', 'like', '%' . $this->search . '%')
                    ->orWhere('code', 'like', '%' . $this->search . '%')
                    ->orWhereHas('barcodes', function ($q) {
                        $q->where('barcode', 'like', '%' . $this->search . '%');
                    });
            })
            ->when($this->selectedGroup, function ($query) {
                $query->whereHas('notes', function ($q) {
                    $q->where('note_id', 1) // Groups have note_id = 1
                        ->where('note_detail_name', function ($subQuery) {
                            $subQuery->select('name')->from('note_details')->where('id', $this->selectedGroup);
                        });
                });
            })
            ->when($this->selectedCategory, function ($query) {
                $query->whereHas('notes', function ($q) {
                    $q->where('note_id', 2) // Categories have note_id = 2
                        ->where('note_detail_name', function ($subQuery) {
                            $subQuery->select('name')->from('note_details')->where('id', $this->selectedCategory);
                        });
                });
            })
            ->get();

        $count = 0;
        foreach ($allFilteredItems as $item) {
            // Get default unit for this item
            $defaultUnit = $item->units->sortBy('pivot.u_val')->first();
            $selectedUnitId = $defaultUnit ? $defaultUnit->id : null;

            // Create ItemViewModel for this item
            $viewModel = new ItemViewModel($this->selectedWarehouse, $item, $selectedUnitId);
            $formattedQuantity = $viewModel->getFormattedQuantity();

            // Count items that have valid quantity data
            if (isset($formattedQuantity['quantity']['integer'])) {
                $count++;
            }
        }
        return $count;
    }

    public function updatedSearch()
    {
        $this->resetPage();
    }

    public function updatedSelectedWarehouse()
    {
        $this->resetPage();
    }

    public function updatedSelectedGroup()
    {
        $this->resetPage();
    }

    public function updatedSelectedCategory()
    {
        $this->resetPage();
    }

    public function clearFilters()
    {
        $this->search = '';
        $this->selectedWarehouse = null;
        $this->selectedGroup = null;
        $this->selectedCategory = null;
        $this->resetPage();
    }

    public function calculateAndStoreDisplayData($itemId)
    {
        $item = $this->items->firstWhere('id', $itemId);
        if (!$item) {
            $this->displayItemData[$itemId] = [];
            return;
        }

        $selectedUnitId = $this->selectedUnit[$itemId];
        $viewModel = new ItemViewModel($this->selectedWarehouse, $item, $selectedUnitId);

        $unitSalePricesData = [];
        if ($selectedUnitId) {
            $rawPrices = $viewModel->getUnitSalePrices();
            foreach ($this->priceTypes as $priceTypeId => $priceTypeName) {
                $unitSalePricesData[$priceTypeId] = $rawPrices[$priceTypeId] ?? ['name' => $priceTypeName, 'price' => null];
            }
        }

        $this->displayItemData[$itemId] = [
            'id' => $item->id,
            'code' => $item->code,
            'name' => $item->name,
            'unitOptions' => $viewModel->getUnitOptions(),
            'formattedQuantity' => $viewModel->getFormattedQuantity(),
            'unitCostPrice' => $viewModel->getUnitCostPrice(),
            'unitAverageCost' => $viewModel->getUnitAverageCost(),
            'quantityCost' => $viewModel->getQuantityCost(),
            'quantityAverageCost' => $viewModel->getQuantityAverageCost(),
            'unitSalePrices' => $unitSalePricesData,
            'unitBarcodes' => $selectedUnitId ? $viewModel->getUnitBarcode() : [],
            'itemNotes' => $item->notes
                ->mapWithKeys(function ($note) {
                    return [$note->id => $note->pivot->note_detail_name];
                })
                ->all(),
        ];
    }

    public function getVisibleColumnsCountProperty()
    {
        $count = 1; // # column
        $count += $this->visibleColumns['code'] ? 1 : 0;
        $count += $this->visibleColumns['name'] ? 1 : 0;
        $count += $this->visibleColumns['units'] ? 1 : 0;
        $count += $this->visibleColumns['quantity'] ? 1 : 0;
        $count += $this->visibleColumns['average_cost'] ? 1 : 0;
        $count += $this->visibleColumns['quantity_average_cost'] ? 1 : 0;
        $count += $this->visibleColumns['last_cost'] ? 1 : 0;
        $count += $this->visibleColumns['quantity_cost'] ? 1 : 0;
        // Count visible prices individually
        foreach ($this->priceTypes as $priceId => $priceName) {
            if (isset($this->visiblePrices[$priceId]) && $this->visiblePrices[$priceId]) {
                $count += 1;
            }
        }
        $count += $this->visibleColumns['barcode'] ? 1 : 0;
        
        // Count visible notes individually
        foreach ($this->noteTypes as $noteId => $noteName) {
            if (isset($this->visibleNotes[$noteId]) && $this->visibleNotes[$noteId]) {
                $count += 1;
            }
        }
        
        $count += $this->visibleColumns['actions'] ? 1 : 0;
        
        return $count;
    }

    public function updated($propertyName, $value)
    {
        if (str_starts_with($propertyName, 'selectedUnit.')) {
            $parts = explode('.', $propertyName);
            $itemId = (int) $parts[1];

            if (isset($this->selectedUnit[$itemId])) {
                $this->calculateAndStoreDisplayData($itemId);
            }
        }
    }

    public function getComputedKey($itemId)
    {
        return 'item-' . $itemId . '-' . ($this->selectedUnit[$itemId] ?? 'no-unit');
    }

    public function edit($itemId)
    {
        redirect()->route('items.edit', $itemId);
    }

    public function delete($itemId)
    {
        // check if the item is used in any operation
        $operationItems = OperationItems::where('item_id', $itemId)->get();
        if ($operationItems->count() > 0) {
            session()->flash('error', 'لا يمكن حذف الصنف لأنه مستخدم في عمليات أخرى');
            return;
        }
        $item = Item::with('units', 'prices', 'notes', 'barcodes')->find($itemId);
        $item->units()->detach();
        $item->prices()->detach();
        $item->notes()->detach();
        $item->barcodes()->delete();
        $item->delete();
        session()->flash('success', 'تم حذف الصنف بنجاح');
    }

    public function viewItemMovement($itemId, $warehouseId = 'all')
    {
        // redirect to item movement page
        return redirect()->route('item-movement', ['itemId' => $itemId, 'warehouseId' => $warehouseId]);
    }

    public function printItems()
    {
        // This method will be used to trigger print functionality
        $this->dispatch('print-items');
    }

    public function toggleColumn($column)
    {
        if (isset($this->visibleColumns[$column])) {
            $this->visibleColumns[$column] = !$this->visibleColumns[$column];
        }
        $this->dispatch('$refresh');
    }

    public function showAllColumns()
    {
        foreach ($this->visibleColumns as $column => $value) {
            $this->visibleColumns[$column] = true;
        }
        foreach ($this->visibleNotes as $noteId => $value) {
            $this->visibleNotes[$noteId] = true;
        }
        foreach ($this->visiblePrices as $priceId => $value) {
            $this->visiblePrices[$priceId] = true;
        }
        // Don't apply changes immediately, let user review and apply manually
    }

    public function hideAllColumns()
    {
        foreach ($this->visibleColumns as $column => $value) {
            $this->visibleColumns[$column] = false;
        }
        foreach ($this->visibleNotes as $noteId => $value) {
            $this->visibleNotes[$noteId] = false;
        }
        foreach ($this->visiblePrices as $priceId => $value) {
            $this->visiblePrices[$priceId] = false;
        }
        // Don't apply changes immediately, let user review and apply manually
    }

    public function toggleNote($noteId)
    {
        if (isset($this->visibleNotes[$noteId])) {
            $this->visibleNotes[$noteId] = !$this->visibleNotes[$noteId];
        }
        $this->dispatch('$refresh');
    }

    public function showAllNotes()
    {
        foreach ($this->visibleNotes as $noteId => $value) {
            $this->visibleNotes[$noteId] = true;
        }
        // Don't apply changes immediately, let user review and apply manually
    }

    public function hideAllNotes()
    {
        foreach ($this->visibleNotes as $noteId => $value) {
            $this->visibleNotes[$noteId] = false;
        }
        // Don't apply changes immediately, let user review and apply manually
    }

    public function togglePrice($priceId)
    {
        if (isset($this->visiblePrices[$priceId])) {
            $this->visiblePrices[$priceId] = !$this->visiblePrices[$priceId];
        }
        $this->dispatch('$refresh');
    }

    public function showAllPrices()
    {
        foreach ($this->visiblePrices as $priceId => $value) {
            $this->visiblePrices[$priceId] = true;
        }
        // Don't apply changes immediately, let user review and apply manually
    }

    public function hideAllPrices()
    {
        foreach ($this->visiblePrices as $priceId => $value) {
            $this->visiblePrices[$priceId] = false;
        }
        // Don't apply changes immediately, let user review and apply manually
    }

    public function applyChanges()
    {
        // Get all checkbox values from the modal using JavaScript
        $this->dispatch('collect-checkbox-values');
    }

    public function updateVisibility($columns, $prices, $notes)
    {
        // Update columns
        $this->visibleColumns = $columns;
        
        // Update prices
        $this->visiblePrices = $prices;
        
        // Update notes
        $this->visibleNotes = $notes;
        
        // Clear display data to force recalculation
        $this->displayItemData = [];
        
        // Reset page to ensure proper display
        $this->resetPage();
        
        // Force refresh the component to apply all changes
        $this->dispatch('$refresh');
        
        // Show success message
        session()->flash('success', 'تم تطبيق التغييرات بنجاح');
        
        // Close modal after applying changes
        $this->dispatch('close-modal');
    }
}; ?>

<div>
    @php
        include_once app_path('Helpers/FormatHelper.php');
    @endphp
    
    <style>
        .print-btn {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border: none;
            padding: 10px 20px;
            font-size: 14px;
            border-radius: 5px;
            cursor: pointer;
            transition: all 0.3s ease;
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }
        
        .print-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2);
        }
    </style>

    <div class="row">
        <div class="col-lg-12">
            @if (session()->has('success'))
                <div class="alert alert-success font-family-cairo fw-bold font-12 mt-2" x-data="{ show: true }"
                    x-show="show" x-init="setTimeout(() => show = false, 3000)">
                    {{ session('success') }}
                </div>
            @endif
            @if (session()->has('error'))
                <div class="alert alert-danger font-family-cairo fw-bold font-12 mt-2" x-data="{ show: true }"
                    x-show="show" x-init="setTimeout(() => show = false, 3000)">
                    {{ session('error') }}
                </div>
            @endif
            <div class="card">
                {{-- card title --}}
                <div class="text-center bg-dark text-white py-3">
                    <h5 class="card-title font-family-cairo fw-bold font-20 text-white">
                        {{ __('قائمه الأصناف مع الأرصده') }}
                    </h5>
                </div>
                

                
                <div class="card-header">
                    <div class="d-flex flex-wrap justify-content-between align-items-center gap-3">
                        {{-- Primary Action Button --}}
                        @can('إضافة الأصناف')
                            <a href="{{ route('items.create') }}"
                                class="btn btn-outline-primary btn-lg font-family-cairo fw-bold mt-4 d-flex justify-content-center align-items-center text-center"
                                style="min-height: 50px;">
                                <i class="fas fa-plus me-2"></i>
                                <span class="w-100 text-center">{{ __('إضافه صنف') }}</span>
                            </a>
                        @endcan

                        {{-- Print Button --}}
                        <div class = "mt-4">
                        <a href="{{ route('items.print', [
                            'search' => $search,
                            'warehouse' => $selectedWarehouse,
                            'group' => $selectedGroup,
                            'category' => $selectedCategory,
                            'priceType' => $selectedPriceType
                        ]) }}" target="_blank" class="print-btn font-family-cairo fw-bold" style="text-decoration: none;">
                                <i class="fas fa-print"></i>
                                طباعة القائمة
                            </a>
                        </div>

                        {{-- Column Visibility Button --}}
                        <div class="mt-4">
                            <button type="button" class="btn btn-outline-info btn-lg font-family-cairo fw-bold" 
                                    data-bs-toggle="modal" data-bs-target="#columnVisibilityModal"
                                    style="min-height: 50px;">
                                <i class="fas fa-columns me-2"></i>
                                خيارات العرض
                            </button>
                        </div>

                        {{-- Search and Filter Group --}}
                        <div class="d-flex flex-grow-1 flex-wrap align-items-center justify-content-end gap-2"
                            style="min-width: 300px;">
                            {{-- Clear Filters Button --}}
                            <div class="d-flex align-items-end mt-4">
                                <button type="button" wire:click="clearFilters" style="min-height: 50px;"
                                    class="btn btn-outline-secondary btn-lg font-family-cairo fw-bold">
                                    <i class="fas fa-times me-1"></i>
                                    مسح الفلاتر
                                </button>
                            </div>
                            {{-- Search Input --}}
                            <div class="flex-grow-1">
                                <label class="form-label font-family-cairo fw-bold font-12 mb-1">البحث:</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="fas fa-search"></i></span>
                                    <input type="text" wire:model.live.debounce.300ms="search"
                                        class="form-control font-family-cairo"
                                        placeholder="بحث بالاسم, الكود, الباركود...">
                                </div>
                            </div>

                            {{-- Warehouse Filter --}}
                            <div class="flex-grow-1">
                                <label class="form-label font-family-cairo fw-bold font-12 mb-1">المخزن:</label>
                                <select wire:model.live="selectedWarehouse"
                                    class="form-select font-family-cairo fw-bold font-14">
                                    <option value="">كل المخازن</option>
                                    @foreach ($warehouses as $warehouse)
                                        <option value="{{ $warehouse->id }}">{{ $warehouse->aname }}</option>
                                    @endforeach
                                </select>
                            </div>

                            {{-- Group Filter --}}
                            <div class="flex-grow-1">
                                <label class="form-label font-family-cairo fw-bold font-12 mb-1">المجموعة:</label>
                                <select wire:model.live="selectedGroup"
                                    class="form-select font-family-cairo fw-bold font-14">
                                    <option value="">كل المجموعات</option>
                                    @foreach ($groups as $id => $name)
                                        <option value="{{ $id }}">{{ $name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            {{-- Category Filter --}}
                            <div class="flex-grow-1">
                                <label class="form-label font-family-cairo fw-bold font-12 mb-1">التصنيف:</label>
                                <select wire:model.live="selectedCategory"
                                    class="form-select font-family-cairo fw-bold font-14">
                                    <option value="">كل التصنيفات</option>
                                    @foreach ($categories as $id => $name)
                                        <option value="{{ $id }}">{{ $name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                </div>

                

                
                <div class="card-body">
                    {{-- Active Filters Display --}}
                    @if ($search || $selectedWarehouse || $selectedGroup || $selectedCategory)
                        <div class="alert alert-info mb-3" x-data="{ show: true }" x-show="show">
                            <div class="d-flex justify-content-between align-items-center">
                                <div class="font-family-cairo fw-bold">
                                    <i class="fas fa-filter me-2"></i>
                                    الفلاتر النشطة:
                                    @if ($search)
                                        <span class="badge bg-primary me-1">البحث: {{ $search }}</span>
                                    @endif
                                    @if ($selectedWarehouse)
                                        @php $warehouse = $warehouses->firstWhere('id', $selectedWarehouse); @endphp
                                        <span class="badge bg-success me-1">المخزن:
                                            {{ $warehouse ? $warehouse->aname : 'غير محدد' }}</span>
                                    @endif
                                    @if ($selectedGroup)
                                        <span class="badge bg-warning me-1">المجموعة:
                                            {{ $groups[$selectedGroup] ?? 'غير محدد' }}</span>
                                    @endif
                                    @if ($selectedCategory)
                                        <span class="badge bg-info me-1">التصنيف:
                                            {{ $categories[$selectedCategory] ?? 'غير محدد' }}</span>
                                    @endif
                                </div>
                                <button type="button" class="btn-close" @click="show = false"></button>
                            </div>
                        </div>
                    @endif

                    <div class="table-responsive" style="overflow-x: auto;" wire:loading.class="opacity-50">
                        <div wire:loading class="position-absolute top-50 start-50 translate-middle">
                            <div class="spinner-border text-primary" role="status">
                                <span class="visually-hidden">جاري التحميل...</span>
                            </div>
                        </div>
                        <table class="table table-striped mb-0 table-hover"
                            style="direction: rtl; font-family: 'Cairo', sans-serif;">
                            <style>
                                /* تخصيص لون الهوفر للصفوف */
                                .table-hover tbody tr:hover {
                                    background-color: #ffc107 !important;
                                    /* لون warning */
                                }
                            </style>
                            <thead class="table-light text-center align-middle">

                                <tr>
                                    <th class="font-family-cairo text-center fw-bold">#</th>
                                    @if($visibleColumns['code'])
                                        <th class="font-family-cairo text-center fw-bold">الكود</th>
                                    @endif
                                    @if($visibleColumns['name'])
                                        <th class="font-family-cairo text-center fw-bold">الاسم</th>
                                    @endif
                                    @if($visibleColumns['units'])
                                        <th class="font-family-cairo text-center fw-bold" style="min-width: 130px;">الوحدات</th>
                                    @endif
                                    @if($visibleColumns['quantity'])
                                        <th class="font-family-cairo text-center fw-bold" style="min-width: 100px;">الكميه</th>
                                    @endif
                                    @if($visibleColumns['average_cost'])
                                        <th class="font-family-cairo text-center fw-bold">متوسط التكلفه</th>
                                    @endif
                                    @if($visibleColumns['quantity_average_cost'])
                                        <th class="font-family-cairo text-center fw-bold">تكلفه المتوسطه للكميه</th>
                                    @endif
                                    @if($visibleColumns['last_cost'])
                                        <th class="font-family-cairo text-center fw-bold">التكلفه الاخيره</th>
                                    @endif
                                    @if($visibleColumns['quantity_cost'])
                                        <th class="font-family-cairo text-center fw-bold">تكلفه الكميه</th>
                                    @endif
                                    @foreach ($this->priceTypes as $priceId => $priceName)
                                        @if(isset($visiblePrices[$priceId]) && $visiblePrices[$priceId])
                                            <th class="font-family-cairo text-center fw-bold">{{ $priceName }}</th>
                                        @endif
                                    @endforeach
                                    @if($visibleColumns['barcode'])
                                        <th class="font-family-cairo text-center fw-bold">الباركود</th>
                                    @endif
                                    @foreach ($this->noteTypes as $noteId => $noteName)
                                        @if(isset($visibleNotes[$noteId]) && $visibleNotes[$noteId])
                                            <th class="font-family-cairo text-center fw-bold">{{ $noteName }}</th>
                                        @endif
                                    @endforeach
                                    @canany(['تعديل الأصناف', 'حذف الأصناف'])
                                        @if($visibleColumns['actions'])
                                            <th class="font-family-cairo fw-bold">العمليات</th>
                                        @endif
                                    @endcanany
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($this->items as $item)
                                    @php
                                        if (!isset($this->selectedUnit[$item->id])) {
                                            $defaultUnit = $item->units->sortBy('pivot.u_val')->first();
                                            $this->selectedUnit[$item->id] = $defaultUnit ? $defaultUnit->id : null;
                                        }
                                        $this->calculateAndStoreDisplayData($item->id);
                                        $itemData = $this->displayItemData[$item->id] ?? [];
                                    @endphp
                                    @if (!empty($itemData))
                                        <tr wire:key="{{ $this->getComputedKey($item->id) }}">
                                            <td class="font-family-cairo text-center fw-bold">{{ $loop->iteration }}</td>
                                            
                                            @if($visibleColumns['code'])
                                                <td class="font-family-cairo text-center fw-bold">{{ $itemData['code'] }}</td>
                                            @endif
                                            
                                            @if($visibleColumns['name'])
                                                <td class="font-family-cairo text-center fw-bold">{{ $itemData['name'] }}
                                                    <a href="{{ route('item-movement', ['itemId' => $item->id]) }}">
                                                        <i class="las la-eye fa-lg text-primary" title="عرض حركات الصنف"></i>
                                                    </a>
                                                </td>
                                            @endif
                                            
                                            @if($visibleColumns['units'])
                                                <td class="font-family-cairo text-center fw-bold">
                                                    @if (!empty($itemData['unitOptions']))
                                                        <select class="form-select font-family-cairo fw-bold font-14"
                                                            wire:model.live="selectedUnit.{{ $item->id }}"
                                                            style="min-width: 105px;">
                                                            @foreach ($itemData['unitOptions'] as $option)
                                                                <option value="{{ $option['value'] }}">
                                                                    {{ $option['label'] }}</option>
                                                            @endforeach
                                                        </select>
                                                    @else
                                                        <span class="font-family-cairo fw-bold font-14">لا يوجد وحدات</span>
                                                    @endif
                                                </td>
                                            @endif
                                            
                                            @if($visibleColumns['quantity'])
                                                <td class="text-center fw-bold">
                                                    @php $fq = $itemData['formattedQuantity']; @endphp
                                                    {{ $fq['quantity']['integer'] }}
                                                    @if (isset($fq['quantity']['remainder']) &&
                                                            $fq['quantity']['remainder'] > 0 &&
                                                            $fq['unitName'] !== $fq['smallerUnitName']
                                                    )
                                                        [{{ $fq['quantity']['remainder'] }} {{ $fq['smallerUnitName'] }}]
                                                    @endif
                                                </td>
                                            @endif
                                            
                                            @if($visibleColumns['average_cost'])
                                                <td class="font-family-cairo text-center fw-bold">
                                                    {{ formatCurrency($itemData['unitAverageCost']) }}
                                                </td>
                                            @endif
                                            
                                            @if($visibleColumns['quantity_average_cost'])
                                                <td class="font-family-cairo text-center fw-bold">
                                                    {{ formatCurrency($itemData['quantityAverageCost']) }}
                                                </td>
                                            @endif
                                            
                                            @if($visibleColumns['last_cost'])
                                                <td class="text-center fw-bold">
                                                    {{ formatCurrency($itemData['unitCostPrice']) }}
                                                </td>
                                            @endif
                                            
                                            @if($visibleColumns['quantity_cost'])
                                                <td class="text-center fw-bold">
                                                    {{ formatCurrency($itemData['quantityCost']) }}
                                                </td>
                                            @endif

                                            {{-- Prices --}}
                                            @foreach ($this->priceTypes as $priceTypeId => $priceTypeName)
                                                @if(isset($visiblePrices[$priceTypeId]) && $visiblePrices[$priceTypeId])
                                                    <td class="font-family-cairo text-center fw-bold">
                                                        {{ isset($itemData['unitSalePrices'][$priceTypeId]['price']) ? formatCurrency($itemData['unitSalePrices'][$priceTypeId]['price']) : 'N/A' }}
                                                    </td>
                                                @endif
                                            @endforeach

                                            @if($visibleColumns['barcode'])
                                                <td class="font-family-cairo fw-bold text-center">
                                                    @if (!empty($itemData['unitBarcodes']))
                                                        <select class="form-select font-family-cairo fw-bold font-14"
                                                            style="min-width: 100px;">
                                                            @foreach ($itemData['unitBarcodes'] as $barcode)
                                                                <option value="{{ formatBarcode($barcode['barcode']) }}">
                                                                    {{ formatBarcode($barcode['barcode']) }}
                                                                </option>
                                                            @endforeach
                                                        </select>
                                                    @else
                                                        <span class="font-family-cairo fw-bold font-14">لا يوجد</span>
                                                    @endif
                                                </td>
                                            @endif

                                            {{-- Notes --}}
                                            @foreach ($this->noteTypes as $noteTypeId => $noteTypeName)
                                                @if(isset($visibleNotes[$noteTypeId]) && $visibleNotes[$noteTypeId])
                                                    <td class="font-family-cairo fw-bold text-center">
                                                        {{ $itemData['itemNotes'][$noteTypeId] ?? '' }}
                                                    </td>
                                                @endif
                                            @endforeach
                                            
                                            @canany(['تعديل الأصناف', 'حذف الأصناف'])
                                                @if($visibleColumns['actions'])
                                                    <td class="d-flex justify-content-center align-items-center gap-2 mt-2">
                                                        @can('تعديل الأصناف')
                                                            <button type="button" title="تعديل الصنف" class="btn btn-success btn-sm"
                                                                wire:click="edit({{ $item->id }})"><i
                                                                    class="las la-edit fa-lg"></i></button>
                                                        @endcan
                                                        @can('حذف الأصناف')
                                                            <button type="button" title="حذف الصنف" class="btn btn-danger btn-sm"
                                                                wire:click="delete({{ $item->id }})"
                                                                onclick="confirm('هل أنت متأكد من حذف هذا الصنف؟') || event.stopImmediatePropagation()">
                                                                <i class="las la-trash fa-lg"></i>
                                                            </button>
                                                        @endcan
                                                    </td>
                                                @endif
                                            @endcanany
                                        </tr>
                                    @endif
                                @empty
                                    @php
                                        $colspan = $this->visibleColumnsCount;
                                    @endphp
                                    <tr>
                                        <td colspan="{{ $colspan }}"
                                            class="text-center font-family-cairo fw-bold">لا يوجد سجلات
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                        {{-- table footer to appear the total items quantity and the total cost or any selected price --}}
                    </div>

                    {{-- Price Selector and Totals Section --}}
                    <div class="row mt-4">
                        <div class="col-md-12">
                            <div class="card border-primary">
                                <div class="card-header bg-primary text-white">
                                    <h6 class="font-family-cairo fw-bold mb-0 text-white">
                                        <i class="fas fa-calculator me-2"></i>
                                        تقيم المخزون
                                    </h6>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-2">
                                            <label class="form-label font-family-cairo fw-bold">اختر نوع السعر:</label>
                                            <select wire:model.live="selectedPriceType"
                                                class="form-select font-family-cairo fw-bold font-14">
                                                <option value="">اختر نوع السعر</option>
                                                <option value="cost">التكلفة</option>
                                                <option value="average_cost">متوسط التكلفة</option>
                                                @foreach ($this->priceTypes as $priceId => $priceName)
                                                    <option value="{{ $priceId }}">{{ $priceName }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="col-md-2">
                                            <label class="form-label font-family-cairo fw-bold">المخزن المحدد:</label>
                                            <div class="form-control-plaintext font-family-cairo fw-bold">
                                                @if ($selectedWarehouse)
                                                    @php
                                                        $warehouse = $warehouses->firstWhere('id', $selectedWarehouse);
                                                    @endphp
                                                    {{ $warehouse ? $warehouse->aname : 'غير محدد' }}
                                                @else
                                                    جميع المخازن
                                                @endif
                                            </div>
                                        </div>
                                        @if ($selectedPriceType)
                                            <div class="col-md-3">
                                                <h6 class="font-family-cairo fw-bold text-primary mb-1"
                                                    style="font-size: 0.95rem;">إجمالي الكمية</h6>
                                                <h4 class="font-family-cairo fw-bold text-success mb-0"
                                                    style="font-size: 1.2rem;">{{ $this->totalQuantity }}</h4>
                                            </div>
                                            <div class="col-md-3">
                                                <h6 class="font-family-cairo fw-bold text-primary">إجمالي القيمة</h6>
                                                <h4 class="font-family-cairo fw-bold text-success">
                                                    {{ formatCurrency($this->totalAmount) }}</h4>
                                            </div>
                                            <div class="col-md-2">
                                                <h6 class="font-family-cairo fw-bold text-primary">عدد الأصناف</h6>
                                                <h4 class="font-family-cairo fw-bold text-success">
                                                    {{ $this->totalItems }}</h4>
                                            </div>
                                        @endif
                                    </div>


                                </div>
                            </div>
                        </div>
                    </div>
                    

                    
                    <div class="mt-3 d-flex justify-content-center">
                        {{ $this->items->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Column Visibility Modal --}}
    <div class="modal fade" id="columnVisibilityModal" tabindex="-1" aria-labelledby="columnVisibilityModalLabel" aria-hidden="true" x-data="{}" @close-modal.window="$el.querySelector('.btn-close').click()">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header bg-info text-white">
                    <h5 class="modal-title font-family-cairo fw-bold" id="columnVisibilityModalLabel">
                        <i class="fas fa-columns me-2"></i>
                        خيارات عرض الأعمدة
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    {{-- Global Controls --}}
                    <div class="row mb-4">
                        <div class="col-12">
                            <div class="d-flex gap-2 justify-content-center">
                                <button type="button" onclick="showAllCheckboxes()" class="btn btn-success btn-sm font-family-cairo fw-bold">
                                    <i class="fas fa-eye me-1"></i>
                                    إظهار الكل
                                </button>
                                <button type="button" onclick="hideAllCheckboxes()" class="btn btn-secondary btn-sm font-family-cairo fw-bold">
                                    <i class="fas fa-eye-slash me-1"></i>
                                    إخفاء الكل
                                </button>
                            </div>
                        </div>
                    </div>
                    
                    {{-- Columns Section --}}
                    <div class="row">
                        <div class="col-md-6">
                            <h6 class="font-family-cairo fw-bold text-primary mb-3">
                                <i class="fas fa-list me-2"></i>
                                الأعمدة الأساسية:
                            </h6>
                            <div class="form-check mb-2">
                                <input class="form-check-input" type="checkbox" id="col_code" {{ $visibleColumns['code'] ? 'checked' : '' }}>
                                <label class="form-check-label font-family-cairo fw-bold" for="col_code">
                                    الكود
                                </label>
                            </div>
                            <div class="form-check mb-2">
                                <input class="form-check-input" type="checkbox" id="col_name" {{ $visibleColumns['name'] ? 'checked' : '' }}>
                                <label class="form-check-label font-family-cairo fw-bold" for="col_name">
                                    الاسم
                                </label>
                            </div>
                            <div class="form-check mb-2">
                                <input class="form-check-input" type="checkbox" id="col_units" {{ $visibleColumns['units'] ? 'checked' : '' }}>
                                <label class="form-check-label font-family-cairo fw-bold" for="col_units">
                                    الوحدات
                                </label>
                            </div>
                            <div class="form-check mb-2">
                                <input class="form-check-input" type="checkbox" id="col_quantity" {{ $visibleColumns['quantity'] ? 'checked' : '' }}>
                                <label class="form-check-label font-family-cairo fw-bold" for="col_quantity">
                                    الكمية
                                </label>
                            </div>
                            <div class="form-check mb-2">
                                <input class="form-check-input" type="checkbox" id="col_barcode" {{ $visibleColumns['barcode'] ? 'checked' : '' }}>
                                <label class="form-check-label font-family-cairo fw-bold" for="col_barcode">
                                    الباركود
                                </label>
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <h6 class="font-family-cairo fw-bold text-primary mb-3">
                                <i class="fas fa-dollar-sign me-2"></i>
                                أعمدة التكلفة والأسعار:
                            </h6>
                            <div class="form-check mb-2">
                                <input class="form-check-input" type="checkbox" id="col_average_cost" {{ $visibleColumns['average_cost'] ? 'checked' : '' }}>
                                <label class="form-check-label font-family-cairo fw-bold" for="col_average_cost">
                                    متوسط التكلفة
                                </label>
                            </div>
                            <div class="form-check mb-2">
                                <input class="form-check-input" type="checkbox" id="col_quantity_average_cost" {{ $visibleColumns['quantity_average_cost'] ? 'checked' : '' }}>
                                <label class="form-check-label font-family-cairo fw-bold" for="col_quantity_average_cost">
                                    تكلفة المتوسطة للكمية
                                </label>
                            </div>
                            <div class="form-check mb-2">
                                <input class="form-check-input" type="checkbox" id="col_last_cost" {{ $visibleColumns['last_cost'] ? 'checked' : '' }}>
                                <label class="form-check-label font-family-cairo fw-bold" for="col_last_cost">
                                    التكلفة الأخيرة
                                </label>
                            </div>
                            <div class="form-check mb-2">
                                <input class="form-check-input" type="checkbox" id="col_quantity_cost" {{ $visibleColumns['quantity_cost'] ? 'checked' : '' }}>
                                <label class="form-check-label font-family-cairo fw-bold" for="col_quantity_cost">
                                    تكلفة الكمية
                                </label>
                            </div>
                        </div>
                    </div>
                    
                    {{-- Prices Section --}}
                    @if(count($this->priceTypes) > 0)
                        <hr class="my-4">
                        <div class="row">
                            <div class="col-12 mb-3">
                                <h6 class="font-family-cairo fw-bold text-info mb-3">
                                    <i class="fas fa-tags me-2"></i>
                                    أسعار البيع:
                                </h6>
                                <div class="d-flex gap-2 mb-3">
                                    <button type="button" onclick="showAllPrices()" class="btn btn-info btn-sm font-family-cairo fw-bold">
                                        <i class="fas fa-eye me-1"></i>
                                        إظهار جميع الأسعار
                                    </button>
                                    <button type="button" onclick="hideAllPrices()" class="btn btn-secondary btn-sm font-family-cairo fw-bold">
                                        <i class="fas fa-eye-slash me-1"></i>
                                        إخفاء جميع الأسعار
                                    </button>
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                @foreach ($this->priceTypes as $priceId => $priceName)
                                    <div class="form-check mb-2">
                                        <input class="form-check-input" type="checkbox" id="price_{{ $priceId }}" {{ ($visiblePrices[$priceId] ?? false) ? 'checked' : '' }}>
                                        <label class="form-check-label font-family-cairo fw-bold" for="price_{{ $priceId }}">
                                            {{ $priceName }}
                                        </label>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif
                    
                    {{-- Actions Section --}}
                    @canany(['تعديل الأصناف', 'حذف الأصناف'])
                        <hr class="my-4">
                        <div class="row">
                            <div class="col-12">
                                <h6 class="font-family-cairo fw-bold text-warning mb-3">
                                    <i class="fas fa-cogs me-2"></i>
                                    العمليات:
                                </h6>
                                <div class="form-check mb-2">
                                    <input class="form-check-input" type="checkbox" id="col_actions" {{ $visibleColumns['actions'] ? 'checked' : '' }}>
                                    <label class="form-check-label font-family-cairo fw-bold" for="col_actions">
                                        العمليات (تعديل/حذف)
                                    </label>
                                </div>
                            </div>
                        </div>
                    @endcanany
                    
                    {{-- Notes Section --}}
                    @if(count($this->noteTypes) > 0)
                        <hr class="my-4">
                        <div class="row">
                            <div class="col-12 mb-3">
                                <h6 class="font-family-cairo fw-bold text-success mb-3">
                                    <i class="fas fa-sticky-note me-2"></i>
                                    الملاحظات:
                                </h6>
                                <div class="d-flex gap-2 mb-3">
                                    <button type="button" onclick="showAllNotes()" class="btn btn-success btn-sm font-family-cairo fw-bold">
                                        <i class="fas fa-eye me-1"></i>
                                        إظهار جميع الملاحظات
                                    </button>
                                    <button type="button" onclick="hideAllNotes()" class="btn btn-secondary btn-sm font-family-cairo fw-bold">
                                        <i class="fas fa-eye-slash me-1"></i>
                                        إخفاء جميع الملاحظات
                                    </button>
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                @foreach ($this->noteTypes as $noteId => $noteName)
                                    <div class="form-check mb-2">
                                        <input class="form-check-input" type="checkbox" id="note_{{ $noteId }}" {{ ($visibleNotes[$noteId] ?? false) ? 'checked' : '' }}>
                                        <label class="form-check-label font-family-cairo fw-bold" for="note_{{ $noteId }}">
                                            {{ $noteName }}
                                        </label>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-primary font-family-cairo fw-bold" wire:click="applyChanges">
                        <i class="fas fa-check me-2"></i>
                        تطبيق التغييرات
                    </button>
                    <button type="button" class="btn btn-secondary font-family-cairo fw-bold" data-bs-dismiss="modal">
                        إغلاق
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// Listen for Livewire event to collect checkbox values
document.addEventListener('livewire:init', () => {
    Livewire.on('collect-checkbox-values', () => {
        collectAndSendValues();
    });
});

function collectAndSendValues() {
    // Collect column values
    const columns = {
        'code': document.getElementById('col_code').checked,
        'name': document.getElementById('col_name').checked,
        'units': document.getElementById('col_units').checked,
        'quantity': document.getElementById('col_quantity').checked,
        'barcode': document.getElementById('col_barcode').checked,
        'average_cost': document.getElementById('col_average_cost').checked,
        'quantity_average_cost': document.getElementById('col_quantity_average_cost').checked,
        'last_cost': document.getElementById('col_last_cost').checked,
        'quantity_cost': document.getElementById('col_quantity_cost').checked,
        'actions': document.getElementById('col_actions').checked
    };
    
    // Collect price values
    const prices = {};
    document.querySelectorAll('input[id^="price_"]').forEach(checkbox => {
        const priceId = checkbox.id.replace('price_', '');
        prices[priceId] = checkbox.checked;
    });
    
    // Collect note values
    const notes = {};
    document.querySelectorAll('input[id^="note_"]').forEach(checkbox => {
        const noteId = checkbox.id.replace('note_', '');
        notes[noteId] = checkbox.checked;
    });
    
    // Send to Livewire
    @this.call('updateVisibility', columns, prices, notes);
}

function showAllCheckboxes() {
    // Show all column checkboxes
    document.getElementById('col_code').checked = true;
    document.getElementById('col_name').checked = true;
    document.getElementById('col_units').checked = true;
    document.getElementById('col_quantity').checked = true;
    document.getElementById('col_barcode').checked = true;
    document.getElementById('col_average_cost').checked = true;
    document.getElementById('col_quantity_average_cost').checked = true;
    document.getElementById('col_last_cost').checked = true;
    document.getElementById('col_quantity_cost').checked = true;
    document.getElementById('col_actions').checked = true;
    
    // Show all price checkboxes
    document.querySelectorAll('input[id^="price_"]').forEach(checkbox => {
        checkbox.checked = true;
    });
    
    // Show all note checkboxes
    document.querySelectorAll('input[id^="note_"]').forEach(checkbox => {
        checkbox.checked = true;
    });
}

function hideAllCheckboxes() {
    // Hide all column checkboxes
    document.getElementById('col_code').checked = false;
    document.getElementById('col_name').checked = false;
    document.getElementById('col_units').checked = false;
    document.getElementById('col_quantity').checked = false;
    document.getElementById('col_barcode').checked = false;
    document.getElementById('col_average_cost').checked = false;
    document.getElementById('col_quantity_average_cost').checked = false;
    document.getElementById('col_last_cost').checked = false;
    document.getElementById('col_quantity_cost').checked = false;
    document.getElementById('col_actions').checked = false;
    
    // Hide all price checkboxes
    document.querySelectorAll('input[id^="price_"]').forEach(checkbox => {
        checkbox.checked = false;
    });
    
    // Hide all note checkboxes
    document.querySelectorAll('input[id^="note_"]').forEach(checkbox => {
        checkbox.checked = false;
    });
}

function showAllPrices() {
    document.querySelectorAll('input[id^="price_"]').forEach(checkbox => {
        checkbox.checked = true;
    });
}

function hideAllPrices() {
    document.querySelectorAll('input[id^="price_"]').forEach(checkbox => {
        checkbox.checked = false;
    });
}

function showAllNotes() {
    document.querySelectorAll('input[id^="note_"]').forEach(checkbox => {
        checkbox.checked = true;
    });
}

function hideAllNotes() {
    document.querySelectorAll('input[id^="note_"]').forEach(checkbox => {
        checkbox.checked = false;
    });
}
</script>
