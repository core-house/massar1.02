@if($hasVaribals && $activeCombination)
    <fieldset class="shadow-sm mt-2">
        <div class="col-md-12 p-2">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h6 class="font-hold fw-bold mb-0">{{ __('items.combination_units') }}: {{ $this->getCombinationDisplayName($activeCombination) }}</h6>
                <button type="button" class="btn btn-main btn-sm font-hold fw-bold"
                    wire:click="addCombinationUnitRow('{{ $activeCombination }}')">
                    <i class="las la-plus"></i> {{ __('items.add_unit_to_combination') }}
                </button>
            </div>
            <div class="table-responsive" style="overflow-x: auto;">
                <table class="table table-striped mb-0" style="min-width: 1200px;">
                    <thead class="table-light text-center align-middle">
                        <tr>
                            <th class="font-hold text-center fw-bold">{{ __('items.unit') }}</th>
                            <th class="font-hold text-center fw-bold">{{ __('items.conversion_factor') }}</th>
                            <th class="font-hold text-center fw-bold">{{ __('items.cost') }}</th>
                            @foreach ($prices as $price)
                                <th class="font-hold fw-bold">{{ translateDynamicValue($price->name) }}</th>
                            @endforeach
                            <th class="font-hold text-center fw-bold">{{ __('items.barcode') }}</th>
                            <th class="font-hold text-center fw-bold">{{ __('common.actions') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($this->getActiveCombinationUnitRows() as $index => $unitRow)
                            <tr wire:key="combination-{{ $activeCombination }}-unit-{{ $index }}">
                                <td class="font-hold fw-bold font-14 text-center">
                                    <select wire:model.live="combinationUnitRows.{{ $activeCombination }}.{{ $index }}.unit_id"
                                        class="form-select font-hold fw-bold font-14"
                                        style="min-width: 100px; height: 50px;">
                                        <option class="font-hold fw-bold" value="">إختر</option>
                                        @foreach ($units as $unit)
                                            <option class="font-hold fw-bold" value="{{ $unit->id }}">
                                                {{ translateDynamicValue($unit->name) }}
                                            </option>
                                        @endforeach
                                    </select>
                                </td>
                                <td class="text-center">
                                    <input type="number" onclick="this.select()"
                                        wire:model="combinationUnitRows.{{ $activeCombination }}.{{ $index }}.u_val"
                                        onkeyup="window.updateCombinationUnitsCostAndPrices('{{ $activeCombination }}', {{ $index }})"
                                        class="form-control font-hold fw-bold" min="1"
                                        step="0.0001" style="min-width: 150px;">
                                </td>
                                <td>
                                    <input type="number" onclick="this.select()"
                                        wire:model="combinationUnitRows.{{ $activeCombination }}.{{ $index }}.cost"
                                        onkeyup="window.updateCombinationUnitsCost('{{ $activeCombination }}', {{ $index }})"
                                        class="form-control font-hold fw-bold" step="0.0001"
                                        style="min-width: 150px;">
                                </td>
                                @foreach ($prices as $price)
                                    <td class="text-center">
                                        <input type="number" onclick="this.select()"
                                            wire:model="combinationUnitRows.{{ $activeCombination }}.{{ $index }}.prices.{{ $price->id }}"
                                            onkeyup="window.updateCombinationPrices('{{ $activeCombination }}', {{ $index }}, {{ $price->id }})"
                                            class="form-control font-hold fw-bold" step="0.0001"
                                            style="min-width: 150px;">
                                    </td>
                                @endforeach
                                <td class="text-center">
                                    <div class="barcode-container">
                                        @if(isset($this->combinationUnitRows[$activeCombination][$index]['barcodes']) && count($this->combinationUnitRows[$activeCombination][$index]['barcodes']) > 0)
                                            <div class="barcode-input-group mb-2">
                                                <div class="input-group">
                                                    <span class="input-group-text bg-light border-end-0">
                                                        <i class="las la-key text-success"></i>
                                                    </span>
                                                    <input type="text" 
                                                           onclick="this.select()"
                                                           wire:model.live="combinationUnitRows.{{ $activeCombination }}.{{ $index }}.barcodes.0"
                                                           class="form-control font-hold fw-bold barcode-input" 
                                                           maxlength="25"
                                                           placeholder="{{ __('items.main_barcode') }}"
                                                           style="min-width: 120px;">
                                                    <span class="input-group-text bg-light border-start-0">
                                                        <i class="las la-barcode text-primary"></i>
                                                    </span>
                                                </div>
                                            </div>
                                        @endif
                                        
                                        <div class="mt-2">
                                            @if($activeCombination)
                                                <button type="button" 
                                                        class="btn btn-outline-primary btn-sm w-100"
                                                        wire:click="openBarcodeModal('{{ $activeCombination }}', {{ $index }})"
                                                        title="{{ __('items.manage_additional_barcodes') }}">
                                                    <i class="las la-barcode me-1"></i>
                                                    <span class="font-hold fw-bold">{{ __('items.manage_barcodes') }}</span>
                                                </button>
                                            @else
                                                <small class="text-muted font-hold">
                                                    <i class="las la-info-circle me-1"></i>
                                                    {{ __('items.select_combination_first') }}
                                                </small>
                                            @endif
                                        </div>
                                    </div>
                                </td>
                                <td class="font-hold fw-bold font-14 text-center">
                                    <button type="button"
                                        class="btn btn-danger btn-icon-square-sm float-end"
                                        wire:click="removeCombinationUnitRow('{{ $activeCombination }}', {{ $index }})">
                                        <i class="far fa-trash-alt"></i>
                                    </button>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </fieldset>
@endif


