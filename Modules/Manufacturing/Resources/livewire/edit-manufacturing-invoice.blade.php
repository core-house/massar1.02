<div class="container-fluid">
    @if ($currentStep === 1)
        <div class="row">
            <div class="col-12">
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-body">
                        <div class="d-flex flex-wrap align-items-start justify-content-between gap-3">
                            <div>
                                <h1 class="h3 fw-bold text-dark mb-2">{{ __('manufacturing::manufacturing.edit_manufacturing_invoice') }}</h1>
                                <div class="d-flex flex-wrap gap-4 text-muted small">
                                    <div>
                                        <span class="fw-semibold">{{ __('manufacturing::manufacturing.invoice_number') }}:</span>
                                        <span class="text-primary fw-bold">{{ $pro_id }}</span>
                                    </div>
                                    <div>
                                        <span class="fw-semibold">{{ __('manufacturing::manufacturing.date') }}:</span>
                                        <span>{{ $invoiceDate }}</span>
                                    </div>
                                </div>
                            </div>

                            <div class="d-flex flex-wrap align-items-center gap-3">
                                <div class="min-w-200">
                                    <x-branches::branch-select :branches="$branches" model="branch_id" />
                                </div>

                                {{-- @if (setting('manufacture_enable_template_saving')) --}}
                                <div class="d-flex flex-wrap gap-2">
                                    <button wire:click="openSaveTemplateModal" class="btn btn-outline-info btn-sm">
                                        <i class="fas fa-save me-1"></i>{{ __('manufacturing::manufacturing.save_as_template') }}
                                    </button>
                                    <button wire:click="openLoadTemplateModal" class="btn btn-outline-warning btn-sm">
                                        <i class="fas fa-folder-open me-1"></i>{{ __('manufacturing::manufacturing.select_template') }}
                                    </button>
                                </div>
                                {{-- @endif --}}

                                <div class="d-flex flex-wrap gap-2">
                                    <button wire:click="adjustCostsByPercentage"
                                        class="btn btn-primary btn-sm d-flex align-items-center gap-1"
                                        @if (empty($selectedProducts)) disabled @endif>
                                        <i class="fas fa-percentage"></i>
                                        <span>{{ __('manufacturing::manufacturing.distribute_costs_by_percentage') }}</span>
                                    </button>
                                    <button wire:click="updateInvoice"
                                        class="btn btn-primary btn-sm d-flex align-items-center gap-1">
                                        <i class="fas fa-save"></i>
                                        <span>{{ __('manufacturing::manufacturing.update_invoice') }}</span>
                                    </button>
                                    <a href="{{ route('manufacturing.index') }}"
                                        class="btn btn-secondary btn-sm d-flex align-items-center gap-1">
                                        <i class="fas fa-times"></i>
                                        <span>{{ __('manufacturing::manufacturing.cancel') }}</span>
                                    </a>
                                </div>
                            </div>
                        </div>

                        @if (!empty($selectedProducts))
                            <div class="alert alert-info mt-3 mb-0 d-flex align-items-center gap-2 py-2">
                                <i class="fas fa-info-circle"></i>
                                <span class="small">
                                    {{ __('manufacturing::manufacturing.total_raw_materials_and_expenses_will_be_distributed') }}
                                    ({{ number_format(collect($additionalExpenses)->map(fn($item) => (float) $item['amount'])->sum()) }}
                                    {{ __('manufacturing::manufacturing.egp_symbol') }})
                                    {{ __('manufacturing::manufacturing.on_products_based_on_specified_percentages') }}
                                </span>
                            </div>
                        @endif
                    </div>
                </div>

                @if (
                    (count($selectedProducts) > 0 || count($selectedRawMaterials) > 0) &&
                        (!empty($templateExpectedTime) || $selectedTemplate))
                    <div class="card border-0 shadow-sm mb-4">
                        <div class="card-body">
                            <div class="row g-3 align-items-end">
                                <div class="col-md-3">
                                    <label class="form-label small text-muted">{{ __('manufacturing::manufacturing.expected_time') }}</label>
                                    <input type="text" wire:model="templateExpectedTime"
                                        class="form-control form-control-sm" readonly>
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label small text-muted">{{ __('manufacturing::manufacturing.actual_time') }}</label>
                                    <input type="text" wire:model="actualTime" class="form-control form-control-sm"
                                        id="actualTimePicker">
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label small text-muted">{{ __('manufacturing::manufacturing.quantity_multiplier') }}</label>
                                    <input type="number" wire:model="quantityMultiplier"
                                        class="form-control form-control-sm" min="0.1" step="0.1"
                                        value="1">
                                </div>
                                <div class="col-md-3">
                                    <button wire:click="applyQuantityMultiplier" class="btn btn-info btn-sm w-100">
                                        <i class="fas fa-calculator me-1"></i>{{ __('manufacturing::manufacturing.apply') }}
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                @endif


                <!-- مودال حفظ النموذج -->
                @if ($showSaveTemplateModal)
                    <div class="modal fade show" style="display: block;">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title">{{ __('manufacturing::manufacturing.save_as_manufacturing_template') }}</h5>
                                </div>
                                <div class="modal-body">
                                    <div class="form-group">
                                        <label>{{ __('manufacturing::manufacturing.template_name') }}</label>
                                        <input type="text" wire:model="templateName" class="form-control">
                                    </div>
                                </div>
                                <div class="modal-body">
                                    <div class="form-group">
                                        <label>{{ __('manufacturing::manufacturing.expected_production_time') }}</label>
                                        <input type="text" wire:model="templateExpectedTime" class="form-control"
                                            id="timepicker">
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

                                <div class="modal-footer">
                                    <button wire:click="saveAsTemplate"
                                        class="btn btn-primary">{{ __('manufacturing::manufacturing.save') }}</button>
                                    <button wire:click="closeSaveTemplateModal"
                                        class="btn btn-secondary">{{ __('manufacturing::manufacturing.cancel') }}</button>
                                </div>
                            </div>
                        </div>
                    </div>
                @endif

                <!-- مودال تحميل النموذج -->
                @if ($showLoadTemplateModal)
                    <div class="modal fade show" style="display: block; background-color: rgba(0,0,0,0.5);">
                        <div class="modal-dialog modal-lg">
                            <div class="modal-content">
                                <div class="modal-header bg-primary text-white">
                                    <h5 class="modal-title">
                                        <i class="fas fa-folder-open me-2"></i>
                                        {{ __('manufacturing::manufacturing.select_manufacturing_template') }}
                                    </h5>
                                    <button type="button" class="btn-close btn-close-white"
                                        wire:click="closeLoadTemplateModal"></button>
                                </div>
                                <div class="modal-body">
                                    @if (count($templates) > 0)
                                        <div class="mb-3">
                                            <label class="form-label fw-bold">{{ __('manufacturing::manufacturing.choose_template') }}:</label>
                                            <select wire:model.live="selectedTemplate"
                                                class="form-select form-select-lg">
                                                <option value="">{{ __('manufacturing::manufacturing.select_template_placeholder') }}
                                                </option>
                                                @foreach ($templates as $template)
                                                    <option value="{{ $template['id'] }}">
                                                        {{ $template['display_name'] }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>

                                        @if ($selectedTemplate)
                                            <div class="alert alert-info">
                                                <i class="fas fa-info-circle me-2"></i>
                                                <strong>{{ __('manufacturing::manufacturing.note') }}:</strong>
                                                {{ __('manufacturing::manufacturing.template_load_note') }}.
                                            </div>

                                            {{-- حقل مضاعف الكمية --}}
                                            <div class="mb-3">
                                                <label class="form-label fw-bold">
                                                    <i class="fas fa-times me-1"></i>
                                                    {{ __('manufacturing::manufacturing.quantity_multiplier') }}:
                                                </label>
                                                <input type="number" 
                                                       wire:model="quantityMultiplier" 
                                                       class="form-control form-control-lg"
                                                       min="0.01" 
                                                       step="0.01"
                                                       placeholder="1">
                                                <small class="form-text text-muted">
                                                    <i class="fas fa-info-circle me-1"></i>
                                                    {{ __('manufacturing::manufacturing.multiplier_note') }}
                                                </small>
                                            </div>
                                        @endif

                                        {{-- معاينة سريعة للنموذج المختار --}}
                                        @if ($selectedTemplate)
                                            <div class="card">
                                                <div class="card-header">
                                                    <h6 class="mb-0">{{ __('manufacturing::manufacturing.template_preview') }}</h6>
                                                </div>
                                                <div class="card-body">
                                                    @php
                                                        $currentTemplate = collect($templates)->firstWhere(
                                                            'id',
                                                            $selectedTemplate,
                                                        );
                                                    @endphp
                                                    @if ($currentTemplate)
                                                        <p class="mb-1">
                                                            <strong>{{ __('manufacturing::manufacturing.date') }}:</strong>
                                                            {{ $currentTemplate['pro_date'] }}
                                                        </p>
                                                        <p class="mb-0">
                                                            <strong>{{ __('manufacturing::manufacturing.value') }}:</strong>
                                                            {{ number_format($currentTemplate['pro_value'], 2) }}
                                                            {{ __('manufacturing::manufacturing.egp_symbol') }}
                                                        </p>
                                                    @endif
                                                </div>
                                            </div>
                                        @endif
                                    @else
                                        <div class="text-center py-4">
                                            <i class="fas fa-folder-open fa-3x text-muted mb-3"></i>
                                            <h5 class="text-muted">{{ __('manufacturing::manufacturing.no_saved_templates') }}</h5>
                                            <p class="text-muted">
                                                {{ __('manufacturing::manufacturing.save_template_first_note') }}</p>
                                        </div>
                                    @endif
                                </div>
                                <div class="modal-footer">
                                    @if (count($templates) > 0)
                                        <button wire:click="loadTemplate" class="btn btn-primary px-4"
                                            {{ !$selectedTemplate ? 'disabled' : '' }}>
                                            <i class="fas fa-download me-2"></i>
                                            {{ __('manufacturing::manufacturing.load_template') }}
                                        </button>
                                    @endif
                                    <button wire:click="closeLoadTemplateModal" class="btn btn-secondary px-4">
                                        <i class="fas fa-times me-2"></i>
                                        {{ __('manufacturing::manufacturing.cancel') }}
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                @endif

                <div class="row">
                    <div class="card-body">
                        <div class="row">

                            <div class="col-lg-2">
                                <label class="form-label" style="font-size: 1em;">{{ __('manufacturing::manufacturing.account') }}</label>
                                <select wire:model="OperatingAccount" class="form-control form-control-sm"
                                    style="font-size: 0.85em; height: 2em; padding: 2px 6px;">
                                    @foreach ($OperatingCenter as $keyOperation => $valueOperation)
                                        <option value="{{ $keyOperation }}">{{ $valueOperation }}</option>
                                    @endforeach
                                </select>
                                @error('OperatingAccount')
                                    <span class="text-red-500 text-sm">{{ $message }}</span>
                                @enderror
                            </div>

                            <div class="col-lg-2">
                                <label class="form-label" style="font-size: 1em;">{{ __('manufacturing::manufacturing.employee') }}</label>
                                <select wire:model="employee" class="form-control form-control-sm"
                                    style="font-size: 0.85em; height: 2em; padding: 2px 6px;">
                                    @foreach ($employeeList as $keyEmployee => $valueEmployee)
                                        <option value="{{ $keyEmployee }}">{{ $valueEmployee }}</option>
                                    @endforeach
                                </select>
                                @error('employee')
                                    <span class="text-red-500 text-sm">{{ $message }}</span>
                                @enderror
                            </div>

                            <div class="col-lg-2">
                                <label for="pro_date" class="form-label"
                                    style="font-size: 1em;">{{ __('manufacturing::manufacturing.date') }}</label>
                                <input type="date" wire:model="invoiceDate"
                                    class="form-control form-control-sm @error('pro_date') is-invalid @enderror"
                                    style="font-size: 0.85em; height: 2em; padding: 2px 6px;">
                                @error('pro_date')
                                    <span class="invalid-feedback"><strong>{{ $message }}</strong></span>
                                @enderror
                            </div>

                            <div class="col-lg-2">
                                <label for="pro_date" class="form-label"
                                    style="font-size: 1em;">{{ __('manufacturing::manufacturing.invoice_number') }}</label>
                                <input type="text" wire:model="pro_id"
                                    class="form-control form-control-sm @error('pro_id') is-invalid @enderror"
                                    style="font-size: 0.85em; height: 2em; padding: 2px 6px;" readonly>
                                @error('pro_id')
                                    <span class="invalid-feedback"><strong>{{ $message }}</strong></span>
                                @enderror
                            </div>

                            <div class="col-lg-2">
                                <label for="patchNumber" class="form-label"
                                    style="font-size: 1em;">{{ __('manufacturing::manufacturing.batch_number') }}</label>
                                <input type="text" wire:model="patchNumber" class="form-control form-control-sm "
                                    style="font-size: 0.85em; height: 2em; padding: 2px 6px;">
                            </div>

                            <div class="col-lg-2">
                                <label for="description" class="form-label"
                                    style="font-size: 1em;">{{ __('manufacturing::manufacturing.operation_description') }}</label>
                                <input type="text" wire:model="description" class="form-control form-control-sm"
                                    style="font-size: 0.85em; height: 2em; padding: 2px 6px;">
                            </div>

                        </div>
                    </div>
                </div>
                <hr style=" border: none; border-top: 12px solid #1908da; margin: 0.1rem 0;">

                <!-- قسم المنتجات المصنعة -->
                <div class="mb-9 card" style="max-height: 200px; overflow-y: auto; overflow-x: hidden;">
                    <!-- حقل البحث للمنتجات المصنعة -->
                    <div class="row">
                        <div class="col-lg-3 mb-0" style="position: relative; z-index: 999;">
                            <input type="text" wire:model.live="productSearchTerm" id="product_search"
                                class="form-control form-control-sm frst"
                                placeholder="{{ __('manufacturing::manufacturing.search_for_product') }}" autocomplete="off"
                                style="font-size: 1em;" wire:keydown.arrow-down="handleKeyDownProduct"
                                wire:keydown.arrow-up="handleKeyUpProduct"
                                wire:keydown.enter.prevent="handleEnterProduct" />
                            @if (strlen($productSearchTerm) > 0 && $productSearchResults->count())
                                <ul class="list-group position-absolute w-100" style="z-index: 999;">
                                    @foreach ($productSearchResults as $index => $item)
                                        <li class="list-group-item list-group-item-action
                                             @if ($productSelectedResultIndex === $index) active @endif"
                                            wire:click="addProductFromSearch({{ $item->id }})">
                                            {{ $item->name }}
                                        </li>
                                    @endforeach
                                </ul>
                            @elseif(strlen($productSearchTerm) > 0)
                                <div class="mt-2" style="position: absolute; z-index: 1000; width: 100%;">
                                    <div class="list-group-item text-danger">
                                        {{ __('manufacturing::manufacturing.no_results_for') }} "{{ $productSearchTerm }}"
                                    </div>
                                </div>
                            @endif
                        </div>

                        <div class="col-lg-2">
                            <select wire:model="productAccount" class="form-control form-control-sm"
                                style="font-size: 1em;">
                                @foreach ($Stors as $key => $value)
                                    <option value="{{ $key }}">{{ $value }}</option>
                                @endforeach
                            </select>
                            @error('productAccount')
                                <span class="text-red-500 text-sm">{{ $message }}</span>
                            @enderror
                        </div>

                    </div>

                    <div class="bg-light rounded-3 p-3 mx-3 mb-3">
                        @if (empty($selectedProducts))
                            <div class="text-center py-5">
                                <i class="fas fa-box-open text-muted mb-3" style="font-size: 2rem;"></i>
                                <h5 class="text-muted">{{ __('manufacturing::manufacturing.no_products') }}</h5>
                                <p class="text-muted small">{{ __('manufacturing::manufacturing.add_products_note') }}</p>
                            </div>
                        @else
                            <div class="space-y-3">
                                <table class="table table-bordered table-sm">
                                    <thead class="table-light">
                                        <tr class="text-center">
                                            <th style="width: 20%">{{ __('manufacturing::manufacturing.product') }}</th>
                                            <th style="width: 15%">{{ __('manufacturing::manufacturing.quantity') }}</th>
                                            <th style="width: 15%">{{ __('manufacturing::manufacturing.unit_cost') }}</th>
                                            <th style="width: 15%">{{ __('manufacturing::manufacturing.cost_percentage') }} %</th>
                                            <th style="width: 15%">{{ __('manufacturing::manufacturing.total') }}</th>
                                            <th style="width: 10%">{{ __('manufacturing::manufacturing.actions') }}</th>
                                        </tr>
                                    </thead>
                                    <tbody id="products_table_body">
                                        @foreach ($selectedProducts as $index => $product)
                                            <tr wire:key="product-{{ $product['id'] ?? 'index-' . $index }}">
                                                <td>
                                                    <input type="text" value="{{ $product['name'] ?? '' }}"
                                                        class="form-control form-control-sm bg-light" readonly
                                                        style="padding:2px;height:30px;font-size: 0.9em;">
                                                </td>
                                                <td>
                                                    <input type="number" id="product_quantity_{{ $index }}"
                                                        wire:model.lazy="selectedProducts.{{ $index }}.quantity"
                                                        wire:blur="updateProductTotal({{ $index }}, 'quantity')"
                                                        min="0.01" step="0.01"
                                                        class="form-control form-control-sm"
                                                        style="padding:2px;height:30px;font-size: 0.9em;"
                                                        placeholder="{{ __('manufacturing::manufacturing.quantity') }}">
                                                </td>

                                                <td>
                                                    <input type="number" id="product_unit_cost_{{ $index }}"
                                                        wire:model.lazy="selectedProducts.{{ $index }}.average_cost"
                                                        min="0" step="0.01"
                                                        class="form-control form-control-sm"
                                                        style="padding:2px;height:30px;font-size: 0.9em;"
                                                        placeholder="{{ __('manufacturing::manufacturing.unit_cost') }}"
                                                        title="{{ __('manufacturing::manufacturing.average_price_note') }}">

                                                    @if (isset($product['old_unit_cost']) && $product['unit_cost'] != $product['old_unit_cost'])
                                                        <small class="text-warning d-block">
                                                            <i class="fas fa-exclamation-triangle"></i>
                                                            {{ __('manufacturing::manufacturing.average_updated_from') }}
                                                            {{ number_format($product['old_unit_cost'], 2) }}
                                                            {{ __('manufacturing::manufacturing.to') }}
                                                            {{ number_format($product['unit_cost'], 2) }}
                                                        </small>
                                                    @endif
                                                </td>

                                                <td>
                                                    <input type="number"
                                                        id="product_cost_percentage_{{ $index }}"
                                                        wire:model.lazy="selectedProducts.{{ $index }}.cost_percentage"
                                                        min="0" max="100" step="0.01"
                                                        class="form-control form-control-sm"
                                                        style="padding:2px;height:30px;font-size: 0.9em;"
                                                        placeholder="{{ __('manufacturing::manufacturing.cost_percentage') }}">
                                                </td>
                                                <td>
                                                    <input type="text"
                                                        value="{{ number_format($product['total_cost'] ?? 0, 2) }} {{ __('manufacturing::manufacturing.egp_symbol') }}"
                                                        class="form-control form-control-sm bg-opacity-10 fw-bold text-green-600"
                                                        readonly style="padding:2px;height:30px;font-size: 0.9em;">
                                                </td>
                                                <td class="text-center">
                                                    <button wire:click="removeProduct({{ $index }})"
                                                        class="btn btn-danger btn-sm"
                                                        style="height:30px;padding:2px 8px;">
                                                        <i class="fa fa-trash"></i>
                                                    </button>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @endif
                    </div>
                </div>
            </div>


            <hr style=" border: none; border-top: 12px solid #1908da; margin: 0.1rem 0;">

            <div class="container-fluid">
                <div class="row">
                    <div class="chat-box-left col-12"
                        style="width: 100% !important; max-width: 100% !important; height: 300px;">

                        <!-- إضافة متغير لحفظ التاب النشط -->
                        <input type="hidden" wire:model="activeTab" value="{{ $activeTab ?? 'general_chat' }}">

                        <ul class="nav nav-pills mb-3 d-flex justify-content-center gap-2" id="pills-tab"
                            role="tablist" style="font-size: 0.8rem;">
                            <li class="nav-item">
                                <a class="nav-link py-1 px-2 {{ ($activeTab ?? 'general_chat') == 'general_chat' ? 'active' : '' }}"
                                    id="general_chat_tab" data-bs-toggle="pill" href="#general_chat"
                                    onclick="setActiveTab('general_chat')"
                                    wire:click="$set('activeTab', 'general_chat')">
                                    {{ __('manufacturing::manufacturing.raw_materials') }}
                                </a>
                            </li>

                            @if (setting('manufacture_enable_expenses'))
                                <li class="nav-item">
                                    <a class="nav-link py-1 px-2 {{ ($activeTab ?? 'general_chat') == 'group_chat' ? 'active' : '' }}"
                                        id="group_chat_tab" data-bs-toggle="pill" href="#group_chat"
                                        onclick="setActiveTab('group_chat')"
                                        wire:click="$set('activeTab', 'group_chat')">
                                        {{ __('manufacturing::manufacturing.expenses') }}
                                    </a>
                                </li>
                            @endif

                        </ul>

                        <div class="chat-list" data-simplebar="init">
                            <div class="simplebar-wrapper" style="margin: 0px;">
                                <div class="simplebar-mask">
                                    <div class="simplebar-offset" style="right: 0px; bottom: 0px;">
                                        <div class="simplebar-content-wrapper"
                                            style="height: 100%; overflow: hidden;">
                                            <div class="simplebar-content" style="padding: 0px;">
                                                <div class="tab-content" id="pills-tabContent">

                                                    <!-- تاب المواد الخام -->
                                                    <div class="tab-pane fade {{ ($activeTab ?? 'general_chat') == 'general_chat' ? 'active show' : '' }}"
                                                        id="general_chat">
                                                        <div class="mb-8 card"
                                                            style="max-height: 250px; overflow-y: auto; overflow-x: hidden;">
                                                            <!-- حقل البحث للمواد الخام -->
                                                            <div class="row p-3">
                                                                <div class="col-lg-3 mb-2"
                                                                    style="position: relative;">
                                                                    <input type="text"
                                                                        wire:model.live="rawMaterialSearchTerm"
                                                                        id="raw_material_search"
                                                                        class="form-control form-control-sm frst"
                                                                        placeholder="{{ __('manufacturing::manufacturing.search_for_raw_material') }}"
                                                                        autocomplete="off" style="font-size: 1em;"
                                                                        wire:keydown.arrow-down="handleKeyDownRawMaterial"
                                                                        wire:keydown.arrow-up="handleKeyUpRawMaterial"
                                                                        wire:keydown.enter.prevent="handleEnterRawMaterial" />

                                                                    @if (strlen($rawMaterialSearchTerm) > 0 && !is_null($rawMaterialSearchResults) && $rawMaterialSearchResults->count())
                                                                        <ul class="list-group position-absolute w-100"
                                                                            style="z-index: 999;">
                                                                            @foreach ($rawMaterialSearchResults as $index => $item)
                                                                                <li class="list-group-item list-group-item-action
                                                                        @if ($rawMaterialSelectedResultIndex === $index) active @endif"
                                                                                    wire:click="addRawMaterialFromSearch({{ $item->id }})">
                                                                                    {{ $item->name }}
                                                                                </li>
                                                                            @endforeach
                                                                        </ul>
                                                                    @elseif (strlen($rawMaterialSearchTerm) > 0)
                                                                        <div class="mt-2"
                                                                            style="position: absolute; z-index: 1000; width: 100%;">
                                                                            <div class="list-group-item text-danger">
                                                                                {{ __('manufacturing::manufacturing.no_results_for') }}
                                                                                "{{ $rawMaterialSearchTerm }}"
                                                                            </div>
                                                                        </div>
                                                                    @endif
                                                                </div>

                                                                <div class="col-lg-2 mb-2">
                                                                    <select wire:model.live="rawAccount"
                                                                        class="form-control form-control-sm"
                                                                        style="font-size: 1em;">
                                                                        @foreach ($Stors as $keyStore1 => $valueStore1)
                                                                            <option value="{{ $keyStore1 }}">
                                                                                {{ $valueStore1 }}</option>
                                                                        @endforeach
                                                                    </select>
                                                                    @error('rawAccount')
                                                                        <span
                                                                            class="text-danger small">{{ $message }}</span>
                                                                    @enderror
                                                                </div>
                                                            </div>

                                                            <div class="bg-light rounded-3 p-3 mx-3 mb-3">
                                                                @if (empty($selectedRawMaterials))
                                                                    <div class="text-center py-5">
                                                                        <i class="fas fa-box-open text-muted mb-3"
                                                                            style="font-size: 2rem;"></i>
                                                                        <h5 class="text-muted">
                                                                            {{ __('manufacturing::manufacturing.no_raw_materials_added') }}</h5>
                                                                        <p class="text-muted small">
                                                                            {{ __('manufacturing::manufacturing.add_raw_materials_note') }}
                                                                        </p>
                                                                    </div>
                                                                @else
                                                                    <div class="space-y-3">
                                                                        <!-- جدول المواد الخام -->
                                                                        <table class="table table-bordered table-sm">
                                                                            <thead class="table-light">
                                                                                <tr class="text-center">
                                                                                    <th style="width: 18%">
                                                                                        {{ __('manufacturing::manufacturing.raw_material') }}
                                                                                    </th>
                                                                                    <th style="width: 13%">
                                                                                        {{ __('manufacturing::manufacturing.unit') }}
                                                                                    </th>
                                                                                    <th style="width: 12%">
                                                                                        {{ __('manufacturing::manufacturing.available_stock') }}
                                                                                    </th>
                                                                                    <th style="width: 12%">
                                                                                        {{ __('manufacturing::manufacturing.quantity') }}
                                                                                    </th>
                                                                                    <th style="width: 13%">
                                                                                        {{ __('manufacturing::manufacturing.cost_price') }}
                                                                                    </th>
                                                                                    <th style="width: 13%">
                                                                                        {{ __('manufacturing::manufacturing.total') }}
                                                                                    </th>
                                                                                    <th style="width: 9%">
                                                                                        {{ __('manufacturing::manufacturing.actions') }}
                                                                                    </th>
                                                                                </tr>
                                                                            </thead>
                                                                            <tbody id="raw_materials_table_body">
                                                                                @foreach ($selectedRawMaterials as $index => $material)
                                                                                    <tr
                                                                                        wire:key="raw-material-{{ $material['id'] ?? 'index-' . $index }}">
                                                                                        <td>
                                                                                            <input type="text"
                                                                                                value="{{ $material['name'] ?? '' }}"
                                                                                                class="form-control form-control-sm bg-light"
                                                                                                readonly
                                                                                                style="padding:2px;height:30px;font-size: 0.9em;">
                                                                                        </td>
                                                                                        <td>
                                                                                            <select
                                                                                                wire:model.live="selectedRawMaterials.{{ $index }}.unit_id"
                                                                                                class="form-control form-control-sm unit-select">
                                                                                                @foreach ($material['unitsList'] ?? [] as $unit)
                                                                                                    <option
                                                                                                        value="{{ $unit['id'] }}">
                                                                                                        {{ $unit['name'] }}
                                                                                                        ({{ number_format($unit['available_qty'], 0, '.', ',') }}
                                                                                                        {{ __('manufacturing::manufacturing.pieces') }})
                                                                                                    </option>
                                                                                                @endforeach
                                                                                            </select>


                                                                                        </td>
                                                                                        <td class="text-center">
                                                                                            <span class="badge bg-info text-dark fw-bold" style="font-size: 0.9em;">
                                                                                                {{ number_format($material['available_stock'] ?? 0, 2) }}
                                                                                            </span>
                                                                                        </td>
                                                                                        <td>
                                                                                            <input type="number"
                                                                                                id="raw_quantity_{{ $index }}"
                                                                                                wire:model.live.debounce.300="selectedRawMaterials.{{ $index }}.quantity"
                                                                                                min="0.01"
                                                                                                step="0.01"
                                                                                                class="form-control form-control-sm"
                                                                                                style="padding:2px;height:30px;font-size: 0.9em;"
                                                                                                placeholder="{{ __('manufacturing::manufacturing.quantity') }}">
                                                                                        </td>
                                                                                        <td>
                                                                                            <input type="number"
                                                                                                id="raw_unit_cost_{{ $index }}"
                                                                                                value="{{ number_format($material['average_cost'] ?? 0, 2) }}"
                                                                                                readonly disabled
                                                                                                class="form-control form-control-sm cost-input bg-light"
                                                                                                style="padding:2px;height:30px;font-size: 0.9em;"
                                                                                                placeholder="{{ __('manufacturing::manufacturing.average_cost') }}"
                                                                                                title="{{ __('manufacturing::manufacturing.average_cost_lock_note') }}">
                                                                                        </td>
                                                                                        <td>
                                                                                            <input type="text"
                                                                                                value="{{ number_format($material['total_cost'] ?? 0, 2) }} {{ __('manufacturing::manufacturing.egp_symbol') }}"
                                                                                                class="form-control form-control-sm  bg-opacity-10  fw-bold"
                                                                                                style="padding:2px;height:30px;font-size: 0.9em;"
                                                                                                readonly>
                                                                                        </td>
                                                                                        <td class="text-center">
                                                                                            <button
                                                                                                wire:click="removeRawMaterial({{ $index }})"
                                                                                                class="btn btn-danger btn-sm"
                                                                                                style="height:30px;padding:2px 8px;">
                                                                                                <i
                                                                                                    class="fa fa-trash"></i>
                                                                                            </button>
                                                                                        </td>
                                                                                    </tr>
                                                                                @endforeach
                                                                            </tbody>
                                                                        </table>
                                                                    </div>
                                                                @endif
                                                            </div>
                                                        </div>
                                                    </div><!--end general chat-->
                                                        <!-- تاب المصروفات -->
                                                        
                                                        <div class="tab-pane fade {{ ($activeTab ?? 'general_chat') == 'group_chat' ? 'active show' : '' }}"
                                                            id="group_chat">
                                                            <div class="col-12">
                                                                <div class="card h-100">
                                                                    <div class="card-header  bg-opacity-10 border-0">
                                                                        <div
                                                                            class="d-flex justify-content-between align-items-center">

                                                                            <button wire:click="addExpense"
                                                                                class="btn btn-primary btn-sm">
                                                                                {{ __('manufacturing::manufacturing.add_expense') }}
                                                                            </button>
                                                                        </div>
                                                                    </div>

                                                                    <div class="bg-light rounded-3 p-3 mx-3 mb-3"
                                                                        style="max-height: 190px; overflow-y: auto; overflow-x: hidden;">
                                                                        @if (count($additionalExpenses) === 0)
                                                                            <div class="text-center py-5">
                                                                                <i class="fas fa-money-bill-wave text-muted mb-3"
                                                                                    style="font-size: 2rem;"></i>
                                                                                <h5 class="text-muted">
                                                                                    {{ __('manufacturing::manufacturing.no_additional_expenses') }}
                                                                                </h5>
                                                                                <p class="text-muted small">
                                                                                    {{ __('manufacturing::manufacturing.add_expenses_note') }}
                                                                                </p>
                                                                            </div>
                                                                        @else
                                                                            <div class="row">
                                                                                {{-- الجزء الخاص بإدخال المصروفات --}}
                                                                                <div class="col-md-8">
                                                                                    <table
                                                                                        class="table table-bordered table-sm">
                                                                                        <thead>
                                                                                            <tr>
                                                                                                <th>{{ __('manufacturing::manufacturing.amount') }}
                                                                                                </th>
                                                                                                <th>{{ __('manufacturing::manufacturing.account') }}
                                                                                                </th>
                                                                                                <th>{{ __('manufacturing::manufacturing.description') }}
                                                                                                </th>
                                                                                                <th>{{ __('manufacturing::manufacturing.delete') }}
                                                                                                </th>
                                                                                            </tr>
                                                                                        </thead>
                                                                                        <tbody
                                                                                            id="additional_expenses_table_body">
                                                                                            @foreach ($additionalExpenses as $index => $expense)
                                                                                                <tr>
                                                                                                    <td>
                                                                                                        <input
                                                                                                            type="number"
                                                                                                            wire:model.live.debounce.300="additionalExpenses.{{ $index }}.amount"
                                                                                                            min="0"
                                                                                                            step="0.01"
                                                                                                            placeholder="0.00"
                                                                                                            class="form-control form-control-sm @error('additionalExpenses.' . $index . '.amount') is-invalid @enderror"
                                                                                                            style="padding:2px;height:30px;">
                                                                                                        @error('additionalExpenses.'
                                                                                                            . $index .
                                                                                                            '.amount')
                                                                                                            <div class="invalid-feedback"
                                                                                                                style="font-size: 0.8em;">
                                                                                                                {{ $message }}
                                                                                                            </div>
                                                                                                        @enderror
                                                                                                    </td>
                                                                                                    <td>
                                                                                                        <select
                                                                                                            wire:model="additionalExpenses.{{ $index }}.account_id"
                                                                                                            class="form-control form-control-sm"
                                                                                                            style="padding:2px;height:30px;">
                                                                                                            @foreach ($expenseAccountList as $keyExpense => $valueExpense)
                                                                                                                <option
                                                                                                                    value="{{ $keyExpense }}">
                                                                                                                    {{ $valueExpense }}
                                                                                                                </option>
                                                                                                            @endforeach
                                                                                                        </select>
                                                                                                    </td>
                                                                                                    <td>
                                                                                                        <input
                                                                                                            type="text"
                                                                                                            wire:model="additionalExpenses.{{ $index }}.description"
                                                                                                            placeholder="{{ __('manufacturing::manufacturing.expense_description') }}"
                                                                                                            class="form-control form-control-sm @error('additionalExpenses.' . $index . '.description') is-invalid @enderror"
                                                                                                            style="padding:2px;height:30px;">
                                                                                                        @error('additionalExpenses.'
                                                                                                            . $index .
                                                                                                            '.description')
                                                                                                            <div class="invalid-feedback"
                                                                                                                style="font-size: 0.8em;">
                                                                                                                {{ $message }}
                                                                                                            </div>
                                                                                                        @enderror
                                                                                                    </td>
                                                                                                    <td
                                                                                                        class="text-center">
                                                                                                        <button
                                                                                                            wire:click="removeExpense({{ $index }})"
                                                                                                            class="btn btn-danger btn-sm"
                                                                                                            style="height:30px;padding:2px 8px;">
                                                                                                            <i
                                                                                                                class="fa fa-trash"></i>
                                                                                                        </button>
                                                                                                    </td>
                                                                                                </tr>
                                                                                            @endforeach
                                                                                        </tbody>
                                                                                    </table>
                                                                                </div>

                                                                                {{-- الجزء الخاص بإجمالي المصروفات --}}
                                                                                <div class="col-md-4">
                                                                                    <div class="card p-3 bg-light">
                                                                                        <h6 class="mb-3 fw-bold">
                                                                                            {{ __('manufacturing::manufacturing.total_additional_expenses') }}
                                                                                        </h6>
                                                                                        <p class="fs-5 text-success">
                                                                                            {{ number_format(collect($additionalExpenses)->map(fn($item) => (float) $item['amount'])->sum()) }}
                                                                                            {{ __('EGP') }}
                                                                                        </p>
                                                                                    </div>
                                                                                </div>
                                                                            </div>
                                                                        @endif
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div><!--end group chat-->
                                                  
                                                </div><!--end tab-content-->
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>


            {{-- ملخص التكاليف --}}
            <div class="row mt-4">
                <div class="col-12">
                    {{-- الجزء الأول: الإجماليات --}}
                    <div class="row gx-2 mb-3">
                        <div class="col-md-3">
                            <div class="card border-0 shadow-sm h-100">
                                <div class="card-body p-3 text-center">
                                    <div class="d-flex align-items-center justify-content-between">
                                        <div class="text-start flex-grow-1">
                                            <small class="text-muted d-block mb-1">{{ __('Total Raw Materials') }}</small>
                                            <h5 class="mb-0 text-info fw-bold">{{ number_format($totalRawMaterialsCost, 2) }}</h5>
                                            <small class="text-muted">{{ __('EGP') }}</small>
                                        </div>
                                        <div class="bg-info bg-opacity-10 rounded p-3">
                                            <i class="fas fa-box fa-2x text-info"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-3">
                            <div class="card border-0 shadow-sm h-100">
                                <div class="card-body p-3 text-center">
                                    <div class="d-flex align-items-center justify-content-between">
                                        <div class="text-start flex-grow-1">
                                            <small class="text-muted d-block mb-1">{{ __('Total Expenses') }}</small>
                                            <h5 class="mb-0 text-warning fw-bold">{{ number_format(collect($additionalExpenses)->sum(fn($item) => (float) $item['amount']), 2) }}</h5>
                                            <small class="text-muted">{{ __('EGP') }}</small>
                                        </div>
                                        <div class="bg-warning bg-opacity-10 rounded p-3">
                                            <i class="fas fa-receipt fa-2x text-warning"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-3">
                            <div class="card border-0 shadow-sm h-100">
                                <div class="card-body p-3 text-center">
                                    <div class="d-flex align-items-center justify-content-between">
                                        <div class="text-start flex-grow-1">
                                            <small class="text-muted d-block mb-1">{{ __('Total Invoice Cost') }}</small>
                                            <h5 class="mb-0 text-danger fw-bold">{{ number_format($totalManufacturingCost, 2) }}</h5>
                                            <small class="text-muted">{{ __('EGP') }}</small>
                                        </div>
                                        <div class="bg-danger bg-opacity-10 rounded p-3">
                                            <i class="fas fa-calculator fa-2x text-danger"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-3">
                            <div class="card border-0 shadow-sm h-100">
                                <div class="card-body p-3 text-center">
                                    <div class="d-flex align-items-center justify-content-between">
                                        <div class="text-start flex-grow-1">
                                            <small class="text-muted d-block mb-1">{{ __('Total Products Value') }}</small>
                                            <h5 class="mb-0 text-success fw-bold">{{ number_format($totalProductsCost, 2) }}</h5>
                                            <small class="text-muted">{{ __('EGP') }}</small>
                                        </div>
                                        <div class="bg-success bg-opacity-10 rounded p-3">
                                            <i class="fas fa-industry fa-2x text-success"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- الجزء الثاني: المعيار والانحراف --}}
                    <div class="row gx-2">
                        <div class="col-md-6">
                            <div class="card border-0 shadow-sm h-100">
                                <div class="card-body p-3">
                                    <div class="d-flex align-items-center justify-content-between">
                                        <div class="text-start flex-grow-1">
                                            <small class="text-muted d-block mb-1">{{ __('Standard Cost (Template)') }}</small>
                                            <h5 class="mb-0 text-primary fw-bold">{{ number_format($totalManufacturingCost, 2) }}</h5>
                                            <small class="text-muted">{{ __('EGP') }}</small>
                                        </div>
                                        <div class="bg-primary bg-opacity-10 rounded p-3">
                                            <i class="fas fa-star fa-2x text-primary"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="card border-0 shadow-sm h-100">
                                <div class="card-body p-3">
                                    @php
                                        $variance = $totalProductsCost - $totalManufacturingCost;
                                        $variancePercentage = $totalManufacturingCost > 0 
                                            ? ($variance / $totalManufacturingCost) * 100 
                                            : 0;
                                        $color = $variance >= 0 ? 'success' : 'danger';
                                        $icon = $variance >= 0 ? 'arrow-up' : 'arrow-down';
                                    @endphp
                                    <div class="d-flex align-items-center justify-content-between">
                                        <div class="text-start flex-grow-1">
                                            <small class="text-muted d-block mb-1">{{ __('Variance (Difference)') }}</small>
                                            <h5 class="mb-0 text-{{ $color }} fw-bold">
                                                <i class="fas fa-{{ $icon }} me-1"></i>
                                                {{ number_format(abs($variance), 2) }}
                                            </h5>
                                            <small class="text-muted">{{ __('EGP') }}</small>
                                            <span class="badge bg-{{ $color }} ms-2">{{ number_format(abs($variancePercentage), 2) }}%</span>
                                        </div>
                                        <div class="bg-{{ $color }} bg-opacity-10 rounded p-3">
                                            <i class="fas fa-exchange-alt fa-2x text-{{ $color }}"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>
</div>
</div>
@endif
</div>
@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
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
                    confirmButtonText: '{{ __('OK') }}'
                }).then(() => {
                    if (d.reload) location.reload();
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
