<form wire:submit.prevent="save">
    <div class="card">
        <div class="card-header">
            <h4 class="card-title">{{ __('installments::installments.installment_setup') }}</h4>
        </div>
        <div class="card-body">
            <div class="row">
                <!-- Client Selection with Select2 -->
                <div class="col-md-4 mb-3">
                    <label for="accHeadId" class="form-label">{{ __('installments::installments.client_account') }}</label>
                    <select wire:model="accHeadId" id="accHeadId"
                        class="form-select select2-client @error('accHeadId') is-invalid @enderror">
                        <option value="">{{ __('installments::installments.select_client') }}</option>
                        @foreach ($clients as $client)
                            <option value="{{ $client->id }}">{{ $client->code }} - {{ $client->aname }}</option>
                        @endforeach
                    </select>
                    @error('accHeadId')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <!-- Account Balance Display -->
                @if ($accHeadId)
                    <div class="col-md-8 mb-3">
                        <div class="alert {{ $showBalanceWarning ? 'alert-danger' : 'alert-info' }} mb-0">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="d-flex justify-content-between align-items-center mb-2">
                                        <span class="fw-bold">{{ __('installments::installments.total_balance') }}:</span>
                                        <span class="fs-5 fw-bold">{{ number_format($accountBalance, 2) }}
                                            {{ __('installments::installments.egp') }}</span>
                                    </div>
                                    @if (count($existingPlans) > 0)
                                        <div class="d-flex justify-content-between align-items-center mb-2">
                                            <span class="fw-bold">{{ __('installments::installments.existing_plans') }}
                                                ({{ count($existingPlans) }}):</span>
                                            <span class="fs-5 fw-bold text-warning">-
                                                {{ number_format($totalInstallmentPlans, 2) }}
                                                {{ __('installments::installments.egp') }}</span>
                                        </div>
                                    @endif
                                </div>
                                <div class="col-md-6">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <span class="fw-bold">{{ __('installments::installments.available_for_installments') }}:</span>
                                        <span
                                            class="fs-4 fw-bold {{ $availableBalance > 0 ? 'text-success' : 'text-danger' }}">
                                            {{ number_format($availableBalance, 2) }} {{ __('installments::installments.egp') }}
                                        </span>
                                    </div>
                                </div>
                            </div>

                            @if ($showBalanceWarning)
                                <div class="mt-2 text-danger">
                                    <i class="fas fa-exclamation-triangle"></i>
                                    {{ __('installments::installments.cannot_create_insufficient_balance') }}
                                </div>
                            @endif

                            @if (count($existingPlans) > 0)
                                <div class="mt-3">
                                    <button type="button" class="btn btn-sm btn-outline-info"
                                        onclick="showExistingPlans()">
                                        <i class="fas fa-info-circle"></i> {{ __('installments::installments.view_existing_plans') }}
                                    </button>
                                </div>
                            @endif
                        </div>
                    </div>
                @endif
            </div>
            <div class="row">
                <!-- Total Amount -->
                <div class="col-md-3 mb-3">
                    <label for="totalAmount" class="form-label">{{ __('installments::installments.total_amount_before_interest') }}</label>
                    <input type="number" step="0.01" wire:model.live.debounce.300ms="totalAmount" id="totalAmount"
                        class="form-control">
                </div>
                <!-- Down Payment -->
                <div class="col-md-3 mb-3">
                    <label for="downPayment" class="form-label">{{ __('installments::installments.down_payment_amount') }}</label>
                    <input type="number" step="0.01" wire:model.live.debounce.500ms="downPayment" id="downPayment"
                        class="form-control">
                </div>

                <!-- Interest Value (Amount) -->
                <div class="col-md-2 mb-3">
                    <label for="interestValue" class="form-label">
                        {{ __('installments::installments.interest_value') }} ({{ __('installments::installments.egp') }})
                    </label>
                    <input type="number" step="0.01" wire:model.live.debounce.500ms="interestValue" id="interestValue"
                        class="form-control" placeholder="0">
                </div>

                <!-- Interest Percentage -->
                <div class="col-md-2 mb-3">
                    <label for="interestPercentage" class="form-label">
                        {{ __('installments::installments.interest_value') }} (%)
                    </label>
                    <input type="number" step="0.01" wire:model.live.debounce.500ms="interestPercentage" id="interestPercentage"
                        class="form-control" placeholder="0">
                </div>

                <!-- Amount to be Installed -->
                <div class="col-md-2 mb-3">
                    <label class="form-label">{{ __('installments::installments.remaining_amount_for_installment') }}</label>
                    <input type="text" value="{{ number_format($amountToBeInstalled, 2) }}" class="form-control bg-light"
                        readonly>
                </div>
            </div>
            <hr>
            <div class="row">
                <!-- Number of Installments -->
                <div class="col-md-3 mb-3">
                    <label for="numberOfInstallments" class="form-label">{{ __('installments::installments.number_of_installments') }}</label>
                    <input type="number" wire:model.live.debounce.500ms="numberOfInstallments"
                        id="numberOfInstallments" class="form-control">
                </div>
                <!-- Installment Amount -->
                <div class="col-md-3 mb-3">
                    <label class="form-label">{{ __('installments::installments.single_installment_amount') }}</label>
                    <input type="text" value="{{ number_format($installmentAmount, 2) }}" class="form-control"
                        readonly>
                </div>
                <!-- Start Date -->
                <div class="col-md-3 mb-3">
                    <label for="startDate" class="form-label">{{ __('installments::installments.first_installment_date') }}</label>
                    <input type="datetime-local" wire:model="startDate" id="startDate" class="form-control">
                </div>
                <!-- Interval -->
                <div class="col-md-3 mb-3">
                    <label class="form-label">{{ __('installments::installments.interval_between_installments') }}</label>
                    <select wire:model="intervalType" class="form-select">
                        <option value="monthly">{{ __('installments::installments.monthly') }}</option>
                        <option value="daily">{{ __('installments::installments.daily') }}</option>
                    </select>
                </div>
            </div>
        </div>
        <div class="card-footer text-end">
            <button type="submit" class="btn btn-primary">{{ __('installments::installments.save_and_generate_installments') }}</button>
        </div>
    </div>
