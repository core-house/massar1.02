<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>فاتورة نقاط البيع - {{ $pro_id }}</title>
    
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Cairo:wght@300;400;500;600;700&display=swap');
        
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Cairo', sans-serif;
            font-size: 11px;
            line-height: 1.3;
            color: #000;
            background: white;
            direction: rtl;
        }
        
        /* حجم 7.8 cm = 78mm = 294.33px (عند 96 DPI) */
        .receipt {
            width: 78mm;
            max-width: 78mm;
            margin: 0 auto;
            padding: 5mm;
            background: white;
            border: 1px solid #ddd;
        }
        
        .receipt-header {
            text-align: center;
            border-bottom: 2px dashed #000;
            padding-bottom: 5px;
            margin-bottom: 5px;
        }
        
        .company-logo {
            font-size: 16px;
            font-weight: 700;
            margin-bottom: 3px;
            color: #000;
        }
        
        .company-info {
            font-size: 9px;
            color: #333;
            margin-bottom: 2px;
        }
        
        .receipt-title {
            font-size: 12px;
            font-weight: 600;
            margin: 5px 0;
            text-transform: uppercase;
        }
        
        .receipt-info {
            margin-bottom: 5px;
            border-bottom: 1px dashed #ccc;
            padding-bottom: 5px;
        }
        
        .info-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 2px;
            font-size: 10px;
        }
        
        .info-label {
            font-weight: 600;
            color: #000;
        }
        
        .info-value {
            color: #333;
        }
        
        .receipt-items {
            margin-bottom: 5px;
        }
        
        .items-header {
            display: flex;
            justify-content: space-between;
            font-weight: 600;
            font-size: 9px;
            border-bottom: 1px solid #000;
            padding-bottom: 2px;
            margin-bottom: 3px;
        }
        
        .item-row {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 3px;
            font-size: 9px;
            padding-bottom: 2px;
            border-bottom: 1px dotted #ccc;
        }
        
        .item-name {
            flex: 1;
            font-weight: 500;
            padding-left: 3px;
            word-break: break-word;
        }
        
        .item-qty {
            width: 25px;
            text-align: center;
        }
        
        .item-price {
            width: 35px;
            text-align: left;
        }
        
        .item-total {
            width: 45px;
            text-align: left;
            font-weight: 600;
        }
        
        .receipt-totals {
            border-top: 2px solid #000;
            padding-top: 3px;
            margin-top: 5px;
        }
        
        .total-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 2px;
            font-size: 10px;
        }
        
        .total-row.grand-total {
            font-size: 12px;
            font-weight: 700;
            border-top: 1px solid #000;
            padding-top: 3px;
            margin-top: 3px;
        }
        
        .receipt-payment {
            margin-top: 5px;
            padding-top: 5px;
            border-top: 1px dashed #ccc;
        }
        
        .payment-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 2px;
            font-size: 10px;
        }
        
        .receipt-footer {
            text-align: center;
            margin-top: 10px;
            padding-top: 5px;
            border-top: 2px dashed #000;
            font-size: 9px;
            color: #333;
        }
        
        .thank-you {
            font-weight: 600;
            font-size: 10px;
            margin-bottom: 3px;
            color: #000;
        }
        
        .footer-info {
            margin-bottom: 2px;
        }
        
        .barcode {
            text-align: center;
            margin: 5px 0;
            font-family: 'Courier New', monospace;
            font-size: 12px;
            font-weight: 600;
        }
        
        @media print {
            @page {
                size: 78mm auto;
                margin: 0;
            }
            
            body {
                margin: 0;
                padding: 0;
            }
            
            .receipt {
                width: 78mm;
                max-width: 78mm;
                margin: 0;
                padding: 5mm;
                border: none;
            }
            
            .no-print {
                display: none !important;
            }
            
            /* تحسينات للطابعات الحرارية */
            body {
                font-size: 10px;
            }
            
            .receipt-title {
                font-size: 11px;
            }
            
            .total-row.grand-total {
                font-size: 11px;
            }
        }
        
        /* للعرض على الشاشة */
        @media screen {
            body {
                padding: 20px;
                background: #f5f5f5;
            }
            
            .receipt {
                box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            }
        }
    </style>
