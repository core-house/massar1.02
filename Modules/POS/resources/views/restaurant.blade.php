<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ __('pos.restaurant_pos') }} - {{ config('app.name') }}</title>
    <link rel="shortcut icon" href="{{ asset('assets/images/favicon.ico') }}?v={{ filemtime(public_path('assets/images/favicon.ico')) }}" type="image/x-icon">
    <link rel="icon" href="{{ asset('assets/images/favicon.ico') }}?v={{ filemtime(public_path('assets/images/favicon.ico')) }}" type="image/x-icon">
    <link rel="stylesheet" href="{{ asset('assets/css/icons.min.css') }}">
    <link rel="stylesheet" href="{{ asset('modules/pos/assets/css/bootstrap.rtl.min.css') }}">
    @include('pos::restaurant.styles')
</head>
<body class="rpos-body">

<div class="rpos-app-layout">
<header class="rpos-header">
    <div class="rpos-header__brand">
    </div>
    <div class="rpos-header__search">
        <i class="fas fa-search"></i>
        <input type="text" id="rpos-search" placeholder="{{ __('pos.search_placeholder') }}...">
    </div>
    <div class="rpos-header__meta">
        <div class="rpos-table-badge" id="rposTableBadge">
            <span class="rpos-table-badge__dot rpos-table-badge__dot--free"></span>
            <span id="rposTableText">{{ __('pos.table_free') }}</span>
        </div>
        <div class="rpos-header__user">
            <strong>{{ auth()->user()->name }}</strong>
            <small id="rposDateTime"></small>
        </div>
        <div class="rpos-header__actions">
            <span id="rposOnlineStatus" class="rpos-online-dot rpos-online-dot--on" title="{{ __('pos.system_online') }}" style="cursor:pointer"></span>
            <button class="rpos-hdr-btn" id="rposDiagBtn" title="تشخيص الاتصال" style="font-size:11px;padding:4px 8px;">
                <i class="fas fa-stethoscope"></i>
            </button>
            <button class="rpos-hdr-btn rpos-hdr-btn--labeled" id="rposHeldBtn" title="{{ __('pos.held_orders') }}">
                <i class="fas fa-pause-circle"></i>
                <span>{{ __('pos.held_orders') }}</span>
                <span class="rpos-badge" id="rposHeldBadge" style="display:none">0</span>
            </button>
            <button class="rpos-hdr-btn rpos-hdr-btn--labeled" id="rposRecentBtn" title="{{ __('pos.recent_orders') }}">
                <i class="fas fa-history"></i>
                <span>{{ __('pos.recent_orders') }}</span>
            </button>
            <button class="rpos-hdr-btn rpos-hdr-btn--labeled" id="rposPendingBtn" title="{{ __('pos.local_pending_transactions') }}">
                <i class="fas fa-list"></i>
                <span>{{ __('pos.pending') ?? 'معلقة' }}</span>
                <span class="rpos-badge" id="rposPendingBadge" style="display:none">0</span>
            </button>
            <button class="rpos-hdr-btn rpos-hdr-btn--labeled" id="rposRefreshBtn" title="{{ __('pos.refresh_items') }}">
                <i class="fas fa-sync-alt"></i>
                <span>{{ __('pos.refresh_items') ?? 'تحديث' }}</span>
            </button>
            <a href="{{ route('pos.create') }}" class="rpos-hdr-btn rpos-hdr-btn--labeled" title="{{ __('pos.cashier_pos') ?? 'كاشير' }}">
                <i class="fas fa-cash-register"></i>
                <span>{{ __('pos.cashier_pos') ?? 'كاشير' }}</span>
            </a>
            <a href="{{ route('pos.index') }}" class="rpos-hdr-btn rpos-hdr-btn--labeled" title="{{ __('common.back_to_home') ?? 'الرئيسية' }}">
                <i class="fas fa-home"></i>
                <span>{{ __('common.back_to_home') ?? 'الرئيسية' }}</span>
            </a>
        </div>
    </div>
</header>

