@extends('admin.dashboard')

@section('sidebar')
    @include('components.sidebar.shipping')
@endsection

@section('content')
    @include('components.breadcrumb', [
        'title' => __('shipping::shipping.orders'),
        'breadcrumb_items' => [
            ['label' => __('shipping::shipping.dashboard'), 'url' => route('admin.dashboard')],
            ['label' => __('shipping::shipping.orders'), 'url' => route('orders.index')],
            ['label' => __('shipping::shipping.edit')],
        ],
    ])

    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-header">
                    <h2>{{ __('shipping::shipping.edit_order') }}</h2>
                </div>
                <div class="card-body">
                    @can('edit Orders')
                        <form action="{{ route('orders.update', $order) }}" method="POST">
                            @csrf
                            @method('PUT')
                            <div class="row">
                                <div class="mb-3 col-lg-4">
                                    <label class="form-label" for="order_number">{{ __('shipping::shipping.order_number') }}</label>
                                    <input type="text" class="form-control" id="order_number" name="order_number"
                                        placeholder="{{ __('shipping::shipping.enter_order_number') }}"
                                        value="{{ old('order_number', $order->order_number) }}">
                                    @error('order_number')
                                        <small class="text-danger">{{ $message }}</small>
                                    @enderror
                                </div>

                                <div class="mb-3 col-lg-4">
                                    <label class="form-label" for="driver_id">{{ __('shipping::shipping.driver') }}</label>
                                    <select class="form-control" id="driver_id" name="driver_id">
                                        @foreach ($drivers as $driver)
                                            <option value="{{ $driver->id }}"
                                                {{ old('driver_id', $order->driver_id) == $driver->id ? 'selected' : '' }}>
                                                {{ $driver->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('driver_id')
                                        <small class="text-danger">{{ $message }}</small>
                                    @enderror
                                </div>

                                <div class="mb-3 col-lg-4">
                                    <label class="form-label" for="shipment_id">{{ __('shipping::shipping.shipment') }}</label>
                                    <select class="form-control" id="shipment_id" name="shipment_id">
                                        @foreach ($shipments as $shipment)
                                            <option value="{{ $shipment->id }}"
                                                {{ old('shipment_id', $order->shipment_id) == $shipment->id ? 'selected' : '' }}>
                                                {{ $shipment->tracking_number }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('shipment_id')
                                        <small class="text-danger">{{ $message }}</small>
                                    @enderror
                                </div>

                                <div class="mb-3 col-lg-4">
                                    <label class="form-label" for="customer_name">{{ __('shipping::shipping.customer_name') }}</label>
                                    <input type="text" class="form-control" id="customer_name" name="customer_name"
                                        placeholder="{{ __('shipping::shipping.enter_customer_name') }}"
                                        value="{{ old('customer_name', $order->customer_name) }}">
                                    @error('customer_name')
                                        <small class="text-danger">{{ $message }}</small>
                                    @enderror
                                </div>

                                <div class="mb-3 col-lg-4">
                                    <label class="form-label" for="customer_address">{{ __('shipping::shipping.customer_address') }}</label>
                                    <textarea class="form-control" id="customer_address" name="customer_address" placeholder="{{ __('shipping::shipping.full_address') }}">{{ old('customer_address', $order->customer_address) }}</textarea>
                                    @error('customer_address')
                                        <small class="text-danger">{{ $message }}</small>
                                    @enderror
                                </div>

                                <div class="mb-3 col-lg-4">
                                    <label class="form-label" for="delivery_status">{{ __('shipping::shipping.delivery_status') }}</label>
                                    <select class="form-control" id="delivery_status" name="delivery_status">
                                        <option value="pending"
                                            {{ old('delivery_status', $order->delivery_status) == 'pending' ? 'selected' : '' }}>
                                            {{ __('shipping::shipping.pending') }}
                                        </option>
                                        <option value="assigned"
                                            {{ old('delivery_status', $order->delivery_status) == 'assigned' ? 'selected' : '' }}>
                                            {{ __('shipping::shipping.assigned') }}
                                        </option>
                                        <option value="in_transit"
                                            {{ old('delivery_status', $order->delivery_status) == 'in_transit' ? 'selected' : '' }}>
                                            {{ __('shipping::shipping.in_transit') }}
                                        </option>
                                        <option value="delivered"
                                            {{ old('delivery_status', $order->delivery_status) == 'delivered' ? 'selected' : '' }}>
                                            {{ __('shipping::shipping.delivered') }}
                                        </option>
                                    </select>
                                    @error('delivery_status')
                                        <small class="text-danger">{{ $message }}</small>
                                    @enderror
                                </div>
                            </div>

                            <div class="d-flex justify-content-start mt-4">
                                <button type="submit" class="btn btn-main me-2">
                                    <i class="las la-save"></i> {{ __('shipping::shipping.save') }}
                                </button>
                                <a href="{{ route('orders.index') }}" class="btn btn-danger">
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
