@extends('admin.dashboard')

{{-- Dynamic Sidebar --}}
@section('sidebar')
    @include('components.sidebar.reports')
@endsection

@section('content')
    <div id="balance-sheet-report" class="container-fluid py-4">
        <!-- Header Section -->
        <div class="card border-0 bg-transparent mb-4">
            <div class="card-body p-0">
                <div class="d-flex flex-column flex-md-row justify-content-between align-items-center gap-3">
                    <div class="d-flex align-items-center">
                        <div class="bg-primary bg-opacity-10 p-3 rounded-circle me-3">
                            <i class="las la-balance-scale text-primary fs-2"></i>
                        </div>
                        <div>
                            <h2 class="mb-0 fw-bold text-dark">{{ __('reports::reports.balance_sheet') }}</h2>
                            <p class="text-muted mb-0">
                                <i class="las la-calendar me-1"></i>{{ __('reports::reports.until_date') }}:
                                <span class="fw-semibold text-primary">{{ \Carbon\Carbon::parse($asOfDate)->format('d/m/Y') }}</span>
                            </p>
                        </div>
                    </div>
                    <div class="d-flex gap-2 flex-wrap justify-content-center">
                        <button class="btn btn-white shadow-sm px-3 hover-lift" onclick="expandAll()" title="{{ __('reports::reports.expand_all') }}">
                            <i class="las la-plus-circle me-1 text-primary"></i>{{ __('reports::reports.expand_all') }}
                        </button>
                        <button class="btn btn-white shadow-sm px-3 hover-lift" onclick="collapseAll()" title="{{ __('reports::reports.collapse_all') }}">
                            <i class="las la-minus-circle me-1 text-secondary"></i>{{ __('reports::reports.collapse_all') }}
                        </button>
                        <button class="btn btn-white shadow-sm px-3 hover-lift" onclick="compareBalances()"
                            title="{{ __('reports::reports.compare_balances') }}" id="compareBtn">
                            <i class="las la-sync-alt me-1 text-info"></i>{{ __('reports::reports.compare_balances') }}
                        </button>
                        <button class="btn btn-primary shadow px-4 hover-lift" onclick="window.print()" title="{{ __('reports::reports.print') }}">
                            <i class="las la-print me-1"></i>{{ __('reports::reports.print') }}
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Filters (Optional - can add a date picker here if needed) -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="card border-0 shadow-sm glass-card">
                    <div class="card-body">
                        <form action="{{ route('reports.general-balance-sheet') }}" method="GET" class="row g-3 align-items-end">
                            <div class="col-md-4">
                                <label class="form-label text-muted small fw-bold">{{ __('reports::reports.as_of_date') }}</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light border-0"><i class="las la-calendar"></i></span>
                                    <input type="date" name="as_of_date" class="form-control border-0 bg-light" value="{{ $asOfDate }}">
                                </div>
                            </div>
                            <div class="col-md-2">
                                <button type="submit" class="btn btn-secondary w-100 border-0 shadow-sm">
                                    <i class="las la-filter me-1"></i>{{ __('reports::reports.filter') }}
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
                <!-- Statistics Cards (Premium Look) -->
                <div class="row g-4 mb-4">
                    <div class="col-xl-3 col-md-6">
                        <div class="card border-0 shadow-sm premium-card bg-gradient-primary h-100 overflow-hidden">
                            <div class="card-body p-4 position-relative">
                                <div class="icon-shape bg-white bg-opacity-20 rounded-pill position-absolute end-0 top-0 mt-3 me-3">
                                    <i class="las la-wallet text-white fs-2"></i>
                                </div>
                                <h6 class="text-white text-opacity-75 text-uppercase fw-bold mb-2 small ls-2">{{ __('reports::reports.assets') }}</h6>
                                <h3 class="text-white fw-bold mb-0" id="total-assets-summary">{{ number_format($totalAssets, 2) }}</h3>
                                <div class="mt-3 d-flex align-items-center">
                                    <span class="text-white text-opacity-75 small">
                                        <i class="las la-info-circle me-1"></i>{{ __('reports::reports.current_balance') }}
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-xl-3 col-md-6">
                        <div class="card border-0 shadow-sm premium-card bg-gradient-info h-100 overflow-hidden">
                            <div class="card-body p-4 position-relative">
                                <div class="icon-shape bg-white bg-opacity-20 rounded-pill position-absolute end-0 top-0 mt-3 me-3">
                                    <i class="las la-handshake text-white fs-2"></i>
                                </div>
                                <h6 class="text-white text-opacity-75 text-uppercase fw-bold mb-2 small ls-2">{{ __('reports::reports.liabilities') }}</h6>
                                <h3 class="text-white fw-bold mb-0" id="total-liabilities-summary">{{ number_format($totalLiabilities, 2) }}</h3>
                                <div class="mt-3">
                                    <span class="text-white text-opacity-75 small">{{ __('reports::reports.total_liabilities') }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-xl-3 col-md-6">
                        <div class="card border-0 shadow-sm premium-card bg-gradient-success h-100 overflow-hidden">
                            <div class="card-body p-4 position-relative">
                                <div class="icon-shape bg-white bg-opacity-20 rounded-pill position-absolute end-0 top-0 mt-3 me-3">
                                    <i class="las la-users text-white fs-2"></i>
                                </div>
                                <h6 class="text-white text-opacity-75 text-uppercase fw-bold mb-2 small ls-2">{{ __('reports::reports.equity') }}</h6>
                                <h3 class="text-white fw-bold mb-0" id="total-equity-summary">{{ number_format($totalEquity, 2) }}</h3>
                                <div class="mt-3">
                                    <span class="text-white text-opacity-75 small">{{ __('reports::reports.total_equity') }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-xl-3 col-md-6">
                        <div class="card border-0 shadow-sm premium-card bg-gradient-{{ $netProfit >= 0 ? 'indigo' : 'danger' }} h-100 overflow-hidden">
                            <div class="card-body p-4 position-relative">
                                <div class="icon-shape bg-white bg-opacity-20 rounded-pill position-absolute end-0 top-0 mt-3 me-3">
                                    <i class="las la-chart-{{ $netProfit >= 0 ? 'line' : 'bar' }} text-white fs-2"></i>
                                </div>
                                <h6 class="text-white text-opacity-75 text-uppercase fw-bold mb-2 small ls-2">{{ __('reports::reports.net_profit/Loss') }}</h6>
                                <h3 class="text-white fw-bold mb-0">{{ number_format($netProfit, 2) }}</h3>
                                <div class="mt-3">
                                    <span class="text-white text-opacity-75 small">
                                        {{ $netProfit >= 0 ? __('reports::reports.result_profit') : __('reports::reports.result_loss') }}
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Equation Visualization -->
                <div class="row mb-5">
                    <div class="col-12">
                        <div class="card border-0 shadow-sm glass-card overflow-hidden">
                            <div class="card-body p-4">
                                <div class="d-flex justify-content-between align-items-center mb-3">
                                    <h5 class="mb-0 fw-bold text-dark">
                                        <i class="las la-balance-scale-left me-2 text-primary"></i>{{ __('reports::reports.balance_sheet_equation') }}
                                    </h5>
                                    <div id="balance-status-badge" class="{{ $isBalanced ? 'text-success' : 'text-danger' }} fw-bold">
                                        {{ $isBalanced ? __('reports::reports.balance_sheet_is_balanced') : __('reports::reports.balance_sheet_is_unbalanced') }}
                                    </div>
                                </div>
                                @php
                                    $maxVal = max($totalAssets, $totalLiabilitiesEquity);
                                    $assetPercent = $maxVal > 0 ? ($totalAssets / $maxVal) * 100 : 0;
                                    $liabEqPercent = $maxVal > 0 ? ($totalLiabilitiesEquity / $maxVal) * 100 : 0;
                                @endphp
                                <div class="row align-items-center g-4">
                                    <div class="col-md-5 text-center">
                                        <div class="small text-muted mb-1">{{ __('reports::reports.total_assets') }}</div>
                                        <h4 class="fw-bold text-primary mb-0" id="total-assets-vis">{{ number_format($totalAssets, 2) }}</h4>
                                    </div>
                                    <div class="col-md-2 text-center text-muted fs-2">
                                        <i class="las la-equals"></i>
                                    </div>
                                    <div class="col-md-5 text-center">
                                        <div class="small text-muted mb-1">{{ __('reports::reports.total_liabilities_plus_equity') }}</div>
                                        <h4 class="fw-bold text-info mb-0" id="total-liab-eq-vis">{{ number_format($totalLiabilitiesEquity, 2) }}</h4>
                                    </div>
                                    <div class="col-12">
                                        <div class="progress shadow-sm overflow-visible" style="height: 12px; background-color: #f1f3f5;">
                                            <div id="assets-progress" class="progress-bar bg-primary rounded-pill position-relative overflow-visible" role="progressbar" style="width: {{ $assetPercent }}%" aria-valuenow="{{ $assetPercent }}" aria-valuemin="0" aria-valuemax="100">
                                                <div class="marker bg-primary position-absolute top-100 start-100 translate-middle-x mt-1 rounded-pill px-2 small text-white">{{ number_format($assetPercent, 0) }}%</div>
                                            </div>
                                            <div id="liab-eq-progress" class="progress-bar bg-info rounded-pill opacity-75 position-relative overflow-visible ms-auto" role="progressbar" style="width: {{ $liabEqPercent }}%" aria-valuenow="{{ $liabEqPercent }}" aria-valuemin="0" aria-valuemax="100">
                                                <div class="marker bg-info position-absolute top-100 start-0 translate-middle-x mt-1 rounded-pill px-2 small text-white">{{ number_format($liabEqPercent, 0) }}%</div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row g-4">
                    <!-- Assets Column -->
                    <div class="col-lg-6">
                        <div class="card border-0 shadow-sm h-100 overflow-visible">
                            <div class="card-header bg-white border-0 py-4 ps-4">
                                <h4 class="mb-0 fw-bold text-dark d-flex align-items-center">
                                    <div class="bg-primary bg-opacity-10 p-2 rounded-3 me-3">
                                        <i class="las la-wallet text-primary fs-3"></i>
                                    </div>
                                    {{ __('reports::reports.assets') }}
                                </h4>
                            </div>
                            <div class="card-body p-0">
                                <div class="table-responsive">
                                    <table class="table table-hover mb-0 align-middle luxury-table">
                                        <thead class="bg-light bg-opacity-50">
                                            <tr>
                                                <th class="py-3 border-0 ps-4 text-muted small text-uppercase fw-bold ls-1">{{ __('reports::reports.account') }}</th>
                                                <th class="py-3 border-0 pe-4 text-end text-muted small text-uppercase fw-bold ls-1">{{ __('reports::reports.amount') }}</th>
                                            </tr>
                                        </thead>
                                        <tbody id="assets-tbody">
                                            @foreach ($assets as $asset)
                                                @include(
                                                    'reports::accounts-reports.partials.account-row-recursive',
                                                    ['account' => $asset, 'level' => 0, 'section' => 'assets']
                                                )
                                            @endforeach
                                        </tbody>
                                        <tfoot class="border-top-0">
                                            <tr class="table-primary border-0 bg-gradient-primary">
                                                <th class="py-4 ps-4 fs-5 fw-bold text-white">{{ __('reports::reports.total_assets') }}</th>
                                                <th class="py-4 pe-4 text-end fs-5 fw-bold text-white" id="total-assets-display">
                                                    {{ number_format($totalAssets, 2) }}</th>
                                            </tr>
                                        </tfoot>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Liabilities Column -->
                    <div class="col-lg-6">
                        <div class="card border-0 shadow-sm h-100 overflow-visible">
                            <div class="card-header bg-white border-0 py-4 ps-4">
                                <h4 class="mb-0 fw-bold text-dark d-flex align-items-center">
                                    <div class="bg-info bg-opacity-10 p-2 rounded-3 me-3">
                                        <i class="las la-hand-holding-usd text-info fs-3"></i>
                                    </div>
                                    {{ __('reports::reports.liabilities_and_equity') }}
                                </h4>
                            </div>
                            <div class="card-body p-0">
                                <div class="table-responsive">
                                    <table class="table table-hover mb-0 align-middle luxury-table">
                                        <thead class="bg-light bg-opacity-50">
                                            <tr>
                                                <th class="py-3 border-0 ps-4 text-muted small text-uppercase fw-bold ls-1">{{ __('reports::reports.account') }}</th>
                                                <th class="py-3 border-0 pe-4 text-end text-muted small text-uppercase fw-bold ls-1">{{ __('reports::reports.amount') }}</th>
                                            </tr>
                                        </thead>
                                        <tbody id="liabilities-equity-tbody">
                                            @foreach ($liabilities as $liability)
                                                @include(
                                                    'reports::accounts-reports.partials.account-row-recursive',
                                                    ['account' => $liability, 'level' => 0, 'section' => 'liabilities']
                                                )
                                            @endforeach

                                            @foreach ($equity as $eq)
                                                @include(
                                                    'reports::accounts-reports.partials.account-row-recursive',
                                                    ['account' => $eq, 'level' => 0, 'section' => 'equity']
                                                )
                                            @endforeach

                                            <tr class="bg-soft-indigo border-top-0">
                                                <th class="py-4 ps-4 fw-bold">
                                                    <div class="d-flex align-items-center">
                                                        <div class="bg-indigo bg-opacity-10 p-2 rounded-circle me-3">
                                                            <i class="las la-chart-pie text-indigo fs-5"></i>
                                                        </div>
                                                        <span class="text-indigo">{{ __('reports::reports.net_profit/Loss') }}</span>
                                                    </div>
                                                </th>
                                                <th class="py-4 pe-4 text-end fs-5 {{ $netProfit >= 0 ? 'text-success' : 'text-danger' }} fw-bold">
                                                    {{ number_format($netProfit, 2) }}
                                                </th>
                                            </tr>
                                        </tbody>
                                        <tfoot class="border-top-0">
                                            <tr class="table-info border-0 bg-gradient-info">
                                                <th class="py-4 ps-4 fs-5 fw-bold text-white">{{ __('reports::reports.total_liabilities_and_equity') }}</th>
                                                <th class="py-4 pe-4 text-end fs-5 fw-bold text-white" id="total-liabilities-equity-display">
                                                    {{ number_format($totalLiabilitiesEquity, 2) }}</th>
                                            </tr>
                                        </tfoot>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Result Summary Alert -->
                <div class="row mt-4">
                    <div class="col-12">
                        <div id="balance-result-alert"
                            class="alert {{ $isBalanced ? 'alert-soft-success' : 'alert-soft-warning' }} border-0 shadow-sm animate__animated animate__fadeInUp">
                            <div class="d-flex align-items-center">
                                <div class="p-3 rounded-circle bg-white shadow-sm me-3">
                                    @if ($isBalanced)
                                        <i class="las la-check-circle text-success fs-2"></i>
                                    @else
                                        <i class="las la-exclamation-triangle text-warning fs-2"></i>
                                    @endif
                                </div>
                                <div class="flex-grow-1">
                                    <h5 class="mb-1 fw-bold text-dark">{{ __('reports::reports.report_summary') }}</h5>
                                    @if ($isBalanced)
                                        <p class="mb-0 text-success fw-semibold">{{ __('reports::reports.balance_sheet_is_balanced') }} ✓</p>
                                    @else
                                        <p class="mb-0 text-dark">
                                            <span class="fw-bold text-warning">{{ __('reports::reports.balance_sheet_is_unbalanced') }}</span> -
                                            {{ __('reports::reports.difference:') }}
                                            <span class="badge bg-danger rounded-pill px-3 ms-2 shadow-sm"
                                                id="balance-difference-badge">{{ number_format($difference, 2) }}</span>
                                        </p>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                </div>
        </div>
    </div>

    <!-- Compare Balances Modal -->
    <div class="modal fade" id="compareBalancesModal" tabindex="-1" aria-labelledby="compareBalancesModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-xl modal-dialog-centered">
            <div class="modal-content border-0 shadow-lg">
                <div class="modal-header bg-info text-white py-3">
                    <h5 class="modal-title fw-bold" id="compareBalancesModalLabel">
                        <i class="las la-balance-scale me-2 fs-4"></i>{{ __('reports::reports.compare_account_balances_with_journal_entries') }}
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                        aria-label="Close"></button>
                </div>
                <div class="modal-body p-4">
                    <div id="compareLoading" class="text-center py-5">
                        <div class="spinner-grow text-info" role="status" style="width: 3rem; height: 3rem;">
                            <span class="visually-hidden">{{ __('Loading...') }}</span>
                        </div>
                        <p class="mt-3 text-muted fw-semibold">{{ __('reports::reports.comparing_balances') }}</p>
                    </div>
                    <div id="compareResults" style="display: none;">
                        <div class="row g-3 mb-4 text-center">
                            <div class="col-md-4">
                                <div class="p-3 bg-soft-primary rounded shadow-sm border-start border-primary border-4">
                                    <div class="text-muted small mb-1">{{ __('reports::reports.total_expense_accounts') }}</div>
                                    <h4 id="totalAccounts" class="mb-0 fw-bold text-primary">0</h4>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="p-3 bg-soft-warning rounded shadow-sm border-start border-warning border-4">
                                    <div class="text-muted small mb-1">{{ __('reports::reports.accounts with Difference:') }}</div>
                                    <h4 id="accountsWithDifference" class="mb-0 fw-bold text-warning">0</h4>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="p-3 bg-soft-danger rounded shadow-sm border-start border-danger border-4">
                                    <div class="text-muted small mb-1">{{ __('reports::reports.total_difference') }}</div>
                                    <h4 id="totalDifference" class="mb-0 fw-bold text-danger">0.00</h4>
                                </div>
                            </div>
                        </div>
                        <div class="table-responsive" style="max-height: 500px; overflow-y: auto;">
                            <table class="table table-hover align-middle">
                                <thead class="bg-light sticky-top">
                                    <tr>
                                        <th class="border-0">{{ __('reports::reports.account_code') }}</th>
                                        <th class="border-0">{{ __('reports::reports.account_name') }}</th>
                                        <th class="border-0 text-end">{{ __('reports::reports.account_balance') }}</th>
                                        <th class="border-0 text-end">{{ __('reports::reports.journal_balance') }}</th>
                                        <th class="border-0 text-end">{{ __('reports::reports.difference') }}</th>
                                        <th class="border-0 text-center">{{ __('reports::reports.status') }}</th>
                                    </tr>
                                </thead>
                                <tbody id="compareTableBody">
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div id="compareError" style="display: none;">
                        <div class="alert alert-soft-danger d-flex align-items-center">
                            <i class="las la-exclamation-triangle fs-3 me-2"></i>
                            <span id="errorMessage"></span>
                        </div>
                    </div>
                </div>
                <div class="modal-footer bg-light border-0">
                    <button type="button" class="btn btn-secondary px-4"
                        data-bs-dismiss="modal">{{ __('reports::reports.reset') }}</button>
                    <button type="button" class="btn btn-info text-white px-4" onclick="compareBalances()">
                        <i class="las la-sync-alt me-1"></i>{{ __('reports::reports.update') }}
                    </button>
                </div>
            </div>
        </div>
    </div>    @push('scripts')
        <script>
            const translations = {
                result: '{{ __('reports::reports.report_summary') }}',
                balanced: '{{ __('reports::reports.balance_sheet_is_balanced') }}',
                unbalanced: '{{ __('reports::reports.balance_sheet_is_unbalanced') }}',
                difference: '{{ __('reports::reports.difference') }}',
                noAccounts: '{{ __('reports::reports.no_accounts_to_compare') }}',
                hasDifference: '{{ __('reports::reports.there_is_a_difference') }}',
                matched: '{{ __('reports::reports.identical') }}',
                serverError: '{{ __('reports::reports.server_connection_error') }}'
            };

            const fmt = (v) => new Intl.NumberFormat('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 }).format(v);

            function updateToggleIcon(button, expanded) {
                const icon = button.querySelector('.toggle-icon');
                if (!icon) return;
                icon.classList.toggle('la-plus-square', !expanded);
                icon.classList.toggle('la-minus-square', expanded);
                button.classList.toggle('collapsed', !expanded);
            }

            function showBranch(accountId) {
                document.querySelectorAll(`tr.children-${accountId}`).forEach(row => {
                    row.classList.remove('d-none');
                    const toggle = row.querySelector('.collapse-toggle');
                    if (toggle && !toggle.classList.contains('collapsed')) showBranch(row.dataset.accountId);
                });
            }

            function hideBranch(accountId) {
                document.querySelectorAll(`tr.children-${accountId}`).forEach(row => {
                    row.classList.add('d-none');
                    const toggle = row.querySelector('.collapse-toggle');
                    if (toggle) {
                        updateToggleIcon(toggle, false);
                        toggle.setAttribute('aria-expanded', 'false');
                    }
                    hideBranch(row.dataset.accountId);
                });
            }

            function toggleChildren(button) {
                const accountId = button.dataset.accountId;
                const expand = button.classList.contains('collapsed');
                updateToggleIcon(button, expand);
                button.setAttribute('aria-expanded', expand ? 'true' : 'false');
                expand ? showBranch(accountId) : hideBranch(accountId);
                calculateTotalAssets();
            }

            function collapseAll() {
                document.querySelectorAll('.account-row').forEach(row => { if (row.dataset.level !== '0') row.classList.add('d-none'); });
                document.querySelectorAll('.collapse-toggle').forEach(btn => updateToggleIcon(btn, false));
                calculateTotalAssets();
            }

            function expandAll() {
                document.querySelectorAll('.account-row').forEach(row => row.classList.remove('d-none'));
                document.querySelectorAll('.collapse-toggle').forEach(btn => updateToggleIcon(btn, true));
                calculateTotalAssets();
            }

            function getSectionTotal(tbodySelector, section) {
                let total = 0;
                document.querySelectorAll(`${tbodySelector} tr.account-row[data-section="${section}"]:not(.d-none)`).forEach(row => {
                    if (row.dataset.hasChildren === '1') {
                        if (document.querySelectorAll(`tr.children-${row.dataset.accountId}:not(.d-none)`).length === 0) {
                            total += parseFloat(row.dataset.totalWithChildren) || parseFloat(row.dataset.balance) || 0;
                        }
                    } else {
                        total += parseFloat(row.dataset.balance) || 0;
                    }
                });
                return total;
            }

            function calculateTotalAssets() {
                const total = getSectionTotal('#assets-tbody', 'assets');
                const val = fmt(total);
                ['total-assets-display', 'total-assets-summary', 'total-assets-vis'].forEach(id => {
                    const el = document.getElementById(id);
                    if (el) el.textContent = val;
                });
                calculateTotalLiabilitiesEquity();
            }

            function calculateTotalLiabilitiesEquity() {
                const liab = getSectionTotal('#liabilities-equity-tbody', 'liabilities');
                const equity = getSectionTotal('#liabilities-equity-tbody', 'equity');
                const netProfit = {{ (float)$netProfit }};
                const totalLiabEq = liab + equity + netProfit;

                if (document.getElementById('total-liabilities-summary')) document.getElementById('total-liabilities-summary').textContent = fmt(liab);
                if (document.getElementById('total-equity-summary')) document.getElementById('total-equity-summary').textContent = fmt(equity);
                if (document.getElementById('total-liabilities-equity-display')) document.getElementById('total-liabilities-equity-display').textContent = fmt(totalLiabEq);
                if (document.getElementById('total-liab-eq-vis')) document.getElementById('total-liab-eq-vis').textContent = fmt(totalLiabEq);

                updatePremiumVisuals(totalLiabEq);
            }

            function updatePremiumVisuals(totalLiabEq) {
                const assets = parseFloat(document.getElementById('total-assets-display').textContent.replace(/,/g, '')) || 0;
                const max = Math.max(assets, totalLiabEq);
                const aP = max > 0 ? (assets / max) * 100 : 0;
                const lP = max > 0 ? (totalLiabEq / max) * 100 : 0;

                const aB = document.getElementById('assets-progress');
                const lB = document.getElementById('liab-eq-progress');
                if (aB) { aB.style.width = aP + '%'; aB.querySelector('.marker').textContent = Math.round(aP) + '%'; }
                if (lB) { lB.style.width = lP + '%'; lB.querySelector('.marker').textContent = Math.round(lP) + '%'; }

                const isB = Math.abs(assets - totalLiabEq) < 0.01;
                const badge = document.getElementById('balance-status-badge');
                if (badge) {
                    badge.textContent = isB ? translations.balanced : translations.unbalanced;
                    badge.className = (isB ? 'text-success' : 'text-danger') + ' fw-bold';
                }

                const alert = document.getElementById('balance-result-alert');
                if (alert) {
                    alert.className = `alert ${isB ? 'alert-soft-success' : 'alert-soft-warning'} border-0 shadow-sm animate__animated animate__fadeInUp`;
                    const icon = alert.querySelector('i');
                    if (icon) icon.className = `las la-${isB ? 'check-circle text-success' : 'exclamation-triangle text-warning'} fs-2`;
                    const msg = alert.querySelector('p');
                    if (msg) {
                        if (isB) {
                            msg.className = 'mb-0 text-success fw-semibold';
                            msg.innerHTML = translations.balanced + ' ✓';
                        } else {
                            msg.className = 'mb-0 text-dark';
                            msg.innerHTML = `<span class="fw-bold text-warning">${translations.unbalanced}</span> - ${translations.difference} <span class="badge bg-danger rounded-pill px-3 ms-2 shadow-sm">${fmt(Math.abs(assets - totalLiabEq))}</span>`;
                        }
                    }
                }
            }

            function compareBalances() {
                const modal = new bootstrap.Modal(document.getElementById('compareBalancesModal'));
                modal.show();
                const l = document.getElementById('compareLoading'), r = document.getElementById('compareResults'), e = document.getElementById('compareError'), b = document.getElementById('compareBtn');
                l.style.display = 'block'; r.style.display = 'none'; e.style.display = 'none'; b.disabled = true;

                fetch(`{{ route('reports.compare-account-balances') }}?as_of_date={{ $asOfDate }}`, {
                    headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' }
                }).then(res => res.json()).then(data => {
                    l.style.display = 'none'; b.disabled = false;
                    if (data.success) {
                        document.getElementById('totalAccounts').textContent = data.total_accounts;
                        document.getElementById('accountsWithDifference').textContent = data.accounts_with_difference;
                        document.getElementById('totalDifference').textContent = fmt(data.total_difference);
                        const tbody = document.getElementById('compareTableBody');
                        tbody.innerHTML = data.comparisons.length ? '' : `<tr><td colspan="6" class="text-center">${translations.noAccounts}</td></tr>`;
                        data.comparisons.forEach(c => {
                            const row = document.createElement('tr');
                            row.className = c.has_difference ? 'table-warning' : '';
                            row.innerHTML = `<td>${c.code}</td><td>${c.name}</td><td class="text-end">${fmt(c.account_balance)}</td><td class="text-end">${fmt(c.journal_balance)}</td><td class="text-end ${c.has_difference ? 'text-danger fw-bold' : ''}">${fmt(c.difference)}</td><td class="text-center"><span class="badge bg-soft-${c.has_difference?'danger':'success'} text-${c.has_difference?'danger':'success'} rounded-pill">${c.has_difference?translations.hasDifference:translations.matched}</span></td>`;
                            tbody.appendChild(row);
                        });
                        r.style.display = 'block';
                    } else { e.style.display = 'block'; document.getElementById('errorMessage').textContent = data.message; }
                }).catch(() => { l.style.display = 'none'; b.disabled = false; e.style.display = 'block'; document.getElementById('errorMessage').textContent = translations.serverError; });
            }

            document.addEventListener('DOMContentLoaded', () => { calculateTotalAssets(); });
        </script>
    @endpush

    @push('styles')
        <style>
            :root {
                --bs-primary-rgb: 13, 110, 253;
                --bs-info-rgb: 13, 202, 240;
                --bs-success-rgb: 25, 135, 84;
                --bs-warning-rgb: 255, 193, 7;
                --bs-danger-rgb: 220, 53, 69;
                --bs-indigo: #6610f2;
                --bs-indigo-rgb: 102, 16, 242;
            }

            .glass-card {
                background: rgba(255, 255, 255, 0.8) !important;
                backdrop-filter: blur(15px);
                border: 1px solid rgba(255, 255, 255, 0.2);
                border-radius: 1.25rem;
            }

            .premium-card {
                border-radius: 1.25rem;
                transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            }
            .premium-card:hover {
                transform: translateY(-8px) scale(1.02);
                box-shadow: 0 15px 30px rgba(0,0,0,0.15) !important;
            }

            .bg-gradient-primary { background: linear-gradient(135deg, #0d6efd 0%, #0a58ca 100%) !important; }
            .bg-gradient-info { background: linear-gradient(135deg, #0dcaf0 0%, #0bacce 100%) !important; }
            .bg-gradient-success { background: linear-gradient(135deg, #198754 0%, #146c43 100%) !important; }
            .bg-gradient-danger { background: linear-gradient(135deg, #dc3545 0%, #bb2d3b 100%) !important; }
            .bg-gradient-indigo { background: linear-gradient(135deg, #6610f2 0%, #520dc2 100%) !important; }

            .bg-soft-primary { background-color: rgba(var(--bs-primary-rgb), 0.1) !important; }
            .bg-soft-info { background-color: rgba(var(--bs-info-rgb), 0.1) !important; }
            .bg-soft-success { background-color: rgba(var(--bs-success-rgb), 0.1) !important; }
            .bg-soft-warning { background-color: rgba(var(--bs-warning-rgb), 0.1) !important; }
            .bg-soft-danger { background-color: rgba(var(--bs-danger-rgb), 0.1) !important; }
            .bg-soft-indigo { background-color: rgba(var(--bs-indigo-rgb), 0.08) !important; }
            .text-indigo { color: var(--bs-indigo) !important; }

            .luxury-table thead th {
                letter-spacing: 0.05em;
                font-size: 0.75rem;
                border-bottom: 2px solid #f1f3f5 !important;
            }
            .luxury-table tbody tr {
                border-bottom: 1px solid #f8f9fa;
            }
            .luxury-table tbody tr:last-child {
                border-bottom: none;
            }

            .icon-shape {
                width: 48px;
                height: 48px;
                display: flex;
                align-items: center;
                justify-content: center;
            }

            .ls-2 { letter-spacing: 2px; }

            .progress {
                background-color: #f1f3f5;
                overflow: visible;
                border-radius: 999px;
            }
            .progress-bar {
                transition: width 1s ease-in-out;
            }
            .marker {
                font-size: 10px;
                font-weight: bold;
                padding: 2px 6px;
                box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            }

            .account-row {
                transition: all 0.2s ease;
            }
            .account-row:hover {
                background-color: rgba(var(--bs-primary-rgb), 0.04) !important;
                transform: translateX(5px);
            }

            .hover-lift {
                transition: transform 0.2s ease, box-shadow 0.2s ease;
            }
            .hover-lift:hover {
                transform: translateY(-3px);
                box-shadow: 0 5px 15px rgba(0,0,0,0.08) !important;
            }

            @media print {
                .premium-card { background: white !important; color: black !important; border: 1px solid #ddd !important; }
                .text-white { color: black !important; }
                .bg-gradient-primary, .bg-gradient-info, .bg-gradient-success { background: none !important; }
            }
        </style>
    @endpush
@endsection

