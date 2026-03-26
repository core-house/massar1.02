@extends('admin.dashboard')

@section('sidebar')
    @include('components.sidebar.decumintations')
@endsection

@section('title', __('decumintations.add_category'))

@section('content')
<div class="col-12 col-lg-6">
    <div class="card">
        <div class="card-header">
            <h4 class="card-title mb-0">{{ __('decumintations.add_category') }}</h4>
        </div>
        <div class="card-body">
            <form action="{{ route('document-categories.store') }}" method="POST">
                @csrf
                @include('decumintations::categories._form')
                <div class="d-flex gap-2 mt-3">
                    <button type="submit" class="btn btn-primary">
                        <i class="las la-save"></i> {{ __('common.save') }}
                    </button>
                    <a href="{{ route('document-categories.index') }}" class="btn btn-secondary">
                        {{ __('common.cancel') }}
                    </a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
