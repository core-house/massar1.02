<div id="invoice-fixed-footer" class="p-2"
    style="background: #fff; border-top: 3px solid #dee2e6; box-shadow: 0 -2px 10px rgba(0,0,0,0.1);">
    <div class="row border border-secondary border-3 rounded p-2 mb-0">
        @if (setting('invoice_show_item_details'))
            <div class="col-3">
                <div class="card" style="font-size: 0.75rem;" id="item-details-card">
                    <div class="card-header text-white py-1">
                        <h6 class="mb-0" style="font-size: 0.8rem;">
                            <i class="las la-box"></i> {{ __('invoices::invoices.item_details') }}
                        </h6>
                    </div>
                    <div class="card-body p-2">
                        <div class="row gx-2">
                            <div class="col-md-6 border-end pe-2">
                                <div class="row mb-1">
                                    <div class="col-5">{{ __('invoices::invoices.item_name') }}</div>
                                    <div class="col-7 fw-bold">
                                        <span class="badge bg-light text-dark" style="font-size: 0.7rem;"
                                            id="selected-item-name">-</span>
                                    </div>
                                </div>
                                <div class="row mb-1">
                                    <div class="col-5">{{ __('invoices::invoices.store') }}</div>
                                    <div class="col-7">
                                        <span class="badge bg-light text-dark" style="font-size: 0.7rem;"
                                            id="selected-item-store">-</span>
                                    </div>
                                </div>
                                <div class="row mb-1">
                                    <div class="col-5">{{ __('invoices::invoices.available_in_store') }}</div>
                                    <div class="col-7">
                                        <span class="badge bg-light text-dark" style="font-size: 0.7rem;"
                                            id="selected-item-available">-</span>
                                    </div>
                                </div>
                                <div class="row mb-1">
                                    <div class="col-6">{{ __('invoices::invoices.total_in_stores') }}</div>
                                    <div class="col-6">
                                        <span class="badge bg-light text-dark" style="font-size: 0.7rem;"
                                            id="selected-item-total">-</span>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6 ps-2">
                                <div class="row mb-1">
                                    <div class="col-6">{{ __('invoices::invoices.unit') }}</div>
                                    <div class="col-6">
                                        <span class="badge bg-light text-dark" style="font-size: 0.7rem;"
                                            id="selected-item-unit">-</span>
                                    </div>
                                </div>
                                <div class="row mb-1">
                                    <div class="col-6">{{ __('invoices::invoices.price') }}</div>
                                    <div class="col-6 text-primary fw-bold">
                                        <span class="badge bg-light text-dark" style="font-size: 0.7rem;"
                                            id="selected-item-price">-</span>
                                    </div>
                                </div>
                                <div class="row mb-1">
                                    <div class="col-6">{{ __('invoices::invoices.last_purchase_price') }}</div>
                                    <div class="col-6 text-success">
                                        <span class="badge bg-light text-dark" style="font-size: 0.7rem;"
                                            id="selected-item-last-price">-</span>
                                    </div>
                                </div>
                                <div class="row mb-1">
                                    <div class="col-6">{{ __('invoices::invoices.average_cost') }}</div>
                                    <div class="col-6 text-success">
                                        <span class="badge bg-light text-dark main-num" style="font-size: 0.7rem;"
                                            id="selected-item-avg-cost">-</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endif

        {{-- Recommended Items --}}
        @if (setting('invoice_show_recommended_items', false))
            <div class="col-2">
                <div class="card" style="font-size: 0.75rem; border: 2px solid #28a745;">
                    <div class="card-header bg-success text-white py-1">
                        <h6 class="mb-0" style="font-size: 0.8rem;">
                            <i class="las la-star"></i> {{ __('invoices::invoices.recommendations') }}
                        </h6>
                    </div>
                    <div class="card-body p-2" id="recommended-items-list"
                        style="min-height: 100px; max-height: 200px; overflow-y: auto;">
                        <p class="text-muted text-center mb-0 small">
                            {{ __('invoices::invoices.select_to_see_recommendations') }}</p>
                    </div>
                </div>
            </div>
        @endif

        @if ($type != 21)
            <div class="col-2">
                <div class="card" style="font-size: 0.75rem;">
                    <div class="card-body p-2">
                        <div class="form-group mb-2">
                            <label for="cash_box_id" style="font-size: 0.75rem;">{{ __('invoices::invoices.cash_box') }}</label>
                            <select id="cash_box_id" class="form-control form-control-sm"
                                style="font-size: 0.75rem; height: 1.8em; padding: 2px 4px;">
                                @foreach ($cashAccounts ?? [] as $account)
                                    <option value="{{ $account->id }}">{{ $account->aname }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="form-group mb-2">
                            @php
                                $isPurchaseInvoice = in_array($type, [11, 13, 15, 17, 20, 24, 25]);
                            @endphp
                            @if ($isPurchaseInvoice)
                                <label for="received_from_client" style="font-size: 0.75rem;">{{ __('invoices::invoices.amount_paid_to_supplier') }}</label>
                            @else
                                <label for="received_from_client" style="font-size: 0.75rem;">{{ __('invoices::invoices.received_amount') }}</label>
                            @endif
                            <input type="number" step="0.01" id="received-from-client"
                                class="form-control form-control-sm scnd"
                                style="font-size: 0.75rem; height: 1.8em; padding: 2px 4px;" min="0"
                                value="0">
                        </div>

                        <div class="form-group mb-0">
                            <label for="payment-notes" style="font-size: 0.75rem;">{{ __('invoices::invoices.payment_notes') }}</label>
                            <textarea id="payment-notes" class="form-control form-control-sm" rows="1"
                                placeholder="{{ __('invoices::invoices.payment_notes_placeholder') }}"
                                style="font-size: 0.75rem; padding: 4px;"></textarea>
                        </div>
                    </div>
                </div>
            </div>
        @endif

        <div class="col-5 ms-auto">
            <div class="card" style="font-size: 0.75rem;">
                <div class="card-body p-2">
                    @if ($type != 21)
                        <div class="row mb-1">
                            <div class="col-3 text-right fw-bolder" style="font-size: 0.85rem;">{{ __('invoices::invoices.subtotal') }}</div>
                            <div class="col-3 text-left text-primary" id="display-subtotal"
                                style="font-size: 0.85rem;">0</div>
                        </div>
                    @endif

                    @if ($type != 18 && $type != 21)
                        <div class="row mb-1 align-items-center">
                            <div class="col-2 text-right font-weight-bold">
                                <label style="font-size: 0.75rem;">{{ __('invoices::invoices.discount_pct') }}</label>
                            </div>
                            <div class="col-3">
                                <div class="input-group input-group-sm">
                                    <input type="number" step="0.01" id="discount-percentage"
                                        class="form-control"
                                        style="font-size: 0.75rem; height: 1.8em; padding: 2px 4px;" min="0"
                                        max="100" value="0">
                                    <div class="input-group-append">
                                        <span class="input-group-text" style="font-size: 0.75rem;">%</span>
                                    </div>
                                </div>
                            </div>
                            <div class="col-2 text-right font-weight-bold">
                                <label for="discount_value" class="form-label"
                                    style="font-size: 0.75rem;">{{ __('invoices::invoices.discount_value_label') }}</label>
                            </div>
                            <div class="col-3">
                                <input type="number" step="0.01" id="discount-value"
                                    class="form-control form-control-sm"
                                    style="font-size: 0.75rem; height: 1.8em; padding: 2px 4px;" value="0"
                                    min="0">
                            </div>
                        </div>

                        <div class="row mb-1 align-items-center">
                            <div class="col-2 text-right font-weight-bold">
                                <label style="font-size: 0.75rem;">{{ __('invoices::invoices.additional_pct') }}</label>
                            </div>
                            <div class="col-3">
                                <div class="input-group input-group-sm">
                                    <input type="number" step="0.01" id="additional-percentage"
                                        class="form-control"
                                        style="font-size: 0.75rem; height: 1.8em; padding: 2px 4px;" min="0"
                                        max="100" value="0">
                                    <div class="input-group-append">
                                        <span class="input-group-text" style="font-size: 0.75rem;">%</span>
                                    </div>
                                </div>
                            </div>
                            <div class="col-2 text-right font-weight-bold">
                                <label for="additional_value" class="form-label"
                                    style="font-size: 0.75rem;">{{ __('invoices::invoices.additional_value_label') }}</label>
                            </div>
                            <div class="col-3">
                                <input type="number" step="0.01" id="additional-value"
                                    class="form-control form-control-sm"
                                    style="font-size: 0.75rem; height: 1.8em; padding: 2px 4px;" value="0"
                                    min="0">
                            </div>
                        </div>

                        {{-- VAT --}}
                        @if (setting('enable_vat_fields') == '1' && setting('vat_level') != 'disabled')
                            <div class="row mb-1 align-items-center">
                                <div class="col-2 text-right font-weight-bold">
                                    <label style="font-size: 0.75rem;">{{ __('invoices::invoices.vat_pct') }}</label>
                                </div>
                                <div class="col-3">
                                    <div class="input-group input-group-sm">
                                        <input type="number" step="0.01" id="vat-percentage" readonly
                                            class="form-control bg-light"
                                            style="font-size: 0.75rem; height: 1.8em; padding: 2px 4px;"
                                            value="{{ $vatPercentage ?? 0 }}">
                                        <div class="input-group-append">
                                            <span class="input-group-text" style="font-size: 0.75rem;">%</span>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-2 text-right font-weight-bold">
                                    <label for="vat_value" class="form-label"
                                        style="font-size: 0.75rem;">{{ __('invoices::invoices.vat_value_label') }}</label>
                                </div>
                                <div class="col-3">
                                    <input type="number" step="0.01" id="vat-value-display" readonly
                                        class="form-control form-control-sm bg-light"
                                        style="font-size: 0.75rem; height: 1.8em; padding: 2px 4px;" value="0">
                                </div>
                            </div>
                        @endif

                        {{-- Withholding Tax --}}
                        @if (setting('enable_vat_fields') == '1' && setting('withholding_tax_level') != 'disabled')
                            <div class="row mb-1 align-items-center">
                                <div class="col-2 text-right font-weight-bold">
                                    <label style="font-size: 0.75rem;">{{ __('invoices::invoices.withholding_tax_pct') }}</label>
                                </div>
                                <div class="col-3">
                                    <div class="input-group input-group-sm">
                                        <input type="number" step="0.01" id="withholding-tax-percentage" readonly
                                            class="form-control bg-light"
                                            style="font-size: 0.75rem; height: 1.8em; padding: 2px 4px;"
                                            value="{{ $withholdingTaxPercentage ?? 0 }}">
                                        <div class="input-group-append">
                                            <span class="input-group-text" style="font-size: 0.75rem;">%</span>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-2 text-right font-weight-bold">
                                    <label for="withholding_tax_value" class="form-label"
                                        style="font-size: 0.75rem;">{{ __('invoices::invoices.withholding_tax_value_label') }}</label>
                                </div>
                                <div class="col-3">
                                    <input type="number" step="0.01" id="withholding-tax-value-display" readonly
                                        class="form-control form-control-sm bg-light"
                                        style="font-size: 0.75rem; height: 1.8em; padding: 2px 4px;" value="0">
                                </div>
                            </div>
                        @endif
                    @endif

                    <hr class="my-1">

                    @if ($type != 21)
                        <div class="row mb-1">
                            <div class="col-3 text-right fw-bolder" style="font-size: 0.9rem;">{{ __('invoices::invoices.net') }}</div>
                            <div class="col-3 text-left font-weight-bold main-num" id="display-total"
                                style="font-size: 0.9rem;">0</div>
                        </div>

                        {{-- Currency Display --}}
                        @if (setting('multi_currency_enabled') == '1')
                            <div class="row mb-1" id="currency-display-row" style="display: none;">
                                <div class="col-3 text-right text-muted" style="font-size: 0.75rem;">
                                    {{ __('invoices::invoices.exchange_rate') }}:</div>
                                <div class="col-3 text-left text-muted" style="font-size: 0.75rem;"
                                    id="footer-exchange-rate">1.00</div>
                            </div>
                        @endif
                    @endif

                    <div class="row mb-1">
                        @if ($type != 21)
                            @php
                                $isPurchaseInvoice = in_array($type, [11, 13, 15, 17, 20, 24, 25]);
                            @endphp
                            <div class="col-3 text-right font-weight-bold" style="font-size: 0.8rem;">
                                @if ($isPurchaseInvoice)
                                    {{ __('invoices::invoices.paid_amount') }}
                                @else
                                    {{ __('invoices::invoices.received_amount') }}
                                @endif
                            </div>
                            <div class="col-3 text-left font-weight-bold" id="display-received"
                                style="font-size: 0.8rem;">0</div>
                        @endif
                    </div>

                    @if ($type != 21)
                        <div class="row">
                            <div class="col-3 text-right font-weight-bold" style="font-size: 0.8rem;">
                                {{ __('invoices::invoices.remaining') }}</div>
                            <div class="col-3 text-left font-weight-bold" style="font-size: 0.8rem;"
                                id="display-remaining">0</div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
