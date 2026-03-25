@extends('admin.dashboard')

@section('sidebar')
    @include('components.sidebar.discounts')
@endsection
@section('content')

    @include('components.breadcrumb', [
        'title' => __('invoices::invoices.discounts'),
        'breadcrumb_items' => [
            ['label' => __('invoices::invoices.home'), 'url' => route('admin.dashboard')],
            ['label' => __('invoices::invoices.discounts'), 'url' => route('discounts.index')],
            ['label' => __('invoices::invoices.edit_discount')],
        ],
    ])

    <div class="container-fluid px-0">
        <section class="content" style="width:100%">
            <form action="{{ route('discounts.update', $discount->id) }}" method="post">
                @csrf
                @method('PUT')

                <div class="card bg-white w-100" style="max-width: 100%;">
                    <div class="m-3">
                        <h3 class="card-title fw-bold fs-2">{{ $titles[$type] }}</h3>
                    </div>

                    <div class="card-body">
                        <div class="row">
                            <input type="hidden" name="type" value="{{ $type }}">

                            @if ($type == 30)
                                <input type="hidden" name="acc2" value="{{ $acc2Fixed->id }}">
                                <div class="col-lg-3">
                                    <label>{{ __('invoices::invoices.debit_account_clients') }}</label>
                                    <select name="acc1" class="form-control" required>
                                        @foreach ($clientsAccounts as $acc)
                                            <option value="{{ $acc->id }}" @selected($discount->acc1 == $acc->id)>
                                                {{ $acc->aname }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            @elseif ($type == 31)
                                <input type="hidden" name="acc1" value="{{ $acc1Fixed->id }}">
                                <div class="col-lg-3">
                                    <label>{{ __('invoices::invoices.credit_account_suppliers') }}</label>
                                    <select name="acc2" class="form-control" required>
                                        @foreach ($suppliers as $acc)
                                            <option value="{{ $acc->id }}" @selected($discount->acc2 == $acc->id)>
                                                {{ $acc->aname }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            @endif

                            <div class="col-lg-3">
                                <label>{{ __('invoices::invoices.date') }}</label>
                                <input type="date" name="pro_date" class="form-control"
                                    value="{{ $discount->pro_date }}">
                            </div>

                            <div class="col-lg-3">
                                <label>{{ __('invoices::invoices.document_number') }}</label>
                                <input type="number" name="pro_id" class="form-control" value="{{ $discount->pro_id }}"
                                    readonly>
                            </div>

                            <div class="col-lg-3">
                                <div class="form-group">
                                    <label for="pro_value">{{ __('invoices::invoices.discount_value') }}</label>
                                    <input type="number" name="pro_value" id="pro_value" step="0.01" min="0.01"
                                        class="form-control @error('pro_value') is-invalid @enderror"
                                        value="{{ $discount->pro_value }}">
                                    @error('pro_value')
                                        <span class="invalid-feedback"><strong>{{ $message }}</strong></span>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-lg-12">
                                <label>{{ __('invoices::invoices.notes') }}</label>
                                <textarea name="info" class="form-control">{{ $discount->info }}</textarea>
                            </div>

                            <div class="col-sm-10 mt-4">
                                <button type="submit" class="btn btn-main">{{ __('invoices::invoices.update') }}</button>
                                <a href="{{ route('discounts.index') }}" class="btn btn-danger">{{ __('invoices::invoices.cancel') }}</a>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </section>
    </div>

@endsection
