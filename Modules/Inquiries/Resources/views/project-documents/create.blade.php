@extends('admin.dashboard')

@section('sidebar')
    @include('components.sidebar.inquiries')
@endsection

@section('content')
    @include('components.breadcrumb', [
        'title' => __('inquiries::inquiries.project_documents'),
        'breadcrumb_items' => [
            ['label' => __('inquiries::inquiries.home'), 'url' => route('admin.dashboard')],
            ['label' => __('inquiries::inquiries.project_documents'), 'url' => route('inquiry.documents.index')],
            ['label' => __('inquiries::inquiries.create')],
        ],
    ])

    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-header">
                    <h2>{{ __('inquiries::inquiries.add_new') }}</h2>
                </div>

                <div class="card-body">
                    <form action="{{ route('inquiry.documents.store') }}" method="POST" onsubmit="disableButton()">
                        @csrf
                        <div class="row">

                            <div class="mb-3 col-lg-4">
                                <label class="form-label" for="name">{{ __('inquiries::inquiries.document_name') }}</label>
                                <input type="text" class="form-control" id="name" name="name"
                                    placeholder="{{ __('inquiries::inquiries.enter_document_name') }}" value="{{ old('name') }}">
                                @error('name')
                                    <small class="text-danger">{{ $message }}</small>
                                @enderror
                            </div>
                        </div>

                        <div class="d-flex justify-content-start mt-4">
                            <button type="submit" class="btn btn-main me-2" id="submitBtn">
                                <i class="las la-save"></i> {{ __('inquiries::inquiries.save') }}
                            </button>

                            <a href="{{ route('inquiry.documents.index') }}" class="btn btn-danger">
                                <i class="las la-times"></i> {{ __('inquiries::inquiries.cancel') }}
                            </a>
                        </div>

                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
