@extends('admin.dashboard')

@section('sidebar')
    @include('components.sidebar.settings')
@endsection

@section('content')
    @include('components.breadcrumb', [
        'title' => __('settings::settings.edit_currency'),
        'breadcrumb_items' => [
            ['label' => __('settings::settings.home'), 'url' => route('admin.dashboard')],
            ['label' => __('settings::settings.currency_management'), 'url' => route('currencies.index')],
            ['label' => __('settings::settings.edit_currency')],
        ],
    ])

    @push('styles')
        <style>
            /* تكبير الـ dropdown */
            .select2-container--bootstrap-5 .select2-dropdown {
                font-size: 1rem;
            }

            /* تحسين عرض النتائج */
            .select2-container--bootstrap-5 .select2-results__option {
                padding: 10px 12px;
            }

            /* Highlight عند الـ hover */
            .select2-container--bootstrap-5 .select2-results__option--highlighted {
                background-color: #0d6efd;
                color: white;
            }

            /* عرض الـ search box */
            .select2-container--bootstrap-5 .select2-search__field {
                padding: 8px 12px;
                font-size: 0.95rem;
            }
        </style>
    @endpush

    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-body">
                    <form action="{{ route('currencies.update', $currency->id) }}" method="POST" id="currency-form">
                        @csrf
                        @method('PUT')

                        <div class="row">
                            <!-- Search & Select Currency -->
                            <div class="col-md-3 mb-5">
                                <label class="form-label required">{{ __('settings::settings.select_currency') }}</label>
                                <select name="code" id="currency-select"
                                    class="form-select @error('code') is-invalid @enderror" required>
                                    <option value="{{ $currency->code }}" selected>
                                        {{ $currency->code }} - {{ $currency->name }}
                                    </option>
                                </select>
                                @error('code')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <small class="text-muted">{{ __('settings::settings.search_select_currency_hint') }}</small>
                            </div>

                            <!-- Currency Name (Auto-filled) -->
                            <div class="col-md-3 mb-5">
                                <label class="form-label required">{{ __('settings::settings.currency_name') }}</label>
                                <input type="text" name="name" id="currency-name"
                                    class="form-control @error('name') is-invalid @enderror"
                                    value="{{ old('name', $currency->name) }}" placeholder="{{ __('settings::settings.currency_name') }}"
                                    readonly required>
                                @error('name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Currency Symbol (Auto-filled) -->
                            <div class="col-md-3 mb-5">
                                <label class="form-label">{{ __('settings::settings.currency_symbol') }}</label>
                                <input type="text" name="symbol" id="currency-symbol"
                                    class="form-control @error('symbol') is-invalid @enderror"
                                    value="{{ old('symbol', $currency->symbol) }}"
                                    placeholder="{{ __('settings::settings.example_currency_symbol') }}">
                                @error('symbol')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Decimal Places -->
                            <div class="col-md-3 mb-5">
                                <label class="form-label required">{{ __('settings::settings.decimal_places') }}</label>
                                <select name="decimal_places"
                                    class="form-select @error('decimal_places') is-invalid @enderror" required>
                                    <option value="0"
                                        {{ old('decimal_places', $currency->decimal_places) == 0 ? 'selected' : '' }}>0
                                    </option>
                                    <option value="2"
                                        {{ old('decimal_places', $currency->decimal_places) == 2 ? 'selected' : '' }}>2
                                    </option>
                                    <option value="3"
                                        {{ old('decimal_places', $currency->decimal_places) == 3 ? 'selected' : '' }}>3
                                    </option>
                                    <option value="4"
                                        {{ old('decimal_places', $currency->decimal_places) == 4 ? 'selected' : '' }}>4
                                    </option>
                                </select>
                                @error('decimal_places')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <small class="text-muted">{{ __('settings::settings.decimal_places_hint') }}</small>
                            </div>
                        </div>

                        <div class="row">
                            <!-- Rate Mode -->
                            <div class="col-md-3 mb-5">
                                <label class="form-label required">{{ __('settings::settings.rate_mode') }}</label>
                                <select name="rate_mode" id="rate_mode"
                                    class="form-select @error('rate_mode') is-invalid @enderror" required>
                                    <option value="automatic"
                                        {{ old('rate_mode', $currency->rate_mode) == 'automatic' ? 'selected' : '' }}>
                                        {{ __('settings::settings.automatic_api') }}
                                    </option>
                                    <option value="manual"
                                        {{ old('rate_mode', $currency->rate_mode) == 'manual' ? 'selected' : '' }}>
                                        {{ __('settings::settings.manual') }}
                                    </option>
                                </select>
                                @error('rate_mode')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Current Rate (Read-only Info) -->
                            <div class="col-md-3 mb-5">
                                <label class="form-label">{{ __('settings::settings.current_rate') }}</label>
                                <input type="text" class="form-control"
                                    value="{{ $currency->latestRate ? number_format($currency->latestRate->rate, $currency->decimal_places) : '-' }}"
                                    readonly>
                                <small class="text-muted">
                                    @if ($currency->latestRate)
                                        {{ __('settings::settings.last_update') }}: {{ $currency->latestRate->rate_date->format('Y-m-d') }}
                                    @else
                                        {{ __('settings::settings.no_rate_set_yet') }}
                                    @endif
                                </small>
                            </div>

                            <!-- Is Default -->
                            <div class="col-md-3 mb-5">
                                <div class="form-check form-switch mt-4">
                                    <input class="form-check-input" type="checkbox" name="is_default" id="is_default"
                                        value="1" {{ old('is_default', $currency->is_default) ? 'checked' : '' }}
                                        {{ $currency->is_default ? 'disabled' : '' }}>
                                    <label class="form-check-label" for="is_default">
                                        {{ __('settings::settings.set_as_default_currency') }}
                                    </label>
                                </div>
                                @if ($currency->is_default)
                                    <input type="hidden" name="is_default" value="1">
                                    <small class="text-info">{{ __('settings::settings.the_default_currency_for_system') }}</small>
                                @endif
                            </div>

                            <!-- Is Active -->
                            <div class="col-md-3 mb-5">
                                <div class="form-check form-switch mt-4">
                                    <input class="form-check-input" type="checkbox" name="is_active" id="is_active"
                                        value="1" {{ old('is_active', $currency->is_active) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="is_active">
                                        {{ __('settings::settings.is_active') }}
                                    </label>
                                </div>
                            </div>
                        </div>

                        <div class="d-flex justify-content-end gap-2 mt-4">
                            <a href="{{ route('currencies.index') }}" class="btn btn-secondary">
                                {{ __('settings::settings.cancel') }}
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i> {{ __('settings::settings.save') }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', function() {

                // ========== Load Available Currencies ==========
                fetch('/currencies/available')
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            const select = document.getElementById('currency-select');
                            const currentCode = '{{ $currency->code }}';
                            const currentName = '{{ $currency->name }}';
                            const currentSymbol = '{{ $currency->symbol }}';

                            select.innerHTML = '';

                            data.currencies.forEach(currency => {
                                const option = document.createElement('option');
                                option.value = currency.code;
                                option.textContent = `${currency.code} - ${currency.name}`;
                                option.dataset.name = currency.name;
                                option.dataset.symbol = currency.symbol;

                                // ✅ حدد العملة الحالية
                                if (currency.code === currentCode) {
                                    option.selected = true;
                                }

                                select.appendChild(option);
                            });

                            // ✅ تفعيل Select2 بعد تحميل البيانات
                            $('#currency-select').select2({
                                theme: 'bootstrap-5',
                                width: '100%',
                                placeholder: '{{ __('settings::settings.search_select_currency_hint') }}',
                                allowClear: true,
                                language: {
                                    noResults: function() {
                                        return "{{ __('settings::settings.no_currencies_added_yet') }}";
                                    },
                                    searching: function() {
                                        return "{{ __('settings::settings.loading') }}";
                                    }
                                }
                            });

                            // ✅ Event عند الاختيار
                            $('#currency-select').on('select2:select', function(e) {
                                const option = $(this).find('option:selected');

                                document.getElementById('currency-name').value = option.data('name') || '';
                                document.getElementById('currency-symbol').value = option.data('symbol') ||
                                    '';
                            });

                        } else {
                            console.error('Failed to load currencies:', data.message);
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                    });
            });
        </script>
    @endpush
@endsection
