@extends('admin.dashboard')

@section('sidebar')
    @include('components.sidebar.service')
@endsection

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="page-title-box no-print">
                    <div class="d-flex justify-content-between align-items-center">
                        <h4 class="page-title">{{ __('maintenance::maintenance.maintenance_details') }}:
                            {{ $maintenance->item_name ?? '#' . $maintenance->id }}</h4>
                        <div class="d-flex gap-2">
                            <a href="{{ route('maintenances.edit', $maintenance) }}" class="btn btn-primary">
                                <i class="fas fa-edit"></i> {{ __('maintenance::maintenance.edit') }}
                            </a>
                            <button onclick="window.print()" class="btn btn-info">
                                <i class="fas fa-print"></i> {{ __('maintenance::maintenance.print') }}
                            </button>
                            <a href="{{ route('maintenances.index') }}" class="btn btn-secondary">
                                <i class="fas fa-arrow-right"></i> {{ __('maintenance::maintenance.back') }}
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
                        <h5 class="mb-0"><i class="fas fa-tools"></i> {{ __('maintenance::maintenance.maintenance_information') }}</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold">{{ __('maintenance::maintenance.client_name') }}:</label>
                                <div class="form-control-static">{{ $maintenance->client_name ?? __('maintenance::maintenance.n_a') }}</div>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold">{{ __('maintenance::maintenance.client_phone') }}:</label>
                                <div class="form-control-static">{{ $maintenance->client_phone ?? __('maintenance::maintenance.n_a') }}</div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold">{{ __('maintenance::maintenance.item_name') }}:</label>
                                <div class="form-control-static">{{ $maintenance->item_name ?? __('maintenance::maintenance.n_a') }}</div>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold">{{ __('maintenance::maintenance.item_number') }}:</label>
                                <div class="form-control-static">{{ $maintenance->item_number ?? __('maintenance::maintenance.n_a') }}</div>
                            </div>
                        </div>

                        <div class="row">
                            @if ($maintenance->type)
                                <div class="col-md-6 mb-3">
                                    <label class="form-label fw-bold">{{ __('maintenance::maintenance.service_type') }}:</label>
                                    <div class="form-control-static">{{ $maintenance->type->name }}</div>
                                </div>
                            @endif
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold">{{ __('maintenance::maintenance.status') }}:</label>
                                <div class="form-control-static">
                                    @php
                                        $statusLabels = [
                                            '0' => __('maintenance::maintenance.pending'),
                                            '1' => __('maintenance::maintenance.in_progress'),
                                            '2' => __('maintenance::maintenance.completed'),
                                            '3' => __('maintenance::maintenance.cancelled'),
                                        ];
                                        $status = (string) ($maintenance->status ?? '0');
                                    @endphp
                                    {{ $statusLabels[$status] ?? $status }}
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold">{{ __('maintenance::maintenance.date') }}:</label>
                                <div class="form-control-static">
                                    {{ $maintenance->date ? $maintenance->date->format('Y-m-d') : __('maintenance::maintenance.n_a') }}
                                </div>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold">{{ __('maintenance::maintenance.accural_date') }}:</label>
                                <div class="form-control-static">
                                    {{ $maintenance->accural_date ? $maintenance->accural_date->format('Y-m-d') : __('maintenance::maintenance.n_a') }}
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold">{{ __('maintenance::maintenance.asset') }}:</label>
                                <div class="form-control-static">
                                    @if ($maintenance->asset)
                                        {{ $maintenance->asset->asset_name }} ({{ __('maintenance::maintenance.accounting') }})
                                    @elseif($maintenance->depreciationItem)
                                        {{ $maintenance->depreciationItem->name }} ({{ __('maintenance::maintenance.direct') }})
                                    @else
                                        {{ __('maintenance::maintenance.n_a') }}
                                    @endif
                                </div>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold">{{ __('maintenance::maintenance.maintenance_type') }}:</label>
                                <div class="form-control-static">
                                    @if($maintenance->maintenance_type)
                                        {{ __('maintenance::maintenance.' . $maintenance->maintenance_type) }}
                                    @else
                                        {{ __('maintenance::maintenance.n_a') }}
                                    @endif
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <label class="form-label fw-bold">{{ __('maintenance::maintenance.spare_parts_cost') }}:</label>
                                <div class="form-control-static">{{ number_format($maintenance->spare_parts_cost ?? 0, 2) }}</div>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label fw-bold">{{ __('maintenance::maintenance.labor_cost') }}:</label>
                                <div class="form-control-static">{{ number_format($maintenance->labor_cost ?? 0, 2) }}</div>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label fw-bold">{{ __('maintenance::maintenance.total_cost') }}:</label>
                                <div class="form-control-static fw-bold text-primary">{{ number_format($maintenance->total_cost ?? 0, 2) }}</div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-12 mb-3">
                                <label class="form-label fw-bold">{{ __('maintenance::maintenance.notes') }}:</label>
                                <div class="form-control-static">{{ $maintenance->notes ?? __('maintenance::maintenance.n_a') }}</div>
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
