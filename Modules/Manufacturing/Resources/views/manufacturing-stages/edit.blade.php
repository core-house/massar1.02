@extends('admin.dashboard')
@section('sidebar')
    @include('components.sidebar.manufacturing')
@endsection
@section('content')
    @include('components.breadcrumb', [
        'title' => __('manufacturing::manufacturing.manufacturing stages'),
        'breadcrumb_items' => [
            ['label' => __('manufacturing::manufacturing.home'), 'url' => route('admin.dashboard')],
            ['label' => __('manufacturing::manufacturing.manufacturing stages'), 'url' => route('manufacturing.stages.index')],
            ['label' => __('manufacturing::manufacturing.edit')],
        ],
    ])

    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-header">
                    <h2>{{ __('manufacturing::manufacturing.edit manufacturing stage') }}: {{ $manufacturingStage->name }}</h2>
                </div>

                <div class="card-body">
                    <form action="{{ route('manufacturing.stages.update', $manufacturingStage->id) }}" method="POST"
                        onsubmit="disableButton()">
                        @csrf
                        @method('PUT')

                        <div class="row">
                            <div class="mb-3 col-lg-4">
                                <label class="form-label" for="name">{{ __('manufacturing::manufacturing.stage name') }} <span
                                        class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="name" name="name"
                                    placeholder="{{ __('manufacturing::manufacturing.enter stage name') }}"
                                    value="{{ old('name', $manufacturingStage->name) }}">
                                @error('name')
                                    <small class="text-danger">{{ $message }}</small>
                                @enderror
                            </div>

                            <div class="mb-3 col-lg-8">
                                <label class="form-label" for="description">{{ __('manufacturing::manufacturing.description') }}</label>
                                <textarea class="form-control" id="description" name="description" rows="4"
                                    placeholder="{{ __('manufacturing::manufacturing.write stage description') }}">{{ old('description', $manufacturingStage->description) }}</textarea>
                                @error('description')
                                    <small class="text-danger">{{ $message }}</small>
                                @enderror
                            </div>
                        </div>

                        <div class="d-flex justify-content-start mt-4">
                            <button type="submit" class="btn btn-primary me-2" id="submitBtn">
                                <i class="las la-save"></i> {{ __('manufacturing::manufacturing.save') }}
                            </button>

                            <a href="{{ route('manufacturing.stages.index') }}" class="btn btn-danger">
                                <i class="las la-times"></i> {{ __('manufacturing::manufacturing.cancel') }}
                            </a>
                        </div>

                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
