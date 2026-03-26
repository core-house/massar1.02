@extends('tenancy::layouts.admin-central')

@section('sidebar')
    @include('tenancy::layouts.admin-sidebar')
@endsection

@section('content')
    @include('components.breadcrumb', [
        'title' => __('Edit Subscription'),
        'items' => [
            ['label' => __('Home'), 'url' => route('admin.dashboard')],
            ['label' => __('Subscriptions'), 'url' => route('subscriptions.index')],
            ['label' => __('Edit')],
        ],
    ])

    <div class="row">
        <div class="col-lg-8">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">{{ __('Subscription Details') }}</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('subscriptions.update', $subscription->id) }}" method="POST">
                        @csrf
                        @method('PUT')
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label">{{ __('Tenant') }}</label>
                                <select name="tenant_id" class="form-select @error('tenant_id') is-invalid @enderror" required>
                                    <option value="">{{ __('Select Tenant') }}</option>
                                    @foreach($tenants as $tenant)
                                        <option value="{{ $tenant->id }}" {{ old('tenant_id', $subscription->tenant_id) == $tenant->id ? 'selected' : '' }}>{{ $tenant->name }} ({{ $tenant->id }})</option>
                                    @endforeach
                                </select>
                                @error('tenant_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">{{ __('Plan') }}</label>
                                <select name="plan_id" class="form-select @error('plan_id') is-invalid @enderror" required>
                                    <option value="">{{ __('Select Plan') }}</option>
                                    @foreach($plans as $plan)
                                        <option value="{{ $plan->id }}" {{ old('plan_id', $subscription->plan_id) == $plan->id ? 'selected' : '' }}>{{ $plan->name }} ({{ number_format((float) $plan->amount, 2) }})</option>
                                    @endforeach
                                </select>
                                @error('plan_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label">{{ __('Starts At') }}</label>
                                <input type="date" name="starts_at" class="form-control @error('starts_at') is-invalid @enderror" value="{{ old('starts_at', $subscription->starts_at ? $subscription->starts_at->format('Y-m-d') : '') }}">
                                @error('starts_at')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">{{ __('Ends At') }}</label>
                                <input type="date" name="ends_at" class="form-control @error('ends_at') is-invalid @enderror" value="{{ old('ends_at', $subscription->ends_at ? $subscription->ends_at->format('Y-m-d') : '') }}">
                                @error('ends_at')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label">{{ __('Paid Amount') }}</label>
                                <input type="number" step="0.01" name="paid_amount" class="form-control @error('paid_amount') is-invalid @enderror" value="{{ old('paid_amount', $subscription->paid_amount) }}" required>
                                @error('paid_amount')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <div class="mt-4">
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" type="checkbox" name="status" value="1" id="statusSwitch" {{ old('status', $subscription->status) ? 'checked' : '' }}>
                                        <label class="form-check-label" for="statusSwitch">{{ __('Active Status') }}</label>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="d-flex justify-content-end">
                            <a href="{{ route('subscriptions.index') }}" class="btn btn-secondary me-2">{{ __('Cancel') }}</a>
                            <button type="submit" class="btn btn-main">{{ __('Update Subscription') }}</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
