@extends('admin.dashboard')

@section('sidebar')
    @include('components.sidebar.shipping')
@endsection

@section('content')
    @include('components.breadcrumb', [
        'title' => __('shipping::shipping.shipments'),
        'breadcrumb_items' => [
            ['label' => __('shipping::shipping.dashboard'), 'url' => route('admin.dashboard')],
            ['label' => __('shipping::shipping.shipments'), 'url' => route('shipments.index')],
            ['label' => __('shipping::shipping.edit')],
        ],
    ])

    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-header">
                    <h2>{{ __('shipping::shipping.edit_shipment') }}</h2>
                </div>
                <div class="card-body">
                    @can('edit Shipments')
                        <form action="{{ route('shipments.update', $shipment) }}" method="POST">
                            @csrf
                            @method('PUT')
                            <div class="row">
                                <div class="mb-3 col-lg-4">
                                    <label class="form-label" for="tracking_number">{{ __('shipping::shipping.tracking_number') }}</label>
                                    <input type="text" class="form-control" id="tracking_number" name="tracking_number"
                                        placeholder="{{ __('shipping::shipping.enter_tracking_number') }}"
                                        value="{{ old('tracking_number', $shipment->tracking_number) }}">
                                    @error('tracking_number')
                                        <small class="text-danger">{{ $message }}</small>
                                    @enderror
                                </div>

                                <div class="mb-3 col-lg-4">
                                    <label class="form-label" for="shipping_company_id">{{ __('shipping::shipping.shipping_company') }}</label>
                                    <select class="form-control" id="shipping_company_id" name="shipping_company_id">
                                        @foreach ($companies as $company)
                                            <option value="{{ $company->id }}"
                                                {{ old('shipping_company_id', $shipment->shipping_company_id) == $company->id ? 'selected' : '' }}>
                                                {{ $company->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('shipping_company_id')
                                        <small class="text-danger">{{ $message }}</small>
                                    @enderror
                                </div>

                                <div class="mb-3 col-lg-4">
                                    <label class="form-label" for="customer_name">{{ __('shipping::shipping.customer_name') }}</label>
                                    <input type="text" class="form-control" id="customer_name" name="customer_name"
                                        placeholder="{{ __('shipping::shipping.enter_customer_name') }}"
                                        value="{{ old('customer_name', $shipment->customer_name) }}">
                                    @error('customer_name')
                                        <small class="text-danger">{{ $message }}</small>
                                    @enderror
                                </div>

                                <div class="mb-3 col-lg-4">
                                    <label class="form-label" for="customer_address">{{ __('shipping::shipping.customer_address') }}</label>
                                    <textarea class="form-control" id="customer_address" name="customer_address" placeholder="{{ __('shipping::shipping.full_address') }}">{{ old('customer_address', $shipment->customer_address) }}</textarea>
                                    @error('customer_address')
                                        <small class="text-danger">{{ $message }}</small>
                                    @enderror
                                </div>

                                <div class="mb-3 col-lg-4">
                                    <label class="form-label" for="weight">{{ __('shipping::shipping.weight') }} ({{ __('shipping::shipping.kg') }})</label>
                                    <input type="number" class="form-control" id="weight" name="weight" step="0.01"
                                        placeholder="{{ __('shipping::shipping.enter_weight') }}" value="{{ old('weight', $shipment->weight) }}">
                                    @error('weight')
                                        <small class="text-danger">{{ $message }}</small>
                                    @enderror
                                </div>

                                <div class="mb-3 col-lg-4">
                                    <label class="form-label" for="status">{{ __('shipping::shipping.status') }}</label>
                                    <select class="form-control" id="status" name="status">
                                        <option value="pending"
                                            {{ old('status', $shipment->status) == 'pending' ? 'selected' : '' }}>
                                            {{ __('shipping::shipping.pending') }}
                                        </option>
                                        <option value="in_transit"
                                            {{ old('status', $shipment->status) == 'in_transit' ? 'selected' : '' }}>
                                            {{ __('shipping::shipping.in_transit') }}
                                        </option>
                                        <option value="delivered"
                                            {{ old('status', $shipment->status) == 'delivered' ? 'selected' : '' }}>
                                            {{ __('shipping::shipping.delivered') }}
                                        </option>
                                    </select>
                                    @error('status')
                                        <small class="text-danger">{{ $message }}</small>
                                    @enderror
                                </div>
                            </div>

                            <div class="d-flex justify-content-start mt-4">
                                <button type="submit" class="btn btn-main me-2">
                                    <i class="las la-save"></i> {{ __('shipping::shipping.save') }}
                                </button>
                                <a href="{{ route('shipments.index') }}" class="btn btn-danger">
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
