{{-- resources/views/maintenance/periodic/create.blade.php --}}
@extends('admin.dashboard')

@section('sidebar')
    @include('components.sidebar.service')
@endsection

@section('content')
    @include('components.breadcrumb', [
        'title' => __('Periodic Maintenance'),
        'items' => [
            ['label' => __('Home'), 'url' => route('admin.dashboard')],
            ['label' => __('Periodic Maintenance'), 'url' => route('periodic.maintenances.index')],
            ['label' => __('Create')],
        ],
    ])

    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-header">
                    <h2>{{ __('Add New Periodic Maintenance Schedule') }}</h2>
                </div>
                <div class="card-body">
                    <form action="{{ route('periodic.maintenances.store') }}" method="POST">
                        @csrf
                        <div class="row">
                            {{-- Client Name --}}
                            <div class="col-md-6 mb-3">
                                <label for="client_name" class="form-label">
                                    {{ __('Client Name') }} <span class="text-danger">*</span>
                                </label>
                                <input type="text" name="client_name" id="client_name"
                                    class="form-control @error('client_name') is-invalid @enderror"
                                    value="{{ old('client_name') }}" required>
                                @error('client_name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            {{-- Client Phone --}}
                            <div class="col-md-6 mb-3">
                                <label for="client_phone" class="form-label">
                                    {{ __('Client Phone') }}
                                </label>
                                <input type="text" name="client_phone" id="client_phone"
                                    class="form-control @error('client_phone') is-invalid @enderror"
                                    value="{{ old('client_phone') }}">
                                @error('client_phone')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="row">
                            {{-- Item Name --}}
                            <div class="col-md-6 mb-3">
                                <label for="item_name" class="form-label">
                                    {{ __('Item Name') }} <span class="text-danger">*</span>
                                </label>
                                <input type="text" name="item_name" id="item_name"
                                    class="form-control @error('item_name') is-invalid @enderror"
                                    value="{{ old('item_name') }}" required>
                                @error('item_name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            {{-- Item Number --}}
                            <div class="col-md-6 mb-3">
                                <label for="item_number" class="form-label">
                                    {{ __('Item Number') }} <span class="text-danger">*</span>
                                </label>
                                <input type="text" name="item_number" id="item_number"
                                    class="form-control @error('item_number') is-invalid @enderror"
                                    value="{{ old('item_number') }}" required>
                                @error('item_number')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="row">
                            {{-- Service Type --}}
                            <div class="col-md-6 mb-3">
                                <label for="service_type_id" class="form-label">
                                    {{ __('Service Type') }} <span class="text-danger">*</span>
                                </label>
                                <select name="service_type_id" id="service_type_id"
                                    class="form-control @error('service_type_id') is-invalid @enderror" required>
                                    <option value="">{{ __('Choose Service Type') }}</option>
                                    @foreach ($types as $type)
                                        <option value="{{ $type->id }}"
                                            {{ old('service_type_id') == $type->id ? 'selected' : '' }}>
                                            {{ $type->name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('service_type_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            {{-- Frequency Days --}}
                            <div class="col-md-6 mb-3">
                                <label for="frequency_days" class="form-label">
                                    {{ __('Frequency (Days)') }} <span class="text-danger">*</span>
                                </label>
                                <input type="number" name="frequency_days" id="frequency_days"
                                    class="form-control @error('frequency_days') is-invalid @enderror"
                                    value="{{ old('frequency_days') }}" required min="1">
                                @error('frequency_days')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="row">
                            {{-- Last Maintenance Date --}}
                            <div class="col-md-6 mb-3">
                                <label for="last_maintenance_date" class="form-label">
                                    {{ __('Last Maintenance Date') }}
                                </label>
                                <input type="date" name="last_maintenance_date" id="last_maintenance_date"
                                    class="form-control @error('last_maintenance_date') is-invalid @enderror"
                                    value="{{ old('last_maintenance_date') }}">
                                @error('last_maintenance_date')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            {{-- Next Maintenance Date --}}
                            <div class="col-md-6 mb-3">
                                <label for="next_maintenance_date" class="form-label">
                                    {{ __('Next Maintenance Date') }} <span class="text-danger">*</span>
                                </label>
                                <input type="date" name="next_maintenance_date" id="next_maintenance_date"
                                    class="form-control @error('next_maintenance_date') is-invalid @enderror"
                                    value="{{ old('next_maintenance_date') }}" required>
                                @error('next_maintenance_date')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="row">
                            {{-- Notes --}}
                            <div class="col-md-12 mb-3">
                                <label for="notes" class="form-label">{{ __('Notes') }}</label>
                                <textarea name="notes" id="notes" rows="3" class="form-control @error('notes') is-invalid @enderror">{{ old('notes') }}</textarea>
                                @error('notes')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="row">
                            {{-- Is Active --}}
                            <div class="col-md-6 mb-3">
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" name="is_active" id="is_active"
                                        value="1" {{ old('is_active', 1) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="is_active">
                                        {{ __('Is Active') }}
                                    </label>
                                </div>
                            </div>
                        </div>

                        <div class="mt-3">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save me-2"></i>{{ __('Save') }}
                            </button>
                            <a href="{{ route('periodic.maintenances.index') }}" class="btn btn-secondary">
                                <i class="fas fa-arrow-left me-2"></i>{{ __('Back') }}
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
