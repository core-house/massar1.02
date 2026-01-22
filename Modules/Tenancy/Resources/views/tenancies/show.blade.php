@extends('tenancy::layouts.admin-central')

@section('sidebar')
    @include('tenancy::layouts.admin-sidebar')
@endsection

@section('content')
    @include('components.breadcrumb', [
        'title' => __('Tenant Details'),
        'items' => [['label' => __('Home'), 'url' => route('admin.dashboard')], ['label' => __('Tenants'), 'url' => route('tenancy.index')], ['label' => __('Details')]],
    ])

    <div class="row">
        <div class="col-lg-4">
            <div class="card mb-4">
                <div class="card-body text-center">
                    <div class="mb-3">
                        <i class="fas fa-building fa-4x text-primary"></i>
                    </div>
                    <h4 class="mb-1">{{ $tenant->name }}</h4>
                    <p class="text-muted mb-3">{{ $tenant->id }}.{{ parse_url(config('app.url'), PHP_URL_HOST) ?: 'localhost' }}</p>
                    
                    @if($tenant->status)
                        <span class="badge bg-success mb-3">{{ __('Active') }}</span>
                    @else
                        <span class="badge bg-danger mb-3">{{ __('Inactive') }}</span>
                    @endif

                    <div class="d-grid gap-2">
                        @if ($tenant->domains->isNotEmpty())
                            <a href="{{ route('tenancy.redirect', $tenant->id) }}" class="btn btn-main" target="_blank">
                                <i class="fas fa-external-link-alt me-2"></i>{{ __('Open Tenant App') }}
                            </a>
                        @endif
                        <a href="{{ route('tenancy.edit', $tenant->id) }}" class="btn btn-warning">
                            <i class="fas fa-edit me-2"></i>{{ __('Edit Tenant') }}
                        </a>
                    </div>
                </div>
            </div>

            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">{{ __('Subscription Info') }}</h5>
                </div>
                <div class="card-body">
                    <div class="d-flex justify-content-between mb-2">
                        <span class="text-muted">{{ __('Current Plan') }}:</span>
                        <strong>{{ $tenant->plan->name ?? __('No Plan') }}</strong>
                    </div>
                    <div class="d-flex justify-content-between mb-2">
                        <span class="text-muted">{{ __('Start Date') }}:</span>
                        <strong>{{ $tenant->subscription_start_at ? $tenant->subscription_start_at->format('Y-m-d') : '---' }}</strong>
                    </div>
                    <div class="d-flex justify-content-between mb-2">
                        <span class="text-muted">{{ __('End Date') }}:</span>
                        <strong>{{ $tenant->subscription_end_at ? $tenant->subscription_end_at->format('Y-m-d') : '---' }}</strong>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-8">
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">{{ __('Contact & Company Details') }}</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="text-muted small d-block">{{ __('Admin Email') }}</label>
                            <strong>{{ $tenant->admin_email }}</strong>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="text-muted small d-block">{{ __('Contact Number') }}</label>
                            <strong>{{ $tenant->contact_number ?: '---' }}</strong>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="text-muted small d-block">{{ __('Company Size') }}</label>
                            <strong>{{ $tenant->company_size ?: '---' }}</strong>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="text-muted small d-block">{{ __('User Position') }}</label>
                            <strong>{{ $tenant->user_position ?: '---' }}</strong>
                        </div>
                        <div class="col-md-12 mb-3">
                            <label class="text-muted small d-block">{{ __('Address') }}</label>
                            <strong>{{ $tenant->address ?: '---' }}</strong>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="text-muted small d-block">{{ __('Referral Code') }}</label>
                            <strong>{{ $tenant->referral_code ?: '---' }}</strong>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">{{ __('Recent Subscriptions') }}</h5>
                    <a href="{{ route('subscriptions.create', ['tenant_id' => $tenant->id]) }}" class="btn btn-sm btn-outline-primary">
                        <i class="fas fa-plus me-1"></i>{{ __('Add New') }}
                    </a>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>{{ __('Plan') }}</th>
                                    <th>{{ __('Duration') }}</th>
                                    <th>{{ __('Paid') }}</th>
                                    <th>{{ __('Status') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($tenant->subscriptions as $sub)
                                    <tr>
                                        <td>{{ $sub->plan->name ?? $sub->plan_id }}</td>
                                        <td>
                                            <small>{{ $sub->starts_at?->format('Y-m-d') }} {{ __('to') }} {{ $sub->ends_at?->format('Y-m-d') }}</small>
                                        </td>
                                        <td>{{ number_format((float) $sub->paid_amount, 2) }}</td>
                                        <td>
                                            @if($sub->status)
                                                <span class="badge bg-success">{{ __('Active') }}</span>
                                            @else
                                                <span class="badge bg-secondary">{{ __('Closed') }}</span>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="text-center text-muted py-3">{{ __('No subscription history found.') }}</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
