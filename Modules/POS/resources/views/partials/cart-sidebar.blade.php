{{-- Cart Sidebar: 1/3 of screen --}}
<div class="pos-cart-sidebar bg-white shadow-sm" style="flex: 1; display: flex; flex-direction: column; border-right: 1px solid #e0e0e0;">
    {{-- Cart Header --}}
    <div class="p-3 border-bottom" style="background: #f8f9fa;">
        <div class="d-flex justify-content-between align-items-center">
            <h5 class="mb-0">
                <i class="fas fa-shopping-cart me-2"></i>
                سلة التسوق
            </h5>
            <span class="badge bg-primary" id="cartItemsCount">0</span>
        </div>
    </div>

            {{-- Cart Items List --}}
            <div class="flex-grow-1" style="overflow-y: auto; padding: 1rem;">
                <div id="cartItems">
                    <div class="text-center py-5">
                        <i class="fas fa-shopping-cart fa-3x text-muted mb-3"></i>
                        <p class="text-muted">السلة فارغة</p>
                        <small class="text-muted">اختر المنتجات لإضافتها للسلة</small>
                    </div>
                </div>
            </div>

    {{-- Cart Footer: Totals --}}
    <div class="border-top bg-light p-3" style="background: #f8f9fa !important;">
        <div class="mb-2">
            <div class="d-flex justify-content-between mb-1">
                <span class="text-muted">المجموع الفرعي:</span>
                <span id="cartSubtotal" class="fw-bold">0.00 ريال</span>
            </div>
            <div class="d-flex justify-content-between mb-1">
                <span class="text-muted">الخصم:</span>
                <span id="cartDiscount" class="text-danger">0.00 ريال</span>
            </div>
            <div class="d-flex justify-content-between mb-1">
                <span class="text-muted">الإضافي:</span>
                <span id="cartAdditional" class="text-success">0.00 ريال</span>
            </div>
        </div>
        <hr>
        <div class="d-flex justify-content-between align-items-center">
            <strong style="font-size: 1.1rem;">الإجمالي:</strong>
            <strong class="text-primary" style="font-size: 1.5rem;" id="cartTotal">0.00 ريال</strong>
        </div>
    </div>
</div>
