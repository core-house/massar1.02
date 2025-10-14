<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>POS System - Bootstrap Version</title>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- Custom POS Bootstrap CSS -->
    <link rel="stylesheet" href="{{ asset('modules/pos/assets/css/pos-bootstrap.css') }}">
</head>
<body class="pos-container">
    <!-- Header -->
    <header class="pos-header">
        <div class="container-fluid">
            <div class="d-flex justify-content-between align-items-center">
                <div class="pos-logo d-flex align-items-center gap-3">
                    <i class="fas fa-cash-register"></i>
                    <h2 class="mb-0">نظام نقاط البيع</h2>
                </div>
                <div class="pos-info d-flex gap-4 align-items-center">
                    <span class="text-muted">المستخدم: أحمد محمد</span>
                    <span class="text-muted">الفرع: الرئيسي</span>
                    <span class="invoice-number">فاتورة #12345</span>
                </div>
            </div>
        </div>
    </header>

    <!-- Invoice Info Section -->
    <div class="container-fluid">
        <div class="invoice-info-section pos-glass-effect pos-rounded pos-shadow">
            <div class="row g-3">
                <div class="col-md-3">
                    <div class="mb-3">
                        <label class="form-label fw-semibold text-dark">العميل</label>
                        <select class="form-select pos-select">
                            <option>اختر العميل</option>
                            <option>عميل نقدي</option>
                            <option>أحمد محمد</option>
                        </select>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="mb-3">
                        <label class="form-label fw-semibold text-dark">طريقة الدفع</label>
                        <select class="form-select pos-select">
                            <option>نقدي</option>
                            <option>فيزا</option>
                            <option>شيك</option>
                        </select>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="mb-3">
                        <label class="form-label fw-semibold text-dark">الموظف</label>
                        <select class="form-select pos-select">
                            <option>سالم أحمد</option>
                            <option>فاطمة محمد</option>
                        </select>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="balance-info">
                        <label class="form-label fw-semibold text-dark">رصيد العميل</label>
                        <div class="balance-display positive">1,250.00 ريال</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Layout -->
    <div class="pos-main-layout">
        <!-- Left Panel -->
        <div class="pos-left-panel pos-card">
            <!-- Search Section -->
            <div class="search-row">
                <div class="search-section">
                    <div class="position-relative">
                        <input type="text" class="form-control pos-search-input" placeholder="البحث عن المنتج...">
                        <i class="fas fa-search search-icon position-absolute top-50 end-0 translate-middle-y me-3 text-muted"></i>
                    </div>
                </div>
                <div class="barcode-section">
                    <div class="d-flex gap-2">
                        <input type="text" class="form-control barcode-input" placeholder="باركود">
                        <button class="btn btn-primary barcode-btn">
                            <i class="fas fa-barcode"></i>
                        </button>
                    </div>
                </div>
            </div>

            <!-- Search Results -->
            <div class="search-results-container">
                <h4 class="text-dark fw-semibold">نتائج البحث</h4>
                <div class="search-results">
                    <div class="search-result-item">
                        <div class="item-info">
                            <div class="item-name">منتج تجريبي 1</div>
                            <div class="item-code">#PROD001</div>
                        </div>
                        <div class="item-price">25.50 ريال</div>
                    </div>
                    <div class="search-result-item">
                        <div class="item-info">
                            <div class="item-name">منتج تجريبي 2</div>
                            <div class="item-code">#PROD002</div>
                        </div>
                        <div class="item-price">15.75 ريال</div>
                    </div>
                </div>
            </div>

            <!-- Categories -->
            <div class="categories-section">
                <h4 class="text-dark fw-bold">
                    <i class="fas fa-th-large me-2"></i>
                    الفئات
                </h4>
                <div class="categories-grid">
                    <div class="category-btn active">
                        <i class="fas fa-utensils"></i>
                        <span>طعام</span>
                    </div>
                    <div class="category-btn">
                        <i class="fas fa-coffee"></i>
                        <span>مشروبات</span>
                    </div>
                    <div class="category-btn">
                        <i class="fas fa-cookie-bite"></i>
                        <span>حلويات</span>
                    </div>
                    <div class="category-btn">
                        <i class="fas fa-apple-alt"></i>
                        <span>فواكه</span>
                    </div>
                </div>
            </div>

            <!-- Quick Access Items -->
            <div class="quick-access-section">
                <h4 class="text-dark fw-semibold">المنتجات السريعة</h4>
                <div class="quick-items-grid">
                    <button class="quick-item-btn">
                        <div class="quick-item-name">قهوة عربية</div>
                        <div class="quick-item-code">#COFFEE001</div>
                    </button>
                    <button class="quick-item-btn">
                        <div class="quick-item-name">شاي أحمر</div>
                        <div class="quick-item-code">#TEA001</div>
                    </button>
                    <button class="quick-item-btn">
                        <div class="quick-item-name">عصير برتقال</div>
                        <div class="quick-item-code">#JUICE001</div>
                    </button>
                    <button class="quick-item-btn">
                        <div class="quick-item-name">ماء</div>
                        <div class="quick-item-code">#WATER001</div>
                    </button>
                </div>
            </div>
        </div>

        <!-- Right Panel -->
        <div class="pos-right-panel pos-card">
            <!-- Shopping Cart -->
            <div class="shopping-cart">
                <div class="cart-header">
                    <h3 class="text-dark mb-0">سلة المشتريات</h3>
                    <span class="items-count">3 عناصر</span>
                </div>
                
                <div class="cart-items">
                    <div class="cart-item pos-hover-lift">
                        <div class="item-main-info">
                            <div class="item-name-code">
                                <div class="name">قهوة عربية</div>
                                <div class="item-code">#COFFEE001</div>
                            </div>
                            <button class="remove-item-btn">
                                <i class="fas fa-times"></i>
                            </button>
                        </div>
                        <div class="item-controls">
                            <div class="quantity-controls">
                                <button class="qty-btn minus">
                                    <i class="fas fa-minus"></i>
                                </button>
                                <input type="number" class="qty-input" value="2" min="1">
                                <button class="qty-btn plus">
                                    <i class="fas fa-plus"></i>
                                </button>
                            </div>
                            <div class="price-info">
                                <div class="unit-price">15.00 ريال</div>
                                <div class="total-price">30.00 ريال</div>
                            </div>
                        </div>
                    </div>

                    <div class="cart-item pos-hover-lift">
                        <div class="item-main-info">
                            <div class="item-name-code">
                                <div class="name">شاي أحمر</div>
                                <div class="item-code">#TEA001</div>
                            </div>
                            <button class="remove-item-btn">
                                <i class="fas fa-times"></i>
                            </button>
                        </div>
                        <div class="item-controls">
                            <div class="quantity-controls">
                                <button class="qty-btn minus">
                                    <i class="fas fa-minus"></i>
                                </button>
                                <input type="number" class="qty-input" value="1" min="1">
                                <button class="qty-btn plus">
                                    <i class="fas fa-plus"></i>
                                </button>
                            </div>
                            <div class="price-info">
                                <div class="unit-price">8.00 ريال</div>
                                <div class="total-price">8.00 ريال</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Transaction Summary -->
            <div class="transaction-summary">
                <div class="summary-row">
                    <span>المجموع الفرعي:</span>
                    <span>38.00 ريال</span>
                </div>
                <div class="summary-row discount">
                    <span>الخصم:</span>
                    <span>-3.00 ريال</span>
                </div>
                <div class="summary-row additional">
                    <span>ضريبة القيمة المضافة:</span>
                    <span>+5.25 ريال</span>
                </div>
                <div class="summary-row total">
                    <span>المجموع الكلي:</span>
                    <span>40.25 ريال</span>
                </div>
            </div>

            <!-- Payment Methods -->
            <div class="payment-section">
                <h4 class="text-dark fw-semibold">طريقة الدفع</h4>
                <div class="payment-methods">
                    <button class="payment-method-btn active">
                        <i class="fas fa-money-bill-wave"></i>
                        <span>نقدي</span>
                    </button>
                    <button class="payment-method-btn">
                        <i class="fas fa-credit-card"></i>
                        <span>فيزا</span>
                    </button>
                    <button class="payment-method-btn">
                        <i class="fas fa-university"></i>
                        <span>شيك</span>
                    </button>
                </div>

                <div class="payment-input-group">
                    <label class="form-label fw-semibold text-dark">المبلغ المدفوع</label>
                    <input type="number" class="form-control payment-input" placeholder="0.00">
                </div>

                <div class="change-amount">
                    <span>المبلغ المتبقي:</span>
                    <span class="change-value">40.25 ريال</span>
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="pos-actions">
                <button class="pos-btn primary large">
                    <i class="fas fa-check"></i>
                    إتمام البيع
                </button>
                <div class="pos-actions-row">
                    <button class="pos-btn secondary">
                        <i class="fas fa-print"></i>
                        طباعة
                    </button>
                    <button class="pos-btn info">
                        <i class="fas fa-save"></i>
                        حفظ
                    </button>
                </div>
                <div class="pos-actions-row">
                    <button class="pos-btn danger">
                        <i class="fas fa-trash"></i>
                        إلغاء
                    </button>
                    <button class="pos-btn secondary">
                        <i class="fas fa-redo"></i>
                        إعادة تعيين
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Customer Display -->
    <div class="customer-display">
        <div class="customer-total">
            <div class="total-label">المجموع</div>
            <div class="total-amount">40.25 ريال</div>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- Custom JavaScript -->
    <script>
        // Category selection
        document.querySelectorAll('.category-btn').forEach(btn => {
            btn.addEventListener('click', function() {
                document.querySelectorAll('.category-btn').forEach(b => b.classList.remove('active'));
                this.classList.add('active');
            });
        });

        // Payment method selection
        document.querySelectorAll('.payment-method-btn').forEach(btn => {
            btn.addEventListener('click', function() {
                document.querySelectorAll('.payment-method-btn').forEach(b => b.classList.remove('active'));
                this.classList.add('active');
            });
        });

        // Search result selection
        document.querySelectorAll('.search-result-item').forEach(item => {
            item.addEventListener('click', function() {
                document.querySelectorAll('.search-result-item').forEach(i => i.classList.remove('selected'));
                this.classList.add('selected');
            });
        });

        // Quantity controls
        document.querySelectorAll('.qty-btn.plus').forEach(btn => {
            btn.addEventListener('click', function() {
                const input = this.parentElement.querySelector('.qty-input');
                input.value = parseInt(input.value) + 1;
            });
        });

        document.querySelectorAll('.qty-btn.minus').forEach(btn => {
            btn.addEventListener('click', function() {
                const input = this.parentElement.querySelector('.qty-input');
                if (parseInt(input.value) > 1) {
                    input.value = parseInt(input.value) - 1;
                }
            });
        });

        // Remove item
        document.querySelectorAll('.remove-item-btn').forEach(btn => {
            btn.addEventListener('click', function() {
                this.closest('.cart-item').remove();
            });
        });
    </script>
</body>
</html>

