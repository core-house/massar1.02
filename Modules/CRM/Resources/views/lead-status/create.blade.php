@extends('admin.dashboard')

@section('sidebar')
    @include('components.sidebar.crm')
@endsection

@section('content')
    @include('components.breadcrumb', [
        'title' => __('crm::crm.lead_statuses'),
        'items' => [
            ['label' => __('crm::crm.dashboard'), 'url' => route('admin.dashboard')],
            ['label' => __('crm::crm.lead_statuses'), 'url' => route('lead-status.index')],
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
                    <form action="{{ route('lead-status.store') }}" method="POST" onsubmit="disableButton()">
                        @csrf
                        <div class="row">

                            <div class="mb-3 col-lg-4">
                                <label class="form-label" for="name">{{ __('crm::crm.title') }}</label>
                                <input type="text" class="form-control" id="name" name="name"
                                    placeholder="{{ __('crm::crm.enter_the_name') }}">
                                @error('name')
                                    <small class="text-danger">{{ $message }}</small>
                                @enderror
                            </div>

                            <div class="mb-3 col-lg-2">
                                <label class="form-label" for="order_column">{{ __('crm::crm.order') }}</label>
                                <input type="text" class="form-control" id="order_column" name="order_column"
                                    placeholder="{{ __('crm::crm.enter_the_order') }}" pattern="\d*" inputmode="numeric"
                                    oninput="this.value = this.value.replace(/[^0-9]/g, '')">
                                @error('order_column')
                                    <small class="text-danger">{{ $message }}</small>
                                @enderror
                            </div>

                            <div class="mb-3 col-lg-4">
                                <label class="form-label" for="color">{{ __('crm::crm.color') }}</label>
                                <input type="color" class="form-control form-control-color" id="color" name="color"
                                    value="#563d7c" title="{{ __('crm::crm.choose_your_color') }}">
                                @error('color')
                                    <small class="text-danger">{{ $message }}</small>
                                @enderror
                            </div>

                            <x-branches::branch-select :branches="$branches" />

                        </div>

                        <div class="d-flex justify-content-start mt-4">
                            <button type="submit" class="btn btn-main me-2" id="submitBtn">
                                <i class="las la-save"></i> {{ __('crm::crm.save') }}
                            </button>

                            <a href="{{ route('lead-status.index') }}" class="btn btn-danger">
                                <i class="las la-times"></i> {{ __('crm::crm.cancel') }}
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
