@extends('admin.dashboard')

@section('sidebar')
    @include('components.sidebar.crm')
@endsection

@section('content')
    @include('components.breadcrumb', [
        'title' => __('crm::crm.client_contacts'),
        'items' => [
            ['label' => __('crm::crm.dashboard'), 'url' => route('admin.dashboard')],
            ['label' => __('crm::crm.client_contacts'), 'url' => route('client-contacts.index')],
            ['label' => __('crm::crm.create')],
        ],
    ])

    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-header">
                    <h2>{{ __('crm::crm.add_new_contact') }}</h2>
                </div>
                <div class="card-body">
                    <form action="{{ route('client-contacts.store') }}" method="POST">
                        @csrf
                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <x-dynamic-search name="client_id" :label="__('crm::crm.company')" column="cname" model="App\Models\Client"
                                    :placeholder="__('crm::crm.search_for_company')" :required="false" :class="'form-select'" />
                            </div>

                            <div class="mb-3 col-lg-4">
                                <label class="form-label" for="name">{{ __('crm::crm.name') }}</label>
                                <input type="text" class="form-control" id="name" name="name"
                                    value="{{ old('name') }}">
                                @error('name')
                                    <small class="text-danger">{{ $message }}</small>
                                @enderror
                            </div>

                            <div class="mb-3 col-lg-4">
                                <label class="form-label" for="email">{{ __('crm::crm.email') }}</label>
                                <input type="email" class="form-control" id="email" name="email"
                                    value="{{ old('email') }}">
                                @error('email')
                                    <small class="text-danger">{{ $message }}</small>
                                @enderror
                            </div>

                            <div class="mb-3 col-lg-4">
                                <label class="form-label" for="phone">{{ __('crm::crm.phone') }}</label>
                                <input type="text" class="form-control" id="phone" name="phone"
                                    value="{{ old('phone') }}">
                                @error('phone')
                                    <small class="text-danger">{{ $message }}</small>
                                @enderror
                            </div>

                            <div class="mb-3 col-lg-4">
                                <label class="form-label" for="position">{{ __('crm::crm.position') }}</label>
                                <input type="text" class="form-control" id="position" name="position"
                                    value="{{ old('position') }}">
                                @error('position')
                                    <small class="text-danger">{{ $message }}</small>
                                @enderror
                            </div>

                            <div class="mb-3 col-lg-4">
                                <label class="form-label" for="preferred_contact_method">{{ __('crm::crm.preferred_contact_method') }}</label>
                                <select class="form-select" id="preferred_contact_method" name="preferred_contact_method">
                                    <option value="phone" {{ old('preferred_contact_method') == 'phone' ? 'selected' : '' }}>{{ __('crm::crm.phone') }}</option>
                                    <option value="whatsapp" {{ old('preferred_contact_method') == 'whatsapp' ? 'selected' : '' }}>{{ __('crm::crm.whatsapp') }}</option>
                                    <option value="email" {{ old('preferred_contact_method') == 'email' ? 'selected' : '' }}>{{ __('crm::crm.email') }}</option>
                                </select>
                                @error('preferred_contact_method')
                                    <small class="text-danger">{{ $message }}</small>
                                @enderror
                            </div>
                        </div>

                        <div class="d-flex justify-content-start mt-4">
                            <button type="submit" class="btn btn-main me-2" id="submitBtn">
                                <i class="las la-save"></i> {{ __('crm::crm.save') }}
                            </button>

                            <a href="{{ route('client-contacts.index') }}" class="btn btn-danger">
                                <i class="las la-times"></i> {{ __('crm::crm.cancel') }}
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
