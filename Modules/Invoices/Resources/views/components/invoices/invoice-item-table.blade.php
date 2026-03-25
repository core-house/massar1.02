<div class="table-responsive invoice-scroll-container card border border-top-0 border-secondary border-3 h-100"
    style="overflow-y: auto; overflow-x: auto; border: 1px solid #dee2e6; position: relative; z-index: 1;">

    <style>
        .invoice-data-grid {
            border-collapse: separate !important;
            border-spacing: 0;
            width: 100%;
            border: none;
        }

        .invoice-data-grid th {
            padding: 6px !important;
            background-color: #bbc8d6ff;
            border: 1px solid #dee2e6;
            border-top: none;
            vertical-align: middle;
            font-weight: bold;
            font-size: 0.8rem;
            position: sticky;
            top: 0;
            z-index: 10;
            box-shadow: 0 1px 0 #dee2e6;
        }

        .invoice-data-grid td {
            padding: 0 !important;
            border: 1px solid #5f5f5fff;
            vertical-align: middle;
            height: 32px;
            font-size: 0.75rem;
        }

        .invoice-data-grid .search-row {
            background-color: #f8f9fa;
            position: sticky;
            top: 40px;
            z-index: 9;
        }

        .invoice-data-grid .search-row td {
            padding: 4px !important;
            border: 1px solid #dee2e6;
        }

        .invoice-data-grid tbody tr:nth-of-type(odd):not(.search-row) {
            background-color: #ffffffff;
        }

        .invoice-data-grid tbody tr:nth-of-type(even):not(.search-row) {
            background-color: #cfcfcf8e;
        }

        .invoice-data-grid .form-control,
        .invoice-data-grid .form-select,
        .invoice-data-grid input,
        .invoice-data-grid select {
            border: none !important;
            border-radius: 0 !important;
            box-shadow: none !important;
            width: 100% !important;
            height: 100% !important;
            min-height: 32px;
            padding: 4px 6px !important;
            background-color: transparent;
            margin: 0 !important;
            font-size: 0.75rem;
        }

        .invoice-data-grid .form-control:focus,
        .invoice-data-grid .form-select:focus,
        .invoice-data-grid input:focus,
        .invoice-data-grid select:focus {
            background-color: #e8f0fe !important;
            outline: none !important;
            box-shadow: inset 0 0 0 2px #0d6efd !important;
            z-index: 1;
            position: relative;
        }

        .invoice-data-grid input[type=number]::-webkit-inner-spin-button,
        .invoice-data-grid input[type=number]::-webkit-outer-spin-button {
            -webkit-appearance: none;
            margin: 0;
        }

        .invoice-data-grid input[readonly],
        .invoice-data-grid input:disabled {
            background-color: #f8f9fa;
            cursor: not-allowed;
            color: #6c757d;
        }

        .invoice-data-grid input.text-center {
            text-align: center;
        }

        .invoice-data-grid td.action-cell {
            padding: 2px !important;
            text-align: center;
        }

        .invoice-data-grid .static-text {
            display: flex;
            align-items: center;
            padding: 0 6px;
            height: 100%;
            width: 100%;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            font-size: 0.75rem;
        }

        .search-results-dropdown {
            position: fixed !important;
            max-height: 400px;
            overflow-y: auto;
            background: white !important;
            border: none !important;
            border-radius: 8px !important;
            z-index: 999999 !important;
            box-shadow: 0 8px 24px rgba(0, 0, 0, 0.15) !important;
            min-width: 400px;
            visibility: visible !important;
            opacity: 1 !important;
        }

        .search-results-dropdown::-webkit-scrollbar {
            width: 8px;
        }

        .search-results-dropdown::-webkit-scrollbar-track {
            background: #f1f1f1;
            border-radius: 4px;
        }

        .search-results-dropdown::-webkit-scrollbar-thumb {
            background: #888;
            border-radius: 4px;
        }

        .search-results-dropdown::-webkit-scrollbar-thumb:hover {
            background: #555;
        }

        .search-results-dropdown .list-group-item {
            border-radius: 0;
            border-left: none;
            border-right: none;
            cursor: pointer;
            transition: all 0.15s ease;
            background: white !important;
            color: #212529 !important;
            display: flex !important;
        }

        .search-results-dropdown .list-group-item:hover {
            background-color: #e8f0fe;
            transform: translateX(4px);
        }

        .search-results-dropdown .list-group-item.active {
            background-color: #0d6efd !important;
            color: white !important;
            border-color: #0d6efd !important;
        }

        .search-results-dropdown .list-group-item.active * {
            color: white !important;
        }

        .search-results-dropdown .list-group-item.active .badge {
            background-color: white !important;
            color: #0d6efd !important;
        }
    </style>

    <table class="table invoice-data-grid mb-0" style="min-width: 1200px;">
        <thead class="table-light text-center align-middle">
            <tr>
                @php
                    $columnNames = [
                        'item_name' => __('invoices::invoices.item_name_col'),
                        'code' => __('invoices::invoices.code_col'),
                        'unit' => __('invoices::invoices.unit_col'),
                        'quantity' => __('invoices::invoices.quantity_col'),
                        'batch_number' => __('invoices::invoices.batch_number_col'),
                        'expiry_date' => __('invoices::invoices.expiry_date_col'),
                        'price' => __('invoices::invoices.price_col'),
                        'discount' => __('invoices::invoices.discount_col'),
                        'sub_value' => __('invoices::invoices.value_col'),
                    ];

                    $visibleColumns = ['item_name', 'code', 'unit', 'quantity'];

                    if (in_array($type, [10, 11, 12, 13, 19, 20])) {
                        $visibleColumns[] = 'batch_number';
                        $visibleColumns[] = 'expiry_date';
                    }

                    $visibleColumns = array_merge($visibleColumns, ['price', 'discount', 'sub_value']);
                @endphp

                @foreach ($visibleColumns as $columnKey)
                    <th class="font-bold fw-bold text-center" style="font-size: 0.8rem;" data-column="{{ $columnKey }}"
                        data-default-width="100">
                        {{ $columnNames[$columnKey] ?? $columnKey }}
                    </th>
                @endforeach
                <th class="font-bold fw-bold text-center" style="font-size: 0.8rem;">
                    {{ __('invoices::invoices.action_col') }}</th>
            </tr>
        </thead>

        <tbody id="invoice-items-tbody">
            {{-- Invoice Items (rendered by JavaScript) - will appear here ABOVE search row --}}

            {{-- Search Row - Always at bottom --}}
            <tr class="search-row">
                <td colspan="2" style="position: relative;">
                    <input type="text" id="search-input" class="form-control"
                        placeholder="{{ __('invoices::invoices.search_item_placeholder') }}"
                        style="min-height: 36px; font-size: 0.85rem;" autocomplete="off" autofocus>

                    {{-- Search Results Dropdown --}}
                    <div id="search-results-dropdown" class="search-results-dropdown"
                        style="display: none; position: absolute; z-index: 999999; left: 0; right: 0;">
                    </div>
                </td>

                <td colspan="2">
                    <input type="text" id="barcode-input" class="form-control"
                        placeholder="{{ __('invoices::invoices.scan_barcode_placeholder') }}"
                        style="min-height: 36px; font-size: 0.85rem;">
                </td>

                <td colspan="20">
                    <div class="d-flex align-items-center justify-content-between px-2" style="min-height: 36px;">
                        <small id="search-status" class="text-muted">
                            <i class="las la-spinner la-spin me-1"></i>
                            {{ __('invoices::invoices.loading_items') }}
                        </small>
                        <button type="button" id="reload-items-btn"
                            onclick="window.reloadSearchItems && window.reloadSearchItems()"
                            class="btn btn-sm btn-outline-primary" style="font-size: 0.7rem; padding: 2px 8px;">
                            <i class="las la-sync"></i>
                            {{ __('invoices::invoices.refresh') }}
                        </button>
                    </div>
                </td>
            </tr>
        </tbody>
    </table>
