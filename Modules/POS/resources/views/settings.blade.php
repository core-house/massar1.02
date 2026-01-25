@extends('pos::layouts.master')

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
                <div class="row g-4">
                    <div class="col-md-6">
                        <label for="def_pos_client" class="form-label fw-bold">
                            <i class="fas fa-user me-2 text-primary"></i>
                            العميل الافتراضي
                        </label>
                        <select class="form-select form-select-lg" id="def_pos_client" name="def_pos_client">
                            <option value="">-- اختر العميل الافتراضي --</option>
                            @foreach($clientsAccounts as $client)
                                <option value="{{ $client->id }}" {{ $settings && $settings->def_pos_client == $client->id ? 'selected' : '' }}>
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
                            <option value="">-- اختر المخزن الافتراضي --</option>
                            @foreach($stores as $store)
                                <option value="{{ $store->id }}" {{ $settings && $settings->def_pos_store == $store->id ? 'selected' : '' }}>
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
                            <option value="">-- اختر الموظف الافتراضي --</option>
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
                            <option value="">-- اختر الصندوق الافتراضي --</option>
                            @foreach($cashAccounts as $cashAccount)
                                <option value="{{ $cashAccount->id }}" {{ $settings && $settings->def_pos_fund == $cashAccount->id ? 'selected' : '' }}>
                                    {{ $cashAccount->aname }}
                                </option>
                            @endforeach
                        </select>
                        <small class="form-text text-muted">سيتم اختيار هذا الصندوق تلقائياً عند إنشاء معاملة جديدة</small>
                    </div>
                </div>
                
                {{-- إعدادات الميزان --}}
                <hr class="my-4">
                <h5 class="mb-3">
                    <i class="fas fa-weight me-2 text-warning"></i>
                    إعدادات الميزان
                </h5>
                <div class="row g-4">
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
                
                <div class="mt-4 d-flex justify-content-end gap-2">
                    <button type="button" class="btn btn-secondary btn-lg" onclick="resetSettings()">
                        <i class="fas fa-undo me-2"></i>
                        إعادة تعيين
                    </button>
                    <button type="submit" class="btn btn-primary btn-lg">
                        <i class="fas fa-save me-2"></i>
                        حفظ الإعدادات
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

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
                document.getElementById('def_pos_client').value = '';
                document.getElementById('def_pos_store').value = '';
                document.getElementById('def_pos_employee').value = '';
                document.getElementById('def_pos_fund').value = '';
                document.getElementById('enable_scale_items').checked = false;
                document.getElementById('scale_code_prefix').value = '';
                document.getElementById('scale_code_digits').value = '5';
                document.getElementById('scale_quantity_digits').value = '5';
                document.getElementById('scale_quantity_divisor').value = '100';
                
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
