@extends('admin.dashboard')

@section('body_class', 'invoice-page-fixed')

@section('sidebar')
    @if (in_array($type, [10, 12, 14, 16, 22, 26]))
        @include('components.sidebar.sales-invoices')
    @elseif (in_array($type, [11, 13, 15, 17, 24, 25]))
        @include('components.sidebar.purchases-invoices')
    @elseif (in_array($type, [18, 19, 20, 21]))
        @include('components.sidebar.inventory-invoices')
    @endif
@endsection

@section('content')

    @push('styles')
        <style>
            /* Body padding for fixed footer - NO SCROLL */
            /* NO PAGE SCROLL - Fixed layout components */
            body.invoice-page-fixed {
                height: 100vh !important;
                overflow: hidden !important;
            }

            .invoice-page-fixed .page-wrapper {
                height: 100vh !important;
                display: flex !important;
                flex-direction: column !important;
                overflow: hidden !important;
            }

            .invoice-page-fixed .page-content {
                flex: 1 !important;
                display: flex !important;
                flex-direction: column !important;
                overflow: hidden !important;
                padding: 0 !important;
            }

            .invoice-page-fixed .container-fluid,
            .invoice-page-fixed .container-fluid>.row {
                flex: 1 !important;
                display: flex !important;
                flex-direction: column !important;
                overflow: hidden !important;
                margin: 0 !important;
                padding: 0 !important;
            }

            .invoice-page-fixed #invoice-app {
                flex: 1 !important;
                display: flex !important;
                flex-direction: column !important;
                overflow: hidden !important;
                background: #fff;
            }

            .invoice-page-fixed #invoice-form {
                flex: 1 !important;
                display: flex !important;
                flex-direction: column !important;
                overflow: hidden !important;
            }

            .invoice-page-fixed .invoice-scroll-container {
                flex: 1 !important;
                overflow-y: auto !important;
                overflow-x: hidden !important;
                padding: 15px !important;
            }

            /* Allow dropdown to show outside scroll container */
            .invoice-scroll-container .table-responsive {
                overflow: visible !important;
            }

            .invoice-page-fixed #invoice-fixed-footer {
                flex-shrink: 0 !important;
                width: 100% !important;
                z-index: 10;
            }

            /* Hide footer when modal is open */
            .modal-open #invoice-fixed-footer {
                z-index: 0 !important;
                visibility: hidden !important;
            }

            /* Ensure modals appear above footer */
            .modal-backdrop {
                z-index: 1050 !important;
            }

            .modal {
                z-index: 1055 !important;
            }

            /* Ensure SweetAlert appears above everything */
            .swal2-container {
                z-index: 10000 !important;
            }

            /* Header styling to match image */
            .invoice-header-card {
                background: #f8f9fa;
                border: 2px solid #6c757d;
                border-radius: 8px;
                margin-bottom: 15px;
            }

            .invoice-header-card .card-header {
                background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
                color: white;
                border-radius: 6px 6px 0 0;
                padding: 10px 15px;
            }

            /* Table header styling to match image */
            .invoice-data-grid thead th {
                background: linear-gradient(135deg, #a8c0ff 0%, #c5d9ff 100%) !important;
                color: #2c3e50;
                font-weight: 600;
                text-align: center;
                border: 1px solid #90a4ae;
            }

            /* Search row styling */
            .invoice-data-grid .search-row {
                background: #e3f2fd !important;
            }

            text-align: center;
            }

            .footer-value.total {
                background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
                color: white;
                font-size: 1.2rem;
            }

            /* Button styling */
            .btn-save-invoice {
                background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%);
                border: none;
                color: white;
                font-weight: 600;
                padding: 12px 30px;
                border-radius: 8px;
                box-shadow: 0 4px 15px rgba(17, 153, 142, 0.3);
                transition: all 0.3s ease;
            }

            .btn-save-invoice:hover {
                transform: translateY(-2px);
                box-shadow: 0 6px 20px rgba(17, 153, 142, 0.4);
            }


            /* Hidden class */
            .hidden {
                display: none !important;
            }

            /* Select2 dropdown positioning */
            .select2-container {
                z-index: 1040 !important;
            }

            .select2-dropdown {
                z-index: 1045 !important;
            }

            /* When modal is open, Select2 inside modal should be above modal */
            .modal .select2-container {
                z-index: 1056 !important;
            }

            .modal .select2-dropdown {
                z-index: 1057 !important;
            }

            /* But Select2 outside modal should be below modal */
            body:not(.modal) .select2-container:not(.select2-container--open) {
                z-index: 1040 !important;
            }

            /* Search dropdown must be above everything */
            #search-results-dropdown {
                position: absolute !important;
                top: 100% !important;
                left: 0 !important;
                right: 0 !important;
                max-width: 100% !important;
                margin-top: 2px !important;
            }

            #search-results-dropdown * {
                visibility: visible !important;
                opacity: 1 !important;
            }

            #search-results-dropdown>div {
                display: flex !important;
            }

            /* Invoice item row hover effect */
            .invoice-item-row {
                transition: background-color 0.2s ease;
            }

            .invoice-item-row:hover {
                background-color: rgba(255, 235, 59, 0.3) !important;
                /* أصفر شفاف */
            }

            /* ✅ Validation States */
            .is-invalid {
                border-color: #dc3545 !important;
                background-color: #fff5f5 !important;
            }

            .is-invalid:focus {
                border-color: #dc3545 !important;
                box-shadow: 0 0 0 0.2rem rgba(220, 53, 69, 0.25) !important;
            }

            .is-warning {
                border-color: #ffc107 !important;
                background-color: #fffbf0 !important;
            }

            .is-warning:focus {
                border-color: #ffc107 !important;
                box-shadow: 0 0 0 0.2rem rgba(255, 193, 7, 0.25) !important;
            }

            .invalid-feedback {
                display: block;
                font-size: 0.875rem;
                color: #dc3545;
                margin-top: 0.25rem;
            }
        </style>
    @endpush
    {{-- Pure HTML - No Alpine --}}
    <div id="invoice-app">
        <form id="invoice-form" method="POST"
            action="@if (isset($isEditMode) && $isEditMode) {{ route('invoices.update', $invoiceId) }}@else{{ route('invoices.store') }} @endif">
            @csrf
            @if (isset($isEditMode) && $isEditMode)
                @method('PUT')
            @endif

            {{-- Success Message --}}
            @if (session('success'))
                <script>
                    document.addEventListener('DOMContentLoaded', function() {
                        Swal.fire({
                            icon: 'success',
                            title: '{{ __('invoices.done_successfully') }}',
                            text: '{{ session('success') }}',
                            timer: 2000,
                            showConfirmButton: false
                        });
                    });
                </script>
            @endif

            {{-- Error Display --}}
            @if ($errors->any())
                <div class="alert alert-danger shadow-sm mb-3">
                    <ul class="mb-0">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            {{-- Hidden inputs for all invoice data --}}
            <input type="hidden" name="type" id="form-type">
            <input type="hidden" name="branch_id" id="form-branch-id">
            <input type="hidden" name="template_id" id="form-template-id">
            <input type="hidden" name="pro_id" id="form-pro-id">
            <input type="hidden" name="acc1_id" id="form-acc1-id">
            <input type="hidden" name="acc2_id" id="form-acc2-id">
            <input type="hidden" name="pro_date" id="form-pro-date">
            <input type="hidden" name="emp_id" id="form-emp-id">
            <input type="hidden" name="delivery_id" id="form-delivery-id">
            <input type="hidden" name="accural_date" id="form-accural-date">
            <input type="hidden" name="serial_number" id="form-serial-number">
            <input type="hidden" name="cash_box_id" id="form-cash-box-id">
            <input type="hidden" name="notes" id="form-notes">
            <input type="hidden" name="payment_notes" id="form-payment-notes">
            <input type="hidden" name="discount_percentage" id="form-discount-percentage">
            <input type="hidden" name="discount_value" id="form-discount-value">
            <input type="hidden" name="additional_percentage" id="form-additional-percentage">
            <input type="hidden" name="additional_value" id="form-additional-value">
            <input type="hidden" name="vat_percentage" id="form-vat-percentage">
            <input type="hidden" name="vat_value" id="form-vat-value">
            <input type="hidden" name="withholding_tax_percentage" id="form-withholding-tax-percentage">
            <input type="hidden" name="withholding_tax_value" id="form-withholding-tax-value">
            <input type="hidden" name="subtotal" id="form-subtotal">
            <input type="hidden" name="total_after_additional" id="form-total-after-additional">
            <input type="hidden" name="received_from_client" id="form-received-from-client">
            <input type="hidden" name="remaining" id="form-remaining">
            <input type="hidden" name="currency_id" id="form-currency-id" value="1">
            <input type="hidden" name="currency_rate" id="form-currency-rate" value="1">
            <div id="form-items-container"></div>

            {{-- Part 1: Invoice Header --}}
            <div class="invoice-header-card">
                @include('invoices::components.invoices.invoice-head', [
                    'type' => $type,
                    'nextProId' => $nextProId,
                    'branches' => $branches,
                    'acc1Role' => $type == 21 ? __('invoices.from_store') : (in_array($type, [10, 12, 14, 16, 19, 22]) ? __('invoices.customer') : __('invoices.supplier')),
                    'acc2Role' => $type == 21 ? __('invoices.to_store') : __('invoices.store'),
                    'acc1Options' => $acc1Options,
                    'acc2List' => $acc2List,
                    'employees' => $employees,
                    'deliverys' => $deliverys,
                    'cashAccounts' => $cashAccounts,
                    'showBalance' => setting('show_balance', '1') === '1',
                    'currentBalance' => 0,
                    'balanceAfterInvoice' => 0,
                    'currency_id' => 1,
                    'currency_rate' => 1,
                    'defaultTemplateId' => $defaultTemplateId ?? null, // ✅ Pass template ID for edit mode
                ])
            </div>

            @include('invoices::components.invoices.invoice-item-table', [
                'type' => $type,
                'branchId' => $branchId,
                'templateColumns' => $templateColumns ?? [], // ✅ Pass template columns for instant rendering
                'templateWidths' => $templateWidths ?? [], // ✅ Pass template widths for instant rendering
            ])
        </form>
    </div>

    {{-- Invoice Footer - NOT fixed, at bottom of content --}}
    <div class="invoice-footer-container">
        @include('invoices::components.invoices.invoice-footer', [
            'type' => $type,
            'vatPercentage' => isVatEnabled() ? setting('default_vat_percentage', 0) : 0,
            'withholdingTaxPercentage' => isWithholdingTaxEnabled()
                ? setting('default_withholding_tax_percentage', 0)
                : 0,
            'showBalance' => setting('show_balance', '1') === '1',
            'cashAccounts' => $cashAccounts,
        ])
    </div>

    {{-- Installment Modal --}}
    @if (setting('enable_installment_from_invoice') && $type == 10)
        @livewire('installments::create-installment-from-invoice', [
            'invoiceTotal' => 0,
            'clientAccountId' => null,
        ])
    @endif

    {{-- Account Creator Modal (Hidden by default) --}}
    <div id="account-creator-container" style="display: none;">
        @if (in_array($type, [10, 12, 14, 16, 19, 22, 26]))
            {{-- Sales invoices - Add Client --}}
            @livewire('accounts::account-creator', ['type' => 'client'])
        @elseif (in_array($type, [11, 13, 15, 17, 20, 24, 25]))
            {{-- Purchase invoices - Add Supplier --}}
            @livewire('accounts::account-creator', ['type' => 'supplier'])
        @endif
    </div>
@endsection

