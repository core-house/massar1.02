@extends('admin.dashboard')

@section('sidebar')
    @include('components.sidebar.daily_progress')
@endsection

@section('content')
    @include('components.breadcrumb', [
        'title' => 'تعديل التصنيف',
        'items' => [
            ['label' => __('الرئيسيه'), 'url' => route('admin.dashboard')],
            ['label' => 'تصنيفات بنود الأعمال', 'url' => route('work-item-categories.index')],
            ['label' => 'تعديل'],
        ],
    ])

    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-body">
                    <form action="{{ route('work-item-categories.update', $category->id) }}" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="mb-3">
                            <label for="name" class="form-label required">{{ __('general.name') }}</label>
                            <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name"
                                value="{{ old('name', $category->name) }}" required>
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="d-flex justify-content-end">
                            <a href="{{ route('work-item-categories.index') }}" class="btn btn-secondary me-2">
                                {{ __('general.cancel') }}
                            </a>
                            <button type="submit" class="btn btn-primary">
                                {{ __('general.save') }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
