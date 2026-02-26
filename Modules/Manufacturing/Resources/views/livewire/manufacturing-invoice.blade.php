<div class="min-h-screen bg-gray-50" x-data="manufacturingCalculator()" x-init="initFromLivewire()">
    @if ($currentStep === 1)
        <div>
            <!-- Header -->
            <header class="bg-white border-b sticky top-0 z-40 px-4 py-3 shadow-sm">
                <div class="w-full flex justify-between items-center flex-wrap gap-4">
                    <div>
                        <h1 class="text-2xl font-bold text-gray-900">{{ __('Manufacturing Invoice') }}</h1>
                        <div class="flex items-center gap-3 mt-1.5 text-sm text-gray-500 flex-wrap">
                            <span
                                class="bg-emerald-50 text-emerald-700 px-3 py-1 rounded-md font-bold border border-emerald-100">
                                {{ __('Invoice Number') }}: #{{ $pro_id }}
                            </span>
                            <span class="flex items-center gap-1.5">
                                <svg class="h-4 w-4 text-gray-400" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path
                                        d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"
                                        stroke-linecap="round" stroke-linejoin="round" stroke-width="2"></path>
                                </svg>
                                {{ $invoiceDate }}
                            </span>
                        </div>
                    </div>

                    <div class="flex items-center gap-3 flex-wrap">
                        <div class="min-w-[200px]">
                            <x-branches::branch-select :branches="$branches" model="branch_id" />
                        </div>

                        <button wire:click="openSaveTemplateModal"
                            class="h-11 px-4 bg-white border border-gray-200 rounded-lg text-gray-700 hover:bg-gray-50 transition-all flex items-center gap-2"
                            title="{{ __('Save Template') }}">
                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path
                                    d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4"
                                    stroke-linecap="round" stroke-linejoin="round" stroke-width="2"></path>
                            </svg>
                            <span class="hidden md:inline">{{ __('Save Template') }}</span>
                        </button>

                        <button type="button" wire:click="openLoadTemplateModal"
                            class="h-11 px-4 bg-white border border-gray-200 rounded-lg text-gray-700 hover:bg-gray-50 transition-all flex items-center gap-2"
                            wire:loading.attr="disabled" title="{{ __('Load Template') }}">
                            <span wire:loading.remove wire:target="openLoadTemplateModal">
                                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path
                                        d="M5 19a2 2 0 01-2-2V7a2 2 0 012-2h4l2 2h4a2 2 0 012 2v1M5 19h14a2 2 0 002-2v-5a2 2 0 00-2-2H9a2 2 0 00-2 2v5a2 2 0 01-2 2z"
                                        stroke-linecap="round" stroke-linejoin="round" stroke-width="2"></path>
                                </svg>
                            </span>
                            <span wire:loading wire:target="openLoadTemplateModal">
                                <svg class="animate-spin h-4 w-4" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10"
                                        stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor"
                                        d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                                    </path>
                                </svg>
                            </span>
                            <span class="hidden md:inline">{{ __('Load Template') }}</span>
                        </button>

                        <button wire:click="distributeCostsByPercentage"
                            class="h-11 px-4 bg-primary text-white rounded-lg hover:bg-accent transition-all flex items-center gap-2 disabled:opacity-50 disabled:cursor-not-allowed"
                            @if (count($selectedProducts) === 0) disabled @endif
                            title="{{ __('Distribute Costs by Percentage') }}">
                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path
                                    d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z"
                                    stroke-linecap="round" stroke-linejoin="round" stroke-width="2"></path>
                            </svg>
                            <span class="hidden md:inline">{{ __('Distribute Costs by Percentage') }}</span>
                        </button>

                        <button x-on:click="if (!$wire.isSaving) { syncForSave(); $wire.saveInvoice(); }"
                            class="h-11 px-6 bg-primary text-white rounded-lg font-bold hover:bg-accent transition-all shadow-md shadow-primary/20 flex items-center gap-2"
                            x-bind:disabled="$wire.isSaving" wire:loading.attr="disabled" wire:target="saveInvoice"
                            title="{{ __('Save Invoice') }}">
                            <span wire:loading.remove wire:target="saveInvoice">
                                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path
                                        d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4"
                                        stroke-linecap="round" stroke-linejoin="round" stroke-width="2"></path>
                                </svg>
                            </span>
                            <span wire:loading wire:target="saveInvoice">
                                <svg class="animate-spin h-5 w-5" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10"
                                        stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor"
                                        d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                                    </path>
                                </svg>
                            </span>
                            {{ __('Save Invoice') }}
                        </button>
                    </div>
                </div>


            </header>

            <!-- Modals -->
            @if ($showSaveTemplateModal)
                <div class="fixed inset-0 z-50 overflow-y-auto" style="background-color: rgba(0,0,0,0.5);">
                    <div class="flex items-center justify-center min-h-screen p-4">
                        <div class="bg-white rounded-xl shadow-2xl max-w-md w-full">
                            <div class="bg-primary text-white px-6 py-4 rounded-t-xl flex items-center justify-between">
                                <h5 class="text-lg font-bold flex items-center gap-2">
                                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path
                                            d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4"
                                            stroke-linecap="round" stroke-linejoin="round" stroke-width="2"></path>
                                    </svg>
                                    {{ __('Save as Manufacturing Template') }}
                                </h5>
                                <button wire:click="closeSaveTemplateModal"
                                    class="text-white hover:text-gray-200 transition-colors">
                                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path d="M6 18L18 6M6 6l12 12" stroke-linecap="round" stroke-linejoin="round"
                                            stroke-width="2"></path>
                                    </svg>
                                </button>
                            </div>
                            <div class="p-6 space-y-4">
                                <div>
                                    <label
                                        class="block text-sm font-medium text-gray-700 mb-2">{{ __('Template Name') }}</label>
                                    <input type="text" wire:model="templateName"
                                        class="w-full px-4 py-2 border border-gray-200 rounded-lg focus:ring-primary focus:border-primary"
                                        placeholder="{{ __('Enter template name') }}">
                                </div>
                                <div>
                                    <label
                                        class="block text-sm font-medium text-gray-700 mb-2">{{ __('Expected Production Time') }}</label>
                                    <input type="text" wire:model="templateExpectedTime"
                                        class="w-full px-4 py-2 border border-gray-200 rounded-lg focus:ring-primary focus:border-primary"
                                        id="timepicker" placeholder="{{ __('HH:MM') }}">
                                </div>
                            </div>

                            <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
                            <script>
                                flatpickr("#timepicker", {
                                    enableTime: true,
                                    noCalendar: true,
                                    dateFormat: "H:i",
                                    time_24hr: true
                                });
                            </script>

                            <div class="px-6 py-4 bg-gray-50 rounded-b-xl flex items-center justify-end gap-3">
                                <button wire:click="closeSaveTemplateModal"
                                    class="px-4 py-2 border border-gray-200 rounded-lg text-gray-700 hover:bg-gray-100 transition-all">
                                    {{ __('Cancel') }}
                                </button>
                                <button wire:click="saveAsTemplate"
                                    class="px-6 py-2 bg-primary text-white rounded-lg hover:bg-accent transition-all font-medium">
                                    {{ __('Save') }}
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            @endif

            @if ($showLoadTemplateModal)
                <div class="fixed inset-0 z-50 overflow-y-auto" style="background-color: rgba(0,0,0,0.5);">
                    <div class="flex items-center justify-center min-h-screen p-4">
                        <div class="bg-white rounded-xl shadow-2xl max-w-2xl w-full">
                            <div
                                class="bg-primary text-white px-6 py-4 rounded-t-xl flex items-center justify-between">
                                <h5 class="text-lg font-bold flex items-center gap-2">
                                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path
                                            d="M5 19a2 2 0 01-2-2V7a2 2 0 012-2h4l2 2h4a2 2 0 012 2v1M5 19h14a2 2 0 002-2v-5a2 2 0 00-2-2H9a2 2 0 00-2 2v5a2 2 0 01-2 2z"
                                            stroke-linecap="round" stroke-linejoin="round" stroke-width="2"></path>
                                    </svg>
                                    {{ __('Select Manufacturing Template') }}
                                </h5>
                                <button wire:click="closeLoadTemplateModal"
                                    class="text-white hover:text-gray-200 transition-colors">
                                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path d="M6 18L18 6M6 6l12 12" stroke-linecap="round" stroke-linejoin="round"
                                            stroke-width="2"></path>
                                    </svg>
                                </button>
                            </div>
                            <div class="p-6">
                                @if (count($templates) > 0)
                                    <div class="space-y-4">
                                        <div>
                                            <label
                                                class="block text-sm font-medium text-gray-700 mb-2">{{ __('Choose Template') }}:</label>
                                            <select wire:model.live="selectedTemplate"
                                                class="w-full px-4 py-3 border border-gray-200 rounded-lg focus:ring-primary focus:border-primary text-base">
                                                <option value="">{{ __('-- Select Template --') }}</option>
                                                @foreach ($templates as $template)
                                                    <option value="{{ $template['id'] }}">
                                                        {{ $template['display_name'] }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>

                                        @if ($selectedTemplate)
                                            <div
                                                class="bg-blue-50 border border-blue-100 p-4 rounded-lg flex items-start gap-3">
                                                <div class="bg-blue-100 p-2 rounded-full">
                                                    <svg class="h-5 w-5 text-blue-600" fill="currentColor"
                                                        viewBox="0 0 20 20">
                                                        <path clip-rule="evenodd"
                                                            d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z"
                                                            fill-rule="evenodd"></path>
                                                    </svg>
                                                </div>
                                                <div class="flex-1">
                                                    <p class="text-sm text-blue-800 font-medium">{{ __('Note') }}:
                                                    </p>
                                                    <p class="text-sm text-blue-700 mt-1">
                                                        {{ __('All products and materials saved in this template will be loaded') }}.
                                                    </p>
                                                </div>
                                            </div>

                                            <div>
                                                <label
                                                    class="block text-sm font-medium text-gray-700 mb-2 flex items-center gap-2">
                                                    <svg class="h-4 w-4 text-primary" fill="none"
                                                        stroke="currentColor" viewBox="0 0 24 24">
                                                        <path d="M6 18L18 6M6 6l12 12" stroke-linecap="round"
                                                            stroke-linejoin="round" stroke-width="2"></path>
                                                    </svg>
                                                    {{ __('Quantity Multiplier') }}:
                                                </label>
                                                <input type="number" wire:model="quantityMultiplier"
                                                    class="w-full px-4 py-3 border border-gray-200 rounded-lg focus:ring-primary focus:border-primary"
                                                    min="0.01" step="0.01" placeholder="1">
                                                <p class="text-xs text-gray-500 mt-2 flex items-center gap-1.5">
                                                    <svg class="h-3.5 w-3.5" fill="currentColor" viewBox="0 0 20 20">
                                                        <path clip-rule="evenodd"
                                                            d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z"
                                                            fill-rule="evenodd"></path>
                                                    </svg>
                                                    {{ __('Enter multiplier to scale all quantities (e.g., 2 will double all quantities)') }}
                                                </p>
                                            </div>

                                            <div class="bg-white border border-gray-200 rounded-lg p-4">
                                                <h6 class="text-sm font-bold text-gray-800 mb-3">
                                                    {{ __('Template Preview') }}</h6>
                                                @php
                                                    $currentTemplate = collect($templates)->firstWhere(
                                                        'id',
                                                        $selectedTemplate,
                                                    );
                                                @endphp
                                                @if ($currentTemplate)
                                                    <div class="space-y-2 text-sm">
                                                        <div class="flex items-center justify-between">
                                                            <span class="text-gray-600">{{ __('Date') }}:</span>
                                                            <span
                                                                class="font-medium text-gray-900">{{ $currentTemplate['pro_date'] }}</span>
                                                        </div>
                                                        <div class="flex items-center justify-between">
                                                            <span class="text-gray-600">{{ __('Value') }}:</span>
                                                            <span
                                                                class="font-bold text-primary">{{ number_format($currentTemplate['pro_value'], 2) }}
                                                                {{ __('EGP') }}</span>
                                                        </div>
                                                    </div>
                                                @endif
                                            </div>
                                        @endif
                                    </div>
                                @else
                                    <div class="text-center py-12">
                                        <div class="bg-gray-100 p-4 rounded-full inline-block mb-4">
                                            <svg class="h-12 w-12 text-gray-400" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path
                                                    d="M5 19a2 2 0 01-2-2V7a2 2 0 012-2h4l2 2h4a2 2 0 012 2v1M5 19h14a2 2 0 002-2v-5a2 2 0 00-2-2H9a2 2 0 00-2 2v5a2 2 0 01-2 2z"
                                                    stroke-linecap="round" stroke-linejoin="round" stroke-width="2">
                                                </path>
                                            </svg>
                                        </div>
                                        <h5 class="text-lg font-medium text-gray-700 mb-2">
                                            {{ __('No Saved Templates') }}</h5>
                                        <p class="text-sm text-gray-500">
                                            {{ __('Save a template first to load it later') }}</p>
                                    </div>
                                @endif
                            </div>
                            <div class="px-6 py-4 bg-gray-50 rounded-b-xl flex items-center justify-end gap-3">
                                <button wire:click="closeLoadTemplateModal"
                                    class="px-4 py-2 border border-gray-200 rounded-lg text-gray-700 hover:bg-gray-100 transition-all flex items-center gap-2">
                                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path d="M6 18L18 6M6 6l12 12" stroke-linecap="round" stroke-linejoin="round"
                                            stroke-width="2"></path>
                                    </svg>
                                    {{ __('Cancel') }}
                                </button>
                                @if (count($templates) > 0)
                                    <button wire:click="loadTemplate"
                                        class="px-6 py-2 bg-primary text-white rounded-lg hover:bg-accent transition-all font-medium flex items-center gap-2">
                                        <svg class="h-4 w-4" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"
                                                stroke-linecap="round" stroke-linejoin="round" stroke-width="2">
                                            </path>
                                        </svg>
                                        {{ __('Load Template') }}
                                    </button>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            @endif

            <!-- Main Content -->
            <main class="w-full grid grid-cols-12 gap-6">
                <!-- Left: Inputs & Tables (8 columns) -->
                <div class="col-span-12 lg:col-span-8 space-y-6 px-4">
                    <!-- قسم المنتجات المصنعة -->
                    <section class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                        <div class="flex items-center justify-between mb-4">
                            <h3 class="text-lg font-bold text-gray-800">{{ __('Manufactured Products') }}</h3>
                            <span class="text-xs text-gray-400">{{ __('Add products used in manufacturing') }}</span>
                        </div>

                        <!-- حقل البحث للمنتجات المصنعة -->
                        <div class="flex gap-3 mb-6" x-data="productSearch()">
                            <div class="relative flex-grow">
                                <input type="text" x-model="searchTerm" id="product_search"
                                    class="w-full h-11 text-sm border-gray-200 rounded-lg focus:ring-primary focus:border-primary pr-10"
                                    placeholder="{{ __('Search for product...') }}" autocomplete="off"
                                    @keydown.arrow-down.prevent="handleKeyDown()"
                                    @keydown.arrow-up.prevent="handleKeyUp()"
                                    @keydown.enter.prevent="handleEnter()" />
                                <div
                                    class="absolute inset-y-0 right-0 pr-3.5 flex items-center pointer-events-none text-gray-400">
                                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" stroke-linecap="round"
                                            stroke-linejoin="round" stroke-width="2"></path>
                                    </svg>
                                </div>

                                <!-- Search Results Dropdown -->
                                <div x-show="results.length > 0"
                                    class="absolute w-full mt-1 bg-white border border-gray-200 rounded-lg shadow-lg z-50 max-h-60 overflow-y-auto">
                                    <ul class="py-1">
                                        <template x-for="(item, index) in results" :key="item.id">
                                            <li class="px-4 py-2 hover:bg-gray-50 cursor-pointer transition-colors"
                                                :class="{ 'bg-primary text-white hover:bg-accent': selectedIndex === index }"
                                                @click="selectItem(item)" x-text="item.name">
                                            </li>
                                        </template>
                                    </ul>
                                </div>

                                <div x-show="showNoResults"
                                    class="absolute w-full mt-1 bg-white border border-red-200 rounded-lg shadow-lg z-50">
                                    <div class="px-4 py-3 text-red-600 text-sm">
                                        {{ __('No results for') }} "<span x-text="searchTerm"></span>"
                                    </div>
                                </div>
                            </div>

                            <select
                                class="w-48 h-11 text-sm border-gray-200 rounded-lg focus:ring-primary focus:border-primary bg-gray-50"
                                wire:model="productAccount">
                                @foreach ($Stors as $key => $value)
                                    <option value="{{ $key }}">{{ $value }}</option>
                                @endforeach
                            </select>
                            @error('productAccount')
                                <span class="text-red-500 text-sm">{{ $message }}</span>
                            @enderror

                            <button type="button" @click="$store.manufacturingItems.loadItems(true)"
                                class="h-11 px-3 border border-gray-200 rounded-lg text-gray-400 hover:text-primary hover:border-primary transition-all"
                                title="{{ __('Refresh Items Data') }}">
                                <svg class="h-5 w-5"
                                    :class="{ 'animate-spin': $store.manufacturingItems && $store.manufacturingItems.loading }"
                                    fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path
                                        d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"
                                        stroke-linecap="round" stroke-linejoin="round" stroke-width="2"></path>
                                </svg>
                            </button>
                        </div>

                        <!-- Products Table or Empty State -->
                        @if (empty($selectedProducts))
                            <div
                                class="empty-state-card bg-gray-50/50 rounded-xl border-2 border-dashed border-gray-200 py-12">
                                <div class="bg-white p-4 rounded-full shadow-sm mb-4">
                                    <svg class="h-10 w-10 text-gray-300" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"
                                            stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"></path>
                                    </svg>
                                </div>
                                <p class="text-gray-500 font-medium">{{ __('No Products') }}</p>
                                <p class="text-[11px] text-gray-400 mt-1">
                                    {{ __('Add products used in manufacturing') }}</p>
                            </div>
                        @else
                            <div class="overflow-x-auto">
                                <table class="w-full desktop-table">
                                    <thead class="bg-gray-50 border-b border-gray-200">
                                        <tr class="text-center text-gray-600 font-medium text-xs">
                                            <th class="px-2 py-2">{{ __('Product') }}</th>
                                            <th class="px-2 py-2">{{ __('Quantity') }}</th>
                                            <th class="px-2 py-2">{{ __('Unit Cost') }}</th>
                                            <th class="px-2 py-2">{{ __('Cost Percentage') }} %</th>
                                            <th class="px-2 py-2">{{ __('Total') }}</th>
                                            <th class="px-2 py-2">{{ __('Actions') }}</th>
                                        </tr>
                                    </thead>
                                    <tbody id="products_table_body" class="divide-y divide-gray-100">
                                        @foreach ($selectedProducts as $index => $product)
                                            <tr wire:key="product-{{ $product['id'] ?? 'index-' . $index }}"
                                                class="hover:bg-gray-50 transition-colors">
                                                <td class="px-2 py-1.5">
                                                    <input type="text" value="{{ $product['name'] ?? '' }}"
                                                        class="w-full px-2 py-1 text-xs bg-gray-50 border-0 rounded"
                                                        readonly>
                                                </td>
                                                <td class="px-2 py-1.5">
                                                    <input type="number" id="product_quantity_{{ $index }}"
                                                        x-model.number="products[{{ $index }}].quantity"
                                                        @input="updateProductTotal({{ $index }})"
                                                        min="0.01" step="0.01"
                                                        class="w-full px-2 py-1 text-xs border-gray-200 rounded focus:ring-primary focus:border-primary text-center"
                                                        placeholder="{{ __('Quantity') }}">
                                                </td>
                                                <td class="px-2 py-1.5">
                                                    <input type="number" id="product_unit_cost_{{ $index }}"
                                                        x-model.number="products[{{ $index }}].average_cost"
                                                        @input="updateProductTotal({{ $index }})"
                                                        min="0" step="0.01"
                                                        class="w-full px-2 py-1 text-xs border-gray-200 rounded focus:ring-primary focus:border-primary text-center"
                                                        placeholder="{{ __('Unit Cost') }}"
                                                        title="{{ __('Average purchase price will be updated') }}">
                                                </td>
                                                <td class="px-2 py-1.5">
                                                    <input type="number"
                                                        id="product_cost_percentage_{{ $index }}"
                                                        x-model.number="products[{{ $index }}].cost_percentage"
                                                        min="0" max="100" step="0.01"
                                                        class="w-full px-2 py-1 text-xs border-gray-200 rounded focus:ring-primary focus:border-primary text-center"
                                                        placeholder="{{ __('Cost Percentage') }}">
                                                </td>
                                                <td class="px-2 py-1.5">
                                                    <input type="text"
                                                        x-bind:value="formatCurrency(products[{{ $index }}].total_cost || 0)"
                                                        class="w-full px-2 py-1 text-xs bg-emerald-50 border-0 rounded font-bold text-emerald-600 text-center"
                                                        readonly>
                                                </td>
                                                <td class="px-2 py-1.5 text-center">
                                                    <button wire:click="removeProduct({{ $index }})"
                                                        title="{{ __('Delete') }}">
                                                        <svg fill="none" stroke="currentColor"
                                                            viewBox="0 0 24 24">
                                                            <path
                                                                d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"
                                                                stroke-linecap="round" stroke-linejoin="round"
                                                                stroke-width="2"></path>
                                                        </svg>
                                                    </button>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @endif
                    </section>

                    <!-- Tabs -->
                    <div class="border-b border-gray-200 flex items-center gap-8">
                        <button wire:click="$set('activeTab', 'general_chat')"
                            class="pb-4 px-2 text-sm font-bold transition-colors {{ ($activeTab ?? 'general_chat') == 'general_chat' ? 'border-b-2 border-primary text-primary' : 'text-gray-400 hover:text-gray-700' }}">
                            {{ __('Raw Materials') }}
                        </button>
                        <button wire:click="$set('activeTab', 'group_chat')"
                            class="pb-4 px-2 text-sm font-medium transition-colors {{ ($activeTab ?? 'general_chat') == 'group_chat' ? 'border-b-2 border-primary text-primary' : 'text-gray-400 hover:text-gray-700' }}">
                            {{ __('Expenses') }}
                        </button>
                    </div>

                    <!-- Tab Content -->
                    <div class="tab-content">
                        <!-- Raw Materials Tab -->
                        <div class="{{ ($activeTab ?? 'general_chat') == 'general_chat' ? '' : 'hidden' }}">
                            <section class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                                <div class="flex gap-3 mb-6" x-data="rawMaterialSearch()">
                                    <div class="relative flex-grow">
                                        <input type="text" x-model="searchTerm" id="raw_material_search"
                                            class="w-full h-11 text-sm border-gray-200 rounded-lg focus:ring-primary focus:border-primary pr-10"
                                            placeholder="{{ __('Search for raw material...') }}" autocomplete="off"
                                            @keydown.arrow-down.prevent="handleKeyDown()"
                                            @keydown.arrow-up.prevent="handleKeyUp()"
                                            @keydown.enter.prevent="handleEnter()" />
                                        <div
                                            class="absolute inset-y-0 right-0 pr-3.5 flex items-center pointer-events-none text-gray-400">
                                            <svg class="h-4 w-4" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"
                                                    stroke-linecap="round" stroke-linejoin="round" stroke-width="2">
                                                </path>
                                            </svg>
                                        </div>

                                        <div x-show="results.length > 0"
                                            class="absolute w-full mt-1 bg-white border border-gray-200 rounded-lg shadow-lg z-50 max-h-60 overflow-y-auto">
                                            <ul class="py-1">
                                                <template x-for="(item, index) in results" :key="item.id">
                                                    <li class="px-4 py-2 hover:bg-gray-50 cursor-pointer transition-colors"
                                                        :class="{
                                                            'bg-primary text-white hover:bg-accent': selectedIndex ===
                                                                index
                                                        }"
                                                        @click="selectItem(item)" x-text="item.name">
                                                    </li>
                                                </template>
                                            </ul>
                                        </div>

                                        <div x-show="showNoResults"
                                            class="absolute w-full mt-1 bg-white border border-red-200 rounded-lg shadow-lg z-50">
                                            <div class="px-4 py-3 text-red-600 text-sm">
                                                {{ __('No results for') }} "<span x-text="searchTerm"></span>"
                                            </div>
                                        </div>
                                    </div>

                                    <select
                                        class="w-48 h-11 text-sm border-gray-200 rounded-lg focus:ring-primary focus:border-primary bg-gray-50"
                                        wire:model.live="rawAccount">
                                        @foreach ($Stors as $keyStore1 => $valueStore1)
                                            <option value="{{ $keyStore1 }}">{{ $valueStore1 }}</option>
                                        @endforeach
                                    </select>
                                    @error('rawAccount')
                                        <span class="text-red-500 text-sm">{{ $message }}</span>
                                    @enderror

                                    <button type="button" @click="$store.manufacturingItems.loadItems(true)"
                                        class="h-11 px-3 border border-gray-200 rounded-lg text-gray-400 hover:text-primary hover:border-primary transition-all"
                                        title="{{ __('Refresh Items Data') }}">
                                        <svg class="h-5 w-5"
                                            :class="{
                                                'animate-spin': $store.manufacturingItems && $store.manufacturingItems
                                                    .loading
                                            }"
                                            fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path
                                                d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"
                                                stroke-linecap="round" stroke-linejoin="round" stroke-width="2">
                                            </path>
                                        </svg>
                                    </button>
                                </div>

                                @if (empty($selectedRawMaterials))
                                    <div
                                        class="empty-state-card bg-gray-50/50 rounded-xl border-2 border-dashed border-gray-200 py-12">
                                        <div class="bg-white p-4 rounded-full shadow-sm mb-4">
                                            <svg class="h-10 w-10 text-gray-300" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path
                                                    d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"
                                                    stroke-linecap="round" stroke-linejoin="round"
                                                    stroke-width="1.5"></path>
                                            </svg>
                                        </div>
                                        <p class="text-gray-500 font-medium">{{ __('No Raw Materials') }}</p>
                                        <p class="text-[11px] text-gray-400 mt-1">
                                            {{ __('Add raw materials used in manufacturing') }}</p>
                                    </div>
                                @else
                                    <div class="overflow-x-auto">
                                        <table class="w-full desktop-table">
                                            <thead class="bg-gray-50 border-b border-gray-200">
                                                <tr class="text-center text-gray-600 font-medium text-xs">
                                                    <th class="px-2 py-2 text-right">{{ __('Raw Material') }}</th>
                                                    <th class="px-2 py-2">{{ __('Unit') }}</th>
                                                    <th class="px-2 py-2">{{ __('Quantity') }}</th>
                                                    <th class="px-2 py-2">{{ __('Cost Price') }}</th>
                                                    <th class="px-2 py-2">{{ __('Total') }}</th>
                                                    <th class="px-2 py-2">{{ __('Actions') }}</th>
                                                </tr>
                                            </thead>
                                            <tbody id="raw_materials_table_body" class="divide-y divide-gray-100">
                                                @foreach ($selectedRawMaterials as $index => $material)
                                                    <tr wire:key="raw-material-{{ $material['id'] ?? 'index-' . $index }}"
                                                        class="hover:bg-gray-50 transition-colors">
                                                        <td class="px-2 py-1.5">
                                                            <input type="text"
                                                                value="{{ $material['name'] ?? '' }}"
                                                                class="w-full px-2 py-1 text-xs bg-gray-50 border-0 rounded"
                                                                readonly>
                                                        </td>
                                                        <td class="px-2 py-1.5">
                                                            <select
                                                                x-model="rawMaterials[{{ $index }}].unit_id"
                                                                @change="updateRawMaterialUnit({{ $index }}); updateRawMaterialTotal({{ $index }})"
                                                                class="w-full px-2 py-1 text-xs border-gray-200 rounded focus:ring-primary focus:border-primary"
                                                                data-item-id="{{ $material['id'] ?? '' }}">
                                                                @foreach ($material['unitsList'] ?? [] as $unit)
                                                                    <option value="{{ $unit['id'] }}">
                                                                        {{ $unit['name'] }}
                                                                        ({{ number_format($unit['available_qty'], 0, '.', '') }}
                                                                        {{ __('pieces') }})
                                                                    </option>
                                                                @endforeach
                                                            </select>
                                                        </td>
                                                        <td class="px-2 py-1.5">
                                                            <input type="number"
                                                                id="raw_quantity_{{ $index }}"
                                                                x-model.number="rawMaterials[{{ $index }}].quantity"
                                                                @input="updateRawMaterialTotal({{ $index }})"
                                                                min="0.01" step="0.01"
                                                                class="w-full px-2 py-1 text-xs border-gray-200 rounded focus:ring-primary focus:border-primary text-center"
                                                                placeholder="{{ __('Quantity') }}">
                                                        </td>
                                                        <td class="px-2 py-1.5">
                                                            <input type="text"
                                                                id="raw_unit_cost_{{ $index }}"
                                                                x-bind:value="rawMaterials[{{ $index }}].average_cost || 0"
                                                                class="w-full px-2 py-1 text-xs bg-gray-50 border-0 rounded text-center"
                                                                readonly>
                                                        </td>
                                                        <td class="px-2 py-1.5">
                                                            <input type="text"
                                                                x-bind:value="formatCurrency(rawMaterials[{{ $index }}]
                                                                    .total_cost || 0)"
                                                                class="w-full px-2 py-1 text-xs bg-amber-50 border-0 rounded font-bold text-amber-600 text-center"
                                                                readonly>
                                                        </td>
                                                        <td class="px-2 py-1.5 text-center">
                                                            <button
                                                                wire:click="removeRawMaterial({{ $index }})"
                                                                title="{{ __('Delete') }}">
                                                                <svg fill="none" stroke="currentColor"
                                                                    viewBox="0 0 24 24">
                                                                    <path
                                                                        d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"
                                                                        stroke-linecap="round" stroke-linejoin="round"
                                                                        stroke-width="2"></path>
                                                                </svg>
                                                            </button>
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                @endif
                            </section>
                        </div>

                        <!-- Expenses Tab -->
                        <div class="{{ ($activeTab ?? 'general_chat') == 'group_chat' ? '' : 'hidden' }}">
                            <section class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                                <div class="flex items-center justify-between mb-6">
                                    <h3 class="text-lg font-bold text-gray-800">{{ __('Additional Expenses') }}</h3>
                                    <button wire:click="addExpense"
                                        class="h-10 px-4 bg-primary text-white rounded-lg hover:bg-accent transition-all flex items-center gap-2">
                                        <svg class="h-4 w-4" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path d="M12 4v16m8-8H4" stroke-linecap="round" stroke-linejoin="round"
                                                stroke-width="2"></path>
                                        </svg>
                                        {{ __('Add Expense') }}
                                    </button>
                                </div>

                                @if (count($additionalExpenses) === 0)
                                    <div
                                        class="empty-state-card bg-gray-50/50 rounded-xl border-2 border-dashed border-gray-200 py-12">
                                        <div class="bg-white p-4 rounded-full shadow-sm mb-4">
                                            <svg class="h-10 w-10 text-gray-300" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path
                                                    d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"
                                                    stroke-linecap="round" stroke-linejoin="round"
                                                    stroke-width="1.5"></path>
                                            </svg>
                                        </div>
                                        <p class="text-gray-500 font-medium">{{ __('No Additional Expenses') }}</p>
                                        <p class="text-[11px] text-gray-400 mt-1">
                                            {{ __('Add additional manufacturing expenses') }}</p>
                                    </div>
                                @else
                                    <div class="overflow-x-auto">
                                        <table class="w-full desktop-table">
                                            <thead class="bg-gray-50 border-b border-gray-200">
                                                <tr class="text-center text-gray-600 font-medium text-xs">
                                                    <th class="px-2 py-2">{{ __('Amount') }}</th>
                                                    <th class="px-2 py-2">{{ __('Account') }}</th>
                                                    <th class="px-2 py-2 text-right">{{ __('Description') }}</th>
                                                    <th class="px-2 py-2">{{ __('Actions') }}</th>
                                                </tr>
                                            </thead>
                                            <tbody id="additional_expenses_table_body"
                                                class="divide-y divide-gray-100">
                                                @foreach ($additionalExpenses as $index => $expense)
                                                    <tr class="hover:bg-gray-50 transition-colors">
                                                        <td class="px-2 py-1.5">
                                                            <input type="number"
                                                                x-model.number="expenses[{{ $index }}].amount"
                                                                @input="updateTotals()" min="0" step="0.01"
                                                                placeholder="0.00"
                                                                class="w-full px-2 py-1 text-xs border-gray-200 rounded focus:ring-primary focus:border-primary text-center">
                                                        </td>
                                                        <td class="px-2 py-1.5">
                                                            <select
                                                                x-model="expenses[{{ $index }}].account_id"
                                                                class="w-full px-2 py-1 text-xs border-gray-200 rounded focus:ring-primary focus:border-primary">
                                                                @foreach ($expenseAccountList as $keyExpense => $valueExpense)
                                                                    <option value="{{ $keyExpense }}">
                                                                        {{ $valueExpense }}</option>
                                                                @endforeach
                                                            </select>
                                                        </td>
                                                        <td class="px-2 py-1.5">
                                                            <input type="text"
                                                                x-model="expenses[{{ $index }}].description"
                                                                placeholder="{{ __('Expense Description') }}"
                                                                class="w-full px-2 py-1 text-xs border-gray-200 rounded focus:ring-primary focus:border-primary">
                                                        </td>
                                                        <td class="px-2 py-1.5 text-center">
                                                            <button wire:click="removeExpense({{ $index }})"
                                                                title="{{ __('Delete') }}">
                                                                <svg fill="none" stroke="currentColor"
                                                                    viewBox="0 0 24 24">
                                                                    <path
                                                                        d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"
                                                                        stroke-linecap="round" stroke-linejoin="round"
                                                                        stroke-width="2"></path>
                                                                </svg>
                                                            </button>
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                @endif
                            </section>
                        </div>
                    </div>
                </div>

                <!-- Right: Summary Sidebar (4 columns) -->
                <div class="col-span-12 lg:col-span-4 px-4">
                    <div class="sticky top-24 space-y-6">
                        {{-- ملخص التكاليف --}}
                        <div class="bg-white rounded-xl shadow-lg border-0">
                            <div class=" p-4 rounded-t-xl">
                                <h3 class="text-lg font-bold mb-0 flex items-center gap-2">
                                    <svg class="h-5 w-5" fill="currentColor" viewBox="0 0 20 20">
                                        <path
                                            d="M2 11a1 1 0 011-1h2a1 1 0 011 1v5a1 1 0 01-1 1H3a1 1 0 01-1-1v-5zM8 7a1 1 0 011-1h2a1 1 0 011 1v9a1 1 0 01-1 1H9a1 1 0 01-1-1V7zM14 4a1 1 0 011-1h2a1 1 0 011 1v12a1 1 0 01-1 1h-2a1 1 0 01-1-1V4z">
                                        </path>
                                    </svg>
                                    {{ __('Cost Summary') }}
                                </h3>
                            </div>

                            <div class="p-6">
                                {{-- القسمين جنب بعض --}}
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    {{-- الجزء الأول: الإجماليات --}}
                                    <div>
                                        <h6
                                            class="text-primary font-bold border-b border-gray-200 pb-2 mb-3 flex items-center gap-2">
                                            <svg class="h-4 w-4" fill="currentColor" viewBox="0 0 20 20">
                                                <path d="M9 2a1 1 0 000 2h2a1 1 0 100-2H9z"></path>
                                                <path fill-rule="evenodd"
                                                    d="M4 5a2 2 0 012-2 3 3 0 003 3h2a3 3 0 003-3 2 2 0 012 2v11a2 2 0 01-2 2H6a2 2 0 01-2-2V5zm3 4a1 1 0 000 2h.01a1 1 0 100-2H7zm3 0a1 1 0 000 2h3a1 1 0 100-2h-3zm-3 4a1 1 0 100 2h.01a1 1 0 100-2H7zm3 0a1 1 0 100 2h3a1 1 0 100-2h-3z"
                                                    clip-rule="evenodd"></path>
                                            </svg>
                                            {{ __('Totals') }}
                                        </h6>

                                        <div class="space-y-3">
                                            {{-- إجمالي الخامات --}}
                                            <div class="flex justify-between items-center">
                                                <div class="flex items-center gap-2 text-gray-600">
                                                    <div class="p-1.5 bg-blue-50 rounded-md">
                                                        <svg class="h-4 w-4 text-blue-500" fill="currentColor"
                                                            viewBox="0 0 20 20">
                                                            <path
                                                                d="M3 4a1 1 0 011-1h12a1 1 0 011 1v2a1 1 0 01-1 1H4a1 1 0 01-1-1V4zM3 10a1 1 0 011-1h6a1 1 0 011 1v6a1 1 0 01-1 1H4a1 1 0 01-1-1v-6zM14 9a1 1 0 00-1 1v6a1 1 0 001 1h2a1 1 0 001-1v-6a1 1 0 00-1-1h-2z">
                                                            </path>
                                                        </svg>
                                                    </div>
                                                    <span
                                                        class="text-xs font-medium">{{ __('Total Raw Materials') }}</span>
                                                </div>
                                                <span class="text-sm font-bold text-blue-600"
                                                    x-text="formatCurrency(totalRawMaterialsCost)">0.00 ج.م</span>
                                            </div>

                                            {{-- إجمالي المصروفات --}}
                                            <div class="flex justify-between items-center">
                                                <div class="flex items-center gap-2 text-gray-600">
                                                    <div class="p-1.5 bg-amber-50 rounded-md">
                                                        <svg class="h-4 w-4 text-amber-500" fill="currentColor"
                                                            viewBox="0 0 20 20">
                                                            <path d="M4 4a2 2 0 00-2 2v1h16V6a2 2 0 00-2-2H4z"></path>
                                                            <path fill-rule="evenodd"
                                                                d="M18 9H2v5a2 2 0 002 2h12a2 2 0 002-2V9zM4 13a1 1 0 011-1h1a1 1 0 110 2H5a1 1 0 01-1-1zm5-1a1 1 0 100 2h1a1 1 0 100-2H9z"
                                                                clip-rule="evenodd"></path>
                                                        </svg>
                                                    </div>
                                                    <span
                                                        class="text-xs font-medium">{{ __('Total Expenses') }}</span>
                                                </div>
                                                <span class="text-sm font-bold text-amber-600"
                                                    x-text="formatCurrency(totalExpenses)">0.00 ج.م</span>
                                            </div>

                                            {{-- إجمالي تكلفة الفاتورة --}}
                                            <div
                                                class="flex justify-between items-center border-t border-gray-200 pt-3">
                                                <div class="flex items-center gap-2 text-gray-700">
                                                    <div class="p-1.5 bg-red-50 rounded-md">
                                                        <svg class="h-4 w-4 text-red-500" fill="currentColor"
                                                            viewBox="0 0 20 20">
                                                            <path
                                                                d="M2 11a1 1 0 011-1h2a1 1 0 011 1v5a1 1 0 01-1 1H3a1 1 0 01-1-1v-5zM8 7a1 1 0 011-1h2a1 1 0 011 1v9a1 1 0 01-1 1H9a1 1 0 01-1-1V7zM14 4a1 1 0 011-1h2a1 1 0 011 1v12a1 1 0 01-1 1h-2a1 1 0 01-1-1V4z">
                                                            </path>
                                                        </svg>
                                                    </div>
                                                    <span
                                                        class="text-xs font-bold">{{ __('Total Invoice Cost') }}</span>
                                                </div>
                                                <span class="text-base font-bold text-red-600"
                                                    x-text="formatCurrency(totalInvoiceCost)">0.00 ج.م</span>
                                            </div>

                                            {{-- إجمالي المنتجات --}}
                                            <div class="flex justify-between items-center">
                                                <div class="flex items-center gap-2 text-gray-700">
                                                    <div class="p-1.5 bg-emerald-50 rounded-md">
                                                        <svg class="h-4 w-4 text-emerald-500" fill="currentColor"
                                                            viewBox="0 0 20 20">
                                                            <path
                                                                d="M7 3a1 1 0 000 2h6a1 1 0 100-2H7zM4 7a1 1 0 011-1h10a1 1 0 110 2H5a1 1 0 01-1-1zM2 11a2 2 0 012-2h12a2 2 0 012 2v4a2 2 0 01-2 2H4a2 2 0 01-2-2v-4z">
                                                            </path>
                                                        </svg>
                                                    </div>
                                                    <span
                                                        class="text-xs font-bold">{{ __('Total Products Value') }}</span>
                                                </div>
                                                <span class="text-base font-bold text-emerald-600"
                                                    x-text="formatCurrency(totalProductsCost)">0.00 ج.م</span>
                                            </div>
                                        </div>
                                    </div>

                                    {{-- الجزء الثاني: المعيار والانحراف --}}
                                    <div>
                                        <h6
                                            class="text-primary font-bold border-b border-gray-200 pb-2 mb-3 flex items-center gap-2">
                                            <svg class="h-4 w-4" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd"
                                                    d="M3 3a1 1 0 000 2v8a2 2 0 002 2h2.586l-1.293 1.293a1 1 0 101.414 1.414L10 15.414l2.293 2.293a1 1 0 001.414-1.414L12.414 15H15a2 2 0 002-2V5a1 1 0 100-2H3zm11.707 4.707a1 1 0 00-1.414-1.414L10 9.586 8.707 8.293a1 1 0 00-1.414 0l-2 2a1 1 0 101.414 1.414L8 10.414l1.293 1.293a1 1 0 001.414 0l4-4z"
                                                    clip-rule="evenodd"></path>
                                            </svg>
                                            {{ __('Standard & Variance Analysis') }}
                                        </h6>

                                        <div class="space-y-3">
                                            {{-- التكلفة المعيارية --}}
                                            <div class="flex justify-between items-center bg-gray-50 p-3 rounded-lg">
                                                <div class="flex items-center gap-2 text-gray-700">
                                                    <div class="p-1.5 bg-blue-100 rounded-md">
                                                        <svg class="h-4 w-4 text-blue-600" fill="currentColor"
                                                            viewBox="0 0 20 20">
                                                            <path
                                                                d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z">
                                                            </path>
                                                        </svg>
                                                    </div>
                                                    <span
                                                        class="text-xs font-bold">{{ __('Standard Cost (Template)') }}</span>
                                                </div>
                                                <span class="text-base font-bold text-primary"
                                                    x-text="formatCurrency(totalManufacturingCost)">0.00 ج.م</span>
                                            </div>

                                            {{-- الانحراف المعياري --}}
                                            <div
                                                class="flex justify-between items-center border-t border-gray-200 pt-3">
                                                <div class="flex items-center gap-2 text-gray-700">
                                                    <div class="p-1.5 bg-purple-50 rounded-md">
                                                        <svg class="h-4 w-4 text-purple-500" fill="currentColor"
                                                            viewBox="0 0 20 20">
                                                            <path fill-rule="evenodd"
                                                                d="M12 7a1 1 0 110-2h5a1 1 0 011 1v5a1 1 0 11-2 0V8.414l-4.293 4.293a1 1 0 01-1.414 0L8 10.414l-4.293 4.293a1 1 0 01-1.414-1.414l5-5a1 1 0 011.414 0L11 10.586 14.586 7H12z"
                                                                clip-rule="evenodd"></path>
                                                        </svg>
                                                    </div>
                                                    <span
                                                        class="text-xs font-bold">{{ __('Variance (Difference)') }}</span>
                                                </div>
                                                <div class="flex flex-col items-end">
                                                    <span class="text-base font-bold"
                                                        :class="(totalProductsCost - totalManufacturingCost) >= 0 ?
                                                            'text-emerald-600' : 'text-red-600'"
                                                        x-text="formatCurrency(Math.abs(totalProductsCost - totalManufacturingCost))">0.00
                                                        ج.م</span>
                                                    <span class="text-xs font-bold px-2 py-0.5 rounded mt-1"
                                                        :class="(totalProductsCost - totalManufacturingCost) >= 0 ?
                                                            'bg-emerald-100 text-emerald-700' :
                                                            'bg-red-100 text-red-700'"
                                                        x-text="totalManufacturingCost > 0 ? (Math.abs((totalProductsCost - totalManufacturingCost) / totalManufacturingCost * 100).toFixed(2) + '%') : '0%'">0%</span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                {{-- ملاحظة توضيحية --}}
                                {{-- <div class="mt-4 bg-blue-50 border border-blue-100 p-3 rounded-lg">
                                    <p class="text-xs text-blue-800 leading-relaxed flex items-start gap-2">
                                        <svg class="h-4 w-4 text-blue-600 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"></path>
                                        </svg>
                                        <span x-show="(totalProductsCost - totalManufacturingCost) >= 0">
                                            {{ __('Positive variance indicates actual cost is lower than standard (favorable)') }}
                                        </span>
                                        <span x-show="(totalProductsCost - totalManufacturingCost) < 0">
                                            {{ __('Negative variance indicates actual cost is higher than standard (unfavorable)') }}
                                        </span>
                                    </p>
                                </div> --}}
                            </div>
                        </div>

                        <div class="bg-emerald-50 border border-emerald-100 p-4 rounded-xl">
                            <p class="text-[11px] text-emerald-800 leading-relaxed">
                                {{ __('Note: Review quantities and prices before saving. Inventory will be updated upon invoice approval.') }}
                            </p>
                        </div>

                        <div>
                            <div x-show="products.length > 0" class="w-full mt-3">
                                <div class="bg-blue-50 border border-blue-100 p-4 rounded-lg flex items-center gap-3">
                                    <div class="bg-blue-100 p-2 rounded-full">
                                        <svg class="h-5 w-5 text-blue-600" fill="currentColor" viewBox="0 0 20 20">
                                            <path clip-rule="evenodd"
                                                d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z"
                                                fill-rule="evenodd"></path>
                                        </svg>
                                    </div>
                                    <p class="text-xs text-blue-800 leading-relaxed font-medium">
                                        {{ __('Total raw materials and expenses will be distributed') }}
                                        (<span x-text="formatCurrency(totalExpenses)" class="font-bold"></span>)
                                        {{ __('on products based on specified percentages') }}
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </main>
        </div>
    @endif
