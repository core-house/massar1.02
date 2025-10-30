<!-- Quotation State Section -->
<div class="row mb-4">
    <div class="col-12">
        <div class="card border-info">
            <div class="card-header">
                <h6 class="card-title mb-0">
                    <i class="fas fa-file-invoice me-2"></i>
                    {{ __('Quotation State') }}
                </h6>
                <small class="d-block mt-1">{{ __('Select quotation state') }}</small>
            </div>
            <div class="card-body">
                <div class="row">

                    <div class="col-md-1 mb-3">
                        <label class="form-label fw-bold">{{ __('Project Size') }}</label>
                        <select wire:model="projectSize" class="form-select">
                            <option value="">{{ __('Select project size...') }}</option>
                            @foreach ($projectSizeOptions as $id => $name)
                                <option value="{{ $id }}">{{ $name }}</option>
                            @endforeach
                        </select>
                        @error('projectSize')
                            <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="col-md-1 mb-3">
                        <label class="form-label fw-bold">{{ __('KON Priority') }}</label>
                        <select wire:model="konPriority" class="form-select">
                            <option value="">{{ __('Select KON priority...') }}</option>
                            @foreach ($konPriorityOptions as $option)
                                <option value="{{ $option }}">
                                    {{ $option }}</option>
                            @endforeach
                        </select>
                        @error('konPriority')
                            <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="col-md-1 mb-3">
                        <label class="form-label fw-bold">{{ __('Client Priority') }}</label>
                        <select wire:model="clientPriority" class="form-select">
                            <option value="">{{ __('Select priority...') }}</option>
                            @foreach ($clientPriorityOptions as $option)
                                <option value="{{ $option }}">
                                    {{ $option }}</option>
                            @endforeach
                        </select>
                        @error('clientPriority')
                            <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="col-md-2 mb-3">
                        <label class="form-label fw-bold">{{ __('Pricing Status') }}</label>
                        <select wire:model.live="quotationState" class="form-select">
                            <option value="">{{ __('Select status...') }}</option>
                            @foreach ($quotationStateOptions as $state)
                                <option value="{{ $state->value }}">
                                    {{ $state->label() }}</option>
                            @endforeach
                        </select>
                        @error('quotationState')
                            <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>

                    @if (in_array($this->quotationState, [
                            \Modules\Inquiries\Enums\QuotationStateEnum::REJECTED->value,
                            \Modules\Inquiries\Enums\QuotationStateEnum::RE_ESTIMATION->value,
                        ]))
                        <div class="col-md-2 mb-3">
                            <label class="form-label fw-bold">{{ __('Status Reason') }}</label>
                            <input type="text" wire:model.live="quotationStateReason" class="form-control"
                                placeholder="{{ __('Enter reason...') }}">
                            @error('quotationStateReason')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>
                    @endif

                    <!-- المهندس -->
                    <div class="col-md-3 mb-2 d-flex flex-column">
                        <label class="form-label fw-bold">{{ __('Engineer') }}</label>
                        <div class="card-body text-center p-2">
                            <div class="d-flex gap-1 align-items-center mb-1">
                                <livewire:app::searchable-select :model="App\Models\Client::class" label-field="cname"
                                    wire-model="assignedEngineer"
                                    placeholder="{{ __('Search for engineer or add new...') }}" :selected-id="$assignedEngineer"
                                    :key="'engineer-select'" />
                                <button type="button" class="btn btn-sm btn-dark" wire:click="openClientModal(5)"
                                    title="{{ __('Add New Engineer') }}">
                                    <i class="fas fa-plus"></i>
                                </button>
                            </div>

                            @if ($assignedEngineer)
                                @php
                                    $engineer = \App\Models\Client::find($assignedEngineer);
                                @endphp
                                @if ($engineer)
                                    <div class="card mt-2 bg-light">
                                        <div class="card-body p-2 text-start">
                                            <small class="d-block mb-0"><strong>{{ __('Name') }}:</strong>
                                                {{ $engineer->cname }}</small>
                                            @if ($engineer->phone)
                                                <small class="d-block mb-0"><strong>{{ __('Phone') }}:</strong>
                                                    {{ $engineer->phone }}</small>
                                            @endif
                                            @if ($engineer->email)
                                                <small class="d-block mb-0"><strong>{{ __('Email') }}:</strong>
                                                    {{ $engineer->email }}</small>
                                            @endif
                                            @if ($engineer->address)
                                                <small class="d-block mb-0"><strong>{{ __('Address') }}:</strong>
                                                    {{ $engineer->address }}</small>
                                            @endif
                                        </div>
                                    </div>
                                @endif
                            @endif
                        </div>
                    </div>

                    {{-- assign engineer date --}}
                    <div class="col-md-2 mb-3">
                        <label class="form-label fw-bold">{{ __('Assign Engineer Date') }}</label>
                        <input type="date" wire:model="assignEngineerDate" class="form-control">
                        @error('assignEngineerDate')
                            <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
