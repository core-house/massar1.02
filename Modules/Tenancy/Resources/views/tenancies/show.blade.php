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
        <div class="col-lg-12">
            <div class="card">
                <div class="card-header">
                    <div class="d-flex justify-content-between align-items-center">
                        <h4 class="card-title mb-0">
                            <i class="fas fa-building me-2"></i>{{ __('Tenant Details') }}
                        </h4>
                        <div class="btn-group">
                            <a href="{{ route('tenancy.edit', $tenant->id) }}" class="btn btn-warning btn-sm">
                                <i class="fas fa-edit me-2"></i>{{ __('Edit') }}
                            </a>
                            @if ($tenant->domains->isNotEmpty())
                                <a href="{{ route('tenancy.redirect', $tenant->id) }}" class="btn btn-success btn-sm"
                                    target="_blank">
                                    <i class="fas fa-external-link-alt me-2"></i>{{ __('Open Tenant') }}
                                </a>
                            @endif
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <table class="table table-bordered">
                                <tbody>
                                    <tr>
                                        <th width="30%">{{ __('Tenant ID') }}</th>
                                        <td><strong>{{ $tenant->id }}</strong></td>
                                    </tr>
                                    <tr>
                                        <th>{{ __('Company Name') }}</th>
                                        <td>{{ $tenant->name }}</td>
                                    </tr>
                                    <tr>
                                        <th>{{ __('Subdomain') }}</th>
                                        <td>
                                            <span class="badge bg-info">{{ $tenant->id }}</span>
                                        </td>
                                    </tr>
                                    <tr>
                                        <th>{{ __('Created At') }}</th>
                                        <td>{{ $tenant->created_at->format('Y-m-d H:i:s') }}</td>
                                    </tr>
                                    <tr>
                                        <th>{{ __('Updated At') }}</th>
                                        <td>{{ $tenant->updated_at->format('Y-m-d H:i:s') }}</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        <div class="col-md-6">
                            <h5 class="mb-3">{{ __('Domains') }}</h5>
                            @if ($tenant->domains->isNotEmpty())
                                <div class="list-group">
                                    @foreach ($tenant->domains as $domain)
                                        <div class="list-group-item">
                                            <div class="d-flex justify-content-between align-items-center">
                                                <span>
                                                    <i class="fas fa-globe me-2"></i>
                                                    <strong>{{ $domain->domain }}</strong>
                                                </span>
                                                @if (isset($domain->status))
                                                    <span class="badge bg-{{ $domain->status === 'active' ? 'success' : 'secondary' }}">
                                                        {{ $domain->status }}
                                                    </span>
                                                @endif
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            @else
                                <p class="text-muted">{{ __('No domains found for this tenant.') }}</p>
                            @endif
                        </div>
                    </div>

                    <div class="mt-4">
                        <a href="{{ route('tenancy.index') }}" class="btn btn-secondary">
                            <i class="fas fa-arrow-left me-2"></i>{{ __('Back to List') }}
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
