@extends('tenancy::layouts.admin-central')

@section('sidebar')
    @include('tenancy::layouts.admin-sidebar')
@endsection

@section('content')
    @include('components.breadcrumb', [
        'title' => __('Create Tenant'),
        'items' => [
            ['label' => __('Home'), 'url' => route('admin.dashboard')],
            ['label' => __('Tenants'), 'url' => route('tenancy.index')],
            ['label' => __('Create')],
        ],
    ])

    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title mb-0">
                        <i class="fas fa-plus-circle me-2"></i>{{ __('Create New Tenant') }}
                    </h4>
                </div>
                <div class="card-body">
                    @if ($errors->any())
                        <div class="alert alert-danger">
                            <ul class="mb-0">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form action="{{ route('tenancy.store') }}" method="POST">
                        @csrf

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="subdomain" class="form-label">
                                        {{ __('Subdomain') }} <span class="text-danger">*</span>
                                    </label>
                                    <input type="text" class="form-control @error('subdomain') is-invalid @enderror"
                                        id="subdomain" name="subdomain" value="{{ old('subdomain') }}"
                                        placeholder="company-name" required>
                                    <small class="form-text text-muted">
                                        {{ __('Only lowercase letters, numbers, and hyphens. Cannot start or end with hyphen.') }}
                                    </small>
                                    @error('subdomain')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="name" class="form-label">
                                        {{ __('Company Name') }} <span class="text-danger">*</span>
                                    </label>
                                    <input type="text" class="form-control @error('name') is-invalid @enderror"
                                        id="name" name="name" value="{{ old('name') }}"
                                        placeholder="{{ __('Enter company name') }}" required>
                                    @error('name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="alert alert-info">
                            <i class="fas fa-info-circle me-2"></i>
                            <strong>{{ __('Note:') }}</strong>
                            {{ __('After creating the tenant, the database will be created automatically and all tenant migrations will be run.') }}
                        </div>

                        <div class="d-flex justify-content-end gap-2">
                            <a href="{{ route('tenancy.index') }}" class="btn btn-secondary">
                                <i class="fas fa-times me-2"></i>{{ __('Cancel') }}
                            </a>
                            <button type="submit" class="btn btn-main">
                                <i class="fas fa-save me-2"></i>{{ __('Create Tenant') }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
