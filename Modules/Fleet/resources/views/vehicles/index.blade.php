@extends('admin.dashboard')

@section('sidebar')
    @include('components.sidebar.fleet')
@endsection

@section('content')
    @include('components.breadcrumb', [
        'title' => __('fleet::fleet.Vehicles'),
        'breadcrumb_items' => [['label' => __('fleet::fleet.Home'), 'url' => route('admin.dashboard')], ['label' => __('fleet::fleet.Vehicles')]],
    ])

    <div class="row">
        <div class="col-lg-12">
            @can('create Vehicles')
                <a href="{{ route('fleet.vehicles.create') }}" type="button" class="btn btn-primary font-hold fw-bold">
                    {{ __('fleet::fleet.Add New') }}
                    <i class="fas fa-plus me-2"></i>
                </a>
            @endcan
            <br><br>

            <div class="card">
                <div class="card-body">
                    <div class="table-responsive" style="overflow-x: auto;">
                        <x-table-export-actions table-id="vehicles-table" filename="vehicles"
                            excel-label="{{ __('fleet::fleet.Export Excel') }}" pdf-label="{{ __('fleet::fleet.Export PDF') }}"
                            print-label="{{ __('fleet::fleet.Print') }}" />
                        <table id="vehicles-table" class="table table-striped mb-0" style="min-width: 1200px;">
                            <thead class="table-light text-center align-middle">
                                <tr>
                                    <th>#</th>
                                    <th>{{ __('fleet::fleet.Code') }}</th>
                                    <th>{{ __('fleet::fleet.Plate Number') }}</th>
                                    <th>{{ __('fleet::fleet.Name') }}</th>
                                    <th>{{ __('fleet::fleet.Vehicle Type') }}</th>
                                    <th>{{ __('fleet::fleet.Driver') }}</th>
                                    <th>{{ __('fleet::fleet.Status') }}</th>
                                    <th>{{ __('fleet::fleet.Current Mileage') }}</th>
                                    @canany(['edit Vehicles', 'delete Vehicles'])
                                        <th>{{ __('fleet::fleet.Actions') }}</th>
                                    @endcanany
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($vehicles as $vehicle)
                                    <tr class="text-center">
                                        <td>{{ $loop->iteration }}</td>
                                        <td>{{ $vehicle->code }}</td>
                                        <td>{{ $vehicle->plate_number }}</td>
                                        <td>{{ $vehicle->name }}</td>
                                        <td>{{ $vehicle->vehicleType->name ?? '-' }}</td>
                                        <td>{{ $vehicle->driver->name ?? '-' }}</td>
                                        <td>
                                            <span class="badge bg-{{ $vehicle->status->color() }}">
                                                {{ $vehicle->status->label() }}
                                            </span>
                                        </td>
                                        <td>{{ number_format($vehicle->current_mileage, 2) }} {{ __('fleet::fleet.km') }}</td>
                                        @canany(['edit Vehicles', 'delete Vehicles'])
                                            <td>
                                                @can('view Vehicles')
                                                    <a class="btn btn-primary btn-icon-square-sm"
                                                        href="{{ route('fleet.vehicles.show', $vehicle->id) }}">
                                                        <i class="las la-eye"></i>
                                                    </a>
                                                @endcan
                                                @can('edit Vehicles')
                                                    <a class="btn btn-success btn-icon-square-sm"
                                                        href="{{ route('fleet.vehicles.edit', $vehicle->id) }}">
                                                        <i class="las la-edit"></i>
                                                    </a>
                                                @endcan
                                                @can('delete Vehicles')
                                                    <form action="{{ route('fleet.vehicles.destroy', $vehicle->id) }}"
                                                        method="POST" style="display:inline-block;"
                                                        onsubmit="return confirm('{{ __('fleet::fleet.confirm_delete_item') }}');">
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
                                            <div class="alert alert-info py-3 mb-0">{{ __('fleet::fleet.no_data_available') }}</div>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                        <div class="mt-3">
                            {{ $vehicles->links() }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
