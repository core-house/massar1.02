@extends('admin.dashboard')

{{-- Dynamic Sidebar --}}
@section('sidebar')
    @include('components.sidebar-wrapper', ['sections' => ['crm', 'accounts']])
@endsection

@section('content')
    @include('components.breadcrumb', [
        'title' => __('مصدر الفرص'),
        'items' => [
            ['label' => __('الرئيسيه'), 'url' => route('admin.dashboard')],
            ['label' => __('مصدر الفرص'), 'url' => route('chance-sources.index')],
            ['label' => __('تعديل')],
        ],
    ])

    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-header">
                    <h2>تعديل مصدر الفرص</h2>
                </div>
                <div class="card-body">
                    <form action="{{ route('chance-sources.update', $chanceSource->id) }}" method="POST">
                        @csrf
                        @method('PUT')
                        <div class="row">
                            <div class="mb-3 col-lg-4">
                                <label class="form-label" for="title">العنوان</label>
                                <input type="text" class="form-control" id="title" name="title"
                                    placeholder="ادخل الاسم" value="{{ old('title', $chanceSource->title) }}">
                                @error('title')
                                    <small class="text-danger">{{ $message }}</small>
                                @enderror
                            </div>
                        </div>

                        <div class="d-flex justify-content-start mt-4">
                            <button type="submit" class="btn btn-primary me-2">
                                <i class="las la-save"></i> حفظ
                            </button>

                            <a href="{{ route('clients.index') }}" class="btn btn-danger">
                                <i class="las la-times"></i> إلغاء
                            </a>
                        </div>

                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
