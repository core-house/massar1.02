<div>
    <!-- Success Message -->
    @if (session()->has('message'))
        <div class="alert alert-success">{{ session('message') }}</div>
    @endif

    <!-- Plan Summary -->
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h4 class="card-title mb-0">{{ __('installments::installments.plan_summary') }}</h4>
            <div>
                @can('edit Installment Plans')
                    <a href="{{ route('installments.plans.edit', $plan->id) }}" class="btn btn-warning btn-sm">
                        <i class="fas fa-edit"></i> {{ __('installments::installments.edit_plan') }}
                    </a>
                @endcan

                @can('delete Installment Plans')
                    <button wire:click="deletePlan" wire:confirm="{{ __('installments::installments.confirm_delete_plan') }} {{ __('installments::installments.all_installments_and_entries_will_be_deleted') }}" class="btn btn-danger btn-sm">
                        <i class="fas fa-trash"></i> {{ __('installments::installments.delete_plan') }}
                    </button>
                @endcan
            </div>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-4"><strong>{{ __('installments::installments.client') }}:</strong> {{ $plan->account->aname ?? __('installments::installments.not_applicable') }}
                    ({{ $plan->account->code ?? '' }})</div>
                <div class="col-md-4"><strong>{{ __('installments::installments.current_balance') }}:</strong>
                    {{ number_format($plan->amount_to_be_installed - $plan->payments->sum('amount_paid'), 2) }}</div>
                <div class="col-md-4"><strong>{{ __('installments::installments.status') }}:</strong> <span
                        class="badge bg-primary">{{ $plan->status }}</span></div>
            </div>
        </div>
    </div>

    <!-- Installments Table -->
    <div class="card">
        <div class="card-header">
            <h4 class="card-title">{{ __('installments::installments.view_all_installments') }}</h4>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered mb-0">
                    <thead class="table-light text-center align-middle">
                        <tr>
                            <th>{{ __('installments::installments.installment_number') }}</th>
                            <th>{{ __('installments::installments.due_date') }}</th>
                            <th>{{ __('installments::installments.installment_amount') }}</th>
                            <th>{{ __('installments::installments.amount_paid') }}</th>
                            <th>{{ __('installments::installments.payment_date') }}</th>
                            <th>{{ __('installments::installments.status') }}</th>
                            <th>{{ __('installments::installments.actions') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($plan->payments as $payment)
                            <tr class="text-center">
                                <td>{{ $payment->installment_number }}</td>
                                <td>{{ \Carbon\Carbon::parse($payment->due_date)->format('Y-m-d') }}</td>
                                <td>{{ number_format($payment->amount_due, 2) }}</td>
                                <td>{{ number_format($payment->amount_paid, 2) }}</td>
                                <td>{{ $payment->payment_date ? \Carbon\Carbon::parse($payment->payment_date)->format('Y-m-d') : '-' }}</td>
                                <td>
                                    @if ($payment->status == 'paid')
                                        <span class="badge bg-success">{{ __('installments::installments.paid') }}</span>
                                    @elseif($payment->status == 'pending' && $payment->due_date < now())
                                        <span class="badge bg-danger">{{ __('installments::installments.overdue') }}</span>
                                    @else
                                        <span class="badge bg-warning">{{ __('installments::installments.pending') }}</span>
                                    @endif
                                </td>
                                <td>
                                    @if ($payment->status != 'paid')
                                        @can('edit Installment Plans')
                                            <button class="btn btn-success btn-sm"
                                                wire:click="openPaymentModal({{ $payment->id }})">
                                                <i class="fas fa-check"></i> {{ __('installments::installments.record_payment') }}
                                            </button>
                                        @endcan

                                        @can('delete Installment Plans')
                                            <button class="btn btn-danger btn-sm"
                                                wire:click="deletePayment({{ $payment->id }})"
                                                wire:confirm="{{ __('installments::installments.confirm_delete_installment') }}">
                                                <i class="fas fa-trash"></i> {{ __('installments::installments.delete') }}
                                            </button>
                                        @endcan
                                    @else
                                        <span class="badge bg-success me-2">
                                            <i class="fas fa-check-circle"></i> {{ __('installments::installments.paid') }}
                                        </span>

                                        @can('edit Installment Plans')
                                            <button class="btn btn-warning btn-sm"
                                                wire:click="cancelPayment({{ $payment->id }})"
                                                wire:confirm="{{ __('installments::installments.confirm_cancel_payment') }} {{ __('installments::installments.associated_journal_entry_will_be_deleted') }}">
                                                <i class="fas fa-undo"></i> {{ __('installments::installments.cancel') }}
                                            </button>
                                        @endcan
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                    <tfoot class="table-light">
                        <tr class="text-center fw-bold">
                            <td colspan="2">{{ __('installments::installments.total') }}</td>
                            <td>{{ number_format($plan->payments->sum('amount_due'), 2) }}</td>
                            <td>{{ number_format($plan->payments->sum('amount_paid'), 2) }}</td>
                            <td colspan="3"></td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>

    <!-- ============== Payment Recording Modal ============== -->
    <div wire:ignore.self class="modal fade" id="paymentModal" tabindex="-1" aria-labelledby="paymentModalLabel"
        aria-hidden="true" data-bs-backdrop="static" data-bs-keyboard="false">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header bg-success text-white">
                    <h5 class="modal-title" id="paymentModalLabel">
                        <i class="fas fa-money-bill-wave me-2"></i>{{ __('installments::installments.record_new_payment') }}
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form wire:submit.prevent="recordPayment">
                    <div class="modal-body">
                        <!-- Payment Amount -->
                        <div class="mb-3">
                            <label for="paymentAmount" class="form-label fw-bold">{{ __('installments::installments.payment_amount') }}</label>
                            <input type="number" step="0.01"
                                class="form-control @error('paymentAmount') is-invalid @enderror" id="paymentAmount"
                                wire:model="paymentAmount">
                            @error('paymentAmount')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <!-- Payment Date -->
                        <div class="mb-3">
                            <label for="paymentDate" class="form-label fw-bold">{{ __('installments::installments.payment_date') }}</label>
                            <input type="datetime-local" class="form-control @error('paymentDate') is-invalid @enderror"
                                id="paymentDate" wire:model="paymentDate">
                            @error('paymentDate')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <!-- Notes -->
                        <div class="mb-3">
                            <label for="notes" class="form-label fw-bold">{{ __('installments::installments.notes') }}</label>
                            <textarea class="form-control" id="notes" wire:model="notes" rows="3"></textarea>
                        </div>
                    </div>
                    <div class="modal-footer bg-light">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                            <i class="fas fa-times me-1"></i>{{ __('installments::installments.close') }}
                        </button>
                        <button type="submit" class="btn btn-success">
                            <i class="fas fa-save me-1"></i>{{ __('installments::installments.save_payment') }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

@push('scripts')
    <style>
        #paymentModal.modal {
            --bs-modal-bg: rgba(40, 167, 69, 0.5); /* خلفية خضراء شفافة */
        }
        #paymentModal .modal-backdrop {
            background-color: rgba(40, 167, 69, 0.3);
        }
    </style>
    <script>
        document.addEventListener('livewire:initialized', () => {
            const modalElement = document.getElementById('paymentModal');
            const modal = new bootstrap.Modal(modalElement);

            @this.on('open-modal', (event) => {
                modal.show();
            });

            @this.on('close-modal', (event) => {
                modal.hide();
            });

            // Listen for payment success
                Livewire.on('payment-success', (data) => {
                const d = Array.isArray(data) ? data[0] : data;
                Swal.fire({
                    icon: 'success',
                    title: d.title || '{{ __('installments::installments.success') }}',
                    text: d.text,
                    confirmButtonText: '{{ __('installments::installments.ok') }}',
                    confirmButtonColor: '#28a745',
                    customClass: {
                        popup: 'text-end'
                    }
                });
            });

            // Listen for payment error
            Livewire.on('payment-error', (data) => {
                const d = Array.isArray(data) ? data[0] : data;
                Swal.fire({
                    icon: 'error',
                    title: d.title || '{{ __('installments::installments.error') }}',
                    text: d.text,
                    confirmButtonText: '{{ __('installments::installments.ok') }}',
                    confirmButtonColor: '#d33',
                    customClass: {
                        popup: 'text-end'
                    }
                });
            });
        });
    </script>
@endpush
