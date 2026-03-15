@extends('admin.dashboard')

@section('sidebar')
    @include('components.sidebar.tasks')
@endsection

@section('content')
    @include('components.breadcrumb', [
        'title' => __('crm::crm.tasks_and_activities_types'),
        'items' => [
            ['label' => __('crm::crm.dashboard'), 'url' => route('admin.dashboard')],
            ['label' => __('crm::crm.tasks_and_activities_types'), 'url' => route('tasks.types.index')],
            ['label' => __('crm::crm.create')],
        ],
    ])

    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-header">
                    <h2>{{ __('crm::crm.add_new') }}</h2>
                </div>
                <div class="card-body">
                    <form action="{{ route('tasks.types.store') }}" method="POST" onsubmit="disableButton()">
                        @csrf
                        <div class="mb-3 col-lg-4">
                            <label class="form-label" for="title">{{ __('crm::crm.title') }}</label>
                            <input type="text" class="form-control @error('title') is-invalid @enderror" id="title" name="title"
                                placeholder="{{ __('crm::crm.enter_the_name') }}" value="{{ old('title') }}">
                            @error('title')
                                <small class="text-danger">{{ $message }}</small>
                            @enderror
                        </div>

                        <div class="mt-4">
                            <button type="submit" class="btn btn-main me-2" id="submitBtn"><i class="las la-save"></i>
                                {{ __('crm::crm.save') }}</button>
                            <a href="{{ route('tasks.types.index') }}" class="btn btn-danger"><i class="las la-times"></i>
                                {{ __('crm::crm.cancel') }}</a>
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
