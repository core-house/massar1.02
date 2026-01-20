@extends('admin.dashboard')

@section('sidebar')
    @include('components.sidebar.admin')
@endsection

@section('content')
    @include('components.breadcrumb', [
        'title' => __('Edit Tenant'),
        'items' => [
            ['label' => __('Home'), 'url' => route('admin.dashboard')],
            ['label' => __('Tenants'), 'url' => route('tenancy.index')],
            ['label' => __('Edit')],
        ],
    ])

    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title mb-0">
                        <i class="fas fa-edit me-2"></i>{{ __('Edit Tenant') }}
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

                    <form action="{{ route('tenancy.update', $tenant->id) }}" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="subdomain" class="form-label">
                                        {{ __('Subdomain') }} <span class="text-danger">*</span>
                                    </label>
                                    <input type="text" class="form-control @error('subdomain') is-invalid @enderror"
                                        id="subdomain" name="subdomain" value="{{ old('subdomain', $tenant->id) }}"
                                        placeholder="company-name" required readonly>
                                    <small class="form-text text-muted">
                                        {{ __('Subdomain cannot be changed after tenant creation.') }}
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
                                        id="name" name="name" value="{{ old('name', $tenant->name) }}"
                                        placeholder="{{ __('Enter company name') }}" required>
                                    @error('name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        @if ($domain)
                            <div class="mb-3">
                                <label class="form-label">{{ __('Current Domain') }}</label>
                                <input type="text" class="form-control" value="{{ $domain->domain }}" readonly>
                            </div>
                        @endif

                        <div class="d-flex justify-content-end gap-2">
                            <a href="{{ route('tenancy.index') }}" class="btn btn-secondary">
                                <i class="fas fa-times me-2"></i>{{ __('Cancel') }}
                            </a>
                            <button type="submit" class="btn btn-main">
                                <i class="fas fa-save me-2"></i>{{ __('Update Tenant') }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
