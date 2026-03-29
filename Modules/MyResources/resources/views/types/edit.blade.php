@extends('admin.dashboard')

@section('sidebar')
    @include('components.sidebar.myresources')
@endsection

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="page-title-box">
                <h4 class="page-title">{{ __('myresources.edit_type') }}</h4>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <form action="{{ route('myresources.types.update', $type) }}" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="name" class="form-label">{{ __('myresources.name') }} <span class="text-danger">*</span></label>
                                <input type="text" name="name" id="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name', $type->name) }}" required>
                                @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="name_ar" class="form-label">{{ __('myresources.arabic_name') }}</label>
                                <input type="text" name="name_ar" id="name_ar" class="form-control @error('name_ar') is-invalid @enderror" value="{{ old('name_ar', $type->name_ar) }}">
                                @error('name_ar') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="resource_category_id" class="form-label">{{ __('myresources.main_category') }}</label>
                                <select name="resource_category_id" id="resource_category_id" class="form-select @error('resource_category_id') is-invalid @enderror">
                                    <option value="">{{ __('myresources.select_category') }}</option>
                                    @foreach($categories as $category)
                                        <option value="{{ $category->id }}" {{ old('resource_category_id', $type->resource_category_id) == $category->id ? 'selected' : '' }}>
                                            {{ $category->display_name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('resource_category_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>

                            <div class="col-md-6 mb-3 d-flex align-items-end">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="is_active" id="is_active" value="1" {{ old('is_active', $type->is_active) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="is_active">{{ __('myresources.active') }}</label>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-12 mb-3">
                                <label for="description" class="form-label">{{ __('myresources.description') }}</label>
                                <textarea name="description" id="description" rows="3" class="form-control @error('description') is-invalid @enderror">{{ old('description', $type->description) }}</textarea>
                                @error('description') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                        </div>

                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i> {{ __('myresources.save') }}
                            </button>
                            <a href="{{ route('myresources.types.index') }}" class="btn btn-secondary">
                                <i class="fas fa-times"></i> {{ __('myresources.cancel') }}
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
