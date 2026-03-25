<?php

declare(strict_types=1);

use App\Models\Item;
use App\Models\Note;
use App\Models\NoteDetails;
use App\Models\Price;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Computed;
use Livewire\Volt\Component;
use Livewire\WithPagination;

new class extends Component
{
    use WithPagination;

    protected $paginationTheme = 'bootstrap';

    public $search = '';

    public $selectedGroup = null;

    public $selectedCategory = null;

    public $perPage = 50;

    public $groups;

    public $categories;

    // For bulk editing - all items editable at once
    public $allPrices = [];

    public $allNotes = [];

    public $hasChanges = false;

    // For bulk price increase
    public $selectedPriceType = null;

    public $increaseType = 'percentage'; // percentage or fixed

    public $increaseValue = 0;

    // Base price for bulk increase calculation
    public $basePriceType = 'last_purchase'; // last_purchase, average, retail, wholesale

    public function mount(): void
    {
        $this->groups = Cache::remember('note_groups', 3600, fn () => NoteDetails::where('note_id', 1)->orderBy('id')->pluck('name', 'id'));
        $this->categories = Cache::remember('note_categories', 3600, fn () => NoteDetails::where('note_id', 2)->orderBy('id')->pluck('name', 'id'));
        $this->loadAllData();
    }

    public function loadAllData(): void
    {
        $items = $this->items;
        $priceTypes = Price::all();
        $noteTypes = Note::all();

        foreach ($items as $item) {
            // Load prices for each unit
            foreach ($item->units as $unit) {
                // Ensure $unit is an object with an id property
                $unitId = is_object($unit) ? $unit->id : (int) $unit;

                foreach ($priceTypes as $priceType) {
                    $itemPrice = $item->prices()
                        ->where('price_id', $priceType->id)
                        ->wherePivot('unit_id', $unitId)
                        ->first();

                    $this->allPrices[$item->id][$unitId][$priceType->id] = $itemPrice ? $itemPrice->pivot->price : '';
                }
            }

            // Load notes (once per item, not per unit)
            foreach ($noteTypes as $noteType) {
                $itemNote = $item->notes->where('id', $noteType->id)->first();
                $this->allNotes[$item->id][$noteType->id] = $itemNote ? $itemNote->pivot->note_detail_name : '';
            }
        }
    }

    #[Computed]
    public function priceTypes()
    {
        return Price::all();
    }

    #[Computed]
    public function noteTypes()
    {
        return Note::all();
    }

    #[Computed]
    public function items()
    {
        $query = Item::query()
            ->with(['units', 'prices', 'notes'])
            ->where('isdeleted', 0);

        if ($this->search) {
            $query->where(function ($q) {
                $q->where('name', 'like', "%{$this->search}%")
                    ->orWhere('code', 'like', "%{$this->search}%");
            });
        }

        if ($this->selectedGroup) {
            $query->whereHas('notes', function ($q) {
                $q->where('note_id', 1)
                    ->where('note_detail_name', $this->selectedGroup);
            });
        }

        if ($this->selectedCategory) {
            $query->whereHas('notes', function ($q) {
                $q->where('note_id', 2)
                    ->where('note_detail_name', $this->selectedCategory);
            });
        }

        $items = $query->paginate($this->perPage);

        // حساب آخر سعر شراء وأسعار أخرى لكل صنف
        foreach ($items as $item) {
            // آخر سعر شراء
            $lastPurchasePrice = DB::table('operation_items')
                ->join('operhead', 'operation_items.pro_id', '=', 'operhead.id')
                ->where('operation_items.item_id', $item->id)
                ->where('operation_items.isdeleted', 0)
                ->whereIn('operhead.pro_type', [11, 13, 15, 17, 24, 25]) // أنواع عمليات الشراء
                ->orderBy('operation_items.created_at', 'desc')
                ->value('operation_items.item_price');

            $item->last_purchase_price = $lastPurchasePrice ?? 0;

            // متوسط سعر الشراء
            $averagePurchasePrice = DB::table('operation_items')
                ->join('operhead', 'operation_items.pro_id', '=', 'operhead.id')
                ->where('operation_items.item_id', $item->id)
                ->where('operation_items.isdeleted', 0)
                ->whereIn('operhead.pro_type', [11, 13, 15, 17, 24, 25])
                ->avg('operation_items.item_price');

            $item->average_purchase_price = $averagePurchasePrice ?? 0;
        }

        return $items;
    }

    public function saveAllChanges(): void
    {
        try {
            DB::transaction(function () {
                foreach ($this->allPrices as $itemId => $units) {
                    $item = Item::find($itemId);
                    if (! $item) {
                        continue;
                    }

                    foreach ($units as $unitId => $prices) {
                        foreach ($prices as $priceId => $price) {
                            if (! empty($price)) {
                                $item->prices()->syncWithoutDetaching([
                                    $priceId => [
                                        'unit_id' => $unitId,
                                        'price' => $price,
                                        'discount' => 0,
                                        'tax_rate' => 0,
                                    ],
                                ]);
                            }
                        }
                    }
                }

                foreach ($this->allNotes as $itemId => $notes) {
                    $item = Item::find($itemId);
                    if (! $item) {
                        continue;
                    }

                    foreach ($notes as $noteId => $noteDetailName) {
                        if (! empty($noteDetailName)) {
                            $item->notes()->syncWithoutDetaching([
                                $noteId => ['note_detail_name' => $noteDetailName],
                            ]);
                        }
                    }
                }
            });

            $this->hasChanges = false;
            session()->flash('success', __('items.all_changes_saved_successfully'));
            $this->loadAllData();
        } catch (\Exception $e) {
            session()->flash('error', __('common.error_occurred').': '.$e->getMessage());
        }
    }

    public function markAsChanged(): void
    {
        $this->hasChanges = true;
    }

    public function updatedSearch(): void
    {
        $this->resetPage();
        $this->loadAllData();
    }

    public function updatedSelectedGroup(): void
    {
        $this->resetPage();
        $this->loadAllData();
    }

    public function updatedSelectedCategory(): void
    {
        $this->resetPage();
        $this->loadAllData();
    }

    public function clearFilters(): void
    {
        $this->search = '';
        $this->selectedGroup = null;
        $this->selectedCategory = null;
        $this->resetPage();
        $this->loadAllData();
    }

    public function updatedPerPage(): void
    {
        $this->resetPage();
        $this->loadAllData();
    }

    public function applyBulkIncrease(): void
    {
        if (! $this->selectedPriceType || ! $this->increaseValue || ! $this->basePriceType) {
            session()->flash('error', __('items.please_select_all_required_fields'));
            return;
        }

        try {
            $items = $this->items;

            foreach ($items as $item) {
                // تحديد السعر الأساسي للحساب
                $basePrice = 0;
                
                switch ($this->basePriceType) {
                    case 'last_purchase':
                        $basePrice = $item->last_purchase_price ?? 0;
                        break;
                    case 'average':
                        $basePrice = $item->average_purchase_price ?? 0;
                        break;
                    case 'retail':
                        // البحث عن سعر التجزئة (افتراض أن id = 1 للتجزئة)
                        $retailPriceType = $this->priceTypes->where('name', 'like', '%تجزئة%')->first() 
                                        ?? $this->priceTypes->where('name', 'like', '%retail%')->first()
                                        ?? $this->priceTypes->first();
                        if ($retailPriceType) {
                            $unitId = $item->units->first() ? $item->units->first()->id : 1;
                            $basePrice = $this->allPrices[$item->id][$unitId][$retailPriceType->id] ?? 0;
                        }
                        break;
                    case 'wholesale':
                        // البحث عن سعر الجملة
                        $wholesalePriceType = $this->priceTypes->where('name', 'like', '%جملة%')->first() 
                                           ?? $this->priceTypes->where('name', 'like', '%wholesale%')->first()
                                           ?? $this->priceTypes->skip(1)->first();
                        if ($wholesalePriceType) {
                            $unitId = $item->units->first() ? $item->units->first()->id : 1;
                            $basePrice = $this->allPrices[$item->id][$unitId][$wholesalePriceType->id] ?? 0;
                        }
                        break;
                }

                if ($basePrice > 0) {
                    foreach ($item->units as $unit) {
                        $unitId = is_object($unit) ? $unit->id : (int) $unit;

                        // حساب السعر الجديد
                        if ($this->increaseType === 'percentage') {
                            $newPrice = $basePrice + ($basePrice * ($this->increaseValue / 100));
                        } else {
                            $newPrice = $basePrice + $this->increaseValue;
                        }

                        // تطبيق السعر الجديد على نوع السعر المحدد فقط
                        $this->allPrices[$item->id][$unitId][$this->selectedPriceType] = round($newPrice, 2);
                    }
                }
            }

            $this->hasChanges = true;
            session()->flash('success', __('items.bulk_increase_applied'));
        } catch (\Exception $e) {
            session()->flash('error', __('common.error_occurred').': '.$e->getMessage());
        }
    }

    public function with(): array
    {
        return [
            'priceTypes' => $this->priceTypes,
            'noteTypes' => $this->noteTypes,
        ];
    }

}; ?>

