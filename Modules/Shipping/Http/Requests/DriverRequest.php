<?php

namespace Modules\Shipping\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class DriverRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $isUpdate = $this->isMethod('put') || $this->isMethod('patch');

        return [
            'name' => 'required|string|max:255',
            'phone' => 'required|string|max:20',
            'vehicle_type' => 'required|string|max:100',
            'is_available' => 'boolean',
            'branch_id' => $isUpdate ? 'nullable|exists:branches,id' : 'required|exists:branches,id',
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => __('shipping::shipping.validation.driver_validation.name_required'),
            'name.string'   => __('shipping::shipping.validation.customer_name.string'),
            'name.max'      => __('shipping::shipping.validation.driver_validation.name_max'),

            'phone.required' => __('shipping::shipping.validation.driver_validation.phone_required'),
            'phone.string'   => __('shipping::shipping.validation.phone_number.string'),
            'phone.max'      => __('shipping::shipping.validation.driver_validation.phone_max'),

            'vehicle_type.required' => __('shipping::shipping.validation.driver_validation.vehicle_type_required'),
            'vehicle_type.string'   => __('shipping::shipping.validation.vehicle_type.string'),
            'vehicle_type.max'      => __('shipping::shipping.validation.driver_validation.vehicle_type_max'),

            'is_available.boolean'  => __('shipping::shipping.validation.company.status_boolean'),

            'branch_id.required' => __('shipping::shipping.validation.branch.required'),
            'branch_id.exists' => __('shipping::shipping.validation.branch.exists'),
        ];
    }
}
