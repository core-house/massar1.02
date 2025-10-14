@extends('admin.dashboard')

@section('sidebar')
    @include('components.sidebar.inquiries')
@endsection

@section('content')
    @include('components.breadcrumb', [
        'title' => __('إضافة حجم مشروع'),
        'items' => [
            ['label' => __('الرئيسيه'), 'url' => route('admin.dashboard')],
            ['label' => __('أحجام المشاريع'), 'url' => route('project-size.index')],
            ['label' => __('إنشاء')],
        ],
    ])

    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-header">
                    <h2>إضافة حجم مشروع جديد</h2>
                </div>
                <div class="card-body">
                    <form action="{{ route('project-size.store') }}" method="POST">
                        @csrf
                        <div class="row">
                            <div class="mb-3 col-lg-4">
                                <label for="name" class="form-label">الاسم</label>
                                <input type="text" name="name" id="name" class="form-control"
                                    placeholder="ادخل الاسم" value="{{ old('name') }}">
                                @error('name')
                                    <small class="text-danger">{{ $message }}</small>
                                @enderror
                            </div>

                            <div class="mb-3 col-lg-6">
                                <label for="description" class="form-label">الوصف</label>
                                <textarea name="description" id="description" class="form-control" rows="3" placeholder="اكتب وصف مختصر">{{ old('description') }}</textarea>
                            </div>
                        </div>

                        <div class="d-flex justify-content-start mt-4">
                            <button type="submit" class="btn btn-primary me-2"><i class="las la-save"></i> حفظ</button>
                            <a href="{{ route('project-size.index') }}" class="btn btn-danger"><i class="las la-times"></i>
                                إلغاء</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
