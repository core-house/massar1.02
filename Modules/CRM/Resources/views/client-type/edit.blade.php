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
            ['label' => __('crm::crm.edit')],
        ],
    ])

    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-header">
                    <h2>{{ __('crm::crm.edit_client_type') }}</h2>
                </div>
                <div class="card-body">
                    <form action="{{ route('client-types.update', $client_type->id) }}" method="POST">
                        @csrf
                        @method('PUT')
                        <div class="row">
                            <div class="mb-3 col-lg-4">
                                <label class="form-label" for="title">{{ __('crm::crm.name') }}</label>
                                <input type="text" class="form-control" id="title" name="title"
                                    placeholder="{{ __('crm::crm.enter_the_name') }}"
                                    value="{{ old('title', $client_type->title) }}">
                                @error('title')
                                    <small class="text-danger">{{ $message }}</small>
                                @enderror
                            </div>

                            <x-branches::branch-select :branches="userBranches()" :selected="$client_type->branch_id" />
                        </div>

                        <div class="d-flex justify-content-start mt-4">
                            <button type="submit" class="btn btn-main me-2">
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
