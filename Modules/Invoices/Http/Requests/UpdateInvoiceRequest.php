<?php

declare(strict_types=1);

namespace Modules\Invoices\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Form Request for updating invoices
 */
class UpdateInvoiceRequest extends FormRequest
{
    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        // Convert empty strings to null for numeric fields
        $items = $this->input('items', []);
        
        foreach ($items as $index => $item) {
            // Convert empty numeric fields to null
            $numericFields = ['discount', 'discount_percentage', 'discount_value', 'additional', 'length', 'width', 'height', 'density'];
            
            foreach ($numericFields as $field) {
                if (isset($item[$field]) && $item[$field] === '') {
                    $items[$index][$field] = null;
                }
            }
        }
        
        $this->merge(['items' => $items]);
        
        // Convert empty numeric fields at invoice level to null
        $invoiceNumericFields = [
            'discount_percentage', 'discount_value', 
            'additional_percentage', 'additional_value',
            'vat_percentage', 'vat_value',
            'withholding_tax_percentage', 'withholding_tax_value',
            'received_from_client', 'remaining', 'exchange_rate'
        ];
        
        $mergeData = [];
        foreach ($invoiceNumericFields as $field) {
            if ($this->input($field) === '') {
                $mergeData[$field] = null;
            }
        }
        
        if (!empty($mergeData)) {
            $this->merge($mergeData);
        }
    }

    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return auth()->check();
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'type' => ['required', 'integer'],
            'branch_id' => ['required', 'integer', 'exists:branches,id'],
            'acc1_id' => ['required', 'integer', 'exists:acc_head,id'],
            'acc2_id' => ['required', 'integer', 'exists:acc_head,id'],
            'pro_date' => ['required', 'date'],
            'notes' => ['nullable', 'string', 'max:1000'],
            'currency_id' => ['nullable', 'integer', 'exists:currencies,id'],
            'exchange_rate' => ['nullable', 'numeric', 'min:0'],
            
            // Calculations
            'subtotal' => ['required', 'numeric', 'min:0'],
            'discount_percentage' => ['nullable', 'numeric', 'min:0', 'max:100'],
            'discount_value' => ['nullable', 'numeric', 'min:0'],
            'additional_percentage' => ['nullable', 'numeric', 'min:0', 'max:100'],
            'additional_value' => ['nullable', 'numeric', 'min:0'],
            'vat_percentage' => ['nullable', 'numeric', 'min:0', 'max:100'],
            'vat_value' => ['nullable', 'numeric', 'min:0'],
            'withholding_tax_percentage' => ['nullable', 'numeric', 'min:0', 'max:100'],
            'withholding_tax_value' => ['nullable', 'numeric', 'min:0'],
            'total' => ['required', 'numeric', 'min:0'],
            'received_from_client' => ['nullable', 'numeric', 'min:0'],
            'remaining' => ['nullable', 'numeric'],
            
            // Items
            'items' => ['required', 'array', 'min:1'],
            'items.*.item_id' => ['required', 'integer', 'exists:items,id'],
            'items.*.unit_id' => ['required', 'integer', 'exists:units,id'],
            'items.*.quantity' => ['required', 'numeric', 'min:0.001'],
            'items.*.price' => ['required', 'numeric', 'min:0'],
            'items.*.discount' => ['nullable', 'numeric', 'min:0'],
            'items.*.additional' => ['nullable', 'numeric', 'min:0'],
            'items.*.sub_value' => ['required', 'numeric'],
            'items.*.batch_number' => ['nullable', 'string', 'max:100'],
            'items.*.expiry_date' => ['nullable', 'date'],
            'items.*.notes' => ['nullable', 'string', 'max:500'],
        ];
    }

    /**
     * Get custom error messages
     */
    public function messages(): array
    {
        return [
            'acc1_id.required' => __('invoices.acc1_required'),
            'acc2_id.required' => __('invoices.acc2_required'),
            'pro_date.required' => __('invoices.date_required'),
            'items.required' => __('invoices.items_required'),
            'items.min' => __('invoices.items_min'),
            'items.*.item_id.required' => __('invoices.item_required'),
            'items.*.unit_id.required' => __('invoices.unit_required'),
            'items.*.quantity.required' => __('invoices.quantity_required'),
            'items.*.quantity.min' => __('invoices.quantity_min'),
            'items.*.price.required' => __('invoices.price_required'),
            'items.*.price.min' => __('invoices.price_min'),
        ];
    }
}
