<?php

namespace Modules\Shipping\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ShipmentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $shipmentId = $this->route('shipment')?->id;
        $isUpdate = $this->isMethod('put') || $this->isMethod('patch');

        return [
            'tracking_number'     => 'required|string|unique:shipments,tracking_number,' . $shipmentId,
            'shipping_company_id' => 'required|exists:shipping_companies,id',
            'customer_name'       => 'required|string|max:255',
            'customer_address'    => 'required|string',
            'weight'              => 'required|numeric|min:0',
            'status'              => 'required|in:pending,in_transit,delivered',
            'branch_id' => $isUpdate ? 'nullable|exists:branches,id' : 'required|exists:branches,id',
        ];
    }

    public function messages(): array
    {
        return [
            'tracking_number.required' => __('Tracking number is required.'),
            'tracking_number.unique'   => __('Tracking number already exists.'),

            'shipping_company_id.required' => __('Shipping company is required.'),
            'shipping_company_id.exists'   => __('Selected shipping company does not exist.'),

            'customer_name.required' => __('Customer name is required.'),
            'customer_name.max'      => __('Customer name must not exceed 255 characters.'),

            'customer_address.required' => __('Customer address is required.'),

            'weight.required' => __('Weight is required.'),
            'weight.numeric'  => __('Weight must be a number.'),
            'weight.min'      => __('Weight must be greater than or equal to 0.'),

            'status.required' => __('Shipment status is required.'),
            'status.in'       => __('Shipment status is invalid.'),

            'branch_id.required' => __('Branch is required.'),
            'branch_id.exists' => __('Selected branch is invalid.'),
        ];
    }
}
