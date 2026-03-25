@extends('admin.dashboard')

@section('sidebar')
    @include('components.sidebar.crm')
@endsection

@section('content')
    @include('components.breadcrumb', [
        'title' => __('crm::crm.activities'),
        'breadcrumb_items' => [
            ['label' => __('crm::crm.dashboard'), 'url' => route('admin.dashboard')],
            ['label' => __('crm::crm.activities'), 'url' => route('activities.index')],
            ['label' => __('crm::crm.create')],
        ],
    ])

    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-header">
                    <h2>{{ __('crm::crm.add_new_activity') }}</h2>
                </div>
                <div class="card-body">
                    <form action="{{ route('activities.store') }}" method="POST" onsubmit="disableButton()">
                        @csrf
                        <div class="row">

                            {{-- Activity Title --}}
                            <div class="mb-3 col-lg-3">
                                <label class="form-label" for="title">{{ __('crm::crm.title') }}</label>
                                <input type="text" class="form-control" id="title" name="title"
                                    placeholder="{{ __('crm::crm.enter_the_name') }}" value="{{ old('title') }}">
                                @error('title')
                                    <small class="text-danger">{{ $message }}</small>
                                @enderror
                            </div>

                            {{-- Activity Type --}}
                            <div class="mb-3 col-lg-3">
                                <label class="form-label" for="type">{{ __('crm::crm.type') }}</label>
                                <select class="form-control" id="type" name="type">
                                    <option value="0" {{ old('type') == 0 ? 'selected' : '' }}>{{ __('crm::crm.call') }}
                                    </option>
                                    <option value="1" {{ old('type') == 1 ? 'selected' : '' }}>{{ __('crm::crm.message') }}
                                    </option>
                                    <option value="2" {{ old('type') == 2 ? 'selected' : '' }}>{{ __('crm::crm.meeting') }}
                                    </option>
                                </select>
                                @error('type')
                                    <small class="text-danger">{{ $message }}</small>
                                @enderror
                            </div>

                            {{-- Activity Date --}}
                            <div class="mb-3 col-lg-3">
                                <label class="form-label" for="activity_date">{{ __('crm::crm.date') }}</label>
                                <input type="date" class="form-control" id="activity_date" name="activity_date"
                                    value="{{ old('activity_date') }}">
                                @error('activity_date')
                                    <small class="text-danger">{{ $message }}</small>
                                @enderror
                            </div>

                            {{-- Scheduled Time --}}
                            <div class="mb-3 col-lg-3">
                                <label class="form-label" for="scheduled_at">{{ __('crm::crm.time') }}</label>
                                <input type="time" class="form-control" id="scheduled_at" name="scheduled_at"
                                    value="{{ old('scheduled_at') }}">
                                @error('scheduled_at')
                                    <small class="text-danger">{{ $message }}</small>
                                @enderror
                            </div>

                            {{-- Client --}}
                            <div class="col-md-3 mb-3">
                                <x-dynamic-search name="client_id" :label="__('crm::crm.client')" column="cname"
                                    model="App\Models\Client" :placeholder="__('crm::crm.search_for_client')" :required="false" :class="'form-select'" :selected="request('client_id')" />
                            </div>

                            {{-- Assigned To --}}
                            <div class="mb-3 col-lg-3">
                                <label class="form-label" for="assigned_to">{{ __('crm::crm.assigned_to') }}</label>
                                <select name="assigned_to" class="form-control">
                                    <option value="">{{ __('crm::crm.select_employee') }}</option>
                                    @foreach ($users as $id => $name)
                                        <option value="{{ $id }}"
                                            {{ old('assigned_to') == $id ? 'selected' : '' }}>
                                            {{ $name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('assigned_to')
                                    <small class="text-danger">{{ $message }}</small>
                                @enderror
                            </div>

                            {{-- Description --}}
                            <div class="mb-3 col-lg-6">
                                <label class="form-label" for="description">{{ __('crm::crm.description') }}</label>
                                <textarea class="form-control" id="description" name="description" rows="3"
                                    placeholder="{{ __('crm::crm.enter_activity_details') }}">{{ old('description') }}</textarea>
                                @error('description')
                                    <small class="text-danger">{{ $message }}</small>
                                @enderror
                            </div>

                            <x-branches::branch-select :branches="$branches" />

                        </div>

                        <div class="d-flex justify-content-start mt-4">
                            <button type="submit" class="btn btn-main me-2" id="submitBtn">
                                <i class="las la-save"></i> {{ __('crm::crm.save') }}
                            </button>

                            <a href="{{ route('activities.index') }}" class="btn btn-danger">
                                <i class="las la-times"></i> {{ __('crm::crm.cancel') }}
                            </a>
                        </div>

                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
