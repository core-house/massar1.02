@extends('admin.dashboard')

@section('sidebar')
    @include('components.sidebar.sales-invoices')
@endsection

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="page-title-box no-print">
                <div class="d-flex justify-content-between align-items-center">
                    <h4 class="page-title">{{ __('invoices::invoices.template_details') }}: {{ $template->name ?? '#' . $template->id }}</h4>
                    <div class="d-flex gap-2">
                        <a href="{{ route('invoice-templates.edit', $template) }}" class="btn btn-primary">
                            <i class="las la-edit"></i> {{ __('invoices::invoices.edit') }}
                        </a>
                        <button onclick="window.print()" class="btn btn-info">
                            <i class="las la-print"></i> {{ __('invoices::invoices.print') }}
                        </button>
                        <a href="{{ route('invoice-templates.index') }}" class="btn btn-secondary">
                            <i class="las la-arrow-right"></i> {{ __('invoices::invoices.back') }}
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
                    <h5 class="mb-0"><i class="las la-file-invoice"></i> {{ __('invoices::invoices.invoice_template_information') }}</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">{{ __('invoices::invoices.name') }}:</label>
                            <div class="form-control-static">{{ $template->name }}</div>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">{{ __('invoices::invoices.code') }}:</label>
                            <div class="form-control-static">{{ $template->code ?? __('invoices::invoices.none') }}</div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-12 mb-3">
                            <label class="form-label fw-bold">{{ __('invoices::invoices.description') }}:</label>
                            <div class="form-control-static">{{ $template->description ?? __('invoices::invoices.none') }}</div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">{{ __('invoices::templates.display_order') }}:</label>
                            <div class="form-control-static">{{ $template->sort_order ?? 0 }}</div>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">{{ __('invoices::invoices.invoice_status') }}:</label>
                            <div class="form-control-static">
                                @if($template->is_active)
                                    <span class="badge bg-success">{{ __('invoices::invoices.active') }}</span>
                                @else
                                    <span class="badge bg-danger">{{ __('invoices::invoices.inactive') }}</span>
                                @endif
                            </div>
                        </div>
                    </div>

                    @if($template->invoiceTypes->count() > 0)
                    <div class="row">
                        <div class="col-12 mb-3">
                            <label class="form-label fw-bold">{{ __('invoices::invoices.invoice_types') }}:</label>
                            <div class="form-control-static">
                                @foreach($template->invoiceTypes as $type)
                                    <span class="badge bg-info me-1">{{ $type->invoice_type }}</span>
                                @endforeach
                            </div>
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

