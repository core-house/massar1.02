@extends('admin.dashboard')

@section('sidebar')
    @include('components.sidebar.projects')
@endsection

@section('content')
    @include('components.breadcrumb', [
        'title' => $project->name,
        'breadcrumb_items' => [
            ['label' => __('projects::projects.home'), 'url' => route('admin.dashboard')],
            ['label' => __('projects::projects.projects'), 'url' => route('projects.index')],
            ['label' => $project->name]
        ],
    ])
    <div class="container-fluid">
        <!-- Project Header -->
        <div class="card mb-4">
            <div class="card-header bg-primary text-white">
                <div class="row align-items-center">
                    <div class="col-md-8">
                        <h4 class="mb-0">
                            <i class="las la-project-diagram me-2"></i>
                            {{ $project->name }}
                        </h4>
                    </div>
                    <div class="col-md-4 text-end">
                        @php
                            $statusClasses = [
                                'pending' => 'bg-warning',
                                'in_progress' => 'bg-info',
                                'completed' => 'bg-success',
                                'cancelled' => 'bg-danger',
                            ];
                            $statusTexts = [
                                'pending' => __('projects::projects.pending'),
                                'in_progress' => __('projects::projects.in_progress'),
                                'completed' => __('projects::projects.completed'),
                                'cancelled' => __('projects::projects.cancelled'),
                            ];
                        @endphp
                        <span class="badge {{ $statusClasses[$project->status] ?? 'bg-secondary' }} p-2">
                            {{ $statusTexts[$project->status] ?? $project->status }}
                        </span>
                    </div>
                </div>
            </div>
            <div class="card-body">
                <p class="text-muted mb-0">{{ $project->description }}</p>
            </div>
        </div>

        <!-- Project Info -->
        <div class="row mb-4">
            <div class="col-md-6">
                <div class="card h-100">
                    <div class="card-header">
                        <h5 class="mb-0"><i class="las la-calendar me-2"></i>{{ __('projects::projects.project_dates') }}</h5>
                    </div>
                    <div class="card-body">
                        <table class="table table-sm">
                            <tr>
                                <th width="40%">{{ __('projects::projects.start_date') }}:</th>
                                <td>{{ $project->start_date?->format('Y-m-d') ?? '-' }}</td>
                            </tr>
                            <tr>
                                <th>{{ __('projects::projects.expected_end_date') }}:</th>
                                <td>{{ $project->end_date?->format('Y-m-d') ?? '-' }}</td>
                            </tr>
                            <tr>
                                <th>{{ __('projects::projects.actual_end_date') }}:</th>
                                <td>{{ $project->actual_end_date?->format('Y-m-d') ?? __('projects::projects.not_completed_yet') }}</td>
                            </tr>
                        </table>
                    </div>
                </div>
            </div>

            <div class="col-md-6">
                <div class="card h-100">
                    <div class="card-header">
                        <h5 class="mb-0"><i class="las la-user me-2"></i>{{ __('projects::projects.project_team') }}</h5>
                    </div>
                    <div class="card-body">
                        <table class="table table-sm">
                            <tr>
                                <th width="40%">{{ __('projects::projects.created_by') }}:</th>
                                <td>{{ $project->createdBy->name ?? '-' }}</td>
                            </tr>
                            <tr>
                                <th>{{ __('projects::projects.updated_by') }}:</th>
                                <td>{{ $project->updatedBy->name ?? '-' }}</td>
                            </tr>
                            <tr>
                                <th>{{ __('projects::projects.created_at') }}:</th>
                                <td>{{ $project->created_at?->format('Y-m-d H:i') ?? '-' }}</td>
                            </tr>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Budget Info -->
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="mb-0"><i class="las la-wallet me-2"></i>{{ __('projects::projects.budget_information') }}</h5>
            </div>
            <div class="card-body">
                @php
                    $totalReceipts = $vouchers->whereIn('pro_type', [1, 32])->sum('pro_value');
                    $totalPayments = $vouchers->whereIn('pro_type', [2, 33, 101])->sum('pro_value');
                    $budgetDiff = $project->budget + $totalReceipts - $totalPayments;
                    $percent = $project->budget > 0 ? ($totalPayments / $project->budget) * 100 : 0;
                @endphp
                <div class="row">
                    <div class="col-md-3">
                        <div class="text-center p-3 border rounded">
                            <small class="text-muted d-block">{{ __('projects::projects.estimated_budget') }}</small>
                            <h4 class="text-primary mb-0">{{ number_format($project->budget, 2) }}</h4>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="text-center p-3 border rounded">
                            <small class="text-muted d-block">{{ __('projects::projects.total_receipts') }}</small>
                            <h4 class="text-success mb-0">{{ number_format($totalReceipts, 2) }}</h4>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="text-center p-3 border rounded">
                            <small class="text-muted d-block">{{ __('projects::projects.total_payments') }}</small></h4></h4></h4></h4></h4></h4></h4></h4></h4></h4></h4></h4></h4></h4></h4></h4></h4></h4></h4></h4></h4></h4></h4></h4></h4></h4></h4></h4></h4></h4></h4></h4></h4></h4></h4></h4></h4></h4></h4></h4></h4></h4></h4></h4></h4></h4></h4></h4></h4></h4></h4></h4></h4></h4></h4></h4></h4></h4></h4></h4></h4></h4></h4></h4>
                            <h4 class="text-danger mb-0">{{ number_format($totalPayments, 2) }}</h4>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="text-center p-3 border rounded">
                            <small class="text-muted d-block">{{ $budgetDiff < 0 ? __('projects::projects.budget_exceeded') : __('projects::projects.remaining_budget') }}</small>
                            <h4 class="{{ $budgetDiff < 0 ? 'text-danger' : 'text-warning' }} mb-0">
                                {{ number_format(abs($budgetDiff), 2) }}
                            </h4>
                        </div>
                    </div>
                </div>
                <div class="mt-3">
                    <div class="progress" style="height: 25px;">
                        <div class="progress-bar {{ $percent > 100 ? 'bg-danger' : 'bg-success' }}" 
                             role="progressbar" 
                             style="width: {{ min($percent, 100) }}%">
                            {{ number_format($percent, 1) }}%
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Operations -->
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="mb-0"><i class="las la-cogs me-2"></i>{{ __('projects::projects.operations') }}</h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-striped table-hover">
                        <thead class="table-light">
                            <tr>
                                <th>{{ __('projects::projects.operation') }}</th>
                                <th>{{ __('projects::projects.amount') }}</th>
                                <th>{{ __('projects::projects.voucher_date') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($operations as $operation)
                                <tr>
                                    <td>{{ $operation->type->ptext ?? '-' }}</td>
                                    <td class="text-success fw-bold">{{ number_format($operation->pro_value, 2) }}</td>
                                    <td>{{ $operation->pro_date }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="3" class="text-center text-muted py-3">
                                        <i class="las la-inbox la-3x d-block mb-2"></i>
                                        {{ __('projects::projects.no_operations') }}
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Equipment -->
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="mb-0"><i class="las la-truck me-2"></i>{{ __('projects::projects.equipment') }}</h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-striped table-hover">
                        <thead class="table-light">
                            <tr>
                                <th>{{ __('projects::projects.equipment') }}</th>
                                <th>{{ __('projects::projects.rental_start') }}</th>
                                <th>{{ __('projects::projects.rental_end') }}</th>
                                <th>{{ __('projects::projects.amount') }}</th>
                                <th>{{ __('projects::projects.actions') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($equipmentOperations as $equipmentOp)
                                <tr>
                                    <td>{{ $equipmentOp['equipment']->aname }}</td>
                                    <td>{{ $equipmentOp['operation']->start_date }}</td>
                                    <td>{{ $equipmentOp['operation']->end_date }}</td>
                                    <td class="text-success fw-bold">
                                        {{ number_format($equipmentOp['operation']->pro_value, 2) }}
                                    </td>
                                    <td>
                                        <a href="{{ route('rentals.edit', $equipmentOp['operation']->id) }}" 
                                           class="btn btn-sm btn-primary">
                                            <i class="las la-eye"></i> {{ __('projects::projects.view') }}
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-center text-muted py-3">
                                        <i class="las la-truck la-3x d-block mb-2"></i>
                                        {{ __('projects::projects.no_equipment') }}
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Vouchers -->
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="mb-0"><i class="las la-receipt me-2"></i>{{ __('projects::projects.receipts_payments') }}</h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-striped table-hover">
                        <thead class="table-light">
                            <tr>
                                <th>{{ __('projects::projects.voucher_type') }}</th>
                                <th>{{ __('projects::projects.voucher_date') }}</th>
                                <th class="text-success">{{ __('projects::projects.receipts') }}</th>
                                <th class="text-danger">{{ __('projects::projects.payments') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($vouchers as $voucher)
                                <tr>
                                    <td>
                                        @if (in_array($voucher->pro_type, [1, 32]))
                                            <span class="badge bg-success">
                                                <i class="las la-arrow-up"></i> {{ $voucher->type->ptext }}
                                            </span>
                                        @elseif(in_array($voucher->pro_type, [2, 33, 101]))
                                            <span class="badge bg-danger">
                                                <i class="las la-arrow-down"></i> {{ $voucher->type->ptext }}
                                            </span>
                                        @else
                                            {{ $voucher->type->ptext ?? __('projects::projects.unspecified') }}
                                        @endif
                                    </td>
                                    <td>{{ $voucher->pro_date }}</td>
                                    <td class="text-success fw-bold">
                                        {{ in_array($voucher->pro_type, [1, 32]) ? number_format($voucher->pro_value, 2) : '0.00' }}
                                    </td>
                                    <td class="text-danger fw-bold">
                                        {{ in_array($voucher->pro_type, [2, 33, 101]) ? number_format($voucher->pro_value, 2) : '0.00' }}
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="text-center text-muted py-3">
                                        <i class="las la-receipt la-3x d-block mb-2"></i>
                                        {{ __('projects::projects.no_vouchers') }}
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                        <tfoot class="table-light">
                            <tr class="fw-bold">
                                <td colspan="2"><i class="las la-calculator me-2"></i>{{ __('projects::projects.total') }}</td>
                                <td class="text-success">
                                    {{ number_format($vouchers->whereIn('pro_type', [1, 32])->sum('pro_value'), 2) }}
                                </td>
                                <td class="text-danger">
                                    {{ number_format($vouchers->whereIn('pro_type', [2, 33, 101])->sum('pro_value'), 2) }}
                                </td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection
