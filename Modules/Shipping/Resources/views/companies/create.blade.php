@extends('admin.dashboard')

@section('sidebar')
    @include('components.sidebar.shipping')
@endsection

@section('content')
    @include('components.breadcrumb', [
        'title' => __('shipping::shipping.shipping_companies'),
        'breadcrumb_items' => [
            ['label' => __('shipping::shipping.dashboard'), 'url' => route('admin.dashboard')],
            ['label' => __('shipping::shipping.shipping_companies'), 'url' => route('companies.index')],
            ['label' => __('shipping::shipping.add_new')],
        ],
    ])

    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-header">
                    <h2>{{ __('shipping::shipping.add_new_company') }}</h2>
                </div>
                <div class="card-body">
                    @can('create Shipping Companies')
                        <form action="{{ route('companies.store') }}" method="POST">
                            @csrf
                            <div class="row">
                                <div class="mb-3 col-lg-4">
                                    <label class="form-label" for="name">{{ __('shipping::shipping.name') }}</label>
                                    <input type="text" class="form-control" id="name" name="name"
                                        placeholder="{{ __('shipping::shipping.enter_company_name') }}" value="{{ old('name') }}">
                                    @error('name')
                                        <small class="text-danger">{{ $message }}</small>
                                    @enderror
                                </div>

                                <div class="mb-3 col-lg-4">
                                    <label class="form-label" for="email">{{ __('shipping::shipping.email') }}</label>
                                    <input type="email" class="form-control" id="email" name="email"
                                        placeholder="{{ __('shipping::shipping.example_email') }}" value="{{ old('email') }}">
                                    @error('email')
                                        <small class="text-danger">{{ $message }}</small>
                                    @enderror
                                </div>

                                <div class="mb-3 col-lg-4">
                                    <label class="form-label" for="phone">{{ __('shipping::shipping.phone') }}</label>
                                    <input type="text" class="form-control" id="phone" name="phone"
                                        placeholder="{{ __('shipping::shipping.enter_phone_number') }}" value="{{ old('phone') }}">
                                    @error('phone')
                                        <small class="text-danger">{{ $message }}</small>
                                    @enderror
                                </div>

                                <div class="mb-3 col-lg-4">
                                    <label class="form-label" for="address">{{ __('shipping::shipping.address') }}</label>
                                    <textarea class="form-control" id="address" name="address" placeholder="{{ __('shipping::shipping.address') }}">{{ old('address') }}</textarea>
                                    @error('address')
                                        <small class="text-danger">{{ $message }}</small>
                                    @enderror
                                </div>

                                <div class="mb-3 col-lg-4">
                                    <label class="form-label" for="base_rate">{{ __('shipping::shipping.base_rate') }}</label>
                                    <input type="number" class="form-control" id="base_rate" name="base_rate" step="0.01"
                                        placeholder="{{ __('shipping::shipping.enter_base_rate') }}" value="{{ old('base_rate') }}">
                                    @error('base_rate')
                                        <small class="text-danger">{{ $message }}</small>
                                    @enderror
                                </div>

                                <div class="mb-3 col-lg-4">
                                    <label class="form-label" for="is_active">{{ __('shipping::shipping.status') }}</label>
                                    <select class="form-control" id="is_active" name="is_active">
                                        <option value="1" {{ old('is_active', 1) == 1 ? 'selected' : '' }}>
                                            {{ __('shipping::shipping.active') }}
                                        </option>
                                        <option value="0" {{ old('is_active') == 0 ? 'selected' : '' }}>
                                            {{ __('shipping::shipping.inactive') }}
                                        </option>
                                    </select>
                                    @error('is_active')
                                        <small class="text-danger">{{ $message }}</small>
                                    @enderror
                                </div>

                                <x-branches::branch-select :branches="$branches" />

                            </div>

                            <div class="d-flex justify-content-start mt-4">
                                <button type="submit" class="btn btn-main me-2">
                                    <i class="las la-save"></i> {{ __('shipping::shipping.save') }}
                                </button>
                                <a href="{{ route('companies.index') }}" class="btn btn-danger">
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
