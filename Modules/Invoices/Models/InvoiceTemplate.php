<?php

namespace Modules\Invoices\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class InvoiceTemplate extends Model
{
    protected $fillable = [
        'name',
        'code',
        'description',
        'visible_columns',
        'sort_order',
        'is_active',
        'column_widths',
        'column_order',
        'printable_sections',
        'preamble_text',
    ];

    protected $casts = [
        'visible_columns' => 'array',
        'is_active' => 'boolean',
        'column_widths' => 'array',
        'column_order' => 'array',
        'printable_sections' => 'array',
    ];

    public static function availableColumns(): array
    {
        return [
            'item_name'          => __('invoices::invoices.item_name'),
            'item_image'         => __('invoices::invoices.image'),
            'code'               => __('invoices::invoices.code'),
            'unit'               => __('invoices::invoices.unit'),
            'quantity'           => __('invoices::invoices.quantity'),
            'batch_number'       => __('invoices::invoices.batch_number'),
            'expiry_date'        => __('invoices::invoices.expiry_date'),
            'length'             => __('invoices::invoices.length'),
            'width'              => __('invoices::invoices.width'),
            'height'             => __('invoices::invoices.height'),
            'density'            => __('invoices::invoices.density'),
            'price'              => __('invoices::invoices.price'),
            'barcode'            => __('invoices::invoices.barcode'),
            'discount'           => __('invoices::invoices.discount'),
            'discount_percentage'=> __('invoices::invoices.discount_percentage'),
            'discount_value'     => __('invoices::invoices.discount_value'),
            'sub_value'          => __('invoices::invoices.value'),
        ];
    }

    /**
     * Get all available printable sections for invoice templates
     * Reads from settings first, falls back to hardcoded values
     */
    public static function availableSections(): array
    {
        // Try to get sections from settings first
        $settingSections = \Modules\Settings\Models\PublicSetting::where('key', 'invoice_available_print_sections')->value('value');
        
        if ($settingSections) {
            $decoded = json_decode($settingSections, true);
            if (is_array($decoded) && !empty($decoded)) {
                return $decoded;
            }
        }

        // Fallback to hardcoded sections if setting not found
        return [
            'header' => [
                'company_logo' => 'شعار الشركة',
                'company_name' => 'اسم الشركة',
                'invoice_title' => 'عنوان الفاتورة',
                'national_address' => 'العنوان الوطني',
                'company_tax_number' => 'الرقم الضريبي للشركة',
            ],
            'parties' => [
                'customer_name' => 'اسم العميل/المورد',
                'customer_address' => 'عنوان العميل/المورد',
                'customer_phone' => 'هاتف العميل/المورد',
                'customer_tax_number' => 'الرقم الضريبي للعميل/المورد',
            ],
            'invoice_details' => [
                'invoice_number' => 'رقم الفاتورة',
                'serial_number' => 'الرقم التسلسلي',
                'invoice_date' => 'تاريخ الفاتورة',
                'due_date' => 'تاريخ الاستحقاق',
                'employee_name' => 'اسم الموظف',
                'delivery_delegate' => 'مندوب التوصيل',
                'branch_name' => 'اسم الفرع',
                'store_name' => 'اسم المخزن',
                'cash_box_name' => 'اسم الصندوق',
                'price_list' => 'فئة السعر',
                'currency' => 'العملة',
                'exchange_rate' => 'سعر الصرف',
            ],
            'content' => [
                'items_table' => 'جدول الأصناف',
                'items_serial_numbers' => 'الأرقام التسلسلية للأصناف',
            ],
            'totals' => [
                'subtotal' => 'المجموع الفرعي',
                'discount' => 'الخصم',
                'additional' => 'الإضافي',
                'vat' => 'ضريبة القيمة المضافة',
                'withholding_tax' => 'الخصم من المنبع',
                'total' => 'الإجمالي النهائي',
                'paid_amount' => 'المبلغ المدفوع',
                'remaining_amount' => 'المبلغ المتبقي',
            ],
            'footer' => [
                'preamble' => 'ديباجة الفاتورة',
                'terms_conditions' => 'الشروط والأحكام',
                'notes' => 'ملاحظات',
                'payment_notes' => 'ملاحظات الدفع',
                'signature_customer' => 'توقيع العميل/المورد',
                'signature_date' => 'التاريخ والوقت',
                'signature_management' => 'توقيع الإدارة',
                'signature_accountant' => 'توقيع المحاسب',
                'signature_receiver' => 'توقيع المستلم',
                'qr_code' => 'رمز QR',
                'barcode' => 'الباركود',
            ],
        ];
    }

