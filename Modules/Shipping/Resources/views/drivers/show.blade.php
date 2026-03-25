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
                    <h4 class="page-title">{{ __('shipping::shipping.driver_details') }}: {{ $driver->name }}</h4>
                    <div class="d-flex gap-2">
                        <a href="{{ route('drivers.edit', $driver) }}" class="btn btn-primary">
                            <i class="las la-edit"></i> {{ __('shipping::shipping.edit') }}
                        </a>
                        <button onclick="window.print()" class="btn btn-info">
                            <i class="las la-print"></i> {{ __('shipping::shipping.print') }}
                        </button>
                        <a href="{{ route('drivers.index') }}" class="btn btn-secondary">
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
                    <h5 class="mb-0 text-white"><i class="las la-user"></i> {{ __('shipping::shipping.driver_details') }}</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">{{ __('shipping::shipping.name') }}:</label>
                            <div class="form-control-static">{{ $driver->name }}</div>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">{{ __('shipping::shipping.phone_number') }}:</label>
                            <div class="form-control-static">{{ $driver->phone ?? __('shipping::shipping.na') }}</div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">{{ __('shipping::shipping.vehicle_type') }}:</label>
                            <div class="form-control-static">{{ $driver->vehicle_type ?? __('shipping::shipping.na') }}</div>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">{{ __('shipping::shipping.status') }}:</label>
                            <div class="form-control-static">
                                @if($driver->is_available)
                                    <span class="badge bg-success">{{ __('shipping::shipping.available') }}</span>
                                @else
                                    <span class="badge bg-danger">{{ __('shipping::shipping.unavailable') }}</span>
                                @endif
                            </div>
                        </div>
                    </div>

                    @if($driver->branch)
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">{{ __('shipping::shipping.branch') }}:</label>
                            <div class="form-control-static">{{ $driver->branch->name }}</div>
                        </div>
                    </div>
                    @endif
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

