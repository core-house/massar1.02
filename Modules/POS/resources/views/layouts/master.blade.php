<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    
    <title>نظام نقاط البيع - {{ config('app.name') }}</title>
    
    <!-- Favicon -->
    <link rel="icon" href="{{ asset('assets/images/favicon.ico') }}" sizes="any">
    
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- Bootstrap RTL -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.rtl.min.css" rel="stylesheet">
    
    <!-- SweetAlert2 -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    
    <!-- POS Global Styles -->
    <style>
        :root {
            /* Mint Green Colors from Main Style */
            --mint-green-50: #e6faf5;
            --mint-green-100: #b3f0e0;
            --mint-green-200: #80e6cb;
            --mint-green-300: #4ddcb6;
            --mint-green-400: #34d3a3;
            --mint-green-500: #2ab88d;
            
            --pos-primary: #374151;
            --pos-success: #10b981;
            --pos-danger: #ef4444;
            --pos-warning: #f59e0b;
            --pos-info: #3b82f6;
            --pos-light: #f9fafb;
            --pos-dark: #111827;
            --pos-gray-50: #f9fafb;
            --pos-gray-100: #f3f4f6;
            --pos-gray-200: #e5e7eb;
            --pos-gray-300: #d1d5db;
            --pos-gray-400: #9ca3af;
            --pos-gray-500: #6b7280;
            --pos-gray-600: #4b5563;
            --pos-gray-700: #374151;
            --pos-gray-800: #1f2937;
            --pos-gray-900: #111827;
            --pos-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
            --pos-shadow-md: 0 4px 6px rgba(0, 0, 0, 0.1);
            --pos-border-radius: 12px;
            --pos-transition: all 0.2s ease;
        }

        * {
            box-sizing: border-box;
        }

        body {
            font-family: 'Cairo', sans-serif;
            font-size: 16px;
            line-height: 1.6;
            color: var(--pos-dark);
            background: #ffffff;
            margin: 0;
            padding: 0;
            direction: rtl;
            overflow-x: hidden;
        }

        .pos-fullscreen {
            height: 100vh;
            overflow: hidden;
        }

        /* إعدادات عامة للنصوص */
        h1, h2, h3, h4, h5, h6 {
            font-family: 'Cairo', sans-serif;
            font-weight: 600;
            margin-bottom: 1rem;
        }

        /* أزرار مخصصة */
        .btn-pos {
            font-family: 'Cairo', sans-serif;
            font-weight: 500;
            border-radius: var(--pos-border-radius);
            padding: 0.75rem 1.5rem;
            transition: var(--pos-transition);
            border: none;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
        }

        .btn-pos:hover {
            transform: translateY(-2px);
            box-shadow: var(--pos-shadow);
        }

        .btn-pos.btn-primary {
            background: var(--pos-gray-700);
            color: white;
        }

        .btn-pos.btn-primary:hover {
            background: var(--pos-gray-800);
        }

        .btn-pos.btn-success {
            background: var(--pos-success);
            color: white;
        }

        .btn-pos.btn-success:hover {
            background: #059669;
        }

        .btn-pos.btn-danger {
            background: var(--pos-danger);
            color: white;
        }

        .btn-pos.btn-danger:hover {
            background: #dc2626;
        }

        /* حقول الإدخال */
        .form-control-pos {
            font-family: 'Cairo', sans-serif;
            border: 2px solid var(--mint-green-200);
            border-radius: 8px;
            padding: 0.75rem 1rem;
            transition: var(--pos-transition);
            background: white;
            color: var(--pos-dark);
        }

        .form-control-pos:focus {
            border-color: var(--mint-green-300);
            box-shadow: 0 0 0 3px rgba(52, 211, 163, 0.1);
            outline: none;
        }

        /* بطاقات */
        .card-pos {
            background: white;
            border: 2px solid var(--mint-green-200);
            border-radius: var(--pos-border-radius);
            padding: 2rem;
            box-shadow: var(--pos-shadow);
            transition: var(--pos-transition);
        }

        .card-pos:hover {
            border-color: var(--mint-green-300);
            box-shadow: 0 4px 12px rgba(52, 211, 163, 0.15);
        }

        /* تحسينات للشاشات الصغيرة */
        @media (max-width: 768px) {
            body {
                font-size: 14px;
            }
            
            .card-pos {
                padding: 1rem;
                margin: 0.5rem;
            }
        }

        /* تأثيرات التحميل */
        .loading-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.5);
            display: flex;
            align-items: center;
            justify-content: center;
            z-index: 9999;
        }

        .loading-spinner {
            width: 50px;
            height: 50px;
            border: 5px solid #f3f3f3;
            border-top: 5px solid var(--pos-primary);
            border-radius: 50%;
            animation: spin 1s linear infinite;
        }

        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }

        /* تخصيص شريط التمرير */
        ::-webkit-scrollbar {
            width: 8px;
            height: 8px;
        }

        ::-webkit-scrollbar-track {
            background: #f1f1f1;
            border-radius: 4px;
        }

        ::-webkit-scrollbar-thumb {
            background: #c1c1c1;
            border-radius: 4px;
        }

        ::-webkit-scrollbar-thumb:hover {
            background: #a8a8a8;
        }

        /* تحسينات للطباعة */
        @media print {
            body {
                background: white !important;
                color: black !important;
            }
            
            .no-print {
                display: none !important;
            }
        }

        /* Dark Mode Styles */
        body.dark-mode {
            background: #111827;
            color: #f9fafb;
        }

        body.dark-mode .card-pos {
            background: #1f2937;
            border-color: #374151;
            color: #f9fafb;
        }

        body.dark-mode .form-control-pos {
            background: #1f2937;
            border-color: #374151;
            color: #f9fafb;
        }

        body.dark-mode .form-control-pos:focus {
            border-color: #6b7280;
            background: #1f2937;
        }

        /* Light Mode - Enhanced borders with mint green */
        body:not(.dark-mode) .card-pos {
            border-color: var(--mint-green-200);
        }

        body:not(.dark-mode) .form-control-pos {
            border-color: var(--mint-green-200);
        }

        body.dark-mode .btn-pos.btn-primary {
            background: #374151;
            color: #f9fafb;
        }

        body.dark-mode .btn-pos.btn-primary:hover {
            background: #4b5563;
        }

        body.dark-mode .loading-overlay {
            background: rgba(0, 0, 0, 0.8);
        }

        body.dark-mode .alert {
            background: #1f2937;
            border-color: #374151;
            color: #f9fafb;
        }

        body.dark-mode ::-webkit-scrollbar-track {
            background: #1f2937;
        }

        body.dark-mode ::-webkit-scrollbar-thumb {
            background: #4b5563;
        }

        body.dark-mode ::-webkit-scrollbar-thumb:hover {
            background: #6b7280;
        }
    </style>

    @livewireStyles
    @stack('styles')