<div class="rpos-body-row">
<div class="rpos-main-wrapper">
{{-- Order Type Tabs --}}
<div class="rpos-order-types">
    <button class="rpos-order-type" data-type="dining" id="rposTypeDining">
        <i class="fas fa-chair me-1"></i>{{ __('pos.dining') }}
    </button>
    <button class="rpos-order-type rpos-order-type--active" data-type="takeaway" id="rposTypeTakeaway">
        <i class="fas fa-shopping-bag me-1"></i>{{ __('pos.takeaway') }}
    </button>
    <button class="rpos-order-type" data-type="delivery" id="rposTypeDelivery">
        <i class="fas fa-motorcycle me-1"></i>{{ __('pos.delivery') }}
    </button>

    {{-- شريط نوع الطلب (Price Group) --}}
    <div class="rpos-price-group-bar" id="rposPriceGroupBar">
        <div class="rpos-price-group-bar__btns" id="rposPriceGroupBtns">
            @foreach($priceGroups ?? [] as $pg)
                <button class="rpos-pg-btn @if($loop->first) rpos-pg-btn--active @endif" data-pg-id="{{ $pg->id }}">{{ $pg->name }}</button>
            @endforeach
        </div>
    </div>
</div>

{{-- Main Layout --}}
<div class="rpos-main">

    {{-- Left Sidebar: Categories --}}
    <aside class="rpos-categories">
        <button class="rpos-cat-item" data-category="" id="rposCatAll">
            <i class="fas fa-th-large"></i>
            <span>{{ __('pos.all') }}</span>
        </button>
        @foreach($categories as $cat)
        <button class="rpos-cat-item" data-category="{{ $cat->id }}">
            <i class="fas fa-utensils"></i>
            <span>{{ $cat->name }}</span>
        </button>
        @endforeach
    </aside>

    {{-- Center: Products Grid --}}
    <section class="rpos-products">
        <div class="rpos-products__grid" id="rposProductsGrid">
            {{-- يتملى بالـ JS بعد اختيار التصنيف --}}
        </div>
    </section>
</div>

</div>{{-- /.rpos-main-wrapper --}}

{{-- Cart Sidebar --}}
<aside class="rpos-cart">
    <div class="rpos-cart__header">
        <span class="rpos-cart__title">{{ __('pos.current_order') }}</span>
        <span class="rpos-cart__order-num" id="rposOrderNum">#ORD-{{ str_pad($nextProId, 4, '0', STR_PAD_LEFT) }}</span>
        <span class="rpos-cart__table-tag" id="rposCartTableTag"></span>
        <button class="rpos-cart__void-icon" id="rposVoidIconBtn" title="{{ __('pos.void_btn') }}">
            <i class="fas fa-trash-alt"></i>
        </button>
    </div>

    <div class="rpos-cart__totals">
        <div class="rpos-cart__total-row">
            <span>{{ __('pos.subtotal') }}</span>
            <span id="rposSubtotal">0.00</span>
        </div>
        <div class="rpos-cart__total-row rpos-cart__total-row--total">
            <span>{{ __('pos.total') }}</span>
            <span id="rposTotal" class="rpos-cart__total-amount">0.00</span>
        </div>
    </div>

    <div class="rpos-cart__actions">
        <button class="rpos-cart__action-btn" id="rposNotesBtn">
            <i class="fas fa-sticky-note"></i> {{ __('pos.notes_title') }}
        </button>
        <button class="rpos-cart__action-btn rpos-cart__action-btn--print" id="rposPrintBtn">
            <i class="fas fa-print"></i> {{ __('pos.print_btn') }}
        </button>
        <button class="rpos-cart__action-btn rpos-cart__action-btn--void" id="rposVoidBtn">
            <i class="fas fa-times"></i> {{ __('pos.void_btn') }}
        </button>
    </div>

    {{-- Delivery Panel (يظهر فقط عند اختيار delivery) --}}
    <div id="rposDeliveryPanel" style="display:none;" class="rpos-delivery-panel px-3 pb-2">
        <div class="rpos-delivery-panel__inner border rounded p-2">
            <div class="d-flex align-items-center gap-2 mb-2">
                <i class="fas fa-motorcycle text-warning"></i>
                <span class="fw-bold small">{{ __('pos.delivery') }}</span>
            </div>

            {{-- السائق فقط --}}
            <select id="rposDeliveryDriver" class="form-select form-select-sm">
                <option value="">-- {{ __('pos.select_driver') }} --</option>
                @foreach($drivers ?? [] as $drv)
                    <option value="{{ $drv->id }}">{{ $drv->name }}</option>
                @endforeach
            </select>
        </div>
    </div>

    <div class="rpos-cart__items" id="rposCartItems">
        <div class="rpos-cart__empty" id="rposCartEmpty">
            <i class="fas fa-utensils"></i>
            <p>{{ __('pos.cart_empty') }}</p>
            <small>{{ __('pos.add_items') }}</small>
        </div>
    </div>

    <button class="rpos-checkout-btn" id="rposCheckoutBtn">
        <i class="fas fa-cash-register"></i>
        {{ __('pos.proceed_checkout') }}
    </button>