<div>
    <div class="row">
        <div class="col-lg-12">
            @if (session()->has('success'))
                <div class="alert alert-success font-hold fw-bold font-12 mt-2" x-data="{ show: true }" x-show="show"
                    x-init="setTimeout(() => show = false, 3000)">
                    {{ session('success') }}
                </div>
            @endif

            @if (session()->has('error'))
                <div class="alert alert-danger font-hold fw-bold font-12 mt-2" x-data="{ show: true }" x-show="show"
                    x-init="setTimeout(() => show = false, 5000)">
                    {{ session('error') }}
                </div>
            @endif

            <div class="card">
                <div class="text-center py-3">
                    <h5 class="card-title font-hold fw-bold font-20 text-white">
                        {{ __('items.manage_prices_and_groups') }}
                    </h5>
                </div>

                <div class="card-header">
                    <!-- Bulk Price Increase Section -->
                    <div class="mb-3 p-3 bg-light rounded" x-data="{ showBulkIncrease: false }">
                        <button type="button" @click="showBulkIncrease = !showBulkIncrease" 
                            class="btn btn-primary btn-lg font-hold fw-bold">
                            <i class="las la-percentage me-1"></i>
                            {{ __('items.bulk_price_increase') }}
                            <i class="las" :class="showBulkIncrease ? 'la-angle-up' : 'la-angle-down'"></i>
                        </button>

                        <div x-show="showBulkIncrease" x-transition class="mt-3">
                            <div class="row g-3 align-items-end">
                                <div class="col-md-2">
                                    <label class="form-label font-hold fw-bold">{{ __('items.base_price_type') }}:</label>
                                    <select wire:model="basePriceType" class="form-select font-hold fw-bold">
                                        <option value="last_purchase">{{ __('items.last_purchase_price') }}</option>
                                        <option value="average">{{ __('items.average_purchase_price') }}</option>
                                        <option value="retail">{{ __('items.retail_price') }}</option>
                                        <option value="wholesale">{{ __('items.wholesale_price') }}</option>
                                    </select>
                                </div>

                                <div class="col-md-2">
                                    <label class="form-label font-hold fw-bold">{{ __('items.target_price_type') }}:</label>
                                    <select wire:model="selectedPriceType" class="form-select font-hold fw-bold">
                                        <option value="">{{ __('common.select') }}</option>
                                        @foreach ($this->priceTypes as $priceType)
                                            <option value="{{ $priceType->id }}">{{ $priceType->name }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="col-md-2">
                                    <label class="form-label font-hold fw-bold">{{ __('items.increase_type') }}:</label>
                                    <select wire:model="increaseType" class="form-select font-hold fw-bold">
                                        <option value="percentage">{{ __('items.percentage') }}</option>
                                        <option value="fixed">{{ __('items.fixed_amount') }}</option>
                                    </select>
                                </div>

                                <div class="col-md-2">
                                    <label class="form-label font-hold fw-bold">{{ __('items.value') }}:</label>
                                    <input type="number" step="0.01" wire:model="increaseValue" 
                                        class="form-control font-hold fw-bold" placeholder="0">
                                </div>

                                <div class="col-md-2">
                                    <button type="button" wire:click="applyBulkIncrease" 
                                        class="btn btn-warning btn-lg font-hold fw-bold w-100">
                                        <i class="las la-calculator me-1"></i>
                                        {{ __('items.apply_increase') }}
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="d-flex flex-wrap justify-content-between align-items-center gap-3">
                        <!-- Save Button -->
                        <div>
                            <button type="button" wire:click="saveAllChanges" 
                                class="btn btn-success btn-lg font-hold fw-bold"
                                @if(!$hasChanges) disabled @endif>
                                <i class="las la-save me-1"></i>
                                {{ __('common.save_all_changes') }}
                            </button>
                            @if($hasChanges)
                                <span class="badge bg-warning text-dark ms-2 font-hold">
                                    <i class="las la-exclamation-triangle"></i>
                                    {{ __('common.unsaved_changes') }}
                                </span>
                            @endif
                        </div>

                        <div class="d-flex flex-grow-1 flex-wrap align-items-center justify-content-end gap-2">
                            <!-- Clear Filters -->
                            <div class="d-flex align-items-end">
                                <button type="button" wire:click="clearFilters" class="btn btn-outline-info btn-lg font-hold fw-bold">
                                    <i class="fas fa-times me-1"></i>
                                    {{ __('common.clear_filters') }}
                                </button>
                            </div>

                            <!-- Search -->
                            <div class="flex-grow-1">
                                <label class="form-label font-hold fw-bold font-12 mb-1">{{ __('common.search') }}:</label>
                                <input type="text" wire:model.live.debounce.500ms="search" class="form-control font-hold"
                                    placeholder="{{ __('items.search_placeholder') }}">
                            </div>

                            <!-- Group Filter -->
                            <div class="flex-grow-1">
                                <label class="form-label font-hold fw-bold font-12 mb-1">{{ __('items.group') }}:</label>
                                <select wire:model.live="selectedGroup" class="form-select font-hold fw-bold font-14">
                                    <option value="">{{ __('items.all_groups') }}</option>
                                    @foreach ($groups as $id => $name)
                                        <option value="{{ $name }}">{{ $name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <!-- Category Filter -->
                            <div class="flex-grow-1">
                                <label class="form-label font-hold fw-bold font-12 mb-1">{{ __('items.category') }}:</label>
                                <select wire:model.live="selectedCategory" class="form-select font-hold fw-bold font-14">
                                    <option value="">{{ __('items.all_categories') }}</option>
                                    @foreach ($categories as $id => $name)
                                        <option value="{{ $name }}">{{ $name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card-body">
                    <!-- Pagination Control -->
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <div class="d-flex align-items-center gap-2">
                            <label class="form-label font-hold fw-bold mb-0">{{ __('items.display') }}:</label>
                            <select wire:model.live="perPage" class="form-select form-select-sm font-hold fw-bold" style="width: auto;">
                                <option value="25">25</option>
                                <option value="50">50</option>
                                <option value="100">100</option>
                            </select>
                            <span class="font-hold fw-bold">{{ __('items.record') }}</span>
                        </div>
                        <div class="font-hold fw-bold text-muted">
                            {{ __('items.total_results') }}: <span class="text-primary">{{ $this->items->total() }}</span>
                        </div>
                    </div>

                    <div class="table-responsive" style="max-height: 70vh; overflow-y: auto;">
                        <table class="table table-striped table-hover mb-0">
                            <thead class="table-light text-center align-middle" style="position: sticky; top: 0; z-index: 10;">
                                <tr>
                                    <th class="font-hold fw-bold">#</th>
                                    <th class="font-hold fw-bold">{{ __('common.code') }}</th>
                                    <th class="font-hold fw-bold">{{ __('common.name') }}</th>
                                    <th class="font-hold fw-bold">{{ __('items.unit') }}</th>
                                    <th class="font-hold fw-bold bg-info-subtle">{{ __('items.last_purchase_price') }}</th>
                                    <th class="font-hold fw-bold bg-warning-subtle">{{ __('items.average_purchase_price') }}</th>
                                    @foreach ($this->priceTypes as $priceType)
                                        <th class="font-hold fw-bold">{{ $priceType->name }}</th>
                                    @endforeach
                                    @foreach ($this->noteTypes as $noteType)
                                        <th class="font-hold fw-bold">{{ $noteType->name }}</th>
                                    @endforeach
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($this->items as $index => $item)
                                    @php
                                        $itemUnits = $item->units;
                                        $unitsCount = $itemUnits->count();
                                    @endphp
                                    
                                    @if($unitsCount > 0)
                                        @foreach($itemUnits as $unitIndex => $unit)
                                            @php
                                                $unitId = is_object($unit) ? $unit->id : (int) $unit;
                                                $unitName = is_object($unit) ? $unit->name : (string) $unit;
                                            @endphp
                                            <tr>
                                                @if($unitIndex === 0)
                                                    <!-- Show item info only in first row -->
                                                    <td class="text-center font-hold" rowspan="{{ $unitsCount }}">
                                                        {{ $this->items->firstItem() + $index }}
                                                    </td>
                                                    <td class="text-center font-hold" rowspan="{{ $unitsCount }}">
                                                        {{ $item->code }}
                                                    </td>
                                                    <td class="font-hold" rowspan="{{ $unitsCount }}">
                                                        {{ $item->name }}
                                                    </td>
                                                @endif

                                                <!-- Unit Name (static text) -->
                                                <td class="text-center font-hold fw-bold">
                                                    {{ $unitName }}
                                                </td>

                                                <!-- Last Purchase Price (Read Only) -->
                                                <td class="text-center font-hold bg-info-subtle">
                                                    {{ number_format($item->last_purchase_price ?? 0, 2) }}
                                                </td>

                                                <!-- Average Purchase Price (Read Only) -->
                                                <td class="text-center font-hold bg-warning-subtle">
                                                    {{ number_format($item->average_purchase_price ?? 0, 2) }}
                                                </td>

                                                <!-- Price Inputs for this unit -->
                                                @foreach ($this->priceTypes as $priceType)
                                                    <td>
                                                        <input type="number" step="0.01" 
                                                            wire:model="allPrices.{{ $item->id }}.{{ $unitId }}.{{ $priceType->id }}"
                                                            wire:change="markAsChanged"
                                                            class="form-control form-control-sm font-hold" 
                                                            placeholder="0.00">
                                                    </td>
                                                @endforeach

                                                @if($unitIndex === 0)
                                                    <!-- Note Inputs (show only in first row) -->
                                                    @foreach ($this->noteTypes as $noteType)
                                                        <td rowspan="{{ $unitsCount }}">
                                                            <select wire:model="allNotes.{{ $item->id }}.{{ $noteType->id }}" 
                                                                wire:change="markAsChanged"
                                                                class="form-select form-select-sm font-hold">
                                                                <option value="">{{ __('common.select') }}</option>
                                                                @php
                                                                    $noteDetails = NoteDetails::where('note_id', $noteType->id)->get();
                                                                @endphp
                                                                @foreach ($noteDetails as $detail)
                                                                    <option value="{{ $detail->name }}">{{ $detail->name }}</option>
                                                                @endforeach
                                                            </select>
                                                        </td>
                                                    @endforeach
                                                @endif
                                            </tr>
                                        @endforeach
                                    @else
                                        <!-- Item has no units -->
                                        <tr>
                                            <td class="text-center font-hold">{{ $this->items->firstItem() + $index }}</td>
                                            <td class="text-center font-hold">{{ $item->code }}</td>
                                            <td class="font-hold">{{ $item->name }}</td>
                                            <td class="text-center text-muted font-hold">-</td>
                                            <td class="text-center text-muted bg-info-subtle">-</td>
                                            <td class="text-center text-muted bg-warning-subtle">-</td>
                                            @foreach ($this->priceTypes as $priceType)
                                                <td class="text-center text-muted">-</td>
                                            @endforeach
                                            @foreach ($this->noteTypes as $noteType)
                                                <td>
                                                    <select wire:model="allNotes.{{ $item->id }}.{{ $noteType->id }}" 
                                                        wire:change="markAsChanged"
                                                        class="form-select form-select-sm font-hold">
                                                        <option value="">{{ __('common.select') }}</option>
                                                        @php
                                                            $noteDetails = NoteDetails::where('note_id', $noteType->id)->get();
                                                        @endphp
                                                        @foreach ($noteDetails as $detail)
                                                            <option value="{{ $detail->name }}">{{ $detail->name }}</option>
                                                        @endforeach
                                                    </select>
                                                </td>
                                            @endforeach
                                        </tr>
                                    @endif
                                @empty
                                    <tr>
                                        <td colspan="{{ 6 + count($priceTypes) + count($noteTypes) }}" 
                                            class="text-center font-hold fw-bold py-4">
                                            {{ __('items.no_items_found') }}
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    <div class="mt-3">
                        {{ $this->items->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>