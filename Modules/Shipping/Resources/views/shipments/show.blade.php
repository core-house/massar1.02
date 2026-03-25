@extends('admin.dashboard')

@section('sidebar')
    @include('components.sidebar.shipping')
@endsection

@section('content')
    @include('components.breadcrumb', [
        'title' => __('shipping::shipping.shipments'),
        'breadcrumb_items' => [
            ['label' => __('shipping::shipping.dashboard'), 'url' => route('admin.dashboard')],
            ['label' => __('shipping::shipping.shipments'), 'url' => route('shipments.index')],
            ['label' => __('shipping::shipping.show')],
        ],
    ])

    <div class="row">
        <!-- معلومات الشحنة -->
        <div class="col-lg-8">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0"><i class="las la-box"></i> {{ __('shipping::shipping.shipment_information') }}</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <strong>{{ __('shipping::shipping.status') }}:</strong>
                            <p>
                                @if ($shipment->status == 'pending')
                                    <span class="badge bg-warning fs-6">{{ __('shipping::shipping.pending') }}</span>
                                @elseif($shipment->status == 'processing')
                                    <span class="badge bg-info fs-6">{{ __('shipping::shipping.processing') }}</span>
                                @elseif($shipment->status == 'in_transit')
                                    <span class="badge bg-primary fs-6">{{ __('shipping::shipping.in_transit') }}</span>
                                @elseif($shipment->status == 'out_for_delivery')
                                    <span class="badge bg-primary fs-6">{{ __('shipping::shipping.out_for_delivery') }}</span>
                                @elseif($shipment->status == 'delivered')
                                    <span class="badge bg-success fs-6">{{ __('shipping::shipping.delivered') }}</span>
                                @elseif($shipment->status == 'returned')
                                    <span class="badge bg-danger fs-6">{{ __('shipping::shipping.returned') }}</span>
                                @else
                                    <span class="badge bg-dark fs-6">{{ __('shipping::shipping.cancelled') }}</span>
                                @endif
                            </p>
                        </div>
                    </div>

                    <hr>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <strong>{{ __('shipping::shipping.customer_name') }}:</strong>
                            <p>{{ $shipment->customer_name }}</p>
                            @if ($shipment->customer_phone)
                                <small><i class="las la-phone"></i> {{ $shipment->customer_phone }}</small><br>
                            @endif
                            @if ($shipment->customer_email)
                                <small><i class="las la-envelope"></i> {{ $shipment->customer_email }}</small>
                            @endif
                        </div>
                        <div class="col-md-6 mb-3">
                            <strong>{{ __('shipping::shipping.tracking_number') }}:</strong>
                            <p>{{ $shipment->shippingCompany->name }}</p>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-12 mb-3">
                            <strong>{{ __('shipping::shipping.address') }}:</strong>
                            <p>{{ $shipment->customer_address }}</p>
                            @if ($shipment->zone)
                                <span class="badge bg-secondary">{{ $shipment->zone }}</span>
                            @endif
                        </div>
                    </div>

                    <hr>

                    <div class="row">
                        <div class="col-md-3 mb-3">
                            <strong>{{ __('shipping::shipping.weight') }}:</strong>
                            <p>{{ $shipment->weight }} {{ __('shipping::shipping.kg') }}</p>
                        </div>
                        <div class="col-md-3 mb-3">
                            <strong>{{ __('shipping::shipping.priority') }}:</strong>
                            <p>
                                @if ($shipment->priority == 'express')
                                    <span class="badge bg-danger">{{ __('shipping::shipping.express') }}</span>
                                @elseif($shipment->priority == 'urgent')
                                    <span class="badge bg-warning">{{ __('shipping::shipping.urgent') }}</span>
                                @else
                                    <span class="badge bg-secondary">{{ __('shipping::shipping.normal') }}</span>
                                @endif
                            </p>
                        </div>
                        <div class="col-md-3 mb-3">
                            <strong>{{ __('shipping::shipping.estimated_delivery') }}:</strong>
                            <p>{{ $shipment->estimated_delivery_date ? $shipment->estimated_delivery_date->format('Y-m-d') : '-' }}
                            </p>
                        </div>
                        <div class="col-md-3 mb-3">
                            <strong>{{ __('shipping::shipping.actual_delivery') }}:</strong>
                            <p>{{ $shipment->actual_delivery_date ? $shipment->actual_delivery_date->format('Y-m-d') : '-' }}
                            </p>
                        </div>
                    </div>

                    @if ($shipment->notes)
                        <div class="alert alert-info">
                            <strong>{{ __('shipping::shipping.notes') }}:</strong>
                            <p class="mb-0">{{ $shipment->notes }}</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- الكارد الجانبي -->
        <div class="col-lg-4">
            <!-- كارد التكلفة -->
            <div class="card bg-gradient-primary text-white">
                <div class="card-body">
                    <h5 class="text-white"><i class="las la-dollar-sign"></i> {{ __('shipping::shipping.cost_breakdown') }}</h5>
                    <hr class="bg-white">
                    <div class="d-flex justify-content-between mb-2">
                        <span>{{ __('shipping::shipping.shipping_cost') }}:</span>
                        <strong>{{ number_format($shipment->shipping_cost, 2) }}</strong>
                    </div>
                    <div class="d-flex justify-content-between mb-2">
                        <span>{{ __('shipping::shipping.insurance') }}:</span>
                        <strong>{{ number_format($shipment->insurance_cost, 2) }}</strong>
                    </div>
                    <div class="d-flex justify-content-between mb-2">
                        <span>{{ __('shipping::shipping.additional_fees') }}:</span>
                        <strong>{{ number_format($shipment->additional_fees, 2) }}</strong>
                    </div>
                    <hr class="bg-white">
                    <div class="d-flex justify-content-between">
                        <h5 class="text-white">{{ __('shipping::shipping.total') }}:</h5>
                        <h5 class="text-white">{{ number_format($shipment->total_cost, 2) }}</h5>
                    </div>
                </div>
            </div>

            <!-- كارد الأبعاد -->
            @if ($shipment->length || $shipment->width || $shipment->height)
                <div class="card mt-3">
                    <div class="card-body">
                        <h6><i class="las la-ruler-combined"></i> {{ __('shipping::shipping.package_dimensions') }}</h6>
                        <hr>
                        <p class="mb-1"><strong>{{ __('shipping::shipping.length') }}:</strong> {{ $shipment->length ?? '-' }} cm</p>
                        <p class="mb-1"><strong>{{ __('shipping::shipping.width') }}:</strong> {{ $shipment->width ?? '-' }} cm</p>
                        <p class="mb-1"><strong>{{ __('shipping::shipping.height') }}:</strong> {{ $shipment->height ?? '-' }} cm</p>
                        @if ($shipment->package_value)
                            <hr>
                            <p class="mb-0"><strong>{{ __('shipping::shipping.package_value') }}:</strong>
                                {{ number_format($shipment->package_value, 2) }}</p>
                        @endif
                    </div>
                </div>
            @endif

            <!-- أزرار الإجراءات -->
            <div class="card mt-3">
                <div class="card-body">
                    <h6><i class="las la-cog"></i> {{ __('shipping::shipping.actions') }}</h6>
                    <hr>
                    <a href="{{ route('shipments.edit', $shipment->id) }}" class="btn btn-warning btn-block mb-2">
                        <i class="las la-edit"></i> {{ __('shipping::shipping.edit') }}
                    </a>
                    <a href="{{ route('shipments.index') }}" class="btn btn-secondary">
                        <i class="las la-arrow-right"></i> {{ __('shipping::shipping.back') }}
                    </a>
                    <button class="btn btn-secondary btn-block" onclick="window.print()">
                        <i class="las la-print"></i> {{ __('shipping::shipping.print') }}
                    </button>
                </div>
            </div>
        </div>
    </div>

    <style>
        .timeline {
            position: relative;
            padding-left: 30px;
        }

        .timeline::before {
            content: '';
            position: absolute;
            left: 10px;
            top: 0;
            bottom: 0;
            width: 2px;
            background: #dee2e6;
        }

        .timeline-badge {
            position: absolute;
            left: 0;
            width: 20px;
            height: 20px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 8px;
            color: white;
        }

        .bg-gradient-primary {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }
    </style>
@endsection