</form>

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Initialize Select2 for client selection
            $('#accHeadId').select2({
                placeholder: '{{ __('installments::installments.select_client') }}',
                allowClear: true,
                width: '100%',
                language: '{{ app()->getLocale() }}',
                dir: '{{ app()->getLocale() == 'ar' ? 'rtl' : 'ltr' }}'
            });

            // Listen for Select2 change and update Livewire
            $('#accHeadId').on('change', function(e) {
                @this.set('accHeadId', e.target.value);
            });

            // Listen for Livewire validation errors
            Livewire.on('validation-error', (data) => {
                const d = Array.isArray(data) ? data[0] : data;

                // Convert text with line breaks to HTML
                let htmlContent = d.text;
                if (d.html) {
                    htmlContent = d.text.replace(/\n/g, '<br>');
                }

                Swal.fire({
                    icon: 'error',
                    title: d.title || '{{ __('installments::installments.error') }}',
                    html: htmlContent,
                    confirmButtonText: '{{ __('installments::installments.ok') }}',
                    confirmButtonColor: '#d33',
                    width: '500px',
                });
            });

            // Listen for amount exceeds balance warning
                Livewire.on('amount-exceeds-balance', (data) => {
                const d = Array.isArray(data) ? data[0] : data;

                let message =
                    `{{ __('installments::installments.requested_amount_greater_than_balance', ['amount' => '${parseFloat(d.amount).toFixed(2)}', 'balance' => '${parseFloat(d.balance).toFixed(2)}']) }}`;

                if (d.existingPlansCount > 0) {
                    message +=
                        `<br><small class="text-muted">{{ __('installments::installments.existing_plans') }} (${d.existingPlansCount}) {{ __('installments::installments.total') }}: ${parseFloat(d.existingPlansTotal).toFixed(2)} {{ __('installments::installments.egp') }}</small>`;
                }

                Swal.fire({
                    icon: 'warning',
                    title: '{{ __('installments::installments.warning') }}',
                    html: message,
                    confirmButtonText: '{{ __('installments::installments.ok') }}',
                    confirmButtonColor: '#ffc107',
                    position: 'top-end',
                    toast: true,
                    timer: 5000,
                    timerProgressBar: true,
                    showConfirmButton: false,
                });
            });

            // Listen for save success
            Livewire.on('save-success', (data) => {
                const d = Array.isArray(data) ? data[0] : data;
                Swal.fire({
                    icon: 'success',
                    title: d.title || '{{ __('installments::installments.success') }}',
                    text: d.text,
                    confirmButtonText: '{{ __('installments::installments.view_plan') }}',
                    confirmButtonColor: '#28a745',
                    showCancelButton: true,
                    cancelButtonText: '{{ __('installments::installments.add_new_plan') }}'
                }).then((result) => {
                    if (result.isConfirmed) {
                        window.location.href = '/installments/plans/' + d.planId;
                    } else {
                        // Reset form
                        @this.call('$refresh');
                        $('#accHeadId').val(null).trigger('change');
                    }
                });
            });
        });

        // Function to show existing plans
        function showExistingPlans() {
            const plans = @json($existingPlans);

            let html = '<div class="table-responsive text-end"><table class="table table-sm">';
            html += '<thead><tr><th>{{ __('installments::installments.plan_number') }}</th><th>{{ __('installments::installments.amount') }}</th><th>{{ __('installments::installments.status') }}</th><th>{{ __('installments::installments.date') }}</th><th>{{ __('installments::installments.actions') }}</th></tr></thead>';
            html += '<tbody>';

            plans.forEach(plan => {
                const date = new Date(plan.created_at).toLocaleDateString('{{ app()->getLocale() == 'ar' ? 'ar-EG' : 'en-US' }}');
                const status = plan.status === 'active' ? '{{ __('installments::installments.active') }}' :
                    plan.status === 'completed' ? '{{ __('installments::installments.completed') }}' :
                    plan.status === 'cancelled' ? '{{ __('installments::installments.cancelled') }}' : plan.status;
                const statusClass = plan.status === 'active' ? 'badge bg-success' :
                    plan.status === 'completed' ? 'badge bg-primary' :
                    'badge bg-secondary';

                html += `<tr>
            <td>#${plan.id}</td>
            <td>${parseFloat(plan.total_amount).toFixed(2)} {{ __('installments::installments.egp') }}</td>
            <td><span class="${statusClass}">${status}</span></td>
            <td>${date}</td>
            <td>
                <a href="/installments/plans/${plan.id}" class="btn btn-sm btn-info">
                    <i class="fas fa-eye"></i> {{ __('installments::installments.view') }}
                </a>
            </td>
        </tr>`;
            });

            html += '</tbody></table></div>';

            Swal.fire({
                title: '{{ __('installments::installments.existing_plans') }}',
                html: html,
                width: '700px',
                confirmButtonText: '{{ __('installments::installments.close') }}',
            });
        }
    </script>
@endpush
