@extends('admin.dashboard')

@section('sidebar')
    @include('components.sidebar.settings')
@endsection

@section('content')
    @include('components.breadcrumb', [
        'title' => __('settings::settings.currency_management'),
        'breadcrumb_items' => [
            ['label' => __('settings::settings.home'), 'url' => route('admin.dashboard')],
            ['label' => __('settings::settings.currency_management')],
        ],
    ])

    <div class="row">
        <div class="col-lg-12">
            @can('create Currencies')
                <a href="{{ route('currencies.create') }}" type="button" class="btn btn-main font-hold fw-bold">
                    {{ __('settings::settings.add_new_currency') }}
                    <i class="fas fa-plus me-2"></i>
                </a>
            @endcan
            <br>
            <br>

            @if (session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <i class="fas fa-check-circle me-2"></i>
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            @if (session('error'))
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <i class="fas fa-exclamation-circle me-2"></i>
                    {{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            <div class="card">
                <div class="card-body">
                    <div class="table-responsive" style="overflow-x: auto;">

                        <x-table-export-actions table-id="currencies-table" filename="currencies-table"
                            excel-label="{{ __('settings::settings.export_excel') }}" pdf-label="{{ __('settings::settings.export_pdf') }}" print-label="{{ __('settings::settings.print') }}" />

                        <table id="currencies-table" class="table table-striped mb-0" style="min-width: 1400px;">
                            <thead class="table-light text-center align-middle">
                                <tr>
                                    <th>#</th>
                                    <th>{{ __('settings::settings.currency_name') }}</th>
                                    <th>{{ __('settings::settings.currency_code') }}</th>
                                    <th>{{ __('settings::settings.currency_symbol') }}</th>
                                    <th>{{ __('settings::settings.decimal_places') }}</th>
                                    <th>{{ __('settings::settings.rate_mode') }}</th>
                                    <th>{{ __('settings::settings.exchange_rate') }}</th>
                                    <th>{{ __('settings::settings.activation_status') }}</th>
                                    @canany(['edit Currencies', 'delete Currencies'])
                                        <th>{{ __('settings::settings.actions') }}</th>
                                    @endcanany
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($currencies as $currency)
                                    <tr class="text-center align-middle">
                                        <td>{{ $loop->iteration }}</td>
                                        <td>
                                            {{ $currency->name }}
                                            @if ($currency->is_default)
                                                <span class="badge bg-success ms-2">{{ __('settings::settings.default_currency') }}</span>
                                            @endif
                                        </td>
                                        <td><span class="badge bg-primary">{{ $currency->code }}</span></td>
                                        <td>{{ $currency->symbol ?? '-' }}</td>
                                        <td>{{ $currency->decimal_places }}</td>
                                        <td>
                                            @if ($currency->is_default)
                                                <span class="text-muted">-</span>
                                            @else
                                                <div class="form-check form-switch d-flex justify-content-center">
                                                    <input class="form-check-input rate-mode-switch" type="checkbox"
                                                        role="switch" id="rate_mode_{{ $currency->id }}"
                                                        data-currency-id="{{ $currency->id }}"
                                                        {{ $currency->rate_mode === 'automatic' ? 'checked' : '' }}
                                                        style="cursor: pointer;">
                                                    <label class="form-check-label ms-2"
                                                        for="rate_mode_{{ $currency->id }}" style="cursor: pointer;">
                                                        <span class="mode-text-{{ $currency->id }}">
                                                            {{ $currency->rate_mode === 'automatic' ? __('settings::settings.automatic_api') : __('settings::settings.manual') }}
                                                        </span>
                                                    </label>
                                                </div>
                                            @endif
                                        </td>
                                        <td>
                                            @if ($currency->is_default)
                                                <span class="text-muted">-</span>
                                            @else
                                                <div class="rate-container-{{ $currency->id }}">
                                                    @if ($currency->rate_mode === 'automatic')
                                                        <!-- Automatic Mode -->
                                                        <div class="d-flex align-items-center justify-content-center gap-2">
                                                            <span
                                                                class="rate-display-{{ $currency->id }} fw-bold text-success"
                                                                data-decimal-places="{{ $currency->decimal_places }}">
                                                                {{ $currency->latestRate ? number_format($currency->latestRate->rate, $currency->decimal_places) : '-' }}
                                                            </span>

                                                            <button type="button"
                                                                class="btn btn-sm btn-primary fetch-rate-btn"
                                                                data-currency-id="{{ $currency->id }}">
                                                                <i class="fas fa-sync-alt"></i>
                                                            </button>
                                                        </div>
                                                    @else
                                                        <!-- Manual Mode -->
                                                        <div class="d-flex align-items-center justify-content-center gap-2">
                                                            <input type="number"
                                                                class="form-control form-control-sm manual-rate-input"
                                                                style="max-width: 150px;" step="0.00000001"
                                                                value="{{ $currency->latestRate?->rate ?? '' }}"
                                                                placeholder="{{ __('settings::settings.enter_rate') }}"
                                                                data-currency-id="{{ $currency->id }}">
                                                            <button type="button"
                                                                class="btn btn-sm btn-success save-manual-rate"
                                                                data-currency-id="{{ $currency->id }}">
                                                                <i class="fas fa-save"></i>
                                                            </button>
                                                        </div>
                                                    @endif

                                                    @if ($currency->latestRate)
                                                        <small class="text-muted d-block mt-1">
                                                            {{ __('settings::settings.last_update') }}:
                                                            {{ $currency->latestRate->rate_date->format('Y-m-d') }}
                                                        </small>
                                                    @endif
                                                </div>
                                            @endif
                                        </td>
                                        <td>
                                            @if ($currency->is_active)
                                                <span class="badge bg-success">{{ __('settings::settings.is_active') }}</span>
                                            @else
                                                <span class="badge bg-danger">{{ __('settings::settings.inactive') }}</span>
                                            @endif
                                        </td>
                                        @canany(['edit Currencies', 'delete Currencies'])
                                            <td>
                                                @can('edit Currencies')
                                                    <a class="btn btn-success btn-icon-square-sm"
                                                        href="{{ route('currencies.edit', $currency->id) }}">
                                                        <i class="las la-edit"></i>
                                                    </a>
                                                @endcan

                                                @can('delete Currencies')
                                                    @unless ($currency->is_default)
                                                        <form action="{{ route('currencies.destroy', $currency->id) }}" method="POST"
                                                            style="display:inline-block;"
                                                            onsubmit="return confirm('{{ __('settings::settings.confirm_delete') }}');">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="submit" class="btn btn-danger btn-icon-square-sm">
                                                                <i class="las la-trash"></i>
                                                            </button>
                                                        </form>
                                                    @endunless
                                                @endcan
                                            </td>
                                        @endcanany
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="9" class="text-center">
                                            <div class="alert alert-info py-3 mb-0"
                                                style="font-size: 1.2rem; font-weight: 500;">
                                                <i class="las la-info-circle me-2"></i>
                                                {{ __('settings::settings.no_currencies_added_yet') }}
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

    @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', function() {

                // ========== Switch Rate Mode (Manual/Automatic) ==========
                document.querySelectorAll('.rate-mode-switch').forEach(switchBtn => {
                    switchBtn.addEventListener('change', function() {
                        const currencyId = this.dataset.currencyId;
                        const isAutomatic = this.checked;
                        const mode = isAutomatic ? 'automatic' : 'manual';

                        // Show loading
                        const modeText = document.querySelector(`.mode-text-${currencyId}`);
                        const originalText = modeText.textContent;
                        modeText.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';

                        // Update in database via AJAX
                        fetch(`/currencies/${currencyId}/update-mode`, {
                                method: 'POST',
                                headers: {
                                    'Content-Type': 'application/json',
                                    'Accept': 'application/json',
                                    'X-Requested-With': 'XMLHttpRequest',
                                    'X-CSRF-TOKEN': document.querySelector(
                                        'meta[name="csrf-token"]').content
                                },
                                body: JSON.stringify({
                                    rate_mode: mode
                                })
                            })
                            .then(response => {
                                const contentType = response.headers.get('content-type');
                                if (contentType && contentType.includes('text/html')) {
                                    throw new Error('{{ __("Session expired. Please refresh the page.") }}');
                                }
                                return response.json();
                            })
                            .then(data => {
                                if (data.success) {
                                    // Update label text
                                    modeText.textContent = isAutomatic ?
                                        '{{ __('settings::settings.automatic_api') }}' : '{{ __('settings::settings.manual') }}';

                                    // Reload page to update rate input UI
                                    setTimeout(() => location.reload(), 500);
                                } else {
                                    throw new Error(data.message);
                                }
                            })
                            .catch(error => {
                                modeText.textContent = originalText;
                                this.checked = !this.checked; // Revert switch

                                Swal.fire({
                                    icon: 'error',
                                    title: '{{ __('settings::settings.error') }}',
                                    text: error.message ||
                                        '{{ __('settings::settings.rate_mode_updated_successfully') }}'
                                });
                            });
                    });
                });

                // ========== Fetch Live Rate (Automatic) ==========
                // ========== Fetch Live Rate (Automatic) ==========
                document.querySelectorAll('.fetch-rate-btn').forEach(btn => {
                    btn.addEventListener('click', function() {
                        const currencyId = this.dataset.currencyId;
                        const btnElement = this;
                        const originalHtml = btnElement.innerHTML;

                        btnElement.disabled = true;
                        btnElement.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';

                        fetch(`/currencies/${currencyId}/fetch-live-rate`, {
                                method: 'POST',
                                headers: {
                                    'Content-Type': 'application/json',
                                    'Accept': 'application/json',
                                    'X-Requested-With': 'XMLHttpRequest',
                                    'X-CSRF-TOKEN': document.querySelector(
                                        'meta[name="csrf-token"]').content
                                }
                            })
                            .then(response => {
                                const contentType = response.headers.get('content-type');
                                if (contentType && contentType.includes('text/html')) {
                                    throw new Error('{{ __("settings::settings.session_expired_refresh") }}');
                                }
                                if (!response.ok) {
                                    return response.json().then(err => {
                                        throw new Error(err.message || '{{ __("settings::settings.download_failed") }}');
                                    });
                                }
                                return response.json();
                            })
                            .then(data => {
                                if (data.success) {
                                    const rateDisplay = document.querySelector(
                                        `.rate-display-${currencyId}`);
                                    const decimalPlaces = parseInt(rateDisplay.dataset
                                        .decimalPlaces) || 2;

                                    const formattedRate = parseFloat(data.rate_raw).toLocaleString(
                                        'en-US', {
                                            minimumFractionDigits: decimalPlaces,
                                            maximumFractionDigits: decimalPlaces
                                        });

                                    rateDisplay.textContent = formattedRate;

                                    Swal.fire({
                                        icon: 'success',
                                        title: '{{ __('settings::settings.success') }}',
                                        text: data.message,
                                        timer: 2000,
                                        showConfirmButton: false
                                    });
                                } else {
                                    throw new Error(data.message);
                                }
                            })
                            .catch(error => {
                                Swal.fire({
                                    icon: 'error',
                                    title: '{{ __('settings::settings.error') }}',
                                    text: error.message ||
                                        '{{ __('settings::settings.failed_to_fetch_rate_from_api') }}'
                                });
                            })
                            .finally(() => {
                                btnElement.disabled = false;
                                btnElement.innerHTML = originalHtml;
                            });
                    });
                });

                // ========== Save Manual Rate ==========
                document.querySelectorAll('.save-manual-rate').forEach(btn => {
                    btn.addEventListener('click', function() {
                        const currencyId = this.dataset.currencyId;
                        const input = document.querySelector(
                            `.manual-rate-input[data-currency-id="${currencyId}"]`);
                        const rate = input.value;

                        if (!rate || rate <= 0) {
                            Swal.fire({
                                icon: 'warning',
                                title: '{{ __('settings::settings.warning') }}',
                                text: '{{ __('settings::settings.please_enter_valid_rate') }}'
                            });
                            return;
                        }

                        const btnElement = this;
                        const originalHtml = btnElement.innerHTML;

                        btnElement.disabled = true;
                        btnElement.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';

                        fetch(`/currencies/${currencyId}/update-rate`, {
                                method: 'POST',
                                headers: {
                                    'Content-Type': 'application/json',
                                    'Accept': 'application/json',
                                    'X-Requested-With': 'XMLHttpRequest',
                                    'X-CSRF-TOKEN': document.querySelector(
                                        'meta[name="csrf-token"]').content
                                },
                                body: JSON.stringify({
                                    rate: rate
                                })
                            })
                            .then(response => {
                                const contentType = response.headers.get('content-type');
                                if (contentType && contentType.includes('text/html')) {
                                    throw new Error('{{ __("settings::settings.session_expired_refresh") }}');
                                }
                                return response.json();
                            })
                            .then(data => {
                                if (data.success) {
                                    Swal.fire({
                                        icon: 'success',
                                        title: '{{ __('settings::settings.success') }}',
                                        text: data.message,
                                        timer: 2000,
                                        showConfirmButton: false
                                    });
                                    input.value = data.rate_raw;
                                } else {
                                    throw new Error(data.message);
                                }
                            })
                            .catch(error => {
                                Swal.fire({
                                    icon: 'error',
                                    title: '{{ __('settings::settings.error') }}',
                                    text: error.message ||
                                        '{{ __('settings::settings.failed_to_save_rate') }}'
                                });
                            })
                            .finally(() => {
                                btnElement.disabled = false;
                                btnElement.innerHTML = originalHtml;
                            });
                    });
                });

            });
        </script>
    @endpush
@endsection