</head>
<body onload="window.print();">
    <div class="receipt">
        <!-- رأس الفاتورة -->
        <div class="receipt-header">
            <div class="company-logo">{{ config('app.name', 'نظام نقاط البيع') }}</div>
            <div class="company-info">فاتورة مبيعات</div>
            <div class="receipt-title">فاتورة رقم: {{ $pro_id }}</div>
        </div>

        <!-- معلومات الفاتورة -->
        <div class="receipt-info">
            <div class="info-row">
                <span class="info-label">التاريخ:</span>
                <span class="info-value">{{ \Carbon\Carbon::parse($pro_date)->format('Y-m-d') }}</span>
            </div>
            <div class="info-row">
                <span class="info-label">الوقت:</span>
                <span class="info-value">{{ \Carbon\Carbon::parse($operation->created_at)->format('H:i:s') }}</span>
            </div>
            @if($acc1List->count() > 0 && $acc1List->first()->aname !== 'عميل نقدي')
            <div class="info-row">
                <span class="info-label">العميل:</span>
                <span class="info-value">{{ $acc1List->first()->aname ?? 'عميل نقدي' }}</span>
            </div>
            @endif
            @if($operation->user)
            <div class="info-row">
                <span class="info-label">الكاشير:</span>
                <span class="info-value">{{ $operation->user->name ?? 'غير محدد' }}</span>
            </div>
            @endif
        </div>

        <!-- عناصر الفاتورة -->
        <div class="receipt-items">
            <div class="items-header">
                <span>الصنف</span>
                <span>الكمية</span>
                <span>السعر</span>
                <span>المجموع</span>
            </div>
            
            @foreach($invoiceItems as $item)
            <div class="item-row">
                <div class="item-name">{{ $item['item_name'] }}</div>
                <div class="item-qty">{{ number_format($item['quantity'], 0) }}</div>
                <div class="item-price">{{ number_format($item['price'], 2) }}</div>
                <div class="item-total">{{ number_format($item['sub_value'], 2) }}</div>
            </div>
            @endforeach
        </div>

        <!-- المجاميع -->
        <div class="receipt-totals">
            <div class="total-row">
                <span>المجموع الفرعي:</span>
                <span>{{ number_format($subtotal, 2) }} ريال</span>
            </div>
            
            @if($discount_value > 0)
            <div class="total-row">
                <span>الخصم:</span>
                <span>-{{ number_format($discount_value, 2) }} ريال</span>
            </div>
            @endif
            
            @if($additional_value > 0)
            <div class="total-row">
                <span>الإضافي:</span>
                <span>+{{ number_format($additional_value, 2) }} ريال</span>
            </div>
            @endif
            
            <div class="total-row grand-total">
                <span>الإجمالي:</span>
                <span>{{ number_format($total_after_additional, 2) }} ريال</span>
            </div>
        </div>

        <!-- معلومات الدفع -->
        @if($received_from_client > 0)
        <div class="receipt-payment">
            <div class="payment-row">
                <span>المدفوع:</span>
                <span>{{ number_format($received_from_client, 2) }} ريال</span>
            </div>
            @if($received_from_client > $total_after_additional)
            <div class="payment-row">
                <span>المتبقي:</span>
                <span>{{ number_format($received_from_client - $total_after_additional, 2) }} ريال</span>
            </div>
            @endif
        </div>
        @endif

        <!-- الباركود -->
        <div class="barcode">
            *{{ $pro_id }}*
        </div>

        <!-- ملاحظات -->
        @if($notes)
        <div class="receipt-info">
            <div class="info-row">
                <span class="info-label">ملاحظات:</span>
            </div>
            <div style="margin-top: 3px; font-size: 9px; color: #333;">
                {{ $notes }}
            </div>
        </div>
        @endif

        <!-- تذييل الفاتورة -->
        <div class="receipt-footer">
            <div class="thank-you">شكراً لك!</div>
            <div class="footer-info">{{ now()->format('Y-m-d H:i:s') }}</div>
        </div>
    </div>

    <script>
        window.onload = function() {
            setTimeout(function() {
                window.print();
            }, 500);
        };

        window.onafterprint = function() {
            // يمكن إغلاق النافذة بعد الطباعة
            // window.close();
        };
    </script>
</body>
</html>
