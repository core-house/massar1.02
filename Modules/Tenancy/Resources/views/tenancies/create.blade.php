@extends('tenancy::layouts.admin-central')

@section('sidebar')
    @include('tenancy::layouts.admin-sidebar')
@endsection

@section('content')
    @include('components.breadcrumb', [
        'title' => __('Add New Tenant'),
        'items' => [['label' => __('Home'), 'url' => route('admin.dashboard')], ['label' => __('Tenants'), 'url' => route('tenancy.index')], ['label' => __('Add New')]],
    ])

    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">{{ __('Tenant Information') }}</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('tenancy.store') }}" method="POST">
                        @csrf
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">{{ __('Company Name') }}</label>
                                <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name') }}" required>
                                @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">{{ __('Subdomain') }}</label>
                                <div class="input-group">
                                    <input type="text" name="subdomain" class="form-control @error('subdomain') is-invalid @enderror" value="{{ old('subdomain') }}" required>
                                    <span class="input-group-text">.{{ str_replace('main.', '', parse_url(config('app.url'), PHP_URL_HOST) ?: 'localhost') }}</span>
                                    @error('subdomain') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">{{ __('Admin Email') }}</label>
                                <input type="email" name="admin_email" class="form-control @error('admin_email') is-invalid @enderror" value="{{ old('admin_email') }}" required>
                                @error('admin_email') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">{{ __('Admin Password') }}</label>
                                <input type="password" name="admin_password" class="form-control @error('admin_password') is-invalid @enderror" required>
                                @error('admin_password') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                        </div>

                        <hr>
                        <h6 class="mb-3">{{ __('Additional Details') }}</h6>

                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <label class="form-label">{{ __('Contact Number') }}</label>
                                <input type="text" name="contact_number" class="form-control @error('contact_number') is-invalid @enderror" value="{{ old('contact_number') }}">
                                @error('contact_number') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label">{{ __('Company Size') }}</label>
                                <select name="company_size" class="form-select @error('company_size') is-invalid @enderror">
                                    <option value="">{{ __('Select Size') }}</option>
                                    <option value="1-5" {{ old('company_size') == '1-5' ? 'selected' : '' }}>1-5 {{ __('Employees') }}</option>
                                    <option value="6-20" {{ old('company_size') == '6-20' ? 'selected' : '' }}>6-20 {{ __('Employees') }}</option>
                                    <option value="21-100" {{ old('company_size') == '21-100' ? 'selected' : '' }}>21-100 {{ __('Employees') }}</option>
                                    <option value="100+" {{ old('company_size') == '100+' ? 'selected' : '' }}>100+ {{ __('Employees') }}</option>
                                </select>
                                @error('company_size') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label">{{ __('User Position') }}</label>
                                <input type="text" name="user_position" class="form-control @error('user_position') is-invalid @enderror" value="{{ old('user_position') }}">
                                @error('user_position') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-12 mb-3">
                                <label class="form-label">{{ __('Address') }}</label>
                                <textarea name="address" class="form-control @error('address') is-invalid @enderror" rows="2">{{ old('address') }}</textarea>
                                @error('address') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                        </div>

                        <hr>
                        <h6 class="mb-3">{{ __('Subscription & Plan') }}</h6>

                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <label class="form-label">{{ __('Plan') }}</label>
                                <select name="plan_id" class="form-select @error('plan_id') is-invalid @enderror" required>
                                    <option value="">{{ __('Select Plan') }}</option>
                                    @foreach($plans as $plan)
                                        <option value="{{ $plan->id }}" {{ old('plan_id') == $plan->id ? 'selected' : '' }}>{{ $plan->name }}</option>
                                    @endforeach
                                </select>
                                @error('plan_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label">{{ __('Subscription Start') }}</label>
                                <input type="date" name="subscription_start_at" class="form-control @error('subscription_start_at') is-invalid @enderror" value="{{ old('subscription_start_at', date('Y-m-d')) }}">
                                @error('subscription_start_at') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label">{{ __('Subscription End') }}</label>
                                <input type="date" name="subscription_end_at" class="form-control @error('subscription_end_at') is-invalid @enderror" value="{{ old('subscription_end_at') }}">
                                @error('subscription_end_at') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">{{ __('Referral Code') }}</label>
                                <input type="text" name="referral_code" class="form-control @error('referral_code') is-invalid @enderror" value="{{ old('referral_code') }}">
                                @error('referral_code') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                            <div class="col-md-6 mb-3">
                                <div class="mt-4">
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" type="checkbox" name="status" value="1" id="statusSwitch" {{ old('status', true) ? 'checked' : '' }}>
                                        <label class="form-check-label" for="statusSwitch">{{ __('Active Status') }}</label>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="d-flex justify-content-end mt-4">
                            <a href="{{ route('tenancy.index') }}" class="btn btn-secondary me-2">{{ __('Cancel') }}</a>
                            <button type="submit" class="btn btn-main">{{ __('Create Tenant') }}</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
