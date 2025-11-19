<?php

namespace Modules\Shipping\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ShippingCompanyRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $companyId = $this->route('company') ? $this->route('company')->id : null;

        // تحديد إذا كانت العملية تعديل أو إضافة
        $isUpdate = $this->isMethod('put') || $this->isMethod('patch');

        return [
            'name'      => 'required|string|max:255',
            'email'     => [
                'required',
                'email',
                Rule::unique('shipping_companies', 'email')->ignore($companyId),
            ],
            'phone'     => 'required|string|max:20',
            'address'   => 'required|string',
            'base_rate' => 'required|numeric|min:0',
            'is_active' => 'nullable|boolean',
            // جعل branch_id مطلوب فقط عند الإنشاء
            'branch_id' => $isUpdate ? 'nullable|exists:branches,id' : 'required|exists:branches,id',
        ];
    }

    public function messages(): array
    {
        return [
            'name.required'      => __('Company name is required.'),
            'name.max'           => __('Company name must not exceed 255 characters.'),

            'email.required'     => __('Email is required.'),
            'email.email'        => __('Email must be a valid email address.'),
            'email.unique'       => __('Email already exists.'),

            'phone.required'     => __('Phone number is required.'),
            'phone.max'          => __('Phone number must not exceed 20 characters.'),

            'address.required'   => __('Company address is required.'),

            'base_rate.required' => __('Base delivery rate is required.'),
            'base_rate.numeric'  => __('Base delivery rate must be a number.'),
            'base_rate.min'      => __('Base delivery rate must be greater than or equal to 0.'),

            'is_active.boolean'  => __('Company status must be true or false.'),

            'branch_id.required' => __('Branch is required.'),
            'branch_id.exists'   => __('Selected branch is invalid.'),
        ];
    }

    /**
     * Prepare data for validation
     */
    protected function prepareForValidation(): void
    {
        // تحويل checkbox إلى boolean
        if ($this->has('is_active')) {
            $this->merge([
                'is_active' => $this->boolean('is_active'),
            ]);
        } else {
            $this->merge([
                'is_active' => false,
            ]);
        }
    }
}
