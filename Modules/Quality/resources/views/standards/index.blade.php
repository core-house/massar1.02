@extends('admin.dashboard')

@section('sidebar')
    @include('components.sidebar.quality')
@endsection

@section('content')
    <div class="container-fluid">
        <div class="row mb-4">
            <div class="col-12">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h2 class="mb-0">
                            <i class="fas fa-ruler-combined me-2"></i>{{ __('quality::quality.quality standards') }}
                        </h2>
                    </div>
                    @can('create standards')
                        <div>
                            <a href="{{ route('quality.standards.create') }}" class="btn btn-primary">
                               <i class="fas fa-plus-circle me-2"></i>{{ __('quality::quality.new standard') }}
                            </a>
                        </div>
                    @endcan
                </div>
            </div>
        </div>

        <!-- Statistics Cards -->
        <div class="row g-3 mb-4">
            <div class="col-md-4">
                <div class="card">
                    <div class="card-body">
                        <h6 class="text-muted">{{ __('quality::quality.total standards') }}</h6>
                        <h3>{{ $stats['total'] }}</h3>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card">
                    <div class="card-body">
                        <h6 class="text-muted">{{ __('quality::quality.active standards') }}</h6>
                        <h3 class="text-success">{{ $stats['active'] }}</h3>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card">
                    <div class="card-body">
                        <h6 class="text-muted">{{ __('quality::quality.inactive standards') }}</h6>
                        <h3 class="text-danger">{{ $stats['inactive'] }}</h3>
                    </div>
                </div>
            </div>
        </div>

        <div class="card">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>{{ __('quality::quality.code') }}</th>
                                <th>{{ __('quality::quality.standard name') }}</th>
                                <th>{{ __('quality::quality.item') }}</th>
                                <th>{{ __('quality::quality.test frequency') }}</th>
                                <th>{{ __('quality::quality.acceptance threshold') }}</th>
                                <th>{{ __('quality::quality.status') }}</th>
                                @canany(['edit standards', 'delete standards', 'view standards'])
                                    <th>{{ __('quality::quality.actions') }}</th>
                                @endcanany
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($standards as $standard)
                                <tr>
                                    <td><strong>{{ $standard->standard_code }}</strong></td>
                                    <td>{{ $standard->standard_name }}</td>
                                    <td>{{ $standard->item->name ?? '---' }}</td>
                                    <td>
                                        {{ match ($standard->test_frequency) {
                                            'per_batch' => __('quality::quality.per batch'),
                                            'daily' => __('quality::quality.daily'),
                                            'weekly' => __('quality::quality.weekly'),
                                            'monthly' => __('quality::quality.monthly'),
                                            default => $standard->test_frequency,
                                        } }}
                                    </td>
                                    <td><strong>{{ $standard->acceptance_threshold }}%</strong></td>
                                    <td>
                                        @if ($standard->is_active)
                                            <span class="badge bg-success">{{ __('quality::quality.active') }}</span>
                                        @else
                                            <span class="badge bg-secondary">{{ __('quality::quality.suspended') }}</span>
                                        @endif
                                    </td>
                                    @canany(['edit standards', 'delete standards', 'view standards'])
                                        <td>
                                            <div class="btn-group" role="group">
                                                @can('view standards')
                                                    <a href="{{ route('quality.standards.show', $standard) }}"
                                                        class="btn btn-sm btn-info" title="{{ __('quality::quality.view') }}">
                                                        <i class="fas fa-eye"></i>
                                                    </a>
                                                @endcan
                                                @can('edit standards')
                                                    <a href="{{ route('quality.standards.edit', $standard) }}"
                                                        class="btn btn-sm btn-warning" title="{{ __('quality::quality.edit') }}">
                                                        <i class="fas fa-edit"></i>
                                                    </a>
                                                @endcan
                                                @can('delete standards')
                                                    <button type="button" class="btn btn-sm btn-danger" data-bs-toggle="modal"
                                                        data-bs-target="#deleteModal{{ $standard->id }}"
                                                        title="{{ __('quality::quality.delete') }}">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                @endcan
                                            </div>

                                            <!-- Delete Modal -->
                                            <div class="modal fade" id="deleteModal{{ $standard->id }}" tabindex="-1">
                                                <div class="modal-dialog">
                                                    <div class="modal-content">
                                                        <div class="modal-header">
                                                            <h5 class="modal-title">{{ __('quality::quality.confirm delete') }}</h5>
                                                            <button type="button" class="btn-close"
                                                                data-bs-dismiss="modal"></button>
                                                        </div>
                                                        <div class="modal-body">
                                                            {{ __('quality::quality.are you sure you want to delete quality standard') }}
                                                            "{{ $standard->standard_name }}"?
                                                        </div>
                                                        <div class="modal-footer">
                                                            <button type="button" class="btn btn-secondary"
                                                                data-bs-dismiss="modal">
                                                                {{ __('quality::quality.cancel') }}
                                                            </button>
                                                            <form action="{{ route('quality.standards.destroy', $standard) }}"
                                                                method="POST" class="d-inline">
                                                                @csrf
                                                                @method('DELETE')
                                                                <button type="submit"
                                                                    class="btn btn-danger">{{ __('quality::quality.delete') }}</button>
                                                            </form>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </td>
                                    @endcanany
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="@canany(['edit standards', 'delete standards', 'view standards']) 7 @else 6 @endcanany"
                                        class="text-center py-4">
                                        {{ __('quality::quality.no quality standards found') }}
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                @if ($standards->hasPages())
                    <div class="d-flex justify-content-center mt-4">
                        {{ $standards->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection
