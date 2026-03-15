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
            ['label' => __('crm::crm.edit')],
        ],
    ])

    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-header">
                    <h2>{{ __('crm::crm.edit_contact') }}</h2>
                </div>
                <div class="card-body">
                    <form action="{{ route('client-contacts.update', $contact->id) }}" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="row">
                            <div class="mb-3 col-lg-4">
                                <x-dynamic-search name="client_id" :label="__('crm::crm.company')" column="cname" model="App\Models\Client"
                                    :placeholder="__('crm::crm.search_for_company')" :required="true" :selected="$contact->client_id" />
                            </div>

                            <div class="mb-3 col-lg-4">
                                <label class="form-label" for="name">{{ __('crm::crm.name') }}</label>
                                <input type="text" class="form-control" id="name" name="name"
                                    value="{{ old('name', $contact->name) }}" required>
                                @error('name')
                                    <small class="text-danger">{{ $message }}</small>
                                @enderror
                            </div>

                            <div class="mb-3 col-lg-4">
                                <label class="form-label" for="email">{{ __('crm::crm.email') }}</label>
                                <input type="email" class="form-control" id="email" name="email"
                                    value="{{ old('email', $contact->email) }}" required>
                                @error('email')
                                    <small class="text-danger">{{ $message }}</small>
                                @enderror
                            </div>

                            <div class="mb-3 col-lg-4">
                                <label class="form-label" for="phone">{{ __('crm::crm.phone') }}</label>
                                <input type="text" class="form-control" id="phone" name="phone"
                                    value="{{ old('phone', $contact->phone) }}" required>
                                @error('phone')
                                    <small class="text-danger">{{ $message }}</small>
                                @enderror
                            </div>

                            <div class="mb-3 col-lg-4">
                                <label class="form-label" for="position">{{ __('crm::crm.position') }}</label>
                                <input type="text" class="form-control" id="position" name="position"
                                    value="{{ old('position', $contact->position) }}">
                                @error('position')
                                    <small class="text-danger">{{ $message }}</small>
                                @enderror
                            </div>

                            <div class="mb-3 col-lg-4">
                                <label class="form-label" for="preferred_contact_method">{{ __('crm::crm.preferred_contact_method') }}</label>
                                <select class="form-select" id="preferred_contact_method" name="preferred_contact_method">
                                    <option value="phone" {{ old('preferred_contact_method', $contact->preferred_contact_method) == 'phone' ? 'selected' : '' }}>{{ __('crm::crm.phone') }}</option>
                                    <option value="whatsapp" {{ old('preferred_contact_method', $contact->preferred_contact_method) == 'whatsapp' ? 'selected' : '' }}>{{ __('crm::crm.whatsapp') }}</option>
                                    <option value="email" {{ old('preferred_contact_method', $contact->preferred_contact_method) == 'email' ? 'selected' : '' }}>{{ __('crm::crm.email') }}</option>
                                </select>
                                @error('preferred_contact_method')
                                    <small class="text-danger">{{ $message }}</small>
                                @enderror
                            </div>
                        </div>

                        <div class="d-flex justify-content-start mt-4">
                            <button type="submit" class="btn btn-main me-2">
                                <i class="las la-save"></i> {{ __('crm::crm.save_changes') }}
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
