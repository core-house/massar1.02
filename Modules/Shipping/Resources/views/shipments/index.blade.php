@extends('admin.dashboard')

@section('sidebar')
    @include('components.sidebar.shipping')
@endsection

@section('content')
    @include('components.breadcrumb', [
        'title' => __('shipping::shipping.shipments'),
        'breadcrumb_items' => [['label' => __('shipping::shipping.dashboard'), 'url' => route('admin.dashboard')], ['label' => __('shipping::shipping.shipments')]],
    ])

    <div class="row">
        <div class="col-lg-12">
            @can('create Shipments')
                <a href="{{ route('shipments.create') }}" type="button" class="btn btn-main font-hold fw-bold">
                    {{ __('shipping::shipping.add_new') }}
                    <i class="las la-plus me-2"></i>
                </a>
            @endcan
            <br><br>

            <div class="card">
                <div class="card-body">
                    <div class="table-responsive" style="overflow-x: auto;">

                        <x-table-export-actions table-id="shipments-table" filename="shipments-table"
                            excel-label="{{ __('shipping::shipping.export_excel') }}" pdf-label="{{ __('shipping::shipping.export_pdf') }}"
                            print-label="{{ __('shipping::shipping.print') }}" />

                        <table id="shipments-table" class="table table-striped mb-0" style="min-width: 1200px;">
                            <thead class="table-light text-center align-middle">
                                <tr>
                                    <th>#</th>
                                    <th>{{ __('shipping::shipping.tracking_number') }}</th>
                                    <th>{{ __('shipping::shipping.shipping_company') }}</th>
                                    <th>{{ __('shipping::shipping.customer_name') }}</th>
                                    <th>{{ __('shipping::shipping.address') }}</th>
                                    <th>{{ __('shipping::shipping.weight') }}</th>
                                    <th>{{ __('shipping::shipping.cost') }}</th>
                                    <th>{{ __('shipping::shipping.priority') }}</th>
                                    <th>{{ __('shipping::shipping.status') }}</th>
                                    @canany(['edit Shipments', 'delete Shipments'])
                                        <th>{{ __('shipping::shipping.actions') }}</th>
                                    @endcanany
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($shipments as $shipment)
                                    <tr class="text-center">
                                        <td>{{ $loop->iteration }}</td>
                                        <td>{{ $shipment->tracking_number }}</td>
                                        <td>{{ $shipment->shippingCompany->name ?? __('shipping::shipping.na') }}</td>
                                        <td>{{ $shipment->customer_name }}</td>
                                        <td>{{ $shipment->customer_address }}</td>
                                        <td>{{ $shipment->weight }} {{ __('shipping::shipping.kg') }}</td>
                                        <td>{{ number_format($shipment->total_cost, 2) }}</td>
                                        <td>
                                            @if($shipment->priority == 'express')
                                                <span class="badge bg-danger">{{ __('shipping::shipping.express') }}</span>
                                            @elseif($shipment->priority == 'urgent')
                                                <span class="badge bg-warning">{{ __('shipping::shipping.urgent') }}</span>
                                            @else
                                                <span class="badge bg-secondary">{{ __('shipping::shipping.normal') }}</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if ($shipment->status == 'pending')
                                                <span class="badge bg-warning text-dark">{{ __('shipping::shipping.pending') }}</span>
                                            @elseif ($shipment->status == 'processing')
                                                <span class="badge bg-info">{{ __('shipping::shipping.processing') }}</span>
                                            @elseif ($shipment->status == 'in_transit')
                                                <span class="badge bg-info">{{ __('shipping::shipping.in_transit') }}</span>
                                            @elseif ($shipment->status == 'out_for_delivery')
                                                <span class="badge bg-primary">{{ __('shipping::shipping.out_for_delivery') }}</span>
                                            @elseif ($shipment->status == 'delivered')
                                                <span class="badge bg-success">{{ __('shipping::shipping.delivered') }}</span>
                                            @elseif ($shipment->status == 'returned')
                                                <span class="badge bg-danger">{{ __('shipping::shipping.returned') }}</span>
                                            @else
                                                <span class="badge bg-danger">{{ __('shipping::shipping.cancelled') }}</span>
                                            @endif
                                        </td>
                                        @canany(['edit Shipments', 'delete Shipments'])
                                            <td>
                                                <a class="btn btn-primary btn-icon-square-sm" 
                                                    href="{{ route('shipments.show', $shipment->id) }}" 
                                                    title="{{ __('shipping::shipping.view_details') }}">
                                                    <i class="las la-eye"></i>
                                                </a>
                                                @can('edit Shipments')
                                                    <a class="btn btn-success btn-icon-square-sm"
                                                        href="{{ route('shipments.edit', $shipment) }}">
                                                        <i class="las la-edit"></i>
                                                    </a>
                                                @endcan

                                                @can('delete Shipments')
                                                    <form action="{{ route('shipments.destroy', $shipment) }}" method="POST"
                                                        style="display:inline-block;"
                                                        onsubmit="return confirm('{{ __('shipping::shipping.confirm_delete_shipment') }}');">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="btn btn-danger btn-icon-square-sm">
                                                            <i class="las la-trash"></i>
                                                        </button>
                                                    </form>
                                                @endcan
                                            </td>
                                        @endcanany
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="10" class="text-center">
                                            <div class="alert alert-info py-3 mb-0"
                                                style="font-size: 1.2rem; font-weight: 500;">
                                                <i class="las la-info-circle me-2"></i>
                                                {{ __('shipping::shipping.no_data_available') }}
                                            </div>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{ $shipments->links() }}
@endsection
