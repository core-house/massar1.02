{{-- resources/views/maintenance/periodic/edit.blade.php --}}
@extends('admin.dashboard')

@section('sidebar')
    @include('components.sidebar.service')
@endsection

@section('content')
    @include('components.breadcrumb', [
        'title' => __('الصيانة الدورية'),
        'items' => [
            ['label' => __('الرئيسية'), 'url' => route('admin.dashboard')],
            ['label' => __('الصيانة الدورية'), 'url' => route('periodic.maintenances.index')],
            ['label' => __('تعديل')],
        ],
    ])

    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-header">
                    <h2>{{ __('تعديل جدول الصيانة الدورية') }}</h2>
                </div>
                <div class="card-body">
                    <form action="{{ route('periodic.maintenances.update', $periodicMaintenance->id) }}" method="POST">
                        @csrf
                        @method('PUT')
                        <div class="row">
                            {{-- اسم العميل --}}
                            <div class="mb-3 col-lg-4">
                                <label class="form-label" for="client_name">{{ __('اسم العميل') }} <span
                                        class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="client_name" name="client_name"
                                    placeholder="ادخل اسم العميل"
                                    value="{{ old('client_name', $periodicMaintenance->client_name) }}" required>
                                @error('client_name')
                                    <small class="text-danger">{{ $message }}</small>
                                @enderror
                            </div>

                            {{-- رقم التليفون --}}
                            <div class="mb-3 col-lg-4">
                                <label class="form-label" for="client_phone">{{ __('رقم التليفون') }} <span
                                        class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="client_phone" name="client_phone"
                                    placeholder="ادخل رقم التليفون"
                                    value="{{ old('client_phone', $periodicMaintenance->client_phone) }}" required>
                                @error('client_phone')
                                    <small class="text-danger">{{ $message }}</small>
                                @enderror
                            </div>

                            {{-- البند --}}
                            <div class="mb-3 col-lg-4">
                                <label class="form-label" for="item_name">{{ __('اسم البند') }} <span
                                        class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="item_name" name="item_name"
                                    placeholder="مثال: فلتر" value="{{ old('item_name', $periodicMaintenance->item_name) }}"
                                    required>
                                @error('item_name')
                                    <small class="text-danger">{{ $message }}</small>
                                @enderror
                            </div>

                            {{-- رقم البند --}}
                            <div class="mb-3 col-lg-4">
                                <label class="form-label" for="item_number">{{ __('رقم البند') }} <span
                                        class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="item_number" name="item_number"
                                    placeholder="ادخل رقم البند"
                                    value="{{ old('item_number', $periodicMaintenance->item_number) }}" required>
                                @error('item_number')
                                    <small class="text-danger">{{ $message }}</small>
                                @enderror
                            </div>

                            {{-- نوع الصيانة --}}
                            <div class="mb-3 col-lg-4">
                                <label class="form-label" for="service_type_id">{{ __('نوع الصيانة') }} <span
                                        class="text-danger">*</span></label>
                                <select class="form-select" id="service_type_id" name="service_type_id" required>
                                    <option value="">{{ __('اختر نوع الصيانة') }}</option>
                                    @foreach ($types as $type)
                                        <option value="{{ $type->id }}" @selected(old('service_type_id', $periodicMaintenance->service_type_id) == $type->id)>
                                            {{ $type->name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('service_type_id')
                                    <small class="text-danger">{{ $message }}</small>
                                @enderror
                            </div>

                            {{-- تاريخ البداية --}}
                            <div class="mb-3 col-lg-4">
                                <label class="form-label" for="start_date">{{ __('تاريخ بداية الجدول') }} <span
                                        class="text-danger">*</span></label>
                                <input type="date" class="form-control" id="start_date" name="start_date"
                                    value="{{ old('start_date', $periodicMaintenance->start_date?->format('Y-m-d')) }}"
                                    required>
                                @error('start_date')
                                    <small class="text-danger">{{ $message }}</small>
                                @enderror
                            </div>

                            {{-- نوع التكرار --}}
                            <div class="mb-3 col-lg-4">
                                <label class="form-label" for="frequency_type">{{ __('تكرار الصيانة') }} <span
                                        class="text-danger">*</span></label>
                                <select class="form-select" id="frequency_type" name="frequency_type" required>
                                    <option value="">{{ __('اختر التكرار') }}</option>
                                    <option value="daily" @selected(old('frequency_type', $periodicMaintenance->frequency_type) == 'daily')>يومي</option>
                                    <option value="weekly" @selected(old('frequency_type', $periodicMaintenance->frequency_type) == 'weekly')>أسبوعي</option>
                                    <option value="monthly" @selected(old('frequency_type', $periodicMaintenance->frequency_type) == 'monthly')>شهري</option>
                                    <option value="quarterly" @selected(old('frequency_type', $periodicMaintenance->frequency_type) == 'quarterly')>ربع سنوي (3 شهور)</option>
                                    <option value="semi_annual" @selected(old('frequency_type', $periodicMaintenance->frequency_type) == 'semi_annual')>نصف سنوي (6 شهور)</option>
                                    <option value="annual" @selected(old('frequency_type', $periodicMaintenance->frequency_type) == 'annual')>سنوي</option>
                                    <option value="custom_days" @selected(old('frequency_type', $periodicMaintenance->frequency_type) == 'custom_days')>مدة مخصصة (بالأيام)</option>
                                </select>
                                @error('frequency_type')
                                    <small class="text-danger">{{ $message }}</small>
                                @enderror
                            </div>

                            {{-- عدد الأيام المخصص --}}
                            <div class="mb-3 col-lg-4" id="custom_days_field"
                                style="display: {{ old('frequency_type', $periodicMaintenance->frequency_type) == 'custom_days' ? 'block' : 'none' }};">
                                <label class="form-label" for="frequency_value">{{ __('عدد الأيام') }}</label>
                                <input type="number" class="form-control" id="frequency_value" name="frequency_value"
                                    placeholder="مثال: 180"
                                    value="{{ old('frequency_value', $periodicMaintenance->frequency_value) }}"
                                    min="1" step="1">
                                @error('frequency_value')
                                    <small class="text-danger">{{ $message }}</small>
                                @enderror
                            </div>

                            {{-- التنبيه قبل --}}
                            <div class="mb-3 col-lg-4">
                                <label class="form-label"
                                    for="notification_days_before">{{ __('إرسال تنبيه قبل (يوم)') }} <span
                                        class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="notification_days_before"
                                    name="notification_days_before" placeholder="مثال: 7"
                                    value="{{ old('notification_days_before', $periodicMaintenance->notification_days_before) }}"
                                    min="1" step="1" required>
                                <small class="text-muted">سيتم إرسال تنبيه قبل موعد الصيانة بهذا العدد من الأيام</small>
                                @error('notification_days_before')
                                    <small class="text-danger">{{ $message }}</small>
                                @enderror
                            </div>

                            {{-- الحالة --}}
                            <div class="mb-3 col-lg-4">
                                <label class="form-label" for="is_active">{{ __('حالة الجدول') }}</label>
                                <select class="form-select" id="is_active" name="is_active">
                                    <option value="1" @selected(old('is_active', $periodicMaintenance->is_active) == 1)>نشط</option>
                                    <option value="0" @selected(old('is_active', $periodicMaintenance->is_active) == 0)>معطل</option>
                                </select>
                                @error('is_active')
                                    <small class="text-danger">{{ $message }}</small>
                                @enderror
                            </div>

                            {{-- معلومات إضافية --}}
                            <div class="mb-3 col-lg-12">
                                <div class="alert alert-info">
                                    <strong>معلومات الصيانة:</strong><br>
                                    <i class="las la-calendar"></i> الصيانة القادمة:
                                    <strong>{{ $periodicMaintenance->next_maintenance_date?->format('Y-m-d') }}</strong><br>
                                    @if ($periodicMaintenance->last_maintenance_date)
                                        <i class="las la-check-circle"></i> آخر صيانة:
                                        <strong>{{ $periodicMaintenance->last_maintenance_date->format('Y-m-d') }}</strong>
                                    @else
                                        <i class="las la-info-circle"></i> لم يتم تنفيذ صيانة بعد
                                    @endif
                                </div>
                            </div>

                            {{-- ملاحظات --}}
                            <div class="mb-3 col-lg-12">
                                <label class="form-label" for="notes">{{ __('ملاحظات') }}</label>
                                <textarea class="form-control" id="notes" name="notes" rows="3" placeholder="أي ملاحظات إضافية">{{ old('notes', $periodicMaintenance->notes) }}</textarea>
                                @error('notes')
                                    <small class="text-danger">{{ $message }}</small>
                                @enderror
                            </div>
                        </div>

                        <div class="d-flex justify-content-start mt-4">
                            <button type="submit" class="btn btn-primary me-2">
                                <i class="las la-save"></i> {{ __('حفظ التعديلات') }}
                            </button>

                            <a href="{{ route('periodic.maintenances.index') }}" class="btn btn-danger">
                                <i class="las la-times"></i> {{ __('إلغاء') }}
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            document.getElementById('frequency_type').addEventListener('change', function() {
                const customDaysField = document.getElementById('custom_days_field');
                const frequencyValue = document.getElementById('frequency_value');

                if (this.value === 'custom_days') {
                    customDaysField.style.display = 'block';
                    frequencyValue.required = true;
                } else {
                    customDaysField.style.display = 'none';
                    frequencyValue.required = false;
                }
            });

            // تشغيل عند التحميل إذا كان هناك قيمة قديمة
            if (document.getElementById('frequency_type').value === 'custom_days') {
                document.getElementById('custom_days_field').style.display = 'block';
            }
        </script>
    @endpush
@endsection
