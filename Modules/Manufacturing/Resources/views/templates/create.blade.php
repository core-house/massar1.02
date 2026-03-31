@extends('admin.dashboard')
@section('sidebar')
    @include('components.sidebar.manufacturing')
@endsection
@section('content')
    <div class="min-h-screen bg-gray-50" id="manufacturing-form-container">
        <!-- Header -->
        <header class="bg-white border-b sticky top-0 z-40 px-4 py-3 shadow-sm">
            <div class="w-full flex justify-between items-center flex-wrap gap-4 mb-3">
                <h1 class="text-2xl font-bold text-gray-900">{{ __('manufacturing::manufacturing.create manufacturing template') }}</h1>

                <div class="flex items-center gap-3 flex-wrap">
                    <a href="{{ route('manufacturing.templates.index') }}"
                        class="h-11 px-4 bg-white border border-gray-200 rounded-lg text-gray-700 hover:bg-gray-50 transition-all flex items-center gap-2">
                        <i class="las la-arrow-right"></i>
                        <span>{{ __('manufacturing::manufacturing.back to templates') }}</span>
                    </a>

                    <button type="button" id="btn-distribute-costs"
                        class="h-11 px-4 bg-primary text-white rounded-lg hover:bg-accent transition-all flex items-center gap-2 disabled:opacity-50 disabled:cursor-not-allowed"
                        title="{{ __('manufacturing::manufacturing.distribute costs by percentage') }}">
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path
                                d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z"
                                stroke-linecap="round" stroke-linejoin="round" stroke-width="2"></path>
                        </svg>
                        <span class="hidden md:inline">{{ __('manufacturing::manufacturing.distribute costs by percentage') }}</span>
                    </button>

                    <button type="button" id="btn-save-invoice"
                        class="h-11 px-6 bg-primary text-white rounded-lg font-bold hover:bg-accent transition-all shadow-md shadow-primary/20 flex items-center gap-2"
                        title="{{ __('manufacturing::manufacturing.save template') }}">
                        <i class="las la-save text-xl"></i>
                        {{ __('manufacturing::manufacturing.save template') }}
                    </button>
                </div>
            </div>

            <!-- New Fields Row -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mt-2">
                <!-- Employee -->
                <div>
                    <label class="block text-xs font-medium text-gray-700 mb-1">{{ __('manufacturing::manufacturing.employee') }}</label>
                    <select id="employee-select-visible"
                        class="w-full h-10 text-sm border-gray-200 rounded-lg focus:ring-primary focus:border-primary">
                        @foreach ($employees as $employee)
                            <option value="{{ $employee->id }}" {{ $loop->first ? 'selected' : '' }}>
                                {{ $employee->aname }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Invoice Number -->
                <div>
                    <label class="block text-xs font-medium text-gray-700 mb-1">{{ __('manufacturing::manufacturing.invoice number') }}</label>
                    <input type="text" id="display-invoice-number" value="{{ $nextInvoiceNumber }}" readonly
                        class="w-full h-10 text-sm border border-emerald-200 bg-emerald-50 rounded-lg focus:ring-emerald-500 focus:border-emerald-500 font-bold text-emerald-800">
                </div>

                <!-- Batch Number -->
                <div>
                    <label class="block text-xs font-medium text-gray-700 mb-1">{{ __('manufacturing::manufacturing.batch number') }}</label>
                    <input type="text" id="display-patch-number" placeholder="{{ __('manufacturing::manufacturing.enter batch number') }}"
                        class="w-full h-10 text-sm border-gray-200 rounded-lg focus:ring-primary focus:border-primary">
                </div>
            </div>
        </header>

        <!-- Main Content -->
        <main class="w-full grid grid-cols-12 gap-6 p-6">
            <!-- Left: Inputs & Tables (8 columns) -->
            <div class="col-span-12 lg:col-span-8 space-y-6">
                <!-- Template Info Section -->
                <section class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">{{ __('manufacturing::manufacturing.template name') }}</label>
                            <input type="text" id="template-name-input"
                                class="w-full px-4 py-2 border border-gray-200 rounded-lg focus:ring-primary focus:border-primary"
                                placeholder="{{ __('manufacturing::manufacturing.enter template name') }}">
                        </div>
                        <div>
                            <label
                                class="block text-sm font-medium text-gray-700 mb-2">{{ __('manufacturing::manufacturing.expected production time (hours)') }}</label>
                            <input type="number" id="actual-time-input"
                                class="w-full px-4 py-2 border border-gray-200 rounded-lg focus:ring-primary focus:border-primary"
                                placeholder="0">
                        </div>
                    </div>
                </section>

                <!-- Manufactured Products Section -->
                <section class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-lg font-bold text-gray-800">{{ __('manufacturing::manufacturing.manufactured products') }}</h3>
                    </div>

                    <!-- Product Search -->
                    <div class="flex gap-3 mb-6">
                        <div class="relative flex-grow">
                            <input type="text" id="product-search"
                                class="w-full h-11 text-sm border-gray-200 rounded-lg focus:ring-primary focus:border-primary pr-10"
                                placeholder="{{ __('manufacturing::manufacturing.search for product...') }}" autocomplete="off" />
                            <div
                                class="absolute inset-y-0 right-0 pr-3.5 flex items-center pointer-events-none text-gray-400">
                                <i class="las la-search scale-125"></i>
                            </div>
                            <div id="product-search-results"
                                class="absolute w-full mt-1 bg-white border border-gray-200 rounded-lg shadow-lg z-50 max-h-60 overflow-y-auto hidden">
                            </div>
                        </div>

                        <select id="product-account"
                            class="w-48 h-11 text-sm border-gray-200 rounded-lg focus:ring-primary focus:border-primary bg-gray-50">
                            @foreach ($accounts as $account)
                                <option value="{{ $account->id }}" {{ $loop->first ? 'selected' : '' }}>{{ $account->aname }}</option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Products Table -->
                    <div id="products-container">
                        <div id="products-empty-state"
                            class="empty-state-card bg-gray-50/50 rounded-xl border-2 border-dashed border-gray-200 py-12">
                            <p class="text-gray-500 font-medium text-center">{{ __('manufacturing::manufacturing.no products added') }}</p>
                        </div>

                        <div id="products-table-container" class="overflow-x-auto hidden">
                            <table class="w-full desktop-table">
                                <thead class="bg-gray-50 border-b border-gray-200">
                                    <tr class="text-center text-gray-600 font-medium text-xs">
                                        <th class="px-2 py-2">{{ __('manufacturing::manufacturing.product') }}</th>
                                        <th class="px-2 py-2">{{ __('manufacturing::manufacturing.unit') }}</th>
                                        <th class="px-2 py-2">{{ __('manufacturing::manufacturing.quantity') }}</th>
                                        <th class="px-2 py-2">{{ __('manufacturing::manufacturing.unit cost') }}</th>
                                        <th class="px-2 py-2">{{ __('manufacturing::manufacturing.cost percentage') }} %</th>
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
                                        placeholder="{{ __('manufacturing::manufacturing.search for raw material...') }}" autocomplete="off" />
                                    <div
                                        class="absolute inset-y-0 right-0 pr-3.5 flex items-center pointer-events-none text-gray-400">
                                        <i class="las la-search scale-125"></i>
                                    </div>
                                    <div id="raw-material-search-results"
                                        class="absolute w-full mt-1 bg-white border border-gray-200 rounded-lg shadow-lg z-50 max-h-60 overflow-y-auto hidden">
                                    </div>
                                </div>

                                <select id="raw-material-account"
                                    class="w-48 h-11 text-sm border-gray-200 rounded-lg focus:ring-primary focus:border-primary bg-gray-50">
                                    @foreach ($rawMaterialAccounts as $account)
                                        <option value="{{ $account->id }}" {{ $loop->first ? 'selected' : '' }}>{{ $account->aname }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div id="raw-materials-container">
                                <div id="raw-materials-empty-state"
                                    class="empty-state-card bg-gray-50/50 rounded-xl border-2 border-dashed border-gray-200 py-12">
                                    <p class="text-gray-500 font-medium text-center">{{ __('manufacturing::manufacturing.no raw materials added') }}
                                    </p>
                                </div>

                                <div id="raw-materials-table-container" class="overflow-x-auto hidden">
                                    <table class="w-full desktop-table">
                                        <thead class="bg-gray-50 border-b border-gray-200">
                                            <tr class="text-center text-gray-600 font-medium text-xs">
                                                <th class="px-2 py-2 text-right">{{ __('manufacturing::manufacturing.raw material') }}</th>
                                                <th class="px-2 py-2">{{ __('manufacturing::manufacturing.unit') }}</th>
                                                <th class="px-2 py-2">{{ __('manufacturing::manufacturing.quantity') }}</th>
                                                <th class="px-2 py-2">{{ __('manufacturing::manufacturing.cost price') }}</th>
                                                <th class="px-2 py-2">{{ __('manufacturing::manufacturing.total') }}</th>
                                                <th class="px-2 py-2">{{ __('manufacturing::manufacturing.actions') }}</th>
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
                                <h3 class="text-lg font-bold text-gray-800">{{ __('manufacturing::manufacturing.additional expenses') }}</h3>
                                <button type="button" id="btn-add-expense"
                                    class="h-10 px-4 bg-primary text-white rounded-lg hover:bg-accent transition-all flex items-center gap-2">
                                    <i class="las la-plus"></i>
                                    {{ __('manufacturing::manufacturing.add expense') }}
                                </button>
                            </div>

                            <div id="expenses-container">
                                <div id="expenses-empty-state"
                                    class="empty-state-card bg-gray-50/50 rounded-xl border-2 border-dashed border-gray-200 py-12">
                                    <p class="text-gray-500 font-medium text-center">{{ __('manufacturing::manufacturing.no expenses added') }}</p>
                                </div>

                                <div id="expenses-table-container" class="overflow-x-auto hidden">
                                    <table class="w-full desktop-table">
                                        <thead class="bg-gray-50 border-b border-gray-200">
                                            <tr class="text-center text-gray-600 font-medium text-xs">
                                                <th class="px-2 py-2">{{ __('manufacturing::manufacturing.amount') }}</th>
                                                <th class="px-2 py-2">{{ __('manufacturing::manufacturing.account') }}</th>
                                                <th class="px-2 py-2 text-right">{{ __('manufacturing::manufacturing.description') }}</th>
                                                <th class="px-2 py-2">{{ __('manufacturing::manufacturing.actions') }}</th>
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
            <div class="col-span-12 lg:col-span-4 ps-4">
                <div class="sticky top-24 space-y-6">
                    <!-- Cost Summary -->
                    <div class="bg-white rounded-xl shadow-lg border-0 p-6">
                        <h3 class="text-lg font-bold mb-4 flex items-center gap-2">
                            <i class="las la-calculator text-primary"></i>
                            {{ __('manufacturing::manufacturing.cost summary') }}
                        </h3>

                        <div class="space-y-4">
                            <div class="flex justify-between items-center text-sm">
                                <span class="text-gray-600">{{ __('manufacturing::manufacturing.total raw materials') }}</span>
                                <span class="font-bold text-blue-600" id="summary-raw-materials">0.00</span>
                            </div>
                            <div class="flex justify-between items-center text-sm">
                                <span class="text-gray-600">{{ __('manufacturing::manufacturing.total expenses') }}</span>
                                <span class="font-bold text-amber-600" id="summary-expenses">0.00</span>
                            </div>
                            <div class="flex justify-between items-center pt-3 border-t">
                                <span class="font-bold text-gray-800">{{ __('manufacturing::manufacturing.total template cost') }}</span>
                                <span class="font-black text-red-600 text-lg" id="summary-invoice-cost">0.00</span>
                            </div>
                            <div class="flex justify-between items-center pt-1">
                                <span class="font-bold text-gray-800">{{ __('manufacturing::manufacturing.total products value') }}</span>
                                <span class="font-black text-emerald-600 text-lg" id="summary-products">0.00</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </main>

        <!-- Hidden form for submission -->
        <form id="manufacturing-form" method="POST" action="{{ route('manufacturing.templates.store') }}">
            @csrf
            <input type="hidden" name="products_data" id="form-products">
            <input type="hidden" name="raw_materials_data" id="form-raw-materials">
            <input type="hidden" name="expenses_data" id="form-expenses">
            <input type="hidden" name="template_name" id="form-template-name">
            <input type="hidden" name="expected_time" id="form-expected-time">
            <input type="hidden" name="acc1" id="product-account-input">
            <input type="hidden" name="acc2" id="raw-account-input">
            <input type="hidden" name="emp_id" id="employee-id">
            <input type="hidden" name="pro_id" id="pro-id" value="{{ $nextInvoiceNumber }}">
            <input type="hidden" name="patch_number" id="patch-number">
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
                            accent: '#006d62'
                        },
                        fontFamily: {
                            sans: ['Tajawal', 'Inter', 'sans-serif']
                        }
                    }
                }
            }
        </script>
    @endpush

    @push('scripts')
        <script src="https://cdn.jsdelivr.net/npm/fuse.js@7.0.0"></script>
        @vite(['Resources/assets/js/manufacturing-form.js'], 'build-manufacturing')
        <script>
            window.__ = function(key) {
                const trans = {
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
                    'Failed to load templates': '{{ __('manufacturing::manufacturing.unknown') }}',
                    'manufacturing::manufacturing.products': '{{ __('manufacturing::manufacturing.manufactured products') }}',
                    'manufacturing::manufacturing.raw_materials': '{{ __('manufacturing::manufacturing.raw materials') }}',
                    'manufacturing::manufacturing.item_exists_in_raw_materials': '{{ __('manufacturing::manufacturing.item_exists_in_raw_materials') }}',
                    'manufacturing::manufacturing.item_exists_in_products': '{{ __('manufacturing::manufacturing.item_exists_in_products') }}',
                    'manufacturing::manufacturing.item_already_added': '{{ __('manufacturing::manufacturing.item_already_added') }}',
                    'manufacturing::manufacturing.products_account_required': '{{ __('manufacturing::manufacturing.products_account_required') }}',
                    'manufacturing::manufacturing.raw_materials_account_required': '{{ __('manufacturing::manufacturing.raw_materials_account_required') }}',
                    'manufacturing::manufacturing.products_required': '{{ __('manufacturing::manufacturing.products_required') }}',
                    'manufacturing::manufacturing.products_account_not_inventory': '{{ __('manufacturing::manufacturing.products_account_not_inventory') }}',
                    'manufacturing::manufacturing.raw_materials_account_not_inventory': '{{ __('manufacturing::manufacturing.raw_materials_account_not_inventory') }}',
                    'manufacturing::manufacturing.accounting_period_closed': '{{ __('manufacturing::manufacturing.accounting_period_closed') }}'
                };
                return trans[key] || key;
            };

            window.expenseAccounts = @json(collect($expenseAccounts)->mapWithKeys(fn($a) => [$a->id => $a->aname])->toArray());

            window.manufacturingConfig = {
                isEditMode: false,
                isTemplateMode: true,
                initialData: {
                    products: [],
                    rawMaterials: [],
                    expenses: []
                },
                routes: {
                    allItems: '/manufacturing/api/all-items',
                    searchProducts: '/manufacturing/api/search-products',
                    searchRawMaterials: '/manufacturing/api/search-raw-materials',
                    getItemWithUnits: '/manufacturing/api/get-item-units/:id',
                    getAvailableStock: '/manufacturing/api/get-available-stock'
                }
            };

            document.addEventListener('DOMContentLoaded', function() {
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

                        // Trigger initial sync
                        target.value = source.value;
                    }
                }

                syncInput('display-patch-number', 'patch-number');
                syncInput('display-invoice-number', 'pro-id');
                syncInput('employee-select-visible', 'employee-id');
                syncInput('template-name-input', 'form-template-name');
                syncInput('actual-time-input', 'form-expected-time');
                syncInput('product-account', 'product-account-input');
                syncInput('raw-material-account', 'raw-account-input');
            });
        </script>
    @endpush
@endsection
