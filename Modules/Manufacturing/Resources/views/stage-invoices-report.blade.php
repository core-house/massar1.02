@extends('admin.dashboard')

@section('sidebar')
    @include('components.sidebar.manufacturing')
@endsection

@section('content')
<div class="container-fluid">
    <div class="card shadow-sm">
        <div class="card-header bg-light">
            <h5 class="mb-0">
                <i class="fas fa-chart-bar me-2"></i>
                {{ __('Stage Invoices Report') }}
            </h5>
        </div>

        <div class="card-body">
            <!-- Filters -->
            <form method="GET" action="{{ route('manufacturing.stage-invoices-report') }}" class="mb-4">
                <div class="row g-3">
                    <div class="col-md-3">
                        <label class="form-label">{{ __('Manufacturing Order') }}</label>
                        <select name="selectedOrderId" class="form-select" onchange="this.form.submit()">
                            <option value="">{{ __('All Orders') }}</option>
                            @foreach($orders as $order)
                                <option value="{{ $order->id }}" {{ $selectedOrderId == $order->id ? 'selected' : '' }}>
                                    {{ $order->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-3">
                        <label class="form-label">{{ __('Stage') }}</label>
                        <select name="selectedStageId" class="form-select" onchange="this.form.submit()">
                            <option value="">{{ __('All Stages') }}</option>
                            @foreach($stages as $stage)
                                <option value="{{ $stage->id }}" {{ $selectedStageId == $stage->id ? 'selected' : '' }}>
                                    {{ $stage->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-2">
                        <label class="form-label">{{ __('From Date') }}</label>
                        <input type="date" name="dateFrom" value="{{ $dateFrom }}" class="form-control">
                    </div>

                    <div class="col-md-2">
                        <label class="form-label">{{ __('To Date') }}</label>
                        <input type="date" name="dateTo" value="{{ $dateTo }}" class="form-control">
                    </div>

                    <div class="col-md-2">
                        <label class="form-label">&nbsp;</label>
                        <button type="submit" class="btn btn-primary w-100">
                            <i class="fas fa-search me-1"></i> {{ __('Filter') }}
                        </button>
                    </div>
                </div>
            </form>

            <!-- Statistics -->
            <div class="row mb-4">
                <div class="col-md-6">
                    <div class="card bg-primary text-white">
                        <div class="card-body">
                            <h6 class="text-white-50">{{ __('Total Invoices') }}</h6>
                            <h3>{{ number_format($totalInvoices) }}</h3>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="card bg-success text-white">
                        <div class="card-body">
                            <h6 class="text-white-50">{{ __('Total Value') }}</h6>
                            <h3>{{ number_format($totalValue, 2) }} {{ __('EGP') }}</h3>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Table -->
            <div class="table-responsive">
                <table class="table table-bordered table-hover">
                    <thead class="table-light">
                        <tr class="text-center">
                            <th>#</th>
                            <th>{{ __('Invoice Number') }}</th>
                            <th>{{ __('Date') }}</th>
                            <th>{{ __('Order') }}</th>
                            <th>{{ __('Stage') }}</th>
                            <th>{{ __('Branch') }}</th>
                            <th>{{ __('Value') }}</th>
                            <th>{{ __('Actions') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($invoices as $invoice)
                            <tr>
                                <td class="text-center">{{ $loop->iteration }}</td>
                                <td class="text-center">
                                    <span class="badge bg-primary">{{ $invoice->pro_id }}</span>
                                </td>
                                <td class="text-center">{{ $invoice->pro_date }}</td>
                                <td>{{ $invoice->manufacturingOrder->name ?? '-' }}</td>
                                <td>{{ $invoice->manufacturingStage->name ?? '-' }}</td>
                                <td>{{ $invoice->branch->name ?? '-' }}</td>
                                <td class="text-end">
                                    <strong class="text-success">{{ number_format($invoice->pro_value, 2) }}</strong>
                                </td>
                                <td class="text-center">
                                    <a href="{{ route('manufacturing.show', $invoice->id) }}" class="btn btn-sm btn-info">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center py-5">
                                    <i class="fas fa-inbox fa-3x text-muted mb-3 d-block"></i>
                                    <h5 class="text-muted">{{ __('No invoices found') }}</h5>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div class="mt-3">
                {{ $invoices->links() }}
            </div>
        </div>
    </div>
</div>
@endsection
