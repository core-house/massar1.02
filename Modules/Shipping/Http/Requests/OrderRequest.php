<?php

namespace Modules\Shipping\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class OrderRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $orderId = $this->route('order')?->id;
        $isUpdate = $this->isMethod('put') || $this->isMethod('patch');

        return [
            'order_number'   => 'required|string|unique:orders,order_number,' . $orderId,
            'driver_id'      => 'required|exists:drivers,id',
            'shipment_id'    => 'required|exists:shipments,id',
            'customer_name'  => 'required|string|max:255',
            'customer_address' => 'required|string',
            'delivery_status'  => 'required|in:pending,assigned,in_transit,delivered',
            'branch_id' => $isUpdate ? 'nullable|exists:branches,id' : 'required|exists:branches,id',
        ];
    }

    public function messages(): array
    {
        return [
            'order_number.required' => __('shipping::shipping.validation.order_number.required'),
            'order_number.unique'   => __('shipping::shipping.validation.order_number.unique'),

            'driver_id.required' => __('shipping::shipping.validation.driver.required'),
            'driver_id.exists'   => __('shipping::shipping.validation.driver.exists'),

            'shipment_id.required' => __('shipping::shipping.validation.shipment.required'),
            'shipment_id.exists'   => __('shipping::shipping.validation.shipment.exists'),

            'customer_name.required' => __('shipping::shipping.validation.customer_name.required'),
            'customer_name.string'   => __('shipping::shipping.validation.customer_name.string'),
            'customer_name.max'      => __('shipping::shipping.validation.customer_name.max'),

            'customer_address.required' => __('shipping::shipping.validation.customer_address.required'),

            'delivery_status.required' => __('shipping::shipping.validation.delivery_status.required'),
            'delivery_status.in'       => __('shipping::shipping.validation.delivery_status.in'),

            'branch_id.required' => __('shipping::shipping.validation.branch.required'),
            'branch_id.exists' => __('shipping::shipping.validation.branch.exists'),
        ];
    }
}
