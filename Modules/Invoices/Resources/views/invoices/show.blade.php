@extends('admin.dashboard')

@section('sidebar')
    @if (in_array($invoice->pro_type, [10, 12, 14, 16, 22]))
        @include('components.sidebar.sales-invoices')
    @elseif (in_array($invoice->pro_type, [11, 13, 15, 17, 24, 25]))
        @include('components.sidebar.purchases-invoices')
    @elseif (in_array($invoice->pro_type, [18, 19, 20, 21]))
        @include('components.sidebar.inventory-invoices')
    @endif
@endsection

@section('content')
    @include('components.breadcrumb', [
        'title' => __('invoices::invoices.invoice_details'),
        'breadcrumb_items' => [
            ['label' => __('invoices::invoices.home'), 'url' => route('admin.dashboard')],
            ['label' => __('invoices::invoices.invoice'), 'url' => route('invoices.index', ['type' => $invoice->pro_type])],
            ['label' => __('invoices::invoices.invoice_details')],
        ],
    ])

    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="page-title-box no-print">
                    <div class="d-flex justify-content-between align-items-center">
                        <h4 class="page-title">
                            {{ __('invoices::invoices.invoice_details') }}: #{{ $invoice->pro_id }}
                            - {{ $invoice->type->ptext ?? __('invoices::invoices.not_specified') }}
                        </h4>
                        <div class="d-flex gap-2">
                            @php
                                $titles = [
                                    10 => __('invoices::invoices.sales_invoice'),
                                    11 => __('invoices::invoices.purchase_invoice'),
                                    12 => __('invoices::invoices.sales_return'),
                                    13 => __('invoices::invoices.purchase_return'),
                                    14 => __('invoices::invoices.sales_order'),
                                    15 => __('invoices::invoices.purchase_order'),
                                    16 => __('invoices::invoices.quotation_to_customer'),
                                    17 => __('invoices::invoices.quotation_from_supplier'),
                                    18 => __('invoices::invoices.damaged_goods_invoice'),
                                    19 => __('invoices::invoices.dispatch_order'),
                                    20 => __('invoices::invoices.addition_order'),
                                    21 => __('invoices::invoices.store_to_store_transfer'),
                                    22 => __('invoices::invoices.booking_order'),
                                    24 => __('invoices::invoices.service_invoice'),
                                    25 => __('invoices::invoices.requisition'),
                                    26 => __('invoices::invoices.pricing_agreement'),
                                ];
                                $permissionName = 'edit ' . ($titles[$invoice->pro_type] ?? '');
                            @endphp
                            @can($permissionName)
                                <a href="{{ route('invoices.edit', $invoice) }}" class="btn btn-primary">
                                    <i class="las la-edit"></i> {{ __('invoices::invoices.edit') }}
                                </a>
                            @endcan
                            <a href="{{ route('invoice.print', $invoice->id) }}" class="btn btn-success" target="_blank">
                                <i class="las la-print"></i> {{ __('invoices::invoices.print_invoice') }}
                            </a>
                            <button onclick="window.print()" class="btn btn-info">
                                <i class="las la-print"></i> {{ __('invoices::invoices.print') }}
                            </button>
                            <a href="{{ route('invoices.index', ['type' => $invoice->pro_type]) }}" class="btn btn-secondary">
                                <i class="las la-arrow-right"></i> {{ __('invoices::invoices.view') }}
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
                        <h5 class="mb-0"><i class="las la-file-invoice"></i> {{ __('invoices::invoices.invoice_information') }}</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold">{{ __('invoices::invoices.invoice_number') }}:</label>
                                <div class="form-control-static">{{ $invoice->pro_id }}</div>
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold">{{ __('invoices::invoices.invoice_type') }}:</label>
                                <div class="form-control-static">
                                    {{ $invoice->type->ptext ?? __('invoices::invoices.not_specified') }}
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold">{{ __('invoices::invoices.invoice_date') }}:</label>
                                <div class="form-control-static">{{ $invoice->pro_date ? \Carbon\Carbon::parse($invoice->pro_date)->format('Y-m-d') : __('invoices::invoices.not_specified') }}</div>
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold">{{ __('invoices::invoices.serial_number') }}:</label>
                                <div class="form-control-static">{{ $invoice->pro_serial ?? __('invoices::invoices.not_specified') }}</div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold">{{ __('invoices::invoices.customer') }}/{{ __('invoices::invoices.supplier') }}:</label>
                                <div class="form-control-static">{{ $invoice->acc1Head->aname ?? __('invoices::invoices.not_specified') }}</div>
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold">{{ __('invoices::invoices.store') }}:</label>
                                <div class="form-control-static">{{ $invoice->acc2Head->aname ?? __('invoices::invoices.not_specified') }}</div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold">{{ __('invoices::invoices.subtotal') }}:</label>
                                <div class="form-control-static">{{ number_format($invoice->fat_val ?? 0, 2) }}</div>
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold">{{ __('invoices::invoices.discount') }}:</label>
                                <div class="form-control-static">{{ number_format($invoice->fat_dis ?? 0, 2) }}</div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold">{{ __('invoices::invoices.additional') }}:</label>
                                <div class="form-control-static">{{ number_format($invoice->fat_add ?? 0, 2) }}</div>
                            </div>

                            @if(setting('enable_vat_fields') == '1' && setting('vat_level') != 'disabled')
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold">{{ __('invoices::invoices.vat') }} ({{ setting('default_vat_percentage', 0) }}%):</label>
                                <div class="form-control-static">{{ number_format($invoice->vat_value ?? 0, 2) }}</div>
                            </div>
                            @endif
                        </div>

                        @if(setting('enable_vat_fields') == '1' && setting('withholding_tax_level') != 'disabled')
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold">{{ __('invoices::invoices.withholding_tax') }} ({{ setting('default_withholding_tax_percentage', 0) }}%):</label>
                                <div class="form-control-static">{{ number_format($invoice->withholding_tax_value ?? 0, 2) }}</div>
                            </div>
                        </div>
                        @endif

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold">{{ __('invoices::invoices.grand_total') }}:</label>
                                <div class="form-control-static">
                                    <h4 class="mb-0">{{ number_format($invoice->fat_net ?? $invoice->pro_value ?? 0, 2) }}</h4>
                                </div>
                            </div>

                            @if($invoice->employee)
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold">{{ __('invoices::invoices.employee') }}:</label>
                                <div class="form-control-static">{{ $invoice->employee->aname ?? __('invoices::invoices.not_specified') }}</div>
                            </div>
                            @endif
                        </div>

                        @if($invoice->operationItems->count() > 0)
                        <div class="row mt-4">
                            <div class="col-12">
                                <h6 class="fw-bold mb-3">{{ __('invoices::invoices.invoice_details') }}:</h6>
                                <div class="table-responsive">
                                    <table class="table table-bordered">
                                        <thead class="table-light">
                                            <tr>
                                                <th>#</th>
                                                <th>{{ __('invoices::invoices.item') }}</th>
                                                <th>{{ __('invoices::invoices.code') }}</th>
                                                <th>{{ __('invoices::invoices.unit') }}</th>
                                                <th>{{ __('invoices::invoices.quantity') }}</th>
                                                <th>{{ __('invoices::invoices.price') }}</th>
                                                <th>{{ __('invoices::invoices.discount_percentage') }}</th>
                                                <th>{{ __('invoices::invoices.discount_value') }}</th>
                                                <th>{{ __('invoices::invoices.total') }}</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @php
                                                $subtotal = 0;
                                            @endphp
                                            @foreach($invoice->operationItems as $index => $item)
                                            @php
                                                $quantity = $item->qty_in ?: $item->qty_out;
                                                $price = $item->item_price ?? 0;
                                                $discountPre = $item->item_discount_pre ?? 0;
                                                $discountValue = $item->item_discount ?? 0;
                                                $itemTotal = $item->detail_value ?? 0;
                                                $subtotal += $itemTotal;
                                            @endphp
                                            <tr>
                                                <td>{{ $index + 1 }}</td>
                                                <td>{{ $item->item->name ?? __('invoices::invoices.not_specified') }}</td>
                                                <td>{{ $item->item->code ?? '-' }}</td>
                                                <td>{{ $item->unit->name ?? '-' }}</td>
                                                <td>{{ number_format($quantity, 2) }}</td>
                                                <td>{{ number_format($price, 2) }}</td>
                                                <td>{{ number_format($discountPre, 2) }}%</td>
                                                <td>{{ number_format($discountValue, 2) }}</td>
                                                <td>{{ number_format($itemTotal, 2) }}</td>
                                            </tr>
                                            @endforeach
                                        </tbody>
                                        <tfoot class="table-light">
                                            <tr>
                                                <td colspan="8" class="text-end fw-bold">{{ __('invoices::invoices.subtotal') }}:</td>
                                                <td class="fw-bold">{{ number_format($subtotal, 2) }}</td>
                                            </tr>
                                            @if($invoice->discount_percentage > 0 || $invoice->discount_value > 0)
                                            <tr>
                                                <td colspan="8" class="text-end">
                                                    {{ __('invoices::invoices.discount') }}
                                                    @if($invoice->discount_percentage > 0)
                                                        ({{ number_format($invoice->discount_percentage, 2) }}%)
                                                    @endif:
                                                </td>
                                                <td class="text-danger">- {{ number_format($invoice->discount_value ?? 0, 2) }}</td>
                                            </tr>
                                            @endif
                                            @if($invoice->additional_percentage > 0 || $invoice->additional_value > 0)
                                            <tr>
                                                <td colspan="8" class="text-end">
                                                    {{ __('invoices::invoices.additional') }}
                                                    @if($invoice->additional_percentage > 0)
                                                        ({{ number_format($invoice->additional_percentage, 2) }}%)
                                                    @endif:
                                                </td>
                                                <td class="text-success">+ {{ number_format($invoice->additional_value ?? 0, 2) }}</td>
                                            </tr>
                                            @endif
                                            @php
                                                $afterDiscount = $subtotal - ($invoice->discount_value ?? 0);
                                                $afterAdditional = $afterDiscount + ($invoice->additional_value ?? 0);
                                            @endphp
                                            @if(isset($invoice->vat_percentage) && $invoice->vat_percentage > 0)
                                            <tr>
                                                <td colspan="8" class="text-end">
                                                    {{ __('invoices::invoices.vat') }} ({{ number_format($invoice->vat_percentage, 2) }}%):
                                                </td>
                                                <td>+ {{ number_format(($afterAdditional * $invoice->vat_percentage) / 100, 2) }}</td>
                                            </tr>
                                            @php
                                                $afterAdditional += ($afterAdditional * $invoice->vat_percentage) / 100;
                                            @endphp
                                            @endif
                                            @if(isset($invoice->withholding_tax_percentage) && $invoice->withholding_tax_percentage > 0)
                                            <tr>
                                                <td colspan="8" class="text-end">
                                                    {{ __('invoices::invoices.withholding_tax') }} ({{ number_format($invoice->withholding_tax_percentage, 2) }}%):
                                                </td>
                                                <td class="text-danger">- {{ number_format(($afterAdditional * $invoice->withholding_tax_percentage) / 100, 2) }}</td>
                                            </tr>
                                            @php
                                                $afterAdditional -= ($afterAdditional * $invoice->withholding_tax_percentage) / 100;
                                            @endphp
                                            @endif
                                            <tr class="table-primary">
                                                <td colspan="8" class="text-end fw-bold fs-5">{{ __('invoices::invoices.grand_total') }}:</td>
                                                <td class="fw-bold fs-5">{{ number_format($afterAdditional, 2) }}</td>
                                            </tr>
                                            @if(isset($invoice->paid_from_client) && $invoice->paid_from_client > 0)
                                            <tr>
                                                <td colspan="8" class="text-end">{{ __('invoices::invoices.paid') }}:</td>
                                                <td>{{ number_format($invoice->paid_from_client, 2) }}</td>
                                            </tr>
                                            <tr class="table-warning">
                                                <td colspan="8" class="text-end fw-bold">{{ __('invoices::invoices.remaining') }}:</td>
                                                <td class="fw-bold">{{ number_format(max($afterAdditional - $invoice->paid_from_client, 0), 2) }}</td>
                                            </tr>
                                            @endif
                                        </tfoot>
                                    </table>
                                </div>
                            </div>
                        </div>
                        @endif

                        @if($invoice->info)
                        <div class="row">
                            <div class="col-12 mb-3">
                                <label class="form-label fw-bold">{{ __('invoices::invoices.notes') }}:</label>
                                <div class="form-control-static">{{ $invoice->info }}</div>
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
            .table { font-size: 10px; }
        }
    </style>
    @endpush
@endsection

