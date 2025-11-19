@extends('admin.dashboard')

@section('sidebar')
    @include('components.sidebar.inquiries')
@endsection

@section('content')
    @include('components.breadcrumb', [
        'title' => __('Contacts'),
        'items' => [
            ['label' => __('Dashboard'), 'url' => route('admin.dashboard')],
            ['label' => __('Contacts'), 'url' => route('contacts.index')],
            ['label' => __('Create')],
        ],
    ])

    <div class="card">
        <div class="card-header">
            <h3 class="card-title">{{ __('Create New Contact') }}</h3>
        </div>

        <form action="{{ route('contacts.store') }}" method="POST">
            @csrf

            <div class="card-body">
                <div class="row">
                    <!-- Name -->
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="name">{{ __('Name') }} <span class="text-danger">*</span></label>
                            <input type="text" name="name" id="name"
                                class="form-control @error('name') is-invalid @enderror" value="{{ old('name') }}"
                                required>
                            @error('name')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>

                    <!-- Type -->
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="type">{{ __('Type') }} <span class="text-danger">*</span></label>
                            <select name="type" id="type" class="form-control @error('type') is-invalid @enderror"
                                required>
                                <option value="">{{ __('Select Type') }}</option>
                                <option value="person" {{ old('type') == 'person' ? 'selected' : '' }}>{{ __('Person') }}
                                </option>
                                <option value="company" {{ old('type') == 'company' ? 'selected' : '' }}>
                                    {{ __('Company') }}</option>
                            </select>
                            @error('type')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>

                    <!-- Email -->
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="email">{{ __('Email') }}</label>
                            <input type="email" name="email" id="email"
                                class="form-control @error('email') is-invalid @enderror" value="{{ old('email') }}">
                            @error('email')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>

                    <!-- Phone 1 -->
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="phone_1">{{ __('Phone 1') }}</label>
                            <input type="text" name="phone_1" id="phone_1"
                                class="form-control @error('phone_1') is-invalid @enderror" value="{{ old('phone_1') }}">
                            @error('phone_1')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>

                    <!-- Phone 2 -->
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="phone_2">{{ __('Phone 2') }}</label>
                            <input type="text" name="phone_2" id="phone_2"
                                class="form-control @error('phone_2') is-invalid @enderror" value="{{ old('phone_2') }}">
                            @error('phone_2')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>

                    <!-- Tax Number -->
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="tax_number">{{ __('Tax Number') }}</label>
                            <input type="text" name="tax_number" id="tax_number"
                                class="form-control @error('tax_number') is-invalid @enderror"
                                value="{{ old('tax_number') }}">
                            @error('tax_number')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>

                    <!-- Address 1 -->
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="address_1">{{ __('Address 1') }}</label>
                            <input type="text" name="address_1" id="address_1"
                                class="form-control @error('address_1') is-invalid @enderror"
                                value="{{ old('address_1') }}">
                            @error('address_1')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>

                    <!-- Address 2 -->
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="address_2">{{ __('Address 2') }}</label>
                            <input type="text" name="address_2" id="address_2"
                                class="form-control @error('address_2') is-invalid @enderror"
                                value="{{ old('address_2') }}">
                            @error('address_2')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>

                    <!-- Main Role -->
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="role_id">{{ __('Main Role') }}</label>
                            <select name="role_id" id="role_id"
                                class="form-control @error('role_id') is-invalid @enderror">
                                <option value="">{{ __('Select Role') }}</option>
                                @foreach ($roles as $role)
                                    <option value="{{ $role->id }}"
                                        {{ old('role_id') == $role->id ? 'selected' : '' }}>
                                        {{ $role->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('role_id')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>

                    <!-- Parent Contact -->
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="parent_id">{{ __('Parent Contact') }}</label>
                            <select name="parent_id" id="parent_id"
                                class="form-control @error('parent_id') is-invalid @enderror">
                                <option value="">{{ __('None') }}</option>
                                @foreach ($parents as $parent)
                                    <option value="{{ $parent->id }}"
                                        {{ old('parent_id') == $parent->id ? 'selected' : '' }}>
                                        {{ $parent->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('parent_id')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>

                    <!-- Multiple Roles -->
                    <div class="col-md-12">
                        <div class="form-group">
                            <label>{{ __('Additional Roles') }}</label>
                            <div class="row">
                                @foreach ($roles as $role)
                                    <div class="col-md-4">
                                        <div class="form-check">
                                            <input type="checkbox" name="roles[]" value="{{ $role->id }}"
                                                id="role_{{ $role->id }}"
                                                class="form-check-input @error('roles') is-invalid @enderror"
                                                {{ in_array($role->id, old('roles', [])) ? 'checked' : '' }}>
                                            <label class="form-check-label" for="role_{{ $role->id }}">
                                                {{ $role->name }}
                                            </label>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                            @error('roles')
                                <span class="invalid-feedback d-block">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>


                    <!-- Notes -->
                    <div class="col-md-12">
                        <div class="form-group">
                            <label for="notes">{{ __('Notes') }}</label>
                            <textarea name="notes" id="notes" rows="3" class="form-control @error('notes') is-invalid @enderror">{{ old('notes') }}</textarea>
                            @error('notes')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>
                </div>
            </div>

            <div class="card-footer">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save"></i> {{ __('Save') }}
                </button>
                <a href="{{ route('contacts.index') }}" class="btn btn-secondary">
                    <i class="fas fa-times"></i> {{ __('Cancel') }}
                </a>
            </div>
        </form>
    </div>

    @push('scripts')
        <script>
            $(document).ready(function() {
                $('.select2').select2({
                    theme: 'bootstrap4',
                    placeholder: '{{ __('Select Roles') }}'
                });
            });
        </script>
    @endpush
@endsection
