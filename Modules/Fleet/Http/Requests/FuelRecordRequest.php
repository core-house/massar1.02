<?php

declare(strict_types=1);

namespace Modules\Fleet\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class FuelRecordRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'vehicle_id' => 'required|exists:vehicles,id',
            'trip_id' => 'nullable|exists:trips,id',
            'branch_id' => 'nullable|exists:branches,id',
            'fuel_date' => 'required|date',
            'fuel_type' => 'required|in:gasoline,diesel,electric',
            'quantity' => 'required|numeric|min:0',
            'cost' => 'required|numeric|min:0',
            'mileage_at_fueling' => 'required|numeric|min:0',
            'station_name' => 'nullable|string|max:255',
            'receipt_number' => 'nullable|string|max:255',
            'notes' => 'nullable|string',
        ];
    }

    public function messages(): array
    {
        return [
            'vehicle_id.required' => __('fleet::fleet.validation.fuel_record_vehicle_id_required'),
            'vehicle_id.exists' => __('fleet::fleet.validation.trip_vehicle_id_exists'),
            'fuel_date.required' => __('fleet::fleet.validation.fuel_record_fuel_date_required'),
            'fuel_type.required' => __('fleet::fleet.validation.fuel_record_fuel_type_required'),
            'quantity.required' => __('fleet::fleet.validation.fuel_record_quantity_required'),
            'cost.required' => __('fleet::fleet.validation.fuel_record_cost_required'),
            'mileage_at_fueling.required' => __('fleet::fleet.validation.fuel_record_mileage_at_fueling_required'),
        ];
    }
}