</div>

<script>
    // Function to apply column widths from template
    window.applyInvoiceTableColumnWidths = function(columnWidths) {
        if (!columnWidths || typeof columnWidths !== 'object') {
            console.warn('Invalid column widths data');
            return;
        }

        console.log('Applying column widths:', columnWidths);

        // ✅ Sync with InvoiceApp if it exists
        if (window.InvoiceApp) {
            window.InvoiceApp.columnWidths = columnWidths;
        }

        // Apply widths to table headers
        document.querySelectorAll('.invoice-data-grid th[data-column]').forEach(th => {
            const columnKey = th.getAttribute('data-column');
            if (columnWidths[columnKey]) {
                // Ensure a minimum width of 5px to prevent broken layout (reduced from 50px as per user request)
                const width = Math.max(5, parseInt(columnWidths[columnKey]) || 0);
                th.style.width = width + 'px';
                th.style.minWidth = width + 'px';
                // Removed maxWidth to allow growth if needed
                th.style.maxWidth = '';
                console.log(`Set column ${columnKey} width to ${width}px`);
            }
        });

        // Set a reasonable width for action column if it exists in the table
        const actionTh = document.querySelector('.invoice-data-grid th:not([data-column])');
        if (actionTh && actionTh.textContent.trim() !== '') {
            actionTh.style.width = '80px';
            actionTh.style.minWidth = '80px';
        }
    };

    // Listen for template change event
    document.addEventListener('DOMContentLoaded', function() {
        const templateSelect = document.getElementById('invoice-template');
        if (templateSelect) {
            templateSelect.addEventListener('change', async function(e) {
                const templateId = e.target.value;
                if (!templateId) {
                    console.log('No template selected');
                    return;
                }

                try {
                    const response = await fetch(`/invoice-templates/${templateId}/data`);
                    const result = await response.json();

                    if (result.success && result.data.column_widths) {
                        window.applyInvoiceTableColumnWidths(result.data.column_widths);
                    } else {
                        console.warn('No column widths in template data');
                    }
                } catch (error) {
                    console.error('Error fetching template data:', error);
                }
            });

            // Apply widths on page load if template is already selected
            if (templateSelect.value) {
                templateSelect.dispatchEvent(new Event('change'));
            }
        }
    });
</script>
