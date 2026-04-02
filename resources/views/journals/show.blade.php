@extends('admin.dashboard')

@section('sidebar')
    @include('components.sidebar.journals')
@endsection

@section('content')
    @include('components.breadcrumb', [
        'title' => __('common.journal_details'),
        'breadcrumb_items' => [
            ['label' => __('Home'), 'url' => route('admin.dashboard')],
            ['label' => __('common.journals'), 'url' => route('journals.index')],
            ['label' => __('common.journal_details')],
        ],
    ])

    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="page-title-box no-print">
                    <div class="d-flex justify-content-between align-items-center">
                        <h4 class="page-title">{{ __('common.journal_details') }}: #{{ $journal->pro_id }}</h4>
                        <div class="d-flex gap-2">
                            @can('edit journals')
                                <a href="{{ route('journals.edit', $journal) }}" class="btn btn-primary">
                                    <i class="fas fa-edit"></i> {{ __('common.edit') }}
                                </a>
                            @endcan
                            <button onclick="window.print()" class="btn btn-info">
                                <i class="fas fa-print"></i> {{ __('common.print') }}
                            </button>
                            <a href="{{ route('journals.index') }}" class="btn btn-secondary">
                                <i class="fas fa-arrow-right"></i> {{ __('common.back') }}
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
                        <h5 class="mb-0"><i class="fas fa-book"></i> {{ __('common.journal_information') }}</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold">{{ __('common.operation_number') }}:</label>
                                <div class="form-control-static">{{ $journal->pro_id }}</div>
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold">{{ __('common.operation_type') }}:</label>
                                <div class="form-control-static">
                                    {{ $journal->type->ptext ?? __('common.no_data_available') }}
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold">{{ __('common.date') }}:</label>
                                <div class="form-control-static">{{ $journal->pro_date ? \Carbon\Carbon::parse($journal->pro_date)->format('Y-m-d') : __('common.no_data_available') }}</div>
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold">{{ __('common.operation_number') }}:</label>
                                <div class="form-control-static">{{ $journal->pro_num ?? __('common.no_data_available') }}</div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold">{{ __('common.from_account') }}:</label>
                                <div class="form-control-static">{{ $journal->acc1Head->aname ?? __('common.no_data_available') }}</div>
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold">{{ __('common.to_account') }}:</label>
                                <div class="form-control-static">{{ $journal->acc2Head->aname ?? __('common.no_data_available') }}</div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold">{{ __('common.amount') }}:</label>
                                <div class="form-control-static">{{ number_format($journal->pro_value ?? 0, 2) }}</div>
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold">{{ __('common.employee') }}:</label>
                                <div class="form-control-static">{{ $journal->employee->aname ?? __('common.no_data_available') }}</div>
                            </div>
                        </div>

                        @if($journal->details)
                        <div class="row">
                            <div class="col-12 mb-3">
                                <label class="form-label fw-bold">{{ __('common.description') }}:</label>
                                <div class="form-control-static">{{ $journal->details }}</div>
                            </div>
                        </div>
                        @endif

                        @if($journal->info)
                        <div class="row">
                            <div class="col-12 mb-3">
                                <label class="form-label fw-bold">{{ __('common.notes') }}:</label>
                                <div class="form-control-static">{{ $journal->info }}</div>
                            </div>
                        </div>
                        @endif

                        @if($journal->info2)
                        <div class="row">
                            <div class="col-12 mb-3">
                                <label class="form-label fw-bold">{{ __('common.general_notes') }}:</label>
                                <div class="form-control-static">{{ $journal->info2 }}</div>
                            </div>
                        </div>
                        @endif

                        @if($journal->journalHead && $journal->journalHead->journalDetails->count() > 0)
                        <div class="row mt-4">
                            <div class="col-12">
                                <h6 class="fw-bold mb-3">{{ __('common.journal_details') }}:</h6>
                                <div class="table-responsive">
                                    <table class="table table-bordered">
                                        <thead>
                                            <tr>
                                                <th>{{ __('common.account') }}</th>
                                                <th>{{ __('common.debit') }}</th>
                                                <th>{{ __('common.credit') }}</th>
                                                <th>{{ __('common.info') }}</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($journal->journalHead->journalDetails as $detail)
                                            <tr>
                                                <td>{{ $detail->accountHead->aname ?? __('common.no_data_available') }}</td>
                                                <td>{{ number_format($detail->debit ?? 0, 2) }}</td>
                                                <td>{{ number_format($detail->credit ?? 0, 2) }}</td>
                                                <td>{{ $detail->info ?? __('common.no_data_available') }}</td>
                                            </tr>
                                            @endforeach
                                        </tbody>
                                        <tfoot>
                                            <tr>
                                                <th class="text-end">{{ __('common.total') }}:</th>
                                                <th>{{ number_format($journal->journalHead->journalDetails->sum('debit'), 2) }}</th>
                                                <th>{{ number_format($journal->journalHead->journalDetails->sum('credit'), 2) }}</th>
                                                <th></th>
                                            </tr>
                                        </tfoot>
                                    </table>
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
            .table { font-size: 10px; }
        }
    </style>
    @endpush
@endsection

