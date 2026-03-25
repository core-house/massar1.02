@extends('admin.dashboard')

@section('sidebar')
    @include('components.sidebar.shipping')
@endsection

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="page-title-box no-print">
                <div class="d-flex justify-content-between align-items-center">
                    <h4 class="page-title">{{ __('shipping::shipping.zone_details') }}: {{ $zone->name }}</h4>
                    <div class="d-flex gap-2">
                        <a href="{{ route('shipping.zones.edit', $zone) }}" class="btn btn-primary">
                            <i class="las la-edit"></i> {{ __('shipping::shipping.edit') }}
                        </a>
                        <button onclick="window.print()" class="btn btn-info">
                            <i class="las la-print"></i> {{ __('shipping::shipping.print') }}
                        </button>
                        <a href="{{ route('shipping.zones.index') }}" class="btn btn-secondary">
                            <i class="las la-arrow-right"></i> {{ __('shipping::shipping.back') }}
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
                    <h5 class="mb-0 text-white"><i class="las la-map-marker"></i> {{ __('shipping::shipping.zone_details') }}</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">{{ __('shipping::shipping.name') }}:</label>
                            <div class="form-control-static">{{ $zone->name }}</div>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">{{ __('shipping::shipping.code') }}:</label>
                            <div class="form-control-static">{{ $zone->code }}</div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-12 mb-3">
                            <label class="form-label fw-bold">{{ __('shipping::shipping.description') }}:</label>
                            <div class="form-control-static">{{ $zone->description ?? __('shipping::shipping.na') }}</div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label class="form-label fw-bold">{{ __('shipping::shipping.base_rate') }}:</label>
                            <div class="form-control-static">{{ number_format($zone->base_rate, 2) }} {{ __('shipping::shipping.egp') }}</div>
                        </div>

                        <div class="col-md-4 mb-3">
                            <label class="form-label fw-bold">{{ __('shipping::shipping.rate_per_kg') }}:</label>
                            <div class="form-control-static">{{ number_format($zone->rate_per_kg, 2) }} {{ __('shipping::shipping.egp') }}</div>
                        </div>

                        <div class="col-md-4 mb-3">
                            <label class="form-label fw-bold">{{ __('shipping::shipping.estimated_days') }}:</label>
                            <div class="form-control-static">{{ $zone->estimated_days }} {{ __('shipping::shipping.days') }}</div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">{{ __('shipping::shipping.branch') }}:</label>
                            <div class="form-control-static">
                                {{ $zone->branch->name ?? __('shipping::shipping.na') }}
                            </div>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">{{ __('shipping::shipping.status') }}:</label>
                            <div class="form-control-static">
                                @if($zone->is_active)
                                    <span class="badge bg-success">{{ __('shipping::shipping.active') }}</span>
                                @else
                                    <span class="badge bg-danger">{{ __('shipping::shipping.inactive') }}</span>
                                @endif
                            </div>
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

    .printable-content {
        page-break-inside: avoid;
    }

    @media print {
        .no-print {
            display: none !important;
        }

        .card {
            border: 1px solid #000 !important;
            box-shadow: none !important;
            page-break-inside: avoid;
        }

        .card-header {
            background: #f1f1f1 !important;
            color: #000 !important;
            -webkit-print-color-adjust: exact;
            print-color-adjust: exact;
        }

        body {
            font-size: 12px;
        }

        .form-control-static {
            background: #fff !important;
            border: 1px solid #000 !important;
        }
    }
</style>
@endpush
@endsection

