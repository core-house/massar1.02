@extends('pos::layouts.master')

@push('styles')
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet">
<style>
    .select2-container--default .select2-selection--single {
        height: calc(3rem + 2px);
        border: 1px solid #dee2e6;
        border-radius: 0.375rem;
        padding: 0.5rem 0.75rem;
        font-family: 'Cairo', sans-serif;
        font-size: 1rem;
    }
    .select2-container--default .select2-selection--single .select2-selection__rendered {
        line-height: 2rem;
        color: #212529;
        padding-right: 0;
        padding-left: 20px;
    }
    .select2-container--default .select2-selection--single .select2-selection__arrow {
        height: calc(3rem + 2px);
        left: 8px;
        right: auto;
    }
    .select2-container--default .select2-selection--single .select2-selection__placeholder {
        color: #6c757d;
    }
    .select2-dropdown {
        font-family: 'Cairo', sans-serif;
        font-size: 0.95rem;
        direction: rtl;
    }
    .select2-search--dropdown .select2-search__field {
        direction: rtl;
        font-family: 'Cairo', sans-serif;
    }
    .select2-container { width: 100% !important; }
</style>
@endpush

@section('content')
<div class="container py-4">
    <div class="mb-4">
        <div class="header-navigation d-flex justify-content-between align-items-center">
            <a href="{{ route('pos.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-right me-2"></i>
                العودة للصفحة الرئيسية
            </a>
            <h3 class="mb-0">
                <i class="fas fa-cog me-2"></i>
                إعدادات الكاشير
            </h3>
            <div></div>
        </div>
    </div>

    <div class="card shadow-sm">
        <div class="card-header bg-primary text-white">
            <h5 class="mb-0">
                <i class="fas fa-sliders-h me-2"></i>
                الإعدادات الافتراضية
            </h5>
        </div>
        <div class="card-body">
            <form id="cashierSettingsForm">
                @csrf
                
                <!-- Action Buttons - Top Right -->
                <div class="mb-4 d-flex justify-content-end gap-2">
                    <button type="submit" class="btn btn-primary btn-lg">
                        <i class="fas fa-save me-2"></i>
                        حفظ الإعدادات
                    </button>
                    <button type="button" class="btn btn-secondary btn-lg" onclick="resetSettings()">
                        <i class="fas fa-undo me-2"></i>
                        إعادة تعيين
                    </button>
                </div>
                
                <div class="row g-4">
                    <div class="col-md-6">
                        <label for="def_pos_client" class="form-label fw-bold">
                            <i class="fas fa-user me-2 text-primary"></i>
                            العميل الافتراضي
                        </label>
                        <select class="form-select form-select-lg" id="def_pos_client" name="def_pos_client">
                            @foreach($clientsAccounts as $client)
                                <option value="{{ $client->id }}" {{ ($settings && $settings->def_pos_client == $client->id) || (!$settings?->def_pos_client && $loop->first) ? 'selected' : '' }}>
                                    {{ $client->aname }}
                                </option>
                            @endforeach
                        </select>
                        <small class="form-text text-muted">سيتم اختيار هذا العميل تلقائياً عند إنشاء معاملة جديدة</small>
                    </div>
                    <div class="col-md-6">
                        <label for="def_pos_store" class="form-label fw-bold">
                            <i class="fas fa-warehouse me-2 text-primary"></i>
                            المخزن الافتراضي
                        </label>
                        <select class="form-select form-select-lg" id="def_pos_store" name="def_pos_store">
                            @foreach($stores as $store)
                                <option value="{{ $store->id }}" {{ ($settings && $settings->def_pos_store == $store->id) || (!$settings?->def_pos_store && $loop->first) ? 'selected' : '' }}>
                                    {{ $store->aname }}
                                </option>
                            @endforeach
                        </select>
                        <small class="form-text text-muted">سيتم اختيار هذا المخزن تلقائياً عند إنشاء معاملة جديدة</small>
                    </div>
                    <div class="col-md-6">
                        <label for="def_pos_employee" class="form-label fw-bold">
                            <i class="fas fa-user-tie me-2 text-primary"></i>
                            الموظف الافتراضي
                        </label>
                        <select class="form-select form-select-lg" id="def_pos_employee" name="def_pos_employee">
                            <option value="">-- بدون موظف --</option>
                            @foreach($employees as $employee)
                                <option value="{{ $employee->id }}" {{ $settings && $settings->def_pos_employee == $employee->id ? 'selected' : '' }}>
                                    {{ $employee->aname }}
                                </option>
                            @endforeach
                        </select>
                        <small class="form-text text-muted">سيتم اختيار هذا الموظف تلقائياً عند إنشاء معاملة جديدة</small>
                    </div>
                    <div class="col-md-6">
                        <label for="def_pos_fund" class="form-label fw-bold">
                            <i class="fas fa-money-bill-wave me-2 text-primary"></i>
                            الصندوق الافتراضي
                        </label>
                        <select class="form-select form-select-lg" id="def_pos_fund" name="def_pos_fund">
                            @foreach($cashAccounts as $cashAccount)
                                <option value="{{ $cashAccount->id }}" {{ ($settings && $settings->def_pos_fund == $cashAccount->id) || (!$settings?->def_pos_fund && $loop->first) ? 'selected' : '' }}>
                                    {{ $cashAccount->aname }}
                                </option>
                            @endforeach
                        </select>
                        <small class="form-text text-muted">سيتم اختيار هذا الصندوق تلقائياً عند إنشاء معاملة جديدة</small>
                    </div>
                    <div class="col-md-6">
                        <label for="def_pos_bank" class="form-label fw-bold">
                            <i class="fas fa-university me-2 text-primary"></i>
                            البنك الافتراضي
                        </label>
                        <select class="form-select form-select-lg" id="def_pos_bank" name="def_pos_bank">
                            @foreach($bankAccounts as $bank)
                                <option value="{{ $bank->id }}" {{ ($settings && isset($settings->def_pos_bank) && $settings->def_pos_bank == $bank->id) || (!isset($settings->def_pos_bank) && $loop->first) ? 'selected' : '' }}>
                                    {{ $bank->aname }}
                                </option>
                            @endforeach
                        </select>
                        <small class="form-text text-muted">سيتم اختيار هذا البنك تلقائياً عند الدفع بالبطاقة</small>
                    </div>
                    <div class="col-md-6">
                        <label for="def_pos_price_group" class="form-label fw-bold">
                            <i class="fas fa-tags me-2 text-primary"></i>
                            السعر الافتراضي (Take Away)
                        </label>
                        <select class="form-select form-select-lg" id="def_pos_price_group" name="def_pos_price_group">
                            @foreach($priceGroups as $pg)
                                <option value="{{ $pg->id }}" {{ ($settings && isset($settings->def_pos_price_group) && $settings->def_pos_price_group == $pg->id) || (!isset($settings->def_pos_price_group) && $loop->first) ? 'selected' : '' }}>
                                    {{ $pg->name }}
                                </option>
                            @endforeach
                        </select>
                        <small class="form-text text-muted">مجموعة الأسعار المستخدمة افتراضياً في Take Away</small>
                    </div>
                </div>
                
                {{-- ===== إعدادات المطبخ ===== --}}
                <hr class="my-4">
                <h5 class="mb-3">
                    <i class="fas fa-utensils me-2 text-danger"></i>
                    إعدادات المطبخ (فاتورة المطعم)
                </h5>
                <div class="alert alert-info py-2 mb-3" role="alert">
                    <i class="fas fa-info-circle me-1"></i>
                    هذه الحسابات تُستخدم في القيود المحاسبية عند حفظ فاتورة المطعم (pro_type = 103).
                    الحسابات المُعلَّمة بـ <span class="text-danger">*</span> مطلوبة لتشغيل قيود التصنيع.
                </div>
                <div class="row g-4">
                    <div class="col-md-6">
                        <label for="restaurant_kitchen_store" class="form-label fw-bold">
                            <i class="fas fa-store me-2 text-danger"></i>
                            مخزن المطبخ <span class="text-danger">*</span>
                        </label>
                        <select class="form-select form-select-lg" id="restaurant_kitchen_store" name="restaurant_kitchen_store">
                            <option value="">-- اختر مخزن المطبخ --</option>
                            @foreach($allAccounts as $acc)
                                <option value="{{ $acc->id }}"
                                    {{ $settings && $settings->restaurant_kitchen_store == $acc->id ? 'selected' : '' }}>
                                    {{ $acc->code }} - {{ $acc->aname }}
                                </option>
                            @endforeach
                        </select>
                        <small class="text-muted">المخزن الذي تُحوَّل إليه الخامات عند التصنيع</small>
                    </div>

                    <div class="col-md-6">
                        <label for="restaurant_operating_account" class="form-label fw-bold">
                            <i class="fas fa-cogs me-2 text-danger"></i>
                            مركز التشغيل (حساب وسيط) <span class="text-danger">*</span>
                        </label>
                        <select class="form-select form-select-lg" id="restaurant_operating_account" name="restaurant_operating_account">
                            <option value="">-- اختر حساب مركز التشغيل --</option>
                            @foreach($allAccounts as $acc)
                                <option value="{{ $acc->id }}"
                                    {{ $settings && $settings->restaurant_operating_account == $acc->id ? 'selected' : '' }}>
                                    {{ $acc->code }} - {{ $acc->aname }}
                                </option>
                            @endforeach
                        </select>
                        <small class="text-muted">الحساب الوسيط بين المخزن ومركز التشغيل</small>
                    </div>

                    <div class="col-md-6">
                        <label for="restaurant_sales_account" class="form-label fw-bold">
                            <i class="fas fa-chart-line me-2 text-success"></i>
                            حساب المبيعات
                        </label>
                        <select class="form-select form-select-lg" id="restaurant_sales_account" name="restaurant_sales_account">
                            <option value="">-- اختر حساب المبيعات --</option>
                            @foreach($allAccounts as $acc)
                                <option value="{{ $acc->id }}"
                                    {{ $settings && $settings->restaurant_sales_account == $acc->id ? 'selected' : '' }}>
                                    {{ $acc->code }} - {{ $acc->aname }}
                                </option>
                            @endforeach
                        </select>
                        <small class="text-muted">الحساب الدائن في قيد المبيعات (افتراضي: 47)</small>
                    </div>

                    <div class="col-md-6">
                        <label for="restaurant_cogs_account" class="form-label fw-bold">
                            <i class="fas fa-boxes me-2 text-warning"></i>
                            حساب تكلفة البضاعة المباعة
                        </label>
                        <select class="form-select form-select-lg" id="restaurant_cogs_account" name="restaurant_cogs_account">
                            <option value="">-- اختر حساب التكلفة --</option>
                            @foreach($allAccounts as $acc)
                                <option value="{{ $acc->id }}"
                                    {{ $settings && $settings->restaurant_cogs_account == $acc->id ? 'selected' : '' }}>
                                    {{ $acc->code }} - {{ $acc->aname }}
                                </option>
                            @endforeach
                        </select>
                        <small class="text-muted">المدين في قيد تكلفة البضاعة المباعة</small>
                    </div>

                    <div class="col-md-6">
                        <label for="restaurant_inventory_account" class="form-label fw-bold">
                            <i class="fas fa-warehouse me-2 text-warning"></i>
                            حساب المخزون
                        </label>
                        <select class="form-select form-select-lg" id="restaurant_inventory_account" name="restaurant_inventory_account">
                            <option value="">-- اختر حساب المخزون --</option>
                            @foreach($allAccounts as $acc)
                                <option value="{{ $acc->id }}"
                                    {{ $settings && $settings->restaurant_inventory_account == $acc->id ? 'selected' : '' }}>
                                    {{ $acc->code }} - {{ $acc->aname }}
                                </option>
                            @endforeach
                        </select>
                        <small class="text-muted">الدائن في قيد تكلفة البضاعة المباعة</small>
                    </div>
                </div>

                {{-- إعدادات الميزان --}}
                <hr class="my-4">
                <h5 class="mb-3">
                    <i class="fas fa-weight me-2 text-warning"></i>
                    إعدادات الميزان
                </h5>                <div class="row g-4">
                    <div class="col-md-12">
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" id="enable_scale_items" name="enable_scale_items" value="1" {{ $settings && $settings->enable_scale_items ? 'checked' : '' }}>
                            <label class="form-check-label fw-bold" for="enable_scale_items">
                                <i class="fas fa-toggle-on me-2 text-success"></i>
                                السماح بأصناف الميزان
                            </label>
                            <small class="form-text text-muted d-block">تفعيل استخدام أصناف الميزان في نظام نقاط البيع</small>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <label for="scale_code_prefix" class="form-label fw-bold">
                            <i class="fas fa-hashtag me-2 text-primary"></i>
                            كود البداية
                        </label>
                        <input type="text" class="form-control form-control-lg" id="scale_code_prefix" name="scale_code_prefix" 
                               value="{{ $settings->scale_code_prefix ?? '' }}" 
                               placeholder="مثال: 2" maxlength="10">
                        <small class="form-text text-muted">الكود الذي يبدأ به كود الصنف في الميزان (مثال: 2)</small>
                    </div>
                    <div class="col-md-6">
                        <label for="scale_code_digits" class="form-label fw-bold">
                            <i class="fas fa-sort-numeric-up me-2 text-primary"></i>
                            عدد الأرقام لكود الصنف
                        </label>
                        <input type="number" class="form-control form-control-lg" id="scale_code_digits" name="scale_code_digits" 
                               value="{{ $settings->scale_code_digits ?? 5 }}" min="1" max="10">
                        <small class="form-text text-muted">عدد الأرقام المستخدمة في كود الصنف (افتراضي: 5)</small>
                    </div>
                    <div class="col-md-6">
                        <label for="scale_quantity_digits" class="form-label fw-bold">
                            <i class="fas fa-sort-numeric-down me-2 text-primary"></i>
                            عدد الأرقام للكمية
                        </label>
                        <input type="number" class="form-control form-control-lg" id="scale_quantity_digits" name="scale_quantity_digits" 
                               value="{{ $settings->scale_quantity_digits ?? 5 }}" min="1" max="10">
                        <small class="form-text text-muted">عدد الأرقام المستخدمة في كمية الصنف (افتراضي: 5)</small>
                    </div>
                    <div class="col-md-6">
                        <label for="scale_quantity_divisor" class="form-label fw-bold">
                            <i class="fas fa-divide me-2 text-primary"></i>
                            القسمة على
                        </label>
                        <select class="form-select form-select-lg" id="scale_quantity_divisor" name="scale_quantity_divisor">
                            <option value="10" {{ $settings && $settings->scale_quantity_divisor == 10 ? 'selected' : '' }}>10</option>
                            <option value="100" {{ $settings && $settings->scale_quantity_divisor == 100 ? 'selected' : (!isset($settings->scale_quantity_divisor) ? 'selected' : '') }}>100</option>
                            <option value="1000" {{ $settings && $settings->scale_quantity_divisor == 1000 ? 'selected' : '' }}>1000</option>
                        </select>
                        <small class="form-text text-muted">القيمة التي يتم قسمة الكمية عليها (افتراضي: 100)</small>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>
    $(document).ready(function() {
        const select2Ids = [
            '#def_pos_client',
            '#def_pos_store',
            '#def_pos_employee',
            '#def_pos_fund',
            '#restaurant_kitchen_store',
            '#restaurant_operating_account',
            '#restaurant_sales_account',
            '#restaurant_cogs_account',
            '#restaurant_inventory_account',
        ];

        select2Ids.forEach(function(id) {
            $(id).select2({
                dir: 'rtl',
                language: {
                    noResults: function() { return 'لا توجد نتائج'; },
                    searching: function() { return 'جاري البحث...'; },
                },
                allowClear: true,
                placeholder: $(id).find('option:first').text(),
            });
        });
    });
