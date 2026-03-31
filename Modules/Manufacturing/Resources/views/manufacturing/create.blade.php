@extends('admin.dashboard')
@section('sidebar')
    @include('components.sidebar.manufacturing')
@endsection
@section('content')
    <div class="min-h-screen bg-gray-50" id="manufacturing-form-container">
        <!-- Header -->
        <header class="bg-white border-bottom sticky-top px-4 py-2 shadow-sm" style="z-index: 9;">
            <!-- Single Row with Title, Fields and Buttons -->
            <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
                <!-- Left: Title and Fields -->
                <div class="d-flex align-items-center gap-2 flex-wrap">
                    <h1 class="h5 mb-0 fw-bold text-primary">{{ __('manufacturing::manufacturing.manufacturing invoice') }}
                    </h1>
                    <span class="text-muted">|</span>

                    <!-- Branch Select (if multiple branches) -->
                    @if (auth()->user()->branches->count() > 1)
                        <select id="branch-id" name="branch_id" class="form-select border-0 bg-light"
                            style="width: 150px; height: 28px; font-size: 0.8rem; padding: 0.2rem 0.5rem;">
                            @foreach (auth()->user()->branches as $branch)
                                <option value="{{ $branch->id }}"
                                    {{ $branch->id == auth()->user()->current_branch_id ? 'selected' : '' }}>
                                    {{ $branch->name }}
                                </option>
                            @endforeach
                        </select>
                    @endif

                    <!-- Employee -->
                    <select id="employee-select-visible" class="form-select border-0 bg-light"
                        style="width: 140px; height: 28px; font-size: 0.8rem; padding: 0.2rem 0.5rem;"
                        title="{{ __('manufacturing::manufacturing.employee') }}">
                        @foreach ($employees as $employee)
                            <option value="{{ $employee->id }}" {{ $loop->first ? 'selected' : '' }}>{{ $employee->aname }}
                            </option>
                        @endforeach
                    </select>

                    <!-- Salaries Account -->
                    {{-- <select id="salaries-account-select" class="form-select border-0 bg-light" style="width: 160px; height: 28px; font-size: 0.8rem; padding: 0.2rem 0.5rem;" title="{{ __('Salaries and Wages Payable') }}">
                        <option value="">{{ __('Default Employee') }}</option>
                        @foreach ($accounts ?? [] as $account)
                            <option value="{{ $account->id }}">{{ $account->aname }}</option>
                        @endforeach
                    </select> --}}

                    <!-- Date -->
                    <input type="date" id="display-invoice-date" value="{{ date('Y-m-d') }}"
                        class="form-control border-0 bg-light"
                        style="width: 145px; height: 28px; font-size: 0.8rem; padding: 0.2rem 0.5rem;"
                        title="{{ __('manufacturing::manufacturing.date') }}"
                        {{ setting('allow_edit_manufacturing_date', true) ? '' : 'readonly' }}>

                    <!-- Invoice Number -->
                    <input type="text" id="display-invoice-number" value="{{ $nextInvoiceNumber }}" readonly
                        class="form-control border-0 bg-success bg-opacity-10 fw-bold text-success"
                        style="width: 100px; height: 28px; font-size: 0.8rem; padding: 0.2rem 0.5rem;"
                        title="{{ __('manufacturing::manufacturing.invoice number') }}">

                    <!-- Batch Number -->
                    <input type="text" id="display-patch-number"
                        placeholder="{{ __('manufacturing::manufacturing.batch number') }}"
                        class="form-control border-0 bg-light"
                        style="width: 120px; height: 28px; font-size: 0.8rem; padding: 0.2rem 0.5rem;"
                        title="{{ __('manufacturing::manufacturing.batch number') }}">
                </div>

                <!-- Right: Action Buttons -->
                <div class="d-flex align-items-center gap-2 flex-wrap">
                    <!-- Reset Button -->
                    <button type="button" id="btn-reset-page" class="btn btn-outline-danger btn-sm"
                        title="{{ __('manufacturing::manufacturing.reset page') }}"
                        onclick="if(confirm('{{ __('manufacturing::manufacturing.are you sure you want to reset the page? all unsaved data will be lost.') }}')) { window.location.reload(); }">
                        <i class="las la-redo-alt"></i>
                        <span class="d-none d-lg-inline">{{ __('manufacturing::manufacturing.reset') }}</span>
                    </button>

                    <!-- Action Buttons -->
                    <button type="button" id="btn-save-template" class="btn btn-outline-secondary btn-sm"
                        title="{{ __('manufacturing::manufacturing.save template') }}">
                        <i class="las la-save"></i>
                        <span class="d-none d-lg-inline">{{ __('manufacturing::manufacturing.save template') }}</span>
                    </button>

                    <button type="button" id="btn-load-template" class="btn btn-outline-secondary btn-sm"
                        title="{{ __('manufacturing::manufacturing.load template') }}">
                        <i class="las la-folder-open"></i>
                        <span class="d-none d-lg-inline">{{ __('manufacturing::manufacturing.load template') }}</span>
                    </button>

                    <button type="button" id="btn-distribute-costs" class="btn text-white btn-sm text-white"
                        style="background: linear-gradient(135deg, #dce2ffff 0%, #ffffffff 100%); border: none;color:white;"
                        title="{{ __('manufacturing::manufacturing.distribute costs by percentage') }}">
                        <i class="las la-percentage"></i>
                        <span
                            class="d-none d-lg-inline text-white">{{ __('manufacturing::manufacturing.distribute costs by percentage') }}</span>
                    </button>

                    <button type="button" id="btn-save-invoice" class="btn btn-primary btn-sm"
                        title="{{ __('manufacturing::manufacturing.execute manufacturing order') }}">
                        <i class="las la-check-circle"></i>
                        {{ __('manufacturing::manufacturing.execute manufacturing order') }}
                    </button>
                </div>
            </div>
        </header>

        <!-- Error Messages Section -->
        @if ($errors->any() || session('error'))
            <div class="px-4 pt-4">
                <div class="alert alert-danger alert-dismissible fade show shadow-sm border-0" role="alert"
                    style="background: linear-gradient(135deg, #dc3545 0%, #c82333 100%); color: white;">
                    <div class="d-flex align-items-start gap-3">
                        <div class="flex-shrink-0">
                            <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                                style="width: 24px; height: 24px;">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z">
                                </path>
                            </svg>
                        </div>
                        <div class="flex-grow-1">
                            <h5 class="mb-2 fw-bold" style="color: white;">
                                <i class="las la-exclamation-triangle"></i>
                                {{ __('manufacturing::manufacturing.validation_errors') }}
                            </h5>
                            <ul class="mb-0 list-unstyled">
                                @if (session('error'))
                                    <li class="mb-1">
                                        <i class="las la-times-circle"></i>
                                        {{ session('error') }}
                                    </li>
                                @endif
                                @foreach ($errors->all() as $error)
                                    <li class="mb-1">
                                        <i class="las la-times-circle"></i>
                                        {{ $error }}
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="alert"
                            aria-label="Close"></button>
                    </div>
                </div>
            </div>
        @endif

        <!-- Success Message Section -->
        @if (session('success'))
            <div class="px-4 pt-4">
                <div class="alert alert-success alert-dismissible fade show shadow-sm border-0" role="alert"
                    style="background: linear-gradient(135deg, #28a745 0%, #218838 100%); color: white;">
                    <div class="d-flex align-items-start gap-3">
                        <div class="flex-shrink-0">
                            <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                                style="width: 24px; height: 24px;">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                        <div class="flex-grow-1">
                            <i class="las la-check-circle"></i>
                            {{ session('success') }}
                        </div>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="alert"
                            aria-label="Close"></button>
                    </div>
                </div>
            </div>
        @endif

        <!-- Save Template Modal -->
        <div id="modal-save-template" class="fixed inset-0 z-[9999] overflow-y-auto hidden"
            style="background-color: rgba(0,0,0,0.7);">
            <div class="flex items-center justify-center min-h-screen p-4">
                <div class="bg-white rounded-xl shadow-2xl max-w-md w-full">
                    <div class="px-6 py-4 rounded-t-xl flex items-center justify-between">
                        <h5 class="text-lg font-bold flex items-center gap-2">
                            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path
                                    d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4"
                                    stroke-linecap="round" stroke-linejoin="round" stroke-width="2"></path>
                            </svg>
                            {{ __('manufacturing::manufacturing.save as manufacturing template') }}
                        </h5>
                        <button type="button" class="modal-close text-gray-400 hover:text-gray-600 transition-colors">
                            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path d="M6 18L18 6M6 6l12 12" stroke-linecap="round" stroke-linejoin="round"
                                    stroke-width="2"></path>
                            </svg>
                        </button>
                    </div>
                    <div class="p-6 space-y-4">
                        <div>
                            <label
                                class="block text-sm font-medium text-gray-700 mb-2">{{ __('manufacturing::manufacturing.template name') }}</label>
                            <input type="text" id="template-name"
                                class="w-full px-4 py-2 border border-gray-200 rounded-lg focus:ring-primary focus:border-primary"
                                placeholder="{{ __('manufacturing::manufacturing.enter template name') }}">
                        </div>
                        <div>
                            <label
                                class="block text-sm font-medium text-gray-700 mb-2">{{ __('manufacturing::manufacturing.expected production time') }}</label>
                            <input type="text" id="template-expected-time"
                                class="w-full px-4 py-2 border border-gray-200 rounded-lg focus:ring-primary focus:border-primary"
                                placeholder="{{ __('manufacturing::manufacturing.hh:mm') }}">
                        </div>
                    </div>
                    <div class="px-6 py-4 bg-gray-50 rounded-b-xl flex items-center justify-end gap-3">
                        <button type="button"
                            class="modal-close px-4 py-2 border border-gray-200 rounded-lg text-gray-700 hover:bg-gray-100 transition-all">
                            {{ __('manufacturing::manufacturing.cancel') }}
                        </button>
                        <button type="button" id="btn-confirm-save-template"
                            class="px-6 py-2 bg-primary text-white rounded-lg hover:bg-accent transition-all font-medium">
                            {{ __('manufacturing::manufacturing.save') }}
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Load Template Modal -->
        <div id="modal-load-template" class="fixed inset-0 z-[9999] overflow-y-auto hidden"
            style="background-color: rgba(0,0,0,0.7);">
            <div class="flex items-center justify-center min-h-screen p-4">
                <div class="bg-white rounded-xl shadow-2xl max-w-2xl w-full">
                    <div class="px-6 py-4 rounded-t-xl flex items-center justify-between">
                        <h5 class="text-lg font-bold flex items-center gap-2">
                            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path
                                    d="M5 19a2 2 0 01-2-2V7a2 2 0 012-2h4l2 2h4a2 2 0 012 2v1M5 19h14a2 2 0 002-2v-5a2 2 0 00-2-2H9a2 2 0 00-2 2v5a2 2 0 01-2 2z"
                                    stroke-linecap="round" stroke-linejoin="round" stroke-width="2"></path>
                            </svg>
                            {{ __('manufacturing::manufacturing.select manufacturing template') }}
                        </h5>
                        <button type="button" class="modal-close text-gray-400 hover:text-gray-600 transition-colors">
                            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path d="M6 18L18 6M6 6l12 12" stroke-linecap="round" stroke-linejoin="round"
                                    stroke-width="2"></path>
                            </svg>
                        </button>
                    </div>
                    <div class="p-6" id="template-modal-content">
                        <div class="text-center py-12">
                            <div
                                class="animate-spin h-8 w-8 border-4 border-primary border-t-transparent rounded-full mx-auto">
                            </div>
                            <p class="mt-4 text-gray-500">{{ __('manufacturing::manufacturing.loading templates...') }}
                            </p>
                        </div>
                    </div>
                    <div class="px-6 py-4 bg-gray-50 rounded-b-xl flex items-center justify-end gap-3">
                        <button type="button"
                            class="modal-close px-4 py-2 border border-gray-200 rounded-lg text-gray-700 hover:bg-gray-100 transition-all flex items-center gap-2">
                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path d="M6 18L18 6M6 6l12 12" stroke-linecap="round" stroke-linejoin="round"
                                    stroke-width="2"></path>
                            </svg>
                            {{ __('manufacturing::manufacturing.cancel') }}
                        </button>
                        {{-- <button type="button" id="btn-confirm-load-template"
                            class="px-6 py-2 bg-primary text-white rounded-lg hover:bg-accent transition-all font-medium flex items-center gap-2">
                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"
                                    stroke-linecap="round" stroke-linejoin="round" stroke-width="2"></path>
                            </svg>
                            {{ __('manufacturing::manufacturing.load template') }}
                        </button> --}}
                    </div>
                </div>
            </div>
        </div>


        <!-- Main Content -->
        <main class="w-100">
            <div class="row g-3 px-2">
                <!-- Left: Inputs & Tables (8 columns) -->
                <div class="col-12 col-lg-8">
                    <!-- Manufactured Products Section -->
                    <section class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                        <div class="flex items-center justify-between mb-4">
                            <h3 class="text-lg font-bold text-gray-800">
                                {{ __('manufacturing::manufacturing.manufactured products') }}</h3>
                            <span
                                class="text-xs text-gray-400">{{ __('manufacturing::manufacturing.add products used in manufacturing') }}</span>
                        </div>

                        <!-- Product Search -->
                        <div class="flex gap-3 mb-6">
                            <div class="relative flex-grow">
                                <input type="text" id="product-search"
                                    class="w-full h-11 text-sm border-gray-200 rounded-lg focus:ring-primary focus:border-primary pr-10"
                                    placeholder="{{ __('manufacturing::manufacturing.search for product...') }}"
                                    autocomplete="off" />
                                <div
                                    class="absolute inset-y-0 right-0 pr-3.5 flex items-center pointer-events-none text-gray-400">
                                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" stroke-linecap="round"
                                            stroke-linejoin="round" stroke-width="2"></path>
                                    </svg>
                                </div>

                                <!-- Search Results Dropdown -->
                                <div id="product-search-results"
                                    class="absolute w-full mt-1 bg-white border border-gray-200 rounded-lg shadow-lg z-50 max-h-60 overflow-y-auto hidden">
                                </div>
                            </div>

                            <select id="product-account"
                                class="w-48 h-11 text-sm border-gray-200 rounded-lg focus:ring-primary focus:border-primary bg-gray-50">
                                @foreach ($accounts as $account)
                                    <option value="{{ $account->id }}">{{ $account->aname }}</option>
                                @endforeach
                            </select>

                            <button type="button" id="btn-refresh-products"
                                class="h-11 px-3 border border-gray-200 rounded-lg text-gray-400 hover:text-primary hover:border-primary transition-all"
                                title="{{ __('manufacturing::manufacturing.refresh') }}">
                                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path
                                        d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"
                                        stroke-linecap="round" stroke-linejoin="round" stroke-width="2"></path>
                                </svg>
                            </button>
                        </div>

                        <!-- Products Table or Empty State -->
                        <div id="products-container">
                            <div id="products-empty-state"
                                class="empty-state-card bg-gray-50/50 rounded-xl border-2 border-dashed border-gray-200 py-12">
                                <div class="bg-white p-4 rounded-full shadow-sm mb-4 inline-block">
                                    <svg class="h-10 w-10 text-gray-300" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"
                                            stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"></path>
                                    </svg>
                                </div>
                                <p class="text-gray-500 font-medium">{{ __('manufacturing::manufacturing.no products') }}
                                </p>
                                <p class="text-[11px] text-gray-400 mt-1">
                                    {{ __('manufacturing::manufacturing.add products used in manufacturing') }}</p>
                            </div>

                            <div id="products-table-container" class="overflow-x-auto hidden">
                                <table class="w-full desktop-table">
                                    <thead class="bg-gray-50 border-b border-gray-200">
                                        <tr class="text-center text-gray-600 font-medium text-xs">
                                            <th class="px-2 py-2">{{ __('manufacturing::manufacturing.product') }}</th>
                                            <th class="px-2 py-2">{{ __('manufacturing::manufacturing.unit') }}</th>
                                            <th class="px-2 py-2">{{ __('manufacturing::manufacturing.quantity') }}</th>
                                            <th class="px-2 py-2">{{ __('manufacturing::manufacturing.unit cost') }}</th>
                                            <th class="px-2 py-2">{{ __('manufacturing::manufacturing.cost percentage') }}
                                                %</th>
                                            <th class="px-2 py-2">{{ __('manufacturing::manufacturing.total') }}</th>
                                            <th class="px-2 py-2">{{ __('manufacturing::manufacturing.actions') }}</th>
                                        </tr>
                                    </thead>
                                    <tbody id="products-table-body" class="divide-y divide-gray-100"></tbody>
                                </table>
                            </div>
                        </div>
                    </section>

                    <!-- Tabs -->
                    <div class="border-b border-gray-200 flex items-center gap-8">
                        <button type="button" id="tab-raw-materials"
                            class="pb-4 px-2 text-sm font-bold transition-colors border-b-2 border-primary text-primary">
                            {{ __('manufacturing::manufacturing.raw materials') }}
                        </button>
                        <button type="button" id="tab-expenses"
                            class="pb-4 px-2 text-sm font-medium transition-colors text-gray-400 hover:text-gray-700">
                            {{ __('manufacturing::manufacturing.expenses') }}
                        </button>
                    </div>


                    <!-- Tab Content -->
                    <div class="tab-content">
                        <!-- Raw Materials Tab -->
                        <div id="tab-content-raw-materials">
                            <section class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                                <div class="flex gap-3 mb-6">
                                    <div class="relative flex-grow">
                                        <input type="text" id="raw-material-search"
                                            class="w-full h-11 text-sm border-gray-200 rounded-lg focus:ring-primary focus:border-primary pr-10"
                                            placeholder="{{ __('manufacturing::manufacturing.search for raw material...') }}"
                                            autocomplete="off" />
                                        <div
                                            class="absolute inset-y-0 right-0 pr-3.5 flex items-center pointer-events-none text-gray-400">
                                            <svg class="h-4 w-4" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"
                                                    stroke-linecap="round" stroke-linejoin="round" stroke-width="2">
                                                </path>
                                            </svg>
                                        </div>

                                        <!-- Search Results Dropdown -->
                                        <div id="raw-material-search-results"
                                            class="absolute w-full mt-1 bg-white border border-gray-200 rounded-lg shadow-lg z-50 max-h-60 overflow-y-auto hidden">
                                        </div>
                                    </div>

                                    <select id="raw-material-account"
                                        class="w-48 h-11 text-sm border-gray-200 rounded-lg focus:ring-primary focus:border-primary bg-gray-50">
                                        @foreach ($rawMaterialAccounts as $account)
                                            <option value="{{ $account->id }}">{{ $account->aname }}</option>
                                        @endforeach
                                    </select>

                                    <button type="button" id="btn-refresh-raw-materials"
                                        class="h-11 px-3 border border-gray-200 rounded-lg text-gray-400 hover:text-primary hover:border-primary transition-all"
                                        title="{{ __('manufacturing::manufacturing.refresh') }}">
                                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path
                                                d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"
                                                stroke-linecap="round" stroke-linejoin="round" stroke-width="2"></path>
                                        </svg>
                                    </button>
                                </div>

                                <div id="raw-materials-container">
                                    <div id="raw-materials-empty-state"
                                        class="empty-state-card bg-gray-50/50 rounded-xl border-2 border-dashed border-gray-200 py-12">
                                        <div class="bg-white p-4 rounded-full shadow-sm mb-4 inline-block">
                                            <svg class="h-10 w-10 text-gray-300" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path
                                                    d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"
                                                    stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5">
                                                </path>
                                            </svg>
                                        </div>
                                        <p class="text-gray-500 font-medium">
                                            {{ __('manufacturing::manufacturing.no raw materials') }}</p>
                                        <p class="text-[11px] text-gray-400 mt-1">
                                            {{ __('manufacturing::manufacturing.add raw materials used in manufacturing') }}
                                        </p>
                                    </div>

                                    <div id="raw-materials-table-container" class="overflow-x-auto hidden">
                                        <table class="w-full desktop-table">
                                            <thead class="bg-gray-50 border-b border-gray-200">
                                                <tr class="text-center text-gray-600 font-medium text-xs">
                                                    <th class="px-2 py-2 text-right">
                                                        {{ __('manufacturing::manufacturing.raw material') }}</th>
                                                    <th class="px-2 py-2">{{ __('manufacturing::manufacturing.unit') }}
                                                    </th>
                                                    <th class="px-2 py-2">
                                                        {{ __('manufacturing::manufacturing.quantity') }}</th>
                                                    <th class="px-2 py-2">
                                                        {{ __('manufacturing::manufacturing.cost price') }}</th>
                                                    <th class="px-2 py-2">{{ __('manufacturing::manufacturing.total') }}
                                                    </th>
                                                    <th class="px-2 py-2">{{ __('manufacturing::manufacturing.actions') }}
                                                    </th>
                                                </tr>
                                            </thead>
                                            <tbody id="raw-materials-table-body" class="divide-y divide-gray-100"></tbody>
                                        </table>
                                    </div>
                                </div>
                            </section>
                        </div>

                        <!-- Expenses Tab -->
                        <div id="tab-content-expenses" class="hidden">
                            <section class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                                <div class="flex items-center justify-between mb-6">
                                    <h3 class="text-lg font-bold text-gray-800">
                                        {{ __('manufacturing::manufacturing.additional expenses') }}</h3>
                                    <button type="button" id="btn-add-expense"
                                        class="h-10 px-4 bg-primary text-white rounded-lg hover:bg-accent transition-all flex items-center gap-2">
                                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path d="M12 4v16m8-8H4" stroke-linecap="round" stroke-linejoin="round"
                                                stroke-width="2"></path>
                                        </svg>
                                        {{ __('manufacturing::manufacturing.add expense') }}
                                    </button>
                                </div>

                                <div id="expenses-container">
                                    <div id="expenses-empty-state"
                                        class="empty-state-card bg-gray-50/50 rounded-xl border-2 border-dashed border-gray-200 py-12">
                                        <div class="bg-white p-4 rounded-full shadow-sm mb-4 inline-block">
                                            <svg class="h-10 w-10 text-gray-300" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path
                                                    d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"
                                                    stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5">
                                                </path>
                                            </svg>
                                        </div>
                                        <p class="text-gray-500 font-medium">
                                            {{ __('manufacturing::manufacturing.no additional expenses') }}</p>
                                        <p class="text-[11px] text-gray-400 mt-1">
                                            {{ __('manufacturing::manufacturing.add additional manufacturing expenses') }}
                                        </p>
                                    </div>

                                    <div id="expenses-table-container" class="overflow-x-auto hidden">
                                        <table class="w-full desktop-table">
                                            <thead class="bg-gray-50 border-b border-gray-200">
                                                <tr class="text-center text-gray-600 font-medium text-xs">
                                                    <th class="px-2 py-2">{{ __('manufacturing::manufacturing.amount') }}
                                                    </th>
                                                    <th class="px-2 py-2">{{ __('manufacturing::manufacturing.account') }}
                                                    </th>
                                                    <th class="px-2 py-2 text-right">
                                                        {{ __('manufacturing::manufacturing.description') }}</th>
                                                    <th class="px-2 py-2">{{ __('manufacturing::manufacturing.actions') }}
                                                    </th>
                                                </tr>
                                            </thead>
                                            <tbody id="expenses-table-body" class="divide-y divide-gray-100"></tbody>
                                        </table>
                                    </div>
                                </div>
                            </section>
                        </div>
                    </div>
                </div>


                <!-- Right: Summary Sidebar (4 columns) -->
                <div class="col-12 col-lg-4" id="manufacturing-sidebar">
                    <div class="sticky-top" style="top: 70px;">
                        <!-- Cost Summary -->
                        <div class="bg-white rounded-xl shadow-lg border-0">
                            <div class="p-4 rounded-t-xl">
                                <h3 class="text-lg font-bold mb-0 flex items-center gap-2">
                                    <svg class="h-5 w-5" fill="currentColor" viewBox="0 0 20 20">
                                        <path
                                            d="M2 11a1 1 0 011-1h2a1 1 0 011 1v5a1 1 0 01-1 1H3a1 1 0 01-1-1v-5zM8 7a1 1 0 011-1h2a1 1 0 011 1v9a1 1 0 01-1 1H9a1 1 0 01-1-1V7zM14 4a1 1 0 011-1h2a1 1 0 011 1v12a1 1 0 01-1 1h-2a1 1 0 01-1-1V4z">
                                        </path>
                                    </svg>
                                    {{ __('manufacturing::manufacturing.cost summary') }}
                                </h3>
                            </div>

                            <div class="px-6 pb-6">
                                <div class="space-y-4">
                                    <!-- First Section: Costs -->
                                    <div>
                                        <div class="space-y-3">
                                            <!-- Total Raw Materials -->
                                            <div class="flex justify-between items-center">
                                                <div class="flex items-center gap-2 text-gray-600">
                                                    <div class="p-1.5 bg-blue-50 rounded-md">
                                                        <svg class="h-4 w-4 text-blue-500" fill="currentColor"
                                                            viewBox="0 0 20 20">
                                                            <path
                                                                d="M3 4a1 1 0 011-1h12a1 1 0 011 1v2a1 1 0 01-1 1H4a1 1 0 01-1-1V4zM3 10a1 1 0 011-1h6a1 1 0 011 1v6a1 1 0 01-1 1H4a1 1 0 01-1-1v-6zM14 9a1 1 0 00-1 1v6a1 1 0 001 1h2a1 1 0 001-1v-6a1 1 0 00-1-1h-2z">
                                                            </path>
                                                        </svg>
                                                    </div>
                                                    <span
                                                        class="text-xs font-medium">{{ __('manufacturing::manufacturing.total raw materials') }}</span>
                                                </div>
                                                <span class="text-sm font-bold text-blue-600"
                                                    id="summary-raw-materials">0.00
                                                    {{ __('manufacturing::manufacturing.egp') }}</span>
                                            </div>

                                            <!-- Total Expenses -->
                                            <div class="flex justify-between items-center">
                                                <div class="flex items-center gap-2 text-gray-600">
                                                    <div class="p-1.5 bg-amber-50 rounded-md">
                                                        <svg class="h-4 w-4 text-amber-500" fill="currentColor"
                                                            viewBox="0 0 20 20">
                                                            <path d="M4 4a2 2 0 00-2 2v1h16V6a2 2 0 00-2-2H4z"></path>
                                                            <path fill-rule="evenodd"
                                                                d="M18 9H2v5a2 2 0 002 2h12a2 2 0 002-2V9zM4 13a1 1 0 011-1h1a1 1 0 110 2H5a1 1 0 01-1-1zm5-1a1 1 0 100 2h1a1 1 0 100-2H9z"
                                                                clip-rule="evenodd"></path>
                                                        </svg>
                                                    </div>
                                                    <span
                                                        class="text-xs font-medium">{{ __('manufacturing::manufacturing.total expenses') }}</span>
                                                </div>
                                                <span class="text-sm font-bold text-amber-600" id="summary-expenses">0.00
                                                    {{ __('manufacturing::manufacturing.egp') }}</span>
                                            </div>

                                            <!-- Total Invoice Cost -->
                                            <div class="flex justify-between items-center border-t border-gray-200 pt-3">
                                                <div class="flex items-center gap-2 text-gray-700">
                                                    <div class="p-1.5 bg-red-50 rounded-md">
                                                        <svg class="h-4 w-4 text-red-500" fill="currentColor"
                                                            viewBox="0 0 20 20">
                                                            <path
                                                                d="M2 11a1 1 0 011-1h2a1 1 0 011 1v5a1 1 0 01-1 1H3a1 1 0 01-1-1v-5zM8 7a1 1 0 011-1h2a1 1 0 011 1v9a1 1 0 01-1 1H9a1 1 0 01-1-1V7zM14 4a1 1 0 011-1h2a1 1 0 011 1v12a1 1 0 01-1 1h-2a1 1 0 01-1-1V4z">
                                                            </path>
                                                        </svg>
                                                    </div>
                                                    <span
                                                        class="text-xs font-bold">{{ __('manufacturing::manufacturing.total invoice cost') }}</span>
                                                </div>
                                                <span class="text-base font-bold text-red-600"
                                                    id="summary-invoice-cost">0.00
                                                    {{ __('manufacturing::manufacturing.egp') }}</span>
                                            </div>

                                            <!-- Total Products -->
                                            <div class="flex justify-between items-center">
                                                <div class="flex items-center gap-2 text-gray-700">
                                                    <div class="p-1.5 bg-emerald-50 rounded-md">
                                                        <svg class="h-4 w-4 text-emerald-500" fill="currentColor"
                                                            viewBox="0 0 20 20">
                                                            <path
                                                                d="M7 3a1 1 0 000 2h6a1 1 0 100-2H7zM4 7a1 1 0 011-1h10a1 1 0 110 2H5a1 1 0 01-1-1zM2 11a2 2 0 012-2h12a2 2 0 012 2v4a2 2 0 01-2 2H4a2 2 0 01-2-2v-4z">
                                                            </path>
                                                        </svg>
                                                    </div>
                                                    <span
                                                        class="text-xs font-bold">{{ __('manufacturing::manufacturing.total products value') }}</span>
                                                </div>
                                                <span class="text-base font-bold text-emerald-600"
                                                    id="summary-products">0.00
                                                    {{ __('manufacturing::manufacturing.egp') }}</span>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Second Section: Standard & Variance -->
                                    <div>
                                        <h6
                                            class="text-primary font-bold border-b border-gray-200 pb-2 mb-3 flex items-center gap-2">
                                            <svg class="h-4 w-4" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd"
                                                    d="M3 3a1 1 0 000 2v8a2 2 0 002 2h2.586l-1.293 1.293a1 1 0 101.414 1.414L10 15.414l2.293 2.293a1 1 0 001.414-1.414L12.414 15H15a2 2 0 002-2V5a1 1 0 100-2H3zm11.707 4.707a1 1 0 00-1.414-1.414L10 9.586 8.707 8.293a1 1 0 00-1.414 0l-2 2a1 1 0 101.414 1.414L8 10.414l1.293 1.293a1 1 0 001.414 0l4-4z"
                                                    clip-rule="evenodd"></path>
                                            </svg>
                                            {{ __('manufacturing::manufacturing.standard & variance analysis') }}
                                        </h6>

                                        <div class="space-y-3">
                                            <!-- Standard Cost -->
                                            <div class="flex justify-between items-center bg-gray-50 p-3 rounded-lg">
                                                <div class="flex items-center gap-2 text-gray-700">
                                                    <div class="p-1.5 bg-blue-100 rounded-md">
                                                        <svg class="h-4 w-4 text-blue-600" fill="currentColor"
                                                            viewBox="0 0 20 20">
                                                            <path
                                                                d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z">
                                                            </path>
                                                        </svg>
                                                    </div>
                                                    <span
                                                        class="text-xs font-bold">{{ __('manufacturing::manufacturing.standard cost (template)') }}</span>
                                                </div>
                                                <span class="text-base font-bold text-primary"
                                                    id="summary-standard-cost">0.00
                                                    {{ __('manufacturing::manufacturing.egp') }}</span>
                                            </div>

                                            <!-- Variance -->
                                            <div class="flex justify-between items-center border-t border-gray-200 pt-3">
                                                <div class="flex items-center gap-2 text-gray-700">
                                                    <div class="p-1.5 bg-purple-50 rounded-md">
                                                        <svg class="h-4 w-4 text-purple-500" fill="currentColor"
                                                            viewBox="0 0 20 20">
                                                            <path fill-rule="evenodd"
                                                                d="M12 7a1 1 0 110-2h5a1 1 0 011 1v5a1 1 0 11-2 0V8.414l-4.293 4.293a1 1 0 01-1.414 0L8 10.414l-4.293 4.293a1 1 0 01-1.414-1.414l5-5a1 1 0 011.414 0L11 10.586 14.586 7H12z"
                                                                clip-rule="evenodd"></path>
                                                        </svg>
                                                    </div>
                                                    <span
                                                        class="text-xs font-bold">{{ __('manufacturing::manufacturing.variance (difference)') }}</span>
                                                </div>
                                                <div class="flex flex-col items-end">
                                                    <span class="text-base font-bold" id="summary-variance-amount">0.00
                                                        {{ __('manufacturing::manufacturing.egp') }}</span>
                                                    <span class="text-xs font-bold px-2 py-0.5 rounded mt-1"
                                                        id="summary-variance-percentage">0%</span>
                                                </div>
                                            </div>

                                            <!-- Variance Warning -->
                                            <div id="variance-warning"
                                                class="hidden mt-3 bg-red-50 border border-red-200 rounded-lg p-3">
                                                <div class="flex items-start gap-2">
                                                    <svg class="h-5 w-5 text-red-500 flex-shrink-0 mt-0.5"
                                                        fill="currentColor" viewBox="0 0 20 20">
                                                        <path fill-rule="evenodd"
                                                            d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z"
                                                            clip-rule="evenodd"></path>
                                                    </svg>
                                                    <div class="flex-1">
                                                        <p class="text-xs font-bold text-red-800">
                                                            {{ __('items.cannot_save_invoice') }}</p>
                                                        <p class="text-xs text-red-700 mt-1">
                                                            {{ __('items.product_value_exceeds_cost') }}</p>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Item Details Card -->
                        <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                            <div class=" px-4 py-2">
                                <h4 class="text-sm font-bold text-white flex items-center gap-2">
                                    <svg class="h-4 w-4" fill="currentColor" viewBox="0 0 20 20">
                                        <path d="M10 12a2 2 0 100-4 2 2 0 000 4z"></path>
                                        <path fill-rule="evenodd"
                                            d="M.458 10C1.732 5.943 5.522 3 10 3s8.268 2.943 9.542 7c-1.274 4.057-5.064 7-9.542 7S1.732 14.057.458 10zM14 10a4 4 0 11-8 0 4 4 0 018 0z"
                                            clip-rule="evenodd"></path>
                                    </svg>
                                    {{ __('manufacturing::manufacturing.manufacturing details') }}
                                </h4>
                            </div>

                            <div class="p-4">
                                <div class="grid grid-cols-2 gap-4 text-xs">
                                    <!-- Left Column -->
                                    <div class="space-y-2 border-e border-gray-200 pe-4">
                                        <div class="flex justify-between items-center py-1.5">
                                            <span
                                                class="text-gray-600">{{ __('manufacturing::manufacturing.name') }}:</span>
                                            <span class="font-bold text-gray-800 text-end"
                                                id="selected-item-name">-</span>
                                        </div>
                                        <div class="flex justify-between items-center py-1.5">
                                            <span
                                                class="text-gray-600">{{ __('manufacturing::manufacturing.branch') }}:</span>
                                            <span class="font-medium text-gray-700 text-end"
                                                id="selected-item-store">-</span>
                                        </div>
                                        <div class="flex justify-between items-center py-1.5">
                                            <span
                                                class="text-gray-600">{{ __('manufacturing::manufacturing.available stock') }}:</span>
                                            <span class="font-bold text-blue-600 text-end"
                                                id="selected-item-available">-</span>
                                        </div>
                                        <div class="flex justify-between items-center py-1.5">
                                            <span
                                                class="text-gray-600">{{ __('manufacturing::manufacturing.total') }}:</span>
                                            <span class="font-bold text-blue-600 text-end"
                                                id="selected-item-total">-</span>
                                        </div>
                                    </div>

                                    <!-- Right Column -->
                                    <div class="space-y-2 ps-4">
                                        <div class="flex justify-between items-center py-1.5">
                                            <span
                                                class="text-gray-600">{{ __('manufacturing::manufacturing.unit') }}:</span>
                                            <span class="font-medium text-gray-700 text-end"
                                                id="selected-item-unit">-</span>
                                        </div>
                                        <div class="flex justify-between items-center py-1.5">
                                            <span
                                                class="text-gray-600">{{ __('manufacturing::manufacturing.cost price') }}:</span>
                                            <span class="font-bold text-primary text-end"
                                                id="selected-item-price">-</span>
                                        </div>
                                        <div class="flex justify-between items-center py-1.5">
                                            <span
                                                class="text-gray-600">{{ __('manufacturing::manufacturing.cost price') }}:</span>
                                            <span class="font-medium text-emerald-600 text-end"
                                                id="selected-item-last-price">-</span>
                                        </div>
                                        <div class="flex justify-between items-center py-1.5">
                                            <span
                                                class="text-gray-600">{{ __('manufacturing::manufacturing.average cost') }}:</span>
                                            <span class="font-bold text-emerald-600 text-end"
                                                id="selected-item-avg-cost">-</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="bg-emerald-50 border border-emerald-100 p-4 rounded-xl">
                            <p class="text-[11px] text-emerald-800 leading-relaxed">
                                {{ __('manufacturing::manufacturing.note: review quantities and prices before saving. inventory will be updated upon invoice approval.') }}
                            </p>
                        </div>

                        <div id="distribution-note" class="hidden">
                            <div class="bg-blue-50 border border-blue-100 p-4 rounded-lg flex items-center gap-3">
                                <div class="bg-blue-100 p-2 rounded-full">
                                    <svg class="h-5 w-5 text-blue-600" fill="currentColor" viewBox="0 0 20 20">
                                        <path clip-rule="evenodd"
                                            d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z"
                                            fill-rule="evenodd"></path>
                                    </svg>
                                </div>
                                <p class="text-xs text-blue-800 leading-relaxed font-medium">
                                    {{ __('manufacturing::manufacturing.total raw materials and expenses will be distributed') }}
                                    (<span id="distribution-total">0.00</span>
                                    {{ __('manufacturing::manufacturing.egp') }})
                                    {{ __('manufacturing::manufacturing.on products based on specified percentages') }}
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </main>

        <!-- Hidden form for submission -->
        <form id="manufacturing-form" method="POST" action="{{ route('manufacturing.store') }}">
            @csrf
            <!-- Data arrays -->
            <input type="hidden" name="products_data" id="form-products">
            <input type="hidden" name="raw_materials_data" id="form-raw-materials">
            <input type="hidden" name="expenses_data" id="form-expenses">

            <!-- Invoice details -->
            <input type="hidden" name="pro_id" id="pro-id" value="{{ $nextInvoiceNumber }}">
            <input type="hidden" name="pro_date" id="invoice-date" value="{{ date('Y-m-d') }}">
            <input type="hidden" name="branch_id" id="form-branch-id"
                value="{{ auth()->user()->current_branch_id ?? auth()->user()->branch_id }}">

            <!-- Accounts -->
            <input type="hidden" name="acc1" id="product-account-input"
                value="{{ $accounts->first()->id ?? '' }}">
            <input type="hidden" name="acc2" id="raw-account-input"
                value="{{ $rawMaterialAccounts->first()->id ?? '' }}">
            <input type="hidden" name="emp_id" id="employee-id" value="{{ $employees->first()->id ?? '' }}">
            <input type="hidden" name="operating_account" id="operating-account" value="73">

            <!-- Optional fields -->
            <input type="hidden" name="info" id="description" value="">
            <input type="hidden" name="pro_serial" id="patch-number" value="">
            <input type="hidden" name="order_id" id="order-id" value="">
            <input type="hidden" name="stage_id" id="stage-id" value="">
            <input type="hidden" name="actual_time" id="actual-time" value="">

            <!-- Template fields -->
            <input type="hidden" name="is_template" id="is-template" value="0">
            <input type="hidden" name="template_name" id="form-template-name-hidden" value="">
        </form>
    </div>

    @push('styles')
        <link rel="stylesheet" href="{{ asset('assets/css/custom-css/manufacturing-invoice.css') }}">
        <script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
        <script>
            tailwind.config = {
                theme: {
                    extend: {
                        colors: {
                            primary: '#009485',
                            secondary: '#f3f4f6',
                            accent: '#006d62'
                        },
                        fontFamily: {
                            sans: ['Tajawal', 'Inter', 'sans-serif'],
                        },
                        borderRadius: {
                            'eight': '8px',
                        }
                    }
                }
            }
        </script>
    @endpush

    @push('scripts')
        <!-- Fuse.js for fuzzy search -->
        <script src="https://cdn.jsdelivr.net/npm/fuse.js@7.0.0"></script>

        @vite(['Resources/assets/js/manufacturing-form.js'], 'build-manufacturing')

        <script>
            // Translation helper
            window.__ = function(key) {
                const translations = {
                    'Quantity': '{{ __('manufacturing::manufacturing.quantity') }}',
                    'Unit Cost': '{{ __('manufacturing::manufacturing.unit cost') }}',
                    'Cost Percentage': '{{ __('manufacturing::manufacturing.cost percentage') }}',
                    'Delete': '{{ __('manufacturing::manufacturing.delete') }}',
                    'EGP': '{{ __('manufacturing::manufacturing.egp') }}',
                    'pieces': '{{ __('manufacturing::manufacturing.pieces') }}',
                    'Expense Description': '{{ __('manufacturing::manufacturing.expense description') }}',
                    'No Saved Templates': '{{ __('manufacturing::manufacturing.no saved templates') }}',
                    'Save a template first to load it later': '{{ __('manufacturing::manufacturing.save a template first to load it later') }}',
                    'Choose Template': '{{ __('manufacturing::manufacturing.choose template') }}',
                    'Select Template': '{{ __('manufacturing::manufacturing.select template') }}',
                    'Template Preview': '{{ __('manufacturing::manufacturing.template preview') }}',
                    'Products': '{{ __('manufacturing::manufacturing.product') }}',
                    'Raw Materials': '{{ __('manufacturing::manufacturing.raw materials') }}',
                    'Expected Time': '{{ __('manufacturing::manufacturing.expected time') }}',
                    'Failed to load templates': '{{ __('manufacturing::manufacturing.unknown') }}'
                };
                return translations[key] || key;
            };

            // Expense accounts data from backend
            window.expenseAccounts = @json(collect($expenseAccounts)->mapWithKeys(function ($account) {
                        return [$account->id => $account->aname];
                    })->toArray());

            // API routes configuration
            window.manufacturingConfig = {
                routes: {
                    allItems: '/manufacturing/api/all-items',
                    searchProducts: '/manufacturing/api/search-products',
                    searchRawMaterials: '/manufacturing/api/search-raw-materials',
                    getItemWithUnits: '/manufacturing/api/get-item-units/:id',
                    getAvailableStock: '/manufacturing/api/get-available-stock'
                }
            };

            // Update branch_id when changed
            document.addEventListener('DOMContentLoaded', function() {
                const branchSelect = document.getElementById('branch-id');
                const branchInput = document.getElementById('form-branch-id');

                if (branchSelect && branchInput) {
                    branchSelect.addEventListener('change', function() {
                        branchInput.value = this.value;
                    });
                }

                // Sync new header inputs with hidden form inputs
                function syncInput(sourceId, targetId) {
                    const source = document.getElementById(sourceId);
                    const target = document.getElementById(targetId);
                    if (source && target) {
                        source.addEventListener('input', () => {
                            target.value = source.value;
                        });
                        source.addEventListener('change', () => {
                            target.value = source.value;
                        });
                    }
                }

                syncInput('display-invoice-date', 'invoice-date');
                syncInput('display-patch-number', 'patch-number');
                syncInput('display-invoice-number', 'pro-id');
                syncInput('employee-select-visible', 'employee-id');
            });
        </script>
    @endpush

    @push('styles')
        <style>
            /* Error Messages Styling for Dark Mode Support */
            .alert {
                border-radius: 0.75rem;
                padding: 1rem 1.25rem;
                margin-bottom: 1rem;
            }

            .alert-danger {
                background: linear-gradient(135deg, #dc3545 0%, #c82333 100%);
                color: white;
                border: none;
            }

            .alert-success {
                background: linear-gradient(135deg, #28a745 0%, #218838 100%);
                color: white;
                border: none;
            }

            /* Dark Mode Support */
            @media (prefers-color-scheme: dark) {
                .alert-danger {
                    background: linear-gradient(135deg, #e74c3c 0%, #c0392b 100%);
                    box-shadow: 0 4px 6px rgba(231, 76, 60, 0.3);
                }

                .alert-success {
                    background: linear-gradient(135deg, #27ae60 0%, #229954 100%);
                    box-shadow: 0 4px 6px rgba(39, 174, 96, 0.3);
                }

                .alert {
                    color: #ffffff;
                }

                .alert ul li {
                    color: #ffffff;
                }

                .btn-close-white {
                    filter: brightness(1.2);
                }
            }

            /* RTL Support */
            [dir="rtl"] .alert ul {
                padding-right: 1.5rem;
                padding-left: 0;
            }

            [dir="ltr"] .alert ul {
                padding-left: 1.5rem;
                padding-right: 0;
            }

            /* Animation */
            .alert {
                animation: slideInDown 0.3s ease-out;
            }

            @keyframes slideInDown {
                from {
                    opacity: 0;
                    transform: translateY(-20px);
                }

                to {
                    opacity: 1;
                    transform: translateY(0);
                }
            }

            /* Icon Styling */
            .alert i.las {
                font-size: 1.1rem;
                margin-left: 0.25rem;
            }

            [dir="rtl"] .alert i.las {
                margin-left: 0;
                margin-right: 0.25rem;
            }
        </style>
    @endpush
@endsection
