<div class="row g-3">
    <div class="col-md-12">
        <label class="form-label">{{ __('decumintations::decumintations.category_name') }} <span class="text-danger">*</span></label>
        <input type="text" name="name" class="form-control @error('name') is-invalid @enderror"
               value="{{ old('name', $category->name ?? '') }}" required>
        @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>

    <div class="col-md-6">
        <label class="form-label">{{ __('decumintations::decumintations.color') }}</label>
        <input type="color" name="color" class="form-control form-control-color @error('color') is-invalid @enderror"
               value="{{ old('color', $category->color ?? '#239d77') }}">
        @error('color') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>

    <div class="col-md-6">
        <label class="form-label">{{ __('decumintations::decumintations.icon') }}</label>
        <input type="text" name="icon" class="form-control @error('icon') is-invalid @enderror"
               value="{{ old('icon', $category->icon ?? 'las la-folder') }}"
               placeholder="las la-folder">
        @error('icon') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>

    <div class="col-md-12">
        <label class="form-label">{{ __('decumintations::decumintations.description') }}</label>
        <textarea name="description" class="form-control @error('description') is-invalid @enderror"
                  rows="3">{{ old('description', $category->description ?? '') }}</textarea>
        @error('description') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>
</div>
