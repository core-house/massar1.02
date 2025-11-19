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
            'name.required' => __('Driver name is required.'),
            'name.string'   => __('Driver name must be a string.'),
            'name.max'      => __('Driver name must not exceed 255 characters.'),

            'phone.required' => __('Phone number is required.'),
            'phone.string'   => __('Phone number must be a string.'),
            'phone.max'      => __('Phone number must not exceed 20 characters.'),

            'vehicle_type.required' => __('Vehicle type is required.'),
            'vehicle_type.string'   => __('Vehicle type must be a string.'),
            'vehicle_type.max'      => __('Vehicle type must not exceed 100 characters.'),

            'is_available.boolean'  => __('Driver availability must be true or false.'),

            'branch_id.required' => __('Branch is required.'),
            'branch_id.exists' => __('Selected branch is invalid.'),
        ];
    }
}
