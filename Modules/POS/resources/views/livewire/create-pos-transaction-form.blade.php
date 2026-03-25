{{-- POS Create Transaction Form - New Design --}}
<div class="pos-create-container" style="height: 100vh; display: flex; flex-direction: column; background: #f5f5f5; overflow: hidden;">
    
    {{-- Top Navigation Bar --}}
    <div class="pos-top-nav bg-white shadow-sm" style="padding: 1rem; border-bottom: 1px solid #e0e0e0;">
        <div class="d-flex align-items-center justify-content-between">
            {{-- Left Side: Menu & Logo --}}
            <div class="d-flex align-items-center gap-3">
                <button type="button" class="btn btn-link p-0" style="font-size: 1.5rem;">
                    <i class="fas fa-bars"></i>
                </button>
                <div class="logo" style="font-size: 1.5rem; font-weight: bold; color: #00695C;">Z</div>
            </div>

            {{-- Center: Search Bar --}}
            <div class="flex-grow-1 mx-4" style="max-width: 500px;">
                <div class="position-relative">
                    <input type="text" 
                           wire:model.live.debounce.300ms="searchTerm"
                           class="form-control form-control-lg"
                           placeholder="البحث عن المنتجات..."
                           style="border-radius: 25px; padding-right: 45px;">
                    <i class="fas fa-search position-absolute" 
                       style="right: 15px; top: 50%; transform: translateY(-50%); color: #999;"></i>
                </div>
            </div>

            {{-- Right Side: Order Number, Orders, Register Button --}}
            <div class="d-flex align-items-center gap-3">
                <div class="d-flex align-items-center gap-2">
                    <span style="font-size: 1.2rem; font-weight: bold; color: #00695C;">{{ $pro_id }}</span>
                    <button type="button" class="btn btn-sm btn-outline-primary" style="border-radius: 50%; width: 30px; height: 30px; padding: 0;">
                        <i class="fas fa-plus"></i>
                    </button>
                </div>
                <button type="button" class="btn btn-link text-dark" style="text-decoration: none;">
                    <i class="fas fa-list me-1"></i> الطلبات
                </button>
                <button type="button" 
                        wire:click="openPaymentModal"
                        class="btn btn-primary"
                        style="border-radius: 25px; padding: 0.5rem 1.5rem;">
                    <i class="fas fa-cash-register me-2"></i> تسجيل
                </button>
            </div>
        </div>
    </div>

    {{-- Category Filter Bar --}}
    <div class="pos-category-bar bg-white shadow-sm" style="padding: 0.75rem 1rem; border-bottom: 1px solid #e0e0e0;">
        <div class="d-flex gap-2">
            <button type="button" 
                    wire:click="selectCategory(null)"
                    class="btn category-btn {{ $selectedCategory === null ? 'active' : '' }}"
                    style="border-radius: 20px; padding: 0.5rem 1.5rem; border: 2px solid #e0e0e0; background: white; color: #333;">
                الكل
            </button>
            @foreach($categories as $category)
                <button type="button" 
                        wire:click="selectCategory({{ $category['id'] }})"
                        class="btn category-btn {{ $selectedCategory == $category['id'] ? 'active' : '' }}"
                        style="border-radius: 20px; padding: 0.5rem 1.5rem; border: 2px solid #e0e0e0; background: {{ $selectedCategory == $category['id'] ? '#FFD700' : 'white' }}; color: #333;">
                    {{ $category['name'] }}
                </button>
            @endforeach
        </div>
    </div>

    {{-- Main Content: Product Grid --}}
    <div class="pos-product-grid flex-grow-1" style="overflow-y: auto; padding: 1rem; background: #f5f5f5;">
        <div class="row g-2">
            @if($searchTerm && count($searchResults) > 0)
                {{-- Search Results --}}
                @foreach($searchResults as $item)
                    <div class="col-lg-2 col-md-3 col-sm-4 col-6">
                        <div class="product-card card h-100" 
                             wire:click="addItemFromSearch({{ $item['id'] }})"
                             style="cursor: pointer; border: none; border-radius: 10px; overflow: hidden; transition: transform 0.2s;"
                             onmouseover="this.style.transform='scale(1.02)'"
                             onmouseout="this.style.transform='scale(1)'">
                            <div class="product-image" style="height: 100px; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); display: flex; align-items: center; justify-content: center; position: relative;">
                                <i class="fas fa-box fa-2x"></i>
                            </div>
                            <div class="card-body p-2">
                                <div class="d-flex justify-content-between align-items-start mb-1">
                                    <h6 class="card-title mb-0" style="font-size: 0.8rem; font-weight: 600; color: #333; flex: 1; line-height: 1.2;">
                                        {{ $item['name'] ?? '' }}
                                    </h6>
                                </div>
                                <div class="d-flex justify-content-between align-items-center">
                                    <span class="text-primary fw-bold" style="font-size: 0.85rem;">
                                        {{ number_format($item['sale_price'] ?? 0, 2) }}
                                    </span>
                                    <button class="btn btn-sm btn-outline-primary product-details-btn" 
                                            wire:click.stop="showProductDetails({{ $item['id'] }})"
                                            style="font-size: 0.7rem; padding: 0.15rem 0.4rem; border-radius: 5px;"
                                            title="{{ __('common.details') }}">
                                        <i class="fas fa-info-circle"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            @elseif($selectedCategory && count($categoryItems) > 0)
                {{-- Category Items --}}
                @foreach($categoryItems as $item)
                    <div class="col-lg-2 col-md-3 col-sm-4 col-6">
                        <div class="product-card card h-100" 
                             wire:click="addItemFromSearch({{ $item['id'] }})"
                             style="cursor: pointer; border: none; border-radius: 10px; overflow: hidden; transition: transform 0.2s;"
                             onmouseover="this.style.transform='scale(1.02)'"
                             onmouseout="this.style.transform='scale(1)'">
                            <div class="product-image" style="height: 100px; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); display: flex; align-items: center; justify-content: center; position: relative;">
                                <i class="fas fa-box fa-2x"></i>
                            </div>
                            <div class="card-body p-2">
                                <div class="d-flex justify-content-between align-items-start mb-1">
                                    <h6 class="card-title mb-0" style="font-size: 0.8rem; font-weight: 600; color: #333; flex: 1; line-height: 1.2;">
                                        {{ $item['name'] ?? '' }}
                                    </h6>
                                </div>
                                <div class="d-flex justify-content-between align-items-center">
                                    <span class="text-primary fw-bold" style="font-size: 0.85rem;">
                                        {{ number_format($item['sale_price'] ?? 0, 2) }}
                                    </span>
                                    <button class="btn btn-sm btn-outline-primary product-details-btn" 
                                            wire:click.stop="showProductDetails({{ $item['id'] }})"
                                            style="font-size: 0.7rem; padding: 0.15rem 0.4rem; border-radius: 5px;"
                                            title="{{ __('common.details') }}">
                                        <i class="fas fa-info-circle"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            @else
                {{-- Default: Show All Items --}}
                @foreach($items as $item)
                    <div class="col-lg-2 col-md-3 col-sm-4 col-6">
                        <div class="product-card card h-100" 
                             wire:click="addItemFromSearch({{ $item->id }})"
                             style="cursor: pointer; border: none; border-radius: 10px; overflow: hidden; transition: transform 0.2s;"
                             onmouseover="this.style.transform='scale(1.02)'"
                             onmouseout="this.style.transform='scale(1)'">
                            <div class="product-image" style="height: 100px; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); display: flex; align-items: center; justify-content: center; position: relative;">
                                <i class="fas {{ $item->is_weight_scale ? 'fa-weight' : 'fa-box' }} fa-2x"></i>
                                @if($item->is_weight_scale)
                                    <span class="badge bg-warning position-absolute top-0 start-0 m-1" style="font-size: 0.65rem;">
                                        <i class="fas fa-weight"></i> ميزان
                                    </span>
                                @endif
                            </div>
                            <div class="card-body p-2">
                                <div class="d-flex justify-content-between align-items-start mb-1">
                                    <h6 class="card-title mb-0" style="font-size: 0.8rem; font-weight: 600; color: #333; flex: 1; line-height: 1.2;">
                                        {{ $item->name }}
                                    </h6>
                                </div>
                                <div class="d-flex justify-content-between align-items-center">
                                    <span class="text-primary fw-bold" style="font-size: 0.85rem;">
                                        {{ number_format($item->sale_price ?? 0, 2) }}
                                    </span>
                                    <button class="btn btn-sm btn-outline-primary product-details-btn" 
                                            wire:click.stop="showProductDetails({{ $item->id }})"
                                            style="font-size: 0.7rem; padding: 0.15rem 0.4rem; border-radius: 5px;"
                                            title="{{ __('common.details') }}">
                                        <i class="fas fa-info-circle"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            @endif
        </div>
    </div>

    {{-- Bottom Action Bar --}}
    <div class="pos-bottom-bar bg-white shadow-lg" style="padding: 1rem; border-top: 1px solid #e0e0e0;">
        <div class="d-flex align-items-center justify-content-end gap-3">
            <button type="button" 
                    wire:click="openCustomerModal"
                    class="btn btn-outline-primary"
                    style="border-radius: 25px; padding: 0.75rem 1.5rem;">
                <i class="fas fa-user me-2"></i> العميل
            </button>
            <button type="button" 
                    wire:click="openNotesModal"
                    class="btn btn-outline-secondary"
                    style="border-radius: 25px; padding: 0.75rem 1.5rem;">
                <i class="fas fa-sticky-note me-2"></i> الملاحظات
            </button>
            <div class="dropdown">
                <button class="btn btn-outline-secondary" 
                        type="button" 
                        id="moreOptionsDropdown" 
                        data-bs-toggle="dropdown"
                        style="border-radius: 25px; padding: 0.75rem 1.5rem;">
                    <i class="fas fa-ellipsis-v"></i>
                </button>
                <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="moreOptionsDropdown">
                    <li><a class="dropdown-item" href="#" wire:click="openTableModal"><i class="fas fa-table me-2"></i> اختيار الطاولة</a></li>
                    <li><a class="dropdown-item" href="#" wire:click="resetForm"><i class="fas fa-redo me-2"></i> إعادة تعيين</a></li>
                    <li><hr class="dropdown-divider"></li>
                    <li><a class="dropdown-item" href="{{ route('pos.index') }}"><i class="fas fa-home me-2"></i> العودة للرئيسية</a></li>
                </ul>
            </div>
        </div>
    </div>

    {{-- Shopping Cart Sidebar (Hidden by default, can be toggled) --}}
    <div class="pos-cart-sidebar" 
         x-data="{ open: false }"
         x-show="open"
         style="position: fixed; top: 0; right: 0; width: 400px; height: 100vh; background: white; box-shadow: -2px 0 10px rgba(0,0,0,0.1); z-index: 1000; overflow-y: auto;">
        <div class="p-3">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h5>سلة التسوق</h5>
                <button type="button" @click="open = false" class="btn btn-sm btn-outline-secondary">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            {{-- Cart Items --}}
            <div class="cart-items">
                @foreach($invoiceItems as $index => $item)
                    <div class="card mb-2">
                        <div class="card-body p-2">
                            <div class="d-flex justify-content-between align-items-start mb-2">
                                <div class="flex-grow-1">
                                    <h6 class="mb-1" style="font-size: 0.9rem;">{{ $item['name'] ?? '' }}</h6>
                                </div>
                                <button type="button" 
                                        wire:click="removeRow({{ $index }})"
                                        class="btn btn-sm btn-outline-danger">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </div>
                            <div class="row g-2">
                                <div class="col-6">
                                    <label class="form-label mb-1" style="font-size: 0.75rem;">الكمية</label>
                                    <input type="number" 
                                           wire:model.blur="invoiceItems.{{ $index }}.quantity"
                                           wire:change="updateQuantity({{ $index }}, $event.target.value)"
                                           class="form-control form-control-sm text-center" 
                                           min="1">
                                </div>
                                <div class="col-6">
                                    <label class="form-label mb-1" style="font-size: 0.75rem;">السعر</label>
                                    <input type="number" 
                                           wire:model.blur="invoiceItems.{{ $index }}.price"
                                           wire:change="recalculateSubValues"
                                           class="form-control form-control-sm text-center" 
                                           min="0"
                                           step="0.01">
                                </div>
                            </div>
                            <div class="text-end mt-2">
                                <strong class="text-success">الإجمالي: {{ number_format(($item['quantity'] ?? 1) * ($item['price'] ?? 0), 2) }} ريال</strong>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
            {{-- Cart Total --}}
            <div class="cart-total mt-3 p-3 bg-light rounded">
                <div class="d-flex justify-content-between mb-2">
                    <span>المجموع الفرعي:</span>
                    <span>{{ number_format($subtotal, 2) }} ريال</span>
                </div>
                <div class="d-flex justify-content-between mb-2">
                    <span>الخصم:</span>
                    <span class="text-danger">- {{ number_format($discount_value, 2) }} ريال</span>
                </div>
                <hr>
                <div class="d-flex justify-content-between">
                    <strong>الإجمالي:</strong>
                    <strong class="text-primary" style="font-size: 1.2rem;">{{ number_format($total_after_additional, 2) }} ريال</strong>
                </div>
            </div>
        </div>
    </div>

    {{-- Bootstrap Modals --}}
    
    {{-- Product Details Modal --}}
    <div class="modal fade" id="productDetailsModal" tabindex="-1" wire:ignore.self>
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content" style="border-radius: 15px; border: none;">
                <div class="modal-header" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; border-radius: 15px 15px 0 0;">
                    <h5 class="modal-title">
                        <i class="fas fa-sticky-note me-2"></i>{{ __('common.notes') }}
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="{{ __('common.close') }}"></button>
                </div>
                <div class="modal-body p-4">
                    @if($selectedProductDetails)
                        <div class="product-details-content">
                            <div class="row mb-3">
                                <div class="col-12 text-center mb-3">
                                    <div class="product-icon" style="width: 80px; height: 80px; margin: 0 auto; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); border-radius: 15px; display: flex; align-items: center; justify-content: center;">
                                        <i class="fas {{ $selectedProductDetails['is_weight_scale'] ?? false ? 'fa-weight' : 'fa-box' }} fa-3x" style="color: #98FF98;"></i>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-12">
                                    <div class="detail-item p-3" style="background: #f8f9fa; border-radius: 10px; margin-bottom: 1rem;">
                                        <label class="text-muted mb-2" style="font-size: 0.85rem;">{{ __('common.name') }}</label>
                                        <div class="fw-bold" style="font-size: 1.1rem;">{{ $selectedProductDetails['name'] ?? '-' }}</div>
                                    </div>
                                </div>
                                <div class="col-12">
                                    <div class="detail-item p-3" style="background: #fff9e6; border-radius: 10px; min-height: 150px;">
                                        <label class="text-muted mb-2" style="font-size: 0.85rem;">
                                            <i class="fas fa-sticky-note me-1"></i>{{ __('common.notes') }}
                                        </label>
                                        <div style="white-space: pre-wrap; line-height: 1.6;">
                                            @if(!empty($selectedProductDetails['notes']))
                                                {{ $selectedProductDetails['notes'] }}
                                            @else
                                                <span class="text-muted">{{ __('common.no_notes') }}</span>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @else
                        <div class="text-center py-4">
                            <div class="spinner-border text-primary" role="status">
                                <span class="visually-hidden">{{ __('common.loading') }}</span>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
    
    {{-- Payment Modal --}}
    <div class="modal fade" id="paymentModal" tabindex="-1" wire:ignore.self>
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">الدفع</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">الإجمالي</label>
                        <input type="text" class="form-control" value="{{ number_format($total_after_additional, 2) }} ريال" readonly>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">طريقة الدفع</label>
                        <select wire:model="paymentMethod" class="form-select">
                            <option value="cash">نقدي</option>
                            <option value="card">بطاقة</option>
                            <option value="mixed">مختلط</option>
                        </select>
                    </div>
                    @if($paymentMethod === 'cash' || $paymentMethod === 'mixed')
                        <div class="mb-3">
                            <label class="form-label">المبلغ النقدي</label>
                            <input type="number" wire:model="cashAmount" class="form-control" step="0.01" min="0">
                        </div>
                    @endif
                    @if($paymentMethod === 'card' || $paymentMethod === 'mixed')
                        <div class="mb-3">
                            <label class="form-label">مبلغ البطاقة</label>
                            <input type="number" wire:model="cardAmount" class="form-control" step="0.01" min="0">
                        </div>
                    @endif
                    @if($changeAmount > 0)
                        <div class="alert alert-success">
                            المبلغ المتبقي للعميل: {{ number_format($changeAmount, 2) }} ريال
                        </div>
                    @endif
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">إلغاء</button>
                    <button type="button" wire:click="saveAndPrint" class="btn btn-primary">
                        <i class="fas fa-print me-2"></i> دفع وطباعة
                    </button>
                    <button type="button" wire:click="saveForm" class="btn btn-success">
                        <i class="fas fa-save me-2"></i> حفظ فقط
                    </button>
                </div>
            </div>
        </div>
    </div>

    {{-- Customer Modal --}}
    <div class="modal fade" id="customerModal" tabindex="-1" wire:ignore.self>
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">اختيار العميل</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">العميل</label>
                        <select wire:model="acc1_id" wire:change="updateCustomerBalance" class="form-select">
                            @foreach($acc1List as $client)
                                <option value="{{ $client->id }}">{{ $client->aname }}</option>
                            @endforeach
                        </select>
                    </div>
                    @if($showBalance)
                        <div class="alert alert-info">
                            <strong>رصيد العميل:</strong> {{ number_format($currentBalance, 2) }} ريال
                        </div>
                    @endif
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">إلغاء</button>
                    <button type="button" class="btn btn-primary" data-bs-dismiss="modal">حفظ</button>
                </div>
            </div>
        </div>
    </div>

    {{-- Notes Modal --}}
    <div class="modal fade" id="notesModal" tabindex="-1" wire:ignore.self>
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">الملاحظات</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">ملاحظات الفاتورة</label>
                        <textarea wire:model="notes" class="form-control" rows="5" placeholder="أدخل ملاحظات الفاتورة..."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">إلغاء</button>
                    <button type="button" class="btn btn-primary" data-bs-dismiss="modal">حفظ</button>
                </div>
            </div>
        </div>
    </div>

    {{-- Table Selection Modal --}}
    <div class="modal fade" id="tableModal" tabindex="-1" wire:ignore.self>
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">اختيار الطاولة</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="row g-3">
                        @for($i = 1; $i <= 20; $i++)
                            <div class="col-md-3 col-sm-4 col-6">
                                <button type="button" 
                                        wire:click="selectTable({{ $i }})"
                                        class="btn btn-outline-primary w-100"
                                        style="height: 80px; border-radius: 10px;">
                                    <i class="fas fa-table d-block mb-2"></i>
                                    طاولة {{ $i }}
                                </button>
                            </div>
                        @endfor
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">إلغاء</button>
                </div>
            </div>
        </div>
    </div>

</div>

@push('scripts')
<script>
    document.addEventListener('livewire:init', function() {
        Livewire.on('openPaymentModal', () => {
            const modal = new bootstrap.Modal(document.getElementById('paymentModal'));
            modal.show();
        });

        Livewire.on('openCustomerModal', () => {
            const modal = new bootstrap.Modal(document.getElementById('customerModal'));
            modal.show();
        });

        Livewire.on('openNotesModal', () => {
            const modal = new bootstrap.Modal(document.getElementById('notesModal'));
            modal.show();
        });

        Livewire.on('openTableModal', () => {
            const modal = new bootstrap.Modal(document.getElementById('tableModal'));
            modal.show();
        });

        Livewire.on('openProductDetailsModal', () => {
            const modal = new bootstrap.Modal(document.getElementById('productDetailsModal'));
            modal.show();
        });
    });
</script>
@endpush