</aside>

{{-- Modals --}}
</div>{{-- /.rpos-body-row --}}

{{-- Footer --}}
<footer class="rpos-footer">
    <span class="rpos-footer__status">
        <span class="rpos-footer__dot rpos-footer__dot--on" id="rposFooterDot"></span>
        {{ __('pos.system_online') }}
    </span>
    <span class="rpos-footer__version">v1.0</span>
    <div class="rpos-footer__links">
        <a href="#" id="rposPayOutBtn" title="{{ __('pos.pay_out_btn') }}">
            <i class="fas fa-hand-holding-usd"></i>
            <span>{{ __('pos.pay_out_btn') }}</span>
        </a>
        <a href="#" id="rposReturnInvoiceBtn" title="{{ __('pos.return_invoice_btn') }}">
            <i class="fas fa-undo-alt"></i>
            <span>{{ __('pos.return_invoice_btn') }}</span>
        </a>
        <a href="{{ route('pos.settings') }}" title="{{ __('pos.terminal_settings') }}">
            <i class="fas fa-cog"></i>
            <span>{{ __('pos.terminal_settings') }}</span>
        </a>
        <a href="{{ route('pos.price-check') }}" title="{{ __('pos.price_check') }}">
            <i class="fas fa-tags"></i>
            <span>{{ __('pos.price_check') }}</span>
        </a>
        <a href="{{ route('logout') }}" title="{{ __('pos.logout') }}"
           onclick="event.preventDefault(); document.getElementById('rpos-logout-form').submit();">
            <i class="fas fa-sign-out-alt"></i>
            <span>{{ __('pos.logout') }}</span>
        </a>
        <form id="rpos-logout-form" action="{{ route('logout') }}" method="POST" style="display:none">@csrf</form>
    </div>
</footer>
</div>{{-- /.rpos-app-layout --}}
@include('pos::partials.modals.order-type-details')
@include('pos::partials.modals.delivery-customer')
@include('pos::partials.modals.payment')

{{-- Diagnostics Modal --}}
<div class="modal fade" id="rposDiagModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-dark text-white">
                <h5 class="modal-title"><i class="fas fa-stethoscope me-2"></i>تشخيص الاتصال والنظام</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-0">
                <div id="rposDiagContent" class="p-3">
                    <div class="text-center py-4"><i class="fas fa-spinner fa-spin fa-2x text-muted"></i></div>
                </div>
            </div>
            <div class="modal-footer">
                <button class="btn btn-primary btn-sm" id="rposDiagRefreshBtn">
                    <i class="fas fa-sync-alt me-1"></i>تحديث
                </button>
                <button class="btn btn-success btn-sm" id="rposDiagSyncBtn">
                    <i class="fas fa-cloud-upload-alt me-1"></i>مزامنة الآن
                </button>
                <button class="btn btn-secondary btn-sm" data-bs-dismiss="modal">إغلاق</button>
            </div>
        </div>
    </div>
</div>
@include('pos::partials.modals.customer')
@include('pos::partials.modals.notes')
@include('pos::partials.modals.held-orders')
@include('pos::partials.modals.pending-transactions')
@include('pos::partials.modals.recent-transactions')
@include('pos::partials.modals.pay-out')
@include('pos::partials.modals.return-invoice')
@include('pos::partials.modals.product-details')

<script src="{{ asset('assets/js/jquery.min.js') }}"></script>
<script src="{{ asset('assets/js/bootstrap.bundle.min.js') }}"></script>
<script src="{{ asset('assets/plugins/sweet-alert2/sweetalert2.min.js') }}"></script>
<script src="{{ asset('modules/pos/js/pos-indexeddb.js') }}?v={{ filemtime(public_path('modules/pos/js/pos-indexeddb.js')) }}"></script>

{{-- Config: routes, i18n, initial data --}}
@include('pos::restaurant.scripts')

{{-- Main POS logic --}}
<script src="{{ asset('modules/pos/js/resturant-pos.js') }}?v={{ time() }}"></script>

</body>
</html>
