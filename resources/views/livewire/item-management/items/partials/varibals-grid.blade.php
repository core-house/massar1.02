@if ($hasVaribals)
    <div class="col-md-12 mb-3">
        <div class="card">
            <div class="card-header bg-light">
                <h6 class="font-hold fw-bold mb-0">{{ __('items.select_item_variations') }}</h6>
            </div>
            <div class="card-body">
                @if($varibals->count() > 0)
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label font-hold fw-bold">{{ __('items.select_variable_for_rows') }}:</label>
                            <select wire:model.live="selectedRowVaribal" class="form-select font-hold fw-bold">
                                <option value="">{{ __('items.select_variable_for_rows') }}</option>
                                @foreach($varibals as $varibal)
                                    <option value="{{ $varibal->id }}">{{ translateDynamicValue($varibal->name) }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label font-hold fw-bold">{{ __('items.select_variable_for_columns') }}:</label>
                            <select wire:model.live="selectedColVaribal" class="form-select font-hold fw-bold">
                                <option value="">{{ __('items.select_variable_for_columns') }}</option>
                                @foreach($varibals as $varibal)
                                    @if($varibal->id != $selectedRowVaribal)
                                        <option value="{{ $varibal->id }}">{{ translateDynamicValue($varibal->name) }}</option>
                                    @endif
                                @endforeach
                            </select>
                        </div>
                    </div>

                    @if($selectedRowVaribal && $selectedColVaribal)
                        <div class="table-responsive">
                            <table class="table table-bordered varibal-grid-table">
                                <thead>
                                    <tr>
                                        <th class="bg-primary text-white font-hold fw-bold" style="width: 100px;"></th>
                                        @foreach($this->getColVaribalValues() as $colValue)
                                            <th class="text-center bg-primary text-white font-hold fw-bold">
                                                {{ $colValue->value }}
                                            </th>
                                        @endforeach
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($this->getRowVaribalValues() as $rowValue)
                                        <tr>
                                            <th class="text-center bg-light font-hold fw-bold" style="width: 100px;">
                                                {{ $rowValue->value }}
                                            </th>
                                            @foreach($this->getColVaribalValues() as $colValue)
                                                <td class="text-center p-2">
                                                    @php
                                                        $isSelected = $this->isVaribalCombinationSelected($selectedRowVaribal, $rowValue->id, $selectedColVaribal, $colValue->id);
                                                    @endphp
                                                    <input type="checkbox" class="form-check-input varibal-checkbox" 
                                                           id="row_{{ $rowValue->id }}_col_{{ $colValue->id }}"
                                                           wire:click="toggleVaribalCombination({{ $selectedRowVaribal }}, {{ $rowValue->id }}, {{ $selectedColVaribal }}, {{ $colValue->id }})"
                                                           @if($isSelected) checked @endif>
                                                </td>
                                            @endforeach
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="alert alert-info text-center">
                            <i class="las la-info-circle"></i>
                            {{ __('items.please_select_two_variables_to_build_grid') }}
                        </div>
                    @endif

                    <div class="mt-3">
                        <h6 class="font-hold fw-bold">{{ __('items.add_new_combination') }}:</h6>
                        <div class="row">
                            <div class="col-md-8">
                                <input type="text" wire:model="newCombinationText" class="form-control font-hold fw-bold" 
                                       placeholder="{{ __('items.enter_combination_placeholder') }}" wire:keydown.enter="addTextCombination">
                            </div>
                            <div class="col-md-4">
                                <button type="button" class="btn btn-success font-hold fw-bold" wire:click="addTextCombination" wire:loading.attr="disabled" wire:target="addTextCombination">
                                    <i class="las la-plus"></i> {{ __('items.add_combination') }}
                                </button>
                            </div>
                        </div>
                    </div>

                    @if(count($selectedVaribalCombinations) > 0)
                        <div class="mt-3">
                            <h6 class="font-hold fw-bold">{{ __('items.selected_combinations') }}:</h6>
                            <div class="row">
                                @foreach($this->getVaribalCombinations() as $index => $combination)
                                    <div class="col-md-3 mb-2" wire:key="combination-card-{{ $combination['key'] }}">
                                        <div class="card card-body p-2 bg-light combination-card">
                                            @if($editingCombinationKey === $combination['key'])
                                                <div class="d-flex flex-column gap-2">
                                                    <input type="text" wire:model="editingCombinationText" class="form-control form-control-sm font-hold fw-bold" placeholder="{{ __('items.enter_updated_combination') }}">
                                                    <div class="d-flex gap-1">
                                                        <button type="button" class="btn btn-success btn-sm font-hold fw-bold" wire:click="saveEditingCombination" wire:loading.attr="disabled" wire:target="saveEditingCombination">
                                                            <i class="las la-check"></i> {{ __('common.save') }}
                                                        </button>
                                                        <button type="button" class="btn btn-secondary btn-sm font-hold fw-bold" wire:click="cancelEditingCombination">
                                                            <i class="las la-times"></i> {{ __('common.cancel') }}
                                                        </button>
                                                    </div>
                                                </div>
                                            @else
                                                <div class="d-flex justify-content-between align-items-center">
                                                    <small class="font-hold flex-grow-1 me-2">
                                                        <span class="badge bg-primary">{{ $combination['display'] }}</span>
                                                    </small>
                                                    <div class="d-flex gap-1">
                                                        <button type="button" class="btn btn-info btn-sm" wire:click="selectCombination('{{ $combination['key'] }}')" title="{{ __('items.manage_units_for_combination') }}">
                                                            <i class="las la-cog"></i>
                                                        </button>
                                                        @if($combination['type'] === 'text')
                                                            <button type="button" class="btn btn-warning btn-sm" wire:click="startEditingCombination('{{ $combination['key'] }}')" title="{{ __('items.edit_combination') }}">
                                                                <i class="las la-edit"></i>
                                                            </button>
                                                            <button type="button" class="btn btn-danger btn-sm" wire:click="removeCombination('{{ $combination['key'] }}')" wire:loading.attr="disabled" wire:target="removeCombination" title="{{ __('items.delete_combination') }}">
                                                                <i class="las la-times"></i>
                                                            </button>
                                                        @endif
                                                    </div>
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif
                @else
                    <div class="alert alert-info text-center">
                        <i class="las la-info-circle"></i>
                        {{ __('items.no_variables_available') }}
                    </div>
                @endif
            </div>
        </div>
    </div>
@endif


