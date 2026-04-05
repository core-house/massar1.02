@extends('admin.dashboard')

@section('sidebar')
    @include('components.sidebar.decumintations')
@endsection

@section('title', __('common.edit') . ' - ' . $document->title)

@section('content')
    <div class="row justify-content-center">
        <div class="col-12 col-lg-8">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title mb-0">{{ __('common.edit') }}: {{ $document->title }}</h4>
                </div>
                <div class="card-body">
                    <form action="{{ route('documents.update', $document) }}" method="POST" enctype="multipart/form-data">
                        @csrf @method('PUT')
                        @include('decumintations::documents._form')
                        <div class="d-flex gap-2 mt-3">
                            <button type="submit" class="btn btn-primary">
                                <i class="las la-save"></i> {{ __('common.save') }}
                            </button>
                            <a href="{{ route('documents.index') }}" class="btn btn-secondary">
                                {{ __('common.cancel') }}
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
