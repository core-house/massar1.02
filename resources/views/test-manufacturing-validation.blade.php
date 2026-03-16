<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>اختبار فاليديشن فاتورة التصنيع</title>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            padding: 20px;
            background: #f5f5f5;
        }
        .container {
            max-width: 800px;
            margin: 0 auto;
            background: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        h1 {
            color: #333;
            margin-bottom: 30px;
        }
        .section {
            margin-bottom: 25px;
            padding: 20px;
            background: #f8f9fa;
            border-radius: 8px;
        }
        .section h3 {
            margin-top: 0;
            color: #495057;
        }
        .input-group {
            margin-bottom: 15px;
        }
        label {
            display: block;
            margin-bottom: 5px;
            font-weight: bold;
            color: #495057;
        }
        input {
            width: 100%;
            padding: 10px;
            border: 1px solid #ced4da;
            border-radius: 5px;
            font-size: 16px;
        }
        .total {
            font-size: 18px;
            font-weight: bold;
            color: #28a745;
            margin-top: 10px;
        }
        .difference {
            font-size: 18px;
            font-weight: bold;
            color: #dc3545;
            margin-top: 10px;
        }
        button {
            background: #007bff;
            color: white;
            border: none;
            padding: 12px 30px;
            border-radius: 5px;
            font-size: 16px;
            cursor: pointer;
            margin-top: 20px;
        }
        button:hover {
            background: #0056b3;
        }
        button:disabled {
            background: #6c757d;
            cursor: not-allowed;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>🧪 اختبار فاليديشن فاتورة التصنيع</h1>
        
        <div class="section">
            <h3>📦 المواد الخام</h3>
            <div class="input-group">
                <label>تكلفة المواد الخام (ج.م):</label>
                <input type="number" id="rawMaterialsCost" value="1000" step="0.01" oninput="calculate()">
            </div>
        </div>

        <div class="section">
            <h3>💰 المصروفات الإضافية</h3>
            <div class="input-group">
                <label>المصروفات (ج.م):</label>
                <input type="number" id="expenses" value="200" step="0.01" oninput="calculate()">
            </div>
        </div>

        <div class="section">
            <h3>🏭 المنتجات المصنعة</h3>
            <div class="input-group">
                <label>تكلفة المنتجات (ج.م):</label>
                <input type="number" id="productsCost" value="1200" step="0.01" oninput="calculate()">
            </div>
        </div>

        <div class="section">
            <h3>📊 النتائج</h3>
            <div class="total">
                إجمالي تكلفة التصنيع (مواد خام + مصروفات): <span id="totalManufacturing">1200.00</span> ج.م
            </div>
            <div class="total">
                تكلفة المنتجات المصنعة: <span id="totalProducts">1200.00</span> ج.م
            </div>
            <div class="difference">
                الفرق: <span id="difference">0.00</span> ج.م
            </div>
            <div id="validationStatus" style="margin-top: 15px; padding: 10px; border-radius: 5px;"></div>
        </div>

        <button onclick="testValidation()">اختبار الفاليديشن</button>
    </div>

    <script>
        function formatCurrency(amount) {
            const num = parseFloat(amount) || 0;
            return new Intl.NumberFormat('ar-EG', {
                minimumFractionDigits: 2,
                maximumFractionDigits: 2
            }).format(num);
        }

        function calculate() {
            const rawMaterialsCost = parseFloat(document.getElementById('rawMaterialsCost').value) || 0;
            const expenses = parseFloat(document.getElementById('expenses').value) || 0;
            const productsCost = parseFloat(document.getElementById('productsCost').value) || 0;

            const totalManufacturing = rawMaterialsCost + expenses;
            const difference = Math.abs(totalManufacturing - productsCost);

            document.getElementById('totalManufacturing').textContent = formatCurrency(totalManufacturing);
            document.getElementById('totalProducts').textContent = formatCurrency(productsCost);
            document.getElementById('difference').textContent = formatCurrency(difference);

            const statusDiv = document.getElementById('validationStatus');
            if (difference <= 0.01) {
                statusDiv.style.background = '#d4edda';
                statusDiv.style.color = '#155724';
                statusDiv.style.border = '1px solid #c3e6cb';
                statusDiv.innerHTML = '✅ التكاليف متطابقة - يمكن الحفظ';
            } else {
                statusDiv.style.background = '#f8d7da';
                statusDiv.style.color = '#721c24';
                statusDiv.style.border = '1px solid #f5c6cb';
                statusDiv.innerHTML = '❌ التكاليف غير متطابقة - لا يمكن الحفظ';
            }
        }

        function testValidation() {
            const rawMaterialsCost = parseFloat(document.getElementById('rawMaterialsCost').value) || 0;
            const expenses = parseFloat(document.getElementById('expenses').value) || 0;
            const productsCost = parseFloat(document.getElementById('productsCost').value) || 0;

            const totalManufacturing = rawMaterialsCost + expenses;
            const difference = Math.abs(totalManufacturing - productsCost);

            if (difference > 0.01) {
                Swal.fire({
                    title: 'خطأ في التكاليف!',
                    html: `
                        <div style="text-align: right; direction: rtl;">
                            <p style="margin-bottom: 15px; font-size: 16px;">
                                تكلفة المواد الخام والمصروفات يجب أن تساوي تكلفة المنتجات المصنعة
                            </p>
                            <div style="background: #f8f9fa; padding: 15px; border-radius: 8px; margin-top: 10px;">
                                <div style="display: flex; justify-content: space-between; margin-bottom: 8px;">
                                    <span style="font-weight: bold;">تكلفة المواد الخام والمصروفات:</span>
                                    <span style="color: #dc3545; font-weight: bold;">${formatCurrency(totalManufacturing)} ج.م</span>
                                </div>
                                <div style="display: flex; justify-content: space-between; margin-bottom: 8px;">
                                    <span style="font-weight: bold;">تكلفة المنتجات المصنعة:</span>
                                    <span style="color: #28a745; font-weight: bold;">${formatCurrency(productsCost)} ج.م</span>
                                </div>
                                <hr style="margin: 10px 0; border-color: #dee2e6;">
                                <div style="display: flex; justify-content: space-between;">
                                    <span style="font-weight: bold; color: #dc3545;">الفرق:</span>
                                    <span style="color: #dc3545; font-weight: bold; font-size: 18px;">${formatCurrency(difference)} ج.م</span>
                                </div>
                            </div>
                            <div style="margin-top: 15px; padding: 10px; background: #fff3cd; border-radius: 5px; border-right: 4px solid #ffc107;">
                                <p style="margin: 0; font-size: 14px; color: #856404;">
                                    💡 <strong>نصيحة:</strong> استخدم زر "توزيع التكاليف حسب النسب" لتوزيع التكاليف تلقائياً على المنتجات
                                </p>
                            </div>
                        </div>
                    `,
                    icon: 'error',
                    confirmButtonText: 'حسناً',
                    confirmButtonColor: '#dc3545',
                    width: '600px'
                });
            } else {
                Swal.fire({
                    title: 'ممتاز!',
                    text: 'التكاليف متطابقة - يمكن حفظ الفاتورة',
                    icon: 'success',
                    confirmButtonText: 'حسناً',
                    confirmButtonColor: '#28a745'
                });
            }
        }

        // Initial calculation
        calculate();
    </script>
</body>
</html>
