@extends('admin.dashboard')

@section('sidebar')
    @include('components.sidebar.vouchers')
@endsection

@section('content')
    @include('components.breadcrumb', [
        'title' => $currentTypeInfo['title'],
        'breadcrumb_items' => [
            ['label' => __('navigation.home'), 'url' => route('admin.dashboard')],
            ['label' => $currentTypeInfo['title']],
        ],
    ])

    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h2>{{ $currentTypeInfo['title'] }}</h2>

            <x-vouchers.create-button :type="$type" :currentTypeInfo="$currentTypeInfo" />
        </div>

        <div class="card-body">
            {{-- Search Filters --}}
            <div class="row mb-3">
                <div class="col-md-3">
                    <input type="text" id="searchInput" class="form-control"
                           placeholder="{{ __('vouchers.search_placeholder') }}">
                </div>
                <div class="col-md-2">
                    <select id="typeFilter" class="form-select">
                        <option value="">{{ __('general.all_types') }}</option>
                        <option value="1">{{ __('navigation.receipt_voucher') }}</option>
                        <option value="101">{{ __('navigation.payment_voucher') }}</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <input type="date" id="dateFrom" class="form-control" placeholder="{{ __('invoices::invoices.from_date') }}">
                </div>
                <div class="col-md-2">
                    <input type="date" id="dateTo" class="form-control" placeholder="{{ __('invoices::invoices.to_date') }}">
                </div>
                <div class="col-md-3">
                    <button type="button" id="resetFilter" class="btn btn-secondary w-100">
                        <i class="fas fa-redo"></i> {{ __('general.reset') }}
                    </button>
                </div>
            </div>

            <div class="table-responsive" style="overflow-x: auto;">
                <table class="table table-striped mb-0" id="vouchersTable" style="min-width: 1200px;">
                    <thead class="table-light text-center align-middle">
                        <tr>
                            <th>{{ __('#') }}</th>
                            <th>{{ __('invoices::invoices.date') }}</th>
                            <th>{{ __('vouchers.operation_number') }}</th>
                            <th>{{ __('invoices::invoices.operation_type') }}</th>
                            <th>{{ __('general.description') }}</th>
                            @if(isMultiCurrencyEnabled())
                                <th>{{ __('vouchers.amount') }} ({{ __('vouchers.foreign_currency') }})</th>
                                <th>{{ __('vouchers.amount') }} ({{ __('vouchers.local_currency') }})</th>
                            @else
                                <th>{{ __('vouchers.amount') }}</th>
                            @endif
                            <th>{{ __('invoices::invoices.account') }}</th>
                            <th>{{ __('vouchers.opposite_account') }}</th>
                            <th>{{ __('general.employee') }}</th>
                            <th>{{ __('vouchers.user') }}</th>
                            <th>{{ __('general.created_at') }}</th>
                            <th>{{ __('general.notes') }}</th>
                            <th>{{ __('vouchers.review') }}</th>
                            <th>{{ __('general.actions') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php $x = 1; @endphp
                        @forelse ($vouchers as $voucher)
                            <tr data-pro-type="{{ $voucher->pro_type }}">
                                <td>{{ $x++ }}</td>
                                <td>{{ $voucher->pro_date }}</td>
                                <td>{{ $voucher->pro_id }}</td>
                                <td>
                                    <x-vouchers.type-badge :proType="$voucher->pro_type" :typeText="$voucher->type->ptext ?? null" />
                                </td>
                                <td>{{ $voucher->details }}</td>
                                @if(isMultiCurrencyEnabled())
                                    <td class="h5 fw-bold">
                                        @if($voucher->currency_id && $voucher->currency_rate > 1)
                                            {{ number_format($voucher->pro_value / $voucher->currency_rate, 2) }}
                                            {{ $voucher->currency?->name ?? '' }}
                                        @else
                                            -
                                        @endif
                                    </td>
                                    <td class="h5 fw-bold">
                                        {{ number_format($voucher->pro_value, 2) }}
                                    </td>
                                @else
                                    <td class="h5 fw-bold">
                                        {{ number_format($voucher->pro_value, 2) }}
                                    </td>
                                @endif
                                <td>{{ $voucher->account1->aname ?? '' }}</td>
                                <td>{{ $voucher->account2->aname ?? '' }}</td>
                                <td>{{ $voucher->emp1->aname ?? '' }}</td>
                                <td>{{ $voucher->user->name ?? '' }}</td>
                                <td>{{ $voucher->created_at ? $voucher->created_at->format('Y-m-d') : '' }}</td>
                                <td>{{ $voucher->notes ?? '' }}</td>
                                <td>
                                    <span class="badge {{ $voucher->is_approved ? 'bg-success' : 'bg-secondary' }}">
                                        {{ $voucher->is_approved ? __('general.yes') : __('general.no') }}
                                    </span>
                                </td>
                                <td>
                                    <x-vouchers.action-buttons :voucher="$voucher" />
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="14" class="text-center">
                                    <div class="alert alert-info py-4 mb-0">
                                        <i class="fas fa-info-circle me-2"></i>
                                        <strong>{{ __('vouchers.no_vouchers_available') }}</strong>
                                        <br>
                                        <small class="text-muted mt-2 d-block">
                                            {{ __('vouchers.add_voucher_hint') }}
                                        </small>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if (method_exists($vouchers, 'links'))
                <div class="d-flex justify-content-center mt-3">
                    {{ $vouchers->appends(request()->query())->links() }}
                </div>
            @endif
        </div>
    </div>

    {{-- Journal Entry Modals --}}
    @foreach ($vouchers as $voucher)
        <x-vouchers.journal-entry-modal :voucher="$voucher" />
    @endforeach
@endsection
