@extends('admin.dashboard')

@section('sidebar')
    @include('components.sidebar.admin')
@endsection

@section('content')
    @include('components.breadcrumb', [
        'title' => __('Tenants Management'),
        'items' => [
            ['label' => __('Home'), 'url' => route('admin.dashboard')],
            ['label' => __('Tenants')],
        ],
    ])

    <div class="row">
        <div class="col-lg-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2 class="mb-0">
                    <i class="fas fa-building me-2"></i>{{ __('Tenants Management') }}
                </h2>
                <a href="{{ route('tenancy.create') }}" type="button" class="btn btn-main">
                    <i class="fas fa-plus me-2"></i>{{ __('Add New Tenant') }}
                </a>
            </div>

            @if (session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            @if (session('error'))
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <i class="fas fa-exclamation-circle me-2"></i>{{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            <div class="card">
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>{{ __('ID') }}</th>
                                    <th>{{ __('Company Name') }}</th>
                                    <th>{{ __('Subdomain') }}</th>
                                    <th>{{ __('Domain') }}</th>
                                    <th>{{ __('Created At') }}</th>
                                    <th>{{ __('Actions') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($tenants as $tenant)
                                    <tr>
                                        <td><strong>{{ $tenant->id }}</strong></td>
                                        <td>{{ $tenant->name }}</td>
                                        <td>
                                            <span class="badge bg-info">{{ $tenant->id }}</span>
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
                                        <td>{{ $tenant->created_at->format('Y-m-d H:i') }}</td>
                                        <td>
                                            <div class="btn-group" role="group">
                                                <a href="{{ route('tenancy.show', $tenant->id) }}"
                                                    class="btn btn-sm btn-info" title="{{ __('View') }}">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                                <a href="{{ route('tenancy.edit', $tenant->id) }}"
                                                    class="btn btn-sm btn-warning" title="{{ __('Edit') }}">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                                @if ($tenant->domains->isNotEmpty())
                                                    <a href="{{ route('tenancy.redirect', $tenant->id) }}"
                                                        class="btn btn-sm btn-success" title="{{ __('Open Tenant') }}"
                                                        target="_blank">
                                                        <i class="fas fa-external-link-alt"></i>
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
                                                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
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
                                        <td colspan="6" class="text-center py-4">
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
