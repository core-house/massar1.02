@extends('admin.dashboard')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="page-title-box no-print">
                <div class="d-flex justify-content-between align-items-center">
                    <h4 class="page-title">{{ __('inquiries::inquiries.pricing_status') }}: {{ $pricingStatus->name ?? '#' . $pricingStatus->id }}</h4>
                    <div class="d-flex gap-2">
                        <a href="{{ route('pricing-statuses.edit', $pricingStatus) }}" class="btn btn-primary">
                            <i class="fas fa-edit"></i> {{ __('inquiries::inquiries.edit') }}
                        </a>
                        <button onclick="window.print()" class="btn btn-info">
                            <i class="fas fa-print"></i> {{ __('inquiries::inquiries.print') }}
                        </button>
                        <a href="{{ route('pricing-statuses.index') }}" class="btn btn-secondary">
                            <i class="fas fa-arrow-right"></i> {{ __('inquiries::inquiries.back') }}
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
                    <h5 class="mb-0"><i class="fas fa-tag"></i> {{ __('inquiries::inquiries.pricing_status') }}</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        @foreach($pricingStatus->getAttributes() as $key => $value)
                            @if(!in_array($key, ['id', 'created_at', 'updated_at', 'deleted_at']))
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold">{{ ucfirst(str_replace('_', ' ', $key)) }}:</label>
                                <div class="form-control-static">
                                    @if($value)
                                        @if($key == 'is_active')
                                            {{ $value ? __('inquiries::inquiries.active') : __('inquiries::inquiries.inactive') }}
                                        @else
                                            {{ is_array($value) ? json_encode($value) : $value }}
                                        @endif
                                    @else
                                        {{ __('inquiries::inquiries.not_specified') }}
                                    @endif
                                </div>
                            </div>
                            @endif
                        @endforeach
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

