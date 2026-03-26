@extends('tenancy::layouts.admin-central')

@section('sidebar')
    @include('tenancy::layouts.admin-sidebar')
@endsection

@section('content')
    @include('components.breadcrumb', [
        'title' => __('Add New Plan'),
        'items' => [
            ['label' => __('Home'), 'url' => route('admin.dashboard')],
            ['label' => __('Plans'), 'url' => route('plans.index')],
            ['label' => __('Add New')],
        ],
    ])

    <div class="row">
        <div class="col-lg-8">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">{{ __('Plan Details') }}</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('plans.store') }}" method="POST">
                        @csrf
                        <div class="row mb-3">
                            <div class="col-md-12">
                                <label class="form-label">{{ __('Plan Name') }}</label>
                                <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name') }}" required>
                                @error('name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label">{{ __('Amount') }}</label>
                                <input type="number" step="0.01" name="amount" class="form-control @error('amount') is-invalid @enderror" value="{{ old('amount') }}" required>
                                @error('amount')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">{{ __('Duration (Days)') }}</label>
                                <input type="number" name="duration_days" class="form-control @error('duration_days') is-invalid @enderror" value="{{ old('duration_days', 30) }}" required>
                                @error('duration_days')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label">{{ __('Max Users') }} ({{ __('Optional') }})</label>
                                <input type="number" name="max_users" class="form-control @error('max_users') is-invalid @enderror" value="{{ old('max_users') }}">
                                @error('max_users')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">{{ __('Max Branches') }} ({{ __('Optional') }})</label>
                                <input type="number" name="max_branches" class="form-control @error('max_branches') is-invalid @enderror" value="{{ old('max_branches') }}">
                                @error('max_branches')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="mb-3">
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" name="status" value="1" id="statusSwitch" {{ old('status', true) ? 'checked' : '' }}>
                                <label class="form-check-label" for="statusSwitch">{{ __('Active Status') }}</label>
                            </div>
                        </div>

                        <div class="d-flex justify-content-end">
                            <a href="{{ route('plans.index') }}" class="btn btn-secondary me-2">{{ __('Cancel') }}</a>
                            <button type="submit" class="btn btn-main">{{ __('Create Plan') }}</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
