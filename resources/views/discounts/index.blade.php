@extends('admin.dashboard')

@section('sidebar')
    @include('components.sidebar.discounts')
@endsection

@section('content')
    @include('components.breadcrumb', [
        'title' => __('invoices::invoices.discounts'),
        'breadcrumb_items' => [['label' => __('invoices::invoices.home'), 'url' => route('admin.dashboard')], ['label' => __('invoices::invoices.discounts')]],
    ])
    <div class="row">
        <div class="col-lg-12">
            <div class="card">

                @if (is_null($type))
                    <div class="alert alert-warning text-center">
                        {{ __('invoices::invoices.please_select_discount_type') }}
                    </div>
                @else
                    <h4>
                        @if ($type == 30)
                            {{ __('invoices::invoices.allowed_discounts_list') }}
                        @elseif ($type == 31)
                            {{ __('invoices::invoices.earned_discounts_list') }}
                        @else
                            {{ __('invoices::invoices.all_discounts') }}
                        @endif
                    </h4>

                    <div class="card-body">
                        <div class="table-responsive" style="overflow-x: auto;">

                            <x-table-export-actions table-id="discount-table" filename="discount-table"
                                excel-label="{{ __('invoices::invoices.export_excel') }}" pdf-label="{{ __('invoices::invoices.export_pdf') }}"
                                print-label="{{ __('invoices::invoices.print_invoice') }}" />

                            <table id="discount-table" class="table table-striped mb-0 text-center"
                                style="min-width: 1000px;">
                                <thead class="table-light">
                                    <tr>
                                        <th>#</th>
                                        <th>{{ __('invoices::invoices.discount_type') }}</th>
                                        <th>{{ __('invoices::invoices.discount_value') }}</th>
                                        <th>{{ __('invoices::invoices.document_date') }}</th>
                                        <th>{{ __('invoices::invoices.document_number') }}</th>
                                        <th>{{ __('invoices::invoices.debit_account') }}</th>
                                        <th>{{ __('invoices::invoices.credit_account') }}</th>
                                        <th>{{ __('invoices::invoices.notes') }}</th>
                                        <th>{{ __('invoices::invoices.actions') }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($discounts as $discount)
                                        <tr>
                                            <td>{{ $loop->iteration }}</td>
                                            <td>
                                                <span
                                                    class="badge
                                                @if ($discount->acc1 == 49 || $discount->acc2 == 49) bg-success text-dark
                                                @elseif($discount->acc1 == 54 || $discount->acc2 == 54)
                                                    bg-warning text-dark
                                                @else
                                                    bg-secondary @endif
                                                text-uppercase">
                                                    @if ($discount->acc1 == 49 || $discount->acc2 == 49)
                                                        {{ __('invoices::invoices.allowed_discount') }}
                                                    @elseif($discount->acc1 == 54 || $discount->acc2 == 54)
                                                        {{ __('invoices::invoices.earned_discount') }}
                                                    @else
                                                        -
                                                    @endif
                                                </span>
                                            </td>
                                            <td>{{ $discount->pro_value }}</td>
                                            <td>{{ \Carbon\Carbon::parse($discount->pro_date)->format('Y-m-d') }}</td>
                                            <td>{{ $discount->pro_id }}</td>
                                            <td>{{ $discount->acc1Head->aname ?? '-' }}</td>
                                            <td>{{ $discount->acc2Head->aname ?? '-' }}</td>
                                            <td>{{ $discount->info }}</td>
                                            @canany(['edit Allowed Discounts', 'delete Allowed Discounts'])
                                                <td>
                                                    @can('edit Allowed Discounts')
                                                        <a href="{{ route('discounts.edit', ['discount' => $discount->id, 'type' => $discount->acc1 == 97 ? 31 : 30]) }}"
                                                            class="btn btn-success btn-sm">
                                                            <i class="las la-edit"></i>
                                                        </a>
                                                    @endcan
                                                    @can('delete Allowed Discounts')
                                                        <form action="{{ route('discounts.destroy', $discount->id) }}"
                                                            method="POST" style="display:inline-block;"
                                                            onsubmit="return confirm('{{ __('invoices::invoices.are_you_sure_delete') }}');">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="submit" class="btn btn-danger btn-sm">
                                                                <i class="las la-trash"></i>
                                                            </button>
                                                        </form>
                                                    @endcan
                                                </td>
                                            @endcanany
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="13">
                                                <div class="alert alert-info text-center mb-0">
                                                    {{ __('invoices::invoices.no_data_added_yet') }}
                                                </div>
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>

                    </div>
            </div>
            @endif
        </div>
    </div>
    </div>
@endsection
