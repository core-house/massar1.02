@props(['voucher'])

@php
    $accounts = \App\Helpers\VoucherHelper::getJournalAccounts($voucher);
    $badge = \App\Helpers\VoucherHelper::getTypeBadge($voucher->pro_type);
@endphp

<div class="modal fade" id="journalEntryModal{{ $voucher->id }}" tabindex="-1"
     aria-labelledby="journalEntryModalLabel{{ $voucher->id }}" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="journalEntryModalLabel{{ $voucher->id }}">
                    <i class="fas fa-book me-2"></i>
                    {{ __('vouchers.journal_entry') }} - {{ __('vouchers.voucher') }} #{{ $voucher->pro_id }}
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="{{ __('vouchers.close') }}"></button>
            </div>
            <div class="modal-body">
                {{-- Voucher Information --}}
                <div class="card mb-3 border-0 bg-light">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <p class="mb-2">
                                    <strong>{{ __('vouchers.date') }}:</strong>
                                    <span class="text-primary">{{ $voucher->pro_date }}</span>
                                </p>
                                <p class="mb-2">
                                    <strong>{{ __('vouchers.operation_type') }}:</strong>
                                    <x-vouchers.type-badge :proType="$voucher->pro_type" :typeText="$voucher->type->ptext ?? null" />
                                </p>
                            </div>
                            <div class="col-md-6">
                                <p class="mb-2">
                                    <strong>{{ __('vouchers.amount') }}:</strong>
                                    <span class="text-success fw-bold">{{ number_format($voucher->pro_value, 2) }}</span>
                                </p>
                                <p class="mb-2">
                                    <strong>{{ __('vouchers.user') }}:</strong>
                                    <span class="text-muted">{{ $voucher->user->name ?? __('vouchers.not_specified') }}</span>
                                </p>
                            </div>
                        </div>
                        @if($voucher->details)
                            <div class="mt-2">
                                <strong>{{ __('general.description') }}:</strong>
                                <p class="mb-0 text-muted">{{ $voucher->details }}</p>
                            </div>
                        @endif
                    </div>
                </div>

                {{-- Journal Entry Table --}}
                <div class="table-responsive">
                    <table class="table table-bordered table-hover mb-0">
                        <thead class="table-primary">
                            <tr class="text-center">
                                <th style="width: 50%; color: #000 !important;">{{ __('invoices::invoices.account') }}</th>
                                <th style="width: 25%; color: #198754 !important;">{{ __('vouchers.debit') }}</th>
                                <th style="width: 25%; color: #dc3545 !important;">{{ __('vouchers.credit') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            {{-- Debit Entry --}}
                            <tr>
                                <td class="text-end">
                                    <strong class="text-primary">{{ __('vouchers.from') }}:</strong>
                                    <span class="fw-bold">{{ $accounts['debit']->aname ?? __('vouchers.not_specified') }}</span>
                                    @if($accounts['debit'] && $accounts['debit']->acc_id)
                                        <br><small class="text-muted">({{ __('vouchers.account_no') }}: {{ $accounts['debit']->acc_id }})</small>
                                    @endif
                                </td>
                                <td class="text-center fw-bold text-success" style="font-size: 18px; background-color: #d1e7dd;">
                                    {{ number_format($accounts['debit_amount'], 2) }}
                                </td>
                                <td class="text-center text-muted" style="background-color: #f8f9fa;">-</td>
                            </tr>

                            {{-- Credit Entry --}}
                            <tr>
                                <td class="text-end">
                                    <strong class="text-primary">{{ __('vouchers.to') }}:</strong>
                                    <span class="fw-bold">{{ $accounts['credit']->aname ?? __('vouchers.not_specified') }}</span>
                                    @if($accounts['credit'] && $accounts['credit']->acc_id)
                                        <br><small class="text-muted">({{ __('vouchers.account_no') }}: {{ $accounts['credit']->acc_id }})</small>
                                    @endif
                                </td>
                                <td class="text-center text-muted" style="background-color: #f8f9fa;">-</td>
                                <td class="text-center fw-bold text-danger" style="font-size: 18px; background-color: #f8d7da;">
                                    {{ number_format($accounts['credit_amount'], 2) }}
                                </td>
                            </tr>
                        </tbody>
                        <tfoot>
                            <tr class="table-light fw-bold" style="font-size: 16px;">
                                <td class="text-end">{{ __('vouchers.total') }}</td>
                                <td class="text-center text-success" style="background-color: #d1e7dd;">
                                    {{ number_format($accounts['debit_amount'], 2) }}
                                </td>
                                <td class="text-center text-danger" style="background-color: #f8d7da;">
                                    {{ number_format($accounts['credit_amount'], 2) }}
                                </td>
                            </tr>
                        </tfoot>
                    </table>
                </div>

                @if($voucher->notes)
                    <div class="alert alert-info mt-3 mb-0">
                        <strong>{{ __('vouchers.notes') }}:</strong> {{ $voucher->notes }}
                    </div>
                @endif
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="fas fa-times me-2"></i>{{ __('vouchers.close') }}
                </button>
            </div>
        </div>
    </div>
</div>
