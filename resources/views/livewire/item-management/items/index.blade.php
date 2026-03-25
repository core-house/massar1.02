<?php

use App\Models\Item;
use App\Models\Note;
use App\Models\NoteDetails;
use App\Models\OperationItems;
use App\Models\Price;
use App\Services\ItemsQueryService;
use App\Support\ItemDataTransformer;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Locked;
use Livewire\Volt\Component;
use Livewire\WithPagination;
use Modules\Accounts\Models\AccHead;

new class extends Component {
    use WithPagination;

    protected $paginationTheme = 'bootstrap';

    public $selectedUnit = [];

    public $displayItemData = [];

    public $perPage = 100; // Flexible pagination

    // Lazy-loaded data caches
    public $loadedPriceData = [];

    public $loadedNoteData = [];

    // Base quantities cache for current page
    public $baseQuantities = [];

    #[Locked]
    public $priceTypes;

    #[Locked]
    public $noteTypes;

    public $search = '';

    #[Locked]
    public $warehouses;

    public $selectedWarehouse = null;

    public $selectedPriceType = '';

    #[Locked]
    public $groups;

    public $selectedGroup = null;

    #[Locked]
    public $categories;

    public $selectedCategory = null;

    // Quantity filter settings
    public $quantityFilterType = '';
    public $quantityFilterValue = null;

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

    // Check if we're in a reports context
    public function isReportsPage(): bool
    {
        $url = request()->fullUrl() ?? (url()->current() ?? '');

        return str_contains($url, 'reports');
    }

    public function mount()
    {
        // Cache static data for 60 minutes
        $this->priceTypes = Cache::remember('price_types', 3600, function () {
            return Price::all()->pluck('name', 'id');
        });

        $this->noteTypes = Cache::remember('note_types', 3600, function () {
            return Note::all()->pluck('name', 'id');
        });

        $this->warehouses = AccHead::where('code', 'like', '1104%')->where('is_basic', 0)->orderBy('id')->get();

        $this->groups = Cache::remember('note_groups', 3600, function () {
            return NoteDetails::where('note_id', 1)->orderBy('id')->pluck('name', 'id');
        });

        $this->categories = Cache::remember('note_categories', 3600, function () {
            return NoteDetails::where('note_id', 2)->orderBy('id')->pluck('name', 'id');
        });

        // Initialize note visibility - all notes visible by default
        foreach ($this->noteTypes as $noteId => $noteName) {
            $this->visibleNotes[$noteId] = true;
        }

        // Initialize price visibility - all prices visible by default
        foreach ($this->priceTypes as $priceId => $priceName) {
            $this->visiblePrices[$priceId] = true;
        }
    }

    #[Computed]
    public function items()
    {
        $queryService = new ItemsQueryService();
        $query = $queryService->buildFilteredQuery($this->search, (int) $this->selectedGroup, (int) $this->selectedCategory);
        
        // Apply quantity filter if set
        if ($this->quantityFilterType && $this->quantityFilterValue !== null && $this->quantityFilterValue !== '') {
            $warehouseId = (int) $this->selectedWarehouse;
            
            // Get item IDs with their quantities
            $itemsWithQuantities = $query->get()->map(function ($item) use ($warehouseId, $queryService) {
                $baseQty = $queryService->getBaseQuantitiesForItems([$item->id], $warehouseId)[$item->id] ?? 0;
                return ['id' => $item->id, 'quantity' => $baseQty];
            });
            
            // Filter based on quantity condition
            $filteredIds = $itemsWithQuantities->filter(function ($item) {
                $qty = $item['quantity'];
                $filterValue = (float) $this->quantityFilterValue;
                
                switch ($this->quantityFilterType) {
                    case 'greater':
                        return $qty > $filterValue;
                    case 'less':
                        return $qty < $filterValue;
                    case 'equal':
                        return abs($qty - $filterValue) < 0.001;
                    case 'greaterOrEqual':
                        return $qty >= $filterValue;
                    case 'lessOrEqual':
                        return $qty <= $filterValue;
                    case 'notEqual':
                        return abs($qty - $filterValue) >= 0.001;
                    default:
                        return true;
                }
            })->pluck('id')->toArray();
            
            // Apply the filter to the query
            $query->whereIn('id', $filteredIds);
        }
        
        $items = $query->paginate($this->perPage);

        // Load base quantities for all items in current page
        $this->baseQuantities = $queryService->getBaseQuantitiesForItems($items->pluck('id')->all(), (int) $this->selectedWarehouse);

        // Pre-calculate display data for all items in current page
        $this->prepareDisplayData($items);

        return $items;
    }

    protected function prepareDisplayData($items)
    {
        // Batch load last purchase prices for all items
        $itemIds = $items->pluck('id')->all();
        $lastPurchasePrices = ItemDataTransformer::getLastPurchasePricesForItems($itemIds);

        foreach ($items as $item) {
            if (!isset($this->selectedUnit[$item->id])) {
                $defaultUnit = $item->units->sortBy('pivot.u_val')->first();
                $this->selectedUnit[$item->id] = $defaultUnit ? $defaultUnit->id : null;
            }

            // Prepare Alpine.js data for client-side calculations
            $itemId = $item->id;
            $baseQty = $this->baseQuantities[$itemId] ?? 0;
            $lastPurchasePrice = $lastPurchasePrices[$itemId] ?? 0;
            $this->displayItemData[$itemId] = ItemDataTransformer::getItemDataForAlpine($item, (int) $this->selectedWarehouse, $baseQty, $lastPurchasePrice);
        }
    }

    public function getTotalQuantityProperty()
    {
        if (!$this->selectedPriceType) {
            return 0;
        }

        $queryService = new ItemsQueryService();

        return $queryService->getTotalQuantity($this->search, (int) $this->selectedGroup, (int) $this->selectedCategory, (int) $this->selectedWarehouse);
    }

    public function getTotalAmountProperty()
    {
        if (!$this->selectedPriceType) {
            return 0;
        }

        $queryService = new ItemsQueryService();

        return $queryService->getTotalAmount($this->search, (int) $this->selectedGroup, (int) $this->selectedCategory, $this->selectedPriceType, (int) $this->selectedWarehouse);
    }

    public function getTotalItemsProperty()
    {
        if (!$this->selectedPriceType) {
            return 0;
        }

        $queryService = new ItemsQueryService();

        return $queryService->getTotalItems($this->search, (int) $this->selectedGroup, (int) $this->selectedCategory, (int) $this->selectedWarehouse);
    }

    public function updatedSearch()
    {
        $this->resetPage();
        $this->clearLazyLoadedData();
    }

    public function updatedSelectedWarehouse()
    {
        $this->resetPage();
        $this->clearLazyLoadedData();
        // Force component refresh to update Alpine.js data
        $this->dispatch('$refresh');
    }

    public function updatedSelectedGroup()
    {
        $this->resetPage();
        $this->clearLazyLoadedData();
    }

    public function updatedSelectedCategory()
    {
        $this->resetPage();
        $this->clearLazyLoadedData();
    }

    public function updatedQuantityFilterType()
    {
        $this->resetPage();
        $this->clearLazyLoadedData();
    }

    public function updatedQuantityFilterValue()
    {
        $this->resetPage();
        $this->clearLazyLoadedData();
    }

    public function updatedPerPage()
    {
        $this->resetPage();
        $this->clearLazyLoadedData();
    }

    /**
     * Clear lazy-loaded data cache
     * Called when filters change or page changes
     */
    protected function clearLazyLoadedData()
    {
        $this->loadedPriceData = [];
        $this->loadedNoteData = [];
        $this->baseQuantities = [];
        $this->displayItemData = [];
    }

    public function clearFilters()
    {
        $this->search = '';
        $this->selectedWarehouse = null;
        $this->selectedGroup = null;
        $this->selectedCategory = null;
        $this->quantityFilterType = '';
        $this->quantityFilterValue = null;
        $this->resetPage();
    }

    public function setPerPage($value)
    {
        $this->perPage = in_array($value, [25, 50, 100, 200]) ? $value : 50;
        $this->resetPage();
    }

    /**
     * Lazy load price data for a specific price type
     * Only loads data for items on current page
     */
    public function loadPriceColumn($priceId)
    {
        if (isset($this->loadedPriceData[$priceId])) {
            return; // Already loaded
        }

        $itemIds = $this->items->pluck('id');

        if ($itemIds->isEmpty()) {
            $this->loadedPriceData[$priceId] = [];

            return;
        }

        $prices = DB::table('item_prices')->whereIn('item_id', $itemIds)->where('price_id', $priceId)->get()->keyBy('item_id');

        $this->loadedPriceData[$priceId] = $prices;
    }

    /**
     * Lazy load note data for a specific note type
     * Only loads data for items on current page
     */
    public function loadNoteColumn($noteId)
    {
        if (isset($this->loadedNoteData[$noteId])) {
            return; // Already loaded
        }

        $itemIds = $this->items->pluck('id');

        if ($itemIds->isEmpty()) {
            $this->loadedNoteData[$noteId] = [];

            return;
        }

        $notes = DB::table('item_notes')->whereIn('item_id', $itemIds)->where('note_id', $noteId)->get()->keyBy('item_id');

        $this->loadedNoteData[$noteId] = $notes;
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

    public function edit($itemId)
    {
        redirect()->route('items.edit', $itemId);
    }

    public function delete($itemId)
    {
        // check if the item is used in any operation
        $operationItems = OperationItems::where('item_id', $itemId)->count();
        if ($operationItems > 0) {
            session()->flash('error', __('items.cannot_delete_item_used_in_operations'));
            return;
        }

        // Check all tables that have a foreign key on item_id
        $relatedTables = [
            'non_conformance_reports',
            'quality_inspections',
        ];

        foreach ($relatedTables as $table) {
            try {
                if (DB::getSchemaBuilder()->hasTable($table) && DB::getSchemaBuilder()->hasColumn($table, 'item_id')) {
                    if (DB::table($table)->where('item_id', $itemId)->exists()) {
                        session()->flash('error', __('items.cannot_delete_item_used_in_operations'));
                        return;
                    }
                }
            } catch (\Exception $e) {
                // Skip table if any error occurs
            }
        }

        $item = Item::with('units', 'prices', 'notes', 'barcodes')->findOrFail($itemId);
        DB::transaction(function () use ($item) {
            $item->units()->detach();
            $item->prices()->detach();
            $item->notes()->detach();
            $item->barcodes()->delete();
            $item->clearMediaCollection('item-thumbnail');
            $item->clearMediaCollection('item-images');
            $item->delete();
        });
        session()->flash('success', __('items.item_deleted_successfully'));
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

    public function updateVisibility($columns, $prices, $notes)
    {
        // Update columns
        $this->visibleColumns = $columns;

        // Update prices and lazy load newly visible ones
        $previousPrices = $this->visiblePrices;
        $this->visiblePrices = $prices;
        foreach ($prices as $priceId => $isVisible) {
            if ($isVisible && !($previousPrices[$priceId] ?? false)) {
                // Newly visible - lazy load
                $this->loadPriceColumn($priceId);
            }
        }

        // Update notes and lazy load newly visible ones
        $previousNotes = $this->visibleNotes;
        $this->visibleNotes = $notes;
        foreach ($notes as $noteId => $isVisible) {
            if ($isVisible && !($previousNotes[$noteId] ?? false)) {
                // Newly visible - lazy load
                $this->loadNoteColumn($noteId);
            }
        }

        // Clear display data to force recalculation
        $this->displayItemData = [];

        // Reset page to ensure proper display
        $this->resetPage();

        // Force refresh the component to apply all changes
        $this->dispatch('$refresh');

        // Show success message
        session()->flash('success', __('items.visibility_changes_applied_successfully'));

        // Close modal after applying changes
        $this->dispatch('close-modal');
    }

    public function loadItemImages(int $itemId): void
    {
        $item = Item::findOrFail($itemId);

        $images = collect();

        $thumbnail = $item->getFirstMedia('item-thumbnail');
        if ($thumbnail) {
            $images->push([
                'url'   => $thumbnail->getUrl(),
                'thumb' => $thumbnail->hasGeneratedConversion('thumb') ? $thumbnail->getUrl('thumb') : $thumbnail->getUrl(),
                'name'  => $thumbnail->name,
            ]);
        }

        // ???? ?????
        $item->getMedia('item-images')->each(function ($media) use ($images) {
            $images->push([
                'url'   => $media->getUrl(),
                'thumb' => $media->hasGeneratedConversion('thumb') ? $media->getUrl('thumb') : $media->getUrl(),
                'name'  => $media->name,
            ]);
        });

        $this->dispatch('show-item-images', images: $images->values()->toArray());
    }
}; ?>

<div>
    @php
        include_once app_path('Helpers/FormatHelper.php');
    @endphp


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
                    x-init="setTimeout(() => show = false, 3000)">
                    {{ session('error') }}
                </div>
            @endif
            <div class="card">
                {{-- card title --}}
                <div class="text-center py-3">
                    <h5 class="card-title font-hold fw-bold font-20 text-white">
                        {{ __('items.items_list_with_balances') }}
                    </h5>
                </div>



                <div class="card-header">
                    <div class="d-flex flex-wrap justify-content-between align-items-center gap-3">
                        {{-- Primary Action Button --}}
                        {{-- @if (Auth::user() && Auth::user()->hasDirectPermission('create items')) --}}
                        @can('create items')
                            <a href="{{ route('items.create') }}" class="btn btn-main btn-lg font-hold fw-bold mt-4"
                                style="min-height: 50px;">
                                <i class="fas fa-plus me-2"></i>
                                {{ __('items.add_new_item') }}
                            </a>
                        @endcan
                        {{-- @endif --}}

                        {{-- Print Button --}}
                        @can('print items')
                            <div class="mt-4">
                                <a href="{{ route('items.print', [
                                    'search' => $search,
                                    'warehouse' => $selectedWarehouse,
                                    'group' => $selectedGroup,
                                    'category' => $selectedCategory,
                                    'priceType' => $selectedPriceType,
                                ]) }}"
                                    target="_blank" class="btn btn-main btn-lg font-hold fw-bold">
                                    <i class="fas fa-print me-2"></i>
                                    {{ __('items.print_list') }}
                                </a>
                            </div>
                        @endcan

                        {{-- Column Visibility Button --}}
                        <div class="mt-4">
                            <button type="button" class="btn btn-main btn-lg font-hold fw-bold" data-bs-toggle="modal"
                                data-bs-target="#columnVisibilityModal">
                                <i class="fas fa-columns me-2"></i>
                                {{ __('items.display_options') }}
                            </button>
                        </div>

                        {{-- Search and Filter Group --}}
                        <div class="d-flex flex-grow-1 flex-wrap align-items-center justify-content-end gap-2"
                            style="min-width: 300px;" x-data="filtersComponent()" x-init="searchValue = @js($this->search);
                            warehouseValue = @js($this->selectedWarehouse);
                            groupValue = @js($this->selectedGroup);
                            categoryValue = @js($this->selectedCategory);">
                            
                            {{-- Search Input --}}
                            <div class="flex-grow-1">
                                <label
                                    class="form-label font-hold fw-bold font-12 mb-1">{{ __('common.search') }}:</label>
                                <div class="input-group">
                                    <span class="input-group-text">
                                        <i class="fas fa-search" wire:loading.remove wire:target="search"></i>
                                        <div class="spinner-border spinner-border-sm" role="status" wire:loading
                                            wire:target="search">
                                            <span class="visually-hidden">{{ __('common.loading') }}</span>
                                        </div>
                                    </span>
                                    <input type="text" x-model="searchValue" @input="updateSearch()"
                                        class="form-control font-hold"
                                        placeholder="{{ __('items.search_placeholder') }}" wire:loading.attr="disabled"
                                        wire:target="search">
                                </div>
                            </div>

                            {{-- Warehouse Filter --}}
                            <div class="flex-grow-1">
                                <label
                                    class="form-label font-hold fw-bold font-12 mb-1">{{ __('items.warehouse') }}:</label>
                                <div class="input-group">
                                    <select x-model="warehouseValue" @change="updateWarehouse()"
                                        class="form-select font-hold fw-bold font-14" wire:loading.attr="disabled"
                                        wire:target="selectedWarehouse">
                                        <option value="">{{ __('items.all_warehouses') }}</option>
                                        @foreach ($warehouses as $warehouse)
                                            <option value="{{ $warehouse->id }}">{{ $warehouse->aname }}</option>
                                        @endforeach
                                    </select>
                                    <span class="input-group-text">
                                        <i class="fas fa-warehouse" wire:loading.remove
                                            wire:target="selectedWarehouse"></i>
                                        <div class="spinner-border spinner-border-sm" role="status" wire:loading
                                            wire:target="selectedWarehouse">
                                            <span class="visually-hidden">{{ __('common.loading') }}</span>
                                        </div>
                                    </span>
                                </div>
                            </div>

                            {{-- Group Filter --}}
                            <div class="flex-grow-1">
                                <label
                                    class="form-label font-hold fw-bold font-12 mb-1">{{ __('items.group') }}:</label>
                                <div class="input-group">
                                    <select x-model="groupValue" @change="updateGroup()"
                                        class="form-select font-hold fw-bold font-14" wire:loading.attr="disabled"
                                        wire:target="selectedGroup">
                                        <option value="">{{ __('items.all_groups') }}</option>
                                        @foreach ($groups as $id => $name)
                                            <option value="{{ $id }}">{{ $name }}</option>
                                        @endforeach
                                    </select>
                                    <span class="input-group-text">
                                        <i class="fas fa-layer-group" wire:loading.remove
                                            wire:target="selectedGroup"></i>
                                        <div class="spinner-border spinner-border-sm" role="status" wire:loading
                                            wire:target="selectedGroup">
                                            <span class="visually-hidden">{{ __('common.loading') }}</span>
                                        </div>
                                    </span>
                                </div>
                            </div>

                            {{-- Category Filter --}}
                            <div class="flex-grow-1">
                                <label
                                    class="form-label font-hold fw-bold font-12 mb-1">{{ __('items.category') }}:</label>
                                <div class="input-group">
                                    <select x-model="categoryValue" @change="updateCategory()"
                                        class="form-select font-hold fw-bold font-14" wire:loading.attr="disabled"
                                        wire:target="selectedCategory">
                                        <option value="">{{ __('items.all_categories') }}</option>
                                        @foreach ($categories as $id => $name)
                                            <option value="{{ $id }}">{{ $name }}</option>
                                        @endforeach
                                    </select>
                                    <span class="input-group-text">
                                        <i class="fas fa-tags" wire:loading.remove wire:target="selectedCategory"></i>
                                        <div class="spinner-border spinner-border-sm" role="status" wire:loading
                                            wire:target="selectedCategory">
                                            <span class="visually-hidden">{{ __('common.loading') }}</span>
                                        </div>
                                    </span>
                                </div>
                            </div>

                            {{-- Quantity Filter --}}
                            <div class="flex-grow-1">
                                <label class="form-label font-hold fw-bold font-12 mb-1">{{ __('items.quantity_filter') }}:</label>
                                <div class="input-group">
                                    <select wire:model.live="quantityFilterType" 
                                            class="form-select font-hold fw-bold font-14" 
                                            wire:loading.attr="disabled"
                                            wire:target="quantityFilterType">
                                        <option value="">{{ __('items.all_quantities') }}</option>
                                        <option value="greater">{{ __('items.greater_than') }}</option>
                                        <option value="less">{{ __('items.less_than') }}</option>
                                        <option value="equal">{{ __('items.equal_to') }}</option>
                                        <option value="greaterOrEqual">{{ __('items.greater_or_equal') }}</option>
                                        <option value="lessOrEqual">{{ __('items.less_or_equal') }}</option>
                                        <option value="notEqual">{{ __('items.not_equal') }}</option>
                                    </select>
                                    @if($quantityFilterType)
                                        <input type="number" 
                                               wire:model.live.debounce.500ms="quantityFilterValue" 
                                               class="form-control font-hold fw-bold font-14"
                                               placeholder="{{ __('items.enter_value') }}"
                                               step="0.01"
                                               style="max-width: 120px;"
                                               wire:loading.attr="disabled"
                                               wire:target="quantityFilterValue">
                                    @endif
                                    <span class="input-group-text">
                                        <i class="fas fa-sort-amount-down" wire:loading.remove wire:target="quantityFilterType,quantityFilterValue"></i>
                                        <div class="spinner-border spinner-border-sm" role="status" wire:loading
                                            wire:target="quantityFilterType,quantityFilterValue">
                                            <span class="visually-hidden">{{ __('common.loading') }}</span>
                                        </div>
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>




                <div class="card-body">
                    {{-- Pagination Control --}}
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <div class="d-flex align-items-center gap-2">
                            <label class="form-label font-hold fw-bold mb-0">{{ __('items.display') }}:</label>
                            <div class="input-group" style="width: auto;">
                                <select wire:model.live="perPage" class="form-select form-select-sm font-hold fw-bold"
                                    wire:loading.attr="disabled" wire:target="perPage">
                                    <option value="25">25</option>
                                    <option value="50">50</option>
                                    <option value="100">100</option>
                                    <option value="200">200</option>
                                </select>
                                <span class="input-group-text">
                                    <i class="fas fa-list" wire:loading.remove wire:target="perPage"></i>
                                    <div class="spinner-border spinner-border-sm" role="status" wire:loading
                                        wire:target="perPage">
                                        <span class="visually-hidden">{{ __('common.loading') }}</span>
                                    </div>
                                </span>
                            </div>
                            <span class="font-hold fw-bold">{{ __('items.record') }}</span>
                        </div>
                        <div class="font-hold fw-bold text-muted">
                            <i class="fas fa-list me-1"></i>
                            {{ __('items.total_results') }}: <span
                                class="text-primary">{{ $this->items->total() }}</span>
                        </div>
                    </div>

                    {{-- Active Filters Display --}}
                    @if ($search || $selectedWarehouse || $selectedGroup || $selectedCategory || $quantityFilterType)
                        <div class="alert alert-info mb-3" x-data="{ show: true }" x-show="show"
                            x-transition:enter="transition ease-out duration-300"
                            x-transition:enter-start="opacity-0 transform translate-y-2"
                            x-transition:enter-end="opacity-100 transform translate-y-0"
                            x-transition:leave="transition ease-in duration-200"
                            x-transition:leave-start="opacity-100 transform translate-y-0"
                            x-transition:leave-end="opacity-0 transform translate-y-2">
                            <div class="d-flex justify-content-between align-items-center">
                                <div class="font-hold fw-bold">
                                    <i class="fas fa-filter me-2"></i>
                                    {{ __('items.active_filters') }}:
                                    @if ($search)
                                        <span class="badge bg-primary me-1">{{ __('common.search') }}:
                                            {{ $search }}</span>
                                    @endif
                                    @if ($selectedWarehouse)
                                        @php $warehouse = $warehouses->firstWhere('id', $selectedWarehouse); @endphp
                                        <span class="badge bg-success me-1">{{ __('items.warehouse') }}:
                                            {{ $warehouse ? $warehouse->aname : __('common.not_specified') }}</span>
                                    @endif
                                    @if ($selectedGroup)
                                        <span class="badge bg-warning me-1">{{ __('items.group') }}:
                                            {{ $groups[$selectedGroup] ?? __('common.not_specified') }}</span>
                                    @endif
                                    @if ($selectedCategory)
                                        <span class="badge bg-info me-1">{{ __('items.category') }}:
                                            {{ $categories[$selectedCategory] ?? __('common.not_specified') }}</span>
                                    @endif
                                    @if ($quantityFilterType)
                                        @php
                                            $filterLabels = [
                                                'greater' => __('items.greater_than'),
                                                'less' => __('items.less_than'),
                                                'equal' => __('items.equal_to'),
                                                'greaterOrEqual' => __('items.greater_or_equal'),
                                                'lessOrEqual' => __('items.less_or_equal'),
                                                'notEqual' => __('items.not_equal')
                                            ];
                                        @endphp
                                        <span class="badge bg-danger me-1">{{ __('items.quantity_filter') }}:
                                            {{ $filterLabels[$quantityFilterType] ?? '' }}
                                            @if($quantityFilterValue !== null && $quantityFilterValue !== '')
                                                {{ $quantityFilterValue }}
                                            @endif
                                        </span>
                                    @endif
                                </div>
                                <button type="button" class="btn-close" @click="show = false"></button>
                            </div>
                        </div>
                    @endif

                    <div class="table-responsive" style="overflow-x: auto; max-height: 70vh; overflow-y: auto;">

                        <table class="table table-striped mb-0 table-hover"
                            style="direction: rtl; font-family: 'Cairo', sans-serif;">
                            <style>
                                /* Hover color for table rows - matching btn-main gradient */
                                .table-hover tbody tr:hover {
                                    background: linear-gradient(135deg, rgba(52, 211, 163, 0.15) 0%, rgba(26, 161, 196, 0.15) 100%) !important;
                                }

                                /* Fixed header styles */
                                .table-responsive {
                                    position: relative;
                                }

                                .table thead th {
                                    position: sticky;
                                    top: 0;
                                    background-color: #f8f9fa !important;
                                    z-index: 10;
                                    border-bottom: 2px solid #dee2e6;
                                }

                                /* Filter dropdown styles */
                                .position-relative .position-absolute {
                                    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
                                }
                                
                                .position-relative .position-absolute input:focus,
                                .position-relative .position-absolute select:focus {
                                    border-color: #34d3a3;
                                    box-shadow: 0 0 0 0.2rem rgba(52, 211, 163, 0.25);
                                }
                            </style>
                            <thead class="table-light text-center align-middle">
                                <tr>
                                    <th class="font-hold text-center fw-bold px-3">#</th>
                                    @if ($visibleColumns['code'])
                                        <th class="font-hold text-center fw-bold">{{ __('common.code') }}</th>
                                    @endif
                                    @if ($visibleColumns['name'])
                                        <th class="font-hold text-center fw-bold">{{ __('common.name') }}</th>
                                    @endif
                                    @if ($visibleColumns['units'])
                                        <th class="font-hold text-center fw-bold" style="min-width: 130px;">
                                            {{ __('items.units') }}</th>
                                    @endif
                                    @if ($visibleColumns['quantity'])
                                        <th class="font-hold text-center fw-bold" style="min-width: 100px;">
                                            {{ __('common.quantity') }}</th>
                                    @endif
                                    @if ($visibleColumns['average_cost'])
                                        <th class="font-hold text-center fw-bold">{{ __('items.average_cost') }}</th>
                                    @endif
                                    @if ($visibleColumns['quantity_average_cost'])
                                        <th class="font-hold text-center fw-bold">
                                            {{ __('items.quantity_average_cost') }}</th>
                                    @endif
                                    @if ($visibleColumns['last_cost'])
                                        <th class="font-hold text-center fw-bold">{{ __('items.last_cost') }}</th>
                                    @endif
                                    @if ($visibleColumns['quantity_cost'])
                                        <th class="font-hold text-center fw-bold">{{ __('items.quantity_cost') }}</th>
                                    @endif
                                    @foreach ($this->priceTypes as $priceId => $priceName)
                                        @if (isset($visiblePrices[$priceId]) && $visiblePrices[$priceId])
                                            <th class="font-hold text-center fw-bold">{{ translateDynamicValue($priceName) }}</th>
                                        @endif
                                    @endforeach
                                    @if ($visibleColumns['barcode'])
                                        <th class="font-hold text-center fw-bold">{{ __('items.item_barcode') }}</th>
                                    @endif
                                    @foreach ($this->noteTypes as $noteId => $noteName)
                                        @if (isset($visibleNotes[$noteId]) && $visibleNotes[$noteId])
                                            <th class="font-hold text-center fw-bold">{{ translateDynamicValue($noteName) }}</th>
                                        @endif
                                    @endforeach
                                    @canany(['edit items', 'delete items'])
                                        @if ($visibleColumns['actions'])
                                            <th class="font-hold fw-bold">{{ __('common.actions') }}</th>
                                        @endif
                                    @endcanany
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($this->items as $item)
                                    @php
                                        // Data already prepared in getItemsProperty()
                                        $itemData = $this->displayItemData[$item->id] ?? [];
                                        $selectedUnitId = $this->selectedUnit[$item->id] ?? null;
                                    @endphp
                                    @if (!empty($itemData))
                                        <tr wire:key="item-{{ $item->id }}-{{ $selectedUnitId ?? 'no-unit' }}-warehouse-{{ $selectedWarehouse ?? 'all' }}"
                                            x-data="itemRow({{ json_encode($itemData) }}, {{ $selectedUnitId }})"
                                            x-transition:enter="transition ease-out duration-200"
                                            x-transition:enter-start="opacity-0 transform scale-95"
                                            x-transition:enter-end="opacity-100 transform scale-100"
                                            x-transition:leave="transition ease-in duration-150"
                                            x-transition:leave-start="opacity-100 transform scale-100"
                                            x-transition:leave-end="opacity-0 transform scale-95">
                                            <td class="font-hold text-center fw-bold px-3">{{ $loop->iteration }}</td>

                                            @if ($visibleColumns['code'])
                                                <td class="font-hold text-center fw-bold" x-text="itemData.code"></td>
                                            @endif

                                            @if ($visibleColumns['name'])
                                                <td class="font-hold text-center fw-bold">
                                                    <span x-text="itemData.name"></span>
                                                    <a href="{{ route('item-movement', ['itemId' => $item->id]) }}">
                                                        <i class="las la-eye fa-lg text-primary"
                                                            title="{{ __('items.view_item_movements') }}"></i>
                                                    </a>
                                                </td>
                                            @endif

                                            @if ($visibleColumns['units'])
                                                <td class="font-hold text-center fw-bold">
                                                    <template x-if="Object.keys(itemData.units).length > 0">
                                                        <div>
                                                            <select class="form-select font-hold fw-bold font-14"
                                                                x-model="selectedUnitId" style="min-width: 105px;">
                                                                <template
                                                                    x-for="[unitId, unit] in Object.entries(itemData.units)"
                                                                    :key="unitId">
                                                                    <option :value="unitId"
                                                                        x-text="unit.name + ' [' + formatNumber(unit.u_val) + ']'">
                                                                    </option>
                                                                </template>
                                                            </select>
                                                        </div>
                                                    </template>
                                                    <template x-if="Object.keys(itemData.units).length === 0">
                                                        <span
                                                            class="font-hold fw-bold font-14">{{ __('items.no_units_found') }}</span>
                                                    </template>
                                                </td>
                                            @endif

                                            @if ($visibleColumns['quantity'])
                                                <td class="text-center fw-bold">
                                                    <span x-text="formattedQuantity.integer"></span>
                                                    <template
                                                        x-if="formattedQuantity.remainder > 0 && formattedQuantity.unitName !== formattedQuantity.smallerUnitName">
                                                        <span
                                                            x-text="'[' + formattedQuantity.remainder + ' ' + formattedQuantity.smallerUnitName + ']'"></span>
                                                    </template>
                                                </td>
                                            @endif

                                            @if ($visibleColumns['average_cost'])
                                                <td class="font-hold text-center fw-bold">
                                                    <span x-text="formatCurrency(unitAverageCost)"></span>
                                                </td>
                                            @endif

                                            @if ($visibleColumns['quantity_average_cost'])
                                                <td class="font-hold text-center fw-bold">
                                                    <span x-text="formatCurrency(quantityAverageCost)"></span>
                                                </td>
                                            @endif

                                            @if ($visibleColumns['last_cost'])
                                                <td class="text-center fw-bold">
                                                    <span x-text="formatCurrency(lastPurchasePrice)"></span>
                                                </td>
                                            @endif

                                            @if ($visibleColumns['quantity_cost'])
                                                <td class="text-center fw-bold">
                                                    <span x-text="formatCurrency(quantityCost)"></span>
                                                </td>
                                            @endif

                                            {{-- Prices --}}
                                            @foreach ($this->priceTypes as $priceTypeId => $priceTypeName)
                                                @if (isset($visiblePrices[$priceTypeId]) && $visiblePrices[$priceTypeId])
                                                    <td class="font-hold text-center fw-bold">
                                                        <span x-text="getPriceForType({{ $priceTypeId }})"></span>
                                                    </td>
                                                @endif
                                            @endforeach

                                            @if ($visibleColumns['barcode'])
                                                <td class="font-hold fw-bold text-center">
                                                    <template x-if="currentBarcodes.length > 0">
                                                        <span class="font-hold fw-bold font-14"
                                                            x-text="currentBarcodes.map(b => b.barcode).join(', ')"></span>
                                                    </template>
                                                    <template x-if="currentBarcodes.length === 0">
                                                        <span
                                                            class="font-hold fw-bold font-14">{{ __('common.no_data_found') }}</span>
                                                    </template>
                                                </td>
                                            @endif

                                            {{-- Notes --}}
                                            @foreach ($this->noteTypes as $noteTypeId => $noteTypeName)
                                                @if (isset($visibleNotes[$noteTypeId]) && $visibleNotes[$noteTypeId])
                                                    <td class="font-hold fw-bold text-center">
                                                        <span
                                                            x-text="itemData.notes[{{ $noteTypeId }}] || ''"></span>
                                                    </td>
                                                @endif
                                            @endforeach

                                            @canany(['edit items', 'delete items'])
                                                @if ($visibleColumns['actions'])
                                                    <td class="d-flex justify-content-center align-items-center gap-2 mt-2" onclick="event.stopPropagation()">
                                                        <button type="button"
                                                            title="{{ __('items.view_item_details') }}"
                                                            class="btn btn-primary btn-sm"
                                                            onclick="event.stopPropagation(); showItemDetailsModal({{ $item->id }})">
                                                            <i class="las la-eye fa-lg"></i>
                                                        </button>
                                                        <button type="button"
                                                            title="{{ __('items.item_images') }}"
                                                            class="btn btn-info btn-sm"
                                                            :disabled="!itemData.has_images"
                                                            :class="{ 'opacity-50': !itemData.has_images }"
                                                            onclick="event.stopPropagation(); massarLoadImages({{ $item->id }}, this)">
                                                            <i class="las la-images fa-lg"></i>
                                                        </button>
                                                        @can('edit items')
                                                            <button type="button"
                                                                title="{{ __('items.edit_item') }}"
                                                                class="btn btn-success btn-sm"
                                                                onclick="event.stopPropagation(); massarCallWire(this, 'edit', {{ $item->id }})">
                                                                <i class="las la-edit fa-lg"></i>
                                                            </button>
                                                        @endcan
                                                        @can('delete items')
                                                            <button type="button"
                                                                title="{{ __('items.delete_item') }}"
                                                                class="btn btn-danger btn-sm"
                                                                onclick="event.stopPropagation(); if(confirm('{{ __('items.confirm_delete_item') }}')) massarCallWire(this, 'delete', {{ $item->id }})">
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
                                        <td colspan="{{ $colspan }}" class="text-center font-hold fw-bold">
                                            {{ __('common.no_records_found') }}
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
                            <div class="card border-info">
                                <div class="card-header bg-info text-white">
                                    <h6 class="font-hold fw-bold mb-0 text-white">
                                        <i class="fas fa-calculator me-2"></i>
                                        {{ __('items.warehouse_valuation') }}
                                    </h6>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-2">
                                            <label
                                                class="form-label font-hold fw-bold">{{ __('items.select_price_type') }}:</label>
                                            <select wire:model.live="selectedPriceType"
                                                class="form-select font-hold fw-bold font-14">
                                                <option value="">{{ __('items.select_price_type') }}</option>
                                                <option value="cost">{{ __('items.cost') }}</option>
                                                <option value="average_cost">{{ __('items.average_cost') }}</option>
                                                @foreach ($this->priceTypes as $priceId => $priceName)
                                                    <option value="{{ $priceId }}">{{ translateDynamicValue($priceName) }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="col-md-2">
                                            <label
                                                class="form-label font-hold fw-bold">{{ __('items.selected_warehouse') }}:</label>
                                            <div class="form-control-plaintext font-hold fw-bold">
                                                @if ($selectedWarehouse)
                                                    @php
                                                        $warehouse = $warehouses->firstWhere('id', $selectedWarehouse);
                                                    @endphp
                                                    {{ $warehouse ? $warehouse->aname : __('common.not_specified') }}
                                                @else
                                                    {{ __('items.all_warehouses') }}
                                                @endif
                                            </div>
                                        </div>
                                        @if ($selectedPriceType)
                                            <div class="col-md-3">
                                                <h6 class="font-hold fw-bold text-primary mb-1"
                                                    style="font-size: 0.95rem;">{{ __('items.total_quantity') }}</h6>
                                                <h4 class="font-hold fw-bold text-success mb-0"
                                                    style="font-size: 1.2rem;">{{ $this->totalQuantity }}</h4>
                                            </div>
                                            <div class="col-md-3">
                                                <h6 class="font-hold fw-bold text-primary">
                                                    {{ __('items.total_value') }}</h6>
                                                <h4 class="font-hold fw-bold text-success">
                                                    {{ formatCurrency($this->totalAmount) }}</h4>
                                            </div>
                                            <div class="col-md-2">
                                                <h6 class="font-hold fw-bold text-primary">
                                                    {{ __('items.items_count') }}</h6>
                                                <h4 class="font-hold fw-bold text-success">
                                                    {{ $this->totalItems }}</h4>
                                            </div>
                                        @endif
                                    </div>


                                </div>
                            </div>
                        </div>
                    </div>



                    {{-- Pagination Links --}}
                    <div class="mt-4 d-flex justify-content-center">
                        <div class="font-hold">
                            {{ $this->items->links() }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Item Details Modal --}}
    <div class="modal fade" id="itemDetailsModal" tabindex="-1" aria-labelledby="itemDetailsModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header" style="background: linear-gradient(135deg, #34d3a3 0%, #2ab88a 100%);">
                    <h5 class="modal-title text-white font-hold fw-bold" id="itemDetailsModalLabel">
                        <i class="las la-box me-2"></i>
                        {{ __('items.item_details') }}
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body" id="itemDetailsModalBody">
                    <div class="text-center py-5">
                        <div class="spinner-border text-primary" role="status">
                            <span class="visually-hidden">{{ __('common.loading') }}</span>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary font-hold fw-bold" data-bs-dismiss="modal">
                        <i class="las la-times me-1"></i>
                        {{ __('common.close') }}
                    </button>
                </div>
            </div>
        </div>
    </div>

    {{-- Column Visibility Modal --}}
    <div class="modal fade" id="columnVisibilityModal" tabindex="-1" aria-labelledby="columnVisibilityModalLabel"
        aria-hidden="true" x-data="columnVisibilityModal()" x-init="columns = @js($this->visibleColumns);
        prices = @js($this->visiblePrices);
        notes = @js($this->visibleNotes);"
        @close-modal.window="$el.querySelector('.btn-close').click()">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header bg-info text-white">
                    <h5 class="modal-title font-hold fw-bold" id="columnVisibilityModalLabel">
                        <i class="fas fa-columns me-2"></i>
                        {{ __('items.column_display_options') }}
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                        aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    {{-- Global Controls --}}
                    <div class="row mb-4">
                        <div class="col-12">
                            <div class="d-flex gap-2 justify-content-center">
                                <button type="button" @click="showAllColumns()"
                                    class="btn btn-success btn-sm font-hold fw-bold">
                                    <i class="fas fa-eye me-1"></i>
                                    {{ __('items.show_all') }}
                                </button>
                                <button type="button" @click="hideAllColumns()"
                                    class="btn btn-secondary btn-sm font-hold fw-bold">
                                    <i class="fas fa-eye-slash me-1"></i>
                                    {{ __('items.hide_all') }}
                                </button>
                            </div>
                        </div>
                    </div>

                    {{-- Columns Section --}}
                    <div class="row">
                        <div class="col-md-6">
                            <h6 class="font-hold fw-bold text-primary mb-3">
                                <i class="fas fa-list me-2"></i>
                                {{ __('items.basic_columns') }}:
                            </h6>
                            <div class="form-check mb-2">
                                <input class="form-check-input" type="checkbox" x-model="columns.code">
                                <label class="form-check-label font-hold fw-bold">
                                    {{ __('common.code') }}
                                </label>
                            </div>
                            <div class="form-check mb-2">
                                <input class="form-check-input" type="checkbox" x-model="columns.name">
                                <label class="form-check-label font-hold fw-bold">
                                    {{ __('common.name') }}
                                </label>
                            </div>
                            <div class="form-check mb-2">
                                <input class="form-check-input" type="checkbox" x-model="columns.units">
                                <label class="form-check-label font-hold fw-bold">
                                    {{ __('items.units') }}
                                </label>
                            </div>
                            <div class="form-check mb-2">
                                <input class="form-check-input" type="checkbox" x-model="columns.quantity">
                                <label class="form-check-label font-hold fw-bold">
                                    {{ __('common.quantity') }}
                                </label>
                            </div>
                            <div class="form-check mb-2">
                                <input class="form-check-input" type="checkbox" x-model="columns.barcode">
                                <label class="form-check-label font-hold fw-bold">
                                    {{ __('items.item_barcode') }}
                                </label>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <h6 class="font-hold fw-bold text-primary mb-3">
                                <i class="fas fa-dollar-sign me-2"></i>
                                {{ __('items.cost_and_price_columns') }}:
                            </h6>
                            <div class="form-check mb-2">
                                <input class="form-check-input" type="checkbox" x-model="columns.average_cost">
                                <label class="form-check-label font-hold fw-bold">
                                    {{ __('items.average_cost') }}
                                </label>
                            </div>
                            <div class="form-check mb-2">
                                <input class="form-check-input" type="checkbox"
                                    x-model="columns.quantity_average_cost">
                                <label class="form-check-label font-hold fw-bold">
                                    {{ __('items.quantity_average_cost') }}
                                </label>
                            </div>
                            <div class="form-check mb-2">
                                <input class="form-check-input" type="checkbox" x-model="columns.last_cost">
                                <label class="form-check-label font-hold fw-bold">
                                    {{ __('items.last_cost') }}
                                </label>
                            </div>
                            <div class="form-check mb-2">
                                <input class="form-check-input" type="checkbox" x-model="columns.quantity_cost">
                                <label class="form-check-label font-hold fw-bold">
                                    {{ __('items.quantity_cost') }}
                                </label>
                            </div>
                        </div>
                    </div>

                    {{-- Prices Section --}}
                    @if (count($this->priceTypes) > 0)
                        <hr class="my-4">
                        <div class="row">
                            <div class="col-12 mb-3">
                                <h6 class="font-hold fw-bold text-info mb-3">
                                    <i class="fas fa-tags me-2"></i>
                                    {{ __('items.sale_prices') }}:
                                </h6>
                                <div class="d-flex gap-2 mb-3">
                                    <button type="button" @click="showAllPrices()"
                                        class="btn btn-info btn-sm font-hold fw-bold">
                                        <i class="fas fa-eye me-1"></i>
                                        {{ __('items.show_all_prices') }}
                                    </button>
                                    <button type="button" @click="hideAllPrices()"
                                        class="btn btn-secondary btn-sm font-hold fw-bold">
                                        <i class="fas fa-eye-slash me-1"></i>
                                        {{ __('items.hide_all_prices') }}
                                    </button>
                                </div>
                            </div>

                            <div class="col-md-6">
                                @foreach ($this->priceTypes as $priceId => $priceName)
                                    <div class="form-check mb-2">
                                        <input class="form-check-input" type="checkbox"
                                            x-model="prices['{{ $priceId }}']">
                                        <label class="form-check-label font-hold fw-bold">
                                            {{ translateDynamicValue($priceName) }}
                                        </label>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    {{-- Actions Section --}}
                    @canany(['edit Categories', 'delete Categories'])
                        <hr class="my-4">
                        <div class="row">
                            <div class="col-12">
                                <h6 class="font-hold fw-bold text-warning mb-3">
                                    <i class="fas fa-cogs me-2"></i>
                                    {{ __('common.actions') }}:
                                </h6>
                                <div class="form-check mb-2">
                                    <input class="form-check-input" type="checkbox" x-model="columns.actions">
                                    <label class="form-check-label font-hold fw-bold">
                                        {{ __('items.actions_edit_delete') }}
                                    </label>
                                </div>
                            </div>
                        </div>
                    @endcanany

                    {{-- Notes Section --}}
                    @if (count($this->noteTypes) > 0)
                        <hr class="my-4">
                        <div class="row">
                            <div class="col-12 mb-3">
                                <h6 class="font-hold fw-bold text-success mb-3">
                                    <i class="fas fa-sticky-note me-2"></i>
                                    {{ __('items.notes') }}:
                                </h6>
                                <div class="d-flex gap-2 mb-3">
                                    <button type="button" @click="showAllNotes()"
                                        class="btn btn-success btn-sm font-hold fw-bold">
                                        <i class="fas fa-eye me-1"></i>
                                        {{ __('items.show_all_notes') }}
                                    </button>
                                    <button type="button" @click="hideAllNotes()"
                                        class="btn btn-secondary btn-sm font-hold fw-bold">
                                        <i class="fas fa-eye-slash me-1"></i>
                                        {{ __('items.hide_all_notes') }}
                                    </button>
                                </div>
                            </div>

                            <div class="col-md-6">
                                @foreach ($this->noteTypes as $noteId => $noteName)
                                    <div class="form-check mb-2">
                                        <input class="form-check-input" type="checkbox"
                                            x-model="notes['{{ $noteId }}']">
                                        <label class="form-check-label font-hold fw-bold">
                                            {{ translateDynamicValue($noteName) }}
                                        </label>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-main font-hold fw-bold" @click="applyChanges()"
                        wire:loading.attr="disabled" wire:target="updateVisibility">
                        <span wire:loading.remove wire:target="updateVisibility">
                            <i class="fas fa-check me-2"></i>
                            {{ __('common.apply') }}
                        </span>
                        <span wire:loading wire:target="updateVisibility">
                            <div class="spinner-border spinner-border-sm me-2" role="status">
                                <span class="visually-hidden">{{ __('common.processing') }}</span>
                            </div>
                            {{ __('common.processing') }}
                        </span>
                    </button>
                    <button type="button" class="btn btn-secondary font-hold fw-bold" data-bs-dismiss="modal">
                        {{ __('common.close') }}
                    </button>
                </div>
            </div>
        </div>
    </div>

    {{-- Images Modal --}}
    <div class="modal fade" id="itemImagesModal" tabindex="-1" aria-labelledby="itemImagesModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title font-hold fw-bold" id="itemImagesModalLabel">
                        <i class="las la-images me-2"></i>
                        {{ __('items.item_images') }}
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="{{ __('common.close') }}"></button>
                </div>
                <div class="modal-body" id="itemImagesModalBody">
                    {{-- Content injected by JS --}}
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary font-hold fw-bold" data-bs-dismiss="modal">
                        {{ __('common.close') }}
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    (function () {
        var _massarImagesActiveIndex = 0;
        var _massarImagesData = [];

        window.massarCallWire = function (el, method, id) {
            var wireEl = el.closest('[wire\\:id]');
            if (wireEl && window.Livewire) {
                window.Livewire.find(wireEl.getAttribute('wire:id')).call(method, id);
            }
        };

        window.massarLoadImages = function (itemId, el) {
            massarCallWire(el, 'loadItemImages', itemId);
        };

        function massarRenderImages() {
            var body = document.getElementById('itemImagesModalBody');
            if (!body) return;

            if (_massarImagesData.length === 0) {
                body.innerHTML = '<div class="text-center py-5"><i class="las la-image fa-3x text-muted mb-3 d-block"></i><p class="text-muted font-hold fw-bold">{{ __('items.no_images_found') }}</p></div>';
                return;
            }

            var img = _massarImagesData[_massarImagesActiveIndex];
            var thumbsHtml = '';
            if (_massarImagesData.length > 1) {
                _massarImagesData.forEach(function (t, i) {
                    var border = i === _massarImagesActiveIndex ? 'border border-primary border-3' : 'border';
                    thumbsHtml += '<img src="' + t.thumb + '" class="rounded ' + border + '" style="width:70px;height:70px;object-fit:cover;cursor:pointer;" onclick="massarSetImage(' + i + ')">';
                });
                thumbsHtml = '<div class="d-flex flex-wrap justify-content-center gap-2 mt-3">' + thumbsHtml + '</div>';
            }

            body.innerHTML =
                '<div class="text-center">' +
                    '<img src="' + img.url + '" class="img-fluid rounded shadow-sm" style="max-height:400px;object-fit:contain;">' +
                '</div>' +
                thumbsHtml +
                '<p class="text-center text-muted mt-2 mb-0 font-hold">' + (_massarImagesActiveIndex + 1) + ' / ' + _massarImagesData.length + '</p>';
        }

        window.massarSetImage = function (index) {
            _massarImagesActiveIndex = index;
            massarRenderImages();
        };

        document.addEventListener('livewire:initialized', function () {
            Livewire.on('show-item-images', function (event) {
                var data = Array.isArray(event) ? event[0] : event;
                _massarImagesData = (data && data.images) ? data.images : [];
                _massarImagesActiveIndex = 0;
                massarRenderImages();
                bootstrap.Modal.getOrCreateInstance(document.getElementById('itemImagesModal')).show();
            });
        });
    })();
