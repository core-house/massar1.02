@extends('admin.dashboard')

@section('sidebar')
    @include('components.sidebar.rentals')
@endsection

@section('content')
    @include('components.breadcrumb', [
        'title' => __('rentals::rentals.leases'),
        'breadcrumb_items' => [
            ['label' => __('navigation.home'), 'url' => route('admin.dashboard')],
            ['label' => __('rentals::rentals.leases')],
        ],
    ])

    <div class="container-fluid px-4">
        <div class="row">
            <div class="col-lg-12">
                @can('create Leases')
                    <a href="{{ route('rentals.leases.create') }}" class="btn btn-primary fw-bold mb-3">
                        <i class="fas fa-plus me-2"></i>{{ __('rentals::rentals.add_new_lease') }}
                    </a>
                @endcan
                
                <div class="card">
                    <div class="card-body">
                        <x-table-export-actions table-id="leases-table" filename="leases" 
                            excel-label="{{ __('rentals::rentals.export_excel') }}"
                            pdf-label="{{ __('rentals::rentals.export_pdf') }}" 
                            print-label="{{ __('rentals::rentals.print') }}" />

                        <div class="table-responsive">
                            <table id="leases-table" class="table table-striped mb-0">
                                <thead class="table-light text-center align-middle">
                                    <tr>
                                        <th>#</th>
                                        <th>{{ __('rentals::rentals.unit') }}</th>
                                        <th>{{ __('rentals::rentals.rent_type') }}</th>
                                        <th>{{ __('rentals::rentals.client') }}</th>
                                        <th>{{ __('rentals::rentals.start_date') }}</th>
                                        <th>{{ __('rentals::rentals.end_date') }}</th>
                                        <th>{{ __('rentals::rentals.rent_amount') }}</th>
                                        <th>{{ __('rentals::rentals.account') }}</th>
                                        <th>{{ __('rentals::rentals.status') }}</th>
                                        <th>{{ __('rentals::rentals.notes') }}</th>
                                        @canany(['edit Leases', 'delete Leases'])
                                            <th>{{ __('rentals::rentals.actions') }}</th>
                                        @endcanany
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($leases as $lease)
                                        <tr class="text-center align-middle">
                                            <td>{{ $loop->iteration }}</td>
                                            <td>
                                                <div class="d-flex flex-column align-items-center">
                                                    <span class="badge {{ ($lease->unit->unit_type ?? 'building') === 'item' ? 'bg-info' : 'bg-primary' }} mb-1" style="font-size: 0.7rem;">
                                                        {{ ($lease->unit->unit_type ?? 'building') === 'item' ? __('rentals::rentals.item') : __('rentals::rentals.unit') }}
                                                    </span>
                                                    <span class="fw-bold">
                                                        @if (($lease->unit->unit_type ?? 'building') === 'item' && $lease->unit->item)
                                                            {{ $lease->unit->item->name }}
                                                        @else
                                                            {{ optional($lease->unit)->name }}
                                                        @endif
                                                    </span>
                                                </div>
                                            </td>
                                            <td>
                                                <span class="badge bg-light text-dark border">
                                                    {{ ucfirst(__('rentals::rentals.' . ($lease->rent_type ?? 'monthly'))) }}
                                                </span>
                                            </td>
                                            <td>{{ optional($lease->client)->cname }}</td>
                                            <td>{{ $lease->start_date }}</td>
                                            <td>{{ $lease->end_date }}</td>
                                            <td>{{ number_format($lease->rent_amount, 2) }}</td>
                                            <td>{{ $lease->account->aname ?? '-' }}</td>
                                            <td>
                                                <span class="badge {{ match ($lease->status) {
                                                    \Modules\Rentals\Enums\LeaseStatus::PENDING => 'bg-warning',
                                                    \Modules\Rentals\Enums\LeaseStatus::ACTIVE => 'bg-success',
                                                    \Modules\Rentals\Enums\LeaseStatus::EXPIRED => 'bg-secondary',
                                                    \Modules\Rentals\Enums\LeaseStatus::TERMINATED => 'bg-danger',
                                                    default => 'bg-secondary',
                                                } }}">
                                                    {{ $lease->status->label() }}
                                                </span>
                                            </td>
                                            <td>{{ $lease->notes ?? '-' }}</td>
                                            @canany(['edit Leases', 'delete Leases'])
                                                <td>
                                                    @can('view Leases')
                                                        <a class="btn btn-primary btn-icon-square-sm" href="{{ route('rentals.leases.show', $lease->id) }}">
                                                            <i class="las la-eye"></i>
                                                        </a>
                                                    @endcan
                                                    @can('edit Leases')
                                                        <a class="btn btn-success btn-icon-square-sm" href="{{ route('rentals.leases.edit', $lease->id) }}">
                                                            <i class="las la-edit"></i>
                                                        </a>
                                                    @endcan
                                                    @can('delete Leases')
                                                        <form action="{{ route('rentals.leases.destroy', $lease->id) }}" method="POST" style="display:inline-block;" onsubmit="return confirm('{{ __('rentals::rentals.delete_lease_confirm') }}');">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="submit" class="btn btn-danger btn-icon-square-sm">
                                                                <i class="las la-trash"></i>
                                                            </button>
                                                        </form>
                                                    @endcan
                                                </td>
                                            @endcanany
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="11" class="text-center">
                                                <div class="alert alert-info py-3 mb-0">
                                                    <i class="las la-info-circle me-2"></i>
                                                    {{ __('rentals::rentals.no_leases_added') }}
                                                </div>
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                        <div class="mt-3">
                            {{ $leases->links() }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
