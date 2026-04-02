@extends('admin.dashboard')

@section('sidebar')
    @include('components.sidebar.myresources')
@endsection

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="page-title-box">
                <h4 class="page-title">{{ __('myresources.add_new_status') }}</h4>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <form action="{{ route('myresources.statuses.store') }}" method="POST">
                        @csrf

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="name" class="form-label">{{ __('myresources.name') }} <span class="text-danger">*</span></label>
                                <input type="text" name="name" id="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name') }}" required>
                                @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="name_ar" class="form-label">{{ __('myresources.arabic_name') }}</label>
                                <input type="text" name="name_ar" id="name_ar" class="form-control @error('name_ar') is-invalid @enderror" value="{{ old('name_ar') }}">
                                @error('name_ar') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-12 mb-3">
                                <label for="description" class="form-label">{{ __('myresources.description') }}</label>
                                <textarea name="description" id="description" rows="3" class="form-control @error('description') is-invalid @enderror">{{ old('description') }}</textarea>
                                @error('description') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="icon" class="form-label">{{ __('myresources.icon') }}</label>
                                <input type="text" name="icon" id="icon" class="form-control @error('icon') is-invalid @enderror" value="{{ old('icon') }}" placeholder="fas fa-check-circle">
                                <small class="text-muted">{{ __('myresources.use_font_awesome_icons') }}</small>
                                @error('icon') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="color" class="form-label">{{ __('myresources.color') }}</label>
                                <select name="color" id="color" class="form-select @error('color') is-invalid @enderror">
                                    <option value="primary"   {{ old('color') == 'primary'   ? 'selected' : '' }}>{{ __('common.color_blue') }}</option>
                                    <option value="success"   {{ old('color') == 'success'   ? 'selected' : '' }}>{{ __('common.color_green') }}</option>
                                    <option value="info"      {{ old('color', 'info') == 'info' ? 'selected' : '' }}>{{ __('common.color_light') }}</option>
                                    <option value="warning"   {{ old('color') == 'warning'   ? 'selected' : '' }}>{{ __('common.color_yellow') }}</option>
                                    <option value="danger"    {{ old('color') == 'danger'    ? 'selected' : '' }}>{{ __('common.color_red') }}</option>
                                    <option value="secondary" {{ old('color') == 'secondary' ? 'selected' : '' }}>{{ __('common.color_gray') }}</option>
                                </select>
                                @error('color') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-12 mb-3">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="is_active" id="is_active" value="1" {{ old('is_active', true) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="is_active">{{ __('myresources.active') }}</label>
                                </div>
                            </div>
                        </div>

                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i> {{ __('myresources.save') }}
                            </button>
                            <a href="{{ route('myresources.statuses.index') }}" class="btn btn-secondary">
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
