<div class="row g-3">
    <div class="col-md-12">
        <label class="form-label">{{ __('decumintations.title') }} <span class="text-danger">*</span></label>
        <input type="text" name="title" class="form-control @error('title') is-invalid @enderror"
               value="{{ old('title', $document->title ?? '') }}" required>
        @error('title') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>

    <div class="col-md-6">
        <label class="form-label">{{ __('decumintations.category') }}</label>
        <select name="category_id" class="form-select @error('category_id') is-invalid @enderror">
            <option value="">{{ __('decumintations.choose_category') }}</option>
            @foreach($categories as $cat)
                <option value="{{ $cat->id }}"
                    {{ old('category_id', $document->category_id ?? '') == $cat->id ? 'selected' : '' }}>
                    {{ $cat->name }}
                </option>
            @endforeach
        </select>
        @error('category_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>

    <div class="col-md-6">
        <label class="form-label">{{ __('decumintations.expiry_date') }}</label>
        <input type="date" name="expiry_date" class="form-control @error('expiry_date') is-invalid @enderror"
               value="{{ old('expiry_date', isset($document) ? $document->expiry_date?->format('Y-m-d') : '') }}">
        @error('expiry_date') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>

    <div class="col-md-12">
        <label class="form-label">{{ __('decumintations.description') }}</label>
        <textarea name="description" class="form-control @error('description') is-invalid @enderror"
                  rows="3">{{ old('description', $document->description ?? '') }}</textarea>
        @error('description') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>

    <div class="col-md-12">
        <label class="form-label">
            {{ __('decumintations.file') }}
            @if(!isset($document)) <span class="text-danger">*</span> @endif
        </label>
        <input type="file" name="file" class="form-control @error('file') is-invalid @enderror"
               {{ !isset($document) ? 'required' : '' }}>
        @if(isset($document))
            <small class="text-muted">{{ __('common.current') }}: {{ $document->file_name }}</small>
        @endif
        @error('file') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>

    <div class="col-md-12">
        <div class="form-check">
            <input type="hidden" name="is_confidential" value="0">
            <input type="checkbox" name="is_confidential" value="1" class="form-check-input" id="is_confidential"
                   {{ old('is_confidential', $document->is_confidential ?? false) ? 'checked' : '' }}>
            <label class="form-check-label" for="is_confidential">
                {{ __('decumintations.is_confidential') }}
            </label>
        </div>
    </div>
</div>
