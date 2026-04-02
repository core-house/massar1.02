{{-- Delivery Customer Modal --}}
<div class="modal fade" id="deliveryCustomerModal" tabindex="-1" aria-labelledby="deliveryCustomerModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content" style="border-radius:16px;border:none;box-shadow:0 12px 40px rgba(0,0,0,.18);overflow:hidden;">

            <div class="modal-header py-3 px-4" style="background:var(--rpos-dark);border:none;">
                <div class="d-flex align-items-center gap-2">
                    <div style="width:32px;height:32px;background:#f59e0b;border-radius:8px;display:flex;align-items:center;justify-content:center;">
                        <i class="fas fa-motorcycle text-white" style="font-size:.85rem;"></i>
                    </div>
                    <h6 class="modal-title fw-bold mb-0 text-white" id="deliveryCustomerModalLabel">
                        {{ __('pos.delivery_modal_title') }}
                    </h6>
                </div>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <div class="modal-body p-4" style="background:#f8fafc;">

                {{-- Phone Search --}}
                <div class="mb-3 position-relative">
                    <label class="form-label small fw-bold text-muted mb-1">
                        <i class="fas fa-phone me-1"></i>{{ __('pos.phone_placeholder') }}
                    </label>
                    <div class="input-group">
                        <input type="tel" id="deliveryPhoneSearch"
                               class="form-control"
                               placeholder="{{ __('pos.phone_placeholder') }}..."
                               autocomplete="off" dir="ltr"
                               style="border-radius:8px;border:1.5px solid var(--rpos-border);">
                        <span class="input-group-text" id="deliveryPhoneSpinner" style="display:none;border-radius:0 8px 8px 0;">
                            <span class="spinner-border spinner-border-sm text-secondary"></span>
                        </span>
                    </div>
                    <div id="deliveryCustomerStatus" class="mt-2" style="display:none;"></div>
                    <div id="deliverySearchResults" class="list-group position-absolute w-100 shadow-sm" style="display:none;z-index:1060;top:100%;"></div>
                </div>

                {{-- Customer Name --}}
                <div class="mb-3">
                    <label class="form-label small fw-bold text-muted mb-1">
                        <i class="fas fa-user me-1"></i>{{ __('pos.customer_name_placeholder') }}
                    </label>
                    <input type="text" id="deliveryCustomerName"
                           class="form-control"
                           placeholder="{{ __('pos.customer_name_placeholder') }}..."
                           style="border-radius:8px;border:1.5px solid var(--rpos-border);">
                    <input type="hidden" id="deliveryCustomerId" value="">
                </div>

                {{-- Saved Addresses --}}
                <div id="deliveryAddressOptions" class="mb-3" style="display:none;">
                    <label class="form-label small fw-bold text-muted mb-1">
                        <i class="fas fa-map-marker-alt me-1"></i>{{ __('pos.select_address') }}
                    </label>
                    <div class="d-flex flex-wrap gap-2" id="deliveryAddressBtns"></div>
                </div>

                {{-- Address Input --}}
                <div class="mb-1">
                    <label class="form-label small fw-bold text-muted mb-1">
                        <i class="fas fa-map-pin me-1"></i>{{ __('pos.address_label') }}
                    </label>
                    <input type="text" id="deliveryAddressInput"
                           class="form-control"
                           placeholder="{{ __('pos.enter_address') }}"
                           style="border-radius:8px;border:1.5px solid var(--rpos-border);">
                </div>

            </div>

            <div class="modal-footer py-2 px-4 gap-2" style="border-top:1px solid var(--rpos-border);background:#fff;">
                <button type="button" class="btn btn-sm btn-light fw-bold border" data-bs-dismiss="modal" style="border-radius:8px;">
                    {{ __('pos.cancel') }}
                </button>
                <button type="button" class="btn btn-sm fw-bold" id="confirmDeliveryBtn"
                        style="border-radius:8px;background:#fef3c7;color:#92400e;border:1.5px solid #fcd34d;">
                    <i class="fas fa-check me-1"></i>{{ __('pos.confirm') }}
                </button>
            </div>

        </div>
    </div>
</div>
