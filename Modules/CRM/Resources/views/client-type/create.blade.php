@extends('admin.dashboard')

@section('sidebar')
    @include('components.sidebar.crm')
@endsection

@section('content')
    @include('components.breadcrumb', [
        'title' => __('crm::crm.client_types'),
        'breadcrumb_items' => [
            ['label' => __('crm::crm.dashboard'), 'url' => route('admin.dashboard')],
            ['label' => __('crm::crm.client_types'), 'url' => route('client-types.index')],
            ['label' => __('crm::crm.add_new')],
        ],
    ])

    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-header">
                    <h2>{{ __('crm::crm.add_new_client_type') }}</h2>
                </div>
                <div class="card-body">
                    <form action="{{ route('client-types.store') }}" method="POST" onsubmit="disableButton()">
                        @csrf
                        <div class="row">
                            <div class="mb-3 col-lg-4">
                                <label class="form-label" for="title">{{ __('crm::crm.name') }}</label>
                                <input type="text" class="form-control" id="title" name="title"
                                    placeholder="{{ __('crm::crm.enter_type_name') }}">
                                @error('title')
                                    <small class="text-danger">{{ $message }}</small>
                                @enderror
                            </div>

                            <x-branches::branch-select :branches="$branches" />
                        </div>

                        <div class="d-flex justify-content-start mt-4">
                            <button type="submit" class="btn btn-main me-2" id="submitBtn">
                                <i class="las la-save"></i> {{ __('crm::crm.save') }}
                            </button>

                            <a href="{{ route('client-types.index') }}" class="btn btn-danger">
                                <i class="las la-times"></i> {{ __('crm::crm.cancel') }}
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
