@extends('admin.dashboard')

@section('sidebar')
    @include('components.sidebar.tasks')
@endsection

@section('content')
    @include('components.breadcrumb', [
        'title' => __('crm::crm.add_new_category'),
        'items' => [
            ['label' => __('crm::crm.dashboard'), 'url' => route('admin.dashboard')],
            ['label' => __('crm::crm.task_types'), 'url' => route('tasks.type-categories.index')],
            ['label' => __('crm::crm.add_new_category')],
        ],
    ])

    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-header">
                    <h2>{{ __('crm::crm.add_new_category') }}</h2>
                </div>
                <div class="card-body">
                    <form action="{{ route('tasks.type-categories.store') }}" method="POST" onsubmit="disableButton()">
                        @csrf

                        <div class="row">
                            <div class="mb-3 col-lg-6">
                                <label class="form-label" for="name">{{ __('crm::crm.category_name') }}</label>
                                <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name"
                                    placeholder="{{ __('crm::crm.enter_the_name') }}" value="{{ old('name') }}">
                                @error('name')
                                    <small class="text-danger">{{ $message }}</small>
                                @enderror
                            </div>

                            <div class="mb-3 col-lg-6">
                                <label class="form-label" for="description">{{ __('crm::crm.description') }}</label>
                                <textarea class="form-control @error('description') is-invalid @enderror" id="description" name="description"
                                    rows="3" placeholder="{{ __('crm::crm.enter_category_description') }}">{{ old('description') }}</textarea>
                                @error('description')
                                    <small class="text-danger">{{ $message }}</small>
                                @enderror
                            </div>
                        </div>

                        <div class="d-flex justify-content-start mt-4">
                            <button type="submit" class="btn btn-main me-2" id="submitBtn">
                                <i class="las la-save"></i> {{ __('crm::crm.save') }}
                            </button>
                            <a href="{{ route('tasks.type-categories.index') }}" class="btn btn-danger">
                                <i class="las la-times"></i> {{ __('crm::crm.cancel') }}
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        function disableButton() {
            const submitBtn = document.getElementById('submitBtn');
            if (submitBtn) {
                submitBtn.disabled = true;
            }
        }
    </script>
@endpush
