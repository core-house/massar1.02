<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" dir="{{ app()->getLocale() == 'ar' ? 'rtl' : 'ltr' }}">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ __('invoices::invoices.print_invoice') }} - {{ $titles[$type] ?? __('invoices::invoices.invoice') }}
    </title>
    <style>
        @media print {
            body {
                margin: 0;
            }

            .no-print {
                display: none;
            }
        }

        body {
            font-family: 'Tahoma', 'Arial', sans-serif;
            font-size: 12px;
            line-height: 1.4;
            margin: 10px;
            color: #000;
            background: white;
        }

        .invoice-container {
            max-width: 800px;
            margin: 0 auto;
            border: 2px solid #000;
            padding: 0;
        }

        .header {
            background-color: #f0f0f0;
            padding: 10px;
            text-align: center;
            border-bottom: 2px solid #000;
        }

        .company-name {
            font-size: 18px;
            font-weight: bold;
            margin-bottom: 5px;
        }

        .invoice-title {
            font-size: 16px;
            font-weight: bold;
            margin-bottom: 10px;
        }

        .invoice-info {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
            padding: 15px;
            border-bottom: 2px solid #000;
        }

        .info-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 8px;
            padding: 3px 0;
        }

        .info-label {
            font-weight: bold;
            min-width: 120px;
        }

        .info-value {
            flex: 1;
            text-align: left;
            padding-left: 10px;
        }

        .items-table {
            border-collapse: collapse;
            margin: 0;
            border: none;
            table-layout: fixed;
            width: 100%; /* Default to 100% but overridden by inline width if specified */
        }

        .items-table th,
        .items-table td {
            border: 1px solid #666;
            padding: 8px 5px;
            text-align: center;
            font-size: 11px;
            word-wrap: break-word; /* Allow wrapping instead of overlap */
            overflow: hidden; /* Prevent text from spilling out */
        }

        .items-table th {
            background-color: #f0f0f0;
            font-weight: bold;
            border: 1px solid #666;
        }

        .totals-section {
            padding: 15px;
            border-top: 2px solid #000;
        }

        .totals-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
        }

        .total-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 5px;
            padding: 2px 0;
        }

        .total-label {
            font-weight: bold;
        }

        .total-value {
            min-width: 100px;
            text-align: left;
        }

        .net-total {
            font-size: 14px;
            font-weight: bold;
            border-top: 2px solid #000;
            padding-top: 5px;
            margin-top: 5px;
        }

        .amount-in-words {
            background-color: #f9f9f9;
            padding: 10px;
            border: 1px solid #666;
            margin: 10px 0;
            text-align: center;
            font-weight: bold;
        }

        .footer {
            display: grid;
            grid-template-columns: 1fr 1fr 1fr;
            gap: 20px;
            padding: 15px;
            border-top: 2px solid #000;
            font-size: 11px;
        }

        .footer-section {
            text-align: center;
        }

        .footer-label {
            font-weight: bold;
            margin-bottom: 5px;
        }

        .page-info {
            position: absolute;
            top: 10px;
            left: 10px;
            font-size: 10px;
        }

        .text-success {
            color: #28a745;
        }

        .text-danger {
            color: #dc3545;
        }

        .text-primary {
            color: #007bff;
        }

        .item-image {
            width: 45px;
            height: 45px;
            object-fit: cover;
            border-radius: 4px;
            border: 1px solid #ddd;
            display: block;
            margin: 0 auto 3px;
        }

        .badge {
            display: inline-block;
            padding: 0.25em 0.4em;
            font-size: 75%;
            font-weight: 700;
            line-height: 1;
            text-align: center;
            white-space: nowrap;
            vertical-align: baseline;
            border-radius: 0.25rem;
        }

        .badge-success {
            color: #fff;
            background-color: #28a745;
        }

        .badge-info {
            color: #fff;
            background-color: #17a2b8;
        }

        .badge-primary {
            color: #fff;
            background-color: #007bff;
        }

        .notes-section {
            padding: 15px;
            border-top: 2px solid #000;
            margin: 0;
            background-color: #fafafa;
        }

        .notes-label {
            font-weight: bold;
            margin-bottom: 5px;
        }

        .preamble-section {
            padding: 20px;
            border-top: 2px solid #000;
            margin: 0;
            background-color: #f9f9f9;
            text-align: right;
            direction: rtl;
        }

        .preamble-section h1,
        .preamble-section h2,
        .preamble-section h3,
        .preamble-section h4 {
            margin: 10px 0;
            font-weight: bold;
        }

        .preamble-section p {
            margin: 8px 0;
            line-height: 1.6;
        }

        .preamble-section ul,
        .preamble-section ol {
            margin: 10px 0;
            padding-right: 20px;
        }

        .footer-section {
            padding: 20px 15px;
            border-top: 2px solid #000;
            margin-top: 0;
            background-color: #f9f9f9;
        }

        .footer-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(120px, 1fr));
            gap: 15px;
            text-align: center;
        }

        .footer-box {
            padding: 10px;
            border: 1px solid #666;
            background-color: white;
            border-radius: 4px;
        }

        .footer-label {
            font-weight: bold;
            font-size: 11px;
            margin-bottom: 10px;
            color: #333;
            border-bottom: 1px solid #ccc;
            padding-bottom: 5px;
        }

        .footer-value {
            font-size: 11px;
            color: #000;
            margin-top: 10px;
            min-height: 40px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .footer-line {
            border-top: 1px solid #666;
            margin: 20px 10px 10px 10px;
            height: 30px;
        }
    </style>
</head>

<body>
    <div class="page-info">{{ __('invoices::invoices.page') }} 1 {{ __('invoices::invoices.of') }} 1

    </div>

    @php
        $taxFieldsEnabled = setting('enable_vat_fields') == '1';
        $vatFieldsEnabled = $taxFieldsEnabled && setting('vat_level') != 'disabled';
        $withholdingTaxFieldsEnabled = $taxFieldsEnabled && setting('withholding_tax_level') != 'disabled';

        $showBatchNumber = collect($invoiceItems ?? [])->contains(fn($i) => !empty($i['batch_number'] ?? null));
        $showExpiryDate = collect($invoiceItems ?? [])->contains(fn($i) => !empty($i['expiry_date'] ?? null));

        // Get template for this invoice
        $template = $operation->invoiceTemplate;

        // Helper function to check if section is enabled
        // If no template, show all sections by default
        $showSection = function ($sectionKey) use ($template) {
            if (!$template) {
                return true; // Show all sections if no template
            }
            return $template->hasSectionEnabled($sectionKey);
        };
    @endphp

    <div class="invoice-container">
        <!-- Header -->
        @if (
            $showSection('company_name') ||
                $showSection('company_logo') ||
                $showSection('invoice_title') ||
                $showSection('national_address') ||
                $showSection('company_tax_number'))
            <div class="header">

                @php
                    $companyName = \Modules\Settings\Models\PublicSetting::where('key', 'campany_name')->value('value');
                    $companyLogo = \Modules\Settings\Models\PublicSetting::where('key', 'company_logo')->value('value');
                    $nationalAddress = \Modules\Settings\Models\PublicSetting::where('key', 'national_address')->value('value');
                    $taxNumber = \Modules\Settings\Models\PublicSetting::where('key', 'tax_number')->value('value');
                @endphp

                @if ($showSection('company_logo') && $companyLogo)
                    <div style="text-align: center; margin-bottom: 10px;">
                        <img src="{{ asset('storage/' . $companyLogo) }}" alt="Company Logo" style="max-height: 80px; max-width: 200px;">
                    </div>
                @endif

                @if ($showSection('company_name'))
                    <div class="company-name">{{ $companyName ?: 'Massar' }}</div>
                @endif

                @if ($showSection('invoice_title'))
                    <div class="invoice-title">{{ $titles[$type] ?? __('invoices::invoices.invoice') }}</div>
                @endif

                @if ($showSection('national_address') || $showSection('company_tax_number'))
                    @if ($nationalAddress || $taxNumber)
                        <div style="font-size: 10px; margin-top: 5px;">
                            @if ($showSection('national_address') && $nationalAddress)
                                <div>{{ __('invoices::invoices.national_address') }}: {{ $nationalAddress }}</div>
                            @endif
                            @if ($showSection('company_tax_number') && $taxNumber)
                                <div>{{ __('invoices::invoices.tax_number') }}: {{ $taxNumber }}</div>
                            @endif
                        </div>
                    @endif
                @endif
            </div>
        @endif

        <!-- Invoice Information -->
        <div class="invoice-info">
            <div class="left-info">
                @if ($showSection('invoice_number'))
                    <div class="info-row">
                        <span class="info-label">{{ __('invoices::invoices.invoice_number') }}
                            {{ $titles[$type] ?? '' }}:</span>
                        <span class="info-value">
                            <span>{{ $pro_id && $pro_id > 0 ? $pro_id : $operation->id ?? __('invoices::invoices.not_specified') }}</span>
                        </span>
                    </div>
                @endif

                @if ($showSection('serial_number') && !empty($serial_number))
                    <div class="info-row">
                        <span class="info-label">{{ __('invoices::invoices.serial_number') }}:</span>
                        <span class="info-value">{{ $serial_number }}</span>
                    </div>
                @endif

                @if ($showSection('invoice_date'))
                    <div class="info-row">
                        <span class="info-label">{{ __('invoices::invoices.invoice_date') }}:</span>
                        <span class="info-value">
                            {{ $pro_date ? \Carbon\Carbon::parse($pro_date)->format('Y-m-d') : __('invoices::invoices.not_specified') }}
                        </span>
                    </div>
                @endif

                @if ($showSection('due_date'))
                    <div class="info-row">
                        <span class="info-label">{{ __('invoices::invoices.due_date') }}:</span>
                        <span class="info-value">
                            {{ $accural_date ? \Carbon\Carbon::parse($accural_date)->format('Y-m-d') : __('invoices::invoices.not_specified') }}
                        </span>
                    </div>
                @endif

                @if ($showSection('employee_name'))
                    <div class="info-row">
                        <span class="info-label">{{ __('invoices::invoices.employee') }}:</span>
                        <span class="info-value">
                            {{ $employee->aname ?? __('invoices::invoices.not_specified') }}
                        </span>
                    </div>
                @endif

                @if ($showSection('delivery_delegate'))
                    <div class="info-row">
                        <span class="info-label">{{ __('invoices::invoices.delivery_delegate') }}:</span>
                        <span class="info-value">
                            {{ $delivery->aname ?? __('invoices::invoices.not_specified') }}
                        </span>
                    </div>
                @endif

                @if ($showSection('branch_name') && !empty($branch?->name))
                    <div class="info-row">
                        <span class="info-label">{{ __('invoices::invoices.branch') }}:</span>
                        <span class="info-value">{{ $branch->name }}</span>
                    </div>
                @endif
            </div>

            <div class="right-info">
                @if ($showSection('customer_name'))
                    <div class="info-row">
                        <span class="info-label">{{ $acc1Role ?? __('invoices::invoices.account') }}:</span>
                        <span class="info-value">
                            <span>{{ $acc1->aname ?? __('invoices::invoices.not_specified') }}</span>
                        </span>
                    </div>
                @endif

                @if ($showSection('customer_address') && !empty($acc1?->address))
                    <div class="info-row">
                        <span class="info-label">{{ __('invoices::invoices.customer_address') }}:</span>
                        <span class="info-value">{{ $acc1->address }}</span>
                    </div>
                @endif

                @if ($showSection('customer_phone') && !empty($acc1?->phone))
                    <div class="info-row">
                        <span class="info-label">{{ __('invoices::invoices.customer_phone') }}:</span>
                        <span class="info-value">{{ $acc1->phone }}</span>
                    </div>
                @endif

                @if ($showSection('customer_tax_number') && !empty($acc1?->tax_number))
                    <div class="info-row">
                        <span class="info-label">{{ __('invoices::invoices.tax_number') }}:</span>
                        <span class="info-value">{{ $acc1->tax_number }}</span>
                    </div>
                @endif

                @if ($showSection('store_name') && !empty($acc2?->aname))
                    <div class="info-row">
                        <span class="info-label">{{ __('invoices::invoices.store') }}:</span>
                        <span class="info-value">{{ $acc2->aname }}</span>
                    </div>
                @endif

                @if ($showSection('cash_box_name') && !empty($cash_box?->aname))
                    <div class="info-row">
                        <span class="info-label">{{ __('invoices::invoices.cash_box') }}:</span>
                        <span class="info-value">{{ $cash_box->aname }}</span>
                    </div>
                @endif

                @if ($showSection('price_list') && !empty($price_list?->name))
                    <div class="info-row">
                        <span class="info-label">{{ __('invoices::invoices.price_list') }}:</span>
                        <span class="info-value">{{ $price_list->name }}</span>
                    </div>
                @endif

                @if ($showSection('currency') && setting('multi_currency_enabled') == '1' && !empty($currency?->name))
                    <div class="info-row">
                        <span class="info-label">{{ __('invoices::invoices.currency') }}:</span>
                        <span class="info-value">{{ $currency->name }}</span>
                    </div>
                @endif

                @if ($showSection('exchange_rate') && setting('multi_currency_enabled') == '1' && !empty($currency_rate))
                    <div class="info-row">
                        <span class="info-label">{{ __('invoices::invoices.exchange_rate') }}:</span>
                        <span class="info-value">{{ number_format((float) ($currency_rate ?? 1), 6) }}</span>
                    </div>
                @endif

                @if ($showSection('paid_amount'))
                    <div class="info-row">
                        <span class="info-label">
                            @if (in_array($type, [10, 12, 14, 16, 18, 21, 22, 26]))
                                {{ __('invoices::invoices.paid_from_customer') }}:
                            @elseif(in_array($type, [11, 13, 15, 17, 20]))
                                {{ __('invoices::invoices.paid_to_supplier') }}:
                            @else
                                {{ __('invoices::invoices.paid') }}:
                            @endif
                        </span>
                        <span class="info-value">
                            <span>{{ number_format($paid_from_client ?? 0, 2) }}
                                {{ __('invoices::invoices.egp') }}</span>
                        </span>
                    </div>
                @endif
            </div>
        </div>

        <!-- Items Table -->
        @if ($showSection('items_table'))
            @php
                $hasCol = function($col) use ($template) {
                    if (!$template) {
                        return in_array($col, ['item_name', 'item_image', 'barcode', 'unit', 'quantity', 'price', 'discount', 'sub_value']);
                    }
                    return $template->hasColumn($col);
                };

                // دالة للحصول على عرض العمود بالبكسل
                $getColWidth = function($col) use ($template) {
                    if (!$template || !isset($template->column_widths[$col])) {
                        // القيم الافتراضية بالبكسل
                        $defaults = [
                            'item_name' => 200,
                            'item_image' => 80,
                            'code' => 100,
                            'barcode' => 100,
                            'unit' => 80,
                            'quantity' => 80,
                            'batch_number' => 100,
                            'expiry_date' => 100,
                            'length' => 60,
                            'width' => 60,
                            'height' => 60,
                            'density' => 60,
                            'price' => 100,
                            'discount' => 80,
                            'discount_percentage' => 80,
                            'discount_value' => 80,
                            'sub_value' => 120,
                        ];
                        return $defaults[$col] ?? 100;
                    }

                    // استخدام الحد الأدنى 5 بكسل كما في الواجهة
                    return max(5, (int) ($template->column_widths[$col] ?? 0));
                };

                // حساب العرض الكلي للجدول
                $totalWidth = 5; // عرض عمود الرقم التسلسلي (تم تقليله بناءً على طلب المستخدم)
                $columns = ['item_name', 'item_image', 'code', 'barcode', 'unit', 'quantity', 'batch_number', 'expiry_date', 'length', 'width', 'height', 'density', 'price', 'discount', 'discount_percentage', 'discount_value', 'sub_value'];
                foreach ($columns as $col) {
                    if ($hasCol($col)) {
                        $totalWidth += $getColWidth($col);
                    }
                }
            @endphp
            @php
                // Helper to render cell with width
                $cellStyle = function($col) use ($getColWidth) {
                    $width = $getColWidth($col);
                    // Use min-width to ensure it doesn't shrink, and width to set the desired size
                    return "width: {$width}px; min-width: {$width}px; max-width: {$width}px;";
                };
            @endphp
            <table class="items-table" style="width: {{ $totalWidth }}px; min-width: 100%;">
                <thead>
                    <tr>
                        <th style="width: 5px; min-width: 5px; padding: 8px 2px;">#</th>
                        @if($hasCol('item_name')) <th style="{{ $cellStyle('item_name') }}">{{ __('invoices::invoices.item_name_col') }}</th> @endif
                        @if($hasCol('item_image')) <th style="{{ $cellStyle('item_image') }}">{{ __('invoices::invoices.image') }}</th> @endif
                        @if($hasCol('code')) <th style="{{ $cellStyle('code') }}">{{ __('invoices::invoices.code_col') }}</th> @endif
                        @if($hasCol('barcode')) <th style="{{ $cellStyle('barcode') }}">{{ __('invoices::invoices.barcode') }}</th> @endif
                        @if($hasCol('unit')) <th style="{{ $cellStyle('unit') }}">{{ __('invoices::invoices.unit_col') }}</th> @endif
                        @if($hasCol('quantity')) <th style="{{ $cellStyle('quantity') }}">{{ __('invoices::invoices.quantity_col') }}</th> @endif
                        @if($hasCol('batch_number')) <th style="{{ $cellStyle('batch_number') }}">{{ __('invoices::invoices.batch_number_col') }}</th> @endif
                        @if($hasCol('expiry_date')) <th style="{{ $cellStyle('expiry_date') }}">{{ __('invoices::invoices.expiry_date_col') }}</th> @endif
                        @if($hasCol('length')) <th style="{{ $cellStyle('length') }}">{{ __('invoices::invoices.length') }}</th> @endif
                        @if($hasCol('width')) <th style="{{ $cellStyle('width') }}">{{ __('invoices::invoices.width') }}</th> @endif
                        @if($hasCol('height')) <th style="{{ $cellStyle('height') }}">{{ __('invoices::invoices.height') }}</th> @endif
                        @if($hasCol('density')) <th style="{{ $cellStyle('density') }}">{{ __('invoices::invoices.density') }}</th> @endif
                        @if($hasCol('price')) <th style="{{ $cellStyle('price') }}">{{ __('invoices::invoices.price_col') }}</th> @endif
                        @if($hasCol('discount')) <th style="{{ $cellStyle('discount') }}">{{ __('invoices::invoices.discount_pct') }}</th> @endif
                        @if($hasCol('discount_percentage')) <th style="{{ $cellStyle('discount_percentage') }}">{{ __('invoices::invoices.discount_pct') }}</th> @endif
                        @if($hasCol('discount_value')) <th style="{{ $cellStyle('discount_value') }}">{{ __('invoices::invoices.discount_value_label') }}</th> @endif
                        @if($hasCol('sub_value')) <th style="{{ $cellStyle('sub_value') }}">{{ __('invoices::invoices.value_col') }}</th> @endif
                    </tr>
                </thead>
                <tbody>
                    @forelse($invoiceItems as $index => $item)
                        <tr>
                            <td style="width: 5px; min-width: 5px; padding: 8px 2px;">{{ $index + 1 }}</td>
                            {{-- Item Name --}}
                            @if($hasCol('item_name'))
                            <td style="text-align: right; {{ $cellStyle('item_name') }}">
                                <strong>{{ $item['item_name'] ?? __('invoices::invoices.not_specified') }}</strong>
                                @if (!empty($item['item_code']) && $hasCol('code') && !$template->hasColumn('code'))
                                    <br><small>{{ __('invoices::invoices.code') }}: {{ $item['item_code'] }}</small>
                                @endif
                            </td>
                            @endif
                            {{-- Item Image --}}
                            @if($hasCol('item_image'))
                                <td style="text-align: center; {{ $cellStyle('item_image') }}">
                                    @php $itemImage = $item['item_image'] ?? null; @endphp
                                    @if ($itemImage && !str_contains($itemImage, 'no-image'))
                                        <img src="{{ $itemImage }}" alt="{{ $item['item_name'] ?? '' }}" class="item-image">
                                    @else
                                        <svg xmlns="http://www.w3.org/2000/svg" width="30" height="30" viewBox="0 0 24 24" fill="none" stroke="#ccc" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" style="display:block;margin:0 auto;">
                                            <rect x="3" y="3" width="18" height="18" rx="2" ry="2"/><circle cx="8.5" cy="8.5" r="1.5"/><polyline points="21 15 16 10 5 21"/>
                                        </svg>
                                    @endif
                                </td>
                            @endif
                            {{-- Code --}}
                            @if($hasCol('code')) <td style="{{ $cellStyle('code') }}">{{ $item['item_code'] ?? '' }}</td> @endif
                            {{-- Barcode --}}
                            @if($hasCol('barcode')) <td style="{{ $cellStyle('barcode') }}"><code>{{ $item['barcode'] ?? '' }}</code></td> @endif
                            {{-- Unit --}}
                            @if($hasCol('unit')) <td style="{{ $cellStyle('unit') }}">{{ $item['unit_name'] ?? '' }}</td> @endif
                            {{-- Quantity --}}
                            @if($hasCol('quantity')) <td style="{{ $cellStyle('quantity') }}">{{ number_format($item['quantity'] ?? 0) }}</td> @endif
                            {{-- Batch --}}
                            @if($hasCol('batch_number')) <td style="{{ $cellStyle('batch_number') }}">{{ $item['batch_number'] ?? '' }}</td> @endif
                            {{-- Expiry --}}
                            @if($hasCol('expiry_date')) <td style="{{ $cellStyle('expiry_date') }}">{{ !empty($item['expiry_date']) ? \Carbon\Carbon::parse($item['expiry_date'])->format('Y-m-d') : '' }}</td> @endif
                            {{-- Spatial Columns --}}
                            @if($hasCol('length')) <td style="{{ $cellStyle('length') }}">{{ $item['length'] ?? '' }}</td> @endif
                            @if($hasCol('width')) <td style="{{ $cellStyle('width') }}">{{ $item['width'] ?? '' }}</td> @endif
                            @if($hasCol('height')) <td style="{{ $cellStyle('height') }}">{{ $item['height'] ?? '' }}</td> @endif
                            @if($hasCol('density')) <td style="{{ $cellStyle('density') }}">{{ $item['density'] ?? '' }}</td> @endif
                            {{-- Price --}}
                            @if($hasCol('price')) <td style="{{ $cellStyle('price') }}"><strong>{{ number_format($item['price'] ?? 0, 2) }}</strong></td> @endif
                            {{-- Discount --}}
                            @if($hasCol('discount')) <td style="{{ $cellStyle('discount') }}">{{ number_format($item['discount'] ?? 0, 2) }}%</td> @endif
                            {{-- Discount Percentage --}}
                            @if($hasCol('discount_percentage')) <td style="{{ $cellStyle('discount_percentage') }}">{{ number_format($item['discount_percentage'] ?? 0, 2) }}%</td> @endif
                            {{-- Discount Value --}}
                            @if($hasCol('discount_value')) <td style="{{ $cellStyle('discount_value') }}">{{ number_format($item['discount_value'] ?? 0, 2) }}</td> @endif
                            {{-- Sub Value --}}
                            @if($hasCol('sub_value')) <td style="{{ $cellStyle('sub_value') }}"><strong>{{ number_format($item['sub_value'] ?? 0, 2) }}</strong></td> @endif
                        </tr>
                    @empty
                        <tr>
                            @php $colCount = collect(['item_image', 'code', 'barcode', 'unit', 'quantity', 'batch_number', 'expiry_date', 'length', 'width', 'height', 'density', 'price', 'discount', 'discount_percentage', 'discount_value', 'sub_value'])->filter(fn($c) => $hasCol($c))->count() + 2; @endphp
                            <td colspan="{{ $colCount }}" style="text-align: center;">
                                {{ __('invoices::invoices.no_items_added') }}
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>

            <!-- Totals Section -->
            {{-- @if ($showSection('subtotal') || $showSection('discount') || $showSection('tax') || $showSection('total') || $showSection('paid_amount') || $showSection('remaining_amount')) --}}
            <div class="totals-section">
                <div class="totals-grid">
                    <div class="left-totals">
                        @if ($showSection('subtotal'))
                            <div class="total-row">
                                <span class="total-label">{{ __('invoices::invoices.subtotal_label') }}</span>
                                <span class="total-value">{{ number_format($subtotal ?? 0, 2) }}
                                    {{ __('invoices::invoices.egp') }}</span>
                            </div>
                        @endif

                        @if ($showSection('discount') && (($discount_percentage ?? 0) > 0 || ($discount_value ?? 0) > 0))
                            <div class="total-row">
                                <span class="total-label">
                                    {{ __('invoices::invoices.discount_col') }}
                                    @if (($discount_percentage ?? 0) > 0)
                                        ({{ number_format($discount_percentage, 2) }}%)
                                    @endif:
                                </span>
                                <span class="total-value">
                                    - {{ number_format($discount_value ?? 0, 2) }} {{ __('invoices::invoices.egp') }}
                                </span>
                            </div>
                        @endif

                        @if ($showSection('additional') && (($additional_percentage ?? 0) > 0 || ($additional_value ?? 0) > 0))
                            <div class="total-row">
                                <span class="total-label">
                                    {{ __('invoices::invoices.additional') }}
                                    @if (($additional_percentage ?? 0) > 0)
                                        ({{ number_format($additional_percentage, 2) }}%)
                                    @endif:
                                </span>
                                <span class="total-value">
                                    + {{ number_format($additional_value ?? 0, 2) }}
                                    {{ __('invoices::invoices.egp') }}
                                </span>
                            </div>
                        @endif

                        @if ($showSection('vat') && $vatFieldsEnabled && ($vat_percentage ?? 0) > 0)
                            <div class="total-row">
                                <span class="total-label">{{ __('invoices::invoices.vat') }}
                                    ({{ number_format($vat_percentage, 2) }}%):</span>
                                <span class="total-value">
                                    + {{ number_format($vat_value ?? 0, 2) }} {{ __('invoices::invoices.egp') }}
                                </span>
                            </div>
                        @endif

                        @if ($showSection('withholding_tax') && $withholdingTaxFieldsEnabled && ($withholding_tax_percentage ?? 0) > 0)
                            <div class="total-row">
                                <span class="total-label">{{ __('invoices::invoices.withholding_tax') }}
                                    ({{ number_format($withholding_tax_percentage, 2) }}%):</span>
                                <span class="total-value">
                                    - {{ number_format($withholding_tax_value ?? 0, 2) }}
                                    {{ __('invoices::invoices.egp') }}
                                </span>
                            </div>
                        @endif
                    </div>

                    <div class="right-totals">
                        @if ($showSection('total'))
                            <div class="total-row net-total">
                                <span class="total-label">{{ __('invoices::invoices.final_total') }}:</span>
                                <span class="total-value">{{ number_format($total ?? 0, 2) }}
                                    {{ __('invoices::invoices.egp') }}</span>
                            </div>
                        @endif

                        @if ($showSection('paid_amount') && ($paid_from_client ?? 0) > 0)
                            <div class="total-row">
                                <span class="total-label">{{ __('invoices::invoices.paid') }}:</span>
                                <span class="total-value">{{ number_format($paid_from_client ?? 0, 2) }}
                                    {{ __('invoices::invoices.egp') }}</span>
                            </div>
                        @endif

                        @if ($showSection('remaining_amount') && ($paid_from_client ?? 0) > 0)
                            <div class="total-row">
                                <span class="total-label">{{ __('invoices::invoices.remaining') }}:</span>
                                <span class="total-value">
                                    {{ number_format($remaining ?? 0, 2) }}
                                    {{ __('invoices::invoices.egp') }}
                                </span>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
            {{-- @endif --}}

            <!-- Notes Section -->
            @if ($showSection('notes') && isset($notes) && $notes)
                <div class="notes-section">
                    <div class="notes-label">{{ __('invoices::invoices.notes') }}:</div>
                    <div>{{ $notes }}</div>
                </div>
            @endif

            @if ($showSection('payment_notes') && isset($payment_notes) && $payment_notes)
                <div class="notes-section">
                    <div class="notes-label">{{ __('invoices::invoices.payment_notes') }}:</div>
                    <div>{{ $payment_notes }}</div>
                </div>
            @endif

            <!-- Preamble Section (HTML Content) -->
            @if ($showSection('preamble') && $template && $template->preamble_text)
                <div class="preamble-section">
                    {!! $template->preamble_text !!}
                </div>
            @endif

            <!-- Footer Section (Customer, Date/Time, Signatures) -->
            {{-- @if ($showSection('signature_customer') || $showSection('signature_date') || $showSection('signature_management') || $showSection('signature_accountant') || $showSection('signature_receiver')) --}}
            <div class="footer-section">
                <div class="footer-grid">
                    @if ($showSection('signature_customer'))
                        <div  >
                            <div class="footer-label">{{ __('invoices::invoices.customer') }}</div>
                            <div class="footer-value">{{ $acc1->aname ?? __('invoices::invoices.not_specified') }}
                            </div>
                        </div>
                    @endif

                    @if ($showSection('signature_date'))
                        <div>
                            <div class="footer-label">{{ __('invoices::invoices.date') }}</div>
                            <div class="footer-value">
                                {{ now()->format('Y-m-d') }}<br>
                                {{ now()->format('H:i:s A') }}
                            </div>
                        </div>
                    @endif

                    @if ($showSection('signature_management'))
                        <div  >
                            <div class="footer-label">{{ __('invoices::invoices.management') }}</div>
                            <div class="footer-line"></div>
                        </div>
                    @endif

                    @if ($showSection('signature_accountant'))
                        <div  >
                            <div class="footer-label">{{ __('invoices::invoices.accountant') }}</div>
                            <div class="footer-line"></div>
                        </div>
                    @endif

                    @if ($showSection('signature_receiver'))
                        <div class="footer">
                            <div class="footer-label">{{ __('invoices::invoices.receiver') }}</div>
                            <div class="footer-line"></div>
                        </div>
                    @endif
                </div>
            </div>
        @endif
    </div>


    {{-- @if ($showSection('signature_management'))
        <div class="signature-box">
            <div class="signature-label">{{ __('invoices::templates.signature_management') }}</div>
            <div class="signature-line"></div>
            <div class="signature-name">{{ __('invoices::invoices.management') }}</div>
        </div>
    @endif

    @if ($showSection('signature_accountant'))
        <div class="signature-box">
            <div class="signature-label">{{ __('invoices::templates.signature_accountant') }}</div>
            <div class="signature-line"></div>
            <div class="signature-name">{{ __('invoices::invoices.accountant') }}</div>
        </div>
    @endif

    @if ($showSection('signature_receiver'))
        <div class="signature-box">
            <div class="signature-label">{{ __('invoices::templates.signature_receiver') }}</div>
            <div class="signature-line"></div>
            <div class="signature-name">{{ __('invoices::invoices.receiver') }}</div>
        </div>
    @endif --}}
    <script>
        // طباعة تلقائية عند تحميل الصفحة
        window.onload = function() {
            window.print();
        };
    </script>
</body>

</html>
