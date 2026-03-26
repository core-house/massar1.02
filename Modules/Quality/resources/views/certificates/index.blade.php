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
                        <h2 class="mb-0"><i class="fas fa-certificate me-2"></i>{{ __("quality::quality.certificates") }}</h2>
                    </div>
                    <div>
                        @can('create certificates')
                            <a href="{{ route('quality.certificates.create') }}" class="btn btn-primary">
                                <i class="fas fa-plus-circle me-2"></i>{{ __("quality::quality.new certificate") }}
                            </a>
                        @endcan
                    </div>
                </div>
            </div>
        </div>
 
        <div class="row g-3 mb-4">
            <div class="col-md-3">
                <div class="card">
                    <div class="card-body">
                        <h6 class="text-muted">{{ __("quality::quality.active certificates") }}</h6>
                        <h3 class="text-success">{{ $stats['active'] }}</h3>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card">
                    <div class="card-body">
                        <h6 class="text-muted">{{ __("quality::quality.expiring soon") }}</h6>
                        <h3 class="text-warning">{{ $stats['expiring_soon'] }}</h3>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card">
                    <div class="card-body">
                        <h6 class="text-muted">{{ __("quality::quality.expired certificate") }}</h6>
                        <h3 class="text-danger">{{ $stats['expired'] }}</h3>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card">
                    <div class="card-body">
                        <h6 class="text-muted">{{ __("quality::quality.total") }}</h6>
                        <h3>{{ $stats['total'] }}</h3>
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
                                <th>{{ __("quality::quality.certificate number") }}</th>
                                <th>{{ __("quality::quality.certificate name") }}</th>
                                <th>{{ __("quality::quality.certificate type") }}</th>
                                <th>{{ __("quality::quality.issuing authority") }}</th>
                                <th>{{ __("quality::quality.issue date") }}</th>
                                <th>{{ __("quality::quality.valid until") }}</th>
                                <th>{{ __("quality::quality.days remaining") }}</th>
                                <th>{{ __("quality::quality.status") }}</th>
                                @canany(['edit certificates', 'delete certificates', 'view certificates'])
                                    <th>{{ __("quality::quality.actions") }}</th>
                                @endcanany
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($certificates as $certificate)
                                <tr
                                    class="{{ $certificate->isExpired() ? 'table-danger' : ($certificate->isExpiringSoon() ? 'table-warning' : '') }}">
                                    <td><strong>{{ $certificate->certificate_number }}</strong></td>
                                    <td>{{ $certificate->certificate_name }}</td>
                                    <td>
                                        <span class="badge bg-info">
                                            {{ __("quality::quality.certificates") }}
                                        </span>
                                    </td>
                                    <td>{{ $certificate->issuing_authority }}</td>
                                    <td>{{ $certificate->issue_date->format('Y-m-d') }}</td>
                                    <td>{{ $certificate->expiry_date->format('Y-m-d') }}</td>
                                    <td>
                                        @php
                                            $daysLeft = $certificate->daysUntilExpiry();
                                        @endphp
                                        <span
                                            class="badge bg-{{ $daysLeft < 0 ? 'danger' : ($daysLeft < 30 ? 'warning' : 'success') }}">
                                            {{ abs($daysLeft) }} {{ $daysLeft < 0 ? __("quality::quality.expired certificate") : __("quality::quality.days") }}
                                        </span>
                                    </td>
                                    <td>
                                        <span
                                            class="badge bg-{{ match ($certificate->status) {
                                                'active' => 'success',
                                                'expired' => 'danger',
                                                'renewal_pending' => 'warning',
                                                'suspended' => 'dark',
                                                default => 'secondary',
                                            } }}">
                                            {{ match ($certificate->status) {
                                                'active' => __("quality::quality.active"),
                                                'expired' => __("quality::quality.expired certificate"),
                                                'renewal_pending' => __("quality::quality.renewal pending"),
                                                'suspended' => __("quality::quality.suspended"),
                                                default => $certificate->status,
                                            } }}
                                        </span>
                                    </td>
                                    @canany(['edit certificates', 'delete certificates', 'view certificates'])
                                        <td>
                                            <div class="btn-group" role="group">
                                                @can('view certificates')
                                                    <a href="{{ route('quality.certificates.show', $certificate) }}"
                                                        class="btn btn-sm btn-info" title="{{ __("quality::quality.view") }}">
                                                        <i class="fas fa-eye"></i>
                                                    </a>
                                                @endcan
                                                @can('edit certificates')
                                                    <a href="{{ route('quality.certificates.edit', $certificate) }}"
                                                        class="btn btn-sm btn-warning" title="{{ __("quality::quality.edit") }}">
                                                        <i class="fas fa-edit"></i>
                                                    </a>
                                                @endcan
                                                @can('delete certificates')
                                                    <button type="button" class="btn btn-sm btn-danger" data-bs-toggle="modal"
                                                        data-bs-target="#deleteModal{{ $certificate->id }}" title="{{ __("quality::quality.delete") }}">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                @endcan
                                            </div>

                                            <!-- Delete Modal -->
                                            <div class="modal fade" id="deleteModal{{ $certificate->id }}" tabindex="-1">
                                                <div class="modal-dialog">
                                                    <div class="modal-content">
                                                        <div class="modal-header">
                                                            <h5 class="modal-title">{{ __("quality::quality.confirm delete") }}</h5>
                                                            <button type="button" class="btn-close"
                                                                data-bs-dismiss="modal"></button>
                                                        </div>
                                                        <div class="modal-body">
                                                            {{ __("quality::quality.are you sure you want to delete certificate") }} "{{ $certificate->certificate_name }}"?
                                                        </div>
                                                        <div class="modal-footer">
                                                            <button type="button" class="btn btn-secondary"
                                                                data-bs-dismiss="modal">{{ __("quality::quality.cancel") }}</button>
                                                            <form
                                                                action="{{ route('quality.certificates.destroy', $certificate) }}"
                                                                method="POST" class="d-inline">
                                                                @csrf
                                                                @method('DELETE')
                                                                <button type="submit" class="btn btn-danger">{{ __("quality::quality.delete") }}</button>
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
                                    <td colspan="9" class="text-center py-4">{{ __("quality::quality.no certificates") }}</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                @if ($certificates->hasPages())
                    <div class="d-flex justify-content-center mt-4">
                        {{ $certificates->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection
