<?php

declare(strict_types=1);

namespace Modules\Fleet\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class TripRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $isUpdate = $this->isMethod('put') || $this->isMethod('patch');

        return [
            'vehicle_id' => 'required|exists:vehicles,id',
            'driver_id' => 'required|exists:drivers,id',
            'branch_id' => 'nullable|exists:branches,id',
            'start_location' => 'required|string|max:255',
            'end_location' => 'required|string|max:255',
            'start_date' => 'required|date',
            'end_date' => $isUpdate ? 'nullable|date|after_or_equal:start_date' : 'nullable|date',
            'start_mileage' => 'required|numeric|min:0',
            'end_mileage' => $isUpdate ? 'nullable|numeric|min:0|gte:start_mileage' : 'nullable|numeric|min:0',
            'purpose' => 'nullable|string|max:255',
            'status' => 'required|in:scheduled,in_progress,completed,cancelled',
            'notes' => 'nullable|string',
        ];
    }

    public function messages(): array
    {
        return [
            'vehicle_id.required' => __('fleet::fleet.validation.trip_vehicle_id_required'),
            'vehicle_id.exists' => __('fleet::fleet.validation.trip_vehicle_id_exists'),
            'driver_id.required' => __('fleet::fleet.validation.trip_driver_id_required'),
            'driver_id.exists' => __('fleet::fleet.validation.trip_driver_id_exists'),
            'start_location.required' => __('fleet::fleet.validation.trip_start_location_required'),
            'end_location.required' => __('fleet::fleet.validation.trip_end_location_required'),
            'start_date.required' => __('fleet::fleet.validation.trip_start_date_required'),
            'start_mileage.required' => __('fleet::fleet.validation.trip_start_mileage_required'),
            'status.required' => __('fleet::fleet.validation.trip_status_required'),
        ];
    }
}
