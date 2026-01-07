@extends('admin.dashboard')

@section('sidebar')
    @include('components.sidebar.fleet')
@endsection

@section('content')
    @include('components.breadcrumb', [
        'title' => __('Trips'),
        'items' => [['label' => __('Home'), 'url' => route('admin.dashboard')], ['label' => __('Trips')]],
    ])

    <div class="row">
        <div class="col-lg-12">
            @can('create Trips')
                <a href="{{ route('fleet.trips.create') }}" type="button" class="btn btn-primary font-hold fw-bold">
                    {{ __('Add New') }}
                    <i class="fas fa-plus me-2"></i>
                </a>
            @endcan
            <br><br>

            <div class="card">
                <div class="card-body">
                    <div class="table-responsive" style="overflow-x: auto;">
                        <x-table-export-actions table-id="trips-table" filename="trips"
                            excel-label="{{ __('Export Excel') }}" pdf-label="{{ __('Export PDF') }}"
                            print-label="{{ __('Print') }}" />
                        <table id="trips-table" class="table table-striped mb-0" style="min-width: 1200px;">
                            <thead class="table-light text-center align-middle">
                                <tr>
                                    <th>#</th>
                                    <th>{{ __('Trip Number') }}</th>
                                    <th>{{ __('Vehicle') }}</th>
                                    <th>{{ __('Driver') }}</th>
                                    <th>{{ __('Start Location') }}</th>
                                    <th>{{ __('End Location') }}</th>
                                    <th>{{ __('Start Date') }}</th>
                                    <th>{{ __('Status') }}</th>
                                    <th>{{ __('Distance') }}</th>
                                    @canany(['edit Trips', 'delete Trips'])
                                        <th>{{ __('Actions') }}</th>
                                    @endcanany
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($trips as $trip)
                                    <tr class="text-center">
                                        <td>{{ $loop->iteration }}</td>
                                        <td>{{ $trip->trip_number }}</td>
                                        <td>{{ $trip->vehicle->name ?? '-' }}</td>
                                        <td>{{ $trip->driver->name ?? '-' }}</td>
                                        <td>{{ $trip->start_location }}</td>
                                        <td>{{ $trip->end_location }}</td>
                                        <td>{{ $trip->start_date->format('Y-m-d H:i') }}</td>
                                        <td>
                                            <span class="badge bg-{{ $trip->status->color() }}">
                                                {{ $trip->status->label() }}
                                            </span>
                                        </td>
                                        <td>{{ $trip->distance ? number_format($trip->distance, 2) . ' km' : '-' }}</td>
                                        @canany(['edit Trips', 'delete Trips'])
                                            <td>
                                                @can('view Trips')
                                                    <a class="btn btn-primary btn-icon-square-sm"
                                                        href="{{ route('fleet.trips.show', $trip->id) }}">
                                                        <i class="las la-eye"></i>
                                                    </a>
                                                @endcan
                                                @can('edit Trips')
                                                    <a class="btn btn-success btn-icon-square-sm"
                                                        href="{{ route('fleet.trips.edit', $trip->id) }}">
                                                        <i class="las la-edit"></i>
                                                    </a>
                                                @endcan
                                                @can('delete Trips')
                                                    <form action="{{ route('fleet.trips.destroy', $trip->id) }}" method="POST"
                                                        style="display:inline-block;"
                                                        onsubmit="return confirm('{{ __('Are you sure you want to delete this item?') }}');">
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
                                        <td colspan="10" class="text-center">
                                            <div class="alert alert-info py-3 mb-0">{{ __('No data available') }}</div>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                        <div class="mt-3">
                            {{ $trips->links() }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
