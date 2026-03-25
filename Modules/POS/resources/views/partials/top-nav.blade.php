{{-- Top Navigation Bar - Cashier POS --}}
<div class="pos-top-nav bg-white shadow-sm" style="padding: 1rem; border-bottom: 1px solid #e0e0e0;">
    <div class="d-flex align-items-center justify-content-between">
        {{-- Left Side --}}
        <div class="d-flex align-items-center gap-3">
            <a href="{{ route('admin.dashboard') }}"
               class="btn btn-link p-0"
               style="font-size: 1.5rem; text-decoration: none; color: #00695C;"
               title="{{ __('common.back_to_home') ?? 'العودة للرئيسية' }}">
                <i class="fas fa-home"></i>
            </a>
            <button type="button" class="btn btn-link p-0" style="font-size: 1.5rem;">
                <i class="fas fa-bars"></i>
            </button>
        </div>

        {{-- Center: Search --}}
        <div class="flex-grow-1 mx-4" style="max-width: 500px;">
            <div class="position-relative">
                <input type="text"
                       id="productSearch"
                       class="form-control form-control-lg"
                       placeholder="{{ __('pos.search_placeholder') }} (F2)"
                       style="border-radius: 25px; padding-right: 45px;">
                <i class="fas fa-search position-absolute"
                   style="right: 15px; top: 50%; transform: translateY(-50%); color: #999;"></i>
            </div>
        </div>

        {{-- Right Side --}}
        <div class="d-flex align-items-center gap-3">
            <label class="dark-mode-switch" title="{{ __('common.dark_mode') ?? 'الوضع الداكن' }}">
                <input type="checkbox" id="darkModeToggle">
                <span class="slider"></span>
            </label>
            <div id="onlineStatus" class="badge" style="padding: 0.5rem 1rem; font-size: 0.85rem;">
                <i class="fas fa-wifi"></i> {{ __('pos.system_online') }}
            </div>
            <div class="d-flex align-items-center gap-2">
                <span id="orderNumber" style="font-size: 1.2rem; font-weight: bold; color: #00695C;">{{ $nextProId }}</span>
            </div>
            {{-- نوع الفاتورة --}}
            <select id="invoiceTypeSelect" class="form-select form-select-sm" style="width: auto; border-radius: 20px;" title="{{ __('pos.invoice_type') }}">
                @foreach($invoiceTypes as $type)
                    <option value="{{ $type->id }}" {{ $type->id === 102 ? 'selected' : '' }}>
                        {{ $type->ptext }}
                    </option>
                @endforeach
            </select>
            <button type="button"
                    id="pendingTransactionsBtn"
                    class="btn btn-link text-dark position-relative"
                    style="text-decoration: none;">
                <i class="fas fa-list me-1"></i> {{ __('pos.held_orders') }}
                <span id="pendingTransactionsBadge"
                      class="badge bg-danger position-absolute top-0 start-100 translate-middle"
                      style="display: none; font-size: 0.7rem; padding: 0.25rem 0.5rem;">0</span>
            </button>
            <button type="button"
                    id="heldOrdersBtn"
                    class="btn btn-link text-dark position-relative"
                    style="text-decoration: none;">
                <i class="fas fa-pause-circle me-1"></i> {{ __('pos.held_orders') }}
                <span id="heldOrdersBadge"
                      class="badge bg-warning position-absolute top-0 start-100 translate-middle"
                      style="display: none; font-size: 0.7rem; padding: 0.25rem 0.5rem;">0</span>
            </button>
            <button type="button"
                    id="recentTransactionsBtn"
                    class="btn btn-link text-dark"
                    style="text-decoration: none;">
                <i class="fas fa-history me-1"></i> {{ __('pos.recent_orders') }}
            </button>
            <a href="{{ route('pos.price-check') }}"
               class="btn btn-link text-dark"
               style="text-decoration: none;">
                <i class="fas fa-search-dollar me-1"></i> {{ __('pos.price_check') }}
            </a>
            <button type="button"
                    id="refreshItemsBtn"
                    class="btn btn-link text-success"
                    style="text-decoration: none;">
                <i class="fas fa-sync-alt me-1"></i> {{ __('pos.refresh_items') }}
            </button>
            <button type="button"
                    id="registerBtn"
                    class="btn btn-primary"
                    style="border-radius: 25px; padding: 0.5rem 1.5rem;">
                <i class="fas fa-cash-register me-2"></i> {{ __('pos.register') }}
            </button>
            <div class="position-relative" style="width: 250px;">
                <input type="text"
                       id="barcodeSearch"
                       class="form-control form-control-lg"
                       placeholder="{{ __('pos.search_placeholder') }} (F1)"
                       style="border-radius: 25px; padding-right: 45px;">
                <i class="fas fa-barcode position-absolute"
                   style="right: 15px; top: 50%; transform: translateY(-50%); color: #999;"></i>
            </div>
        </div>
    </div>
</div>
