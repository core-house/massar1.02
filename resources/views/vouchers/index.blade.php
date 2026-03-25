@extends('admin.dashboard')

@section('sidebar')
    @include('components.sidebar.vouchers')
@endsection

@section('content')
    @include('components.breadcrumb', [
        'title' => $currentTypeInfo['title'],
        'items' => [
            ['label' => __('Home'), 'url' => route('admin.dashboard')],
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
                           placeholder="{{ __('Search by number, description, account...') }}">
                </div>
                <div class="col-md-2">
                    <select id="typeFilter" class="form-select">
                        <option value="">{{ __('All Types') }}</option>
                        <option value="1">{{ __('Receipt Voucher') }}</option>
                        <option value="101">{{ __('Payment Voucher') }}</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <input type="date" id="dateFrom" class="form-control" placeholder="{{ __('From Date') }}">
                </div>
                <div class="col-md-2">
                    <input type="date" id="dateTo" class="form-control" placeholder="{{ __('To Date') }}">
                </div>
                <div class="col-md-3">
                    <button type="button" id="resetFilter" class="btn btn-secondary w-100">
                        <i class="fas fa-redo"></i> {{ __('Reset') }}
                    </button>
                </div>
            </div>

            <div class="table-responsive" style="overflow-x: auto;">
                <table class="table table-striped mb-0" id="vouchersTable" style="min-width: 1200px;">
                    <thead class="table-light text-center align-middle">
                        <tr>
                            <th>{{ __('#') }}</th>
                            <th>{{ __('Date') }}</th>
                            <th>{{ __('Operation Number') }}</th>
                            <th>{{ __('Operation Type') }}</th>
                            <th>{{ __('Description') }}</th>
                            @if(isMultiCurrencyEnabled())
                                <th>{{ __('Amount') }} ({{ __('Foreign Currency') }})</th>
                                <th>{{ __('Amount') }} ({{ __('Local Currency') }})</th>
                            @else
                                <th>{{ __('Amount') }}</th>
                            @endif
                            <th>{{ __('Account') }}</th>
                            <th>{{ __('Opposite Account') }}</th>
                            <th>{{ __('Employee') }}</th>
                            <th>{{ __('User') }}</th>
                            <th>{{ __('Created At') }}</th>
                            <th>{{ __('Notes') }}</th>
                            <th>{{ __('Review') }}</th>
                            <th>{{ __('Actions') }}</th>
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
                                        {{ $voucher->is_approved ? __('Yes') : __('No') }}
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
                                        <strong>{{ __('No vouchers currently available', ['type' => $currentTypeInfo['title']]) }}</strong>
                                        <br>
                                        <small class="text-muted mt-2 d-block">
                                            {{ __('You can add new voucher using the button above', ['type' => strtolower($currentTypeInfo['create_text'])]) }}
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
