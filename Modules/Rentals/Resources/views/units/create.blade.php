@extends('admin.dashboard')

@section('sidebar')
    @include('components.sidebar.rentals')
@endsection

@section('content')
    @include('components.breadcrumb', [
        'title' => __('rentals::rentals.add_unit'),
        'breadcrumb_items' => array_merge(
            [
                ['label' => __('navigation.home'), 'url' => route('admin.dashboard')],
                ['label' => __('rentals::rentals.buildings_and_units'), 'url' => route('rentals.buildings.index')],
            ],
            $building 
                ? [['label' => $building->name, 'url' => route('rentals.buildings.show', $building->id)]] 
                : [],
            [
                ['label' => __('rentals::rentals.add_unit')],
            ]
        ),
    ])

    <div class="container-fluid px-4">
        @can('create Unit')
            <div class="card shadow-sm">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="fas fa-plus-circle me-2"></i>
                        @if($building)
                            {{ __('rentals::rentals.add_new_unit_in_building') }} <strong class="text-primary">{{ $building->name }}</strong>
                        @else
                            {{ __('rentals::rentals.add_rental_item_unit') }}
                        @endif
                    </h5>
                </div>
                <div class="card-body" x-data="{ unitType: '{{ old('unit_type', $building ? 'building' : 'building') }}' }">
                    <form action="{{ route('rentals.units.store') }}" method="POST">
                        @csrf
                        
                        <div class="row mb-4">
                            <div class="col-md-12">
                                <label class="form-label fw-bold">{{ __('rentals::rentals.unit_type') }}</label>
                                <div class="d-flex gap-4">
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="unit_type" id="typeBuilding" value="building" x-model="unitType" {{ old('unit_type', $building ? 'building' : 'building') === 'building' ? 'checked' : '' }}>
                                        <label class="form-check-label" for="typeBuilding">{{ __('rentals::rentals.residential_unit') }}</label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="unit_type" id="typeItem" value="item" x-model="unitType" {{ old('unit_type') === 'item' ? 'checked' : '' }}>
                                        <label class="form-check-label" for="typeItem">{{ __('rentals::rentals.inventory_item') }}</label>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row">

                            {{-- Building Selection --}}
                            <div class="col-md-3 mb-3" x-show="unitType === 'building'">
                                <label for="building_id" class="form-label">{{ __('rentals::rentals.building') }} <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="fas fa-building"></i></span>
                                    @if($building)
                                        <input type="text" class="form-control" value="{{ $building->name }}" readonly disabled>
                                        <input type="hidden" name="building_id" value="{{ $building->id }}">
                                    @else
                                        <select name="building_id" id="building_id" class="form-select @error('building_id') is-invalid @enderror" :required="unitType === 'building'">
                                            <option value="">{{ __('rentals::rentals.select_building') }}</option>
                                            @foreach(\Modules\Rentals\Models\RentalsBuilding::all() as $b)
                                                <option value="{{ $b->id }}" {{ old('building_id') == $b->id ? 'selected' : '' }}>{{ $b->name }}</option>
                                            @endforeach
                                        </select>
                                    @endif
                                </div>
                                @error('building_id')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>

                            {{-- Item Selection --}}
                            <div class="col-md-3 mb-3" x-show="unitType === 'item'" x-cloak>
                                <label for="item_id" class="form-label">{{ __('rentals::rentals.inventory_item_label') }} <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="fas fa-tshirt"></i></span>
                                    <select name="item_id" id="item_id" class="form-select @error('item_id') is-invalid @enderror" :required="unitType === 'item'">
                                        <option value="">{{ __('rentals::rentals.select_item') }}</option>
                                        @foreach($items as $item)
                                            <option value="{{ $item->id }}" {{ old('item_id') == $item->id ? 'selected' : '' }}>{{ $item->name }} ({{ $item->code }})</option>
                                        @endforeach
                                    </select>
                                </div>
                                @error('item_id')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>

                            {{-- Unit Name --}}
                            <div class="col-md-3 mb-3">
                                <label for="name" class="form-label">{{ __('rentals::rentals.unit_name_number') }} <span
                                        class="text-danger">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="fas fa-tag"></i></span>
                                    <input type="text" name="name" id="name"
                                        class="form-control @error('name') is-invalid @enderror" value="{{ old('name') }}" required>
                                </div>
                                @error('name')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>

                            {{-- Area --}}
                            <div class="col-md-2 mb-3" x-show="unitType === 'building'">
                                <label for="area" class="form-label">{{ __('rentals::rentals.area_m2') }}</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="fas fa-ruler-combined"></i></span>
                                    <input type="number" step="0.01" name="area" id="area"
                                        class="form-control @error('area') is-invalid @enderror" value="{{ old('area') }}">
                                </div>
                                @error('area')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>

                            {{-- Floor --}}
                            <div class="col-md-2 mb-3" x-show="unitType === 'building'">
                                <label for="floor" class="form-label">{{ __('rentals::rentals.floor') }} <span
                                        class="text-danger" x-show="unitType === 'building'">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="fas fa-layer-group"></i></span>
                                    <select name="floor" id="floor"
                                        class="form-select @error('floor') is-invalid @enderror" :required="unitType === 'building'">
                                        <option value="">{{ __('rentals::rentals.select_floor') }}</option>
                                        @foreach ($floors as $floor)
                                            <option value="{{ $floor }}"
                                                {{ old('floor') == $floor ? 'selected' : '' }}>
                                                {{ $floor }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                @error('floor')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>

                            {{-- Status --}}
                            <div class="col-md-2 mb-3">
                                <label for="status" class="form-label">{{ __('rentals::rentals.status') }} <span
                                        class="text-danger">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="fas fa-check-circle"></i></span>
                                    <select name="status" id="status"
                                        class="form-select @error('status') is-invalid @enderror" required>
                                        @foreach (\Modules\Rentals\Enums\UnitStatus::cases() as $status)
                                            <option value="{{ $status->value }}"
                                                {{ old('status', \Modules\Rentals\Enums\UnitStatus::AVAILABLE->value) == $status->value ? 'selected' : '' }}>
                                                {{ $status->label() }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                @error('status')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>

                            {{-- Details --}}
                            <div class="col-12 mb-3">
                                <label for="details" class="form-label">{{ __('rentals::rentals.additional_details_specs') }}</label>
                                <textarea name="details" id="details" class="form-control @error('details') is-invalid @enderror" rows="4">{{ old('details') }}</textarea>
                                @error('details')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>

                        </div>

                        <div class="card-footer text-end bg-transparent border-top pt-3 pe-0">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save me-2"></i>{{ __('rentals::rentals.save_unit') }}
                            </button>
                            <a href="{{ url()->previous() }}" class="btn btn-secondary">
                                <i class="fas fa-times me-2"></i>{{ __('rentals::rentals.cancel') }}
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        @endcan
    </div>
@endsection
