@extends('admin.dashboard')

@section('sidebar')
    @include('components.sidebar.quality')
@endsection

@section('content')
    <div class="container-fluid">
        <div class="row mb-4">
            <div class="col-12">
                <h2>
                    <i class="fas fa-edit me-2"></i>{{ __('quality::quality.edit') }} {{ __('quality::quality.quality standard') }}:
                    {{ $standard->standard_code }}
                </h2>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item">
                            <a href="{{ route('quality.dashboard') }}">{{ __('quality::quality.quality') }}</a>
                        </li>
                        <li class="breadcrumb-item">
                            <a href="{{ route('quality.standards.index') }}">{{ __('quality::quality.quality standards') }}</a>
                        </li>
                        <li class="breadcrumb-item active" aria-current="page">{{ __('quality::quality.edit') }}</li>
                    </ol>
                </nav>
            </div>
        </div>

        <form action="{{ route('quality.standards.update', $standard) }}" method="POST">
            @csrf
            @method('PUT')
            <div class="row">
                <div class="col-lg-8">
                    <div class="card mb-4">
                        <div class="card-header">
                            <h5 class="mb-0">{{ __('quality::quality.standard details') }}</h5>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label required">{{ __('quality::quality.item') }}</label>
                                    <select name="item_id" class="form-select @error('item_id') is-invalid @enderror"
                                        required>
                                        <option value="">{{ __('quality::quality.select item') }}</option>
                                        @foreach ($items as $item)
                                            <option value="{{ $item->id }}"
                                                {{ old('item_id', $standard->item_id) == $item->id ? 'selected' : '' }}>
                                                {{ $item->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('item_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label class="form-label required">{{ __('quality::quality.standard code') }}</label>
                                    <input type="text" name="standard_code"
                                        class="form-control @error('standard_code') is-invalid @enderror"
                                        value="{{ old('standard_code', $standard->standard_code) }}" required>
                                    @error('standard_code')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-12 mb-3">
                                    <label class="form-label required">{{ __('quality::quality.standard name') }}</label>
                                    <input type="text" name="standard_name"
                                        class="form-control @error('standard_name') is-invalid @enderror"
                                        value="{{ old('standard_name', $standard->standard_name) }}" required>
                                    @error('standard_name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-12 mb-3">
                                    <label class="form-label">{{ __('quality::quality.description') }}</label>
                                    <textarea name="description" rows="3" class="form-control"
                                        placeholder="{{ __('quality::quality.detailed description of the standard...') }}">
                                    {{ old('description', $standard->description) }}
                                </textarea>
                                </div>

                                <div class="col-12 mb-3">
                                    <label class="form-label">{{ __('quality::quality.test method') }}</label>
                                    <textarea name="test_method" rows="3" class="form-control"
                                        placeholder="{{ __('quality::quality.explain the test execution method...') }}">
                                    {{ old('test_method', $standard->test_method) }}
                                </textarea>
                                </div>

                                <div class="col-12 mb-3">
                                    <label class="form-label">{{ __('quality::quality.notes') }}</label>
                                    <textarea name="notes" rows="3" class="form-control" placeholder="{{ __('quality::quality.additional notes...') }}">
                                    {{ old('notes', $standard->notes) }}
                                </textarea>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-lg-4">
                    <div class="card mb-4">
                        <div class="card-header">
                            <h5 class="mb-0">{{ __('quality::quality.test criteria') }}</h5>
                        </div>
                        <div class="card-body">
                            <div class="mb-3">
                                <label class="form-label required">{{ __('quality::quality.sample size') }}</label>
                                <input type="number" name="sample_size"
                                    class="form-control @error('sample_size') is-invalid @enderror"
                                    value="{{ old('sample_size', $standard->sample_size) }}" min="1" required>
                                @error('sample_size')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label class="form-label required">{{ __('quality::quality.test frequency') }}</label>
                                <select name="test_frequency"
                                    class="form-select @error('test_frequency') is-invalid @enderror" required>
                                    <option value="">{{ __('quality::quality.select frequency') }}</option>
                                    <option value="per_batch"
                                        {{ old('test_frequency', $standard->test_frequency) == 'per_batch' ? 'selected' : '' }}>
                                        {{ __('quality::quality.per batch') }}
                                    </option>
                                    <option value="daily"
                                        {{ old('test_frequency', $standard->test_frequency) == 'daily' ? 'selected' : '' }}>
                                        {{ __('quality::quality.daily') }}
                                    </option>
                                    <option value="weekly"
                                        {{ old('test_frequency', $standard->test_frequency) == 'weekly' ? 'selected' : '' }}>
                                        {{ __('quality::quality.weekly') }}
                                    </option>
                                    <option value="monthly"
                                        {{ old('test_frequency', $standard->test_frequency) == 'monthly' ? 'selected' : '' }}>
                                        {{ __('quality::quality.monthly') }}
                                    </option>
                                </select>
                                @error('test_frequency')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label class="form-label required">{{ __('quality::quality.acceptance threshold (%)') }}</label>
                                <input type="number" step="0.01" name="acceptance_threshold"
                                    class="form-control @error('acceptance_threshold') is-invalid @enderror"
                                    value="{{ old('acceptance_threshold', $standard->acceptance_threshold) }}"
                                    min="0" max="100" required>
                                @error('acceptance_threshold')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label class="form-label required">{{ __('quality::quality.max allowed defects') }}</label>
                                <input type="number" name="max_defects_allowed"
                                    class="form-control @error('max_defects_allowed') is-invalid @enderror"
                                    value="{{ old('max_defects_allowed', $standard->max_defects_allowed) }}" min="0"
                                    required>
                                @error('max_defects_allowed')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label class="form-label">{{ __('quality::quality.status') }}</label>
                                <select name="is_active" class="form-select">
                                    <option value="1"
                                        {{ old('is_active', $standard->is_active) == '1' ? 'selected' : '' }}>
                                        {{ __('quality::quality.active') }}
                                    </option>
                                    <option value="0"
                                        {{ old('is_active', $standard->is_active) == '0' ? 'selected' : '' }}>
                                        {{ __('quality::quality.inactive') }}
                                    </option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="d-grid gap-2">
                        <button type="submit" class="btn btn-warning btn-lg">
                            <i class="fas fa-save me-2"></i>{{ __('quality::quality.update standard') }}
                        </button>
                        <a href="{{ route('quality.standards.index') }}" class="btn btn-secondary">
                            <i class="fas fa-times me-2"></i>{{ __('quality::quality.cancel') }}
                        </a>
                    </div>
                </div>
            </div>
        </form>
    </div>
@endsection
