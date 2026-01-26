@extends('tenancy::layouts.admin-central')

@section('sidebar')
    @include('tenancy::layouts.admin-sidebar')
@endsection

@section('content')
    @include('components.breadcrumb', [
        'title' => __('Tenants Management'),
        'items' => [['label' => __('Home'), 'url' => route('admin.dashboard')], ['label' => __('Tenants')]],
    ])

    <div class="row">
        <div class="col-12">
            <!-- Header Section -->
            <div
                class="d-flex flex-column flex-lg-row justify-content-between align-items-start align-items-lg-center mb-4 gap-3">
                <h2 class="mb-0">
                    <i class="fas fa-building me-2"></i>{{ __('Tenants Management') }}
                </h2>
                <div class="d-flex flex-column flex-sm-row gap-2 w-100 w-lg-auto">
                    <a href="{{ route('plans.index') }}" class="btn btn-outline-primary">
                        <i class="fas fa-list me-1"></i>{{ __('Plans') }}
                    </a>
                    <a href="{{ route('subscriptions.index') }}" class="btn btn-outline-info">
                        <i class="fas fa-calendar-check me-1"></i>{{ __('Subscriptions') }}
                    </a>
                    <a href="{{ route('tenancy.create') }}" class="btn btn-main">
                        <i class="fas fa-plus me-2"></i>{{ __('Add New Tenant') }}
                    </a>
                </div>
            </div>

            <div class="card">
                <div class="card-body">
                    <!-- Desktop Table View -->
                    <div class="table-responsive d-none d-lg-block">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>{{ __('ID') }}</th>
                                    <th>{{ __('Company') }}</th>
                                    <th>{{ __('Admin') }}</th>
                                    <th>{{ __('Domain') }}</th>
                                    <th>{{ __('Plan') }}</th>
                                    <th>{{ __('Modules') }}</th>
                                    <th>{{ __('Status') }}</th>
                                    <th>{{ __('Actions') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($tenants as $tenant)
                                    <tr>
                                        <td><strong>{{ $tenant->id }}</strong></td>
                                        <td>
                                            {{ $tenant->name }}<br>
                                            <small class="text-muted">{{ $tenant->contact_number }}</small>
                                        </td>
                                        <td>{{ $tenant->admin_email }}</td>
                                        <td>
                                            @if ($tenant->domains->isNotEmpty())
                                                <span class="badge bg-secondary">
                                                    {{ $tenant->domains->first()->domain }}
                                                </span>
                                            @else
                                                <span class="text-muted">---</span>
                                            @endif
                                        </td>
                                        <td>
                                            <span class="badge bg-info">{{ $tenant->plan->name ?? __('No Plan') }}</span>
                                        </td>
                                        <td>
                                            @php
                                                $enabledModules = $tenant->enabled_modules ?? [];
                                                $modulesConfig = config('modules_list');
                                            @endphp

                                            @if (!empty($enabledModules))
                                                <div
                                                    style="display: grid; grid-template-columns: repeat(3, auto); gap: 2px; justify-content: start;">
                                                    @foreach ($enabledModules as $moduleKey)
                                                        @if (isset($modulesConfig[$moduleKey]))
                                                            <span class="badge bg-light text-dark"
                                                                style="font-size: 0.5rem; padding: 0.15em 0.3em; text-align: center; white-space: nowrap; border: 1px solid #dee2e6;">
                                                                {{ $modulesConfig[$moduleKey]['name'] }}
                                                            </span>
                                                        @endif
                                                    @endforeach
                                                </div>
                                            @else
                                                <span class="text-muted small">---</span>
                                            @endif
                                        </td>
                                        <td>
                                            <form action="{{ route('tenancy.toggle-status', $tenant->id) }}"
                                                method="POST">
                                                @csrf
                                                @method('PATCH')
                                                <div class="form-check form-switch cursor-pointer">
                                                    <input class="form-check-input" type="checkbox" role="switch"
                                                        id="statusSwitch{{ $tenant->id }}"
                                                        {{ $tenant->status ? 'checked' : '' }}
                                                        onchange="this.form.submit()">
                                                    <label class="form-check-label" for="statusSwitch{{ $tenant->id }}">
                                                        {{ $tenant->status ? __('Active') : __('Inactive') }}
                                                    </label>
                                                </div>
                                            </form>
                                        </td>
                                        <td>
                                            <div class="btn-group" role="group">
                                                <a href="{{ route('tenancy.show', $tenant->id) }}"
                                                    class="btn btn-sm btn-info" title="{{ __('View') }}">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                                <a href="{{ route('tenancy.edit', $tenant->id) }}"
                                                    class="btn btn-sm btn-success" title="{{ __('Edit') }}">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                                @if ($tenant->domains->isNotEmpty())
                                                    <a href="{{ route('tenancy.redirect', $tenant->id) }}"
                                                        class="btn btn-sm btn-primary" title="{{ __('Open Tenant') }}"
                                                        target="_blank">
                                                        <i class="fas fa-arrow-up-right-from-square"></i>
                                                    </a>
                                                @endif
                                                <button type="button" class="btn btn-sm btn-danger" data-bs-toggle="modal"
                                                    data-bs-target="#deleteModal{{ $tenant->id }}"
                                                    title="{{ __('Delete') }}">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="8" class="text-center py-4">
                                            <i class="fas fa-inbox fa-2x text-muted mb-2"></i>
                                            <p class="text-muted">{{ __('No tenants found') }}</p>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <!-- Mobile Card View -->
                    <div class="d-lg-none">
                        @forelse($tenants as $tenant)
                            <div class="card mb-3 shadow-sm">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between align-items-start mb-3">
                                        <div>
                                            <h5 class="card-title mb-1">{{ $tenant->name }}</h5>
                                            <span class="badge bg-info">{{ $tenant->plan->name ?? __('No Plan') }}</span>
                                        </div>
                                        <span class="badge bg-secondary">ID: {{ $tenant->id }}</span>
                                    </div>

                                    <div class="mb-2">
                                        <small class="text-muted d-block"><i class="fas fa-envelope me-1"></i>
                                            {{ __('Admin') }}</small>
                                        <span>{{ $tenant->admin_email }}</span>
                                    </div>

                                    @if ($tenant->contact_number)
                                        <div class="mb-2">
                                            <small class="text-muted d-block"><i class="fas fa-phone me-1"></i>
                                                {{ __('Contact') }}</small>
                                            <span>{{ $tenant->contact_number }}</span>
                                        </div>
                                    @endif

                                    <div class="mb-2">
                                        <small class="text-muted d-block"><i class="fas fa-globe me-1"></i>
                                            {{ __('Domain') }}</small>
                                        @if ($tenant->domains->isNotEmpty())
                                            <span class="badge bg-secondary">{{ $tenant->domains->first()->domain }}</span>
                                        @else
                                            <span class="text-muted">---</span>
                                        @endif
                                    </div>

                                    @php
                                        $enabledModules = $tenant->enabled_modules ?? [];
                                        $modulesConfig = config('modules_list');
                                    @endphp

                                    @if (!empty($enabledModules))
                                        <div class="mb-3">
                                            <small class="text-muted d-block mb-1"><i class="fas fa-puzzle-piece me-1"></i>
                                                {{ __('Modules') }}</small>
                                            <div class="d-flex flex-wrap gap-1">
                                                @foreach ($enabledModules as $moduleKey)
                                                    @if (isset($modulesConfig[$moduleKey]))
                                                        <span class="badge bg-light text-dark"
                                                            style="border: 1px solid #dee2e6;">
                                                            {{ $modulesConfig[$moduleKey]['name'] }}
                                                        </span>
                                                    @endif
                                                @endforeach
                                            </div>
                                        </div>
                                    @endif

                                    <div class="mb-3">
                                        <small class="text-muted d-block mb-1"><i class="fas fa-toggle-on me-1"></i>
                                            {{ __('Status') }}</small>
                                        <form action="{{ route('tenancy.toggle-status', $tenant->id) }}" method="POST">
                                            @csrf
                                            @method('PATCH')
                                            <div class="form-check form-switch">
                                                <input class="form-check-input" type="checkbox" role="switch"
                                                    id="statusSwitchMobile{{ $tenant->id }}"
                                                    {{ $tenant->status ? 'checked' : '' }} onchange="this.form.submit()">
                                                <label class="form-check-label"
                                                    for="statusSwitchMobile{{ $tenant->id }}">
                                                    {{ $tenant->status ? __('Active') : __('Inactive') }}
                                                </label>
                                            </div>
                                        </form>
                                    </div>

                                    <div class="d-flex gap-2 flex-wrap">
                                        <a href="{{ route('tenancy.show', $tenant->id) }}"
                                            class="btn btn-sm btn-info flex-fill">
                                            <i class="fas fa-eye me-1"></i>{{ __('View') }}
                                        </a>
                                        <a href="{{ route('tenancy.edit', $tenant->id) }}"
                                            class="btn btn-sm btn-success flex-fill">
                                            <i class="fas fa-edit me-1"></i>{{ __('Edit') }}
                                        </a>
                                        @if ($tenant->domains->isNotEmpty())
                                            <a href="{{ route('tenancy.redirect', $tenant->id) }}"
                                                class="btn btn-sm btn-primary flex-fill" target="_blank">
                                                <i class="fas fa-arrow-up-right-from-square me-1"></i>{{ __('Open') }}
                                            </a>
                                        @endif
                                        <button type="button" class="btn btn-sm btn-danger flex-fill"
                                            data-bs-toggle="modal" data-bs-target="#deleteModal{{ $tenant->id }}">
                                            <i class="fas fa-trash me-1"></i>{{ __('Delete') }}
                                        </button>
                                    </div>
                                </div>
                            </div>

                            <!-- Delete Modal (shared for both views) -->
                            <div class="modal fade" id="deleteModal{{ $tenant->id }}" tabindex="-1">
                                <div class="modal-dialog modal-dialog-centered">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title">{{ __('Confirm Delete') }}</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                        </div>
                                        <div class="modal-body">
                                            {{ __('Are you sure you want to delete tenant ":name"? This will also delete the tenant database.', ['name' => $tenant->name]) }}
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-secondary"
                                                data-bs-dismiss="modal">{{ __('Cancel') }}</button>
                                            <form action="{{ route('tenancy.destroy', $tenant->id) }}" method="POST"
                                                class="d-inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit"
                                                    class="btn btn-danger">{{ __('Delete') }}</button>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div class="text-center py-5">
                                <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
                                <p class="text-muted">{{ __('No tenants found') }}</p>
                            </div>
                        @endforelse
                    </div>

                    @if ($tenants->hasPages())
                        <div class="d-flex justify-content-center mt-4">
                            {{ $tenants->links() }}
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection
