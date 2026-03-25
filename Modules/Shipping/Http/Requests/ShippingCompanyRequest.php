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
            'name.required'      => __('shipping::shipping.validation.company.name_required'),
            'name.max'           => __('shipping::shipping.validation.company.name_max'),

            'email.required'     => __('shipping::shipping.validation.company.email_required'),
            'email.email'        => __('shipping::shipping.validation.company.email_email'),
            'email.unique'       => __('shipping::shipping.validation.company.email_unique'),

            'phone.required'     => __('shipping::shipping.validation.company.phone_required'),
            'phone.max'          => __('shipping::shipping.validation.company.phone_max'),

            'address.required'   => __('shipping::shipping.validation.company.address_required'),

            'base_rate.required' => __('shipping::shipping.validation.company.base_rate_required'),
            'base_rate.numeric'  => __('shipping::shipping.validation.company.base_rate_numeric'),
            'base_rate.min'      => __('shipping::shipping.validation.company.base_rate_min'),

            'is_active.boolean'  => __('shipping::shipping.validation.company.status_boolean'),

            'branch_id.required' => __('shipping::shipping.validation.branch.required'),
            'branch_id.exists'   => __('shipping::shipping.validation.branch.exists'),
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