</script>

<script>
    // Translations resolved at Blade render time - runs only once per page
    window._itemDetailsTrans = {
        loading: @json(__('common.loading')),
        main_image: @json(__('items.main_image')),
        additional_images: @json(__('items.additional_images')),
        item_images: @json(__('items.item_images')),
        no_images_found: @json(__('items.no_images_found')),
        add_images_from_edit_page: @json(__('items.add_images_from_edit_page')),
        units: @json(__('items.units')),
        unit_name: @json(__('items.unit_name')),
        conversion_value: @json(__('items.conversion_value')),
        cost: @json(__('items.cost')),
        item_code: @json(__('items.item_code')),
        item_name: @json(__('items.item_name')),
        item_type: @json(__('items.item_type')),
        status: @json(__('items.status')),
        item_description: @json(__('items.item_description')),
        active: @json(__('common.active')),
        inactive: @json(__('common.inactive')),
        error_loading_data: @json(__('common.error_loading_data')),
    };

    function showItemDetailsModal(itemId) {
        const modalBody = document.getElementById('itemDetailsModalBody');
        const t = _itemDetailsTrans;

        modalBody.innerHTML = `
            <div class="text-center py-5">
                <div class="spinner-border text-primary" role="status">
                    <span class="visually-hidden">${t.loading}</span>
                </div>
            </div>
        `;

        const modal = new bootstrap.Modal(document.getElementById('itemDetailsModal'));
        modal.show();

        fetch(`/items/${itemId}/json`)
            .then(response => response.json())
            .then(item => {
                const hasThumbnail = item.thumbnail && item.thumbnail.url;
                const hasImages = item.images && item.images.length > 0;

                let imagesHtml = '';
                if (hasThumbnail || hasImages) {
                    const thumbnailHtml = hasThumbnail ? `
                        <div class="mb-3">
                            <p class="font-hold fw-bold text-muted mb-1">${t.main_image}</p>
                            <img src="${item.thumbnail.url}" class="rounded shadow-sm"
                                 style="width: 120px; height: 120px; object-fit: cover; cursor: pointer;"
                                 onclick="window.open('${item.thumbnail.url}', '_blank')">
                        </div>
                    ` : '';

                    const additionalHtml = hasImages ? `
                        <div>
                            <p class="font-hold fw-bold text-muted mb-1">${t.additional_images}</p>
                            <div class="d-flex flex-wrap gap-2">
                                ${item.images.map(img => `
                                    <img src="${img.url}" class="rounded shadow-sm"
                                         style="width: 100px; height: 100px; object-fit: cover; cursor: pointer;"
                                         onclick="window.open('${img.url}', '_blank')">
                                `).join('')}
                            </div>
                        </div>
                    ` : '';

                    imagesHtml = `
                        <div class="row mb-4">
                            <div class="col-12">
                                <h6 class="font-hold fw-bold text-primary mb-3">
                                    <i class="las la-images me-2"></i>
                                    ${t.item_images}
                                </h6>
                                ${thumbnailHtml}
                                ${additionalHtml}
                            </div>
                        </div>
                    `;
                } else {
                    imagesHtml = `
                        <div class="alert alert-info mb-4 text-center">
                            <i class="las la-info-circle fa-2x mb-2 d-block"></i>
                            <p class="mb-0 font-hold fw-bold">${t.no_images_found}</p>
                            <small class="text-muted">${t.add_images_from_edit_page}</small>
                        </div>
                    `;
                }

                let unitsHtml = '';
                if (item.units && item.units.length > 0) {
                    unitsHtml = `
                        <div class="row mb-3">
                            <div class="col-12">
                                <h6 class="font-hold fw-bold text-primary mb-3">
                                    <i class="las la-balance-scale me-2"></i>
                                    ${t.units}
                                </h6>
                                <div class="table-responsive">
                                    <table class="table table-sm table-bordered">
                                        <thead class="table-light">
                                            <tr>
                                                <th class="font-hold fw-bold">${t.unit_name}</th>
                                                <th class="font-hold fw-bold">${t.conversion_value}</th>
                                                <th class="font-hold fw-bold">${t.cost}</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            ${item.units.map(unit => `
                                                <tr>
                                                    <td class="font-hold">${unit.name || '-'}</td>
                                                    <td class="font-hold">${unit.pivot?.u_val || '-'}</td>
                                                    <td class="font-hold">${unit.pivot?.cost ? parseFloat(unit.pivot.cost).toFixed(2) : '-'}</td>
                                                </tr>
                                            `).join('')}
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    `;
                }

                modalBody.innerHTML = `
                    ${imagesHtml}

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label font-hold fw-bold text-muted">${t.item_code}</label>
                            <div class="form-control-plaintext font-hold fw-bold">${item.code || '-'}</div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label font-hold fw-bold text-muted">${t.item_name}</label>
                            <div class="form-control-plaintext font-hold fw-bold">${item.name || '-'}</div>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label font-hold fw-bold text-muted">${t.item_type}</label>
                            <div class="form-control-plaintext font-hold">${item.type || '-'}</div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label font-hold fw-bold text-muted">${t.status}</label>
                            <div class="form-control-plaintext font-hold">
                                ${item.is_active
                                    ? '<span class="badge bg-success">' + t.active + '</span>'
                                    : '<span class="badge bg-danger">' + t.inactive + '</span>'}
                            </div>
                        </div>
                    </div>

                    ${item.info ? `
                        <div class="row mb-3">
                            <div class="col-12">
                                <label class="form-label font-hold fw-bold text-muted">${t.item_description}</label>
                                <div class="form-control-plaintext font-hold">${item.info}</div>
                            </div>
                        </div>
                    ` : ''}

                    ${unitsHtml}
                `;
            })
            .catch(error => {
                console.error('Error loading item details:', error);
                modalBody.innerHTML = `
                    <div class="alert alert-danger">
                        <i class="las la-exclamation-triangle me-2"></i>
                        ${_itemDetailsTrans.error_loading_data}
                    </div>
                `;
            });
    }
