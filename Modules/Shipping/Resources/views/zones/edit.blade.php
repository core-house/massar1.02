@extends('admin.dashboard')

@section('content')
@include('components.breadcrumb', [
    'title' => __('shipping::shipping.shipping_zones'),
    'breadcrumb_items' => [
        ['label' => __('shipping::shipping.dashboard'), 'url' => route('admin.dashboard')],
        ['label' => __('shipping::shipping.shipping_zones'), 'url' => route('shipping.zones.index')],
        ['label' => __('shipping::shipping.edit')],
    ],
])
<div class="row">
    <div class="col-lg-8">
        <div class="card">
            <div class="card-body">
                <h2>{{ __('shipping::shipping.edit_zone') }}</h2>
                <form action="{{ route('shipping.zones.update', $zone->id) }}" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="mb-3">
                        <label class="form-label" for="name">{{ __('shipping::shipping.name') }}</label>
                        <input type="text" class="form-control" id="name" name="name"
                            placeholder="{{ __('shipping::shipping.enter_zone_name') }}" value="{{ old('name', $zone->name) }}" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label" for="code">{{ __('shipping::shipping.code') }}</label>
                        <input type="text" class="form-control" id="code" name="code"
                            placeholder="{{ __('shipping::shipping.enter_zone_code') }}" value="{{ old('code', $zone->code) }}" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label" for="description">{{ __('shipping::shipping.description') }}</label>
                        <textarea class="form-control" id="description" name="description" rows="3"
                            placeholder="{{ __('shipping::shipping.enter_zone_description') }}">{{ old('description', $zone->description) }}</textarea>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label" for="base_rate">{{ __('shipping::shipping.base_rate') }}</label>
                            <input type="number" step="0.01" class="form-control" id="base_rate" name="base_rate"
                                placeholder="{{ __('shipping::shipping.enter_base_rate') }}" value="{{ old('base_rate', $zone->base_rate) }}" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label" for="rate_per_kg">{{ __('shipping::shipping.rate_per_kg') }}</label>
                            <input type="number" step="0.01" class="form-control" id="rate_per_kg" name="rate_per_kg"
                                placeholder="{{ __('shipping::shipping.enter_rate_per_kg') }}" value="{{ old('rate_per_kg', $zone->rate_per_kg) }}" required>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label" for="estimated_days">{{ __('shipping::shipping.estimated_days') }}</label>
                        <input type="number" class="form-control" id="estimated_days" name="estimated_days"
                            placeholder="{{ __('shipping::shipping.enter_estimated_days') }}" value="{{ old('estimated_days', $zone->estimated_days) }}" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label" for="branch_id">{{ __('shipping::shipping.branch') }}</label>
                        <select class="form-control" id="branch_id" name="branch_id" required>
                            <option value="">{{ __('shipping::shipping.select_branch') }}</option>
                            @foreach($branches as $branch)
                            <option value="{{ $branch->id }}" {{ old('branch_id', $zone->branch_id) == $branch->id ? 'selected' : '' }}>{{ $branch->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <div class="form-check">
                            <input type="checkbox" class="form-check-input" id="is_active" name="is_active" value="1" {{ old('is_active', $zone->is_active) ? 'checked' : '' }}>
                            <label class="form-check-label" for="is_active">{{ __('shipping::shipping.active') }}</label>
                        </div>
                    </div>
                    <button type="submit" class="btn btn-primary">
                        <i class="las la-save"></i> {{ __('shipping::shipping.save') }}
                    </button>
                    <a href="{{ route('shipping.zones.index') }}" class="btn btn-danger">
                        <i class="las la-times"></i> {{ __('shipping::shipping.cancel') }}
                    </a>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
