<div>
    <!-- رسالة النجاح -->
    @if (session()->has('message'))
        <div class="alert alert-success">{{ session('message') }}</div>
    @endif

    <!-- ملخص الخطة -->
    <div class="card">
        <div class="card-header">
            <h4 class="card-title">[translate:ملخص الخطة]</h4>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-4"><strong>[translate:العميل:]</strong> {{ $plan->client->name ?? 'N/A' }}</div>
                <div class="col-md-4"><strong>[translate:الرصيد الحالي:]</strong>
                    {{ number_format($plan->amount_to_be_installed - $plan->payments->sum('amount_paid'), 2) }}</div>
                <div class="col-md-4"><strong>[translate:الحالة:]</strong> <span
                        class="badge bg-primary">{{ $plan->status }}</span></div>
            </div>
        </div>
    </div>

    <!-- جدول الأقساط -->
    <div class="card">
        <div class="card-header">
            <h4 class="card-title">[translate:عرض جميع الأقساط]</h4>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered mb-0">
                    <thead class="table-light text-center align-middle">
                        <tr>
                            <th>[translate:رقم القسط]</th>
                            <th>[translate:تاريخ الاستحقاق]</th>
                            <th>[translate:قيمة القسط]</th>
                            <th>[translate:المبلغ المسدد]</th>
                            <th>[translate:تاريخ السداد]</th>
                            <th>[translate:الحالة]</th>
                            <th>[translate:الإجراءات]</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($plan->payments as $payment)
                            <tr class="text-center">
                                <td>{{ $payment->installment_number }}</td>
                                <td>{{ $payment->due_date }}</td>
                                <td>{{ number_format($payment->amount_due, 2) }}</td>
                                <td>{{ number_format($payment->amount_paid, 2) }}</td>
                                <td>{{ $payment->payment_date ?? '-' }}</td>
                                <td>
                                    @if ($payment->status == 'paid')
                                        <span class="badge bg-success">[translate:مدفوع]</span>
                                    @elseif($payment->status == 'pending' && $payment->due_date < now())
                                        <span class="badge bg-danger">[translate:متأخر]</span>
                                    @else
                                        <span class="badge bg-warning">[translate:مستحق]</span>
                                    @endif
                                </td>
                                <td>
                                    @if ($payment->status != 'paid')
                                        <button class="btn btn-success btn-sm"
                                            wire:click="openPaymentModal({{ $payment->id }})">
                                            [translate:تسجيل سداد]
                                        </button>
                                    @else
                                        <i class="fas fa-check-circle text-success"></i>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                    <tfoot class="table-light">
                        <tr class="text-center fw-bold">
                            <td colspan="2">[translate:الإجمالي]</td>
                            <td>{{ number_format($plan->payments->sum('amount_due'), 2) }}</td>
                            <td>{{ number_format($plan->payments->sum('amount_paid'), 2) }}</td>
                            <td colspan="3"></td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>

    <!-- ============== النافذة المنبثقة لتسجيل الدفع ============== -->
    <div wire:ignore.self class="modal fade" id="paymentModal" tabindex="-1" aria-labelledby="paymentModalLabel"
        aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="paymentModalLabel">[translate:تسجيل دفعة جديدة]</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form wire:submit.prevent="recordPayment">
                    <div class="modal-body">
                        <!-- مبلغ الدفعة -->
                        <div class="mb-3">
                            <label for="paymentAmount" class="form-label">[translate:مبلغ الدفعة]</label>
                            <input type="number" step="0.01"
                                class="form-control @error('paymentAmount') is-invalid @enderror" id="paymentAmount"
                                wire:model="paymentAmount">
                            @error('paymentAmount')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <!-- تاريخ الدفعة -->
                        <div class="mb-3">
                            <label for="paymentDate" class="form-label">[translate:تاريخ الدفعة]</label>
                            <input type="date" class="form-control @error('paymentDate') is-invalid @enderror"
                                id="paymentDate" wire:model="paymentDate">
                            @error('paymentDate')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <!-- ملاحظات -->
                        <div class="mb-3">
                            <label for="notes" class="form-label">[translate:ملاحظات]</label>
                            <textarea class="form-control" id="notes" wire:model="notes" rows="3"></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary"
                            data-bs-dismiss="modal">[translate:إغلاق]</button>
                        <button type="submit" class="btn btn-primary">[translate:حفظ الدفعة]</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

@push('scripts')
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
        });
    </script>
@endpush
