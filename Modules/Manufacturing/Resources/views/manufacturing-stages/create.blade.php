@extends('admin.dashboard')
@section('sidebar')
    @include('components.sidebar.manufacturing')
@endsection
@section('content')
    @include('components.breadcrumb', [
        'title' => __('مراحل التصنيع'),
        'items' => [
            ['label' => __('الرئيسيه'), 'url' => route('admin.dashboard')],
            ['label' => __('مراحل التصنيع'), 'url' => route('manufacturing.stages.index')],
            ['label' => __('إنشاء')],
        ],
    ])

    <div class="row">
        <div class="col-lg-12">

            <div class="card">
                <div class="card-header">
                    <h2>إضافة مرحلة جديدة</h2>
                </div>

                <div class="card-body">
                    <form action="{{ route('manufacturing.stages.store') }}" method="POST" onsubmit="disableButton()">
                        @csrf
                        <div class="row">
                            <div class="mb-3 col-lg-3">
                                <label class="form-label" for="name">اسم المرحلة</label>
                                <input type="text" class="form-control" id="name" name="name"
                                    placeholder="ادخل اسم المرحلة" value="{{ old('name') }}">
                                @error('name')
                                    <small class="text-danger">{{ $message }}</small>
                                @enderror
                            </div>

                            <div class="mb-3 col-lg-8">
                                <label class="form-label" for="description">الوصف</label>
                                <textarea class="form-control" id="description" name="description" rows="3" placeholder="ادخل وصف المرحلة">{{ old('description') }}</textarea>
                                @error('description')
                                    <small class="text-danger">{{ $message }}</small>
                                @enderror
                            </div>

                            <x-branches::branch-select :branches="$branches" />


                        </div>

                        <div class="d-flex justify-content-start mt-4">
                            <button type="submit" class="btn btn-primary me-2" id="submitBtn">
                                <i class="las la-save"></i> حفظ
                            </button>

                            <a href="{{ route('manufacturing.stages.index') }}" class="btn btn-danger">
                                <i class="las la-times"></i> إلغاء
                            </a>
                        </div>
                    </form>
                </div>
            </div>

        </div>
    </div>
@endsection
