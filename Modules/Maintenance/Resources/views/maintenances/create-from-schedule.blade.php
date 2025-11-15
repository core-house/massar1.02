
@extends('admin.dashboard')

@section('sidebar')
    @include('components.sidebar.service')
@endsection

@section('content')
    @include('components.breadcrumb', [
        'title' => __('إنشاء صيانة من جدول دوري'),
        'items' => [
            ['label' => __('الرئيسية'), 'url' => route('admin.dashboard')],
            ['label' => __('الصيانة الدورية'), 'url' => route('periodic.maintenances.index')],
            ['label' => __('إنشاء صيانة')],
        ],
    ])

    <div class="row">
        <div class="col-lg-12">
            {{-- معلومات الجدول الدوري --}}
            <div class="card mb-3">
                <div class="card-header bg-info text-white">
                    <h5 class="mb-0"><i class="las la-info-circle"></i> معلومات الجدول الدوري</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-3">
                            <strong>العميل:</strong> {{ $schedule->client_name }}<br>
                            <strong>التليفون:</strong> {{ $schedule->client_phone }}
                        </div>
                        <div class="col-md-3">
                            <strong>البند:</strong> {{ $schedule->item_name }}<br>
                            <strong>رقم البند:</strong> {{ $schedule->item_number }}
                        </div>
                        <div class="col-md-3">
                            <strong>نوع الصيانة:</strong> {{ $schedule->serviceType->name }}<br>
                            <strong>التكرار:</strong> {{ $schedule->getFrequencyLabel() }}
                        </div>
                        <div class="col-md-3">
                            <strong>الصيانة القادمة:</strong> {{ $schedule->next_maintenance_date->format('Y-m-d') }}<br>
                            @if ($schedule->isOverdue())
                                <span class="badge bg-danger">متأخرة</span>
                            @elseif($schedule->isMaintenanceDueSoon())
                                <span class="badge bg-warning">قريباً</span>
                            @else
                                <span class="badge bg-success">في الموعد</span>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            {{-- فورم إنشاء الصيانة --}}
            <div class="card">
                <div class="card-header">
                    <h2>{{ __('إضافة عملية صيانة جديدة') }}</h2>
                </div>
                <div class="card-body">
                    <form action="{{ route('maintenances.store') }}" method="POST">
                        @csrf

                        {{-- حقل مخفي للربط بالجدول الدوري --}}
                        <input type="hidden" name="periodic_schedule_id" value="{{ $schedule->id }}">

                        <div class="row">
                            {{-- اسم العميل --}}
                            <div class="mb-3 col-lg-4">
                                <label class="form-label" for="client_name">{{ __('اسم العميل') }}</label>
                                <input type="text" class="form-control" id="client_name" name="client_name"
                                    placeholder="ادخل اسم العميل" value="{{ old('client_name', $schedule->client_name) }}">
                                @error('client_name')
                                    <small class="text-danger">{{ $message }}</small>
                                @enderror
                            </div>

                            {{-- رقم التليفون --}}
                            <div class="mb-3 col-lg-4">
                                <label class="form-label" for="client_phone">{{ __('رقم التليفون') }}</label>
                                <input type="text" class="form-control" id="client_phone" name="client_phone"
                                    placeholder="ادخل رقم التليفون"
                                    value="{{ old('client_phone', $schedule->client_phone) }}">
                                @error('client_phone')
                                    <small class="text-danger">{{ $message }}</small>
                                @enderror
                            </div>

                            {{-- البند --}}
                            <div class="mb-3 col-lg-4">
                                <label class="form-label" for="item_name">{{ __('البند') }}</label>
                                <input type="text" class="form-control" id="item_name" name="item_name"
                                    placeholder="ادخل البند" value="{{ old('item_name', $schedule->item_name) }}">
                                @error('item_name')
                                    <small class="text-danger">{{ $message }}</small>
                                @enderror
                            </div>

                            {{-- التاريخ --}}
                            <div class="mb-3 col-lg-4">
                                <label class="form-label" for="date">{{ __('تاريخ الصيانة') }}</label>
                                <input type="date" class="form-control" id="date" name="date"
                                    value="{{ old('date', now()->format('Y-m-d')) }}">
                                @error('date')
                                    <small class="text-danger">{{ $message }}</small>
                                @enderror
                            </div>

                            {{-- تاريخ الاستحقاق --}}
                            <div class="mb-3 col-lg-4">
                                <label class="form-label" for="accural_date">{{ __('تاريخ الاستحقاق') }}</label>
                                <input type="date" class="form-control" id="accural_date" name="accural_date"
                                    value="{{ old('accural_date', $schedule->next_maintenance_date->format('Y-m-d')) }}">
                                @error('accural_date')
                                    <small class="text-danger">{{ $message }}</small>
                                @enderror
                            </div>

                            {{-- رقم البند --}}
                            <div class="mb-3 col-lg-4">
                                <label class="form-label" for="item_number">{{ __('رقم البند') }}</label>
                                <input type="text" class="form-control" id="item_number" name="item_number"
                                    placeholder="ادخل رقم البند" value="{{ old('item_number', $schedule->item_number) }}">
                                @error('item_number')
                                    <small class="text-danger">{{ $message }}</small>
                                @enderror
                            </div>

                            {{-- نوع الصيانة --}}
                            <div class="mb-3 col-lg-4">
                                <label class="form-label" for="service_type_id">{{ __('نوع الصيانة') }}</label>
                                <select class="form-select" id="service_type_id" name="service_type_id">
                                    <option value="">{{ __('اختر نوع الصيانة') }}</option>
                                    @foreach (\Modules\Maintenance\Models\ServiceType::all() as $type)
                                        <option value="{{ $type->id }}" @selected(old('service_type_id', $schedule->service_type_id) == $type->id)>
                                            {{ $type->name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('service_type_id')
                                    <small class="text-danger">{{ $message }}</small>
                                @enderror
                            </div>

                            {{-- الحالة --}}
                            <div class="mb-3 col-lg-4">
                                <label class="form-label" for="status">{{ __('الحالة') }}</label>
                                <select class="form-select" id="status" name="status">
                                    <option value="">{{ __('اختر الحالة') }}</option>
                                    @foreach (\Modules\Maintenance\Enums\MaintenanceStatus::cases() as $status)
                                        <option value="{{ $status->value }}" @selected(old('status') == $status->value)>
                                            {{ $status->label() }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('status')
                                    <small class="text-danger">{{ $message }}</small>
                                @enderror
                            </div>

                            {{-- الفرع --}}
                            <div class="mb-3 col-lg-4">
                                <input type="hidden" name="branch_id" value="{{ $schedule->branch_id }}">
                            </div>
                        </div>

                        <div class="alert alert-warning">
                            <i class="las la-exclamation-triangle"></i>
                            <strong>ملاحظة:</strong> عند حفظ هذه الصيانة، سيتم تحديث موعد الصيانة القادمة في الجدول الدوري
                            تلقائياً.
                        </div>

                        <div class="d-flex justify-content-start mt-4">
                            <button type="submit" class="btn btn-primary me-2">
                                <i class="las la-save"></i> {{ __('حفظ وتحديث الجدول') }}
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
@endsection
