@extends('admin.dashboard')

@section('sidebar')
    @include('components.sidebar.inquiries')
@endsection

@section('content')
    @include('components.breadcrumb', [
        'title' => __('inquiries::inquiries.add_project_size'),
        'breadcrumb_items' => [
            ['label' => __('inquiries::inquiries.home'), 'url' => route('admin.dashboard')],
            ['label' => __('inquiries::inquiries.project_sizes'), 'url' => route('project-size.index')],
            ['label' => __('inquiries::inquiries.create')],
        ],
    ])

    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-header">
                    <h2>{{ __('inquiries::inquiries.add_new_project_size') }}</h2>
                </div>
                <div class="card-body">
                    <form action="{{ route('project-size.store') }}" method="POST">
                        @csrf
                        <div class="row">
                            <div class="mb-3 col-lg-4">
                                <label for="name" class="form-label">{{ __('inquiries::inquiries.name') }}</label>
                                <input type="text" name="name" id="name" class="form-control"
                                    placeholder="{{ __('inquiries::inquiries.enter_name') }}" value="{{ old('name') }}">
                                @error('name')
                                    <small class="text-danger">{{ $message }}</small>
                                @enderror
                            </div>

                            <div class="mb-3 col-lg-6">
                                <label for="description" class="form-label">{{ __('inquiries::inquiries.description') }}</label>
                                <textarea name="description" id="description" class="form-control" rows="3"
                                    placeholder="{{ __('inquiries::inquiries.write_a_short_description') }}">{{ old('description') }}</textarea>
                            </div>
                        </div>

                        <div class="d-flex justify-content-start mt-4">
                            <button type="submit" class="btn btn-main me-2">
                                <i class="las la-save"></i> {{ __('inquiries::inquiries.save') }}
                            </button>
                            <a href="{{ route('project-size.index') }}" class="btn btn-danger">
                                <i class="las la-times"></i> {{ __('inquiries::inquiries.cancel') }}
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
