@extends('admin.dashboard')

@section('sidebar')
    @include('components.sidebar.shipping')
@endsection

@section('content')
    @include('components.breadcrumb', [
        'title' => __('shipping::shipping.drivers'),
        'breadcrumb_items' => [
            ['label' => __('shipping::shipping.dashboard'), 'url' => route('admin.dashboard')],
            ['label' => __('shipping::shipping.drivers'), 'url' => route('drivers.index')],
            ['label' => __('shipping::shipping.add_new')],
        ],
    ])

    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-header">
                    <h2>{{ __('shipping::shipping.add_new_driver') }}</h2>
                </div>
                <div class="card-body">
                    @can('create Drivers')
                        <form action="{{ route('drivers.store') }}" method="POST">
                            @csrf
                            <div class="row">
                                <div class="mb-3 col-lg-4">
                                    <label class="form-label" for="name">{{ __('shipping::shipping.name') }}</label>
                                    <input type="text" class="form-control" id="name" name="name"
                                        placeholder="{{ __('shipping::shipping.enter_driver_name') }}" value="{{ old('name') }}">
                                    @error('name')
                                        <small class="text-danger">{{ $message }}</small>
                                    @enderror
                                </div>

                                <div class="mb-3 col-lg-4">
                                    <label class="form-label" for="phone">{{ __('shipping::shipping.phone_number') }}</label>
                                    <input type="text" class="form-control" id="phone" name="phone"
                                        placeholder="{{ __('shipping::shipping.enter_phone_number') }}" value="{{ old('phone') }}">
                                    @error('phone')
                                        <small class="text-danger">{{ $message }}</small>
                                    @enderror
                                </div>

                                <div class="mb-3 col-lg-4">
                                    <label class="form-label" for="vehicle_type">{{ __('shipping::shipping.vehicle_type') }}</label>
                                    <input type="text" class="form-control" id="vehicle_type" name="vehicle_type"
                                        placeholder="{{ __('shipping::shipping.enter_vehicle_type') }}" value="{{ old('vehicle_type') }}">
                                    @error('vehicle_type')
                                        <small class="text-danger">{{ $message }}</small>
                                    @enderror
                                </div>

                                <div class="mb-3 col-lg-4">
                                    <label class="form-label" for="is_available">{{ __('shipping::shipping.status') }}</label>
                                    <select class="form-control" id="is_available" name="is_available">
                                        <option value="1" {{ old('is_available', 1) == 1 ? 'selected' : '' }}>
                                            {{ __('shipping::shipping.available') }}
                                        </option>
                                        <option value="0" {{ old('is_available') == 0 ? 'selected' : '' }}>
                                            {{ __('shipping::shipping.unavailable') }}
                                        </option>
                                    </select>
                                    @error('is_available')
                                        <small class="text-danger">{{ $message }}</small>
                                    @enderror
                                </div>

                                <x-branches::branch-select :branches="$branches" />

                            </div>

                            <div class="d-flex justify-content-start mt-4">
                                <button type="submit" class="btn btn-main me-2">
                                    <i class="las la-save"></i> {{ __('shipping::shipping.save') }}
                                </button>
                                <a href="{{ route('drivers.index') }}" class="btn btn-danger">
                                    <i class="las la-times"></i> {{ __('shipping::shipping.cancel') }}
                                </a>
                            </div>
                        </form>
                    @endcan
                </div>
            </div>
        </div>
    </div>
@endsection
