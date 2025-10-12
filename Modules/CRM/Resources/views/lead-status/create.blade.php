@extends('admin.dashboard')

{{-- Dynamic Sidebar --}}
@section('sidebar')
    @include('components.sidebar.crm')
    @include('components.sidebar.accounts')
@endsection

@section('content')
    @include('components.breadcrumb', [
        'title' => __('حالات الفرص'),
        'items' => [
            ['label' => __('الرئيسيه'), 'url' => route('admin.dashboard')],
            ['label' => __('حالات الفرص'), 'url' => route('lead-status.index')],
            ['label' => __('انشاء')],
        ],
    ])

    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-header">
                    <h2>اضافة جديده</h2>
                </div>
                <div class="card-body">
                    <form action="{{ route('lead-status.store') }}" method="POST" onsubmit="disableButton()">
                        @csrf
                        <div class="row">

                            <div class="mb-3 col-lg-4">
                                <label class="form-label" for="name">العنوان</label>
                                <input type="text" class="form-control" id="name" name="name"
                                    placeholder="ادخل الاسم">
                                @error('name')
                                    <small class="text-danger">{{ $message }}</small>
                                @enderror
                            </div>

                            <div class="mb-3 col-lg-2">
                                <label class="form-label" for="order_column">الترتيب </label>
                                <input type="text" class="form-control" id="order_column" name="order_column"
                                    placeholder="ادخل الترتيب" pattern="\d*" inputmode="numeric"
                                    oninput="this.value = this.value.replace(/[^0-9]/g, '')">
                                @error('order_column')
                                    <small class="text-danger">{{ $message }}</small>
                                @enderror
                            </div>

                            <div class="mb-3 col-lg-4">
                                <label class="form-label" for="color">اللون</label>
                                <input type="color" class="form-control form-control-color" id="color" name="color"
                                    value="#563d7c" title="اختر اللون">
                                @error('color')
                                    <small class="text-danger">{{ $message }}</small>
                                @enderror
                            </div>

                            <x-branches::branch-select :branches="$branches" />

                        </div>

                        <div class="d-flex justify-content-start mt-4">
                            <button type="submit" class="btn btn-primary me-2" id="submitBtn">
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
