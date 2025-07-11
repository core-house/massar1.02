<?php

namespace Modules\CRM\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;



class LeadStatusRequest extends FormRequest
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
        $id = $this->route('lead_status');

        return [
            'name'  => ['required', 'string', 'max:255'],
            'order_column' => [
                'required',
                'integer',
                'min:1',
                Rule::unique('lead_statuses', 'order_column')->ignore($id),
            ],
            'color' => ['required', 'regex:/^#[0-9A-Fa-f]{6}$/'],
        ];
    }

    /**
     * Custom messages for validation errors.
     */
    public function messages(): array
    {
        return [
            'name.required'  => 'حقل العنوان مطلوب',
            'name.string'    => 'العنوان يجب أن يكون نصًا',
            'name.max'       => 'العنوان يجب ألا يزيد عن 255 حرفًا',
            'color.required' => 'حقل اللون مطلوب',
            'color.regex'    => 'اللون يجب أن يكون كود لوني صالح مثل #FFFFFF',
            'order_column.required' => 'حقل الترتيب مطلوب',
            'order_column.integer' => 'يجب أن يكون الترتيب رقماً صحيحاً',
            'order_column.min' => 'يجب أن يكون الترتيب أكبر من الصفر',
            'order_column.unique' => 'هذا الترتيب موجود بالفعل، برجاء اختيار ترتيب مختلف',

        ];
    }
}
