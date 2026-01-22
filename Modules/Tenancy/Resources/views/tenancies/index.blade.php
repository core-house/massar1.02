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
        <div class="col-lg-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2 class="mb-0">
                    <i class="fas fa-building me-2"></i>{{ __('Tenants Management') }}
                </h2>
                <div>
                    <a href="{{ route('plans.index') }}" class="btn btn-outline-primary me-2">
                        <i class="fas fa-list me-1"></i>{{ __('Plans') }}
                    </a>
                    <a href="{{ route('subscriptions.index') }}" class="btn btn-outline-info me-2">
                        <i class="fas fa-calendar-check me-1"></i>{{ __('Subscriptions') }}
                    </a>
                    <a href="{{ route('tenancy.create') }}" type="button" class="btn btn-main">
                        <i class="fas fa-plus me-2"></i>{{ __('Add New Tenant') }}
                    </a>
                </div>
            </div>

            <div class="card">
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>{{ __('ID') }}</th>
                                    <th>{{ __('Company') }}</th>
                                    <th>{{ __('Admin') }}</th>
                                    <th>{{ __('Domain') }}</th>
                                    <th>{{ __('Plan') }}</th>
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
                                        <td>
                                            {{ $tenant->admin_email }}
                                        </td>
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
                                            <form action="{{ route('tenancy.toggle-status', $tenant->id) }}" method="POST">
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

                                            <!-- Delete Modal -->
                                            <div class="modal fade" id="deleteModal{{ $tenant->id }}" tabindex="-1">
                                                <div class="modal-dialog">
                                                    <div class="modal-content">
                                                        <div class="modal-header">
                                                            <h5 class="modal-title">{{ __('Confirm Delete') }}</h5>
                                                            <button type="button" class="btn-close"
                                                                data-bs-dismiss="modal"></button>
                                                        </div>
                                                        <div class="modal-body">
                                                            {{ __('Are you sure you want to delete tenant ":name"? This will also delete the tenant database.', ['name' => $tenant->name]) }}
                                                        </div>
                                                        <div class="modal-footer">
                                                            <button type="button" class="btn btn-secondary"
                                                                data-bs-dismiss="modal">{{ __('Cancel') }}</button>
                                                            <form action="{{ route('tenancy.destroy', $tenant->id) }}"
                                                                method="POST" class="d-inline">
                                                                @csrf
                                                                @method('DELETE')
                                                                <button type="submit"
                                                                    class="btn btn-danger">{{ __('Delete') }}</button>
                                                            </form>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="text-center py-4">
                                            <i class="fas fa-inbox fa-2x text-muted mb-2"></i>
                                            <p class="text-muted">{{ __('No tenants found') }}</p>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
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
