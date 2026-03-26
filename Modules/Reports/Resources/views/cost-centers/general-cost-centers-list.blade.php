@extends('admin.dashboard')

@section('sidebar')
    @include('components.sidebar.reports')
@endsection

@section('content')
    <div class="container">
        <div class="card">
            <div class="card-head">
                <h2>{{ __('reports::reports.cost_centers_list') }}</h2>
            </div>
            <div class="card-body">
                <div class="row mb-3">
                    <div class="col-md-3">
                        <label for="as_of_date">{{ __('reports::reports.until_date') }}:</label>
                        <input type="date" id="as_of_date" class="form-control" wire:model="asOfDate">
                    </div>
                    <div class="col-md-3">
                        <label for="cost_center_type">{{ __('reports::reports.cost_center_type') }}:</label>
                        <select id="cost_center_type" class="form-control" wire:model="costCenterType">
                            <option value="">{{ __('reports::reports.all') }}</option>
                            <option value="department">{{ __('reports::reports.department') }}</option>
                            <option value="project">{{ __('reports::reports.project') }}</option>
                            <option value="branch">{{ __('reports::reports.branch') }}</option>
                            <option value="other">{{ __('reports::reports.other') }}</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label for="search">{{ __('reports::reports.search') }}:</label>
                        <input type="text" id="search" class="form-control" wire:model="search"
                            placeholder="{{ __('reports::reports.search_by_account_name') }}...">
                    </div>
                    <div class="col-md-3">
                        <button class="btn btn-primary mt-4"
                            wire:click="generateReport">{{ __('reports::reports.generate_report') }}</button>
                    </div>
                </div>

                <div class="table-responsive">
                    <table class="table table-bordered table-striped">
                        <thead>
                            <tr>
                                <th>{{ __('reports::reports.cost_center_code') }}</th>
                                <th>{{ __('reports::reports.cost_center_name') }}</th>
                                <th>{{ __('reports::reports.type') }}</th>
                                <th>{{ __('reports::reports.manager') }}</th>
                                <th class="text-end">{{ __('reports::reports.total_expenses') }}</th>
                                <th class="text-end">{{ __('reports::reports.total_revenues') }}</th>
                                <th class="text-end">{{ __('reports::reports.net_cost') }}</th>
                                <th>{{ __('reports::reports.status') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($costCenters as $center)
                                <tr>
                                    <td>{{ $center->code ?? '---' }}</td>
                                    <td>{{ $center->name ?? '---' }}</td>
                                    <td>{{ $center->type ?? '---' }}</td>
                                    <td>{{ $center->manager->name ?? '---' }}</td>
                                    <td class="text-end">{{ number_format($center->total_expenses, 2) }}</td>
                                    <td class="text-end">{{ number_format($center->total_revenues, 2) }}</td>
                                    <td class="text-end">{{ number_format($center->net_cost, 2) }}</td>
                                    <td>
                                        @if ($center->is_active)
                                            <span class="badge bg-success">{{ __('reports::reports.active') }}</span>
                                        @else
                                            <span class="badge bg-secondary">{{ __('reports::reports.inactive') }}</span>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" class="text-center">{{ __('reports::reports.no_data_available') }}</td>
                                </tr>
                            @endforelse
                        </tbody>
                        <tfoot>
                            <tr class="table-primary">
                                <th colspan="4">{{ __('reports::reports.total') }}</th>
                                <th class="text-end">{{ number_format($totalExpenses, 2) }}</th>
                                <th class="text-end">{{ number_format($totalRevenues, 2) }}</th>
                                <th class="text-end">{{ number_format($totalNetCost, 2) }}</th>
                                <th></th>
                            </tr>
                        </tfoot>
                    </table>
                </div>

                @if ($costCenters->hasPages())
                    <div class="d-flex justify-content-center">
                        {{ $costCenters->links() }}
                    </div>
                @endif

                <div class="row mt-3">
                    <div class="col-md-3">
                        <div class="alert alert-info">
                            <strong>{{ __('reports::reports.total_cost_centers') }}:</strong> {{ $totalCostCenters }}
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="alert alert-success">
                            <strong>{{ __('reports::reports.active_cost_centers') }}</strong> {{ $activeCostCenters }}
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="alert alert-warning">
                            <strong>{{ __('reports::reports.average_cost_per_center') }}:</strong>
                            {{ number_format($averageCostPerCenter, 2) }}
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="alert alert-primary">
                            <strong>{{ __('reports::reports.net_cost_total') }}</strong> {{ number_format($totalNetCost, 2) }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
