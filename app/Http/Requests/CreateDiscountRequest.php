<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CreateDiscountRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $rules = [
            'pro_id' => 'required|integer',
            'type' => 'required|in:30,31',
            'pro_date' => 'required|date',
            'info' => 'nullable|string',
            'pro_value' => 'required|numeric|min:0.01',
            'branch_id' => 'nullable|exists:branches,id',
        ];

        if ($this->input('type') == 30) {
            $rules['acc1'] = 'required|exists:acc_head,id';
            $rules['acc2'] = 'nullable|exists:acc_head,id';
        } elseif ($this->input('type') == 31) {
            $rules['acc1'] = 'nullable|exists:acc_head,id';
            $rules['acc2'] = 'required|exists:acc_head,id';
        }

        return $rules;
    }


    public function messages(): array
    {
        return [
            'type.required' => __('invoices::invoices.type_required'),
            'type.in' => __('invoices::invoices.type_in'),

            'acc1.required' => __('invoices::invoices.acc1_required'),
            'acc1.exists' => __('invoices::invoices.acc1_exists'),

            'acc2.required' => __('invoices::invoices.acc2_required'),
            'acc2.exists' => __('invoices::invoices.acc2_exists'),

            'pro_date.required' => __('invoices::invoices.pro_date_required'),
            'pro_date.date' => __('invoices::invoices.pro_date_date'),

            'info.string' => __('invoices::invoices.info_string'),

            'pro_value.required' => __('invoices::invoices.pro_value_required'),
            'pro_value.numeric' => __('invoices::invoices.pro_value_numeric'),
            'pro_value.min' => __('invoices::invoices.pro_value_min'),

            'branch_id.exists' => __('invoices::invoices.branch_id_exists'),
            'pro_id.required' => __('invoices::invoices.pro_id_required'),
            'pro_id.integer' => __('invoices::invoices.pro_id_integer'),
        ];
    }
}
