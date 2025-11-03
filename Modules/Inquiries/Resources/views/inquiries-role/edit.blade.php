@extends('admin.dashboard')

@section('sidebar')
    @include('components.sidebar.crm')
@endsection

@section('content')
    @include('components.breadcrumb', [
        'title' => __('الأدوار'),
        'items' => [
            ['label' => __('الرئيسيه'), 'url' => route('admin.dashboard')],
            ['label' => __('الأدوار'), 'url' => route('inquiries-roles.index')],
            ['label' => __('تعديل')],
        ],
    ])

    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-header">
                    <h2>تعديل الدور</h2>
                </div>
                <div class="card-body">
                    <form action="{{ route('inquiries-roles.update', $inquiries_role->id) }}" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="row">
                            <div class="mb-3 col-lg-4">
                                <label class="form-label" for="name">الاسم</label>
                                <input type="text" class="form-control" id="name" name="name"
                                    placeholder="ادخل اسم الدور" value="{{ old('name', $inquiries_role->name) }}">
                                @error('name')
                                    <small class="text-danger">{{ $message }}</small>
                                @enderror
                            </div>

                            <div class="mb-3 col-lg-8">
                                <label class="form-label" for="description">الوصف</label>
                                <input type="text" class="form-control" id="description" name="description"
                                    placeholder="ادخل وصف الدور" value="{{ old('description', $inquiries_role->description) }}">
                                @error('description')
                                    <small class="text-danger">{{ $message }}</small>
                                @enderror
                            </div>
                        </div>

                        <div class="d-flex justify-content-start mt-4">
                            <button type="submit" class="btn btn-primary me-2">
                                <i class="las la-save"></i> حفظ
                            </button>

                            <a href="{{ route('inquiries-roles.index') }}" class="btn btn-danger">
                                <i class="las la-times"></i> إلغاء
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
