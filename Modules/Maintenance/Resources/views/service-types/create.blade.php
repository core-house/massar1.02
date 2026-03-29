@extends('admin.dashboard')

@section('sidebar')
    @include('components.sidebar.service')
@endsection

@section('content')
    @include('components.breadcrumb', [
        'title' => __('maintenance::maintenance.service_types'),
        'breadcrumb_items' => [
            ['label' => __('navigation.home'), 'url' => route('admin.dashboard')],
            ['label' => __('maintenance::maintenance.service_types'), 'url' => route('service.types.index')],
            ['label' => __('maintenance::maintenance.create')],
        ],
    ])

    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-header">
                    <h2>{{ __('maintenance::maintenance.add_new_service_type') }}</h2>
                </div>
                <div class="card-body">
                    <form action="{{ route('service.types.store') }}" method="POST">
                        @csrf
                        <div class="row">

                            <div class="mb-3 col-lg-3">
                                <label class="form-label" for="name">
                                    {{ __('maintenance::maintenance.name') }} <span class="text-danger">*</span>
                                </label>
                                <input type="text" class="form-control @error('name') is-invalid @enderror"
                                    id="name" name="name" placeholder="{{ __('maintenance::maintenance.enter_name') }}"
                                    value="{{ old('name') }}" required>
                                @error('name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <x-branches::branch-select :branches="$branches" />

                            <div class="mb-3 col-lg-6">
                                <label class="form-label" for="description">{{ __('maintenance::maintenance.description') }}</label>
                                <textarea class="form-control @error('description') is-invalid @enderror" id="description" name="description"
                                    rows="3" placeholder="{{ __('maintenance::maintenance.enter_description') }}">{{ old('description') }}</textarea>
                                @error('description')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="d-flex justify-content-start mt-4">
                            <button type="submit" class="btn btn-primary me-2">
                                <i class="las la-save"></i> {{ __('maintenance::maintenance.save') }}
                            </button>
                            <a href="{{ route('service.types.index') }}" class="btn btn-danger">
                                <i class="las la-times"></i> {{ __('maintenance::maintenance.cancel') }}
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