    /**
     * Get flat list of all sections (without grouping)
     */
    public static function availableSectionsFlat(): array
    {
        $sections = [];
        foreach (self::availableSections() as $group => $items) {
            foreach ($items as $key => $label) {
                $sections[$key] = $label;
            }
        }

        return $sections;
    }

    /**
     * Check if a specific section is enabled for printing
     */
    public function hasSectionEnabled(string $sectionKey): bool
    {
        $sections = $this->printable_sections ?? [];

        return isset($sections[$sectionKey]) && $sections[$sectionKey] === true;
    }

    /**
     * Get default printable sections for new templates
     */
    public static function defaultPrintableSections(): array
    {
        return [
            // Header
            'company_logo' => false,
            'company_name' => true,
            'invoice_title' => true,
            'national_address' => true,
            'company_tax_number' => true,

            // Parties
            'customer_name' => true,
            'customer_address' => false,
            'customer_phone' => false,
            'customer_tax_number' => false,

            // Invoice Details
            'invoice_number' => true,
            'serial_number' => false,
            'invoice_date' => true,
            'due_date' => false,
            'employee_name' => false,
            'delivery_delegate' => false,
            'branch_name' => true,
            'store_name' => false,
            'cash_box_name' => false,
            'price_list' => false,
            'currency' => false,
            'exchange_rate' => false,

            // Content
            'items_table' => true,
            'items_serial_numbers' => false,

            // Totals
            'subtotal' => true,
            'discount' => true,
            'additional' => false,
            'vat' => true,
            'withholding_tax' => false,
            'total' => true,
            'paid_amount' => false,
            'remaining_amount' => false,

            // Footer
            'terms_conditions' => false,
            'notes' => true,
            'payment_notes' => false,
            'preamble' => false,
            'signature_customer' => false,
            'signature_date' => false,
            'signature_management' => false,
            'signature_accountant' => false,
            'signature_receiver' => false,
            'qr_code' => false,
            'barcode' => false,
        ];
    }

    public static function invoiceTypeNames(): array
    {
        return [
            10 => 'فاتورة مبيعات',
            11 => 'فاتورة مشتريات',
            12 => 'مردود مبيعات',
            13 => 'مردود مشتريات',
            14 => 'أمر بيع',
            15 => 'أمر شراء',
            16 => 'عرض سعر لعميل',
            17 => 'عرض سعر من مورد',
            18 => 'فاتورة توالف',
            19 => 'أمر صرف',
            20 => 'أمر إضافة',
            21 => 'تحويل من مخزن لمخزن',
            22 => 'أمر حجز',
            24 => 'فاتورة خدمة',
            25 => 'طلب احتياج',
        ];
    }

    public static function getInvoiceTypeName(int $typeId): string
    {
        return self::invoiceTypeNames()[$typeId] ?? 'نوع غير معروف';
    }

    public function invoiceTypes(): HasMany
    {
        return $this->hasMany(InvoiceTypeTemplate::class, 'template_id');
    }

    public static function getDefaultForType(int $invoiceType): ?self
    {
        return self::whereHas('invoiceTypes', function ($query) use ($invoiceType) {
            $query->where('invoice_type', $invoiceType)
                ->where('is_default', true);
        })
            ->where('is_active', true)
            ->first();
    }

    public static function getForType(int $invoiceType)
    {
        return self::whereHas('invoiceTypes', function ($query) use ($invoiceType) {
            $query->where('invoice_type', $invoiceType);
        })
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->get();
    }

    public function hasColumn(string $columnKey): bool
    {
        return in_array($columnKey, $this->visible_columns ?? []);
    }

    public function getColumnWidth(string $columnKey): int
    {
        return $this->column_widths[$columnKey] ?? 10;
    }

    public function getOrderedColumns(): array
    {
        // Define the fixed order that matches the blade file structure
        $canonicalOrder = [
            'item_name',
            'code',
            'unit',
            'quantity',
            'batch_number',
            'expiry_date',
            'length',
            'width',
            'height',
            'density',
            'price',
            'discount',
            'sub_value',
        ];

        $visible = $this->visible_columns ?? [];

        // Return visible columns sorted by their position in canonicalOrder
        // Columns not in canonicalOrder will be appended at the end
        return collect($visible)->sortBy(function ($col) use ($canonicalOrder) {
            $index = array_search($col, $canonicalOrder);

            return $index === false ? 999 : $index;
        })->values()->toArray();
    }
}
