@if(!$hasVaribals)
<fieldset class="shadow-sm mb-2 p-2" style="border: 2px solid #80e6cb; border-radius: 0.5rem;">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <div class="d-flex align-items-center gap-2">
            <h6 class="font-hold fw-bold mb-0">{{ __('items.item_units') }}</h6>
            <button type="button"
                class="btn btn-outline-success btn-sm font-hold fw-bold"
                wire:click="openModal('unit')"
                title="{{ __('items.create_new_unit') }}">
                <i class="las la-plus"></i> {{ __('items.create_new_unit') }}
            </button>
        </div>
        <button type="button" class="btn btn-main btn-sm font-hold fw-bold"
            wire:click="addUnitRow">
            <i class="las la-plus"></i> {{ __('items.add_unit_to_item') }}
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
                    @foreach ($unitRows as $index => $unitRow)
                        <tr>
                            <td class="font-hold fw-bold font-14 text-center">
                                <select wire:model.live="unitRows.{{ $index }}.unit_id"
                                    class="form-select font-hold fw-bold font-14"
                                    style="min-width: 100px; height: 50px;">
                                    <option class="font-hold fw-bold" value="">
                                        {{ __('common.select') }}</option>
                                    @foreach ($units as $unit)
                                        <option class="font-hold fw-bold"
                                            value="{{ $unit->id }}">
                                            {{ translateDynamicValue($unit->name) }}
                                        </option>
                                    @endforeach
                                </select>
                                @error("unitRows.{$index}.unit_id")
                                    <span class="text-danger font-hold fw-bold">{{ $message }}</span>
                                @enderror
                            </td>
                            <td class="text-center">
                                <input type="number" onclick="this.select()"
                                    wire:model="unitRows.{{ $index }}.u_val"
                                    onkeyup="window.updateUnitsCostAndPrices({{ $index }})"
                                    class="form-control font-hold fw-bold" min="1"
                                    step="0.0001" style="min-width: 150px;">
                                @error("unitRows.{$index}.u_val")
                                    <span class="text-danger font-hold fw-bold">{{ $message }}</span>
                                @enderror
                            </td>
                            <td>
                                <input type="number" onclick="this.select()"
                                    disabled readonly
                                    wire:model="unitRows.{{ $index }}.cost"
                                    onkeyup="window.updateUnitsCost({{ $index }})"
                                    class="form-control font-hold fw-bold" step="0.0001"
                                    style="min-width: 150px;">
                                @error("unitRows.{$index}.cost")
                                    <span class="text-danger font-hold fw-bold">{{ $message }}</span>
                                @enderror
                            </td>
                            @foreach ($prices as $price)
                                <td class="text-center">
                                    <input type="number" onclick="this.select()"
                                        wire:model="unitRows.{{ $index }}.prices.{{ $price->id }}"
                                        class="form-control font-hold fw-bold" step="0.0001"
                                        style="min-width: 150px;">
                                    @error("unitRows.{$index}.prices.{$price->id}")
                                        <span class="text-danger font-hold fw-bold">{{ $message }}</span>
                                    @enderror
                                </td>
                            @endforeach
                            <td class="d-flex text-center flex-column gap-1 mt-4">
                                <input type="text" onclick="this.select()"
                                    @if (!$creating) disabled readonly @endif
                                    wire:model.live="unitRows.{{ $index }}.barcodes.0"
                                    class="form-control font-hold fw-bold" maxlength="25"
                                    style="min-width: 150px;">
                                @if ($creating)
                                    <button type="button"
                                        class="btn btn-main btn-sm font-hold fw-bold"
                                        wire:click="addAdditionalBarcode({{ $index }})">
                                        <i class="las la-plus"></i> {{ __('items.additional_barcode') }}
                                    </button>
                                @endif
                                @if (!$creating)
                                    <button type="button"
                                        class="btn btn-main btn-sm font-hold fw-bold"
                                        wire:click="showBarcodes({{ $index }})">
                                        <i class="las la-eye"></i> {{ __('items.view_barcode') }}
                                    </button>
                                @endif
                                @error("unitRows.{$index}.barcodes.{$index}")
                                    <span class="text-danger font-hold fw-bold font-12">{{ $message }}</span>
                                @enderror
                            </td>
                            <td class="font-hold fw-bold font-14 text-center">
                                @if ($creating)
                                    <button type="button"
                                        class="btn btn-danger btn-icon-square-sm float-end"
                                        wire:click="removeUnitRow({{ $index }})">
                                        <i class="far fa-trash-alt"></i>
                                    </button>
                                @endif
                            </td>
                        </tr>
                        @include('livewire.item-management.items.partials.unit-barcodes-modal', ['index' => $index, 'unitRow' => $unitRow])
                    @endforeach
                </tbody>
            </table>
        </div>
</fieldset>
@endif