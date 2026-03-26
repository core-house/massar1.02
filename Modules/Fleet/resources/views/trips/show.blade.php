@extends('admin.dashboard')

@section('sidebar')
    @include('components.sidebar.fleet')
@endsection

@section('content')
    @include('components.breadcrumb', [
        'title' => __('fleet::fleet.Trip'),
        'breadcrumb_items' => [
            ['label' => __('fleet::fleet.Home'), 'url' => route('admin.dashboard')],
            ['label' => __('fleet::fleet.Trips'), 'url' => route('fleet.trips.index')],
            ['label' => $trip->trip_number],
        ],
    ])

    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-header">
                    <div class="d-flex justify-content-between align-items-center">
                        <h4 class="page-title">{{ __('fleet::fleet.Trip') }}: {{ $trip->trip_number }}</h4>
                        <div class="d-flex gap-2">
                            @can('edit Trips')
                                <a href="{{ route('fleet.trips.edit', $trip) }}" class="btn btn-primary">
                                    <i class="fas fa-edit"></i> {{ __('fleet::fleet.Edit') }}
                                </a>
                            @endcan
                            <a href="{{ route('fleet.trips.index') }}" class="btn btn-secondary">
                                <i class="fas fa-arrow-right"></i> {{ __('fleet::fleet.Back') }}
                            </a>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">{{ __('fleet::fleet.Trip Number') }}:</label>
                            <div class="form-control-static">{{ $trip->trip_number }}</div>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">{{ __('fleet::fleet.Status') }}:</label>
                            <div class="form-control-static">
                                <span class="badge bg-{{ $trip->status->color() }}">
                                    {{ $trip->status->label() }}
                                </span>
                            </div>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">{{ __('fleet::fleet.Vehicle') }}:</label>
                            <div class="form-control-static">{{ $trip->vehicle->name ?? '-' }} ({{ $trip->vehicle->plate_number ?? '-' }})</div>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">{{ __('fleet::fleet.Driver') }}:</label>
                            <div class="form-control-static">{{ $trip->driver->name ?? '-' }}</div>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">{{ __('fleet::fleet.Start Location') }}:</label>
                            <div class="form-control-static">{{ $trip->start_location }}</div>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">{{ __('fleet::fleet.End Location') }}:</label>
                            <div class="form-control-static">{{ $trip->end_location }}</div>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">{{ __('fleet::fleet.Start Date') }}:</label>
                            <div class="form-control-static">{{ $trip->start_date->format('Y-m-d H:i') }}</div>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">{{ __('fleet::fleet.End Date') }}:</label>
                            <div class="form-control-static">{{ $trip->end_date?->format('Y-m-d H:i') ?? '-' }}</div>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">{{ __('fleet::fleet.Start Mileage') }}:</label>
                            <div class="form-control-static">{{ number_format($trip->start_mileage, 2) }} {{ __('fleet::fleet.km') }}</div>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">{{ __('fleet::fleet.End Mileage') }}:</label>
                            <div class="form-control-static">{{ $trip->end_mileage ? number_format($trip->end_mileage, 2) . ' ' . __('fleet::fleet.km') : '-' }}</div>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">{{ __('fleet::fleet.Distance') }}:</label>
                            <div class="form-control-static">{{ $trip->distance ? number_format($trip->distance, 2) . ' ' . __('fleet::fleet.km') : '-' }}</div>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">{{ __('fleet::fleet.Purpose') }}:</label>
                            <div class="form-control-static">{{ $trip->purpose ?? '-' }}</div>
                        </div>

                        <div class="col-md-12 mb-3">
                            <label class="form-label fw-bold">{{ __('fleet::fleet.Notes') }}:</label>
                            <div class="form-control-static">{{ $trip->notes ?? '-' }}</div>
                        </div>
                    </div>

                    @if($trip->fuelRecords->count() > 0)
                        <hr>
                        <h5 class="mb-3">{{ __('fleet::fleet.Fuel Records') }}</h5>
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>{{ __('fleet::fleet.Fuel Date') }}</th>
                                        <th>{{ __('fleet::fleet.Fuel Type') }}</th>
                                        <th>{{ __('fleet::fleet.Quantity') }}</th>
                                        <th>{{ __('fleet::fleet.Cost') }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($trip->fuelRecords as $record)
                                        <tr>
                                            <td>{{ $loop->iteration }}</td>
                                            <td>{{ $record->fuel_date->format('Y-m-d') }}</td>
                                            <td>{{ $record->fuel_type->label() }}</td>
                                            <td>{{ number_format($record->quantity, 2) }} L</td>
                                            <td>{{ number_format($record->cost, 2) }} {{ __('fleet::fleet.SAR') }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection

