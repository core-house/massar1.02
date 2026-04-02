@extends('admin.dashboard')

@section('sidebar')
    @include('components.sidebar.myresources')
@endsection

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="page-title-box no-print">
                <div class="d-flex justify-content-between align-items-center">
                    <h4 class="page-title">{{ __('myresources.type_details') }}: {{ $type->display_name }}</h4>
                    <div class="d-flex gap-2">
                        <a href="{{ route('myresources.types.edit', $type) }}" class="btn btn-success">
                            <i class="fas fa-edit"></i> {{ __('myresources.edit') }}
                        </a>
                        <button onclick="window.print()" class="btn btn-info">
                            <i class="fas fa-print"></i> {{ __('common.print') }}
                        </button>
                        <a href="{{ route('myresources.types.index') }}" class="btn btn-secondary">
                            <i class="fas fa-arrow-right"></i> {{ __('common.back') }}
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card printable-content">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0"><i class="fas fa-list"></i> {{ __('myresources.type_information') }}</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">{{ __('myresources.name') }}:</label>
                            <div class="form-control-static">{{ $type->name }}</div>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">{{ __('myresources.arabic_name') }}:</label>
                            <div class="form-control-static">{{ $type->name_ar ?? __('common.unspecified') }}</div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">{{ __('myresources.main_category') }}:</label>
                            <div class="form-control-static">{{ $type->category->display_name ?? __('common.unspecified') }}</div>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">{{ __('myresources.status') }}:</label>
                            <div class="form-control-static">
                                @if($type->is_active)
                                    <span class="badge bg-success">{{ __('myresources.active') }}</span>
                                @else
                                    <span class="badge bg-danger">{{ __('common.inactive') }}</span>
                                @endif
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-12 mb-3">
                            <label class="form-label fw-bold">{{ __('myresources.description') }}:</label>
                            <div class="form-control-static">{{ $type->description ?? __('myresources.no_description') }}</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('styles')
<style>
    .form-control-static {
        padding: 0.5rem;
        background-color: #f8f9fa;
        border: 1px solid #dee2e6;
        border-radius: 0.375rem;
        min-height: 2.5rem;
        display: flex;
        align-items: center;
    }
    @media print {
        .no-print { display: none !important; }
        .card { border: 1px solid #000 !important; box-shadow: none !important; }
        .card-header { background: #f1f1f1 !important; color: #000 !important; }
        body { font-size: 12px; }
        .form-control-static { background: #fff !important; border: 1px solid #000 !important; }
    }
</style>
@endpush
@endsection
