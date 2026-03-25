<?php

declare(strict_types=1);

namespace Modules\Fleet\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class VehicleTypeRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $isUpdate = $this->isMethod('put') || $this->isMethod('patch');
        $vehicleType = $this->route('vehicle_type');

        // اجيب الـ ID بس
        $vehicleTypeId = $vehicleType?->id;

        return [
            'name' => [
                'required',
                'string',
                'max:255',
                $isUpdate && $vehicleTypeId
                    ? Rule::unique('vehicle_types', 'name')->ignore($vehicleTypeId)
                    : 'unique:vehicle_types,name'
            ],
            'description' => 'nullable|string',
            'is_active' => 'nullable|boolean',
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => __('fleet::fleet.validation.vehicle_type_name_required'),
            'name.unique' => __('fleet::fleet.validation.vehicle_type_name_unique'),
            'is_active.boolean' => __('fleet::fleet.validation.vehicle_type_is_active_boolean'),
        ];
    }
}
