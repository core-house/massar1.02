@extends('admin.dashboard')

@section('sidebar')
    @include('components.sidebar.manufacturing')
@endsection

@section('content')
    @include('components.breadcrumb', [
        'title' => __('reports::reports.manufacturing_invoices_report'),
        'breadcrumb_items' => [
            ['label' => __('reports::reports.home'), 'url' => route('admin.dashboard')],
            ['label' => __('reports::reports.manufacturing_invoices')],
        ],
    ])

    <div class="row">
        <div class="col-lg-12">
            <br><br>
            <div class="card">
                <div class="card-body">
                    <div class="table-responsive" style="overflow-x: auto;">
                        <x-table-export-actions table-id="manufacturing-invoice-report-table"
                            filename="manufacturing-invoice-report-table" excel-label="{{ __('reports::reports.export_excel') }}"
                            pdf-label="{{ __('reports::reports.export_pdf') }}" print-label="{{ __('reports::reports.print') }}" />

                        <table id="manufacturing-invoice-report-table"
                            class="table table-bordered table-striped text-center" style="min-width: 1200px;">
                            <thead class="table-light">
                                <tr>
                                    <th>#</th>
                                    <th>{{ __('reports::reports.invoice_number') }}</th>
                                    <th>{{ __('reports::reports.type') }}</th>
                                    <th>{{ __('reports::reports.balance_value') }}</th>
                                    <th>{{ __('reports::reports.invoice_date') }}</th>
                                    <th>{{ __('reports::reports.account_entry') }}</th>
                                    <th>{{ __('reports::reports.account_entry') }}</th>
                                    <th>{{ __('reports::reports.employee') }}</th>
                                    <th>{{ __('reports::reports.user') }}</th>
                                    <th>{{ __('reports::reports.actions') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($invoices as $invoice)
                                    <tr>
                                        <td>{{ $loop->iteration }}</td>
                                        <td>{{ $invoice->pro_id }}</td>
                                        <td>
                                            @if ($invoice->pro_type == 59)
                                                <span class="badge bg-primary">{{ __('reports::reports.manufacturing') }}</span>
                                            @endif
                                        </td>
                                        <td>{{ number_format($invoice->pro_value, 2) }}</td>
                                        <td>{{ $invoice->pro_date }}</td>
                                        <td>{{ $invoice->acc1Head->aname ?? '-' }}</td>
                                        <td>{{ $invoice->acc2Head->aname ?? '-' }}</td>
                                        <td>{{ $invoice->employee->aname ?? '-' }}</td>
                                        <td>{{ $invoice->user->name ?? '-' }}</td>
                                        <td>
                                            <div class="d-flex flex-wrap gap-1">
                                                <a class="btn btn-primary px-3 py-2 fs-6"
                                                    href="{{ route('manufacturing.edit', $invoice->id) }}">
                                                    {{ __('reports::reports.edit_invoice') }}
                                                </a>

                                                <form action="{{ route('invoices.destroy', $invoice->id) }}" method="POST"
                                                    style="display:inline-block;"
                                                    onsubmit="return confirm('{{ __('reports::reports.are_you_sure_want_to_delete_this_record') }}');">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-danger px-3 py-2 fs-6">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="10" class="text-center">
                                            <div class="alert alert-info py-3 mb-0"
                                                style="font-size: 1.2rem; font-weight: 500;">
                                                <i class="fas fa-info-circle me-2"></i>
                                                {{ __('reports::reports.no_data_available_yet') }}
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
@endsection