@section('script')
    @php
        // ✅ Prepare all config as JSON to avoid Blade syntax issues
        $invoiceConfig = [
            'type' => $type,
            'branchId' => $branchId ?? null,
            'vatPercentage' => isVatEnabled() ? setting('default_vat_percentage', 0) : 0,
            'withholdingTaxPercentage' => isWithholdingTaxEnabled()
                ? setting('default_withholding_tax_percentage', 0)
                : 0,
            'storeUrl' => route('invoices.store'),
            'userSettings' => $userSettings ?? [],
            'defaultAcc1Id' => $defaultAcc1Id ?? null,
            'defaultAcc2Id' => $defaultAcc2Id ?? null,
            'translations' => [
                'item_name' => __('invoices.item_name_col'),
                'code' => __('invoices.code_col'),
                'unit' => __('invoices.unit_col'),
                'quantity' => __('invoices.quantity_col'),
                'batch_number' => __('invoices.batch_number_col'),
                'expiry_date' => __('invoices.expiry_date_col'),
                'price' => __('invoices.price_col'),
                'discount' => __('invoices.discount_col'),
                'discount_percentage' => __('invoices.discount_pct'),
                'discount_value' => __('invoices.discount_value_label'),
                'sub_value' => __('invoices.value_col'),
                'length' => __('invoices.length'),
                'width' => __('invoices.width'),
                'height' => __('invoices.height'),
                'density' => __('invoices.density'),
                'action' => __('invoices.action_col'),
            ],
        ];
    @endphp

    {{-- Main Invoice JavaScript --}}
    <script>
        // ✅ Config from PHP (safe JSON encoding)
        const CONFIG = @json($invoiceConfig);
        const INVOICE_STORE_URL = CONFIG.storeUrl;

        // Invoice State (Global)
        window.InvoiceApp = {
            // Config
            type: CONFIG.type,
            settings: CONFIG.userSettings || {},
            branchId: CONFIG.branchId,
            vatPercentage: CONFIG.vatPercentage,
            withholdingTaxPercentage: CONFIG.withholdingTaxPercentage,
            currencyId: 1, // Default
            exchangeRate: 1, // Default
            selectedPriceListId: null, // Selected price list for sales invoices

            // Template columns
            visibleColumns: ['item_name', 'code', 'unit', 'quantity', 'price', 'discount_percentage', 'discount_value',
                'sub_value'
            ],
            allColumns: CONFIG.translations,
            columnWidths: {},

            // Data
            invoiceItems: [],
            allItems: [],
            fuse: null,
            isSaved: false, // ✅ Track if invoice is saved

            // Totals
            subtotal: 0,
            discountPercentage: 0,
            discountValue: 0,
            additionalPercentage: 0,
            additionalValue: 0,
            vatValue: 0,
            withholdingTaxValue: 0,
            totalAfterAdditional: 0,
            receivedFromClient: 0,
            remaining: 0,

            // Account Balance
            currentBalance: 0,
            calculatedBalanceAfter: 0,

            // Search
            searchResults: [],
            selectedIndex: -1,

            // Initialize
            init() {
                @if (isset($isEditMode) && $isEditMode)
                    // ✅ Set template columns and widths IMMEDIATELY from server-side (NO DELAY)
                    @if (!empty($templateColumns))
                        let columns = @json($templateColumns);
                        
                        // Hide expiry columns if disabled in settings
                        if (this.settings.expiry_mode && this.settings.expiry_mode.disabled) {
                            columns = columns.filter(c => c !== 'batch_number' && c !== 'expiry_date');
                        }
                        
                        this.visibleColumns = columns;
                    @endif

                    @if (!empty($templateWidths))
                        this.columnWidths = @json($templateWidths);
                    @endif
                @endif

                this.initializeSelect2();
                @if (setting('multi_currency_enabled'))
                    this.loadCurrencies(); // ✅ Load currencies only if enabled
                @endif
                @if (isset($isEditMode) && $isEditMode)
                    // ✅ For edit mode: Load invoice data (template already set above)
                    this.loadInvoiceData(); // ✅ Load existing invoice data
                @else
                    // ✅ For create mode: Load default template, then set default values
                    this.loadDefaultTemplate();
                    this.setDefaultValues(); // ✅ Only set defaults for new invoices
                @endif
                this.loadItems();
                this.attachEventListeners();
                @if (!isset($isEditMode) || !$isEditMode)
                    this.renderItems();
                @endif
                this.initializePriceListSelector();
            },

            // Load default template
            loadDefaultTemplate() {
                const templateSelect = document.getElementById('invoice-template');
                if (templateSelect) {
                    const defaultOption = templateSelect.querySelector('option[selected]');
                    if (defaultOption) {
                        const columnsJson = defaultOption.getAttribute('data-columns');
                        if (columnsJson) {
                            try {
                                let columns = JSON.parse(columnsJson);

                                // ✅ Hide expiry columns if disabled in settings
                                if (this.settings.expiry_mode && this.settings.expiry_mode.disabled) {
                                    columns = columns.filter(c => c !== 'batch_number' && c !== 'expiry_date');
                                }

                                this.visibleColumns = columns;

                                // ✅ Also fetch full template data for column widths
                                const templateId = templateSelect.value;
                                fetch(`/invoice-templates/${templateId}/data`)
                                    .then(res => res.json())
                                    .then(result => {
                                        if (result.success && result.data.column_widths) {
                                            this.columnWidths = result.data.column_widths;
                                            this.updateTableHeaders();
                                            this.renderItems();
                                        }
                                    });
                            } catch (e) {
                                console.error('❌ Error parsing default template columns:', e);
                            }
                        }
                    }
                }
            },

            // Initialize Select2 for searchable dropdowns
            initializeSelect2() {
                // Save reference to this
                const self = this;

                // Initialize Select2 for acc1 (Customer/Supplier) with search
                $('#acc1-id').select2({
                    theme: 'bootstrap-5',
                    placeholder: "{{ __('invoices::invoices.search_for_client_supplier') }}",
                    allowClear: true,
                    dropdownParent: $('#invoice-app'),
                    language: {
                        noResults: () => "{{ __('invoices::invoices.no_results') }}",
                        searching: () => "{{ __('invoices::invoices.searching') }}"
                    }
                });

                // ✅ Attach event listener for account balance updates
                $('#acc1-id').on('change', function(e) {
                    const accountId = $(this).val();

                    if (accountId) {
                        self.updateAccountBalance(accountId);
                    } else {
                        self.currentBalance = 0;
                        self.calculatedBalanceAfter = 0;

                        // Clear balance display
                        const currentBalanceEl = document.getElementById('current-balance-header');
                        const balanceAfterEl = document.getElementById('balance-after-header');
                        if (currentBalanceEl) currentBalanceEl.textContent = '0.00';
                        if (balanceAfterEl) balanceAfterEl.textContent = '0.00';

                        // Clear recommended items
                        self.clearRecommendedItems();
                    }
                });

                // Initialize Select2 for acc2 (Store) with search
                $('#acc2-id').select2({
                    theme: 'bootstrap-5',
                    placeholder: "{{ __('invoices::invoices.search_for_store') }}",
                    allowClear: true,
                    dropdownParent: $('#invoice-app'),
                    language: {
                        noResults: () => "{{ __('invoices::invoices.no_results') }}",
                        searching: () => "{{ __('invoices::invoices.searching') }}"
                    }
                });
            },

            // Set default values from settings (for new invoices only)
            setDefaultValues() {
                // Set default employee
                const defaultEmployeeId = '{{ $defaultEmployeeId ?? '' }}';
                if (defaultEmployeeId) {
                    const empElement = document.getElementById('emp-id');
                    if (empElement) {
                        empElement.value = defaultEmployeeId;
                    }
                }

                // Set default delivery
                const defaultDeliveryId = '{{ $defaultDeliveryId ?? '' }}';
                if (defaultDeliveryId) {
                    const deliveryElement = document.getElementById('delivery-id');
                    if (deliveryElement) {
                        deliveryElement.value = defaultDeliveryId;
                    }
                }

                // Set default customer/supplier and store from CONFIG
                if (CONFIG.defaultAcc1Id) {
                    $('#acc1-id').val(CONFIG.defaultAcc1Id).trigger('change');
                }

                if (CONFIG.defaultAcc2Id) {
                    $('#acc2-id').val(CONFIG.defaultAcc2Id).trigger('change');
                }
            },

            @if (isset($isEditMode) && $isEditMode)
                // Load existing invoice data for edit mode
                loadInvoiceData() {
                        let invoiceData;

                        try {
                            invoiceData = @json($invoice ?? null);

                            if (!invoiceData) {
                                alert("{{ __('invoices::invoices.error') }}: {{ __('invoices::invoices.no_data_available') }}");
                                return;
                            }

                            if (!invoiceData.operation_items || invoiceData.operation_items.length === 0) {
                                console.warn('⚠️ ' + "{{ __('invoices::invoices.no_items') }}");
                                return;
                            }
                        } catch (error) {
                             alert("{{ __('invoices::invoices.error') }}: " + error.message);
                            return;
                        }

                        // ✅ Template already loaded in init() - no need to load again

                        // Set invoice header data
                        // ✅ Use correct column names from database (acc1, acc2, not acc1_id, acc2_id)
                        if (invoiceData.acc1) {
                            $('#acc1-id').val(invoiceData.acc1).trigger('change');
                        }

                        if (invoiceData.acc2) {
                            $('#acc2-id').val(invoiceData.acc2).trigger('change');
                        }

                        if (invoiceData.emp_id) {
                            const empSelect = document.getElementById('emp-id');
                            if (empSelect) empSelect.value = invoiceData.emp_id;
                        }

                        if (invoiceData.emp2_id) {
                            const deliverySelect = document.getElementById('delivery-id');
                            if (deliverySelect) deliverySelect.value = invoiceData.emp2_id;
                        }

                        if (invoiceData.pro_date) {
                            const dateInput = document.getElementById('pro-date');
                            if (dateInput) dateInput.value = invoiceData.pro_date;
                        }

                        if (invoiceData.accural_date) {
                            const accuralDateInput = document.getElementById('accural-date');
                            if (accuralDateInput) accuralDateInput.value = invoiceData.accural_date;
                        }

                        if (invoiceData.pro_serial) {
                            const serialInput = document.getElementById('serial-number');
                            if (serialInput) serialInput.value = invoiceData.pro_serial;
                        }

                        if (invoiceData.acc_fund) {
                            const cashBoxSelect = document.getElementById('cash_box_id');
                            if (cashBoxSelect) cashBoxSelect.value = invoiceData.acc_fund;
                        }

                        if (invoiceData.details) {
                            const notesInput = document.getElementById('notes');
                            if (notesInput) notesInput.value = invoiceData.details;
                        }

                        if (invoiceData.info2) {
                            const paymentNotesInput = document.getElementById('payment-notes');
                            if (paymentNotesInput) paymentNotesInput.value = invoiceData.info2;
                        }

                        // Set discount inputs
                        const discountPercentageInput = document.getElementById('discount-percentage');
                        const discountValueInput = document.getElementById('discount-value');
                        if (discountPercentageInput) {
                            discountPercentageInput.value = invoiceData.fat_disc_per || 0;
                        }
                        if (discountValueInput) {
                            discountValueInput.value = invoiceData.fat_disc || 0;
                        }

                        // Set additional inputs
                        const additionalPercentageInput = document.getElementById('additional-percentage');
                        const additionalValueInput = document.getElementById('additional-value');
                        if (additionalPercentageInput) {
                            additionalPercentageInput.value = invoiceData.fat_plus_per || 0;
                        }
                        if (additionalValueInput) {
                            additionalValueInput.value = invoiceData.fat_plus || 0;
                        }

                        // Set received from client
                        const receivedInput = document.getElementById('received-from-client');
                        if (receivedInput) {
                            receivedInput.value = invoiceData.paid_from_client || 0;
                        }

                        // Set totals
                        this.discountPercentage = parseFloat(invoiceData.fat_disc_per) || 0;

                        // ✅ Set currency data from invoice first
                        this.currencyId = invoiceData.currency_id || 1;
                        this.exchangeRate = parseFloat(invoiceData.currency_rate) || 1;

                        // ✅ Convert from base currency only if multi-currency is enabled
                        @if (setting('multi_currency_enabled'))
                            const exchangeRate = this.exchangeRate;
                        @else
                            const exchangeRate = 1;
                        @endif

                        this.discountValue = Math.round((parseFloat(invoiceData.fat_disc) || 0) / exchangeRate * 100) / 100;
                        this.additionalPercentage = parseFloat(invoiceData.fat_plus_per) || 0;
                        this.additionalValue = Math.round((parseFloat(invoiceData.fat_plus) || 0) / exchangeRate * 100) / 100;
                        this.receivedFromClient = Math.round((parseFloat(invoiceData.paid_from_client) || 0) / exchangeRate * 100) / 100;

                        // Clear existing items
                        this.invoiceItems = [];

                        // Load invoice items
                        if (invoiceData.operation_items && invoiceData.operation_items.length > 0) {
                            invoiceData.operation_items.forEach((item, index) => {
                                // ✅ Use fat_quantity (display quantity) instead of base quantity
                                const quantity = parseFloat(item.fat_quantity || 0);

                                // ✅ Use fat_price (display price) - this is the ORIGINAL price before any discounts
                                const priceInBaseCurrency = parseFloat(item.fat_price || item.item_price || 0);
                                
                                // ✅ Calculate original sub_value BEFORE item discount (quantity × price)
                                const originalSubValue = quantity * priceInBaseCurrency;

                                // ✅ Get item discount percentage (this is the discount on THIS item, not invoice discount)
                                const itemDiscountPercentage = parseFloat(item.item_discount_pre || 0);
                                
                                // ✅ Calculate item discount value from percentage
                                const itemDiscountValue = Math.round(originalSubValue * itemDiscountPercentage / 100 * 100) / 100;

                                @if (setting('multi_currency_enabled'))
                                    const exchangeRate = this.exchangeRate;
                                @else
                                    const exchangeRate = 1;
                                @endif

                                const price = Math.round(priceInBaseCurrency / exchangeRate * 100) / 100;
                                const discountValue = Math.round(itemDiscountValue / exchangeRate * 100) / 100;
                                const subValue = Math.round(originalSubValue / exchangeRate * 100) / 100;

                                const invoiceItem = {
                                    item_id: item.item_id,
                                    item_name: item.item?.name || 'Unknown Item',
                                    code: item.item?.code || '',
                                    unit_id: item.unit_id,
                                    unit_name: item.unit?.name || '',
                                    quantity: quantity, // ✅ Display quantity (fat_quantity)
                                    price: price, // ✅ Display price (fat_price) - ORIGINAL price
                                    discount: 0, // ✅ Initialize discount field
                                    discount_percentage: itemDiscountPercentage, // ✅ Item discount percentage
                                    discount_value: discountValue, // ✅ Item discount value (calculated from percentage)
                                    additional: 0, // ✅ Initialize additional field
                                    sub_value: subValue, // ✅ ORIGINAL sub_value (quantity × price) BEFORE discount
                                    batch_number: item.batch_number || '',
                                    expiry_date: item.expiry_date || '',
                                    notes: item.notes || '',
                                    length: 0, // ✅ Initialize length field
                                    width: 0, // ✅ Initialize width field
                                    height: 0, // ✅ Initialize height field
                                    density: 0, // ✅ Initialize density field
                                    units: item.item?.units || []
                                };

                                this.invoiceItems.push(invoiceItem);
                            });
                        }

                        console.log('✅ Invoice data loaded:', this.invoiceItems.length, 'items');
                        console.log('📊 Invoice items:', this.invoiceItems);

                        // Calculate totals and render
                        this.calculateTotals();
                        this.renderItems();
                        this.updateTotalsDisplay();
                    },
            @endif

            // ✅ Load currencies from API
            loadCurrencies() {
                fetch('/currencies/active', {
                        headers: {
                            'Accept': 'application/json',
                            'X-Requested-With': 'XMLHttpRequest'
                        }
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success && data.currencies) {
                            const currencySelect = document.getElementById('currency-id');
                            if (currencySelect) {
                                currencySelect.innerHTML = '';

                                data.currencies.forEach(currency => {
                                    const option = document.createElement('option');
                                    option.value = currency.id;
                                    option.textContent = `${currency.code} - ${currency.name}`;
                                    option.dataset.symbol = currency.symbol;
                                    option.dataset.decimals = currency.decimal_places;
                                    option.dataset.isDefault = currency.is_default;

                                    // Select the currency from invoice data
                                    if (currency.id == this.currencyId) {
                                        option.selected = true;
                                    }

                                    currencySelect.appendChild(option);
                                });

                                // Add change event listener
                                currencySelect.addEventListener('change', (e) => this.handleCurrencyChange(e));

                                // Update display for current currency
                                this.updateCurrencyRateDisplay(this.exchangeRate, this.currencyId == 1);
                            }
                        }
                    })
                    .catch(error => {
                        console.error('❌ Error loading currencies:', error);
                    });
            },

            // ✅ Handle currency change
            handleCurrencyChange(e) {
                const selectedOption = e.target.options[e.target.selectedIndex];
                const currencyId = parseInt(e.target.value);
                const isDefault = selectedOption.dataset.isDefault === 'true';

                this.currencyId = currencyId;

                if (isDefault) {
                    this.exchangeRate = 1;
                    this.updateCurrencyRateDisplay(1, true);
                    this.calculateTotals();
                } else {
                    // Fetch exchange rate
                    this.fetchExchangeRate(currencyId);
                }
            },

            // ✅ Fetch exchange rate for selected currency
            fetchExchangeRate(currencyId) {
                fetch(`/api/currencies/${currencyId}/rate`, {
                        headers: {
                            'Accept': 'application/json',
                            'X-Requested-With': 'XMLHttpRequest'
                        }
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success && data.rate) {
                            this.exchangeRate = parseFloat(data.rate);
                            this.updateCurrencyRateDisplay(this.exchangeRate, false);
                            this.calculateTotals();
                        } else {
                            this.exchangeRate = 1;
                            this.updateCurrencyRateDisplay(1, false);
                            console.warn('⚠️ No exchange rate found, using 1');
                        }
                    })
                    .catch(error => {
                        console.error('❌ Error fetching exchange rate:', error);
                        this.exchangeRate = 1;
                        this.updateCurrencyRateDisplay(1, false);
                    });
            },

            // ✅ Update currency rate display
            updateCurrencyRateDisplay(rate, isDefault) {
                const rateDisplay = document.getElementById('currency-rate-display');
                const rateValue = document.getElementById('currency-rate-value');

                if (rateDisplay && rateValue) {
                    if (isDefault) {
                        rateDisplay.style.display = 'none';
                    } else {
                        rateDisplay.style.display = 'block';
                        rateValue.textContent = rate.toFixed(4);
                    }
                }
            },

            // Load items from API
            loadItems() {
                const url = `/api/items/lite?branch_id=${this.branchId}&type=${this.type}&_t=${Date.now()}`;
                this.updateStatus('جاري تحميل الأصناف...', 'primary');

                fetch(url, {
                        headers: {
                            'Accept': 'application/json',
                            'X-Requested-With': 'XMLHttpRequest'
                        }
                    })
                    .then(response => {
                        if (!response.ok) {
                            throw new Error(`HTTP error! status: ${response.status}`);
                        }
                        return response.json();
                    })
                    .then(data => {

                        if (Array.isArray(data)) {
                            this.allItems = data;
                            this.updateStatus('تم تحميل ' + data.length + ' صنف - البحث جاهز ✓', 'success');
                        } else {
                            console.error('❌ Response is not an array:', data);
                            this.allItems = [];
                            this.updateStatus('خطأ: البيانات المستلمة غير صحيحة', 'danger');
                        }
                    })
                    .catch(error => {
                        console.error('❌ Error loading items:', error);
                        console.error('❌ Error details:', error.message);
                        this.allItems = [];
                        this.updateStatus('خطأ في تحميل الأصناف: ' + error.message, 'danger');
                    });
            },

            // Attach event listeners
            attachEventListeners() {
                // Search input
                const searchInput = document.getElementById('search-input');
                if (searchInput) {
                    searchInput.addEventListener('input', (e) => this.handleSearch(e.target.value));
                    searchInput.addEventListener('keydown', (e) => this.handleSearchKeydown(e));
                }

                // ✅ Barcode input - Add item on Enter
                const barcodeInput = document.getElementById('barcode-input');
                if (barcodeInput) {

                    barcodeInput.addEventListener('keydown', (e) => {
                        if (e.key === 'Enter') {
                            e.preventDefault();
                            const barcode = e.target.value.trim();
                            console.log('🔍 Barcode Enter pressed:', barcode);
                            if (barcode) {
                                this.handleBarcodeSearch(barcode);
                            }
                        }
                    });
                } else {
                    console.error('❌ Barcode input not found!');
                }

                // Form submit
                const form = document.getElementById('invoice-form');
                if (form) {
                    form.addEventListener('submit', (e) => {
                        e.preventDefault();
                        this.saveInvoice();
                    });
                }
                document.addEventListener('click', (e) => {
                    if (!e.target.closest('#search-results-dropdown') && e.target.id !== 'search-input') {
                        this.hideSearchResults();
                    }
                });
                // Discount/Additional inputs with auto-sync between percentage and value
                document.getElementById('discount-percentage')?.addEventListener('input', (e) => {
                    this.discountPercentage = parseFloat(e.target.value) || 0;
                    // Auto-calculate discount value from percentage
                    if (this.subtotal > 0) {
                        this.discountValue = (this.subtotal * this.discountPercentage) / 100;
                        document.getElementById('discount-value').value = this.discountValue.toFixed(2);
                    }
                    this.calculateTotals();
                });

                document.getElementById('discount-value')?.addEventListener('input', (e) => {
                    this.discountValue = parseFloat(e.target.value) || 0;
                    // Auto-calculate discount percentage from value
                    if (this.subtotal > 0) {
                        this.discountPercentage = (this.discountValue / this.subtotal) * 100;
                        document.getElementById('discount-percentage').value = this.discountPercentage.toFixed(
                            2);
                    }
                    this.calculateTotals();
                });

                document.getElementById('additional-percentage')?.addEventListener('input', (e) => {
                    this.additionalPercentage = parseFloat(e.target.value) || 0;
                    // Auto-calculate additional value from percentage
                    const afterDiscount = this.subtotal - this.discountValue;
                    if (afterDiscount > 0) {
                        this.additionalValue = (afterDiscount * this.additionalPercentage) / 100;
                        document.getElementById('additional-value').value = this.additionalValue.toFixed(2);
                    }
                    this.calculateTotals();
                });

                document.getElementById('additional-value')?.addEventListener('input', (e) => {
                    this.additionalValue = parseFloat(e.target.value) || 0;
                    // Auto-calculate additional percentage from value
                    const afterDiscount = this.subtotal - this.discountValue;
                    if (afterDiscount > 0) {
                        this.additionalPercentage = (this.additionalValue / afterDiscount) * 100;
                        document.getElementById('additional-percentage').value = this.additionalPercentage
                            .toFixed(2);
                    }
                    this.calculateTotals();
                });

                document.getElementById('received-from-client')?.addEventListener('input', (e) => {
                    this.receivedFromClient = parseFloat(e.target.value) || 0;
                    this.calculateTotals();
                });

                // Template selector
                document.getElementById('invoice-template')?.addEventListener('change', async (e) => {
                    const templateId = e.target.value;
                    const selectedOption = e.target.options[e.target.selectedIndex];
                    const columnsJson = selectedOption.getAttribute('data-columns');

                    if (templateId) {
                        try {
                            const response = await fetch(`/invoice-templates/${templateId}/data`);
                            const result = await response.json();

                            if (result.success && result.data.column_widths) {
                                this.columnWidths = result.data.column_widths;
                                // ✅ Sync with global helper
                                if (window.applyInvoiceTableColumnWidths) {
                                    window.applyInvoiceTableColumnWidths(result.data.column_widths);
                                }
                            }
                        } catch (error) {
                            console.error('❌ Error fetching template data:', error);
                        }
                    }

                    if (columnsJson) {
                        try {
                            let columns = JSON.parse(columnsJson);

                            // ✅ Hide expiry columns if disabled in settings
                            if (this.settings.expiry_mode && this.settings.expiry_mode.disabled) {
                                columns = columns.filter(c => c !== 'batch_number' && c !== 'expiry_date');
                            }

                            this.visibleColumns = columns;
                            this.updateTableHeaders();
                            this.renderItems();

                            // ✅ Focus على أول حقل editable في أول صنف (لو موجود)
                            if (this.invoiceItems.length > 0) {
                                requestAnimationFrame(() => {
                                    requestAnimationFrame(() => {
                                        const editableColumns = this.getEditableColumns();
                                        if (editableColumns.length > 0) {
                                            const firstEditableColumn = editableColumns[0];
                                            this.focusField(firstEditableColumn, 0);
                                        }
                                    });
                                });
                            }
                        } catch (error) {
                            console.error('❌ Error parsing columns:', error);
                        }
                    }
                });

                // ✅ Update item details when warehouse changes
                $('#acc2-id').on('change', () => {
                    if (this.lastSelectedIndex !== undefined) {
                        this.showItemDetails(this.lastSelectedIndex);
                    }
                });

                // ✅ Update item details when customer/supplier changes
                $('#acc1-id').on('change', () => {
                    if (this.lastSelectedIndex !== undefined) {
                        this.showItemDetails(this.lastSelectedIndex);
                    }

                    // Update installment modal data if it exists
                    this.updateInstallmentModalData();
                });

            },

            /**
             * Update installment modal data when client or total changes
             */
            updateInstallmentModalData() {
                const acc1Id = $('#acc1-id').val();

                if (!acc1Id || acc1Id === '' || acc1Id === 'null') {
                    return;
                }

                // Dispatch Livewire event to update modal data (without opening it)
                Livewire.dispatch('client-changed-in-invoice', {
                    invoiceTotal: this.totalAfterAdditional,
                    clientAccountId: acc1Id
                });
            },

            // Update table headers based on visible columns
            updateTableHeaders() {
                const thead = document.querySelector('.invoice-data-grid thead tr');
                if (!thead) return;

                // Clear existing headers (except action column)
                thead.innerHTML = '';

                // Add headers for visible columns
                this.visibleColumns.forEach(col => {
                    const th = document.createElement('th');
                    th.className = 'font-bold fw-bold text-center';
                    th.style.fontSize = '0.8rem';
                    th.textContent = this.allColumns[col] || col;

                    // ✅ Add attributes and apply width from template
                    th.setAttribute('data-column', col);
                    th.setAttribute('data-default-width', '100');

                    if (this.columnWidths && this.columnWidths[col]) {
                        // Ensure a minimum width of 5px to prevent broken layout (reduced from 50px as per user request)
                        const width = Math.max(5, parseInt(this.columnWidths[col]) || 0);
                        th.style.width = width + 'px';
                        th.style.minWidth = width + 'px';
                        // Removed maxWidth to allow growth if needed
                        console.log(`✅ Applied width ${width}px to col ${col}`);
                    }

                    thead.appendChild(th);
                });

                // Add action column
                const actionTh = document.createElement('th');
                actionTh.className = 'font-bold fw-bold text-center';
                actionTh.style.fontSize = '0.8rem';
                actionTh.style.width = '80px'; // Set a reasonable width for action column
                actionTh.style.minWidth = '80px';
                actionTh.textContent = CONFIG.translations.action;
                thead.appendChild(actionTh);
            },

            // Handle search
            handleSearch(term) {
                if (!term || term.length < 1) {
                    this.hideSearchResults();
                    return;
                }

                const lowerTerm = term.toLowerCase();
                this.searchResults = this.allItems.filter(item => {
                    // Name match
                    if (item.name && item.name.toLowerCase().includes(lowerTerm)) {
                        return true;
                    }

                    // Code match
                    if (item.code && item.code.toString().toLowerCase().includes(lowerTerm)) {
                        return true;
                    }

                    // Barcode match - handle all cases
                    if (item.barcode) {
                        // If it's an array
                        if (Array.isArray(item.barcode)) {
                            for (let i = 0; i < item.barcode.length; i++) {
                                if (item.barcode[i] && item.barcode[i].toString().toLowerCase().includes(
                                        lowerTerm)) {
                                    return true;
                                }
                            }
                        }
                        // If it's a string
                        else if (typeof item.barcode === 'string' && item.barcode.toLowerCase().includes(
                                lowerTerm)) {
                            return true;
                        }
                    }

                    return false;
                }).slice(0, 50);

                this.selectedIndex = this.searchResults.length > 0 ? 0 : -1;
                this.renderSearchResults();
                this.showSearchResults();
            },

            // Handle search keydown
            handleSearchKeydown(e) {
                const dropdown = document.getElementById('search-results-dropdown');
                const isDropdownVisible = dropdown && dropdown.style.display === 'block' && !dropdown.classList
                    .contains('hidden');

                if (e.key === 'Enter') {
                    e.preventDefault();
                    if (isDropdownVisible && this.selectedIndex >= 0 && this.searchResults[this.selectedIndex]) {
                        // Add selected item
                        this.addItem(this.searchResults[this.selectedIndex]);
                    } else if (isDropdownVisible && this.searchResults.length > 0) {
                        // Auto-select first result if none selected
                        this.addItem(this.searchResults[0]);
                    } else {
                        // Create new item if no results
                        const searchInput = document.getElementById('search-input');
                        if (searchInput && searchInput.value.trim()) {
                            this.createNewItem(searchInput.value.trim());
                        }
                    }
                    return;
                }

                if (!isDropdownVisible) return;

                switch (e.key) {
                    case 'ArrowDown':
                        e.preventDefault();
                        if (this.selectedIndex < this.searchResults.length - 1) {
                            this.selectedIndex++;
                            this.highlightSelectedResult();
                        }
                        break;
                    case 'ArrowUp':
                        e.preventDefault();
                        if (this.selectedIndex > 0) {
                            this.selectedIndex--;
                            this.highlightSelectedResult();
                        }
                        break;
                    case 'Escape':
                        e.preventDefault();
                        this.hideSearchResults();
                        break;
                }
            },
            highlightSelectedResult() {
                const dropdown = document.getElementById('search-results-dropdown');
                if (!dropdown) return;

                const items = dropdown.querySelectorAll('.search-result-item');
                items.forEach((item, index) => {
                    if (index === this.selectedIndex) {
                        // ✅ تظليل بالـ gradient البنفسجي
                        item.style.setProperty('background',
                            'linear-gradient(135deg, #667eea 0%, #764ba2 100%)', 'important');
                        item.style.setProperty('border-left', '4px solid #4051d4', 'important');
                        item.style.setProperty('box-shadow', '0 3px 10px rgba(102, 126, 234, 0.3)',
                        'important');
                        item.style.setProperty('transform', 'translateX(2px)', 'important');

                        // ✅ خلي كل النصوص بيضا
                        const allElements = item.querySelectorAll('div, span');
                        allElements.forEach(el => {
                            el.style.setProperty('color', 'white', 'important');
                            // لو badge، غير الخلفية
                            if (el.tagName === 'SPAN') {
                                el.style.setProperty('background', 'rgba(255, 255, 255, 0.25)',
                                    'important');
                            }
                        });

                        item.scrollIntoView({
                            block: 'nearest',
                            behavior: 'smooth'
                        });
                    } else {
                        // ✅ رجع للألوان الأصلية
                        item.style.setProperty('background', 'white', 'important');
                        item.style.setProperty('border-left', 'none', 'important');
                        item.style.setProperty('box-shadow', 'none', 'important');
                        item.style.setProperty('transform', 'none', 'important');

                        // رجع ألوان النصوص
                        const nameDiv = item.querySelector('div:first-child');
                        if (nameDiv) {
                            nameDiv.style.setProperty('color', '#1a1a1a', 'important');
                        }

                        // رجع ألوان الـ badges
                        const spans = item.querySelectorAll('span');
                        spans.forEach((span, spanIndex) => {
                            if (spanIndex === 0) { // الكود
                                span.style.setProperty('background', '#f0f0f0', 'important');
                                span.style.setProperty('color', '#555', 'important');
                            } else { // السعر
                                span.style.setProperty('background', '#e8f5e9', 'important');
                                span.style.setProperty('color', '#2e7d32', 'important');
                            }
                        });
                    }
                });
            },

            // Render search results
            renderSearchResults() {
                const dropdown = document.getElementById('search-results-dropdown');
                if (!dropdown) {
                    console.error('❌ Dropdown not found!');
                    return;
                }

                dropdown.innerHTML = '';

                if (this.searchResults.length === 0) {
                    const searchInput = document.getElementById('search-input');
                    const searchTerm = searchInput?.value || '';

                    if (searchTerm.trim().length > 0) {
                        const createBtn = document.createElement('div');
                        createBtn.className = 'create-new-item-btn';
                        createBtn.style.cssText = `
                display: block !important;
                padding: 15px !important;
                cursor: pointer !important;
                background: #667eea !important;
                color: white !important;
                font-size: 16px !important;
                font-weight: bold !important;
                border-bottom: 1px solid #e0e0e0 !important;
                text-align: right !important;
            `;
                        createBtn.textContent = '➕ إنشاء صنف جديد: ' + searchTerm;
                        createBtn.onclick = () => this.createNewItem(searchTerm);
                        createBtn.onmouseenter = function() {
                            this.style.background = '#5568d3 !important';
                        };
                        createBtn.onmouseleave = function() {
                            this.style.background = '#667eea !important';
                        };

                        dropdown.appendChild(createBtn);
                    }
                } else {
                    this.searchResults.forEach((item, index) => {
                        const resultDiv = document.createElement('div');
                        resultDiv.className = 'search-result-item';
                        resultDiv.style.cssText = `
                display: flex !important;
                flex-direction: column !important;
                padding: 10px 14px !important;
                cursor: pointer !important;
                background: white !important;
                border-bottom: 1px solid #e0e0e0 !important;
                transition: all 0.2s ease !important;
            `;

                        // اسم الصنف (العنوان الرئيسي)
                        const nameDiv = document.createElement('div');
                        nameDiv.style.cssText = `
                color: #1a1a1a !important;
                font-size: 14px !important;
                font-weight: 600 !important;
                line-height: 1.3 !important;
                margin-bottom: 5px !important;
            `;
                        nameDiv.textContent = item.name || 'بدون اسم';

                        // الكود والسعر (معلومات ثانوية مظللة)
                        const detailsDiv = document.createElement('div');
                        detailsDiv.style.cssText = `
                display: flex !important;
                gap: 8px !important;
                align-items: center !important;
            `;

                        const codeSpan = document.createElement('span');
                        codeSpan.style.cssText = `
                background: #f0f0f0 !important;
                color: #555 !important;
                padding: 2px 8px !important;
                border-radius: 3px !important;
                font-size: 11px !important;
                font-weight: 500 !important;
            `;
                        codeSpan.textContent = 'كود: ' + (item.code || '-');

                        const priceSpan = document.createElement('span');
                        priceSpan.style.cssText = `
                background: #e8f5e9 !important;
                color: #2e7d32 !important;
                padding: 2px 8px !important;
                border-radius: 3px !important;
                font-size: 11px !important;
                font-weight: 600 !important;
            `;
                        priceSpan.textContent = (parseFloat(item.price) || 0).toFixed(2) + ' ج.م';

                        detailsDiv.appendChild(codeSpan);
                        detailsDiv.appendChild(priceSpan);

                        resultDiv.appendChild(nameDiv);
                        resultDiv.appendChild(detailsDiv);

                        // ✅ تظليل العنصر المختار بالأسهم
                        if (index === this.selectedIndex) {
                            resultDiv.style.setProperty('background',
                                'linear-gradient(135deg, #667eea 0%, #764ba2 100%)', 'important');
                            resultDiv.style.setProperty('border-left', '4px solid #4051d4', 'important');
                            resultDiv.style.setProperty('box-shadow', '0 3px 10px rgba(102, 126, 234, 0.3)',
                                'important');
                            resultDiv.style.setProperty('transform', 'translateX(2px)', 'important');

                            // تغيير ألوان النصوص للأبيض
                            nameDiv.style.setProperty('color', 'white', 'important');
                            codeSpan.style.setProperty('background', 'rgba(255, 255, 255, 0.25)', 'important');
                            codeSpan.style.setProperty('color', 'white', 'important');
                            priceSpan.style.setProperty('background', 'rgba(255, 255, 255, 0.25)', 'important');
                            priceSpan.style.setProperty('color', 'white', 'important');
                        }

                        // Hover effects
                        resultDiv.onmouseenter = function() {
                            if (index !== InvoiceApp.selectedIndex) {
                                this.style.setProperty('background', '#f8f9fa', 'important');
                                this.style.setProperty('border-left', '3px solid #dee2e6', 'important');
                            }
                        };
                        resultDiv.onmouseleave = function() {
                            if (index !== InvoiceApp.selectedIndex) {
                                this.style.setProperty('background', 'white', 'important');
                                this.style.setProperty('border-left', 'none', 'important');
                            }
                        };

                        resultDiv.onclick = () => this.addItem(item);

                        dropdown.appendChild(resultDiv);
                    });
                }
            },

            // Show/hide search results
            showSearchResults() {
                const dropdown = document.getElementById('search-results-dropdown');
                const searchInput = document.getElementById('search-input');

                if (!dropdown) {
                    console.error('❌ Dropdown element not found!');
                    return;
                }

                if (!searchInput) {
                    console.error('❌ Search input not found!');
                    return;
                }

                // Calculate position relative to search input
                const rect = searchInput.getBoundingClientRect();
                const viewportWidth = window.innerWidth;
                const viewportHeight = window.innerHeight;

                // Calculate dropdown width (don't exceed viewport)
                const maxWidth = Math.min(550, viewportWidth - 40); // Max 800px or viewport - 40px margin
                const dropdownWidth = Math.min(rect.width * 2, maxWidth);

                // Calculate left position (ensure it stays in viewport)
                let leftPosition = rect.left;
                if (leftPosition + dropdownWidth > viewportWidth) {
                    // If dropdown exceeds viewport, align to right edge
                    leftPosition = viewportWidth - dropdownWidth - 20; // 20px margin from edge
                }

                // Calculate top position (below search input)
                let topPosition = rect.bottom + 2;

                // If dropdown would go below viewport, show it above input instead
                const estimatedHeight = 300; // Estimated dropdown height
                if (topPosition + estimatedHeight > viewportHeight) {
                    topPosition = rect.top - estimatedHeight - 2;
                }

                // Set ALL required styles with corrected positioning
                dropdown.style.position = 'fixed';
                dropdown.style.top = topPosition + 'px';
                dropdown.style.left = leftPosition + 'px';
                dropdown.style.width = dropdownWidth + 'px';
                dropdown.style.maxWidth = maxWidth + 'px';
                dropdown.style.maxHeight = '400px';
                dropdown.style.overflowY = 'auto';
                dropdown.style.background = 'white';
                dropdown.style.border = '2px solid #667eea'; // Make it more visible
                dropdown.style.borderRadius = '8px';
                dropdown.style.boxShadow = '0 8px 24px rgba(0, 0, 0, 0.3)'; // Stronger shadow
                dropdown.style.zIndex = '999999';
                dropdown.style.display = 'block';
                dropdown.style.visibility = 'visible';
                dropdown.style.opacity = '1';

                dropdown.classList.remove('hidden');

            },

            hideSearchResults() {
                const dropdown = document.getElementById('search-results-dropdown');
                if (dropdown) {
                    dropdown.classList.add('hidden');
                    dropdown.style.display = 'none';
                }
            },

            // Add item to invoice
            // Add item to invoice
            addItem(item) {
                // Ensure we have required fields
                if (!item.id || !item.name) {
                    console.error('❌ Invalid item data:', item);
                    this.updateStatus('خطأ: بيانات الصنف غير صحيحة', 'danger');
                    return;
                }

                // ✅ Check for duplicate items based on settings
                const isSales = [10, 12, 14, 16, 22, 26].includes(this.type);
                const isPurchases = [11, 13, 15, 17, 24, 25].includes(this.type);

                const preventDuplicate = (isSales && this.settings.prevent_duplicate_items_in_sales) ||
                    (isPurchases && this.settings.prevent_duplicate_items_in_purchases);

                if (preventDuplicate) {
                    const existingIndex = this.invoiceItems.findIndex(i => i.item_id === item.id);
                    if (existingIndex !== -1) {
                        // Show options: Merge quantities or Cancel
                        Swal.fire({
                            icon: 'question',
                            title: 'الصنف موجود بالفعل',
                            html: `
                                <p>الصنف <strong>${item.name}</strong> موجود بالفعل في الفاتورة.</p>
                                <p>الكمية الحالية: <strong>${this.invoiceItems[existingIndex].quantity}</strong></p>
                                <p>هل تريد دمج الكميات؟</p>
                            `,
                            showCancelButton: true,
                            confirmButtonText: 'دمج الكميات',
                            cancelButtonText: 'إلغاء',
                            confirmButtonColor: '#28a745',
                            cancelButtonColor: '#6c757d'
                        }).then((result) => {
                            if (result.isConfirmed) {
                                // Merge quantities
                                this.invoiceItems[existingIndex].quantity += 1;
                                this.calculateItemTotal(existingIndex);
                                this.renderItems();
                                this.updateStatus('✓ تم دمج الكميات', 'success');

                                // Focus on the existing item
                                setTimeout(() => {
                                    this.showItemDetails(existingIndex);
                                }, 100);
                            }
                        });
                        return;
                    }
                }

                // Get default unit
                const defaultUnitId = item.default_unit_id || item.unit_id || (item.units && item.units.length > 0 ?
                    item.units[0].id : 1);

                // ✅ For purchase invoices, use last_purchase_price if available
                const isPurchaseInvoice = [11, 13, 15, 17, 24, 25].includes(this.type);
                let itemPrice = parseFloat(item.price) || 0;

                if (isPurchaseInvoice && item.last_purchase_price) {
                    itemPrice = parseFloat(item.last_purchase_price) || 0;
                }

                // ✅ DON'T convert price - user enters in selected currency
                // Conversion happens when saving to database

                const newItem = {
                    id: Date.now(),
                    item_id: item.id,
                    name: item.name,
                    code: item.code || '',
                    barcode: item.barcode || '',
                    unit_id: defaultUnitId,
                    quantity: 1,
                    price: itemPrice,
                    item_price: itemPrice,
                    discount: 0,
                    discount_percentage: 0,
                    discount_value: 0,
                    additional: 0, // ✅ Add additional field
                    sub_value: itemPrice,
                    batch_number: '',
                    expiry_date: null,
                    length: 0, // ✅ Add length field
                    width: 0, // ✅ Add width field
                    height: 0, // ✅ Add height field
                    density: 0, // ✅ Add density field
                    notes: '', // ✅ Add notes field
                    units: item.units || []
                };

                this.invoiceItems.push(newItem);
                this.renderItems();
                this.calculateTotals();

                // Clear search first
                const searchInput = document.getElementById('search-input');
                if (searchInput) searchInput.value = '';
                this.hideSearchResults();

                this.updateStatus('✓ تم إضافة الصنف', 'success');

                // ✅ Focus على أول حقل editable - بعد الـ rendering مباشرة
                const itemIndex = this.invoiceItems.length - 1;

                // Use multiple requestAnimationFrame to ensure DOM is fully rendered
                requestAnimationFrame(() => {
                    requestAnimationFrame(() => {
                        const editableColumns = this.getEditableColumns();

                        if (editableColumns.length > 0) {
                            const firstEditableColumn = editableColumns[0];
                            this.focusField(firstEditableColumn, itemIndex);
                        }
                        // Show item details AFTER focus attempt
                        this.showItemDetails(itemIndex);
                    });
                });
            },

            // Create new item
            createNewItem(name) {
                if (!name || name.trim().length === 0) return;

                this.updateStatus('جاري إنشاء الصنف...', 'primary');

                fetch('/api/items/quick-create', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                            'X-Requested-With': 'XMLHttpRequest'
                        },
                        body: JSON.stringify({
                            name: name.trim(),
                            code: 'AUTO',
                            price: 0,
                            unit_id: 1
                        })
                    })
                    .then(response => {
                        if (!response.ok) {
                            throw new Error(`HTTP error! status: ${response.status}`);
                        }
                        return response.json();
                    })
                    .then(data => {
                        if (data.item) {
                            this.allItems.push(data.item);
                            this.updateStatus('✓ تم إنشاء الصنف بنجاح', 'success');

                            // Hide search results
                            this.hideSearchResults();

                            // Add item to invoice
                            this.addItem(data.item);
                        } else {
                            console.error('❌ No item in response:', data);
                            this.updateStatus('خطأ: لم يتم إرجاع بيانات الصنف', 'danger');
                            this.hideSearchResults();
                        }
                    })
                    .catch(error => {
                        console.error('❌ Error creating item:', error);
                        this.updateStatus('خطأ في إنشاء الصنف: ' + error.message, 'danger');
                        this.hideSearchResults();
                    });
            },

            // Render items table
            renderItems() {
                const tbody = document.getElementById('invoice-items-tbody');
                if (!tbody) return;

                // Find the search row
                const searchRow = tbody.querySelector('.search-row');

                if (this.invoiceItems.length === 0) {
                    // Remove all rows except search row
                    const rows = tbody.querySelectorAll('tr:not(.search-row)');
                    rows.forEach(row => row.remove());
                    return;
                }

                // Generate items HTML
                const itemsHTML = this.invoiceItems.map((item, index) => this.renderItemRow(item, index)).join('');

                // Insert items BEFORE search row
                if (searchRow) {
                    // Remove old item rows (keep search row)
                    const rows = tbody.querySelectorAll('tr:not(.search-row)');
                    rows.forEach(row => row.remove());

                    // Insert new items before search row
                    searchRow.insertAdjacentHTML('beforebegin', itemsHTML);
                } else {
                    // Fallback: just set innerHTML
                    tbody.innerHTML = itemsHTML;
                }

                // Attach event listeners to inputs
                this.attachItemEventListeners();
            },

            // Render single item row
            renderItemRow(item, index) {
                let html =
                    `<tr data-index="${index}" onclick="InvoiceApp.showItemDetails(${index})" style="cursor: pointer;" class="invoice-item-row">`;

                // Render columns based on visible columns
                this.visibleColumns.forEach(col => {
                    html += this.renderColumn(col, item, index);
                });

                // Action column
                html += `
                    <td class="action-cell" style="width: 80px; min-width: 80px;" onclick="event.stopPropagation();">
                        <button type="button" class="btn btn-link text-danger p-0" onclick="InvoiceApp.removeItem(${index})">
                            <i class="fas fa-trash-alt"></i>
                        </button>
                    </td>
                </tr>`;

                return html;
            },

            // Render single column
            renderColumn(columnName, item, index) {
                // Determine column width from template or defaults
                let colWidth = '';
                if (this.columnWidths && this.columnWidths[columnName]) {
                    const width = Math.max(5, parseInt(this.columnWidths[columnName]) || 0);
                    colWidth = `width: ${width}px; min-width: ${width}px;`;
                }

                switch (columnName) {
                    case 'item_name':
                        return `
                            <td style="${colWidth || 'width: 18%;'}">
                                <div class="static-text" style="font-weight: 900; font-size: 1.2rem; color: #000;">
                                    ${item.item_name || item.name || ''}
                                </div>
                            </td>`;

                    case 'code':
                        return `
                            <td style="${colWidth || 'width: 10%;'}">
                                <div class="static-text">${item.code || ''}</div>
                            </td>`;

                    case 'unit':
                        const itemUnits = item.units || item.available_units || [];
                        return `
                            <td style="${colWidth || 'width: 10%;'}" onclick="event.stopPropagation();">
                                <select id="unit-${index}" class="form-control" data-index="${index}" data-field="unit">
                                    ${itemUnits.map(unit => `
                                            <option value="${unit.id}" data-u-val="${unit.u_val || unit.pivot?.u_val || 1}" ${unit.id == item.unit_id ? 'selected' : ''}>
                                                ${unit.name} [${unit.u_val || unit.pivot?.u_val || 1}]
                                            </option>
                                        `).join('')}
                                </select>
                            </td>`;

                    case 'quantity':
                        return `
                            <td style="${colWidth || 'width: 10%;'}" onclick="event.stopPropagation();">
                                <input type="number" id="quantity-${index}" class="form-control text-center"
                                       value="${item.quantity}" step="0.001" min="0"
                                       data-index="${index}" data-field="quantity">
                            </td>`;

                    case 'batch_number':
                        return `
                            <td style="${colWidth || 'width: 12%;'}" onclick="event.stopPropagation();">
                                <input type="text" id="batch-${index}" class="form-control text-center"
                                       value="${item.batch_number || ''}"
                                       data-index="${index}" data-field="batch">
                            </td>`;

                    case 'expiry_date':
                        return `
                            <td style="${colWidth || 'width: 12%;'}" onclick="event.stopPropagation();">
                                <input type="date" id="expiry-${index}" class="form-control text-center"
                                       value="${item.expiry_date || ''}"
                                       data-index="${index}" data-field="expiry">
                            </td>`;

                    case 'price':
                        const canEditPrice = this.settings.permissions.allow_price_change;
                        return `
                            <td style="${colWidth || 'width: 15%;'}" onclick="event.stopPropagation();">
                                <input type="number" id="price-${index}" class="form-control text-center"
                                       value="${item.price}" step="0.01"
                                       ${!canEditPrice ? 'readonly tabindex="-1" style="background-color: #f8f9fa;"' : ''}
                                       data-index="${index}" data-field="price">
                            </td>`;

                    case 'discount_percentage':
                        const canEditDiscount = this.settings.permissions.allow_discount_change;
                        return `
                            <td style="${colWidth || 'width: 10%;'}" onclick="event.stopPropagation();">
                                <input type="number" id="discount-percentage-${index}" class="form-control text-center"
                                       value="${item.discount_percentage || 0}" step="0.01" min="0" max="100"
                                       ${!canEditDiscount ? 'readonly tabindex="-1" style="background-color: #f8f9fa;"' : ''}
                                       data-index="${index}" data-field="discount_percentage">
                            </td>`;

                    case 'discount_value':
                        const canEditDiscountValue = this.settings.permissions.allow_discount_change;
                        return `
                            <td style="${colWidth || 'width: 10%;'}" onclick="event.stopPropagation();">
                                <input type="number" id="discount-value-${index}" class="form-control text-center"
                                       value="${item.discount_value || 0}" step="0.01" min="0"
                                       ${!canEditDiscountValue ? 'readonly tabindex="-1" style="background-color: #f8f9fa;"' : ''}
                                       data-index="${index}" data-field="discount_value">
                            </td>`;

                    case 'discount':
                        // Legacy support - if old templates still use 'discount'
                        const canEditDiscountLegacy = this.settings.permissions.allow_discount_change;
                        return `
                            <td style="${colWidth || 'width: 15%;'}" onclick="event.stopPropagation();">
                                <input type="number" id="discount-${index}" class="form-control text-center"
                                       value="${item.discount || 0}" step="0.01"
                                       ${!canEditDiscountLegacy ? 'readonly tabindex="-1" style="background-color: #f8f9fa;"' : ''}
                                       data-index="${index}" data-field="discount">
                            </td>`;

                    case 'sub_value':
                        return `
                            <td style="${colWidth || 'width: 15%;'}" onclick="event.stopPropagation();">
                                <input type="number" id="sub-value-${index}" class="form-control text-center"
                                    value="${item.sub_value}" readonly tabindex="-1">
                            </td>`;

                    case 'length':
                    case 'width':
                    case 'height':
                    case 'density':
                        return `
                            <td style="${colWidth || 'width: 10%;'}" onclick="event.stopPropagation();">
                                <input type="number" id="${columnName}-${index}" class="form-control text-center"
                                       value="${item[columnName] || 0}" step="0.01"
                                       data-index="${index}" data-field="${columnName}">
                            </td>`;

                    default:
                        return `<td style="${colWidth}"></td>`;
                }
            },

            // Attach event listeners to item inputs
            attachItemEventListeners() {
                // ✅ Add focus event to all item inputs to show item details
                document.querySelectorAll('[data-index]').forEach(element => {
                    if (element.dataset.index !== undefined) {
                        element.addEventListener('focus', (e) => {
                            const index = parseInt(e.target.dataset.index);
                            if (!isNaN(index)) {
                                this.showItemDetails(index);
                            }
                        });
                    }
                });

                document.querySelectorAll('[id^="sub-value-"]').forEach(input => {
                    input.addEventListener('focus', (e) => {
                        e.preventDefault();
                        e.target.blur(); // ✅ ارجع الـ focus فوراً

                        // ✅ روح على البحث بدلاً منه
                        const searchInput = document.getElementById('search-input');
                        if (searchInput) {
                            searchInput.focus();
                            searchInput.select();
                        }
                    });

                    // ✅ امنع keyboard navigation على sub_value
                    input.addEventListener('keydown', (e) => {
                        e.preventDefault();
                        if (e.key === 'Enter' || e.key === 'Tab') {
                            const searchInput = document.getElementById('search-input');
                            if (searchInput) {
                                searchInput.focus();
                                searchInput.select();
                            }
                        }
                    });
                });

                // Quantity, price, discount inputs
                document.querySelectorAll(
                        '[data-field="quantity"], [data-field="price"], [data-field="discount"], [data-field="discount_percentage"], [data-field="discount_value"]'
                    )
                    .forEach(input => {
                        input.addEventListener('input', (e) => {
                            const index = parseInt(e.target.dataset.index);
                            const field = e.target.dataset.field;
                            const value = parseFloat(e.target.value) || 0;

                            // ✅ Real-time Validation
                            let isValid = true;
                            let errorMessage = '';

                            // Validate Quantity
                            if (field === 'quantity') {
                                const item = this.invoiceItems[index];

                                // Check if quantity is negative
                                if (value < 0) {
                                    isValid = false;
                                    errorMessage = 'الكمية لا يمكن أن تكون سالبة';
                                }

                                // Check stock availability for sales invoices
                                if (isValid && [10, 12, 14, 16, 22, 26].includes(this.type)) {
                                    if (this.settings.permissions.prevent_transactions_without_stock) {
                                        // Fetch stock from API
                                        const warehouseId = $('#acc2-id').val();
                                        if (warehouseId && item.item_id) {
                                            fetch(
                                                    `/api/invoices/items/${item.item_id}/stock?warehouse_id=${warehouseId}&branch_id=${this.branchId}`)
                                                .then(res => res.json())
                                                .then(data => {
                                                    if (data.success && data.stock < value) {
                                                        e.target.classList.add('is-invalid');
                                                        this.showValidationError(e.target,
                                                            `المخزون المتاح: ${data.stock}`);
                                                    } else {
                                                        e.target.classList.remove('is-invalid');
                                                        this.hideValidationError(e.target);
                                                    }
                                                });
                                        }
                                    }
                                }
                            }

                            // Validate Price
                            if (field === 'price') {
                                const item = this.invoiceItems[index];

                                // Check if price is negative
                                if (value < 0) {
                                    isValid = false;
                                    errorMessage = 'السعر لا يمكن أن يكون سالباً';
                                }

                                // Check if price is zero (based on settings)
                                if (isValid && value === 0 && !this.settings.allow_zero_price_in_invoice) {
                                    isValid = false;
                                    errorMessage = 'السعر لا يمكن أن يكون صفر';
                                }

                                // Check if price is less than cost (for sales)
                                if (isValid && [10, 12, 14, 16, 22, 26].includes(this.type)) {
                                    if (item.cost_price && value < item.cost_price) {
                                        e.target.classList.add('is-warning');
                                        this.showValidationWarning(e.target,
                                            `تحذير: السعر أقل من التكلفة (${item.cost_price})`);
                                    } else {
                                        e.target.classList.remove('is-warning');
                                        this.hideValidationError(e.target);
                                    }
                                }
                            }

                            // Validate Discount
                            if (field === 'discount_percentage') {
                                if (value < 0) {
                                    isValid = false;
                                    errorMessage = 'الخصم لا يمكن أن يكون سالباً';
                                } else if (value > 100) {
                                    isValid = false;
                                    errorMessage = 'الخصم لا يمكن أن يتجاوز 100%';
                                }
                            }

                            // Show/Hide validation error
                            if (!isValid) {
                                e.target.classList.add('is-invalid');
                                this.showValidationError(e.target, errorMessage);
                                return; // Don't update value if invalid
                            } else {
                                e.target.classList.remove('is-invalid');
                                this.hideValidationError(e.target);
                            }

                            this.invoiceItems[index][field] = value;

                            // ✅ Sync discount_percentage and discount_value
                            if (field === 'discount_percentage') {
                                // Calculate discount_value from percentage
                                const subtotal = this.invoiceItems[index].quantity * this.invoiceItems[
                                    index].price;
                                this.invoiceItems[index].discount_value = (subtotal * value) / 100;

                                // Update the discount_value input
                                const discountValueInput = document.getElementById(
                                    `discount-value-${index}`);
                                if (discountValueInput) {
                                    discountValueInput.value = this.invoiceItems[index].discount_value
                                        .toFixed(2);
                                }
                            } else if (field === 'discount_value') {
                                // Calculate discount_percentage from value
                                const subtotal = this.invoiceItems[index].quantity * this.invoiceItems[
                                    index].price;
                                if (subtotal > 0) {
                                    this.invoiceItems[index].discount_percentage = (value / subtotal) * 100;

                                    // Update the discount_percentage input
                                    const discountPercentageInput = document.getElementById(
                                        `discount-percentage-${index}`);
                                    if (discountPercentageInput) {
                                        discountPercentageInput.value = this.invoiceItems[index]
                                            .discount_percentage.toFixed(2);
                                    }
                                }
                            }

                            this.calculateItemTotal(index);
                        });

                        input.addEventListener('focus', (e) => e.target.select());

                        // ✅ Add keyboard navigation - Enter/Tab to next field
                        input.addEventListener('keydown', (e) => {
                            if (e.key === 'Enter') {
                                e.preventDefault();
                                const index = parseInt(e.target.dataset.index);
                                const field = e.target.dataset.field;
                                this.moveToNextField(index, field, false);
                            } else if (e.key === 'Tab' && !e.shiftKey) {
                                e.preventDefault();
                                const index = parseInt(e.target.dataset.index);
                                const field = e.target.dataset.field;
                                this.moveToNextField(index, field, false);
                            } else if (e.key === 'Tab' && e.shiftKey) {
                                e.preventDefault();
                                const index = parseInt(e.target.dataset.index);
                                const field = e.target.dataset.field;
                                this.moveToNextField(index, field, true);
                            }
                        });
                    });

                // Unit select
                document.querySelectorAll('[data-field="unit"]').forEach(select => {
                    select.addEventListener('change', (e) => {
                        const index = parseInt(e.target.dataset.index);
                        const selectedOption = e.target.options[e.target.selectedIndex];
                        const uVal = parseFloat(selectedOption.dataset.uVal) || 1;

                        this.invoiceItems[index].unit_id = parseInt(e.target.value);
                        this.invoiceItems[index].price = (this.invoiceItems[index]
                                .item_price || 0) *
                            uVal;

                        this.calculateItemTotal(index);
                        this.renderItems();
                    });

                    // ✅ Add keyboard navigation for select
                    select.addEventListener('keydown', (e) => {
                        if (e.key === 'Enter') {
                            e.preventDefault();
                            const index = parseInt(e.target.dataset.index);
                            this.moveToNextField(index, 'unit', false);
                        }
                    });
                });

                // Batch and expiry
                document.querySelectorAll('[data-field="batch"], [data-field="expiry"]').forEach(input => {
                    input.addEventListener('input', (e) => {
                        const index = parseInt(e.target.dataset.index);
                        const field = e.target.dataset.field;

                        if (field === 'batch') {
                            this.invoiceItems[index].batch_number = e.target.value;
                        } else if (field === 'expiry') {
                            this.invoiceItems[index].expiry_date = e.target.value;
                        }
                    });

                    // ✅ Add keyboard navigation
                    input.addEventListener('keydown', (e) => {
                        if (e.key === 'Enter') {
                            e.preventDefault();
                            const index = parseInt(e.target.dataset.index);
                            const field = e.target.dataset.field;
                            this.moveToNextField(index, field, false);
                        }
                    });
                });

                // ✅ Length, Width, Height, Density fields
                document.querySelectorAll(
                        '[data-field="length"], [data-field="width"], [data-field="height"], [data-field="density"]')
                    .forEach(input => {
                        input.addEventListener('input', (e) => {
                            const index = parseInt(e.target.dataset.index);
                            const field = e.target.dataset.field;
                            const value = parseFloat(e.target.value) || 0;

                            this.invoiceItems[index][field] = value;
                            // You might want to recalculate totals here if needed
                        });

                        input.addEventListener('focus', (e) => e.target.select());

                        // ✅ Add keyboard navigation
                        input.addEventListener('keydown', (e) => {
                            if (e.key === 'Enter') {
                                e.preventDefault();
                                const index = parseInt(e.target.dataset.index);
                                const field = e.target.dataset.field;
                                this.moveToNextField(index, field, false);
                            } else if (e.key === 'Tab' && !e.shiftKey) {
                                e.preventDefault();
                                const index = parseInt(e.target.dataset.index);
                                const field = e.target.dataset.field;
                                this.moveToNextField(index, field, false);
                            } else if (e.key === 'Tab' && e.shiftKey) {
                                e.preventDefault();
                                const index = parseInt(e.target.dataset.index);
                                const field = e.target.dataset.field;
                                this.moveToNextField(index, field, true);
                            }
                        });
                    });
            },

            // Calculate item total
            calculateItemTotal(index) {
                const item = this.invoiceItems[index];
                const quantity = parseFloat(item.quantity) || 0;
                const price = parseFloat(item.price) || 0;

                // Calculate sub_value using discount_value (always use value, not percentage)
                const subtotal = quantity * price;
                const discountAmount = parseFloat(item.discount_value) || 0;

                // ✅ Use Math.round to avoid floating point errors
                item.sub_value = Math.round((subtotal - discountAmount) * 100) / 100;

                // Update display
                const subValueInput = document.getElementById('sub-value-' + index);
                if (subValueInput) {
                    subValueInput.value = item.sub_value.toFixed(2);
                }

                this.calculateTotals();
            },

            // Calculate totals
            calculateTotals() {
                // Subtotal - ✅ Use Math.round to avoid floating point errors
                this.subtotal = this.invoiceItems.reduce((sum, item) => sum + (parseFloat(item.sub_value) || 0), 0);
                this.subtotal = Math.round(this.subtotal * 100) / 100;

                // Discount
                if (this.discountPercentage > 0) {
                    this.discountValue = Math.round((this.subtotal * this.discountPercentage) / 100 * 100) / 100;
                    // ✅ Update discount value input field
                    const discountValueInput = document.getElementById('discount-value');
                    if (discountValueInput) {
                        discountValueInput.value = this.discountValue.toFixed(2);
                    }
                } else if (this.subtotal > 0 && this.discountValue > 0) {
                    this.discountPercentage = Math.round((this.discountValue / this.subtotal) * 100 * 100) / 100;
                    // ✅ Update discount percentage input field
                    const discountPercentageInput = document.getElementById('discount-percentage');
                    if (discountPercentageInput) {
                        discountPercentageInput.value = this.discountPercentage.toFixed(2);
                    }
                }

                const afterDiscount = Math.round((this.subtotal - this.discountValue) * 100) / 100;

                // Additional
                if (this.additionalPercentage > 0) {
                    this.additionalValue = Math.round((afterDiscount * this.additionalPercentage) / 100 * 100) / 100;
                    // ✅ Update additional value input field
                    const additionalValueInput = document.getElementById('additional-value');
                    if (additionalValueInput) {
                        additionalValueInput.value = this.additionalValue.toFixed(2);
                    }
                } else if (afterDiscount > 0 && this.additionalValue > 0) {
                    this.additionalPercentage = Math.round((this.additionalValue / afterDiscount) * 100 * 100) / 100;
                    // ✅ Update additional percentage input field
                    const additionalPercentageInput = document.getElementById('additional-percentage');
                    if (additionalPercentageInput) {
                        additionalPercentageInput.value = this.additionalPercentage.toFixed(2);
                    }
                }

                const afterAdditional = Math.round((afterDiscount + this.additionalValue) * 100) / 100;

                // VAT
                this.vatValue = Math.round((afterAdditional * this.vatPercentage) / 100 * 100) / 100;

                // Withholding Tax
                this.withholdingTaxValue = Math.round((afterAdditional * this.withholdingTaxPercentage) / 100 * 100) / 100;

                // Total
                this.totalAfterAdditional = Math.round((afterAdditional + this.vatValue - this.withholdingTaxValue) * 100) / 100;

                // Remaining
                this.remaining = Math.round((this.totalAfterAdditional - this.receivedFromClient) * 100) / 100;

                this.updateTotalsDisplay();

                // Update balance after invoice
                this.calculateBalance();

                // Update installment modal data if client is selected
                this.updateInstallmentModalData();

                // ✅ Handle cash account auto-fill when total changes
                this.handleCashAccountReceivedAmount();
            },

            // Update totals display
            updateTotalsDisplay() {
                // Update all display fields
                const displayUpdates = {
                    'display-subtotal': this.subtotal.toFixed(2),
                    'display-total': this.totalAfterAdditional.toFixed(2),
                    'display-received': this.receivedFromClient.toFixed(2),
                    'display-remaining': this.remaining.toFixed(2)
                };

                Object.entries(displayUpdates).forEach(([id, value]) => {
                    const el = document.getElementById(id);
                    if (el) el.textContent = value;
                });

                // ✅ Update currency display in footer
                const currencyDisplayRow = document.getElementById('currency-display-row');
                const footerExchangeRate = document.getElementById('footer-exchange-rate');

                if (this.exchangeRate !== 1 && currencyDisplayRow && footerExchangeRate) {
                    currencyDisplayRow.style.display = 'flex';
                    footerExchangeRate.textContent = this.exchangeRate.toFixed(4);
                } else if (currencyDisplayRow) {
                    currencyDisplayRow.style.display = 'none';
                }

                // Update readonly input fields
                const inputUpdates = {
                    'vat-value-display': this.vatValue.toFixed(2),
                    'withholding-tax-value-display': this.withholdingTaxValue.toFixed(2)
                };

                Object.entries(inputUpdates).forEach(([id, value]) => {
                    const el = document.getElementById(id);
                    if (el) el.value = value;
                });

                // Update remaining color
                const remainingEl = document.getElementById('display-remaining');
                if (remainingEl) {
                    remainingEl.classList.remove('text-danger', 'text-success');
                    if (this.remaining > 0.01) {
                        remainingEl.classList.add('text-danger');
                    } else if (this.remaining < -0.01) {
                        remainingEl.classList.add('text-success');
                    }
                }
            },

            // Remove item
            removeItem(index) {
                if (confirm('هل تريد حذف هذا الصنف؟')) {
                    this.invoiceItems.splice(index, 1);
                    this.renderItems();
                    this.calculateTotals();
                }
            },

            // Show item details in footer (Fetch from API)
            showItemDetails(index) {
                const item = this.invoiceItems[index];
                if (!item) {
                    console.error('❌ الصنف غير موجود');
                    return;
                }

                // Track last selected index for refreshing when warehouse/customer changes
                this.lastSelectedIndex = index;

                // Check if item details card exists (might be hidden by settings)
                const itemDetailsCard = document.getElementById('item-details-card');
                if (!itemDetailsCard) {
                    return;
                }

                // Helper function to safely update element
                const safeUpdate = (id, value) => {
                    const element = document.getElementById(id);
                    if (element) {
                        element.textContent = value;
                    }
                };

                // Set basic details first (fast)
                safeUpdate('selected-item-name', item.item_name || '-');

                // Find unit name
                const unitSelect = document.getElementById(`unit-${index}`);
                const unitName = unitSelect ? unitSelect.options[unitSelect.selectedIndex].text : (item.unit_name ||
                    '-');
                safeUpdate('selected-item-unit', unitName);
                safeUpdate('selected-item-price', (item.price || 0).toFixed(2));

                // Show loading state for API data
                safeUpdate('selected-item-store', '...');
                safeUpdate('selected-item-available', '...');
                safeUpdate('selected-item-total', '...');
                safeUpdate('selected-item-last-price', '...');
                safeUpdate('selected-item-avg-cost', '...');

                // Fetch real-time details from API
                const customerId = $('#acc1-id').val();
                const warehouseId = $('#acc2-id').val();
                const branchId = this.branchId;

                let url = `/api/invoices/items/${item.item_id}/details?branch_id=${branchId}`;
                if (customerId) url += `&customer_id=${customerId}`;
                if (warehouseId) url += `&warehouse_id=${warehouseId}`;

                fetch(url)
                    .then(response => response.json())
                    .then(res => {
                        if (res.success && res.data) {
                            const data = res.data;

                            // Get store name if available
                            const storeSelect = document.getElementById('acc2-id');
                            const storeName = storeSelect && storeSelect.selectedIndex >= 0 ?
                                storeSelect.options[storeSelect.selectedIndex].text : '-';

                            safeUpdate('selected-item-store', storeName);
                            safeUpdate('selected-item-available', (data.warehouse_stock || 0).toLocaleString());
                            safeUpdate('selected-item-total', (data.stock_quantity || 0).toLocaleString());
                            safeUpdate('selected-item-last-price', (data.last_purchase_price || 0).toFixed(2));
                            safeUpdate('selected-item-avg-cost', (data.item?.average_cost || 0).toFixed(2));
                            safeUpdate('selected-item-price', (data.sale_price || 0).toFixed(2));
                        }
                    })
                    .catch(error => {
                        console.error('❌ خطأ في تحميل بيانات الصنف:', error);
                        // Reset on error
                        safeUpdate('selected-item-available', '-');
                        safeUpdate('selected-item-total', '-');
                        safeUpdate('selected-item-store', '-');
                        safeUpdate('selected-item-last-price', '-');
                        safeUpdate('selected-item-avg-cost', '-');
                    });
            },

            /**
             * Update account balance when account changes
             */
            updateAccountBalance(accountId) {
                if (!accountId) {
                    this.currentBalance = 0;
                    this.calculateBalance();
                    this.clearRecommendedItems();
                    // Clear cash account auto-fill
                    this.handleCashAccountReceivedAmount();
                    return;
                }

                const url = `/api/accounts/${accountId}/balance`;

                // Fetch account balance from API
                fetch(url)
                    .then(response => {
                        return response.json();
                    })
                    .then(data => {
                        this.currentBalance = parseFloat(data.balance) || 0;

                        this.calculateBalance();

                        // Update display
                        const balanceDisplay = document.getElementById('current-balance-header');
                        if (balanceDisplay) {
                            balanceDisplay.textContent = this.currentBalance.toFixed(2);
                        } else {
                            console.error('❌ Element current-balance-header not found!');
                        }

                        // ✅ Handle cash account auto-fill AFTER balance is fetched
                        this.handleCashAccountReceivedAmount();
                    })
                    .catch(error => {
                        console.error('❌ Error fetching account balance:', error);
                    });

                // Fetch recommended items
                this.loadRecommendedItems(accountId);
            },

            /**
             * Load recommended items for account
             */
            loadRecommendedItems(accountId) {

                const url = `/api/invoices/customers/${accountId}/recommended-items?limit=5`;

                fetch(url)
                    .then(response => {
                        if (!response.ok) {
                            throw new Error(`HTTP error! status: ${response.status}`);
                        }
                        return response.json();
                    })
                    .then(data => {
                        if (data.success && data.items) {
                            this.displayRecommendedItems(data.items);
                        } else {
                            console.error('❌ No items in response or success=false');
                            this.clearRecommendedItems();
                        }
                    })
                    .catch(error => {
                        console.error('❌ Error fetching recommended items:', error);
                        this.clearRecommendedItems();
                    });
            },

            /**
             * Display recommended items in the footer
             */
            displayRecommendedItems(items) {

                const container = document.getElementById('recommended-items-list');

                if (!container) {
                    console.error('❌ Element recommended-items-list not found!');
                    return;
                }

                if (!items || items.length === 0) {
                    container.innerHTML = '<p class="text-muted text-center mb-0 small">لا توجد أصناف موصى بها</p>';
                    return;
                }

                let html = '<div class="list-group list-group-flush">';
                items.forEach((item, index) => {
                    html += `
                        <a href="#" class="list-group-item list-group-item-action p-1 small"
                           onclick="InvoiceApp.addItemById(${item.id}); return false;"
                           title="اضغط لإضافة الصنف">
                            <div class="d-flex justify-content-between align-items-center">
                                <div class="text-truncate" style="max-width: 150px;">
                                    <strong>${item.name}</strong>
                                    ${item.code ? `<small class="text-muted">(${item.code})</small>` : ''}
                                </div>
                                <div class="text-end">
                                    <small class="badge bg-primary">${item.transaction_count}×</small>
                                    <small class="text-muted">${parseFloat(item.avg_price).toFixed(2)}</small>
                                </div>
                            </div>
                        </a>
                    `;
                });
                html += '</div>';

                container.innerHTML = html;
            },

            /**
             * Clear recommended items
             */
            clearRecommendedItems() {
                const container = document.getElementById('recommended-items-list');
                if (container) {
                    container.innerHTML = '<p class="text-muted text-center mb-0 small">لا توجد أصناف موصى بها</p>';
                }
            },

            /**
             * Add item by ID (from recommended items)
             */
            addItemById(itemId) {
                const item = this.allItems.find(i => i.id === itemId);
                if (item) {
                    this.addItem(item, false);
                } else {
                    console.error('❌ Item not found:', itemId);
                    this.updateStatus('الصنف غير موجود', 'danger');
                }
            },

            /**
             * Calculate balance after invoice
             */
            calculateBalance() {
                const invoiceType = parseInt(this.type) || 10;
                // Make sure we have valid numbers
                const currentBal = parseFloat(this.currentBalance) || 0;
                const totalAfter = parseFloat(this.totalAfterAdditional) || 0;
                const received = parseFloat(this.receivedFromClient) || 0;

                // ✅ حساب الرصيد حسب نوع الفاتورة
                const isSales = [10, 12, 14, 16, 19, 22].includes(invoiceType);
                const isPurchase = [11, 13, 15, 17, 20, 23, 24, 25].includes(invoiceType);

                if (isSales) {
                    // المبيعات: العميل مدين - الرصيد يزيد بقيمة الفاتورة وينقص بالمدفوع
                    this.calculatedBalanceAfter = currentBal + totalAfter - received;
                } else if (isPurchase) {
                    // المشتريات: المورد دائن - الرصيد ينقص بقيمة الفاتورة ويزيد بالمدفوع
                    this.calculatedBalanceAfter = currentBal - totalAfter + received;
                } else {
                    // الحالات الأخرى
                    this.calculatedBalanceAfter = currentBal - totalAfter + received;
                }

                // Update display
                const balanceAfterDisplay = document.getElementById('balance-after-header');
                if (balanceAfterDisplay) {
                    balanceAfterDisplay.textContent = this.calculatedBalanceAfter.toFixed(2);

                    // Color coding based on invoice type and balance
                    if (this.calculatedBalanceAfter === 0) {
                        // Zero balance - neutral (gray)
                        balanceAfterDisplay.className = 'badge bg-secondary text-white';
                    } else if (isSales) {
                        // Sales: Positive = Customer owes us (green), Negative = We owe customer (red)
                        balanceAfterDisplay.className = this.calculatedBalanceAfter > 0 ?
                            'badge bg-success text-white' : 'badge bg-danger text-white';
                    } else if (isPurchase) {
                        // Purchase: Negative = We owe supplier (red is normal), Positive = Supplier owes us (green)
                        balanceAfterDisplay.className = this.calculatedBalanceAfter < 0 ?
                            'badge bg-warning text-dark' : 'badge bg-info text-white';
                    } else {
                        // Default
                        balanceAfterDisplay.className = 'badge bg-light text-dark';
                    }
                } else {
                    console.error('❌ Element balance-after-header not found!');
                }
            },

            /**
             * Handle cash account auto-fill for received amount
             * Cash Customer ID: 61 (العميل النقدي)
             * Cash Supplier ID: 64 (المورد النقدي)
             */
            handleCashAccountReceivedAmount() {
                const acc1Id = $('#acc1-id').val();
                const receivedInput = document.getElementById('received-from-client');

                if (!receivedInput) {
                    return;
                }

                // Check if account is cash account (61 or 64)
                const isCashAccount = (acc1Id === '61' || acc1Id === '64');

                if (isCashAccount) {
                    // Auto-fill with total and make readonly
                    receivedInput.value = this.totalAfterAdditional.toFixed(2);
                    this.receivedFromClient = this.totalAfterAdditional;
                    receivedInput.readOnly = true;
                    receivedInput.style.backgroundColor = '#e9ecef'; // Gray background
                    receivedInput.style.cursor = 'not-allowed';
                } else {
                    // Make editable for other accounts
                    receivedInput.readOnly = false;
                    receivedInput.style.backgroundColor = '';
                    receivedInput.style.cursor = '';
                }

                // Recalculate remaining - ✅ Use Math.round to avoid floating point errors
                this.remaining = Math.round((this.totalAfterAdditional - this.receivedFromClient) * 100) / 100;

                // Update display for received amount
                const receivedDisplay = document.getElementById('display-received');
                if (receivedDisplay) {
                    receivedDisplay.textContent = this.receivedFromClient.toFixed(2);
                }

                // Update display for remaining
                const remainingDisplay = document.getElementById('display-remaining');
                if (remainingDisplay) {
                    remainingDisplay.textContent = this.remaining.toFixed(2);
                    remainingDisplay.classList.remove('text-danger', 'text-success');
                    if (this.remaining > 0.01) {
                        remainingDisplay.classList.add('text-danger');
                    } else if (this.remaining < -0.01) {
                        remainingDisplay.classList.add('text-success');
                    }
                }
            },

            /**
             * Initialize price list selector (for sales invoices only)
             */
            initializePriceListSelector() {
                const priceListSelect = document.getElementById('price-list-id');
                if (!priceListSelect) {
                    return;
                }

                // Set default price list (first option)
                this.selectedPriceListId = priceListSelect.value || null;

                // Save reference to this
                const self = this;

                // Listen for price list changes
                priceListSelect.addEventListener('change', function(e) {
                    const newPriceListId = e.target.value;

                    self.selectedPriceListId = newPriceListId;

                    // Update prices for all items in the invoice
                    self.updateAllItemPrices();
                });
            },

            /**
             * Update prices for all items in the invoice based on selected price list
             */
            updateAllItemPrices() {
                if (!this.selectedPriceListId) {
                    return;
                }


                // Update each item's price
                this.invoiceItems.forEach((item, index) => {
                    this.updateItemPrice(item, index);
                });

                // Re-render and recalculate
                this.renderItems();
                this.calculateTotals();
            },

            /**
             * Update single item price based on selected price list
             */
            updateItemPrice(item, index) {
                if (!this.selectedPriceListId || !item.item_id || !item.unit_id) {
                    return;
                }

                // Fetch price from API
                const url =
                    `/api/invoices/items/${item.item_id}/price?price_list_id=${this.selectedPriceListId}&unit_id=${item.unit_id}`;

                fetch(url)
                    .then(response => response.json())
                    .then(data => {
                        if (data.success && data.price !== null) {
                            item.price = parseFloat(data.price);

                            // Calculate sub_value using the same logic as calculateItemTotal
                            const subtotal = item.quantity * item.price;
                            let discountAmount = 0;

                            if (this.itemDiscountType === 'percentage') {
                                discountAmount = (subtotal * item.discount) / 100;
                            } else {
                                discountAmount = item.discount;
                            }

                            item.sub_value = subtotal - discountAmount;

                            // Update display
                            this.renderItems();
                            this.calculateTotals();
                        } else {
                            console.log(
                                `⚠️ No price found for item ${item.item_id} in price list ${this.selectedPriceListId}`
                            );
                        }
                    })
                    .catch(error => {
                        console.error('❌ Error fetching item price:', error);
                    });
            },

            // Save invoice - NO VALIDATION, just send everything
            submitForm(printAfterSave = false) {

                if (!this.validateForm()) {
                    return;
                }

                // ✅ Fill hidden inputs with current data
                document.getElementById('form-type').value = this.type;
                document.getElementById('form-branch-id').value = this.branchId;

                // ✅ Add pro_id from the visible field
                const proId = document.getElementById('pro-id')?.value || '';
                document.getElementById('form-pro-id').value = proId;

                // For Select2 inputs, use jQuery to get current value
                const acc1Val = $('#acc1-id').val();
                const acc2Val = $('#acc2-id').val();

                document.getElementById('form-acc1-id').value = acc1Val || '';
                document.getElementById('form-acc2-id').value = acc2Val || '';

                document.getElementById('form-currency-id').value = this.currencyId || 1;
                document.getElementById('form-currency-rate').value = this.exchangeRate || 1;

                // ✅ Add template ID
                const templateSelect = document.getElementById('invoice-template');
                const selectedTemplateId = templateSelect?.value || '';
                if (document.getElementById('form-template-id')) {
                    document.getElementById('form-template-id').value = selectedTemplateId;
                }

                document.getElementById('form-pro-date').value = document.getElementById('pro-date')?.value || '';
                document.getElementById('form-emp-id').value = document.getElementById('emp-id')?.value || '';
                document.getElementById('form-delivery-id').value = document.getElementById('delivery-id')?.value || '';
                document.getElementById('form-accural-date').value = document.getElementById('accural-date')?.value ||
                    '';
                document.getElementById('form-serial-number').value = document.getElementById('serial-number')?.value ||
                    '';
                document.getElementById('form-cash-box-id').value = document.getElementById('cash_box_id')?.value || '';
                document.getElementById('form-notes').value = document.getElementById('notes')?.value || '';
                document.getElementById('form-payment-notes').value = document.getElementById('payment-notes')?.value || '';

                // ✅ Determine exchange rate based on multi-currency setting
                @if (setting('multi_currency_enabled'))
                    const exchangeRate = this.exchangeRate || 1;
                @else
                    const exchangeRate = 1;
                @endif

                // ✅ Convert totals to base currency before saving (round properly and format to 2 decimals)
                document.getElementById('form-discount-percentage').value = parseFloat(this.discountPercentage).toFixed(2);
                document.getElementById('form-discount-value').value = (Math.round(this.discountValue * exchangeRate * 100) / 100).toFixed(2);
                document.getElementById('form-additional-percentage').value = parseFloat(this.additionalPercentage).toFixed(2);
                document.getElementById('form-additional-value').value = (Math.round(this.additionalValue * exchangeRate * 100) / 100).toFixed(2);
                document.getElementById('form-vat-percentage').value = parseFloat(this.vatPercentage).toFixed(2);
                document.getElementById('form-vat-value').value = (Math.round(this.vatValue * exchangeRate * 100) / 100).toFixed(2);
                document.getElementById('form-withholding-tax-percentage').value = parseFloat(this.withholdingTaxPercentage).toFixed(2);
                document.getElementById('form-withholding-tax-value').value = (Math.round(this.withholdingTaxValue * exchangeRate * 100) / 100).toFixed(2);
                document.getElementById('form-subtotal').value = (Math.round(this.subtotal * exchangeRate * 100) / 100).toFixed(2);
                document.getElementById('form-total-after-additional').value = (Math.round(this.totalAfterAdditional * exchangeRate * 100) / 100).toFixed(2);
                document.getElementById('form-received-from-client').value = (Math.round(this.receivedFromClient * exchangeRate * 100) / 100).toFixed(2);
                document.getElementById('form-remaining').value = (Math.round(this.remaining * exchangeRate * 100) / 100).toFixed(2);

                // ✅ Add items as hidden inputs
                const itemsContainer = document.getElementById('form-items-container');
                itemsContainer.innerHTML = ''; // Clear previous items

                this.invoiceItems.forEach((item, index) => {
                    // ✅ Convert to base currency and round properly
                    const priceInBaseCurrency = Math.round((parseFloat(item.price) || 0) * exchangeRate * 100) / 100;
                    const subValueInBaseCurrency = Math.round((parseFloat(item.sub_value) || 0) * exchangeRate * 100) / 100;
                    const discountValueInBaseCurrency = Math.round((parseFloat(item.discount_value) || 0) * exchangeRate * 100) / 100;

                    // Create hidden inputs for each item field
                    const fields = {
                        'item_id': item.item_id,
                        'unit_id': item.unit_id,
                        'quantity': parseFloat(item.quantity || 0).toFixed(2),
                        'price': priceInBaseCurrency.toFixed(2), // ✅ Converted to base currency with 2 decimals
                        'discount': parseFloat(item.discount || 0).toFixed(2), // ✅ Ensure it's a number
                        'discount_percentage': parseFloat(item.discount_percentage || 0).toFixed(2),
                        'discount_value': discountValueInBaseCurrency.toFixed(2), // ✅ Converted to base currency with 2 decimals
                        'additional': parseFloat(item.additional || 0).toFixed(2), // ✅ Ensure it's a number
                        'sub_value': subValueInBaseCurrency.toFixed(2), // ✅ Converted to base currency with 2 decimals
                        'batch_number': item.batch_number || '',
                        'expiry_date': item.expiry_date || '',
                        'notes': item.notes || '',
                        'length': parseFloat(item.length || 0).toFixed(2),
                        'width': parseFloat(item.width || 0).toFixed(2),
                        'height': parseFloat(item.height || 0).toFixed(2),
                        'density': parseFloat(item.density || 0).toFixed(2)
                    };

                    Object.entries(fields).forEach(([field, value]) => {
                        const input = document.createElement('input');
                        input.type = 'hidden';
                        input.name = `items[${index}][${field}]`;
                        input.value = value;
                        itemsContainer.appendChild(input);
                    });
                });

                // ✅ Mark as saved to prevent beforeunload warning
                this.isSaved = true;

                // If print after save, submit via AJAX
                if (printAfterSave) {
                    this.submitFormAjax();
                } else {
                    // ✅ Submit the form normally
                    document.getElementById('invoice-form').submit();
                }
            },

            /**
             * Submit form via AJAX and print after success
             */
            submitFormAjax() {
                const form = document.getElementById('invoice-form');
                const formData = new FormData(form);

                // Show loading
                Swal.fire({
                    title: 'جاري الحفظ...',
                    text: 'يرجى الانتظار',
                    allowOutsideClick: false,
                    didOpen: () => {
                        Swal.showLoading();
                    }
                });

                fetch(form.action, {
                        method: 'POST',
                        body: formData,
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest',
                            'Accept': 'application/json'
                        }
                    })
                    .then(response => response.json())
                    .then(data => {
                        Swal.close();

                        if (data.success && data.operation_id) {

                            Swal.fire({
                                title: 'تم الحفظ بنجاح!',
                                text: 'سيتم فتح صفحة الطباعة',
                                icon: 'success',
                                timer: 1500,
                                showConfirmButton: false
                            }).then(() => {
                                // Open print page in new window
                                const printUrl = data.print_url || `/invoice/print/${data.operation_id}`;
                                window.open(printUrl, '_blank');

                                // Optionally reload the page to show the saved invoice
                                if (this.settings.new_after_save) {
                                    window.location.reload();
                                }
                            });
                        } else {
                            Swal.fire('خطأ', data.message || 'حدث خطأ أثناء الحفظ', 'error');
                        }
                    })
                    .catch(error => {
                        Swal.close();
                        console.error('❌ Error saving invoice:', error);
                        Swal.fire('خطأ', 'حدث خطأ أثناء الحفظ', 'error');
                    });
            },

            /**
             * Print invoice
             * Save invoice first then print
             */
            printInvoice() {

                // Check if we have items
                if (this.invoiceItems.length === 0) {
                    Swal.fire('خطأ', 'لا يمكن طباعة فاتورة بدون أصناف.', 'error');
                    return;
                }

                // Validate form
                if (!this.validateForm()) {
                    return;
                }

                // Save and print directly without confirmation
                this.submitForm(true);
            },

            /**
             * Open installment modal
             */
            openInstallmentModal() {
                // Get current client account ID
                const acc1Id = $('#acc1-id').val();

                if (!acc1Id || acc1Id === '' || acc1Id === 'null') {
                    Swal.fire({
                        icon: 'warning',
                        title: 'تحذير',
                        text: 'يرجى اختيار العميل في الفاتورة أولاً',
                        confirmButtonText: 'حسناً'
                    });
                    return;
                }

                // Use Livewire.dispatch instead of window event
                const eventData = {
                    invoiceTotal: this.totalAfterAdditional,
                    paidAmount: this.receivedFromClient || 0,
                    clientAccountId: acc1Id
                };


                // Dispatch Livewire event to update modal data AND open it
                Livewire.dispatch('update-installment-data', {
                    invoiceTotal: this.totalAfterAdditional,
                    paidAmount: this.receivedFromClient || 0,
                    clientAccountId: acc1Id
                });
            },

            // ✅ Validate form before submission
            validateForm() {
                // 1. Check for items
                if (this.invoiceItems.length === 0) {
                    Swal.fire('خطأ', 'لا يمكن حفظ فاتورة بدون أصناف.', 'error');
                    return false;
                }

                // 2. Check for required headers
                const acc1Val = $('#acc1-id').val();
                if (!acc1Val) {
                    Swal.fire('خطأ', 'يرجى اختيار العميل/المورد.', 'error');
                    return false;
                }

                const acc2Val = $('#acc2-id').val();
                if (!acc2Val) {
                    Swal.fire('خطأ', 'يرجى اختيار المخزن.', 'error');
                    return false;
                }

                // 3. Check items data
                for (let i = 0; i < this.invoiceItems.length; i++) {
                    const item = this.invoiceItems[i];

                    // Prevent zero/negative quantity
                    if (item.quantity <= 0) {
                        Swal.fire('خطأ', `الصنف "${item.name}" لديه كمية غير صالحة.`, 'error');
                        return false;
                    }

                    // Prevent zero price if settings don't allow it
                    if (!this.settings.allow_zero_price_in_invoice && item.price <= 0) {
                        Swal.fire('خطأ', `الصنف "${item.name}" لديه سعر صفري وهذا غير مسموح به.`, 'error');
                        return false;
                    }
                }

                // 4. Check negative invoice total if settings don't allow it
                if (this.remaining < 0 && this.settings.prevent_negative_invoice) {
                    Swal.fire('خطأ', 'قيمة الفاتورة لا يمكن أن تكون سالبة.', 'error');
                    return false;
                }

                // 5. Check zero invoice total if settings don't allow it
                if (this.remaining === 0 && !this.settings.allow_zero_invoice_total) {
                    Swal.fire('خطأ', 'قيمة الفاتورة لا يمكن أن تكون صفراً.', 'error');
                    return false;
                }

                return true;
            },

            // Update status message
            updateStatus(text, type = 'info') {
                const status = document.getElementById('search-status');
                if (status) {
                    status.innerHTML = text;
                    status.className = 'text-' + type;
                }
            },

            // ✅ Handle barcode search
            handleBarcodeSearch(barcode) {
                if (!this.allItems || this.allItems.length === 0) {
                    console.error('❌ No items loaded!');
                    alert('{{ __("Items not loaded yet. Please wait...") }}');
                    return;
                }

                // البحث في الأصناف المحملة
                let foundItem = null;

                for (let i = 0; i < this.allItems.length; i++) {
                    const item = this.allItems[i];

                    if (!item.barcode) {
                        continue;
                    }

                    // Handle both array and string formats
                    if (Array.isArray(item.barcode)) {
                        if (item.barcode.includes(barcode)) {
                            console.log('✅ Found in array!', item.name);
                            foundItem = item;
                            break;
                        }
                    } else if (typeof item.barcode === 'string') {
                        if (item.barcode === barcode) {
                            console.log('✅ Found as string!', item.name);
                            foundItem = item;
                            break;
                        }
                    }
                }
                if (foundItem) {
                    // ✅ وجدنا الصنف - أضفه للفاتورة
                    this.addItem(foundItem);

                    // Clear barcode input
                    const barcodeInput = document.getElementById('barcode-input');
                    if (barcodeInput) {
                        barcodeInput.value = '';
                    }

                    this.updateStatus('✓ {{ __("Item added") }}: ' + foundItem.name, 'success');
                } else {
                    this.openCreateItemModal(barcode);
                }
            },

            // ✅ Open modal to create new item with barcode
            openCreateItemModal(barcode) {
                const barcodeInput = document.getElementById('barcode-input');
                if (barcodeInput) {
                    barcodeInput.value = '';
                }

                // Check if Swal is loaded
                if (typeof Swal === 'undefined') {
                    console.error('❌ SweetAlert2 is not loaded!');
                    alert('{{ __("Barcode") }} ' + barcode + ' {{ __("not found. Do you want to create a new item?") }}');
                    const itemName = prompt('{{ __("Enter item name") }}');
                    if (itemName && itemName.trim()) {
                        this.createItemWithBarcode(itemName.trim(), barcode);
                    }
                    return;
                }

                Swal.fire({
                    title: '{{ __("Item not found") }}',
                    html: `
                        <p class="mb-3">{{ __("Barcode") }} <strong>${barcode}</strong> {{ __("is not registered in the system.") }}</p>
                        <div class="form-group text-start">
                            <label for="new-item-name" class="form-label">{{ __("Item Name:") }}</label>
                            <input type="text" id="new-item-name" class="form-control" placeholder="{{ __("Enter item name") }}">
                        </div>
                    `,
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonText: '{{ __("Create Item") }}',
                    cancelButtonText: '{{ __("Cancel") }}',
                    confirmButtonColor: '#28a745',
                    cancelButtonColor: '#6c757d',
                    preConfirm: () => {
                        const itemName = document.getElementById('new-item-name').value.trim();
                        if (!itemName) {
                            Swal.showValidationMessage('{{ __("Item name is required") }}');
                            return false;
                        }
                        return {
                            itemName,
                            barcode
                        };
                    },
                    didOpen: () => {
                        // Focus on input
                        const input = document.getElementById('new-item-name');
                        if (input) {
                            input.focus();
                            // Submit on Enter
                            input.addEventListener('keydown', (e) => {
                                if (e.key === 'Enter') {
                                    e.preventDefault();
                                    Swal.clickConfirm();
                                }
                            });
                        }
                    }
                }).then((result) => {
                    if (result.isConfirmed && result.value) {
                        this.createItemWithBarcode(result.value.itemName, result.value.barcode);
                    }
                });
            },

            // ✅ Create new item with barcode via API
            createItemWithBarcode(itemName, barcode) {
                // Show loading
                Swal.fire({
                    title: '{{ __("Creating...") }}',
                    text: '{{ __("Please wait") }}',
                    allowOutsideClick: false,
                    didOpen: () => {
                        Swal.showLoading();
                    }
                });

                // Send request to create item
                fetch('/api/items/quick-create', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                            'Accept': 'application/json'
                        },
                        body: JSON.stringify({
                            name: itemName,
                            barcode: barcode,
                            active: 1
                        })
                    })
                    .then(response => response.json())
                    .then(data => {
                        Swal.close();

                        if (data.success && data.item) {
                            // ✅ تم إنشاء الصنف بنجاح
                            Swal.fire({
                                title: '{{ __("Success!") }}',
                                text: `{{ __("Item created and added to invoice") }}: "${itemName}"`,
                                icon: 'success',
                                timer: 2000,
                                showConfirmButton: false
                            });

                            // Add item to allItems array
                            this.allItems.push(data.item);

                            // Add item to invoice
                            this.addItem(data.item);

                            this.updateStatus('✓ {{ __("Item created and added") }}: ' + itemName, 'success');
                        } else {
                            Swal.fire('{{ __("Error") }}', data.message || '{{ __("An error occurred while creating the item") }}', 'error');
                        }
                    })
                    .catch(error => {
                        Swal.close();
                        console.error('❌ Error creating item:', error);
                        Swal.fire('{{ __("Error") }}', '{{ __("An error occurred while creating the item") }}', 'error');
                    });
            },

            // ✅ Show validation error
            showValidationError(input, message) {
                // Remove existing error
                this.hideValidationError(input);

                // Create error element
                const errorDiv = document.createElement('div');
                errorDiv.className = 'invalid-feedback d-block';
                errorDiv.style.cssText = 'font-size: 0.875rem; color: #dc3545; margin-top: 0.25rem;';
                errorDiv.textContent = message;
                errorDiv.dataset.validationError = 'true';

                // Insert after input
                input.parentNode.appendChild(errorDiv);
            },

            // ✅ Show validation warning
            showValidationWarning(input, message) {
                // Remove existing warning
                this.hideValidationError(input);

                // Create warning element
                const warningDiv = document.createElement('div');
                warningDiv.className = 'text-warning d-block';
                warningDiv.style.cssText = 'font-size: 0.875rem; margin-top: 0.25rem;';
                warningDiv.textContent = message;
                warningDiv.dataset.validationError = 'true';

                // Insert after input
                input.parentNode.appendChild(warningDiv);
            },

            // ✅ Hide validation error
            hideValidationError(input) {
                const parent = input.parentNode;
                const existingError = parent.querySelector('[data-validation-error="true"]');
                if (existingError) {
                    existingError.remove();
                }
            },

            // Debug function - test search manually
            testSearch(term = 'test') {
                // Check if dropdown exists
                const dropdown = document.getElementById('search-results-dropdown');
                // Check if search input exists
                const searchInput = document.getElementById('search-input');
                // Run search
                this.handleSearch(term);
            },

            // Debug function - show dropdown manually
            forceShowDropdown() {
                const dropdown = document.getElementById('search-results-dropdown');
                if (dropdown) {
                    dropdown.style.display = 'block';
                    dropdown.style.visibility = 'visible';
                    dropdown.style.opacity = '1';
                    dropdown.style.position = 'fixed';
                    dropdown.style.top = '200px';
                    dropdown.style.left = '200px';
                    dropdown.style.zIndex = '999999';
                    dropdown.style.background = 'white';
                    dropdown.style.border = '2px solid red';
                    dropdown.style.padding = '20px';
                    dropdown.innerHTML =
                        '<div style="color: red; font-size: 20px;">TEST DROPDOWN - إذا ظهرت هذه الرسالة، الـ dropdown موجود!</div>';
                    dropdown.classList.remove('hidden');
                } else {
                    console.error('❌ Dropdown not found!');
                }
            },
            // Move to next/previous field (Tab Order)
            moveToNextField(currentIndex, currentField, isReverse = false) {
                const fieldMap = {
                    'unit': 'unit',
                    'quantity': 'quantity',
                    'batch': 'batch_number',
                    'expiry': 'expiry_date',
                    'price': 'price',
                    'discount': 'discount',
                    'discount_percentage': 'discount_percentage',
                    'discount_value': 'discount_value',
                    'length': 'length',
                    'width': 'width',
                    'height': 'height',
                    'density': 'density'
                };

                const currentColumn = fieldMap[currentField] || currentField;
                const editableColumns = this.getEditableColumns();
                const currentPos = editableColumns.indexOf(currentColumn);

                if (currentPos === -1) {
                    this.focusSearchInput();
                    return;
                }

                if (isReverse) {
                    // Shift+Tab: Go backward
                    if (currentPos > 0) {
                        const prevColumn = editableColumns[currentPos - 1];
                        this.focusField(prevColumn, currentIndex);
                    } else {
                        // First field → go to search
                        this.focusSearchInput();
                    }
                } else {
                    // Enter/Tab: Go forward
                    if (currentPos < editableColumns.length - 1) {
                        const nextColumn = editableColumns[currentPos + 1];
                        this.focusField(nextColumn, currentIndex);
                    } else {
                        // ✅ Last field → Skip delete button, go back to search
                        this.focusSearchInput();
                    }
                }
            },

            // Get editable columns (skip item_name, code, sub_value) - IN ORDER
            getEditableColumns() {
                const nonEditable = ['item_name', 'code', 'sub_value'];
                // Return columns in the same order as visibleColumns
                return this.visibleColumns.filter(col => !nonEditable.includes(col));
            },

            // Focus a specific field
            focusField(columnName, index) {
                const fieldId = this.getFieldIdFromColumn(columnName, index);

                if (!fieldId) {
                    console.warn('⚠️ No fieldId generated for:', {
                        columnName,
                        index
                    });
                    console.warn('⚠️ NOT falling back to search - staying on current field');
                    return; // Don't fallback to search
                }

                const el = document.getElementById(fieldId);
                if (el) {
                    el.focus();
                    if (el.tagName === 'INPUT' && el.type !== 'date') {
                        el.select();
                    }
                    return;
                } else {
                    console.error('❌ Element not found in DOM:', fieldId);
                    console.error('❌ Available elements with similar IDs:');
                    // List all elements with IDs starting with the column name
                    const columnPrefix = columnName.replace('_', '-');
                    const similarElements = document.querySelectorAll(`[id^="${columnPrefix}"]`);
                    similarElements.forEach(elem => {});
                }
            },

            // Focus search input helper
            focusSearchInput() {
                const searchInput = document.getElementById('search-input');
                if (searchInput) {
                    searchInput.focus();
                    searchInput.select();
                }
            },

            // Get field ID from column name
            getFieldIdFromColumn(columnName, index) {
                const columnToFieldMap = {
                    'unit': 'unit-' + index,
                    'quantity': 'quantity-' + index,
                    'batch_number': 'batch-' + index,
                    'expiry_date': 'expiry-' + index,
                    'price': 'price-' + index,
                    'discount': 'discount-' + index,
                    'discount_percentage': 'discount-percentage-' + index,
                    'discount_value': 'discount-value-' + index,
                    'length': 'length-' + index,
                    'width': 'width-' + index,
                    'height': 'height-' + index,
                    'density': 'density-' + index
                };

                return columnToFieldMap[columnName] || null;
            },
        };

        // Initialize when DOM is ready AND jQuery + Select2 are loaded
        function initWhenReady() {
            if (typeof jQuery === 'undefined') {
                setTimeout(initWhenReady, 100);
                return;
            }

            if (typeof jQuery.fn.select2 === 'undefined') {
                setTimeout(initWhenReady, 100);
                return;
            }

            const recommendedContainer = document.getElementById('recommended-items-list');


            InvoiceApp.init();

            // ✅ Add beforeunload event to warn user before closing page with unsaved data
            window.addEventListener('beforeunload', function(e) {
                // Check if there are items in the invoice AND it's not saved yet
                if (InvoiceApp.invoiceItems && InvoiceApp.invoiceItems.length > 0 && !InvoiceApp.isSaved) {
                    // Standard way to show confirmation dialog
                    e.preventDefault();
                    e.returnValue = ''; // Chrome requires returnValue to be set
                    return ''; // Some browsers show this message
                }
            });

            // ✅ Handle Add Account Button Click
            const addAcc1Btn = document.getElementById('add-acc1-btn');
            if (addAcc1Btn) {
                addAcc1Btn.addEventListener('click', function() {
                    // Trigger Livewire component to open modal
                    const container = document.getElementById('account-creator-container');
                    if (container) {
                        container.style.display = 'block';
                        // Find the Livewire component button and click it
                        const livewireBtn = container.querySelector('button[wire\\:click="openModal"]');
                        if (livewireBtn) {
                            livewireBtn.click();
                        }
                    }
                });
            }

            // ✅ Listen for account-created event from Livewire
            // Method 1: Using Livewire.on (Livewire 3)
            document.addEventListener('livewire:initialized', () => {
                Livewire.on('account-created', (event) => {
                    handleAccountCreated(event);
                });
            });

            // Method 2: Using window event listener (fallback)
            window.addEventListener('account-created', (event) => {
                handleAccountCreated(event.detail);
            });

            // Handler function
            function handleAccountCreated(eventData) {
                // Handle both array format and direct object format
                const accountData = Array.isArray(eventData) ? eventData[0] : eventData;


                if (accountData && accountData.account) {
                    const account = accountData.account;

                    // Add new option to Select2
                    const newOption = new Option(account.aname, account.id, true, true);
                    $('#acc1-id').append(newOption).trigger('change');

                    // Show success message
                    Swal.fire({
                        icon: 'success',
                        title: 'تم بنجاح',
                        text: 'تم إضافة ' + account.aname + ' وتحديده في الفاتورة',
                        timer: 2000,
                        showConfirmButton: false
                    });

                    // Hide the container
                    const container = document.getElementById('account-creator-container');
                    if (container) {
                        container.style.display = 'none';
                    }
                } else {
                    console.error('❌ Invalid account data:', accountData);
                }
            }
        }

        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', initWhenReady);
        } else {
            initWhenReady();
        }

        // Expose reload function
        window.reloadSearchItems = () => InvoiceApp.loadItems();
    </script>
@endsection
