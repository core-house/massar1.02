@extends('admin.dashboard')
{{-- Dynamic Sidebar --}}
@section('sidebar')
    @include('components.sidebar.crm')
@endsection

@section('content')
    @include('components.breadcrumb', [
        'title' => __('Clients'),
        'breadcrumb_items' => [
            ['label' => __('Home'), 'url' => route('admin.dashboard')],
            ['label' => __('Clients'), 'url' => route('clients.index')],
            ['label' => __('Create')],
        ],
    ])
    @push('styles')
        <style>
            .form-select {
                border: 2px solid #e9ecef;
                border-radius: 8px;
                padding: 10px 12px;
                transition: all 0.3s ease;
            }

            .form-select:focus {
                border-color: #0d6efd;
                box-shadow: 0 0 0 0.2rem rgba(13, 110, 253, 0.25);
            }

            .status-container {
                background-color: #f8f9fa;
                border-radius: 8px;
                padding: 15px;
                border: 2px solid #e9ecef;
            }

            .form-switch .form-check-input:checked {
                background-color: #2821eb;
            }

            .form-switch .form-check-input:focus {
                box-shadow: 0 0 0 0.25rem rgba(25, 135, 84, 0.25);
            }

            .status-label {
                font-weight: 600;
                color: #495057;
            }

            @media (max-width: 768px) {
                .status-container {
                    margin-top: 15px;
                }
            }
        </style>
    @endpush

    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-body">
                    <form action="{{ route('clients.store') }}" method="POST">
                        @csrf
                        <div class="row">
                            {{-- الاسم --}}
                            <div class="mb-3 col-lg-4">
                                <label class="form-label">{{ __('Company Name') }}</label>
                                <input type="text" name="cname" class="form-control">
                                @error('cname')
                                    <small class="text-danger">{{ $message }}</small>
                                @enderror
                            </div>

                            {{-- البريد --}}
                            <div class="mb-3 col-lg-4">
                                <label class="form-label">{{ __('Email') }}</label>
                                <input type="email" name="email" class="form-control">
                                @error('email')
                                    <small class="text-danger">{{ $message }}</small>
                                @enderror
                            </div>

                            {{-- الهاتف 1 --}}
                            <div class="mb-3 col-lg-4">
                                <label class="form-label">{{ __('Primary Phone') }}</label>
                                <input type="text" name="phone" class="form-control">
                                @error('phone')
                                    <small class="text-danger">{{ $message }}</small>
                                @enderror
                            </div>

                            {{-- الهاتف 2 --}}
                            <div class="mb-3 col-lg-4">
                                <label class="form-label">{{ __('Alternative Phone') }}</label>
                                <input type="text" name="phone2" class="form-control">
                            </div>

                            {{-- العنوان 1 --}}
                            <div class="mb-3 col-lg-4">
                                <label class="form-label">{{ __('Main Address / Branch Address') }}</label>
                                <input type="text" name="address" class="form-control">
                            </div>

                            {{-- العنوان 2 --}}
                            <div class="mb-3 col-lg-4">
                                <label class="form-label">{{ __('Address 2') }}</label>
                                <input type="text" name="address2" class="form-control">
                            </div>

                            {{-- تاريخ الميلاد --}}
                            {{-- <div class="mb-3 col-lg-4">
                                <label class="form-label">تاريخ الميلاد</label>
                                <input type="date" name="date_of_birth" class="form-control">
                            </div> --}}

                            {{-- الرقم القومي --}}
                            {{-- <div class="mb-3 col-lg-4">
                                <label class="form-label">الرقم القومي</label>
                                <input type="text" name="national_id" class="form-control">
                            </div> --}}

                            <div class="mb-3 col-lg-4">
                                <label class="form-label">{{ __('Job') }}</label>
                                <input type="text" name="job" class="form-control">
                            </div>

                            {{-- السجل التجاري --}}
                            <div class="mb-3 col-lg-4">
                                <label class="form-label">{{ __('Commercial Register') }}</label>
                                <input type="text" name="commercial_register" class="form-control">
                            </div>

                            {{-- الشهادة الضريبية --}}
                            <div class="mb-3 col-lg-4">
                                <label class="form-label">{{ __('Tax Certificate') }}</label>
                                <input type="text" name="tax_certificate" class="form-control">
                            </div>

                            {{-- شخص للتواصل --}}
                            <div class="mb-3 col-lg-4">
                                <label class="form-label">{{ __('Contact Person') }}</label>
                                <input type="text" name="contact_person" class="form-control">
                            </div>

                            {{-- هاتف التواصل --}}
                            <div class="mb-3 col-lg-4">
                                <label class="form-label">{{ __('Contact Phone') }}</label>
                                <input type="text" name="contact_phone" class="form-control">
                            </div>

                            {{-- العلاقة --}}
                            {{-- <div class="mb-3 col-lg-4">
                                <label class="form-label">صلة القرابة</label>
                                <input type="text" name="contact_relation" class="form-control">
                            </div> --}}

                            {{-- معلومات إضافية --}}
                            <div class="mb-3 col-lg-12">
                                <label class="form-label">{{ __('Notes') }}</label>
                                <textarea name="info" class="form-control" rows="2"></textarea>
                            </div>

                            {{-- النوع والحالة --}}
                            <div class="row g-3">
                                <!-- حقل النوع -->
                                <div class="col-md-3 mb-3">
                                    <label for="client_type_id" class="form-label">{{ __('Client Type') }}</label>
                                    <select name="client_type_id" id="client_type_id" class="form-select" required>
                                        <option value="">{{ __('Select Client Type') }}</option>
                                        @foreach ($clientTypes as $type)
                                            <option value="{{ $type->id }}"
                                                {{ old('client_type_id') == $type->id ? 'selected' : '' }}>
                                                {{ $type->title }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

                                <!-- حقل المستخدم المسؤول -->
                                <div class="col-md-3 mb-3">
                                    <label for="assigned_user_id" class="form-label">{{ __('Assigned User') }}</label>
                                    <select name="assigned_user_id" id="assigned_user_id" class="form-select">
                                        <option value="">{{ __('Select User') }}</option>
                                        @foreach (\App\Models\User::all() as $user)
                                            <option value="{{ $user->id }}"
                                                {{ old('assigned_user_id') == $user->id ? 'selected' : '' }}>
                                                {{ $user->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

                                {{-- <div class="col-lg-3 col-md-6">
                                    <label class="form-label">النوع</label>
                                    <select name="gender" id="gender" class="form-select">
                                        <option value="">اختر النوع</option>
                                        <option value="male">ذكر</option>
                                        <option value="female">أنثى</option>
                                    </select>
                                </div> --}}

                                <div class="mb-3 col-lg-3">
                                    <label class="form-label">{{ __('Client Category') }}</label>
                                    <select name="client_category_id" class="form-select">
                                        <option value="">{{ __('Select Category') }}</option>
                                        @foreach ($categories as $category)
                                            <option value="{{ $category->id }}"
                                                {{ old('client_category_id') == $category->id ? 'selected' : '' }}>
                                                {{ $category->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('client_category_id')
                                        <small class="text-danger">{{ $message }}</small>
                                    @enderror
                                </div>

                                <!-- حقل الحالة -->
                                <div class="col-lg-3 col-md-6">
                                    <label class="form-label">{{ __('Status') }}</label>
                                    <div class="status-container d-flex align-items-center justify-content-between">
                                        <span class="status-label">{{ __('Active') }}</span>
                                        <div class="form-check form-switch m-0">
                                            <input type="checkbox" class="form-check-input" id="is_active" name="is_active"
                                                value="1" checked>
                                        </div>
                                    </div>
                                </div>
                                <x-branches::branch-select :branches="$branches" />

                            </div>
                        </div>

                        <div class="d-flex justify-content-start mt-4">
                            <button type="submit" class="btn btn-main me-2">
                                <i class="las la-save"></i> {{ __('Save') }}
                            </button>

                            <a href="{{ route('clients.index') }}" class="btn btn-danger">
                                <i class="las la-times"></i> {{ __('Cancel') }}
                            </a>
                        </div>

                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