</script>

<script>
    // Alpine.js component for item row calculations
    function itemRow(itemData, initialUnitId) {
        return {
            itemData: itemData,
            selectedUnitId: initialUnitId,

            get selectedUnit() {
                return this.itemData.units[this.selectedUnitId] || null;
            },

            get selectedUVal() {
                return this.selectedUnit?.u_val || 1;
            },

            get currentUnitQuantity() {
                return this.selectedUVal > 0 ? this.itemData.base_quantity / this.selectedUVal : 0;
            },

            get formattedQuantity() {
                const integer = this.selectedUVal > 0 ? Math.floor(this.itemData.base_quantity / this
                    .selectedUVal) : 0;
                const remainder = this.selectedUVal > 0 ? this.itemData.base_quantity % this.selectedUVal : 0;

                // Find smaller unit
                const units = Object.values(this.itemData.units);
                const smallerUnit = units.length > 0 ? units.reduce((min, unit) =>
                    unit.u_val < min.u_val ? unit : min
                ) : null;

                return {
                    integer: integer,
                    remainder: remainder,
                    unitName: this.selectedUnit?.name || '',
                    smallerUnitName: smallerUnit?.name || ''
                };
            },

            get unitCostPrice() {
                return this.selectedUnit?.cost || 0;
            },

            get lastPurchasePrice() {
                return this.itemData.last_purchase_price * this.selectedUVal;
            },

            get quantityCost() {
                return this.currentUnitQuantity * this.lastPurchasePrice;
            },

            get unitAverageCost() {
                return this.itemData.average_cost * this.selectedUVal;
            },

            get quantityAverageCost() {
                return this.currentUnitQuantity * this.unitAverageCost;
            },

            get currentBarcodes() {
                return this.itemData.barcodes[this.selectedUnitId] || [];
            },

            getPriceForType(priceTypeId) {
                const unitPrices = this.itemData.prices[this.selectedUnitId] || {};
                const price = unitPrices[priceTypeId];
                return price ? this.formatCurrency(price.price) : '{{ __('common.not_available') }}';
            },

            formatCurrency(value) {
                if (value === null || value === undefined) return '0.00';
                return new Intl.NumberFormat('ar-SA', {
                    minimumFractionDigits: 2,
                    maximumFractionDigits: 2
                }).format(value);
            },

            formatNumber(value) {
                if (value === null || value === undefined) return '0';
                // Remove trailing zeros and decimal point if not needed
                return parseFloat(value).toString();
            }
        }
    }

    // Alpine.js component for filters with debouncing
    function filtersComponent() {
        return {
            searchValue: '',
            warehouseValue: '',
            groupValue: '',
            categoryValue: '',
            searchTimeout: null,

            updateSearch() {
                clearTimeout(this.searchTimeout);
                this.searchTimeout = setTimeout(() => {
                    this.$wire.set('search', this.searchValue);
                }, 500);
            },

            updateWarehouse() {
                this.$wire.set('selectedWarehouse', this.warehouseValue).then(() => {
                    // Force Livewire to refresh after warehouse change
                    this.$wire.$refresh();
                });
            },

            updateGroup() {
                this.$wire.set('selectedGroup', this.groupValue);
            },

            updateCategory() {
                this.$wire.set('selectedCategory', this.categoryValue);
            },

            clearFilters() {
                this.searchValue = '';
                this.warehouseValue = '';
                this.groupValue = '';
                this.categoryValue = '';
                this.$wire.call('clearFilters');
            }
        }
    }

    // Alpine.js component for column visibility modal
    function columnVisibilityModal() {
        return {
            columns: {},
            prices: {},
            notes: {},

            showAllColumns() {
                console.log('showAllColumns called', {
                    columns: this.columns,
                    prices: this.prices,
                    notes: this.notes
                });
                Object.keys(this.columns).forEach(key => this.columns[key] = true);
                Object.keys(this.prices).forEach(key => this.prices[key] = true);
                Object.keys(this.notes).forEach(key => this.notes[key] = true);
            },

            hideAllColumns() {
                console.log('hideAllColumns called', {
                    columns: this.columns,
                    prices: this.prices,
                    notes: this.notes
                });
                Object.keys(this.columns).forEach(key => this.columns[key] = false);
                Object.keys(this.prices).forEach(key => this.prices[key] = false);
                Object.keys(this.notes).forEach(key => this.notes[key] = false);
            },

            showAllPrices() {
                console.log('showAllPrices called', {
                    prices: this.prices
                });
                Object.keys(this.prices).forEach(key => this.prices[key] = true);
            },

            hideAllPrices() {
                console.log('hideAllPrices called', {
                    prices: this.prices
                });
                Object.keys(this.prices).forEach(key => this.prices[key] = false);
            },

            showAllNotes() {
                console.log('showAllNotes called', {
                    notes: this.notes
                });
                Object.keys(this.notes).forEach(key => this.notes[key] = true);
            },

            hideAllNotes() {
                console.log('hideAllNotes called', {
                    notes: this.notes
                });
                Object.keys(this.notes).forEach(key => this.notes[key] = false);
            },

            applyChanges() {
                console.log('applyChanges called', {
                    columns: this.columns,
                    prices: this.prices,
                    notes: this.notes
                });
                this.$wire.call('updateVisibility', this.columns, this.prices, this.notes);
            }
        }
    }
</script>
