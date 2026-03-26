<?php

namespace Modules\Settings\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CurrencyExchangeRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'operation_type' => 'required|in:80,81', // 80 = buy, 81 = sell
            'pro_date' => 'required|date',
            'pro_num' => 'nullable|string',
            'acc1' => 'required|integer|exists:acc_head,id',
            'acc2' => 'required|integer|exists:acc_head,id',
            'currency_id' => 'required|integer|exists:currencies,id',
            'currency_rate' => 'required|numeric|min:0',
            'pro_value' => 'required|numeric|min:0',
            'details' => 'nullable|string',
            'cost_center' => 'nullable|integer|exists:cost_centers,id',
            'branch_id' => 'required|exists:branches,id',
        ];
    }

    /**
     * Get custom attributes for validator errors.
     */
    public function attributes(): array
    {
        return [
            'operation_type' => __('Operation Type'),
            'pro_date'       => __('Date'),
            'pro_num'        => __('Receipt Number'),
            'acc1'           => __('To Fund (Debit)'),
            'acc2'           => __('From Fund (Credit)'),
            'currency_id'    => __('Target Currency'),
            'currency_rate'  => __('Exchange Rate'),
            'pro_value'      => __('Amount (Foreign Currency)'),
            'details'        => __('Description'),
            'cost_center'    => __('Cost Center'),
            'branch_id'      => __('Branch'), // تأكد من إضافة Branch لملف الترجمة إذا لم تكن موجودة
        ];
    }
}