</head>
<body>
    <!-- مؤشر التحميل -->
    <div id="loading-overlay" class="loading-overlay" style="display: none;">
        <div class="loading-spinner"></div>
    </div>

    <!-- المحتوى الأساسي -->
    <main class="pos-main">
        @yield('content')
    </main>

    <!-- رسائل التنبيه -->
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show position-fixed" style="top: 20px; right: 20px; z-index: 9999;">
            <i class="fas fa-check-circle me-2"></i>
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show position-fixed" style="top: 20px; right: 20px; z-index: 9999;">
            <i class="fas fa-exclamation-circle me-2"></i>
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if(session('warning'))
        <div class="alert alert-warning alert-dismissible fade show position-fixed" style="top: 20px; right: 20px; z-index: 9999;">
            <i class="fas fa-exclamation-triangle me-2"></i>
            {{ session('warning') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <!-- Scripts -->
    <!-- jQuery يجب أن يكون قبل Bootstrap -->
    <script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    @livewireScripts

    <!-- Register Service Worker for Offline Support -->
    <script>
        if ('serviceWorker' in navigator) {
            window.addEventListener('load', function() {
                navigator.serviceWorker.register('{{ asset("modules/pos/js/pos-service-worker.js") }}')
                    .then(function(registration) {
                        console.log('POS Service Worker registered:', registration.scope);
                    })
                    .catch(function(error) {
                        console.log('POS Service Worker registration failed:', error);
                    });
            });
        }
    </script>

    <!-- POS Global Scripts -->
    <script>
        // Dark Mode Initialization
        (function() {
            const savedTheme = localStorage.getItem('pos-dark-mode');
            const prefersDark = window.matchMedia('(prefers-color-scheme: dark)').matches;
            
            if (savedTheme === 'enabled' || (!savedTheme && prefersDark)) {
                document.body.classList.add('dark-mode');
            }
        })();

        // إعدادات عامة للPOS
        window.POS = {
            config: {
                currency: 'ريال',
                locale: 'ar-SA',
                dateFormat: 'YYYY-MM-DD',
                timeFormat: 'HH:mm'
            },
            
            // دوال مساعدة
            utils: {
                formatCurrency: function(amount) {
                    return new Intl.NumberFormat('ar-SA', {
                        style: 'currency',
                        currency: 'SAR',
                        minimumFractionDigits: 2
                    }).format(amount);
                },
                
                formatNumber: function(number) {
                    return new Intl.NumberFormat('ar-SA').format(number);
                },
                
                showLoading: function() {
                    document.getElementById('loading-overlay').style.display = 'flex';
                },
                
                hideLoading: function() {
                    document.getElementById('loading-overlay').style.display = 'none';
                },
                
                showToast: function(message, type = 'success') {
                    Swal.fire({
                        toast: true,
                        position: 'top-end',
                        icon: type,
                        title: message,
                        showConfirmButton: false,
                        timer: 3000,
                        timerProgressBar: true
                    });
                }
            }
        };

        // إعداد CSRF token للطلبات
        window.Laravel = {
            csrfToken: '{{ csrf_token() }}'
        };

        // إعداد Axios للطلبات
        if (window.axios) {
            window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';
            window.axios.defaults.headers.common['X-CSRF-TOKEN'] = window.Laravel.csrfToken;
        }

        // التعامل مع أخطاء الشبكة
        window.addEventListener('online', function() {
            POS.utils.showToast('تم استعادة الاتصال بالإنترنت', 'success');
        });

        window.addEventListener('offline', function() {
            POS.utils.showToast('انقطع الاتصال بالإنترنت', 'warning');
        });

        // إخفاء رسائل التنبيه تلقائياً
        document.addEventListener('DOMContentLoaded', function() {
            const alerts = document.querySelectorAll('.alert');
            alerts.forEach(function(alert) {
                setTimeout(function() {
                    const bsAlert = new bootstrap.Alert(alert);
                    bsAlert.close();
                }, 5000);
            });
        });

        // دعم اختصارات لوحة المفاتيح العامة
        document.addEventListener('keydown', function(e) {
            // F1 - التركيز على البحث بالباركود
            if (e.key === 'F1') {
                e.preventDefault();
                const barcodeInput = document.getElementById('barcodeSearch');
                if (barcodeInput) {
                    barcodeInput.focus();
                    barcodeInput.select();
                }
                return;
            }
            
            // F2 - التركيز على البحث عن الأصناف
            if (e.key === 'F2') {
                e.preventDefault();
                const productInput = document.getElementById('productSearch');
                if (productInput) {
                    productInput.focus();
                    productInput.select();
                }
                return;
            }
            
            // F12 - فتح modal الدفع
            if (e.key === 'F12') {
                e.preventDefault();
                const registerBtn = document.getElementById('registerBtn');
                if (registerBtn) {
                    registerBtn.click();
                }
                return;
            }
            
            // Ctrl+Home للعودة للصفحة الرئيسية
            if (e.ctrlKey && e.key === 'Home') {
                e.preventDefault();
                window.location.href = '{{ route("pos.index") }}';
            }
            
            // Alt+F4 للخروج (العودة للنظام الأساسي)
            if (e.altKey && e.key === 'F4') {
                e.preventDefault();
                window.location.href = '{{ url("/") }}';
            }
        });

        // تحسين الأداء للأجهزة اللوحية
        if ('ontouchstart' in window) {
            document.body.classList.add('touch-device');
        }

        // إعداد PWA (إذا كان مطلوباً)
        if ('serviceWorker' in navigator) {
            window.addEventListener('load', function() {
                // يمكن إضافة service worker هنا لاحقاً
            });
        }

        // معالجة أحداث Livewire
        document.addEventListener('livewire:init', function() {
            // معالجة حدث فتح نافذة الطباعة
            Livewire.on('open-print-window', function(data) {
                if (data.url) {
                    const printWindow = window.open(data.url, '_blank', 'width=800,height=600');
                    if (printWindow) {
                        printWindow.focus();
                        // إغلاق النافذة بعد الطباعة
                        printWindow.addEventListener('afterprint', function() {
                            printWindow.close();
                        });
                    } else {
                        POS.utils.showToast('تم منع فتح نافذة الطباعة. يرجى السماح بالنوافذ المنبثقة.', 'warning');
                    }
                }
            });

            // معالجة رسائل الخطأ
            Livewire.on('error', function(data) {
                if (data.title && data.text) {
                    Swal.fire({
                        title: data.title,
                        text: data.text,
                        icon: data.icon || 'error',
                        confirmButtonText: 'موافق',
                        confirmButtonColor: '#e74c3c'
                    });
                }
            });

            // معالجة رسائل النجاح
            Livewire.on('swal', function(data) {
                if (data.title && data.text) {
                    Swal.fire({
                        title: data.title,
                        text: data.text,
                        icon: data.icon || 'success',
                        confirmButtonText: 'موافق',
                        confirmButtonColor: '#27ae60'
                    });
                }
            });

            // معالجة رسائل التنبيه
            Livewire.on('alert', function(data) {
                if (data.type && data.message) {
                    POS.utils.showToast(data.message, data.type);
                }
            });
        });
    </script>

    @stack('scripts')
</body>
</html>
