<?php

namespace Modules\Invoices\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class InvoiceTemplateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        // تحويل sort_order إلى integer إذا كان موجوداً
        if ($this->has('sort_order') && $this->sort_order !== null && $this->sort_order !== '') {
            $this->merge([
                'sort_order' => (int) $this->sort_order,
            ]);
        }

        // تحويل is_active إلى boolean
        $this->merge([
            'is_active' => $this->has('is_active') && $this->is_active == '1',
        ]);

        // تنظيف column_widths - إزالة القيم الفارغة وتحويلها لـ integer
        if ($this->has('column_widths')) {

            $columnWidths = [];
            foreach ($this->input('column_widths', []) as $key => $value) {
                if ($value !== null && $value !== '') {
                    $columnWidths[$key] = (int) $value;
                }
            }
            $this->merge(['column_widths' => $columnWidths]);
        }

        // تحويل printable_sections إلى boolean
        // نحتاج نتأكد إن كل الأقسام موجودة (حتى اللي مش checked)
        $allSections = array_keys(\Modules\Invoices\Models\InvoiceTemplate::availableSectionsFlat());
        $submittedSections = $this->input('printable_sections', []);

        $sections = [];
        foreach ($allSections as $sectionKey) {
            // إذا القسم موجود في الـ request ومحدد، يبقى true
            // إذا مش موجود أو قيمته "0"، يبقى false
            $sections[$sectionKey] = isset($submittedSections[$sectionKey]) &&
                filter_var($submittedSections[$sectionKey], FILTER_VALIDATE_BOOLEAN);
        }

        $this->merge([
            'printable_sections' => $sections,
        ]);
    }

    public function rules()
    {
        $templateId = $this->route('template')?->id ?? $this->route('invoice_template')?->id;

        return [
            'name' => 'required|string|max:255',
            'code' => [
                'required',
                'string',
                'max:255',
                Rule::unique('invoice_templates', 'code')->ignore($templateId),
            ],
            'description' => 'nullable|string',
            'visible_columns' => 'required|array|min:1',
            'visible_columns.*' => 'string',
            'column_widths' => 'nullable|array',
            'column_widths.*' => 'nullable|integer|min:5|max:500',
            'column_order' => 'nullable|array',
            'column_order.*' => 'nullable|string',
            'printable_sections' => 'nullable|array',
            'preamble_text' => 'nullable|string',
            'invoice_types' => 'required|array|min:1',
            'invoice_types.*' => 'integer|in:10,11,12,13,14,15,16,17,18,19,20,21,22,24,25',
            'is_default' => 'nullable|array',
            'is_default.*' => 'integer',
            'sort_order' => 'nullable|integer',
            'is_active' => 'nullable|boolean',
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => __('invoices::invoices.template_name_required'),
            'code.required' => __('invoices::invoices.template_code_required'),
            'code.unique' => __('invoices::invoices.template_code_unique'),
            'visible_columns.required' => __('invoices::invoices.visible_columns_required'),
            'invoice_types.required' => __('invoices::invoices.invoice_types_required'),
            'sort_order.integer' => __('invoices::invoices.sort_order_integer'),
            'column_widths.*.min' => 'عرض العمود يجب أن يكون 5 بكسل على الأقل',
            'column_widths.*.max' => 'عرض العمود يجب أن يكون 500 بكسل كحد أقصى',
            'column_widths.*.integer' => 'عرض العمود يجب أن يكون رقماً صحيحاً',
        ];
    }

    /**
     * Handle a failed validation attempt.
     */
    protected function failedValidation(\Illuminate\Contracts\Validation\Validator $validator)
    {
        parent::failedValidation($validator);
    }
}
