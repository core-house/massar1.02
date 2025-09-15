@props(['branches', 'selected' => null])

@if ($branches->count() > 1)
    <div class="mb-3 col-lg-3">
        <label class="form-label" for="branch_id">{{ __('الفرع') }}</label>
        <select class="form-control" id="branch_id" name="branch_id">
            @foreach ($branches as $branch)
                <option value="{{ $branch->id }}" {{ old('branch_id', $selected) == $branch->id ? 'selected' : '' }}>
                    {{ $branch->name }}
                </option>
            @endforeach
        </select>
        @error('branch_id')
            <small class="text-danger">{{ $message }}</small>
        @enderror
    </div>
@elseif($branches->count() === 1)
    <input type="hidden" name="branch_id" value="{{ $branches->first()->id }}">
@endif
