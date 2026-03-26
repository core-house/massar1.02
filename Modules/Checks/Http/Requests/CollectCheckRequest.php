<?php

namespace Modules\Checks\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CollectCheckRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        $check = $this->route('check');
        
        return $check && $this->user()->can('clear', $check);
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'account_type' => ['required', 'in:bank,cash'],
            'account_id' => ['required', 'integer', 'exists:acc_head,id'],
            'collection_date' => ['required', 'date'],
            'branch_id' => ['required', 'integer', 'exists:branches,id'],
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'account_type.required' => 'نوع الحساب مطلوب',
            'account_type.in' => 'نوع الحساب يجب أن يكون بنك أو صندوق',
            'account_id.required' => 'الحساب مطلوب',
            'account_id.exists' => 'الحساب غير موجود',
            'collection_date.required' => 'تاريخ التحصيل مطلوب',
            'collection_date.date' => 'تاريخ التحصيل غير صحيح',
            'branch_id.required' => 'الفرع مطلوب',
            'branch_id.exists' => 'الفرع غير موجود',
        ];
    }
}

