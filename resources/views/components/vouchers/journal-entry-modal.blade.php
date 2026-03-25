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
                    {{ __('Journal Entry') }} - {{ __('Voucher') }} #{{ $voucher->pro_id }}
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                {{-- Voucher Information --}}
                <div class="card mb-3 border-0 bg-light">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <p class="mb-2">
                                    <strong>{{ __('Date') }}:</strong> 
                                    <span class="text-primary">{{ $voucher->pro_date }}</span>
                                </p>
                                <p class="mb-2">
                                    <strong>{{ __('Operation Type') }}:</strong>
                                    <x-vouchers.type-badge :proType="$voucher->pro_type" :typeText="$voucher->type->ptext ?? null" />
                                </p>
                            </div>
                            <div class="col-md-6">
                                <p class="mb-2">
                                    <strong>{{ __('Amount') }}:</strong> 
                                    <span class="text-success fw-bold">{{ number_format($voucher->pro_value, 2) }}</span>
                                </p>
                                <p class="mb-2">
                                    <strong>{{ __('User') }}:</strong> 
                                    <span class="text-muted">{{ $voucher->user->name ?? __('Not Specified') }}</span>
                                </p>
                            </div>
                        </div>
                        @if($voucher->details)
                            <div class="mt-2">
                                <strong>{{ __('Description') }}:</strong>
                                <p class="mb-0 text-muted">{{ $voucher->details }}</p>
                            </div>
                        @endif
                    </div>
                </div>

                {{-- Journal Entry Table --}}
                <div class="table-responsive">
                    <table class="table table-bordered table-hover mb-0">
                        <thead class="table-dark">
                            <tr class="text-center">
                                <th style="width: 50%;">{{ __('Account') }}</th>
                                <th style="width: 25%;" class="text-success">{{ __('Debit') }}</th>
                                <th style="width: 25%;" class="text-danger">{{ __('Credit') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            {{-- Debit Entry --}}
                            <tr>
                                <td class="text-end">
                                    <strong class="text-primary">{{ __('From') }}:</strong> 
                                    <span class="fw-bold">{{ $accounts['debit']->aname ?? __('Not Specified') }}</span>
                                    @if($accounts['debit'] && $accounts['debit']->acc_id)
                                        <br><small class="text-muted">({{ __('Account No') }}: {{ $accounts['debit']->acc_id }})</small>
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
                                    <strong class="text-primary">{{ __('To') }}:</strong> 
                                    <span class="fw-bold">{{ $accounts['credit']->aname ?? __('Not Specified') }}</span>
                                    @if($accounts['credit'] && $accounts['credit']->acc_id)
                                        <br><small class="text-muted">({{ __('Account No') }}: {{ $accounts['credit']->acc_id }})</small>
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
                                <td class="text-end">{{ __('Total') }}</td>
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
                        <strong>{{ __('Notes') }}:</strong> {{ $voucher->notes }}
                    </div>
                @endif
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="fas fa-times me-2"></i>{{ __('Close') }}
                </button>
            </div>
        </div>
    </div>
</div>
