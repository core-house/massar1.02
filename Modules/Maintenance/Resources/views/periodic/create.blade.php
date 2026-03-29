@extends('admin.dashboard')

@section('sidebar')
    @include('components.sidebar.service')
@endsection

@section('content')
    @include('components.breadcrumb', [
        'title' => __('maintenance::maintenance.periodic_maintenance'),
        'breadcrumb_items' => [
            ['label' => __('navigation.home'), 'url' => route('admin.dashboard')],
            ['label' => __('maintenance::maintenance.periodic_maintenance'), 'url' => route('periodic.maintenances.index')],
            ['label' => __('maintenance::maintenance.create')],
        ],
    ])

    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-header">
                    <h2>{{ __('maintenance::maintenance.add_new_periodic_schedule') }}</h2>
                </div>
                <div class="card-body">
                    <form action="{{ route('periodic.maintenances.store') }}" method="POST">
                        @csrf

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="client_name" class="form-label">
                                    {{ __('maintenance::maintenance.client_name') }} <span class="text-danger">*</span>
                                </label>
                                <input type="text" name="client_name" id="client_name"
                                    class="form-control @error('client_name') is-invalid @enderror"
                                    value="{{ old('client_name') }}" required>
                                @error('client_name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="client_phone" class="form-label">
                                    {{ __('maintenance::maintenance.client_phone') }} <span class="text-danger">*</span>
                                </label>
                                <input type="text" name="client_phone" id="client_phone"
                                    class="form-control @error('client_phone') is-invalid @enderror"
                                    value="{{ old('client_phone') }}" required>
                                @error('client_phone')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="item_name" class="form-label">
                                    {{ __('maintenance::maintenance.item_name') }} <span class="text-danger">*</span>
                                </label>
                                <input type="text" name="item_name" id="item_name"
                                    class="form-control @error('item_name') is-invalid @enderror"
                                    value="{{ old('item_name') }}" required>
                                @error('item_name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="item_number" class="form-label">
                                    {{ __('maintenance::maintenance.item_number') }} <span class="text-danger">*</span>
                                </label>
                                <input type="text" name="item_number" id="item_number"
                                    class="form-control @error('item_number') is-invalid @enderror"
                                    value="{{ old('item_number') }}" required>
                                @error('item_number')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="service_type_id" class="form-label">
                                    {{ __('maintenance::maintenance.service_type') }} <span class="text-danger">*</span>
                                </label>
                                <select name="service_type_id" id="service_type_id"
                                    class="form-control @error('service_type_id') is-invalid @enderror" required>
                                    <option value="">{{ __('maintenance::maintenance.choose_service_type') }}</option>
                                    @foreach ($types as $type)
                                        <option value="{{ $type->id }}"
                                            {{ old('service_type_id') == $type->id ? 'selected' : '' }}>
                                            {{ $type->name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('service_type_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <x-branches::branch-select :branches="$branches" />
                                @error('branch_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="frequency_type" class="form-label">
                                    {{ __('maintenance::maintenance.frequency_type') }} <span class="text-danger">*</span>
                                </label>
                                <select name="frequency_type" id="frequency_type"
                                    class="form-control @error('frequency_type') is-invalid @enderror" required>
                                    <option value="">{{ __('maintenance::maintenance.choose_frequency_type') }}</option>
                                    <option value="daily" {{ old('frequency_type') == 'daily' ? 'selected' : '' }}>{{ __('maintenance::maintenance.daily') }}</option>
                                    <option value="weekly" {{ old('frequency_type') == 'weekly' ? 'selected' : '' }}>{{ __('maintenance::maintenance.weekly') }}</option>
                                    <option value="monthly" {{ old('frequency_type') == 'monthly' ? 'selected' : '' }}>{{ __('maintenance::maintenance.monthly') }}</option>
                                    <option value="quarterly" {{ old('frequency_type') == 'quarterly' ? 'selected' : '' }}>{{ __('maintenance::maintenance.quarterly') }}</option>
                                    <option value="semi_annual" {{ old('frequency_type') == 'semi_annual' ? 'selected' : '' }}>{{ __('maintenance::maintenance.semi_annual') }}</option>
                                    <option value="annual" {{ old('frequency_type') == 'annual' ? 'selected' : '' }}>{{ __('maintenance::maintenance.annual') }}</option>
                                    <option value="custom_days" {{ old('frequency_type') == 'custom_days' ? 'selected' : '' }}>{{ __('maintenance::maintenance.custom_days') }}</option>
                                </select>
                                @error('frequency_type')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="frequency_value" class="form-label">{{ __('maintenance::maintenance.frequency_value_days') }}</label>
                                <input type="number" name="frequency_value" id="frequency_value"
                                    class="form-control @error('frequency_value') is-invalid @enderror"
                                    value="{{ old('frequency_value') }}" min="1">
                                @error('frequency_value')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="start_date" class="form-label">
                                    {{ __('maintenance::maintenance.start_date') }} <span class="text-danger">*</span>
                                </label>
                                <input type="date" name="start_date" id="start_date"
                                    class="form-control @error('start_date') is-invalid @enderror"
                                    value="{{ old('start_date', now()->format('Y-m-d')) }}" required>
                                @error('start_date')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="next_maintenance_date" class="form-label">
                                    {{ __('maintenance::maintenance.next_maintenance_date') }} <span class="text-danger">*</span>
                                </label>
                                <input type="date" name="next_maintenance_date" id="next_maintenance_date"
                                    class="form-control @error('next_maintenance_date') is-invalid @enderror"
                                    value="{{ old('next_maintenance_date') }}" required>
                                @error('next_maintenance_date')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="last_maintenance_date" class="form-label">{{ __('maintenance::maintenance.last_maintenance_date') }}</label>
                                <input type="date" name="last_maintenance_date" id="last_maintenance_date"
                                    class="form-control @error('last_maintenance_date') is-invalid @enderror"
                                    value="{{ old('last_maintenance_date') }}">
                                @error('last_maintenance_date')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="notification_days_before" class="form-label">
                                    {{ __('maintenance::maintenance.notification_days_before') }} <span class="text-danger">*</span>
                                </label>
                                <input type="number" name="notification_days_before" id="notification_days_before"
                                    class="form-control @error('notification_days_before') is-invalid @enderror"
                                    value="{{ old('notification_days_before', 7) }}" min="1" max="365" required>
                                @error('notification_days_before')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-12 mb-3">
                                <label for="notes" class="form-label">{{ __('maintenance::maintenance.notes') }}</label>
                                <textarea name="notes" id="notes" rows="3"
                                    class="form-control @error('notes') is-invalid @enderror">{{ old('notes') }}</textarea>
                                @error('notes')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" name="is_active" id="is_active"
                                        value="1" {{ old('is_active', 1) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="is_active">
                                        {{ __('maintenance::maintenance.is_active') }}
                                    </label>
                                </div>
                                @error('is_active')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                            </div>
                        </div>

                        <div class="mt-3">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save me-2"></i>{{ __('maintenance::maintenance.save') }}
                            </button>
                            <a href="{{ route('periodic.maintenances.index') }}" class="btn btn-secondary">
                                <i class="fas fa-arrow-left me-2"></i>{{ __('maintenance::maintenance.back') }}
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
