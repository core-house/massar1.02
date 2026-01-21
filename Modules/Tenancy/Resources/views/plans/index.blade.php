@extends('tenancy::layouts.admin-central')

@section('sidebar')
    @include('tenancy::layouts.admin-sidebar')
@endsection

@section('content')
    @include('components.breadcrumb', [
        'title' => __('Plans Management'),
        'items' => [['label' => __('Home'), 'url' => route('admin.dashboard')], ['label' => __('Plans')]],
    ])

    <div class="row">
        <div class="col-lg-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2 class="mb-0">
                    <i class="fas fa-list me-2"></i>{{ __('Plans Management') }}
                </h2>
                <a href="{{ route('plans.create') }}" type="button" class="btn btn-main">
                    <i class="fas fa-plus me-2"></i>{{ __('Add New Plan') }}
                </a>
            </div>

            <div class="card">
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>{{ __('ID') }}</th>
                                    <th>{{ __('Name') }}</th>
                                    <th>{{ __('Amount') }}</th>
                                    <th>{{ __('Duration') }}</th>
                                    <th>{{ __('Users/Branches') }}</th>
                                    <th>{{ __('Status') }}</th>
                                    <th>{{ __('Actions') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($plans as $plan)
                                    <tr>
                                        <td><strong>{{ $plan->id }}</strong></td>
                                        <td>{{ $plan->name }}</td>
                                        <td>{{ number_format((float) $plan->amount, 2) }}</td>
                                        <td>{{ $plan->duration_days }} {{ __('Days') }}</td>
                                        <td>
                                            <span class="badge bg-info">{{ $plan->max_users ?? __('Unlimited') }} {{ __('Users') }}</span>
                                            <span class="badge bg-secondary">{{ $plan->max_branches ?? __('Unlimited') }} {{ __('Branches') }}</span>
                                        </td>
                                        <td>
                                            <form action="{{ route('plans.toggle-status', $plan->id) }}" method="POST">
                                                @csrf
                                                @method('PATCH')
                                                <div class="form-check form-switch cursor-pointer">
                                                    <input class="form-check-input" type="checkbox" role="switch"
                                                           id="statusSwitch{{ $plan->id }}"
                                                           {{ $plan->status ? 'checked' : '' }}
                                                           onchange="this.form.submit()">
                                                    <label class="form-check-label" for="statusSwitch{{ $plan->id }}">
                                                        {{ $plan->status ? __('Active') : __('Inactive') }}
                                                    </label>
                                                </div>
                                            </form>
                                        </td>
                                        <td>
                                            <div class="btn-group" role="group">
                                                <a href="{{ route('plans.edit', $plan->id) }}"
                                                    class="btn btn-sm btn-success" title="{{ __('Edit') }}">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                                <button type="button" class="btn btn-sm btn-danger" data-bs-toggle="modal"
                                                    data-bs-target="#deleteModal{{ $plan->id }}"
                                                    title="{{ __('Delete') }}">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </div>

                                            <div class="modal fade" id="deleteModal{{ $plan->id }}" tabindex="-1">
                                                <div class="modal-dialog">
                                                    <div class="modal-content">
                                                        <div class="modal-header">
                                                            <h5 class="modal-title">{{ __('Confirm Delete') }}</h5>
                                                            <button type="button" class="btn-close"
                                                                data-bs-dismiss="modal"></button>
                                                        </div>
                                                        <div class="modal-body">
                                                            {{ __('Are you sure you want to delete plan ":name"?', ['name' => $plan->name]) }}
                                                        </div>
                                                        <div class="modal-footer">
                                                            <button type="button" class="btn btn-secondary"
                                                                data-bs-dismiss="modal">{{ __('Cancel') }}</button>
                                                            <form action="{{ route('plans.destroy', $plan->id) }}"
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
                                            <p class="text-muted">{{ __('No plans found') }}</p>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    @if ($plans->hasPages())
                        <div class="d-flex justify-content-center mt-4">
                            {{ $plans->links() }}
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection
