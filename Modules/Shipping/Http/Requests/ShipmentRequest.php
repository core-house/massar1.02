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
            'tracking_number.required' => __('shipping::shipping.validation.shipment_validation.tracking_number_required'),
            'tracking_number.unique'   => __('shipping::shipping.validation.shipment_validation.tracking_number_unique'),

            'shipping_company_id.required' => __('shipping::shipping.validation.shipment_validation.company_required'),
            'shipping_company_id.exists'   => __('shipping::shipping.validation.shipment_validation.company_exists'),

            'customer_name.required' => __('shipping::shipping.validation.customer_name.required'),
            'customer_name.max'      => __('shipping::shipping.validation.customer_name.max'),

            'customer_address.required' => __('shipping::shipping.validation.customer_address.required'),

            'weight.required' => __('shipping::shipping.validation.shipment_validation.weight_required'),
            'weight.numeric'  => __('shipping::shipping.validation.shipment_validation.weight_numeric'),
            'weight.min'      => __('shipping::shipping.validation.shipment_validation.weight_min'),

            'status.required' => __('shipping::shipping.validation.shipment_validation.status_required'),
            'status.in'       => __('shipping::shipping.validation.shipment_validation.status_in'),

            'branch_id.required' => __('shipping::shipping.validation.branch.required'),
            'branch_id.exists' => __('shipping::shipping.validation.branch.exists'),
        ];
    }
}
