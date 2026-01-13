@extends('admin.dashboard')

@section('sidebar')
    @include('components.sidebar.settings')
@endsection

@section('content')
    @include('components.breadcrumb', [
        'title' => __('Add Currency Exchange'),
        'items' => [
            ['label' => __('Home'), 'url' => route('admin.dashboard')],
            ['label' => __('Currency Exchange'), 'url' => route('settings.currency-exchange.index')],
            ['label' => __('Add New Operation')],
        ],
    ])

    <div class="content-wrapper">
        <section class="content">
            <form id="currencyExchangeForm" action="{{ route('settings.currency-exchange.store') }}" method="POST">
                @csrf

                <div class="card col-md-10 container">
                    <div class="card-header bg-light">
                        <h2 class="card-title">{{ __('Add Currency Exchange') }}</h2>
                    </div>

                    <div class="card-body">
                        @if ($errors->any())
                            <div class="alert alert-danger alert-dismissible fade show">
                                <ul class="mb-0">
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                            </div>
                        @endif

                        {{-- نوع العملية --}}
                        <div class="row mb-4">
                            <div class="col-12 text-center">
                                <label class="form-label fw-bold fs-3 mb-3">{{ __('Operation Type') }} <span
                                        class="text-danger">*</span></label>
                                <div class="d-flex justify-content-center gap-5">
                                    <div class="form-check d-flex align-items-center gap-2">
                                        <input class="form-check-input mt-0" type="radio" name="operation_type"
                                            id="buy" value="80" checked style="transform: scale(1.5);">
                                        <label class="form-check-label fs-4 fw-bold" for="buy"
                                            style="cursor: pointer;">
                                            <i class="las la-shopping-cart text-success fs-3"></i> {{ __('Buy Currency') }}
                                        </label>
                                    </div>
                                    <div class="form-check d-flex align-items-center gap-2">
                                        <input class="form-check-input mt-0" type="radio" name="operation_type"
                                            id="sell" value="81" style="transform: scale(1.5);">
                                        <label class="form-check-label fs-4 fw-bold" for="sell"
                                            style="cursor: pointer;">
                                            <i class="las la-hand-holding-usd text-info fs-3"></i> {{ __('Sell Currency') }}
                                        </label>
                                    </div>

                                </div>
                            </div>
                        </div>

                        <hr>

                        {{-- التاريخ ورقم السند --}}
                        <div class="row">
                            <div class="col-lg-4">
                                <label>{{ __('Date') }} <span class="text-danger">*</span></label>
                                <input type="date" name="pro_date" class="form-control"
                                    value="{{ old('pro_date', date('Y-m-d')) }}" required>
                            </div>
                            <div class="col-lg-4">
                                <label>{{ __('Bond Number (Auto)') }}</label>
                                <input type="text" id="pro_id_display" class="form-control" value="{{ $newProIdBuy }}"
                                    readonly>
                            </div>
                            <div class="col-lg-4">
                                <label>{{ __('Receipt Number') }}</label>
                                <input type="text" name="pro_num" class="form-control" value="{{ old('pro_num') }}">
                            </div>
                        </div>

                        <hr>

                        {{-- الحسابات --}}
                        <div class="row">
                            <div class="col-lg-6">
                                <label>{{ __('From Fund (Credit)') }} <span class="text-danger">*</span></label>
                                <div class="d-flex align-items-center gap-2">
                                    <select name="acc2" id="acc2" class="form-control js-tom-select" required
                                        style="flex: 1;">
                                        <option value="">{{ __('Select Fund') }}</option>
                                        @foreach ($cashAccounts as $account)
                                            <option value="{{ $account->id }}" data-balance="{{ $account->balance }}"
                                                data-currency-id="{{ $account->currency_id }}"
                                                data-currency-name="{{ $account->currency?->name ?? '' }}"
                                                {{ old('acc2') == $account->id ? 'selected' : '' }}>
                                                {{ $account->aname }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @if (isMultiCurrencyEnabled())
                                        <span id="acc2_currency" class="badge bg-info text-white px-3 py-2"
                                            style="min-width: 60px; font-size: 14px;">—</span>
                                    @endif
                                </div>
                            </div>

                            <div class="col-lg-6">
                                <label>{{ __('To Fund (Debit)') }} <span class="text-danger">*</span></label>
                                <div class="d-flex align-items-center gap-2">
                                    <select name="acc1" id="acc1" class="form-control js-tom-select" required
                                        style="flex: 1;">
                                        <option value="">{{ __('Select Fund') }}</option>
                                        @foreach ($cashAccounts as $account)
                                            <option value="{{ $account->id }}" data-balance="{{ $account->balance }}"
                                                data-currency-id="{{ $account->currency_id }}"
                                                data-currency-name="{{ $account->currency?->name ?? '' }}"
                                                {{ old('acc1') == $account->id ? 'selected' : '' }}>
                                                {{ $account->aname }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @if (isMultiCurrencyEnabled())
                                        <span id="acc1_currency" class="badge bg-success text-white px-3 py-2"
                                            style="min-width: 60px; font-size: 14px;">—</span>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <hr>

                        {{-- العملة والقيمة --}}
                        <div class="row">
                            <div class="col-lg-4">
                                <label>{{ __('Target Currency') }} <span class="text-danger">*</span></label>
                                <select name="currency_id" id="currency_id" class="form-control" required>
                                    <option value="">{{ __('Select Currency') }}</option>
                                    @foreach ($currencies as $currency)
                                        <option value="{{ $currency->id }}"
                                            data-rate="{{ $currency->latestRate->rate ?? 1 }}"
                                            {{ old('currency_id') == $currency->id ? 'selected' : '' }}>
                                            {{ $currency->name }} ({{ $currency->symbol }})
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-lg-4">
                                <label>{{ __('Exchange Rate') }} <span class="text-danger">*</span></label>
                                <input type="number" step="0.0001" name="currency_rate" id="currency_rate"
                                    class="form-control" value="{{ old('currency_rate', 1) }}" required min="0">
                            </div>

                            <div class="col-lg-4">
                                <label>{{ __('Amount (Foreign Currency)') }} <span class="text-danger">*</span></label>
                                <input type="number" step="0.01" name="pro_value" id="pro_value"
                                    class="form-control" value="{{ old('pro_value') }}" required min="0">
                            </div>
                        </div>

                        {{-- القيمة المحولة (عرض فقط) --}}
                        <div class="row mt-3">
                            <div class="col-12">
                                <div class="alert alert-success d-flex align-items-center justify-content-between">
                                    <div>
                                        <i class="las la-calculator fs-4 me-2"></i>
                                        <strong>{{ __('Converted Amount (Local Currency)') }}:</strong>
                                    </div>
                                    <h3 class="mb-0 text-success" id="converted_amount">0.00</h3>
                                </div>
                            </div>
                        </div>

                        <hr>

                        {{-- البيان --}}
                        <div class="row">
                            <div class="col-12">
                                <label>{{ __('Description') }}</label>
                                <textarea name="details" class="form-control" rows="3">{{ old('details') }}</textarea>
                            </div>
                        </div>

                        <hr>

                        {{-- مركز التكلفة والفرع --}}
                        <div class="row">
                            <div class="col-lg-6">
                                <label>{{ __('Cost Center') }}</label>
                                <select name="cost_center" class="form-control">
                                    <option value="">{{ __('No Cost Center') }}</option>
                                    @foreach ($costCenters as $cc)
                                        <option value="{{ $cc->id }}"
                                            {{ old('cost_center') == $cc->id ? 'selected' : '' }}>
                                            {{ $cc->cname }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-lg-6">
                                <x-branches::branch-select :branches="$branches" :selected="old('branch_id', session('branch_id'))" />
                            </div>
                        </div>
                    </div>

                    <div class="card-footer">
                        <div class="row">
                            <div class="col">
                                <button class="btn btn-main" type="submit">
                                    <i class="las la-save me-2"></i>{{ __('Save') }}
                                </button>
                            </div>
                            <div class="col">
                                <a href="{{ route('settings.currency-exchange.index') }}" class="btn btn-secondary">
                                    <i class="las la-times me-2"></i>{{ __('Cancel') }}
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </section>
    </div>

    @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                const proValueInput = document.getElementById('pro_value');
                const currencyRateInput = document.getElementById('currency_rate');
                const convertedAmountDisplay = document.getElementById('converted_amount');
                const currencySelect = document.getElementById('currency_id');
                const operationTypeInputs = document.querySelectorAll('input[name="operation_type"]');
                const proIdDisplay = document.getElementById('pro_id_display');

                // تحديث رقم السند عند تغيير نوع العملية
                operationTypeInputs.forEach(input => {
                    input.addEventListener('change', function() {
                        if (this.value === '80') {
                            proIdDisplay.value = '{{ $newProIdBuy }}';
                        } else {
                            proIdDisplay.value = '{{ $newProIdSell }}';
                        }
                    });
                });

                // حساب القيمة المحولة تلقائياً
                function calculateConversion() {
                    const value = parseFloat(proValueInput.value) || 0;
                    const rate = parseFloat(currencyRateInput.value) || 0;
                    const result = value * rate;
                    convertedAmountDisplay.textContent = result.toFixed(2);
                }

                // عند إدخال القيمة أو سعر الصرف
                proValueInput.addEventListener('input', calculateConversion);
                currencyRateInput.addEventListener('input', calculateConversion);

                // عند اختيار العملة، جلب سعر الصرف تلقائياً
                currencySelect.addEventListener('change', function() {
                    const selectedOption = this.options[this.selectedIndex];
                    const rate = selectedOption.getAttribute('data-rate');

                    if (rate) {
                        currencyRateInput.value = parseFloat(rate).toFixed(4);
                        calculateConversion();
                    }
                });

                // Initialize Tom Select for searchable selects
                function initTomSelect() {
                    if (window.TomSelect) {
                        const acc1Select = document.getElementById('acc1');
                        const acc2Select = document.getElementById('acc2');

                        if (acc1Select && !acc1Select.tomselect) {
                            new TomSelect(acc1Select, {
                                create: false,
                                searchField: ['text'],
                                sortField: {
                                    field: 'text',
                                    direction: 'asc'
                                },
                                dropdownInput: true,
                                placeholder: '{{ __('Search...') }}',
                                onItemAdd: function() {
                                    updateCurrencyBadges();
                                }
                            });
                        }

                        if (acc2Select && !acc2Select.tomselect) {
                            new TomSelect(acc2Select, {
                                create: false,
                                searchField: ['text'],
                                sortField: {
                                    field: 'text',
                                    direction: 'asc'
                                },
                                dropdownInput: true,
                                placeholder: '{{ __('Search...') }}',
                                onItemAdd: function() {
                                    updateCurrencyBadges();
                                }
                            });
                        }
                    } else {
                        setTimeout(initTomSelect, 100);
                    }
                }

                // Function to get currency symbol from account
                function getAccountCurrencySymbol(accountElement) {
                    if (!accountElement) return '—';

                    let selectedOption = null;

                    if (accountElement.tomselect) {
                        const selectedValue = accountElement.tomselect.getValue();
                        if (selectedValue) {
                            selectedOption = accountElement.querySelector(`option[value="${selectedValue}"]`);
                        }
                    } else {
                        const selectedIndex = accountElement.selectedIndex;
                        if (selectedIndex >= 0) {
                            selectedOption = accountElement.options[selectedIndex];
                        }
                    }

                    if (selectedOption) {
                        return selectedOption.getAttribute('data-currency-name') || '—';
                    }

                    return '—';
                }

                // Update currency badges
                function updateCurrencyBadges() {
                    const multiCurrencyEnabled = {{ isMultiCurrencyEnabled() ? 'true' : 'false' }};

                    if (!multiCurrencyEnabled) return;

                    const acc1El = document.getElementById('acc1');
                    const acc2El = document.getElementById('acc2');
                    const acc1Badge = document.getElementById('acc1_currency');
                    const acc2Badge = document.getElementById('acc2_currency');

                    if (acc1El && acc1Badge) {
                        const symbol = getAccountCurrencySymbol(acc1El);
                        acc1Badge.textContent = symbol;
                    }

                    if (acc2El && acc2Badge) {
                        const symbol = getAccountCurrencySymbol(acc2El);
                        acc2Badge.textContent = symbol;
                    }
                }

                // Initialize
                initTomSelect();
                updateCurrencyBadges();
                calculateConversion();
            });
        </script>
    @endpush
@endsection
