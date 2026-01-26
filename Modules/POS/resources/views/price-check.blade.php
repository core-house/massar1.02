@extends('pos::layouts.master')

@push('styles')
<style>
    .price-check-container {
        min-height: 100vh;
        padding: 2rem;
        background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
    }

    .price-check-card {
        max-width: 900px;
        margin: 0 auto;
        background: white;
        border-radius: 20px;
        box-shadow: 0 10px 40px rgba(0, 0, 0, 0.1);
        overflow: hidden;
    }

    .price-check-header {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        padding: 2rem;
        text-align: center;
    }

    .price-check-header h1 {
        margin: 0;
        font-size: 2rem;
        font-weight: 700;
    }

    .price-check-body {
        padding: 2rem;
    }

    .barcode-input-container {
        margin-bottom: 2rem;
    }

    .barcode-input-wrapper {
        position: relative;
        max-width: 500px;
        margin: 0 auto;
    }

    .barcode-input {
        width: 100%;
        padding: 1.5rem;
        font-size: 1.5rem;
        text-align: center;
        border: 3px solid #667eea;
        border-radius: 15px;
        outline: none;
        transition: all 0.3s;
    }

    .barcode-input:focus {
        border-color: #764ba2;
        box-shadow: 0 0 0 4px rgba(102, 126, 234, 0.1);
    }

    .barcode-icon {
        position: absolute;
        right: 20px;
        top: 50%;
        transform: translateY(-50%);
        font-size: 1.5rem;
        color: #667eea;
    }

    .item-info {
        background: #f8f9fa;
        border-radius: 15px;
        padding: 2rem;
        margin-bottom: 2rem;
        display: none;
    }

    .item-info.show {
        display: block;
        animation: fadeIn 0.5s;
    }

    @keyframes fadeIn {
        from {
            opacity: 0;
            transform: translateY(-20px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    .item-name {
        font-size: 1.8rem;
        font-weight: 700;
        color: #2d3748;
        margin-bottom: 0.5rem;
    }

    .item-code {
        font-size: 1rem;
        color: #718096;
        margin-bottom: 1rem;
    }

    .item-barcode {
        display: inline-block;
        background: #e2e8f0;
        padding: 0.5rem 1rem;
        border-radius: 8px;
        font-family: 'Courier New', monospace;
        font-size: 1.1rem;
        color: #4a5568;
    }

    .prices-section {
        margin-top: 2rem;
    }

    .prices-section-title {
        font-size: 1.3rem;
        font-weight: 600;
        color: #2d3748;
        margin-bottom: 1.5rem;
        padding-bottom: 0.5rem;
        border-bottom: 2px solid #e2e8f0;
    }

    .unit-prices {
        margin-bottom: 2rem;
    }

    .unit-name {
        font-size: 1.2rem;
        font-weight: 600;
        color: #4a5568;
        margin-bottom: 1rem;
        padding: 0.75rem;
        background: #edf2f7;
        border-radius: 8px;
    }

    .price-list {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
        gap: 1rem;
    }

    .price-card {
        background: white;
        border: 2px solid #e2e8f0;
        border-radius: 12px;
        padding: 1.5rem;
        text-align: center;
        transition: all 0.3s;
    }

    .price-card:hover {
        border-color: #667eea;
        transform: translateY(-5px);
        box-shadow: 0 5px 15px rgba(102, 126, 234, 0.2);
    }

    .price-name {
        font-size: 0.9rem;
        color: #718096;
        margin-bottom: 0.5rem;
    }

    .price-value {
        font-size: 1.8rem;
        font-weight: 700;
        color: #2d3748;
    }

    .price-currency {
        font-size: 1rem;
        color: #718096;
        margin-right: 0.25rem;
    }

    .no-prices {
        text-align: center;
        padding: 2rem;
        color: #718096;
    }

    .error-message {
        background: #fed7d7;
        color: #c53030;
        padding: 1rem;
        border-radius: 8px;
        text-align: center;
        margin-top: 1rem;
        display: none;
    }

    .error-message.show {
        display: block;
        animation: shake 0.5s;
    }

    @keyframes shake {
        0%, 100% { transform: translateX(0); }
        25% { transform: translateX(-10px); }
        75% { transform: translateX(10px); }
    }

    .loading-spinner {
        text-align: center;
        padding: 2rem;
        display: none;
    }

    .loading-spinner.show {
        display: block;
    }

    .spinner {
        border: 4px solid #f3f3f3;
        border-top: 4px solid #667eea;
        border-radius: 50%;
        width: 50px;
        height: 50px;
        animation: spin 1s linear infinite;
        margin: 0 auto;
    }

    @keyframes spin {
        0% { transform: rotate(0deg); }
        100% { transform: rotate(360deg); }
    }

    .back-button {
        position: absolute;
        top: 2rem;
        right: 2rem;
        background: white;
        border: none;
        border-radius: 50%;
        width: 50px;
        height: 50px;
        display: flex;
        align-items: center;
        justify-content: center;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        cursor: pointer;
        transition: all 0.3s;
    }

    .back-button:hover {
        transform: scale(1.1);
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2);
    }

    @media (max-width: 768px) {
        .price-check-container {
            padding: 1rem;
        }

        .price-check-header h1 {
            font-size: 1.5rem;
        }

        .barcode-input {
            font-size: 1.2rem;
            padding: 1rem;
        }

        .price-list {
            grid-template-columns: 1fr;
        }
    }
</style>
@endpush

@section('content')
<div class="price-check-container">
    <a href="{{ route('pos.create') }}" class="back-button" title="العودة">
        <i class="fas fa-arrow-right" style="font-size: 1.2rem; color: #667eea;"></i>
    </a>

    <div class="price-check-card">
        <div class="price-check-header">
            <h1><i class="fas fa-barcode me-2"></i> فحص السعر بالباركود</h1>
        </div>

        <div class="price-check-body">
            <div class="barcode-input-container">
                <div class="barcode-input-wrapper">
                    <input type="text" 
                           id="barcodeInput" 
                           class="barcode-input" 
                           placeholder="امسح الباركود أو اكتبه هنا..."
                           autocomplete="off"
                           autofocus>
                    <i class="fas fa-barcode barcode-icon"></i>
                </div>
            </div>

            <div class="loading-spinner" id="loadingSpinner">
                <div class="spinner"></div>
                <p style="margin-top: 1rem; color: #718096;">جاري البحث...</p>
            </div>

            <div class="error-message" id="errorMessage"></div>

            <div class="item-info" id="itemInfo">
                <div class="item-name" id="itemName"></div>
                <div class="item-code" id="itemCode"></div>
                <div class="item-barcode" id="itemBarcode"></div>

                <div class="prices-section">
                    <div class="prices-section-title">
                        <i class="fas fa-tags me-2"></i> الأسعار
                    </div>
                    <div id="pricesContainer"></div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    $(document).ready(function() {
        const barcodeInput = $('#barcodeInput');
        const loadingSpinner = $('#loadingSpinner');
        const errorMessage = $('#errorMessage');
        const itemInfo = $('#itemInfo');
        const itemName = $('#itemName');
        const itemCode = $('#itemCode');
        const itemBarcode = $('#itemBarcode');
        const pricesContainer = $('#pricesContainer');

        // التركيز على حقل الباركود عند تحميل الصفحة
        barcodeInput.focus();

        // البحث عند الضغط على Enter
        barcodeInput.on('keypress', function(e) {
            if (e.which === 13) { // Enter key
                e.preventDefault();
                const barcode = $(this).val().trim();
                if (barcode.length > 0) {
                    searchPrice(barcode);
                }
            }
        });

        // مسح الحقل بعد البحث
        barcodeInput.on('input', function() {
            if ($(this).val().length === 0) {
                hideResults();
            }
        });

        function searchPrice(barcode) {
            // إخفاء النتائج السابقة
            hideResults();
            
            // إظهار مؤشر التحميل
            loadingSpinner.addClass('show');
            
            // البحث
            $.ajax({
                url: '{{ route("pos.api.price-check", ":barcode") }}'.replace(':barcode', encodeURIComponent(barcode)),
                method: 'GET',
                success: function(response) {
                    loadingSpinner.removeClass('show');
                    
                    if (response.success && response.item) {
                        displayItemInfo(response.item);
                        // مسح الحقل بعد النجاح
                        setTimeout(function() {
                            barcodeInput.val('').focus();
                        }, 100);
                    } else {
                        showError('حدث خطأ أثناء البحث');
                    }
                },
                error: function(xhr) {
                    loadingSpinner.removeClass('show');
                    
                    if (xhr.status === 404) {
                        showError(xhr.responseJSON?.message || 'لم يتم العثور على صنف بهذا الباركود');
                    } else {
                        showError('حدث خطأ أثناء الاتصال بالخادم');
                    }
                    
                    // مسح الحقل بعد الخطأ
                    setTimeout(function() {
                        barcodeInput.val('').focus();
                    }, 2000);
                }
            });
        }

        function displayItemInfo(item) {
            // عرض معلومات الصنف
            itemName.text(item.name);
            itemCode.text('كود الصنف: ' + item.code);
            itemBarcode.text(item.barcode);
            
            // عرض الأسعار
            pricesContainer.empty();
            
            if (item.prices_by_unit && item.prices_by_unit.length > 0) {
                item.prices_by_unit.forEach(function(unitData) {
                    const unitDiv = $('<div class="unit-prices"></div>');
                    const unitName = $('<div class="unit-name"><i class="fas fa-ruler me-2"></i>' + unitData.unit_name + '</div>');
                    unitDiv.append(unitName);
                    
                    if (unitData.prices && unitData.prices.length > 0) {
                        const priceList = $('<div class="price-list"></div>');
                        
                        unitData.prices.forEach(function(price) {
                            const priceCard = $('<div class="price-card"></div>');
                            const priceName = $('<div class="price-name">' + price.name + '</div>');
                            const priceValue = $('<div class="price-value"><span class="price-currency">ر.س</span>' + formatPrice(price.price) + '</div>');
                            
                            priceCard.append(priceName);
                            priceCard.append(priceValue);
                            priceList.append(priceCard);
                        });
                        
                        unitDiv.append(priceList);
                    } else {
                        const noPrices = $('<div class="no-prices">لا توجد أسعار لهذه الوحدة</div>');
                        unitDiv.append(noPrices);
                    }
                    
                    pricesContainer.append(unitDiv);
                });
            } else {
                const noPrices = $('<div class="no-prices">لا توجد أسعار متاحة لهذا الصنف</div>');
                pricesContainer.append(noPrices);
            }
            
            // إظهار معلومات الصنف
            itemInfo.addClass('show');
        }

        function showError(message) {
            errorMessage.text(message);
            errorMessage.addClass('show');
            
            setTimeout(function() {
                errorMessage.removeClass('show');
            }, 5000);
        }

        function hideResults() {
            itemInfo.removeClass('show');
            errorMessage.removeClass('show');
            pricesContainer.empty();
        }

        function formatPrice(price) {
            return new Intl.NumberFormat('ar-SA', {
                minimumFractionDigits: 2,
                maximumFractionDigits: 2
            }).format(price);
        }

        // دعم اختصار لوحة المفاتيح للعودة
        $(document).on('keydown', function(e) {
            // Escape للعودة
            if (e.key === 'Escape') {
                window.location.href = '{{ route("pos.create") }}';
            }
        });
    });
</script>
@endpush
