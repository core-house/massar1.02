<div>
    <div class="card">
        <div class="card-header">
            <h4 class="card-title">{{ __('installments::installments.edit_installment_plan') }}</h4>
        </div>
        <div class="card-body">
            @if ($paidPaymentsCount > 0)
                <div class="alert alert-warning" role="alert">
                    <i class="fas fa-exclamation-triangle"></i>
                    <strong>{{ __('installments::installments.warning') }}:</strong> {{ __('installments::installments.plan_contains_paid_installments', ['count' => $paidPaymentsCount]) }}. 
                    {{ __('installments::installments.editing_affects_unpaid_only') }}.
                </div>
            @endif

            <form wire:submit.prevent="update">
                <!-- Client Selection -->
                <div class="mb-3">
                    <label for="acc_head_id" class="form-label">{{ __('installments::installments.client') }} <span class="text-danger">*</span></label>
                    <select wire:model.live="acc_head_id" id="acc_head_id" class="form-select select2-client"
                        @error('acc_head_id') is-invalid @enderror>
                        <option value="">{{ __('installments::installments.select_client') }}</option>
                        @foreach ($clients as $client)
                            <option value="{{ $client['id'] }}">{{ $client['code'] }} - {{ $client['aname'] }}</option>
                        @endforeach
                    </select>
                    @error('acc_head_id')
                        <div class="invalid-feedback d-block">{{ $message }}</div>
                    @enderror
                </div>

                <!-- Account Balance Info -->
                @if ($acc_head_id)
                    <div class="alert alert-info">
                        <div class="row">
                            <div class="col-md-4">
                                <strong>{{ __('installments::installments.total_account_balance') }}:</strong> {{ number_format($accountBalance, 2) }}
                            </div>
                            <div class="col-md-4">
                                <strong>{{ __('installments::installments.existing_plans') }}:</strong> {{ number_format($existingPlansTotal, 2) }}
                            </div>
                            <div class="col-md-4">
                                <strong>{{ __('installments::installments.available_balance') }}:</strong>
                                <span class="badge bg-{{ $availableBalance > 0 ? 'success' : 'danger' }}">
                                    {{ number_format($availableBalance, 2) }}
                                </span>
                            </div>
                        </div>
                    </div>
                @endif

                <div class="row">
                    <!-- Total Amount -->
                    <div class="col-md-3 mb-3">
                        <label for="total_amount" class="form-label">{{ __('installments::installments.total_amount') }} <span
                                class="text-danger">*</span></label>
                        <input type="number" step="0.01" wire:model.live.debounce.300ms="total_amount" id="total_amount"
                            class="form-control @error('total_amount') is-invalid @enderror">
                        @error('total_amount')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Down Payment -->
                    <div class="col-md-3 mb-3">
                        <label for="down_payment" class="form-label">{{ __('installments::installments.down_payment') }} <span
                                class="text-danger">*</span></label>
                        <input type="number" step="0.01" wire:model.live.debounce.500ms="down_payment" id="down_payment"
                            class="form-control @error('down_payment') is-invalid @enderror">
                        @error('down_payment')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Interest Type -->
                    <div class="col-md-2 mb-3">
                        <label class="form-label">{{ __('installments::installments.interest_type') }}</label>
                        <select wire:model.live="interestType" class="form-select">
                            <option value="fixed">{{ __('installments::installments.fixed_amount') }}</option>
                            <option value="percentage">{{ __('installments::installments.percentage') }}</option>
                        </select>
                    </div>

                    <!-- Interest Value -->
                    <div class="col-md-2 mb-3">
                        <label for="interestValue" class="form-label">
                            {{ __('installments::installments.interest_value') }}
                            @if($interestType === 'percentage') (%) @endif
                        </label>
                        <input type="number" step="0.01" wire:model.live.debounce.500ms="interestValue" id="interestValue"
                            class="form-control" placeholder="0">
                    </div>

                    <!-- Amount to be Installed -->
                    <div class="col-md-2 mb-3">
                        <label for="amount_to_be_installed" class="form-label">{{ __('installments::installments.amount_to_be_installed') }} <span
                                class="text-danger">*</span></label>
                        <input type="number" step="0.01" wire:model="amount_to_be_installed" id="amount_to_be_installed"
                            class="form-control bg-light @error('amount_to_be_installed') is-invalid @enderror" readonly>
                        @error('amount_to_be_installed')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        @if ($amount_to_be_installed > $availableBalance && $acc_head_id)
                            <div class="text-danger mt-1">
                                <i class="fas fa-exclamation-circle"></i>
                                {{ __('installments::installments.amount_greater_than_available') }}
                            </div>
                        @endif
                    </div>
                </div>

                <div class="row">
                    <!-- Number of Installments -->
                    <div class="col-md-4 mb-3">
                        <label for="number_of_installments" class="form-label">{{ __('installments::installments.number_of_installments') }} <span
                                class="text-danger">*</span></label>
                        <input type="number" wire:model.live.debounce.300ms="number_of_installments" id="number_of_installments"
                            class="form-control @error('number_of_installments') is-invalid @enderror">
                        @error('number_of_installments')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Start Date -->
                    <div class="col-md-4 mb-3">
                        <label for="start_date" class="form-label">{{ __('installments::installments.start_date') }} <span
                                class="text-danger">*</span></label>
                        <input type="datetime-local" wire:model="start_date" id="start_date"
                            class="form-control @error('start_date') is-invalid @enderror">
                        @error('start_date')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Interval Type -->
                    <div class="col-md-4 mb-3">
                        <label for="interval_type" class="form-label">{{ __('installments::installments.interval_type') }} <span
                                class="text-danger">*</span></label>
                        <select wire:model="interval_type" id="interval_type"
                            class="form-select @error('interval_type') is-invalid @enderror">
                            <option value="daily">{{ __('installments::installments.daily') }}</option>
                            <option value="weekly">{{ __('installments::installments.weekly') }}</option>
                            <option value="monthly">{{ __('installments::installments.monthly') }}</option>
                            <option value="yearly">{{ __('installments::installments.yearly') }}</option>
                        </select>
                        @error('interval_type')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="d-flex justify-content-between">
                    <a href="{{ route('installments.plans.show', $plan->id) }}" class="btn btn-secondary">
                        <i class="fas fa-arrow-right"></i> {{ __('installments::installments.back') }}
                    </a>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i> {{ __('installments::installments.save_changes') }}
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
    <script>
        document.addEventListener('livewire:initialized', () => {
            // Initialize Select2
            $('.select2-client').select2({
                dir: '{{ app()->getLocale() == 'ar' ? 'rtl' : 'ltr' }}',
                language: '{{ app()->getLocale() }}',
                placeholder: '{{ __('installments::installments.search_client') }}',
                allowClear: true
            });

            // Update Livewire when Select2 changes
            $('.select2-client').on('change', function() {
                @this.set('acc_head_id', $(this).val());
            });

            // Listen for validation errors
            Livewire.on('validation-error', (data) => {
                const d = Array.isArray(data) ? data[0] : data;
                Swal.fire({
                    icon: 'error',
                    title: d.title || '{{ __('installments::installments.error') }}',
                    text: d.text,
                    toast: true,
                    position: 'top-end',
                    showConfirmButton: false,
                    timer: 5000,
                    timerProgressBar: true,
                });
            });

            // Listen for plan updated
            Livewire.on('plan-updated', (data) => {
                const d = Array.isArray(data) ? data[0] : data;
                Swal.fire({
                    icon: 'success',
                    title: d.title || '{{ __('installments::installments.success') }}',
                    text: d.text,
                    showCancelButton: true,
                    confirmButtonText: '{{ __('installments::installments.view_plan') }}',
                    cancelButtonText: '{{ __('installments::installments.stay_here') }}',
                    confirmButtonColor: '#28a745',
                    cancelButtonColor: '#6c757d',
                }).then((result) => {
                    if (result.isConfirmed) {
                        window.location.href = '/installments/plans/' + d.planId;
                    }
                });
            });
        });
    </script>
@endpush
