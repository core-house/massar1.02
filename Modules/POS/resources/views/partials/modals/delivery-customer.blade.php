{{-- مودال بيانات عميل التوصيل --}}
<div class="modal fade" id="deliveryCustomerModal" tabindex="-1" aria-labelledby="deliveryCustomerModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="deliveryCustomerModalLabel">
                    <i class="fas fa-motorcycle text-warning me-2"></i>
                    {{ __('pos.delivery_modal_title') }}
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">

                {{-- بحث بالتليفون --}}
                <div class="mb-3 position-relative">
                    <label class="form-label fw-bold">
                        <i class="fas fa-phone me-1 text-muted"></i>
                        {{ __('pos.phone_placeholder') }}
                    </label>
                    <div class="input-group">
                        <input type="tel" id="deliveryPhoneSearch"
                               class="form-control form-control-lg"
                               placeholder="{{ __('pos.phone_placeholder') }}..."
                               autocomplete="off"
                               dir="ltr">
                        <span class="input-group-text" id="deliveryPhoneSpinner" style="display:none;">
                            <span class="spinner-border spinner-border-sm text-secondary"></span>
                        </span>
                    </div>
                    {{-- رسالة حالة العميل --}}
                    <div id="deliveryCustomerStatus" class="mt-2" style="display:none;"></div>
                    {{-- نتائج البحث --}}
                    <div id="deliverySearchResults" class="list-group position-absolute w-100 shadow-sm" style="display:none; z-index:1060; top:100%;"></div>
                </div>

                {{-- اسم العميل --}}
                <div class="mb-3">
                    <label class="form-label fw-bold">
                        <i class="fas fa-user me-1 text-muted"></i>
                        {{ __('pos.customer_name_placeholder') }}
                    </label>
                    <input type="text" id="deliveryCustomerName"
                           class="form-control form-control-lg"
                           placeholder="{{ __('pos.customer_name_placeholder') }}...">
                    <input type="hidden" id="deliveryCustomerId" value="">
                </div>

                {{-- عناوين العميل المحفوظة --}}
                <div id="deliveryAddressOptions" class="mb-3" style="display:none;">
                    <label class="form-label fw-bold">
                        <i class="fas fa-map-marker-alt me-1 text-muted"></i>
                        {{ __('pos.select_address') }}
                    </label>
                    <div class="d-flex flex-wrap gap-2" id="deliveryAddressBtns"></div>
                </div>

                {{-- حقل العنوان القابل للتعديل --}}
                <div class="mb-3">
                    <label class="form-label fw-bold">
                        <i class="fas fa-map-pin me-1 text-muted"></i>
                        {{ __('pos.address_label') }}
                    </label>
                    <input type="text" id="deliveryAddressInput"
                           class="form-control form-control-lg"
                           placeholder="{{ __('pos.enter_address') }}">
                </div>

            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ __('pos.cancel') }}</button>
                <button type="button" class="btn btn-warning text-white fw-bold" id="confirmDeliveryBtn">
                    <i class="fas fa-check me-1"></i> {{ __('pos.confirm') }}
                </button>
            </div>
        </div>
    </div>
</div>