</script>
@endpush

<script>
    // معالجة نموذج إعدادات الكاشير
    document.getElementById('cashierSettingsForm').addEventListener('submit', function(e) {
        e.preventDefault();
        
        const formData = new FormData(this);
        
        // إضافة قيمة checkbox إذا لم تكن محددة
        const enableScaleItems = document.getElementById('enable_scale_items');
        if (!enableScaleItems.checked) {
            formData.append('enable_scale_items', '0');
        }
        
        const submitButton = this.querySelector('button[type="submit"]');
        const originalText = submitButton.innerHTML;
        
        // تعطيل الزر وإظهار حالة التحميل
        submitButton.disabled = true;
        submitButton.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>جاري الحفظ...';
        
        fetch('{{ route("pos.api.settings.update") }}', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json',
            },
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                Swal.fire({
                    icon: 'success',
                    title: 'نجح',
                    text: data.message || 'تم تحديث إعدادات الكاشير بنجاح',
                    timer: 2000,
                    showConfirmButton: false,
                    toast: true,
                    position: 'top-end'
                });
            } else {
                Swal.fire({
                    icon: 'error',
                    title: 'خطأ',
                    text: data.message || 'حدث خطأ أثناء تحديث الإعدادات',
                    confirmButtonText: 'موافق'
                });
            }
        })
        .catch(error => {
            console.error('Error:', error);
            Swal.fire({
                icon: 'error',
                title: 'خطأ',
                text: 'حدث خطأ أثناء تحديث الإعدادات',
                confirmButtonText: 'موافق'
            });
        })
        .finally(() => {
            // إعادة تفعيل الزر
            submitButton.disabled = false;
            submitButton.innerHTML = originalText;
        });
    });

    // دالة إعادة تعيين الإعدادات
    function resetSettings() {
        Swal.fire({
            title: 'هل أنت متأكد؟',
            text: 'سيتم إعادة تعيين جميع الإعدادات إلى القيم الافتراضية',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'نعم، إعادة تعيين',
            cancelButtonText: 'إلغاء'
        }).then((result) => {
            if (result.isConfirmed) {
                document.getElementById('def_pos_client').selectedIndex = 0;
                document.getElementById('def_pos_store').selectedIndex = 0;
                document.getElementById('def_pos_employee').value = '';
                document.getElementById('def_pos_fund').selectedIndex = 0;
                document.getElementById('def_pos_bank').selectedIndex = 0;
                document.getElementById('def_pos_price_group').selectedIndex = 0;
                document.getElementById('enable_scale_items').checked = false;
                document.getElementById('scale_code_prefix').value = '';
                document.getElementById('scale_code_digits').value = '5';
                document.getElementById('scale_quantity_digits').value = '5';
                document.getElementById('scale_quantity_divisor').value = '100';
                
                // إعادة تعيين إعدادات المطبخ
                ['restaurant_kitchen_store','restaurant_operating_account',
                 'restaurant_sales_account','restaurant_cogs_account',
                 'restaurant_inventory_account'].forEach(id => {
                    const el = document.getElementById(id);
                    if (el) el.value = '';
                });
                
                Swal.fire({
                    icon: 'success',
                    title: 'تم',
                    text: 'تم إعادة تعيين الإعدادات',
                    timer: 1500,
                    showConfirmButton: false,
                    toast: true,
                    position: 'top-end'
                });
            }
        });
    }
</script>
@endsection
