@push('scripts')
    <style>
        /* تحسين شكل الـ Cards */
        .form-group .card {
            transition: all 0.3s ease;
        }

        .form-group .card:hover {
            box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15) !important;
        }

        .form-check-input:checked {
            background-color: #34d3a3;
            border-color: #34d3a3;
        }

        .section-checkbox {
            cursor: pointer;
        }

        .form-check-label {
            cursor: pointer;
            user-select: none;
        }

        .card-header h6 {
            font-weight: 600;
        }

        /* Preamble Editor Styles */
        #preamble_editor {
            border: 1px solid #ced4da;
            border-radius: 0.25rem;
            padding: 0.75rem;
            font-family: Tahoma, Arial, sans-serif;
            font-size: 14px;
            line-height: 1.6;
        }

        #preamble_editor:focus {
            outline: none;
            border-color: #34d3a3;
            box-shadow: 0 0 0 0.2rem rgba(52, 211, 163, 0.25);
        }

        #preamble_editor p {
            margin-bottom: 0.5rem;
        }

        #preamble_editor ul,
        #preamble_editor ol {
            margin-bottom: 0.5rem;
            padding-right: 20px;
        }

        /* Column Width Input Validation Styles */
        .column-width-input.is-invalid {
            border-color: #dc3545;
            background-color: #fff5f5;
        }

        .column-width-input.is-invalid:focus {
            border-color: #dc3545;
            box-shadow: 0 0 0 0.2rem rgba(220, 53, 69, 0.25);
        }
    </style>

    <script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.0/Sortable.min.js"></script>
    <script>
        // Wait for jQuery to be loaded before executing
        (function() {
            function initInvoiceTemplates() {
                // Check if jQuery is loaded
                if (typeof jQuery === 'undefined' || typeof $ === 'undefined') {
                    // Retry after a short delay
                    setTimeout(initInvoiceTemplates, 100);
                    return;
                }

                // Use jQuery safely
                jQuery(document).ready(function($) {
                    // ============================================
                    // 1️⃣ تفعيل السحب والإفلات (Drag & Drop)
                    // ============================================
                    var sortable = new Sortable(document.getElementById('sortable-columns'), {
                        animation: 150,
                        handle: '.fa-grip-vertical',
                        onEnd: function() {
                            updateColumnOrder();
                        }
                    });

                    // ============================================
                    // 2️⃣ تحديث ترتيب الأعمدة بعد السحب
                    // ============================================
                    function updateColumnOrder() {
                        $('#sortable-columns .column-item').each(function(index) {
                            var columnKey = $(this).data('column');
                            // تحديث قيمة الـ hidden input
                            $(this).find('.column-order-input').val(columnKey);
                        });

                        console.log('✅ Column order updated');
                    }

                    // ============================================
                    // 3️⃣ تحديث عرض العمود عند تغيير القيمة
                    // ============================================
                    $(document).on('input change', '.column-width-input', function() {
                        var columnKey = $(this).data('column');
                        var width = $(this).val();
                        $('#width_' + columnKey).text(width + 'px');
                    });

                    // تحديث جميع الـ badges عند تحميل الصفحة
                    $('.column-width-input').each(function() {
                        var columnKey = $(this).data('column');
                        var width = $(this).val();
                        $('#width_' + columnKey).text(width + 'px');
                    });

                    // ============================================
                    // 4️⃣ إظهار/إخفاء خيار "افتراضي" لأنواع الفواتير
                    // ============================================

                    // عند تحميل الصفحة - إظهار الخيارات المحددة مسبقاً
                    $('.invoice-type-checkbox').each(function() {
                        const typeId = $(this).val();
                        if ($(this).is(':checked')) {
                            $('#default_' + typeId).show();
                        } else {
                            $('#default_' + typeId).hide();
                        }
                    });

                    // عند تغيير الاختيار
                    $('.invoice-type-checkbox').on('change', function() {
                        const typeId = $(this).val();
                        if ($(this).is(':checked')) {
                            $('#default_' + typeId).slideDown(200);
                        } else {
                            $('#default_' + typeId).slideUp(200);
                            $('#default_switch_' + typeId).prop('checked', false);
                        }
                    });

                    // ============================================
                    // 5️⃣ التحقق قبل الإرسال (Validation)
                    // ============================================

                    // منع إدخال الأرقام العشرية في حقل sort_order
                    $('#sort_order').on('input', function() {
                        // إزالة أي أرقام عشرية
                        this.value = this.value.replace(/[^0-9]/g, '');
                    });

                    $('form').on('submit', function(e) {
                        var checkedColumns = $('.column-checkbox:checked').length;
                        var checkedTypes = $('.invoice-type-checkbox:checked').length;

                        if (checkedColumns === 0) {
                            e.preventDefault();
                            alert('⚠️ يجب اختيار عمود واحد على الأقل');
                            return false;
                        }

                        if (checkedTypes === 0) {
                            e.preventDefault();
                            alert('⚠️ يجب اختيار نوع فاتورة واحد على الأقل');
                            return false;
                        }

                        // تحديث الترتيب قبل الإرسال
                        updateColumnOrder();

                        // التحقق من صحة حقول العرض
                        var invalidInputs = [];
                        var errorMessages = [];

                        $('.column-checkbox:checked').each(function() {
                            var columnKey = $(this).val();
                            var columnName = $(this).next('label').text().trim();
                            var $widthInput = $('input[name="column_widths[' + columnKey +
                                ']"]');
                            var val = parseInt($widthInput.val());

                            console.log('Column:', columnKey, 'Width:', val);

                            if (isNaN(val) || val < 5) {
                                console.warn('Width too small for column:', columnKey);
                                invalidInputs.push(columnKey);
                                errorMessages.push('⚠️ تحذير: عرض عمود "' + columnName +
                                    '" يجب أن يكون 5 بكسل على الأقل (القيمة الحالية: ' +
                                    $widthInput.val() + ')');
                                $widthInput.addClass('is-invalid');
                            } else if (val > 500) {
                                console.warn('Width too large for column:', columnKey);
                                invalidInputs.push(columnKey);
                                errorMessages.push('⚠️ تحذير: عرض عمود "' + columnName +
                                    '" يجب أن يكون 500 بكسل كحد أقصى (القيمة الحالية: ' +
                                    val + ')');
                                $widthInput.addClass('is-invalid');
                            } else {
                                $widthInput.removeClass('is-invalid');
                            }
                        });

                        if (invalidInputs.length > 0) {
                            e.preventDefault();
                            alert('⚠️ تحذير: يوجد أخطاء في عرض الأعمدة\n\n' + errorMessages.join(
                                '\n\n') + '\n\nالحد الأدنى: 5 بكسل\nالحد الأقصى: 500 بكسل');
                            // التركيز على أول حقل خاطئ
                            $('input[name="column_widths[' + invalidInputs[0] + ']"]').focus();
                            return false;
                        }

                        return true;
                    });
                });
            }

            // Start initialization
            initInvoiceTemplates();
        })();
    </script>
@endpush
