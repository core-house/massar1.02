@extends('admin.dashboard')

{{-- Dynamic Sidebar --}}
@section('sidebar')
    @include('components.sidebar.transfers')
@endsection

@section('content')
    @include('components.breadcrumb', [
        'title' => __('general.edit'),
        'breadcrumb_items' => [
            ['label' => __('navigation.home'), 'url' => route('admin.dashboard')],
            ['label' => __('navigation.cash_transfers'), 'url' => route('transfers.index')],
            ['label' => __('general.edit')],
        ],
    ])

    <form id="myForm" action="{{ route('transfers.update', $transfer->id) }}" method="POST">
        @csrf
        @method('PUT')
        <input type="hidden" name="pro_type" value="{{ $pro_type }}">
        <input type="hidden" name="currency_id" id="currency_id" value="{{ $transfer->currency_id ?? 1 }}">
        <input type="hidden" name="currency_rate" id="currency_rate" value="{{ $transfer->currency_rate ?? 1 }}">

        <div class="card bg-white col-md-11 container">
            <div class="card-header">
                <h1 class="h1 mb-0">
                    @switch($type)
                        @case('cash_to_cash') {{ __('vouchers.pro_type_cash_to_cash') }} @break
                        @case('cash_to_bank') {{ __('vouchers.pro_type_cash_to_bank') }} @break
                        @case('bank_to_cash') {{ __('vouchers.pro_type_bank_to_cash') }} @break
                        @case('bank_to_bank') {{ __('vouchers.pro_type_bank_to_bank') }} @break
                    @endswitch
                </h1>
            </div>

            <div class="card-body">
                @if ($errors->any())
                    <div class="alert alert-danger">
                        <ul>
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <div class="row">
                    <div class="col-lg-2">
                        <div class="form-group">
                            <label>{{ __('vouchers.operation_number') }}</label>
                            <input type="text" name="pro_id" class="form-control" value="{{ $transfer->pro_id }}" readonly>
                        </div>
                    </div>
                    <div class="col-lg-2">
                        <div class="form-group">
                            <label>{{ __('vouchers.serial_number') }}</label>
                            <input type="text" name="pro_serial" class="form-control" value="{{ $transfer->pro_serial }}">
                        </div>
                    </div>
                    <div class="col-lg-2">
                        <div class="form-group">
                            <label>{{ __('vouchers.receipt_number') }}</label>
                            <input type="text" name="pro_num" class="form-control" value="{{ old('pro_num', $transfer->pro_num ?? '') }}">
                        </div>
                    </div>
                    <div class="col-lg-4">
                        <div class="form-group">
                            <label>{{ __('vouchers.date') }}</label>
                            <input type="date" name="pro_date" class="form-control"
                                value="{{ old('pro_date', $transfer->pro_date ? date('Y-m-d', strtotime($transfer->pro_date)) : date('Y-m-d')) }}">
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-lg-3">
                        <div class="form-group">
                            <label>{{ __('vouchers.amount') }}</label>
                            @php
                                $displayValue = $transfer->pro_value;
                                if ($transfer->currency_rate && $transfer->currency_rate > 0) {
                                    $displayValue = $transfer->pro_value / $transfer->currency_rate;
                                }
                            @endphp
                            <input type="number" step="0.01" name="pro_value" id="pro_value" class="form-control"
                                value="{{ old('pro_value', number_format($displayValue, 2, '.', '')) }}">
                        </div>
                    </div>

                    @if(isMultiCurrencyEnabled())
                        <div class="col-lg-3">
                            <div class="form-group">
                                <label>{{ __('vouchers.currency') }}</label>
                                <select id="currency_selector" class="form-control">
                                    <option value="">{{ __('vouchers.select_currency') }}</option>
                                    @foreach($allCurrencies as $currency)
                                        <option value="{{ $currency->id }}"
                                                data-rate="{{ $currency->latestRate->rate ?? 1 }}"
                                                data-name="{{ $currency->name }}"
                                                {{ $transfer->currency_id == $currency->id ? 'selected' : '' }}>
                                            {{ $currency->name }} ({{ number_format($currency->latestRate->rate ?? 1, 2) }})
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-lg-3">
                            <div class="form-group">
                                <label>{{ __('vouchers.converted_amount') }}</label>
                                <input type="text" id="converted_amount" readonly class="form-control bg-light"
                                    value="{{ number_format($transfer->pro_value, 2) }}" placeholder="0.00">
                            </div>
                        </div>
                    @endif

                    <div class="col-lg-{{ isMultiCurrencyEnabled() ? '3' : '6' }}">
                        <div class="form-group">
                            <label>{{ __('vouchers.description') }}</label>
                            <input type="text" name="details" class="form-control"
                                value="{{ old('details', $transfer->details ?? '') }}"
                                placeholder="{{ __('vouchers.enter_description') }}">
                        </div>
                    </div>
                </div>

                @php
                    $types = [
                        'cash_to_cash' => [__('vouchers.pro_type_cash_to_cash'), __('vouchers.pro_type_cash_to_cash')],
                        'cash_to_bank' => [__('vouchers.cash_account'), __('vouchers.banks')],
                        'bank_to_cash' => [__('vouchers.banks'), __('vouchers.cash_account')],
                        'bank_to_bank' => [__('vouchers.banks'), __('vouchers.banks')],
                    ];
                    [$acc1_text, $acc2_text] = $types[$type] ?? ['—', '—'];
                @endphp

                <div class="row">
                    <div class="col-lg-6">
                        <div class="form-group">
                            <label>{{ __('vouchers.from_account') }} ({{ $acc1_text }})</label>
                            <div class="d-flex align-items-center gap-2">
                                <select name="acc1" id="acc1" class="form-control js-tom-select flex-grow-1">
                                    <option value="">{{ __('vouchers.select_account') }}</option>
                                    @foreach ($fromAccounts as $account)
                                        <option value="{{ $account->id }}"
                                            data-balance="{{ $account->balance }}"
                                            data-currency-id="{{ $account->currency_id }}"
                                            data-currency-name="{{ $account->currency?->name ?? '' }}"
                                            {{ old('acc1', $transfer->acc1 ?? '') == $account->id ? 'selected' : '' }}>
                                            {{ $account->aname }}
                                        </option>
                                    @endforeach
                                </select>
                                @if(isMultiCurrencyEnabled())
                                    <span id="acc1_currency" class="badge bg-info" style="display:none;"></span>
                                @endif
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-6">
                        <div class="form-group">
                            <label>{{ __('vouchers.to_account') }} ({{ $acc2_text }})</label>
                            <div class="d-flex align-items-center gap-2">
                                <select name="acc2" id="acc2" class="form-control js-tom-select flex-grow-1">
                                    <option value="">{{ __('vouchers.select_account') }}</option>
                                    @foreach ($toAccounts as $account)
                                        <option value="{{ $account->id }}"
                                            data-balance="{{ $account->balance }}"
                                            data-currency-id="{{ $account->currency_id }}"
                                            data-currency-name="{{ $account->currency?->name ?? '' }}"
                                            {{ old('acc2', $transfer->acc2 ?? '') == $account->id ? 'selected' : '' }}>
                                            {{ $account->aname }}
                                        </option>
                                    @endforeach
                                </select>
                                @if(isMultiCurrencyEnabled())
                                    <span id="acc2_currency" class="badge bg-info" style="display:none;"></span>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-lg-6">
                        <div class="form-group">
                            <label>{{ __('vouchers.employee') }}</label>
                            <select name="emp_id" class="form-control">
                                <option value="">—</option>
                                @foreach ($employeeAccounts as $emp)
                                    <option value="{{ $emp->id }}" {{ old('emp_id', $transfer->emp_id ?? '') == $emp->id ? 'selected' : '' }}>
                                        {{ $emp->aname }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-lg-6">
                        <div class="form-group">
                            <label>{{ __('vouchers.collection_representative') }}</label>
                            <select name="emp2_id" class="form-control">
                                <option value="">—</option>
                                @foreach ($employeeAccounts as $emp)
                                    <option value="{{ $emp->id }}" {{ old('emp2_id', $transfer->emp2_id ?? '') == $emp->id ? 'selected' : '' }}>
                                        {{ $emp->aname }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-lg-6">
                        <div class="form-group">
                            <label>{{ __('vouchers.cost_center') }}</label>
                            <select name="cost_center" class="form-control">
                                <option value="">—</option>
                                @if(!empty($costCenters))
                                    @foreach($costCenters as $cc)
                                        <option value="{{ $cc->id }}" {{ old('cost_center', $transfer->cost_center ?? '') == $cc->id ? 'selected' : '' }}>
                                            {{ $cc->name }}
                                        </option>
                                    @endforeach
                                @endif
                            </select>
                        </div>
                    </div>
                    <div class="col-lg-6">
                        <div class="form-group">
                            <label>{{ __('vouchers.notes') }}</label>
                            <input type="text" name="info" class="form-control"
                                value="{{ old('info', $transfer->info ?? '') }}"
                                placeholder="{{ __('vouchers.enter_notes') }}">
                        </div>
                    </div>
                </div>
            </div>

            <div class="card-footer">
                <button type="submit" class="btn btn-main btn-lg">{{ __('vouchers.update') }}</button>
            </div>
        </div>
    </form>
@endsection

@push('scripts')
<script>
document.addEventListener("DOMContentLoaded", function() {
    function initTomSelect() {
        if (window.TomSelect) {
            ['acc1', 'acc2'].forEach(id => {
                const el = document.getElementById(id);
                if (el && !el.tomselect) {
                    const ts = new TomSelect(el, {
                        create: false,
                        searchField: ['text'],
                        dropdownInput: true,
                        placeholder: '{{ __('vouchers.search') }}',
                        onItemAdd: () => checkAndUpdateCurrency(),
                        onItemRemove: () => checkAndUpdateCurrency(),
                    });
                    ts.on('dropdown_open', () => {
                        const dd = el.parentElement.querySelector('.ts-dropdown');
                        if (dd) dd.style.zIndex = '99999';
                    });
                }
            });
        } else {
            setTimeout(initTomSelect, 100);
        }
    }
    initTomSelect();

    function getAccountCurrencyId(el) {
        const opt = el.tomselect
            ? el.querySelector(`option[value="${el.tomselect.getValue()}"]`)
            : el.options[el.selectedIndex];
        return opt ? String(opt.dataset.currencyId || opt.getAttribute('data-currency-id') || '') : null;
    }

    function getAccountCurrencySymbol(el) {
        const opt = el.tomselect
            ? el.querySelector(`option[value="${el.tomselect.getValue()}"]`)
            : el.options[el.selectedIndex];
        return opt ? (opt.dataset.currencyName || opt.getAttribute('data-currency-name') || '') : '';
    }

    function updateCurrencyBadges() {
        if (!{{ isMultiCurrencyEnabled() ? 'true' : 'false' }}) return;
        [['acc1', 'acc1_currency'], ['acc2', 'acc2_currency']].forEach(([id, badgeId]) => {
            const el = document.getElementById(id);
            const badge = document.getElementById(badgeId);
            if (!el || !badge) return;
            const sym = getAccountCurrencySymbol(el);
            badge.textContent = sym;
            badge.style.display = sym ? 'inline-block' : 'none';
        });
    }

    function checkAndUpdateCurrency() {
        if (!{{ isMultiCurrencyEnabled() ? 'true' : 'false' }}) {
            document.getElementById('currency_id').value = '1';
            document.getElementById('currency_rate').value = '1';
            return true;
        }
        const acc1El = document.getElementById('acc1');
        const acc2El = document.getElementById('acc2');
        updateCurrencyBadges();
        const id1 = getAccountCurrencyId(acc1El);
        const id2 = getAccountCurrencyId(acc2El);
        if (!id1 || !id2) {
            document.getElementById('currency_id').value = '1';
            document.getElementById('currency_rate').value = '1';
            return true;
        }
        if (id1 !== id2) {
            alert('{{ __('vouchers.currency_mismatch') }}');
            return false;
        }
        const rates = @json($allCurrencies->mapWithKeys(fn($c) => [$c->id => $c->latestRate->rate ?? 1]));
        document.getElementById('currency_id').value = id1;
        document.getElementById('currency_rate').value = rates[id1] || 1;
        return true;
    }

    const proValue = document.getElementById('pro_value');
    const currSel  = document.getElementById('currency_selector');
    if (proValue) proValue.addEventListener('input', calcConverted);
    if (currSel)  currSel.addEventListener('change', calcConverted);

    function calcConverted() {
        if (!{{ isMultiCurrencyEnabled() ? 'true' : 'false' }}) return;
        const sel = document.getElementById('currency_selector');
        const out = document.getElementById('converted_amount');
        if (!sel || !out) return;
        const rate = parseFloat(sel.options[sel.selectedIndex]?.dataset.rate) || 1;
        out.value = ((parseFloat(proValue.value) || 0) * rate).toFixed(2);
        document.getElementById('currency_id').value = sel.value || '1';
        document.getElementById('currency_rate').value = rate;
    }

    document.getElementById('myForm')?.addEventListener('submit', e => {
        if (!checkAndUpdateCurrency()) { e.preventDefault(); }
    });

    checkAndUpdateCurrency();
    updateCurrencyBadges();
    calcConverted();
});
</script>
@endpush
