@extends('admin.dashboard')

@section('sidebar')
    @include('components.sidebar.reports')
@endsection

@section('content')
    @include('components.breadcrumb', [
        'title' => __('reports::reports.Inactive Items Report'),
        'breadcrumb_items' => [
            ['label' => __('reports::reports.home'), 'url' => route('admin.dashboard')],
            ['label' => __('reports::reports.Reports'), 'url' => route('reports.index')],
            ['label' => __('reports::reports.Inactive Items Report')],
        ],
    ])

    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <x-table-export-actions table-id="inactive-items-table" filename="inactive-items"
                    excel-label="{{ __('reports::reports.export_excel') }}" pdf-label="{{ __('reports::reports.export_pdf') }}"
                    print-label="{{ __('reports::reports.print') }}" />

                <table id="inactive-items-table" class="table table-striped table-bordered text-center">
                    <thead class="table-light">
                        <tr>
                            <th>#</th>
                            <th>{{ __('reports::reports.code') }}</th>
                            <th>{{ __('reports::reports.name') }}</th>
                            <th>{{ __('reports::reports.description') }}</th>
                            <th>{{ __('reports::reports.Branch') }}</th>
                            <th class="text-end">{{ __('reports::reports.min_order_quantity') }}</th>
                            <th class="text-end">{{ __('reports::reports.max_order_quantity') }}</th>
                            <th class="text-end">{{ __('reports::reports.average_cost') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($items as $item)
                            <tr class="table-warning">
                                <td>{{ $loop->iteration }}</td>
                                <td>{{ $item->code }}</td>
                                <td>{{ $item->name }}</td>
                                <td>{{ $item->info }}</td>
                                <td>{{ $item->branch }}</td>
                                <td class="text-end">{{ $item->min_order_quantity ?? 0 }}</td>
                                <td class="text-end">{{ $item->max_order_quantity ?? 0 }}</td>
                                <td class="text-end">
                                    <span class="fw-bold text-primary">
                                        {{ number_format($item->average_cost ?? 0, 2) }}
                                    </span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center">
                                    <div class="alert alert-info mb-0">{{ __('reports::reports.No Inactive Items') }}</div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>

                <div class="mt-3">
                    {{ $items->links() }}
                </div>
            </div>
        </div>
    </div>
@endsection