</div>

@push('scripts')
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
    <link
        href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Tajawal:wght@400;500;700&display=swap"
        rel="stylesheet" />
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: '#009485',
                        secondary: '#f3f4f6',
                        accent: '#006d62'
                    },
                    fontFamily: {
                        sans: ['Tajawal', 'Inter', 'sans-serif'],
                    },
                    borderRadius: {
                        'eight': '8px',
                    }
                }
            }
        }
    </script>
    <style>
        .custom-scrollbar::-webkit-scrollbar {
            width: 4px;
        }

        .custom-scrollbar::-webkit-scrollbar-track {
            background: #f1f1f1;
        }

        .custom-scrollbar::-webkit-scrollbar-thumb {
            background: #ccc;
            border-radius: 4px;
        }

        .empty-state-card {
            min-height: 200px;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            border: 2px dashed #e5e7eb;
        }

        /* Desktop-style table */
        .desktop-table {
            border-collapse: collapse;
            font-size: 0.7rem;
            border: 1px solid #a5b4fc;
        }

        .desktop-table * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        .desktop-table thead tr {
            background-color: #c7d2fe;
            height: 0.2rem;
        }

        .desktop-table th {
            border: 1px solid #a5b4fc;
            padding: 0.1rem 0.2rem;
            text-align: center;
            font-weight: 600;
            color: #1e293b;
            font-size: 0.7rem;
            line-height: 1;
            height: 0.2rem;
        }

        .desktop-table tbody tr {
            height: auto;
            min-height: 1.5rem;
        }

        .desktop-table td {
            border: 1px solid #a5b4fc;
            padding: 0.15rem 0.25rem;
            margin: 0;
            line-height: 1.2;
            vertical-align: middle;
            height: auto;
        }

        .desktop-table tbody tr:nth-child(even) {
            background-color: #dbeafe;
        }

        .desktop-table tbody tr:nth-child(odd) {
            background-color: #ffffff;
        }

        .desktop-table input {
            background: transparent;
            border: none;
            outline: none;
            width: 100%;
            padding: 0.1rem 0.2rem;
            margin: 0;
            font-size: 0.7rem;
            line-height: 1.2;
            height: auto;
            min-height: 1rem;
        }

        .desktop-table select {
            background: transparent;
            border: none;
            outline: none;
            width: 100%;
            padding: 0.1rem 0.2rem;
            margin: 0;
            font-size: 0.7rem;
            line-height: 1.2;
            height: auto;
            min-height: 1rem;
        }

        .desktop-table button {
            padding: 0;
            margin: 0;
            line-height: 1;
            height: 1.2rem;
            width: 1.2rem;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            background: transparent;
            border: none;
            cursor: pointer;
        }

        .desktop-table button:hover {
            background-color: rgba(239, 68, 68, 0.1);
        }

        .desktop-table svg {
            width: 1.1rem;
            height: 1.1rem;
            display: block;
            color: #ef4444;
        }
    </style>
    <script src="https://cdn.jsdelivr.net/npm/fuse.js@6.6.2"></script>
    <script src="{{ asset('modules/manufacturing/js/manufacturing-calculator.js') }}"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            function setupKeyboardNavigation() {
                // Product quantity field - navigate to search
                document.querySelectorAll('input[id^="product_quantity_"]').forEach(function(field) {
                    field.addEventListener('keydown', function(e) {
                        if (e.key === 'Enter') {
                            e.preventDefault();
                            const searchField = document.getElementById('product_search');
                            if (searchField) {
                                searchField.focus();
                                searchField.value = '';
                            }
                        }
                    });
                });

                // Raw material quantity field - navigate to price field
                document.querySelectorAll('input[id^="raw_quantity_"]').forEach(function(field) {
                    field.addEventListener('keydown', function(e) {
                        if (e.key === 'Enter') {
                            e.preventDefault();
                            const index = this.id.split('_')[2];
                            const nextField = document.getElementById('raw_unit_cost_' + index);
                            if (nextField) {
                                nextField.focus();
                                nextField.select();
                            }
                        }
                    });
                });

                // Price field - navigate to search field
                document.querySelectorAll('input[id^="raw_quantity_"]').forEach(function(field) {
                    field.addEventListener('keydown', function(e) {
                        if (e.key === 'Enter') {
                            e.preventDefault();
                            const searchField = document.getElementById('raw_material_search');
                            if (searchField) {
                                searchField.focus();
                                searchField.value = '';
                            }
                        }
                    });
                });
            }

            document.addEventListener('livewire:init', () => {
                Livewire.on('show-alert', (data) => {
                    Swal.fire({
                        title: data.title,
                        text: data.text,
                        icon: data.icon,
                    })
                });
            })

            // Initialize on page load
            setupKeyboardNavigation();
            document.addEventListener('livewire:init', () => {
                // Focus on product quantity field
                Livewire.on('focusProductQuantity', (index) => {
                    setTimeout(() => {
                        const field = document.getElementById(`product_quantity_${index}`);
                        if (field) {
                            field.focus();
                            field.select();
                        }
                    }, 300);
                });

                // Focus on raw material quantity field
                Livewire.on('focusRawMaterialQuantity', (index) => {
                    setTimeout(() => {
                        const field = document.getElementById(`raw_quantity_${index}`);
                        if (field) {
                            field.focus();
                            field.select();
                        }
                    }, 300);
                });

                // Re-initialize navigation after Livewire updates
                Livewire.hook('morph.updated', () => {
                    setTimeout(setupKeyboardNavigation, 50);
                });
            });

            // Global focus functions (can be called from anywhere)
            window.focusProductSearch = function() {
                const field = document.getElementById('product_search');
                if (field) {
                    field.focus();
                    field.value = '';
                }
            };

            window.focusRawMaterialSearch = function() {
                const field = document.getElementById('raw_material_search');
                if (field) {
                    field.focus();
                    field.value = '';
                }
            };

            window.focusQuantityField = function(section, index) {
                setTimeout(() => {
                    const field = document.getElementById(`${section}_quantity_${index}`);
                    if (field) {
                        field.focus();
                        field.select();
                    }
                }, 200);
            };

            // Success Alert
            Livewire.on('success-swal', (data) => {
                const d = Array.isArray(data) ? data[0] : data;
                Swal.fire({
                    title: d.title || '{{ __('Done!') }}',
                    text: d.text || '{{ __('Operation completed successfully') }}',
                    icon: d.icon || 'success',
                    confirmButtonText: '{{ __('OK') }}',
                    allowOutsideClick: false,
                    allowEscapeKey: false
                }).then(() => {
                    if (d.reload || d.reload === true) {
                        window.location.reload();
                    }
                });
            });

            // Error Alert
            Livewire.on('error-swal', (data) => {
                const d = Array.isArray(data) ? data[0] : data;
                Swal.fire({
                    title: d.title || '{{ __('Error!') }}',
                    text: d.text || '{{ __('An unexpected error occurred') }}',
                    icon: d.icon || 'error',
                    confirmButtonText: '{{ __('OK') }}'
                });
            });

            // General Alert (optional)
            Livewire.on('alert-swal', (data) => {
                const d = Array.isArray(data) ? data[0] : data;
                Swal.fire(d);
            });

            document.addEventListener('DOMContentLoaded', function() {
                // Track value changes
                Livewire.on('template-selected', (templateId) => {
                    console.log('Template selected:', templateId);
                });

                Livewire.on('templates-loaded', (count) => {
                    console.log('Templates loaded:', count);
                });
            });
        });
    </script>
@endpush
