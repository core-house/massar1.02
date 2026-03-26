@extends('admin.dashboard')

@section('sidebar')
    @include('components.sidebar.shipping')
@endsection

@section('content')
    @include('components.breadcrumb', [
        'title' => __('shipping::shipping.drivers'),
        'breadcrumb_items' => [['label' => __('shipping::shipping.dashboard'), 'url' => route('admin.dashboard')], ['label' => __('shipping::shipping.drivers')]],
    ])

    <div class="row">
        <div class="col-lg-12">
            @can('create Drivers')
                <a href="{{ route('drivers.create') }}" type="button" class="btn btn-main font-hold fw-bold">
                    {{ __('shipping::shipping.add_new') }}
                    <i class="las la-plus me-2"></i>
                </a>
            @endcan
            <br><br>

            <div class="card">
                <div class="card-body">
                    <div class="table-responsive" style="overflow-x: auto;">

                        <x-table-export-actions table-id="drivers-table" filename="drivers-table"
                            excel-label="{{ __('shipping::shipping.export_excel') }}" pdf-label="{{ __('shipping::shipping.export_pdf') }}"
                            print-label="{{ __('shipping::shipping.print') }}" />

                        <table id="drivers-table" class="table table-striped mb-0" style="min-width: 1200px;">
                            <thead class="table-light text-center align-middle">
                                <tr>
                                    <th>#</th>
                                    <th>{{ __('shipping::shipping.name') }}</th>
                                    <th>{{ __('shipping::shipping.phone_number') }}</th>
                                    <th>{{ __('shipping::shipping.vehicle_type') }}</th>
                                    <th>{{ __('shipping::shipping.rating') }}</th>
                                    <th>{{ __('shipping::shipping.deliveries') }}</th>
                                    <th>{{ __('shipping::shipping.success_rate') }}</th>
                                    <th>{{ __('shipping::shipping.status') }}</th>
                                    @canany(['edit Drivers', 'delete Drivers'])
                                        <th>{{ __('shipping::shipping.actions') }}</th>
                                    @endcanany
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($drivers as $driver)
                                    <tr class="text-center">
                                        <td>{{ $loop->iteration }}</td>
                                        <td>{{ $driver->name }}</td>
                                        <td>{{ $driver->phone }}</td>
                                        <td>{{ $driver->vehicle_type }}</td>
                                        <td>
                                            @if($driver->rating > 0)
                                                <span class="badge bg-warning">
                                                    <i class="las la-star"></i> {{ number_format($driver->rating, 1) }}
                                                </span>
                                                <small>({{ $driver->total_ratings }})</small>
                                            @else
                                                <span class="text-muted">{{ __('shipping::shipping.no_ratings') }}</span>
                                            @endif
                                        </td>
                                        <td>
                                            <span class="badge bg-success">{{ $driver->completed_deliveries }}</span> / 
                                            <span class="badge bg-danger">{{ $driver->failed_deliveries }}</span>
                                        </td>
                                        <td>
                                            @if($driver->success_rate > 0)
                                                <span class="badge bg-{{ $driver->success_rate >= 80 ? 'success' : ($driver->success_rate >= 50 ? 'warning' : 'danger') }}">
                                                    {{ $driver->success_rate }}%
                                                </span>
                                            @else
                                                <span class="text-muted">-</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if ($driver->is_available)
                                                <span class="badge bg-primary">{{ __('shipping::shipping.available') }}</span>
                                            @else
                                                <span class="badge bg-danger">{{ __('shipping::shipping.unavailable') }}</span>
                                            @endif
                                        </td>
                                        @canany(['edit Drivers', 'delete Drivers'])
                                            <td>
                                                @can('edit Drivers')
                                                    <a class="btn btn-success btn-icon-square-sm"
                                                        href="{{ route('drivers.edit', $driver) }}">
                                                        <i class="las la-edit"></i>
                                                    </a>
                                                @endcan

                                                @can('delete Drivers')
                                                    <form action="{{ route('drivers.destroy', $driver) }}" method="POST"
                                                        style="display:inline-block;"
                                                        onsubmit="return confirm('{{ __('shipping::shipping.confirm_delete_driver') }}');">
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
                                        <td colspan="9" class="text-center">
                                            <div class="alert alert-info py-3 mb-0"
                                                style="font-size: 1.2rem; font-weight: 500;">
                                                <i class="las la-info-circle me-2"></i>
                                                {{ __('shipping::shipping.no_data_available') }}
                                            </div>
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

    {{ $drivers->links() }}
@endsection
