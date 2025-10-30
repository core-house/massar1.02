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

<<<<<<< Updated upstream
                    <div class="col-md-2 mb-3">
=======
                    <!-- Project Size -->
                    <div class="col-md-1 mb-3">
>>>>>>> Stashed changes
                        <label class="form-label fw-bold">{{ __('Project Size') }}</label>
                        <select wire:model="projectSize" class="form-select">
                            <option value="">{{ __('Select...') }}</option>
                            @foreach ($projectSizeOptions as $id => $name)
                                <option value="{{ $id }}">{{ $name }}</option>
                            @endforeach
                        </select>
                        @error('projectSize')
                            <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>

<<<<<<< Updated upstream
                    <div class="col-md-2 mb-3">
=======
                    <!-- KON Priority -->
                    <div class="col-md-1 mb-3">
>>>>>>> Stashed changes
                        <label class="form-label fw-bold">{{ __('KON Priority') }}</label>
                        <select wire:model="konPriority" class="form-select">
                            <option value="">{{ __('Select...') }}</option>
                            @foreach ($konPriorityOptions as $option)
                                <option value="{{ $option }}">{{ $option }}</option>
                            @endforeach
                        </select>
                        @error('konPriority')
                            <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>

<<<<<<< Updated upstream
                    <div class="col-md-2 mb-3">
=======
                    <!-- Client Priority -->
                    <div class="col-md-1 mb-3">
>>>>>>> Stashed changes
                        <label class="form-label fw-bold">{{ __('Client Priority') }}</label>
                        <select wire:model="clientPriority" class="form-select">
                            <option value="">{{ __('Select...') }}</option>
                            @foreach ($clientPriorityOptions as $option)
                                <option value="{{ $option }}">{{ $option }}</option>
                            @endforeach
                        </select>
                        @error('clientPriority')
                            <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>
<<<<<<< Updated upstream
=======

                    <!-- Pricing Status -->
                    <div class="col-md-2 mb-3">
                        <label class="form-label fw-bold">{{ __('Pricing Status') }}</label>
                        <select wire:model.live="quotationState" class="form-select">
                            <option value="">{{ __('Select status...') }}</option>
                            @foreach ($quotationStateOptions as $state)
                                <option value="{{ $state->value }}">{{ $state->label() }}</option>
                            @endforeach
                        </select>
                        @error('quotationState')
                            <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>

                    <!-- Status Reason (if rejected or re-estimation) -->
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

                    <!-- Assigned Engineer (from ContactSelector) -->
                    <div class="col-md-2 mb-3">
                        <label class="form-label fw-bold">{{ __('Assigned Engineer') }}</label>
                        @if (!empty($this->contactSelectors['engineer']['primary']))
                            @php
                                $engineerId = $this->contactSelectors['engineer']['primary'];
                                $engineer = \Modules\Inquiries\Models\Contact::find($engineerId);
                            @endphp
                            @if ($engineer)
                                <div class="bg-light p-2 rounded small">
                                    <strong>{{ Str::limit($engineer->name, 20) }}</strong>
                                    @if ($engineer->phone)
                                        <br><small><i class="fas fa-phone"></i> {{ $engineer->phone }}</small>
                                    @endif
                                </div>
                            @endif
                        @else
                            <span class="text-muted small">{{ __('Not assigned') }}</span>
                        @endif
                    </div>

                    <!-- Assign Engineer Date -->
                    <div class="col-md-2 mb-3">
                        <label class="form-label fw-bold">{{ __('Assign Date') }}</label>
                        <input type="date" wire:model="assignEngineerDate" class="form-control">
                        @error('assignEngineerDate')
                            <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>

                    <!-- Client (from ContactSelector) - Optional: show primary client -->
                    <div class="col-md-2 mb-3">
                        <label class="form-label fw-bold">{{ __('Client') }}</label>
                        @if (!empty($this->contactSelectors['client']['primary']))
                            @php
                                $clientId = $this->contactSelectors['client']['primary'];
                                $client = \Modules\Inquiries\Models\Contact::find($clientId);
                            @endphp
                            @if ($client)
                                <div class="bg-light p-2 rounded small">
                                    <strong>{{ Str::limit($client->name, 20) }}</strong>
                                    @if ($client->phone)
                                        <br><small><i class="fas fa-phone"></i> {{ $client->phone }}</small>
                                    @endif
                                </div>
                            @endif
                        @else
                            <span class="text-muted small">{{ __('Not selected') }}</span>
                        @endif
                    </div>

>>>>>>> Stashed changes
                </div>
            </div>
        </div>
    </div>
</div>
