@extends('tenancy::layouts.admin-central')

@section('sidebar')
    @include('tenancy::layouts.admin-sidebar')
@endsection

@section('content')
    @include('components.breadcrumb', [
        'title' => __('Tenant Details'),
        'items' => [
            ['label' => __('Home'), 'url' => route('admin.dashboard')],
            ['label' => __('Tenants'), 'url' => route('tenancy.index')],
            ['label' => __('Details')],
        ],
    ])

    <div class="row">
        <!-- Left Column -->
        <div class="col-lg-4">
            <!-- Tenant Info Card -->
            <div class="card mb-4">
                <div class="card-body text-center">
                    <div class="mb-3">
                        <i class="fas fa-building fa-4x text-primary"></i>
                    </div>
                    <h4 class="mb-1">{{ $tenant->name }}</h4>
                    <p class="text-muted mb-2">
                        {{ $tenant->id }}.{{ parse_url(config('app.url'), PHP_URL_HOST) ?: 'localhost' }}</p>

                    @if ($tenant->isActive())
                        <span class="badge bg-success mb-3">
                            <i class="fas fa-check-circle me-1"></i>{{ __('Active') }}
                        </span>
                    @else
                        <span class="badge bg-danger mb-3">
                            <i class="fas fa-times-circle me-1"></i>{{ __('Inactive') }}
                        </span>
                    @endif

                    <div class="d-grid gap-2 mt-3">
                        @if ($tenant->domains->isNotEmpty())
                            <a href="{{ route('tenancy.redirect', $tenant->id) }}" class="btn btn-main" target="_blank">
                                <i class="fas fa-external-link-alt me-2"></i>{{ __('Open Tenant App') }}
                            </a>
                        @endif
                        <a href="{{ route('tenancy.edit', $tenant->id) }}" class="btn btn-warning">
                            <i class="fas fa-edit me-2"></i>{{ __('Edit Tenant') }}
                        </a>
                        <form action="{{ route('tenancy.toggle-status', $tenant->id) }}" method="POST" class="d-inline">
                            @csrf
                            @method('PUT')
                            <button type="submit" class="btn btn-outline-secondary w-100">
                                <i class="fas fa-power-off me-2"></i>{{ __('Toggle Status') }}
                            </button>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Subscription Info Card -->
            <div class="card mb-4">
                <div class="card-header bg-light">
                    <h5 class="mb-0">
                        <i class="fas fa-calendar-check me-2 text-primary"></i>{{ __('Subscription Info') }}
                    </h5>
                </div>
                <div class="card-body">
                    @php
                        $latestSub = $tenant->subscriptions()->latest()->first();
                    @endphp
                    <div class="mb-3 pb-3 border-bottom">
                        <label class="text-muted small d-block mb-1">{{ __('Current Plan') }}</label>
                        <h6 class="mb-0">
                            <span class="badge bg-primary">{{ $latestSub->plan->name ?? ($tenant->plan->name ?? __('No Plan')) }}</span>
                        </h6>
                    </div>
                    <div class="mb-3 pb-3 border-bottom">
                        <label class="text-muted small d-block mb-1">{{ __('Start Date') }}</label>
                        <strong>
                            <i class="fas fa-calendar-alt me-1 text-success"></i>
                            {{ $latestSub ? $latestSub->starts_at?->format('Y-m-d') : '---' }}
                        </strong>
                    </div>
                    <div class="mb-0">
                        <label class="text-muted small d-block mb-1">{{ __('End Date') }}</label>
                        <strong>
                            <i class="fas fa-calendar-times me-1 text-danger"></i>
                            {{ $latestSub ? $latestSub->ends_at?->format('Y-m-d') : '---' }}
                        </strong>
                    </div>
                </div>
            </div>

            <!-- Enabled Modules Card -->
            <div class="card">
                <div class="card-header bg-light">
                    <h5 class="mb-0">
                        <i class="fas fa-puzzle-piece me-2 text-primary"></i>{{ __('Enabled Modules') }}
                    </h5>
                </div>
                <div class="card-body">
                    @php
                        $enabledModules = $tenant->enabled_modules ?? [];
                        $modulesConfig = config('modules_list');
                    @endphp

                    @if (!empty($enabledModules))
                        <div
                            style="display: grid; grid-template-columns: repeat(2, auto); gap: 4px; justify-content: start;">
                            @foreach ($enabledModules as $moduleKey)
                                @if (isset($modulesConfig[$moduleKey]))
                                    <span class="badge bg-light text-dark"
                                        style="font-size: 0.7rem; padding: 0.35em 0.5em; text-align: center; white-space: nowrap; border: 1px solid #dee2e6;">
                                        <i class="fas fa-check-circle me-1" style="font-size: 0.65rem;"></i>
                                        {{ $modulesConfig[$moduleKey]['name'] }}
                                    </span>
                                @endif
                            @endforeach
                        </div>
                    @else
                        <p class="text-muted mb-0 text-center">
                            <i class="fas fa-info-circle me-1"></i>{{ __('No modules enabled') }}
                        </p>
                    @endif
                </div>
            </div>
        </div>

        <!-- Right Column -->
        <div class="col-lg-8">
            <!-- Contact & Company Details -->
            <div class="card mb-4">
                <div class="card-header bg-light">
                    <h5 class="mb-0">
                        <i class="fas fa-address-card me-2 text-primary"></i>{{ __('Contact & Company Details') }}
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <div class="p-3 bg-light rounded">
                                <label class="text-muted small d-block mb-1">
                                    <i class="fas fa-envelope me-1"></i>{{ __('Admin Email') }}
                                </label>
                                <strong>{{ $tenant->admin_email }}</strong>
                            </div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <div class="p-3 bg-light rounded">
                                <label class="text-muted small d-block mb-1">
                                    <i class="fas fa-phone me-1"></i>{{ __('Contact Number') }}
                                </label>
                                <strong>{{ $tenant->contact_number ?: '---' }}</strong>
                            </div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <div class="p-3 bg-light rounded">
                                <label class="text-muted small d-block mb-1">
                                    <i class="fas fa-building me-1"></i>{{ __('Company Name') }}
                                </label>
                                <strong>{{ $tenant->company_name ?: '---' }}</strong>
                            </div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <div class="p-3 bg-light rounded">
                                <label class="text-muted small d-block mb-1">
                                    <i class="fas fa-users me-1"></i>{{ __('Company Size') }}
                                </label>
                                <strong>{{ $tenant->company_size ?: '---' }}</strong>
                            </div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <div class="p-3 bg-light rounded">
                                <label class="text-muted small d-block mb-1">
                                    <i class="fas fa-user-tie me-1"></i>{{ __('User Position') }}
                                </label>
                                <strong>{{ $tenant->user_position ?: '---' }}</strong>
                            </div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <div class="p-3 bg-light rounded">
                                <label class="text-muted small d-block mb-1">
                                    <i class="fas fa-gift me-1"></i>{{ __('Referral Code') }}
                                </label>
                                <strong>{{ $tenant->referral_code ?: '---' }}</strong>
                            </div>
                        </div>
                        <div class="col-md-12 mb-0">
                            <div class="p-3 bg-light rounded">
                                <label class="text-muted small d-block mb-1">
                                    <i class="fas fa-map-marker-alt me-1"></i>{{ __('Address') }}
                                </label>
                                <strong>{{ $tenant->address ?: '---' }}</strong>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Recent Subscriptions -->
            <div class="card">
                <div class="card-header bg-light d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">
                        <i class="fas fa-history me-2 text-primary"></i>{{ __('Recent Subscriptions') }}
                    </h5>
                    <a href="{{ route('subscriptions.create', ['tenant_id' => $tenant->id]) }}"
                        class="btn btn-sm btn-primary">
                        <i class="fas fa-plus me-1"></i>{{ __('Add New') }}
                    </a>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle">
                            <thead class="table-light">
                                <tr>
                                    <th><i class="fas fa-tag me-1"></i>{{ __('Plan') }}</th>
                                    <th><i class="fas fa-calendar-alt me-1"></i>{{ __('Duration') }}</th>
                                    <th><i class="fas fa-dollar-sign me-1"></i>{{ __('Paid') }}</th>
                                    <th><i class="fas fa-info-circle me-1"></i>{{ __('Status') }}</th>
                                    <th><i class="fas fa-cogs me-1"></i>{{ __('Actions') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($tenant->subscriptions as $sub)
                                    <tr>
                                        <td><strong>{{ $sub->plan->name ?? $sub->plan_id }}</strong></td>
                                        <td>
                                            <small class="text-muted">
                                                {{ $sub->starts_at?->format('Y-m-d') }}
                                                <i class="fas fa-arrow-right mx-1"></i>
                                                {{ $sub->ends_at?->format('Y-m-d') }}
                                            </small>
                                        </td>
                                        <td>
                                            <span class="badge bg-success">
                                                {{ number_format((float) $sub->paid_amount, 2) }}
                                            </span>
                                        </td>
                                        <td>
                                            @if ($sub->status)
                                                <span class="badge bg-success">
                                                    <i class="fas fa-check me-1"></i>{{ __('Active') }}
                                                </span>
                                            @else
                                                <span class="badge bg-secondary">
                                                    <i class="fas fa-times me-1"></i>{{ __('Closed') }}
                                                </span>
                                            @endif
                                        </td>
                                        <td>
                                            <button type="button" class="btn btn-sm btn-info text-white" 
                                                    data-bs-toggle="modal" data-bs-target="#renewModal{{ $sub->id }}">
                                                <i class="fas fa-sync me-1"></i>{{ __('Renew') }}
                                            </button>
                                            @include('tenancy::subscriptions.renew-modal')
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="text-center text-muted py-4">
                                            <i class="fas fa-inbox fa-2x mb-2 d-block"></i>
                                            {{ __('No subscription history found.') }}
                                        </td>
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
