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
            ['label' => __('maintenance::maintenance.edit')],
        ],
    ])

    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-header">
                    <h2>{{ __('maintenance::maintenance.edit_periodic_schedule') }}</h2>
                </div>
                <div class="card-body">
                    <form action="{{ route('periodic.maintenances.update', $periodicMaintenance->id) }}" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="client_name" class="form-label">
                                    {{ __('maintenance::maintenance.client_name') }} <span class="text-danger">*</span>
                                </label>
                                <input type="text" name="client_name" id="client_name"
                                    class="form-control @error('client_name') is-invalid @enderror"
                                    value="{{ old('client_name', $periodicMaintenance->client_name) }}" required>
                                @error('client_name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="client_phone" class="form-label">
                                    {{ __('maintenance::maintenance.client_phone') }} <span class="text-danger">*</span>
                                </label>
                                <input type="text" name="client_phone" id="client_phone"
                                    class="form-control @error('client_phone') is-invalid @enderror"
                                    value="{{ old('client_phone', $periodicMaintenance->client_phone) }}" required>
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
                                    value="{{ old('item_name', $periodicMaintenance->item_name) }}" required>
                                @error('item_name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="item_number" class="form-label">
                                    {{ __('maintenance::maintenance.item_number') }} <span class="text-danger">*</span>
                                </label>
                                <input type="text" name="item_number" id="item_number"
                                    class="form-control @error('item_number') is-invalid @enderror"
                                    value="{{ old('item_number', $periodicMaintenance->item_number) }}" required>
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
                                            {{ old('service_type_id', $periodicMaintenance->service_type_id) == $type->id ? 'selected' : '' }}>
                                            {{ $type->name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('service_type_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="branch_id" class="form-label">{{ __('maintenance::maintenance.branch') }}</label>
                                <select name="branch_id" id="branch_id"
                                    class="form-control @error('branch_id') is-invalid @enderror">
                                    <option value="">{{ __('maintenance::maintenance.choose_branch') }}</option>
                                    @foreach (userBranches() as $branch)
                                        <option value="{{ $branch->id }}"
                                            {{ old('branch_id', $periodicMaintenance->branch_id) == $branch->id ? 'selected' : '' }}>
                                            {{ $branch->name }}
                                        </option>
                                    @endforeach
                                </select>
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
                                    <option value="daily" {{ old('frequency_type', $periodicMaintenance->frequency_type) == 'daily' ? 'selected' : '' }}>{{ __('maintenance::maintenance.daily') }}</option>
                                    <option value="weekly" {{ old('frequency_type', $periodicMaintenance->frequency_type) == 'weekly' ? 'selected' : '' }}>{{ __('maintenance::maintenance.weekly') }}</option>
                                    <option value="monthly" {{ old('frequency_type', $periodicMaintenance->frequency_type) == 'monthly' ? 'selected' : '' }}>{{ __('maintenance::maintenance.monthly') }}</option>
                                    <option value="quarterly" {{ old('frequency_type', $periodicMaintenance->frequency_type) == 'quarterly' ? 'selected' : '' }}>{{ __('maintenance::maintenance.quarterly') }}</option>
                                    <option value="semi_annual" {{ old('frequency_type', $periodicMaintenance->frequency_type) == 'semi_annual' ? 'selected' : '' }}>{{ __('maintenance::maintenance.semi_annual') }}</option>
                                    <option value="annual" {{ old('frequency_type', $periodicMaintenance->frequency_type) == 'annual' ? 'selected' : '' }}>{{ __('maintenance::maintenance.annual') }}</option>
                                    <option value="custom_days" {{ old('frequency_type', $periodicMaintenance->frequency_type) == 'custom_days' ? 'selected' : '' }}>{{ __('maintenance::maintenance.custom_days') }}</option>
                                </select>
                                @error('frequency_type')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="frequency_value" class="form-label">{{ __('maintenance::maintenance.frequency_value_days') }}</label>
                                <input type="number" name="frequency_value" id="frequency_value"
                                    class="form-control @error('frequency_value') is-invalid @enderror"
                                    value="{{ old('frequency_value', $periodicMaintenance->frequency_value) }}" min="1">
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
                                    value="{{ old('start_date', $periodicMaintenance->start_date?->format('Y-m-d')) }}" required>
                                @error('start_date')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="notification_days_before" class="form-label">
                                    {{ __('maintenance::maintenance.notification_days_before') }} <span class="text-danger">*</span>
                                </label>
                                <input type="number" name="notification_days_before" id="notification_days_before"
                                    class="form-control @error('notification_days_before') is-invalid @enderror"
                                    value="{{ old('notification_days_before', $periodicMaintenance->notification_days_before) }}"
                                    min="1" max="365" required>
                                @error('notification_days_before')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-12 mb-3">
                                <label for="notes" class="form-label">{{ __('maintenance::maintenance.notes') }}</label>
                                <textarea name="notes" id="notes" rows="3"
                                    class="form-control @error('notes') is-invalid @enderror">{{ old('notes', $periodicMaintenance->notes) }}</textarea>
                                @error('notes')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" name="is_active" id="is_active"
                                        value="1" {{ old('is_active', $periodicMaintenance->is_active) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="is_active">
                                        {{ __('maintenance::maintenance.is_active') }}
                                    </label>
                                </div>
                            </div>
                        </div>

                        <div class="mt-3">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save me-2"></i>{{ __('maintenance::maintenance.update') }}
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
