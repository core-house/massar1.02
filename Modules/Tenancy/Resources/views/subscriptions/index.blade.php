@extends('tenancy::layouts.admin-central')

@section('sidebar')
    @include('tenancy::layouts.admin-sidebar')
@endsection

@section('content')
    @include('components.breadcrumb', [
        'title' => __('Subscriptions Management'),
        'items' => [['label' => __('Home'), 'url' => route('admin.dashboard')], ['label' => __('Subscriptions')]],
    ])

    <div class="row">
        <div class="col-lg-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2 class="mb-0">
                    <i class="fas fa-calendar-check me-2"></i>{{ __('Subscriptions Management') }}
                </h2>
                <a href="{{ route('subscriptions.create') }}" type="button" class="btn btn-main">
                    <i class="fas fa-plus me-2"></i>{{ __('Add New Subscription') }}
                </a>
            </div>

            <div class="card">
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>{{ __('ID') }}</th>
                                    <th>{{ __('Tenant') }}</th>
                                    <th>{{ __('Plan') }}</th>
                                    <th>{{ __('Starts At') }}</th>
                                    <th>{{ __('Ends At') }}</th>
                                    <th>{{ __('Paid Amount') }}</th>
                                    <th>{{ __('Status') }}</th>
                                    <th>{{ __('Actions') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($subscriptions as $subscription)
                                    <tr>
                                        <td><strong>{{ $subscription->id }}</strong></td>
                                        <td>{{ $subscription->tenant->name ?? $subscription->tenant_id }}</td>
                                        <td>{{ $subscription->plan->name ?? $subscription->plan_id }}</td>
                                        <td>{{ $subscription->starts_at ? $subscription->starts_at->format('Y-m-d') : '---' }}</td>
                                        <td>{{ $subscription->ends_at ? $subscription->ends_at->format('Y-m-d') : '---' }}</td>
                                        <td>{{ number_format((float) $subscription->paid_amount, 2) }}</td>
                                        <td>
                                            <form action="{{ route('subscriptions.toggle-status', $subscription->id) }}" method="POST">
                                                @csrf
                                                @method('PATCH')
                                                <div class="form-check form-switch cursor-pointer">
                                                    <input class="form-check-input" type="checkbox" role="switch"
                                                           id="statusSwitch{{ $subscription->id }}"
                                                           {{ $subscription->status ? 'checked' : '' }}
                                                           onchange="this.form.submit()">
                                                    <label class="form-check-label" for="statusSwitch{{ $subscription->id }}">
                                                        {{ $subscription->status ? __('Active') : __('Inactive') }}
                                                    </label>
                                                </div>
                                            </form>
                                        </td>
                                        <td>
                                            <div class="btn-group" role="group">
                                                <a href="{{ route('subscriptions.edit', $subscription->id) }}"
                                                    class="btn btn-sm btn-success" title="{{ __('Edit') }}">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                                <button type="button" class="btn btn-sm btn-danger" data-bs-toggle="modal"
                                                    data-bs-target="#deleteModal{{ $subscription->id }}"
                                                    title="{{ __('Delete') }}">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </div>

                                            <div class="modal fade" id="deleteModal{{ $subscription->id }}" tabindex="-1">
                                                <div class="modal-dialog">
                                                    <div class="modal-content">
                                                        <div class="modal-header">
                                                            <h5 class="modal-title">{{ __('Confirm Delete') }}</h5>
                                                            <button type="button" class="btn-close"
                                                                data-bs-dismiss="modal"></button>
                                                        </div>
                                                        <div class="modal-body">
                                                            {{ __('Are you sure you want to delete this subscription?') }}
                                                        </div>
                                                        <div class="modal-footer">
                                                            <button type="button" class="btn btn-secondary"
                                                                data-bs-dismiss="modal">{{ __('Cancel') }}</button>
                                                            <form action="{{ route('subscriptions.destroy', $subscription->id) }}"
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
                                        <td colspan="8" class="text-center py-4">
                                            <i class="fas fa-inbox fa-2x text-muted mb-2"></i>
                                            <p class="text-muted">{{ __('No subscriptions found') }}</p>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    @if ($subscriptions->hasPages())
                        <div class="d-flex justify-content-center mt-4">
                            {{ $subscriptions->links() }}
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection
